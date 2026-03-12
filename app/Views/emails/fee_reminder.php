<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<p>Prezado(a) <strong><?= $student_name ?? $name ?? '' ?></strong>,</p>

<div class="info-box" style="border-left-color: #E8A020;">
    <p><strong>⚠️ Aviso de Prazo Próximo</strong></p>
</div>

<p>Identificamos que a seguinte propina está próxima do vencimento:</p>

<table>
    <tr>
        <td><strong>Descrição:</strong></td>
        <td><?= $fee_description ?? 'Propina Mensal' ?></td>
    </tr>
    <tr>
        <td><strong>Data de Vencimento:</strong></td>
        <td><?= date('d/m/Y', strtotime($due_date ?? 'now')) ?></td>
    </tr>
    <tr>
        <td><strong>Valor:</strong></td>
        <td><strong><?= $amount ?? '0,00' ?> Kz</strong></td>
    </tr>
    <?php if (!empty($fine_amount) && $fine_amount > 0): ?>
    <tr>
        <td><strong>Multa por Atraso:</strong></td>
        <td><?= $fine_amount ?> Kz</td>
    </tr>
    <?php endif; ?>
</table>

<p>Por favor, regularize sua situação dentro do prazo para evitar multas e juros.</p>

<p>Em caso de dúvidas, entre em contato com a secretaria.</p>

<p>Atenciosamente,<br>
<strong>Financeiro <?= $school_name ?></strong></p>

<a href="<?= site_url('student/payments') ?>" class="button">Regularizar Pagamento</a>

<?= $this->endSection() ?>