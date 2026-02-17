<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
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
                        <?php if (!empty($semesters)): ?>
                            <?php foreach ($semesters as $sem): ?>
                                <option value="<?= $sem->id ?>" <?= $selectedSemester == $sem->id ? 'selected' : '' ?>>
                                    <?= $sem->semester_name ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-8 d-flex align-items-end">
                <p class="text-muted mb-0">
                    <i class="fas fa-info-circle"></i> 
                    Os resultados abaixo referem-se ao semestre selecionado.
                </p>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Estatísticas Melhorados -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label mb-1">Total de Exames</p>
                        <h2 class="stat-value mb-0"><?= $total ?></h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-pencil-alt fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="stat-footer mt-3">
                    <small><i class="fas fa-chart-line me-1"></i> <?= $total ?> exames realizados</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label mb-1">Aprovados</p>
                        <h2 class="stat-value mb-0"><?= $approved ?></h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="stat-footer mt-3">
                    <?php $taxaAprovacao = $total > 0 ? round(($approved / $total) * 100, 1) : 0; ?>
                    <small><i class="fas fa-percentage me-1"></i> Taxa: <?= $taxaAprovacao ?>%</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card bg-danger text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label mb-1">Reprovados</p>
                        <h2 class="stat-value mb-0"><?= $failed ?></h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-times-circle fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="stat-footer mt-3">
                    <?php $taxaReprovacao = $total > 0 ? round(($failed / $total) * 100, 1) : 0; ?>
                    <small><i class="fas fa-percentage me-1"></i> Taxa: <?= $taxaReprovacao ?>%</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label mb-1">Média Geral</p>
                        <h2 class="stat-value mb-0"><?= number_format($average, 1) ?></h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="stat-footer mt-3">
                    <?php 
                    $conceito = $average >= 14 ? 'Excelente' : ($average >= 10 ? 'Bom' : 'Precisa Melhorar');
                    ?>
                    <small><i class="fas fa-star me-1"></i> Conceito: <?= $conceito ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Resultados -->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-star me-2"></i>Resultados por Exame
            <span class="badge bg-secondary ms-2"><?= count($results) ?> registros</span>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-primary" onclick="exportTableToExcel()">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($results)): ?>
            <div class="table-responsive">
                <table id="resultsTable" class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Data</th>
                            <th>Exame</th>
                            <th>Disciplina</th>
                            <th>Tipo</th>
                            <th class="text-center">Nota</th>
                            <th class="text-center">Percentual</th>
                            <th class="text-center">Conceito</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $result): 
                            $percentual = $result->score_percentage ?? round(($result->score / 20) * 100, 1);
                            $statusClass = $result->score >= 10 ? 'success' : 'danger';
                            $statusText = $result->score >= 10 ? 'Aprovado' : 'Reprovado';
                            
                            // Determinar conceito
                            if ($result->score >= 18) $conceito = 'Excelente';
                            elseif ($result->score >= 15) $conceito = 'Bom';
                            elseif ($result->score >= 12) $conceito = 'Regular';
                            elseif ($result->score >= 10) $conceito = 'Suficiente';
                            else $conceito = 'Insuficiente';
                            
                            $conceitoClass = $result->score >= 10 ? 
                                ($result->score >= 18 ? 'success' : ($result->score >= 15 ? 'info' : 'warning')) : 'danger';
                        ?>
                            <tr>
                                <td>
                                    <span class="fw-bold"><?= date('d/m/Y', strtotime($result->exam_date)) ?></span>
                                </td>
                                <td><?= $result->exam_name ?></td>
                                <td><?= $result->discipline_name ?></td>
                                <td>
                                    <span class="badge bg-info"><?= $result->board_type ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="fs-5 fw-bold <?= $result->score >= 10 ? 'text-success' : 'text-danger' ?>">
                                        <?= number_format($result->score, 1) ?>
                                    </span>
                                    <small class="text-muted">/20</small>
                                </td>
                                <td class="text-center" style="min-width: 120px;">
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar bg-<?= $statusClass ?>" 
                                                 style="width: <?= $percentual ?>%"></div>
                                        </div>
                                        <span class="small"><?= number_format($percentual, 1) ?>%</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $conceitoClass ?> p-2">
                                        <?= $conceito ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $statusClass ?> p-2">
                                        <i class="fas fa-<?= $result->score >= 10 ? 'check-circle' : 'times-circle' ?> me-1"></i>
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#resultModal<?= $result->id ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Rodapé com resumo -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="alert alert-info mb-0">
                        <div class="row">
                            <div class="col-md-4">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>Aprovados:</strong> <?= $approved ?>
                            </div>
                            <div class="col-md-4">
                                <i class="fas fa-times-circle text-danger me-2"></i>
                                <strong>Reprovados:</strong> <?= $failed ?>
                            </div>
                            <div class="col-md-4">
                                <i class="fas fa-chart-line text-primary me-2"></i>
                                <strong>Média Geral:</strong> <?= number_format($average, 1) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum resultado encontrado</h5>
                <p class="text-muted mb-4">Não há resultados para o semestre selecionado.</p>
                <a href="<?= site_url('students/exams') ?>" class="btn btn-primary">
                    <i class="fas fa-calendar-alt"></i> Ver Exames
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modals para detalhes dos resultados -->
<?php if (!empty($results)): ?>
    <?php foreach ($results as $result): ?>
        <div class="modal fade" id="resultModal<?= $result->id ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-info-circle me-2"></i>Detalhes do Resultado
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-info d-inline-flex p-3 mb-3">
                                <i class="fas fa-pencil-alt fa-2x text-white"></i>
                            </div>
                            <h4><?= $result->exam_name ?></h4>
                            <span class="badge bg-info"><?= $result->board_type ?></span>
                        </div>
                        
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 40%">Disciplina</th>
                                <td><?= $result->discipline_name ?></td>
                            </tr>
                            <tr>
                                <th>Data do Exame</th>
                                <td><?= date('d/m/Y', strtotime($result->exam_date)) ?></td>
                            </tr>
                            <tr>
                                <th>Nota Obtida</th>
                                <td class="fw-bold <?= $result->score >= 10 ? 'text-success' : 'text-danger' ?>">
                                    <?= number_format($result->score, 1) ?> / 20
                                </td>
                            </tr>
                            <tr>
                                <th>Percentual</th>
                                <td>
                                    <?php $percent = $result->score_percentage ?? round(($result->score / 20) * 100, 1); ?>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 10px;">
                                            <div class="progress-bar bg-<?= $result->score >= 10 ? 'success' : 'danger' ?>" 
                                                 style="width: <?= $percent ?>%"></div>
                                        </div>
                                        <span><?= number_format($percent, 1) ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Classificação</th>
                                <td><?= $result->grade ?: '-' ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-<?= $result->score >= 10 ? 'success' : 'danger' ?> p-2">
                                        <i class="fas fa-<?= $result->score >= 10 ? 'check-circle' : 'times-circle' ?> me-1"></i>
                                        <?= $result->score >= 10 ? 'Aprovado' : 'Reprovado' ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        
                        <?php if ($result->observations): ?>
                            <div class="mt-3">
                                <strong>Observações:</strong>
                                <p class="text-muted"><?= $result->observations ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#resultsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[0, 'desc']], // Ordenar por data decrescente
        pageLength: 10,
        responsive: true,
        columnDefs: [
            { type: 'num', targets: 4 } // Coluna de nota como numérica
        ]
    });
});

// Exportar para Excel
function exportTableToExcel() {
    const table = document.getElementById('resultsTable');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    let csv = [];
    
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        // Remover coluna de detalhes (última)
        const filteredCells = cells.filter((_, index) => index !== cells.length - 1);
        const rowData = filteredCells.map(cell => cell.innerText.trim().replace(/,/g, ';'));
        csv.push(rowData.join(','));
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
/* Estilos personalizados */
.stat-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.stat-icon {
    opacity: 0.8;
}

.table-hover tbody tr:hover {
    background-color: rgba(23, 162, 184, 0.05);
}

.progress {
    border-radius: 10px;
    background-color: #f0f0f0;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.5s ease;
}

.badge {
    font-size: 0.85em;
    padding: 0.5em 0.75em;
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