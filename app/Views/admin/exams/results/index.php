<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active" aria-current="page">Resultados</li>
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
                        <?php foreach ($disciplines as $discipline): ?>
                            <option value="<?= $discipline->id ?>" <?= $selectedDiscipline == $discipline->id ? 'selected' : '' ?>>
                                <?= $discipline->discipline_name ?>
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
                        <?php foreach ($semesters as $semester): ?>
                            <option value="<?= $semester->id ?>" <?= $selectedSemester == $semester->id ? 'selected' : '' ?>>
                                <?= $semester->semester_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/exams/results') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Results Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-star"></i> Resultados de Exames
    </div>
    <div class="card-body">
        <table id="resultsTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Exame</th>
                    <th>Aluno</th>
                    <th>Turma</th>
                    <th>Disciplina</th>
                    <th>Nota</th>
                    <th>Percentagem</th>
                    <th>Classificação</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($results)): ?>
                    <?php foreach ($results as $result): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($result->exam_date)) ?></td>
                            <td><?= $result->exam_name ?></td>
                            <td><?= $result->first_name ?> <?= $result->last_name ?></td>
                            <td><?= $result->class_name ?></td>
                            <td><?= $result->discipline_name ?></td>
                            <td class="text-center fw-bold"><?= number_format($result->score, 1) ?></td>
                            <td class="text-center"><?= number_format($result->score_percentage, 1) ?>%</td>
                            <td class="text-center"><?= $result->grade ?: '-' ?></td>
                            <td>
                                <?php
                                $statusClass = $result->score >= 10 ? 'success' : 'danger';
                                $statusText = $result->score >= 10 ? 'Aprovado' : 'Reprovado';
                                ?>
                                <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                            </td>
                            <td>
                                <a href="<?= site_url('admin/exams/view/' . $result->exam_id) ?>" 
                                   class="btn btn-sm btn-info" title="Ver Exame">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= site_url('admin/students/view/' . $result->student_id) ?>" 
                                   class="btn btn-sm btn-success" title="Ver Aluno">
                                    <i class="fas fa-user-graduate"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">Nenhum resultado encontrado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?= $pager->links() ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#resultsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[0, 'desc']],
        pageLength: 25,
        searching: false
    });
});
</script>
<?= $this->endSection() ?>