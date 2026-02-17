<?php

namespace App\Models;

class CurrencyModel extends BaseModel
{
    protected $table = 'tbl_currencies';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'currency_name',
        'currency_code',
        'currency_symbol',
        'is_default'
    ];
    
    protected $validationRules = [
        'currency_name' => 'required|min_length[2]|max_length[100]',
        'currency_code' => 'required|min_length[3]|max_length[3]|is_unique[tbl_currencies.currency_code,id,{id}]',
        'currency_symbol' => 'required|max_length[10]'
    ];
    
    protected $validationMessages = [
        'currency_name' => [
            'required' => 'O nome da moeda é obrigatório'
        ],
        'currency_code' => [
            'required' => 'O código da moeda é obrigatório',
            'is_unique' => 'Este código de moeda já existe'
        ]
    ];
    
    /**
     * Get default currency
     */
    public function getDefault()
    {
        return $this->where('is_default', 1)->first();
    }
    
    /**
     * Set default currency
     */
    public function setDefault($id)
    {
        $this->transStart();
        
        // Remove default from all
        $this->where('is_default', 1)->set(['is_default' => 0])->update();
        
        // Set new default
        $this->update($id, ['is_default' => 1]);
        
        $this->transComplete();
        
        return $this->transStatus();
    }
}