<?php

namespace App\Models;

class TaxModel extends BaseModel
{
    protected $table = 'tbl_taxes';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'tax_name',
        'tax_code',
        'tax_rate',
        'description',
        'is_active'
    ];
    
    protected $validationRules = [
        'tax_name' => 'required',
        'tax_code' => 'required|is_unique[tbl_taxes.tax_code,id,{id}]',
        'tax_rate' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]'
    ];
    
    protected $validationMessages = [
        'tax_code' => [
            'is_unique' => 'Este código de taxa já existe'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get active taxes
     */
    public function getActive()
    {
        return $this->where('is_active', 1)
            ->orderBy('tax_rate', 'ASC')
            ->findAll();
    }
    
    /**
     * Get tax by code
     */
    public function getByCode($code)
    {
        return $this->where('tax_code', $code)->first();
    }
    
    /**
     * Get default tax (VAT)
     */
    public function getDefaultVat()
    {
        return $this->where('tax_code', 'IVA')
            ->where('is_active', 1)
            ->first();
    }
    
    /**
     * Calculate tax amount
     */
    public function calculateTax($amount, $taxId = null)
    {
        if ($taxId) {
            $tax = $this->find($taxId);
            $rate = $tax ? $tax->tax_rate : 0;
        } else {
            $rate = 0;
        }
        
        return $amount * ($rate / 100);
    }
    
    /**
     * Get tax usage statistics
     */
    public function getUsageStats()
    {
        $db = db_connect();
        
        return $db->table('tbl_taxes')
            ->select('tbl_taxes.tax_name, tbl_taxes.tax_rate, COUNT(tbl_invoice_items.id) as usage_count')
            ->join('tbl_invoice_items', 'tbl_invoice_items.tax_rate = tbl_taxes.tax_rate', 'left')
            ->groupBy('tbl_taxes.id')
            ->orderBy('usage_count', 'DESC')
            ->get()
            ->getResult();
    }
    
    /**
     * Toggle tax status
     */
    public function toggleStatus($id)
    {
        $tax = $this->find($id);
        
        if ($tax) {
            $newStatus = $tax->is_active ? 0 : 1;
            return $this->update($id, ['is_active' => $newStatus]);
        }
        
        return false;
    }
}