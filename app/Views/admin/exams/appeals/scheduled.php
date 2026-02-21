<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Exames de Recurso Agendados</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-check me-1"></i>
                <?= $semester->semester_name ?? '' ?>
            </p>
        </div>
        <div>
            <a href="<?= site_url('admin/exams/appeals') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/appeals') ?>">Recursos</a></li>
            <li class="breadcrumb-item active">Exames Agendados</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Períodos de Exame -->
<?php if (!empty($periods)): ?>
    <?php foreach ($periods as $period): ?>
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i>
                    <?= $period->period_name ?>
                    <small class="text-muted ms-3">
                        <?= date('d/m/Y', strtotime($period->start_date)) ?> a 
                        <?= date('d/m/Y', strtotime($period->end_date)) ?>
                    </small>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 py-3 ps-3">Data</th>
                                <th class="border-0 py-3">Turma</th>
                                <th class="border-0 py-3">Disciplina</th>
                                <th class="border-0 py-3 text-center">Alunos</th>
                                <th class="border-0 py-3 text-center">Presenças</th>
                                <th class="border-0 py-3 text-center">Notas</th>
                                <th class="border-0 py-3 text-center">Status</th>
                                <th class="border-0 py-3 text-center pe-3">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $periodExams = array_filter($exams, function($e) use ($period) {
                                return $e->exam_period_id == $period->id;
                            });
                            ?>
                            <?php if (!empty($periodExams)): ?>
                                <?php foreach ($periodExams as $exam): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <?= date('d/m/Y', strtotime($exam->exam_date)) ?>
                                            <br>
                                            <small class="text-muted"><?= $exam->exam_time ?></small>
                                        </td>
                                        <td class="fw-semibold"><?= $exam->class_name ?></td>
                                        <td><?= $exam->discipline_name ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-info"><?= $exam->total_students ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success"><?= $exam->results_count ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($exam->results_count > 0): ?>
                                                <span class="badge bg-warning">Registadas</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Pendente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $statusColors = [
                                                'Agendado' => 'secondary',
                                                'Realizado' => 'success',
                                                'Cancelado' => 'danger'
                                            ];
                                            $color = $statusColors[$exam->status] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $color ?> p-2">
                                                <?= $exam->status ?>
                                            </span>
                                        </td>
                                        <td class="text-center pe-3">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= site_url('admin/exams/appeals/exam-students/' . $exam->id) ?>" 
                                                   class="btn btn-outline-primary" title="Gerir alunos">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                                <a href="<?= site_url('admin/exams/schedules/attendance/' . $exam->id) ?>" 
                                                   class="btn btn-outline-success" title="Presenças">
                                                    <i class="fas fa-user-check"></i>
                                                </a>
                                                <a href="<?= site_url('admin/exams/schedules/results/' . $exam->id) ?>" 
                                                   class="btn btn-outline-warning" title="Notas">
                                                    <i class="fas fa-graduation-cap"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-3">
                                        Nenhum exame agendado neste período
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-calendar-times empty-state-icon"></i>
        <h5 class="empty-state-title">Nenhum exame de recurso agendado</h5>
        <p class="empty-state-text">
            Utilize a opção "Gerar Exames de Recurso" para criar exames automaticamente.
        </p>
        <a href="<?= site_url('admin/exams/appeals') ?>" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>
<?php endif; ?>

<style>
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: #fff;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.empty-state-icon {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1rem;
}
</style>

<?= $this->endSection() ?>