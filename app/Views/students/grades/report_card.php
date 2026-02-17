<?= $this->extend('students/layouts/index') ?>

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
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('students/grades') ?>">Notas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Boletim</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Semester Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="semester" class="form-label">Semestre</label>
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
        </form>
    </div>
</div>

<!-- Report Card -->
<?php if (isset($reportCard) && $reportCard): ?>
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Boletim de Notas - <?= $reportCard->semester_name ?></h5>
    </div>
    <div class="card-body">
        <!-- Student Info -->
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th style="width: 150px;">Nome:</th>
                        <td><?= $reportCard->first_name ?> <?= $reportCard->last_name ?></td>
                    </tr>
                    <tr>
                        <th>Matrícula:</th>
                        <td><?= $reportCard->student_number ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th style="width: 150px;">Turma:</th>
                        <td><?= $reportCard->class_name ?></td>
                    </tr>
                    <tr>
                        <th>Ano Letivo:</th>
                        <td><?= $reportCard->year_name ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Grades Table -->
        <?php if (!empty($grades)): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Disciplina</th>
                            <th class="text-center">Média</th>
                            <th class="text-center">Exame</th>
                            <th class="text-center">Nota Final</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grades as $grade): ?>
                            <tr>
                                <td><?= $grade->discipline_name ?></td>
                                <td class="text-center"><?= number_format($grade->average_score, 1) ?></td>
                                <td class="text-center"><?= $grade->exam_score ? number_format($grade->exam_score, 1) : '-' ?></td>
                                <td class="text-center fw-bold"><?= number_format($grade->final_score, 1) ?></td>
                                <td class="text-center">
                                    <?php
                                    $statusClass = [
                                        'Aprovado' => 'success',
                                        'Reprovado' => 'danger',
                                        'Recurso' => 'warning',
                                        'Dispensado' => 'info'
                                    ][$grade->status] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>"><?= $grade->status ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">Média Geral:</th>
                            <th class="text-center"><?= number_format($reportCard->average_score, 1) ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Attendance -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <i class="fas fa-calendar-check"></i> Presenças
                        </div>
                        <div class="card-body">
                            <p class="mb-1">Total de Faltas: <strong><?= $reportCard->total_absences ?></strong></p>
                            <p class="mb-0">Faltas Justificadas: <strong><?= $reportCard->justified_absences ?></strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <i class="fas fa-info-circle"></i> Informações
                        </div>
                        <div class="card-body">
                            <p class="mb-1">Data de Emissão: <strong><?= date('d/m/Y', strtotime($reportCard->generated_date)) ?></strong></p>
                            <p class="mb-0">Nº do Boletim: <strong><?= $reportCard->report_number ?></strong></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Observations -->
            <?php if ($reportCard->observations): ?>
            <div class="mt-4">
                <div class="alert alert-info">
                    <strong>Observações:</strong><br>
                    <?= nl2br($reportCard->observations) ?>
                </div>
            </div>
            <?php endif; ?>
            
        <?php else: ?>
            <p class="text-muted text-center">Nenhuma nota encontrada para este semestre.</p>
        <?php endif; ?>
    </div>
    
    <div class="card-footer text-muted">
        <small>Boletim gerado em <?= date('d/m/Y H:i') ?></small>
    </div>
</div>
<?php else: ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle"></i> Nenhum boletim disponível para o período selecionado.
</div>
<?php endif; ?>

<style media="print">
    .btn, .page-header .btn, .breadcrumb, .navbar, .sidebar, footer {
        display: none !important;
    }
    .content {
        margin-left: 0 !important;
    }
    .main-content {
        padding: 0 !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
</style>

<?= $this->endSection() ?>