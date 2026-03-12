<?php
// app/Controllers/admin/AcademicDocuments.php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\StudentModel;
use App\Models\AcademicYearModel;
use App\Models\GradeLevelModel;
use App\Models\CourseModel;
use App\Models\AcademicDocumentModel;
use App\Models\ExamResultModel;
use App\Models\DisciplineAverageModel;
use App\Models\UserModel;

class AcademicDocuments extends BaseController
{
    protected $enrollmentModel;
    protected $studentModel;
    protected $academicYearModel;
    protected $gradeLevelModel;
    protected $courseModel;
    protected $academicDocumentModel;
    protected $examResultModel;
    protected $disciplineAverageModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->studentModel = new StudentModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->courseModel = new CourseModel();
        $this->academicDocumentModel = new AcademicDocumentModel();
        $this->examResultModel = new ExamResultModel();
        $this->disciplineAverageModel = new DisciplineAverageModel();
        $this->userModel = new \App\Models\UserModel();
        
        helper(['auth', 'log', 'settings']);
        
        // Os models já devem estar configurados para retornar array
        // Verifique se em cada model tem: protected $returnType = 'array';
    }
    
    /**
     * Página principal de documentos acadêmicos
     */
    public function index()
    {
        $data['title'] = 'Documentos Acadêmicos';
        
        return view('admin/academic-documents/index', $data);
    }
    
    /**
     * Lista de alunos aptos para certificado
     * Critérios:
     * - Iniciação à 9ª classe: is_cycle_end = 1 na tbl_grade_levels
     * - 10ª à 13ª classe: o nível atual é o end_grade_id do curso
     */
    public function eligibleForCertificate()
    {
        $data['title'] = 'Alunos Aptos para Certificado';
        
        $academicYearId = $this->request->getGet('academic_year') ?? current_academic_year();
        
        // Buscar alunos do Ensino Geral (Iniciação à 9ª) que concluíram ciclo
        $generalStudents = $this->enrollmentModel
            ->select('
                tbl_enrollments.id as enrollment_id,
                tbl_enrollments.final_average,
                tbl_enrollments.completion_date,
                tbl_students.id as student_id,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_grade_levels.level_name,
                tbl_grade_levels.is_cycle_end,
                tbl_grade_levels.education_level,
                tbl_academic_years.year_name,
                tbl_classes.class_name,
                NULL as course_name,
                "Ensino Geral" as document_type
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_enrollments.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->where('tbl_enrollments.academic_year_id', $academicYearId)
            ->where('tbl_enrollments.status', 'Concluído')
            ->where('tbl_enrollments.final_result', 'Aprovado')
            ->where('tbl_grade_levels.is_cycle_end', 1)
            ->where('tbl_enrollments.course_id IS NULL') // Apenas Ensino Geral
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Buscar alunos do Ensino Médio (10ª à 13ª) que concluíram o curso
        $highSchoolStudents = $this->enrollmentModel
            ->select('
                tbl_enrollments.id as enrollment_id,
                tbl_enrollments.final_average,
                tbl_enrollments.completion_date,
                tbl_students.id as student_id,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_grade_levels.level_name,
                tbl_grade_levels.is_cycle_end,
                tbl_grade_levels.education_level,
                tbl_academic_years.year_name,
                tbl_classes.class_name,
                tbl_courses.course_name,
                tbl_courses.course_code,
                "Curso Médio" as document_type
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_enrollments.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_courses', 'tbl_courses.id = tbl_enrollments.course_id', 'left')
            ->where('tbl_enrollments.academic_year_id', $academicYearId)
            ->where('tbl_enrollments.status', 'Concluído')
            ->where('tbl_enrollments.final_result', 'Aprovado')
            ->where('tbl_enrollments.course_id IS NOT NULL') // Tem curso associado
            ->where('tbl_grade_levels.id = tbl_courses.end_grade_id') // Nível atual é o fim do curso
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Combinar os dois arrays
        $students = array_merge($generalStudents, $highSchoolStudents);
        
        // Ordenar por nome
        usort($students, function($a, $b) {
            return strcmp($a['first_name'] . ' ' . $a['last_name'], $b['first_name'] . ' ' . $b['last_name']);
        });
        
        // Verificar se já foram emitidos certificados
        foreach ($students as &$student) {
            $existing = $this->academicDocumentModel
                ->where('student_id', $student['student_id'])
                ->where('enrollment_id', $student['enrollment_id'])
                ->where('document_type', 'certificate')
                ->first();
            
            $student['certificate_issued'] = !empty($existing);
            $student['certificate_number'] = $existing ? $existing['document_number'] : null;
            $student['certificate_date'] = $existing ? $existing['issue_date'] : null;
        }
        
        $data['students'] = $students;
        $data['academicYearId'] = $academicYearId;
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('year_name', 'DESC')
            ->findAll();
        
        return view('admin/academic-documents/eligible_certificates', $data);
    }
    
    /**
     * Lista de alunos aptos para declaração (fim de ano letivo)
     * Todos os alunos aprovados, independente do nível
     */
    public function eligibleForDeclaration()
    {
        $data['title'] = 'Alunos Aptos para Declaração';
        
        $academicYearId = $this->request->getGet('academic_year') ?? current_academic_year();
        
        // Buscar alunos que concluíram o ano letivo com aprovação
        $students = $this->enrollmentModel
            ->select('
                tbl_enrollments.id as enrollment_id,
                tbl_enrollments.final_average,
                tbl_enrollments.completion_date,
                tbl_students.id as student_id,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_grade_levels.level_name,
                tbl_grade_levels.is_cycle_end,
                tbl_academic_years.year_name,
                tbl_classes.class_name,
                tbl_courses.course_name
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_enrollments.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_courses', 'tbl_courses.id = tbl_enrollments.course_id', 'left')
            ->where('tbl_enrollments.academic_year_id', $academicYearId)
            ->where('tbl_enrollments.status', 'Concluído')
            ->where('tbl_enrollments.final_result', 'Aprovado')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Verificar se já foram emitidas declarações
        foreach ($students as &$student) {
            $existing = $this->academicDocumentModel
                ->where('student_id', $student['student_id'])
                ->where('enrollment_id', $student['enrollment_id'])
                ->where('document_type', 'declaration')
                ->first();
            
            $student['declaration_issued'] = !empty($existing);
            $student['declaration_number'] = $existing ? $existing['document_number'] : null;
            $student['declaration_date'] = $existing ? $existing['issue_date'] : null;
        }
        
        $data['students'] = $students;
        $data['academicYearId'] = $academicYearId;
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('year_name', 'DESC')
            ->findAll();
        
        return view('admin/academic-documents/eligible_declarations', $data);
    }
    
    /**
     * Gerar certificado de conclusão de ciclo/curso
     */
    public function generateCertificate($enrollmentId)
    {
        // Verificar permissão
        if (!has_permission('documents.generate') && !is_admin()) {
            return redirect()->back()->with('error', 'Sem permissão para gerar certificados');
        }
        
        // Buscar dados da matrícula
        $enrollment = $this->enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_students.id as student_id,
                tbl_students.student_number,
                tbl_students.birth_date,
                tbl_students.birth_place,
                tbl_students.nationality,
                tbl_students.gender,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_grade_levels.level_name,
                tbl_grade_levels.is_cycle_end,
                tbl_grade_levels.education_level,
                tbl_academic_years.year_name,
                tbl_classes.class_name,
                tbl_courses.course_name,
                tbl_courses.course_code,
                tbl_courses.duration_years,
                tbl_courses.start_grade_id,
                tbl_courses.end_grade_id
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_enrollments.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_courses', 'tbl_courses.id = tbl_enrollments.course_id', 'left')
            ->where('tbl_enrollments.id', $enrollmentId)
            ->first();
        
        if (!$enrollment) {
            return redirect()->back()->with('error', 'Matrícula não encontrada');
        }
        
        // Validar se tem direito a certificado
        $validation = $this->validateCertificateEligibility($enrollment);
        
        if (!$validation['eligible']) {
            return redirect()->back()->with('error', $validation['message']);
        }
        
        // Verificar se já foi emitido
        $existing = $this->academicDocumentModel
            ->where('student_id', $enrollment['student_id'])
            ->where('enrollment_id', $enrollment['id'])
            ->where('document_type', 'certificate')
            ->first();
        
        if ($existing) {
            return redirect()->back()->with('warning', 'Certificado já foi emitido anteriormente');
        }
        
        // Buscar médias por disciplina
        $disciplines = $this->disciplineAverageModel
            ->select('
                tbl_discipline_averages.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
            ->where('tbl_discipline_averages.enrollment_id', $enrollmentId)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        // Gerar número do certificado
        $certificateNumber = $this->academicDocumentModel->generateDocumentNumber('certificate');
        
        // Registrar emissão
        $this->academicDocumentModel->insert([
            'student_id' => $enrollment['student_id'],
            'enrollment_id' => $enrollmentId,
            'document_type' => 'certificate',
            'document_number' => $certificateNumber,
            'issue_date' => date('Y-m-d'),
            'status' => 'issued',
            'issued_by' => session()->get('user_id')
        ]);
        
        // Log da ação
        log_action(
            'generate',
            "Certificado {$certificateNumber} gerado para aluno {$enrollment['first_name']} {$enrollment['last_name']}",
            $enrollment['student_id'],
            'student'
        );
        
        // Gerar PDF
        return $this->generateCertificatePDF($enrollment, $disciplines, $certificateNumber);
    }
    
    /**
     * Validar se aluno tem direito a certificado
     */
    private function validateCertificateEligibility($enrollment)
    {
        // Caso 1: Ensino Geral (Iniciação à 9ª classe) - verificar is_cycle_end
        if (!$enrollment['course_id']) {
            if ($enrollment['is_cycle_end']) {
                return ['eligible' => true, 'message' => 'Aluno concluiu ciclo do Ensino Geral'];
            } else {
                return ['eligible' => false, 'message' => 'Este nível não é fim de ciclo no Ensino Geral'];
            }
        }
        
        // Caso 2: Ensino Médio (com curso) - verificar se o nível atual é o fim do curso
        if ($enrollment['course_id']) {
            // Buscar o curso para confirmar o nível final
            $course = $this->courseModel->find($enrollment['course_id']);
            
            if (!$course) {
                return ['eligible' => false, 'message' => 'Curso não encontrado'];
            }
            
            // Verificar se o nível atual é o nível final do curso
            if ($enrollment['grade_level_id'] == $course['end_grade_id']) {
                return ['eligible' => true, 'message' => 'Aluno concluiu o curso'];
            } else {
                return ['eligible' => false, 'message' => 'Aluno ainda não concluiu o curso completo'];
            }
        }
        
        return ['eligible' => false, 'message' => 'Não elegível para certificado'];
    }
    
    /**
     * Gerar PDF do certificado
     */
    private function generateCertificatePDF($enrollment, $disciplines, $certificateNumber)
    {
        // Determinar o título do certificado baseado no tipo
        if ($enrollment['course_id']) {
            $certificateTitle = 'CERTIFICADO DE CONCLUSÃO DE CURSO';
            $completionText = 'concluiu com aproveitamento o Curso de';
            $completionName = $enrollment['course_name'];
        } else {
            $certificateTitle = 'CERTIFICADO DE CONCLUSÃO DE CICLO';
            $completionText = 'concluiu com aproveitamento o Ciclo de';
            $completionName = $enrollment['education_level'];
        }
        
        $data = [
            'certificate_number' => $certificateNumber,
            'certificate_title' => $certificateTitle,
            'completion_text' => $completionText,
            'completion_name' => $completionName,
            'student_name' => $enrollment['first_name'] . ' ' . $enrollment['last_name'],
            'student_number' => $enrollment['student_number'],
            'birth_date' => date('d/m/Y', strtotime($enrollment['birth_date'])),
            'birth_place' => $enrollment['birth_place'],
            'nationality' => $enrollment['nationality'],
            'level_name' => $enrollment['level_name'],
            'class_name' => $enrollment['class_name'],
            'academic_year' => $enrollment['year_name'],
            'final_average' => number_format($enrollment['final_average'], 1),
            'completion_date' => date('d/m/Y', strtotime($enrollment['completion_date'] ?? date('Y-m-d'))),
            'disciplines' => $disciplines,
            'school_name' => setting('school_name'),
            'school_logo' => school_logo_url(),
            'director_name' => setting('director_name'),
            'director_title' => setting('director_title'),
            'secretary_name' => setting('secretary_name') ?? 'Secretário(a)'
        ];
        
        // Gerar PDF usando dompdf
        $dompdf = new \Dompdf\Dompdf();
        $html = view('admin/academic-documents/certificate_pdf', $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Nome do arquivo
        $filename = "certificado_{$enrollment['student_number']}_{$certificateNumber}.pdf";
        
        // Salvar arquivo
        $filePath = FCPATH . 'uploads/documents/' . $filename;
        file_put_contents($filePath, $dompdf->output());
        
        // Atualizar registro com caminho do arquivo
        $this->academicDocumentModel
            ->where('document_number', $certificateNumber)
            ->set(['file_path' => 'uploads/documents/' . $filename])
            ->update();
        
        // Download
        return $dompdf->stream($filename, ['Attachment' => true]);
    }
    
    /**
     * Gerar declaração de conclusão de ano letivo
     */
    public function generateDeclaration($enrollmentId)
    {
        // Verificar permissão
        if (!has_permission('documents.generate') && !is_admin()) {
            return redirect()->back()->with('error', 'Sem permissão para gerar declarações');
        }
        
        // Buscar dados da matrícula
        $enrollment = $this->enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_students.id as student_id,
                tbl_students.student_number,
                tbl_students.birth_date,
                tbl_students.birth_place,
                tbl_students.nationality,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_grade_levels.level_name,
                tbl_academic_years.year_name,
                tbl_classes.class_name,
                tbl_courses.course_name
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_enrollments.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_courses', 'tbl_courses.id = tbl_enrollments.course_id', 'left')
            ->where('tbl_enrollments.id', $enrollmentId)
            ->first();
        
        if (!$enrollment) {
            return redirect()->back()->with('error', 'Matrícula não encontrada');
        }
        
        // Validar se foi aprovado
        if ($enrollment['final_result'] != 'Aprovado') {
            return redirect()->back()->with('error', 'Aluno não foi aprovado');
        }
        
        // Verificar se já foi emitida declaração para este ano
        $existing = $this->academicDocumentModel
            ->where('student_id', $enrollment['student_id'])
            ->where('enrollment_id', $enrollment['id'])
            ->where('document_type', 'declaration')
            ->first();
        
        if ($existing) {
            return redirect()->back()->with('warning', 'Declaração já foi emitida anteriormente');
        }
        
        // Gerar número da declaração
        $declarationNumber = $this->academicDocumentModel->generateDocumentNumber('declaration');
        
        // Registrar emissão
        $this->academicDocumentModel->insert([
            'student_id' => $enrollment['student_id'],
            'enrollment_id' => $enrollmentId,
            'document_type' => 'declaration',
            'document_number' => $declarationNumber,
            'issue_date' => date('Y-m-d'),
            'status' => 'issued',
            'issued_by' => session()->get('user_id')
        ]);
        
        // Log da ação
        log_action(
            'generate',
            "Declaração {$declarationNumber} gerada para aluno {$enrollment['first_name']} {$enrollment['last_name']}",
            $enrollment['student_id'],
            'student'
        );
        
        // Gerar PDF
        return $this->generateDeclarationPDF($enrollment, $declarationNumber);
    }
    
    /**
     * Gerar PDF da declaração
     */
    private function generateDeclarationPDF($enrollment, $declarationNumber)
    {
        $data = [
            'declaration_number' => $declarationNumber,
            'student_name' => $enrollment['first_name'] . ' ' . $enrollment['last_name'],
            'student_number' => $enrollment['student_number'],
            'birth_date' => date('d/m/Y', strtotime($enrollment['birth_date'])),
            'birth_place' => $enrollment['birth_place'],
            'nationality' => $enrollment['nationality'],
            'course_name' => $enrollment['course_name'] ?? 'Ensino Geral',
            'level_name' => $enrollment['level_name'],
            'class_name' => $enrollment['class_name'],
            'academic_year' => $enrollment['year_name'],
            'final_average' => number_format($enrollment['final_average'], 1),
            'completion_date' => date('d/m/Y', strtotime($enrollment['completion_date'] ?? date('Y-m-d'))),
            'school_name' => setting('school_name'),
            'school_logo' => school_logo_url(),
            'director_name' => setting('director_name'),
            'director_title' => setting('director_title')
        ];
        
        // Gerar PDF usando dompdf
        $dompdf = new \Dompdf\Dompdf();
        $html = view('admin/academic-documents/declaration_pdf', $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Nome do arquivo
        $filename = "declaracao_{$enrollment['student_number']}_{$declarationNumber}.pdf";
        
        // Salvar arquivo
        $filePath = FCPATH . 'uploads/documents/' . $filename;
        file_put_contents($filePath, $dompdf->output());
        
        // Atualizar registro com caminho do arquivo
        $this->academicDocumentModel
            ->where('document_number', $declarationNumber)
            ->set(['file_path' => 'uploads/documents/' . $filename])
            ->update();
        
        // Download
        return $dompdf->stream($filename, ['Attachment' => true]);
    }
    
/**
 * Histórico de documentos emitidos
 */
public function history()
{
    $data['title'] = 'Histórico de Documentos Emitidos';
    
    $documents = $this->academicDocumentModel
        ->select('
            tbl_academic_documents.*,
            students.first_name,
            students.last_name,
            tbl_students.student_number,
            tbl_academic_years.year_name,
            issuer.first_name as issuer_first,
            issuer.last_name as issuer_last
        ')
        ->join('tbl_students', 'tbl_students.id = tbl_academic_documents.student_id')
        ->join('tbl_users as students', 'students.id = tbl_students.user_id')
        ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_academic_documents.enrollment_id', 'left')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id', 'left')
        ->join('tbl_users as issuer', 'issuer.id = tbl_academic_documents.issued_by', 'left')
        ->orderBy('tbl_academic_documents.created_at', 'DESC')
        ->findAll();
    
    $data['documents'] = $documents;
    
    return view('admin/academic-documents/history', $data);
}
}