<?php
// app/Controllers/guardians/Profile.php
namespace App\Controllers\guardians;

use App\Controllers\BaseController;

class Profile extends BaseController
{
    public function index()
    {
        $guardianId = getGuardianIdFromUser();
        
        if (!$guardianId) {
            return redirect()->to('/guardians/auth/logout')
                ->with('error', 'Perfil de encarregado não encontrado.');
        }
        
        $db = db_connect();
        
        // Buscar dados do guardião
        $guardian = $db->table('tbl_guardians')
            ->where('id', $guardianId)
            ->get()
            ->getRowArray();
        
        // Buscar dados do usuário
        $user = $db->table('tbl_users')
            ->where('id', currentUserId())
            ->get()
            ->getRowArray();
        
        $data['title'] = 'Meu Perfil';
        $data['guardian'] = $guardian;
        $data['user'] = $user;
        
        return view('guardians/profile', $data);
    }
    
    public function update()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requisição inválida']);
        }
        
        $guardianId = getGuardianIdFromUser();
        $userId = currentUserId();
        
        $rules = [
            'full_name' => 'required|min_length[3]|max_length[255]',
            'phone' => 'required|max_length[20]',
            'email' => 'required|valid_email',
            'address' => 'permit_empty',
            'city' => 'permit_empty|max_length[100]',
            'province' => 'permit_empty|max_length[100]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $db = db_connect();
        
        try {
            // Atualizar guardião
            $db->table('tbl_guardians')
                ->where('id', $guardianId)
                ->update([
                    'full_name' => $this->request->getPost('full_name'),
                    'phone' => $this->request->getPost('phone'),
                    'phone2' => $this->request->getPost('phone2'),
                    'email' => $this->request->getPost('email'),
                    'address' => $this->request->getPost('address'),
                    'city' => $this->request->getPost('city'),
                    'municipality' => $this->request->getPost('municipality'),
                    'province' => $this->request->getPost('province')
                ]);
            
            // Atualizar usuário
            $nameParts = explode(' ', trim($this->request->getPost('full_name')));
            $firstName = $nameParts[0];
            $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
            
            $db->table('tbl_users')
                ->where('id', $userId)
                ->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $this->request->getPost('email'),
                    'phone' => $this->request->getPost('phone')
                ]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Perfil atualizado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao atualizar perfil: ' . $e->getMessage()
            ]);
        }
    }
    
    public function updatePhoto()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requisição inválida']);
        }
        
        $userId = currentUserId();
        
        $file = $this->request->getFile('photo');
        
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Arquivo inválido'
            ]);
        }
        
        if (!$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/guardians', $newName);
            
            $db = db_connect();
            $db->table('tbl_users')
                ->where('id', $userId)
                ->update(['photo' => 'uploads/guardians/' . $newName]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Foto atualizada com sucesso',
                'photo_url' => base_url('uploads/guardians/' . $newName)
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao fazer upload da foto'
        ]);
    }
}