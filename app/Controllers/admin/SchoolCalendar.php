<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\SchoolEventModel;
use App\Models\AcademicYearModel;

class SchoolCalendar extends BaseController
{
    protected $eventModel;
    protected $academicYearModel;
    
    public function __construct()
    {
        $this->eventModel = new SchoolEventModel();
        $this->academicYearModel = new AcademicYearModel();
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
     * Get events for calendar (AJAX)
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
            
            $events = $this->eventModel->getForCalendar($startDate, $endDate, $academicYearId);
            
            $formattedEvents = [];
            foreach ($events as $event) {
                // Verificar se é array e acessar como array
                if (is_array($event)) {
                    $formattedEvents[] = [
                        'id' => $event['id'],
                        'title' => $event['event_title'],
                        'start' => $event['start_date'] . ($event['start_time'] ? 'T' . $event['start_time'] : ''),
                        'end' => ($event['end_date'] ?: $event['start_date']) . ($event['end_time'] ? 'T' . $event['end_time'] : ''),
                        'allDay' => (bool)$event['all_day'],
                        'backgroundColor' => $event['color'] ?: $this->getEventColor($event['event_type']),
                        'borderColor' => $event['color'] ?: $this->getEventColor($event['event_type']),
                        'textColor' => '#ffffff',
                        'extendedProps' => [
                            'event_type' => $event['event_type'],
                            'description' => $event['event_description'],
                            'location' => $event['location'],
                            'start_time' => $event['start_time'],
                            'end_time' => $event['end_time'],
                            'academic_year_id' => $event['academic_year_id'],
                            'created_by' => isset($event['first_name']) ? $event['first_name'] . ' ' . ($event['last_name'] ?? '') : 'Sistema'
                        ]
                    ];
                } else {
                    // Se for objeto (fallback)
                    $formattedEvents[] = [
                        'id' => $event->id,
                        'title' => $event->event_title,
                        'start' => $event->start_date . ($event->start_time ? 'T' . $event->start_time : ''),
                        'end' => ($event->end_date ?: $event->start_date) . ($event->end_time ? 'T' . $event->end_time : ''),
                        'allDay' => (bool)$event->all_day,
                        'backgroundColor' => $event->color ?: $this->getEventColor($event->event_type),
                        'borderColor' => $event->color ?: $this->getEventColor($event->event_type),
                        'textColor' => '#ffffff',
                        'extendedProps' => [
                            'event_type' => $event->event_type,
                            'description' => $event->event_description,
                            'location' => $event->location,
                            'start_time' => $event->start_time,
                            'end_time' => $event->end_time,
                            'academic_year_id' => $event->academic_year_id,
                            'created_by' => isset($event->first_name) ? $event->first_name . ' ' . ($event->last_name ?? '') : 'Sistema'
                        ]
                    ];
                }
            }
            
            return $this->response->setJSON($formattedEvents);
            
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar eventos: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([]);
        }
    }
    
    /**
     * Save event (AJAX)
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
     * Delete event (AJAX)
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
     * Update event date (AJAX - for drag & drop)
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