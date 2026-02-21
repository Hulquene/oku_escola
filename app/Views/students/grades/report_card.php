<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1><?= $title ?? 'Boletim de Notas' ?></h1>
        <div>
            <button onclick="window.print()" class="btn btn-success">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <a href="<?= site_url('students/grades') ?>" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Voltar para Notas
            </a>
        </div>
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

<!-- Semester Filter -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <i class="fas fa-filter"></i> Selecionar Período
    </div>
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="semester" class="form-label">Semestre/Trimestre</label>
                <select class="form-select" id="semester" name="semester" onchange="this.form.submit()">
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
                    <i class="fas fa-info-circle"></i> 
                    Selecione o período para visualizar o boletim correspondente.
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Report Card -->
<?php if (isset($reportCard) && $reportCard): ?>
<div class="card" id="report-card">
    <div class="card-header bg-primary text-white text-center py-3">
        <h3 class="mb-0"><?= $reportCard->school_name ?? 'Boletim de Notas' ?></h3>
        <small><?= $reportCard->school_address ?? '' ?></small>
    </div>
    
    <div class="card-body">
        <!-- Cabeçalho do Boletim -->
        <div class="row mb-4">
            <div class="col-12 text-center mb-3">
                <h4><?= $reportCard->semester_name ?? 'Boletim do Período' ?></h4>
                <h5>Ano Letivo: <?= $reportCard->year_name ?? date('Y') ?></h5>
            </div>
        </div>
        
        <!-- Informações do Aluno -->
        <div class="row mb-4">
            <div class="col-md-8">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th style="width: 150px;">Nome do Aluno:</th>
                        <td><strong><?= esc($reportCard->first_name ?? '') ?> <?= esc($reportCard->last_name ?? '') ?></strong></td>
                    </tr>
                    <tr>
                        <th>Nº de Matrícula:</th>
                        <td><?= esc($reportCard->student_number ?? '') ?></td>
                    </tr>
                    <tr>
                        <th>Turma:</th>
                        <td><?= esc($reportCard->class_name ?? '') ?></td>
                    </tr>
                    <tr>
                        <th>Período:</th>
                        <td><?= esc($reportCard->semester_name ?? $reportCard->period_name ?? '') ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th>Data de Emissão:</th>
                        <td><?= isset($reportCard->generated_date) ? date('d/m/Y', strtotime($reportCard->generated_date)) : date('d/m/Y') ?></td>
                    </tr>
                    <tr>
                        <th>Nº do Boletim:</th>
                        <td><?= esc($reportCard->report_number ?? '') ?></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <span class="badge bg-<?= ($reportCard->status ?? 'Emitido') == 'Emitido' ? 'success' : 'warning' ?>">
                                <?= $reportCard->status ?? 'Emitido' ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Tabela de Notas -->
        <?php if (!empty($averages ?? $grades)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Disciplina</th>
                            <th class="text-center">Média das Avaliações</th>
                            <th class="text-center">Nota do Exame</th>
                            <th class="text-center">Nota Final</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalScore = 0;
                        $countDisciplines = 0;
                        $approvedCount = 0;
                        
                        // Usar averages se disponível, senão usar grades
                        $items = $averages ?? $grades ?? [];
                        foreach ($items as $item): 
                            $finalScore = $item->final_score ?? $item->final_score ?? 0;
                            $examScore = $item->exam_score ?? '-';
                            $avgScore = $item->ac_score ?? $item->average_score ?? 0;
                            $status = $item->status ?? ($finalScore >= 10 ? 'Aprovado' : 'Reprovado');
                            
                            $totalScore += $finalScore;
                            $countDisciplines++;
                            if ($status == 'Aprovado') $approvedCount++;
                        ?>
                            <tr>
                                <td><?= esc($item->discipline_name ?? 'N/A') ?></td>
                                <td class="text-center"><?= is_numeric($avgScore) ? number_format($avgScore, 1, ',', '.') : '-' ?></td>
                                <td class="text-center"><?= is_numeric($examScore) ? number_format($examScore, 1, ',', '.') : '-' ?></td>
                                <td class="text-center fw-bold <?= $finalScore >= 10 ? 'text-success' : 'text-danger' ?>">
                                    <?= number_format($finalScore, 1, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $statusClass = [
                                        'Aprovado' => 'success',
                                        'Reprovado' => 'danger',
                                        'Recurso' => 'warning',
                                        'Dispensado' => 'info',
                                        'Em Andamento' => 'secondary'
                                    ][$status] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>"><?= $status ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">Média Geral:</th>
                            <th class="text-center">
                                <?= $countDisciplines > 0 ? number_format($totalScore / $countDisciplines, 1, ',', '.') : '-' ?>
                            </th>
                            <th class="text-center">
                                <span class="badge bg-info">
                                    Aproveitamento: <?= $countDisciplines > 0 ? round(($approvedCount / $countDisciplines) * 100, 1) : 0 ?>%
                                </span>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                Nenhuma nota encontrada para este período.
            </div>
        <?php endif; ?>
        
        <!-- Informações de Presença -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <i class="fas fa-calendar-check"></i> Frequência
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-1">Total de Faltas:</p>
                                <h4><?= $reportCard->total_absences ?? 0 ?></h4>
                            </div>
                            <div class="col-6">
                                <p class="mb-1">Faltas Justificadas:</p>
                                <h4><?= $reportCard->justified_absences ?? 0 ?></h4>
                            </div>
                        </div>
                        <?php 
                        $totalClasses = ($reportCard->total_absences ?? 0) + ($reportCard->justified_absences ?? 0);
                        if ($totalClasses > 0):
                            $presenceRate = 100 - ((($reportCard->total_absences ?? 0) / $totalClasses) * 100);
                        ?>
                            <div class="mt-3">
                                <p class="mb-1">Taxa de Presença:</p>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?= $presenceRate ?>%;" 
                                         aria-valuenow="<?= $presenceRate ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?= round($presenceRate, 1) ?>%
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <i class="fas fa-chart-pie"></i> Legenda
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><span class="badge bg-success">Aprovado</span> - Nota final ≥ 10 valores</li>
                            <li><span class="badge bg-warning">Recurso</span> - Nota final entre 7 e 9 valores</li>
                            <li><span class="badge bg-danger">Reprovado</span> - Nota final < 7 valores</li>
                            <li><span class="badge bg-info">Dispensado</span> - Dispensado da disciplina</li>
                            <li><span class="badge bg-secondary">Em Andamento</span> - Avaliações em curso</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Observações -->
        <?php if ($reportCard->observations ?? false): ?>
        <div class="mt-4">
            <div class="alert alert-info">
                <strong>Observações:</strong><br>
                <?= nl2br(esc($reportCard->observations)) ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Assinaturas -->
        <div class="row mt-5">
            <div class="col-4 text-center">
                <hr class="w-75 mx-auto">
                <p>Professor(a) da Turma</p>
            </div>
            <div class="col-4 text-center">
                <hr class="w-75 mx-auto">
                <p>Diretor(a) Pedagógico(a)</p>
            </div>
            <div class="col-4 text-center">
                <hr class="w-75 mx-auto">
                <p>Encarregado de Educação</p>
            </div>
        </div>
    </div>
    
    <div class="card-footer text-muted text-center">
        <small>Documento gerado em <?= date('d/m/Y H:i:s') ?> - Válido para todos os efeitos legais</small>
    </div>
</div>
<?php else: ?>
<div class="alert alert-warning text-center py-5">
    <i class="fas fa-exclamation-triangle fa-4x mb-3 text-warning"></i>
    <h4>Boletim não disponível</h4>
    <p class="mb-3">Não há boletim disponível para o período selecionado.</p>
    <?php if (empty($semesters)): ?>
        <p class="small">Não há períodos letivos configurados.</p>
    <?php else: ?>
        <p class="small">Selecione outro período ou aguarde a publicação das notas.</p>
    <?php endif; ?>
    <a href="<?= site_url('students/grades') ?>" class="btn btn-primary mt-3">
        <i class="fas fa-arrow-left"></i> Voltar para Notas
    </a>
</div>
<?php endif; ?>

<style media="print">
    @page {
        size: A4;
        margin: 2cm;
    }
    
    body {
        background: white;
        font-size: 12pt;
    }
    
    .btn, .page-header .btn, .breadcrumb, .navbar, .sidebar, footer,
    .card-header .btn, .btn-group, form, .alert {
        display: none !important;
    }
    
    .content, .main-content, .card {
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
    }
    
    #report-card {
        border: 1px solid #ddd !important;
        padding: 1cm !important;
    }
    
    .table {
        border-collapse: collapse;
        width: 100%;
    }
    
    .table th, .table td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    
    .badge {
        border: 1px solid #000;
        padding: 2px 5px;
        font-size: 10pt;
    }
</style>

<?= $this->endSection() ?>