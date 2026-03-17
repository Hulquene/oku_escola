<?php
// app/Controllers/guardians/Documents.php
namespace App\Controllers\guardians;

use App\Controllers\BaseController;

class Documents extends BaseController
{
    public function index()
    {
        $userId = currentUserId();
        
        $db = db_connect();
        
        $documents = $db->table('tbl_documents')
            ->where('user_id', $userId)
            ->where('user_type', 'guardian')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        $data['title'] = 'Meus Documentos';
        $data['documents'] = $documents;
        
        return view('guardians/documents/index', $data);
    }
    
    public function download($id)
    {
        $userId = currentUserId();
        
        $db = db_connect();
        
        $document = $db->table('tbl_documents')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->where('user_type', 'guardian')
            ->get()
            ->getRowArray();
        
        if (!$document) {
            return redirect()->back()->with('error', 'Documento não encontrado.');
        }
        
        $path = FCPATH . $document['file_path'];
        
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'Arquivo não encontrado.');
        }
        
        return $this->response->download($path, null);
    }
    
    public function view($id)
    {
        $userId = currentUserId();
        
        $db = db_connect();
        
        $document = $db->table('tbl_documents')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->where('user_type', 'guardian')
            ->get()
            ->getRowArray();
        
        if (!$document) {
            return redirect()->back()->with('error', 'Documento não encontrado.');
        }
        
        $data['title'] = 'Visualizar Documento';
        $data['document'] = $document;
        
        return view('guardians/documents/view', $data);
    }
}