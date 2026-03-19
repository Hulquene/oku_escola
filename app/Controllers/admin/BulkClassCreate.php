<?php
// app/Controllers/admin/BulkClassCreate.php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\ClassModel;
use App\Models\AcademicYearModel;
use App\Models\GradeLevelModel;
use App\Models\CourseModel;
use App\Models\ClassDisciplineModel;
use App\Models\GradeDisciplineModel;
use App\Models\CourseDisciplineModel;

class BulkClassCreate extends BaseController
{
    protected $classModel;
    protected $academicYearModel;
    protected $gradeLevelModel;
    protected $courseModel;
    protected $classDisciplineModel;
    protected $gradeDisciplineModel;
    protected $courseDisciplineModel;
    
    public function __construct()
    {
        $this->classModel = new ClassModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->courseModel = new CourseModel();
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->gradeDisciplineModel = new GradeDisciplineModel();
        $this->courseDisciplineModel = new CourseDisciplineModel();
        
        helper('url');
    }
    
    /**
     * Página de criação em lote
     */
    public function index()
    {
        $data['title'] = 'Criar Turmas em Lote';
        
        // Dados para os selects
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        $data['gradeLevels'] = $this->gradeLevelModel
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        
        $data['courses'] = $this->courseModel
            ->where('is_active', 1)
            ->orderBy('course_name', 'ASC')
            ->findAll();
        
        return view('admin/classes/classes/bulk_create', $data);
    }
    
    /**
     * Processar criação em lote
     */
    public function process()
    {
        // Regras de validação corrigidas
        $rules = [
            'academic_year_id' => 'required|is_natural_no_zero',
            'class_shift' => 'required|in_list[Manhã,Tarde,Noite,Integral]',
            'start_number' => 'required|integer|greater_than[0]|less_than[100]',
            'end_number' => 'required|integer|greater_than[0]|less_than[100]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        // Validação manual para grade_level_ids
        $gradeLevelIds = $this->request->getPost('grade_level_ids');
        if (!is_array($gradeLevelIds) || empty($gradeLevelIds)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Selecione pelo menos um nível de ensino');
        }
        
        // Recolher dados
        $academicYearId = $this->request->getPost('academic_year_id');
        $classShift = $this->request->getPost('class_shift');
        $courseIds = $this->request->getPost('course_ids') ?: [];
        $startNumber = (int) $this->request->getPost('start_number');
        $endNumber = (int) $this->request->getPost('end_number');
        $classPrefix = $this->request->getPost('class_prefix') ?: 'Turma';
        $capacity = $this->request->getPost('capacity') ?: 30;
        $roomPrefix = $this->request->getPost('room_prefix') ?: 'Sala';
        
        // Validar range
        if ($startNumber > $endNumber) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'O número inicial deve ser menor que o número final');
        }
        
        // Limite de segurança
        $totalClasses = ($endNumber - $startNumber + 1) * count($gradeLevelIds);
        if ($totalClasses > 50) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Máximo de 50 turmas por vez. Você está tentando criar ' . $totalClasses);
        }
        
        $db = db_connect();
        $db->transStart();
        
        $created = 0;
        $suggested = 0;
        $createdClasses = [];
        
        try {
            foreach ($gradeLevelIds as $gradeLevelId) {
                $gradeLevel = $this->gradeLevelModel->find($gradeLevelId);
                if (!$gradeLevel) continue;
                
                // Determinar curso (se for Ensino Médio)
                $courseId = null;
                if ($gradeLevel['education_level'] == 'Ensino Médio' && isset($courseIds[$gradeLevelId])) {
                    $courseId = $courseIds[$gradeLevelId];
                }
                
                for ($i = $startNumber; $i <= $endNumber; $i++) {
                    // Gerar dados da turma
                    $className = $classPrefix . ' ' . $i;
                    $classCode = $this->generateClassCode($gradeLevel, $i, $classShift, $academicYearId);
                    $classRoom = $roomPrefix . ' ' . $i;
                    
                    // Verificar se código já existe
                    $counter = 1;
                    $originalCode = $classCode;
                    while ($this->classModel->where('class_code', $classCode)->first()) {
                        $classCode = $originalCode . '-' . $counter;
                        $counter++;
                    }
                    
                    // Criar turma
                    $classData = [
                        'class_name' => $className,
                        'class_code' => $classCode,
                        'grade_level_id' => $gradeLevelId,
                        'course_id' => $courseId,
                        'academic_year_id' => $academicYearId,
                        'class_shift' => $classShift,
                        'class_room' => $classRoom,
                        'capacity' => $capacity,
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $this->classModel->save($classData);
                    $classId = $this->classModel->getInsertID();
                    $created++;
                    
                    $createdClasses[] = [
                        'id' => $classId,
                        'name' => $className,
                        'level' => $gradeLevel['level_name']
                    ];
                    
                    // Sugerir disciplinas automaticamente
                    $suggested += $this->suggestDisciplinesForClass($classId, $gradeLevelId, $courseId);
                }
            }
            
            $db->transComplete();
            
            // Mensagem de sucesso
            $message = "✅ <strong>{$created} turmas</strong> criadas com sucesso!<br>";
            $message .= "📚 <strong>{$suggested} disciplinas</strong> sugeridas automaticamente.<br><br>";
            $message .= "Turmas criadas:<br>";
            
            $counter = 0;
            foreach ($createdClasses as $class) {
                if ($counter < 5) {
                    $message .= "• {$class['name']} ({$class['level']})<br>";
                }
                $counter++;
            }
            
            if (count($createdClasses) > 5) {
                $message .= "... e mais " . (count($createdClasses) - 5) . " turmas";
            }
            
            return redirect()->to('/admin/classes/classes')
                ->with('success', $message);
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Erro na criação em lote: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao criar turmas: ' . $e->getMessage());
        }
    }
    
    /**
     * Gerar código único para a turma
     */
    private function generateClassCode($gradeLevel, $number, $shift, $academicYearId)
    {
        $year = $this->academicYearModel->find($academicYearId);
        $yearSuffix = $year ? date('y', strtotime($year['start_date'])) : date('y');
        
        $shiftCode = match($shift) {
            'Manhã' => 'M',
            'Tarde' => 'T',
            'Noite' => 'N',
            'Integral' => 'I',
            default => 'X'
        };
        
        // Formato: [CódigoNível]-[Número][Turno]-[Ano]
        return $gradeLevel['level_code'] . '-' . $number . $shiftCode . '-' . $yearSuffix;
    }
    
    /**
     * Sugerir disciplinas para a turma
     */
    private function suggestDisciplinesForClass($classId, $gradeLevelId, $courseId = null)
    {
        $suggested = 0;
        
        if ($courseId) {
            // Ensino Médio - busca em course_disciplines
            $disciplines = $this->courseDisciplineModel
                ->where('course_id', $courseId)
                ->where('grade_level_id', $gradeLevelId)
                ->where('is_mandatory', 1)
                ->findAll();
            
            foreach ($disciplines as $d) {
                $periodMap = [
                    '1' => '1º Semestre',
                    '2' => '2º Semestre',
                    '3' => 'Anual',
                    'Anual' => 'Anual'
                ];
                $periodType = $periodMap[$d->semester] ?? 'Anual';
                
                $exists = $this->classDisciplineModel
                    ->where('class_id', $classId)
                    ->where('discipline_id', $d->discipline_id)
                    ->first();
                
                if (!$exists) {
                    $this->classDisciplineModel->save([
                        'class_id' => $classId,
                        'discipline_id' => $d->discipline_id,
                        'workload_hours' => $d['workload_hours'],
                        'period_type' => $periodType,
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $suggested++;
                }
            }
        } else {
            // Ensino Geral - busca em grade_disciplines
            $disciplines = $this->gradeDisciplineModel
                ->where('grade_level_id', $gradeLevelId)
                ->where('is_mandatory', 1)
                ->findAll();
            
            foreach ($disciplines as $d) {
                $exists = $this->classDisciplineModel
                    ->where('class_id', $classId)
                    ->where('discipline_id', $d->discipline_id)
                    ->first();
                
                if (!$exists) {
                    $this->classDisciplineModel->save([
                        'class_id' => $classId,
                        'discipline_id' => $d->discipline_id,
                        'workload_hours' => $d['workload_hours'],
                        'period_type' => $d->semester,
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $suggested++;
                }
            }
        }
        
        return $suggested;
    }
}