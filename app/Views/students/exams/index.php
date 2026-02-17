<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Meus Exames</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Upcoming Exams -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <i class="fas fa-calendar-alt"></i> Próximos Exames
    </div>
    <div class="card-body">
        <?php if (!empty($upcomingExams)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Exame</th>
                            <th>Disciplina</th>
                            <th>Tipo</th>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Sala</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcomingExams as $exam): ?>
                            <tr>
                                <td><?= $exam->exam_name ?></td>
                                <td><?= $exam->discipline_name ?></td>
                                <td><span class="badge bg-info"><?= $exam->board_name ?></span></td>
                                <td><?= date('d/m/Y', strtotime($exam->exam_date)) ?></td>
                                <td><?= $exam->exam_time ? date('H:i', strtotime($exam->exam_time)) : '-' ?></td>
                                <td><?= $exam->exam_room ?: '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Nenhum exame agendado.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Past Exams -->
<div class="card">
    <div class="card-header bg-secondary text-white">
        <i class="fas fa-history"></i> Exames Realizados
    </div>
    <div class="card-body">
        <?php if (!empty($pastExams)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Exame</th>
                            <th>Disciplina</th>
                            <th>Data</th>
                            <th>Nota</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pastExams as $exam): ?>
                            <?php $result = $results[$exam->id] ?? null; ?>
                            <tr>
                                <td><?= $exam->exam_name ?></td>
                                <td><?= $exam->discipline_name ?></td>
                                <td><?= date('d/m/Y', strtotime($exam->exam_date)) ?></td>
                                <td class="fw-bold">
                                    <?= $result ? number_format($result->score, 1) : 'Pendente' ?>
                                </td>
                                <td>
                                    <?php if ($result): ?>
                                        <?php if ($result->score >= 10): ?>
                                            <span class="badge bg-success">Aprovado</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Reprovado</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Aguardando</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Nenhum exame realizado.</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>