<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('students/attendance') ?>" class="btn btn-primary">
            <i class="fas fa-calendar-alt"></i> Presenças Mensais
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('students/attendance') ?>">Presenças</a></li>
            <li class="breadcrumb-item active" aria-current="page">Histórico</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros Avançados -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <i class="fas fa-filter me-2"></i>Filtros do Histórico
        <button class="btn btn-sm btn-link float-end" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="filterCollapse">
        <div class="card-body">
            <form method="get" id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label for="semester" class="form-label fw-bold">Semestre</label>
                    <select class="form-select" id="semester" name="semester">
                        <option value="">Todos os semestres</option>
                        <?php if (!empty($semesters)): ?>
                            <?php foreach ($semesters as $sem): ?>
                                <option value="<?= $sem->id ?>" <?= $selectedSemester == $sem->id ? 'selected' : '' ?>>
                                    <?= $sem->semester_name ?> (<?= date('Y', strtotime($sem->start_date)) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="discipline" class="form-label fw-bold">Disciplina</label>
                    <select class="form-select" id="discipline" name="discipline">
                        <option value="">Todas as disciplinas</option>
                        <?php 
                        // Extrair disciplinas únicas das presenças
                        $uniqueDisciplines = [];
                        foreach ($attendances ?? [] as $att) {
                            if (!empty($att->discipline_name) && !in_array($att->discipline_name, $uniqueDisciplines)) {
                                $uniqueDisciplines[] = $att->discipline_name;
                            }
                        }
                        sort($uniqueDisciplines);
                        ?>
                        <?php foreach ($uniqueDisciplines as $disciplineName): ?>
                            <option value="<?= $disciplineName ?>" <?= ($selectedDiscipline ?? '') == $disciplineName ? 'selected' : '' ?>>
                                <?= $disciplineName ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="status" class="form-label fw-bold">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="Presente" <?= ($selectedStatus ?? '') == 'Presente' ? 'selected' : '' ?>>Presente</option>
                        <option value="Ausente" <?= ($selectedStatus ?? '') == 'Ausente' ? 'selected' : '' ?>>Ausente</option>
                        <option value="Atrasado" <?= ($selectedStatus ?? '') == 'Atrasado' ? 'selected' : '' ?>>Atrasado</option>
                        <option value="Falta Justificada" <?= ($selectedStatus ?? '') == 'Falta Justificada' ? 'selected' : '' ?>>Falta Justificada</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="year" class="form-label fw-bold">Ano</label>
                    <select class="form-select" id="year" name="year">
                        <option value="">Todos</option>
                        <?php 
                        $currentYear = date('Y');
                        for ($y = $currentYear; $y >= $currentYear - 3; $y--): 
                        ?>
                            <option value="<?= $y ?>" <?= ($selectedYear ?? '') == $y ? 'selected' : '' ?>>
                                <?= $y ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cards de Resumo -->
<?php 
if (!empty($attendances)):
    $stats = [
        'total' => count($attendances),
        'present' => 0,
        'absent' => 0,
        'late' => 0,
        'justified' => 0,
        'by_discipline' => []
    ];
    
    foreach ($attendances as $att) {
        switch ($att->status) {
            case 'Presente': $stats['present']++; break;
            case 'Ausente': $stats['absent']++; break;
            case 'Atrasado': $stats['late']++; break;
            case 'Falta Justificada': $stats['justified']++; break;
        }
        
        // Estatísticas por disciplina
        $discipline = $att->discipline_name ?? 'Geral';
        if (!isset($stats['by_discipline'][$discipline])) {
            $stats['by_discipline'][$discipline] = ['total' => 0, 'present' => 0];
        }
        $stats['by_discipline'][$discipline]['total']++;
        if ($att->status == 'Presente') {
            $stats['by_discipline'][$discipline]['present']++;
        }
    }
?>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">Total de Registros</h6>
                            <h2 class="mb-0"><?= $stats['total'] ?></h2>
                        </div>
                        <i class="fas fa-calendar-check fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">Presenças</h6>
                            <h2 class="mb-0"><?= $stats['present'] ?></h2>
                        </div>
                        <i class="fas fa-user-check fa-3x text-white-50"></i>
                    </div>
                    <small class="text-white-50">
                        <?= $stats['total'] > 0 ? round(($stats['present'] / $stats['total']) * 100, 1) : 0 ?>% do total
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card bg-danger text-white stat-card">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Ausências</h6>
                    <h3 class="mb-0"><?= $stats['absent'] ?></h3>
                    <small class="text-white-50">
                        <?= $stats['total'] > 0 ? round(($stats['absent'] / $stats['total']) * 100, 1) : 0 ?>%
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card bg-warning text-dark stat-card">
                <div class="card-body">
                    <h6 class="card-title">Atrasos</h6>
                    <h3 class="mb-0"><?= $stats['late'] ?></h3>
                    <small>
                        <?= $stats['total'] > 0 ? round(($stats['late'] / $stats['total']) * 100, 1) : 0 ?>%
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card bg-info text-white stat-card">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Justificados</h6>
                    <h3 class="mb-0"><?= $stats['justified'] ?></h3>
                    <small class="text-white-50">
                        <?= $stats['total'] > 0 ? round(($stats['justified'] / $stats['total']) * 100, 1) : 0 ?>%
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gráfico de Presenças por Disciplina -->
    <?php if (count($stats['by_discipline']) > 1): ?>
    <div class="card mb-4">
        <div class="card-header bg-light">
            <i class="fas fa-chart-pie"></i> Presenças por Disciplina
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($stats['by_discipline'] as $discipline => $data): ?>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <strong><?= $discipline ?></strong>
                            <span><?= $data['present'] ?>/<?= $data['total'] ?> (<?= round(($data['present'] / $data['total']) * 100, 1) ?>%)</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: <?= ($data['present'] / $data['total']) * 100 ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Tabela de Presenças -->
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table"></i> Registros de Presença
                <span class="badge bg-secondary ms-2"><?= count($attendances) ?> registros</span>
            </div>
            <div class="btn-group">
                <button class="btn btn-sm btn-outline-primary" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i> Exportar
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="attendanceTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Data</th>
                            <th>Dia da Semana</th>
                            <th>Disciplina</th>
                            <th>Status</th>
                            <th>Justificação</th>
                            <th>Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendances as $att): 
                            $statusClass = [
                                'Presente' => 'success',
                                'Ausente' => 'danger',
                                'Atrasado' => 'warning',
                                'Falta Justificada' => 'info',
                                'Dispensado' => 'secondary'
                            ][$att->status] ?? 'secondary';
                            
                            $dayOfWeek = date('l', strtotime($att->attendance_date));
                            $days = [
                                'Sunday' => 'Domingo',
                                'Monday' => 'Segunda-feira',
                                'Tuesday' => 'Terça-feira',
                                'Wednesday' => 'Quarta-feira',
                                'Thursday' => 'Quinta-feira',
                                'Friday' => 'Sexta-feira',
                                'Saturday' => 'Sábado'
                            ];
                            $dayName = $days[$dayOfWeek] ?? $dayOfWeek;
                        ?>
                            <tr>
                                <td>
                                    <span class="fw-bold"><?= date('d/m/Y', strtotime($att->attendance_date)) ?></span>
                                    <?php if ($att->attendance_date == date('Y-m-d')): ?>
                                        <span class="badge bg-success ms-1">Hoje</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $dayName ?></td>
                                <td>
                                    <?php if ($att->discipline_name): ?>
                                        <span class="badge bg-info"><?= $att->discipline_name ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Geral</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $statusClass ?> p-2">
                                        <i class="fas fa-<?= $att->status == 'Presente' ? 'check-circle' : 
                                            ($att->status == 'Atrasado' ? 'clock' : 
                                            ($att->status == 'Falta Justificada' ? 'file-alt' : 'times-circle')) ?> me-1"></i>
                                        <?= $att->status ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $att->justification ?: '-' ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#attendanceModal<?= $att->id ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
<?php else: ?>
    <div class="alert alert-info text-center py-5">
        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">Nenhum registro de presença encontrado</h5>
        <p class="text-muted">Não há registros de presença para os filtros selecionados.</p>
        <a href="<?= site_url('students/attendance') ?>" class="btn btn-primary mt-3">
            <i class="fas fa-calendar-alt"></i> Ver Presenças Mensais
        </a>
    </div>
<?php endif; ?>

<!-- Modais de Detalhes -->
<?php if (!empty($attendances)): ?>
    <?php foreach ($attendances as $att): ?>
        <div class="modal fade" id="attendanceModal<?= $att->id ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-info-circle me-2"></i>Detalhes da Presença
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 40%">Data</th>
                                <td><?= date('d/m/Y', strtotime($att->attendance_date)) ?> (<?= $dayName ?>)</td>
                            </tr>
                            <tr>
                                <th>Disciplina</th>
                                <td><?= $att->discipline_name ?? 'Geral' ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-<?= $statusClass ?> p-2">
                                        <i class="fas fa-<?= $att->status == 'Presente' ? 'check-circle' : 
                                            ($att->status == 'Atrasado' ? 'clock' : 
                                            ($att->status == 'Falta Justificada' ? 'file-alt' : 'times-circle')) ?> me-1"></i>
                                        <?= $att->status ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Justificação</th>
                                <td><?= $att->justification ?: 'Nenhuma justificação fornecida' ?></td>
                            </tr>
                            <tr>
                                <th>Registrado por</th>
                                <td>Professor</td>
                            </tr>
                            <tr>
                                <th>Data do Registro</th>
                                <td><?= date('d/m/Y H:i', strtotime($att->created_at)) ?></td>
                            </tr>
                        </table>
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
    $('#attendanceTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true
    });
});

// Auto-submit quando filtros mudarem
let filterTimeout;
$('#filterForm select').change(function() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => $('#filterForm').submit(), 500);
});

// Exportar para Excel
function exportToExcel() {
    const table = document.getElementById('attendanceTable');
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
    link.setAttribute('download', 'historico_presencas_' + new Date().toISOString().split('T')[0] + '.csv');
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

.progress {
    background-color: #f0f0f0;
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.5s ease;
}

/* Estilo para impressão */
@media print {
    .btn, .page-header .btn, .breadcrumb, .navbar, footer, .modal-footer, .card-header .btn {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .table {
        border: 1px solid #ddd !important;
    }
}
</style>
<?= $this->endSection() ?>