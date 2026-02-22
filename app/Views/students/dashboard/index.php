<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?? 'Dashboard do Aluno' ?></h1>
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

<?php if (session()->has('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> <?= session('warning') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Welcome Card -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <?php if ($student->photo ?? false): ?>
                        <img src="<?= base_url('uploads/students/' . $student->photo) ?>" 
                             class="rounded-circle me-3 border border-white" 
                             style="width: 70px; height: 70px; object-fit: cover;"
                             alt="Foto do aluno">
                    <?php elseif (session()->get('photo')): ?>
                        <img src="<?= base_url('uploads/users/' . session()->get('photo')) ?>" 
                             class="rounded-circle me-3 border border-white" 
                             style="width: 70px; height: 70px; object-fit: cover;"
                             alt="Foto do aluno">
                    <?php else: ?>
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width: 70px; height: 70px; color: #667eea; font-size: 2rem;">
                            <?= strtoupper(substr(session()->get('first_name') ?? 'A', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h3 class="mb-1">Bem-vindo(a), <?= session()->get('first_name') ?? 'Aluno' ?>!</h3>
                        <p class="mb-0">
                            <i class="fas fa-id-card"></i> 
                            Nº Aluno: <?= $student->student_number ?? 'N/A' ?> | 
                            <i class="fas fa-calendar"></i> <?= date('d/m/Y') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Current Enrollment Card - COM DADOS REAIS -->
<!-- Current Enrollment Card - COM DADOS REAIS -->
<?php if (!empty($enrollment)): ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <span><i class="fas fa-graduation-cap"></i> Matrícula Atual - Ano Letivo <?= $enrollment->year_name ?? date('Y') ?></span>
                <span class="badge bg-<?php 
                    switch($enrollment->status ?? '') {
                        case 'Ativo': echo 'success'; break;
                        case 'Pendente': echo 'warning'; break;
                        case 'Concluído': echo 'secondary'; break;
                        case 'Transferido': echo 'info'; break;
                        case 'Anulado': echo 'danger'; break;
                        default: echo 'light text-dark';
                    }
                ?> p-2">
                    <i class="fas fa-<?php 
                        switch($enrollment->status ?? '') {
                            case 'Ativo': echo 'check-circle'; break;
                            case 'Pendente': echo 'clock'; break;
                            case 'Concluído': echo 'flag-checkered'; break;
                            case 'Transferido': echo 'exchange-alt'; break;
                            case 'Anulado': echo 'times-circle'; break;
                            default: echo 'question-circle';
                        }
                    ?> me-1"></i>
                    <?= $enrollment->status ?? 'Não definido' ?>
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Turma:</strong></p>
                        <h5><?= $enrollment->class_name ?? 'Não definida' ?></h5>
                        <small class="text-muted">Cód: <?= $enrollment->class_code ?? 'N/A' ?></small>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Curso:</strong></p>
                        <h5>
                            <?php if (isset($enrollment->course_name) && $enrollment->course_name): ?>
                                <?= $enrollment->course_name ?>
                                <small class="text-white-50">(<?= $enrollment->course_code ?? '' ?>)</small>
                            <?php else: ?>
                                Ensino Geral
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Nível/Classe:</strong></p>
                        <h5><?= $enrollment->level_name ?? 'N/A' ?></h5>
                        <small class="text-muted"><?= $enrollment->education_level ?? '' ?></small>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Turno:</strong></p>
                        <h5><?= $enrollment->class_shift ?? 'N/A' ?></h5>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Data Matrícula:</strong></p>
                        <h6><?= isset($enrollment->enrollment_date) ? date('d/m/Y', strtotime($enrollment->enrollment_date)) : 'N/A' ?></h6>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Nº Matrícula:</strong></p>
                        <h6><?= $enrollment->enrollment_number ?? 'N/A' ?></h6>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Sala:</strong></p>
                        <h6><?= $enrollment->class_room ?? 'N/A' ?></h6>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Professor Responsável:</strong></p>
                        <h6><?= isset($enrollment->teacher_name) ? $enrollment->teacher_name : 'Não atribuído' ?></h6>
                    </div>
                </div>
                <?php if ($enrollment->status ?? '' == 'Pendente'): ?>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Sua matrícula está pendente de confirmação pela secretaria.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Média Geral</h6>
                        <h2 class="text-white mb-0"><?= $stats['average_grade'] ?? '-' ?></h2>
                        <small><?= isset($stats['total_grades']) ? $stats['total_grades'] . ' notas' : '' ?></small>
                    </div>
                    <i class="fas fa-star fa-2x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Presenças</h6>
                        <h2 class="text-white mb-0"><?= $stats['attendance_percentage'] ?? '0%' ?></h2>
                        <?php if (isset($attendance) && $attendance->total > 0): ?>
                            <small><?= $attendance->present ?? 0 ?>/<?= $attendance->total ?? 0 ?> dias</small>
                        <?php endif; ?>
                    </div>
                    <i class="fas fa-calendar-check fa-2x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Propinas Pendentes</h6>
                        <h2 class="text-white mb-0"><?= count($pendingFees ?? []) ?></h2>
                        <?php if (!empty($pendingFees)): ?>
                            <small>Total: <?= number_format(array_sum(array_column($pendingFees, 'total_amount')), 0, ',', '.') ?> Kz</small>
                        <?php endif; ?>
                    </div>
                    <i class="fas fa-money-bill fa-2x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Próximo Exame</h6>
                        <h5 class="text-white mb-0"><?= $stats['next_exam'] ?? '-' ?></h5>
                        <?php if (!empty($upcomingExams)): ?>
                            <small><?= date('d/m', strtotime($upcomingExams[0]->exam_date)) ?></small>
                        <?php endif; ?>
                    </div>
                    <i class="fas fa-pencil-alt fa-2x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Exams -->
<?php if (!empty($upcomingExams)): ?>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <i class="fas fa-clock"></i> Próximos Exames
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Disciplina</th>
                                <th>Data</th>
                                <th>Hora</th>
                                <th>Sala</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcomingExams as $exam): ?>
                                <tr>
                                    <td><strong><?= $exam->discipline_name ?></strong></td>
                                    <td><?= $exam->formatted_date ?? date('d/m/Y', strtotime($exam->exam_date)) ?></td>
                                    <td><?= $exam->formatted_time ?? ($exam->exam_time ? date('H:i', strtotime($exam->exam_time)) : '--:--') ?></td>
                                    <td><?= $exam->exam_room ?? 'N/D' ?></td>
                                    <td><span class="badge bg-info"><?= $exam->board_type ?? 'Normal' ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="<?= site_url('students/exams/schedule') ?>" class="btn btn-sm btn-outline-warning">
                        Ver Calendário Completo
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Recent Grades & Pending Fees -->
<div class="row mt-3">
    <!-- Recent Grades -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-star"></i> Últimas Notas
            </div>
            <div class="card-body">
                <?php if (!empty($recentGrades)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Disciplina</th>
                                    <th>Nota</th>
                                    <th>Avaliação</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentGrades as $grade): ?>
                                    <?php 
                                    $score = is_object($grade) ? $grade->score : ($grade['score'] ?? 0);
                                    $discipline = is_object($grade) ? ($grade->discipline_name ?? 'N/A') : ($grade['discipline_name'] ?? 'N/A');
                                    $board = is_object($grade) ? ($grade->board_type ?? 'Exame') : ($grade['board_type'] ?? 'Exame');
                                    $date = is_object($grade) ? ($grade->exam_date ?? null) : ($grade['exam_date'] ?? null);
                                    ?>
                                    <tr>
                                        <td><?= $discipline ?></td>
                                        <td class="fw-bold <?= $score >= 10 ? 'text-success' : 'text-danger' ?>">
                                            <?= number_format($score, 1, ',', '.') ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?= $board ?>
                                            </span>
                                        </td>
                                        <td><small><?= $date ? date('d/m', strtotime($date)) : '' ?></small></td>
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
                    <div class="text-center py-4">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhuma nota registrada ainda.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Pending Fees -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-warning text-white">
                <i class="fas fa-money-bill"></i> Propinas Pendentes
            </div>
            <div class="card-body">
                <?php if (!empty($pendingFees)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Descrição</th>
                                    <th>Vencimento</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingFees as $fee): ?>
                                    <?php 
                                    $dueDate = strtotime($fee->due_date);
                                    $isOverdue = $dueDate < time() && $fee->status == 'Pendente';
                                    $status = $isOverdue ? 'Vencido' : $fee->status;
                                    $statusClass = $isOverdue ? 'danger' : ($fee->status == 'Vencido' ? 'danger' : 'warning');
                                    ?>
                                    <tr>
                                        <td><?= $fee->type_name ?? $fee->description ?? 'Propina' ?></td>
                                        <td>
                                            <?= date('d/m/Y', $dueDate) ?>
                                            <?php if ($isOverdue): ?>
                                                <span class="badge bg-danger">Vencido</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold"><?= number_format($fee->total_amount ?? $fee->amount ?? 0, 2, ',', '.') ?> Kz</td>
                                        <td>
                                            <span class="badge bg-<?= $statusClass ?>">
                                                <?= $status ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= site_url('students/fees') ?>" class="btn btn-sm btn-outline-warning">
                            Ver Todas as Propinas
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="text-muted">Nenhuma propina pendente.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Financial Summary -->
<?php if (isset($financialSummary) && ($financialSummary->total_due > 0 || $financialSummary->total_paid > 0)): ?>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <i class="fas fa-chart-pie"></i> Resumo Financeiro
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="p-3">
                            <h6 class="text-muted">Total Devido</h6>
                            <h3 class="text-primary"><?= number_format($financialSummary->total_due, 2, ',', '.') ?> Kz</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border-start border-end">
                            <h6 class="text-muted">Total Pago</h6>
                            <h3 class="text-success"><?= number_format($financialSummary->total_paid, 2, ',', '.') ?> Kz</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <h6 class="text-muted">Saldo Pendente</h6>
                            <h3 class="<?= ($financialSummary->balance ?? 0) > 0 ? 'text-danger' : 'text-success' ?>">
                                <?= number_format($financialSummary->balance ?? 0, 2, ',', '.') ?> Kz
                            </h3>
                        </div>
                    </div>
                </div>
                <?php if (($financialSummary->balance ?? 0) > 0): ?>
                <div class="text-center mt-2">
                    <a href="<?= site_url('students/fees') ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-credit-card"></i> Regularizar Pagamentos
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Quick Links -->
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <i class="fas fa-link"></i> Acesso Rápido
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-3 col-6">
                        <a href="<?= site_url('students/grades') ?>" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-star fa-2x mb-2"></i><br>
                            <span class="d-none d-md-inline">Minhas Notas</span>
                            <span class="d-md-none">Notas</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="<?= site_url('students/exams/schedule') ?>" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-calendar-alt fa-2x mb-2"></i><br>
                            <span class="d-none d-md-inline">Calendário</span>
                            <span class="d-md-none">Exames</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="<?= site_url('students/attendance') ?>" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-calendar-check fa-2x mb-2"></i><br>
                            <span class="d-none d-md-inline">Presenças</span>
                            <span class="d-md-none">Presenças</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="<?= site_url('students/fees') ?>" class="btn btn-outline-warning w-100 py-3">
                            <i class="fas fa-money-bill fa-2x mb-2"></i><br>
                            <span class="d-none d-md-inline">Propinas</span>
                            <span class="d-md-none">Propinas</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mt-2">
                        <a href="<?= site_url('students/subjects') ?>" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-book fa-2x mb-2"></i><br>
                            <span class="d-none d-md-inline">Disciplinas</span>
                            <span class="d-md-none">Disciplinas</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mt-2">
                        <a href="<?= site_url('students/profile') ?>" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-user fa-2x mb-2"></i><br>
                            <span class="d-none d-md-inline">Meu Perfil</span>
                            <span class="d-md-none">Perfil</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mt-2">
                        <a href="<?= site_url('students/documents') ?>" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-file-alt fa-2x mb-2"></i><br>
                            <span class="d-none d-md-inline">Documentos</span>
                            <span class="d-md-none">Documentos</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mt-2">
                        <a href="<?= site_url('students/grades/report-card') ?>" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-file-pdf fa-2x mb-2"></i><br>
                            <span class="d-none d-md-inline">Boletim</span>
                            <span class="d-md-none">Boletim</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- If no enrollment -->
<?php if (empty($enrollment)): ?>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> 
            Você não possui uma matrícula ativa para o ano letivo atual.
            Entre em contato com a secretaria para regularizar sua situação.
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.stat-card {
    transition: transform 0.2s;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
</style>

<?= $this->endSection() ?>