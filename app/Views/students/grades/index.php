<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('students/grades/report-card') ?>" class="btn btn-primary">
            <i class="fas fa-file-alt"></i> Ver Boletim
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Minhas Notas</li>
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
            <div class="col-md-8">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> 
                    Média Geral: <strong><?= number_format($average, 1) ?></strong>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Final Grades -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-star"></i> Notas Finais
    </div>
    <div class="card-body">
        <?php if (!empty($finalGrades)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Disciplina</th>
                            <th class="text-center">Média</th>
                            <th class="text-center">Exame</th>
                            <th class="text-center">Nota Final</th>
                            <th class="text-center">Status</th>
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
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Nenhuma nota final encontrada para este semestre.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Exam Results -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-pencil-alt"></i> Resultados de Exames
    </div>
    <div class="card-body">
        <?php if (!empty($examResults)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Disciplina</th>
                            <th>Exame</th>
                            <th>Tipo</th>
                            <th>Data</th>
                            <th class="text-center">Nota</th>
                            <th class="text-center">Percentagem</th>
                            <th class="text-center">Classificação</th>
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
        <?php else: ?>
            <p class="text-muted text-center">Nenhum resultado de exame encontrado para este semestre.</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>