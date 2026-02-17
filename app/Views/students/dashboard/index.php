<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?= session('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Welcome Card -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <?php if (session()->get('photo')): ?>
                        <img src="<?= base_url('uploads/users/' . session()->get('photo')) ?>" 
                             class="rounded-circle me-3 border border-white" style="width: 70px; height: 70px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width: 70px; height: 70px; color: #f5576c; font-size: 2rem;">
                            <?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h3 class="mb-1">Bem-vindo(a), <?= session()->get('first_name') ?>!</h3>
                        <p class="mb-0">
                            <i class="fas fa-id-card"></i> Matrícula: <?= session()->get('student_number') ?? 'N/A' ?> | 
                            <i class="fas fa-calendar"></i> <?= date('d/m/Y') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Current Enrollment Card -->
<?php if ($currentEnrollment): ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <i class="fas fa-graduation-cap"></i> Matrícula Atual
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Turma:</strong></p>
                        <h5><?= $currentEnrollment->class_name ?></h5>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Ano Letivo:</strong></p>
                        <h5><?= $currentEnrollment->year_name ?></h5>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Data Matrícula:</strong></p>
                        <h5><?= date('d/m/Y', strtotime($currentEnrollment->enrollment_date)) ?></h5>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Nº Matrícula:</strong></p>
                        <h5><?= $currentEnrollment->enrollment_number ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <i class="fas fa-star stat-icon"></i>
            <div class="stat-label">Média Geral</div>
            <div class="stat-value">
                <?php
                if (!empty($recentGrades)) {
                    $total = 0;
                    foreach ($recentGrades as $grade) {
                        $total += $grade->score;
                    }
                    echo number_format($total / count($recentGrades), 1);
                } else {
                    echo '-';
                }
                ?>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="fas fa-calendar-check stat-icon"></i>
            <div class="stat-label">Presenças</div>
            <div class="stat-value">
                <?php 
                if ($attendance && $attendance->total > 0) {
                    echo round(($attendance->present / $attendance->total) * 100, 1) . '%';
                } else {
                    echo '-';
                }
                ?>
            </div>
            <small><?= $attendance->present ?? 0 ?>/<?= $attendance->total ?? 0 ?> dias</small>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="fas fa-money-bill stat-icon"></i>
            <div class="stat-label">Propinas Pendentes</div>
            <div class="stat-value"><?= count($pendingFees ?? []) ?></div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
            <i class="fas fa-pencil-alt stat-icon"></i>
            <div class="stat-label">Total Avaliações</div>
            <div class="stat-value"><?= count($recentGrades ?? []) ?></div>
        </div>
    </div>
</div>

<!-- Recent Grades -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-star"></i> Últimas Notas
            </div>
            <div class="card-body">
                <?php if (!empty($recentGrades)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Disciplina</th>
                                    <th>Exame</th>
                                    <th>Nota</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentGrades as $grade): ?>
                                    <tr>
                                        <td><?= $grade->discipline_name ?></td>
                                        <td><?= $grade->exam_name ?></td>
                                        <td class="fw-bold"><?= number_format($grade->score, 1) ?></td>
                                        <td>
                                            <?php if ($grade->score >= 10): ?>
                                                <span class="badge bg-success">Aprovado</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Reprovado</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= site_url('students/grades') ?>" class="btn btn-sm btn-outline-primary">
                            Ver Todas as Notas
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Nenhuma nota registrada ainda.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Pending Fees -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-money-bill"></i> Propinas Pendentes
            </div>
            <div class="card-body">
                <?php if (!empty($pendingFees)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Referência</th>
                                    <th>Valor</th>
                                    <th>Vencimento</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingFees as $fee): ?>
                                    <tr>
                                        <td><?= $fee->reference_number ?></td>
                                        <td class="fw-bold"><?= number_format($fee->total_amount, 2, ',', '.') ?> Kz</td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($fee->due_date)) ?>
                                            <?php if ($fee->due_date < date('Y-m-d')): ?>
                                                <span class="badge bg-danger">Vencido</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $fee->status == 'Vencido' ? 'danger' : 'warning' ?>">
                                                <?= $fee->status ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= site_url('students/fees') ?>" class="btn btn-sm btn-outline-primary">
                            Ver Todas as Propinas
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Nenhuma propina pendente.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-link"></i> Acesso Rápido
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-2">
                        <a href="<?= site_url('students/grades/report-card') ?>" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-file-alt fa-2x mb-2"></i><br>
                            Boletim de Notas
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?= site_url('students/exams/schedule') ?>" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-calendar-alt fa-2x mb-2"></i><br>
                            Calendário de Exames
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?= site_url('students/attendance') ?>" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-calendar-check fa-2x mb-2"></i><br>
                            Minhas Presenças
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?= site_url('students/fees') ?>" class="btn btn-outline-warning w-100 py-3">
                            <i class="fas fa-money-bill fa-2x mb-2"></i><br>
                            Minhas Propinas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>