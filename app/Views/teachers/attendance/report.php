<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <button onclick="window.print()" class="btn btn-success">
            <i class="fas fa-print"></i> Imprimir
        </button>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/attendance') ?>">Presenças</a></li>
            <li class="breadcrumb-item active" aria-current="page">Relatório</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <label for="class" class="form-label">Turma</label>
                <select class="form-select" id="class" name="class">
                    <option value="">Todas</option>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class->id ?>" <?= $selectedClass == $class->id ? 'selected' : '' ?>>
                                <?= $class->class_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="discipline" class="form-label">Disciplina</label>
                <select class="form-select" id="discipline" name="discipline">
                    <option value="">Todas</option>
                    <?php if (!empty($disciplines)): ?>
                        <?php foreach ($disciplines as $disc): ?>
                            <option value="<?= $disc->id ?>" <?= $selectedDiscipline == $disc->id ? 'selected' : '' ?>>
                                <?= $disc->discipline_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="start_date" class="form-label">Data Início</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $startDate ?>">
            </div>
            
            <div class="col-md-2">
                <label for="end_date" class="form-label">Data Fim</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $endDate ?>">
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($report) && !empty($report)): ?>
    <!-- Summary Statistics -->
    <div class="row mb-4">
        <?php
        $totalPresent = array_sum(array_column($report, 'present'));
        $totalAbsent = array_sum(array_column($report, 'absent'));
        $totalLate = array_sum(array_column($report, 'late'));
        $totalJustified = array_sum(array_column($report, 'justified'));
        $totalRecords = $totalPresent + $totalAbsent + $totalLate + $totalJustified;
        $attendanceRate = $totalRecords > 0 ? round(($totalPresent / $totalRecords) * 100, 1) : 0;
        ?>
        
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Taxa de Presença</h6>
                    <h3><?= $attendanceRate ?>%</h3>
                    <small>Total: <?= $totalRecords ?> registos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Presentes</h6>
                    <h3><?= $totalPresent ?></h3>
                    <small><?= $totalRecords > 0 ? round(($totalPresent / $totalRecords) * 100, 1) : 0 ?>%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">Ausentes</h6>
                    <h3><?= $totalAbsent ?></h3>
                    <small><?= $totalRecords > 0 ? round(($totalAbsent / $totalRecords) * 100, 1) : 0 ?>%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="card-title">Atrasados</h6>
                    <h3><?= $totalLate ?></h3>
                    <small><?= $totalRecords > 0 ? round(($totalLate / $totalRecords) * 100, 1) : 0 ?>%</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Report Table -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-table"></i> Relatório de Presenças
            <span class="float-end">
                <?php if ($selectedClass): ?>
                    <span class="badge bg-info">Turma selecionada</span>
                <?php endif; ?>
            </span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nº Matrícula</th>
                            <th>Aluno</th>
                            <th class="text-center">Presente</th>
                            <th class="text-center">Ausente</th>
                            <th class="text-center">Atrasado</th>
                            <th class="text-center">Justificado</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Taxa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report as $row): 
                            $total = $row->present + $row->absent + $row->late + $row->justified;
                            $rate = $total > 0 ? round(($row->present / $total) * 100, 1) : 0;
                            $rateClass = $rate >= 75 ? 'success' : ($rate >= 50 ? 'warning' : 'danger');
                        ?>
                            <tr>
                                <td><?= $row->student_number ?></td>
                                <td><?= $row->first_name ?> <?= $row->last_name ?></td>
                                <td class="text-center text-success fw-bold"><?= $row->present ?></td>
                                <td class="text-center text-danger fw-bold"><?= $row->absent ?></td>
                                <td class="text-center text-warning fw-bold"><?= $row->late ?></td>
                                <td class="text-center text-info fw-bold"><?= $row->justified ?></td>
                                <td class="text-center fw-bold"><?= $total ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $rateClass ?>"><?= $rate ?>%</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer text-muted">
            <i class="fas fa-clock"></i> Relatório gerado em <?= date('d/m/Y H:i') ?> | 
            <i class="fas fa-calendar"></i> Período: <?= date('d/m/Y', strtotime($startDate)) ?> a <?= date('d/m/Y', strtotime($endDate)) ?>
        </div>
    </div>
    
<?php elseif ($selectedClass): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> Nenhum dado encontrado para o período selecionado.
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Selecione uma turma para gerar o relatório.
    </div>
<?php endif; ?>

<style media="print">
    .btn, .page-header .btn, .breadcrumb, .navbar, .sidebar, footer, form {
        display: none !important;
    }
    .content {
        margin-left: 0 !important;
    }
    .main-content {
        padding: 0 !important;
    }
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
    .table th {
        background-color: #f2f2f2 !important;
    }
    @page {
        size: landscape;
    }
</style>

<?= $this->endSection() ?>