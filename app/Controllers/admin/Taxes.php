<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\TaxModel;

class Taxes extends BaseController
{
    protected $taxModel;
    
    public function __construct()
    {
        $this->taxModel = new TaxModel();
    }
    
    /**
     * List taxes
     */
    public function index()
    {
        $data['title'] = 'Taxas e Impostos';
        $data['taxes'] = $this->taxModel
            ->orderBy('tax_rate', 'ASC')
            ->findAll();
        
        return view('admin/financial/taxes/index', $data);
    }
    
    /**
     * Save tax
     */
    public function save()
    {
        $rules = [
            'tax_name' => 'required',
            'tax_code' => 'required|is_unique[tbl_taxes.tax_code,id,{id}]',
            'tax_rate' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'tax_name' => $this->request->getPost('tax_name'),
            'tax_code' => strtoupper($this->request->getPost('tax_code')),
            'tax_rate' => $this->request->getPost('tax_rate'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        $id = $this->request->getPost('id');
        
        if ($id) {
            $this->taxModel->update($id, $data);
            $message = 'Taxa atualizada com sucesso';
        } else {
            $this->taxModel->insert($data);
            $message = 'Taxa criada com sucesso';
        }
        
        return redirect()->to('/admin/financial/taxes')->with('success', $message);
    }
    
    /**
     * Delete tax
     */
    public function delete($id)
    {
        $tax = $this->taxModel->find($id);
        
        if (!$tax) {
            return redirect()->back()->with('error', 'Taxa não encontrada');
        }
        
        // Check if used in invoice items
        $invoiceItemModel = new \App\Models\InvoiceItemModel();
        $used = $invoiceItemModel->where('tax_rate', $tax->tax_rate)->countAllResults();
        
        if ($used > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível eliminar esta taxa pois está associada a faturas');
        }
        
        $this->taxModel->delete($id);
        
        return redirect()->to('/admin/financial/taxes')->with('success', 'Taxa eliminada com sucesso');
    }
    
    /**
     * Toggle tax status
     */
    public function toggleStatus($id)
    {
        $tax = $this->taxModel->find($id);
        
        if (!$tax) {
            return $this->response->setJSON(['success' => false, 'message' => 'Taxa não encontrada']);
        }
        
        $newStatus = $tax->is_active ? 0 : 1;
        $this->taxModel->update($id, ['is_active' => $newStatus]);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Status atualizado com sucesso',
            'is_active' => $newStatus
        ]);
    }
}