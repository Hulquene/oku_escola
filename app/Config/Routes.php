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
use App\Controllers\admin\Grades;
use App\Controllers\admin\AcademicRecords;

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
use App\Controllers\students\StudentAttendance as StudentAttendance;
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
// $routes->get('/', 'Home::index');
// Páginas Públicas
$routes->get('/', 'PublicSite::index');
$routes->get('/cursos', 'PublicSite::courses');
$routes->get('/sobre', 'PublicSite::about');
$routes->get('/contato', 'PublicSite::contact');

// Inscrição
$routes->get('/inscricao', 'PublicEnrollment::index');
$routes->post('/inscricao/submit', 'PublicEnrollment::submit');
//$routes->get('/viewinvoice', 'Home::index');
//$routes->get('lang/{locale}', [Language::class, 'index']);



// Rotas de autenticação específicas
$routes->group('auth', function ($routes) {
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

    // Visualizar detalhes (NOVO)
        $routes->get('view/(:num)', [AcademicYears::class, 'view'], ["as" => 'academic.years.view']);
    
        $routes->get('form-edit/(:num)', [AcademicYears::class, 'form'], ["as" => 'academic.years.form/$1']);
        $routes->post('save', [AcademicYears::class, 'save'], ["as" => 'academic.years.save']);
        $routes->get('set-current/(:num)', [AcademicYears::class, 'setCurrent'], ["as" => 'academic.years.setCurrent']); 
         // Ativar/Desativar
        $routes->post('toggle-active/(:num)', [AcademicYears::class, 'toggleActive'], ["as" => 'academic.years.toggleActive']);
        $routes->get('delete/(:num)', [AcademicYears::class, 'delete'], ["as" => 'academic.years.delete/$1']);
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
        $routes->get('get-by-year/(:num)', [Semesters::class, 'getByYear/$1'], ["as" => 'academic.semesters.getByYear']);
        $routes->post('process', [Semesters::class, 'process'], ['as' => 'admin.semesters.process']);
        $routes->get('info/(:num)', [Semesters::class, 'info/$1'], ['as' => 'admin.semesters.info']);
        $routes->get('conclude/(:num)', [Semesters::class, 'conclude/$1'], ['as' => 'academic.semesters.conclude']);
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

      // Adicione no grupo 'classes' das rotas
      $routes->get('get-by-year/(:num)', [Classes::class, 'getByYear/$1'], ["as" => 'classes.get-by-year']);

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

        // Adicionar estas rotas no grupo class-subjects
        $routes->post('save-bulk', [ClassSubjects::class, 'saveBulkAssignments'], ['as' => 'classes.class-subjects.save-bulk']);
        $routes->get('get-class-disciplines/(:num)/(:num)', [ClassSubjects::class, 'getClassDisciplinesWithAssignments/$1/$2'], ['as' => 'classes.class-subjects.get-detailed']);
        $routes->get('get-class-disciplines/(:num)', [ClassSubjects::class, 'getClassDisciplinesWithAssignments/$1'], ['as' => 'classes.class-subjects.get-detailed']);
        
    });
  });


  // ======================================================================
  // NOVAS ROTAS PARA GESTÃO DE EXAMES (ESTRUTURA HÍBRIDA)
  // ======================================================================

  /**
   * Gestão de Exames e Avaliações - NOVA ESTRUTURA
   */
  $routes->group('exams', function ($routes) {
    
    // Tipos de Exames
    $routes->group('boards', function ($routes) {
      $routes->get('', [ExamBoards::class, 'index'], ["as" => 'exams.boards']);
      $routes->post('save', [ExamBoards::class, 'save'], ["as" => 'exams.boards.save']);
      $routes->get('delete/(:num)', [ExamBoards::class, 'delete'], ["as" => 'exams.boards.delete/$1']);
    });
    // === PERÍODOS DE EXAME ===
    $routes->group('periods', function ($routes) {
        $routes->get('/', [\App\Controllers\admin\ExamPeriods::class, 'index'], ['as' => 'exams.periods']);
        $routes->get('create', [\App\Controllers\admin\ExamPeriods::class, 'create'], ['as' => 'exams.periods.create']);
        $routes->get('edit/(:num)', [\App\Controllers\admin\ExamPeriods::class, 'edit/$1'], ['as' => 'exams.periods.edit']);
        $routes->get('view/(:num)', [\App\Controllers\admin\ExamPeriods::class, 'view/$1'], ['as' => 'exams.periods.view']);
        $routes->post('save', [\App\Controllers\admin\ExamPeriods::class, 'save'], ['as' => 'exams.periods.save']);
        $routes->get('delete/(:num)', [\App\Controllers\admin\ExamPeriods::class, 'delete/$1'], ['as' => 'exams.periods.delete']);
        
        // Geração automática de calendário
        $routes->get('generate-schedule/(:num)', [\App\Controllers\admin\ExamPeriods::class, 'generateSchedule/$1'], ['as' => 'exams.periods.generate-schedule']);
        // $routes->post('generate-schedule/(:num)', [\App\Controllers\admin\ExamPeriods::class, 'generateSchedule/$1'], ['as' => 'exams.periods.generate-schedule.post']);
        $routes->post('save-schedule/(:num)', [\App\Controllers\admin\ExamPeriods::class, 'saveSchedule/$1'], ['as' => 'exams.periods.save-schedule']);

        // No grupo 'periods', adicione:
        $routes->get('start/(:num)', [\App\Controllers\admin\ExamPeriods::class, 'start/$1'], ['as' => 'exams.periods.start']);
        $routes->get('pause/(:num)', [\App\Controllers\admin\ExamPeriods::class, 'pause/$1'], ['as' => 'exams.periods.pause']);
        $routes->get('complete/(:num)', [\App\Controllers\admin\ExamPeriods::class, 'complete/$1'], ['as' => 'exams.periods.complete']);
        $routes->get('cancel/(:num)', [\App\Controllers\admin\ExamPeriods::class, 'cancel/$1'], ['as' => 'exams.periods.cancel']);
    });

    // === AGENDAMENTO DE EXAMES ===
    $routes->group('schedules', function ($routes) {
        $routes->get('/', [\App\Controllers\admin\ExamSchedules::class, 'index'], ['as' => 'exams.schedules']);
        $routes->get('create', [\App\Controllers\admin\ExamSchedules::class, 'create'], ['as' => 'exams.schedules.create']);
        $routes->get('edit/(:num)', [\App\Controllers\admin\ExamSchedules::class, 'edit/$1'], ['as' => 'exams.schedules.edit']);
        $routes->get('view/(:num)', [\App\Controllers\admin\ExamSchedules::class, 'view/$1'], ['as' => 'exams.schedules.view']);
        $routes->post('save', [\App\Controllers\admin\ExamSchedules::class, 'save'], ['as' => 'exams.schedules.save']);
        $routes->post('update/(:num)', [\App\Controllers\admin\ExamSchedules::class, 'update/$1'], ['as' => 'exams.schedules.update']);
        $routes->get('delete/(:num)', [\App\Controllers\admin\ExamSchedules::class, 'delete/$1'], ['as' => 'exams.schedules.delete']);

         // Rotas para calendário
        $routes->get('calendar-events', [\App\Controllers\admin\ExamSchedules::class, 'getCalendarEvents'], ['as' => 'admin.exams.schedules.calendar']);
        $routes->get('filtered-calendar-events', [\App\Controllers\admin\ExamSchedules::class, 'getFilteredCalendarEvents'], ['as' => 'admin.exams.schedules.filteredCalendar']);
        // Gestão de presenças e notas
        $routes->get('attendance/(:num)', [\App\Controllers\admin\ExamSchedules::class, 'attendance/$1'], ['as' => 'exams.schedules.attendance']);
        $routes->post('attendance/save/(:num)', [\App\Controllers\admin\ExamSchedules::class, 'saveAttendance/$1'], ['as' => 'exams.schedules.saveAttendance']);
        $routes->get('results/(:num)', [\App\Controllers\admin\ExamSchedules::class, 'results/$1'], ['as' => 'exams.schedules.results']);
        $routes->post('results/save/(:num)', [\App\Controllers\admin\ExamSchedules::class, 'saveResults/$1'], ['as' => 'exams.schedules.results.post']);
        
        // AJAX
        $routes->get('get-disciplines/(:num)', [\App\Controllers\admin\ExamSchedules::class, 'getDisciplinesByClass/$1'], ['as' => 'exams.schedules.get-disciplines']);
    });

    // === TIPOS DE EXAME (BOARDS) - Mantendo compatibilidade ===
    $routes->group('boards', function ($routes) {
        $routes->get('', [ExamBoards::class, 'index'], ["as" => 'exams.boards']);
        $routes->post('save', [ExamBoards::class, 'save'], ["as" => 'exams.boards.save']);
        $routes->get('delete/(:num)', [ExamBoards::class, 'delete'], ["as" => 'exams.boards.delete/$1']);
    });

    // === RESULTADOS E PAUTAS ===
    $routes->group('results', function ($routes) {
        $routes->get('', [ExamResults::class, 'index'], ["as" => 'exams.results']);
        $routes->post('save', [ExamResults::class, 'save'], ["as" => 'exams.results.save']);
        $routes->get('report-card/(:num)', [ExamResults::class, 'reportCard'], ["as" => 'exams.results.report/$1']);
        $routes->get('transcript/(:num)', [ExamResults::class, 'transcript'], ["as" => 'exams.results.transcript/$1']);
        $routes->get('class/(:num)', [ExamResults::class, 'classResults/$1'], ["as" => 'exams.results.class']);
    });

    // === PESOS DAS AVALIAÇÕES ===
    $routes->group('weights', function ($routes) {
        $routes->get('/', [\App\Controllers\admin\GradeWeights::class, 'index'], ['as' => 'exams.weights']);
        $routes->get('create', [\App\Controllers\admin\GradeWeights::class, 'create'], ['as' => 'exams.weights.create']);
        $routes->get('edit/(:num)', [\App\Controllers\admin\GradeWeights::class, 'edit/$1'], ['as' => 'exams.weights.edit']);
        $routes->post('save', [\App\Controllers\admin\GradeWeights::class, 'save'], ['as' => 'exams.weights.save']);
        $routes->get('delete/(:num)', [\App\Controllers\admin\GradeWeights::class, 'delete/$1'], ['as' => 'exams.weights.delete']);
        $routes->get('by-level/(:num)', [\App\Controllers\admin\GradeWeights::class, 'getByLevel/$1'], ['as' => 'exams.weights.by-level']);
        $routes->post('copy', [\App\Controllers\admin\GradeWeights::class, 'copyFromPrevious'], ['as' => 'exams.weights.copy']);
    });

    // === CÁLCULO DE MÉDIAS ===
    $routes->group('calculate', function ($routes) {
        $routes->get('discipline/(:num)/(:num)/(:num)', [\App\Controllers\admin\GradeCalculator::class, 'discipline/$1/$2/$3'], ['as' => 'exams.calculate.discipline']);
        $routes->get('semester/(:num)/(:num)', [\App\Controllers\admin\GradeCalculator::class, 'semester/$1/$2'], ['as' => 'exams.calculate.semester']);
        $routes->get('class/(:num)/(:num)', [\App\Controllers\admin\GradeCalculator::class, 'class/$1/$2'], ['as' => 'exams.calculate.class']);
        $routes->post('run', [\App\Controllers\admin\GradeCalculator::class, 'runCalculation'], ['as' => 'exams.calculate.run']);
    });

    

    // === EXAMES ANTIGOS (para compatibilidade temporária) ===
    /* $routes->get('', [Exams::class, 'index'], ["as" => 'exams']);
    $routes->get('form-add', [Exams::class, 'form'], ["as" => 'exams.form']);
    $routes->get('form-edit/(:num)', [Exams::class, 'form'], ["as" => 'exams.form/$1']);
    $routes->post('save', [Exams::class, 'save'], ["as" => 'exams.save']);
    $routes->get('delete/(:num)', [Exams::class, 'delete'], ["as" => 'exams.delete/$1']);
    $routes->get('schedule', [Exams::class, 'schedule'], ["as" => 'exams.schedule']);
    $routes->get('view/(:num)', [Exams::class, 'view'], ["as" => 'exams.view/$1']);
    $routes->post('save-results', [Exams::class, 'saveResults'], ["as" => 'exams.save-results']);
    $routes->get('publish/(:num)', [Exams::class, 'publish'], ["as" => 'exams.publish/$1']); */
  });

  // === MÉDIAS DISCIPLINARES ===
  $routes->group('discipline-averages', function ($routes) {
      // ROTA RAIZ - Listagem ou dashboard de médias
      $routes->get('/', [\App\Controllers\admin\DisciplineAverages::class, 'index'], ['as' => 'discipline-averages.index']);
      
      $routes->get('student/(:num)/(:num)', [\App\Controllers\admin\DisciplineAverages::class, 'student/$1/$2'], ['as' => 'discipline-averages.student']);
      $routes->get('class/(:num)/(:num)', [\App\Controllers\admin\DisciplineAverages::class, 'class/$1/$2'], ['as' => 'discipline-averages.class']);
      $routes->get('export/(:num)/(:num)', [\App\Controllers\admin\DisciplineAverages::class, 'export/$1/$2'], ['as' => 'discipline-averages.export']);
  });

    // === RESULTADOS SEMESTRAIS ===
  $routes->group('semester-results', function ($routes) {
      // ROTA RAIZ - Dashboard de resultados semestrais
      $routes->get('/', [\App\Controllers\admin\SemesterResults::class, 'index'], ['as' => 'semester-results.index']);
      
      $routes->get('student/(:num)/(:num)', [\App\Controllers\admin\SemesterResults::class, 'student/$1/$2'], ['as' => 'semester-results.student']);
      $routes->get('class/(:num)/(:num)', [\App\Controllers\admin\SemesterResults::class, 'class/$1/$2'], ['as' => 'semester-results.class']);
      $routes->get('summary/(:num)/(:num)', [\App\Controllers\admin\SemesterResults::class, 'summary/$1/$2'], ['as' => 'semester-results.summary']);
      $routes->get('export/(:num)/(:num)', [\App\Controllers\admin\SemesterResults::class, 'export/$1/$2'], ['as' => 'semester-results.export']);
  });

    // === EXAMES DE RECURSO ===
    $routes->group('appeals', function ($routes) {
        $routes->get('/', [\App\Controllers\admin\AppealExams::class, 'index'], ['as' => 'exams.appeals']);
        $routes->post('generate', [\App\Controllers\admin\AppealExams::class, 'generateAppealExams'], ['as' => 'exams.appeals.generate']);
        $routes->get('scheduled/(:num)', [\App\Controllers\admin\AppealExams::class, 'scheduled/$1'], ['as' => 'exams.appeals.scheduled']);
        $routes->get('exam-students/(:num)', [\App\Controllers\admin\AppealExams::class, 'examStudents/$1'], ['as' => 'exams.appeals.exam-students']);
        $routes->post('register-attendance/(:num)', [\App\Controllers\admin\AppealExams::class, 'registerAttendance/$1'], ['as' => 'exams.appeals.register-attendance']);
        $routes->post('register-results/(:num)', [\App\Controllers\admin\AppealExams::class, 'registerResults/$1'], ['as' => 'exams.appeals.register-results']);
        $routes->get('summary/(:num)', [\App\Controllers\admin\AppealExams::class, 'summary/$1'], ['as' => 'exams.appeals.summary']);
        $routes->get('export/(:num)', [\App\Controllers\admin\AppealExams::class, 'export/$1'], ['as' => 'exams.appeals.export']);
        $routes->get('get-stats/(:num)', [\App\Controllers\admin\AppealExams::class, 'getStats/$1'], ['as' => 'exams.appeals.stats']);
    });
  // ======================================================================
  // FIM DAS NOVAS ROTAS
  // ======================================================================

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
    // Adicione no grupo 'students' das rotas
    $routes->get('search', [Clients::class, 'search'], ["as" => 'students.search']);

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

    // NOVA ROTA PARA ESTATÍSTICAS AJAX
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

 
    // Adicionar no grupo 'admin'
  $routes->group('grades', function($routes) {
      $routes->get('/', [Grades::class, 'index'], ['as' => 'admin.grades']);
      $routes->get('class/(:num)', [Grades::class, 'class/$1'], ['as' => 'admin.grades.class']);
      $routes->get('student/(:num)', [Grades::class, 'student/$1'], ['as' => 'admin.grades.student']);
      $routes->get('report', [Grades::class, 'report'], ['as' => 'admin.grades.report']);
      // Adicione no grupo 'grades' das rotas
      $routes->get('statistics', [Grades::class, 'statistics'], ["as" => 'admin.grades.statistics']);
      $routes->get('period', [Grades::class, 'period'], ['as' => 'admin.grades.period']);
  });

    // Pautas/Histórico Acadêmico
  $routes->group('academic-records', ['filter' => 'auth:admin,teachers'], function($routes) {
      $routes->get('/', [AcademicRecords::class, 'index'], ['as' => 'academic.records']);
      $routes->get('class/(:num)', [AcademicRecords::class, 'class/$1'], ['as' => 'academic.records.class']);
      $routes->get('student/(:num)', [AcademicRecords::class, 'student/$1'], ['as' => 'academic.records.student']);
      $routes->get('semester/(:num)', [AcademicRecords::class, 'semester/$1'], ['as' => 'academic.records.semester']);
      $routes->get('export', [AcademicRecords::class, 'export'], ['as' => 'academic.records.export']);
      $routes->post('finalize-year', [AcademicRecords::class, 'finalizeYear'], ['as' => 'academic.records.finalize']);
      $routes->get('transcript/(:num)', [AcademicRecords::class, 'transcript/$1'], ['as' => 'academic.records.transcript']);
      $routes->get('certificate/(:num)', [AcademicRecords::class, 'certificate/$1'], ['as' => 'academic.records.certificate']);
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
           // Formulário de edição (GET)
        // Formulário para criar/editar perfil (GET)
     // OU duas rotas separadas:
        $routes->get('form', [Roles::class, 'form'], ["as" => 'roles.form']);      // criação
        $routes->get('form/(:num)', [Roles::class, 'form'], ["as" => 'roles.form.edit']); // edição
        
        // Salvar perfil (criar ou atualizar) (POST)
        // SALVAR - separados
        $routes->post('save', [Roles::class, 'save'], ["as" => 'roles.save']);           // criação
        $routes->post('save/(:num)', [Roles::class, 'save'], ["as" => 'roles.save.update']); // atualização
        
        // Eliminar perfil (GET ou POST - recomendo POST para segurança)
        $routes->post('delete/(:num)', [Roles::class, 'delete'], ["as" => 'roles.delete']);
        $routes->get('permissions', [Roles::class, 'grouppermissions'], ["as" => 'roles.permissions']);
        // Página de permissões de um perfil específico (com ID)
        $routes->get('permission/(:num)', [Roles::class, 'role'], ["as" => 'roles.permission']);
         // ✅ NOVA ROTA: Buscar permissões de um perfil via AJAX
        $routes->get('get-role-permissions', [Roles::class, 'getRolePermissions'], ["as" => 'roles.get.permissions']);
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
        // ✅ NOVA ROTA: Pré-visualizar modelo por código
        $routes->get('preview-template/(:any)', [DocumentGenerator::class, 'previewTemplate/$1'], ['as' => 'admin.document-generator.preview-template']);
    });

    // Rotas de notificações para admin
    $routes->group('notifications', function ($routes) {
        $routes->get('', [\App\Controllers\admin\Notifications::class, 'index'], ['as' => 'admin.notifications']);
        $routes->get('read/(:num)', [\App\Controllers\admin\Notifications::class, 'read/$1'], ['as' => 'admin.notifications.read']);
        $routes->get('mark-all-read', [\App\Controllers\admin\Notifications::class, 'markAllRead'], ['as' => 'admin.notifications.markAllRead']);
        $routes->get('delete/(:num)', [\App\Controllers\admin\Notifications::class, 'delete/$1'], ['as' => 'admin.notifications.delete']);
        $routes->get('unread-count', [\App\Controllers\admin\Notifications::class, 'getUnreadCount'], ['as' => 'admin.notifications.unreadCount']);
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
    
    // Rotas protegidas 
    $routes->group('', ['filter' => 'auth:teachers'], function ($routes) {
        
        // Dashboard
        $routes->get('dashboard', [\App\Controllers\teachers\Dashboard::class, 'index'], ["as" => 'teachers.dashboard']);
        
        // Profile
        $routes->get('profile', [\App\Controllers\teachers\Profile::class, 'index'], ['as' => 'teachers.profile']);
        $routes->post('profile/update', [\App\Controllers\teachers\Profile::class, 'update'], ['as' => 'teachers.profile.update']);
        $routes->post('profile/update-photo', [\App\Controllers\teachers\Profile::class, 'updatePhoto'], ['as' => 'teachers.profile.update-photo']);

        // Minhas Turmas
        $routes->group('classes', function ($routes) {
            $routes->get('', [\App\Controllers\teachers\Classes::class, 'index'], ["as" => 'teachers.classes']);
            $routes->get('students/(:num)', [\App\Controllers\teachers\Classes::class, 'students/$1'], ["as" => 'teachers.classes.students/$1']);
            // view removido porque students já faz a função
        });
        
        // === EXAMES (Nova estrutura) ===
        $routes->group('exams', function ($routes) {
            // Visão geral dos exames do professor
            $routes->get('', [\App\Controllers\teachers\Exams::class, 'index'], ['as' => 'teachers.exams']);
            
            // Formulários
            $routes->get('form-add', [\App\Controllers\teachers\Exams::class, 'form'], ['as' => 'teachers.exams.form']);
            $routes->get('form-edit/(:num)', [\App\Controllers\teachers\Exams::class, 'form/$1'], ['as' => 'teachers.exams.form.edit']);
            $routes->post('save', [\App\Controllers\teachers\Exams::class, 'save'], ['as' => 'teachers.exams.save']);
            
            // Exames agendados
            $routes->get('upcoming', [\App\Controllers\teachers\Exams::class, 'upcoming'], ['as' => 'teachers.exams.upcoming']);
            $routes->get('completed', [\App\Controllers\teachers\Exams::class, 'completed'], ['as' => 'teachers.exams.completed']);
            
            // Gestão de presenças
            $routes->get('attendance/(:num)', [\App\Controllers\teachers\Exams::class, 'attendance/$1'], ['as' => 'teachers.exams.attendance']);
            $routes->post('attendance/(:num)', [\App\Controllers\teachers\Exams::class, 'saveAttendance/$1'], ['as' => 'teachers.exams.attendance.save']);
            
            // Gestão de notas
            $routes->get('grade/(:num)', [\App\Controllers\teachers\Exams::class, 'grade/$1'], ['as' => 'teachers.exams.grade']);
            $routes->post('save-grades', [\App\Controllers\teachers\Exams::class, 'saveGrades'], ['as' => 'teachers.exams.saveGrades']);
            
            // Resultados
            $routes->get('results/(:num)', [\App\Controllers\teachers\Exams::class, 'results/$1'], ['as' => 'teachers.exams.results']);
            
            // AJAX
            $routes->get('get-disciplines/(:num)', [\App\Controllers\teachers\Exams::class, 'getDisciplines/$1'], ['as' => 'teachers.exams.getDisciplines']);
        });
        
        // === AVALIAÇÕES CONTÍNUAS (Grades) ===
        $routes->group('grades', function ($routes) {
            $routes->get('', [\App\Controllers\teachers\Grades::class, 'index'], ["as" => 'teachers.grades']);
            $routes->post('save', [\App\Controllers\teachers\Grades::class, 'save'], ["as" => 'teachers.grades.save']);
            $routes->get('get-disciplines/(:num)', [\App\Controllers\teachers\Grades::class, 'getDisciplines/$1'], ["as" => 'teachers.grades.getDisciplines']);
            $routes->get('report/select', [\App\Controllers\teachers\Grades::class, 'selectReport'], ["as" => 'teachers.grades.report.select']);
            $routes->get('report/(:num)', [\App\Controllers\teachers\Grades::class, 'report/$1'], ["as" => 'teachers.grades.report']);
        });
        
        // === PRESENÇAS ===
        $routes->group('attendance', function ($routes) {
            $routes->get('', [\App\Controllers\teachers\Attendance::class, 'index'], ["as" => 'teachers.attendance']);
            $routes->post('save', [\App\Controllers\teachers\Attendance::class, 'save'], ["as" => 'teachers.attendance.save']);
            $routes->get('report', [\App\Controllers\teachers\Attendance::class, 'report'], ["as" => 'teachers.attendance.report']);
            $routes->get('get-disciplines/(:num)', [\App\Controllers\teachers\Attendance::class, 'getDisciplines/$1'], ["as" => 'teachers.attendance.getDisciplines']);
        });

        // === DOCUMENTOS ===
        $routes->group('documents', function ($routes) {
            $routes->get('/', [\App\Controllers\teachers\Documents::class, 'index'], ['as' => 'teachers.documents']);
            $routes->get('requests', [\App\Controllers\teachers\Documents::class, 'requests'], ['as' => 'teachers.documents.requests']);
            $routes->get('archive', [\App\Controllers\teachers\Documents::class, 'archive'], ['as' => 'teachers.documents.archive']);
            $routes->post('upload', [\App\Controllers\teachers\Documents::class, 'upload'], ['as' => 'teachers.documents.upload']);
            $routes->get('download/(:num)', [\App\Controllers\teachers\Documents::class, 'download/$1'], ['as' => 'teachers.documents.download']);
            $routes->get('view/(:num)', [\App\Controllers\teachers\Documents::class, 'view/$1'], ['as' => 'teachers.documents.view']);
            $routes->get('delete/(:num)', [\App\Controllers\teachers\Documents::class, 'delete/$1'], ['as' => 'teachers.documents.delete']);
            $routes->post('request', [\App\Controllers\teachers\Documents::class, 'createRequest'], ['as' => 'teachers.documents.request']);
            $routes->get('request/(:num)', [\App\Controllers\teachers\Documents::class, 'viewRequest/$1'], ['as' => 'teachers.documents.viewRequest']);
            $routes->get('request/cancel/(:num)', [\App\Controllers\teachers\Documents::class, 'cancelRequest/$1'], ['as' => 'teachers.documents.cancelRequest']);
        });

        // Rotas de notificações para teacher
        $routes->group('notifications', function ($routes) {
            $routes->get('', [\App\Controllers\teachers\Notifications::class, 'index'], ['as' => 'teachers.notifications']);
            $routes->get('read/(:num)', [\App\Controllers\teachers\Notifications::class, 'read/$1'], ['as' => 'teachers.notifications.read']);
            $routes->get('mark-all-read', [\App\Controllers\teachers\Notifications::class, 'markAllRead'], ['as' => 'teachers.notifications.markAllRead']);
            $routes->get('delete/(:num)', [\App\Controllers\teachers\Notifications::class, 'delete/$1'], ['as' => 'teachers.notifications.delete']);
            $routes->get('unread-count', [\App\Controllers\teachers\Notifications::class, 'getUnreadCount'], ['as' => 'teachers.notifications.unreadCount']);
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

               
        // === NOVAS ROTAS PARA ALUNOS - EXAMES ===
        $routes->group('exams', function ($routes) {
            // Calendário de exames
            $routes->get('', [\App\Controllers\students\Exams::class, 'index'], ['as' => 'students.exams']);
            $routes->get('schedule', [\App\Controllers\students\Exams::class, 'schedule'], ['as' => 'students.exams.schedule']);
            $routes->get('upcoming', [\App\Controllers\students\Exams::class, 'upcoming'], ['as' => 'students.exams.upcoming']);
            
            // Resultados
            $routes->get('results', [\App\Controllers\students\Exams::class, 'results'], ['as' => 'students.exams.results']);
            $routes->get('results/semester/(:num)', [\App\Controllers\students\Exams::class, 'semesterResults/$1'], ['as' => 'students.exams.semester-results']);
            $routes->get('results/discipline/(:num)', [\App\Controllers\students\Exams::class, 'disciplineResults/$1'], ['as' => 'students.exams.discipline-results']);
            
            // Detalhes de exame específico
            $routes->get('view/(:num)', [\App\Controllers\students\Exams::class, 'view/$1'], ['as' => 'students.exams.view']);
        });
          
        // Minhas Notas
        $routes->group('grades', function ($routes) {
          $routes->get('', [StudentGrades::class, 'index'], ["as" => 'students.grades']);
          $routes->get('subject/(:num)', [StudentGrades::class, 'bySubject'], ["as" => 'students.grades.subject/$1']);
          $routes->get('report-card', [StudentGrades::class, 'reportCard'], ["as" => 'students.grades.report']);
        });


        // Minhas Presenças
       $routes->group('attendance', function ($routes) {
            $routes->get('', [\App\Controllers\students\Attendance::class, 'index'], ["as" => 'students.attendance.my']);
            $routes->get('history', [\App\Controllers\students\Attendance::class, 'history'], ["as" => 'students.attendance.history']);
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

        // Rotas de notificações para student
        $routes->group('notifications', function ($routes) {
            $routes->get('', [\App\Controllers\student\Notifications::class, 'index'], ['as' => 'student.notifications']);
            $routes->get('read/(:num)', [\App\Controllers\student\Notifications::class, 'read/$1'], ['as' => 'student.notifications.read']);
            $routes->get('mark-all-read', [\App\Controllers\student\Notifications::class, 'markAllRead'], ['as' => 'student.notifications.markAllRead']);
            $routes->get('delete/(:num)', [\App\Controllers\student\Notifications::class, 'delete/$1'], ['as' => 'student.notifications.delete']);
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