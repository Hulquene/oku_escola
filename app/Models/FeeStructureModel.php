<?php

namespace App\Models;

class FeeStructureModel extends BaseModel
{
    protected $table = 'tbl_fee_structure';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'academic_year_id',
        'grade_level_id',
        'fee_type_id',
        'amount',
        'currency_id',
        'due_day',
        'description',
        'is_mandatory',
        'is_active'
    ];
    
    protected $validationRules = [
        'academic_year_id' => 'required|numeric',
        'grade_level_id' => 'required|numeric',
        'fee_type_id' => 'required|numeric',
        'amount' => 'required|numeric'
    ];
    
    /**
     * Get fee structure for grade level
     */
    public function getByGradeLevel($gradeLevelId, $academicYearId = null)
    {
        $builder = $this->select('
                tbl_fee_structure.*,
                tbl_fee_types.type_name,
                tbl_fee_types.type_category,
                tbl_fee_types.is_recurring,
                tbl_fee_types.recurrence_period,
                tbl_currencies.currency_code,
                tbl_currencies.currency_symbol
            ')
            ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
            ->join('tbl_currencies', 'tbl_currencies.id = tbl_fee_structure.currency_id', 'left')
            ->where('tbl_fee_structure.grade_level_id', $gradeLevelId)
            ->where('tbl_fee_structure.is_active', 1);
        
        if ($academicYearId) {
            $builder->where('tbl_fee_structure.academic_year_id', $academicYearId);
        } else {
            $academicYearModel = new AcademicYearModel();
            $currentYear = $academicYearModel->getCurrent();
            if ($currentYear) {
                $builder->where('tbl_fee_structure.academic_year_id', $currentYear->id);
            }
        }
        
        return $builder->orderBy('tbl_fee_types.type_category', 'ASC')
            ->orderBy('tbl_fee_types.type_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Generate fees for enrolled students
     */
    public function generateStudentFees($academicYearId = null)
    {
        if (!$academicYearId) {
            $academicYearModel = new AcademicYearModel();
            $currentYear = $academicYearModel->getCurrent();
            $academicYearId = $currentYear ? $currentYear->id : null;
        }
        
        if (!$academicYearId) {
            return false;
        }
        
        $enrollmentModel = new EnrollmentModel();
        $studentFeeModel = new StudentFeeModel();
        
        $enrollments = $enrollmentModel->where('academic_year_id', $academicYearId)
            ->where('status', 'Ativo')
            ->findAll();
        
        $this->transStart();
        
        foreach ($enrollments as $enrollment) {
            // Get class to find grade level
            $classModel = new ClassModel();
            $class = $classModel->find($enrollment->class_id);
            
            if (!$class) {
                continue;
            }
            
            // Get fee structure for this grade level
            $feeStructures = $this->getByGradeLevel($class->grade_level_id, $academicYearId);
            
            foreach ($feeStructures as $fee) {
                // Check if already generated
                $exists = $studentFeeModel->where('enrollment_id', $enrollment->id)
                    ->where('fee_structure_id', $fee->id)
                    ->first();
                
                if ($exists) {
                    continue;
                }
                
                // Generate reference number
                $referenceNumber = 'FEE-' . $enrollment->id . '-' . $fee->id . '-' . date('Ym');
                
                // Calculate due date
                $dueDate = date('Y-m-d', strtotime("+{$fee->due_day} days"));
                
                $studentFeeModel->insert([
                    'enrollment_id' => $enrollment->id,
                    'fee_structure_id' => $fee->id,
                    'reference_number' => $referenceNumber,
                    'due_date' => $dueDate,
                    'amount' => $fee->amount,
                    'total_amount' => $fee->amount,
                    'status' => 'Pendente'
                ]);
            }
        }
        
        $this->transComplete();
        
        return $this->transStatus();
    }
}