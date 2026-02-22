<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1>Lançar Notas - <?= $exam->exam_name ?? $exam->board_name ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active" aria-current="page">Lançar Notas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Exam Info -->
<div class="alert alert-info mb-4">
    <div class="row">
        <div class="col-md-3">
            <strong>Exame:</strong> <?= $exam->exam_name ?? $exam->board_name ?>
        </div>
        <div class="col-md-3">
            <strong>Tipo:</strong> <span class="badge bg-info"><?= $exam->board_type ?></span>
        </div>
        <div class="col-md-3">
            <strong>Turma:</strong> <?= $exam->class_name ?>
        </div>
        <div class="col-md-3">
            <strong>Disciplina:</strong> <?= $exam->discipline_name ?>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-3">
            <strong>Data:</strong> <?= date('d/m/Y', strtotime($exam->exam_date)) ?>
        </div>
        <div class="col-md-3">
            <strong>Valor Máximo:</strong> <?= $exam->max_score ?>
        </div>
        <div class="col-md-3">
            <strong>Nota Mínima Aprovação:</strong> <?= $exam->approval_score ?? 10 ?>
        </div>
    </div>
</div>

<!-- Grades Form -->
<div class="card">
    <div class="card-header bg-success text-white">
        <i class="fas fa-star"></i> Notas dos Alunos
    </div>
    <div class="card-body">
        <?php if (!empty($students)): ?>
            <form action="<?= site_url('teachers/exams/save-grades') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="exam_schedule_id" value="<?= $exam->id ?>">
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nº Matrícula</th>
                                <th>Aluno</th>
                                <th>Nota (0-<?= $exam->max_score ?>)</th>
                                <th>Falta</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <?php 
                                $result = $results[$student->enrollment_id] ?? null;
                                $score = $result ? $result->score : '';
                                $isAbsent = $result && $result->is_absent ? 'checked' : '';
                                ?>
                                <tr>
                                    <td><?= $student->student_number ?></td>
                                    <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                                    <td style="width: 150px;">
                                        <input type="number" 
                                               class="form-control score-input" 
                                               name="scores[<?= $student->enrollment_id ?>]" 
                                               value="<?= $score ?>"
                                               step="0.1"
                                               min="0"
                                               max="<?= $exam->max_score ?>">
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="absences[<?= $student->enrollment_id ?>]" 
                                                   value="1" <?= $isAbsent ?>>
                                            <label class="form-check-label">Faltou</label>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($isAbsent): ?>
                                            <span class="badge bg-danger">Falta</span>
                                        <?php elseif ($score !== ''): ?>
                                            <?php $approvalScore = $exam->approval_score ?? 10; ?>
                                            <?php if ($score >= $approvalScore): ?>
                                                <span class="badge bg-success">Aprovado</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Reprovado</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Pendente</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between">
                    <a href="<?= site_url('teachers/exams') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <div>
                        <button type="button" class="btn btn-info me-2" onclick="fillAllScores()">
                            <i class="fas fa-magic"></i> Preencher Todos
                        </button>
                        <button type="button" class="btn btn-warning me-2" onclick="markAllAbsent()">
                            <i class="fas fa-user-times"></i> Todos Faltaram
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Salvar Notas
                        </button>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <p class="text-muted text-center">Nenhum aluno encontrado para esta turma.</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function fillAllScores() {
    const inputs = document.querySelectorAll('.score-input');
    inputs.forEach(input => {
        if (!input.value) {
            input.value = '<?= ($exam->max_score / 2) ?>';
        }
    });
}

function markAllAbsent() {
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = true;
    });
    document.querySelectorAll('.score-input').forEach(input => {
        input.value = '';
    });
}

// Update status when score changes
document.querySelectorAll('.score-input').forEach(input => {
    input.addEventListener('change', function() {
        const row = this.closest('tr');
        const statusCell = row.querySelector('td:last-child');
        const score = parseFloat(this.value);
        const absentCheck = row.querySelector('input[type="checkbox"]');
        const approvalScore = <?= $exam->approval_score ?? 10 ?>;
        
        if (absentCheck.checked) {
            statusCell.innerHTML = '<span class="badge bg-danger">Falta</span>';
        } else if (score !== '' && !isNaN(score)) {
            if (score >= approvalScore) {
                statusCell.innerHTML = '<span class="badge bg-success">Aprovado</span>';
            } else {
                statusCell.innerHTML = '<span class="badge bg-danger">Reprovado</span>';
            }
        } else {
            statusCell.innerHTML = '<span class="badge bg-secondary">Pendente</span>';
        }
    });
});

// Update status when checkbox changes
document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const row = this.closest('tr');
        const statusCell = row.querySelector('td:last-child');
        const score = row.querySelector('.score-input').value;
        const approvalScore = <?= $exam->approval_score ?? 10 ?>;
        
        if (this.checked) {
            statusCell.innerHTML = '<span class="badge bg-danger">Falta</span>';
        } else if (score !== '' && !isNaN(parseFloat(score))) {
            if (parseFloat(score) >= approvalScore) {
                statusCell.innerHTML = '<span class="badge bg-success">Aprovado</span>';
            } else {
                statusCell.innerHTML = '<span class="badge bg-danger">Reprovado</span>';
            }
        } else {
            statusCell.innerHTML = '<span class="badge bg-secondary">Pendente</span>';
        }
    });
});
</script>
<?= $this->endSection() ?>