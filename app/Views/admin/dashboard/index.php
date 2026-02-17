<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Início</a></li>
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

<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(45deg, #4e73df, #224abe);">
            <i class="fas fa-user-graduate stat-icon"></i>
            <div class="stat-label">Total de Alunos</div>
            <div class="stat-value"><?= number_format($totalStudents) ?></div>
            <div class="stat-change">
                <i class="fas fa-arrow-up"></i> Ativos
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(45deg, #1cc88a, #169b6b);">
            <i class="fas fa-chalkboard-teacher stat-icon"></i>
            <div class="stat-label">Professores</div>
            <div class="stat-value"><?= number_format($totalTeachers) ?></div>
            <div class="stat-change">
                <i class="fas fa-user-check"></i> Ativos
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(45deg, #36b9cc, #258391);">
            <i class="fas fa-users stat-icon"></i>
            <div class="stat-label">Funcionários</div>
            <div class="stat-value"><?= number_format($totalStaff) ?></div>
            <div class="stat-change">
                <i class="fas fa-briefcase"></i> Staff
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(45deg, #f6c23e, #dda20a);">
            <i class="fas fa-file-signature stat-icon"></i>
            <div class="stat-label">Matrículas Ativas</div>
            <div class="stat-value"><?= number_format($activeEnrollments) ?></div>
            <div class="stat-change">
                <i class="fas fa-calendar"></i> Ano Atual
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-line"></i> Evolução de Matrículas
            </div>
            <div class="card-body">
                <canvas id="enrollmentChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-pie"></i> Receitas vs Pendentes
            </div>
            <div class="card-body">
                <canvas id="feeChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Financial Summary and Recent Enrollments -->
<div class="row">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-coins"></i> Resumo Financeiro
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label>Total de Propinas</label>
                    <h3><?= number_format($feeStats->total_amount, 2, ',', '.') ?> Kz</h3>
                </div>
                
                <div class="progress mb-3" style="height: 25px;">
                    <?php 
                    $paidPercentage = $feeStats->total_amount > 0 ? ($feeStats->paid_amount / $feeStats->total_amount) * 100 : 0;
                    $pendingPercentage = $feeStats->total_amount > 0 ? ($feeStats->pending_amount / $feeStats->total_amount) * 100 : 0;
                    ?>
                    <div class="progress-bar bg-success" style="width: <?= $paidPercentage ?>%">
                        Pago: <?= number_format($paidPercentage, 1) ?>%
                    </div>
                    <div class="progress-bar bg-warning" style="width: <?= $pendingPercentage ?>%">
                        Pendente: <?= number_format($pendingPercentage, 1) ?>%
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-6">
                        <div class="text-center p-3 border rounded">
                            <div class="text-success">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                            <h5><?= number_format($feeStats->paid_amount, 2, ',', '.') ?> Kz</h5>
                            <small class="text-muted">Recebido</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 border rounded">
                            <div class="text-warning">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                            <h5><?= number_format($feeStats->pending_amount, 2, ',', '.') ?> Kz</h5>
                            <small class="text-muted">Pendente</small>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="<?= site_url('admin/fees/payments') ?>" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-arrow-right"></i> Ver Detalhes
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-calendar-check"></i> Presenças Hoje
            </div>
            <div class="card-body text-center">
                <div class="display-1 mb-3"><?= number_format($attendanceToday) ?></div>
                <p class="text-muted">Alunos presentes hoje</p>
                
                <div class="row mt-4">
                    <div class="col-6">
                        <div class="text-center">
                            <span class="badge bg-success p-2">
                                <i class="fas fa-check"></i> Presentes
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <span class="badge bg-danger p-2">
                                <i class="fas fa-times"></i> Ausentes
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="<?= site_url('admin/students/attendance') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-calendar-alt"></i> Registrar Presenças
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-clock"></i> Matrículas Recentes
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (!empty($recentEnrollments)): ?>
                        <?php foreach ($recentEnrollments as $enrollment): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0"><?= $enrollment->first_name ?> <?= $enrollment->last_name ?></h6>
                                        <small class="text-muted">
                                            <i class="fas fa-school"></i> <?= $enrollment->class_name ?>
                                        </small>
                                    </div>
                                    <small class="text-muted">
                                        <?= date('d/m/Y', strtotime($enrollment->created_at)) ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item text-center text-muted">
                            Nenhuma matrícula recente
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-footer">
                    <a href="<?= site_url('admin/students/enrollments') ?>" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-plus"></i> Nova Matrícula
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bolt"></i> Ações Rápidas
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-2 col-6 mb-3">
                        <a href="<?= site_url('admin/students/form-add') ?>" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-user-plus fa-2x mb-2"></i><br>
                            Novo Aluno
                        </a>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <a href="<?= site_url('admin/teachers/form-add') ?>" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i><br>
                            Novo Professor
                        </a>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <a href="<?= site_url('admin/students/enrollments/form-add') ?>" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-file-signature fa-2x mb-2"></i><br>
                            Nova Matrícula
                        </a>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <a href="<?= site_url('admin/fees/payments') ?>" class="btn btn-outline-warning w-100 py-3">
                            <i class="fas fa-money-bill-wave fa-2x mb-2"></i><br>
                            Registrar Pagamento
                        </a>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <a href="<?= site_url('admin/exams/exams/form-add') ?>" class="btn btn-outline-danger w-100 py-3">
                            <i class="fas fa-pencil-alt fa-2x mb-2"></i><br>
                            Novo Exame
                        </a>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <a href="<?= site_url('admin/reports') ?>" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i><br>
                            Relatórios
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Enrollment Chart
const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
new Chart(enrollmentCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($enrollmentChart['months']) ?>,
        datasets: [{
            label: 'Matrículas',
            data: <?= json_encode($enrollmentChart['counts']) ?>,
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Fee Chart
const feeCtx = document.getElementById('feeChart').getContext('2d');
new Chart(feeCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($feeChart['months']) ?>,
        datasets: [
            {
                label: 'Recebido',
                data: <?= json_encode($feeChart['collected']) ?>,
                backgroundColor: '#1cc88a'
            },
            {
                label: 'Pendente',
                data: <?= json_encode($feeChart['pending']) ?>,
                backgroundColor: '#f6c23e'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('pt-AO') + ' Kz';
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.raw.toLocaleString('pt-AO') + ' Kz';
                    }
                }
            }
        }
    }
});
</script>
<?= $this->endSection() ?>