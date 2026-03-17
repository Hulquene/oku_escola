<?php
// app/Models/ScheduleModel.php
namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table = 'tbl_schedules';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'class_id',
        'schedule_data',
        'total_items',
        'total_hours',
        'version',
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    
    /**
     * Busca ou cria um registro de horário para a turma
     */
    public function findOrCreateByClass($classId)
    {
        $schedule = $this->where('class_id', $classId)->first();
        
        if (!$schedule) {
            // Criar estrutura padrão
            $defaultSchedule = $this->getDefaultScheduleStructure();
            
            $data = [
                'class_id' => $classId,
                'schedule_data' => json_encode($defaultSchedule),
                'total_items' => 0,
                'total_hours' => 0,
                'version' => 1,
                'is_active' => 1
            ];
            
            $this->insert($data);
            $schedule = $this->where('class_id', $classId)->first();
        }
        
        // Garantir que schedule_data seja array
        if (is_string($schedule->schedule_data)) {
            $schedule->schedule_data = json_decode($schedule->schedule_data, true);
        }
        
        return $schedule;
    }
    
    /**
     * Atualiza o horário da turma
     */
    public function updateSchedule($classId, $scheduleData)
    {
        $schedule = $this->where('class_id', $classId)->first();
        
        if (!$schedule) {
            return $this->insert([
                'class_id' => $classId,
                'schedule_data' => json_encode($scheduleData),
                'total_items' => $this->countItems($scheduleData),
                'total_hours' => $this->calculateTotalHours($scheduleData),
                'version' => 1,
                'is_active' => 1
            ]);
        }
        
        return $this->update($schedule['id'], [
            'schedule_data' => json_encode($scheduleData),
            'total_items' => $this->countItems($scheduleData),
            'total_hours' => $this->calculateTotalHours($scheduleData)
        ]);
    }
    
    /**
     * Retorna a estrutura padrão do horário
     */
    public function getDefaultScheduleStructure()
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $periods = ['1', '2', '3', '4', '5', '6'];
        
        $schedule = [];
        foreach ($days as $day) {
            $schedule[$day] = [];
            foreach ($periods as $period) {
                $schedule[$day][$period] = [];
            }
        }
        
        return $schedule;
    }
    
    /**
     * Conta o número total de itens no horário
     */
    private function countItems($scheduleData)
    {
        $count = 0;
        foreach ($scheduleData as $day => $periods) {
            foreach ($periods as $period => $items) {
                $count += count($items);
            }
        }
        return $count;
    }
    
    /**
     * Calcula o total de horas no horário
     */
    private function calculateTotalHours($scheduleData)
    {
        $totalHours = 0;
        foreach ($scheduleData as $day => $periods) {
            foreach ($periods as $period => $items) {
                foreach ($items as $item) {
                    if (isset($item['start_time']) && isset($item['end_time'])) {
                        $start = strtotime($item['start_time']);
                        $end = strtotime($item['end_time']);
                        $totalHours += ($end - $start) / 3600;
                    }
                }
            }
        }
        return round($totalHours, 2);
    }
}