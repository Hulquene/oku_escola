<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\ExamBoardModel;

class ExamBoards extends BaseController
{
    protected $examBoardModel;
    
    public function __construct()
    {
        $this->examBoardModel = new ExamBoardModel();
    }
    
    /**
     * List exam boards
     */
    public function index()
    {
        $data['title'] = 'Tipos de Exames';
        $data['boards'] = $this->examBoardModel
            ->orderBy('board_type', 'ASC')
            ->orderBy('weight', 'DESC')
            ->findAll();
        
        return view('admin/exams/boards/index', $data);
    }
    
    /**
     * Save exam board
     */
    public function save()
    {
        $rules = [
            'board_name' => 'required',
            'board_code' => 'required|is_unique[tbl_exam_boards.board_code,id,{id}]',
            'board_type' => 'required',
            'weight' => 'permit_empty|numeric'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'board_name' => $this->request->getPost('board_name'),
            'board_code' => $this->request->getPost('board_code'),
            'board_type' => $this->request->getPost('board_type'),
            'weight' => $this->request->getPost('weight') ?: 1.00,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        $id = $this->request->getPost('id');
        
        if ($id) {
            $this->examBoardModel->update($id, $data);
            $message = 'Tipo de exame atualizado com sucesso';
        } else {
            $this->examBoardModel->insert($data);
            $message = 'Tipo de exame criado com sucesso';
        }
        
        return redirect()->to('/admin/exams/boards')->with('success', $message);
    }
    
    /**
     * Delete exam board
     */
    public function delete($id)
    {
        $board = $this->examBoardModel->find($id);
        
        if (!$board) {
            return redirect()->back()->with('error', 'Tipo de exame não encontrado');
        }
        
        // Check if used in exams
        $examModel = new \App\Models\ExamModel();
        $used = $examModel->where('exam_board_id', $id)->countAllResults();
        
        if ($used > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível eliminar este tipo pois está associado a exames');
        }
        
        $this->examBoardModel->delete($id);
        
        return redirect()->to('/admin/exams/boards')->with('success', 'Tipo de exame eliminado com sucesso');
    }
    
    /**
     * Get exam board details (AJAX)
     */
    public function getBoard($id)
    {
        $board = $this->examBoardModel->find($id);
        
        if (!$board) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tipo de exame não encontrado']);
        }
        
        return $this->response->setJSON(['success' => true, 'data' => $board]);
    }
}