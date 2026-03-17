<?= $this->extend('guardians/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-star me-2" style="color: var(--warning);"></i>Notas do Aluno</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/students') ?>">Meus Alunos</a></li>
                    <li class="breadcrumb-item active">Notas</li>
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

<?php if (!empty($disciplines)): ?>
    <?php foreach ($disciplines as $discipline): ?>
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-book"></i>
                    <span><?= $discipline['discipline_name'] ?></span>
                </div>
                <span class="badge-ci primary">
                    Média: <?= number_format($discipline['average'], 1) ?>
                </span>
            </div>
            
            <div class="ci-card-body p0">
                <div class="table-responsive">
                    <table class="ci-table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Avaliação</th>
                                <th>Período</th>
                                <th class="center">Nota</th>
                                <th class="center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($discipline['grades'] as $grade): ?>
                                <?php
                                $statusClass = $grade['score'] >= 10 ? 'success' : ($grade['score'] >= 7 ? 'warning' : 'danger');
                                $statusText = $grade['score'] >= 10 ? 'Aprovado' : ($grade['score'] >= 7 ? 'Recurso' : 'Reprovado');
                                ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($grade['exam_date'])) ?></td>
                                    <td><?= $grade['board_name'] ?></td>
                                    <td><?= $grade['period_name'] ?></td>
                                    <td class="center">
                                        <span class="num-chip <?= $statusClass ?>">
                                            <?= number_format($grade['score'], 1) ?>
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
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-star"></i>
        <h5>Nenhuma nota encontrada</h5>
        <p class="text-muted">Este aluno ainda não possui notas registadas.</p>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>