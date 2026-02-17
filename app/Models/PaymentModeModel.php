<?php

namespace App\Models;

class PaymentModeModel extends BaseModel
{
    protected $table = 'tbl_payment_modes';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'mode_name',
        'mode_code',
        'description',
        'is_active'
    ];
    
    protected $validationRules = [
        'mode_name' => 'required',
        'mode_code' => 'required|is_unique[tbl_payment_modes.mode_code,id,{id}]'
    ];
    
    protected $validationMessages = [
        'mode_code' => [
            'is_unique' => 'Este código de método de pagamento já existe'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get active payment modes
     */
    public function getActive()
    {
        return $this->where('is_active', 1)
            ->orderBy('mode_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get payment mode by code
     */
    public function getByCode($code)
    {
        return $this->where('mode_code', $code)->first();
    }
    
    /**
     * Get payment modes usage stats
     */
    public function getUsageStats($startDate = null, $endDate = null)
    {
        $db = db_connect();
        
        $builder = $db->table('tbl_payment_modes')
            ->select('tbl_payment_modes.mode_name, 
                     COUNT(tbl_payments.id) as usage_count,
                     SUM(tbl_payments.amount) as total_amount')
            ->join('tbl_payments', 'tbl_payments.payment_method = tbl_payment_modes.mode_code', 'left')
            ->groupBy('tbl_payment_modes.id');
        
        if ($startDate) {
            $builder->where('tbl_payments.payment_date >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('tbl_payments.payment_date <=', $endDate);
        }
        
        return $builder->orderBy('usage_count', 'DESC')
            ->get()
            ->getResult();
    }
}