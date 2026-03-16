<?php
// app/Controllers/DownloadAssets.php
namespace App\Controllers;

class DownloadAssets extends BaseController
{
    public function index()
    {
        if (ENVIRONMENT !== 'development') {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Este recurso só está disponível em ambiente de desenvolvimento');
        }
        
        $output = $this->downloadAssets();
        
        return view('admin/download_assets', ['output' => $output]);
    }
    
    private function downloadAssets()
    {
        ob_start();
        
        echo "<pre>\n";
        echo "========================================\n";
        echo "DOWNLOAD DOS ASSETS DO SISTEMA ESCOLAR\n";
        echo "========================================\n\n";
        
        $assets = [
            // CSS
            'bootstrap.min.css' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
            'datatables.min.css' => 'https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css',
            'buttons.dataTables.min.css' => 'https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css',
            'select2.min.css' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
            'select2-bootstrap-5-theme.min.css' => 'https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css',
            'toastr.min.css' => 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css',
            'sweetalert2.min.css' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
            'bootstrap-datepicker.min.css' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css',
            'bootstrap-timepicker.min.css' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css',
            'fullcalendar.min.css' => 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css',

            // JS
            'jquery.min.js' => 'https://code.jquery.com/jquery-3.7.1.min.js',
            'bootstrap.bundle.min.js' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
            'datatables.min.js' => 'https://cdn.datatables.net/2.3.7/js/dataTables.min.js',
            'dataTables.buttons.min.js' => 'https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js',
            'buttons.html5.min.js' => 'https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js',
            'buttons.print.min.js' => 'https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js',
            'jquery.mask.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js',
            'select2.min.js' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            'select2.pt-BR.js' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js',
            'toastr.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js',
            'sweetalert2.min.js' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js',
            'chart.min.js' => 'https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js',
            'bootstrap-datepicker.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js',
            'bootstrap-datepicker.pt-BR.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/locales/bootstrap-datepicker.pt-BR.min.js',
            'bootstrap-timepicker.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js',
            'fullcalendar.min.js' => 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js',
            'inputmask.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/jquery.inputmask.min.js',
            'jquery.validate.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js',
            'messages_pt_BR.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/localization/messages_pt_BR.min.js',
            'jszip.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js',
            'pdfmake.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js',
            'vfs_fonts.js' => 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js'
        ];
        
        // Criar diretórios
        $baseDir = FCPATH . 'assets/';
        $cssDir = $baseDir . 'css/vendor/';
        $jsDir = $baseDir . 'js/vendor/';
        
        foreach ([$cssDir, $jsDir] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
                echo "📁 Diretório criado: $dir\n";
            }
        }
        
        $total = count($assets);
        $success = 0;
        $failed = 0;
        
        foreach ($assets as $file => $url) {
            $path = (strpos($file, '.css') !== false) ? $cssDir : $jsDir;
            $fullPath = $path . $file;
            
            echo "\n📥 Baixando $file... ";
            
            // Usar cURL
            $ch = curl_init($url);
            $fp = fopen($fullPath, 'w');
            curl_setopt_array($ch, [
                CURLOPT_FILE => $fp,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            
            curl_exec($ch);
            
            if (curl_error($ch)) {
                echo "❌ Erro: " . curl_error($ch);
                $failed++;
            } else {
                $info = curl_getinfo($ch);
                echo "✅ OK! (" . number_format($info['size_download'] / 1024, 1) . " KB)";
                $success++;
            }
            
            curl_close($ch);
            fclose($fp);
        }
        
        echo "\n\n========================================\n";
        echo "📊 RESUMO\n";
        echo "========================================\n";
        echo "✅ Sucesso: $success arquivos\n";
        echo "❌ Falhas: $failed arquivos\n";
        echo "📁 CSS: " . count(glob($cssDir . '*')) . " arquivos\n";
        echo "📁 JS: " . count(glob($jsDir . '*')) . " arquivos\n";
        echo "========================================\n\n";
        
        return ob_get_clean();
    }
}