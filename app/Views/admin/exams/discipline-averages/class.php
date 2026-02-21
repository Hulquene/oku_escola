<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Médias da Turma</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-users me-1"></i>
                <?= $class->class_name ?> • <?= $class->level_name ?> • <?= $class->year_name ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/exams/schedules?class_id=' . $class->id) ?>" class="btn btn-outline-info">
                <i class="fas fa-calendar-alt me-1"></i> Ver Exames
            </a>
            <a href="<?= site_url('admin/classes/view/' . $class->id) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Turma
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes') ?>">Turmas</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes/view/' . $class->id) ?>"><?= $class->class_name ?></a></li>
            <li class="breadcrumb-item active">Médias - <?= $semester->semester_name ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações do Semestre -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-calendar-alt fa-2x text-info"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="mb-1"><?= $semester->semester_name ?></h4>
                        <p class="text-muted mb-0">
                            <?= date('d/m/Y', strtotime($semester->start_date)) ?> a <?= date('d/m/Y', strtotime($semester->end_date)) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-calculator fa-2x text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Média Geral da Turma</h6>
                        <h2 class="mb-0 <?= $classStats['overall_average'] >= 10 ? 'text-success' : 'text-warning' ?>">
                            <?= number_format($classStats['overall_average'], 2) ?>
                        </h2>
                        <small class="text-muted"><?= $classStats['total_students'] ?> alunos</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cards de Estatísticas Gerais -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                            <i class="fas fa-check-circle text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Alunos Aprovados</h6>
                        <h3 class="mb-0"><?= $classStats['approved_students'] ?></h3>
                        <small class="text-success">
                            <?= $classStats['total_students'] > 0 ? round(($classStats['approved_students'] / $classStats['total_students']) * 100) : 0 ?>%
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Alunos em Recurso</h6>
                        <h3 class="mb-0"><?= $classStats['appeal_students'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-danger bg-opacity-10 p-2 rounded">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Alunos Reprovados</h6>
                        <h3 class="mb-0"><?= $classStats['failed_students'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-secondary bg-opacity-10 p-2 rounded">
                            <i class="fas fa-chart-bar text-secondary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Taxa de Aprovação</h6>
                        <h3 class="mb-0">
                            <?= $classStats['total_students'] > 0 ? round(($classStats['approved_students'] / $classStats['total_students']) * 100, 1) : 0 ?>%
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas por Disciplina -->
<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-chart-pie me-2 text-info"></i>
            Desempenho por Disciplina
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 py-3 ps-3">Disciplina</th>
                        <th class="border-0 py-3 text-center">Código</th>
                        <th class="border-0 py-3 text-center">Média</th>
                        <th class="border-0 py-3 text-center">Máxima</th>
                        <th class="border-0 py-3 text-center">Mínima</th>
                        <th class="border-0 py-3 text-center">Aprovados</th>
                        <th class="border-0 py-3 text-center">Recurso</th>
                        <th class="border-0 py-3 text-center">Reprovados</th>
                        <th class="border-0 py-3 text-center pe-3">Taxa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($disciplineStats as $stat): ?>
                        <?php
                        $approvalRate = $stat['total_students'] > 0 
                            ? round(($stat['approved'] / $stat['total_students']) * 100, 1) 
                            : 0;
                        ?>
                        <tr>
                            <td class="ps-3 fw-semibold"><?= $stat['name'] ?></td>
                            <td class="text-center">
                                <span class="badge bg-secondary"><?= $stat['code'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold <?= $stat['avg_score'] >= 10 ? 'text-success' : 'text-warning' ?>">
                                    <?= number_format($stat['avg_score'], 1) ?>
                                </span>
                            </td>
                            <td class="text-center text-success"><?= number_format($stat['max_score'], 1) ?></td>
                            <td class="text-center text-danger"><?= number_format($stat['min_score'], 1) ?></td>
                            <td class="text-center">
                                <span class="badge bg-success"><?= $stat['approved'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark"><?= $stat['appeal'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger"><?= $stat['failed'] ?></span>
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-success" 
                                             style="width: <?= $approvalRate ?>%"></div>
                                    </div>
                                    <small class="ms-2"><?= $approvalRate ?>%</small>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tabela de Médias por Aluno -->
<div class="card">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-table me-2 text-primary"></i>
                Médias Individuais por Aluno
            </h5>
            <div class="d-flex gap-2">
                <a href="<?= site_url('admin/discipline-averages/export/' . $class->id . '/' . $semester->id) ?>?type=excel" 
                   class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel me-1"></i> Excel
                </a>
                <a href="<?= site_url('admin/discipline-averages/export/' . $class->id . '/' . $semester->id) ?>?type=pdf" 
                   class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 py-3 ps-3" width="50">#</th>
                        <th class="border-0 py-3">Nº Aluno</th>
                        <th class="border-0 py-3">Nome do Aluno</th>
                        <?php foreach ($disciplines as $discipline): ?>
                            <th class="border-0 py-3 text-center" title="<?= $discipline->discipline_name ?>">
                                <?= $discipline->discipline_code ?>
                            </th>
                        <?php endforeach; ?>
                        <th class="border-0 py-3 text-center">Média</th>
                        <th class="border-0 py-3 text-center pe-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students)): ?>
                        <?php $i = 1; ?>
                        <?php foreach ($students as $student): ?>
                            <?php
                            $statusClass = 'secondary';
                            $statusText = 'Em Andamento';
                            
                            if ($student['overall_average'] >= 10) {
                                $statusClass = 'success';
                                $statusText = 'Aprovado';
                            } elseif ($student['overall_average'] >= 7) {
                                $statusClass = 'warning';
                                $statusText = 'Recurso';
                            } elseif ($student['overall_average'] > 0) {
                                $statusClass = 'danger';
                                $statusText = 'Reprovado';
                            }
                            ?>
                            <tr>
                                <td class="ps-3"><?= $i++ ?></td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info p-2">
                                        <?= $student['student_number'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= site_url('admin/discipline-averages/student/' . $student['enrollment_id'] . '/' . $semester->id) ?>" 
                                       class="text-decoration-none fw-semibold">
                                        <?= $student['student_name'] ?>
                                    </a>
                                </td>
                                <?php foreach ($disciplines as $discipline): ?>
                                    <td class="text-center">
                                        <?php if (isset($student['averages'][$discipline->id])): ?>
                                            <?php 
                                            $score = $student['averages'][$discipline->id]['score'];
                                            $scoreClass = $score >= 10 ? 'text-success' : ($score >= 7 ? 'text-warning' : 'text-danger');
                                            ?>
                                            <span class="fw-bold <?= $scoreClass ?>">
                                                <?= number_format($score, 1) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class="text-center">
                                    <span class="fw-bold fs-5 <?= $student['overall_average'] >= 10 ? 'text-success' : 'text-warning' ?>">
                                        <?= number_format($student['overall_average'], 2) ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <span class="badge bg-<?= $statusClass ?> p-2">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= 5 + count($disciplines) ?>" class="text-center py-5">
                                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum aluno encontrado</h5>
                                <p class="text-muted">Esta turma não possui alunos ativos.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.table th {
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    white-space: nowrap;
}

.table td {
    vertical-align: middle;
    white-space: nowrap;
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}

.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 10px;
}
</style>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Export functions
function exportToExcel() {
    window.location.href = '<?= site_url('admin/discipline-averages/export/' . $class->id . '/' . $semester->id) ?>?type=excel';
}

function exportToPDF() {
    window.location.href = '<?= site_url('admin/discipline-averages/export/' . $class->id . '/' . $semester->id) ?>?type=pdf';
}

// Search functionality
document.getElementById('searchInput')?.addEventListener('keyup', function() {
    let searchText = this.value.toLowerCase();
    let rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchText) ? '' : 'none';
    });
});
</script>

<?= $this->endSection() ?>