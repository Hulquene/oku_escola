<?php

namespace App\Models;

use CodeIgniter\Model;

abstract class BaseModel extends Model
{
    protected $DBGroup = 'default';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [];
    
    // Datas
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    // Validação
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];
    
    /**
     * Get data formatted for datatables
     */
    public function getDatatable($columns, $searchableColumns, $orderColumn = 'id', $orderDir = 'desc')
    {
        $request = service('request');
        
        // Get parameters
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $search = $request->getPost('search')['value'];
        $order = $request->getPost('order');
        
        // Base query
        $builder = $this->builder();
        
        // Apply search
        if (!empty($search)) {
            $builder->groupStart();
            foreach ($searchableColumns as $column) {
                $builder->orLike($column, $search);
            }
            $builder->groupEnd();
        }
        
        // Get total records
        $recordsTotal = $this->countAllResults();
        $recordsFiltered = $builder->countAllResults(false);
        
        // Apply order
        if (!empty($order)) {
            $columnIndex = $order[0]['column'];
            $columnName = $columns[$columnIndex];
            $dir = $order[0]['dir'];
            $builder->orderBy($columnName, $dir);
        } else {
            $builder->orderBy($orderColumn, $orderDir);
        }
        
        // Apply limit
        if ($length != -1) {
            $builder->limit($length, $start);
        }
        
        // Get data
        $data = $builder->get()->getResult();
        
        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }
    
    /**
     * Generate unique code
     */
    protected function generateCode($prefix, $field, $length = 6)
    {
        $code = $prefix . strtoupper(substr(uniqid(), -$length));
        
        // Check if exists
        while ($this->where($field, $code)->first()) {
            $code = $prefix . strtoupper(substr(uniqid(), -$length));
        }
        
        return $code;
    }
}