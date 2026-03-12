<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<p>Olá <strong><?= $name ?? '' ?></strong>,</p>

<p>Seja bem-vindo ao <strong><?= $school_name ?></strong>! Estamos muito felizes em tê-lo(a) conosco.</p>

<div class="info-box">
    <p><strong>Seus dados de acesso:</strong></p>
    <p>Usuário: <?= $username ?? '' ?></p>
    <p>Senha temporária: <?= $password ?? '' ?></p>
</div>

<p>Por favor, acesse o sistema e altere sua senha no primeiro login.</p>

<p><strong>Links úteis:</strong></p>
<ul>
    <li><a href="<?= site_url('login') ?>">Acessar o Sistema</a></li>
    <li><a href="<?= site_url('recuperar-senha') ?>">Recuperar Senha</a></li>
</ul>

<p>Em caso de dúvidas, entre em contato conosco através do email <?= setting('school_email') ?>.</p>

<p>Atenciosamente,<br>
<strong>Equipe <?= $school_name ?></strong></p>

<?php if (!empty($button_text) && !empty($button_url)): ?>
<a href="<?= $button_url ?>" class="button"><?= $button_text ?></a>
<?php endif; ?>

<?= $this->endSection() ?>