<?php

namespace App\Models;

class GuardianModel extends BaseModel
{
    protected $table = 'tbl_guardians';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'user_id',
        'guardian_type',
        'full_name',
        'identity_document',
        'identity_type',
        'nif',
        'profession',
        'workplace',
        'work_phone',
        'phone',
        'email',
        'address',
        'city',
        'municipality',
        'province',
        'is_primary',
        'is_active'
    ];
    
    protected $validationRules = [
        'id' => 'permit_empty|is_natural_no_zero',
        'user_id' => 'permit_empty|numeric|is_unique[tbl_guardians.user_id,id,{id}]',
        'guardian_type' => 'required|in_list[Pai,Mãe,Tutor,Encarregado,Outro]',
        'full_name' => 'required|min_length[3]|max_length[255]',
        'identity_document' => 'permit_empty|max_length[50]',
        'identity_type' => 'permit_empty|in_list[BI,Passaporte,Cédula,Outro]',
        'nif' => 'permit_empty|max_length[20]',
        'profession' => 'permit_empty|max_length[255]',
        'workplace' => 'permit_empty|max_length[255]',
        'work_phone' => 'permit_empty|max_length[20]',
        'phone' => 'required|max_length[20]',
        'email' => 'permit_empty|valid_email|max_length[255]',
        'address' => 'permit_empty',
        'city' => 'permit_empty|max_length[100]',
        'municipality' => 'permit_empty|max_length[100]',
        'province' => 'permit_empty|max_length[100]',
        'is_primary' => 'permit_empty|in_list[0,1]',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'user_id' => [
            'is_unique' => 'Este usuário já está associado a um guardião'
        ],
        'guardian_type' => [
            'required' => 'O tipo de guardião é obrigatório',
            'in_list' => 'Tipo de guardião inválido'
        ],
        'full_name' => [
            'required' => 'O nome completo é obrigatório',
            'min_length' => 'O nome deve ter pelo menos 3 caracteres'
        ],
        'phone' => [
            'required' => 'O telefone é obrigatório'
        ],
        'email' => [
            'valid_email' => 'Por favor, insira um email válido'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get guardian with user info
     */
    public function getWithUser($id)
    {
        return $this->select('
                tbl_guardians.*,
                tbl_users.username,
                tbl_users.email as user_email,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_users.photo as user_photo,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as user_full_name
            ')
            ->join('tbl_users', 'tbl_users.id = tbl_guardians.user_id', 'left')
            ->where('tbl_guardians.id', $id)
            ->first();
    }
    
    /**
     * Get guardian by user id
     */
    public function getByUserId($userId)
    {
        return $this->where('user_id', $userId)->first();
    }
    
    /**
     * Get guardians by student
     */
    public function getByStudent($studentId)
    {
        $db = db_connect();
        
        $builder = $db->table('tbl_guardians g');
        $builder->select('
                g.*,
                sg.relationship,
                sg.is_authorized
            ')
            ->join('tbl_student_guardians sg', 'sg.guardian_id = g.id')
            ->where('sg.student_id', $studentId)
            ->where('g.is_active', 1)
            ->orderBy('sg.is_authorized', 'DESC')
            ->orderBy('g.is_primary', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get primary guardian by student
     */
    public function getPrimaryByStudent($studentId)
    {
        $db = db_connect();
        
        $builder = $db->table('tbl_guardians g');
        $builder->select('
                g.*,
                sg.relationship,
                sg.is_authorized
            ')
            ->join('tbl_student_guardians sg', 'sg.guardian_id = g.id')
            ->where('sg.student_id', $studentId)
            ->where('g.is_primary', 1)
            ->where('g.is_active', 1)
            ->orderBy('sg.is_authorized', 'DESC')
            ->limit(1);
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Get students by guardian
     */
    public function getStudents($guardianId)
    {
        $db = db_connect();
        
        $builder = $db->table('tbl_students s');
        $builder->select('
                s.*,
                u.first_name,
                u.last_name,
                u.email,
                CONCAT(u.first_name, " ", u.last_name) as full_name,
                sg.relationship,
                sg.is_authorized,
                c.class_name,
                c.class_code,
                e.status as enrollment_status
            ')
            ->join('tbl_student_guardians sg', 'sg.student_id = s.id')
            ->join('tbl_users u', 'u.id = s.user_id')
            ->join('tbl_enrollments e', 'e.student_id = s.id AND e.status = "Ativo"', 'left')
            ->join('tbl_classes c', 'c.id = e.class_id', 'left')
            ->where('sg.guardian_id', $guardianId)
            ->where('s.is_active', 1)
            ->orderBy('u.first_name', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Associate guardian with student
     */
    public function associateWithStudent($guardianId, $studentId, $relationship = null, $isAuthorized = 1)
    {
        $db = db_connect();
        
        $data = [
            'student_id' => $studentId,
            'guardian_id' => $guardianId,
            'relationship' => $relationship,
            'is_authorized' => $isAuthorized,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $db->table('tbl_student_guardians')->insert($data);
    }
    
    /**
     * Remove association with student
     */
    public function dissociateFromStudent($guardianId, $studentId)
    {
        $db = db_connect();
        
        return $db->table('tbl_student_guardians')
            ->where('guardian_id', $guardianId)
            ->where('student_id', $studentId)
            ->delete();
    }
    
    /**
     * Update association relationship
     */
    public function updateAssociation($guardianId, $studentId, $data)
    {
        $db = db_connect();
        
        return $db->table('tbl_student_guardians')
            ->where('guardian_id', $guardianId)
            ->where('student_id', $studentId)
            ->update($data);
    }
    
    /**
     * Check if guardian is associated with student
     */
    public function isAssociatedWithStudent($guardianId, $studentId)
    {
        $db = db_connect();
        
        $result = $db->table('tbl_student_guardians')
            ->where('guardian_id', $guardianId)
            ->where('student_id', $studentId)
            ->get()
            ->getRow();
        
        return !empty($result);
    }
    
    /**
     * Search guardians
     */
    public function search($query)
    {
        return $this->groupStart()
                ->like('full_name', $query)
                ->orLike('identity_document', $query)
                ->orLike('nif', $query)
                ->orLike('email', $query)
                ->orLike('phone', $query)
            ->groupEnd()
            ->where('is_active', 1)
            ->orderBy('full_name', 'ASC')
            ->limit(20)
            ->findAll();
    }
    
    /**
     * Get guardians with pending documents
     */
    public function getWithPendingDocuments()
    {
        $db = db_connect();
        
        $builder = $db->table('tbl_guardians g');
        $builder->select('
                g.*,
                COUNT(d.id) as pending_docs
            ')
            ->join('tbl_documents d', 'd.user_id = g.user_id AND d.user_type = "guardian" AND d.is_verified = 0', 'left')
            ->where('g.is_active', 1)
            ->groupBy('g.id')
            ->having('pending_docs > 0')
            ->orderBy('pending_docs', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Count active guardians
     */
    public function countActive()
    {
        return $this->where('is_active', 1)->countAllResults();
    }
    
    /**
     * Count by type
     */
    public function countByType()
    {
        $db = db_connect();
        
        $builder = $db->table('tbl_guardians');
        $builder->select('guardian_type, COUNT(*) as total')
            ->where('is_active', 1)
            ->groupBy('guardian_type');
        
        $results = $builder->get()->getResultArray();
        
        $counts = [
            'Pai' => 0,
            'Mãe' => 0,
            'Tutor' => 0,
            'Encarregado' => 0,
            'Outro' => 0
        ];
        
        foreach ($results as $row) {
            $counts[$row['guardian_type']] = $row['total'];
        }
        
        return $counts;
    }
    
    /**
     * Get guardians by city
     */
    public function getByCity($city)
    {
        return $this->where('city', $city)
            ->where('is_active', 1)
            ->orderBy('full_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get guardians without user account
     */
    public function getWithoutUser()
    {
        return $this->select('tbl_guardians.*')
            ->join('tbl_users', 'tbl_users.id = tbl_guardians.user_id', 'left')
            ->where('tbl_guardians.user_id IS NULL')
            ->where('tbl_guardians.is_active', 1)
            ->findAll();
    }
    
    /**
     * Create user account for guardian
     */
    public function createUserAccount($guardianId, $username = null, $password = null)
    {
        $guardian = $this->find($guardianId);
        if (!$guardian) {
            return false;
        }
        
        $userModel = new \App\Models\UserModel();
        
        // Gerar username se não fornecido
        if (!$username) {
            $nameParts = explode(' ', trim($guardian['full_name']));
            $firstName = $nameParts[0];
            $lastName = end($nameParts);
            $username = strtolower($firstName . '.' . $lastName);
            $username = preg_replace('/[^a-z0-9.]/', '', $username);
        }
        
        // Gerar senha se não fornecida
        if (!$password) {
            $password = bin2hex(random_bytes(4)); // 8 caracteres
        }
        
        // Criar usuário
        $userData = [
            'username' => $username,
            'email' => $guardian['email'] ?? $username . '@escola.ao',
            'password' => $password,
            'first_name' => $nameParts[0] ?? '',
            'last_name' => $nameParts[1] ?? '',
            'phone' => $guardian['phone'],
            'address' => $guardian['address'],
            'role_id' => 6, // role_id para Encarregado (ajuste conforme sua tabela)
            'user_type' => 'guardian',
            'is_active' => 1
        ];
        
        $userId = $userModel->insert($userData);
        
        if ($userId) {
            // Atualizar guardião com user_id
            $this->update($guardianId, ['user_id' => $userId]);
            
            return [
                'user_id' => $userId,
                'username' => $username,
                'password' => $password
            ];
        }
        
        return false;
    }
    
    /**
     * Get guardians statistics
     */
    public function getStatistics()
    {
        $total = $this->countAll();
        $active = $this->countActive();
        $withUser = $this->where('user_id IS NOT NULL')->countAllResults();
        $primary = $this->where('is_primary', 1)->countAllResults();
        
        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'with_user' => $withUser,
            'without_user' => $total - $withUser,
            'primary' => $primary
        ];
    }
    
    /**
     * Get guardians by document number
     */
    public function getByIdentityDocument($docNumber)
    {
        return $this->where('identity_document', $docNumber)->first();
    }
    
    /**
     * Get guardians by NIF
     */
    public function getByNif($nif)
    {
        return $this->where('nif', $nif)->first();
    }
    
    /**
     * Bulk insert guardians
     */
    public function bulkInsert(array $guardians)
    {
        $db = db_connect();
        $db->transStart();
        
        $inserted = 0;
        foreach ($guardians as $guardian) {
            if ($this->insert($guardian)) {
                $inserted++;
            }
        }
        
        $db->transComplete();
        
        return $inserted;
    }
    
    /**
     * Get guardians with multiple students
     */
    public function getWithMultipleStudents($minStudents = 2)
    {
        $db = db_connect();
        
        $builder = $db->table('tbl_guardians g');
        $builder->select('
                g.*,
                COUNT(sg.student_id) as total_students
            ')
            ->join('tbl_student_guardians sg', 'sg.guardian_id = g.id')
            ->where('g.is_active', 1)
            ->groupBy('g.id')
            ->having('total_students >=', $minStudents)
            ->orderBy('total_students', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get guardians with payment history
     */
    public function getWithPaymentHistory($guardianId)
    {
        $db = db_connect();
        
        $builder = $db->table('tbl_payments p');
        $builder->select('
                p.*,
                i.invoice_number,
                i.total_amount as invoice_total,
                CONCAT(u.first_name, " ", u.last_name) as student_name
            ')
            ->join('tbl_invoices i', 'i.id = p.invoice_id')
            ->join('tbl_students s', 's.id = i.student_id')
            ->join('tbl_users u', 'u.id = s.user_id')
            ->where('i.guardian_id', $guardianId)
            ->orderBy('p.payment_date', 'DESC')
            ->limit(20);
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Deactivate guardian
     */
    public function deactivate($id)
    {
        return $this->update($id, ['is_active' => 0]);
    }
    
    /**
     * Activate guardian
     */
    public function activate($id)
    {
        return $this->update($id, ['is_active' => 1]);
    }
    
    /**
     * Set as primary guardian for all associated students
     */
    public function setAsPrimary($guardianId)
    {
        $db = db_connect();
        
        // Remover primary de todos os outros
        $db->table('tbl_guardians')
            ->where('is_primary', 1)
            ->update(['is_primary' => 0]);
        
        // Definir este como primary
        return $this->update($guardianId, ['is_primary' => 1]);
    }
    
    /**
     * Get guardian by email
     */
    public function getByEmail($email)
    {
        return $this->where('email', $email)->first();
    }
    
    /**
     * Get guardians born in month (for birthday)
     */
    public function getByBirthMonth($month)
    {
        // Nota: tbl_guardians não tem birth_date, então você pode querer adicionar
        // ou usar user_id para buscar da tbl_users
        $db = db_connect();
        
        $builder = $db->table('tbl_guardians g');
        $builder->select('g.*, u.birth_date')
            ->join('tbl_users u', 'u.id = g.user_id', 'left')
            ->where('MONTH(u.birth_date)', $month)
            ->where('g.is_active', 1)
            ->orderBy('DAY(u.birth_date)', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get guardian profile completion
     */
    public function getProfileCompletion($guardianId)
    {
        $guardian = $this->find($guardianId);
        if (!$guardian) {
            return 0;
        }
        
        $fields = [
            'full_name' => 10,
            'identity_document' => 10,
            'nif' => 10,
            'profession' => 10,
            'workplace' => 10,
            'work_phone' => 10,
            'phone' => 10,
            'email' => 10,
            'address' => 5,
            'city' => 5,
            'municipality' => 5,
            'province' => 5
        ];
        
        $total = 0;
        foreach ($fields as $field => $weight) {
            if (!empty($guardian[$field])) {
                $total += $weight;
            }
        }
        
        // Bônus por ter user_id
        if (!empty($guardian['user_id'])) {
            $total += 10;
        }
        
        return min($total, 100);
    }
}