<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Lançar Notas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="class" class="form-label">Turma</label>
                <select class="form-select" id="class" name="class" required>
                    <option value="">Selecione...</option>
                    <?php
                    $teacherId = session()->get('user_id');
                    $classDisciplineModel = new \App\Models\ClassDisciplineModel();
                    $classes = $classDisciplineModel
                        ->select('tbl_classes.id, tbl_classes.class_name, tbl_classes.class_code')
                        ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
                        ->where('tbl_class_disciplines.teacher_id', $teacherId)
                        ->distinct()
                        ->findAll();
                    ?>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class->id ?>" <?= $this->request->getGet('class') == $class->id ? 'selected' : '' ?>>
                                <?= $class->class_name ?> (<?= $class->class_code ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-4">
                <label for="discipline" class="form-label">Disciplina</label>
                <select class="form-select" id="discipline" name="discipline" required>
                    <option value="">Selecione...</option>
                </select>
            </div>
            
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-search"></i> Carregar Alunos
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($this->request->getGet('class') && $this->request->getGet('discipline')): ?>
    <?php
    $enrollmentModel = new \App\Models\EnrollmentModel();
    $students = $enrollmentModel
        ->select('tbl_enrollments.id as enrollment_id, tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->where('tbl_enrollments.class_id', $this->request->getGet('class'))
        ->where('tbl_enrollments.status', 'Ativo')
        ->orderBy('tbl_users.first_name', 'ASC')
        ->findAll();
    
    $continuousModel = new \App\Models\ContinuousAssessmentModel();
    $semesterModel = new \App\Models\SemesterModel();
    $currentSemester = $semesterModel->getCurrent();
    ?>
    
    <?php if (!empty($students)): ?>
        <div class="card">
            <div class="card-header">
                <i class="fas fa-star"></i> Avaliações Contínuas
            </div>
            <div class="card-body">
                <form action="<?= site_url('teachers/grades/save') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="class_id" value="<?= $this->request->getGet('class') ?>">
                    <input type="hidden" name="discipline_id" value="<?= $this->request->getGet('discipline') ?>">
                    <input type="hidden" name="semester_id" value="<?= $currentSemester->id ?? '' ?>">
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nº Matrícula</th>
                                    <th>Aluno</th>
                                    <th>AC1</th>
                                    <th>AC2</th>
                                    <th>AC3</th>
                                    <th>Média</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <?php
                                    $ac1 = $continuousModel
                                        ->where('enrollment_id', $student->enrollment_id)
                                        ->where('discipline_id', $this->request->getGet('discipline'))
                                        ->where('assessment_type', 'AC1')
                                        ->first();
                                    
                                    $ac2 = $continuousModel
                                        ->where('enrollment_id', $student->enrollment_id)
                                        ->where('discipline_id', $this->request->getGet('discipline'))
                                        ->where('assessment_type', 'AC2')
                                        ->first();
                                    
                                    $ac3 = $continuousModel
                                        ->where('enrollment_id', $student->enrollment_id)
                                        ->where('discipline_id', $this->request->getGet('discipline'))
                                        ->where('assessment_type', 'AC3')
                                        ->first();
                                    
                                    $scores = [
                                        $ac1 ? $ac1->score : null,
                                        $ac2 ? $ac2->score : null,
                                        $ac3 ? $ac3->score : null
                                    ];
                                    
                                    $validScores = array_filter($scores);
                                    $average = !empty($validScores) ? array_sum($validScores) / count($validScores) : 0;
                                    ?>
                                    
                                    <tr>
                                        <td><?= $student->student_number ?></td>
                                        <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                                        <td>
                                            <input type="number" class="form-control" name="ac1[<?= $student->enrollment_id ?>]" 
                                                   value="<?= $ac1 ? $ac1->score : '' ?>" step="0.1" min="0" max="20">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="ac2[<?= $student->enrollment_id ?>]" 
                                                   value="<?= $ac2 ? $ac2->score : '' ?>" step="0.1" min="0" max="20">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="ac3[<?= $student->enrollment_id ?>]" 
                                                   value="<?= $ac3 ? $ac3->score : '' ?>" step="0.1" min="0" max="20">
                                        </td>
                                        <td class="fw-bold"><?= number_format($average, 1) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <hr>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Salvar Avaliações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Load disciplines based on selected class
document.getElementById('class').addEventListener('change', function() {
    const classId = this.value;
    const disciplineSelect = document.getElementById('discipline');
    
    if (classId) {
        fetch(`<?= site_url('teachers/exams/get-disciplines/') ?>/${classId}`)
            .then(response => response.json())
            .then(data => {
                disciplineSelect.innerHTML = '<option value="">Selecione...</option>';
                data.forEach(discipline => {
                    disciplineSelect.innerHTML += `<option value="${discipline.id}">${discipline.discipline_name}</option>`;
                });
            });
    } else {
        disciplineSelect.innerHTML = '<option value="">Selecione...</option>';
    }
});
</script>
<?= $this->endSection() ?>