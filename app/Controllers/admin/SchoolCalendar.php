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
        
        $data['selectedYear'] = $this->request->getGet('academic_year') ?: 
            ($this->academicYearModel->getCurrent()->id ?? null);
        
        return view('admin/academic/calendar/index', $data);
    }
    
    /**
     * Get events for calendar (AJAX)
     */
    public function getEvents()
    {
        $startDate = $this->request->getGet('start');
        $endDate = $this->request->getGet('end');
        $academicYearId = $this->request->getGet('academic_year');
        
        $events = $this->eventModel->getForCalendar($startDate, $endDate, $academicYearId);
        
        $formattedEvents = [];
        foreach ($events as $event) {
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
                    'created_by' => $event->first_name ? $event->first_name . ' ' . $event->last_name : 'Sistema'
                ]
            ];
        }
        
        return $this->response->setJSON($formattedEvents);
    }
    
    /**
     * Save event (AJAX)
     */
    public function saveEvent()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Requisição inválida'
            ]);
        }
        
        $rules = [
            'academic_year_id' => 'required|numeric',
            'event_title' => 'required',
            'event_type' => 'required',
            'start_date' => 'required|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $data = [
            'academic_year_id' => $this->request->getPost('academic_year_id'),
            'event_title' => $this->request->getPost('event_title'),
            'event_type' => $this->request->getPost('event_type'),
            'event_description' => $this->request->getPost('event_description'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date') ?: null,
            'start_time' => $this->request->getPost('start_time') ?: null,
            'end_time' => $this->request->getPost('end_time') ?: null,
            'location' => $this->request->getPost('location'),
            'all_day' => $this->request->getPost('all_day') ? 1 : 0,
            'color' => $this->request->getPost('color') ?: null,
            'created_by' => session()->get('user_id')
        ];
        
        $id = $this->request->getPost('id');
        
        if ($id) {
            $this->eventModel->update($id, $data);
            $message = 'Evento atualizado com sucesso';
        } else {
            $this->eventModel->insert($data);
            $message = 'Evento criado com sucesso';
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => $message
        ]);
    }
    
    /**
     * Delete event (AJAX)
     */
    public function deleteEvent($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
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
        
        $this->eventModel->delete($id);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Evento eliminado com sucesso'
        ]);
    }
    
    /**
     * Update event date (AJAX - for drag & drop)
     */
    public function updateDate()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Requisição inválida'
            ]);
        }
        
        $json = $this->request->getJSON();
        
        $event = $this->eventModel->find($json->id);
        
        if (!$event) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Evento não encontrado'
            ]);
        }
        
        $data = [
            'start_date' => $json->start_date,
            'end_date' => $json->end_date ?? $json->start_date,
            'all_day' => $json->all_day ?? $event->all_day
        ];
        
        $this->eventModel->update($json->id, $data);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data do evento atualizada'
        ]);
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