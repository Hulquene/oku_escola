<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pré-visualização - <?= $request->request_number ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f0f2f5;
        }
        .document-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .document-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .document-content {
            padding: 30px;
            min-height: 297mm;
        }
        .info-bar {
            background: #f8f9fa;
            padding: 10px 20px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            gap: 20px;
            font-size: 14px;
        }
        .btn-actions {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-warning { background: #f39c12; color: white; }
        .badge-info { background: #3498db; color: white; }
        
        /* Estilos do documento */
        .document-paper {
            background: white;
            border: 1px solid #dee2e6;
        }
        .school-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .school-header h1 { font-size: 18px; margin: 0; }
        .school-header h2 { font-size: 16px; margin: 5px 0; }
        .school-header h3 { font-size: 14px; margin: 0; color: #666; }
        .document-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            text-decoration: underline;
            margin: 30px 0;
        }
        .document-content-text {
            line-height: 1.8;
            font-size: 14px;
        }
        .field-label { font-weight: bold; width: 150px; display: inline-block; }
        .field-value { border-bottom: 1px solid #999; min-width: 200px; display: inline-block; }
        .signature-area {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin: 30px 0 5px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="document-container">
        <div class="document-header">
            <h2><i class="fas fa-file-pdf me-2"></i> Pré-visualização</h2>
            <div>
                <span class="badge badge-info"><?= $request->request_number ?></span>
                <span class="badge badge-warning"><?= $request->document_type ?></span>
            </div>
        </div>
        
        <div class="info-bar">
            <div><strong>Solicitante:</strong> <?= $request->user_fullname ?></div>
            <div><strong>Tipo:</strong> <?= $request->user_type == 'student' ? 'Aluno' : 'Professor' ?></div>
            <div><strong>Formato:</strong> <?= ucfirst($request->format) ?></div>
            <div><strong>Quantidade:</strong> <?= $request->quantity ?></div>
        </div>
        
        <div class="document-content">
            <?php
            // Simular visualização do documento baseado no tipo
            $docCode = $request->document_code;
            $dataAtual = date('d/m/Y');
            $escola = session()->get('school_name') ?? 'ESCOLA ANGOLANA';
            $escolaCidade = session()->get('school_city') ?? 'Luanda';
            ?>
            
            <div class="document-paper">
                <div class="school-header">
                    <h1>REPÚBLICA DE ANGOLA</h1>
                    <h2>MINISTÉRIO DA EDUCAÇÃO</h2>
                    <h3><?= $escola ?></h3>
                    <hr style="margin: 20px 0;">
                </div>
                
                <?php if ($docCode == 'CERT_MATRICULA'): ?>
                    <div class="document-title">CERTIFICADO DE MATRÍCULA</div>
                    <div class="document-content-text">
                        <p style="text-align: center;"><strong>Nº <?= $request->request_number ?></strong></p>
                        
                        <p>Para os devidos efeitos se declara que o(a) aluno(a):</p>
                        
                        <p>
                            <span class="field-label">Nome:</span>
                            <span class="field-value"><?= $request->user_fullname ?></span>
                        </p>
                        <p>
                            <span class="field-label">Bilhete de Identidade Nº:</span>
                            <span class="field-value"><?= $request->user_bi ?? '_______________' ?></span>
                        </p>
                        <p>
                            <span class="field-label">Classe:</span>
                            <span class="field-value">_______________________</span>
                        </p>
                        <p>
                            <span class="field-label">Ano Letivo:</span>
                            <span class="field-value">_______________________</span>
                        </p>
                        
                        <p style="margin-top: 40px;">E por ser verdade, passou-se o presente certificado que vai por mim assinado e carimbado.</p>
                    </div>
                    
                <?php elseif ($docCode == 'DECL_FREQUENCIA'): ?>
                    <div class="document-title">DECLARAÇÃO DE FREQUÊNCIA</div>
                    <div class="document-content-text">
                        <p style="text-align: center;"><strong>Nº <?= $request->request_number ?></strong></p>
                        
                        <p>Declaro para os devidos fins que o(a) aluno(a) <strong><?= $request->user_fullname ?></strong>,</p>
                        
                        <p>frequenta regularmente o curso/ano/classe _______________________________, no período ____________,</p>
                        
                        <p>neste estabelecimento de ensino.</p>
                        
                        <p>A presente declaração é passada a pedido da interessada para fins de <strong><?= $request->purpose ?></strong>.</p>
                        
                        <p style="margin-top: 40px;">Por ser verdade, passou-se a presente declaração.</p>
                    </div>
                    
                <?php elseif ($docCode == 'HISTORICO_NOTAS'): ?>
                    <div class="document-title">HISTÓRICO DE NOTAS</div>
                    <div class="document-content-text">
                        <p><strong>Aluno:</strong> <?= $request->user_fullname ?></p>
                        
                        <table border="1" cellpadding="5" style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                            <thead>
                                <tr style="background: #f0f0f0;">
                                    <th>Ano Letivo</th>
                                    <th>Classe</th>
                                    <th>Disciplina</th>
                                    <th>1º Semestre</th>
                                    <th>2º Semestre</th>
                                    <th>Média Final</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2025</td>
                                    <td>10ª Classe</td>
                                    <td>Matemática</td>
                                    <td>14</td>
                                    <td>15</td>
                                    <td>15</td>
                                </tr>
                                <tr>
                                    <td>2025</td>
                                    <td>10ª Classe</td>
                                    <td>Português</td>
                                    <td>13</td>
                                    <td>14</td>
                                    <td>14</td>
                                </tr>
                                <tr>
                                    <td>2025</td>
                                    <td>10ª Classe</td>
                                    <td>Física</td>
                                    <td>12</td>
                                    <td>13</td>
                                    <td>13</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                <?php elseif ($docCode == 'CERT_CONCLUSAO'): ?>
                    <div class="document-title">CERTIFICADO DE CONCLUSÃO</div>
                    <div class="document-content-text">
                        <p style="text-align: center;"><strong>Nº <?= $request->request_number ?></strong></p>
                        
                        <p>Certificamos que o(a) aluno(a) <strong><?= $request->user_fullname ?></strong>,</p>
                        
                        <p>concluiu com aproveitamento o _______________________________, no ano letivo _______________.</p>
                        
                        <p>Obteve classificação final de _______________ valores.</p>
                    </div>
                    
                <?php elseif (strpos($docCode, 'DECL') !== false): ?>
                    <div class="document-title"><?= $request->document_type ?></div>
                    <div class="document-content-text">
                        <p style="text-align: center;"><strong>Nº <?= $request->request_number ?></strong></p>
                        
                        <p>Declara-se para os devidos efeitos que <strong><?= $request->user_fullname ?></strong>,</p>
                        
                        <p>______________________________________________________________</p>
                        
                        <p>A presente declaração é passada a pedido da interessada para fins de <strong><?= $request->purpose ?></strong>.</p>
                    </div>
                <?php endif; ?>
                
                <div class="signature-area">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div>O Secretário</div>
                    </div>
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div>O Diretor</div>
                    </div>
                </div>
                
                <div class="footer">
                    <?= $escolaCidade ?>, <?= $dataAtual ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="btn-actions">
        <button class="btn btn-primary" onclick="window.close()">
            <i class="fas fa-times me-2"></i> Fechar
        </button>
        <a href="<?= site_url('admin/document-generator/generate/' . $request->id) ?>" class="btn btn-success">
            <i class="fas fa-file-pdf me-2"></i> Gerar Documento
        </a>
    </div>
</body>
</html>