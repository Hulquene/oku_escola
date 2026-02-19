<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes/class-subjects') ?>">Disciplinas por Turma</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Form -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-<?= $assignment ? 'edit' : 'plus-circle' ?> me-2"></i>
            <?= $title ?>
        </h5>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/classes/class-subjects/assign') ?>" method="post" id="assignmentForm">
            <?= csrf_field() ?>
            
            <?php if ($assignment): ?>
                <input type="hidden" name="id" value="<?= $assignment->id ?>">
            <?php endif; ?>
            
            <!-- STEP 1: Selecionar Curso -->
            <div class="card mb-3">
                <div class="card-header bg-light py-2">
                    <span class="fw-bold">Passo 1:</span> Selecione o Curso
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <select class="form-select" id="course_id" name="course_filter">
                                <option value="">-- Selecione um curso --</option>
                                <?php if (isset($hasGeneralEducation) && $hasGeneralEducation): ?>
                                    <option value="0" <?= ($selectedCourseId === 0 || $selectedCourseId === '0') ? 'selected' : '' ?>>Ensino Geral</option>
                                <?php endif; ?>
                                <?php if (!empty($courses)): ?>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?= $course->id ?>" <?= $selectedCourseId == $course->id ? 'selected' : '' ?>>
                                            <?= esc($course->course_name) ?> (<?= esc($course->course_code) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- STEP 2: Selecionar Nível -->
            <div class="card mb-3" id="levelCard" style="<?= !$selectedCourseId ? 'display:none;' : '' ?>">
                <div class="card-header bg-light py-2">
                    <span class="fw-bold">Passo 2:</span> Selecione o Nível
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <select class="form-select" id="grade_level_id" name="level_filter">
                                <option value="">-- Selecione um nível --</option>
                                <?php if (!empty($allLevels)): ?>
                                    <?php foreach ($allLevels as $level): ?>
                                        <option value="<?= $level->id ?>" <?= $selectedLevelId == $level->id ? 'selected' : '' ?>>
                                            <?= esc($level->level_name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- STEP 3: Selecionar Turma -->
            <div class="card mb-3" id="classCard" style="<?= !$selectedLevelId ? 'display:none;' : '' ?>">
                <div class="card-header bg-light py-2">
                    <span class="fw-bold">Passo 3:</span> Selecione a Turma <span class="text-danger">*</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <select class="form-select <?= session('errors.class_id') ? 'is-invalid' : '' ?>" 
                                    id="class_id" 
                                    name="class_id" 
                                    required>
                                <option value="">-- Selecione uma turma --</option>
                                <?php if (!empty($filteredClasses)): ?>
                                    <?php foreach ($filteredClasses as $class): ?>
                                        <option value="<?= $class->id ?>" 
                                            data-course-id="<?= $class->course_id ?? 0 ?>"
                                            data-level-id="<?= $class->grade_level_id ?>"
                                            <?= (old('class_id', $assignment->class_id ?? $selectedClass ?? '') == $class->id) ? 'selected' : '' ?>>
                                            <?= esc($class->class_name) ?> (<?= esc($class->class_code) ?>) - 
                                            <?= esc($class->class_shift) ?> - 
                                            <?= esc($class->year_name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (session('errors.class_id')): ?>
                                <div class="invalid-feedback"><?= session('errors.class_id') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- STEP 4: Selecionar Disciplina -->
            <div class="card mb-3" id="disciplineCard" style="display:none;">
                <div class="card-header bg-light py-2">
                    <span class="fw-bold">Passo 4:</span> Selecione a Disciplina <span class="text-danger">*</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <select class="form-select <?= session('errors.discipline_id') ? 'is-invalid' : '' ?>" 
                                    id="discipline_id" 
                                    name="discipline_id" 
                                    required>
                                <option value="">-- Selecione uma disciplina --</option>
                            </select>
                            <?php if (session('errors.discipline_id')): ?>
                                <div class="invalid-feedback"><?= session('errors.discipline_id') ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <div id="disciplineInfo" class="alert alert-info p-2 mb-0" style="display:none;">
                                <small><i class="fas fa-info-circle"></i> <span id="disciplineHint"></span></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Detalhes da atribuição -->
            <div class="card mb-3" id="detailsCard" style="display:none;">
                <div class="card-header bg-light py-2">
                    <span class="fw-bold">Detalhes da Atribuição</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="teacher_id" class="form-label fw-semibold">Professor</label>
                            <select class="form-select" id="teacher_id" name="teacher_id">
                                <option value="">-- Sem professor --</option>
                                <?php if (!empty($teachers)): ?>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= $teacher->id ?>" 
                                            <?= (old('teacher_id', $assignment->teacher_id ?? '') == $teacher->id) ? 'selected' : '' ?>>
                                            <?= esc($teacher->first_name) ?> <?= esc($teacher->last_name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="workload_hours" class="form-label fw-semibold">Carga Horária</label>
                            <input type="number" class="form-control" id="workload_hours" name="workload_hours" 
                                   value="<?= old('workload_hours', $assignment->workload_hours ?? '') ?>"
                                   min="0" max="999" step="1"
                                   placeholder="Sugerida">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="semester_id" class="form-label fw-semibold">Semestre</label>
                            <select class="form-select" id="semester_id" name="semester_id">
                                <option value="">-- Todos --</option>
                                <?php if (!empty($semesters)): ?>
                                    <?php foreach ($semesters as $semester): ?>
                                        <option value="<?= $semester->id ?>" 
                                            <?= (old('semester_id', $assignment->semester_id ?? '') == $semester->id) ? 'selected' : '' ?>>
                                            <?= esc($semester->semester_name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   <?= (old('is_active', $assignment->is_active ?? true)) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">
                                <strong>Ativo</strong> - Marque para que esta disciplina fique ativa na turma
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Botões de ação -->
            <div class="d-flex justify-content-between mt-4" id="formButtons" style="display:none;">
                <a href="<?= site_url('admin/classes/class-subjects') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Salvar Atribuição
                </button>
            </div>
            
            <!-- Mensagem de carregamento -->
            <div id="loadingMessage" class="text-center text-muted py-3" style="display:none;">
                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                <p>Carregando...</p>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    
    // Se estamos editando, disparar eventos para carregar os selects
    <?php if ($selectedCourseId !== null): ?>
        // Forçar o carregamento dos níveis
        $('#course_id').val('<?= $selectedCourseId ?>').trigger('change');
        
        // Após carregar os níveis, selecionar o nível
        setTimeout(function() {
            $('#grade_level_id').val('<?= $selectedLevelId ?>').trigger('change');
        }, 500);
    <?php endif; ?>
    
    // Quando o curso muda
    $('#course_id').change(function() {
        const courseId = $(this).val();
        $('#loadingMessage').show();
        
        if (courseId !== '') {
            // Mostrar card de nível
            $('#levelCard').show();
            
            // Limpar selects dependentes
            $('#grade_level_id').html('<option value="">Carregando níveis...</option>').prop('disabled', true);
            $('#class_id').html('<option value="">-- Selecione uma turma --</option>').prop('disabled', true);
            $('#discipline_id').html('<option value="">-- Selecione uma disciplina --</option>').prop('disabled', true);
            
            // Ocultar seções seguintes
            $('#classCard, #disciplineCard, #detailsCard, #formButtons').hide();
            
            // Buscar níveis por curso
            let url = courseId == '0' 
                ? '<?= site_url('admin/classes/class-subjects/get-levels-by-course') ?>'
                : '<?= site_url('admin/classes/class-subjects/get-levels-by-course/') ?>' + courseId;
            
            $.get(url, function(levels) {
                let options = '<option value="">-- Selecione um nível --</option>';
                $.each(levels, function(i, level) {
                    options += '<option value="' + level.id + '">' + level.level_name + '</option>';
                });
                $('#grade_level_id').html(options).prop('disabled', false);
                $('#loadingMessage').hide();
            }).fail(function() {
                $('#loadingMessage').hide();
                alert('Erro ao carregar níveis');
            });
        } else {
            $('#levelCard').hide();
            $('#classCard, #disciplineCard, #detailsCard, #formButtons').hide();
            $('#loadingMessage').hide();
        }
    });
    
    // Quando o nível muda
    $('#grade_level_id').change(function() {
        const levelId = $(this).val();
        const courseId = $('#course_id').val();
        $('#loadingMessage').show();
        
        if (levelId !== '') {
            // Mostrar card de turma
            $('#classCard').show();
            
            // Limpar selects dependentes
            $('#class_id').html('<option value="">Carregando turmas...</option>').prop('disabled', true);
            $('#discipline_id').html('<option value="">-- Selecione uma disciplina --</option>').prop('disabled', true);
            
            // Ocultar seções seguintes
            $('#disciplineCard, #detailsCard, #formButtons').hide();
            
            // Buscar turmas por curso e nível
            let url = '<?= site_url('admin/classes/class-subjects/get-classes-by-course-level/') ?>' 
                     + courseId + '/' + levelId;
            
            $.get(url, function(classes) {
                let options = '<option value="">-- Selecione uma turma --</option>';
                $.each(classes, function(i, cls) {
                    options += '<option value="' + cls.id + '" ' +
                               'data-course-id="' + (cls.course_id || 0) + '" ' +
                               'data-level-id="' + cls.grade_level_id + '">' +
                               cls.class_name + ' (' + cls.class_code + ') - ' + 
                               cls.class_shift + ' - ' + cls.year_name +
                               '</option>';
                });
                $('#class_id').html(options).prop('disabled', false);
                $('#loadingMessage').hide();
            }).fail(function() {
                $('#loadingMessage').hide();
                alert('Erro ao carregar turmas');
            });
        } else {
            $('#classCard').hide();
            $('#disciplineCard, #detailsCard, #formButtons').hide();
            $('#loadingMessage').hide();
        }
    });
    
    // Quando a turma muda
    $('#class_id').change(function() {
        const classId = $(this).val();
        $('#loadingMessage').show();
        
        if (classId !== '') {
            // Mostrar card de disciplina
            $('#disciplineCard').show();
            
            // Limpar select de disciplina
            $('#discipline_id').html('<option value="">Carregando disciplinas...</option>').prop('disabled', true);
            
            // Ocultar seções seguintes
            $('#detailsCard, #formButtons').hide();
            
            // Buscar disciplinas disponíveis para esta turma
            let url = '<?= site_url('admin/classes/class-subjects/get-available-disciplines/') ?>' + classId;
            
            $.get(url, function(disciplines) {
                if (disciplines.length > 0) {
                    let options = '<option value="">-- Selecione uma disciplina --</option>';
                    $.each(disciplines, function(i, disc) {
                        options += '<option value="' + disc.id + '" ' +
                                   'data-workload="' + (disc.suggested_workload || disc.workload_hours || '') + '" ' +
                                   'data-semester="' + (disc.suggested_semester || '') + '">' +
                                   disc.discipline_name + ' (' + disc.discipline_code + ')' +
                                   (disc.is_mandatory ? ' [Obrigatória]' : '') +
                                   '</option>';
                    });
                    $('#discipline_id').html(options).prop('disabled', false);
                } else {
                    $('#discipline_id').html('<option value="">-- Nenhuma disciplina disponível --</option>').prop('disabled', true);
                }
                $('#loadingMessage').hide();
            }).fail(function() {
                $('#loadingMessage').hide();
                alert('Erro ao carregar disciplinas');
            });
        } else {
            $('#disciplineCard').hide();
            $('#detailsCard, #formButtons').hide();
            $('#loadingMessage').hide();
        }
    });
    
    // Quando a disciplina muda
    $('#discipline_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const workload = selectedOption.data('workload');
        const semester = selectedOption.data('semester');
        
        if ($(this).val() !== '') {
            // Mostrar seções seguintes
            $('#detailsCard').show();
            $('#formButtons').show();
            
            // Sugerir carga horária se disponível
            if (workload) {
                $('#workload_hours').attr('placeholder', 'Sugerido: ' + workload + 'h');
                $('#disciplineInfo').show();
                $('#disciplineHint').text('Carga horária sugerida: ' + workload + 'h');
                
                // Se o campo estiver vazio, sugerir o valor
                if ($('#workload_hours').val() === '') {
                    $('#workload_hours').val(workload);
                }
            } else {
                $('#workload_hours').attr('placeholder', '');
                $('#disciplineInfo').hide();
            }
            
            // Sugerir semestre se disponível
            if (semester) {
                $('#semester_id option').each(function() {
                    if ($(this).text().toLowerCase().includes(semester.toLowerCase())) {
                        $(this).prop('selected', true);
                    }
                });
            }
        } else {
            $('#detailsCard').hide();
            $('#formButtons').hide();
        }
    });
    
});
</script>

<style>
.card {
    border: 1px solid rgba(0,0,0,0.125);
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 1rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0,0,0,0.125);
}

.form-label {
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

#loadingMessage {
    color: #6c757d;
}

/* Animações */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

/* Estilo para os passes */
.fw-bold {
    color: #495057;
}
</style>
<?= $this->endSection() ?>