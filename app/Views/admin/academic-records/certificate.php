<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Conclusão</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            background: #fff;
            margin: 0;
            padding: 40px;
        }
        .certificate {
            max-width: 900px;
            margin: 0 auto;
            border: 15px solid #0d6efd;
            padding: 40px;
            position: relative;
            background: #fff;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .certificate:before {
            content: '';
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 2px solid #0d6efd;
            pointer-events: none;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .school-name {
            font-size: 28px;
            font-weight: bold;
            color: #0d6efd;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }
        .certificate-title {
            font-size: 36px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            margin: 20px 0;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
        }
        .content {
            font-size: 18px;
            line-height: 2;
            margin: 40px 0;
            text-align: center;
        }
        .student-name {
            font-size: 32px;
            font-weight: bold;
            color: #0d6efd;
            margin: 20px 0;
        }
        .course-info {
            font-size: 20px;
            margin: 20px 0;
        }
        .details {
            margin: 30px 0;
            font-size: 16px;
        }
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .seal {
            position: absolute;
            bottom: 50px;
            right: 50px;
            width: 100px;
            height: 100px;
            border: 2px solid #0d6efd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #0d6efd;
            transform: rotate(-15deg);
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            <div class="school-name"><?= getSetting('school_name') ?></div>
            <div class="certificate-title">Certificado de Conclusão</div>
        </div>
        
        <div class="content">
            <p>Certificamos que</p>
            <div class="student-name"><?= $student->first_name ?> <?= $student->last_name ?></div>
            <p>com o número de matrícula <strong><?= $student->student_number ?></strong>,</p>
            <p>concluiu com êxito o ano letivo</p>
            <div class="course-info">
                <strong><?= $history->year_name ?></strong><br>
                na turma <strong><?= $history->class_name ?></strong> (<?= $history->level_name ?>)<br>
                <?php if ($history->course_name): ?>
                    Curso: <strong><?= $history->course_name ?></strong>
                <?php endif; ?>
            </div>
            
            <div class="details">
                <p>Com média final de <strong><?= number_format($history->final_average, 2) ?></strong> valores</p>
                <p>Obtendo o resultado: <strong><?= $history->final_status ?></strong></p>
            </div>
        </div>
        
        <div class="signature">
            <div>
                <div class="signature-line">Diretor Pedagógico</div>
            </div>
            <div>
                <div class="signature-line">Secretário Escolar</div>
            </div>
            <div>
                <div class="signature-line">Encarregado de Educação</div>
            </div>
        </div>
        
        <div class="footer">
            <p>Documento emitido em <?= date('d/m/Y') ?> pelo Sistema de Gestão Escolar</p>
            <p>Documento válido para todos os efeitos legais</p>
        </div>
        
        <div class="seal">SELO<br>OFICIAL</div>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>