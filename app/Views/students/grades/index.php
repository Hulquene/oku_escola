<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1><?= $title ?? 'Minhas Notas' ?></h1>
        <div>
            <a href="<?= site_url('students/grades/report-card') ?>" class="btn btn-primary">
                <i class="fas fa-file-alt"></i> Ver Boletim
            </a>
            <a href="<?= site_url('students/subjects') ?>" class="btn btn-info">
                <i class="fas fa-book"></i> Disciplinas
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Minhas Notas</li>
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

<!-- Informação da Matrícula -->
<?php if (isset($enrollment) && $enrollment): ?>
<div class="alert alert-info mb-4">
    <i class="fas fa-graduation-cap"></i> 
    <strong>Turma:</strong> <?= $enrollment->class_name ?? 'N/A' ?> | 
    <strong>Ano Letivo:</strong> <?= $enrollment->year_name ?? date('Y') ?>
</div>
<?php endif; ?>

<!-- Semester Filter -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <i class="fas fa-filter"></i> Filtrar por Período
    </div>
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="semester" class="form-label">Semestre/Trimestre</label>
                <select class="form-select" id="semester" name="semester" onchange="this.form.submit()">
                    <option value="">Todos os períodos</option>
                    <?php if (!empty($semesters)): ?>
                        <?php foreach ($semesters as $sem): ?>
                            <option value="<?= $sem->id ?>" <?= ($selectedSemester ?? '') == $sem->id ? 'selected' : '' ?>>
                                <?= $sem->semester_name ?? $sem->semester_type ?? 'Período' ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-8">
                <div class="alert alert-info mb-0">
                    <div class="row">
                        <div class="col-md-4">
                            <i class="fas fa-chart-line"></i> 
                            Média Geral: <strong><?= number_format($overallAverage ?? 0, 1, ',', '.') ?></strong>
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-star"></i> 
                            Total Avaliações: <strong><?= $stats['total_exams'] ?? 0 ?></strong>
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-check-circle"></i> 
                            Aproveitamento: <strong><?= $stats['approval_rate'] ?? 0 ?>%</strong>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Resumo -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body text-center">
                <i class="fas fa-star fa-2x mb-2"></i>
                <h6>Média Geral</h6>
                <h3><?= number_format($overallAverage ?? 0, 1, ',', '.') ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h6>Aprovadas</h6>
                <h3><?= $stats['approved'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white h-100">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <h6>Recurso</h6>
                <h3><?= $stats['appeal'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white h-100">
            <div class="card-body text-center">
                <i class="fas fa-times-circle fa-2x mb-2"></i>
                <h6>Reprovadas</h6>
                <h3><?= $stats['failed'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Notas por Disciplina (Cards) -->
<?php if (!empty($disciplines)): ?>
    <?php foreach ($disciplines as $discipline): ?>
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-book"></i> <?= esc($discipline['discipline_name']) ?>
                </h5>
                <span class="badge bg-<?= ($discipline['average'] ?? 0) >= 10 ? 'success' : 'warning' ?>">
                    Média: <?= number_format($discipline['average'] ?? 0, 1, ',', '.') ?>
                </span>
            </div>
            <div class="card-body">
                <?php if (!empty($discipline['grades'])): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Avaliação</th>
                                    <th>Tipo</th>
                                    <th class="text-center">Nota</th>
                                    <th class="text-center">Classificação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($discipline['grades'] as $grade): ?>
                                    <tr>
                                        <td><?= $grade->formatted_date ?? date('d/m/Y', strtotime($grade->exam_date ?? 'now')) ?></td>
                                        <td><?= esc($grade->board_name ?? $grade->exam_name ?? 'Avaliação') ?></td>
                                        <td>
                                            <span class="badge bg-info"><?= esc($grade->board_type ?? 'Normal') ?></span>
                                        </td>
                                        <td class="text-center fw-bold <?= ($grade->score ?? 0) >= 10 ? 'text-success' : 'text-danger' ?>">
                                            <?= number_format($grade->score ?? 0, 1, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($grade->grade ?? false): ?>
                                                <span class="badge bg-secondary"><?= $grade->grade ?></span>
                                            <?php elseif (($grade->score ?? 0) >= 10): ?>
                                                <span class="badge bg-success">Aprovado</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Reprovado</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Média da Disciplina:</strong></td>
                                    <td class="text-center fw-bold <?= ($discipline['average'] ?? 0) >= 10 ? 'text-success' : 'text-danger' ?>">
                                        <?= number_format($discipline['average'] ?? 0, 1, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        $status = ($discipline['average'] ?? 0) >= 10 ? 'Aprovado' : 'Em curso';
                                        $statusColor = ($discipline['average'] ?? 0) >= 10 ? 'success' : 'warning';
                                        ?>
                                        <span class="badge bg-<?= $statusColor ?>"><?= $status ?></span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Link para detalhes da disciplina -->
                    <div class="text-end mt-2">
                        <a href="<?= site_url('students/subjects/details/' . $discipline['discipline_id']) ?>" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> Ver detalhes da disciplina
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Nenhuma nota registrada para esta disciplina.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-warning text-center">
        <i class="fas fa-info-circle fa-3x mb-3"></i>
        <h5>Nenhuma nota encontrada</h5>
        <p>Não há notas registradas para o período selecionado.</p>
    </div>
<?php endif; ?>

<!-- Se não houver matrícula ativa -->
<?php if (empty($disciplines) && (!isset($enrollment) || !$enrollment)): ?>
<div class="alert alert-warning text-center">
    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
    <h5>Matrícula não encontrada</h5>
    <p>Você não possui uma matrícula ativa para o ano letivo atual.</p>
    <p class="small">Entre em contato com a secretaria para regularizar sua situação.</p>
</div>
<?php endif; ?>

<!-- Gráfico de Desempenho (opcional) -->
<?php if (!empty($disciplines) && count($disciplines) > 0): ?>
<div class="card mt-4">
    <div class="card-header bg-success text-white">
        <i class="fas fa-chart-bar"></i> Gráfico de Desempenho
    </div>
    <div class="card-body">
        <canvas id="gradesChart" style="max-height: 300px;"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('gradesChart').getContext('2d');
    
    const disciplines = <?= json_encode(array_column($disciplines, 'discipline_name')) ?>;
    const averages = <?= json_encode(array_map(function($d) { return $d['average']; }, $disciplines)) ?>;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: disciplines,
            datasets: [{
                label: 'Média por Disciplina',
                data: averages,
                backgroundColor: averages.map(avg => avg >= 10 ? 'rgba(40, 167, 69, 0.7)' : 'rgba(255, 193, 7, 0.7)'),
                borderColor: averages.map(avg => avg >= 10 ? '#28a745' : '#ffc107'),
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 20,
                    title: {
                        display: true,
                        text: 'Valores (0-20)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
<?php endif; ?>

<!-- Botões de Ação -->
<div class="row mt-4">
    <div class="col-12">
        <div class="btn-group" role="group">
            <a href="<?= site_url('students/dashboard') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="<?= site_url('students/subjects') ?>" class="btn btn-info">
                <i class="fas fa-book"></i> Disciplinas
            </a>
            <a href="<?= site_url('students/exams') ?>" class="btn btn-warning">
                <i class="fas fa-pencil-alt"></i> Exames
            </a>
            <a href="<?= site_url('students/grades/report-card') ?>" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> Boletim Completo
            </a>
        </div>
    </div>
</div>

<style>
.btn-group {
    flex-wrap: wrap;
    gap: 0.5rem;
}
.btn-group .btn {
    border-radius: 0.25rem !important;
    margin: 0;
}
</style>

<?= $this->endSection() ?>