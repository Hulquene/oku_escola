<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Pauta - <?= $schedule->discipline_name ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 5px 0;
            font-weight: normal;
        }
        .info {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
            border: 1px solid #999;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .info-item {
            margin: 2px 0;
        }
        .info-item strong {
            display: inline-block;
            width: 100px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        th {
            background-color: #4472C4;
            color: white;
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
            font-weight: bold;
        }
        .sub-header {
            background-color: #D9E1F2;
            color: black;
            font-weight: bold;
        }
        td {
            border: 1px solid #333;
            padding: 4px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .approved {
            color: green;
            font-weight: bold;
        }
        .appeal {
            color: orange;
            font-weight: bold;
        }
        .failed {
            color: red;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }
        .signature {
            text-align: center;
        }
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .legend {
            margin-top: 20px;
            padding: 10px;
            border: 1px dashed #999;
            background-color: #f5f5f5;
            font-size: 9px;
        }
        .stats {
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #999;
            background-color: #e9f1f9;
        }
        .stats span {
            margin-right: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0.5cm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MINI PAUTA DE AVALIAÇÃO</h1>
        <h3><?= $schedule->discipline_name ?> (<?= $schedule->discipline_code ?>)</h3>
    </div>
    
    <div class="info">
        <div class="info-item"><strong>Curso:</strong> <?= $schedule->course_name ?? 'N/A' ?></div>
        <div class="info-item"><strong>Turma:</strong> <?= $schedule->class_name ?></div>
        <div class="info-item"><strong>Classe:</strong> <?= $schedule->level_name ?? 'N/A' ?></div>
        <div class="info-item"><strong>Ano Letivo:</strong> <?= $schedule->year_name ?></div>
        <div class="info-item"><strong>Período:</strong> <?= $schedule->period_name ?></div>
        <div class="info-item"><strong>Data Impressão:</strong> <?= $data_impressao ?></div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th rowspan="2" width="30">Nº</th>
                <th rowspan="2">Nome do Aluno</th>
                <th colspan="4" width="200">1º TRIMESTRE</th>
                <th colspan="4" width="200">2º TRIMESTRE</th>
                <th colspan="4" width="200">3º TRIMESTRE</th>
                <th rowspan="2" width="40">MDF</th>
                <th rowspan="2" width="80">Situação</th>
                <th rowspan="2" width="80">Nº Processo</th>
            </tr>
            <tr class="sub-header">
                <th>MAC</th>
                <th>NPP</th>
                <th>NPT</th>
                <th>MT</th>
                <th>MAC</th>
                <th>NPP</th>
                <th>NPT</th>
                <th>MT</th>
                <th>MAC</th>
                <th>NPP</th>
                <th>NPT</th>
                <th>MT</th>
            </tr>
        </thead>
        <tbody>
            <?php $counter = 1; ?>
            <?php foreach ($alunos as $aluno): ?>
                <?php $medias = $medias[$aluno->enrollment_id] ?? []; ?>
                <tr>
                    <td class="text-center"><?= $counter++ ?></td>
                    <td><?= $aluno->full_name ?? $aluno->first_name . ' ' . $aluno->last_name ?></td>
                    
                    <!-- 1º Trimestre -->
                    <td class="text-center"><?= $medias['trimestres'][1]['AC'] ?? '—' ?></td>
                    <td class="text-center"><?= $medias['trimestres'][1]['NPP'] ?? '—' ?></td>
                    <td class="text-center"><?= $medias['trimestres'][1]['NPT'] ?? '—' ?></td>
                    <td class="text-center"><strong><?= $medias['trimestres'][1]['MT'] ?? '—' ?></strong></td>
                    
                    <!-- 2º Trimestre -->
                    <td class="text-center"><?= $medias['trimestres'][2]['AC'] ?? '—' ?></td>
                    <td class="text-center"><?= $medias['trimestres'][2]['NPP'] ?? '—' ?></td>
                    <td class="text-center"><?= $medias['trimestres'][2]['NPT'] ?? '—' ?></td>
                    <td class="text-center"><strong><?= $medias['trimestres'][2]['MT'] ?? '—' ?></strong></td>
                    
                    <!-- 3º Trimestre -->
                    <td class="text-center"><?= $medias['trimestres'][3]['AC'] ?? '—' ?></td>
                    <td class="text-center"><?= $medias['trimestres'][3]['NPP'] ?? '—' ?></td>
                    <td class="text-center"><?= $medias['trimestres'][3]['NPT'] ?? '—' ?></td>
                    <td class="text-center"><strong><?= $medias['trimestres'][3]['MT'] ?? '—' ?></strong></td>
                    
                    <!-- MDF -->
                    <td class="text-center">
                        <?php if (($medias['MDF'] ?? '—') !== '—'): ?>
                            <strong class="<?= $medias['MDF'] >= 10 ? 'approved' : ($medias['MDF'] >= 7 ? 'appeal' : 'failed') ?>">
                                <?= $medias['MDF'] ?>
                            </strong>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    
                    <!-- Situação -->
                    <td class="text-center">
                        <?php if (isset($medias['situacao'])): ?>
                            <strong class="<?= $medias['situacaoClass'] == 'success' ? 'approved' : ($medias['situacaoClass'] == 'warning' ? 'appeal' : 'failed') ?>">
                                <?= $medias['situacao'] ?>
                            </strong>
                        <?php else: ?>
                            Pendente
                        <?php endif; ?>
                    </td>
                    
                    <!-- Nº Processo -->
                    <td class="text-center"><?= $aluno->student_number ?? '—' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Estatísticas -->
    <div class="stats">
        <strong>RESUMO DA TURMA:</strong><br>
        <span>Total de Alunos: <?= $estatisticas['totalAlunos'] ?></span>
        <span>Aprovados: <?= $estatisticas['aprovados'] ?></span>
        <span>Recurso: <?= $estatisticas['recurso'] ?></span>
        <span>Reprovados: <?= $estatisticas['reprovados'] ?></span>
        <span>Pendentes: <?= $estatisticas['pendentes'] ?></span><br>
        <span>Média da Turma: <?= $estatisticas['mediaTurma'] ?></span>
        <span>Taxa de Aprovação: <?= $estatisticas['taxaAprovacao'] ?>%</span>
    </div>
    
    <!-- Legenda -->
    <div class="legend">
        <strong>LEGENDA:</strong><br>
        MAC = Média das Avaliações Contínuas | NPP = Nota da Prova do Professor | NPT = Nota da Prova Trimestral<br>
        MT = Média Trimestral (MAC + NPP + NPT) / 3 | MDF = Média Final da Disciplina (MT1 + MT2 + MT3) / 3<br>
        <strong>Aprovado:</strong> MDF ≥ 10 | <strong>Recurso:</strong> MDF 7 - 9 | <strong>Reprovado:</strong> MDF < 7
    </div>
    
    <div class="footer">
        <div class="signature">
            <div class="signature-line">O Professor</div>
        </div>
        <div class="signature">
            <div class="signature-line">O Coordenador</div>
        </div>
        <div class="signature">
            <div class="signature-line">O Director</div>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">
            <i class="fas fa-print"></i> Imprimir / Salvar PDF
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
            <i class="fas fa-times"></i> Fechar
        </button>
    </div>
    
    <div style="text-align: center; margin-top: 10px; font-size: 8px; color: #666;">
        Documento gerado em <?= $data_impressao ?>
    </div>
</body>
</html>