<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;

class Users extends BaseController
{
    protected $userModel;
    protected $roleModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }
    
    /**
     * List users
     */
    public function index()
    {
        $data['title'] = 'Utilizadores';
        $data['users'] = $this->userModel
            ->select('tbl_users.*, tbl_roles.role_name')
            ->join('tbl_roles', 'tbl_roles.id = tbl_users.role_id')
            ->orderBy('tbl_users.created_at', 'DESC')
            ->paginate(10);
        
        $data['pager'] = $this->userModel->pager;
        
        return view('admin/users/index', $data);
    }
    
    /**
     * Get users for datatable
     */
    public function get_users_table()
    {
        if (!$this->request->isAJAX()) {
            return $this->respondWithError('Requisição inválida');
        }
        
        $datatable = $this->getDatatableRequest();
        
        $columns = ['id', 'username', 'first_name', 'last_name', 'email', 'role_name', 'is_active', 'last_login'];
        $searchable = ['username', 'first_name', 'last_name', 'email'];
        
        $builder = $this->userModel
            ->select('tbl_users.*, tbl_roles.role_name')
            ->join('tbl_roles', 'tbl_roles.id = tbl_users.role_id');
        
        // Apply search
        if (!empty($datatable['search'])) {
            $builder->groupStart();
            foreach ($searchable as $field) {
                $builder->orLike('tbl_users.' . $field, $datatable['search']);
            }
            $builder->groupEnd();
        }
        
        // Get total records
        $recordsTotal = $this->userModel->countAllResults();
        $recordsFiltered = $builder->countAllResults(false);
        
        // Apply order
        if (!empty($datatable['order'])) {
            $order = $datatable['order'][0];
            $columnIndex = $order['column'];
            $columnName = $columns[$columnIndex] ?? 'id';
            $dir = $order['dir'];
            $builder->orderBy('tbl_users.' . $columnName, $dir);
        } else {
            $builder->orderBy('tbl_users.id', 'DESC');
        }
        
        // Apply limit
        $builder->limit($datatable['length'], $datatable['start']);
        
        $data = $builder->get()->getResult();
        
        return $this->respondWithJson([
            'draw' => $datatable['draw'],
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }
    
    /**
     * User form (add/edit)
     */
    public function form($id = null)
    {
        $data['title'] = $id ? 'Editar Utilizador' : 'Novo Utilizador';
        $data['user'] = $id ? $this->userModel->find($id) : null;
        $data['roles'] = $this->roleModel->findAll();
        
        return view('admin/users/form', $data);
    }
    
    /**
     * Save user
     */
    public function save()
    {
        $rules = [
            'username' => 'required|min_length[3]|is_unique[tbl_users.username,id,{id}]',
            'email' => 'required|valid_email|is_unique[tbl_users.email,id,{id}]',
            'first_name' => 'required',
            'last_name' => 'required',
            'role_id' => 'required|numeric'
        ];
        
        // Password required for new users
        if (!$this->request->getPost('id')) {
            $rules['password'] = 'required|min_length[6]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'role_id' => $this->request->getPost('role_id'),
            'user_type' => $this->request->getPost('user_type') ?? 'staff',
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        // Set password if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        $id = $this->request->getPost('id');
        
        if ($id) {
            $this->userModel->update($id, $data);
            $message = 'Utilizador atualizado com sucesso';
        } else {
            $this->userModel->insert($data);
            $message = 'Utilizador criado com sucesso';
        }
        
        return redirect()->to('/admin/users')->with('success', $message);
    }
    
    /**
     * Delete user
     */
    public function delete($id)
    {
        if (!$this->userModel->find($id)) {
            return redirect()->back()->with('error', 'Utilizador não encontrado');
        }
        
        // Don't allow deleting yourself
        if ($id == $this->session->get('user_id')) {
            return redirect()->back()->with('error', 'Não é possível eliminar o próprio utilizador');
        }
        
        $this->userModel->delete($id);
        
        return redirect()->to('/admin/users')->with('success', 'Utilizador eliminado com sucesso');
    }
    
    /**
     * User profile
     */
    public function profile()
    {
        $data['title'] = 'Meu Perfil';
        $data['user'] = $this->userModel->find($this->session->get('user_id'));
        
        return view('admin/users/profile', $data);
    }
    
    /**
     * View user details
     */
    public function view_user($id)
    {
        $data['title'] = 'Detalhes do Utilizador';
        $data['user'] = $this->userModel
            ->select('tbl_users.*, tbl_roles.role_name')
            ->join('tbl_roles', 'tbl_roles.id = tbl_users.role_id')
            ->where('tbl_users.id', $id)
            ->first();
        
        if (!$data['user']) {
            return redirect()->to('/admin/users')->with('error', 'Utilizador não encontrado');
        }
        
        return view('admin/users/view', $data);
    }
}