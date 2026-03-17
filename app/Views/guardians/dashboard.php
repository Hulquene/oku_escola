<?= $this->extend('guardians/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Stats Cards -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <div class="stat-label">Total de Alunos</div>
            <div class="stat-value"><?= $total_students ?></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-bell"></i>
        </div>
        <div>
            <div class="stat-label">Notificações</div>
            <div class="stat-value"><?= $unread_notifications ?></div>
            <div class="stat-sub">não lidas</div>
        </div>
    </div>
</div>

<!-- Lista de Alunos -->
<?php if (!empty($students)): ?>
    <div class="row g-4 mt-2">
        <?php foreach ($students as $student): ?>
            <div class="col-md-6 col-lg-4">
                <div class="ci-card">
                    <div class="ci-card-header" style="background: var(--primary);">
                        <div class="ci-card-title" style="color: #fff;">
                            <i class="fas fa-user-graduate me-2"></i>
                            <span><?= $student['first_name'] ?> <?= $student['last_name'] ?></span>
                        </div>
                        <span class="badge-ci light"><?= $student['student_number'] ?></span>
                    </div>
                    
                    <div class="ci-card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Turma:</span>
                                <span class="fw-semibold"><?= $student['class_name'] ?? 'Não matriculado' ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Turno:</span>
                                <span class="fw-semibold"><?= $student['class_shift'] ?? '-' ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Presenças:</span>
                                <span class="fw-semibold"><?= $student['attendance_percentage'] ?>%</span>
                            </div>
                        </div>
                        
                        <!-- Próximos Exames -->
                        <?php if (!empty($student['upcoming_exams'])): ?>
                            <div class="mt-3">
                                <h6 class="fw-semibold mb-2">
                                    <i class="fas fa-calendar-alt text-warning me-1"></i>
                                    Próximos Exames
                                </h6>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($student['upcoming_exams'] as $exam): ?>
                                        <div class="list-group-item px-0 py-2">
                                            <div class="d-flex justify-content-between">
                                                <span class="small"><?= $exam['discipline_name'] ?></span>
                                                <span class="badge-ci info">
                                                    <?= date('d/m', strtotime($exam['exam_date'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Últimas Notas -->
                        <?php if (!empty($student['recent_grades'])): ?>
                            <div class="mt-3">
                                <h6 class="fw-semibold mb-2">
                                    <i class="fas fa-star text-success me-1"></i>
                                    Últimas Notas
                                </h6>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($student['recent_grades'] as $grade): ?>
                                        <div class="list-group-item px-0 py-2">
                                            <div class="d-flex justify-content-between">
                                                <span class="small"><?= $grade['discipline_name'] ?></span>
                                                <span class="num-chip <?= $grade['score'] >= 10 ? 'success' : 'danger' ?>">
                                                    <?= $grade['score'] ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="ci-card-footer">
                        <a href="<?= site_url('guardians/students/view/' . $student['student_id']) ?>" 
                           class="btn-ci outline w-100">
                            <i class="fas fa-eye me-1"></i> Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-users-slash"></i>
        <h5>Nenhum aluno associado</h5>
        <p class="text-muted">Você ainda não tem alunos associados ao seu perfil.</p>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>