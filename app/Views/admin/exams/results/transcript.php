<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Histórico Escolar</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-history me-1"></i>
                <?= $student->first_name ?> <?= $student->last_name ?>
            </p>
        </div>
        <div class="d-flex gap-2">
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
            <li class="breadcrumb-item active">Histórico Escolar</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações do Aluno -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4 class="mb-3"><?= $student->first_name ?> <?= $student->last_name ?></h4>
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-1"><strong>Nº de Matrícula:</strong></p>
                        <p class="mb-1"><strong>Data de Nascimento:</strong></p>
                        <p class="mb-1"><strong>BI/Passaporte:</strong></p>
                    </div>
                    <div class="col-md-8">
                        <p class="mb-1"><?= $student->student_number ?></p>
                        <p class="mb-1"><?= date('d/m/Y', strtotime($student->birth_date)) ?></p>
                        <p class="mb-1"><?= $student->identity_document ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <p><strong>Data de Emissão:</strong> <?= date('d/m/Y') ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Histórico por Ano Letivo -->
<?php if (!empty($transcript)): ?>
    <?php foreach ($transcript as $yearData): ?>
        <?php $year = $yearData['year']; ?>
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i>
                    Ano Letivo: <?= $year->year_name ?> • <?= $year->class_name ?>
                </h5>
            </div>
            <div class="card-body p-0">
                <?php foreach ($yearData['semesters'] as $semData): ?>
                    <?php $semester = $semData['semester']; ?>
                    <div class="p-3 <?= !$loop->last ? 'border-bottom' : '' ?>">
                        <h6 class="fw-semibold mb-3"><?= $semester->semester_name ?> (<?= $semester->semester_type ?>)</h6>
                        
                        <?php if (!empty($semData['averages'])): ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Disciplina</th>
                                            <th class="text-center" width="100">Nota Final</th>
                                            <th class="text-center" width="150">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($semData['averages'] as $avg): ?>
                                            <tr>
                                                <td><?= $avg->discipline_name ?></td>
                                                <td class="text-center fw-bold <?= $avg->final_score >= 10 ? 'text-success' : ($avg->final_score >= 7 ? 'text-warning' : 'text-danger') ?>">
                                                    <?= number_format($avg->final_score, 1) ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-<?= $avg->status == 'Aprovado' ? 'success' : ($avg->status == 'Recurso' ? 'warning' : 'danger') ?>">
                                                        <?= $avg->status ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <?php if ($semData['result']): ?>
                                        <tfoot class="bg-light">
                                            <tr>
                                                <td colspan="2" class="text-end"><strong>Média do Semestre:</strong></td>
                                                <td class="text-center">
                                                    <span class="fw-bold <?= $semData['result']->overall_average >= 10 ? 'text-success' : 'text-warning' ?>">
                                                        <?= number_format($semData['result']->overall_average, 2) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    <?php endif; ?>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center py-3">Nenhuma nota registada para este semestre</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
                <?php if ($yearData['yearly_result']): ?>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Resultado Anual:</span>
                            <span class="badge bg-<?= $yearData['yearly_result']->final_status == 'Aprovado' ? 'success' : 'danger' ?> p-3">
                                <?= $yearData['yearly_result']->final_status ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Nenhum histórico encontrado para este aluno.
    </div>
<?php endif; ?>

<style>
@media print {
    .page-header, .btn, .breadcrumb, .card-header .btn {
        display: none !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }
    
    table {
        font-size: 10pt;
    }
}
</style>

<?= $this->endSection() ?>