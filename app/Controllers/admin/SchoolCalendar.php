<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\SchoolEventModel;
use App\Models\AcademicYearModel;
use App\Models\SemesterModel;
use App\Models\ExamScheduleModel;
use App\Models\ExamPeriodModel;

class SchoolCalendar extends BaseController
{
    protected $eventModel;
    protected $academicYearModel;
    protected $semesterModel;
    protected $examScheduleModel;
    protected $examPeriodModel;
    
    public function __construct()
    {
        $this->eventModel = new SchoolEventModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->semesterModel = new SemesterModel();
        $this->examScheduleModel = new ExamScheduleModel();
        $this->examPeriodModel = new ExamPeriodModel();
    }
    
    /**
     * Calendar page
     */
    public function index()
    {
        $data['title'] = 'Calendário Escolar';
        
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        $currentYear = $this->academicYearModel->getCurrent();
        $data['selectedYear'] = $this->request->getGet('academic_year') ?: 
            ($currentYear->id ?? null);
        
        return view('admin/academic/calendar/index', $data);
    }
    
    /**
     * Get all events for calendar (AJAX)
     */
    public function getEvents()
    {
        try {
            $startDate = $this->request->getGet('start');
            $endDate = $this->request->getGet('end');
            $academicYearId = $this->request->getGet('academic_year');
            
            // Validar datas
            if (!$startDate || !$endDate) {
                return $this->response->setJSON([]);
            }
            
            $allEvents = [];
            
            // 1. Buscar eventos manuais do calendário
            $manualEvents = $this->getManualEvents($startDate, $endDate, $academicYearId);
            $allEvents = array_merge($allEvents, $manualEvents);
            
            // 2. Buscar fim de semestres
            $semesterEndEvents = $this->getSemesterEndEvents($startDate, $endDate, $academicYearId);
            $allEvents = array_merge($allEvents, $semesterEndEvents);
            
            // 3. Buscar exames agendados
            $examEvents = $this->getExamEvents($startDate, $endDate, $academicYearId);
            $allEvents = array_merge($allEvents, $examEvents);
            
            return $this->response->setJSON($allEvents);
            
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar eventos: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([]);
        }
    }
    
    /**
     * Get manual calendar events
     */
    private function getManualEvents($startDate, $endDate, $academicYearId)
    {
        $events = $this->eventModel->getForCalendar($startDate, $endDate, $academicYearId);
        
        $formattedEvents = [];
        foreach ($events as $event) {
            // Verificar se é array ou objeto
            if (is_array($event)) {
                $formattedEvents[] = [
                    'id' => 'manual_' . $event['id'],
                    'title' => $event['event_title'],
                    'start' => $event['start_date'] . ($event['start_time'] ? 'T' . $event['start_time'] : ''),
                    'end' => ($event['end_date'] ?: $event['start_date']) . ($event['end_time'] ? 'T' . $event['end_time'] : ''),
                    'allDay' => (bool)$event['all_day'],
                    'backgroundColor' => $event['color'] ?: $this->getEventColor($event['event_type']),
                    'borderColor' => $event['color'] ?: $this->getEventColor($event['event_type']),
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => 'manual',
                        'event_type' => $event['event_type'],
                        'description' => $event['event_description'],
                        'location' => $event['location'],
                        'start_time' => $event['start_time'],
                        'end_time' => $event['end_time'],
                        'academic_year_id' => $event['academic_year_id'],
                        'created_by' => isset($event['first_name']) ? $event['first_name'] . ' ' . ($event['last_name'] ?? '') : 'Sistema',
                        'can_edit' => true
                    ]
                ];
            } else {
                $formattedEvents[] = [
                    'id' => 'manual_' . $event->id,
                    'title' => $event->event_title,
                    'start' => $event->start_date . ($event->start_time ? 'T' . $event->start_time : ''),
                    'end' => ($event->end_date ?: $event->start_date) . ($event->end_time ? 'T' . $event->end_time : ''),
                    'allDay' => (bool)$event->all_day,
                    'backgroundColor' => $event->color ?: $this->getEventColor($event->event_type),
                    'borderColor' => $event->color ?: $this->getEventColor($event->event_type),
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => 'manual',
                        'event_type' => $event->event_type,
                        'description' => $event->event_description,
                        'location' => $event->location,
                        'start_time' => $event->start_time,
                        'end_time' => $event->end_time,
                        'academic_year_id' => $event->academic_year_id,
                        'created_by' => isset($event->first_name) ? $event->first_name . ' ' . ($event->last_name ?? '') : 'Sistema',
                        'can_edit' => true
                    ]
                ];
            }
        }
        
        return $formattedEvents;
    }
    
    /**
     * Get semester end events
     */
    private function getSemesterEndEvents($startDate, $endDate, $academicYearId)
    {
        $builder = $this->semesterModel
            ->select('tbl_semesters.*, tbl_academic_years.year_name')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_semesters.academic_year_id')
            ->where('tbl_semesters.end_date >=', $startDate)
            ->where('tbl_semesters.end_date <=', $endDate);
        
        if ($academicYearId) {
            $builder->where('tbl_semesters.academic_year_id', $academicYearId);
        }
        
        $semesters = $builder->findAll();
        
        $formattedEvents = [];
        foreach ($semesters as $semester) {
            // Determinar cor baseada no status
            $color = '#6c757d'; // cinza padrão
            if ($semester->status == 'concluido') {
                $color = '#28a745'; // verde
            } elseif ($semester->status == 'processado') {
                $color = '#ffc107'; // amarelo
            } elseif ($semester->is_current) {
                $color = '#007bff'; // azul
            }
            
            $formattedEvents[] = [
                'id' => 'semester_end_' . $semester->id,
                'title' => 'Fim: ' . $semester->semester_name,
                'start' => $semester->end_date,
                'end' => $semester->end_date,
                'allDay' => true,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'semester_end',
                    'semester_name' => $semester->semester_name,
                    'semester_type' => $semester->semester_type,
                    'status' => $semester->status ?? ($semester->is_active ? 'ativo' : 'inativo'),
                    'is_current' => $semester->is_current,
                    'academic_year' => $semester->year_name,
                    'description' => 'Término do ' . $semester->semester_name,
                    'can_edit' => false
                ]
            ];
        }
        
        return $formattedEvents;
    }
    
    /**
     * Get exam events
     */
    private function getExamEvents($startDate, $endDate, $academicYearId)
    {
        $builder = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_disciplines.discipline_name,
                tbl_classes.class_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_boards.weight,
                tbl_exam_periods.period_name,
                tbl_exam_periods.semester_id,
                tbl_semesters.semester_name,
                tbl_academic_years.id as academic_year_id,
                tbl_academic_years.year_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_exam_periods.semester_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_semesters.academic_year_id')
            ->where('tbl_exam_schedules.exam_date >=', $startDate)
            ->where('tbl_exam_schedules.exam_date <=', $endDate);
        
        if ($academicYearId) {
            $builder->where('tbl_academic_years.id', $academicYearId);
        }
        
        $exams = $builder->orderBy('tbl_exam_schedules.exam_date', 'ASC')
            ->orderBy('tbl_exam_schedules.exam_time', 'ASC')
            ->findAll();
        
        $formattedEvents = [];
        foreach ($exams as $exam) {
            $today = date('Y-m-d');
            $isPast = $exam->exam_date < $today;
            $isToday = $exam->exam_date == $today;
            
            // Determinar cor baseada no status
            if ($isToday) {
                $color = '#fd7e14'; // laranja
            } elseif ($isPast) {
                $color = '#6c757d'; // cinza
            } else {
                $color = '#dc3545'; // vermelho
            }
            
            // Título do evento
            $title = $exam->board_type . ': ' . $exam->discipline_name;
            
            $formattedEvents[] = [
                'id' => 'exam_' . $exam->id,
                'title' => $title,
                'start' => $exam->exam_date . ($exam->exam_time ? 'T' . $exam->exam_time : ''),
                'end' => $exam->exam_date . ($exam->exam_time ? 'T' . date('H:i:s', strtotime($exam->exam_time) + ($exam->duration_minutes ?? 120) * 60) : ''),
                'allDay' => false,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'exam',
                    'exam_id' => $exam->id,
                    'discipline' => $exam->discipline_name,
                    'class' => $exam->class_name,
                    'board_type' => $exam->board_type,
                    'board_name' => $exam->board_name,
                    'weight' => $exam->weight,
                    'period' => $exam->period_name,
                    'semester' => $exam->semester_name,
                    'academic_year' => $exam->year_name,
                    'room' => $exam->exam_room,
                    'duration' => $exam->duration_minutes,
                    'status' => $exam->status,
                    'is_today' => $isToday,
                    'is_past' => $isPast,
                    'description' => 'Exame de ' . $exam->discipline_name . ' - Turma ' . $exam->class_name,
                    'can_edit' => false
                ]
            ];
        }
        
        return $formattedEvents;
    }
    
    /**
     * Save manual event (AJAX)
     */
    public function saveEvent()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Requisição inválida'
                ]);
            }
            
            $rules = [
                'academic_year_id' => 'required|numeric',
                'event_title' => 'required|min_length[3]|max_length[255]',
                'event_type' => 'required|in_list[Feriado,Reunião,Prova,Entrega de Notas,Matrícula,Inscrição,Outro]',
                'start_date' => 'required|valid_date'
            ];
            
            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $this->validator->getErrors()
                ]);
            }
            
            // Validar se end_date é posterior a start_date
            $startDate = $this->request->getPost('start_date');
            $endDate = $this->request->getPost('end_date');
            
            if ($endDate && strtotime($endDate) < strtotime($startDate)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'A data de fim não pode ser anterior à data de início'
                ]);
            }
            
            $data = [
                'academic_year_id' => $this->request->getPost('academic_year_id'),
                'event_title' => $this->request->getPost('event_title'),
                'event_type' => $this->request->getPost('event_type'),
                'event_description' => $this->request->getPost('event_description'),
                'start_date' => $startDate,
                'end_date' => $endDate ?: null,
                'start_time' => $this->request->getPost('start_time') ?: null,
                'end_time' => $this->request->getPost('end_time') ?: null,
                'location' => $this->request->getPost('location'),
                'all_day' => $this->request->getPost('all_day') ? 1 : 0,
                'color' => $this->request->getPost('color') ?: null,
                'created_by' => session()->get('user_id')
            ];
            
            $id = $this->request->getPost('id');
            
            if ($id) {
                // Verificar se o evento existe
                $existingEvent = $this->eventModel->find($id);
                if (!$existingEvent) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Evento não encontrado'
                    ]);
                }
                
                if ($this->eventModel->update($id, $data)) {
                    $message = 'Evento atualizado com sucesso';
                    log_message('info', "Evento ID {$id} atualizado por usuário " . session()->get('user_id'));
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Erro ao atualizar evento',
                        'errors' => $this->eventModel->errors()
                    ]);
                }
            } else {
                $newId = $this->eventModel->insert($data);
                if ($newId) {
                    $message = 'Evento criado com sucesso';
                    log_message('info', "Novo evento ID {$newId} criado por usuário " . session()->get('user_id'));
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Erro ao inserir evento no banco de dados',
                        'errors' => $this->eventModel->errors()
                    ]);
                }
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Erro ao salvar evento: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Delete manual event (AJAX)
     */
    public function deleteEvent($id)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Requisição inválida'
                ]);
            }
            
            $event = $this->eventModel->find($id);
            
            if (!$event) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Evento não encontrado'
                ]);
            }
            
            if ($this->eventModel->delete($id)) {
                log_message('info', "Evento ID {$id} eliminado por usuário " . session()->get('user_id'));
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Evento eliminado com sucesso'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao eliminar evento',
                    'errors' => $this->eventModel->errors()
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Erro ao eliminar evento: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ]);
        }
    }
    
    /**
     * Update manual event date (AJAX - for drag & drop)
     */
    public function updateDate()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Requisição inválida'
                ]);
            }
            
            $id = $this->request->getPost('id');
            $startDate = $this->request->getPost('start_date');
            $endDate = $this->request->getPost('end_date');
            $allDay = $this->request->getPost('all_day');
            
            // Extrair ID real (remover prefixo)
            if (strpos($id, 'manual_') === 0) {
                $id = substr($id, 7);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Apenas eventos manuais podem ser movidos'
                ]);
            }
            
            if (!$id || !$startDate) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Dados incompletos'
                ]);
            }
            
            $event = $this->eventModel->find($id);
            
            if (!$event) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Evento não encontrado'
                ]);
            }
            
            $data = [
                'start_date' => $startDate,
                'end_date' => $endDate ?: $startDate,
                'all_day' => $allDay ? 1 : 0
            ];
            
            if ($this->eventModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data do evento atualizada'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao atualizar data',
                    'errors' => $this->eventModel->errors()
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar data do evento: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ]);
        }
    }
    
    /**
     * Get event color based on type
     */
    private function getEventColor($type)
    {
        $colors = [
            'Feriado' => '#dc3545',
            'Reunião' => '#28a745',
            'Prova' => '#fd7e14',
            'Entrega de Notas' => '#17a2b8',
            'Matrícula' => '#007bff',
            'Inscrição' => '#6f42c1',
            'Outro' => '#6c757d'
        ];
        
        return $colors[$type] ?? '#3788d8';
    }
}