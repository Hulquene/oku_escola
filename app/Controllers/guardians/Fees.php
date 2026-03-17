<?php
// app/Controllers/guardians/Fees.php
namespace App\Controllers\guardians;

use App\Controllers\BaseController;

class Fees extends BaseController
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
                e.id as enrollment_id
            ')
            ->join('tbl_students s', 's.id = sg.student_id')
            ->join('tbl_users u', 'u.id = s.user_id')
            ->join('tbl_enrollments e', 'e.student_id = s.id AND e.status = "Ativo"', 'left')
            ->where('sg.guardian_id', $guardianId)
            ->get()
            ->getResultArray();
        
        // Para cada aluno, buscar resumo de propinas
        foreach ($students as &$student) {
            $fees = $db->table('tbl_student_fees sf')
                ->select('
                    SUM(CASE WHEN sf.status = "Pago" THEN sf.total_amount ELSE 0 END) as paid,
                    SUM(CASE WHEN sf.status IN ("Pendente", "Vencido") THEN sf.total_amount ELSE 0 END) as pending
                ')
                ->where('sf.enrollment_id', $student['enrollment_id'])
                ->get()
                ->getRowArray();
            
            $student['paid'] = $fees['paid'] ?? 0;
            $student['pending'] = $fees['pending'] ?? 0;
            $student['total'] = $student['paid'] + $student['pending'];
        }
        
        $data['title'] = 'Propinas';
        $data['students'] = $students;
        
        return view('guardians/fees/index', $data);
    }
    
    public function student($studentId)
    {
        $guardianId = getGuardianIdFromUser();
        
        // Verificar permissão
        $db = db_connect();
        
        $isAssociated = $db->table('tbl_student_guardians')
            ->where('student_id', $studentId)
            ->where('guardian_id', $guardianId)
            ->countAllResults();
        
        if (!$isAssociated) {
            return redirect()->to('/guardians/fees')
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
        
        // Buscar propinas
        $fees = $db->table('tbl_student_fees sf')
            ->select('
                sf.*,
                fs.fee_name,
                ft.type_name,
                DATE_FORMAT(sf.due_date, "%d/%m/%Y") as formatted_due_date
            ')
            ->join('tbl_fee_structure fs', 'fs.id = sf.fee_structure_id')
            ->join('tbl_fee_types ft', 'ft.id = fs.fee_type_id')
            ->where('sf.enrollment_id', $enrollment['id'])
            ->orderBy('sf.due_date', 'ASC')
            ->get()
            ->getResultArray();
        
        $data['title'] = 'Propinas do Aluno';
        $data['student'] = $db->table('tbl_students s')
            ->select('s.*, u.first_name, u.last_name')
            ->join('tbl_users u', 'u.id = s.user_id')
            ->where('s.id', $studentId)
            ->get()
            ->getRowArray();
        $data['fees'] = $fees;
        
        return view('guardians/fees/student', $data);
    }
    
    public function pay($feeId)
    {
        $guardianId = getGuardianIdFromUser();
        
        // Verificar se a propina pertence a um aluno associado
        $db = db_connect();
        
        $fee = $db->table('tbl_student_fees sf')
            ->select('sf.*, e.student_id')
            ->join('tbl_enrollments e', 'e.id = sf.enrollment_id')
            ->where('sf.id', $feeId)
            ->get()
            ->getRowArray();
        
        if (!$fee) {
            return redirect()->back()->with('error', 'Propina não encontrada.');
        }
        
        $isAssociated = $db->table('tbl_student_guardians')
            ->where('student_id', $fee['student_id'])
            ->where('guardian_id', $guardianId)
            ->countAllResults();
        
        if (!$isAssociated) {
            return redirect()->to('/guardians/fees')
                ->with('error', 'Você não tem permissão para pagar esta propina.');
        }
        
        // Redirecionar para gateway de pagamento (a implementar)
        return redirect()->to('/guardians/fees/student/' . $fee['student_id'])
            ->with('info', 'Funcionalidade de pagamento online em desenvolvimento.');
    }
    
    public function history()
    {
        $guardianId = getGuardianIdFromUser();
        
        $db = db_connect();
        
        // Buscar histórico de pagamentos de todos os alunos associados
        $payments = $db->table('tbl_payments p')
            ->select('
                p.*,
                i.invoice_number,
                i.total_amount,
                u.first_name,
                u.last_name,
                s.student_number
            ')
            ->join('tbl_invoices i', 'i.id = p.invoice_id')
            ->join('tbl_students s', 's.id = i.student_id')
            ->join('tbl_users u', 'u.id = s.user_id')
            ->join('tbl_student_guardians sg', 'sg.student_id = s.id')
            ->where('sg.guardian_id', $guardianId)
            ->orderBy('p.payment_date', 'DESC')
            ->get()
            ->getResultArray();
        
        $data['title'] = 'Histórico de Pagamentos';
        $data['payments'] = $payments;
        
        return view('guardians/fees/history', $data);
    }
}