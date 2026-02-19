<?php
// app/Models/EnrollmentModel.php 
namespace App\Models;

use App\Models\AcademicYearModel; 

class EnrollmentModel extends BaseModel
{
    protected $table = 'tbl_enrollments';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'student_id',
        'class_id',
        'academic_year_id',
        'grade_level_id',
        'previous_grade_id',
          'course_id', 
        'enrollment_date',
        'enrollment_number',
        'enrollment_type',
        'previous_class_id',
        'status',
        'observations',
        'created_by'
    ];
    
    protected $validationRules = [
        'student_id' => 'required|numeric',
        'class_id' => 'permit_empty|numeric',
        'academic_year_id' => 'required|numeric',
        'grade_level_id' => 'required|numeric', 
        'course_id' => 'permit_empty|numeric',
        'enrollment_date' => 'required|valid_date',
        'enrollment_number' => 'required|is_unique[tbl_enrollments.enrollment_number,id,{id}]'
    ];
    
    protected $validationMessages = [
        'enrollment_number' => [
            'is_unique' => 'Este número de matrícula já está em uso'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Generate enrollment number
     */
    public function generateEnrollmentNumber()
    {
        $year = date('Y');
        $last = $this->select('enrollment_number')
            ->like('enrollment_number', "ENR{$year}", 'after')
            ->orderBy('id', 'DESC')
            ->first();
        
        if ($last) {
            $lastNumber = intval(substr($last->enrollment_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "ENR{$year}{$newNumber}";
    }
    
   /**
     * Get enrollment with all details for view and form
     * AGORA INCLUI INFORMAÇÕES DO CURSO
     */
    public function getWithDetails($id)
    {
        return $this->select('
                tbl_enrollments.*,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_users.email,
                tbl_users.phone,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_classes.class_shift,
                tbl_classes.class_room,
                tbl_academic_years.year_name,
                tbl_academic_years.start_date as year_start,
                tbl_academic_years.end_date as year_end,
                tbl_grade_levels.level_name as grade_level_name,
                tbl_grade_levels.education_level,
                tbl_courses.id as course_id,
                tbl_courses.course_name,
                tbl_courses.course_code,
                tbl_courses.course_type,
                tbl_users_created.first_name as created_by_first,
                tbl_users_created.last_name as created_by_last,
                CONCAT(tbl_users_created.first_name, " ", tbl_users_created.last_name) as created_by_username
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id', 'left')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_enrollments.grade_level_id', 'left')
            ->join('tbl_courses', 'tbl_courses.id = tbl_enrollments.course_id', 'left')  
            ->join('tbl_users as tbl_users_created', 'tbl_users_created.id = tbl_enrollments.created_by', 'left')
            ->where('tbl_enrollments.id', $id)
            ->first();
    }
    /**
     * Get active enrollments by class
     */
    public function getActiveByClass($classId)
    {
        return $this->select('
                tbl_enrollments.*,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_users.email,
                tbl_users.photo
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
    }
    /**
 * Get enrollment with all details for view
 */
public function getForView($id)
{
    return $this->select('
            tbl_enrollments.*,
            tbl_students.student_number,
            tbl_users.first_name,
            tbl_users.last_name,
            tbl_users.email,
            tbl_users.phone,
            tbl_classes.class_name,
            tbl_classes.class_code,
            tbl_classes.class_shift,
            tbl_classes.class_room,
            tbl_academic_years.year_name,
            tbl_academic_years.start_date,
            tbl_academic_years.end_date,
            tbl_users_created.first_name as created_by_first,
            tbl_users_created.last_name as created_by_last,
            CONCAT(tbl_users_created.first_name, " ", tbl_users_created.last_name) as created_by_username
        ')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
        ->join('tbl_users as tbl_users_created', 'tbl_users_created.id = tbl_enrollments.created_by', 'left')
        ->where('tbl_enrollments.id', $id)
        ->first();
}
    /**
     * Get active enrollments by academic year
     */
    public function getActiveByAcademicYear($academicYearId)
    {
        return $this->select('
                tbl_enrollments.*,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_classes.class_name
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->where('tbl_enrollments.academic_year_id', $academicYearId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Check if student is enrolled in class
     */
    public function isEnrolled($studentId, $classId, $academicYearId = null)
    {
        $builder = $this->where('student_id', $studentId)
            ->where('class_id', $classId)
            ->where('status', 'Ativo');
        
        if ($academicYearId) {
            $builder->where('academic_year_id', $academicYearId);
        }
        
        return $builder->countAllResults() > 0;
    }
    
    /**
     * Check if student has active enrollment in any class
     */
    public function hasActiveEnrollment($studentId, $academicYearId = null)
    {
        $builder = $this->where('student_id', $studentId)
            ->where('status', 'Ativo');
        
        if ($academicYearId) {
            $builder->where('academic_year_id', $academicYearId);
        }
        
        return $builder->countAllResults() > 0;
    }
    
    /**
     * Get current active enrollment for student
     */
    public function getCurrentForStudent($studentId)
    {
        return $this->select('
                tbl_enrollments.*,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_classes.class_shift,
                tbl_academic_years.year_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->where('tbl_enrollments.student_id', $studentId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_enrollments.id', 'DESC')
            ->first();
    }
    
    /**
     * Get enrollment history for student
     */
    public function getStudentHistory($studentId)
    {
        return $this->select('
                tbl_enrollments.*,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_academic_years.year_name,
                tbl_academic_years.start_date,
                tbl_academic_years.end_date
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->where('tbl_enrollments.student_id', $studentId)
            ->orderBy('tbl_academic_years.start_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Get enrollments by status
     */
    public function getByStatus($status, $academicYearId = null)
    {
        $builder = $this->select('
                tbl_enrollments.*,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_students.student_number,
                tbl_classes.class_name
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->where('tbl_enrollments.status', $status);
        
        if ($academicYearId) {
            $builder->where('tbl_enrollments.academic_year_id', $academicYearId);
        }
        
        return $builder->orderBy('tbl_enrollments.enrollment_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Count enrollments by class
     */
    public function countByClass($classId, $status = 'Ativo')
    {
        return $this->where('class_id', $classId)
            ->where('status', $status)
            ->countAllResults();
    }
    
    /**
     * Get enrollment statistics
     */
    public function getStatistics($academicYearId = null)
    {
        $builder = $this->select('
                COUNT(*) as total,
                SUM(CASE WHEN status = "Ativo" THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = "Pendente" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "Concluído" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "Transferido" THEN 1 ELSE 0 END) as transferred,
                SUM(CASE WHEN status = "Anulado" THEN 1 ELSE 0 END) as cancelled
            ');
        
        if ($academicYearId) {
            $builder->where('academic_year_id', $academicYearId);
        }
        
        return $builder->first();
    }
    
    /**
     * Get enrollment trends by month
     */
    public function getMonthlyTrends($year = null)
    {
        $year = $year ?: date('Y');
        
        return $this->select('
                MONTH(enrollment_date) as month,
                COUNT(*) as total,
                SUM(CASE WHEN enrollment_type = "Nova" THEN 1 ELSE 0 END) as new,
                SUM(CASE WHEN enrollment_type = "Renovação" THEN 1 ELSE 0 END) as renewal,
                SUM(CASE WHEN enrollment_type = "Transferência" THEN 1 ELSE 0 END) as transfer
            ')
            ->where('YEAR(enrollment_date)', $year)
            ->groupBy('MONTH(enrollment_date)')
            ->orderBy('MONTH(enrollment_date)', 'ASC')
            ->findAll();
    }
    
    /**
     * Bulk update enrollment status
     */
    public function bulkUpdateStatus(array $enrollmentIds, $status)
    {
        return $this->whereIn('id', $enrollmentIds)
            ->set(['status' => $status])
            ->update();
    }
    
    /**
     * Complete academic year - move students to next class
     */
    public function completeAcademicYear($oldAcademicYearId, $newAcademicYearId)
    {
        // Get all active enrollments from old year
        $enrollments = $this->where('academic_year_id', $oldAcademicYearId)
            ->where('status', 'Ativo')
            ->findAll();
        
        $results = [
            'total' => count($enrollments),
            'promoted' => 0,
            'retained' => 0,
            'errors' => []
        ];
        
        foreach ($enrollments as $enrollment) {
            // Mark old enrollment as completed
            $this->update($enrollment->id, ['status' => 'Concluído']);
            
            // Create new enrollment for next academic year
            // This logic depends on your promotion rules
            $newEnrollment = [
                'student_id' => $enrollment->student_id,
                'class_id' => $this->getNextClass($enrollment->class_id), // You need to implement this
                'academic_year_id' => $newAcademicYearId,
                'enrollment_date' => date('Y-m-d'),
                'enrollment_number' => $this->generateEnrollmentNumber(),
                'enrollment_type' => 'Renovação',
                'status' => 'Ativo',
                'previous_class_id' => $enrollment->class_id
            ];
            
            if ($this->insert($newEnrollment)) {
                $results['promoted']++;
            } else {
                $results['retained']++;
                $results['errors'][] = "Failed to promote student ID: {$enrollment->student_id}";
            }
        }
        
        return $results;
    }
    
    /**
     * Get next class based on current class (helper method)
     */
    protected function getNextClass($currentClassId)
    {
        // Implement logic to determine next class
        // This depends on your grade progression rules
        $classModel = new ClassModel();
        $currentClass = $classModel->find($currentClassId);
        
        if (!$currentClass) {
            return null;
        }
        
        // Find next grade level
        $gradeLevelModel = new GradeLevelModel();
        $nextLevel = $gradeLevelModel->getNextLevel($currentClass->grade_level_id);
        
        if (!$nextLevel) {
            return null; // No next level (graduated)
        }
        
        // Find class for next level in new academic year
        $nextClass = $classModel
            ->where('grade_level_id', $nextLevel->id)
            ->where('class_shift', $currentClass->class_shift)
            ->first();
        
        return $nextClass ? $nextClass->id : null;
    }
    /**
     * Get current enrollment by user ID (para alunos)
     * 
     * @param int $userId ID do usuário
     * @return object|null
     */
    public function getCurrentByUserId($userId)
    {
        return $this->select('
                tbl_enrollments.*,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_classes.class_shift,
                tbl_academic_years.year_name,
                tbl_students.id as student_id,
                tbl_students.student_number
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->where('tbl_students.user_id', $userId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_enrollments.id', 'DESC')
            ->first();
    }
    /**
     * Get enrollments by course
     */
    public function getByCourse($courseId, $academicYearId = null)
    {
        $builder = $this->select('
                tbl_enrollments.*,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_classes.class_name
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->where('tbl_enrollments.course_id', $courseId)
            ->where('tbl_enrollments.status', 'Ativo');
        
        if ($academicYearId) {
            $builder->where('tbl_enrollments.academic_year_id', $academicYearId);
        }
        
        return $builder->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
    }
}