<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/reports') ?>">Relatórios</a></li>
            <li class="breadcrumb-item active" aria-current="page">Académico</li>
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
                <label for="academic_year" class="form-label">Ano Letivo</label>
                <select class="form-select" id="academic_year" name="academic_year" onchange="this.form.submit()">
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= $year->id ?>" <?= $selectedYear == $year->id ? 'selected' : '' ?>>
                                <?= $year->year_name ?>
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
            
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/reports/academic') ?>" class="btn btn-secondary ms-2">
                    <i class="fas fa-undo"></i>
                </a>
                <button class="btn btn-success ms-2" onclick="window.print()">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Aqui você pode adicionar os gráficos e tabelas académicas -->
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i>
    Selecione os filtros para gerar os relatórios académicos.
</div>

<?= $this->endSection() ?>