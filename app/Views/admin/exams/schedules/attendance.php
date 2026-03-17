<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-user-check me-2" style="color: var(--success);"></i>Registar Presenças</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/schedules') ?>">Exames</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/schedules/view/' . $schedule['id']) ?>">Detalhes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Presenças</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/exams/schedules/view/' . $schedule['id']) ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar aos Detalhes
            </a>
        </div>
    </div>
    <div class="mt-2">
        <span class="badge-ci info me-2">
            <i class="fas fa-book me-1"></i> <?= $schedule['discipline_name'] ?>
        </span>
        <span class="badge-ci primary me-2">
            <i class="fas fa-users me-1"></i> <?= $schedule['class_name'] ?>
        </span>
        <?php
        $statusColors = [
            'Agendado' => 'warning',
            'Realizado' => 'success',
            'Cancelado' => 'danger',
            'Adiado' => 'info'
        ];
        $statusColor = $statusColors[$schedule['status']] ?? 'secondary';
        ?>
        <span class="badge-ci <?= $statusColor ?>">
            <i class="fas fa-clock me-1"></i> <?= $schedule['status'] ?>
        </span>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row g-4">
    <div class="col-md-8">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-list"></i>
                    <span>Lista de Alunos</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <?php if ($isRecorded): ?>
                        <span class="badge-ci success">
                            <i class="fas fa-check-circle me-1"></i> Presenças já registadas
                        </span>
                    <?php endif; ?>
                    <div class="d-flex gap-2">
                        <span class="text-muted small">
                            <i class="fas fa-check-circle text-success"></i> Presente
                        </span>
                        <span class="text-muted small">
                            <i class="fas fa-times-circle text-danger"></i> Ausente
                        </span>
                    </div>
                </div>
            </div>
            <div class="ci-card-body">
                <?php if (empty($students)): ?>
                    <div class="empty-state">
                        <i class="fas fa-users-slash"></i>
                        <h5>Nenhum aluno encontrado</h5>
                        <p class="text-muted mb-3">
                            Esta turma não possui alunos com matrícula ativa ou pendente.
                        </p>
                        <a href="<?= site_url('admin/enrollments?class_id=' . $schedule['class_id']) ?>" class="btn-ci primary">
                            <i class="fas fa-plus me-1"></i> Matricular Alunos
                        </a>
                    </div>
                <?php else: ?>
                <form method="post" action="<?= route_to('exams.schedules.saveAttendance', $schedule['id']) ?>" id="attendanceForm">
                    <?= csrf_field() ?>
                    
                    <!-- Campos hidden para garantir que todos os alunos sejam processados -->
                    <?php foreach ($students as $student): ?>
                        <input type="hidden" name="attendance[<?= $student['enrollment_id'] ?>][exists]" value="1">
                    <?php endforeach; ?>
                    
                    <!-- Campo de pesquisa -->
                    <div class="mb-3">
                        <div class="input-group" style="max-width: 300px;">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" 
                                   class="filter-select" 
                                   id="searchStudent" 
                                   placeholder="Pesquisar aluno por nome ou número...">
                        </div>
                    </div>
                    
                    <!-- Barra de ações rápidas na tabela -->
                    <div class="mb-3 d-flex gap-2">
                        <button type="button" class="btn-ci success" style="padding: 0.3rem 0.8rem; font-size: 0.75rem;" onclick="markAllPresent()">
                            <i class="fas fa-check-circle me-1"></i> Todos Presentes
                        </button>
                        <button type="button" class="btn-ci danger" style="padding: 0.3rem 0.8rem; font-size: 0.75rem;" onclick="markAllAbsent()">
                            <i class="fas fa-times-circle me-1"></i> Todos Ausentes
                        </button>
                        <button type="button" class="btn-ci primary" style="padding: 0.3rem 0.8rem; font-size: 0.75rem;" onclick="invertSelection()">
                            <i class="fas fa-sync-alt me-1"></i> Inverter
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="ci-table" id="studentsTable">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Nº Aluno</th>
                                    <th>Nome do Aluno</th>
                                    <th class="center">Presença</th>
                                    <th>Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($students as $student): ?>
                                    <?php 
                                    $existing = $attendanceMap[$student['enrollment_id']] ?? null;
                                    $isPresent = $existing ? $existing['attended'] : 1;
                                    ?>
                                    <tr class="student-row" data-enrollment-id="<?= $student['enrollment_id'] ?>">
                                        <td><?= str_pad($i++, 2, '0', STR_PAD_LEFT) ?></td>
                                        <td>
                                            <span class="code-badge"><?= $student['student_number'] ?></span>
                                            <?php if (isset($student->enrollment_status) && $student->enrollment_status == 'Pendente'): ?>
                                                <span class="badge-ci warning d-block mt-1" style="font-size: 0.65rem;">Pendente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="teacher-initials me-2" style="width: 35px; height: 35px; font-size: 0.9rem; background: var(--<?= $isPresent ? 'success' : 'danger' ?>);">
                                                    <?= strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <strong><?= $student['full_name'] ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <div class="ci-switch" style="width: 50px;">
                                                    <input type="checkbox" 
                                                           class="attendance-checkbox"
                                                           name="attendance[<?= $student['enrollment_id'] ?>][attended]" 
                                                           value="1"
                                                           id="present_<?= $student['enrollment_id'] ?>"
                                                           <?= $isPresent ? 'checked' : '' ?>>
                                                    <label class="ci-switch-track" for="present_<?= $student['enrollment_id'] ?>"></label>
                                                </div>
                                                <span class="badge-ci <?= $isPresent ? 'success' : 'secondary' ?> present-badge" style="min-width: 60px;">
                                                    <?= $isPresent ? 'Presente' : 'Ausente' ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" 
                                                   class="form-input-ci" 
                                                   name="attendance[<?= $student['enrollment_id'] ?>][observations]"
                                                   value="<?= $existing ? htmlspecialchars($existing['observations']) : '' ?>"
                                                   placeholder="Ex: justificado, atrasado..."
                                                   style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge-ci primary">
                                <i class="fas fa-check-circle text-success me-1"></i>
                                <span id="selectedCount"><?= $presentCount ?></span> de <?= $totalStudents ?> alunos presentes
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="<?= site_url('admin/exams/schedules/view/' . $schedule['id']) ?>" class="btn-filter clear">
                                Cancelar
                            </a>
                            <button type="submit" class="btn-ci success" id="submitBtn">
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
        <div class="ci-card mb-3">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-info-circle"></i>
                    <span>Informações do Exame</span>
                </div>
            </div>
            <div class="ci-card-body">
                <table class="ci-table table-borderless">
                    <tr>
                        <td class="text-muted">Disciplina:</td>
                        <td class="fw-semibold"><?= $schedule['discipline_name'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Código:</td>
                        <td class="fw-semibold"><?= $schedule['discipline_code'] ?? 'N/A' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Turma:</td>
                        <td class="fw-semibold"><?= $schedule['class_name'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Data:</td>
                        <td class="fw-semibold"><?= date('d/m/Y', strtotime($schedule['exam_date'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Horário:</td>
                        <td class="fw-semibold">
                            <?= $schedule['exam_time'] ? date('H:i', strtotime($schedule['exam_time'])) : 'Não definida' ?>
                            <?php if ($schedule['duration_minutes']): ?>
                                <small class="text-muted">(<?= $schedule['duration_minutes'] ?> min)</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Sala:</td>
                        <td class="fw-semibold"><?= $schedule['exam_room'] ?: 'Não definida' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Período:</td>
                        <td class="fw-semibold"><?= $schedule['period_name'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ano Letivo:</td>
                        <td class="fw-semibold"><?= $schedule['year_name'] ?? 'N/A' ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Card de Estatísticas Rápidas -->
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-chart-pie"></i>
                    <span>Estatísticas</span>
                </div>
            </div>
            <div class="ci-card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle text-success me-2"></i> Presentes:</span>
                        <span class="fw-bold" id="statsPresent"><?= $presentCount ?></span>
                    </div>
                    <div class="progress mb-3" style="height: 8px; background: var(--surface);">
                        <div class="progress-bar" style="width: <?= $totalStudents > 0 ? ($presentCount / $totalStudents) * 100 : 0 ?>%; background: var(--success); border-radius: 4px;"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle text-danger me-2"></i> Ausentes:</span>
                        <span class="fw-bold" id="statsAbsent"><?= $absentCount ?></span>
                    </div>
                    <div class="progress mb-3" style="height: 8px; background: var(--surface);">
                        <div class="progress-bar" style="width: <?= $totalStudents > 0 ? ($absentCount / $totalStudents) * 100 : 0 ?>%; background: var(--danger); border-radius: 4px;"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-clock text-warning me-2"></i> Taxa Presença:</span>
                        <span class="fw-bold"><?= $totalStudents > 0 ? round(($presentCount / $totalStudents) * 100) : 0 ?>%</span>
                    </div>
                </div>
                
                <hr class="my-3">
                
                <div class="alert-ci info small">
                    <i class="fas fa-info-circle me-1"></i>
                    Alunos com matrícula pendente também são incluídos na lista.
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
    updatePresentBadges();
    initSwitches();
});

// Inicializar switches
function initSwitches() {
    document.querySelectorAll('.ci-switch-track').forEach(track => {
        const input = track.previousElementSibling;
        if (input && input.type === 'checkbox' && input.checked) {
            track.classList.add('checked');
        }
    });
}

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
    
    document.querySelectorAll('.progress-bar')[0].style.width = presentPercent + '%';
    document.querySelectorAll('.progress-bar')[1].style.width = absentPercent + '%';
}

// Função para atualizar badges de presença
function updatePresentBadges() {
    document.querySelectorAll('.student-row').forEach(row => {
        const checkbox = row.querySelector('input[type="checkbox"]');
        const badge = row.querySelector('.present-badge');
        const initials = row.querySelector('.teacher-initials');
        
        if (checkbox.checked) {
            badge.className = 'badge-ci success present-badge';
            badge.textContent = 'Presente';
            initials.style.background = 'var(--success)';
        } else {
            badge.className = 'badge-ci secondary present-badge';
            badge.textContent = 'Ausente';
            initials.style.background = 'var(--danger)';
        }
    });
}

// Marcar todos como presentes
function markAllPresent() {
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = true;
        const track = checkbox.nextElementSibling;
        if (track && track.classList.contains('ci-switch-track')) {
            track.classList.add('checked');
        }
    });
    updateSelectedCount();
    updatePresentBadges();
    
    // Efeito visual
    document.querySelectorAll('.student-row').forEach(row => {
        row.classList.add('row-assigned');
        setTimeout(() => row.classList.remove('row-assigned'), 500);
    });
}

// Marcar todos como ausentes
function markAllAbsent() {
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
        const track = checkbox.nextElementSibling;
        if (track && track.classList.contains('ci-switch-track')) {
            track.classList.remove('checked');
        }
    });
    updateSelectedCount();
    updatePresentBadges();
    
    // Efeito visual
    document.querySelectorAll('.student-row').forEach(row => {
        row.classList.add('row-warning');
        setTimeout(() => row.classList.remove('row-warning'), 500);
    });
}

// Inverter seleções
function invertSelection() {
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = !checkbox.checked;
        const track = checkbox.nextElementSibling;
        if (track && track.classList.contains('ci-switch-track')) {
            if (checkbox.checked) {
                track.classList.add('checked');
            } else {
                track.classList.remove('checked');
            }
        }
    });
    updateSelectedCount();
    updatePresentBadges();
}

// Atualizar quando checkbox muda
document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const track = this.nextElementSibling;
        if (track && track.classList.contains('ci-switch-track')) {
            if (this.checked) {
                track.classList.add('checked');
            } else {
                track.classList.remove('checked');
            }
        }
        updateSelectedCount();
        updatePresentBadges();
        
        // Feedback visual na linha
        const row = this.closest('.student-row');
        if (this.checked) {
            row.classList.add('row-assigned');
            setTimeout(() => row.classList.remove('row-assigned'), 300);
        } else {
            row.classList.add('row-warning');
            setTimeout(() => row.classList.remove('row-warning'), 300);
        }
    });
});

// Função para filtrar alunos
function filterStudents() {
    const searchTerm = document.getElementById('searchStudent').value.toLowerCase();
    const rows = document.querySelectorAll('.student-row');
    
    rows.forEach(row => {
        const studentNumber = row.querySelector('td:nth-child(2) .code-badge').textContent.toLowerCase();
        const studentName = row.querySelector('td:nth-child(3) strong').textContent.toLowerCase();
        
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

<style>
/* Estilos adicionais específicos para esta página */
.teacher-initials {
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    text-transform: uppercase;
    color: white;
    flex-shrink: 0;
    transition: background 0.2s;
}

.student-row {
    transition: background-color 0.2s;
}

.student-row.row-assigned {
    background: rgba(22,168,125,0.08);
    border-left: 3px solid var(--success);
}

.student-row.row-warning {
    background: rgba(232,160,32,0.08);
    border-left: 3px solid var(--warning);
}

/* Ajustes para o switch */
.ci-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.ci-switch input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
}

.ci-switch-track {
    position: absolute;
    inset: 0;
    border-radius: 50px;
    background: var(--danger);
    transition: background 0.22s;
    cursor: pointer;
}

.ci-switch-track::after {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    transition: transform 0.22s;
}

.ci-switch input:checked + .ci-switch-track {
    background: var(--success);
}

.ci-switch input:checked + .ci-switch-track::after {
    transform: translateX(26px);
}

.present-badge {
    min-width: 60px;
    text-align: center;
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

.input-group {
    display: flex;
    width: 100%;
}

.input-group .input-group-text {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-right: none;
    border-radius: var(--radius-sm) 0 0 var(--radius-sm);
    color: var(--text-muted);
    padding: 0 0.65rem;
    display: flex;
    align-items: center;
}

.input-group .filter-select {
    border-left: none;
    border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    height: 38px;
}

/* Animações */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

.student-row.row-assigned,
.student-row.row-warning {
    animation: pulse 0.3s ease-in-out;
}

/* Progress bar customizada */
.progress {
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    border-radius: 4px;
    transition: width 0.3s ease;
}

/* Responsividade */
@media (max-width: 768px) {
    .ci-table td {
        min-width: 120px;
    }
    
    .ci-table td:first-child {
        min-width: auto;
    }
    
    .d-flex.gap-2 {
        flex-wrap: wrap;
    }
}
</style>
<?= $this->endSection() ?>