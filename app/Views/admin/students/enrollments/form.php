<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students/enrollments') ?>">Matrículas</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-<?= $enrollment ? 'edit' : 'plus-circle' ?>"></i> <?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/students/enrollments/save') ?>" method="post">
            <?= csrf_field() ?>
            
            <?php if ($enrollment): ?>
                <input type="hidden" name="id" value="<?= $enrollment->id ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Aluno <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.student_id') ? 'is-invalid' : '' ?>" 
                                id="student_id" 
                                name="student_id" 
                                required>
                            <option value="">Selecione o aluno...</option>
                            <?php if (!empty($students)): ?>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?= $student->id ?>" 
                                        <?= (old('student_id', $enrollment->student_id ?? $selectedStudent ?? '') == $student->id) ? 'selected' : '' ?>>
                                        <?= $student->first_name ?> <?= $student->last_name ?> (<?= $student->student_number ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.student_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.student_id') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="class_id" class="form-label">Turma <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.class_id') ? 'is-invalid' : '' ?>" 
                                id="class_id" 
                                name="class_id" 
                                required>
                            <option value="">Selecione a turma...</option>
                            <?php if (!empty($classes)): ?>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class->id ?>" 
                                        data-capacity="<?= $class->capacity ?>"
                                        data-level="<?= $class->level_name ?>"
                                        <?= (old('class_id', $enrollment->class_id ?? $selectedClass ?? '') == $class->id) ? 'selected' : '' ?>>
                                        <?= $class->class_name ?> (<?= $class->class_code ?>) - <?= $class->class_shift ?> - <?= $class->level_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.class_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.class_id') ?></div>
                        <?php endif; ?>
                        <small class="text-muted" id="classInfo"></small>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="academic_year_id" class="form-label">Ano Letivo <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.academic_year_id') ? 'is-invalid' : '' ?>" 
                                id="academic_year_id" 
                                name="academic_year_id" 
                                required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($academicYears)): ?>
                                <?php foreach ($academicYears as $year): ?>
                                    <option value="<?= $year->id ?>" 
                                        <?= (old('academic_year_id', $enrollment->academic_year_id ?? '') == $year->id) ? 'selected' : '' ?>>
                                        <?= $year->year_name ?> (<?= date('Y', strtotime($year->start_date)) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.academic_year_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.academic_year_id') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="enrollment_date" class="form-label">Data da Matrícula <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control <?= session('errors.enrollment_date') ? 'is-invalid' : '' ?>" 
                               id="enrollment_date" 
                               name="enrollment_date" 
                               value="<?= old('enrollment_date', $enrollment->enrollment_date ?? date('Y-m-d')) ?>"
                               required>
                        <?php if (session('errors.enrollment_date')): ?>
                            <div class="invalid-feedback"><?= session('errors.enrollment_date') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="enrollment_type" class="form-label">Tipo de Matrícula <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.enrollment_type') ? 'is-invalid' : '' ?>" 
                                id="enrollment_type" 
                                name="enrollment_type" 
                                required>
                            <option value="">Selecione...</option>
                            <option value="Nova" <?= old('enrollment_type', $enrollment->enrollment_type ?? '') == 'Nova' ? 'selected' : '' ?>>Nova</option>
                            <option value="Renovação" <?= old('enrollment_type', $enrollment->enrollment_type ?? '') == 'Renovação' ? 'selected' : '' ?>>Renovação</option>
                            <option value="Transferência" <?= old('enrollment_type', $enrollment->enrollment_type ?? '') == 'Transferência' ? 'selected' : '' ?>>Transferência</option>
                        </select>
                        <?php if (session('errors.enrollment_type')): ?>
                            <div class="invalid-feedback"><?= session('errors.enrollment_type') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="previous_class_id" class="form-label">Classe Anterior</label>
                        <select class="form-select" id="previous_class_id" name="previous_class_id">
                            <option value="">Selecione...</option>
                            <?php if (!empty($classes)): ?>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class->id ?>" 
                                        <?= (old('previous_class_id', $enrollment->previous_class_id ?? '') == $class->id) ? 'selected' : '' ?>>
                                        <?= $class->class_name ?> (<?= $class->year_name ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="Pendente" <?= old('status', $enrollment->status ?? '') == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                            <option value="Ativo" <?= old('status', $enrollment->status ?? '') == 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                            <option value="Concluído" <?= old('status', $enrollment->status ?? '') == 'Concluído' ? 'selected' : '' ?>>Concluído</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="observations" class="form-label">Observações</label>
                <textarea class="form-control" id="observations" name="observations" rows="3"><?= old('observations', $enrollment->observations ?? '') ?></textarea>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/students/enrollments') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Matrícula
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('class_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (selected.value) {
        const capacity = selected.dataset.capacity;
        const level = selected.dataset.level;
        document.getElementById('classInfo').textContent = `Capacidade: ${capacity} alunos | Nível: ${level}`;
    } else {
        document.getElementById('classInfo').textContent = '';
    }
});

// Verificar disponibilidade de vagas
document.getElementById('class_id').addEventListener('change', function() {
    const classId = this.value;
    if (classId) {
        fetch(`<?= site_url('admin/classes/check-availability/') ?>/${classId}`)
            .then(response => response.json())
            .then(data => {
                if (data.available <= 0) {
                    alert('Atenção: Esta turma não possui vagas disponíveis!');
                }
            });
    }
});
</script>
<?= $this->endSection() ?>