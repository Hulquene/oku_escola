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
<?= view('admin/partials/alerts') ?>

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
            <i class="fas fa-users stat-icon"></i>
            <div class="stat-label">Total Alunos</div>
            <div class="stat-value"><?= $totalStudents ?? 0 ?></div>
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
            <div class="stat-value"><?= $todayAttendance ?? 0 ?></div>
        </div>
    </div>
</div>

<!-- Minhas Turmas e Disciplinas -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-school"></i> Minhas Turmas e Disciplinas
            </div>
            <div class="card-body">
                <?php if (!empty($myClasses)): ?>
                    <div class="row">
                        <?php 
                        $enrollmentModel = new \App\Models\EnrollmentModel();
                        $processedClasses = [];
                        
                        foreach ($myClasses as $class):
                            // Evitar duplicar a mesma turma no display
                            $classKey = $class->class_id;
                            if (!isset($processedClasses[$classKey])):
                                $processedClasses[$classKey] = true;
                                
                                // Contar alunos da turma
                                $studentCount = $enrollmentModel
                                    ->where('class_id', $class->class_id)
                                    ->where('status', 'Ativo')
                                    ->countAllResults();
                        ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><?= $class->class_name ?></h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-2">
                                            <i class="fas fa-clock text-success"></i> 
                                            <strong>Turno:</strong> <?= $class->class_shift ?>
                                        </p>
                                        <p class="mb-2">
                                            <i class="fas fa-door-open text-success"></i> 
                                            <strong>Sala:</strong> <?= $class->class_room ?: 'Não definida' ?>
                                        </p>
                                        <p class="mb-2">
                                            <i class="fas fa-graduation-cap text-success"></i> 
                                            <strong>Nível:</strong> <?= $class->level_name ?? 'N/A' ?>
                                        </p>
                                        <p class="mb-0">
                                            <i class="fas fa-users text-success"></i> 
                                            <strong>Alunos:</strong> <?= $studentCount ?>
                                        </p>
                                    </div>
                                    <div class="card-footer bg-white">
                                        <div class="btn-group w-100">
                                            <a href="<?= site_url('teachers/classes/students/' . $class->class_id) ?>" 
                                               class="btn btn-sm btn-success">
                                                <i class="fas fa-users"></i> Alunos
                                            </a>
                                            <a href="<?= site_url('teachers/attendance?class_id=' . $class->class_id) ?>" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-calendar-check"></i> Presenças
                                            </a>
                                            <a href="<?= site_url('teachers/grades?class=' . $class->class_id) ?>" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-star"></i> Notas
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                    
                    <!-- Lista de disciplinas por turma (abaixo) -->
                    <div class="mt-4">
                        <h6 class="text-muted"><i class="fas fa-book-open"></i> Disciplinas por Turma</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Turma</th>
                                        <th>Disciplina</th>
                                        <th>Código</th>
                                        <th>Carga Horária</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($myClasses as $class): ?>
                                        <tr>
                                            <td><?= $class->class_name ?></td>
                                            <td><?= $class->discipline_name ?></td>
                                            <td><span class="badge bg-info"><?= $class->discipline_code ?></span></td>
                                            <td><?= $class->workload_hours ?? 'N/A' ?> h</td>
                                            <td>
                                                <a href="<?= site_url('teachers/grades?class=' . $class->class_id . '&discipline=' . $class->discipline_id) ?>" 
                                                   class="btn btn-sm btn-outline-success" 
                                                   title="Lançar Notas">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <a href="<?= site_url('teachers/attendance?class_id=' . $class->class_id . '&discipline_id=' . $class->discipline_id) ?>" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="Registrar Presenças">
                                                    <i class="fas fa-calendar-check"></i>
                                                </a>
                                                <a href="<?= site_url('teachers/exams?class=' . $class->class_id . '&discipline=' . $class->discipline_id) ?>" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Ver Exames">
                                                    <i class="fas fa-file-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-school fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhuma turma atribuída</h5>
                        <p class="text-muted">Aguardando atribuição de turmas e disciplinas pela secretaria.</p>
                    </div>
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
                                    <th>Tipo</th>
                                    <th>Turma</th>
                                    <th>Disciplina</th>
                                    <th>Data</th>
                                    <th>Hora</th>
                                    <th>Sala</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcomingExams as $exam): ?>
                                    <tr>
                                        <td>
                                            <strong><?= $exam->exam_name ?? $exam->board_name ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                switch($exam->board_type) {
                                                    case 'Avaliação Contínua': echo 'info'; break;
                                                    case 'Prova Professor': echo 'primary'; break;
                                                    case 'Prova Trimestral': echo 'warning'; break;
                                                    case 'Exame Final': echo 'danger'; break;
                                                    default: echo 'secondary';
                                                }
                                            ?>">
                                                <?= $exam->board_type ?>
                                            </span>
                                        </td>
                                        <td><?= $exam->class_name ?></td>
                                        <td><?= $exam->discipline_name ?></td>
                                        <td><?= date('d/m/Y', strtotime($exam->exam_date)) ?></td>
                                        <td><?= $exam->exam_time ? date('H:i', strtotime($exam->exam_time)) : '-' ?></td>
                                        <td><?= $exam->exam_room ?: '-' ?></td>
                                        <td>
                                            <?php if ($exam->status == 'Agendado'): ?>
                                                <span class="badge bg-success">Agendado</span>
                                            <?php elseif ($exam->status == 'Realizado'): ?>
                                                <span class="badge bg-secondary">Realizado</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning"><?= $exam->status ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= site_url('teachers/exams/grade/' . $exam->id) ?>" 
                                                   class="btn btn-sm btn-success" 
                                                   title="Lançar Notas">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <a href="<?= site_url('teachers/exams/attendance/' . $exam->id) ?>" 
                                                   class="btn btn-sm btn-info" 
                                                   title="Registrar Presenças">
                                                    <i class="fas fa-user-check"></i>
                                                </a>
                                                <a href="<?= site_url('teachers/exams/results/' . $exam->id) ?>" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="Ver Resultados">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhum exame agendado para os próximos dias.</p>
                    </div>
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
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <a href="<?= site_url('teachers/attendance') ?>" class="quick-action-card">
                            <i class="fas fa-calendar-check fa-3x mb-3 text-success"></i>
                            <h6>Registrar Presenças</h6>
                            <small class="text-muted">Marque presenças dos alunos</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="<?= site_url('teachers/grades') ?>" class="quick-action-card">
                            <i class="fas fa-star fa-3x mb-3 text-warning"></i>
                            <h6>Lançar Notas (AC)</h6>
                            <small class="text-muted">Avaliações Contínuas</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="<?= site_url('teachers/exams') ?>" class="quick-action-card">
                            <i class="fas fa-file-alt fa-3x mb-3 text-info"></i>
                            <h6>Meus Exames</h6>
                            <small class="text-muted">Gerencie exames e provas</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="<?= site_url('teachers/classes') ?>" class="quick-action-card">
                            <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                            <h6>Minhas Turmas</h6>
                            <small class="text-muted">Lista de turmas e alunos</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="<?= site_url('teachers/attendance/report') ?>" class="quick-action-card">
                            <i class="fas fa-chart-line fa-3x mb-3 text-secondary"></i>
                            <h6>Relatório Presenças</h6>
                            <small class="text-muted">Análise de frequência</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="<?= site_url('teachers/grades/report/select') ?>" class="quick-action-card">
                            <i class="fas fa-chart-pie fa-3x mb-3 text-danger"></i>
                            <h6>Relatório Notas</h6>
                            <small class="text-muted">Médias e aproveitamento</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas Detalhadas (opcional) -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-pie"></i> Distribuição por Tipo de Avaliação
            </div>
            <div class="card-body">
                <canvas id="evaluationChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-tasks"></i> Pendências
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Avaliações Contínuas pendentes
                        <span class="badge bg-warning rounded-pill">3</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Presenças por registrar
                        <span class="badge bg-info rounded-pill">2</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Notas de exames por lançar
                        <span class="badge bg-danger rounded-pill">1</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de distribuição (exemplo)
    const ctx = document.getElementById('evaluationChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Avaliações Contínuas', 'Provas Professor', 'Provas Trimestrais', 'Exames Finais'],
                datasets: [{
                    data: [65, 15, 10, 10],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderWidth: 1
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
    }
});
</script>

<style>
.stat-card {
    background: linear-gradient(45deg, #28a745, #218838);
    border-radius: 8px;
    color: white;
    padding: 20px;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s;
    margin-bottom: 20px;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 2.5rem;
    opacity: 0.3;
}

.stat-label {
    font-size: 0.9rem;
    text-transform: uppercase;
    opacity: 0.9;
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    margin-top: 10px;
}

.quick-action-card {
    display: block;
    text-align: center;
    padding: 20px;
    border-radius: 8px;
    background: #f8f9fa;
    color: #333;
    text-decoration: none;
    transition: all 0.3s;
    height: 100%;
}

.quick-action-card:hover {
    background: #e9ecef;
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    color: #28a745;
}
</style>
<?= $this->endSection() ?>