<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Boletim de Notas</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-graduation-cap me-1"></i>
                <?= $student->first_name ?> <?= $student->last_name ?> • <?= $enrollment->class_name ?? '' ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" style="width: 200px;" onchange="changeSemester(this.value)">
                <?php foreach ($semesters as $sem): ?>
                    <option value="<?= $sem->id ?>" <?= $selectedSemester == $sem->id ? 'selected' : '' ?>>
                        <?= $sem->semester_name ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Imprimir
            </button>
            <a href="<?= site_url('admin/students/view/' . $student->id) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students') ?>">Alunos</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students/view/' . $student->id) ?>"><?= $student->first_name ?> <?= $student->last_name ?></a></li>
            <li class="breadcrumb-item active">Boletim - <?= $semester->semester_name ?? '' ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cabeçalho do Boletim -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="120"><strong>Aluno:</strong></td>
                        <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                    </tr>
                    <tr>
                        <td><strong>Nº Matrícula:</strong></td>
                        <td><?= $student->student_number ?></td>
                    </tr>
                    <tr>
                        <td><strong>Turma:</strong></td>
                        <td><?= $enrollment->class_name ?? 'Não definida' ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="120"><strong>Semestre:</strong></td>
                        <td><?= $semester->semester_name ?? '' ?></td>
                    </tr>
                    <tr>
                        <td><strong>Ano Letivo:</strong></td>
                        <td><?= $enrollment->year_name ?? '' ?></td>
                    </tr>
                    <tr>
                        <td><strong>Data:</strong></td>
                        <td><?= date('d/m/Y') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Cards de Resumo -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-1">Média Geral</h6>
                <h2 class="mb-0 <?= $average >= 10 ? 'text-success' : 'text-warning' ?>">
                    <?= number_format($average, 2) ?>
                </h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-1">Disciplinas</h6>
                <h2 class="mb-0"><?= $stats['total'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-1">Aprovadas</h6>
                <h2 class="mb-0 text-success"><?= $stats['approved'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-1">Resultado</h6>
                <h4 class="mb-0">
                    <?php if ($semesterResult ?? null): ?>
                        <span class="badge bg-<?= $semesterResult->status == 'Aprovado' ? 'success' : ($semesterResult->status == 'Recurso' ? 'warning' : 'danger') ?> p-3">
                            <?= $semesterResult->status ?>
                        </span>
                    <?php else: ?>
                        <span class="badge bg-secondary p-3">Em Andamento</span>
                    <?php endif; ?>
                </h4>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Notas por Disciplina -->
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-table me-2 text-primary"></i>
            Desempenho por Disciplina
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 py-3 ps-3" width="50">#</th>
                        <th class="border-0 py-3">Disciplina</th>
                        <th class="border-0 py-3 text-center">Código</th>
                        <th class="border-0 py-3 text-center">Média AC</th>
                        <th class="border-0 py-3 text-center">Exame</th>
                        <th class="border-0 py-3 text-center">Nota Final</th>
                        <th class="border-0 py-3 text-center pe-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($disciplineAverages)): ?>
                        <?php $i = 1; ?>
                        <?php foreach ($disciplineAverages as $avg): ?>
                            <?php
                            $statusClass = [
                                'Aprovado' => 'success',
                                'Recurso' => 'warning',
                                'Reprovado' => 'danger'
                            ][$avg->status] ?? 'secondary';
                            ?>
                            <tr>
                                <td class="ps-3"><?= $i++ ?></td>
                                <td class="fw-semibold"><?= $avg->discipline_name ?></td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= $avg->discipline_code ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($avg->ac_score > 0): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary p-2">
                                            <?= number_format($avg->ac_score, 1) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($avg->exam_score > 0): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info p-2">
                                            <?= number_format($avg->exam_score, 1) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold fs-5 <?= $avg->final_score >= 10 ? 'text-success' : 'text-warning' ?>">
                                        <?= number_format($avg->final_score, 1) ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <span class="badge bg-<?= $statusClass ?> p-2">
                                        <?= $avg->status ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                                <p class="text-muted">Nenhuma nota registada para este semestre</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Gráfico de Desempenho -->
<div class="card mt-4 d-print-none">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-chart-pie me-2 text-success"></i>
            Distribuição das Notas
        </h5>
    </div>
    <div class="card-body">
        <canvas id="gradesChart" style="height: 300px;"></canvas>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function changeSemester(semesterId) {
    window.location.href = '<?= site_url('admin/exams/results/report-card/' . $student->id) ?>?semester=' + semesterId;
}

// Gráfico de Distribuição
const ctx = document.getElementById('gradesChart')?.getContext('2d');
if (ctx) {
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Aprovadas (≥10)', 'Recurso (7-9.9)', 'Reprovadas (<7)'],
            datasets: [{
                data: [<?= $stats['approved'] ?? 0 ?>, <?= $stats['appeal'] ?? 0 ?>, <?= $stats['failed'] ?? 0 ?>],
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                borderWidth: 0
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
</script>

<style>
@media print {
    .page-header, .btn, .d-print-none, .breadcrumb, .card-header .btn {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .table {
        font-size: 12px;
    }
}
</style>

<?= $this->endSection() ?>