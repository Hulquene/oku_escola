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
        
        // Log this view (opcional - pode gerar muitos logs)
        // log_view('logs', null, 'Visualizou lista de logs');
        
        return view('admin/tools/logs/index', $data);
    }
    
    /**
     * View single log entry
     */
    public function view($id)
    {
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
        
        return view('admin/tools/logs/view', $data);
    }
    
    /**
     * Clear all logs (with confirmation)
     */
    public function clear()
    {
        // Check if user has permission (only root/admin)
        if (!$this->isRoot() && !$this->hasPermission('delete_logs')) {
            return redirect()->back()->with('error', 'Não tem permissão para limpar os logs');
        }
        
        // Ask for confirmation
        if (!$this->request->getGet('confirm')) {
            return redirect()->back()->with('warning', 'Tem certeza que deseja limpar todos os logs? Esta ação não pode ser desfeita. <a href="' . site_url('admin/tools/logs/clear?confirm=1') . '" class="alert-link">Clique aqui para confirmar</a>');
        }
        
        // Log antes de limpar
        $count = $this->userLogModel->countAll();
        log_action('delete', "Limpou todos os logs do sistema ({$count} registos)", null, 'logs', ['count' => $count]);
        
        $this->userLogModel->truncate();
        
        return redirect()->to('/admin/tools/logs')->with('success', 'Todos os logs foram limpos com sucesso');
    }
    
    /**
     * Export logs to CSV
     */
    public function export()
    {
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
        
        // Log export
        log_export('logs', 'Exportou logs para CSV', [
            'user_id' => $userId,
            'action' => $action,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'count' => count($logs)
        ]);
        
        // Generate CSV
        $filename = 'logs_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, ['ID', 'Data/Hora', 'Usuário', 'Email', 'Ação', 'Descrição', 'IP Address', 'User Agent', 'Target Type', 'Target ID', 'Detalhes'], ';');
        
        // Data
        foreach ($logs as $log) {
            fputcsv($output, [
                $log->id,
                date('d/m/Y H:i:s', strtotime($log->created_at)),
                $log->first_name . ' ' . $log->last_name . ' (' . $log->username . ')',
                $log->email,
                $log->action,
                $log->description,
                $log->ip_address ?: '-',
                $log->user_agent ?: '-',
                $log->target_type ?: '-',
                $log->target_id ?: '-',
                $log->details ?: '-'
            ], ';');
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Delete multiple logs
     */
    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->respondWithError('Requisição inválida');
        }
        
        $ids = $this->request->getPost('ids');
        
        if (empty($ids)) {
            return $this->respondWithError('Nenhum log selecionado');
        }
        
        $count = count($ids);
        $this->userLogModel->whereIn('id', $ids)->delete();
        
        // Log this deletion
        log_action('delete', "Eliminou {$count} logs selecionados", null, 'logs', ['ids' => $ids, 'count' => $count]);
        
        return $this->respondWithSuccess("{$count} logs eliminados com sucesso");
    }
    
    /**
     * Get logs chart data
     */
    public function chartData()
    {
        if (!$this->request->isAJAX()) {
            return $this->respondWithError('Requisição inválida');
        }
        
        $days = $this->request->getGet('days') ?: 7;
        $userId = $this->request->getGet('user_id');
        $action = $this->request->getGet('action');
        
        $data = [];
        $labels = [];
        
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
        }
        
        return $this->respondWithJson([
            'success' => true,
            'labels' => $labels,
            'data' => $data
        ]);
    }
    
    /**
     * Get statistics
     */
    public function statistics()
    {
        if (!$this->request->isAJAX()) {
            return $this->respondWithError('Requisição inválida');
        }
        
        $stats = $this->userLogModel->getStatistics();
        
        return $this->respondWithJson([
            'success' => true,
            'data' => $stats
        ]);
    }
}