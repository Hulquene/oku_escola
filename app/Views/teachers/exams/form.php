<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-<?= isset($exam) ? 'edit' : 'plus-circle' ?>"></i> <?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= site_url('teachers/exams/save') ?>" method="post">
            <?= csrf_field() ?>
            
            <?php if (isset($exam) && $exam): ?>
                <input type="hidden" name="id" value="<?= $exam->id ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="exam_name" class="form-label">Nome do Exame <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="exam_name" name="exam_name" 
                               value="<?= old('exam_name', $exam->exam_name ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="exam_board_id" class="form-label">Tipo de Exame <span class="text-danger">*</span></label>
                        <select class="form-select" id="exam_board_id" name="exam_board_id" required>
                            <option value="">Selecione...</option>
                            <?php
                            $boardModel = new \App\Models\ExamBoardModel();
                            $boards = $boardModel->where('is_active', 1)->findAll();
                            ?>
                            <?php if (!empty($boards)): ?>
                                <?php foreach ($boards as $board): ?>
                                    <option value="<?= $board->id ?>" <?= old('exam_board_id', $exam->exam_board_id ?? '') == $board->id ? 'selected' : '' ?>>
                                        <?= $board->board_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="class_id" class="form-label">Turma <span class="text-danger">*</span></label>
                        <select class="form-select" id="class_id" name="class_id" required>
                            <option value="">Selecione...</option>
                            <?php
                            $teacherId = session()->get('user_id');
                            $classDisciplineModel = new \App\Models\ClassDisciplineModel();
                            $teacherClasses = $classDisciplineModel
                                ->select('tbl_classes.id, tbl_classes.class_name, tbl_classes.class_code')
                                ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
                                ->where('tbl_class_disciplines.teacher_id', $teacherId)
                                ->distinct()
                                ->findAll();
                            ?>
                            <?php if (!empty($teacherClasses)): ?>
                                <?php foreach ($teacherClasses as $class): ?>
                                    <option value="<?= $class->id ?>" <?= old('class_id', $exam->class_id ?? '') == $class->id ? 'selected' : '' ?>>
                                        <?= $class->class_name ?> (<?= $class->class_code ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="discipline_id" class="form-label">Disciplina <span class="text-danger">*</span></label>
                        <select class="form-select" id="discipline_id" name="discipline_id" required>
                            <option value="">Selecione...</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="exam_date" class="form-label">Data <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="exam_date" name="exam_date" 
                               value="<?= old('exam_date', $exam->exam_date ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="exam_time" class="form-label">Hora</label>
                        <input type="time" class="form-control" id="exam_time" name="exam_time" 
                               value="<?= old('exam_time', $exam->exam_time ?? '') ?>">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="exam_room" class="form-label">Sala</label>
                        <input type="text" class="form-control" id="exam_room" name="exam_room" 
                               value="<?= old('exam_room', $exam->exam_room ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="max_score" class="form-label">Valor Máximo</label>
                        <input type="number" class="form-control" id="max_score" name="max_score" 
                               step="0.01" min="0" value="<?= old('max_score', $exam->max_score ?? 20) ?>">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Descrição / Instruções</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', $exam->description ?? '') ?></textarea>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('teachers/exams') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Salvar Exame
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Load disciplines based on selected class
document.getElementById('class_id').addEventListener('change', function() {
    const classId = this.value;
    const disciplineSelect = document.getElementById('discipline_id');
    
    if (classId) {
        fetch(`<?= site_url('teachers/exams/get-disciplines/') ?>/${classId}`)
            .then(response => response.json())
            .then(data => {
                disciplineSelect.innerHTML = '<option value="">Selecione...</option>';
                data.forEach(discipline => {
                    disciplineSelect.innerHTML += `<option value="${discipline.id}" <?= (isset($exam) && $exam->discipline_id == ' + discipline.id + ') ? 'selected' : '' ?>>${discipline.discipline_name}</option>`;
                });
            });
    } else {
        disciplineSelect.innerHTML = '<option value="">Selecione...</option>';
    }
});

// Trigger change if editing
<?php if (isset($exam) && $exam): ?>
document.getElementById('class_id').dispatchEvent(new Event('change'));
<?php endif; ?>
</script>
<?= $this->endSection() ?>