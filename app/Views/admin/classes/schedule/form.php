<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
:root {
    --primary:       #1B2B4B;
    --primary-light: #243761;
    --accent:        #3B7FE8;
    --accent-hover:  #2C6FD4;
    --success:       #16A87D;
    --danger:        #E84646;
    --warning:       #E8A020;
    --surface:       #F5F7FC;
    --surface-card:  #FFFFFF;
    --border:        #E2E8F4;
    --text-primary:  #1A2238;
    --text-secondary:#6B7A99;
    --text-muted:    #9AA5BE;
    --shadow-sm:     0 1px 4px rgba(27,43,75,.07);
    --shadow-md:     0 4px 16px rgba(27,43,75,.10);
    --shadow-lg:     0 8px 32px rgba(27,43,75,.14);
    --radius:        12px;
    --radius-sm:     8px;
}

* { font-family: 'Sora', sans-serif; }
body { background: var(--surface); }

/* Page Header */
.ci-page-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 60%, #2D4A7A 100%);
    border-radius: var(--radius);
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-lg);
}

.ci-page-header h1 {
    font-size: 1.4rem;
    font-weight: 700;
    color: #fff;
}

.ci-page-header .breadcrumb-item a {
    color: rgba(255,255,255,.6);
    text-decoration: none;
}

/* Form Card */
.form-card {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    margin-bottom: 1.5rem;
}

.form-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
}

.form-header h5 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

.form-header i {
    color: var(--accent);
}

.form-body {
    padding: 1.5rem;
}

/* Form Elements */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-label {
    font-size: .8rem;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: .5rem;
    display: block;
}

.form-label i {
    color: var(--accent);
    margin-right: .4rem;
    font-size: .75rem;
}

.form-control {
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: .6rem 1rem;
    font-size: .9rem;
    transition: all .2s;
}

.form-control:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,.1);
}

.form-select {
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: .6rem 1rem;
    font-size: .9rem;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B7A99' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    appearance: none;
    transition: all .2s;
}

.form-select:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,.1);
}

textarea.form-control {
    min-height: 100px;
    resize: vertical;
}

/* Time Input Group */
.time-group {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .75rem;
}

.time-input {
    display: flex;
    align-items: center;
    gap: .5rem;
}

.time-input .form-control {
    text-align: center;
}

/* Checkbox Group */
.checkbox-group {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .5rem 0;
}

.form-check {
    display: flex;
    align-items: center;
    gap: .5rem;
}

.form-check-input {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.form-check-label {
    font-size: .85rem;
    color: var(--text-secondary);
    cursor: pointer;
}

/* Action Buttons */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border);
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .6rem 1.5rem;
    border-radius: var(--radius-sm);
    font-size: .85rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
    cursor: pointer;
    border: none;
}

.btn-primary {
    background: var(--accent);
    color: #fff;
    box-shadow: 0 3px 10px rgba(59,127,232,.28);
}

.btn-primary:hover {
    background: var(--accent-hover);
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(59,127,232,.35);
}

.btn-secondary {
    background: var(--surface);
    color: var(--text-secondary);
    border: 1.5px solid var(--border);
}

.btn-secondary:hover {
    border-color: var(--accent);
    color: var(--accent);
    transform: translateY(-1px);
}

.btn-danger {
    background: var(--danger);
    color: #fff;
}

.btn-danger:hover {
    background: #c03939;
    transform: translateY(-1px);
}

.btn-sm {
    padding: .3rem 1rem;
    font-size: .75rem;
}

/* Conflict Alert */
.conflict-alert {
    background: rgba(232, 70, 70, 0.1);
    border-left: 3px solid var(--danger);
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}

.conflict-alert .message {
    display: flex;
    align-items: center;
    gap: .75rem;
    color: var(--danger);
    font-weight: 500;
}

.conflict-alert .message i {
    font-size: 1.2rem;
}

.conflict-actions {
    display: flex;
    gap: .75rem;
}

/* Info Card */
.info-card {
    background: rgba(59,127,232,.05);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.info-item {
    display: flex;
    align-items: center;
    gap: .5rem;
}

.info-item i {
    color: var(--accent);
    font-size: .9rem;
}

.info-item span {
    font-size: .85rem;
    color: var(--text-primary);
}

.info-item strong {
    color: var(--text-primary);
    font-weight: 700;
    margin-left: .3rem;
}

/* Error List */
.error-list {
    background: rgba(232, 70, 70, 0.1);
    border-left: 3px solid var(--danger);
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    border-radius: var(--radius-sm);
    color: var(--danger);
}

.error-list ul {
    margin: .5rem 0 0 1.5rem;
    padding: 0;
}

.error-list li {
    font-size: .8rem;
    margin-bottom: .2rem;
}

/* Responsive */
@media(max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .time-group {
        grid-template-columns: 1fr;
    }
    
    .conflict-alert {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-calendar-alt me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/schedule') ?>">Horários</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/schedule/view/' . $class['id']) ?>"><?= $class['class_name'] ?></a></li>
                    <li class="breadcrumb-item active"><?= isset($schedule) ? 'Editar' : 'Novo' ?> Horário</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/schedule/view/' . $class['id']) ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Voltar
            </a>
        </div>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- Class Info Card -->
<div class="info-card">
    <div class="info-item">
        <i class="fas fa-school"></i>
        <span>Turma: <strong><?= $class['class_name'] ?> (<?= $class['class_code'] ?>)</strong></span>
    </div>
    <div class="info-item">
        <i class="fas fa-layer-group"></i>
        <span>Nível: <strong><?= $class['level_name'] ?? 'N/A' ?></strong></span>
    </div>
    <div class="info-item">
        <i class="fas fa-clock"></i>
        <span>Turno: <strong><?= $class['class_shift'] ?></strong></span>
    </div>
    <div class="info-item">
        <i class="fas fa-door-open"></i>
        <span>Sala: <strong><?= $class->class_room ?: 'Não definida' ?></strong></span>
    </div>
</div>

<!-- Conflict Alert (if any) -->
<?php if (session()->has('conflict')): 
    $conflict = session('conflict');
?>
    <div class="conflict-alert">
        <div class="message">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?= $conflict['message'] ?></span>
        </div>
        <div class="conflict-actions">
            <form method="post" action="<?= site_url('admin/schedule/save') ?>" style="display: inline;">
                <?= csrf_field() ?>
                <?php 
                // Reenviar todos os dados do POST
                foreach ($_POST as $key => $value): 
                    if ($key != 'ignore_conflict'): 
                ?>
                    <input type="hidden" name="<?= $key ?>" value="<?= is_array($value) ? '' : $value ?>">
                <?php 
                    endif;
                endforeach; 
                ?>
                <input type="hidden" name="ignore_conflict" value="1">
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-check me-1"></i>Ignorar e Salvar
                </button>
            </form>
            <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
                <i class="fas fa-times me-1"></i>Cancelar
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Error List -->
<?php if (session()->has('errors')): ?>
    <div class="error-list">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Por favor, corrija os seguintes erros:</strong>
        <ul>
            <?php foreach (session('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Edit Form -->
<div class="form-card">
    <div class="form-header">
        <h5><i class="fas fa-clock me-2"></i><?= isset($schedule) ? 'Editar Horário' : 'Novo Horário' ?></h5>
        <span class="badge bg-primary"><?= $class['class_name'] ?></span>
    </div>
    
    <div class="form-body">
        <form action="<?= site_url('admin/schedule/save') ?>" method="post" id="scheduleForm">
            <?= csrf_field() ?>
            
            <?php if (isset($schedule)): ?>
                <input type="hidden" name="id" value="<?= $schedule['id'] ?>">
            <?php endif; ?>
            
            <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
            
            <div class="form-grid">
                <!-- Disciplina -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-book"></i>Disciplina <span class="text-danger">*</span>
                    </label>
                    <select name="discipline_id" class="form-select" required>
                        <option value="">Selecione uma disciplina...</option>
                        <?php foreach ($disciplines as $disc): ?>
                            <option value="<?= $disc->discipline_id ?>" 
                                <?= isset($schedule) && $schedule['discipline_id'] == $disc->discipline_id ? 'selected' : '' ?>
                                <?= old('discipline_id') == $disc->discipline_id ? 'selected' : '' ?>>
                                <?= $disc['discipline_name'] ?> (<?= $disc['discipline_code'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Dia da Semana -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-calendar-day"></i>Dia da Semana <span class="text-danger">*</span>
                    </label>
                    <select name="day_of_week" class="form-select" required>
                        <option value="">Selecione o dia...</option>
                        <?php foreach ($daysOfWeek as $day => $dayName): ?>
                            <option value="<?= $day ?>" 
                                <?= isset($schedule) && $schedule->day_of_week == $day ? 'selected' : '' ?>
                                <?= old('day_of_week') == $day ? 'selected' : '' ?>>
                                <?= $dayName ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Período -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-clock"></i>Período <span class="text-danger">*</span>
                    </label>
                    <select name="period" class="form-select" required>
                        <option value="">Selecione o período...</option>
                        <?php foreach ($periods as $period => $periodName): ?>
                            <option value="<?= $period ?>" 
                                <?= isset($schedule) && $schedule->period == $period ? 'selected' : '' ?>
                                <?= old('period') == $period ? 'selected' : '' ?>>
                                <?= $periodName ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Horário de Início -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-hourglass-start"></i>Hora de Início <span class="text-danger">*</span>
                    </label>
                    <input type="time" 
                           name="start_time" 
                           class="form-control" 
                           value="<?= isset($schedule) ? substr($schedule->start_time, 0, 5) : old('start_time') ?>"
                           required>
                </div>
                
                <!-- Horário de Término -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-hourglass-end"></i>Hora de Término <span class="text-danger">*</span>
                    </label>
                    <input type="time" 
                           name="end_time" 
                           class="form-control" 
                           value="<?= isset($schedule) ? substr($schedule->end_time, 0, 5) : old('end_time') ?>"
                           required>
                </div>
                
                <!-- Professor -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-chalkboard-teacher"></i>Professor
                    </label>
                    <select name="teacher_id" class="form-select">
                        <option value="">Sem professor atribuído</option>
                        <?php foreach ($teachers as $teacher): ?>
                            <option value="<?= $teacher['id'] ?>" 
                                <?= isset($schedule) && $schedule->teacher_id == $teacher['id'] ? 'selected' : '' ?>
                                <?= old('teacher_id') == $teacher['id'] ? 'selected' : '' ?>>
                                <?= $teacher['first_name'] ?> <?= $teacher['last_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Sala -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-door-open"></i>Sala
                    </label>
                    <input type="text" 
                           name="room" 
                           class="form-control" 
                           placeholder="Ex: Sala 101"
                           value="<?= isset($schedule) ? $schedule->room : old('room') ?>">
                </div>
                
                <!-- Status (Ativo/Inativo) -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-power-off"></i>Status
                    </label>
                    <div class="checkbox-group">
                        <div class="form-check">
                            <input type="checkbox" 
                                   name="is_active" 
                                   class="form-check-input" 
                                   value="1"
                                   <?= (!isset($schedule) || $schedule->is_active) ? 'checked' : '' ?>
                                   <?= old('is_active') ? 'checked' : '' ?>>
                            <label class="form-check-label">Horário ativo</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ações -->
            <div class="form-actions">
                <a href="<?= site_url('admin/schedule/view/' . $class['id']) ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>Salvar Horário
                </button>
                <?php if (isset($schedule)): ?>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $schedule['id'] ?>, '<?= $schedule['discipline_name'] ?? 'este horário' ?>')">
                        <i class="fas fa-trash"></i>Eliminar
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Quick Tips -->
<div class="alert alert-info" style="background: rgba(59,127,232,.05); border: 1px solid var(--border); border-radius: var(--radius-sm);">
    <div class="d-flex align-items-start gap-3">
        <i class="fas fa-lightbulb text-warning mt-1" style="font-size: 1.2rem;"></i>
        <div>
            <strong class="d-block mb-2">Dicas para criar horários:</strong>
            <ul class="mb-0" style="color: var(--text-secondary); font-size: .8rem;">
                <li>Certifique-se de que o horário de término é posterior ao de início</li>
                <li>Evite sobreposição de horários para a mesma turma</li>
                <li>O sistema verificará conflitos automaticamente</li>
                <li>Pode deixar o professor em branco para atribuir depois</li>
            </ul>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: var(--radius); border: none; overflow: hidden;">
            <div class="modal-header" style="background: var(--danger); color: white; border: none;">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 1.5rem;">
                <p>Tem certeza que deseja eliminar o horário de <strong id="deleteDisciplineName"></strong>?</p>
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Esta ação não pode ser desfeita.
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border); padding: 1rem 1.5rem;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Validação do formulário
    $('#scheduleForm').on('submit', function(e) {
        const startTime = $('input[name="start_time"]').val();
        const endTime = $('input[name="end_time"]').val();
        
        if (startTime && endTime) {
            if (startTime >= endTime) {
                e.preventDefault();
                alert('A hora de término deve ser posterior à hora de início!');
                return false;
            }
        }
        
        return true;
    });
    
    // Auto-formatação de hora (opcional)
    $('input[type="time"]').on('change', function() {
        const time = $(this).val();
        if (time) {
            // Validar formato HH:MM
            const regex = /^([0-1][0-9]|2[0-3]):[0-5][0-9]$/;
            if (!regex.test(time)) {
                alert('Formato de hora inválido! Use HH:MM');
                $(this).val('');
            }
        }
    });
    
    // Carregar horários existentes para evitar conflitos (opcional)
    $('#discipline_id, #day_of_week, #period').on('change', function() {
        checkAvailability();
    });
    
    function checkAvailability() {
        const classId = $('input[name="class_id"]').val();
        const dayOfWeek = $('select[name="day_of_week"]').val();
        const period = $('select[name="period"]').val();
        const scheduleId = $('input[name="id"]').val();
        
        if (dayOfWeek && period) {
            $.ajax({
                url: '<?= site_url('admin/schedule/check-availability') ?>',
                method: 'POST',
                data: {
                    class_id: classId,
                    day_of_week: dayOfWeek,
                    period: period,
                    schedule_id: scheduleId || 0,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (!response.available) {
                        $('#availabilityWarning').remove();
                        $('<div id="availabilityWarning" class="alert alert-warning mt-2">' +
                          '<i class="fas fa-exclamation-triangle me-2"></i>' +
                          'Já existe um horário para este período!</div>')
                            .insertAfter('select[name="period"]');
                    } else {
                        $('#availabilityWarning').remove();
                    }
                }
            });
        }
    }
});

// Função para confirmar eliminação
function confirmDelete(id, disciplineName) {
    $('#deleteDisciplineName').text(disciplineName);
    $('#confirmDeleteBtn').attr('href', '<?= site_url('admin/schedule/delete/') ?>' + id);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Atalhos de teclado
$(document).keydown(function(e) {
    // Ctrl + S = Salvar
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        $('#scheduleForm').submit();
    }
    // Ctrl + D = Duplicar (se for edição)
    <?php if (isset($schedule)): ?>
    if (e.ctrlKey && e.key === 'd') {
        e.preventDefault();
        // Criar cópia do formulário
        $('#scheduleForm').attr('action', '<?= site_url('admin/schedule/duplicate') ?>');
        $('#scheduleForm').submit();
    }
    <?php endif; ?>
});

// Tooltips
$('[title]').tooltip();

// Mostrar/esconder campos baseado na seleção (opcional)
$('#discipline_id').on('change', function() {
    const disciplineId = $(this).val();
    if (disciplineId) {
        // Buscar carga horária da disciplina (opcional)
        $.get('<?= site_url('admin/schedule/get-discipline-workload/') ?>' + disciplineId, function(response) {
            if (response.workload) {
                // Sugerir horários baseado na carga horária
                console.log('Carga horária sugerida:', response.workload);
            }
        });
    }
});
</script>
<?= $this->endSection() ?>