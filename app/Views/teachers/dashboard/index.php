<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Início</a></li>
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
        <div class="card bg-light">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <?php if (session()->get('photo')): ?>
                        <img src="<?= base_url('uploads/users/' . session()->get('photo')) ?>" 
                             class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center text-white me-3"
                             style="width: 60px; height: 60px; font-size: 1.5rem;">
                            <?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h4 class="mb-1">Bem-vindo(a), <?= session()->get('name') ?>!</h4>
                        <p class="mb-0 text-muted">
                            <i class="fas fa-calendar"></i> <?= date('d/m/Y') ?> | 
                            <i class="fas fa-clock"></i> <?= date('H:i') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <i class="fas fa-school stat-icon"></i>
            <div class="stat-label">Minhas Turmas</div>
            <div class="stat-value"><?= count($myClasses) ?></div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(45deg, #17a2b8, #138496);">
            <i class="fas fa-pencil-alt stat-icon"></i>
            <div class="stat-label">Total Alunos</div>
            <div class="stat-value">
                <?php
                $totalStudents = 0;
                foreach ($myClasses as $class) {
                    $enrollmentModel = new \App\Models\EnrollmentModel();
                    $totalStudents += $enrollmentModel
                        ->where('class_id', $class->class_id)
                        ->where('status', 'Ativo')
                        ->countAllResults();
                }
                echo $totalStudents;
                ?>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(45deg, #ffc107, #e0a800);">
            <i class="fas fa-calendar-alt stat-icon"></i>
            <div class="stat-label">Próximos Exames</div>
            <div class="stat-value"><?= count($upcomingExams) ?></div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(45deg, #dc3545, #c82333);">
            <i class="fas fa-user-check stat-icon"></i>
            <div class="stat-label">Presenças Hoje</div>
            <div class="stat-value"><?= $todayAttendance ?></div>
        </div>
    </div>
</div>

<!-- Minhas Turmas -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-school"></i> Minhas Turmas e Disciplinas
            </div>
            <div class="card-body">
                <?php if (!empty($myClasses)): ?>
                    <div class="row">
                        <?php foreach ($myClasses as $class): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><?= $class->class_name ?></h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-2">
                                            <i class="fas fa-book text-success"></i> 
                                            <strong>Disciplina:</strong> <?= $class->discipline_name ?>
                                        </p>
                                        <p class="mb-2">
                                            <i class="fas fa-clock text-success"></i> 
                                            <strong>Turno:</strong> <?= $class->class_shift ?>
                                        </p>
                                        <p class="mb-2">
                                            <i class="fas fa-door-open text-success"></i> 
                                            <strong>Sala:</strong> <?= $class->class_room ?: 'Não definida' ?>
                                        </p>
                                        <p class="mb-0">
                                            <i class="fas fa-users text-success"></i> 
                                            <strong>Alunos:</strong> 
                                            <?php
                                            $enrollmentModel = new \App\Models\EnrollmentModel();
                                            echo $enrollmentModel
                                                ->where('class_id', $class->class_id)
                                                ->where('status', 'Ativo')
                                                ->countAllResults();
                                            ?>
                                        </p>
                                    </div>
                                    <div class="card-footer bg-white">
                                        <a href="<?= site_url('teachers/classes/students/' . $class->id) ?>" 
                                           class="btn btn-sm btn-success w-100">
                                            <i class="fas fa-users"></i> Ver Alunos
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Nenhuma turma atribuída.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Próximos Exames -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-calendar-alt"></i> Próximos Exames
            </div>
            <div class="card-body">
                <?php if (!empty($upcomingExams)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Exame</th>
                                    <th>Turma</th>
                                    <th>Disciplina</th>
                                    <th>Data</th>
                                    <th>Sala</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcomingExams as $exam): ?>
                                    <tr>
                                        <td><?= $exam->exam_name ?></td>
                                        <td><?= $exam->class_name ?></td>
                                        <td><?= $exam->discipline_name ?></td>
                                        <td><?= date('d/m/Y', strtotime($exam->exam_date)) ?></td>
                                        <td><?= $exam->exam_room ?: '-' ?></td>
                                        <td>
                                            <a href="<?= site_url('teachers/exams/grade/' . $exam->id) ?>" 
                                               class="btn btn-sm btn-success">
                                                <i class="fas fa-pencil-alt"></i> Lançar Notas
                                            </a>
                                        </td>
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
    </div>
</div>

<!-- Ações Rápidas -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bolt"></i> Ações Rápidas
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-2">
                        <a href="<?= site_url('teachers/attendance') ?>" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-calendar-check fa-2x mb-2"></i><br>
                            Registrar Presenças
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?= site_url('teachers/exams/form-add') ?>" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                            Novo Exame
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?= site_url('teachers/grades') ?>" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-star fa-2x mb-2"></i><br>
                            Lançar Notas
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?= site_url('teachers/classes') ?>" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-users fa-2x mb-2"></i><br>
                            Ver Turmas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>