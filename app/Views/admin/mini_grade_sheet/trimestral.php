<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Pautas Trimestrais</h1>
        <a href="<?= site_url('admin/mini-grade-sheet') ?>" class="btn btn-info">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/mini-grade-sheet') ?>">Mini Pautas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pautas Trimestrais</li>
        </ol>
    </nav>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Selecionar Período</h5>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/mini-grade-sheet/trimestral') ?>" method="get">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ano Letivo</label>
                    <select name="academic_year" class="form-select" id="academicYear">
                        <option value="">Selecione</option>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= $year->id ?>" <?= ($selectedYear ?? '') == $year->id ? 'selected' : '' ?>>
                                <?= $year->year_name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Trimestre/Semestre</label>
                    <select name="semester" class="form-select" id="semester">
                        <option value="">Selecione</option>
                        <?php foreach ($semesters as $sem): ?>
                            <option value="<?= $sem->id ?>" <?= ($selectedSemester ?? '') == $sem->id ? 'selected' : '' ?>>
                                <?= $sem->semester_name ?> (<?= date('d/m/Y', strtotime($sem->start_date)) ?> - <?= date('d/m/Y', strtotime($sem->end_date)) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Turma</label>
                    <select name="class" class="form-select" id="class">
                        <option value="">Selecione</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class->id ?>" <?= ($selectedClass ?? '') == $class->id ? 'selected' : '' ?>>
                                <?= $class->class_name ?> (<?= $class->year_name ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Visualizar Pauta
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($selectedClass && $selectedSemester): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Selecione uma turma e trimestre para visualizar a pauta.
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<script>
document.getElementById('academicYear').addEventListener('change', function() {
    const yearId = this.value;
    const semesterSelect = document.getElementById('semester');
    
    if (yearId) {
        fetch('<?= site_url('admin/academic/getSemesters/') ?>' + yearId)
            .then(response => response.json())
            .then(data => {
                semesterSelect.innerHTML = '<option value="">Selecione</option>';
                data.forEach(sem => {
                    semesterSelect.innerHTML += `<option value="${sem.id}">${sem.semester_name}</option>`;
                });
            });
    }
});
</script>