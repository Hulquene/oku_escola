<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
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

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Exames</h6>
                <h3><?= $total ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Aprovados</h6>
                <h3><?= $approved ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title">Reprovados</h6>
                <h3><?= $failed ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Média</h6>
                <h3><?= number_format($average, 1) ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Results Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-star"></i> Resultados por Exame
    </div>
    <div class="card-body">
        <?php if (!empty($results)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Exame</th>
                            <th>Disciplina</th>
                            <th>Tipo</th>
                            <th class="text-center">Nota</th>
                            <th class="text-center">Percentagem</th>
                            <th class="text-center">Classificação</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $result): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($result->exam_date)) ?></td>
                                <td><?= $result->exam_name ?></td>
                                <td><?= $result->discipline_name ?></td>
                                <td><span class="badge bg-info"><?= $result->board_type ?></span></td>
                                <td class="text-center fw-bold"><?= number_format($result->score, 1) ?></td>
                                <td class="text-center"><?= number_format($result->score_percentage, 1) ?>%</td>
                                <td class="text-center"><?= $result->grade ?: '-' ?></td>
                                <td class="text-center">
                                    <?php if ($result->score >= 10): ?>
                                        <span class="badge bg-success">Aprovado</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Reprovado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Nenhum resultado encontrado para este semestre.</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>