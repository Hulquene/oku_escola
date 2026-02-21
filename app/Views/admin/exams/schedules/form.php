<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
        </div>
        <div>
            <a href="<?= site_url('admin/exams/schedules') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/schedules') ?>">Agendamentos</a></li>
            <li class="breadcrumb-item active"><?= isset($schedule) ? 'Editar' : 'Novo' ?> Agendamento</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-<?= isset($schedule) ? 'edit' : 'plus-circle' ?> me-2 text-primary"></i>
            <?= isset($schedule) ? 'Editar Agendamento' : 'Novo Agendamento de Exame' ?>
        </h5>
    </div>
    <div class="card-body">
        <form method="post" action="<?= isset($schedule) ? site_url('admin/exams/schedules/update/' . $schedule->id) : site_url('admin/exams/schedules/save') ?>" class="needs-validation" novalidate>
            <?= csrf_field() ?>
            
            <div class="row g-3">
                <!-- Período de Exame -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold required">Período de Exame</label>
                    <select class="form-select <?= session('errors.exam_period_id') ? 'is-invalid' : '' ?>" 
                            name="exam_period_id" required>
                        <option value="">Selecione um período...</option>
                        <?php foreach ($periods as $period): ?>
                            <option value="<?= $period->id ?>" 
                                <?= (old('exam_period_id') == $period->id || (isset($schedule) && $schedule->exam_period_id == $period->id)) ? 'selected' : '' ?>>
                                <?= $period->period_name ?> (<?= $period->period_type ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= session('errors.exam_period_id') ?? 'Selecione o período de exame' ?>
                    </div>
                </div>
                
                <!-- Turma -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold required">Turma</label>
                    <select class="form-select <?= session('errors.class_id') ? 'is-invalid' : '' ?>" 
                            name="class_id" id="class_id" required>
                        <option value="">Selecione uma turma...</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class->id ?>" 
                                <?= (old('class_id') == $class->id || (isset($schedule) && $schedule->class_id == $class->id)) ? 'selected' : '' ?>>
                                <?= $class->class_name ?> (<?= $class->class_code ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= session('errors.class_id') ?? 'Selecione a turma' ?>
                    </div>
                </div>
                
                <!-- Disciplina -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold required">Disciplina</label>
                    <select class="form-select <?= session('errors.discipline_id') ? 'is-invalid' : '' ?>" 
                            name="discipline_id" id="discipline_id" required>
                        <option value="">Primeiro selecione a turma...</option>
                        <?php if (isset($schedule)): ?>
                            <option value="<?= $schedule->discipline_id ?>" selected>
                                <?= $schedule->discipline_name ?>
                            </option>
                        <?php endif; ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= session('errors.discipline_id') ?? 'Selecione a disciplina' ?>
                    </div>
                </div>
                
                <!-- Tipo de Exame -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold required">Tipo de Exame</label>
                    <select class="form-select <?= session('errors.exam_board_id') ? 'is-invalid' : '' ?>" 
                            name="exam_board_id" required>
                        <option value="">Selecione o tipo...</option>
                        <?php foreach ($examBoards as $board): ?>
                            <option value="<?= $board->id ?>" 
                                <?= (old('exam_board_id') == $board->id || (isset($schedule) && $schedule->exam_board_id == $board->id)) ? 'selected' : '' ?>>
                                <?= $board->board_name ?> (<?= $board->board_type ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= session('errors.exam_board_id') ?? 'Selecione o tipo de exame' ?>
                    </div>
                </div>
                
                <!-- Data -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold required">Data do Exame</label>
                    <input type="date" class="form-control <?= session('errors.exam_date') ? 'is-invalid' : '' ?>" 
                           name="exam_date" value="<?= old('exam_date') ?? (isset($schedule) ? $schedule->exam_date : '') ?>" required>
                    <div class="invalid-feedback">
                        <?= session('errors.exam_date') ?? 'Selecione a data do exame' ?>
                    </div>
                </div>
                
                <!-- Hora -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Hora de Início</label>
                    <input type="time" class="form-control <?= session('errors.exam_time') ? 'is-invalid' : '' ?>" 
                           name="exam_time" value="<?= old('exam_time') ?? (isset($schedule) && $schedule->exam_time ? substr($schedule->exam_time, 0, 5) : '') ?>">
                    <div class="invalid-feedback"><?= session('errors.exam_time') ?></div>
                </div>
                
                <!-- Duração -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Duração (minutos)</label>
                    <input type="number" class="form-control <?= session('errors.duration_minutes') ? 'is-invalid' : '' ?>" 
                           name="duration_minutes" value="<?= old('duration_minutes') ?? (isset($schedule) ? $schedule->duration_minutes : 120) ?>" 
                           min="30" step="15">
                    <div class="invalid-feedback"><?= session('errors.duration_minutes') ?></div>
                </div>
                
                <!-- Sala -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Sala/Local</label>
                    <input type="text" class="form-control <?= session('errors.exam_room') ? 'is-invalid' : '' ?>" 
                           name="exam_room" value="<?= old('exam_room') ?? (isset($schedule) ? $schedule->exam_room : '') ?>" 
                           placeholder="Ex: Sala 101, Laboratório, etc.">
                    <div class="invalid-feedback"><?= session('errors.exam_room') ?></div>
                </div>
                
                <!-- Status (apenas para edição) -->
                <?php if (isset($schedule)): ?>
                <div class="col-md-6">
                    <label class="form-label fw-semibold required">Status</label>
                    <select class="form-select <?= session('errors.status') ? 'is-invalid' : '' ?>" name="status" required>
                        <option value="Agendado" <?= (old('status') ?? $schedule->status) == 'Agendado' ? 'selected' : '' ?>>Agendado</option>
                        <option value="Realizado" <?= (old('status') ?? $schedule->status) == 'Realizado' ? 'selected' : '' ?>>Realizado</option>
                        <option value="Cancelado" <?= (old('status') ?? $schedule->status) == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        <option value="Adiado" <?= (old('status') ?? $schedule->status) == 'Adiado' ? 'selected' : '' ?>>Adiado</option>
                    </select>
                    <div class="invalid-feedback">
                        <?= session('errors.status') ?? 'Selecione o status' ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Observações -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Observações</label>
                    <textarea class="form-control <?= session('errors.observations') ? 'is-invalid' : '' ?>" 
                              name="observations" rows="3" 
                              placeholder="Informações adicionais sobre o exame..."><?= old('observations') ?? (isset($schedule) ? $schedule->observations : '') ?></textarea>
                    <div class="invalid-feedback"><?= session('errors.observations') ?></div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= site_url('admin/exams/schedules') ?>" class="btn btn-light">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    <?= isset($schedule) ? 'Atualizar' : 'Agendar' ?> Exame
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script para carregar disciplinas por turma -->
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
                }
            });
        } else {
            disciplineSelect.html('<option value="">Primeiro selecione a turma...</option>').prop('disabled', true);
        }
    });
    
    // Se já temos turma selecionada (modo edição), carregar disciplinas automaticamente
    <?php if (isset($schedule) && $schedule->class_id): ?>
    $('#class_id').trigger('change');
    <?php endif; ?>
});
</script>

<style>
.required:after {
    content: " *";
    color: #dc3545;
    font-weight: normal;
}
</style>

<?= $this->endSection() ?>