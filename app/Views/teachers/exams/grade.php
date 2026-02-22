<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Lançar Notas - <?= $exam->exam_name ?? $exam->board_name ?></h1>
        <div>
            <a href="<?= site_url('teachers/exams/attendance/' . $exam->id) ?>" class="btn btn-info me-2">
                <i class="fas fa-user-check"></i> Presenças
            </a>
            <a href="<?= site_url('teachers/exams') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
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
        <div class="col-md-3">
            <strong>Presenças:</strong>
            <span class="badge bg-success"><?= $attendanceStats['present'] ?? 0 ?> presentes</span>
            <span class="badge bg-danger"><?= $attendanceStats['absent'] ?? 0 ?> ausentes</span>
        </div>
    </div>
</div>

<!-- Grades Form -->
<div class="card">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-star me-2"></i>Notas dos Alunos</h5>
        <div>
            <span class="badge bg-light text-dark me-2">
                <i class="fas fa-check-circle text-success"></i> Aprovado: ≥ <?= $exam->approval_score ?? 10 ?>
            </span>
            <span class="badge bg-light text-dark">
                <i class="fas fa-times-circle text-danger"></i> Reprovado: < <?= $exam->approval_score ?? 10 ?>
            </span>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($students)): ?>
            <form action="<?= site_url('teachers/exams/save-grades') ?>" method="post" id="gradeForm">
                <?= csrf_field() ?>
                <input type="hidden" name="exam_schedule_id" value="<?= $exam->id ?>">
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nº Matrícula</th>
                                <th>Aluno</th>
                                <th>Presença</th>
                                <th>Nota (0-<?= $exam->max_score ?>)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <?php 
                                $result = $results[$student->enrollment_id] ?? null;
                                $attendance = $attendances[$student->enrollment_id] ?? null;
                                $score = $result ? $result->score : '';
                                $isAbsent = $attendance && !$attendance->attended;
                                $absentChecked = $isAbsent ? 'checked' : '';
                                $scoreDisabled = $isAbsent ? 'disabled' : '';
                                ?>
                                <tr>
                                    <td><span class="badge bg-secondary"><?= $student->student_number ?></span></td>
                                    <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                                    <td class="text-center">
                                        <?php if ($isAbsent): ?>
                                            <span class="badge bg-danger">Faltou</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Presente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="width: 150px;">
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               name="scores[<?= $student->enrollment_id ?>]" 
                                               value="<?= $score ?>"
                                               step="0.1"
                                               min="0"
                                               max="<?= $exam->max_score ?>"
                                               <?= $scoreDisabled ?>>
                                    </td>
                                    <td class="status-cell">
                                        <?php if ($isAbsent): ?>
                                            <span class="badge bg-danger">Falta</span>
                                        <?php elseif ($score !== ''): ?>
                                            <?php if ($score >= $exam->approval_score): ?>
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
                
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <i class="fas fa-users me-1"></i> Total: <strong><?= $totalStudents ?></strong> alunos
                        <span class="ms-3">
                            <i class="fas fa-check-circle text-success"></i> 
                            <span id="approvedCount">0</span> aprovados
                        </span>
                        <span class="ms-3">
                            <i class="fas fa-times-circle text-danger"></i> 
                            <span id="failedCount">0</span> reprovados
                        </span>
                    </div>
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
            <p class="text-muted text-center py-4">Nenhum aluno encontrado para esta turma.</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const maxScore = <?= $exam->max_score ?>;
const approvalScore = <?= $exam->approval_score ?? 10 ?>;

function fillAllScores() {
    const inputs = document.querySelectorAll('.score-input:not(:disabled)');
    inputs.forEach(input => {
        if (!input.value) {
            input.value = (maxScore / 2).toFixed(1);
            input.dispatchEvent(new Event('change'));
        }
    });
}

// Update status when score changes
document.querySelectorAll('.score-input').forEach(input => {
    input.addEventListener('change', function() {
        const row = this.closest('tr');
        const statusCell = row.querySelector('.status-cell');
        const score = parseFloat(this.value);
        
        if (!isNaN(score)) {
            if (score >= approvalScore) {
                statusCell.innerHTML = '<span class="badge bg-success">Aprovado</span>';
            } else {
                statusCell.innerHTML = '<span class="badge bg-danger">Reprovado</span>';
            }
        } else {
            statusCell.innerHTML = '<span class="badge bg-secondary">Pendente</span>';
        }
        updateStats();
    });
});

// Atualizar estatísticas
function updateStats() {
    let approved = 0, failed = 0;
    
    document.querySelectorAll('.status-cell').forEach(cell => {
        const text = cell.textContent.trim();
        if (text === 'Aprovado') approved++;
        else if (text === 'Reprovado') failed++;
    });
    
    document.getElementById('approvedCount').textContent = approved;
    document.getElementById('failedCount').textContent = failed;
}

// Atualizar na carga inicial
document.addEventListener('DOMContentLoaded', function() {
    updateStats();
});
</script>
<?= $this->endSection() ?>