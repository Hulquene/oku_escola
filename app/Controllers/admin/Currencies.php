<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\CurrencyModel;

class Currencies extends BaseController
{
    protected $currencyModel;
    
    public function __construct()
    {
        $this->currencyModel = new CurrencyModel();
    }
    
    /**
     * List currencies
     */
    public function currencies()
    {
        $data['title'] = 'Moedas';
        $data['currencies'] = $this->currencyModel
            ->orderBy('is_default', 'DESC')
            ->orderBy('currency_name', 'ASC')
            ->findAll();
        
        return view('admin/financial/currencies/index', $data);
    }
    
    /**
     * Save currency
     */
    public function save()
    {
        $rules = [
            'currency_name' => 'required',
            'currency_code' => 'required|min_length[3]|max_length[3]|is_unique[tbl_currencies.currency_code,id,{id}]',
            'currency_symbol' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'currency_name' => $this->request->getPost('currency_name'),
            'currency_code' => strtoupper($this->request->getPost('currency_code')),
            'currency_symbol' => $this->request->getPost('currency_symbol'),
            'is_default' => $this->request->getPost('is_default') ? 1 : 0
        ];
        
        $id = $this->request->getPost('id');
        
        $db = db_connect();
        $db->transStart();
        
        // If setting as default, remove default from others
        if ($data['is_default']) {
            $this->currencyModel->set(['is_default' => 0])->update();
        }
        
        if ($id) {
            $this->currencyModel->update($id, $data);
            $message = 'Moeda atualizada com sucesso';
        } else {
            $this->currencyModel->insert($data);
            $message = 'Moeda criada com sucesso';
        }
        
        $db->transComplete();
        
        if ($db->transStatus()) {
            return redirect()->to('/admin/financial/currencies')->with('success', $message);
        } else {
            return redirect()->back()->withInput()
                ->with('error', 'Erro ao salvar moeda');
        }
    }
    
    /**
     * Set as default currency
     */
    public function setDefault($id)
    {
        $currency = $this->currencyModel->find($id);
        
        if (!$currency) {
            return redirect()->back()->with('error', 'Moeda não encontrada');
        }
        
        $db = db_connect();
        $db->transStart();
        
        // Remove default from all
        $this->currencyModel->set(['is_default' => 0])->update();
        
        // Set new default
        $this->currencyModel->update($id, ['is_default' => 1]);
        
        $db->transComplete();
        
        if ($db->transStatus()) {
            return redirect()->back()->with('success', 'Moeda definida como padrão');
        } else {
            return redirect()->back()->with('error', 'Erro ao definir moeda padrão');
        }
    }
    
    /**
     * Delete currency
     */
    public function delete($id)
    {
        $currency = $this->currencyModel->find($id);
        
        if (!$currency) {
            return redirect()->back()->with('error', 'Moeda não encontrada');
        }
        
        if ($currency->is_default) {
            return redirect()->back()->with('error', 'Não é possível eliminar a moeda padrão');
        }
        
        // Check if used in fee structure
        $feeStructureModel = new \App\Models\FeeStructureModel();
        $used = $feeStructureModel->where('currency_id', $id)->countAllResults();
        
        if ($used > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível eliminar esta moeda pois está associada a estruturas de taxas');
        }
        
        $this->currencyModel->delete($id);
        
        return redirect()->to('/admin/financial/currencies')->with('success', 'Moeda eliminada com sucesso');
    }
}