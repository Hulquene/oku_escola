<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">
                <i class="fas fa-user-check me-2 text-success"></i>
                Registar Presenças
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-alt me-1"></i>
                <strong><?= $schedule->discipline_name ?></strong> • 
                <strong><?= $schedule->class_name ?></strong> • 
                <span class="badge bg-<?= $schedule->status == 'Agendado' ? 'warning' : ($schedule->status == 'Realizado' ? 'success' : 'secondary') ?>">
                    <?= $schedule->status ?>
                </span>
            </p>
        </div>
        <div>
            <a href="<?= site_url('admin/exams/schedules/view/' . $schedule->id) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar aos Detalhes
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/schedules') ?>">Exames</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/schedules/view/' . $schedule->id) ?>">Detalhes</a></li>
            <li class="breadcrumb-item active">Presenças</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Lista de Alunos
                    <?php if ($isRecorded): ?>
                        <span class="badge bg-success ms-2">Presenças já registadas</span>
                    <?php endif; ?>
                </h5>
                <div>
                    <span class="text-muted me-2">
                        <i class="fas fa-check-circle text-success"></i> Presente
                    </span>
                    <span class="text-muted">
                        <i class="fas fa-times-circle text-danger"></i> Ausente
                    </span>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($students)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-users-slash fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum aluno encontrado</h5>
                        <p class="text-muted mb-0">
                            Esta turma não possui alunos com matrícula ativa ou pendente.
                        </p>
                        <a href="<?= site_url('admin/enrollments?class_id=' . $schedule->class_id) ?>" class="btn btn-sm btn-outline-primary mt-3">
                            <i class="fas fa-plus me-1"></i> Matricular Alunos
                        </a>
                    </div>
                <?php else: ?>
                <form method="post" action="<?= route_to('exams.schedules.saveAttendance', $schedule->id) ?>" id="attendanceForm">
                    <?= csrf_field() ?>
                    
                    <!-- Campos hidden para garantir que todos os alunos sejam processados -->
                    <?php foreach ($students as $student): ?>
                        <input type="hidden" name="attendance[<?= $student->enrollment_id ?>][exists]" value="1">
                    <?php endforeach; ?>
                    
                    <!-- Campo de pesquisa -->
                    <div class="mb-3">
                        <input type="text" 
                               class="form-control form-control-sm" 
                               id="searchStudent" 
                               placeholder="🔍 Pesquisar aluno por nome ou número...">
                    </div>
                    
                    <!-- Barra de ações rápidas na tabela -->
                    <div class="mb-3 d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="markAllPresent()">
                            <i class="fas fa-check-circle me-1"></i> Todos Presentes
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAllAbsent()">
                            <i class="fas fa-times-circle me-1"></i> Todos Ausentes
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="invertSelection()">
                            <i class="fas fa-sync-alt me-1"></i> Inverter
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="studentsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Nº Aluno</th>
                                    <th>Nome do Aluno</th>
                                    <th width="120" class="text-center">Presença</th>
                                    <th>Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($students as $student): ?>
                                    <?php 
                                    $existing = $attendanceMap[$student->enrollment_id] ?? null;
                                    $isPresent = $existing ? $existing->attended : 1;
                                    ?>
                                    <tr class="student-row" data-enrollment-id="<?= $student->enrollment_id ?>">
                                        <td><?= $i++ ?></td>
                                        <td>
                                            <span class="fw-semibold"><?= $student->student_number ?></span>
                                            <?php if (isset($student->enrollment_status) && $student->enrollment_status == 'Pendente'): ?>
                                                <span class="badge bg-warning d-block mt-1">Pendente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-<?= $isPresent ? 'success' : 'danger' ?> bg-opacity-10 me-2">
                                                    <span class="text-<?= $isPresent ? 'success' : 'danger' ?> fw-bold">
                                                        <?= strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <strong><?= $student->full_name ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       name="attendance[<?= $student->enrollment_id ?>][attended]" 
                                                       value="1"
                                                       id="present_<?= $student->enrollment_id ?>"
                                                       <?= $isPresent ? 'checked' : '' ?>
                                                       style="transform: scale(1.3); cursor: pointer;">
                                                <label class="form-check-label ms-2" for="present_<?= $student->enrollment_id ?>">
                                                    <span class="badge bg-<?= $isPresent ? 'success' : 'secondary' ?> present-badge">
                                                        <?= $isPresent ? 'Presente' : 'Ausente' ?>
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" 
                                                   class="form-control form-control-sm" 
                                                   name="attendance[<?= $student->enrollment_id ?>][observations]"
                                                   value="<?= $existing ? htmlspecialchars($existing->observations) : '' ?>"
                                                   placeholder="Ex: justificado, atrasado...">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                <span id="selectedCount"><?= $presentCount ?></span> de <?= $totalStudents ?> alunos presentes
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="<?= site_url('admin/exams/schedules/view/' . $schedule->id) ?>" class="btn btn-light">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="fas fa-save me-2"></i> 
                                <?= $isRecorded ? 'Atualizar Presenças' : 'Registar Presenças' ?>
                            </button>
                        </div>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Card de Informações do Exame -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-info-circle me-2 text-info"></i>
                    Informações do Exame
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted">Disciplina:</td>
                        <td class="fw-semibold"><?= $schedule->discipline_name ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Código:</td>
                        <td class="fw-semibold"><?= $schedule->discipline_code ?? 'N/A' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Turma:</td>
                        <td class="fw-semibold"><?= $schedule->class_name ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Data:</td>
                        <td class="fw-semibold"><?= date('d/m/Y', strtotime($schedule->exam_date)) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Horário:</td>
                        <td class="fw-semibold">
                            <?= $schedule->exam_time ? date('H:i', strtotime($schedule->exam_time)) : 'Não definida' ?>
                            <?php if ($schedule->duration_minutes): ?>
                                <small class="text-muted">(<?= $schedule->duration_minutes ?> min)</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Sala:</td>
                        <td class="fw-semibold"><?= $schedule->exam_room ?: 'Não definida' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Período:</td>
                        <td class="fw-semibold"><?= $schedule->period_name ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ano Letivo:</td>
                        <td class="fw-semibold"><?= $schedule->year_name ?? 'N/A' ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Card de Estatísticas Rápidas -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-chart-pie me-2 text-primary"></i>
                    Estatísticas
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle text-success me-2"></i> Presentes:</span>
                        <span class="fw-bold" id="statsPresent"><?= $presentCount ?></span>
                    </div>
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: <?= $totalStudents > 0 ? ($presentCount / $totalStudents) * 100 : 0 ?>%"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle text-danger me-2"></i> Ausentes:</span>
                        <span class="fw-bold" id="statsAbsent"><?= $absentCount ?></span>
                    </div>
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar bg-danger" role="progressbar" 
                             style="width: <?= $totalStudents > 0 ? ($absentCount / $totalStudents) * 100 : 0 ?>%"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-clock text-warning me-2"></i> Taxa Presença:</span>
                        <span class="fw-bold"><?= $totalStudents > 0 ? round(($presentCount / $totalStudents) * 100) : 0 ?>%</span>
                    </div>
                </div>
                
                <hr>
                
                <div class="small text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Alunos com matrícula pendente também são incluídos na lista.
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.85rem;
}

.student-row {
    transition: background-color 0.2s;
}

.student-row:hover {
    background-color: rgba(0,0,0,0.02);
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.form-check-input:not(:checked) {
    background-color: #dc3545;
    border-color: #dc3545;
}

.present-badge {
    transition: all 0.2s;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

#searchStudent {
    max-width: 300px;
}

/* Animações */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.student-row.bg-success {
    animation: pulse 0.3s ease-in-out;
}

.student-row.bg-danger {
    animation: pulse 0.3s ease-in-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
    updatePresentBadges();
});

// Função para atualizar contador de selecionados
function updateSelectedCount() {
    const count = document.querySelectorAll('input[type="checkbox"]:checked').length;
    const total = document.querySelectorAll('.student-row').length;
    document.getElementById('selectedCount').textContent = count;
    
    // Atualizar estatísticas
    document.getElementById('statsPresent').textContent = count;
    document.getElementById('statsAbsent').textContent = total - count;
    
    // Atualizar barras de progresso
    const presentPercent = total > 0 ? (count / total) * 100 : 0;
    const absentPercent = total > 0 ? ((total - count) / total) * 100 : 0;
    
    document.querySelector('.progress-bar.bg-success').style.width = presentPercent + '%';
    document.querySelector('.progress-bar.bg-danger').style.width = absentPercent + '%';
}

// Função para atualizar badges de presença
function updatePresentBadges() {
    document.querySelectorAll('.student-row').forEach(row => {
        const checkbox = row.querySelector('input[type="checkbox"]');
        const badge = row.querySelector('.present-badge');
        
        if (checkbox.checked) {
            badge.className = 'badge bg-success present-badge';
            badge.textContent = 'Presente';
        } else {
            badge.className = 'badge bg-secondary present-badge';
            badge.textContent = 'Ausente';
        }
    });
}

// Marcar todos como presentes
function markAllPresent() {
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = true;
    });
    updateSelectedCount();
    updatePresentBadges();
    
    // Efeito visual
    document.querySelectorAll('.student-row').forEach(row => {
        row.classList.add('bg-success', 'bg-opacity-10');
        setTimeout(() => row.classList.remove('bg-success', 'bg-opacity-10'), 500);
    });
}

// Marcar todos como ausentes
function markAllAbsent() {
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSelectedCount();
    updatePresentBadges();
    
    // Efeito visual
    document.querySelectorAll('.student-row').forEach(row => {
        row.classList.add('bg-danger', 'bg-opacity-10');
        setTimeout(() => row.classList.remove('bg-danger', 'bg-opacity-10'), 500);
    });
}

// Inverter seleções
function invertSelection() {
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = !checkbox.checked;
    });
    updateSelectedCount();
    updatePresentBadges();
}

// Atualizar quando checkbox muda
document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        updateSelectedCount();
        updatePresentBadges();
        
        // Feedback visual na linha
        const row = this.closest('.student-row');
        if (this.checked) {
            row.classList.add('bg-success', 'bg-opacity-10');
            setTimeout(() => row.classList.remove('bg-success', 'bg-opacity-10'), 300);
        } else {
            row.classList.add('bg-danger', 'bg-opacity-10');
            setTimeout(() => row.classList.remove('bg-danger', 'bg-opacity-10'), 300);
        }
    });
});

// Função para filtrar alunos
function filterStudents() {
    const searchTerm = document.getElementById('searchStudent').value.toLowerCase();
    const rows = document.querySelectorAll('.student-row');
    
    rows.forEach(row => {
        const studentNumber = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const studentName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        
        if (studentNumber.includes(searchTerm) || studentName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Event listener para o campo de pesquisa
document.getElementById('searchStudent')?.addEventListener('keyup', filterStudents);

// Confirm before submit
document.getElementById('attendanceForm')?.addEventListener('submit', function(e) {
    const presentCount = document.querySelectorAll('input[type="checkbox"]:checked').length;
    const totalCount = document.querySelectorAll('.student-row').length;
    
    if (!confirm(`Tem certeza que deseja registrar estas presenças?\n\nPresentes: ${presentCount}\nAusentes: ${totalCount - presentCount}`)) {
        e.preventDefault();
    }
});
</script>

<?= $this->endSection() ?>