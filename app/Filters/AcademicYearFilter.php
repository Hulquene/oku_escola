<?php
// app/Filters/AcademicYearFilter.php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AcademicYearFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Carregar o helper de configurações
        helper('settings');
        
        // Verificar se o ano acadêmico está na sessão
        if (!session()->has('academic_year_id')) {
            // Buscar das configurações
            $academicYearId = setting('current_academic_year');
            $semesterId = setting('current_semester');
            
            // Se não encontrar nas configurações, usar valores padrão
            if (!$academicYearId) {
                // Buscar o ano letivo mais recente
                $academicYearModel = new \App\Models\AcademicYearModel();
                $latestYear = $academicYearModel
                    ->where('is_active', 1)
                    ->orderBy('start_date', 'DESC')
                    ->first();
                    
                $academicYearId = $latestYear ? $latestYear->id : null;
                $academicYearName = $latestYear ? $latestYear->year_name : null;
            } else {
                $academicYearModel = new \App\Models\AcademicYearModel();
                $academicYear = $academicYearModel->find($academicYearId);
                $academicYearName = $academicYear ? $academicYear->year_name : null;
            }
            
            // Guardar na sessão
            session()->set([
                'academic_year_id' => $academicYearId,
                'academic_year_name' => $academicYearName,
                'semester_id' => $semesterId
            ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Não faz nada
    }
}