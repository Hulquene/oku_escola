<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - <?= $payment->payment_number ?></title>
    
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #000;
        }
        
        .header h3 {
            margin: 5px 0;
            font-weight: normal;
            color: #666;
        }
        
        .school-info {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .receipt-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 20px 0;
            padding: 10px;
            background: #f8f9fa;
        }
        
        .info-box {
            margin: 20px 0;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        
        .info-table td.label {
            width: 30%;
            font-weight: bold;
            background: #f8f9fa;
        }
        
        .amount-box {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px dashed #28a745;
        }
        
        .amount-box small {
            display: block;
            font-size: 12px;
            color: #666;
            font-weight: normal;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        
        .signature {
            margin-top: 40px;
        }
        
        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            margin-top: 40px;
        }
        
        .print-button {
            text-align: center;
            margin: 20px 0;
        }
        
        .print-button button {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button onclick="window.print()">
            <i class="fas fa-print"></i> Imprimir Recibo
        </button>
        <button onclick="window.close()">
            <i class="fas fa-times"></i> Fechar
        </button>
    </div>
    
    <div class="receipt">
        <div class="header">
            <h1><?= $school->name ?></h1>
            <h3><?= $school->address ?></h3>
            <h3>Tel: <?= $school->phone ?> | Email: <?= $school->email ?></h3>
            <h3>NIF: <?= $school->nif ?></h3>
        </div>
        
        <div class="receipt-title">
            RECIBO DE PAGAMENTO Nº <?= $payment->payment_number ?>
        </div>
        
        <div class="info-box">
            <table class="info-table">
                <tr>
                    <td class="label">Aluno:</td>
                    <td><?= $payment->first_name ?> <?= $payment->last_name ?></td>
                </tr>
                <tr>
                    <td class="label">Nº de Matrícula:</td>
                    <td><?= $payment->student_number ?></td>
                </tr>
                <tr>
                    <td class="label">Turma:</td>
                    <td><?= $payment->class_name ?> (<?= $payment->class_code ?>)</td>
                </tr>
                <tr>
                    <td class="label">Ano Letivo:</td>
                    <td><?= $payment->year_name ?></td>
                </tr>
            </table>
        </div>
        
        <div class="info-box">
            <table class="info-table">
                <tr>
                    <td class="label">Referência da Propina:</td>
                    <td><?= $payment->fee_reference ?></td>
                </tr>
                <tr>
                    <td class="label">Tipo de Taxa:</td>
                    <td><?= $payment->type_name ?> (<?= $payment->type_category ?>)</td>
                </tr>
                <tr>
                    <td class="label">Data de Vencimento:</td>
                    <td><?= date('d/m/Y', strtotime($payment->due_date)) ?></td>
                </tr>
            </table>
        </div>
        
        <div class="amount-box">
            <small>Valor Recebido</small>
            <?= number_format($payment->amount_paid, 2, ',', '.') ?> Kz
            <small><?= $payment->payment_method ?></small>
        </div>
        
        <div class="info-box">
            <table class="info-table">
                <tr>
                    <td class="label">Data do Pagamento:</td>
                    <td><?= date('d/m/Y', strtotime($payment->payment_date)) ?></td>
                </tr>
                <tr>
                    <td class="label">Método de Pagamento:</td>
                    <td><?= $payment->payment_method ?></td>
                </tr>
                <?php if ($payment->payment_reference): ?>
                <tr>
                    <td class="label">Referência:</td>
                    <td><?= $payment->payment_reference ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($payment->bank_name): ?>
                <tr>
                    <td class="label">Banco:</td>
                    <td><?= $payment->bank_name ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($payment->bank_account): ?>
                <tr>
                    <td class="label">Conta:</td>
                    <td><?= $payment->bank_account ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($payment->check_number): ?>
                <tr>
                    <td class="label">Nº do Cheque:</td>
                    <td><?= $payment->check_number ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        
        <?php if ($payment->observations): ?>
        <div class="info-box">
            <table class="info-table">
                <tr>
                    <td class="label">Observações:</td>
                    <td><?= nl2br($payment->observations) ?></td>
                </tr>
            </table>
        </div>
        <?php endif; ?>
        
        <div class="footer">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 50%;">
                        <div class="signature">
                            <div class="signature-line"></div>
                            <p>Recebido por: <?= session()->get('name') ?></p>
                        </div>
                    </td>
                    <td style="width: 50%; text-align: right;">
                        <div class="signature">
                            <div class="signature-line" style="margin-left: auto;"></div>
                            <p>Assinatura do Responsável</p>
                        </div>
                    </td>
                </tr>
            </table>
            
            <p style="text-align: center; margin-top: 30px; font-size: 10px; color: #666;">
                Este recibo é um documento oficial. Guarde-o para futuras referências.<br>
                Emitido em <?= date('d/m/Y H:i') ?>
            </p>
        </div>
    </div>
    
    <script>
    // Auto print
    // window.onload = function() { window.print(); }
    </script>
</body>
</html>