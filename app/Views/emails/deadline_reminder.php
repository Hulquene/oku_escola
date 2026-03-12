<?php
$title = 'Aviso de Prazo - ' . $school_name;
$content = '
    <div class="alert-warning">
        <p><strong>Atenção!</strong> Você tem prazos próximos do vencimento.</p>
    </div>
    
    <table>
        <tr>
            <th>Descrição</th>
            <th>Data de Vencimento</th>
            <th>Valor</th>
        </tr>
        ' . ($items ?? '') . '
    </table>
    
    <p>Por favor, regularize sua situação o quanto antes para evitar multas e juros.</p>
';

$buttonText = 'Regularizar Pendências';
$buttonUrl = site_url('student/payments');

include 'default_template.php';
?>

<?= $this->extend('emails/layout/default') ?>

<?= $this->section('content') ?>
<div class="alert-warning">
    <p><strong>Atenção!</strong> Você tem prazos próximos do vencimento.</p>
</div>

<table>
    <tr>
        <th>Descrição</th>
        <th>Data de Vencimento</th>
        <th>Valor</th>
    </tr>
    <?= $items ?? '' ?>
</table>

<p>Por favor, regularize sua situação o quanto antes para evitar multas e juros.</p>

<a href="<?= site_url('student/payments') ?>" class="button">Regularizar Pendências</a>
<?= $this->endSection() ?>