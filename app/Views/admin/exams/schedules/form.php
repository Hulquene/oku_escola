<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-<?= isset($schedule) ? 'edit' : 'plus-circle' ?> me-2" style="color: var(--accent);"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/schedules') ?>">Agendamentos</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= isset($schedule) ? 'Editar' : 'Novo' ?> Agendamento</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/exams/schedules') ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-<?= isset($schedule) ? 'edit' : 'plus-circle' ?>"></i>
            <span><?= isset($schedule) ? 'Editar Agendamento' : 'Novo Agendamento de Exame' ?></span>
        </div>
    </div>
    <div class="ci-card-body">
        <form method="post" action="<?= isset($schedule) ? site_url('admin/exams/schedules/update/' . $schedule['id']) : site_url('admin/exams/schedules/save') ?>" class="needs-validation" novalidate>
            <?= csrf_field() ?>
            
            <div class="row g-3">
                <!-- Período de Exame -->
                <div class="col-md-6">
                    <label class="filter-label required">Período de Exame</label>
                    <select class="filter-select <?= session('errors.exam_period_id') ? 'is-invalid' : '' ?>" 
                            name="exam_period_id" required>
                        <option value="">Selecione um período...</option>
                        <?php foreach ($periods as $period): ?>
                            <option value="<?= $period['id'] ?>" 
                                <?= (old('exam_period_id') == $period['id'] || (isset($schedule) && $schedule['exam_period_id'] == $period['id'])) ? 'selected' : '' ?>>
                                <?= $period['period_name'] ?> (<?= $period['period_type'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-error">
                        <?= session('errors.exam_period_id') ?? '' ?>
                    </div>
                </div>
                
                <!-- Turma -->
                <div class="col-md-6">
                    <label class="filter-label required">Turma</label>
                    <select class="filter-select <?= session('errors.class_id') ? 'is-invalid' : '' ?>" 
                            name="class_id" id="class_id" required>
                        <option value="">Selecione uma turma...</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class['id'] ?>" 
                                <?= (old('class_id') == $class['id'] || (isset($schedule) && $schedule['class_id'] == $class['id'])) ? 'selected' : '' ?>>
                                <?= $class['class_name'] ?> (<?= $class['class_code'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-error">
                        <?= session('errors.class_id') ?? '' ?>
                    </div>
                </div>
                
                <!-- Disciplina -->
                <div class="col-md-6">
                    <label class="filter-label required">Disciplina</label>
                    <select class="filter-select <?= session('errors.discipline_id') ? 'is-invalid' : '' ?>" 
                            name="discipline_id" id="discipline_id" required>
                        <option value="">Primeiro selecione a turma...</option>
                        <?php if (isset($schedule)): ?>
                            <option value="<?= $schedule['discipline_id'] ?>" selected>
                                <?= $schedule['discipline_name'] ?>
                            </option>
                        <?php endif; ?>
                    </select>
                    <div class="form-error">
                        <?= session('errors.discipline_id') ?? '' ?>
                    </div>
                </div>
                
                <!-- Tipo de Exame -->
                <div class="col-md-6">
                    <label class="filter-label required">Tipo de Exame</label>
                    <select class="filter-select <?= session('errors.exam_board_id') ? 'is-invalid' : '' ?>" 
                            name="exam_board_id" required>
                        <option value="">Selecione o tipo...</option>
                        <?php foreach ($examBoards as $board): ?>
                            <option value="<?= $board['id'] ?>" 
                                <?= (old('exam_board_id') == $board['id'] || (isset($schedule) && $schedule['exam_board_id'] == $board['id'])) ? 'selected' : '' ?>>
                                <?= $board['board_name'] ?> (<?= $board['board_type'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-error">
                        <?= session('errors.exam_board_id') ?? '' ?>
                    </div>
                </div>
                
                <!-- Data -->
                <div class="col-md-4">
                    <label class="filter-label required">Data do Exame</label>
                    <input type="date" class="form-input-ci <?= session('errors.exam_date') ? 'is-invalid' : '' ?>" 
                           name="exam_date" value="<?= old('exam_date') ?? (isset($schedule) ? $schedule['exam_date'] : '') ?>" required>
                    <div class="form-error">
                        <?= session('errors.exam_date') ?? '' ?>
                    </div>
                </div>
                
                <!-- Hora -->
                <div class="col-md-4">
                    <label class="filter-label">Hora de Início</label>
                    <input type="time" class="form-input-ci <?= session('errors.exam_time') ? 'is-invalid' : '' ?>" 
                           name="exam_time" value="<?= old('exam_time') ?? (isset($schedule) && $schedule['exam_time'] ? substr($schedule['exam_time'], 0, 5) : '') ?>">
                    <div class="form-error"><?= session('errors.exam_time') ?? '' ?></div>
                </div>
                
                <!-- Duração -->
                <div class="col-md-4">
                    <label class="filter-label">Duração (minutos)</label>
                    <input type="number" class="form-input-ci <?= session('errors.duration_minutes') ? 'is-invalid' : '' ?>" 
                           name="duration_minutes" value="<?= old('duration_minutes') ?? (isset($schedule) ? $schedule['duration_minutes'] : 120) ?>" 
                           min="30" step="15">
                    <div class="form-error"><?= session('errors.duration_minutes') ?? '' ?></div>
                </div>
                
                <!-- Sala -->
                <div class="col-md-6">
                    <label class="filter-label">Sala/Local</label>
                    <input type="text" class="form-input-ci <?= session('errors.exam_room') ? 'is-invalid' : '' ?>" 
                           name="exam_room" value="<?= old('exam_room') ?? (isset($schedule) ? $schedule['exam_room'] : '') ?>" 
                           placeholder="Ex: Sala 101, Laboratório, etc.">
                    <div class="form-error"><?= session('errors.exam_room') ?? '' ?></div>
                </div>
                
                <!-- Status (apenas para edição) -->
                <?php if (isset($schedule)): ?>
                <div class="col-md-6">
                    <label class="filter-label required">Status</label>
                    <select class="filter-select <?= session('errors.status') ? 'is-invalid' : '' ?>" name="status" required>
                        <option value="Agendado" <?= (old('status') ?? $schedule['status']) == 'Agendado' ? 'selected' : '' ?>>Agendado</option>
                        <option value="Realizado" <?= (old('status') ?? $schedule['status']) == 'Realizado' ? 'selected' : '' ?>>Realizado</option>
                        <option value="Cancelado" <?= (old('status') ?? $schedule['status']) == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        <option value="Adiado" <?= (old('status') ?? $schedule['status']) == 'Adiado' ? 'selected' : '' ?>>Adiado</option>
                    </select>
                    <div class="form-error">
                        <?= session('errors.status') ?? '' ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Observações -->
                <div class="col-12">
                    <label class="filter-label">Observações</label>
                    <textarea class="form-input-ci <?= session('errors.observations') ? 'is-invalid' : '' ?>" 
                              name="observations" rows="3" 
                              placeholder="Informações adicionais sobre o exame..."><?= old('observations') ?? (isset($schedule) ? $schedule['observations'] : '') ?></textarea>
                    <div class="form-error"><?= session('errors.observations') ?? '' ?></div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= site_url('admin/exams/schedules') ?>" class="btn-filter clear">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn-ci primary">
                    <i class="fas fa-save me-2"></i>
                    <?= isset($schedule) ? 'Atualizar' : 'Agendar' ?> Exame
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Quando a turma mudar, carregar disciplinas
    $('#class_id').change(function() {
        let classId = $(this).val();
        let disciplineSelect = $('#discipline_id');
        
        if (classId) {
            $.ajax({
                url: '<?= site_url('admin/exams/schedules/get-disciplines') ?>/' + classId,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    disciplineSelect.html('<option value="">Carregando...</option>').prop('disabled', true);
                },
                success: function(disciplines) {
                    let options = '<option value="">Selecione uma disciplina...</option>';
                    
                    if (disciplines && disciplines.length > 0) {
                        disciplines.forEach(function(discipline) {
                            options += `<option value="${discipline.id}">${discipline.discipline_name}</option>`;
                        });
                    } else {
                        options = '<option value="">Nenhuma disciplina disponível</option>';
                    }
                    
                    disciplineSelect.html(options).prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('Erro:', error);
                    disciplineSelect.html('<option value="">Erro ao carregar disciplinas</option>').prop('disabled', false);
                    
                    // Mostrar mensagem de erro com toastr ou sweetalert
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Erro ao carregar disciplinas');
                    }
                }
            });
        } else {
            disciplineSelect.html('<option value="">Primeiro selecione a turma...</option>').prop('disabled', true);
        }
    });
    
    // Se já temos turma selecionada (modo edição), carregar disciplinas automaticamente
    <?php if (isset($schedule) && $schedule['class_id']): ?>
    $('#class_id').trigger('change');
    <?php endif; ?>
    
    // Validação do formulário
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Validar campos obrigatórios
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            
            // Mostrar mensagem de erro
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos obrigatórios',
                    text: 'Preencha todos os campos obrigatórios (*)',
                    confirmButtonColor: 'var(--warning)'
                });
            } else {
                alert('Preencha todos os campos obrigatórios (*)');
            }
        }
    });
    
    // Remover classe is-invalid quando o campo for preenchido
    $('[required]').on('input change', function() {
        if ($(this).val()) {
            $(this).removeClass('is-invalid');
        }
    });
});
</script>

<style>
/* Estilos adicionais específicos para esta página */
.required:after {
    content: " *";
    color: var(--danger);
    font-weight: normal;
}

/* Ajustes para inputs em estado de erro */
.form-input-ci.is-invalid,
.filter-select.is-invalid {
    border-color: var(--danger) !important;
    background: rgba(232,70,70,0.02) !important;
}

.form-input-ci.is-invalid:focus,
.filter-select.is-invalid:focus {
    box-shadow: 0 0 0 3px rgba(232,70,70,0.12) !important;
}

.form-error {
    font-size: 0.72rem;
    color: var(--danger);
    margin-top: 0.3rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

/* Animação */
.ci-card {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsividade */
@media (max-width: 768px) {
    .btn-ci, .btn-filter {
        width: 100%;
    }
    
    .d-flex.gap-2 {
        flex-wrap: wrap;
    }
}
</style>
<?= $this->endSection() ?>