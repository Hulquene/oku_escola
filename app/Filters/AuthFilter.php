<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = service('session');
        
        // Verificar se o usuário está logado
        if (!$session->has('user_id')) {
            return redirect()->to('/auth/login')
                ->with('error', 'Por favor, faça login para continuar.');
        }
        
        // Se não há argumentos, apenas verifica se está logado
        if (empty($arguments)) {
            return;
        }
        
        $userType = $session->get('user_type');
        $userId = $session->get('user_id');
        $currentUri = $request->getUri()->getPath();
        
        // Admin (root - ID 1) tem acesso a tudo
        if ($userId == 1) {
            return;
        }
        
        // MAPEAMENTO: converter user_type para o valor esperado nos argumentos
        $typeMap = [
            'admin' => 'admin',
            'teacher' => 'teachers', // teacher -> teachers
            'student' => 'students', // student -> students
            'guardian' => 'guardians'
        ];
        
        $mappedType = $typeMap[$userType] ?? $userType;
        
        // Verificar se o tipo do usuário (mapeado) está nos argumentos permitidos
        if (!in_array($mappedType, $arguments)) {
            // Se não tiver permissão, redireciona para o dashboard apropriado
            switch ($userType) {
                case 'admin':
                    $redirectUrl = '/admin/dashboard';
                    break;
                case 'teacher':
                    $redirectUrl = '/teachers/dashboard';
                    break;
                case 'student':
                    $redirectUrl = '/students/dashboard';
                    break;
                default:
                    $redirectUrl = '/';
            }
            
            return redirect()->to($redirectUrl)
                ->with('error', 'Acesso não autorizado a esta área.');
        }
        
        // Se chegou aqui, tem permissão - deixa a requisição prosseguir
        return;
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}