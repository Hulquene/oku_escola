<?= $this->extend('guardians/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-users me-2"></i>Meus Alunos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Meus Alunos</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="mt-2">
        <span class="badge-ci info">
            <i class="fas fa-users me-1"></i> <?= count($students) ?> aluno(s) associado(s)
        </span>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Lista de Alunos -->
<?php if (!empty($students)): ?>
    <div class="row g-4">
        <?php foreach ($students as $student): ?>
            <div class="col-md-6 col-lg-4">
                <div class="ci-card">
                    <div class="ci-card-header" style="background: var(--primary);">
                        <div class="ci-card-title" style="color: #fff;">
                            <i class="fas fa-user-graduate me-2"></i>
                            <span><?= $student['first_name'] ?> <?= $student['last_name'] ?></span>
                        </div>
                        <span class="badge-ci light">#<?= $student['student_number'] ?></span>
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
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="d-flex justify-content-around mt-3 pt-3 border-top">
                            <div class="text-center">
                                <div class="small text-muted">Notas</div>
                                <a href="<?= site_url('guardians/students/grades/' . $student['student_id']) ?>" 
                                   class="btn-ci outline btn-sm" title="Ver notas">
                                    <i class="fas fa-star text-warning"></i>
                                </a>
                            </div>
                            <div class="text-center">
                                <div class="small text-muted">Presenças</div>
                                <a href="<?= site_url('guardians/students/attendance/' . $student['student_id']) ?>" 
                                   class="btn-ci outline btn-sm" title="Ver presenças">
                                    <i class="fas fa-calendar-check text-success"></i>
                                </a>
                            </div>
                            <div class="text-center">
                                <div class="small text-muted">Exames</div>
                                <a href="<?= site_url('guardians/students/exams/' . $student['student_id']) ?>" 
                                   class="btn-ci outline btn-sm" title="Ver exames">
                                    <i class="fas fa-file-alt text-info"></i>
                                </a>
                            </div>
                            <div class="text-center">
                                <div class="small text-muted">Propinas</div>
                                <a href="<?= site_url('guardians/students/fees/' . $student['student_id']) ?>" 
                                   class="btn-ci outline btn-sm" title="Ver propinas">
                                    <i class="fas fa-money-bill text-primary"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ci-card-footer">
                        <a href="<?= site_url('guardians/students/view/' . $student['student_id']) ?>" 
                           class="btn-ci primary w-100">
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