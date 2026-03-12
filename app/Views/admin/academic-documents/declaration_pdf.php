<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Declaração de Conclusão</title>
    <style>
        @page {
            margin: 2.5cm 2cm 2cm 2cm;
        }
        body {
            font-family: 'Times New Roman', serif;
            background: #fff;
            color: #1B2B4B;
            line-height: 1.6;
        }
        .declaration {
            position: relative;
            min-height: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 1.5cm;
            border-bottom: 2px solid #1B2B4B;
            padding-bottom: 0.5cm;
        }
        .header img {
            max-height: 80px;
            margin-bottom: 15px;
        }
        .republic {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #1B2B4B;
        }
        .school-name {
            font-size: 20px;
            font-weight: bold;
            color: #3B7FE8;
            margin-top: 5px;
        }
        .declaration-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #1B2B4B;
            margin: 0.8cm 0;
            text-transform: uppercase;
            border-bottom: 2px solid #1B2B4B;
            padding-bottom: 10px;
        }
        .content {
            font-size: 16px;
            margin: 1.5cm 0;
        }
        .declaration-text {
            text-align: justify;
            margin: 30px 0;
        }
        .student-info {
            margin: 25px 0;
            padding: 20px;
            background: #F5F7FC;
            border-radius: 8px;
        }
        .info-row {
            margin: 10px 0;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .info-value {
            display: inline-block;
        }
        .average {
            font-size: 18px;
            font-weight: bold;
            color: #1B2B4B;
            text-align: center;
            margin: 25px 0;
        }
        .signature {
            margin-top: 2.5cm;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 2px solid #000;
            margin-top: 40px;
            padding-top: 10px;
        }
        .footer {
            position: absolute;
            bottom: 1cm;
            left: 2cm;
            right: 2cm;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .declaration-number {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-bottom: 20px;
        }
        .watermark {
            position: absolute;
            top: 40%;
            left: 20%;
            opacity: 0.05;
            font-size: 60px;
            transform: rotate(-30deg);
            color: #1B2B4B;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="declaration">
        <div class="declaration-number">
            <strong>Nº:</strong> <?= $declaration_number ?>
        </div>
        
        <div class="watermark"><?= $school_name ?></div>
        
        <div class="header">
            <?php if (!empty($school_logo)): ?>
                <img src="<?= $school_logo ?>" alt="Logo">
            <?php endif; ?>
            <div class="republic">REPÚBLICA DE ANGOLA</div>
            <div class="school-name"><?= $school_name ?></div>
        </div>
        
        <div class="declaration-title">DECLARAÇÃO DE CONCLUSÃO</div>
        
        <div class="content">
            <div class="declaration-text">
                <p style="text-indent: 2cm;">
                    Para os devidos efeitos, declara-se que o(a) aluno(a) abaixo identificado(a) 
                    concluiu com aproveitamento o ano letivo <strong><?= $academic_year ?></strong>, 
            </div>
            
            <div class="student-info">
                <div class="info-row">
                    <span class="info-label">Nome:</span>
                    <span class="info-value"><?= $student_name ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nº de Aluno:</span>
                    <span class="info-value"><?= $student_number ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Data de Nascimento:</span>
                    <span class="info-value"><?= $birth_date ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Naturalidade:</span>
                    <span class="info-value"><?= $birth_place ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nacionalidade:</span>
                    <span class="info-value"><?= $nationality ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Curso:</span>
                    <span class="info-value"><?= $course_name ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Classe:</span>
                    <span class="info-value"><?= $level_name ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Turma:</span>
                    <span class="info-value"><?= $class_name ?></span>
                </div>
            </div>
            
            <div class="average">
                Classificação Final: <?= $final_average ?> valores
            </div>
            
            <div class="declaration-text">
                <p style="text-indent: 2cm;">
                    Por ser verdade e para que produza os seus efeitos legais, vai a presente 
                    declaração ser assinada e autenticada com o carimbo em uso nesta Instituição de Ensino.
                </p>
            </div>
        </div>
        
        <div class="signature">
            <div class="signature-box">
                <div class="signature-line">O Diretor</div>
                <div><strong><?= $director_name ?></strong></div>
                <div style="font-size: 12px;"><?= $director_title ?></div>
            </div>
            <div class="signature-box">
                <div class="signature-line">O Secretário</div>
                <div><strong>Secretário(a)</strong></div>
            </div>
        </div>
        
        <div class="footer">
            <p>Luanda, <?= $completion_date ?></p>
            <p>Documento gerado eletronicamente pelo Sistema de Gestão Escolar</p>
        </div>
    </div>
</body>
</html>