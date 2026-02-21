<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Pauta da Turma - <?= $class->class_name ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
        }
        .header h2 {
            font-size: 14px;
            margin: 5px 0;
            color: #666;
        }
        .school-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .info {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
        }
        .info table {
            width: 100%;
        }
        .info td {
            padding: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        td {
            text-align: center;
        }
        .student-name {
            text-align: left;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature div {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 30px;
            padding-top: 5px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name"><?= getSetting('school_name') ?></div>
        <h1>Pauta de Avaliações</h1>
        <h2><?= $class->class_name ?> • <?= $class->level_name ?> • <?= $class->year_name ?></h2>
        <h3><?= $semester->semester_name ?> (<?= $semester->semester_type ?>)</h3>
    </div>
    
    <div class="info">
        <table>
            <tr>
                <td width="150"><strong>Turma:</strong></td>
                <td width="300"><?= $class->class_name ?> (<?= $class->class_code ?>)</td>
                <td width="150"><strong>Ano Letivo:</strong></td>
                <td><?= $class->year_name ?></td>
            </tr>
            <tr>
                <td><strong>Nível:</strong></td>
                <td><?= $class->level_name ?></td>
                <td><strong>Data:</strong></td>
                <td><?= date('d/m/Y') ?></td>
            </tr>
        </table>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="40">Nº</th>
                <th width="80">Matrícula</th>
                <th width="200" class="student-name">Nome do Aluno</th>
                <?php foreach ($disciplines as $disc): ?>
                    <th><?= $disc->discipline_name ?></th>
                <?php endforeach; ?>
                <th width="70">Média</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= $student['student_number'] ?></td>
                    <td class="student-name"><?= $student['student_name'] ?></td>
                    <?php foreach ($disciplines as $disc): ?>
                        <td>
                            <?php if (isset($student['averages'][$disc->id])): ?>
                                <?= number_format($student['averages'][$disc->id], 1) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td>
                        <?php
                        $total = 0;
                        $count = 0;
                        foreach ($disciplines as $disc) {
                            if (isset($student['averages'][$disc->id])) {
                                $total += $student['averages'][$disc->id];
                                $count++;
                            }
                        }
                        echo $count > 0 ? number_format($total / $count, 2) : '-';
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="signature">
        <div>
            <div class="signature-line">Diretor Pedagógico</div>
        </div>
        <div>
            <div class="signature-line">Professor Titular</div>
        </div>
        <div>
            <div class="signature-line">Encarregado de Educação</div>
        </div>
    </div>
    
    <div class="footer">
        <p>Documento gerado em <?= date('d/m/Y H:i') ?> pelo Sistema de Gestão Escolar</p>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>