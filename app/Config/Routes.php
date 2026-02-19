<?php

use App\Controllers\admin\AcademicYears;
use App\Controllers\admin\Classes;
use App\Controllers\admin\ClassSubjects;
use App\Controllers\admin\Clients;
use App\Controllers\admin\CreditNotes;
use App\Controllers\admin\Currencies;
use App\Controllers\admin\Dashboard;
use App\Controllers\admin\Disciplines;
use App\Controllers\admin\Enrollments;
use App\Controllers\admin\ExamBoards;
use App\Controllers\admin\ExamResults;
use App\Controllers\admin\Exams;
use App\Controllers\admin\Expenses;
use App\Controllers\admin\Fees;
use App\Controllers\admin\FeesPayments;
use App\Controllers\admin\GradeLevels;
use App\Controllers\admin\Inventory;
use App\Controllers\admin\Invoices;
use App\Controllers\admin\Payments;
use App\Controllers\admin\Pos;
use App\Controllers\admin\Products;
use App\Controllers\admin\Proposals;
use App\Controllers\admin\Purchases;
use App\Controllers\admin\Reports;
use App\Controllers\admin\Roles;
use App\Controllers\admin\SchoolCalendar;
use App\Controllers\admin\SchoolSettings;
use App\Controllers\admin\Semesters;
use App\Controllers\admin\Settings;
use App\Controllers\admin\Attendance;
use App\Controllers\admin\StudentGuardians;
use App\Controllers\admin\Suppliers;
use App\Controllers\admin\Taxes;
use App\Controllers\admin\Teachers;
use App\Controllers\admin\Teste;
use App\Controllers\admin\TuitionFees;
use App\Controllers\admin\Users;
use App\Controllers\admin\Logs;
use App\Controllers\admin\AdminDocuments;
use App\Controllers\admin\DocumentGenerator;
use App\Controllers\admin\Courses;
use App\Controllers\admin\CourseCurriculum;

use App\Controllers\auth\Auth;
use App\Controllers\Home;
use App\Controllers\GeneratePdf;
use App\Controllers\Install;
use App\Controllers\Invoice;
use App\Controllers\Language;
use App\Controllers\students\Dashboard as StudentsDashboard;
use App\Controllers\students\Profile as StudentsProfile;
use App\Controllers\students\Subjects as StudentsSubjects;
use App\Controllers\students\Exams as StudentExams;
use App\Controllers\students\Fees as StudentFees;
use App\Controllers\students\Grades as StudentGrades;
use App\Controllers\students\Attendance as StudentAttendance;
use App\Controllers\students\Documents as StudentADocuments;

use App\Controllers\teachers\Dashboard as TeachersDashboard;
use App\Controllers\teachers\Profile as TeachersProfile;
use App\Controllers\teachers\Classes as TeacherClasses;
use App\Controllers\teachers\Exams as TeacherExams;
use App\Controllers\teachers\Grades as TeacherGrades;
use App\Controllers\teachers\Attendance as TeacherAttendance;
use App\Controllers\teachers\Documents as TeacherDocuments;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
//$routes->get('/viewinvoice', 'Home::index');
//$routes->get('lang/{locale}', [Language::class, 'index']);



/**
 * Rotas de Autenticação
 */
/* $routes->group('auth', function ($routes) {
  $routes->get('', [Auth::class, 'index'], ["as" => 'auth']);
  $routes->get('login', [Auth::class, 'index'], ["as" => 'auth']);
  $routes->post('signin', [Auth::class, 'signin'], ["as" => 'auth.signin']);
  $routes->get('logout', [Auth::class, 'logout'], ["as" => 'auth.logout']);
  $routes->get('forgetpassword', [Auth::class, 'forgetpassword'], ["as" => 'auth.forgetpassword']);
}); */
// Rotas de autenticação específicas
$routes->group('auth', function ($routes) {

//var_dump("teste");die;
    // Login geral
    $routes->get('/', [Auth::class, 'index'], ["as" => 'auth']);
    $routes->get('login', [Auth::class, 'index'], ["as" => 'auth.login']);
    $routes->post('signin', [Auth::class, 'signin'], ["as" => 'auth.signin']);
    
    // Login específico para admin
    $routes->get('admin', [Auth::class, 'adminIndex'], ["as" => 'auth.admin']);
    $routes->post('admin/signin', [Auth::class, 'adminSignin'], ["as" => 'auth.admin.signin']);
    
    // Login específico para professores
    $routes->get('teachers', [Auth::class, 'teachersIndex'], ["as" => 'auth.teachers']);
    $routes->post('teachers/signin', [Auth::class, 'teachersSignin'], ["as" => 'auth.teachers.signin']);
    
    // Login específico para alunos
    $routes->get('students', [Auth::class, 'studentsIndex'], ["as" => 'auth.students']);
    $routes->post('students/signin', [Auth::class, 'studentsSignin'], ["as" => 'auth.students.signin']);
    
    // Logout e recuperação de senha
    $routes->get('logout', [Auth::class, 'logout'], ["as" => 'auth.logout']);
    $routes->get('forgetpassword', [Auth::class, 'forgetpassword'], ["as" => 'auth.forgetpassword']);
    $routes->post('send-reset-link', [Auth::class, 'sendResetLink'], ["as" => 'auth.send_reset']);
});

/**
 * Rotas Públicas
 */
/* $routes->get('/viewinvoice(:num)/(:any)', [Invoice::class, 'invoice'], ["as" => 'invoice/index/$1/$2']);

$routes->group('printpdf', function ($routes) {
  $routes->get('viewinvoice/(:num)/(:any)', [Invoice::class, 'index'], ["as" => 'invoice/index/$1/$2']);
  $routes->get('invoice/(:num)', [GeneratePdf::class, 'invoice'], ["as" => 'invoice/$1']);
  $routes->get('download_invoice/(:num)', [GeneratePdf::class, 'download_invoice'], ["as" => 'download_invoice/$1']);
}); */

/**
 * Rotas de Instalação
 */
/* $routes->group('install', function ($routes) {
  $routes->get('', [Install::class, 'index'], ["as" => 'install']);
  $routes->get('step1', [Install::class, 'step1'], ["as" => 'install.step1']);
  $routes->post('step2', [Install::class, 'step2'], ["as" => 'install.step2']);
  $routes->get('step3', [Install::class, 'step3'], ["as" => 'install.step3']);
  $routes->post('step4', [Install::class, 'step4'], ["as" => 'install.step4']);
  $routes->get('finish', [Install::class, 'finish'], ["as" => 'install.finish']);
}); */

/**
 * Área Administrativa - Gestão Escolar
 */
$routes->group('admin', ['filter' => 'auth:admin'], function ($routes) {

  // Dashboard
  $routes->get('dashboard', [Dashboard::class, 'index'], ["as" => 'dashboard']);

  /**
   * Gestão Académica
   */
  $routes->group('academic', function ($routes) {
    // Anos Letivos
    $routes->group('years', function ($routes) {
        $routes->get('', [AcademicYears::class, 'index'], ["as" => 'academic.years']);
        $routes->get('form-add', [AcademicYears::class, 'form'], ["as" => 'academic.years.form']);
        $routes->get('form-edit/(:num)', [AcademicYears::class, 'form'], ["as" => 'academic.years.form/$1']);
        $routes->post('save', [AcademicYears::class, 'save'], ["as" => 'academic.years.save']);
        $routes->get('set-current/(:num)', [AcademicYears::class, 'setCurrent'], ["as" => 'academic.years.setCurrent']); // <-- ADICIONE ESTA LINHA
        $routes->get('delete/(:num)', [AcademicYears::class, 'delete'], ["as" => 'academic.years.delete/$1']);
        // No grupo academic/years
        $routes->get('check-dates/(:num)', [AcademicYears::class, 'checkDates'], ["as" => 'academic.years.check-dates']);


    });

      // Semestres/Trimestres
    $routes->group('semesters', function ($routes) {
        $routes->get('', [Semesters::class, 'index'], ["as" => 'academic.semesters']);
        $routes->get('form-add', [Semesters::class, 'form'], ["as" => 'academic.semesters.form']);
        $routes->get('form-edit/(:num)', [Semesters::class, 'form/$1'], ["as" => 'academic.semesters.form.edit']);
        $routes->post('save', [Semesters::class, 'save'], ["as" => 'academic.semesters.save']);
        $routes->get('view/(:num)', [Semesters::class, 'view/$1'], ["as" => 'academic.semesters.view']);
        $routes->get('set-current/(:num)', [Semesters::class, 'setCurrent/$1'], ["as" => 'academic.semesters.setCurrent']); // <-- ADICIONAR ESTA LINHA
        $routes->get('delete/(:num)', [Semesters::class, 'delete/$1'], ["as" => 'academic.semesters.delete']);
        $routes->get('get-dates/(:num)', [Semesters::class, 'getDates/$1'], ["as" => 'academic.semesters.getDates']);
    });

    // Calendário Escolar
    $routes->group('calendar', function ($routes) {
      $routes->get('', [SchoolCalendar::class, 'index'], ["as" => 'academic.calendar']);
      $routes->post('save-event', [SchoolCalendar::class, 'saveEvent'], ["as" => 'academic.calendar.save']);
      $routes->get('get-events', [SchoolCalendar::class, 'getEvents'], ["as" => 'academic.calendar.events']);
    });
  });


        // Cursos (Ensino Médio)
        $routes->group('courses', function ($routes) {
            $routes->get('/', [Courses::class, 'index'], ['as' => 'admin.courses']);
            $routes->get('form-add', [Courses::class, 'form'], ['as' => 'admin.courses.form']);
            $routes->get('form-edit/(:num)', [Courses::class, 'form/$1'], ['as' => 'admin.courses.form.edit']);
            $routes->post('save', [Courses::class, 'save'], ['as' => 'admin.courses.save']);
            $routes->get('view/(:num)', [Courses::class, 'view/$1'], ['as' => 'admin.courses.view']);
            $routes->get('delete/(:num)', [Courses::class, 'delete/$1'], ['as' => 'admin.courses.delete']);
            
            // Currículo do curso
            $routes->get('curriculum/(:num)', [CourseCurriculum::class, 'index/$1'], ['as' => 'admin.courses.curriculum']);
            $routes->post('curriculum/add-discipline', [CourseCurriculum::class, 'addDiscipline'], ['as' => 'admin.courses.curriculum.add']);
            $routes->get('curriculum/edit/(:num)', [CourseCurriculum::class, 'editDiscipline/$1'], ['as' => 'admin.courses.curriculum.edit']);
            $routes->post('curriculum/update/(:num)', [CourseCurriculum::class, 'updateDiscipline/$1'], ['as' => 'admin.courses.curriculum.update']);
            $routes->get('curriculum/remove/(:num)', [CourseCurriculum::class, 'removeDiscipline/$1'], ['as' => 'admin.courses.curriculum.remove']);
            $routes->get('curriculum/get-available/(:num)/(:num)', [CourseCurriculum::class, 'getAvailableDisciplines/$1/$2'], ['as' => 'admin.courses.curriculum.available']);
        });


  /**
   * Gestão de Classes/Turmas
   */
  $routes->group('classes', function ($routes) {
    // Níveis de Ensino
    $routes->group('levels', function ($routes) {
      $routes->get('', [GradeLevels::class, 'index'], ["as" => 'classes.levels']);
      $routes->post('save', [GradeLevels::class, 'save'], ["as" => 'classes.levels.save']);
      $routes->get('delete/(:num)', [GradeLevels::class, 'delete'], ["as" => 'classes.levels.delete/$1']);
    });

    // Classes/Turmas
    /* $routes->group('classes', function ($routes) {
      $routes->get('', [Classes::class, 'index'], ["as" => 'classes']);
      $routes->get('form-add', [Classes::class, 'form'], ["as" => 'classes.form']);
      $routes->get('form-edit/(:num)', [Classes::class, 'form'], ["as" => 'classes.form/$1']);
      $routes->post('save', [Classes::class, 'save'], ["as" => 'classes.save']);
      $routes->get('delete/(:num)', [Classes::class, 'delete'], ["as" => 'classes.delete/$1']);
      $routes->get('view/(:num)', [Classes::class, 'view'], ["as" => 'classes.view/$1']);
      $routes->get('list-students/(:num)', [Classes::class, 'listStudents'], ["as" => 'classes.students/$1']);
    }); */

    // Classes/Turmas
    $routes->group('classes', function ($routes) {
        $routes->get('', [Classes::class, 'index'], ["as" => 'classes']);
        $routes->get('form-add', [Classes::class, 'form'], ["as" => 'classes.form']);
        $routes->get('form-edit/(:num)', [Classes::class, 'form/$1'], ["as" => 'classes.form.edit']);
        $routes->post('save', [Classes::class, 'save'], ["as" => 'classes.save']); // <-- MÉTODO SAVE AQUI
        $routes->get('delete/(:num)', [Classes::class, 'delete/$1'], ["as" => 'classes.delete']);
        $routes->get('activate/(:num)', [Classes::class, 'activate/$1'], ["as" => 'classes.activate']);
        $routes->get('view/(:num)', [Classes::class, 'view/$1'], ["as" => 'classes.view']);
        $routes->get('list-students/(:num)', [Classes::class, 'listStudents/$1'], ["as" => 'classes.students']);

        // Rota para obter disciplinas da turma (AJAX) - FALTANDO
        $routes->get('get-class-disciplines/(:num)', [Classes::class, 'getClassDisciplines/$1'], ["as" => 'classes.get-disciplines']);
        
        // Rota para obter horário da turma - FALTANDO
        $routes->get('schedule/(:num)', [Classes::class, 'schedule/$1'], ["as" => 'classes.schedule']);
        
        // Rota para exportar lista de alunos da turma - FALTANDO
        $routes->get('export-students/(:num)', [Classes::class, 'exportStudents/$1'], ["as" => 'classes.export-students']);
        
        // Rota para imprimir pauta da turma - FALTANDO
        $routes->get('print-sheet/(:num)', [Classes::class, 'printSheet/$1'], ["as" => 'classes.print-sheet']);
       
    });
      $routes->get('get-by-level-and-year/(:num)/(:num)', [Classes::class, 'getByLevelAndYear/$1/$2'], ["as" => 'classes.get-by-level-and-year']);
      $routes->get('check-availability/(:num)', [Classes::class, 'checkAvailability/$1'], ["as" => 'classes.check-availability']);

    // Disciplinas
    $routes->group('subjects', function ($routes) {
      $routes->get('', [Disciplines::class, 'index'], ["as" => 'classes.subjects']);
      $routes->post('save', [Disciplines::class, 'save'], ["as" => 'classes.subjects.save']);
      $routes->get('delete/(:num)', [Disciplines::class, 'delete'], ["as" => 'classes.subjects.delete/$1']);
    });
    // Alocação de Disciplinas por Classe
    $routes->group('class-subjects', function ($routes) {
        $routes->get('', [ClassSubjects::class, 'index'], ["as" => 'classes.class-subjects']);
        $routes->get('assign', [ClassSubjects::class, 'assign'], ["as" => 'classes.class-subjects.assign']);
        $routes->post('assign', [ClassSubjects::class, 'assignSave'], ["as" => 'classes.class-subjects.assign.save']);
        $routes->get('get-by-class/(:num)', [ClassSubjects::class, 'getByClass'], ["as" => 'classes.class-subjects.get/$1']);
        
            // ROTA DE DELETE 
        $routes->get('delete/(:num)', [ClassSubjects::class, 'delete/$1'], ["as" => 'classes.class-subjects.delete']);

        // NOVAS ROTAS AJAX
        $routes->get('get-levels-by-course/(:num)', [ClassSubjects::class, 'getLevelsByCourse/$1'], ["as" => 'classes.class-subjects.get-levels']);
        $routes->get('get-levels-by-course', [ClassSubjects::class, 'getLevelsByCourse'], ["as" => 'classes.class-subjects.get-levels-all']);
        $routes->get('get-classes-by-course-level/(:num)/(:num)', [ClassSubjects::class, 'getClassesByCourseAndLevel/$1/$2'], ["as" => 'classes.class-subjects.get-classes']);
        $routes->get('get-available-disciplines/(:num)', [ClassSubjects::class, 'getAvailableDisciplinesForClass/$1'], ["as" => 'classes.class-subjects.available-disciplines']);
        
        // Rotas para atribuir professores
        $routes->get('assign-teachers/(:num)', [ClassSubjects::class, 'assignTeachers/$1'], ["as" => 'classes.class-subjects.assign-teachers']);
        $routes->post('save-teachers', [ClassSubjects::class, 'saveTeachers'], ["as" => 'classes.class-subjects.save-teachers']);
    });
  });

  /**
   * Gestão de Alunos (Estudantes)
   */
  $routes->group('students', function ($routes) {

    $routes->get('', [Clients::class, 'students'], ["as" => 'students']);
    $routes->get('form-add', [Clients::class, 'studentForm'], ["as" => 'students.form']);
    $routes->get('form-edit/(:num)', [Clients::class, 'studentForm'], ["as" => 'students.form/$1']);
    $routes->post('save', [Clients::class, 'saveStudent'], ["as" => 'students.save']);
    $routes->get('delete/(:num)', [Clients::class, 'delete'], ["as" => 'students.delete/$1']);
    $routes->get('view/(:num)', [Clients::class, 'viewStudent'], ["as" => 'students.view/$1']);
    $routes->get('get_students_table', [Clients::class, 'get_students_table'], ["as" => 'students.get_table']);

    // Matrículas
    $routes->group('enrollments', function ($routes) {
        $routes->get('', [Enrollments::class, 'index'], ["as" => 'students.enrollments']);
        $routes->get('pending', [Enrollments::class, 'pending'], ["as" => 'students.enrollments.pending']); 
        $routes->get('form-add', [Enrollments::class, 'form'], ["as" => 'students.enrollments.form']);
        $routes->get('form-edit/(:num)', [Enrollments::class, 'form/$1'], ["as" => 'students.enrollments.form.edit']);
        $routes->post('save', [Enrollments::class, 'save'], ["as" => 'students.enrollments.save']);
        $routes->get('view/(:num)', [Enrollments::class, 'view'], ["as" => 'students.enrollments.view/$1']);
        $routes->get('history/(:num)', [Enrollments::class, 'history'], ["as" => 'students.enrollments.history/$1']);
        $routes->get('approve/(:num)', [Enrollments::class, 'approve/$1'], ["as" => 'students.enrollments.approve']);
        $routes->get('delete/(:num)', [Enrollments::class, 'delete/$1'], ["as" => 'students.enrollments.delete']);
    });

    // Encarregados de Educação
    $routes->group('guardians', function ($routes) {
      $routes->get('', [StudentGuardians::class, 'index'], ["as" => 'students.guardians']);
      $routes->post('save', [StudentGuardians::class, 'save'], ["as" => 'students.guardians.save']);
      $routes->get('get-by-student/(:num)', [StudentGuardians::class, 'getByStudent'], ["as" => 'students.guardians.get/$1']);
    });

    // Presenças
    $routes->group('attendance', function ($routes) {
      $routes->get('', [Attendance::class, 'index'], ["as" => 'students.attendance']);
      $routes->post('save', [Attendance::class, 'save'], ["as" => 'students.attendance.save']);
      $routes->get('report', [Attendance::class, 'report'], ["as" => 'students.attendance.report']);
    });
  });

  /**
   * Gestão de Professores
   */
  $routes->group('teachers', function ($routes) {
    $routes->get('', [Teachers::class, 'index'], ["as" => 'teachers']);
    $routes->get('form-add', [Teachers::class, 'form'], ["as" => 'teachers.form']);
    $routes->get('form-edit/(:num)', [Teachers::class, 'form'], ["as" => 'teachers.form/$1']);
    $routes->post('save', [Teachers::class, 'save'], ["as" => 'teachers.save']);
    $routes->get('delete/(:num)', [Teachers::class, 'delete'], ["as" => 'teachers.delete/$1']);
    $routes->get('view/(:num)', [Teachers::class, 'view'], ["as" => 'teachers.view/$1']);
    $routes->get('assign-class/(:num)', [Teachers::class, 'assignClass'], ["as" => 'teachers.assign-class/$1']);
    $routes->post('save-assignment', [Teachers::class, 'saveAssignment'], ["as" => 'teachers.save-assignment']);

    // ✅ NOVA ROTA PARA ESTATÍSTICAS AJAX
    $routes->get('get-stats', [Teachers::class, 'getStats'], ["as" => 'teachers.get-stats']);
    
    // Outras rotas que você possa ter
    $routes->get('activate/(:num)', [Teachers::class, 'activate/$1'], ["as" => 'teachers.activate']);
    $routes->get('schedule/(:num)', [Teachers::class, 'schedule/$1'], ["as" => 'teachers.schedule']);
    $routes->get('export', [Teachers::class, 'export'], ["as" => 'teachers.export']);

    // Rota para ativar professor - FALTANDO (você tem delete mas não activate)
    $routes->get('activate/(:num)', [Teachers::class, 'activate/$1'], ["as" => 'teachers.activate']);
    
    // Rota para obter disciplinas do professor (AJAX) - FALTANDO
    $routes->get('get-disciplines/(:num)', [Teachers::class, 'getDisciplines/$1'], ["as" => 'teachers.get-disciplines']);
    
    // Rota para remover atribuição específica - FALTANDO
    $routes->get('remove-assignment/(:num)', [Teachers::class, 'removeAssignment/$1'], ["as" => 'teachers.remove-assignment']);
  });

  /**
   * Gestão de Propinas e Taxas
   */
  $routes->group('fees', function ($routes) {
    // Tipos de Taxas
    $routes->group('types', function ($routes) {
      $routes->get('', [Fees::class, 'types'], ["as" => 'fees.types']);
      $routes->post('save', [Fees::class, 'saveType'], ["as" => 'fees.types.save']);
      $routes->get('delete/(:num)', [Fees::class, 'deleteType'], ["as" => 'fees.types.delete/$1']);
    });

    // Estrutura de Propinas
    $routes->group('structure', function ($routes) {
      $routes->get('', [TuitionFees::class, 'index'], ["as" => 'fees.structure']);
      $routes->post('save', [TuitionFees::class, 'save'], ["as" => 'fees.structure.save']);
      $routes->get('get-by-class/(:num)', [TuitionFees::class, 'getByClass'], ["as" => 'fees.structure.get/$1']);
    });

    // Pagamentos
    $routes->group('payments', function ($routes) {
      $routes->get('', [FeesPayments::class, 'index'], ["as" => 'fees.payments']);
      $routes->get('form-add', [FeesPayments::class, 'form'], ["as" => 'fees.payments.form']);
      $routes->post('save', [FeesPayments::class, 'save'], ["as" => 'fees.payments.save']);
      $routes->get('receipt/(:num)', [FeesPayments::class, 'receipt'], ["as" => 'fees.payments.receipt/$1']);
      $routes->get('history/(:num)', [FeesPayments::class, 'history'], ["as" => 'fees.payments.history/$1']);
    });
  });

  /**
   * Gestão de Exames e Avaliações
   */
  $routes->group('exams', function ($routes) {
    // Tipos de Exames
    $routes->group('boards', function ($routes) {
      $routes->get('', [ExamBoards::class, 'index'], ["as" => 'exams.boards']);
      $routes->post('save', [ExamBoards::class, 'save'], ["as" => 'exams.boards.save']);
      $routes->get('delete/(:num)', [ExamBoards::class, 'delete'], ["as" => 'exams.boards.delete/$1']);
    });

    // Exames
    $routes->get('', [Exams::class, 'index'], ["as" => 'exams']);
    $routes->get('form-add', [Exams::class, 'form'], ["as" => 'exams.form']);
    $routes->get('form-edit/(:num)', [Exams::class, 'form'], ["as" => 'exams.form/$1']);
    $routes->post('save', [Exams::class, 'save'], ["as" => 'exams.save']);
    $routes->get('delete/(:num)', [Exams::class, 'delete'], ["as" => 'exams.delete/$1']);
    $routes->get('schedule', [Exams::class, 'schedule'], ["as" => 'exams.schedule']);
    $routes->get('view/(:num)', [Exams::class, 'view'], ["as" => 'exams.view/$1']);
    $routes->post('save-results', [Exams::class, 'saveResults'], ["as" => 'exams.save-results']);
    $routes->get('publish/(:num)', [Exams::class, 'publish'], ["as" => 'exams.publish/$1']);

    // Resultados
    $routes->group('results', function ($routes) {
      $routes->get('', [ExamResults::class, 'index'], ["as" => 'exams.results']);
      $routes->post('save', [ExamResults::class, 'save'], ["as" => 'exams.results.save']);
      $routes->get('report-card/(:num)', [ExamResults::class, 'reportCard'], ["as" => 'exams.results.report/$1']);
      $routes->get('transcript/(:num)', [ExamResults::class, 'transcript'], ["as" => 'exams.results.transcript/$1']);
    });
  });

  /**
   * Gestão Financeira (mantendo módulos existentes)
   */
  $routes->group('financial', function ($routes) {
    // Faturas/Recibos
    $routes->group('invoices', function ($routes) {
      $routes->get('', [Invoices::class, 'index'], ["as" => 'financial.invoices']);
      $routes->get('view/(:num)', [Invoices::class, 'viewinvoices'], ["as" => 'financial.invoices.view/$1']);
      $routes->get('form-add', [Invoices::class, 'form'], ["as" => 'financial.invoices.form']);
      $routes->post('save', [Invoices::class, 'save'], ["as" => 'financial.invoices.save']);
      $routes->get('delete/(:num)', [Invoices::class, 'delete'], ["as" => 'financial.invoices.delete/$1']);
    });

    // Pagamentos
    $routes->group('payments', function ($routes) {
      $routes->get('', [Payments::class, 'payments'], ['as' => 'financial.payments']);
      $routes->get('modes', [Payments::class, 'paymentsmode'], ["as" => 'financial.payments.modes']);
      $routes->post('modes', [Payments::class, 'save_paymentsmode'], ["as" => 'financial.payments.save_modes']);
      $routes->get('receipts', [Payments::class, 'receipts'], ['as' => 'financial.payments.receipts']);
    });

    // Despesas
    $routes->group('expenses', function ($routes) {
      $routes->get('', [Expenses::class, 'index'], ["as" => 'financial.expenses']);
      $routes->get('form-add', [Expenses::class, 'form'], ["as" => 'financial.expenses.form']);
      $routes->post('save', [Expenses::class, 'save'], ["as" => 'financial.expenses.save']);
      $routes->get('delete/(:num)', [Expenses::class, 'delete'], ["as" => 'financial.expenses.delete/$1']);
    });

    // Moedas
    $routes->group('currencies', function ($routes) {
      $routes->get('', [Currencies::class, 'currencies'], ["as" => 'financial.currencies']);
      $routes->post('save', [Currencies::class, 'save'], ["as" => 'financial.currencies.save']);
    });

    // Taxas
    $routes->group('taxes', function ($routes) {
      $routes->get('', [Taxes::class, 'index'], ["as" => 'financial.taxes']);
      $routes->post('save', [Taxes::class, 'save'], ["as" => 'financial.taxes.save']);
    });
  });

  /**
   * Gestão de Stock/Inventário
   */
/*   $routes->group('inventory', function ($routes) {
    $routes->get('', [Inventory::class, 'index'], ["as" => 'inventory']);
    $routes->get('goods-receipt', [Inventory::class, 'goods_receipt'], ["as" => 'inventory.goods_receipt']);
    $routes->get('form-add-goods-receipt', [Inventory::class, 'form_goods_receipt'], ["as" => 'inventory.form_goods_receipt']);
    $routes->post('save-goods-receipt', [Inventory::class, 'save_goods_receipt'], ["as" => 'inventory.save_goods_receipt']);
  }); */

  /**
   * Relatórios
   */
  $routes->group('reports', function ($routes) {
    $routes->get('', [Reports::class, 'index'], ["as" => 'reports']);
    $routes->get('academic', [Reports::class, 'academic'], ["as" => 'reports.academic']);
    $routes->get('financial', [Reports::class, 'financial'], ["as" => 'reports.financial']);
    $routes->get('students', [Reports::class, 'students'], ["as" => 'reports.students']);
    $routes->get('attendance', [Reports::class, 'attendance'], ["as" => 'reports.attendance']);
    $routes->get('exams', [Reports::class, 'exams'], ["as" => 'reports.exams']);
    $routes->get('teachers', [Reports::class, 'teachers'], ["as" => 'reports.teachers']);
    $routes->get('fees', [Reports::class, 'fees'], ["as" => 'reports.fees']);
  });

  /**
   * Configurações da Escola
   */
  $routes->group('school-settings', function ($routes) {
    $routes->get('', [SchoolSettings::class, 'index'], ["as" => 'school.settings']);
    $routes->post('save-general', [SchoolSettings::class, 'saveGeneral'], ["as" => 'school.settings.save_general']);
    $routes->post('save-academic', [SchoolSettings::class, 'saveAcademic'], ["as" => 'school.settings.save_academic']);
    $routes->post('save-fees', [SchoolSettings::class, 'saveFees'], ["as" => 'school.settings.save_fees']);
  });

  /**
   * Gestão de Utilizadores e Permissões (mantendo existente)
   */
  $routes->group('users', function ($routes) {
    $routes->get('', [Users::class, 'index'], ["as" => 'users']);
    $routes->get('form-add', [Users::class, 'form'], ["as" => 'users.form']);
    $routes->get('form-edit/(:num)', [Users::class, 'form'], ["as" => 'users.form/$1']);
    $routes->post('save', [Users::class, 'save'], ["as" => 'users.save']);
    $routes->get('profile', [Users::class, 'profile'], ["as" => 'users.profile']);
    $routes->get('view/(:num)', [Users::class, 'view_user'], ["as" => 'users.view/$1']);
    $routes->get('delete/(:num)', [Users::class, 'delete'], ["as" => 'users.delete/$1']);
  });

    /**
     * Gestão de Perfis e Permissões
     */
    $routes->group('roles', function ($routes) {
        // Lista de perfis
        $routes->get('', [Roles::class, 'index'], ["as" => 'roles']);
        
        // Página de permissões de um perfil específico (com ID)
        $routes->get('permission/(:num)', [Roles::class, 'role'], ["as" => 'roles.permission']);
        
        // Atualizar permissões (POST)
        $routes->post('update-permissions', [Roles::class, 'update_permissions'], ["as" => 'roles.permissions.update']);
    });

  /**
   * Configurações Gerais (mantendo existente)
   */
  $routes->group('settings', function ($routes) {
    $routes->get('', [Settings::class, 'settings'], ["as" => 'settings']);
    $routes->get('general', [Settings::class, 'generalsettings'], ["as" => 'settings.general']);
    $routes->post('save-general', [Settings::class, 'saveGeneralsettings'], ["as" => 'settings.save_general']);
    $routes->post('save-company', [Settings::class, 'saveDatacompany'], ["as" => 'settings.save_company']);
    $routes->get('email', [Settings::class, 'emailsettings'], ["as" => 'settings.email']);
    $routes->get('payment', [Settings::class, 'paymentsettings'], ["as" => 'settings.payment']);
    $routes->get('permissions', [Settings::class, 'grouppermissions'], ["as" => 'settings.permissions']);
  });

    /**
   * Ferramentas
   */
  $routes->group('tools', function ($routes) {
      $routes->get('logs', [Logs::class, 'index'], ["as" => 'tools.logs']);
      $routes->get('logs/view/(:num)', [Logs::class, 'view'], ["as" => 'tools.logs.view/$1']);
      $routes->get('logs/clear', [Logs::class, 'clear'], ["as" => 'tools.logs.clear']);
      $routes->get('logs/export', [Logs::class, 'export'], ["as" => 'tools.logs.export']);
      $routes->post('logs/delete', [Logs::class, 'delete'], ["as" => 'tools.logs.delete']);
  });

    // Central de Documentos
    $routes->group('documents', function ($routes) {
        $routes->get('/', [AdminDocuments::class, 'index'], ['as' => 'admin.documents']);
        $routes->get('pending', [AdminDocuments::class, 'pending'], ['as' => 'admin.documents.pending']);
        $routes->get('verified', [AdminDocuments::class, 'verified'], ['as' => 'admin.documents.verified']);
        $routes->get('types', [AdminDocuments::class, 'types'], ['as' => 'admin.documents.types']);
        $routes->post('types/save', [AdminDocuments::class, 'saveType'], ['as' => 'admin.documents.types.save']);
        $routes->get('types/delete/(:num)', [AdminDocuments::class, 'deleteType/$1'], ['as' => 'admin.documents.types.delete']);
        $routes->get('requests', [AdminDocuments::class, 'requests'], ['as' => 'admin.documents.requests']);
        $routes->get('request/view/(:num)', [AdminDocuments::class, 'viewRequest/$1'], ['as' => 'admin.documents.view-request']);
        $routes->post('request/process/(:num)', [AdminDocuments::class, 'processRequest/$1'], ['as' => 'admin.documents.process-request']);
        $routes->post('request/reject/(:num)', [AdminDocuments::class, 'rejectRequest/$1'], ['as' => 'admin.documents.reject-request']);
        $routes->post('request/deliver/(:num)', [AdminDocuments::class, 'deliverRequest/$1'], ['as' => 'admin.documents.deliver-request']);
        
        // Rotas para visualização e download - CORRIGIDAS
        $routes->get('view/(:num)', [AdminDocuments::class, 'view/$1'], ['as' => 'admin.documents.view']);
        $routes->get('serve/(:num)', [AdminDocuments::class, 'serve/$1'], ['as' => 'admin.documents.serve']);
        $routes->get('download/(:num)', [AdminDocuments::class, 'download/$1'], ['as' => 'admin.documents.download']); // <- CORREÇÃO AQUI
        
        $routes->get('verify/(:num)', [AdminDocuments::class, 'verify/$1'], ['as' => 'admin.documents.verify']);
        $routes->post('verify/save/(:num)', [AdminDocuments::class, 'saveVerification/$1'], ['as' => 'admin.documents.save-verification']);
        $routes->get('reports', [AdminDocuments::class, 'reports'], ['as' => 'admin.documents.reports']);
        $routes->get('export', [AdminDocuments::class, 'export'], ['as' => 'admin.documents.export']);

           // Rotas para Documentos Solicitáveis (tbl_requestable_documents)
        $routes->get('requestable', [AdminDocuments::class, 'requestableTypes'], ['as' => 'admin.documents.requestable']);
        $routes->post('requestable/save', [AdminDocuments::class, 'saveRequestableType'], ['as' => 'admin.documents.requestable.save']);
        $routes->get('requestable/delete/(:num)', [AdminDocuments::class, 'deleteRequestableType/$1'], ['as' => 'admin.documents.requestable.delete']);
    });

    
    $routes->group('document-generator', function ($routes) {
        $routes->get('/', [DocumentGenerator::class, 'index'], ['as' => 'admin.document-generator']);
        $routes->get('pending', [DocumentGenerator::class, 'pending'], ['as' => 'admin.document-generator.pending']);
        $routes->get('generated', [DocumentGenerator::class, 'generated'], ['as' => 'admin.document-generator.generated']);
        $routes->get('templates', [DocumentGenerator::class, 'templates'], ['as' => 'admin.document-generator.templates']);
        $routes->get('generate/(:num)', [DocumentGenerator::class, 'generate/$1'], ['as' => 'admin.document-generator.generate']);
        $routes->post('generate/bulk', [DocumentGenerator::class, 'generateBulk'], ['as' => 'admin.document-generator.generate-bulk']);
        $routes->get('preview/(:num)', [DocumentGenerator::class, 'preview/$1'], ['as' => 'admin.document-generator.preview']);
        $routes->get('download/(:num)', [DocumentGenerator::class, 'download/$1'], ['as' => 'admin.document-generator.download']);
        $routes->post('template/save', [DocumentGenerator::class, 'saveTemplate'], ['as' => 'admin.document-generator.template.save']);
        $routes->get('template/edit/(:num)', [DocumentGenerator::class, 'editTemplate/$1'], ['as' => 'admin.document-generator.template.edit']);
    });


});
/**
 * Área dos Professores 
 */
$routes->group('teachers', function ($routes) {
    
    // Rotas de autenticação - SEM FILTRO
    $routes->group('auth', function ($routes) {
        $routes->get('', [Auth::class, 'teachersIndex'], ["as" => 'teachers.auth']);
        $routes->get('login', [Auth::class, 'teachersIndex'], ["as" => 'teachers.auth']);
        $routes->post('signin', [Auth::class, 'teachersSignin'], ["as" => 'teachers.auth.signin']);
        $routes->get('logout', [Auth::class, 'logout'], ["as" => 'teachers.auth.logout']);
    });
    
    // Rotas protegidas - APLICAR FILTRO AQUI
    $routes->group('', ['filter' => 'auth:teachers'], function ($routes) {
        
        $routes->get('dashboard', [TeachersDashboard::class, 'index'], ["as" => 'teachers.dashboard']);
        
        // Profile
          $routes->get('profile', [TeachersProfile::class, 'index'], ['as' => 'teachers.profile']);
          $routes->post('profile/update', [TeachersProfile::class, 'update'], ['as' => 'teachers.profile.update']);
          $routes->post('profile/update-photo', [TeachersProfile::class, 'updatePhoto'], ['as' => 'teachers.profile.update-photo']);

        // Minhas Turmas
        $routes->group('classes', function ($routes) {
            $routes->get('', [TeacherClasses::class, 'index'], ["as" => 'teachers.classes']);
            $routes->get('view/(:num)', [TeacherClasses::class, 'view'], ["as" => 'teachers.classes.view/$1']);
            $routes->get('students/(:num)', [TeacherClasses::class, 'students'], ["as" => 'teachers.classes.students/$1']);
        });
        
       // Avaliações
        $routes->group('exams', function ($routes) {
            $routes->get('', [TeacherExams::class, 'index'], ["as" => 'teachers.exams']);
            $routes->get('form-add', [TeacherExams::class, 'form'], ["as" => 'teachers.exams.form']);
            $routes->get('form-edit/(:num)', [TeacherExams::class, 'form/$1'], ["as" => 'teachers.exams.form.edit']);
            $routes->post('save', [TeacherExams::class, 'save'], ["as" => 'teachers.exams.save']);
            $routes->get('grade/(:num)', [TeacherExams::class, 'grade'], ["as" => 'teachers.exams.grade/$1']);
            $routes->post('save-grades', [TeacherExams::class, 'saveGrades'], ["as" => 'teachers.exams.save_grades']);
            $routes->get('get-disciplines/(:num)', [TeacherExams::class, 'getDisciplines/$1'], ["as" => 'teachers.exams.get-disciplines']);
            $routes->get('results/(:num)', [TeacherExams::class, 'results/$1'], ["as" => 'teachers.exams.results']); // <-- NOVA ROTA
        });
         // Notas (Avaliações Contínuas)
        $routes->group('grades', function ($routes) {
            $routes->get('', [TeacherGrades::class, 'index'], ["as" => 'teachers.grades']);
            $routes->post('save', [TeacherGrades::class, 'save'], ["as" => 'teachers.grades.save']);
            $routes->get('get-disciplines/(:num)', [TeacherGrades::class, 'getDisciplines/$1'], ["as" => 'teachers.grades.get-disciplines']); // <-- AGORA AQUI
            $routes->get('report/select', [TeacherGrades::class, 'selectReport'], ["as" => 'teachers.grades.report.select']);
            $routes->get('report/(:num)', [TeacherGrades::class, 'report'], ["as" => 'teachers.grades.report/$1']);
        });
        
      // Presenças
      $routes->group('attendance', function ($routes) {
          $routes->get('', [TeacherAttendance::class, 'index'], ["as" => 'teachers.attendance']);
          $routes->post('save', [TeacherAttendance::class, 'save'], ["as" => 'teachers.attendance.save']);
          $routes->get('report', [TeacherAttendance::class, 'report'], ["as" => 'teachers.attendance.report']);
          $routes->get('get-disciplines/(:num)', [TeacherAttendance::class, 'getDisciplines/$1'], ["as" => 'teachers.attendance.get-disciplines']);
      });

        // Documentos
      $routes->group('documents', function ($routes) {
          $routes->get('/', [TeacherDocuments::class, 'index'], ['as' => 'teachers.documents']);
          $routes->get('requests', [TeacherDocuments::class, 'requests'], ['as' => 'teachers.documents.requests']);
          $routes->get('archive', [TeacherDocuments::class, 'archive'], ['as' => 'teachers.documents.archive']);
          $routes->post('upload', [TeacherDocuments::class, 'upload'], ['as' => 'teachers.documents.upload']);
          $routes->get('download/(:num)', [TeacherDocuments::class, 'download/$1'], ['as' => 'teachers.documents.download']);
          $routes->get('view/(:num)', [TeacherDocuments::class, 'view/$1'], ['as' => 'teachers.documents.view']);
          $routes->get('delete/(:num)', [TeacherDocuments::class, 'delete/$1'], ['as' => 'teachers.documents.delete']);
          $routes->post('request', [TeacherDocuments::class, 'createRequest'], ['as' => 'teachers.documents.request']);
          $routes->get('request/(:num)', [TeacherDocuments::class, 'viewRequest/$1'], ['as' => 'teachers.documents.view-request']);
          $routes->get('request/cancel/(:num)', [TeacherDocuments::class, 'cancelRequest/$1'], ['as' => 'teachers.documents.cancel-request']);
      });

    });
});
/**
 * Área dos Alunos/Estudantes
 */
$routes->group('students', function ($routes) {

   
    // Rotas de autenticação - SEM FILTRO
    $routes->group('auth', function ($routes) {
        $routes->get('', [Auth::class, 'studentsIndex'], ["as" => 'students.auth']);
        $routes->get('login', [Auth::class, 'studentsIndex'], ["as" => 'students.auth']);
        $routes->post('signin', [Auth::class, 'studentsSignin'], ["as" => 'students.auth.signin']);
        $routes->get('logout', [Auth::class, 'logout'], ["as" => 'students.auth.logout']);
    });
    // Rotas protegidas - APLICAR FILTRO AQUI
    $routes->group('', ['filter' => 'auth:students'], function ($routes) {
          
          $routes->get('dashboard', [StudentsDashboard::class, 'index'], ["as" => 'students.dashboard']);
           // Profile
          $routes->get('profile', [StudentsProfile::class, 'index'], ['as' => 'students.profile']);
          $routes->post('profile/update', [StudentsProfile::class, 'update'], ['as' => 'students.profile.update']);
          $routes->post('profile/update-photo', [StudentsProfile::class, 'updatePhoto'], ['as' => 'students.profile.update-photo']);
          $routes->get('profile/guardians', [StudentsProfile::class, 'guardians'], ['as' => 'students.profile.guardians']);
          $routes->get('profile/academic-history', [StudentsProfile::class, 'academicHistory'], ['as' => 'students.profile.academic-history']);

           // Minhas Disciplinas
          $routes->group('subjects', function ($routes) {
              $routes->get('/', [StudentsSubjects::class, 'index'], ['as' => 'students.subjects']);
              $routes->get('details/(:num)', [StudentsSubjects::class, 'details/$1'], ['as' => 'students.subjects.details']);
          });
          
        // Minhas Notas
        $routes->group('grades', function ($routes) {
          $routes->get('', [StudentGrades::class, 'index'], ["as" => 'students.grades']);
          $routes->get('subject/(:num)', [StudentGrades::class, 'bySubject'], ["as" => 'students.grades.subject/$1']);
          $routes->get('report-card', [StudentGrades::class, 'reportCard'], ["as" => 'students.grades.report']);
        });

        // Meus Exames
        $routes->group('exams', function ($routes) {
          $routes->get('', [StudentExams::class, 'index'], ["as" => 'students.exams']);
          $routes->get('schedule', [StudentExams::class, 'schedule'], ["as" => 'students.exams.schedule']);
          $routes->get('results', [StudentExams::class, 'results'], ["as" => 'students.exams.results']);
        });

        // Minhas Presenças
        $routes->group('attendance', function ($routes) {
          $routes->get('', [StudentAttendance::class, 'index'], ["as" => 'students.attendance']);
          $routes->get('history', [StudentAttendance::class, 'history'], ["as" => 'students.attendance.history']);
        }); 

        // Minhas Propinas
        $routes->group('fees', function ($routes) {
          $routes->get('', [StudentFees::class, 'index'], ["as" => 'students.fees']);
          $routes->get('history', [StudentFees::class, 'history'], ["as" => 'students.fees.history']);
          $routes->get('pay/(:num)', [StudentFees::class, 'pay'], ["as" => 'students.fees.pay/$1']);
          $routes->get('receipts', [StudentFees::class, 'receipts'], ["as" => 'students.fees.receipts']);
        });

          // Documentos
        $routes->group('documents', function ($routes) {
            $routes->get('/', [StudentADocuments::class, 'index'], ['as' => 'students.documents']);
            $routes->get('requests', [StudentADocuments::class, 'requests'], ['as' => 'students.documents.requests']);
            $routes->get('archive', [StudentADocuments::class, 'archive'], ['as' => 'students.documents.archive']);
            $routes->post('upload', [StudentADocuments::class, 'upload'], ['as' => 'students.documents.upload']);
            $routes->get('download/(:num)', [StudentADocuments::class, 'download/$1'], ['as' => 'students.documents.download']);
            $routes->get('view/(:num)', [StudentADocuments::class, 'view/$1'], ['as' => 'students.documents.view']);
            $routes->get('delete/(:num)', [StudentADocuments::class, 'delete/$1'], ['as' => 'students.documents.delete']);
            $routes->post('request', [StudentADocuments::class, 'createRequest'], ['as' => 'students.documents.request']);
            $routes->get('request/(:num)', [StudentADocuments::class, 'viewRequest/$1'], ['as' => 'students.documents.view-request']);
            $routes->get('request/cancel/(:num)', [StudentADocuments::class, 'cancelRequest/$1'], ['as' => 'students.documents.cancel-request']);
        });

    });

});

/**
 * Inclusão de Módulos
 */
if (file_exists(ROOTPATH . 'modules')) {
  $modulesPath = ROOTPATH . 'modules/';
  $modules = scandir($modulesPath);

  foreach ($modules as $module) {
    if ($module === '.' || $module === '..')
      continue;
    if (is_dir($modulesPath . '/' . $module)) {
      $routesPath = $modulesPath . $module . '/Config/Routes.php';
      if (file_exists($routesPath)) {
        require($routesPath);
      }
    }
  }
}