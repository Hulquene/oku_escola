<?php

namespace App\Models;

class ClassDisciplineModel extends BaseModel
{
    protected $table = 'tbl_class_disciplines';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'class_id',
        'discipline_id',
        'teacher_id',
        'workload_hours',
        'period_type',
        'is_active'
    ];
    
    protected $validationRules = [
        'class_id' => 'required|numeric',
        'discipline_id' => 'required|numeric',
        'teacher_id' => 'permit_empty|numeric',
        'period_type' => 'required|in_list[Anual,1º Semestre,2º Semestre]',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'period_type' => [
            'required' => 'O tipo de período é obrigatório',
            'in_list' => 'O tipo de período deve ser: Anual, 1º Semestre ou 2º Semestre'
        ]
    ];
    
    /**
     * Assign teacher to discipline
     */
    public function assignTeacher($classId, $disciplineId, $teacherId)
    {
        return $this->where('class_id', $classId)
            ->where('discipline_id', $disciplineId)
            ->set(['teacher_id' => $teacherId])
            ->update();
    }
    
    /**
     * Get disciplines by class with teacher info
     */
    public function getClassDisciplines($classId)
    {
        return $this->select('
                tbl_class_disciplines.*, 
                tbl_disciplines.discipline_name, 
                tbl_disciplines.discipline_code, 
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as teacher_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->orderBy('tbl_class_disciplines.period_type', 'ASC')
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get teacher schedule
     * Agora usando period_type em vez de semester_id
     */
    public function getTeacherSchedule($teacherId, $periodType = null)
    {
        $builder = $this->select('
                tbl_class_disciplines.*, 
                tbl_classes.class_name, 
                tbl_classes.class_shift,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->where('tbl_class_disciplines.is_active', 1);
        
        if ($periodType) {
            $builder->where('tbl_class_disciplines.period_type', $periodType);
        }
        
        return $builder->orderBy('tbl_classes.class_name', 'ASC')
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get disciplines by class and period type
     * Substitui o antigo getByClassAndSemester
     */
    public function getByClassAndPeriod($classId, $periodType)
    {
        return $this->select('
                tbl_class_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as teacher_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.period_type', $periodType)
            ->where('tbl_class_disciplines.is_active', 1)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get disciplines by class with period type
     */
    public function getByClassWithPeriod($classId, $periodType = null)
    {
        $builder = $this->select('
                tbl_class_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as teacher_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.is_active', 1);
        
        if ($periodType) {
            $builder->where('tbl_class_disciplines.period_type', $periodType);
        }
        
        return $builder->orderBy('tbl_class_disciplines.period_type', 'ASC')
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get disciplines grouped by period type
     */
    public function getGroupedByPeriod($classId)
    {
        $disciplines = $this->select('
                tbl_class_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as teacher_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        $grouped = [
            'Anual' => [],
            '1º Semestre' => [],
            '2º Semestre' => []
        ];
        
        foreach ($disciplines as $discipline) {
            $period = $discipline->period_type ?? 'Anual';
            $grouped[$period][] = $discipline;
        }
        
        return $grouped;
    }
    
    /**
     * Check if discipline exists in class for a specific period
     */
    public function existsInClass($classId, $disciplineId, $periodType = null)
    {
        $builder = $this->where('class_id', $classId)
            ->where('discipline_id', $disciplineId);
        
        if ($periodType) {
            $builder->where('period_type', $periodType);
        }
        
        return $builder->countAllResults() > 0;
    }
    
    /**
     * Bulk insert disciplines from curriculum
     */
    public function bulkInsertFromCurriculum($classId, array $disciplines)
    {
        $data = [];
        foreach ($disciplines as $disc) {
            // Converter valores do formulário para ENUM
            $periodMap = [
                'Anual' => 'Anual',
                '1' => '1º Semestre',
                '2' => '2º Semestre',
                '3' => 'Anual' // 3º trimestre como Anual
            ];
            
            $semesterValue = $disc['semester'] ?? 'Anual';
            $periodType = $periodMap[$semesterValue] ?? 'Anual';
            
            $data[] = [
                'class_id' => $classId,
                'discipline_id' => $disc['discipline_id'],
                'workload_hours' => $disc['workload_hours'] ?? null,
                'period_type' => $periodType,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
        
        return $this->insertBatch($data);
    }
    
    /**
     * Get disciplines available for a specific period
     * Útil para saber quais disciplinas estão disponíveis em cada trimestre
     */
    public function getForPeriod($classId, $periodNumber)
    {
        // periodNumber: 1, 2, 3 (trimestres)
        $periodMap = [
            'Anual'  => 'Anual',
            1 => '1º Semestre',
            2 => '2º Semestre',
            3 => 'Anual' // 3º trimestre considera também as anuais
        ];
        
        $periodType = $periodMap[$periodNumber] ?? null;
        
        if (!$periodType) {
            return [];
        }
        
        $builder = $this->select('
                tbl_class_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as teacher_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.is_active', 1);
        
        // Para o 3º trimestre, inclui também as disciplinas anuais
        if ($periodNumber == 3) {
            $builder->whereIn('tbl_class_disciplines.period_type', ['Anual', '1º Semestre', '2º Semestre']);
        } else {
            $builder->where('tbl_class_disciplines.period_type', $periodType);
        }
        
        return $builder->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Count disciplines by period type
     */
    public function countByPeriodType($classId)
    {
        $result = $this->select('
                period_type,
                COUNT(*) as total
            ')
            ->where('class_id', $classId)
            ->where('is_active', 1)
            ->groupBy('period_type')
            ->findAll();
        
        $counts = [
            'Anual' => 0,
            '1º Semestre' => 0,
            '2º Semestre' => 0
        ];
        
        foreach ($result as $row) {
            $counts[$row->period_type] = $row->total;
        }
        
        return $counts;
    }
    /**
     * Verificar se uma disciplina está disponível em um determinado trimestre
     * 
     * @param int $classDisciplineId ID da atribuição
     * @param int $trimesterNumber Número do trimestre (1, 2, 3)
     * @return bool
     */
    public function isAvailableInTrimester($classDisciplineId, $trimesterNumber)
    {
        $discipline = $this->find($classDisciplineId);
        
        if (!$discipline) {
            return false;
        }
        
        switch ($discipline->period_type) {
            case 'Anual':
                return in_array($trimesterNumber, [1, 2, 3]);
            case '1º Semestre':
                return $trimesterNumber == 1;
            case '2º Semestre':
                return $trimesterNumber == 2;
            default:
                return false;
        }
    }
}