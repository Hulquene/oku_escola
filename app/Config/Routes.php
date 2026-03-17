<?php

use App\Controllers\admin\AcademicDocuments;
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
use App\Controllers\admin\AppealExams;
use App\Controllers\admin\BulkClassCreate;
use App\Controllers\admin\EmailController;
use App\Controllers\admin\ExamPeriods;
use App\Controllers\admin\ExamSchedules;
use App\Controllers\admin\GradeCurriculum;
use App\Controllers\admin\GradeWeights;
use App\Controllers\admin\MiniGradeSheet;
use App\Controllers\admin\Notifications;
use App\Controllers\admin\Schedule;
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
    $routes->get('', [Dashboard::class, 'index'], ['as' => 'admin.dashboard']);
    $routes->get('dashboard', [Dashboard::class, 'index'], ['as' => 'dashboard']);

    /**
     * =====================================================
     * GESTÃO ACADÉMICA
     * =====================================================
     */
    $routes->group('academic', function ($routes) {
        
        // === ANOS LETIVOS ===
        $routes->group('years', function ($routes) {
            $routes->get('', [AcademicYears::class, 'index'], ['as' => 'academic.years']);
            $routes->post('get-table-data', [AcademicYears::class, 'getTableData'], ['as' => 'academic.years.get-table-data']);
            $routes->get('get-stats', [AcademicYears::class, 'getStats'], ['as' => 'academic.years.get-stats']);
            $routes->get('form-add', [AcademicYears::class, 'form'], ['as' => 'academic.years.form-add']);
            $routes->get('form-edit/(:num)', [AcademicYears::class, 'form/$1'], ['as' => 'academic.years.form-edit']);
            $routes->post('save', [AcademicYears::class, 'save'], ['as' => 'academic.years.save']);
            $routes->get('view/(:num)', [AcademicYears::class, 'view/$1'], ['as' => 'academic.years.view']);
            $routes->get('set-current/(:num)', [AcademicYears::class, 'setCurrent/$1'], ['as' => 'academic.years.set-current']);
            $routes->post('toggle-active/(:num)', [AcademicYears::class, 'toggleActive/$1'], ['as' => 'academic.years.toggle-active']);
            $routes->get('delete/(:num)', [AcademicYears::class, 'delete/$1'], ['as' => 'academic.years.delete']);
        });

        // === SEMESTRES/TRIMESTRES ===
        $routes->group('semesters', function ($routes) {
            $routes->get('', [Semesters::class, 'index'], ['as' => 'academic.semesters']);
            $routes->post('get-table-data', [Semesters::class, 'getTableData'], ['as' => 'academic.semesters.get-table-data']);
            $routes->get('get-stats', [Semesters::class, 'getStats'], ['as' => 'academic.semesters.get-stats']);
            $routes->get('form-add', [Semesters::class, 'form'], ['as' => 'academic.semesters.form-add']);
            $routes->get('form-edit/(:num)', [Semesters::class, 'form/$1'], ['as' => 'academic.semesters.form-edit']);
            $routes->post('save', [Semesters::class, 'save'], ['as' => 'academic.semesters.save']);
            $routes->get('view/(:num)', [Semesters::class, 'view/$1'], ['as' => 'academic.semesters.view']);
            $routes->get('set-current/(:num)', [Semesters::class, 'setCurrent/$1'], ['as' => 'academic.semesters.set-current']);
            $routes->get('delete/(:num)', [Semesters::class, 'delete/$1'], ['as' => 'academic.semesters.delete']);
            $routes->get('get-dates/(:num)', [Semesters::class, 'getDates/$1'], ['as' => 'academic.semesters.get-dates']);
            $routes->get('get-by-year/(:num)', [Semesters::class, 'getByYear/$1'], ['as' => 'academic.semesters.get-by-year']);
            $routes->post('process', [Semesters::class, 'process'], ['as' => 'academic.semesters.process']);
            $routes->get('conclude/(:num)', [Semesters::class, 'conclude/$1'], ['as' => 'academic.semesters.conclude']);
        });

        // === CURSOS ===
        $routes->group('courses', function ($routes) {
            $routes->get('', [Courses::class, 'index'], ['as' => 'admin.courses']);
            $routes->post('get-table-data', [Courses::class, 'getTableData'], ['as' => 'admin.courses.get-table-data']);
            $routes->get('get-stats', [Courses::class, 'getStats'], ['as' => 'admin.courses.get-stats']);
            $routes->get('form-add', [Courses::class, 'form'], ['as' => 'admin.courses.form-add']);
            $routes->get('form-edit/(:num)', [Courses::class, 'form/$1'], ['as' => 'admin.courses.form-edit']);
            $routes->post('save', [Courses::class, 'save'], ['as' => 'admin.courses.save']);
            $routes->get('view/(:num)', [Courses::class, 'view/$1'], ['as' => 'admin.courses.view']);
            $routes->get('delete/(:num)', [Courses::class, 'delete/$1'], ['as' => 'admin.courses.delete']);
            
            // Currículo do curso
            $routes->group('curriculum', function ($routes) {
                $routes->get('(:num)', [CourseCurriculum::class, 'index/$1'], ['as' => 'admin.courses.curriculum']);
                $routes->post('add-discipline', [CourseCurriculum::class, 'addDiscipline'], ['as' => 'admin.courses.curriculum.add']);
                $routes->get('edit/(:num)', [CourseCurriculum::class, 'editDiscipline/$1'], ['as' => 'admin.courses.curriculum.edit']);
                $routes->post('update/(:num)', [CourseCurriculum::class, 'updateDiscipline/$1'], ['as' => 'admin.courses.curriculum.update']);
                $routes->get('remove/(:num)', [CourseCurriculum::class, 'removeDiscipline/$1'], ['as' => 'admin.courses.curriculum.remove']);
                $routes->get('get-available/(:num)/(:num)', [CourseCurriculum::class, 'getAvailableDisciplines/$1/$2'], ['as' => 'admin.courses.curriculum.get-available']);
            });
        });

        // === CALENDÁRIO ESCOLAR ===
        $routes->group('calendar', function ($routes) {
            $routes->get('', [SchoolCalendar::class, 'index'], ['as' => 'academic.calendar']);
            $routes->post('save-event', [SchoolCalendar::class, 'saveEvent'], ['as' => 'academic.calendar.save']);
            $routes->get('get-events', [SchoolCalendar::class, 'getEvents'], ['as' => 'academic.calendar.events']);
        });

        // === DOCUMENTOS ACADÉMICOS ===
        $routes->group('documents', function($routes) {
            $routes->get('', [AcademicDocuments::class, 'index'], ['as' => 'academic.documents.index']);
            $routes->post('get-table-data', [AcademicDocuments::class, 'getTableData'], ['as' => 'academic.documents.get-table-data']);
            $routes->get('eligible-certificates', [AcademicDocuments::class, 'eligibleForCertificate'], ['as' => 'academic.documents.certificates']);
            $routes->get('eligible-declarations', [AcademicDocuments::class, 'eligibleForDeclaration'], ['as' => 'academic.documents.declarations']);
            $routes->get('generate-certificate/(:num)', [AcademicDocuments::class, 'generateCertificate/$1'], ['as' => 'academic.documents.generate-certificate']);
            $routes->get('generate-declaration/(:num)', [AcademicDocuments::class, 'generateDeclaration/$1'], ['as' => 'academic.documents.generate-declaration']);
            $routes->get('history', [AcademicDocuments::class, 'history'], ['as' => 'academic.documents.history']);
        });

        // === GRADE CURRICULUM ===
        $routes->group('grade-curriculum', function ($routes) {
            $routes->get('(:num)', [GradeCurriculum::class, 'index/$1'], ['as' => 'admin.grade.curriculum']);
            $routes->post('add-discipline', [GradeCurriculum::class, 'addDiscipline'], ['as' => 'admin.grade.curriculum.add']);
            $routes->get('edit/(:num)', [GradeCurriculum::class, 'editDiscipline/$1'], ['as' => 'admin.grade.curriculum.edit']);
            $routes->post('update/(:num)', [GradeCurriculum::class, 'updateDiscipline/$1'], ['as' => 'admin.grade.curriculum.update']);
            $routes->get('remove/(:num)', [GradeCurriculum::class, 'removeDiscipline/$1'], ['as' => 'admin.grade.curriculum.remove']);
            $routes->get('get-available/(:num)', [GradeCurriculum::class, 'getAvailableDisciplines/$1'], ['as' => 'admin.grade.curriculum.get-available']);
        });
    });

    /**
     * =====================================================
     * GESTÃO DE CLASSES/TURMAS
     * =====================================================
     */
    $routes->group('classes', function ($routes) {
        
        // === NÍVEIS DE ENSINO ===
        $routes->group('levels', function ($routes) {
            $routes->get('', [GradeLevels::class, 'index'], ['as' => 'classes.levels']);
            $routes->post('get-table-data', [GradeLevels::class, 'getTableData'], ['as' => 'classes.levels.get-table-data']);
            $routes->post('save', [GradeLevels::class, 'save'], ['as' => 'classes.levels.save']);
            $routes->get('delete/(:num)', [GradeLevels::class, 'delete/$1'], ['as' => 'classes.levels.delete']);
        });

        // === TURMAS ===
        $routes->group('classes', function ($routes) {
            $routes->get('', [Classes::class, 'index'], ['as' => 'classes']);
            $routes->post('get-table-data', [Classes::class, 'getTableData'], ['as' => 'classes.get-table-data']);
            $routes->get('get-stats', [Classes::class, 'getStats'], ['as' => 'classes.get-stats']);
            $routes->get('form-add', [Classes::class, 'form'], ['as' => 'classes.form-add']);
            $routes->get('form-edit/(:num)', [Classes::class, 'form/$1'], ['as' => 'classes.form-edit']);
            $routes->post('save', [Classes::class, 'save'], ['as' => 'classes.save']);
            $routes->get('delete/(:num)', [Classes::class, 'delete/$1'], ['as' => 'classes.delete']);
            $routes->get('activate/(:num)', [Classes::class, 'activate/$1'], ['as' => 'classes.activate']);
            $routes->get('view/(:num)', [Classes::class, 'view/$1'], ['as' => 'classes.view']);
            $routes->get('list-students/(:num)', [Classes::class, 'listStudents/$1'], ['as' => 'classes.students']);
            $routes->get('get-class-disciplines/(:num)', [Classes::class, 'getClassDisciplines/$1'], ['as' => 'classes.get-disciplines']);
            $routes->get('export-students/(:num)', [Classes::class, 'exportStudents/$1'], ['as' => 'classes.export-students']);
            $routes->get('print-sheet/(:num)', [Classes::class, 'printSheet/$1'], ['as' => 'classes.print-sheet']);
        });

        // === CRIAÇÃO EM LOTE ===
        $routes->get('bulk-create', [BulkClassCreate::class, 'index'], ['as' => 'classes.bulk-create']);
        $routes->post('bulk-create/process', [BulkClassCreate::class, 'process'], ['as' => 'classes.bulk-create.process']);

        $routes->get('get-by-level-and-year/(:num)/(:num)', [Classes::class, 'getByLevelAndYear/$1/$2'], ['as' => 'classes.get-by-level-and-year']);
        $routes->get('get-by-year/(:num)', [Classes::class, 'getByYear/$1'], ['as' => 'classes.get-by-year']);

        // === HORÁRIOS (SCHEDULE) ===
        $routes->group('schedule', function($routes) {
            $routes->get('', [Schedule::class, 'index'], ['as' => 'classes.schedule']);
            $routes->post('get-data', [Schedule::class, 'getData'], ['as' => 'classes.schedule.get-data']);
            $routes->post('get-schedule-data', [Schedule::class, 'getScheduleData'], ['as' => 'classes.schedule.get-schedule-data']);
            $routes->get('view/(:num)', [Schedule::class, 'view/$1'], ['as' => 'classes.schedule.view']);
            $routes->post('save', [Schedule::class, 'save'], ['as' => 'classes.schedule.save']);
            $routes->get('delete/(:alphanum)', [Schedule::class, 'delete/$1'], ['as' => 'classes.schedule.delete']);
            $routes->post('duplicate', [Schedule::class, 'duplicate'], ['as' => 'classes.schedule.duplicate']);
            $routes->post('check-availability', [Schedule::class, 'checkAvailability'], ['as' => 'classes.schedule.check-availability']);
            $routes->post('get-schedule-item', [Schedule::class, 'getScheduleItem'], ['as' => 'classes.schedule.get-item']);
            $routes->get('export-pdf/(:num)', [Schedule::class, 'exportPdf/$1'], ['as' => 'classes.schedule.export-pdf']);
            $routes->get('export-excel/(:num)', [Schedule::class, 'exportExcel/$1'], ['as' => 'classes.schedule.export-excel']);
        });

        // === DISCIPLINAS ===
        $routes->group('subjects', function ($routes) {
            $routes->get('', [Disciplines::class, 'index'], ['as' => 'classes.subjects']);
            $routes->post('get-table-data', [Disciplines::class, 'getTableData'], ['as' => 'classes.subjects.get-table-data']);
            $routes->post('save', [Disciplines::class, 'save'], ['as' => 'classes.subjects.save']);
            $routes->get('delete/(:num)', [Disciplines::class, 'delete/$1'], ['as' => 'classes.subjects.delete']);
            $routes->get('search', [Disciplines::class, 'search'], ['as' => 'classes.subjects.search']);
            $routes->get('search/(:any)', [Disciplines::class, 'search/$1'], ['as' => 'classes.subjects.search-term']);
        });

        // === ALOCAÇÃO DE DISCIPLINAS ===
        $routes->group('class-subjects', function ($routes) {
            $routes->get('', [ClassSubjects::class, 'index'], ['as' => 'classes.class-subjects']);
            $routes->post('get-table-data', [ClassSubjects::class, 'getTableData'], ['as' => 'classes.class-subjects.get-table-data']);
            $routes->get('assign', [ClassSubjects::class, 'assign'], ['as' => 'classes.class-subjects.assign']);
            $routes->post('assign', [ClassSubjects::class, 'assignSave'], ['as' => 'classes.class-subjects.assign-save']);
            $routes->get('get-by-class/(:num)', [ClassSubjects::class, 'getByClass/$1'], ['as' => 'classes.class-subjects.get-by-class']);
            $routes->get('delete/(:num)', [ClassSubjects::class, 'delete/$1'], ['as' => 'classes.class-subjects.delete']);
            $routes->get('get-levels-by-course/(:num)', [ClassSubjects::class, 'getLevelsByCourse/$1'], ['as' => 'classes.class-subjects.get-levels']);
            $routes->get('get-classes-by-course-level/(:num)/(:num)', [ClassSubjects::class, 'getClassesByCourseAndLevel/$1/$2'], ['as' => 'classes.class-subjects.get-classes']);
            $routes->get('get-available-disciplines/(:num)', [ClassSubjects::class, 'getAvailableDisciplinesForClass/$1'], ['as' => 'classes.class-subjects.available-disciplines']);
            $routes->get('assign-teachers/(:num)', [ClassSubjects::class, 'assignTeachers/$1'], ['as' => 'classes.class-subjects.assign-teachers']);
            $routes->post('save-teachers', [ClassSubjects::class, 'saveTeachers'], ['as' => 'classes.class-subjects.save-teachers']);
            $routes->post('save-bulk', [ClassSubjects::class, 'saveBulkAssignments'], ['as' => 'classes.class-subjects.save-bulk']);
            $routes->get('get-class-disciplines/(:num)/(:num)', [ClassSubjects::class, 'getClassDisciplinesWithAssignments/$1/$2'], ['as' => 'classes.class-subjects.get-detailed']);
        });
    });

    /**
     * =====================================================
     * GESTÃO DE EXAMES
     * =====================================================
     */
    $routes->group('exams', function ($routes) {
        
        // === CONSELHOS DE EXAME ===
        $routes->group('boards', function ($routes) {
            $routes->get('', [ExamBoards::class, 'index'], ['as' => 'exams.boards']);
            $routes->post('get-table-data', [ExamBoards::class, 'getTableData'], ['as' => 'exams.boards.get-table-data']);
            $routes->post('save', [ExamBoards::class, 'save'], ['as' => 'exams.boards.save']);
            $routes->get('delete/(:num)', [ExamBoards::class, 'delete/$1'], ['as' => 'exams.boards.delete']);
        });

        // === RESULTADOS E PAUTAS ===
        $routes->group('results', function ($routes) {
            $routes->get('', [ExamResults::class, 'index'], ["as" => 'exams.results']);
            $routes->post('save', [ExamResults::class, 'save'], ["as" => 'exams.results.save']);
            $routes->get('report-card/(:num)', [ExamResults::class, 'reportCard'], ["as" => 'exams.results.report/$1']);
            $routes->get('transcript/(:num)', [ExamResults::class, 'transcript'], ["as" => 'exams.results.transcript/$1']);
            $routes->get('class/(:num)', [ExamResults::class, 'classResults/$1'], ["as" => 'exams.results.class']);
        });

        // === PERÍODOS DE EXAME ===
        $routes->group('periods', function ($routes) {
            $routes->get('', [ExamPeriods::class, 'index'], ['as' => 'exams.periods']);
            $routes->post('get-table-data', [ExamPeriods::class, 'getTableData'], ['as' => 'exams.periods.get-table-data']);
            $routes->get('get-stats', [ExamPeriods::class, 'getStats'], ['as' => 'exams.periods.get-stats']);
            $routes->get('create', [ExamPeriods::class, 'create'], ['as' => 'exams.periods.create']);
            $routes->get('edit/(:num)', [ExamPeriods::class, 'edit/$1'], ['as' => 'exams.periods.edit']);
            $routes->get('view/(:num)', [ExamPeriods::class, 'view/$1'], ['as' => 'exams.periods.view']);
            $routes->post('save', [ExamPeriods::class, 'save'], ['as' => 'exams.periods.save']);
            $routes->get('delete/(:num)', [ExamPeriods::class, 'delete/$1'], ['as' => 'exams.periods.delete']);
            $routes->get('generate-schedule/(:num)', [ExamPeriods::class, 'generateSchedule/$1'], ['as' => 'exams.periods.generate-schedule']);
            $routes->post('save-schedule/(:num)', [ExamPeriods::class, 'saveSchedule/$1'], ['as' => 'exams.periods.save-schedule']);
            $routes->get('start/(:num)', [ExamPeriods::class, 'start/$1'], ['as' => 'exams.periods.start']);
            $routes->get('pause/(:num)', [ExamPeriods::class, 'pause/$1'], ['as' => 'exams.periods.pause']);
            $routes->get('complete/(:num)', [ExamPeriods::class, 'complete/$1'], ['as' => 'exams.periods.complete']);
            $routes->get('cancel/(:num)', [ExamPeriods::class, 'cancel/$1'], ['as' => 'exams.periods.cancel']);
        });

        // === AGENDAMENTO DE EXAMES ===
        $routes->group('schedules', function ($routes) {
            $routes->get('', [ExamSchedules::class, 'index'], ['as' => 'exams.schedules']);
            $routes->post('get-table-data', [ExamSchedules::class, 'getTableData'], ['as' => 'exams.schedules.get-table-data']);
            $routes->get('create', [ExamSchedules::class, 'create'], ['as' => 'exams.schedules.create']);
            $routes->get('edit/(:num)', [ExamSchedules::class, 'edit/$1'], ['as' => 'exams.schedules.edit']);
            $routes->get('view/(:num)', [ExamSchedules::class, 'view/$1'], ['as' => 'exams.schedules.view']);
            $routes->post('save', [ExamSchedules::class, 'save'], ['as' => 'exams.schedules.save']);
            $routes->post('update/(:num)', [ExamSchedules::class, 'update/$1'], ['as' => 'exams.schedules.update']);
            $routes->post('update-status/(:num)', [ExamSchedules::class, 'updateStatus/$1'], ['as' => 'exams.schedules.update-status']);
            $routes->get('delete/(:num)', [ExamSchedules::class, 'delete/$1'], ['as' => 'exams.schedules.delete']);
            $routes->get('calendar-events', [ExamSchedules::class, 'getCalendarEvents'], ['as' => 'exams.schedules.calendar-events']);
            $routes->get('attendance/(:num)', [ExamSchedules::class, 'attendance/$1'], ['as' => 'exams.schedules.attendance']);
            $routes->post('attendance/save/(:num)', [ExamSchedules::class, 'saveAttendance/$1'], ['as' => 'exams.schedules.saveAttendance']);
            $routes->get('results/(:num)', [ExamSchedules::class, 'results/$1'], ['as' => 'exams.schedules.results']);
            $routes->post('results/save/(:num)', [ExamSchedules::class, 'saveResults/$1'], ['as' => 'exams.schedules.results.post']);
            $routes->get('get-disciplines/(:num)', [ExamSchedules::class, 'getDisciplinesByClass/$1'], ['as' => 'exams.schedules.get-disciplines']);
        });

        // === PESOS DAS AVALIAÇÕES ===
        $routes->group('weights', function ($routes) {
            $routes->get('', [GradeWeights::class, 'index'], ['as' => 'exams.weights']);
            $routes->post('get-table-data', [GradeWeights::class, 'getTableData'], ['as' => 'exams.weights.get-table-data']);
            $routes->get('create', [GradeWeights::class, 'create'], ['as' => 'exams.weights.create']);
            $routes->get('edit/(:num)', [GradeWeights::class, 'edit/$1'], ['as' => 'exams.weights.edit']);
            $routes->post('save', [GradeWeights::class, 'save'], ['as' => 'exams.weights.save']);
            $routes->get('delete/(:num)', [GradeWeights::class, 'delete/$1'], ['as' => 'exams.weights.delete']);
            $routes->get('by-level/(:num)', [GradeWeights::class, 'getByLevel/$1'], ['as' => 'exams.weights.by-level']);
            $routes->post('copy', [GradeWeights::class, 'copyFromPrevious'], ['as' => 'exams.weights.copy']);
        });

        // === CÁLCULO DE MÉDIAS ===
        $routes->group('calculate', function ($routes) {
            $routes->get('discipline/(:num)/(:num)/(:num)', [\App\Controllers\admin\GradeCalculator::class, 'discipline/$1/$2/$3'], ['as' => 'exams.calculate.discipline']);
            $routes->get('semester/(:num)/(:num)', [\App\Controllers\admin\GradeCalculator::class, 'semester/$1/$2'], ['as' => 'exams.calculate.semester']);
            $routes->get('class/(:num)/(:num)', [\App\Controllers\admin\GradeCalculator::class, 'class/$1/$2'], ['as' => 'exams.calculate.class']);
            $routes->post('run', [\App\Controllers\admin\GradeCalculator::class, 'runCalculation'], ['as' => 'exams.calculate.run']);
        });

        // === EXAMES DE RECURSO ===
        $routes->group('appeals', function ($routes) {
            $routes->get('', [AppealExams::class, 'index'], ['as' => 'exams.appeals']);
            $routes->post('get-table-data', [AppealExams::class, 'getTableData'], ['as' => 'exams.appeals.get-table-data']);
            $routes->post('generate', [AppealExams::class, 'generateAppealExams'], ['as' => 'exams.appeals.generate']);
            $routes->get('scheduled/(:num)', [AppealExams::class, 'scheduled/$1'], ['as' => 'exams.appeals.scheduled']);
            $routes->get('exam-students/(:num)', [AppealExams::class, 'examStudents/$1'], ['as' => 'exams.appeals.exam-students']);
            $routes->post('register-attendance/(:num)', [AppealExams::class, 'registerAttendance/$1'], ['as' => 'exams.appeals.register-attendance']);
            $routes->post('register-results/(:num)', [AppealExams::class, 'registerResults/$1'], ['as' => 'exams.appeals.register-results']);
            $routes->get('summary/(:num)', [AppealExams::class, 'summary/$1'], ['as' => 'exams.appeals.summary']);
            $routes->get('export/(:num)', [AppealExams::class, 'export/$1'], ['as' => 'exams.appeals.export']);
            $routes->get('get-stats/(:num)', [AppealExams::class, 'getStats/$1'], ['as' => 'exams.appeals.get-stats']);
        });
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
    /**
     * =====================================================
     * GESTÃO DE ALUNOS
     * =====================================================
     */
    $routes->group('students', function ($routes) {
        $routes->get('', [Clients::class, 'index'], ['as' => 'students']);
        $routes->post('get-table-data', [Clients::class, 'getTableData'], ['as' => 'students.get-table-data']);
        $routes->get('get-stats', [Clients::class, 'getStats'], ['as' => 'students.get-stats']);
        $routes->get('form-add', [Clients::class, 'studentForm'], ['as' => 'students.form-add']);
        $routes->get('form-edit/(:num)', [Clients::class, 'studentForm/$1'], ['as' => 'students.form-edit']);
        $routes->post('save', [Clients::class, 'saveStudent'], ['as' => 'students.save']);
        $routes->get('delete/(:num)', [Clients::class, 'delete/$1'], ['as' => 'students.delete']);
        $routes->get('view/(:num)', [Clients::class, 'viewStudent/$1'], ['as' => 'students.view']);
        $routes->get('search', [Clients::class, 'search'], ['as' => 'students.search']);

        // Matrículas
        $routes->group('enrollments', function ($routes) {
            $routes->get('', [Enrollments::class, 'index'], ['as' => 'students.enrollments']);
            $routes->post('get-table-data', [Enrollments::class, 'getTableData'], ['as' => 'students.enrollments.get-table-data']);
            $routes->get('get-stats', [Enrollments::class, 'getStats'], ['as' => 'students.enrollments.get-stats']);
            $routes->get('pending', [Enrollments::class, 'pending'], ['as' => 'students.enrollments.pending']); 
            $routes->get('form-add', [Enrollments::class, 'form'], ['as' => 'students.enrollments.form-add']);
            $routes->get('form-edit/(:num)', [Enrollments::class, 'form/$1'], ['as' => 'students.enrollments.form-edit']);
            $routes->post('save', [Enrollments::class, 'save'], ['as' => 'students.enrollments.save']);
            $routes->get('view/(:num)', [Enrollments::class, 'view/$1'], ['as' => 'students.enrollments.view']);
            $routes->get('history/(:num)', [Enrollments::class, 'history/$1'], ['as' => 'students.enrollments.history']);
            $routes->get('approve/(:num)', [Enrollments::class, 'approve/$1'], ['as' => 'students.enrollments.approve']);
            $routes->get('delete/(:num)', [Enrollments::class, 'delete/$1'], ['as' => 'students.enrollments.delete']);
            $routes->get('get-classes-by-year/(:num)', [Enrollments::class, 'getClassesByYear/$1'], ['as' => 'students.enrollments.get-classes-by-year']);
        });

       // Encarregados de Educação
        $routes->group('guardians', function ($routes) {
            // Listar e exportar
            $routes->get('', [StudentGuardians::class, 'index'], ['as' => 'students.guardians']);
            $routes->get('export', [StudentGuardians::class, 'export'], ['as' => 'students.guardians.export']);
            
            // CRUD
            $routes->post('save', [StudentGuardians::class, 'save'], ['as' => 'students.guardians.save']);  // Create/Update
            $routes->get('delete/(:num)', [StudentGuardians::class, 'delete/$1'], ['as' => 'students.guardians.delete']);
            $routes->post('toggle-status/(:num)', [StudentGuardians::class, 'toggleStatus/$1'], ['as' => 'students.guardians.toggle-status']);
            
            // Visualização
            $routes->get('view/(:num)', [StudentGuardians::class, 'view/$1'], ['as' => 'students.guardians.view']);
            
            // AJAX para edição
            $routes->get('get-guardian/(:num)', [StudentGuardians::class, 'getGuardian/$1'], ['as' => 'students.guardians.get']);
            
            // Associação com alunos
            $routes->post('associate-students', [StudentGuardians::class, 'associateStudents'], ['as' => 'students.guardians.associate-students']);
            $routes->get('get-by-student/(:num)', [StudentGuardians::class, 'getByStudent/$1'], ['as' => 'students.guardians.get-by-student']);
            $routes->get('get-available/(:num)', [StudentGuardians::class, 'getAvailable/$1'], ['as' => 'students.guardians.get-available']);
            $routes->post('link-to-student', [StudentGuardians::class, 'linkToStudent'], ['as' => 'students.guardians.link-to-student']);
            $routes->post('remove-from-student', [StudentGuardians::class, 'removeFromStudent'], ['as' => 'students.guardians.remove-from-student']);
            
            // Filtros AJAX
            $routes->get('get-students-by-class/(:num)', [StudentGuardians::class, 'getStudentsByClass/$1'], ['as' => 'students.guardians.get-students-by-class']);
        });
    });

    /**
     * =====================================================
     * GESTÃO DE PROFESSORES
     * =====================================================
     */
    $routes->group('teachers', function ($routes) {
        $routes->get('', [Teachers::class, 'index'], ['as' => 'teachers']);
        $routes->post('get-table-data', [Teachers::class, 'getTableData'], ['as' => 'teachers.get-table-data']);
        $routes->get('get-stats', [Teachers::class, 'getStats'], ['as' => 'teachers.get-stats']);
        $routes->get('export', [Teachers::class, 'export'], ['as' => 'teachers.export']);
        $routes->get('form-add', [Teachers::class, 'form'], ['as' => 'teachers.form-add']);
        $routes->get('form-edit/(:num)', [Teachers::class, 'form/$1'], ['as' => 'teachers.form-edit']);
        $routes->post('save', [Teachers::class, 'save'], ['as' => 'teachers.save']);
        $routes->get('delete/(:num)', [Teachers::class, 'delete/$1'], ['as' => 'teachers.delete']);
        $routes->get('view/(:num)', [Teachers::class, 'view/$1'], ['as' => 'teachers.view']);
        $routes->get('activate/(:num)', [Teachers::class, 'activate/$1'], ['as' => 'teachers.activate']);
        $routes->get('get-disciplines/(:num)', [Teachers::class, 'getDisciplines/$1'], ['as' => 'teachers.get-disciplines']);
        $routes->get('remove-assignment/(:num)', [Teachers::class, 'removeAssignment/$1'], ['as' => 'teachers.remove-assignment']);
        $routes->get('assign-class/(:num)', [Teachers::class, 'assignClass/$1'], ['as' => 'teachers.assign-class']);
        $routes->post('save-assignment', [Teachers::class, 'saveAssignment'], ['as' => 'teachers.save-assignment']);
    });

    /**
     * =====================================================
     * GESTÃO DE NOTAS E RESULTADOS
     * =====================================================
     */
    $routes->group('grades', function($routes) {
        $routes->get('', [Grades::class, 'index'], ['as' => 'admin.grades']);
        $routes->post('get-table-data', [Grades::class, 'getTableData'], ['as' => 'admin.grades.get-table-data']);
        $routes->get('class/(:num)', [Grades::class, 'class/$1'], ['as' => 'admin.grades.class']);
        $routes->get('student/(:num)', [Grades::class, 'student/$1'], ['as' => 'admin.grades.student']);
        $routes->get('report', [Grades::class, 'report'], ['as' => 'admin.grades.report']);
        $routes->get('statistics', [Grades::class, 'statistics'], ['as' => 'admin.grades.statistics']);
        $routes->get('period', [Grades::class, 'period'], ['as' => 'admin.grades.period']);
    });

    $routes->group('academic-records', function($routes) {
        $routes->get('', [AcademicRecords::class, 'index'], ['as' => 'academic.records']);
        $routes->post('get-table-data', [AcademicRecords::class, 'getTableData'], ['as' => 'academic.records.get-table-data']);
        $routes->get('class/(:num)', [AcademicRecords::class, 'class/$1'], ['as' => 'academic.records.class']);
        $routes->get('student/(:num)', [AcademicRecords::class, 'student/$1'], ['as' => 'academic.records.student']);
        $routes->get('semester/(:num)', [AcademicRecords::class, 'semester/$1'], ['as' => 'academic.records.semester']);
        $routes->get('export', [AcademicRecords::class, 'export'], ['as' => 'academic.records.export']);
        $routes->post('finalize-year', [AcademicRecords::class, 'finalizeYear'], ['as' => 'academic.records.finalize-year']);
        $routes->get('transcript/(:num)', [AcademicRecords::class, 'transcript/$1'], ['as' => 'academic.records.transcript']);
        $routes->get('certificate/(:num)', [AcademicRecords::class, 'certificate/$1'], ['as' => 'academic.records.certificate']);
        
        // Aprovações
        $routes->get('approvals', [AcademicRecords::class, 'approvalClasses'], ['as' => 'academic.approvals']);
        $routes->get('approvals/(:num)', [AcademicRecords::class, 'processApprovals/$1'], ['as' => 'academic.approvals.process']);
        $routes->post('save-approvals', [AcademicRecords::class, 'saveApprovals'], ['as' => 'academic.approvals.save']);
        $routes->post('promote-students', [AcademicRecords::class, 'promoteStudents'], ['as' => 'academic.promote-students']);
        $routes->get('check-eligibility/(:num)/(:num)', [AcademicRecords::class, 'checkEligibility/$1/$2'], ['as' => 'academic.check-eligibility']);
    });

    // Mini Grade Sheet
    $routes->group('mini-grade-sheet', function($routes) {
        $routes->get('', [MiniGradeSheet::class, 'index'], ['as' => 'mini-grade-sheet']);
        $routes->post('get-table-data', [MiniGradeSheet::class, 'getTableData'], ['as' => 'mini-grade-sheet.get-table-data']);
        $routes->get('view/(:num)', [MiniGradeSheet::class, 'view/$1'], ['as' => 'mini-grade-sheet.view']);
        $routes->get('print/(:num)', [MiniGradeSheet::class, 'print/$1'], ['as' => 'mini-grade-sheet.print']);
        $routes->get('export/(:num)', [MiniGradeSheet::class, 'export/$1'], ['as' => 'mini-grade-sheet.export']);
        $routes->get('trimestral', [MiniGradeSheet::class, 'trimestral'], ['as' => 'mini-grade-sheet.trimestral']);
        $routes->get('trimestral/class/(:num)', [MiniGradeSheet::class, 'trimestralClass/$1'], ['as' => 'mini-grade-sheet.trimestral-class']);
        $routes->get('trimestral/export/(:num)', [MiniGradeSheet::class, 'exportTrimestral/$1'], ['as' => 'mini-grade-sheet.trimestral-export']);
        $routes->get('disciplina', [MiniGradeSheet::class, 'disciplina'], ['as' => 'mini-grade-sheet.disciplina']);
        $routes->get('disciplina/view/(:num)/(:num)', [MiniGradeSheet::class, 'disciplinaView/$1/$2'], ['as' => 'mini-grade-sheet.disciplina-view']);
        $routes->get('disciplina/export/(:num)/(:num)', [MiniGradeSheet::class, 'exportDisciplina/$1/$2'], ['as' => 'mini-grade-sheet.disciplina-export']);
    });

    /**
     * =====================================================
     * GESTÃO FINANCEIRA
     * =====================================================
     */
    $routes->group('financial', function ($routes) {
        
        // Tipos de Taxas
        $routes->group('fee-types', function ($routes) {
            $routes->get('', [Fees::class, 'types'], ['as' => 'financial.fee-types']);
            $routes->post('get-table-data', [Fees::class, 'getTableData'], ['as' => 'financial.fee-types.get-table-data']);
            $routes->post('save', [Fees::class, 'saveType'], ['as' => 'financial.fee-types.save']);
            $routes->get('delete/(:num)', [Fees::class, 'deleteType/$1'], ['as' => 'financial.fee-types.delete']);
        });

        // Estrutura de Propinas
        $routes->group('fee-structure', function ($routes) {
            $routes->get('', [TuitionFees::class, 'index'], ['as' => 'financial.fee-structure']);
            $routes->post('get-table-data', [TuitionFees::class, 'getTableData'], ['as' => 'financial.fee-structure.get-table-data']);
            $routes->post('save', [TuitionFees::class, 'save'], ['as' => 'financial.fee-structure.save']);
        });

        // Pagamentos
        $routes->group('payments', function ($routes) {
            $routes->get('', [FeesPayments::class, 'index'], ['as' => 'financial.payments']);
            $routes->post('get-table-data', [FeesPayments::class, 'getTableData'], ['as' => 'financial.payments.get-table-data']);
            $routes->get('form-add', [FeesPayments::class, 'form'], ['as' => 'financial.payments.form-add']);
            $routes->post('save', [FeesPayments::class, 'save'], ['as' => 'financial.payments.save']);
            $routes->get('receipt/(:num)', [FeesPayments::class, 'receipt/$1'], ['as' => 'financial.payments.receipt']);
            $routes->get('history/(:num)', [FeesPayments::class, 'history/$1'], ['as' => 'financial.payments.history']);
        });

        // Faturas
        $routes->group('invoices', function ($routes) {
            $routes->get('', [Invoices::class, 'index'], ['as' => 'financial.invoices']);
            $routes->post('get-table-data', [Invoices::class, 'getTableData'], ['as' => 'financial.invoices.get-table-data']);
            $routes->get('view/(:num)', [Invoices::class, 'viewinvoices/$1'], ['as' => 'financial.invoices.view']);
        });

        // Modos de Pagamento
        $routes->group('payment-modes', function ($routes) {
            $routes->get('', [Payments::class, 'paymentsmode'], ['as' => 'financial.payment-modes']);
            $routes->post('get-table-data', [Payments::class, 'getTableData'], ['as' => 'financial.payment-modes.get-table-data']);
            $routes->post('save', [Payments::class, 'save_paymentsmode'], ['as' => 'financial.payment-modes.save']);
        });

        // Despesas
        $routes->group('expenses', function ($routes) {
            $routes->get('', [Expenses::class, 'index'], ['as' => 'financial.expenses']);
            $routes->post('get-table-data', [Expenses::class, 'getTableData'], ['as' => 'financial.expenses.get-table-data']);
        });

        // Moedas
        $routes->group('currencies', function ($routes) {
            $routes->get('', [Currencies::class, 'currencies'], ['as' => 'financial.currencies']);
            $routes->post('save', [Currencies::class, 'save'], ['as' => 'financial.currencies.save']);
        });

        // Taxas/Impostos
        $routes->group('taxes', function ($routes) {
            $routes->get('', [Taxes::class, 'index'], ['as' => 'financial.taxes']);
            $routes->post('save', [Taxes::class, 'save'], ['as' => 'financial.taxes.save']);
        });
    });

    /**
     * =====================================================
     * GESTÃO DE DOCUMENTOS
     * =====================================================
     */
    $routes->group('documents', function ($routes) {
        $routes->get('', [AdminDocuments::class, 'index'], ['as' => 'admin.documents']);
        $routes->post('get-table-data', [AdminDocuments::class, 'getTableData'], ['as' => 'admin.documents.get-table-data']);
        $routes->get('pending', [AdminDocuments::class, 'pending'], ['as' => 'admin.documents.pending']);
        $routes->get('verified', [AdminDocuments::class, 'verified'], ['as' => 'admin.documents.verified']);
        $routes->get('types', [AdminDocuments::class, 'types'], ['as' => 'admin.documents.types']);
        $routes->get('requests', [AdminDocuments::class, 'requests'], ['as' => 'admin.documents.requests']);
        $routes->get('reports', [AdminDocuments::class, 'reports'], ['as' => 'admin.documents.reports']);
        $routes->get('view/(:num)', [AdminDocuments::class, 'view/$1'], ['as' => 'admin.documents.view']);
        $routes->get('download/(:num)', [AdminDocuments::class, 'download/$1'], ['as' => 'admin.documents.download']);
        $routes->get('requestable', [AdminDocuments::class, 'requestableTypes'], ['as' => 'admin.documents.requestable']);
        $routes->post('requestable/save', [AdminDocuments::class, 'saveRequestableType'], ['as' => 'admin.documents.requestable.save']);
        $routes->get('requestable/delete/(:num)', [AdminDocuments::class, 'deleteRequestableType/$1'], ['as' => 'admin.documents.requestable.delete']);
    });

    // Gerador de Documentos
    $routes->group('document-generator', function ($routes) {
        $routes->get('', [DocumentGenerator::class, 'index'], ['as' => 'admin.document-generator']);
        $routes->post('get-table-data', [DocumentGenerator::class, 'getTableData'], ['as' => 'admin.document-generator.get-table-data']);
        $routes->get('pending', [DocumentGenerator::class, 'pending'], ['as' => 'admin.document-generator.pending']);
        $routes->get('generated', [DocumentGenerator::class, 'generated'], ['as' => 'admin.document-generator.generated']);
        $routes->get('templates', [DocumentGenerator::class, 'templates'], ['as' => 'admin.document-generator.templates']);
        $routes->get('generate/(:num)', [DocumentGenerator::class, 'generate/$1'], ['as' => 'admin.document-generator.generate']);
        $routes->post('generate-bulk', [DocumentGenerator::class, 'generateBulk'], ['as' => 'admin.document-generator.generate-bulk']);
        $routes->get('preview/(:num)', [DocumentGenerator::class, 'preview/$1'], ['as' => 'admin.document-generator.preview']);
        $routes->post('template/save', [DocumentGenerator::class, 'saveTemplate'], ['as' => 'admin.document-generator.template.save']);
        $routes->get('template/edit/(:num)', [DocumentGenerator::class, 'editTemplate/$1'], ['as' => 'admin.document-generator.template.edit']);
        $routes->get('preview-template/(:any)', [DocumentGenerator::class, 'previewTemplate/$1'], ['as' => 'admin.document-generator.preview-template']);
    });

    /**
     * =====================================================
     * GESTÃO DE UTILIZADORES E PERMISSÕES
     * =====================================================
     */
    $routes->group('users', function ($routes) {
        $routes->get('', [Users::class, 'index'], ['as' => 'users']);
        $routes->post('get-table-data', [Users::class, 'getTableData'], ['as' => 'users.get-table-data']);
        $routes->get('form-add', [Users::class, 'form'], ['as' => 'users.form-add']);
        $routes->get('form-edit/(:num)', [Users::class, 'form/$1'], ['as' => 'users.form-edit']);
        $routes->post('save', [Users::class, 'save'], ['as' => 'users.save']);
        $routes->get('profile', [Users::class, 'profile'], ['as' => 'users.profile']);
        $routes->get('view/(:num)', [Users::class, 'view_user/$1'], ['as' => 'users.view']);
    });

    $routes->group('roles', function ($routes) {
        $routes->get('', [Roles::class, 'index'], ['as' => 'roles']);
        $routes->post('get-table-data', [Roles::class, 'getTableData'], ['as' => 'roles.get-table-data']);
        $routes->get('form', [Roles::class, 'form'], ['as' => 'roles.form-add']);
        $routes->get('form/(:num)', [Roles::class, 'form/$1'], ['as' => 'roles.form-edit']);
        $routes->post('save', [Roles::class, 'save'], ['as' => 'roles.save']);
        $routes->get('permissions', [Roles::class, 'grouppermissions'], ['as' => 'roles.permissions']);
        $routes->post('update-permissions', [Roles::class, 'update_permissions'], ['as' => 'roles.permissions.update']);
        $routes->get('get-role-permissions', [Roles::class, 'getRolePermissions'], ['as' => 'roles.get-permissions']);
    });

    /**
     * =====================================================
     * NOTIFICAÇÕES
     * =====================================================
     */
    $routes->group('notifications', function ($routes) {
        $routes->get('', [Notifications::class, 'index'], ['as' => 'admin.notifications']);
        $routes->post('get-table-data', [Notifications::class, 'getTableData'], ['as' => 'admin.notifications.get-table-data']);
        $routes->get('read/(:num)', [Notifications::class, 'read/$1'], ['as' => 'admin.notifications.read']);
        $routes->get('mark-all-read', [Notifications::class, 'markAllRead'], ['as' => 'admin.notifications.mark-all-read']);
        $routes->get('delete/(:num)', [Notifications::class, 'delete/$1'], ['as' => 'admin.notifications.delete']);
        $routes->get('unread-count', [Notifications::class, 'getUnreadCount'], ['as' => 'admin.notifications.unread-count']);
    });

    /**
     * =====================================================
     * CONFIGURAÇÕES DO SISTEMA
     * =====================================================
     */
    $routes->group('settings', function ($routes) {
        $routes->get('', [Settings::class, 'index'], ['as' => 'settings']);
        $routes->get('school', [Settings::class, 'index'], ['as' => 'settings.school']);

        // Save routes
        $routes->post('save-school', [Settings::class, 'saveSchool'], ['as' => 'settings.save-school']);
        $routes->post('save-branding', [Settings::class, 'saveBranding'], ['as' => 'settings.save-branding']);
        $routes->post('save-management', [Settings::class, 'saveManagement'], ['as' => 'settings.save-management']);
        $routes->post('save-academic', [Settings::class, 'saveAcademic'], ['as' => 'settings.save-academic']);
        $routes->post('save-payment', [Settings::class, 'savePayment'], ['as' => 'settings.save-payment']);
        $routes->post('save-email', [Settings::class, 'saveEmail'], ['as' => 'settings.save-email']);
        $routes->post('test-email', [Settings::class, 'testEmail'], ['as' => 'settings.test-email']);

        // Remove logo routes
        $routes->get('remove-logo/(:any)', [Settings::class, 'removeLogo/$1'], ['as' => 'settings.remove-logo']);

        // Utility routes
        $routes->get('clear-cache', [Settings::class, 'clearCache'], ['as' => 'settings.clear-cache']);
        $routes->get('backup', [Settings::class, 'backup'], ['as' => 'settings.backup']);
    });

    /**
     * =====================================================
     * FERRAMENTAS
     * =====================================================
     */
    $routes->group('tools', function ($routes) {
        $routes->get('logs', [Logs::class, 'index'], ['as' => 'tools.logs']);
        $routes->get('logs/view/(:num)', [Logs::class, 'view/$1'], ['as' => 'tools.logs.view']);
        $routes->get('logs/clear', [Logs::class, 'clear'], ['as' => 'tools.logs.clear']);
        $routes->get('logs/export', [Logs::class, 'export'], ['as' => 'tools.logs.export']);
        $routes->post('logs/delete', [Logs::class, 'delete'], ['as' => 'tools.logs.delete']);
    });

    // Download Assets (apenas em desenvolvimento)
    $routes->get('download-assets', 'DownloadAssets::index');
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

         // Rotas de notificações
        $routes->group('notifications', function ($routes) {
          $routes->get('', [\App\Controllers\students\Notifications::class, 'index']);
          $routes->get('read/(:num)', [\App\Controllers\students\Notifications::class, 'read/$1']);
          $routes->get('markAllRead', [\App\Controllers\students\Notifications::class, 'markAllRead']);
          $routes->get('delete/(:num)', [\App\Controllers\students\Notifications::class, 'delete/$1']);
          $routes->get('getUnreadCount', [\App\Controllers\students\Notifications::class, 'getUnreadCount']);
        });


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
        
       $routes->group('mini-grade-sheet', function ($routes) {
          $routes->get('', [\App\Controllers\teachers\MiniGradeSheet::class, 'index']);
          $routes->get('filter', [\App\Controllers\teachers\MiniGradeSheet::class, 'filter']);
          $routes->get('print/(:num)',[\App\Controllers\teachers\MiniGradeSheet::class, 'print/$1']); // schedule_id
          $routes->get('export/(:num)',[\App\Controllers\teachers\MiniGradeSheet::class, 'export/$1']); // schedule_id
          $routes->get('get-disciplinas/(:num)',[\App\Controllers\teachers\MiniGradeSheet::class, 'getDisciplinas/$1']); // class_id
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

            // Rotas de notificações
      $routes->group('notifications', function ($routes) {
        $routes->get('', [\App\Controllers\students\Notifications::class, 'index']);
        $routes->get('read/(:num)', [\App\Controllers\students\Notifications::class, 'read/$1']);
        $routes->get('markAllRead', [\App\Controllers\students\Notifications::class, 'markAllRead']);
        $routes->get('delete/(:num)', [\App\Controllers\students\Notifications::class, 'delete/$1']);
        $routes->get('getUnreadCount', [\App\Controllers\students\Notifications::class, 'getUnreadCount']);
      });

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