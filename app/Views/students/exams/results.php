<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?? 'Meus Resultados' ?></h1>
        <div>
            <a href="<?= site_url('students/exams') ?>" class="btn btn-primary">
                <i class="fas fa-calendar-alt"></i> Ver Exames
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('students/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active" aria-current="page">Resultados</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informação da Matrícula -->
<?php if (isset($enrollment) && $enrollment): ?>
<div class="alert alert-info mb-4">
    <i class="fas fa-graduation-cap"></i> 
    <strong>Turma:</strong> <?= $enrollment->class_name ?? 'N/A' ?> | 
    <strong>Ano Letivo:</strong> <?= $enrollment->year_name ?? date('Y') ?>
</div>
<?php endif; ?>

<!-- Filtro de Semestre -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <i class="fas fa-filter me-2"></i>Filtros
    </div>
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="semester" class="form-label fw-bold">Selecione o Semestre</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
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
            </div>
            <div class="col-md-8 d-flex align-items-end">
                <p class="text-muted mb-0">
                    <i class="fas fa-info-circle"></i> 
                    Os resultados abaixo referem-se ao período selecionado.
                </p>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label mb-1">Total de Exames</p>
                        <h2 class="stat-value mb-0"><?= $total ?? 0 ?></h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-pencil-alt fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="stat-footer mt-3">
                    <small><i class="fas fa-chart-line me-1"></i> <?= $total ?? 0 ?> exames realizados</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label mb-1">Aprovados</p>
                        <h2 class="stat-value mb-0"><?= $approved ?? 0 ?></h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="stat-footer mt-3">
                    <?php $taxaAprovacao = ($total ?? 0) > 0 ? round((($approved ?? 0) / ($total ?? 1)) * 100, 1) : 0; ?>
                    <small><i class="fas fa-percentage me-1"></i> Taxa: <?= $taxaAprovacao ?>%</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-danger text-white stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label mb-1">Reprovados</p>
                        <h2 class="stat-value mb-0"><?= $failed ?? 0 ?></h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-times-circle fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="stat-footer mt-3">
                    <?php $taxaReprovacao = ($total ?? 0) > 0 ? round((($failed ?? 0) / ($total ?? 1)) * 100, 1) : 0; ?>
                    <small><i class="fas fa-percentage me-1"></i> Taxa: <?= $taxaReprovacao ?>%</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label mb-1">Média Geral</p>
                        <h2 class="stat-value mb-0"><?= number_format($average ?? 0, 1) ?></h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="stat-footer mt-3">
                    <?php 
                    $conceito = ($average ?? 0) >= 14 ? 'Excelente' : (($average ?? 0) >= 10 ? 'Bom' : 'Precisa Melhorar');
                    ?>
                    <small><i class="fas fa-star me-1"></i> Conceito: <?= $conceito ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Resultados por Disciplina -->
<div class="card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-star me-2"></i>Resultados por Disciplina
        </div>
        <div>
            <button class="btn btn-sm btn-outline-primary" onclick="exportTableToExcel()">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($disciplines)): ?>
            <?php foreach ($disciplines as $discipline): ?>
                <div class="card mb-3">
                    <div class="card-header bg-secondary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-book me-2"></i><?= $discipline['discipline_name'] ?>
                            </h6>
                            <span class="badge bg-light text-dark">
                                Média: <?= number_format($discipline['average'], 1) ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Exame</th>
                                        <th>Tipo</th>
                                        <th class="text-center">Nota</th>
                                        <th class="text-center">Percentual</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($discipline['grades'] as $grade): ?>
                                        <?php 
                                        $percentual = $grade->score_percentage ?? round(($grade->score / ($grade->max_score ?? 20)) * 100, 1);
                                        $statusClass = $grade->score >= 10 ? 'success' : 'danger';
                                        ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($grade->exam_date)) ?></td>
                                            <td><?= $grade->board_name ?? 'Exame' ?></td>
                                            <td><span class="badge bg-info"><?= $grade->board_type ?? 'Normal' ?></span></td>
                                            <td class="text-center">
                                                <span class="fw-bold <?= $grade->score >= 10 ? 'text-success' : 'text-danger' ?>">
                                                    <?= number_format($grade->score, 1) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                        <div class="progress-bar bg-<?= $statusClass ?>" 
                                                             style="width: <?= $percentual ?>%"></div>
                                                    </div>
                                                    <small><?= $percentual ?>%</small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-<?= $statusClass ?>">
                                                    <?= $grade->score >= 10 ? 'Aprovado' : 'Reprovado' ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum resultado encontrado</h5>
                <p class="text-muted mb-4">Não há resultados para o período selecionado.</p>
                <a href="<?= site_url('students/exams') ?>" class="btn btn-primary">
                    <i class="fas fa-calendar-alt"></i> Ver Exames
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Resumo Geral -->
<?php if (!empty($disciplines)): ?>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="alert alert-info mb-0">
            <div class="row">
                <div class="col-md-3">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    <strong>Aprovados:</strong> <?= $approved ?? 0 ?>
                </div>
                <div class="col-md-3">
                    <i class="fas fa-times-circle text-danger me-2"></i>
                    <strong>Reprovados:</strong> <?= $failed ?? 0 ?>
                </div>
                <div class="col-md-3">
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    <strong>Média Geral:</strong> <?= number_format($average ?? 0, 1) ?>
                </div>
                <div class="col-md-3">
                    <i class="fas fa-percentage text-warning me-2"></i>
                    <strong>Aproveitamento:</strong> <?= round(($approved ?? 0) / max(($total ?? 1), 1) * 100, 1) ?>%
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar DataTables
    $('.table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        pageLength: 10,
        responsive: true,
        order: [[0, 'desc']]
    });
});

// Exportar para Excel
function exportTableToExcel() {
    const tables = document.querySelectorAll('.table');
    let csv = [];
    
    tables.forEach((table, index) => {
        const rows = Array.from(table.querySelectorAll('tr'));
        
        rows.forEach(row => {
            const cells = Array.from(row.querySelectorAll('th, td'));
            const rowData = cells.map(cell => cell.innerText.trim().replace(/,/g, ';'));
            csv.push(rowData.join(','));
        });
        
        if (index < tables.length - 1) {
            csv.push(''); // Linha em branco entre tabelas
        }
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'resultados_' + new Date().toISOString().split('T')[0] + '.csv');
    link.click();
}
</script>

<style>
.stat-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.stat-icon {
    opacity: 0.8;
}

.progress {
    border-radius: 10px;
    background-color: #f0f0f0;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.5s ease;
}

/* Animações */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.3s ease-out;
}
</style>

<?= $this->endSection() ?>