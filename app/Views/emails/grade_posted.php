<?php
$title = 'Nota Lançada - ' . $school_name;
$content = '
    <p>Olá <strong>' . ($student_name ?? '') . '</strong>,</p>
    
    <p>Uma nova nota foi lançada em seu boletim.</p>
    
    <table>
        <tr>
            <th colspan="2">Detalhes da Avaliação</th>
        </tr>
        <tr>
            <td><strong>Disciplina:</strong></td>
            <td>' . ($discipline ?? '') . '</td>
        </tr>
        <tr>
            <td><strong>Tipo:</strong></td>
            <td>' . ($assessment_type ?? '') . '</td>
        </tr>
        <tr>
            <td><strong>Nota:</strong></td>
            <td><strong>' . ($grade ?? '') . '</strong></td>
        </tr>
        <tr>
            <td><strong>Data:</strong></td>
            <td>' . ($date ?? '') . '</td>
        </tr>
        <tr>
            <td><strong>Período:</strong></td>
            <td>' . ($semester ?? '') . '</td>
        </tr>
    </table>
    
    <p>Continue se dedicando aos estudos!</p>
';

$buttonText = 'Ver Boletim';
$buttonUrl = site_url('student/grades');

include 'default_template.php';
?>

<?= $this->extend('emails/layout/default') ?>

<?= $this->section('content') ?>
<p>Olá <strong><?= $student_name ?? '' ?></strong>,</p>

<p>Uma nova nota foi lançada em seu boletim.</p>

<table>
    <tr>
        <th colspan="2">Detalhes da Avaliação</th>
    </tr>
    <tr>
        <td><strong>Disciplina:</strong></td>
        <td><?= $discipline ?? '' ?></td>
    </tr>
    <tr>
        <td><strong>Tipo:</strong></td>
        <td><?= $assessment_type ?? '' ?></td>
    </tr>
    <tr>
        <td><strong>Nota:</strong></td>
        <td><strong><?= $grade ?? '' ?></strong></td>
    </tr>
    <tr>
        <td><strong>Data:</strong></td>
        <td><?= $date ?? '' ?></td>
    </tr>
    <tr>
        <td><strong>Período:</strong></td>
        <td><?= $semester ?? '' ?></td>
    </tr>
</table>

<p>Continue se dedicando aos estudos!</p>

<a href="<?= site_url('student/grades') ?>" class="button">Ver Boletim</a>
<?= $this->endSection() ?>