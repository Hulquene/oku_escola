<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?= $certificate_title ?></title>
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
        .certificate {
            position: relative;
            min-height: 100%;
            border: 3px solid #1B2B4B;
            padding: 2cm;
        }
        .watermark {
            position: absolute;
            top: 30%;
            left: 20%;
            opacity: 0.1;
            font-size: 80px;
            transform: rotate(-30deg);
            color: #1B2B4B;
            z-index: -1;
        }
        .header {
            text-align: center;
            margin-bottom: 1.5cm;
            border-bottom: 2px solid #1B2B4B;
            padding-bottom: 0.5cm;
        }
        .header img {
            max-height: 100px;
            margin-bottom: 20px;
        }
        .republic {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #1B2B4B;
        }
        .school-name {
            font-size: 22px;
            font-weight: bold;
            color: #3B7FE8;
            margin-top: 5px;
        }
        .certificate-title {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: #1B2B4B;
            margin: 0.8cm 0;
            text-transform: uppercase;
            border-top: 2px solid #1B2B4B;
            border-bottom: 2px solid #1B2B4B;
            padding: 15px 0;
            letter-spacing: 2px;
        }
        .content {
            font-size: 16px;
            margin: 1.5cm 0;
            text-align: center;
        }
        .content p {
            margin: 15px 0;
        }
        .student-name {
            font-size: 28px;
            font-weight: bold;
            color: #1B2B4B;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .completion-text {
            font-size: 18px;
            font-style: italic;
        }
        .completion-name {
            font-size: 22px;
            font-weight: bold;
            color: #3B7FE8;
            margin: 15px 0;
        }
        .details {
            margin: 30px 0;
            font-size: 16px;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .details td {
            padding: 8px;
            border-bottom: 1px solid #E2E8F4;
        }
        .details .label {
            font-weight: bold;
            width: 40%;
        }
        .disciplines-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .disciplines-table th {
            background: #1B2B4B;
            color: white;
            padding: 8px;
            font-size: 14px;
        }
        .disciplines-table td {
            padding: 6px;
            border-bottom: 1px solid #E2E8F4;
            text-align: center;
        }
        .signature {
            margin-top: 2cm;
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
        .certificate-number {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-bottom: 20px;
        }
        .average {
            font-size: 18px;
            font-weight: bold;
            color: #1B2B4B;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="certificate-number">
            <strong>Nº:</strong> <?= $certificate_number ?>
        </div>
        
        <div class="header">
            <?php if (!empty($school_logo)): ?>
                <img src="<?= $school_logo ?>" alt="Logo">
            <?php endif; ?>
            <div class="republic">REPÚBLICA DE ANGOLA</div>
            <div class="school-name"><?= $school_name ?></div>
        </div>
        
        <div class="certificate-title"><?= $certificate_title ?></div>
        
        <div class="content">
            <p>Certificamos que</p>
            <div class="student-name"><?= $student_name ?></div>
            <p>natural de <strong><?= $birth_place ?></strong>,</p>
            <p>com data de nascimento <strong><?= $birth_date ?></strong>,</p>
            <p>portador do Bilhete de Identidade nº <strong><?= $student_number ?></strong>,</p>
            
            <div class="completion-text"><?= $completion_text ?></div>
            <div class="completion-name"><?= $completion_name ?></div>
            
            <p>na turma <strong><?= $class_name ?></strong>,</p>
            <p>no ano letivo <strong><?= $academic_year ?></strong>,</p>
            
            <div class="average">com a Classificação Final de <?= $final_average ?> valores</div>
        </div>
        
        <?php if (!empty($disciplines)): ?>
        <div class="details">
            <h4 style="text-align: center;">Relação das Disciplinas e Classificações</h4>
            <table class="disciplines-table">
                <thead>
                    <tr>
                        <th>Disciplina</th>
                        <th>Classificação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($disciplines as $disc): ?>
                    <tr>
                        <td><?= $disc['discipline_name'] ?></td>
                        <td><?= number_format($disc['final_score'], 1) ?> valores</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <div class="signature">
            <div class="signature-box">
                <div class="signature-line">O Diretor</div>
                <div><strong><?= $director_name ?></strong></div>
                <div style="font-size: 12px;"><?= $director_title ?></div>
            </div>
            <div class="signature-box">
                <div class="signature-line">O Secretário</div>
                <div><strong><?= $secretary_name ?></strong></div>
                <div style="font-size: 12px;">Secretário</div>
            </div>
        </div>
        
        <div class="footer">
            <p>Luanda, <?= $completion_date ?></p>
            <p>Documento gerado eletronicamente pelo Sistema de Gestão Escolar</p>
        </div>
    </div>
</body>
</html>