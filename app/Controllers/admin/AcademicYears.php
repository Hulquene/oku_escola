<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\AcademicYearModel;

class AcademicYears extends BaseController
{
    protected $academicYearModel;
    
    public function __construct()
    {
        $this->academicYearModel = new AcademicYearModel();
    }
    
    /**
     * List academic years
     */
    public function index()
    {
        $data['title'] = 'Anos Letivos';
        $data['years'] = $this->academicYearModel
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        return view('admin/academic/years/index', $data);
    }
    
    /**
     * Academic year form
     */
    public function form($id = null)
    {
        $data['title'] = $id ? 'Editar Ano Letivo' : 'Novo Ano Letivo';
        $data['year'] = $id ? $this->academicYearModel->find($id) : null;
        
        return view('admin/academic/years/form', $data);
    }
    
    /**
     * Save academic year - VERSÃO COM MENSAGENS DETALHADAS
     */
    public function save()
    {
        $id = $this->request->getPost('id');
        
        $data = [
            'id' => $id,  // Incluir o ID nos dados
            'year_name' => $this->request->getPost('year_name'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        // Validação adicional: data de fim não pode ser anterior à data de início
        if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
            return redirect()->back()->withInput()
                ->with('error', 'A data de fim não pode ser anterior à data de início.');
        }
        
        $isCurrent = $this->request->getPost('is_current');
        
        if ($id) {
            // ATUALIZAÇÃO - usar o método save() do model que já lida com placeholders
            if ($this->academicYearModel->save($data)) {
                if ($isCurrent) {
                    $this->academicYearModel->setCurrent($id);
                }
                return redirect()->to('/admin/academic/years')
                    ->with('success', "Ano letivo '{$data['year_name']}' atualizado com sucesso!");
            } else {
                $errors = $this->academicYearModel->errors();
                if (!empty($errors)) {
                    return redirect()->back()->withInput()
                        ->with('errors', $errors);
                }
                return redirect()->back()->withInput()
                    ->with('error', 'Erro ao atualizar ano letivo');
            }
        } else {
            // INSERÇÃO
            unset($data['id']);
            $newId = $this->academicYearModel->insert($data);
            
            if ($newId) {
                if ($isCurrent) {
                    $this->academicYearModel->setCurrent($newId);
                }
                return redirect()->to('/admin/academic/years')
                    ->with('success', "Ano letivo '{$data['year_name']}' criado com sucesso!");
            } else {
                $errors = $this->academicYearModel->errors();
                if (!empty($errors)) {
                    return redirect()->back()->withInput()
                        ->with('errors', $errors);
                }
                return redirect()->back()->withInput()
                    ->with('error', 'Erro ao criar ano letivo');
            }
        }
    }
    /**
     * Set current academic year
     */
    public function setCurrent($id)
    {
        $year = $this->academicYearModel->find($id);
        
        if (!$year) {
            return redirect()->back()->with('error', 'Ano letivo não encontrado.');
        }
        
        if ($this->academicYearModel->setCurrent($id)) {
            log_message('info', "Ano letivo ID {$id} ('{$year->year_name}') definido como atual");
            return redirect()->back()->with('success', "Ano letivo '{$year->year_name}' definido como atual.");
        } else {
            $errors = $this->academicYearModel->errors();
            if (!empty($errors)) {
                return redirect()->back()->with('error', 'Erro ao definir ano letivo: ' . implode(', ', $errors));
            }
            return redirect()->back()->with('error', 'Erro ao definir ano letivo.');
        }
    }
    
    /**
     * Delete academic year
     */
    public function delete($id)
    {
        $year = $this->academicYearModel->find($id);
        
        if (!$year) {
            return redirect()->back()->with('error', 'Ano letivo não encontrado.');
        }
        
        if ($year->is_current) {
            return redirect()->back()->with('error', 'Não é possível eliminar o ano letivo atual. Defina outro ano como atual primeiro.');
        }
        
        // Verificar se existem matrículas associadas
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrollmentsCount = $enrollmentModel->where('academic_year_id', $id)->countAllResults();
        
        if ($enrollmentsCount > 0) {
            return redirect()->back()->with('error', "Não é possível eliminar este ano letivo porque existem {$enrollmentsCount} matrículas associadas a ele.");
        }
        
        if ($this->academicYearModel->delete($id)) {
            log_message('info', "Ano letivo ID {$id} ('{$year->year_name}') eliminado com sucesso");
            return redirect()->to('/admin/academic/years')->with('success', "Ano letivo '{$year->year_name}' eliminado com sucesso.");
        } else {
            $errors = $this->academicYearModel->errors();
            if (!empty($errors)) {
                return redirect()->back()->with('error', 'Erro ao eliminar ano letivo: ' . implode(', ', $errors));
            }
            return redirect()->back()->with('error', 'Erro ao eliminar ano letivo.');
        }
    }
     /**
     * Check if dates are within academic year (AJAX)
     */
    /**
     * Check if dates are within academic year
     */
    public function checkDates($id)
    {
        // REMOVA esta verificação - não precisa forçar AJAX
        // if (!$this->request->isAJAX()) {
        //     return $this->response->setJSON(['valid' => false, 'message' => 'Requisição inválida']);
        // }
        
        $year = $this->academicYearModel->find($id);
        
        if (!$year) {
            return $this->response->setJSON(['valid' => false, 'message' => 'Ano letivo não encontrado']);
        }
        
        $startDate = $this->request->getGet('start');
        $endDate = $this->request->getGet('end');
        
        if (!$startDate || !$endDate) {
            return $this->response->setJSON(['valid' => true]);
        }
        
        $valid = true;
        $message = '';
        
        if ($startDate < $year->start_date) {
            $valid = false;
            $message = 'A data de início não pode ser anterior ao início do ano letivo (' . date('d/m/Y', strtotime($year->start_date)) . ')';
        } elseif ($endDate > $year->end_date) {
            $valid = false;
            $message = 'A data de fim não pode ser posterior ao fim do ano letivo (' . date('d/m/Y', strtotime($year->end_date)) . ')';
        }
        
        return $this->response->setJSON([
            'valid' => $valid,
            'message' => $message
        ]);
    }
}