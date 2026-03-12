<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<p>Prezado(a) <strong><?= $student_name ?? $name ?? '' ?></strong>,</p>

<p>Informamos que o pagamento foi processado com sucesso em nossa instituição.</p>

<table>
    <tr>
        <th colspan="2">Detalhes do Pagamento</th>
    </tr>
    <tr>
        <td><strong>Fatura:</strong></td>
        <td><?= $invoice_number ?? 'N/A' ?></td>
    </tr>
    <tr>
        <td><strong>Valor:</strong></td>
        <td><strong><?= $amount ?? '0,00' ?> Kz</strong></td>
    </tr>
    <tr>
        <td><strong>Data:</strong></td>
        <td><?= $payment_date ?? date('d/m/Y') ?></td>
    </tr>
    <tr>
        <td><strong>Forma de Pagamento:</strong></td>
        <td><?= $payment_method ?? 'N/A' ?></td>
    </tr>
    <tr>
        <td><strong>Status:</strong></td>
        <td><span style="color: #16A87D; font-weight: 600;">Confirmado</span></td>
    </tr>
</table>

<p>Agradecemos pela confiança e pontualidade.</p>

<p>Atenciosamente,<br>
<strong>Secretaria <?= $school_name ?></strong></p>

<?php if (!empty($button_text) && !empty($button_url)): ?>
<a href="<?= $button_url ?>" class="button"><?= $button_text ?></a>
<?php endif; ?>

<?= $this->endSection() ?>