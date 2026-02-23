<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\UserLogModel;
use App\Models\UserModel;

class Logs extends BaseController
{
    protected $userLogModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->userLogModel = new UserLogModel();
        $this->userModel = new UserModel();
        helper('log'); // Carregar o helper de logs
    }
    
    /**
     * List system logs
     */
    public function index()
    {
        // Verificar permissão
        if (!$this->hasPermission('logs.view')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para aceder aos logs');
        }
        
        $data['title'] = 'Logs do Sistema';
        
        // Get filters
        $userId = $this->request->getGet('user_id');
        $action = $this->request->getGet('action');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        
        $builder = $this->userLogModel
            ->select('tbl_user_logs.*, tbl_users.username, tbl_users.first_name, tbl_users.last_name, tbl_users.photo')
            ->join('tbl_users', 'tbl_users.id = tbl_user_logs.user_id', 'left');
        
        if ($userId) {
            $builder->where('tbl_user_logs.user_id', $userId);
        }
        
        if ($action) {
            $builder->where('tbl_user_logs.action', $action);
        }
        
        if ($startDate) {
            $builder->where('tbl_user_logs.created_at >=', $startDate . ' 00:00:00');
        }
        
        if ($endDate) {
            $builder->where('tbl_user_logs.created_at <=', $endDate . ' 23:59:59');
        }
        
        $data['logs'] = $builder->orderBy('tbl_user_logs.created_at', 'DESC')
            ->paginate(50);
        
        $data['pager'] = $this->userLogModel->pager;
        
        // Get users for filter
        $data['users'] = $this->userModel
            ->select('id, username, first_name, last_name')
            ->where('is_active', 1)
            ->orderBy('first_name', 'ASC')
            ->findAll();
        
        // Get unique actions for filter
        $data['actions'] = get_user_actions(); // Usando o helper
        
        // Summary statistics
        $data['totalToday'] = $this->userLogModel
            ->where('DATE(created_at)', date('Y-m-d'))
            ->countAllResults();
        
        $data['totalWeek'] = $this->userLogModel
            ->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
            ->countAllResults();
        
        $data['totalMonth'] = $this->userLogModel
            ->where('created_at >=', date('Y-m-01'))
            ->countAllResults();
        
        $data['selectedUser'] = $userId;
        $data['selectedAction'] = $action;
        $data['selectedStartDate'] = $startDate;
        $data['selectedEndDate'] = $endDate;
        
        // Log this view (apenas para ações importantes, não para cada visualização de lista)
        // log_view('logs', null, 'Visualizou lista de logs');
        
        return view('admin/tools/logs/index', $data);
    }
    
    /**
     * View single log entry
     */
    public function view($id)
    {
        // Verificar permissão
        if (!$this->hasPermission('logs.view')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para aceder aos logs');
        }
        
        $data['title'] = 'Detalhes do Log';
        
        $data['log'] = $this->userLogModel
            ->select('tbl_user_logs.*, tbl_users.username, tbl_users.first_name, tbl_users.last_name, tbl_users.email, tbl_users.photo')
            ->join('tbl_users', 'tbl_users.id = tbl_user_logs.user_id', 'left')
            ->where('tbl_user_logs.id', $id)
            ->first();
        
        if (!$data['log']) {
            return redirect()->to('/admin/tools/logs')->with('error', 'Log não encontrado');
        }
        
        // Decode details if exists
        if ($data['log']->details) {
            $data['log']->details_array = json_decode($data['log']->details, true);
        }
        
        // Log da visualização do log (meta, né?)
        log_view('log', $id, "Visualizou detalhes do log #{$id}");
        
        return view('admin/tools/logs/view', $data);
    }
    
    /**
     * Clear all logs (with confirmation)
     */
    public function clear()
    {
        // Verificar permissão - apenas root ou admin com permissão específica
        if (!$this->isRoot() && !$this->hasPermission('logs.delete')) {
            return redirect()->back()->with('error', 'Não tem permissão para limpar os logs');
        }
        
        $count = $this->userLogModel->countAll();
        
        // Ask for confirmation
        if (!$this->request->getGet('confirm')) {
            return redirect()->back()->with('warning', 
                "Tem certeza que deseja limpar todos os {$count} logs? Esta ação não pode ser desfeita. " . 
                '<a href="' . site_url('admin/tools/logs/clear?confirm=1') . '" class="alert-link">Clique aqui para confirmar</a>'
            );
        }
        
        // Log ANTES de limpar (para registar quem limpou)
        log_action('delete', "Limpou TODOS os logs do sistema", null, 'logs', [
            'total_logs_removed' => $count,
            'action' => 'clear_all'
        ]);
        
        // Limpar os logs
        $this->userLogModel->truncate();
        
        return redirect()->to('/admin/tools/logs')->with('success', "Todos os {$count} logs foram limpos com sucesso");
    }
    
    /**
     * Delete multiple logs
     */
    public function delete()
    {
        // Verificar permissão
        if (!$this->hasPermission('logs.delete')) {
            return $this->respondWithError('Sem permissão para eliminar logs');
        }
        
        if (!$this->request->isAJAX()) {
            return $this->respondWithError('Requisição inválida');
        }
        
        $ids = $this->request->getPost('ids');
        
        if (empty($ids)) {
            return $this->respondWithError('Nenhum log selecionado');
        }
        
        // Buscar informações dos logs antes de eliminar (para o detalhe)
        $logsToDelete = $this->userLogModel
            ->select('id, action, description, target_type, target_id')
            ->whereIn('id', $ids)
            ->findAll();
        
        $count = count($ids);
        $details = [
            'logs_deleted' => $ids,
            'count' => $count,
            'actions' => array_column($logsToDelete, 'action')
        ];
        
        // Eliminar os logs
        $this->userLogModel->whereIn('id', $ids)->delete();
        
        // Log desta eliminação (num log novo, já que os antigos foram eliminados)
        log_action('delete', "Eliminou {$count} logs selecionados", null, 'logs', $details);
        
        return $this->respondWithSuccess("{$count} logs eliminados com sucesso");
    }
    
    /**
     * Export logs to CSV
     */
    public function export()
    {
        // Verificar permissão
        if (!$this->hasPermission('logs.export')) {
            return redirect()->back()->with('error', 'Não tem permissão para exportar logs');
        }
        
        $userId = $this->request->getGet('user_id');
        $action = $this->request->getGet('action');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        
        $builder = $this->userLogModel
            ->select('tbl_user_logs.*, tbl_users.username, tbl_users.first_name, tbl_users.last_name, tbl_users.email')
            ->join('tbl_users', 'tbl_users.id = tbl_user_logs.user_id', 'left')
            ->orderBy('tbl_user_logs.created_at', 'DESC');
        
        if ($userId) {
            $builder->where('tbl_user_logs.user_id', $userId);
        }
        
        if ($action) {
            $builder->where('tbl_user_logs.action', $action);
        }
        
        if ($startDate) {
            $builder->where('tbl_user_logs.created_at >=', $startDate . ' 00:00:00');
        }
        
        if ($endDate) {
            $builder->where('tbl_user_logs.created_at <=', $endDate . ' 23:59:59');
        }
        
        $logs = $builder->findAll();
        
        // Log export com detalhes
        log_export('logs', 'Exportou logs para CSV', [
            'filters' => [
                'user_id' => $userId,
                'action' => $action,
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'total_exported' => count($logs),
            'filename' => 'logs_' . date('Y-m-d_His') . '.csv'
        ]);
        
        // Generate CSV
        $filename = 'logs_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, ['ID', 'Data/Hora', 'Usuário', 'Email', 'Ação', 'Descrição', 'IP Address', 'Target Type', 'Target ID', 'Detalhes'], ';');
        
        // Data
        foreach ($logs as $log) {
            // Formatar detalhes se existirem
            $details = '';
            if ($log->details) {
                $detailsArray = json_decode($log->details, true);
                $details = is_array($detailsArray) ? json_encode($detailsArray, JSON_UNESCAPED_UNICODE) : $log->details;
            }
            
            fputcsv($output, [
                $log->id,
                date('d/m/Y H:i:s', strtotime($log->created_at)),
                $log->first_name ? $log->first_name . ' ' . $log->last_name . ' (' . $log->username . ')' : 'Sistema',
                $log->email ?: '-',
                $log->action,
                $log->description ?: '-',
                $log->ip_address ?: '-',
                $log->target_type ?: '-',
                $log->target_id ?: '-',
                $details ?: '-'
            ], ';');
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Get logs chart data
     */
    public function chartData()
    {
        // Verificar permissão
        if (!$this->hasPermission('logs.view')) {
            return $this->respondWithError('Sem permissão');
        }
        
        if (!$this->request->isAJAX()) {
            return $this->respondWithError('Requisição inválida');
        }
        
        $days = $this->request->getGet('days') ?: 7;
        $userId = $this->request->getGet('user_id');
        $action = $this->request->getGet('action');
        
        $data = [];
        $labels = [];
        $detailedData = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('d/m', strtotime($date));
            
            $builder = $this->userLogModel->where('DATE(created_at)', $date);
            
            if ($userId) {
                $builder->where('user_id', $userId);
            }
            
            if ($action) {
                $builder->where('action', $action);
            }
            
            $count = $builder->countAllResults();
            $data[] = $count;
            
            // Detalhamento por ação (opcional)
            $actionsDetail = $this->userLogModel
                ->select('action, COUNT(*) as count')
                ->where('DATE(created_at)', $date)
                ->groupBy('action')
                ->findAll();
            
            $detailedData[$date] = $actionsDetail;
        }
        
        return $this->respondWithJson([
            'success' => true,
            'labels' => $labels,
            'data' => $data,
            'detailed' => $detailedData
        ]);
    }
    
    /**
     * Get statistics
     */
    public function statistics()
    {
        // Verificar permissão
        if (!$this->hasPermission('logs.view')) {
            return $this->respondWithError('Sem permissão');
        }
        
        if (!$this->request->isAJAX()) {
            return $this->respondWithError('Requisição inválida');
        }
        
        $stats = $this->userLogModel->getStatistics();
        
        return $this->respondWithJson([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * Get logs by target (para visualizar histórico de um registo específico)
     */
    public function byTarget($targetType, $targetId)
    {
        // Verificar permissão
        if (!$this->hasPermission('logs.view')) {
            return $this->respondWithError('Sem permissão');
        }
        
        if (!$this->request->isAJAX()) {
            return $this->respondWithError('Requisição inválida');
        }
        
        $logs = $this->userLogModel->getByTarget($targetType, $targetId);
        
        return $this->respondWithJson([
            'success' => true,
            'data' => $logs,
            'total' => count($logs)
        ]);
    }
}