<?php

namespace App\Models;

class SettingsModel extends BaseModel
{
    protected $table = 'tbl_settings';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'setting_name',
        'setting_value'
    ];
    
    protected $validationRules = [
        'setting_name' => 'required|is_unique[tbl_settings.setting_name,id,{id}]',
    ];
    
    protected $validationMessages = [
        'setting_name' => [
            'required' => 'O nome da configuração é obrigatório',
            'is_unique' => 'Esta configuração já existe'
        ]
    ];
    
    /**
     * Get setting value
     */
    public function get($key, $default = null)
    {
        $setting = $this->where('setting_name', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }
    
    /**
     * Set setting value
     */
    public function saveSetting($key, $value)
    {
        $setting = $this->where('setting_name', $key)->first();
        
        if ($setting) {
            return $this->update($setting->id, ['setting_value' => $value]);
        } else {
            return $this->insert([
                'setting_name' => $key,
                'setting_value' => $value
            ]);
        }
    }
    
    /**
     * Get all settings as array
     */
    public function getAll()
    {
        $settings = $this->findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->setting_name] = $setting->setting_value;
        }
        
        return $result;
    }
    
    /**
     * Update multiple settings - CORRIGIDO com assinatura compatível
     */
    public function updateBatch(?array $set = null, ?string $index = null, int $batchSize = 100, bool $returnSQL = false)
    {
        if ($set === null) {
            return false;
        }
        
        $success = true;
        foreach ($set as $key => $value) {
            $result = $this->saveSetting($key, $value);
            if (!$result) {
                $success = false;
            }
        }
        
        return $success;
    }
}