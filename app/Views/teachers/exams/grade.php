<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1>Lançar Notas - <?= $exam->exam_name ?></h1>
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
            <strong>Exame:</strong> <?= $exam->exam_name ?>
        </div>
        <div class="col-md-3">
            <strong>Turma:</strong> <?= $exam->class_name ?>
        </div>
        <div class="col-md-3">
            <strong>Disciplina:</strong> <?= $exam->discipline_name ?>
        </div>
        <div class="col-md-3">
            <strong>Valor Máximo:</strong> <?= $exam->max_score ?>
        </div>
    </div>
</div>

<!-- Grades Form -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-star"></i> Notas dos Alunos
    </div>
    <div class="card-body">
        <?php if (!empty($students)): ?>
            <form action="<?= site_url('teachers/exams/save-grades') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="exam_id" value="<?= $exam->id ?>">
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nº Matrícula</th>
                                <th>Aluno</th>
                                <th>Nota (0-<?= $exam->max_score ?>)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <?php 
                                $result = isset($results[$student->enrollment_id]) ? $results[$student->enrollment_id] : null;
                                $score = $result ? $result->score : '';
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
                                    <td>
                                        <?php if ($score !== ''): ?>
                                            <?php if ($score >= 10): ?>
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
            input.value = '<?= $exam->max_score / 2 ?>';
        }
    });
}

// Update status when score changes
document.querySelectorAll('.score-input').forEach(input => {
    input.addEventListener('change', function() {
        const row = this.closest('tr');
        const statusCell = row.querySelector('td:last-child');
        const score = parseFloat(this.value);
        
        if (score !== '' && !isNaN(score)) {
            if (score >= 10) {
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