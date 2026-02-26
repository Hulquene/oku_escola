<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Lançar Notas - <?= $exam->exam_name ?? $exam->board_name ?></h1>
        <div>
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
        <h5 class="mb-0"><i class="fas fa-star me-2"></i>Notas e Presenças dos Alunos</h5>
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
                                <th width="100">Nº Matrícula</th>
                                <th>Aluno</th>
                                <th width="120" class="text-center">Presença</th>
                                <th width="150">Nota (0-<?= $exam->max_score ?>)</th>
                                <th width="120">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <?php 
                                $result = $results[$student->enrollment_id] ?? null;
                                $attendance = $attendances[$student->enrollment_id] ?? null;
                                $score = $result ? $result->score : '';
                                $isAbsent = ($attendance && !$attendance->attended) || ($result && $result->is_absent);
                                $absentChecked = $isAbsent ? 'checked' : '';
                                $scoreValue = $isAbsent ? 0 : $score;
                                ?>
                                <tr class="<?= $isAbsent ? 'table-danger' : '' ?>" id="row-<?= $student->enrollment_id ?>">
                                    <td><span class="badge bg-secondary"><?= $student->student_number ?></span></td>
                                    <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                                    <td class="text-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input attendance-checkbox" 
                                                   type="checkbox" 
                                                   name="attendance[<?= $student->enrollment_id ?>]" 
                                                   id="attendance_<?= $student->enrollment_id ?>"
                                                   value="1"
                                                   <?= $absentChecked ?>
                                                   onchange="toggleAbsent(<?= $student->enrollment_id ?>, this.checked)">
                                            <label class="form-check-label" for="attendance_<?= $student->enrollment_id ?>">
                                                <?= $isAbsent ? '<span class="badge bg-danger">Ausente</span>' : '<span class="badge bg-success">Presente</span>' ?>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               name="scores[<?= $student->enrollment_id ?>]" 
                                               id="score_<?= $student->enrollment_id ?>"
                                               value="<?= $scoreValue ?>"
                                               step="0.1"
                                               min="0"
                                               max="<?= $exam->max_score ?>"
                                               <?= $isAbsent ? 'readonly' : '' ?>
                                               onchange="updateStatus(<?= $student->enrollment_id ?>, this.value)">
                                        <?php if ($isAbsent): ?>
                                            <small class="text-muted">(Ausente = 0)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="status-cell" id="status_<?= $student->enrollment_id ?>">
                                        <?php if ($isAbsent): ?>
                                            <span class="badge bg-danger">Falta</span>
                                        <?php elseif ($scoreValue !== '' && $scoreValue !== null): ?>
                                            <?php if ($scoreValue >= ($exam->approval_score ?? 10)): ?>
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
                        <span class="ms-3">
                            <i class="fas fa-user-times text-warning"></i> 
                            <span id="absentCount">0</span> ausentes
                        </span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-info me-2" onclick="markAllPresent()">
                            <i class="fas fa-user-check"></i> Marcar Todos Presentes
                        </button>
                        <button type="button" class="btn btn-warning me-2" onclick="fillAllScores()">
                            <i class="fas fa-magic"></i> Preencher Notas (Média)
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Salvar Notas e Presenças
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

// Função para quando o checkbox de ausente é alterado
function toggleAbsent(enrollmentId, isAbsent) {
    const scoreInput = document.getElementById('score_' + enrollmentId);
    const row = document.getElementById('row-' + enrollmentId);
    const label = document.querySelector('label[for="attendance_' + enrollmentId + '"]');
    
    if (isAbsent) {
        // Aluno ausente: nota = 0, readonly, row fica vermelha
        scoreInput.value = 0;
        scoreInput.readOnly = true;
        row.classList.add('table-danger');
        label.innerHTML = '<span class="badge bg-danger">Ausente</span>';
        updateStatus(enrollmentId, 0);
    } else {
        // Aluno presente: nota editável, row normal
        scoreInput.value = '';
        scoreInput.readOnly = false;
        row.classList.remove('table-danger');
        label.innerHTML = '<span class="badge bg-success">Presente</span>';
        updateStatus(enrollmentId, '');
    }
    
    updateStats();
}

// Função para atualizar status (aprovado/reprovado)
function updateStatus(enrollmentId, score) {
    const statusCell = document.getElementById('status_' + enrollmentId);
    const attendanceCheckbox = document.getElementById('attendance_' + enrollmentId);
    
    if (attendanceCheckbox.checked) {
        statusCell.innerHTML = '<span class="badge bg-danger">Falta</span>';
        return;
    }
    
    const scoreValue = parseFloat(score);
    
    if (!isNaN(scoreValue) && scoreValue !== '') {
        if (scoreValue >= approvalScore) {
            statusCell.innerHTML = '<span class="badge bg-success">Aprovado</span>';
        } else {
            statusCell.innerHTML = '<span class="badge bg-danger">Reprovado</span>';
        }
    } else {
        statusCell.innerHTML = '<span class="badge bg-secondary">Pendente</span>';
    }
    
    updateStats();
}

// Função para marcar todos como presentes
function markAllPresent() {
    document.querySelectorAll('.attendance-checkbox').forEach(checkbox => {
        if (checkbox.checked) {
            const enrollmentId = checkbox.name.match(/\[(\d+)\]/)[1];
            checkbox.checked = false;
            toggleAbsent(enrollmentId, false);
        }
    });
}

// Função para preencher todas as notas com a média
function fillAllScores() {
    document.querySelectorAll('.score-input').forEach(input => {
        if (!input.readOnly && !input.value) {
            input.value = (maxScore / 2).toFixed(1);
            const enrollmentId = input.id.replace('score_', '');
            updateStatus(enrollmentId, input.value);
        }
    });
}

// Função para atualizar estatísticas
function updateStats() {
    let approved = 0, failed = 0, absent = 0;
    
    document.querySelectorAll('.status-cell').forEach(cell => {
        const text = cell.textContent.trim();
        if (text === 'Aprovado') approved++;
        else if (text === 'Reprovado') failed++;
        else if (text === 'Falta') absent++;
    });
    
    document.getElementById('approvedCount').textContent = approved;
    document.getElementById('failedCount').textContent = failed;
    document.getElementById('absentCount').textContent = absent;
}

// Inicializar event listeners para inputs de nota
document.querySelectorAll('.score-input').forEach(input => {
    input.addEventListener('change', function() {
        const enrollmentId = this.id.replace('score_', '');
        updateStatus(enrollmentId, this.value);
    });
    
    input.addEventListener('keyup', function() {
        const enrollmentId = this.id.replace('score_', '');
        updateStatus(enrollmentId, this.value);
    });
});

// Atualizar estatísticas na carga inicial
document.addEventListener('DOMContentLoaded', function() {
    updateStats();
});

// Confirmar antes de sair se houver alterações não salvas
let formChanged = false;
document.getElementById('gradeForm').addEventListener('change', function() {
    formChanged = true;
});

document.getElementById('gradeForm').addEventListener('submit', function() {
    formChanged = false;
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = 'Existem alterações não salvas. Deseja realmente sair?';
    }
});
</script>
<?= $this->endSection() ?>