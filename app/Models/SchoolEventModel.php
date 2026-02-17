<?php

namespace App\Models;

class SchoolEventModel extends BaseModel
{
    protected $table = 'tbl_school_events';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'academic_year_id',
        'event_title',
        'event_type',
        'event_description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'all_day',
        'color',
        'created_by'
    ];
    
    protected $validationRules = [
        'academic_year_id' => 'required|numeric',
        'event_title' => 'required',
        'event_type' => 'required',
        'start_date' => 'required|valid_date'
    ];
    
    protected $useTimestamps = true;
    
    /**
     * Get events for calendar
     */
    public function getForCalendar($startDate, $endDate, $academicYearId = null)
    {
        $builder = $this->select('
                tbl_school_events.*,
                tbl_users.first_name,
                tbl_users.last_name
            ')
            ->join('tbl_users', 'tbl_users.id = tbl_school_events.created_by', 'left')
            ->where('start_date >=', $startDate)
            ->where('start_date <=', $endDate);
        
        if ($academicYearId) {
            $builder->where('academic_year_id', $academicYearId);
        }
        
        return $builder->orderBy('start_date', 'ASC')
            ->orderBy('start_time', 'ASC')
            ->findAll();
    }
    
    /**
     * Get upcoming events
     */
    public function getUpcoming($limit = 10)
    {
        $academicYearModel = new AcademicYearModel();
        $currentYear = $academicYearModel->getCurrent();
        
        return $this->where('academic_year_id', $currentYear->id)
            ->where('start_date >=', date('Y-m-d'))
            ->orderBy('start_date', 'ASC')
            ->orderBy('start_time', 'ASC')
            ->limit($limit)
            ->findAll();
    }
    
    /**
     * Get events by type
     */
    public function getByType($type, $academicYearId = null)
    {
        $builder = $this->where('event_type', $type);
        
        if ($academicYearId) {
            $builder->where('academic_year_id', $academicYearId);
        }
        
        return $builder->orderBy('start_date', 'DESC')
            ->findAll();
    }
}