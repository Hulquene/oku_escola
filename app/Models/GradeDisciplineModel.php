<?php
// app/Models/GradeDisciplineModel.php

namespace App\Models;

use CodeIgniter\Model;

class GradeDisciplineModel extends Model
{
    protected $table = 'tbl_grade_disciplines';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType = 'object'; // Pode ser 'array' ou 'object'
    protected $useSoftDeletes = false; // Não usamos soft delete
    
    protected $allowedFields = [
        'grade_level_id',
        'discipline_id',
        'workload_hours',
        'is_mandatory',
        'semester'
    ];
    
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Validation
    protected $validationRules = [
        'grade_level_id' => 'required|is_natural_no_zero',
        'discipline_id'  => 'required|is_natural_no_zero',
        'workload_hours' => 'permit_empty|is_natural|less_than[1000]',
        'is_mandatory'   => 'permit_empty|in_list[0,1]',
        'semester'       => 'permit_empty|in_list[Anual,1º Semestre,2º Semestre]'
    ];
    
    protected $validationMessages = [
        'grade_level_id' => [
            'required' => 'O nível de ensino é obrigatório',
            'is_natural_no_zero' => 'Nível de ensino inválido'
        ],
        'discipline_id' => [
            'required' => 'A disciplina é obrigatória',
            'is_natural_no_zero' => 'Disciplina inválida'
        ],
        'workload_hours' => [
            'is_natural' => 'A carga horária deve ser um número positivo',
            'less_than' => 'A carga horária não pode exceder 1000 horas'
        ],
        'semester' => [
            'in_list' => 'O período deve ser Anual, 1º Semestre ou 2º Semestre'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Busca disciplinas por nível de ensino com informações completas
     * 
     * @param int $gradeLevelId ID do nível de ensino
     * @param bool $onlyMandatory Se true, retorna apenas obrigatórias
     * @param bool $withDefaultWorkload Se true, inclui carga horária padrão da disciplina
     * @return array
     */
    public function getDisciplinesByGradeLevel(int $gradeLevelId, bool $onlyMandatory = false, bool $withDefaultWorkload = true)
    {
        $builder = $this->select('
                tbl_grade_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_disciplines.workload_hours as default_workload,
                tbl_disciplines.approval_grade,
                tbl_disciplines.discipline_type,
                tbl_disciplines.min_grade,
                tbl_disciplines.max_grade,
                tbl_grade_levels.level_name,
                tbl_grade_levels.level_code,
                tbl_grade_levels.education_level
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_grade_disciplines.discipline_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_grade_disciplines.grade_level_id')
            ->where('tbl_grade_disciplines.grade_level_id', $gradeLevelId)
            ->where('tbl_disciplines.is_active', 1);
        
        if ($onlyMandatory) {
            $builder->where('tbl_grade_disciplines.is_mandatory', 1);
        }
        
        return $builder->orderBy('tbl_disciplines.discipline_name', 'ASC')
                      ->findAll();
    }
    
    /**
     * Verifica se uma disciplina já está associada a um nível
     * 
     * @param int $gradeLevelId
     * @param int $disciplineId
     * @return bool
     */
    public function exists(int $gradeLevelId, int $disciplineId): bool
    {
        return $this->where('grade_level_id', $gradeLevelId)
                    ->where('discipline_id', $disciplineId)
                    ->countAllResults() > 0;
    }
    
    /**
     * Remove todas as disciplinas de um nível
     * 
     * @param int $gradeLevelId
     * @return bool
     */
    public function deleteByGradeLevel(int $gradeLevelId): bool
    {
        return $this->where('grade_level_id', $gradeLevelId)
                    ->delete();
    }
    
    /**
     * Obtém a carga horária total de um nível
     * 
     * @param int $gradeLevelId
     * @return int
     */
    public function getTotalWorkload(int $gradeLevelId): int
    {
        $result = $this->select('SUM(COALESCE(workload_hours, 
                    (SELECT workload_hours FROM tbl_disciplines WHERE id = discipline_id), 0)) as total')
                    ->where('grade_level_id', $gradeLevelId)
                    ->first();
        
        return (int)($result->total ?? 0);
    }
    
    /**
     * Conta disciplinas por período
     * 
     * @param int $gradeLevelId
     * @return array
     */
    public function countBySemester(int $gradeLevelId): array
    {
        return $this->select('semester, COUNT(*) as total')
                    ->where('grade_level_id', $gradeLevelId)
                    ->groupBy('semester')
                    ->findAll();
    }
    
    /**
     * Busca disciplinas não associadas a um nível (para dropdown)
     * 
     * @param int $gradeLevelId
     * @return array
     */
    public function getAvailableDisciplines(int $gradeLevelId): array
    {
        $db = \Config\Database::connect();
        
        $sql = "SELECT d.* 
                FROM tbl_disciplines d
                WHERE d.is_active = 1
                AND d.id NOT IN (
                    SELECT gd.discipline_id 
                    FROM tbl_grade_disciplines gd 
                    WHERE gd.grade_level_id = ?
                )
                ORDER BY d.discipline_name ASC";
        
        $query = $db->query($sql, [$gradeLevelId]);
        return $query->getResult();
    }
    
    /**
     * Obtém uma disciplina com todos os detalhes
     * 
     * @param int $id
     * @return object|null
     */
    public function getDisciplineDetails(int $id)
    {
        return $this->select('
                tbl_grade_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_disciplines.workload_hours as default_workload,
                tbl_disciplines.approval_grade,
                tbl_disciplines.discipline_type,
                tbl_grade_levels.level_name,
                tbl_grade_levels.education_level
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_grade_disciplines.discipline_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_grade_disciplines.grade_level_id')
            ->where('tbl_grade_disciplines.id', $id)
            ->first();
    }
    
    /**
     * Duplica configurações de um nível para outro
     * 
     * @param int $sourceLevelId Nível de origem
     * @param int $targetLevelId Nível de destino
     * @return int Número de registros inseridos
     */
    public function duplicateFromLevel(int $sourceLevelId, int $targetLevelId): int
    {
        $sourceDisciplines = $this->where('grade_level_id', $sourceLevelId)->findAll();
        
        if (empty($sourceDisciplines)) {
            return 0;
        }
        
        $insertData = [];
        foreach ($sourceDisciplines as $item) {
            // Verifica se já não existe no destino
            if (!$this->exists($targetLevelId, $item->discipline_id)) {
                $insertData[] = [
                    'grade_level_id' => $targetLevelId,
                    'discipline_id' => $item->discipline_id,
                    'workload_hours' => $item->workload_hours,
                    'is_mandatory' => $item->is_mandatory,
                    'semester' => $item->semester
                ];
            }
        }
        
        if (!empty($insertData)) {
            return $this->insertBatch($insertData);
        }
        
        return 0;
    }
    
    /**
     * Obtém estatísticas por nível
     * 
     * @param int $gradeLevelId
     * @return array
     */
    public function getStatistics(int $gradeLevelId): array
    {
        $total = $this->where('grade_level_id', $gradeLevelId)->countAllResults();
        
        $mandatory = $this->where('grade_level_id', $gradeLevelId)
                          ->where('is_mandatory', 1)
                          ->countAllResults();
        
        $optional = $total - $mandatory;
        
        $bySemester = $this->countBySemester($gradeLevelId);
        
        $workload = $this->getTotalWorkload($gradeLevelId);
        
        return [
            'total' => $total,
            'mandatory' => $mandatory,
            'optional' => $optional,
            'workload' => $workload,
            'by_semester' => $bySemester
        ];
    }
    
    /**
     * Busca disciplinas por período
     * 
     * @param int $gradeLevelId
     * @param string $semester
     * @return array
     */
    public function getDisciplinesBySemester(int $gradeLevelId, string $semester)
    {
        return $this->select('
                tbl_grade_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_grade_disciplines.discipline_id')
            ->where('tbl_grade_disciplines.grade_level_id', $gradeLevelId)
            ->where('tbl_grade_disciplines.semester', $semester)
            ->where('tbl_disciplines.is_active', 1)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Atualiza em lote as disciplinas de um nível
     * 
     * @param int $gradeLevelId
     * @param array $disciplines Array de disciplinas com dados
     * @return bool
     */
    public function bulkUpdate(int $gradeLevelId, array $disciplines): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Remove todas as existentes
        $this->where('grade_level_id', $gradeLevelId)->delete();
        
        // Insere as novas
        if (!empty($disciplines)) {
            $insertData = array_map(function($item) use ($gradeLevelId) {
                return [
                    'grade_level_id' => $gradeLevelId,
                    'discipline_id' => $item['discipline_id'],
                    'workload_hours' => $item['workload_hours'] ?? null,
                    'is_mandatory' => $item['is_mandatory'] ?? 1,
                    'semester' => $item['semester'] ?? 'Anual'
                ];
            }, $disciplines);
            
            $this->insertBatch($insertData);
        }
        
        $db->transComplete();
        
        return $db->transStatus();
    }
}