<?= $this->extend('guardians/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-user-graduate me-2"></i><?= $student['first_name'] ?> <?= $student['last_name'] ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/students') ?>">Meus Alunos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('guardians/students') ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row g-4">
    <div class="col-md-4">
        <!-- Card do Aluno -->
        <div class="ci-card text-center">
            <div class="ci-card-body">
                <div class="avatar-circle" style="width: 100px; height: 100px; font-size: 2.5rem; margin: 0 auto 1rem; background: var(--accent);">
                    <?= strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)) ?>
                </div>
                <h4 class="fw-semibold mb-1"><?= $student['first_name'] ?> <?= $student['last_name'] ?></h4>
                <p class="text-muted small"><?= $student['student_number'] ?></p>
                
                <?php if ($student['class_name']): ?>
                    <div class="mt-3">
                        <span class="badge-ci info"><?= $student['class_name'] ?></span>
                        <span class="badge-ci secondary"><?= $student['class_shift'] ?></span>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="ci-card-footer bg-light">
                <div class="d-flex justify-content-center gap-2">
                    <a href="<?= site_url('guardians/students/grades/' . $student['id']) ?>" class="btn-ci outline btn-sm">
                        <i class="fas fa-star"></i> Notas
                    </a>
                    <a href="<?= site_url('guardians/students/attendance/' . $student['id']) ?>" class="btn-ci outline btn-sm">
                        <i class="fas fa-calendar-check"></i> Presenças
                    </a>
                    <a href="<?= site_url('guardians/students/exams/' . $student['id']) ?>" class="btn-ci outline btn-sm">
                        <i class="fas fa-file-alt"></i> Exames
                    </a>
                    <a href="<?= site_url('guardians/students/fees/' . $student['id']) ?>" class="btn-ci outline btn-sm">
                        <i class="fas fa-money-bill"></i> Propinas
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Informações Pessoais -->
        <div class="ci-card mt-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-info-circle"></i>
                    <span>Informações Pessoais</span>
                </div>
            </div>
            <div class="ci-card-body">
                <table class="ci-table table-borderless">
                    <tr>
                        <td class="text-muted">Nº Processo:</td>
                        <td class="fw-semibold"><?= $student['student_number'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email:</td>
                        <td class="fw-semibold"><?= $student['email'] ?? '-' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Telefone:</td>
                        <td class="fw-semibold"><?= $student['phone'] ?? '-' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Data Nasc.:</td>
                        <td class="fw-semibold"><?= $student['birth_date'] ? date('d/m/Y', strtotime($student['birth_date'])) : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Gênero:</td>
                        <td class="fw-semibold"><?= $student['gender'] ?? '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Informações Acadêmicas -->
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Informações Acadêmicas</span>
                </div>
            </div>
            <div class="ci-card-body">
                <table class="ci-table table-borderless">
                    <tr>
                        <td class="text-muted">Turma:</td>
                        <td class="fw-semibold"><?= $student['class_name'] ?? 'Não matriculado' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Código:</td>
                        <td class="fw-semibold"><?= $student['class_code'] ?? '-' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Turno:</td>
                        <td class="fw-semibold"><?= $student['class_shift'] ?? '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Ações Rápidas -->
        <div class="row g-3 mt-2">
            <div class="col-md-6">
                <a href="<?= site_url('guardians/students/grades/' . $student['id']) ?>" class="text-decoration-none">
                    <div class="ci-card h-100">
                        <div class="ci-card-body text-center py-4">
                            <i class="fas fa-star fa-3x text-warning mb-3"></i>
                            <h5>Notas</h5>
                            <p class="text-muted small">Acompanhe o desempenho escolar</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-6">
                <a href="<?= site_url('guardians/students/attendance/' . $student['id']) ?>" class="text-decoration-none">
                    <div class="ci-card h-100">
                        <div class="ci-card-body text-center py-4">
                            <i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
                            <h5>Presenças</h5>
                            <p class="text-muted small">Controle de frequência escolar</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-6">
                <a href="<?= site_url('guardians/students/exams/' . $student['id']) ?>" class="text-decoration-none">
                    <div class="ci-card h-100">
                        <div class="ci-card-body text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-info mb-3"></i>
                            <h5>Exames</h5>
                            <p class="text-muted small">Calendário e resultados de exames</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-6">
                <a href="<?= site_url('guardians/students/fees/' . $student['id']) ?>" class="text-decoration-none">
                    <div class="ci-card h-100">
                        <div class="ci-card-body text-center py-4">
                            <i class="fas fa-money-bill fa-3x text-primary mb-3"></i>
                            <h5>Propinas</h5>
                            <p class="text-muted small">Situação financeira e pagamentos</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>