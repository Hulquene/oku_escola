<?php
// app/Controllers/guardians/Dashboard.php
namespace App\Controllers\guardians;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data['title'] = 'Dashboard do Encarregado';
        
        // Buscar alunos associados a este encarregado
        $guardianId = getGuardianIdFromUser();
        
        if (!$guardianId) {
            return redirect()->to('/guardians/auth/logout')
                ->with('error', 'Perfil de encarregado não encontrado.');
        }
        
        $db = db_connect();
        
        // Buscar alunos associados
        $students = $db->table('tbl_student_guardians sg')
            ->select('
                s.id as student_id,
                u.first_name,
                u.last_name,
                s.student_number,
                e.id as enrollment_id,
                c.class_name,
                c.class_shift
            ')
            ->join('tbl_students s', 's.id = sg.student_id')
            ->join('tbl_users u', 'u.id = s.user_id')
            ->join('tbl_enrollments e', 'e.student_id = s.id AND e.status = "Ativo"', 'left')
            ->join('tbl_classes c', 'c.id = e.class_id', 'left')
            ->where('sg.guardian_id', $guardianId)
            ->get()
            ->getResult();
        
        $data['students'] = $students;
        $data['total_students'] = count($students);
        
        return view('guardians/dashboard', $data);
    }
}