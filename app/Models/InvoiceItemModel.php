<?php

namespace App\Models;

class InvoiceItemModel extends BaseModel
{
    protected $table = 'tbl_invoice_items';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'discount_rate',
        'discount_amount',
        'total',
        'fee_type_id'
    ];
    
    protected $validationRules = [
        'invoice_id' => 'required|numeric',
        'description' => 'required',
        'quantity' => 'required|numeric|greater_than[0]',
        'unit_price' => 'required|numeric|greater_than_equal_to[0]',
        'total' => 'required|numeric'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get items by invoice
     */
    public function getByInvoice($invoiceId)
    {
        return $this->select('tbl_invoice_items.*, tbl_fee_types.type_name')
            ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_invoice_items.fee_type_id', 'left')
            ->where('tbl_invoice_items.invoice_id', $invoiceId)
            ->orderBy('tbl_invoice_items.id', 'ASC')
            ->findAll();
    }
    
    /**
     * Calculate item total
     */
    public function calculateTotal($quantity, $unitPrice, $taxRate = 0, $discountRate = 0)
    {
        $subtotal = $quantity * $unitPrice;
        $taxAmount = $subtotal * ($taxRate / 100);
        $discountAmount = $subtotal * ($discountRate / 100);
        
        return $subtotal + $taxAmount - $discountAmount;
    }
    
    /**
     * Bulk insert items
     */
    public function bulkInsert($invoiceId, array $items)
    {
        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'invoice_id' => $invoiceId,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'] ?? 0,
                'tax_amount' => $item['tax_amount'] ?? 0,
                'discount_rate' => $item['discount_rate'] ?? 0,
                'discount_amount' => $item['discount_amount'] ?? 0,
                'total' => $item['total'],
                'fee_type_id' => $item['fee_type_id'] ?? null
            ];
        }
        
        return $this->insertBatch($data);
    }
}