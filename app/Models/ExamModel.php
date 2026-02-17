<?php

namespace App\Models;

class ExamModel extends BaseModel
{
    protected $table = 'tbl_exams';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'exam_name',
        'class_id',
        'discipline_id',
        'semester_id',
        'exam_board_id',
        'exam_date',
        'exam_time',
        'exam_room',
        'max_score',
        'min_score',
        'approval_score',
        'description',
        'is_published',
        'created_by'
    ];
    
    protected $validationRules = [
        'exam_name' => 'required',
        'class_id' => 'required|numeric',
        'discipline_id' => 'required|numeric',
        'semester_id' => 'required|numeric',
        'exam_board_id' => 'required|numeric',
        'exam_date' => 'required|valid_date'
    ];
    
    protected $useTimestamps = true;
    
    /**
     * Get exam with details
     */
    public function getWithDetails($id)
    {
        return $this->select('
                tbl_exams.*,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                tbl_semesters.semester_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_boards.weight,
                tbl_users.first_name,
                tbl_users.last_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exams.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_exams.semester_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exams.exam_board_id')
            ->join('tbl_users', 'tbl_users.id = tbl_exams.created_by', 'left')
            ->where('tbl_exams.id', $id)
            ->first();
    }
    
    /**
     * Get exams by class and semester
     */
    public function getByClassAndSemester($classId, $semesterId)
    {
        return $this->select('
                tbl_exams.*,
                tbl_disciplines.discipline_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exams.exam_board_id')
            ->where('tbl_exams.class_id', $classId)
            ->where('tbl_exams.semester_id', $semesterId)
            ->orderBy('tbl_exams.exam_date', 'ASC')
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get upcoming exams
     */
    public function getUpcoming($classId = null, $days = 30)
    {
        $builder = $this->select('
                tbl_exams.*,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exams.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->where('tbl_exams.exam_date >=', date('Y-m-d'))
            ->where('tbl_exams.exam_date <=', date('Y-m-d', strtotime("+{$days} days")));
        
        if ($classId) {
            $builder->where('tbl_exams.class_id', $classId);
        }
        
        return $builder->orderBy('tbl_exams.exam_date', 'ASC')
            ->findAll();
    }
    
    /**
     * Get exams by teacher
     */
    public function getByTeacher($teacherId, $semesterId = null)
    {
        $builder = $this->select('
                tbl_exams.*,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                tbl_exam_boards.board_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exams.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exams.exam_board_id')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.class_id = tbl_exams.class_id AND tbl_class_disciplines.discipline_id = tbl_exams.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId);
        
        if ($semesterId) {
            $builder->where('tbl_exams.semester_id', $semesterId);
        }
        
        return $builder->orderBy('tbl_exams.exam_date', 'ASC')
            ->findAll();
    }
}