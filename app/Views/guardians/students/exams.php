<?= $this->extend('guardians/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-file-alt me-2" style="color: var(--info);"></i>Exames do Aluno</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/students') ?>">Meus Alunos</a></li>
                    <li class="breadcrumb-item active">Exames</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('guardians/students/view/' . $student['id']) ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <div class="mt-2">
        <span class="badge-ci info">
            <i class="fas fa-user-graduate me-1"></i> <?= $student['first_name'] ?> <?= $student['last_name'] ?> (<?= $student['student_number'] ?>)
        </span>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Próximos Exames -->
<div class="ci-card mb-4">
    <div class="ci-card-header" style="background: var(--warning);">
        <div class="ci-card-title" style="color: #fff;">
            <i class="fas fa-clock"></i>
            <span>Próximos Exames</span>
        </div>
        <span class="badge-ci light"><?= count($upcoming) ?> agendados</span>
    </div>
    
    <div class="ci-card-body p0">
        <?php if (!empty($upcoming)): ?>
            <div class="table-responsive">
                <table class="ci-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Disciplina</th>
                            <th>Tipo</th>
                            <th>Sala</th>
                            <th class="center">Duração</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcoming as $exam): ?>
                            <tr>
                                <td><?= $exam['formatted_date'] ?></td>
                                <td><?= $exam['formatted_time'] ?? '--:--' ?></td>
                                <td><?= $exam['discipline_name'] ?></td>
                                <td>
                                    <span class="badge-ci info"><?= $exam['board_type'] ?></span>
                                </td>
                                <td><?= $exam['exam_room'] ?? '-' ?></td>
                                <td class="center"><?= $exam['duration_minutes'] ?> min</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-check"></i>
                <p class="text-muted">Nenhum exame agendado.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Exames Realizados -->
<div class="ci-card">
    <div class="ci-card-header" style="background: var(--success);">
        <div class="ci-card-title" style="color: #fff;">
            <i class="fas fa-check-circle"></i>
            <span>Exames Realizados</span>
        </div>
        <span class="badge-ci light"><?= count($completed) ?> realizados</span>
    </div>
    
    <div class="ci-card-body p0">
        <?php if (!empty($completed)): ?>
            <div class="table-responsive">
                <table class="ci-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Disciplina</th>
                            <th>Tipo</th>
                            <th class="center">Nota</th>
                            <th class="center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($completed as $exam): ?>
                            <?php
                            $statusClass = $exam['score'] >= 10 ? 'success' : ($exam['score'] >= 7 ? 'warning' : 'danger');
                            $statusText = $exam['score'] >= 10 ? 'Aprovado' : ($exam['score'] >= 7 ? 'Recurso' : 'Reprovado');
                            ?>
                            <tr>
                                <td><?= $exam['formatted_date'] ?></td>
                                <td><?= $exam['discipline_name'] ?></td>
                                <td>
                                    <span class="badge-ci info"><?= $exam['board_type'] ?></span>
                                </td>
                                <td class="center">
                                    <span class="num-chip <?= $statusClass ?>">
                                        <?= number_format($exam['score'], 1) ?>
                                    </span>
                                </td>
                                <td class="center">
                                    <span class="badge-ci <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <p class="text-muted">Nenhum exame realizado ainda.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>