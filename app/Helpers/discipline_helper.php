<?php
// app/Helpers/discipline_helper.php

use App\Models\DisciplineModel;

if (!function_exists('getAvailableDisciplines')) {
    /**
     * Obtém disciplinas disponíveis (não atribuídas a um nível específico)
     * 
     * @param int $gradeLevelId ID do nível de ensino (opcional)
     * @param array $assignedIds Array com IDs das disciplinas já atribuídas
     * @param bool $onlyActive Apenas disciplinas ativas
     * @return array
     */
    function getAvailableDisciplines($gradeLevelId = null, $assignedIds = [], $onlyActive = true)
    {
        $disciplineModel = new DisciplineModel();
        
        $builder = $disciplineModel->select('
                id,
                discipline_name,
                discipline_code,
                discipline_type,
                workload_hours,
                min_grade,
                max_grade,
                approval_grade,
                description,
                is_active
            ');
        
        if ($onlyActive) {
            $builder->where('is_active', 1);
        }
        
        // Se temos IDs para excluir
        if (!empty($assignedIds)) {
            $builder->whereNotIn('id', $assignedIds);
        }
        
        return $builder->orderBy('discipline_name', 'ASC')
                       ->findAll();
    }
}

if (!function_exists('getAllDisciplines')) {
    /**
     * Obtém todas as disciplinas (com opção de filtro)
     * 
     * @param bool $onlyActive Apenas disciplinas ativas
     * @param string $orderBy Campo para ordenação
     * @param string $order Direção da ordenação (ASC ou DESC)
     * @return array
     */
    function getAllDisciplines($onlyActive = true, $orderBy = 'discipline_name', $order = 'ASC')
    {
        $disciplineModel = new DisciplineModel();
        
        $builder = $disciplineModel->select('
                id,
                discipline_name,
                discipline_code,
                discipline_type,
                workload_hours,
                min_grade,
                max_grade,
                approval_grade,
                description,
                is_active,
                created_at,
                updated_at
            ');
        
        if ($onlyActive) {
            $builder->where('is_active', 1);
        }
        
        return $builder->orderBy($orderBy, $order)
                       ->findAll();
    }
}

if (!function_exists('getDisciplineById')) {
    /**
     * Obtém uma disciplina pelo ID
     * 
     * @param int $id ID da disciplina
     * @return object|null
     */
    function getDisciplineById($id)
    {
        $disciplineModel = new DisciplineModel();
        return $disciplineModel->find($id);
    }
}

if (!function_exists('formatDisciplineForSelect')) {
    /**
     * Formata uma disciplina para uso em selects/options
     * 
     * @param object $discipline Objeto da disciplina
     * @return array
     */
    function formatDisciplineForSelect($discipline)
    {
        return [
            'id' => $discipline['id'],
            'name' => $discipline['discipline_name'],
            'code' => $discipline['discipline_code'],
            'type' => $discipline->discipline_type,
            'workload' => $discipline['workload_hours'] ?? 0,
            'min_grade' => $discipline->min_grade ?? 0,
            'max_grade' => $discipline->max_grade ?? 20,
            'approval' => $discipline->approval_grade ?? 10,
            'is_active' => $discipline->is_active ?? 1,
            'formatted_text' => $discipline['discipline_code'] . ' - ' . $discipline['discipline_name']
        ];
    }
}

if (!function_exists('searchDisciplines')) {
    /**
     * Busca disciplinas por termo (nome, código, tipo)
     * 
     * @param string $term Termo de busca
     * @param bool $onlyActive Apenas disciplinas ativas
     * @param int $limit Limite de resultados
     * @return array
     */
    function searchDisciplines($term, $onlyActive = true, $limit = 50)
    {
        $disciplineModel = new DisciplineModel();
        
        $builder = $disciplineModel->select('
                id,
                discipline_name,
                discipline_code,
                discipline_type,
                workload_hours,
                min_grade,
                max_grade,
                approval_grade,
                description,
                is_active
            ')
            ->groupStart()
                ->like('discipline_name', $term)
                ->orLike('discipline_code', $term)
                ->orLike('discipline_type', $term)
                ->orLike('description', $term)
            ->groupEnd();
        
        if ($onlyActive) {
            $builder->where('is_active', 1);
        }
        
        return $builder->orderBy('discipline_name', 'ASC')
                       ->limit($limit)
                       ->findAll();
    }
}

if (!function_exists('countDisciplinesByType')) {
    /**
     * Conta disciplinas por tipo
     * 
     * @param bool $onlyActive Apenas disciplinas ativas
     * @return array
     */
    function countDisciplinesByType($onlyActive = true)
    {
        $disciplineModel = new DisciplineModel();
        
        $builder = $disciplineModel->select('discipline_type, COUNT(*) as total');
        
        if ($onlyActive) {
            $builder->where('is_active', 1);
        }
        
        return $builder->groupBy('discipline_type')
                       ->findAll();
    }
}

if (!function_exists('getDisciplinesByType')) {
    /**
     * Obtém disciplinas por tipo
     * 
     * @param string $type Tipo da disciplina
     * @param bool $onlyActive Apenas disciplinas ativas
     * @return array
     */
    function getDisciplinesByType($type, $onlyActive = true)
    {
        $disciplineModel = new DisciplineModel();
        
        $builder = $disciplineModel->where('discipline_type', $type);
        
        if ($onlyActive) {
            $builder->where('is_active', 1);
        }
        
        return $builder->orderBy('discipline_name', 'ASC')
                       ->findAll();
    }
}