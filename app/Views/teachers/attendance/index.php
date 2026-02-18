<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('teachers/attendance/report') ?>" class="btn btn-info me-2">
                <i class="fas fa-chart-bar"></i> Relatórios
            </a>
            <a href="<?= site_url('teachers/attendance?date=' . date('Y-m-d')) ?>" class="btn btn-secondary">
                <i class="fas fa-calendar-day"></i> Hoje
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Presenças</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros Avançados -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <i class="fas fa-filter me-2"></i>Filtros
    </div>
    <div class="card-body">
        <form method="get" id="attendanceForm" class="row g-3">
            <div class="col-md-4">
                <label for="class" class="form-label fw-bold">Turma <span class="text-danger">*</span></label>
                <select class="form-select" id="class" name="class_id" required>
                    <option value="">Selecione...</option>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class->id ?>" <?= $selectedClass == $class->id ? 'selected' : '' ?>>
                                <?= $class->class_name ?> (<?= $class->class_code ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-4">
                <label for="discipline" class="form-label fw-bold">Disciplina <span class="text-danger">*</span></label>
                <select class="form-select" id="discipline" name="discipline_id" required>
                    <option value="">Selecione...</option>
                    <!-- Carregado via AJAX -->
                </select>
                <small class="text-muted">Selecione a disciplina para registrar presença</small>
            </div>
            
            <div class="col-md-2">
                <label for="date" class="form-label fw-bold">Data <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="date" name="date" 
                       value="<?= $selectedDate ?>" max="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100" id="loadButton" disabled>
                    <i class="fas fa-search"></i> Carregar
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($selectedClass && $selectedDisciplineId && $selectedDate): ?>
    <?php if (!empty($students)): ?>
        <!-- Cards de Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-success text-white stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Presentes</h6>
                                <h2 class="mb-0" id="statPresent">0</h2>
                            </div>
                            <i class="fas fa-user-check fa-2x opacity-50"></i>
                        </div>
                        <small class="mt-2 d-block" id="presentPercent">0%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Ausentes</h6>
                                <h2 class="mb-0" id="statAbsent">0</h2>
                            </div>
                            <i class="fas fa-user-times fa-2x opacity-50"></i>
                        </div>
                        <small class="mt-2 d-block" id="absentPercent">0%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Atrasados</h6>
                                <h2 class="mb-0" id="statLate">0</h2>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-50"></i>
                        </div>
                        <small class="mt-2 d-block" id="latePercent">0%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Justificados</h6>
                                <h2 class="mb-0" id="statJustified">0</h2>
                            </div>
                            <i class="fas fa-file-alt fa-2x opacity-50"></i>
                        </div>
                        <small class="mt-2 d-block" id="justifiedPercent">0%</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informação da Disciplina -->
        <div class="alert alert-info mb-3">
            <i class="fas fa-book me-2"></i>
            <strong>Disciplina:</strong> 
            <span id="selectedDisciplineName"><?= $selectedDisciplineName ?></span>
        </div>
        
        <!-- Formulário de Presenças -->
        <div class="card">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-calendar-check me-2"></i>
                    Registro de Presenças - <?= date('d/m/Y', strtotime($selectedDate)) ?>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-light" onclick="markAll('Presente')">
                        <i class="fas fa-check-circle text-success me-1"></i>Todos Presentes
                    </button>
                    <button type="button" class="btn btn-sm btn-light" onclick="markAll('Ausente')">
                        <i class="fas fa-times-circle text-danger me-1"></i>Todos Ausentes
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form action="<?= site_url('teachers/attendance/save') ?>" method="post" id="attendanceSaveForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="class_id" value="<?= $selectedClass ?>">
                    <input type="hidden" name="discipline_id" value="<?= $selectedDisciplineId ?>">
                    <input type="hidden" name="attendance_date" value="<?= $selectedDate ?>">
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="100">Nº Matrícula</th>
                                    <th>Aluno</th>
                                    <th width="200">Status</th>
                                    <th>Justificação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <?php $attendance = $attendances[$student->enrollment_id] ?? null; ?>
                                    <tr>
                                        <td><span class="badge bg-secondary"><?= $student->student_number ?></span></td>
                                        <td>
                                            <strong><?= $student->first_name ?> <?= $student->last_name ?></strong>
                                        </td>
                                        <td>
                                            <select class="form-select status-select" 
                                                    name="attendance[<?= $student->enrollment_id ?>][status]"
                                                    onchange="updateStats()">
                                                <option value="Presente" <?= ($attendance && $attendance->status == 'Presente') ? 'selected' : '' ?>>Presente</option>
                                                <option value="Ausente" <?= ($attendance && $attendance->status == 'Ausente') ? 'selected' : '' ?>>Ausente</option>
                                                <option value="Atrasado" <?= ($attendance && $attendance->status == 'Atrasado') ? 'selected' : '' ?>>Atrasado</option>
                                                <option value="Falta Justificada" <?= ($attendance && $attendance->status == 'Falta Justificada') ? 'selected' : '' ?>>Falta Justificada</option>
                                                <option value="Dispensado" <?= ($attendance && $attendance->status == 'Dispensado') ? 'selected' : '' ?>>Dispensado</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" 
                                                   name="attendance[<?= $student->enrollment_id ?>][justification]" 
                                                   value="<?= $attendance ? $attendance->justification : '' ?>" 
                                                   placeholder="Justificação (opcional)">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <i class="fas fa-users me-1"></i>
                            Total: <strong><?= count($students) ?></strong> alunos
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary me-2" onclick="resetForm()">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Salvar Presenças
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
    <?php else: ?>
        <div class="alert alert-warning text-center py-4">
            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
            <h5>Nenhum aluno encontrado</h5>
            <p class="mb-0">Não há alunos matriculados nesta turma/disciplina.</p>
        </div>
    <?php endif; ?>
    
<?php elseif ($selectedClass || $selectedDisciplineId || $selectedDate): ?>
    <div class="alert alert-info text-center py-4">
        <i class="fas fa-info-circle fa-3x mb-3"></i>
        <h5>Selecione todos os filtros</h5>
        <p class="mb-0">Para carregar as presenças, selecione turma, disciplina e data.</p>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Elementos do formulário
const classSelect = document.getElementById('class');
const disciplineSelect = document.getElementById('discipline');
const dateInput = document.getElementById('date');
const loadButton = document.getElementById('loadButton');
const selectedDisciplineId = '<?= $selectedDisciplineId ?>';

// Função para verificar se todos os campos estão preenchidos
function checkFormCompletion() {
    const classSelected = classSelect.value !== '';
    const disciplineSelected = disciplineSelect.value !== '';
    const dateSelected = dateInput.value !== '';
    
    loadButton.disabled = !(classSelected && disciplineSelected && dateSelected);
}

// Carregar disciplinas quando turma mudar
classSelect.addEventListener('change', function() {
    const classId = this.value;
    
    if (classId) {
        disciplineSelect.innerHTML = '<option value="">Carregando...</option>';
        disciplineSelect.disabled = true;
        
        fetch(`<?= site_url('teachers/attendance/get-disciplines/') ?>/${classId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            disciplineSelect.innerHTML = '<option value="">Selecione...</option>';
            disciplineSelect.disabled = false;
            
            data.forEach(discipline => {
                const selected = (selectedDisciplineId == discipline.id) ? 'selected' : '';
                disciplineSelect.innerHTML += `<option value="${discipline.id}" ${selected}>${discipline.discipline_name}</option>`;
            });
            
            console.log('Disciplinas carregadas:', data);
            checkFormCompletion();
        })
        .catch(error => {
            console.error('Erro ao carregar disciplinas:', error);
            disciplineSelect.innerHTML = '<option value="">Erro ao carregar disciplinas</option>';
            disciplineSelect.disabled = false;
            checkFormCompletion();
        });
    } else {
        disciplineSelect.innerHTML = '<option value="">Selecione...</option>';
        disciplineSelect.disabled = false;
        checkFormCompletion();
    }
});

// Adicionar listeners para verificar quando os campos mudam
classSelect.addEventListener('change', checkFormCompletion);
disciplineSelect.addEventListener('change', checkFormCompletion);
dateInput.addEventListener('change', checkFormCompletion);

// Disparar change se turma já selecionada
<?php if ($selectedClass): ?>
classSelect.dispatchEvent(new Event('change'));
<?php endif; ?>

// Verificar campos no carregamento da página
window.addEventListener('load', checkFormCompletion);

// Função para marcar todos
function markAll(status) {
    if (confirm(`Marcar todos os alunos como "${status}"?`)) {
        document.querySelectorAll('.status-select').forEach(select => {
            select.value = status;
        });
        updateStats();
    }
}

// Função para resetar o formulário
function resetForm() {
    if (confirm('Resetar todas as presenças para os valores originais?')) {
        location.reload();
    }
}

// Atualizar estatísticas
function updateStats() {
    let present = 0, absent = 0, late = 0, justified = 0;
    const total = <?= count($students ?? []) ?>;
    
    document.querySelectorAll('.status-select').forEach(select => {
        switch(select.value) {
            case 'Presente': present++; break;
            case 'Ausente': absent++; break;
            case 'Atrasado': late++; break;
            case 'Falta Justificada': justified++; break;
        }
    });
    
    document.getElementById('statPresent').textContent = present;
    document.getElementById('statAbsent').textContent = absent;
    document.getElementById('statLate').textContent = late;
    document.getElementById('statJustified').textContent = justified;
    
    // Calcular percentuais
    if (total > 0) {
        document.getElementById('presentPercent').textContent = Math.round((present / total) * 100) + '%';
        document.getElementById('absentPercent').textContent = Math.round((absent / total) * 100) + '%';
        document.getElementById('latePercent').textContent = Math.round((late / total) * 100) + '%';
        document.getElementById('justifiedPercent').textContent = Math.round((justified / total) * 100) + '%';
    }
}

// Atualizar estatísticas na carga inicial
document.addEventListener('DOMContentLoaded', function() {
    updateStats();
});

// Atualizar estatísticas quando status mudar
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', updateStats);
});

// Debug - Ver dados do formulário antes de enviar
document.getElementById('attendanceSaveForm')?.addEventListener('submit', function(e) {
    const present = parseInt(document.getElementById('statPresent').textContent);
    const total = <?= count($students ?? []) ?>;
    
    // Debug no console
    const formData = new FormData(this);
    console.log('=== DADOS DO FORMULÁRIO ===');
    console.log('Class ID:', formData.get('class_id'));
    console.log('Discipline ID:', formData.get('discipline_id'));
    console.log('Date:', formData.get('attendance_date'));
    
    let studentsWithStatus = 0;
    for (let pair of formData.entries()) {
        if (pair[0].includes('[status]')) {
            studentsWithStatus++;
            console.log(pair[0], ':', pair[1]);
        }
    }
    console.log('Total alunos com status:', studentsWithStatus);
    console.log('============================');
    
    if (total > 0 && present === 0) {
        if (!confirm('Nenhum aluno marcado como presente. Deseja continuar mesmo assim?')) {
            e.preventDefault();
        }
    }
});
</script>

<style>
.stat-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.85em;
    padding: 0.5em 0.75em;
}

.status-select {
    cursor: pointer;
}

.status-select option[value="Presente"] { background-color: #d4edda; }
.status-select option[value="Ausente"] { background-color: #f8d7da; }
.status-select option[value="Atrasado"] { background-color: #fff3cd; }
.status-select option[value="Falta Justificada"] { background-color: #d1ecf1; }

/* Animações */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.3s ease-out;
}

button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
<?= $this->endSection() ?>