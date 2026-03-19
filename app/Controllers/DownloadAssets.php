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
            // =============================================
            // CSS FILES
            // =============================================
            
            // Bootstrap Core
            'bootstrap.min.css' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
            
            // DataTables Core e Temas
            'datatables.min.css' => 'https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css',
            'dataTables.bootstrap5.min.css' => 'https://cdn.datatables.net/2.3.7/css/dataTables.bootstrap5.min.css',
            
            // DataTables Buttons
            'buttons.dataTables.min.css' => 'https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css',
            'buttons.bootstrap5.min.css' => 'https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.min.css',
            
            // Select2
            'select2.min.css' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
            'select2-bootstrap-5-theme.min.css' => 'https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css',
            
            // Toastr
            'toastr.min.css' => 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css',
            
            // SweetAlert2
            'sweetalert2.min.css' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
            
            // Date/Time Pickers
            'bootstrap-datepicker.min.css' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css',
            'bootstrap-timepicker.min.css' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css',
            
            // FullCalendar
            'fullcalendar.min.css' => 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css',
            
            // =============================================
            // JS FILES
            // =============================================
            
            // jQuery Core
            'jquery.min.js' => 'https://code.jquery.com/jquery-3.7.1.min.js',
            
            // Bootstrap
            'bootstrap.bundle.min.js' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
            
            // DataTables Core
            'datatables.min.js' => 'https://cdn.datatables.net/2.3.7/js/dataTables.min.js',
            'dataTables.bootstrap5.min.js' => 'https://cdn.datatables.net/2.3.7/js/dataTables.bootstrap5.min.js',
            
            // DataTables Buttons Core
            'dataTables.buttons.min.js' => 'https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js',
            'buttons.bootstrap5.min.js' => 'https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.min.js',
            
            // DataTables Buttons Extensions
            'buttons.html5.min.js' => 'https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js',
            'buttons.print.min.js' => 'https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js',
            'buttons.colVis.min.js' => 'https://cdn.datatables.net/buttons/3.0.2/js/buttons.colVis.min.js',
            
            // DataTables Export Dependencies
            'jszip.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js',
            'pdfmake.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js',
            'vfs_fonts.js' => 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js',
            
            // jQuery Plugins
            'jquery.mask.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js',
            'inputmask.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/jquery.inputmask.min.js',
            
            // jQuery Validation
            'jquery.validate.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js',
            'messages_pt_BR.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/localization/messages_pt_BR.min.js',
            
            // Select2
            'select2.min.js' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            'select2.pt-BR.js' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js',
            
            // Date/Time Pickers
            'bootstrap-datepicker.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js',
            'bootstrap-datepicker.pt-BR.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/locales/bootstrap-datepicker.pt-BR.min.js',
            'bootstrap-timepicker.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js',
            
            // UI Components
            'toastr.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js',
            'sweetalert2.min.js' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js',
            
            // Charts
            'chart.min.js' => 'https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js',
            
            // Calendar
            'fullcalendar.min.js' => 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js',
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
        $failedFiles = [];
        
        foreach ($assets as $file => $url) {
            $path = (strpos($file, '.css') !== false) ? $cssDir : $jsDir;
            $fullPath = $path . $file;
            
            // Verificar se arquivo já existe
            if (file_exists($fullPath)) {
                echo "\n⏭️  Pulando $file (já existe)... ";
                $success++;
                continue;
            }
            
            echo "\n📥 Baixando $file... ";
            
            // Usar cURL com opções melhoradas
            $ch = curl_init($url);
            $fp = fopen($fullPath, 'w+');
            
            curl_setopt_array($ch, [
                CURLOPT_FILE => $fp,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
            ]);
            
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_error($ch) || $httpCode !== 200) {
                $error = curl_error($ch) ?: "HTTP $httpCode";
                echo "❌ Erro: " . $error;
                $failed++;
                $failedFiles[] = "$file ($error)";
                @unlink($fullPath); // Remove arquivo incompleto
            } else {
                $info = curl_getinfo($ch);
                $size = $info['size_download'] ?: filesize($fullPath);
                echo "✅ OK! (" . number_format($size / 1024, 1) . " KB)";
                $success++;
            }
            
            curl_close($ch);
            fclose($fp);
        }
        
        echo "\n\n========================================\n";
        echo "📊 RESUMO DO DOWNLOAD\n";
        echo "========================================\n";
        echo "📦 Total de assets: $total\n";
        echo "✅ Sucesso: $success arquivos\n";
        echo "❌ Falhas: $failed arquivos\n";
        
        if (!empty($failedFiles)) {
            echo "\n📋 Arquivos com falha:\n";
            foreach ($failedFiles as $failedFile) {
                echo "   • $failedFile\n";
            }
        }
        
        echo "\n📁 CSS Vendor: " . count(glob($cssDir . '*')) . " arquivos\n";
        echo "📁 JS Vendor: " . count(glob($jsDir . '*')) . " arquivos\n";
        echo "========================================\n\n";
        
        // Verificar arquivos críticos
        $criticalFiles = [
            'jquery.min.js',
            'bootstrap.bundle.min.js',
            'datatables.min.js',
            'dataTables.bootstrap5.min.js',
            'dataTables.buttons.min.js',
            'buttons.bootstrap5.min.js',
            'jszip.min.js',
            'pdfmake.min.js',
            'vfs_fonts.js'
        ];
        
        echo "\n🔍 VERIFICAÇÃO DE ARQUIVOS CRÍTICOS:\n";
        echo "========================================\n";
        
        $missingCritical = [];
        foreach ($criticalFiles as $criticalFile) {
            $path = (strpos($criticalFile, '.css') !== false) ? $cssDir : $jsDir;
            $fullPath = $path . $criticalFile;
            
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                echo "✅ $criticalFile - " . number_format($size / 1024, 1) . " KB\n";
            } else {
                echo "❌ $criticalFile - NÃO ENCONTRADO!\n";
                $missingCritical[] = $criticalFile;
            }
        }
        
        if (!empty($missingCritical)) {
            echo "\n⚠️  ATENÇÃO: Arquivos críticos faltando. Execute o download novamente.\n";
        } else {
            echo "\n🎉 Todos os arquivos críticos estão presentes!\n";
        }
        
        echo "========================================\n\n";
        
        return ob_get_clean();
    }
}