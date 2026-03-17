<?php
// app/Controllers/admin/Schedule.php
namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\ScheduleModel;
use App\Models\ClassModel;
use App\Models\ClassDisciplineModel;
use App\Models\DisciplineModel;
use App\Models\UserModel;
use App\Models\GradeLevelModel;
use App\Models\AcademicYearModel;

class Schedule extends BaseController
{
    protected $scheduleModel;
    protected $classModel;
    protected $classDisciplineModel;
    protected $disciplineModel;
    protected $userModel;
    protected $gradeLevelModel;
    protected $academicYearModel;
    
    public function __construct()
    {
        $this->scheduleModel = new ScheduleModel();
        $this->classModel = new ClassModel();
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->disciplineModel = new DisciplineModel();
        $this->userModel = new UserModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->academicYearModel = new AcademicYearModel();
        
        helper(['form', 'url']);
    }
    
    /**
     * Lista de horários - View principal
     */
    public function index()
    {
        $data['title'] = 'Horários';
        
        // Buscar anos letivos para o filtro
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        // Buscar ano letivo corrente para o filtro padrão
        $currentYear = $this->academicYearModel
            ->where('id', current_academic_year())
            ->where('is_active', 1)
            ->first();
        
        $data['currentYearId'] = $currentYear ? $currentYear['id'] : null;
        $data['selectedYear'] = $this->request->getGet('academic_year') ?: ($currentYear ? $currentYear['id'] : '');
        
        return view('admin/classes/schedule/index', $data);
    }
    
    /**
     * Retorna dados para o DataTables (AJAX)
     */
    public function getData()
    {
        $request = service('request');
        
        // Parâmetros do DataTables
        $draw = (int)($request->getPost('draw') ?? 0);
        $start = (int)($request->getPost('start') ?? 0);
        $length = (int)($request->getPost('length') ?? 10);
        $searchValue = $request->getPost('search')['value'] ?? '';
        
        // Filtros
        $academicYearId = $request->getPost('academic_year');
        
        // Ordenação
        $orderColumnIndex = $request->getPost('order')[0]['column'] ?? 1;
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'asc';
        
        // Colunas para ordenação
        $columns = [
            0 => 'tbl_classes.class_name',
            1 => 'tbl_classes.class_code',
            2 => 'tbl_grade_levels.level_name',
            3 => 'tbl_academic_years.year_name',
            4 => 'tbl_classes.class_shift',
            5 => 'tbl_schedules.total_items',
            6 => 'tbl_schedules.total_hours',
        ];
        
        $orderColumn = $columns[$orderColumnIndex] ?? 'tbl_classes.class_name';
        
        // Query principal
        $builder = $this->scheduleModel
            ->select('
                tbl_schedules.*,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_classes.class_shift,
                tbl_grade_levels.level_name,
                tbl_academic_years.year_name,
                (SELECT COUNT(*) FROM tbl_class_disciplines WHERE class_id = tbl_classes.id) as total_disciplines
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_schedules.class_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id', 'left')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id', 'left')
            ->where('tbl_schedules.is_active', 1);
        
        // Aplicar filtro de ano letivo
        if (!empty($academicYearId)) {
            $builder->where('tbl_classes.academic_year_id', $academicYearId);
        }
        
        // Aplicar busca
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('tbl_classes.class_name', $searchValue)
                ->orLike('tbl_classes.class_code', $searchValue)
                ->orLike('tbl_grade_levels.level_name', $searchValue)
                ->orLike('tbl_academic_years.year_name', $searchValue)
                ->groupEnd();
        }
        
        // Contar total de registros filtrados
        $recordsFiltered = $builder->countAllResults(false);
        
        // Aplicar ordenação e paginação
        $data = $builder->orderBy($orderColumn, $orderDir)
            ->limit($length, $start)
            ->get()
            ->getResult();
        
        // Processar dados para o DataTables
        foreach ($data as &$row) {
            // Determinar status
            if ($row->total_items == 0) {
                $row->status = '<span class="badge bg-danger">Sem horário</span>';
            } elseif ($row->total_items < ($row->total_disciplines ?? 1)) {
                $row->status = '<span class="badge bg-warning">Incompleto</span>';
            } else {
                $row->status = '<span class="badge bg-success">Completo</span>';
            }
            
            // Turno com badge
            $shiftClass = '';
            if ($row->class_shift == 'Manhã') $shiftClass = 'badge bg-info';
            elseif ($row->class_shift == 'Tarde') $shiftClass = 'badge bg-warning';
            elseif ($row->class_shift == 'Noite') $shiftClass = 'badge bg-secondary';
            else $shiftClass = 'badge bg-primary';
            
            $row->shift = '<span class="' . $shiftClass . '">' . $row->class_shift . '</span>';
            
            // Carga horária formatada
            $row->hours = number_format($row->total_hours ?? 0, 1) . 'h';
            
            // Ações
            $row->actions = '
                <button class="btn btn-sm btn-outline-primary view-schedule" data-class-id="' . $row->class_id . '" data-class-name="' . $row->class_name . '" title="Ver horário">
                    <i class="fas fa-eye"></i>
                </button>
                <a href="' . site_url('admin/classes/schedule/export-pdf/' . $row->class_id) . '" class="btn btn-sm btn-outline-secondary" title="PDF">
                    <i class="fas fa-file-pdf"></i>
                </a>
            ';
        }
        
        // Total de registros sem filtros
        $totalRecords = $this->scheduleModel
            ->where('is_active', 1)
            ->countAllResults();
        
        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    /**
     * Busca dados do horário para o modal (AJAX)
     */
    public function getScheduleData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requisição inválida']);
        }
        
        $classId = $this->request->getPost('class_id');
        
        if (!$classId) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID da turma não fornecido']);
        }
        
        // Buscar dados da turma
        $class = $this->classModel
            ->select('tbl_classes.*, tbl_academic_years.year_name')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->find($classId);
        
        if (!$class) {
            return $this->response->setJSON(['success' => false, 'message' => 'Turma não encontrada']);
        }
        
        // Buscar horário
        $scheduleRecord = $this->scheduleModel->where('class_id', $classId)->first();
        
        if (!$scheduleRecord) {
            // Retornar estrutura vazia
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'class_name' => $class['class_name'],
                    'class_shift' => $class['class_shift'],
                    'year_name' => $class['year_name'],
                    'total_items' => 0,
                    'total_hours' => 0,
                    'schedule' => []
                ]
            ]);
        }
        
        // Decodificar schedule_data
        $scheduleData = $scheduleRecord->schedule_data;
        if (is_string($scheduleData)) {
            $scheduleData = json_decode($scheduleData, true);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'class_name' => $class['class_name'],
                'class_shift' => $class['class_shift'],
                'year_name' => $class['year_name'],
                'total_items' => $scheduleRecord->total_items ?? 0,
                'total_hours' => $scheduleRecord->total_hours ?? 0,
                'schedule' => $scheduleData ?: []
            ]
        ]);
    }

    /**
     * Carrega os dados de um horário para edição (AJAX)
     */
    public function getScheduleItem()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false]);
        }
        
        $id = $this->request->getPost('id');
        $classId = $this->request->getPost('class_id');
        
        if (!$id || !$classId) {
            return $this->response->setJSON(['success' => false]);
        }
        
        $scheduleRecord = $this->scheduleModel->where('class_id', $classId)->first();
        
        if (!$scheduleRecord) {
            return $this->response->setJSON(['success' => false]);
        }
        
        // Converter schedule_data de string para array se necessário
        $data = $scheduleRecord->schedule_data;
        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        
        if (!is_array($data)) {
            return $this->response->setJSON(['success' => false]);
        }
        
        foreach ($data as $day => $periods) {
            if (!is_array($periods)) continue;
            
            foreach ($periods as $period => $items) {
                if (!is_array($items)) continue;
                
                foreach ($items as $item) {
                    if (isset($item['id']) && $item['id'] == $id) {
                        return $this->response->setJSON([
                            'success' => true,
                            'item' => $item,
                            'day' => $day,
                            'period' => $period
                        ]);
                    }
                }
            }
        }
        
        return $this->response->setJSON(['success' => false]);
    }

    /**
     * Salva horário (criar ou atualizar)
     */
    public function save()
    {
        $id = $this->request->getPost('id');
        $classId = $this->request->getPost('class_id');
        
        // Validar dados básicos
        $validationRules = [
            'class_id' => 'required|numeric',
            'discipline_id' => 'required|numeric',
            'day_of_week' => 'required|in_list[monday,tuesday,wednesday,thursday,friday,saturday]',
            'period' => 'required|in_list[1,2,3,4,5,6]',
            'start_time' => 'required|regex_match[/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/]',
            'end_time' => 'required|regex_match[/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/]'
        ];
        
        if (!$this->validate($validationRules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        // Buscar registro de horário
        $scheduleRecord = $this->scheduleModel->findOrCreateByClass($classId);
        $scheduleData = $scheduleRecord->schedule_data;
        
        $day = $this->request->getPost('day_of_week');
        $period = $this->request->getPost('period');
        
        // Preparar dados do novo item
        $itemData = [
            'id' => $id ?: uniqid(),
            'discipline_id' => $this->request->getPost('discipline_id'),
            'discipline_name' => $this->getDisciplineName($this->request->getPost('discipline_id')),
            'teacher_id' => $this->request->getPost('teacher_id') ?: null,
            'teacher_name' => $this->getTeacherName($this->request->getPost('teacher_id')),
            'start_time' => $this->request->getPost('start_time') . ':00',
            'end_time' => $this->request->getPost('end_time') . ':00',
            'room' => $this->request->getPost('room'),
            'period' => $period
        ];
        
        // Se for edição, remover o item antigo
        if ($id) {
            foreach ($scheduleData[$day][$period] as $key => $item) {
                if ($item['id'] == $id) {
                    unset($scheduleData[$day][$period][$key]);
                    break;
                }
            }
        }
        
        // Adicionar novo item
        $scheduleData[$day][$period][] = $itemData;
        
        // Reindexar arrays
        $scheduleData[$day][$period] = array_values($scheduleData[$day][$period]);
        
        // Salvar
        if ($this->scheduleModel->updateSchedule($classId, $scheduleData)) {
            $action = $id ? 'atualizado' : 'criado';
            return redirect()->to('/admin/classes/schedule/view/' . $classId)
                ->with('success', "Horário {$action} com sucesso!");
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao salvar horário');
        }
    }
    
    /**
     * Remove um item do horário 
     */
    public function delete($id)
    {
        if (!$id) {
            return redirect()->back()
                ->with('error', 'ID do horário não fornecido');
        }
        
        // Buscar todos os registros para encontrar o item
        $schedules = $this->scheduleModel->findAll();
        $found = false;
        $classId = null;
        $deletedItem = null;
        
        foreach ($schedules as $schedule) {
            // Converter schedule_data de string para array se necessário
            $data = $schedule->schedule_data;
            if (is_string($data)) {
                $data = json_decode($data, true);
            }
            
            // Verificar se a decodificação foi bem-sucedida
            if (!is_array($data)) {
                log_message('error', "schedule_data inválido para o schedule ID: {$schedule['id']}");
                continue;
            }
            
            foreach ($data as $day => $periods) {
                if (!is_array($periods)) continue;
                
                foreach ($periods as $period => $items) {
                    if (!is_array($items)) continue;
                    
                    foreach ($items as $key => $item) {
                        if (isset($item['id']) && $item['id'] == $id) {
                            $deletedItem = $item;
                            unset($data[$day][$period][$key]);
                            $data[$day][$period] = array_values($data[$day][$period]);
                            
                            // Atualizar o registro no banco
                            $this->scheduleModel->updateSchedule($schedule['class_id'], $data);
                            
                            $classId = $schedule['class_id'];
                            $found = true;
                            break 4;
                        }
                    }
                }
            }
        }
        
        if (!$found) {
            return redirect()->back()
                ->with('error', 'Horário não encontrado');
        }
        
        log_message('info', "Horário ID {$id} (disciplina: " . ($deletedItem['discipline_name'] ?? 'N/A') . ") eliminado por usuário " . session()->get('user_id'));
        
        return redirect()->to('/admin/classes/schedule/view/' . $classId)
            ->with('success', 'Horário eliminado com sucesso');
    }
    
    /**
     * Verifica disponibilidade de horário via AJAX
     */
    public function checkAvailability()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['available' => false]);
        }
        
        $classId = $this->request->getPost('class_id');
        $dayOfWeek = $this->request->getPost('day_of_week');
        $period = $this->request->getPost('period');
        $scheduleId = $this->request->getPost('schedule_id') ?: 0;
        
        $scheduleRecord = $this->scheduleModel->findOrCreateByClass($classId);
        
        // Converter schedule_data de string para array se necessário
        $scheduleData = $scheduleRecord->schedule_data;
        if (is_string($scheduleData)) {
            $scheduleData = json_decode($scheduleData, true);
        }
        
        if (!is_array($scheduleData)) {
            return $this->response->setJSON([
                'available' => true,
                'message' => 'Disponível',
                'count' => 0
            ]);
        }
        
        $existing = isset($scheduleData[$dayOfWeek][$period]) ? count($scheduleData[$dayOfWeek][$period]) : 0;
        
        return $this->response->setJSON([
            'available' => $existing == 0 || $scheduleId,
            'message' => $existing > 0 ? 'Já existe um horário para este período' : 'Disponível',
            'count' => $existing
        ]);
    }
    
    /**
     * Duplica horário de uma turma para outra
     */
    public function duplicate()
    {
        $sourceClassId = $this->request->getPost('source_class_id');
        $targetClassId = $this->request->getPost('target_class_id');
        
        if (!$sourceClassId || !$targetClassId) {
            return redirect()->back()
                ->with('error', 'Turmas de origem e destino são obrigatórias');
        }
        
        // Buscar horário da turma de origem
        $sourceSchedule = $this->scheduleModel->where('class_id', $sourceClassId)->first();
        
        if (!$sourceSchedule) {
            return redirect()->back()
                ->with('warning', 'A turma de origem não tem horários para duplicar');
        }
        
        // Duplicar horário
        $data = [
            'class_id' => $targetClassId,
            'schedule_data' => $sourceSchedule->schedule_data,
            'total_items' => $sourceSchedule->total_items,
            'total_hours' => $sourceSchedule->total_hours,
            'version' => $sourceSchedule->version,
            'is_active' => 1
        ];
        
        // Verificar se destino já tem horário
        $targetSchedule = $this->scheduleModel->where('class_id', $targetClassId)->first();
        
        if ($targetSchedule) {
            $this->scheduleModel->update($targetSchedule->id, $data);
        } else {
            $this->scheduleModel->insert($data);
        }
        
        return redirect()->to('/admin/classes/schedule/view/' . $targetClassId)
            ->with('success', 'Horário duplicado com sucesso!');
    }

    /**
     * Exibe o horário de uma turma específica (página completa)
     */
    public function view($id)
    {
        // Buscar dados da turma
        $class = $this->classModel
            ->select('
                tbl_classes.*,
                tbl_grade_levels.level_name,
                tbl_academic_years.year_name,
                tbl_academic_years.start_date,
                tbl_academic_years.end_date,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as teacher_name,
                tbl_users.phone as teacher_phone,
                tbl_users.email as teacher_email
            ')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->join('tbl_users', 'tbl_users.id = tbl_classes.class_teacher_id', 'left')
            ->find($id);
        
        if (!$class) {
            return redirect()->to('/admin/schedule')
                ->with('error', 'Turma não encontrada');
        }
        
        // Buscar disciplinas da turma com professores
        $disciplines = $this->classDisciplineModel
            ->select('
                tbl_class_disciplines.discipline_id,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_disciplines.workload_hours,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as teacher_name,
                tbl_users.id as teacher_id
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->where('tbl_class_disciplines.class_id', $id)
            ->where('tbl_class_disciplines.is_active', 1)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        // Buscar ou criar registro de horário
        $scheduleRecord = $this->scheduleModel->findOrCreateByClass($id);
        $scheduleData = $scheduleRecord->schedule_data;
        
        // Estatísticas
        $totalDisciplines = count($disciplines);
        $assignedTeachers = 0;
        $weeklyHours = $scheduleRecord->total_hours ?? 0;
        $totalSchedules = $scheduleRecord->total_items ?? 0;
        
        foreach ($disciplines as $disc) {
            if ($disc['teacher_id']) $assignedTeachers++;
        }
        
        // Buscar todos os professores para o formulário rápido
        $teachers = $this->userModel
            ->select('tbl_users.id, tbl_users.first_name, tbl_users.last_name')
            ->join('tbl_roles', 'tbl_roles.id = tbl_users.role_id')
            ->where('tbl_roles.role_name', 'Professor')
            ->where('tbl_users.is_active', 1)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Preparar dados para a view
        $data['title'] = 'Horário da Turma: ' . $class['class_name'];
        $data['class'] = $class;
        $data['disciplines'] = $disciplines;
        $data['teachers'] = $teachers;
        $data['schedule'] = $scheduleData;
        $data['scheduleRecord'] = $scheduleRecord;
        $data['totalDisciplines'] = $totalDisciplines;
        $data['assignedTeachers'] = $assignedTeachers;
        $data['weeklyHours'] = $weeklyHours;
        $data['totalSchedules'] = $totalSchedules;
        
        // Dias da semana em português
        $data['weekDays'] = [
            'monday' => 'Segunda-feira',
            'tuesday' => 'Terça-feira',
            'wednesday' => 'Quarta-feira',
            'thursday' => 'Quinta-feira',
            'friday' => 'Sexta-feira',
            'saturday' => 'Sábado'
        ];
        
        // Dias da semana em inglês (para o banco)
        $data['daysOfWeek'] = [
            'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'
        ];
        
        // Períodos do dia (padrão)
        $data['periods'] = $this->getDefaultPeriods();
        
        return view('admin/classes/schedule/view', $data);
    }

    /**
     * Exporta horário para PDF
     */
    public function exportPdf($classId)
    {
        // Buscar dados da turma
        $class = $this->classModel
            ->select('
                tbl_classes.*,
                tbl_grade_levels.level_name,
                tbl_academic_years.year_name,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as teacher_name
            ')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->join('tbl_users', 'tbl_users.id = tbl_classes.class_teacher_id', 'left')
            ->find($classId);
        
        if (!$class) {
            return redirect()->back()
                ->with('error', 'Turma não encontrada');
        }
        
        // Buscar horários
        $scheduleRecord = $this->scheduleModel->where('class_id', $classId)->first();
        
        if (!$scheduleRecord) {
            return redirect()->back()
                ->with('error', 'Horário não encontrado');
        }
        
        $scheduleData = $scheduleRecord->schedule_data;
        if (is_string($scheduleData)) {
            $scheduleData = json_decode($scheduleData, true);
        }
        
        $data['class'] = $class;
        $data['schedule'] = $scheduleData;
        $data['weekDays'] = [
            'monday' => 'Segunda-feira',
            'tuesday' => 'Terça-feira',
            'wednesday' => 'Quarta-feira',
            'thursday' => 'Quinta-feira',
            'friday' => 'Sexta-feira',
            'saturday' => 'Sábado'
        ];
        $data['periods'] = $this->getDefaultPeriods();
        
        // Por enquanto, redirecionar com mensagem
        return redirect()->back()
            ->with('info', 'Funcionalidade de exportação PDF em desenvolvimento');
    }

    // ============ MÉTODOS AUXILIARES ============
    
    /**
     * Retorna períodos padrão
     */
    private function getDefaultPeriods()
    {
        return [
            '1' => ['time' => '07:30 - 09:00', 'name' => '1º Período'],
            '2' => ['time' => '09:15 - 10:45', 'name' => '2º Período'],
            '3' => ['time' => '11:00 - 12:30', 'name' => '3º Período'],
            '4' => ['time' => '13:30 - 15:00', 'name' => '4º Período'],
            '5' => ['time' => '15:15 - 16:45', 'name' => '5º Período'],
            '6' => ['time' => '17:00 - 18:30', 'name' => '6º Período']
        ];
    }
    
    /**
     * Obtém nome da disciplina
     */
    private function getDisciplineName($disciplineId)
    {
        $discipline = $this->disciplineModel->find($disciplineId);
        return $discipline ? $discipline['discipline_name'] : '';
    }
    
    /**
     * Obtém nome do professor
     */
    private function getTeacherName($teacherId)
    {
        if (!$teacherId) return 'Não atribuído';
        $teacher = $this->userModel->find($teacherId);
        return $teacher ? $teacher['first_name'] . ' ' . $teacher['last_name'] : 'Não atribuído';
    }
}