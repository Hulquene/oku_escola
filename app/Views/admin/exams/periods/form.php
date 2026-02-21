<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-plus me-1"></i>
                <?= isset($period) ? 'Edite as informações do período de exame' : 'Preencha os dados para criar um novo período' ?>
            </p>
        </div>
        <div>
            <a href="<?= site_url('admin/exams/periods') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/periods') ?>">Períodos</a></li>
            <li class="breadcrumb-item active"><?= isset($period) ? 'Editar' : 'Novo' ?> Período</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-<?= isset($period) ? 'edit' : 'plus' ?> me-2 text-primary"></i>
                    <?= isset($period) ? 'Editar' : 'Novo' ?> Período de Exame
                </h5>
            </div>
            <div class="card-body">
                <form method="post" action="<?= site_url('admin/exams/periods/save') ?>">
                    <?= csrf_field() ?>
                    
                    <?php if (isset($period)): ?>
                        <input type="hidden" name="id" value="<?= $period->id ?>">
                    <?php endif; ?>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nome do Período <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?= session('errors.period_name') ? 'is-invalid' : '' ?>" 
                                       name="period_name" 
                                       value="<?= old('period_name') ?: ($period->period_name ?? '') ?>"
                                       placeholder="Ex: 1ª Época - 1º Semestre 2024" required>
                                <?php if (session('errors.period_name')): ?>
                                    <div class="invalid-feedback"><?= session('errors.period_name') ?></div>
                                <?php endif; ?>
                                <div class="form-text">Nome descritivo para identificar o período</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Ano Letivo <span class="text-danger">*</span></label>
                                <select class="form-select <?= session('errors.academic_year_id') ? 'is-invalid' : '' ?>" 
                                        name="academic_year_id" id="academicYear" required>
                                    <option value="">Selecione</option>
                                    <?php foreach ($academicYears as $year): ?>
                                        <option value="<?= $year->id ?>" 
                                            <?= (old('academic_year_id') ?: ($period->academic_year_id ?? '')) == $year->id ? 'selected' : '' ?>
                                            data-start="<?= $year->start_date ?>" 
                                            data-end="<?= $year->end_date ?>">
                                            <?= $year->year_name ?> <?= $year->is_current ? '(Atual)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.academic_year_id')): ?>
                                    <div class="invalid-feedback"><?= session('errors.academic_year_id') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Semestre <span class="text-danger">*</span></label>
                                <select class="form-select <?= session('errors.semester_id') ? 'is-invalid' : '' ?>" 
                                        name="semester_id" id="semester" required>
                                    <option value="">Selecione</option>
                                    <?php foreach ($semesters as $semester): ?>
                                        <option value="<?= $semester->id ?>" 
                                            data-year="<?= $semester->academic_year_id ?>"
                                            <?= (old('semester_id') ?: ($period->semester_id ?? '')) == $semester->id ? 'selected' : '' ?>>
                                            <?= $semester->semester_name ?> (<?= $semester->semester_type ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.semester_id')): ?>
                                    <div class="invalid-feedback"><?= session('errors.semester_id') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tipo de Período <span class="text-danger">*</span></label>
                                <select class="form-select <?= session('errors.period_type') ? 'is-invalid' : '' ?>" 
                                        name="period_type" required>
                                    <option value="">Selecione</option>
                                    <option value="Normal" <?= (old('period_type') ?: ($period->period_type ?? '')) == 'Normal' ? 'selected' : '' ?>>Normal</option>
                                    <option value="Recurso" <?= (old('period_type') ?: ($period->period_type ?? '')) == 'Recurso' ? 'selected' : '' ?>>Recurso</option>
                                    <option value="Especial" <?= (old('period_type') ?: ($period->period_type ?? '')) == 'Especial' ? 'selected' : '' ?>>Especial</option>
                                    <option value="Final" <?= (old('period_type') ?: ($period->period_type ?? '')) == 'Final' ? 'selected' : '' ?>>Final</option>
                                    <option value="Admissão" <?= (old('period_type') ?: ($period->period_type ?? '')) == 'Admissão' ? 'selected' : '' ?>>Admissão</option>
                                </select>
                                <?php if (session('errors.period_type')): ?>
                                    <div class="invalid-feedback"><?= session('errors.period_type') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select <?= session('errors.status') ? 'is-invalid' : '' ?>" 
                                        name="status" id="status">
                                    <option value="Planejado" <?= (old('status') ?: ($period->status ?? 'Planejado')) == 'Planejado' ? 'selected' : '' ?>>Planejado</option>
                                    <option value="Em Andamento" <?= (old('status') ?: ($period->status ?? '')) == 'Em Andamento' ? 'selected' : '' ?>>Em Andamento</option>
                                    <option value="Concluído" <?= (old('status') ?: ($period->status ?? '')) == 'Concluído' ? 'selected' : '' ?>>Concluído</option>
                                    <option value="Cancelado" <?= (old('status') ?: ($period->status ?? '')) == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                </select>
                                <?php if (session('errors.status')): ?>
                                    <div class="invalid-feedback"><?= session('errors.status') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Data de Início <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control <?= session('errors.start_date') ? 'is-invalid' : '' ?>" 
                                       name="start_date" 
                                       id="startDate"
                                       value="<?= old('start_date') ?: ($period->start_date ?? '') ?>" required>
                                <?php if (session('errors.start_date')): ?>
                                    <div class="invalid-feedback"><?= session('errors.start_date') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Data de Fim <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control <?= session('errors.end_date') ? 'is-invalid' : '' ?>" 
                                       name="end_date" 
                                       id="endDate"
                                       value="<?= old('end_date') ?: ($period->end_date ?? '') ?>" required>
                                <?php if (session('errors.end_date')): ?>
                                    <div class="invalid-feedback"><?= session('errors.end_date') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Descrição</label>
                                <textarea class="form-control <?= session('errors.description') ? 'is-invalid' : '' ?>" 
                                          name="description" 
                                          rows="3"
                                          placeholder="Informações adicionais sobre este período..."><?= old('description') ?: ($period->description ?? '') ?></textarea>
                                <?php if (session('errors.description')): ?>
                                    <div class="invalid-feedback"><?= session('errors.description') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?= site_url('admin/exams/periods') ?>" class="btn btn-light">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    <?= isset($period) ? 'Atualizar' : 'Salvar' ?> Período
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Card de Ajuda -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-info-circle me-2 text-info"></i>
                    Informações
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="fw-semibold">Sobre os Períodos:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Organizam os exames por época
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Cada período pertence a um semestre
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Definem as datas para agendamento
                        </li>
                    </ul>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-semibold">Tipos de Período:</h6>
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between mb-2">
                            <span><strong>Normal:</strong></span>
                            <span>Época regular de exames</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><strong>Recurso:</strong></span>
                            <span>Para alunos em recuperação</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><strong>Especial:</strong></span>
                            <span>Casos especiais</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><strong>Final:</strong></span>
                            <span>Exames finais</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span><strong>Admissão:</strong></span>
                            <span>Exames de admissão</span>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Dica:</strong><br>
                    Após criar o período, utilize a opção "Gerar Calendário" para agendar automaticamente os exames para as turmas.
                </div>
            </div>
        </div>
        
        <!-- Card de Visualização de Datas -->
        <div class="card mt-3" id="dateInfoCard" style="display: none;">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Período Selecionado</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Duração:</span>
                    <span id="durationDays">0 dias</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Dias úteis:</span>
                    <span id="workDays">0 dias</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Fins de semana:</span>
                    <span id="weekendDays">0 dias</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Filtrar semestres por ano letivo
    $('#academicYear').change(function() {
        let yearId = $(this).val();
        
        $('#semester option').each(function() {
            if ($(this).val() === '') return;
            
            if ($(this).data('year') == yearId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        
        // Reset semester if hidden
        if ($('#semester option:selected').is(':hidden')) {
            $('#semester').val('');
        }
        
        // Set date limits
        let selectedYear = $(this).find('option:selected');
        let yearStart = selectedYear.data('start');
        let yearEnd = selectedYear.data('end');
        
        if (yearStart && yearEnd) {
            $('#startDate').attr('min', yearStart);
            $('#startDate').attr('max', yearEnd);
            $('#endDate').attr('min', yearStart);
            $('#endDate').attr('max', yearEnd);
        }
    });
    
    // Trigger on load if year is selected
    if ($('#academicYear').val()) {
        $('#academicYear').trigger('change');
    }
    
    // Calculate days between dates
    function calculateDateInfo() {
        let start = $('#startDate').val();
        let end = $('#endDate').val();
        
        if (start && end) {
            let startDate = new Date(start);
            let endDate = new Date(end);
            
            if (endDate >= startDate) {
                let diffTime = Math.abs(endDate - startDate);
                let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                
                // Count working days (Monday to Friday)
                let workDays = 0;
                let currentDate = new Date(startDate);
                
                while (currentDate <= endDate) {
                    let dayOfWeek = currentDate.getDay();
                    if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                        workDays++;
                    }
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                
                let weekendDays = diffDays - workDays;
                
                $('#durationDays').text(diffDays + ' dias');
                $('#workDays').text(workDays + ' dias');
                $('#weekendDays').text(weekendDays + ' dias');
                $('#dateInfoCard').show();
            } else {
                $('#dateInfoCard').hide();
            }
        } else {
            $('#dateInfoCard').hide();
        }
    }
    
    $('#startDate, #endDate').on('change', calculateDateInfo);
    
    // Validate end date >= start date on form submit
    $('form').submit(function(e) {
        let start = $('#startDate').val();
        let end = $('#endDate').val();
        
        if (start && end && new Date(end) < new Date(start)) {
            alert('A data de fim deve ser maior ou igual à data de início.');
            e.preventDefault();
            return false;
        }
        
        return true;
    });
});
</script>

<?= $this->endSection() ?>