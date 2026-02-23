<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?= $titulo ?? 'Declaração de Aproveitamento' ?></title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 2cm;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 100px;
            max-height: 100px;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 10px 0 5px;
            text-transform: uppercase;
        }
        h2 {
            font-size: 16pt;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }
        h3 {
            font-size: 14pt;
            font-weight: bold;
            margin: 5px 0;
        }
        h4 {
            font-size: 13pt;
            font-weight: bold;
            margin: 20px 0 10px;
            text-align: center;
            text-decoration: underline;
        }
        hr {
            margin: 20px 0;
            border: 1px solid #000;
        }
        .content {
            text-align: justify;
            margin: 30px 0;
        }
        .assinaturas {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .assinatura {
            text-align: center;
            width: 45%;
        }
        .linha {
            border-top: 1px solid #000;
            margin: 40px 0 5px;
            width: 100%;
        }
        .data-local {
            margin-top: 30px;
            text-align: right;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 10pt;
            text-align: center;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="header">
        <?php if (isset($logotipo) && file_exists($logotipo)): ?>
            <img src="<?= $logotipo ?>" class="logo" alt="Logotipo">
        <?php endif; ?>
        <h1>REPÚBLICA DE ANGOLA</h1>
        <h2>MINISTÉRIO DA EDUCAÇÃO</h2>
        <h3><?= $escola ?? '___________________________' ?></h3>
    </div>

    <hr>

    <h4><?= $titulo ?? 'DECLARAÇÃO DE APROVEITAMENTO' ?></h4>

    <div class="content">
        <p>Declaro para os devidos fins que o(a) aluno(a) <strong><?= $aluno ?? '____________________' ?></strong>, 
        portador do Bilhete de Identidade nº <strong><?= $bi ?? '____________________' ?></strong>, 
        matriculado(a) na <strong><?= $classe ?? '____ª Classe' ?></strong>, 
        no período <strong><?= $turno ?? 'Manhã/Tarde/Noite' ?></strong>, 
        no ano letivo <strong><?= $ano_letivo ?? '____' ?></strong>, 
        obteve o seguinte aproveitamento no <strong><?= $periodo ?? '1º Semestre' ?></strong>:</p>

        <table>
            <thead>
                <tr>
                    <th>Disciplina</th>
                    <th>Classificação</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($disciplinas) && !empty($disciplinas)): ?>
                    <?php foreach ($disciplinas as $disc): ?>
                        <tr>
                            <td><?= $disc->discipline_name ?? $disc['discipline_name'] ?? 'Matemática' ?></td>
                            <td><?= $disc->final_score ?? $disc['final_score'] ?? '14.5' ?> valores</td>
                            <td><?= $disc->status ?? $disc['status'] ?? 'Aprovado' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td>Língua Portuguesa</td>
                        <td>14.5 valores</td>
                        <td>Aprovado</td>
                    </tr>
                    <tr>
                        <td>Matemática</td>
                        <td>16.0 valores</td>
                        <td>Aprovado</td>
                    </tr>
                    <tr>
                        <td>Ciências da Natureza</td>
                        <td>15.5 valores</td>
                        <td>Aprovado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <p><strong>Média Geral:</strong> <?= $media ?? '15.3' ?> valores</p>
        <p><strong>Total de Disciplinas:</strong> <?= $total_disciplinas ?? '6' ?></p>
        <p><strong>Aprovadas:</strong> <?= $aprovadas ?? '5' ?></p>
        <p><strong>Em Recurso:</strong> <?= $recurso ?? '1' ?></p>
        <p><strong>Status Final:</strong> <?= $status ?? 'Aprovado' ?></p>

        <?php if (isset($finalidade) && !empty($finalidade)): ?>
            <p>A presente declaração é passada a pedido do(a) interessado(a) para fins de <strong><?= $finalidade ?></strong>.</p>
        <?php endif; ?>
    </div>

    <div class="data-local">
        <p><?= $cidade ?? 'Luanda' ?>, <?= $data ?? date('d') ?> de <?= $mes ?? date('m') ?> de <?= $ano ?? date('Y') ?></p>
    </div>

    <div class="assinaturas">
        <div class="assinatura">
            <div class="linha"></div>
            <p><strong>O(A) Secretário(a)</strong></p>
        </div>
        <div class="assinatura">
            <div class="linha"></div>
            <p><strong>O(A) Diretor(a)</strong></p>
        </div>
    </div>

    <div class="footer">
        <p>Documento gerado eletronicamente em <?= date('d/m/Y H:i:s') ?> - Nº <?= $numero ?? 'DOC' . date('Ymd') . '001' ?></p>
    </div>
</body>
</html>