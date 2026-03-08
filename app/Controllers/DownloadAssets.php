<?php
// app/Controllers/DownloadAssets.php
namespace App\Controllers;

class DownloadAssets extends BaseController
{
    public function index()
    {
        // Verificar se está em ambiente de desenvolvimento
        if (ENVIRONMENT !== 'development') {
            return redirect()->to('/admin/dashboard')->with('error', 'Este recurso só está disponível em ambiente de desenvolvimento');
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
            'bootstrap.min.css' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
            'dataTables.bootstrap5.min.css' => 'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css',
            'select2.min.css' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
            'select2-bootstrap-5-theme.min.css' => 'https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css',
            'toastr.min.css' => 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css',
            'sweetalert2.min.css' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
            'bootstrap-datepicker.min.css' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css',
            'bootstrap-timepicker.min.css' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css',
            'fullcalendar.min.css' => 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css',
            
            // JS
            'jquery.min.js' => 'https://code.jquery.com/jquery-3.7.0.min.js',
            'bootstrap.bundle.min.js' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
            'jquery.dataTables.min.js' => 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',
            'dataTables.bootstrap5.min.js' => 'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js',
            'jquery.mask.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js',
            'select2.min.js' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            'select2.pt-BR.js' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js',
            'toastr.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js',
            'sweetalert2.min.js' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11',
            'chart.min.js' => 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
            'bootstrap-datepicker.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js',
            'bootstrap-datepicker.pt-BR.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/locales/bootstrap-datepicker.pt-BR.min.js',
            'bootstrap-timepicker.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js',
            'fullcalendar.min.js' => 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js',
            'jquery.mCustomScrollbar.concat.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js',
            'inputmask.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/jquery.inputmask.min.js',
            'jquery.validate.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js',
            'messages_pt_BR.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/localization/messages_pt_BR.min.js',
            'highcharts.js' => 'https://code.highcharts.com/highcharts.js',
            'highcharts-exporting.js' => 'https://code.highcharts.com/modules/exporting.js',
            'highcharts-accessibility.js' => 'https://code.highcharts.com/modules/accessibility.js'
        ];
        
        // Criar diretórios se não existirem
        $baseDir = FCPATH . 'assets/';
        $cssDir = $baseDir . 'css/vendor/';
        $jsDir = $baseDir . 'js/vendor/';
        
        if (!is_dir($cssDir)) {
            mkdir($cssDir, 0777, true);
            echo "📁 Diretório CSS criado: $cssDir\n";
        }
        
        if (!is_dir($jsDir)) {
            mkdir($jsDir, 0777, true);
            echo "📁 Diretório JS criado: $jsDir\n";
        }
        
        $total = count($assets);
        $success = 0;
        $failed = 0;
        
        foreach ($assets as $file => $url) {
            $path = (strpos($file, '.css') !== false) ? $cssDir : $jsDir;
            $fullPath = $path . $file;
            
            echo "\n📥 Baixando $file... ";
            
            // Usar cURL se disponível, senão file_get_contents
            if (function_exists('curl_init')) {
                $ch = curl_init($url);
                $fp = fopen($fullPath, 'w');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
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
            } else {
                $options = [
                    'http' => [
                        'method' => 'GET',
                        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false
                    ]
                ];
                
                $context = stream_context_create($options);
                $content = @file_get_contents($url, false, $context);
                
                if ($content !== false) {
                    if (file_put_contents($fullPath, $content) !== false) {
                        echo "✅ OK! (" . number_format(strlen($content) / 1024, 1) . " KB)";
                        $success++;
                    } else {
                        echo "❌ Erro ao salvar arquivo";
                        $failed++;
                    }
                } else {
                    echo "❌ Erro ao baixar";
                    $failed++;
                }
            }
        }
        
        echo "\n\n========================================\n";
        echo "📊 RESUMO DO DOWNLOAD\n";
        echo "========================================\n";
        echo "✅ Sucesso: $success arquivos\n";
        echo "❌ Falhas: $failed arquivos\n";
        echo "📁 CSS: " . count(glob($cssDir . '*')) . " arquivos\n";
        echo "📁 JS: " . count(glob($jsDir . '*')) . " arquivos\n";
        echo "========================================\n\n";
        
        return ob_get_clean();
    }
}