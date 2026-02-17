<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/students/view/' . $student->id) ?>" class="btn btn-info">
                <i class="fas fa-user-graduate"></i> Ver Aluno
            </a>
            <button onclick="window.print()" class="btn btn-success">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/results') ?>">Resultados</a></li>
            <li class="breadcrumb-item active" aria-current="page">Boletim de Notas</li>
        </ol>
    </nav>
</div>

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
                                <?= $sem->semester_name ?> (<?= $sem->semester_type ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Report Card -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-file-alt"></i> Boletim de Notas
        <?php if ($semester): ?>
            <span class="float-end"><?= $semester->semester_name ?> - <?= $semester->semester_type ?></span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <!-- Student Info -->
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th style="width: 150px;">Nome:</th>
                        <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                    </tr>
                    <tr>
                        <th>Nº Matrícula:</th>
                        <td><?= $student->student_number ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th style="width: 150px;">Turma:</th>
                        <td><?= $enrollment->class_name ?? 'N/A' ?></td>
                    </tr>
                    <tr>
                        <th>Ano Letivo:</th>
                        <td><?= $enrollment->year_name ?? date('Y') ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Exam Results -->
        <?php if (!empty($examResults)): ?>
            <h5 class="mb-3">Resultados de Exames</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Disciplina</th>
                            <th>Exame</th>
                            <th>Tipo</th>
                            <th>Data</th>
                            <th>Nota</th>
                            <th>Percentagem</th>
                            <th>Classificação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $currentDiscipline = '';
                        foreach ($examResults as $result): 
                            if ($currentDiscipline != $result->discipline_name):
                                $currentDiscipline = $result->discipline_name;
                        ?>
                            <tr class="table-secondary">
                                <td colspan="7" class="fw-bold"><?= $result->discipline_name ?></td>
                            </tr>
                        <?php endif; ?>
                            <tr>
                                <td></td>
                                <td><?= $result->exam_name ?></td>
                                <td><span class="badge bg-info"><?= $result->board_type ?></span></td>
                                <td><?= date('d/m/Y', strtotime($result->exam_date)) ?></td>
                                <td class="text-center fw-bold"><?= number_format($result->score, 1) ?></td>
                                <td class="text-center"><?= number_format($result->score_percentage, 1) ?>%</td>
                                <td class="text-center"><?= $result->grade ?: '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <!-- Final Grades -->
        <?php if (!empty($finalGrades)): ?>
            <h5 class="mb-3">Notas Finais</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Disciplina</th>
                            <th>Média</th>
                            <th>Exame</th>
                            <th>Nota Final</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($finalGrades as $grade): ?>
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
                            <th class="text-center"><?= number_format($average, 1) ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
        
        <?php if (empty($examResults) && empty($finalGrades)): ?>
            <div class="text-center py-4">
                <i class="fas fa-file-excel fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhum resultado encontrado para este semestre.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="card-footer text-muted">
        <small>Boletim gerado em <?= date('d/m/Y H:i') ?></small>
    </div>
</div>

<style media="print">
    .btn, .page-header .btn, .breadcrumb, .card-header .float-end {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
</style>

<?= $this->endSection() ?>