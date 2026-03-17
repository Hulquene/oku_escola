<?php
// app/Controllers/guardians/Students.php
namespace App\Controllers\guardians;

use App\Controllers\BaseController;

class Students extends BaseController
{
    public function index()
    {
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
        
        $data['title'] = 'Meus Alunos';
        $data['students'] = $students;
        
        return view('guardians/students/index', $data);
    }
    
    public function view($studentId)
    {
        $guardianId = getGuardianIdFromUser();
        
        // Verificar se o aluno está associado a este encarregado
        $db = db_connect();
        
        $isAssociated = $db->table('tbl_student_guardians')
            ->where('student_id', $studentId)
            ->where('guardian_id', $guardianId)
            ->countAllResults();
        
        if (!$isAssociated) {
            return redirect()->to('/guardians/students')
                ->with('error', 'Você não tem permissão para ver este aluno.');
        }
        
        // Dados do aluno
        $student = $db->table('tbl_students s')
            ->select('
                s.*,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.photo,
                e.id as enrollment_id,
                c.class_name,
                c.class_code,
                c.class_shift,
                c.id as class_id
            ')
            ->join('tbl_users u', 'u.id = s.user_id')
            ->join('tbl_enrollments e', 'e.student_id = s.id AND e.status = "Ativo"', 'left')
            ->join('tbl_classes c', 'c.id = e.class_id', 'left')
            ->where('s.id', $studentId)
            ->get()
            ->getRowArray();
        
        if (!$student) {
            return redirect()->to('/guardians/students')
                ->with('error', 'Aluno não encontrado.');
        }
        
        $data['title'] = 'Detalhes do Aluno';
        $data['student'] = $student;
        
        return view('guardians/students/view', $data);
    }
    
    public function grades($studentId)
    {
        $guardianId = getGuardianIdFromUser();
        
        // Verificar permissão
        $db = db_connect();
        
        $isAssociated = $db->table('tbl_student_guardians')
            ->where('student_id', $studentId)
            ->where('guardian_id', $guardianId)
            ->countAllResults();
        
        if (!$isAssociated) {
            return redirect()->to('/guardians/students')
                ->with('error', 'Você não tem permissão para ver este aluno.');
        }
        
        // Buscar matrícula ativa
        $enrollment = $db->table('tbl_enrollments')
            ->where('student_id', $studentId)
            ->where('status', 'Ativo')
            ->get()
            ->getRowArray();
        
        if (!$enrollment) {
            return redirect()->back()->with('error', 'Aluno sem matrícula ativa.');
        }
        
        // Buscar notas agrupadas por disciplina
        $grades = $db->table('tbl_exam_results r')
            ->select('
                r.*,
                d.discipline_name,
                d.discipline_code,
                es.exam_date,
                eb.board_name,
                eb.board_type,
                ep.period_name,
                ep.semester_id
            ')
            ->join('tbl_exam_schedules es', 'es.id = r.exam_schedule_id')
            ->join('tbl_disciplines d', 'd.id = es.discipline_id')
            ->join('tbl_exam_boards eb', 'eb.id = es.exam_board_id')
            ->join('tbl_exam_periods ep', 'ep.id = es.exam_period_id')
            ->where('r.enrollment_id', $enrollment['id'])
            ->orderBy('es.exam_date', 'DESC')
            ->get()
            ->getResultArray();
        
        // Agrupar por disciplina
        $disciplines = [];
        foreach ($grades as $grade) {
            $disciplineId = $grade['discipline_id'];
            
            if (!isset($disciplines[$disciplineId])) {
                $disciplines[$disciplineId] = [
                    'discipline_name' => $grade['discipline_name'],
                    'discipline_code' => $grade['discipline_code'],
                    'grades' => [],
                    'average' => 0
                ];
            }
            
            $disciplines[$disciplineId]['grades'][] = $grade;
        }
        
        // Calcular médias
        foreach ($disciplines as &$discipline) {
            $total = 0;
            $count = 0;
            foreach ($discipline['grades'] as $grade) {
                $total += $grade['score'];
                $count++;
            }
            $discipline['average'] = $count > 0 ? round($total / $count, 1) : 0;
        }
        
        $data['title'] = 'Notas do Aluno';
        $data['student'] = $db->table('tbl_students s')
            ->select('s.*, u.first_name, u.last_name')
            ->join('tbl_users u', 'u.id = s.user_id')
            ->where('s.id', $studentId)
            ->get()
            ->getRowArray();
        $data['disciplines'] = $disciplines;
        
        return view('guardians/students/grades', $data);
    }
    
    public function attendance($studentId)
    {
        $guardianId = getGuardianIdFromUser();
        
        // Verificar permissão
        $db = db_connect();
        
        $isAssociated = $db->table('tbl_student_guardians')
            ->where('student_id', $studentId)
            ->where('guardian_id', $guardianId)
            ->countAllResults();
        
        if (!$isAssociated) {
            return redirect()->to('/guardians/students')
                ->with('error', 'Você não tem permissão para ver este aluno.');
        }
        
        // Buscar matrícula ativa
        $enrollment = $db->table('tbl_enrollments')
            ->where('student_id', $studentId)
            ->where('status', 'Ativo')
            ->get()
            ->getRowArray();
        
        if (!$enrollment) {
            return redirect()->back()->with('error', 'Aluno sem matrícula ativa.');
        }
        
        // Buscar presenças por disciplina
        $attendance = $db->table('tbl_attendance a')
            ->select('
                a.*,
                d.discipline_name,
                d.discipline_code,
                DATE_FORMAT(a.date, "%d/%m/%Y") as formatted_date
            ')
            ->join('tbl_disciplines d', 'd.id = a.discipline_id')
            ->where('a.enrollment_id', $enrollment['id'])
            ->orderBy('a.date', 'DESC')
            ->get()
            ->getResultArray();
        
        // Estatísticas por disciplina
        $stats = $db->table('tbl_attendance a')
            ->select('
                a.discipline_id,
                d.discipline_name,
                COUNT(*) as total,
                SUM(CASE WHEN a.status IN ("Presente", "Atrasado", "Falta Justificada") THEN 1 ELSE 0 END) as present
            ')
            ->join('tbl_disciplines d', 'd.id = a.discipline_id')
            ->where('a.enrollment_id', $enrollment['id'])
            ->groupBy('a.discipline_id')
            ->get()
            ->getResultArray();
        
        foreach ($stats as &$stat) {
            $stat['percentage'] = $stat['total'] > 0 
                ? round(($stat['present'] / $stat['total']) * 100, 1) 
                : 0;
            $stat['absent'] = $stat['total'] - $stat['present'];
        }
        
        $data['title'] = 'Presenças do Aluno';
        $data['student'] = $db->table('tbl_students s')
            ->select('s.*, u.first_name, u.last_name')
            ->join('tbl_users u', 'u.id = s.user_id')
            ->where('s.id', $studentId)
            ->get()
            ->getRowArray();
        $data['attendance'] = $attendance;
        $data['stats'] = $stats;
        
        return view('guardians/students/attendance', $data);
    }
    
    public function exams($studentId)
    {
        $guardianId = getGuardianIdFromUser();
        
        // Verificar permissão
        $db = db_connect();
        
        $isAssociated = $db->table('tbl_student_guardians')
            ->where('student_id', $studentId)
            ->where('guardian_id', $guardianId)
            ->countAllResults();
        
        if (!$isAssociated) {
            return redirect()->to('/guardians/students')
                ->with('error', 'Você não tem permissão para ver este aluno.');
        }
        
        // Buscar matrícula ativa
        $enrollment = $db->table('tbl_enrollments')
            ->where('student_id', $studentId)
            ->where('status', 'Ativo')
            ->get()
            ->getRowArray();
        
        if (!$enrollment) {
            return redirect()->back()->with('error', 'Aluno sem matrícula ativa.');
        }
        
        // Buscar próximos exames
        $upcoming = $db->table('tbl_exam_schedules es')
            ->select('
                es.*,
                d.discipline_name,
                d.discipline_code,
                eb.board_name,
                eb.board_type,
                ep.period_name,
                DATE_FORMAT(es.exam_date, "%d/%m/%Y") as formatted_date,
                DATE_FORMAT(es.exam_time, "%H:%i") as formatted_time
            ')
            ->join('tbl_disciplines d', 'd.id = es.discipline_id')
            ->join('tbl_exam_boards eb', 'eb.id = es.exam_board_id')
            ->join('tbl_exam_periods ep', 'ep.id = es.exam_period_id')
            ->where('es.class_id', $enrollment['class_id'])
            ->where('es.exam_date >=', date('Y-m-d'))
            ->orderBy('es.exam_date', 'ASC')
            ->orderBy('es.exam_time', 'ASC')
            ->get()
            ->getResultArray();
        
        // Buscar exames realizados
        $completed = $db->table('tbl_exam_results r')
            ->select('
                r.*,
                d.discipline_name,
                d.discipline_code,
                eb.board_name,
                eb.board_type,
                ep.period_name,
                es.exam_date,
                DATE_FORMAT(es.exam_date, "%d/%m/%Y") as formatted_date
            ')
            ->join('tbl_exam_schedules es', 'es.id = r.exam_schedule_id')
            ->join('tbl_disciplines d', 'd.id = es.discipline_id')
            ->join('tbl_exam_boards eb', 'eb.id = es.exam_board_id')
            ->join('tbl_exam_periods ep', 'ep.id = es.exam_period_id')
            ->where('r.enrollment_id', $enrollment['id'])
            ->orderBy('es.exam_date', 'DESC')
            ->get()
            ->getResultArray();
        
        $data['title'] = 'Exames do Aluno';
        $data['student'] = $db->table('tbl_students s')
            ->select('s.*, u.first_name, u.last_name')
            ->join('tbl_users u', 'u.id = s.user_id')
            ->where('s.id', $studentId)
            ->get()
            ->getRowArray();
        $data['upcoming'] = $upcoming;
        $data['completed'] = $completed;
        
        return view('guardians/students/exams', $data);
    }
    
    public function fees($studentId)
    {
        $guardianId = getGuardianIdFromUser();
        
        // Verificar permissão
        $db = db_connect();
        
        $isAssociated = $db->table('tbl_student_guardians')
            ->where('student_id', $studentId)
            ->where('guardian_id', $guardianId)
            ->countAllResults();
        
        if (!$isAssociated) {
            return redirect()->to('/guardians/students')
                ->with('error', 'Você não tem permissão para ver este aluno.');
        }
        
        // Buscar matrícula ativa
        $enrollment = $db->table('tbl_enrollments')
            ->where('student_id', $studentId)
            ->where('status', 'Ativo')
            ->get()
            ->getRowArray();
        
        if (!$enrollment) {
            return redirect()->back()->with('error', 'Aluno sem matrícula ativa.');
        }
        
        // Buscar propinas do aluno
        $fees = $db->table('tbl_student_fees sf')
            ->select('
                sf.*,
                fs.fee_name,
                ft.type_name,
                DATE_FORMAT(sf.due_date, "%d/%m/%Y") as formatted_due_date,
                DATE_FORMAT(sf.payment_date, "%d/%m/%Y") as formatted_payment_date
            ')
            ->join('tbl_fee_structure fs', 'fs.id = sf.fee_structure_id')
            ->join('tbl_fee_types ft', 'ft.id = fs.fee_type_id')
            ->where('sf.enrollment_id', $enrollment['id'])
            ->orderBy('sf.due_date', 'ASC')
            ->get()
            ->getResultArray();
        
        // Resumo financeiro
        $summary = [
            'total' => 0,
            'paid' => 0,
            'pending' => 0,
            'overdue' => 0
        ];
        
        foreach ($fees as $fee) {
            $summary['total'] += $fee['total_amount'];
            if ($fee['status'] == 'Pago') {
                $summary['paid'] += $fee['total_amount'];
            } elseif (in_array($fee['status'], ['Pendente', 'Vencido'])) {
                $summary['pending'] += $fee['total_amount'];
                if ($fee['status'] == 'Vencido') {
                    $summary['overdue']++;
                }
            }
        }
        
        $data['title'] = 'Propinas do Aluno';
        $data['student'] = $db->table('tbl_students s')
            ->select('s.*, u.first_name, u.last_name')
            ->join('tbl_users u', 'u.id = s.user_id')
            ->where('s.id', $studentId)
            ->get()
            ->getRowArray();
        $data['fees'] = $fees;
        $data['summary'] = $summary;
        
        return view('guardians/students/fees', $data);
    }
}