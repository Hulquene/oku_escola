<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/reports') ?>">Relatórios</a></li>
            <li class="breadcrumb-item active" aria-current="page">Exames</li>
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
                <label for="exam" class="form-label">Exame</label>
                <select class="form-select" id="exam" name="exam">
                    <option value="">Todos</option>
                    <?php if (!empty($exams)): ?>
                        <?php foreach ($exams as $exam): ?>
                            <option value="<?= $exam->id ?>" <?= $selectedExam == $exam->id ? 'selected' : '' ?>>
                                <?= $exam->exam_name ?> (<?= date('d/m/Y', strtotime($exam->exam_date)) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-2">
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
            
            <div class="col-md-2">
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
            
            <div class="col-md-3">
                <label for="semester" class="form-label">Semestre</label>
                <select class="form-select" id="semester" name="semester">
                    <option value="">Todos</option>
                    <?php if (!empty($semesters)): ?>
                        <?php foreach ($semesters as $sem): ?>
                            <option value="<?= $sem->id ?>" <?= $selectedSemester == $sem->id ? 'selected' : '' ?>>
                                <?= $sem->semester_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/reports/exams') ?>" class="btn btn-secondary ms-2">
                    <i class="fas fa-undo"></i>
                </a>
                <button class="btn btn-success ms-2" onclick="window.print()">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <?php
    $totalStudents = array_sum(array_column($results, 'total_students'));
    $totalApproved = array_sum(array_column($results, 'approved'));
    $totalFailed = array_sum(array_column($results, 'failed'));
    $avgScore = count($results) > 0 ? array_sum(array_column($results, 'average_score')) / count($results) : 0;
    ?>
    
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Alunos</h6>
                <h2><?= $totalStudents ?></h2>
                <small>Avaliados</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Aprovados</h6>
                <h2><?= $totalApproved ?></h2>
                <small><?= $totalStudents > 0 ? round(($totalApproved / $totalStudents) * 100, 1) : 0 ?>%</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title">Reprovados</h6>
                <h2><?= $totalFailed ?></h2>
                <small><?= $totalStudents > 0 ? round(($totalFailed / $totalStudents) * 100, 1) : 0 ?>%</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Média Geral</h6>
                <h2><?= number_format($avgScore, 1) ?></h2>
                <small>valores</small>
            </div>
        </div>
    </div>
</div>

<!-- Results Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-table"></i> Resultados por Exame
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="examsTable">
                <thead>
                    <tr>
                        <th>Exame</th>
                        <th>Turma</th>
                        <th>Disciplina</th>
                        <th>Data</th>
                        <th class="text-center">Alunos</th>
                        <th class="text-center">Média</th>
                        <th class="text-center">Mín</th>
                        <th class="text-center">Máx</th>
                        <th class="text-center">Aprovados</th>
                        <th class="text-center">Reprovados</th>
                        <th class="text-center">Taxa Aprov.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($results)): ?>
                        <?php foreach ($results as $row): ?>
                            <?php $approvalRate = $row->total_students > 0 ? round(($row->approved / $row->total_students) * 100, 1) : 0; ?>
                            <tr>
                                <td><?= $row->exam_name ?></td>
                                <td><?= $row->class_name ?></td>
                                <td><?= $row->discipline_name ?></td>
                                <td><?= date('d/m/Y', strtotime($row->exam_date)) ?></td>
                                <td class="text-center"><?= $row->total_students ?></td>
                                <td class="text-center"><?= number_format($row->average_score, 1) ?></td>
                                <td class="text-center"><?= number_format($row->min_score, 1) ?></td>
                                <td class="text-center"><?= number_format($row->max_score, 1) ?></td>
                                <td class="text-center text-success"><?= $row->approved ?></td>
                                <td class="text-center text-danger"><?= $row->failed ?></td>
                                <td class="text-center">
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: <?= $approvalRate ?>%" 
                                             aria-valuenow="<?= $approvalRate ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            <?= $approvalRate ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center">Nenhum resultado encontrado</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#examsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[3, 'desc']],
        pageLength: 25
    });
});
</script>
<?= $this->endSection() ?>