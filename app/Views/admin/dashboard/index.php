<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div class="text-muted">
            <i class="fas fa-calendar"></i> <?= date('d/m/Y') ?> | 
            <i class="fas fa-clock"></i> <?= date('H:i') ?>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Statistics Cards - Primeira Linha -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(45deg, #4e73df, #224abe);">
            <i class="fas fa-user-graduate stat-icon"></i>
            <div class="stat-label">Total de Alunos</div>
            <div class="stat-value"><?= number_format($totalStudents) ?></div>
            <div class="stat-change">
                <i class="fas fa-male"></i> <?= $genderChart['male'] ?? 0 ?> Masculino | 
                <i class="fas fa-female"></i> <?= $genderChart['female'] ?? 0 ?> Feminino
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(45deg, #1cc88a, #169b6b);">
            <i class="fas fa-chalkboard-teacher stat-icon"></i>
            <div class="stat-label">Professores</div>
            <div class="stat-value"><?= number_format($totalTeachers) ?></div>
            <div class="stat-change">
                <i class="fas fa-clock"></i> <?= $examsToday ?? 0 ?> exames hoje
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(45deg, #36b9cc, #258391);">
            <i class="fas fa-users stat-icon"></i>
            <div class="stat-label">Funcionários</div>
            <div class="stat-value"><?= number_format($totalStaff) ?></div>
            <div class="stat-change">
                <i class="fas fa-briefcase"></i> Staff ativo
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(45deg, #f6c23e, #dda20a);">
            <i class="fas fa-file-signature stat-icon"></i>
            <div class="stat-label">Matrículas Ativas</div>
            <div class="stat-value"><?= number_format($activeEnrollments) ?></div>
            <div class="stat-change">
                <i class="fas fa-calendar"></i> <?= $currentYear->year_name ?? 'N/A' ?>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards - Segunda Linha -->
<div class="row mt-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(45deg, #e74a3b, #be2617);">
            <i class="fas fa-money-bill-wave stat-icon"></i>
            <div class="stat-label">Receita Mensal</div>
            <div class="stat-value"><?= number_format($currentMonthPayments, 2, ',', '.') ?> Kz</div>
            <div class="stat-change">
                <i class="fas fa-calendar-check"></i> Pagamentos do mês
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(45deg, #fd7e14, #dc3545);">
            <i class="fas fa-exclamation-triangle stat-icon"></i>
            <div class="stat-label">Pagamentos Vencidos</div>
            <div class="stat-value"><?= number_format($overduePayments) ?></div>
            <div class="stat-change">
                <i class="fas fa-clock"></i> Pendentes com atraso
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(45deg, #20c9a6, #12a184);">
            <i class="fas fa-user-check stat-icon"></i>
            <div class="stat-label">Presenças Hoje</div>
            <div class="stat-value"><?= number_format($attendanceToday) ?></div>
            <div class="stat-change">
                <i class="fas fa-chart-line"></i> Taxa: <?= $attendanceRate ?>%
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(45deg, #6610f2, #520dc2);">
            <i class="fas fa-pencil-alt stat-icon"></i>
            <div class="stat-label">Exames da Semana</div>
            <div class="stat-value"><?= $examsThisWeek ?></div>
            <div class="stat-change">
                <i class="fas fa-calendar"></i> <?= $examsToday ?> hoje
            </div>
        </div>
    </div>
</div>

<!-- Charts Row - Primeira Linha -->
<div class="row mt-4">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-chart-line text-primary"></i> Evolução de Matrículas (12 meses)</h6>
                <span class="badge bg-primary"><?= array_sum($enrollmentChart['counts']) ?> total</span>
            </div>
            <div class="card-body">
                <canvas id="enrollmentChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-chart-pie text-success"></i> Distribuição por Gênero</h6>
                <span class="badge bg-success"><?= $genderChart['male'] + $genderChart['female'] ?> alunos</span>
            </div>
            <div class="card-body">
                <canvas id="genderChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row - Segunda Linha -->
<div class="row mt-4">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-chart-bar text-warning"></i> Receitas vs Pendentes (6 meses)</h6>
                <span class="badge bg-success"><?= number_format($feeStats->paid_amount, 0, ',', '.') ?> Kz recebido</span>
            </div>
            <div class="card-body">
                <canvas id="feeChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-chart-area text-info"></i> Presenças (7 dias)</h6>
                <span class="badge bg-info">Taxa média: <?= $attendanceRate ?>%</span>
            </div>
            <div class="card-body">
                <canvas id="attendanceChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Performance Chart e Turmas por Turno -->
<div class="row mt-4">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-pie text-danger"></i> Desempenho Acadêmico</h6>
            </div>
            <div class="card-body">
                <canvas id="performanceChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-pie text-secondary"></i> Turmas por Turno</h6>
            </div>
            <div class="card-body">
                <canvas id="shiftChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Resumo Financeiro e Próximos Exames -->
<div class="row mt-4">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-coins text-warning"></i> Resumo Financeiro
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted">Total de Propinas</label>
                    <h3 class="text-primary"><?= number_format($feeStats->total_amount, 2, ',', '.') ?> Kz</h3>
                </div>
                
                <div class="progress mb-3" style="height: 30px;">
                    <div class="progress-bar bg-success" style="width: <?= $feeStats->paid_percentage ?>%">
                        Pago <?= $feeStats->paid_percentage ?>%
                    </div>
                    <div class="progress-bar bg-warning" style="width: <?= $feeStats->pending_percentage ?>%">
                        Pendente <?= $feeStats->pending_percentage ?>%
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-6">
                        <div class="text-center p-3 border rounded bg-light">
                            <div class="text-success mb-2">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                            <h5><?= number_format($feeStats->paid_amount, 2, ',', '.') ?> Kz</h5>
                            <small class="text-muted">Recebido</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 border rounded bg-light">
                            <div class="text-warning mb-2">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                            <h5><?= number_format($feeStats->pending_amount, 2, ',', '.') ?> Kz</h5>
                            <small class="text-muted">Pendente</small>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="<?= site_url('admin/fees/payments') ?>" class="btn btn-primary w-100">
                        <i class="fas fa-arrow-right"></i> Ver Detalhes Financeiros
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-calendar-alt text-info"></i> Próximos Exames
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (!empty($upcomingExams)): ?>
                        <?php foreach ($upcomingExams as $exam): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?= $exam->board_name ?> - <?= $exam->discipline_name ?></h6>
                                        <small class="text-muted">
                                            <i class="fas fa-school"></i> <?= $exam->class_name ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-<?= $exam->exam_date == date('Y-m-d') ? 'success' : 'primary' ?>">
                                        <?= date('d/m', strtotime($exam->exam_date)) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="fas fa-calendar-times fa-2x mb-2"></i>
                            <p>Nenhum exame agendado</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-footer">
                    <a href="<?= site_url('admin/exams') ?>" class="btn btn-info btn-sm w-100">
                        <i class="fas fa-calendar"></i> Ver Calendário de Exames
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-clock text-success"></i> Matrículas Recentes
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
                                            (<?= $enrollment->level_name ?>)
                                        </small>
                                    </div>
                                    <small class="text-muted">
                                        <?= date('d/m', strtotime($enrollment->created_at)) ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="fas fa-user-slash fa-2x mb-2"></i>
                            <p>Nenhuma matrícula recente</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-footer">
                    <a href="<?= site_url('admin/enrollments') ?>" class="btn btn-success btn-sm w-100">
                        <i class="fas fa-plus"></i> Nova Matrícula
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bolt text-warning"></i> Ações Rápidas
            </div>
            <div class="card-body">
                <div class="row text-center g-3">
                    <div class="col-md-2 col-6">
                        <a href="<?= site_url('admin/students/form-add') ?>" class="quick-action-card">
                            <i class="fas fa-user-plus fa-2x mb-2 text-primary"></i>
                            <h6>Novo Aluno</h6>
                            <small class="text-muted">Cadastrar aluno</small>
                        </a>
                    </div>
                    <div class="col-md-2 col-6">
                        <a href="<?= site_url('admin/teachers/form-add') ?>" class="quick-action-card">
                            <i class="fas fa-chalkboard-teacher fa-2x mb-2 text-success"></i>
                            <h6>Novo Professor</h6>
                            <small class="text-muted">Cadastrar professor</small>
                        </a>
                    </div>
                    <div class="col-md-2 col-6">
                        <a href="<?= site_url('admin/enrollments/form-add') ?>" class="quick-action-card">
                            <i class="fas fa-file-signature fa-2x mb-2 text-info"></i>
                            <h6>Nova Matrícula</h6>
                            <small class="text-muted">Matricular aluno</small>
                        </a>
                    </div>
                    <div class="col-md-2 col-6">
                        <a href="<?= site_url('admin/fees/payments/add') ?>" class="quick-action-card">
                            <i class="fas fa-money-bill-wave fa-2x mb-2 text-warning"></i>
                            <h6>Registrar Pagamento</h6>
                            <small class="text-muted">Receber propina</small>
                        </a>
                    </div>
                    <div class="col-md-2 col-6">
                        <a href="<?= site_url('admin/exams/form-add') ?>" class="quick-action-card">
                            <i class="fas fa-pencil-alt fa-2x mb-2 text-danger"></i>
                            <h6>Agendar Exame</h6>
                            <small class="text-muted">Criar exame</small>
                        </a>
                    </div>
                    <div class="col-md-2 col-6">
                        <a href="<?= site_url('admin/reports') ?>" class="quick-action-card">
                            <i class="fas fa-chart-bar fa-2x mb-2 text-secondary"></i>
                            <h6>Relatórios</h6>
                            <small class="text-muted">Gerar relatórios</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Gender Chart
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: ['Masculino', 'Feminino'],
            datasets: [{
                data: [<?= $genderChart['male'] ?>, <?= $genderChart['female'] ?>],
                backgroundColor: ['#4e73df', '#f6c23e'],
                hoverBackgroundColor: ['#224abe', '#dda20a']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
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

    // Attendance Chart
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(attendanceCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($attendanceChart['days']) ?>,
            datasets: [
                {
                    label: 'Presentes',
                    data: <?= json_encode($attendanceChart['present']) ?>,
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Ausentes',
                    data: <?= json_encode($attendanceChart['absent']) ?>,
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231, 74, 59, 0.1)',
                    tension: 0.3,
                    fill: true
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
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'pie',
        data: {
            labels: ['Excelente (17-20)', 'Bom (14-16)', 'Satisfatório (10-13)', 'Insuficiente (<10)'],
            datasets: [{
                data: [
                    <?= $performanceChart['excelente'] ?>, 
                    <?= $performanceChart['bom'] ?>, 
                    <?= $performanceChart['satisfatorio'] ?>, 
                    <?= $performanceChart['insuficiente'] ?>
                ],
                backgroundColor: ['#1cc88a', '#4e73df', '#f6c23e', '#e74a3b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Shift Chart
    const shiftCtx = document.getElementById('shiftChart').getContext('2d');
    new Chart(shiftCtx, {
        type: 'doughnut',
        data: {
            labels: ['Manhã', 'Tarde', 'Noite', 'Integral'],
            datasets: [{
                data: [
                    <?= $classesByShift['Manhã'] ?>, 
                    <?= $classesByShift['Tarde'] ?>, 
                    <?= $classesByShift['Noite'] ?>, 
                    <?= $classesByShift['Integral'] ?>
                ],
                backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>

<style>
.stat-card {
    border-radius: 10px;
    color: white;
    padding: 20px;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-icon {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 3rem;
    opacity: 0.3;
}

.stat-label {
    font-size: 0.9rem;
    text-transform: uppercase;
    opacity: 0.9;
    margin-bottom: 5px;
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 10px;
}

.stat-change {
    font-size: 0.8rem;
    opacity: 0.8;
    border-top: 1px solid rgba(255,255,255,0.2);
    padding-top: 8px;
}

.quick-action-card {
    display: block;
    padding: 15px 5px;
    border-radius: 10px;
    background: #f8f9fa;
    text-decoration: none;
    color: #333;
    transition: all 0.3s;
    height: 100%;
}

.quick-action-card:hover {
    background: #e9ecef;
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>
<?= $this->endSection() ?>