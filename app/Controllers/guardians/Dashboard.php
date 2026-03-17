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
                c.class_shift,
                c.id as class_id
            ')
            ->join('tbl_students s', 's.id = sg.student_id')
            ->join('tbl_users u', 'u.id = s.user_id')
            ->join('tbl_enrollments e', 'e.student_id = s.id AND e.status = "Ativo"', 'left')
            ->join('tbl_classes c', 'c.id = e.class_id', 'left')
            ->where('sg.guardian_id', $guardianId)
            ->get()
            ->getResultArray();
        
        // Para cada aluno, buscar estatísticas básicas
        foreach ($students as &$student) {
            // Últimas notas
            $student['recent_grades'] = $db->table('tbl_exam_results r')
                ->select('r.score, d.discipline_name, es.exam_date')
                ->join('tbl_exam_schedules es', 'es.id = r.exam_schedule_id')
                ->join('tbl_disciplines d', 'd.id = es.discipline_id')
                ->where('r.enrollment_id', $student['enrollment_id'])
                ->orderBy('es.exam_date', 'DESC')
                ->limit(3)
                ->get()
                ->getResultArray();
            
            // Próximos exames
            $student['upcoming_exams'] = $db->table('tbl_exam_schedules es')
                ->select('es.*, d.discipline_name')
                ->join('tbl_disciplines d', 'd.id = es.discipline_id')
                ->where('es.class_id', $student['class_id'])
                ->where('es.exam_date >=', date('Y-m-d'))
                ->orderBy('es.exam_date', 'ASC')
                ->limit(3)
                ->get()
                ->getResultArray();
            
            // Resumo de presenças
            $attendance = $db->table('tbl_attendance a')
                ->select('
                    COUNT(*) as total,
                    SUM(CASE WHEN a.status IN ("Presente", "Atrasado", "Falta Justificada") THEN 1 ELSE 0 END) as present
                ')
                ->where('a.enrollment_id', $student['enrollment_id'])
                ->get()
                ->getRowArray();
            
            $student['attendance_percentage'] = ($attendance['total'] > 0) 
                ? round(($attendance['present'] / $attendance['total']) * 100, 1) 
                : 0;
        }
        
        $data['students'] = $students;
        $data['total_students'] = count($students);
        
        // Notificações não lidas
        $data['unread_notifications'] = $db->table('tbl_notifications')
            ->where('user_id', currentUserId())
            ->where('is_read', 0)
            ->countAllResults();
        
        return view('guardians/dashboard', $data);
    }
}