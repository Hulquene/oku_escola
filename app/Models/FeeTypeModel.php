<?php

namespace App\Models;

class FeeTypeModel extends BaseModel
{
    protected $table = 'tbl_fee_types';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'type_name',
        'type_code',
        'type_category',
        'is_recurring',
        'recurrence_period',
        'description',
        'is_active'
    ];
    
    protected $validationRules = [
        'type_name' => 'required',
        'type_code' => 'required|is_unique[tbl_fee_types.type_code,id,{id}]',
        'type_category' => 'required'
    ];
    
    /**
     * Get active fee types
     */
    public function getActive()
    {
        return $this->where('is_active', 1)
            ->orderBy('type_category', 'ASC')
            ->orderBy('type_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get recurring fees
     */
    public function getRecurring()
    {
        return $this->where('is_recurring', 1)
            ->where('is_active', 1)
            ->findAll();
    }
}