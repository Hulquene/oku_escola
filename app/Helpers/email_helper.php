<?php
// app/Helpers/email_helper.php

use App\Models\SettingsModel;

if (!function_exists('send_email')) {
    /**
     * Send email using configured settings
     * 
     * @param string|array $to Recipient email(s)
     * @param string $subject Email subject
     * @param string $template Template name
     * @param array $data Data for template
     * @param array $options Additional options (attachments, cc, bcc, etc)
     * @return array Result with success, message, and debug info
     */
    function send_email($to, $subject, $template, $data = [], $options = [])
    {
        // Load settings model
        $settingsModel = new SettingsModel();
        $settings = $settingsModel->getAll();
        
        // Get email configuration
        $emailConfig = get_email_config($settings);
        
        // Initialize email service
        $email = \Config\Services::email();
        $email->initialize($emailConfig);
        
        // Set sender
        $from = $settings['email_from'] ?? 'noreply@escola.ao';
        $fromName = $settings['email_from_name'] ?? 'Sistema Escolar';
        $email->setFrom($from, $fromName);
        
        // Set recipient(s)
        if (is_array($to)) {
            $email->setTo($to);
        } else {
            $email->setTo($to);
        }
        
        // Set CC if provided
        if (!empty($options['cc'])) {
            $email->setCC($options['cc']);
        }
        
        // Set BCC if provided
        if (!empty($options['bcc'])) {
            $email->setBCC($options['bcc']);
        }
        
        // Set Reply-To if provided
        if (!empty($options['reply_to'])) {
            $email->setReplyTo($options['reply_to']);
        }
        
        // Set subject
        $email->setSubject($subject);
        
        // Get email template
        $templateData = array_merge($data, [
            'settings' => $settings,
            'school_name' => $settings['school_name'] ?? 'Sistema Escolar',
            'school_logo' => school_logo_url(),
            'current_year' => date('Y')
        ]);
        
        $message = render_email_template($template, $templateData);
        $email->setMessage($message);
        
        // Set alternative message (plain text)
        if (!empty($options['alt_message'])) {
            $email->setAltMessage($options['alt_message']);
        } else {
            // Generate plain text version
            $email->setAltMessage(strip_tags($message));
        }
        
        // Add attachments
        if (!empty($options['attachments'])) {
            foreach ($options['attachments'] as $attachment) {
                if (is_array($attachment)) {
                    $email->attach($attachment['path'], $attachment['name'] ?? '');
                } else {
                    $email->attach($attachment);
                }
            }
        }
        
        // Set mail type
        $email->setMailType('html');
        
        // Send email
        $result = [
            'success' => false,
            'message' => '',
            'debug' => []
        ];
        
        if ($email->send()) {
            $result['success'] = true;
            $result['message'] = 'Email enviado com sucesso';
        } else {
            $result['message'] = 'Falha ao enviar email';
            $result['debug'] = $email->printDebugger(['headers']);
        }
        
        return $result;
    }
}

if (!function_exists('get_email_config')) {
    /**
     * Get email configuration based on settings
     * 
     * @param array $settings Settings array
     * @return array Email configuration
     */
    function get_email_config($settings)
    {
        $config = [
            'mailType' => 'html',
            'charset' => 'utf-8',
            'wordWrap' => true,
            'validate' => true
        ];
        
        $protocol = $settings['email_protocol'] ?? 'smtp';
        
        switch ($protocol) {
            case 'smtp':
                $config['protocol'] = 'smtp';
                $config['SMTPHost'] = $settings['email_smtp_host'] ?? '';
                $config['SMTPPort'] = $settings['email_smtp_port'] ?? 587;
                $config['SMTPUser'] = $settings['email_smtp_user'] ?? '';
                $config['SMTPPass'] = $settings['email_smtp_pass'] ?? '';
                $config['SMTPCrypto'] = $settings['email_smtp_crypto'] ?? 'tls';
                $config['SMTPTimeout'] = $settings['email_smtp_timeout'] ?? 30;
                break;
                
            case 'sendmail':
                $config['protocol'] = 'sendmail';
                $config['mailPath'] = $settings['email_sendmail_path'] ?? '/usr/sbin/sendmail';
                break;
                
            default:
                $config['protocol'] = 'mail';
                break;
        }
        
        return $config;
    }
}

if (!function_exists('render_email_template')) {
    /**
     * Render email template with data
     * 
     * @param string $template Template name
     * @param array $data Template data
     * @return string Rendered HTML
     */
    function render_email_template($template, $data = [])
    {
        $viewPath = 'App\Views\emails\\' . $template;
        
        // Try to load from views/emails folder
        $renderer = \Config\Services::renderer();
        
        try {
            return $renderer->setData($data)->render('emails/' . $template);
        } catch (\Exception $e) {
            // Fallback to default template if specific template not found
            return render_default_email_template($template, $data);
        }
    }
}

if (!function_exists('render_default_email_template')) {
    /**
     * Render default email template structure
     * 
     * @param string $template Template name
     * @param array $data Template data
     * @return string Rendered HTML
     */
    function render_default_email_template($template, $data = [])
    {
        $title = $data['title'] ?? 'Sistema Escolar';
        $content = $data['content'] ?? '';
        $buttonText = $data['button_text'] ?? '';
        $buttonUrl = $data['button_url'] ?? '';
        
        $schoolName = $data['school_name'] ?? 'Sistema Escolar';
        $schoolLogo = $data['school_logo'] ?? '';
        $currentYear = $data['current_year'] ?? date('Y');
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . $title . '</title>
            <style>
                body {
                    font-family: "Sora", Arial, sans-serif;
                    line-height: 1.6;
                    color: #1A2238;
                    margin: 0;
                    padding: 0;
                    background-color: #F5F7FC;
                }
                .email-wrapper {
                    max-width: 600px;
                    margin: 20px auto;
                    background-color: #FFFFFF;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 4px 16px rgba(27,43,75,.10);
                }
                .email-header {
                    background: linear-gradient(135deg, #1B2B4B 0%, #243761 100%);
                    padding: 30px 20px;
                    text-align: center;
                }
                .email-header img {
                    max-width: 150px;
                    max-height: 60px;
                    margin-bottom: 15px;
                }
                .email-header h1 {
                    color: #FFFFFF;
                    margin: 10px 0 0;
                    font-size: 24px;
                    font-weight: 600;
                }
                .email-body {
                    padding: 30px;
                }
                .email-footer {
                    background-color: #F5F7FC;
                    padding: 20px;
                    text-align: center;
                    border-top: 1px solid #E2E8F4;
                }
                .email-footer p {
                    color: #6B7A99;
                    font-size: 14px;
                    margin: 5px 0;
                }
                .button {
                    display: inline-block;
                    padding: 12px 30px;
                    background-color: #3B7FE8;
                    color: #FFFFFF;
                    text-decoration: none;
                    border-radius: 8px;
                    font-weight: 600;
                    margin: 20px 0;
                    transition: background-color 0.3s;
                }
                .button:hover {
                    background-color: #2C6FD4;
                }
                .info-box {
                    background-color: #F5F7FC;
                    border-left: 4px solid #3B7FE8;
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 8px;
                }
                .info-box p {
                    margin: 5px 0;
                    color: #1A2238;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }
                th {
                    background-color: #1B2B4B;
                    color: #FFFFFF;
                    padding: 10px;
                    text-align: left;
                }
                td {
                    padding: 10px;
                    border-bottom: 1px solid #E2E8F4;
                }
                .alert-success {
                    background-color: #E8F5E9;
                    border: 1px solid #16A87D;
                    color: #0E7A5A;
                    padding: 15px;
                    border-radius: 8px;
                    margin: 20px 0;
                }
                .alert-warning {
                    background-color: #FFF3E0;
                    border: 1px solid #E8A020;
                    color: #8A5A00;
                    padding: 15px;
                    border-radius: 8px;
                    margin: 20px 0;
                }
                .alert-danger {
                    background-color: #FFEBEE;
                    border: 1px solid #E84646;
                    color: #B03030;
                    padding: 15px;
                    border-radius: 8px;
                    margin: 20px 0;
                }
                @media only screen and (max-width: 600px) {
                    .email-wrapper {
                        margin: 10px;
                    }
                    .email-body {
                        padding: 20px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="email-wrapper">
                <div class="email-header">
                    ' . ($schoolLogo ? '<img src="' . $schoolLogo . '" alt="' . $schoolName . '">' : '') . '
                    <h1>' . $title . '</h1>
                </div>
                <div class="email-body">
                    ' . $content . '
                    
                    ' . ($buttonText && $buttonUrl ? '<a href="' . $buttonUrl . '" class="button">' . $buttonText . '</a>' : '') . '
                </div>
                <div class="email-footer">
                    <p>&copy; ' . $currentYear . ' ' . $schoolName . '. Todos os direitos reservados.</p>
                    <p>Este é um email automático, por favor não responda.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}