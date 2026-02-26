<?php
// app/Models/SettingsModel.php

namespace App\Models;

class SettingsModel extends BaseModel
{
    protected $table = 'tbl_settings';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'name',
        'value',
        'status'
    ];
     protected $useTimestamps = false;
    protected $validationRules = [
        'name' => 'required|is_unique[tbl_settings.name,id,{id}]',
        'value' => 'permit_empty',
        'status' => 'permit_empty|in_list[0,1]'
    ];
    
    // Sem timestamps - a tabela não tem created_at/updated_at
    
    /**
     * Get all settings as key-value pairs
     */
    public function getAll($activeOnly = true)
    {
        $builder = $this->select('name, value');
        
        if ($activeOnly) {
            $builder->where('status', 1);
        }
        
        $settings = $builder->findAll();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->name] = $setting->value;
        }
        
        return $result;
    }
    
    /**
     * Get a single setting value
     */
    public function get($key, $default = null)
    {
        $setting = $this->where('name', $key)
            ->where('status', 1)
            ->first();
        
        return $setting ? $setting->value : $default;
    }
    
    /**
     * Save a setting (update or insert)
     */
    public function saveSetting($key, $value)
    {
        $existing = $this->where('name', $key)->first();
        
        if ($existing) {
            return $this->update($existing->id, [
                'value' => $value,
                'status' => 1
            ]);
        } else {
            return $this->insert([
                'name' => $key,
                'value' => $value,
                'status' => 1
            ]);
        }
    }
    
    /**
     * Save multiple settings at once
     */
    public function saveSettings(array $settings)
    {
        $success = true;
        
        foreach ($settings as $key => $value) {
            if (!$this->saveSetting($key, $value)) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Delete a setting
     */
    public function deleteSetting($key)
    {
        return $this->where('name', $key)->delete();
    }
    
    /**
     * Get settings by prefix (e.g., 'school_', 'payment_')
     */
    public function getByPrefix($prefix)
    {
        $settings = $this->like('name', $prefix, 'after')
            ->where('status', 1)
            ->findAll();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->name] = $setting->value;
        }
        
        return $result;
    }
    
    /**
     * Toggle setting status
     */
    public function toggleStatus($id)
    {
        $setting = $this->find($id);
        if (!$setting) {
            return false;
        }
        
        $newStatus = $setting->status == 1 ? 0 : 1;
        
        return $this->update($id, ['status' => $newStatus]);
    }
}