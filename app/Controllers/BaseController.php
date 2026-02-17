<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\Session\Session;

/**
 * Base Controller
 * 
 * Provides shared functionality for all controllers
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object
     */
    protected $request;
    
    /**
     * Instance of the main Response object
     */
    protected $response;
    
    /**
     * Instance of the main Logger object
     */
    protected $logger;
    
    /**
     * Instance of the Session object
     */
    protected $session;
    
    /**
     * Current user data
     */
    protected $currentUser;
    
    /**
     * Validation instance
     */
    protected $validation;
    
    /**
     * Helpers to load
     */
    protected $helpers = ['url', 'form', 'html', 'text', 'log','date'];
    
    /**
     * Data to be passed to views
     */
    protected $data = [];
    
    /**
     * Constructor
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        // Initialize properties
        $this->session = service('session');
        $this->validation = service('validation');
        
        // Set current user if logged in
        if ($this->session->has('user_id')) {
            $this->currentUser = (object)[
                'id' => $this->session->get('user_id'),
                'username' => $this->session->get('username'),
                'name' => $this->session->get('name'),
                'role' => $this->session->get('role'),
                'role_id' => $this->session->get('role_id'),
                'user_type' => $this->session->get('user_type')
            ];
        }
        
        // Set common view data
        $this->data['currentUser'] = $this->currentUser;
        $this->data['session'] = $this->session;
        $this->data['request'] = $this->request;
    }
    
    /**
     * Check if user is logged in
     */
    protected function isLoggedIn(): bool
    {
        return $this->session->has('user_id');
    }
    
    /**
     * Check if current user is the root administrator (ID 1)
     */
    protected function isRoot(): bool
    {
        return $this->currentUser && $this->currentUser->id == 1;
    }
    
    /**
     * Check if user has permission - ROOT TEM ACESSO TOTAL
     */
    protected function hasPermission(string $permission): bool
    {
        // Root tem acesso a tudo
        if ($this->isRoot()) {
            return true;
        }
        
        // Usuários não logados não têm permissão
        if (!$this->currentUser) {
            return false;
        }
        
        // Verificar permissões na sessão
        $permissions = $this->session->get('permissions') ?? [];
        return in_array($permission, $permissions);
    }
    
    /**
     * Check if user has any of the given permissions
     */
    protected function hasAnyPermission(array $permissions): bool
    {
        // Root tem acesso a tudo
        if ($this->isRoot()) {
            return true;
        }
        
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if user has all of the given permissions
     */
    protected function hasAllPermissions(array $permissions): bool
    {
        // Root tem acesso a tudo
        if ($this->isRoot()) {
            return true;
        }
        
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Redirect if not logged in
     */
    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/auth/login')->with('error', 'Por favor, faça login primeiro.');
        }
    }
    
    /**
     * Redirect if no permission
     */
    protected function requirePermission(string $permission)
    {
        if (!$this->hasPermission($permission)) {
            return redirect()->back()->with('error', 'Você não tem permissão para acessar esta página.');
        }
    }
    
    /**
     * Redirect if user is not root
     */
    protected function requireRoot()
    {
        if (!$this->isRoot()) {
            return redirect()->back()->with('error', 'Apenas o administrador principal pode aceder a esta função.');
        }
    }
    
    /**
     * Send JSON response
     */
    protected function respondWithJson($data, int $statusCode = 200)
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($data);
    }
    
    /**
     * Send success JSON response
     */
    protected function respondWithSuccess(string $message = 'Operação realizada com sucesso', $data = null)
    {
        return $this->respondWithJson([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }
    
    /**
     * Send error JSON response
     */
    protected function respondWithError(string $message = 'Erro ao realizar operação', int $statusCode = 400)
    {
        return $this->respondWithJson([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }
    
    /**
     * Get DataTable request data
     */
    protected function getDatatableRequest()
    {
        return [
            'draw' => $this->request->getPost('draw'),
            'start' => $this->request->getPost('start'),
            'length' => $this->request->getPost('length'),
            'search' => $this->request->getPost('search')['value'] ?? '',
            'order' => $this->request->getPost('order') ?? []
        ];
    }
}