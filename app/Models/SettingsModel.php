<?php
// app/Models/SettingsModel.php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table = 'tbl_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'value', 'status'];
    protected $useTimestamps = false;
    
    // 🔴 Garantir que retorne array (padrão do CI4)
    protected $returnType = 'array';
    
    /**
     * Get a setting value
     * 
     * @param string $key Setting key
     * @param mixed $default Default value if setting not found
     * @return mixed
     */
    public function get($key, $default = null)
    {
        // Tentar do cache primeiro
        $cache = service('cache');
        $cached = $cache->get('setting_' . $key);
        
        if ($cached !== null) {
            return $cached;
        }
        
        $setting = $this->where('name', $key)->first();
        
        if ($setting) {
            // 🔴 ACESSO COMO ARRAY
            $value = $setting['value'];
            $cache->save('setting_' . $key, $value, 3600); // Cache por 1 hora
            return $value;
        }
        
        return $default;
    }
    
    /**
     * Save a setting
     * 
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @return bool
     */
    public function saveSetting($key, $value)
    {
        $exists = $this->where('name', $key)->first();
        
        if ($exists) {
            // 🔴 ACESSO COMO ARRAY
            $result = $this->update($exists['id'], ['value' => $value]);
        } else {
            $result = $this->insert(['name' => $key, 'value' => $value]);
        }
        
        // Limpar cache
        cache()->delete('setting_' . $key);
        
        return $result;
    }
    
    /**
     * Save multiple settings
     * 
     * @param array $settings Key-value pairs of settings
     * @return bool
     */
    public function saveSettings(array $settings)
    {
        foreach ($settings as $key => $value) {
            $this->saveSetting($key, $value);
        }
        return true;
    }
    
    /**
     * Get all settings as key-value array
     * 
     * @return array
     */
    public function getAll()
    {
        $settings = $this->findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            // 🔴 ACESSO COMO ARRAY
            $result[$setting['name']] = $setting['value'];
        }
        
        return $result;
    }
    
    /**
     * Get multiple settings at once
     * 
     * @param array $keys Array of setting keys
     * @return array
     */
    public function getMultiple(array $keys)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }
        return $result;
    }
    
    /**
     * Delete a setting
     * 
     * @param string $key Setting key to delete
     * @return bool
     */
    public function deleteSetting($key)
    {
        $exists = $this->where('name', $key)->first();
        
        if ($exists) {
            $result = $this->delete($exists['id']);
            cache()->delete('setting_' . $key);
            return $result;
        }
        
        return false;
    }
    
    /**
     * Check if a setting exists
     * 
     * @param string $key Setting key
     * @return bool
     */
    public function has($key)
    {
        return $this->where('name', $key)->countAllResults() > 0;
    }
}