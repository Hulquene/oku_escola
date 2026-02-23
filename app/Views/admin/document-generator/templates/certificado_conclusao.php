<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?= $titulo ?? 'Certificado de Conclusão' ?></title>
    <style>
        /* Reset e estilos base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            background: #fff;
            color: #2c3e50;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        /* Container do certificado com borda decorativa */
        .certificate-container {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            position: relative;
            background: white;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        /* Borda dourada decorativa */
        .certificate-border {
            border: 15px solid transparent;
            border-image: repeating-linear-gradient(45deg, #c5a47e, #e6d5b8, #c5a47e) 30 stretch;
            padding: 5px;
            background: white;
        }
        
        /* Conteúdo principal */
        .certificate-content {
            background: white;
            padding: 40px 50px;
            position: relative;
        }
        
        /* Marca d'água sutil */
        .certificate-content::before {
            content: "★";
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 120px;
            color: rgba(197, 164, 126, 0.1);
            font-family: serif;
            transform: rotate(15deg);
            pointer-events: none;
        }
        
        /* Cabeçalho com brasão/logotipo */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #c5a47e;
        }
        
        .logo {
            max-width: 100px;
            max-height: 100px;
            margin-bottom: 15px;
        }
        
        .republic {
            font-size: 20px;
            font-weight: normal;
            letter-spacing: 2px;
            color: #7f8c8d;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .ministry {
            font-size: 18px;
            font-weight: normal;
            color: #34495e;
            margin-bottom: 5px;
        }
        
        .school-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            margin-top: 10px;
            letter-spacing: 1px;
        }
        
        /* Título do certificado */
        .certificate-title {
            text-align: center;
            margin: 40px 0;
        }
        
        .certificate-title h1 {
            font-size: 42px;
            font-weight: bold;
            color: #c5a47e;
            text-transform: uppercase;
            letter-spacing: 5px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .certificate-title .subtitle {
            font-size: 18px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        
        /* Corpo do certificado */
        .body-text {
            text-align: center;
            margin: 40px 0;
            font-size: 18px;
            color: #34495e;
        }
        
        .body-text p {
            margin: 20px 0;
        }
        
        .recipient-name {
            font-size: 48px;
            font-weight: bold;
            color: #2c3e50;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 3px;
            border-bottom: 2px dotted #c5a47e;
            border-top: 2px dotted #c5a47e;
            padding: 15px 0;
        }
        
        .completion-text {
            font-style: italic;
            color: #5d6d7e;
        }
        
        .details {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin: 30px 0;
        }
        
        .details p {
            margin: 10px 0;
            font-size: 16px;
        }
        
        .details strong {
            color: #c5a47e;
            min-width: 150px;
            display: inline-block;
        }
        
        /* Selo dourado */
        .seal {
            text-align: center;
            margin: 30px 0;
        }
        
        .seal i {
            font-size: 60px;
            color: #c5a47e;
        }
        
        /* Assinaturas */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px dashed #c5a47e;
        }
        
        .signature-box {
            text-align: center;
            width: 40%;
        }
        
        .signature-line {
            width: 100%;
            border-top: 2px solid #2c3e50;
            margin: 30px 0 10px;
            position: relative;
        }
        
        .signature-line::before {
            content: "✍";
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 0 10px;
            color: #c5a47e;
            font-size: 20px;
        }
        
        .signature-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin-top: 5px;
        }
        
        .signature-subtitle {
            font-size: 14px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        /* Data e local */
        .date-place {
            text-align: right;
            margin-top: 30px;
            font-size: 16px;
            color: #34495e;
            font-style: italic;
        }
        
        /* Número do registro */
        .registration-number {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #95a5a6;
            border-top: 1px solid #ecf0f1;
            padding-top: 20px;
        }
        
        .registration-number strong {
            color: #c5a47e;
        }
        
        /* Rodapé */
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #bdc3c7;
        }
        
        /* Elementos decorativos */
        .decorative-line {
            height: 3px;
            background: linear-gradient(90deg, transparent, #c5a47e, transparent);
            margin: 30px 0;
        }
        
        .decorative-star {
            text-align: center;
            font-size: 24px;
            color: #c5a47e;
            margin: 20px 0;
        }
        
        .decorative-star span {
            margin: 0 10px;
        }
        
        /* Responsividade */
        @media print {
            body {
                padding: 0;
                background: white;
            }
            .certificate-container {
                box-shadow: none;
            }
        }
        
        @media (max-width: 768px) {
            .certificate-content {
                padding: 20px;
            }
            
            .recipient-name {
                font-size: 32px;
            }
            
            .certificate-title h1 {
                font-size: 28px;
            }
            
            .signatures {
                flex-direction: column;
                align-items: center;
            }
            
            .signature-box {
                width: 80%;
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <div class="certificate-content">
                
                <!-- Cabeçalho -->
                <div class="header">
                    <?php if (isset($logotipo) && file_exists($logotipo)): ?>
                        <img src="<?= $logotipo ?>" class="logo" alt="Logotipo da Escola">
                    <?php endif; ?>
                    
                    <div class="republic">REPÚBLICA DE ANGOLA</div>
                    <div class="ministry">MINISTÉRIO DA EDUCAÇÃO</div>
                    <div class="school-name"><?= $escola ?? 'ESCOLA NACIONAL' ?></div>
                </div>
                
                <!-- Título -->
                <div class="certificate-title">
                    <h1><?= $titulo ?? 'CERTIFICADO DE CONCLUSÃO' ?></h1>
                    <div class="subtitle">DO ENSINO SECUNDÁRIO</div>
                </div>
                
                <!-- Linha decorativa -->
                <div class="decorative-star">
                    <span>✧</span> <span>✦</span> <span>✧</span>
                </div>
                
                <!-- Corpo do texto -->
                <div class="body-text">
                    <p class="completion-text">Certificamos que</p>
                    
                    <div class="recipient-name">
                        <?= $aluno ?? 'NOME DO ALUNO' ?>
                    </div>
                    
                    <p>natural de <strong><?= $naturalidade ?? 'Luanda' ?></strong>,</p>
                    <p>portador do Bilhete de Identidade nº <strong><?= $bi ?? '00XXXXXLA000' ?></strong>,</p>
                    
                    <div class="details">
                        <p><strong>concluiu com êxito o</strong> <?= $classe ?? '12ª Classe' ?></p>
                        <p><strong>do curso de</strong> <?= $curso ?? 'Ciências Físicas e Biológicas' ?></p>
                        <p><strong>no ano letivo de</strong> <?= $ano_conclusao ?? date('Y') ?></p>
                        <p><strong>com classificação final de</strong> <?= $media_final ?? '14' ?> valores</p>
                    </div>
                    
                    <p class="completion-text">
                        E por estar conforme as exigências do Sistema Nacional de Educação,
                        é digno do presente Certificado de Conclusão.
                    </p>
                </div>
                
                <!-- Selo decorativo -->
                <div class="seal">
                    <i>⚜️</i>
                </div>
                
                <!-- Data e local -->
                <div class="date-place">
                    <?= $cidade ?? 'Luanda' ?>, <?= $data ?? date('d \d\e F \d\e Y') ?>
                </div>
                
                <!-- Assinaturas -->
                <div class="signatures">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-title">O Diretor</div>
                        <div class="signature-subtitle">(Assinatura e Carimbo)</div>
                    </div>
                    
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-title">O Secretário</div>
                        <div class="signature-subtitle">(Assinatura e Carimbo)</div>
                    </div>
                </div>
                
                <!-- Número de registro -->
                <div class="registration-number">
                    Registrado sob o nº <strong><?= $numero ?? 'CERT' . date('Ymd') . '0001' ?></strong>
                    no Livro de Registro de Certificados
                </div>
                
                <!-- Rodapé com data de emissão -->
                <div class="footer">
                    <span>Documento gerado eletronicamente em <?= date('d/m/Y H:i:s') ?></span>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>