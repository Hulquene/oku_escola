<?php
$title = 'Recuperação de Senha - ' . $school_name;
$content = '
    <p>Olá <strong>' . ($name ?? '') . '</strong>,</p>
    
    <p>Recebemos uma solicitação para redefinir a senha da sua conta no ' . $school_name . '.</p>
    
    <div class="alert-warning">
        <p>Se você não solicitou esta alteração, ignore este email.</p>
    </div>
    
    <p>Para redefinir sua senha, clique no botão abaixo:</p>
';

$buttonText = 'Redefinir Senha';
$buttonUrl = site_url('reset-password/' . ($token ?? ''));

include 'default_template.php';
?>

<?= $this->extend('emails/layout/default') ?>

<?= $this->section('content') ?>
<p>Olá <strong><?= $name ?? '' ?></strong>,</p>

<p>Recebemos uma solicitação para redefinir a senha da sua conta no <?= $school_name ?>.</p>

<div class="alert-warning">
    <p>Se você não solicitou esta alteração, ignore este email.</p>
</div>

<p>Para redefinir sua senha, clique no botão abaixo:</p>

<a href="<?= site_url('reset-password/' . ($token ?? '')) ?>" class="button">Redefinir Senha</a>

<p>Ou copie e cole este link no seu navegador:</p>
<p><?= site_url('reset-password/' . ($token ?? '')) ?></p>

<p>Este link expira em 24 horas.</p>

<p>Atenciosamente,<br>
<strong>Equipe <?= $school_name ?></strong></p>
<?= $this->endSection() ?>