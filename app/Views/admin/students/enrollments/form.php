<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
/* Estilos específicos para o formulário de matrícula */
.form-section {
    margin-bottom: 2rem;
}

.form-section-title {
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
    margin-bottom: 1.25rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-section-title i {
    color: var(--accent);
    font-size: 0.9rem;
}

.prereg-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.prereg-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--warning), var(--accent));
}

.prereg-card-header {
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.prereg-card-header i {
    color: var(--warning);
    font-size: 1rem;
}

.prereg-card-header h5 {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

.prereg-card-body {
    padding: 1.25rem;
}

.prereg-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.prereg-info-item {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.75rem 1rem;
}

.prereg-info-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--text-muted);
    margin-bottom: 0.2rem;
}

.prereg-info-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-primary);
}

.status-badge-form {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 1rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
}

.status-badge-form.pending {
    background: rgba(232,160,32,0.1);
    color: var(--warning);
    border: 1px solid rgba(232,160,32,0.3);
}

.status-badge-form.active {
    background: rgba(22,168,125,0.1);
    color: var(--success);
    border: 1px solid rgba(22,168,125,0.3);
}

.status-badge-form.completed {
    background: rgba(59,127,232,0.1);
    color: var(--accent);
    border: 1px solid rgba(59,127,232,0.3);
}

.status-badge-form.cancelled {
    background: rgba(232,70,70,0.1);
    color: var(--danger);
    border: 1px solid rgba(232,70,70,0.3);
}

.class-info-box {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.6rem 1rem;
    margin-top: 0.5rem;
    font-size: 0.8rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.class-info-box i {
    color: var(--accent);
}

.alert-ci {
    border-radius: var(--radius-sm);
    border: none;
    font-size: 0.85rem;
    padding: 0.8rem 1rem;
}

.alert-ci.success {
    background: rgba(22,168,125,0.08);
    color: #0E7A5A;
    border-left: 3px solid var(--success);
}

.alert-ci.warning {
    background: rgba(232,160,32,0.08);
    color: #8A5D00;
    border-left: 3px solid var(--warning);
}

.alert-ci.info {
    background: rgba(59,127,232,0.08);
    color: #1E4D8C;
    border-left: 3px solid var(--accent);
}

.alert-ci.danger {
    background: rgba(232,70,70,0.08);
    color: #B03030;
    border-left: 3px solid var(--danger);
}

.readonly-field {
    background: var(--surface);
    padding: 0.6rem 0.9rem;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-muted);
    font-size: 0.875rem;
}

.enrollment-number-display {
    font-family: var(--font-mono);
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--accent);
    padding: 0.4rem 0.8rem;
    background: rgba(59,127,232,0.08);
    border-radius: var(--radius-sm);
    display: inline-block;
}
</style>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-<?= $enrollment ? 'edit' : 'plus-circle' ?> me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= route_to('admin.dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= route_to('students.enrollments') ?>">Matrículas</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
                </ol>
            </nav>
        </div>
        <?php if ($enrollment): ?>
            <?php
            $statusClass = match($enrollment['status']) {
                'Pendente' => 'warning',
                'Ativo' => 'success',
                'Concluído' => 'info',
                'Cancelado' => 'danger',
                default => 'secondary'
            };
            $statusIcon = match($enrollment['status']) {
                'Pendente' => 'clock',
                'Ativo' => 'check-circle',
                'Concluído' => 'check-double',
                'Cancelado' => 'ban',
                default => 'circle'
            };
            ?>
            <span class="status-badge-form <?= strtolower($enrollment['status']) ?>">
                <i class="fas fa-<?= $statusIcon ?>"></i> <?= $enrollment['status'] ?>
            </span>
        <?php else: ?>
            <span class="status-badge-form pending">
                <i class="fas fa-clock"></i> Nova Matrícula
            </span>
        <?php endif; ?>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Formulário -->
<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-title">
            <i class="fas fa-<?= $enrollment ? 'edit' : 'plus-circle' ?>"></i>
            <?= $title ?>
            <?php if ($enrollment): ?>
                <?php if ($enrollment['status'] == 'Pendente'): ?>
                    <span class="badge bg-warning ms-2">Passo 2 de 2 - Concluir Matrícula</span>
                <?php endif; ?>
            <?php else: ?>
                <span class="badge bg-info ms-2">Nova Matrícula</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="form-card-body">
        <form action="<?= site_url('admin/students/enrollments/save') ?>" method="post" id="enrollmentForm">
            <?= csrf_field() ?>
            
            <?php if ($enrollment): ?>
                <input type="hidden" name="id" value="<?= $enrollment['id'] ?>">
            <?php endif; ?>
            
            <!-- Dados da Pré-Matrícula (se for pendente) -->
            <?php if ($enrollment && $enrollment['status'] == 'Pendente' && isset($enrollment['grade_level_name'])): ?>
                <div class="alert-ci info mb-4">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Matrícula Pendente</strong> - Complete os dados abaixo para ativar a matrícula.
                </div>
                
                <div class="prereg-card mb-4">
                    <div class="prereg-card-header">
                        <i class="fas fa-clipboard-list"></i>
                        <h5>Dados da Pré-Matrícula</h5>
                    </div>
                    <div class="prereg-card-body">
                        <div class="prereg-info-grid">
                            <div class="prereg-info-item">
                                <div class="prereg-info-label">Aluno</div>
                                <div class="prereg-info-value">
                                    <i class="fas fa-user-graduate me-1"></i>
                                    <?= $enrollment->student_name ?? $enrollment['first_name'] . ' ' . $enrollment['last_name'] ?>
                                </div>
                            </div>
                            
                            <div class="prereg-info-item">
                                <div class="prereg-info-label">Nº Matrícula</div>
                                <div class="prereg-info-value">
                                    <span class="enrollment-number-display"><?= $enrollment['student_number'] ?></span>
                                </div>
                            </div>
                            
                            <div class="prereg-info-item">
                                <div class="prereg-info-label">Nível Pretendido</div>
                                <div class="prereg-info-value">
                                    <span class="type-badge type-info"><?= $enrollment['grade_level_name'] ?? $enrollment->level_name ?></span>
                                </div>
                            </div>
                            
                            <div class="prereg-info-item">
                                <div class="prereg-info-label">Ano Letivo</div>
                                <div class="prereg-info-value">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <?= $enrollment['year_name'] ?>
                                </div>
                            </div>
                            
                            <div class="prereg-info-item">
                                <div class="prereg-info-label">Tipo</div>
                                <div class="prereg-info-value">
                                    <?php
                                    $typeClass = match($enrollment['enrollment_type']) {
                                        'Nova' => 'type-primary',
                                        'Renovação' => 'type-success',
                                        'Transferência' => 'type-warning',
                                        default => 'type-secondary'
                                    };
                                    ?>
                                    <span class="type-badge <?= $typeClass ?>"><?= $enrollment['enrollment_type'] ?></span>
                                </div>
                            </div>
                            
                            <div class="prereg-info-item">
                                <div class="prereg-info-label">Data Solicitação</div>
                                <div class="prereg-info-value">
                                    <i class="fas fa-clock me-1"></i>
                                    <?= date('d/m/Y', strtotime($enrollment['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Campo hidden para o nível (já definido na pré-matrícula) -->
                <input type="hidden" name="grade_level_id" value="<?= $enrollment['grade_level_id'] ?>">
            <?php endif; ?>
            
            <!-- Dados da Matrícula para Edição -->
            <?php if ($enrollment && $enrollment['status'] != 'Pendente'): ?>
                <div class="alert-ci info mb-4">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Modo Edição</strong> - Você está editando uma matrícula existente.
                </div>
            <?php endif; ?>
            
            <!-- Seção: Aluno e Nível -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-user-graduate"></i>
                    <span>Aluno e Nível de Ensino</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-ci" for="student_id">
                                <i class="fas fa-user"></i> Aluno <span class="req">*</span>
                            </label>
                            <select class="form-select-ci <?= session('errors.student_id') ? 'is-invalid' : '' ?>" 
                                    id="student_id" 
                                    name="student_id" 
                                    <?= ($enrollment && ($enrollment['status'] == 'Pendente' || $enrollment['status'] == 'Ativo')) ? 'disabled' : 'required' ?>>
                                <option value="">Selecione o aluno...</option>
                                <?php if (!empty($students)): ?>
                                    <?php 
                                    // Se students é um objeto único (edição) ou array (novo)
                                    $studentList = is_array($students) ? $students : [$students];
                                    ?>
                                    <?php foreach ($studentList as $student): ?>
                                        <?php 
                                        // Verificar se este aluno deve ser selecionado
                                        $isSelected = false;
                                        if (old('student_id') !== null) {
                                            $isSelected = (old('student_id') == $student['id']);
                                        } elseif ($enrollment && isset($enrollment['student_id'])) {
                                            $isSelected = ($enrollment['student_id'] == $student['id']);
                                        }
                                        ?>
                                        <option value="<?= $student['id'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                                            <?= $student['first_name'] ?> <?= $student['last_name'] ?> (<?= $student['student_number'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (session('errors.student_id')): ?>
                                <div class="form-error"><?= session('errors.student_id') ?></div>
                            <?php endif; ?>
                            <?php if ($enrollment && ($enrollment['status'] == 'Pendente' || $enrollment['status'] == 'Ativo')): ?>
                                <input type="hidden" name="student_id" value="<?= $enrollment['student_id'] ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-ci" for="grade_level_id">
                                <i class="fas fa-layer-group"></i> Nível de Ensino <span class="req">*</span>
                            </label>
                            <select class="form-select-ci <?= session('errors.grade_level_id') ? 'is-invalid' : '' ?>" 
                                    id="grade_level_id" 
                                    name="grade_level_id" 
                                    required
                                    <?= ($enrollment && $enrollment['status'] == 'Ativo') ? 'disabled' : '' ?>>
                                <option value="">Selecione o nível...</option>
                                <?php if (!empty($gradeLevels)): ?>
                                    <?php foreach ($gradeLevels as $level): ?>
                                        <?php 
                                        // Verificar se este nível deve ser selecionado
                                        $isSelected = false;
                                        
                                        if (old('grade_level_id') !== null) {
                                            $isSelected = (old('grade_level_id') == $level->id);
                                        } elseif ($enrollment && isset($enrollment['grade_level_id'])) {
                                            $isSelected = ($enrollment['grade_level_id'] == $level->id);
                                        }
                                        ?>
                                        <option value="<?= $level->id ?>" <?= $isSelected ? 'selected' : '' ?>>
                                            <?= $level->level_name ?> (<?= $level->education_level ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (session('errors.grade_level_id')): ?>
                                <div class="form-error"><?= session('errors.grade_level_id') ?></div>
                            <?php endif; ?>
                            <?php if ($enrollment && $enrollment['status'] == 'Ativo'): ?>
                                <input type="hidden" name="grade_level_id" value="<?= $enrollment['grade_level_id'] ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Campo Curso (Ensino Médio) -->
            <div class="row g-3" id="courseField" style="<?= (isset($enrollment) && $enrollment['grade_level_id'] >= 13 && $enrollment['grade_level_id'] <= 16) ? '' : 'display: none;' ?>">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-ci" for="course_id">
                            <i class="fas fa-graduation-cap"></i> Curso (Ensino Médio)
                        </label>
                        <select class="form-select-ci <?= session('errors.course_id') ? 'is-invalid' : '' ?>" 
                                id="course_id" 
                                name="course_id">
                            <option value="">Ensino Geral (sem curso específico)</option>
                            <?php if (!empty($courses)): ?>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?= $course->id ?>" 
                                        <?= (old('course_id', $enrollment['course_id'] ?? '') == $course->id) ? 'selected' : '' ?>>
                                        <?= $course['course_name'] ?> (<?= $course->course_code ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Selecione o curso apenas para alunos do Ensino Médio (10ª à 13ª classe)
                        </div>
                        <?php if (session('errors.course_id')): ?>
                            <div class="form-error"><?= session('errors.course_id') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Seção: Turma e Ano Letivo -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-school"></i>
                    <span>Turma e Ano Letivo</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-ci" for="class_id">
                                <i class="fas fa-users"></i> Turma 
                                <?php if ($enrollment && $enrollment['status'] == 'Pendente'): ?>
                                    <span class="req">* (Obrigatório para concluir)</span>
                                <?php else: ?>
                                    <span class="req">*</span>
                                <?php endif; ?>
                            </label>
                            <select class="form-select-ci <?= session('errors.class_id') ? 'is-invalid' : '' ?>" 
                                    id="class_id" 
                                    name="class_id" 
                                    required>
                                <option value="">Selecione a turma...</option>
                                <?php 
                             
                                // Verificar qual array de classes usar
                                $classesList = [];
                                if (isset($classes_enrolled_count) && !empty($classes_enrolled_count)) {
                                    $classesList = $classes_enrolled_count;
                                } elseif (isset($classes) && !empty($classes)) {
                                    $classesList = $classes;
                                }
                                
                                // Determinar a turma selecionada
                                $currentSelectedClass = null;
                                if (old('class_id') !== null) {
                                    $currentSelectedClass = old('class_id');
                                } elseif ($enrollment && isset($enrollment->class_id)) {
                                    $currentSelectedClass = $enrollment->class_id;
                                }
                                ?>
                                
                                <?php if (!empty($classesList)): ?>
                                    <?php foreach ($classesList as $class): ?>
                                        <?php 
                                        // Calcular vagas disponíveis
                                        $capacity = $class['capacity']  ?? 0;
                                        $enrolled = isset($class['enrolled_count'] ) ? $class['enrolled_count']  : 0;
                                        $availableSeats = $capacity - $enrolled;
                                        
                                        // Verificar se é a turma selecionada
                                        $isSelected = ($currentSelectedClass == $class->id);
                                        
                                        // Desabilitar se não houver vagas (a menos que seja a turma já selecionada)
                                        $isDisabled = ($availableSeats <= 0 && !$isSelected) ? 'disabled' : '';
                                        
                                        // Obter nome do nível
                                        $levelName = $class->level_name ?? '';
                                        ?>
                                        <option value="<?= $class->id ?>" 
                                            data-capacity="<?= $capacity ?>"
                                            data-level="<?= $levelName ?>"
                                            data-available="<?= $availableSeats ?>"
                                            <?= $isSelected ? 'selected' : '' ?>
                                            <?= $isDisabled ?>>
                                            <?= $class->class_name ?> 
                                            <?php if (!empty($class->class_code)): ?>(<?= $class->class_code ?>)<?php endif; ?> - 
                                            <?= $class->class_shift ?? 'N/A' ?>
                                            <?php if (!empty($levelName)): ?>- <?= $levelName ?><?php endif; ?>
                                            (<?= $availableSeats ?> vagas)
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>Nenhuma turma disponível para este nível e ano letivo</option>
                                <?php endif; ?>
                            </select>
                            <?php if (session('errors.class_id')): ?>
                                <div class="form-error"><?= session('errors.class_id') ?></div>
                            <?php endif; ?>
                            
                            <div class="class-info-box" id="classInfo"></div>
                            
                            <?php if (empty($classesList) && $enrollment && $enrollment['status'] == 'Pendente'): ?>
                                <div class="alert-ci warning mt-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Não existem turmas disponíveis para esta matrícula. Selecione outro nível ou ano letivo.
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($enrollment && $enrollment['status'] == 'Pendente' && empty($currentSelectedClass)): ?>
                                <div class="alert-ci warning mt-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Esta matrícula está pendente. Selecione uma turma para ativá-la.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-ci" for="academic_year_id">
                                <i class="fas fa-calendar-alt"></i> Ano Letivo <span class="req">*</span>
                            </label>
                            <select class="form-select-ci <?= session('errors.academic_year_id') ? 'is-invalid' : '' ?>" 
                                    id="academic_year_id" 
                                    name="academic_year_id" 
                                    <?= ($enrollment && ($enrollment['status'] == 'Pendente' || $enrollment['status'] == 'Ativo')) ? 'disabled' : 'required' ?>>
                                <option value="">Selecione...</option>
                                <?php if (!empty($academicYears)): ?>
                                    <?php foreach ($academicYears as $year): ?>
                                        <?php 
                                        // Verificar se este ano deve ser selecionado
                                        $isSelected = false;
                                        if (old('academic_year_id') !== null) {
                                            $isSelected = (old('academic_year_id') == $year['id']);
                                        } elseif ($enrollment && isset($enrollment->academic_year_id)) {
                                            $isSelected = ($enrollment->academic_year_id == $year['id']);
                                        } elseif (!$enrollment && $currentYear && $currentYear['id'] == $year['id']) {
                                            $isSelected = true;
                                        }
                                        ?>
                                        <option value="<?= $year['id'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                                            <?= $year['year_name'] ?> <?= $year['id'] == current_academic_year() ? '(Atual)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (session('errors.academic_year_id')): ?>
                                <div class="form-error"><?= session('errors.academic_year_id') ?></div>
                            <?php endif; ?>
                            <?php if ($enrollment && ($enrollment['status'] == 'Pendente' || $enrollment['status'] == 'Ativo')): ?>
                                <input type="hidden" name="academic_year_id" value="<?= $enrollment->academic_year_id ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Seção: Datas e Tipos -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-clock"></i>
                    <span>Datas e Tipos</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label-ci" for="enrollment_date">
                                <i class="fas fa-calendar"></i> Data da Matrícula <span class="req">*</span>
                            </label>
                            <input type="date" 
                                   class="form-input-ci <?= session('errors.enrollment_date') ? 'is-invalid' : '' ?>" 
                                   id="enrollment_date" 
                                   name="enrollment_date" 
                                   value="<?= old('enrollment_date', $enrollment['enrollment_date'] ?? date('Y-m-d')) ?>"
                                   required>
                            <?php if (session('errors.enrollment_date')): ?>
                                <div class="form-error"><?= session('errors.enrollment_date') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label-ci" for="enrollment_type">
                                <i class="fas fa-file-signature"></i> Tipo de Matrícula <span class="req">*</span>
                            </label>
                            <select class="form-select-ci <?= session('errors.enrollment_type') ? 'is-invalid' : '' ?>" 
                                    id="enrollment_type" 
                                    name="enrollment_type" 
                                    <?= ($enrollment && ($enrollment['status'] == 'Pendente' || $enrollment['status'] == 'Ativo')) ? 'disabled' : 'required' ?>>
                                <option value="">Selecione...</option>
                                <?php 
                                $enrollmentTypes = ['Nova', 'Renovação', 'Transferência'];
                                foreach ($enrollmentTypes as $type): 
                                    $isSelected = false;
                                    if (old('enrollment_type') !== null) {
                                        $isSelected = (old('enrollment_type') == $type);
                                    } elseif ($enrollment && isset($enrollment['enrollment_type'])) {
                                        $isSelected = ($enrollment['enrollment_type'] == $type);
                                    }
                                ?>
                                    <option value="<?= $type ?>" <?= $isSelected ? 'selected' : '' ?>>
                                        <?= $type ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session('errors.enrollment_type')): ?>
                                <div class="form-error"><?= session('errors.enrollment_type') ?></div>
                            <?php endif; ?>
                            <?php if ($enrollment && ($enrollment['status'] == 'Pendente' || $enrollment['status'] == 'Ativo')): ?>
                                <input type="hidden" name="enrollment_type" value="<?= $enrollment['enrollment_type'] ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label-ci" for="enrollment_number">
                                <i class="fas fa-hashtag"></i> Número da Matrícula
                            </label>
                            <div class="readonly-field">
                                <?= old('enrollment_number', $enrollment['enrollment_number'] ?? '(gerado automaticamente)') ?>
                            </div>
                            <div class="form-hint">Gerado automaticamente pelo sistema</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Seção: Informações Adicionais -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-info-circle"></i>
                    <span>Informações Adicionais</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-ci" for="previous_grade_id">
                                <i class="fas fa-history"></i> Classe Anterior
                            </label>
                            <select class="form-select-ci" id="previous_grade_id" name="previous_grade_id">
                                <option value="">Selecione...</option>
                                <?php if (!empty($gradeLevels)): ?>
                                    <?php foreach ($gradeLevels as $level): ?>
                                        <?php 
                                        $isSelected = false;
                                        if (old('previous_grade_id') !== null) {
                                            $isSelected = (old('previous_grade_id') == $level->id);
                                        } elseif ($enrollment && isset($enrollment->previous_grade_id)) {
                                            $isSelected = ($enrollment->previous_grade_id == $level->id);
                                        }
                                        ?>
                                        <option value="<?= $level->id ?>" <?= $isSelected ? 'selected' : '' ?>>
                                            <?= $level->level_name ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-hint">Classe que o aluno concluiu anteriormente</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-ci" for="status">
                                <i class="fas fa-flag"></i> Status da Matrícula
                            </label>
                            <select class="form-select-ci" id="status" name="status">
                                <?php 
                                $statusOptions = ['Pendente', 'Ativo', 'Concluído', 'Cancelado'];
                                foreach ($statusOptions as $opt): 
                                    $isSelected = false;
                                    if (old('status') !== null) {
                                        $isSelected = (old('status') == $opt);
                                    } elseif ($enrollment && isset($enrollment['status'])) {
                                        $isSelected = ($enrollment['status'] == $opt);
                                    } elseif (!$enrollment && $opt == 'Pendente') {
                                        $isSelected = true;
                                    }
                                ?>
                                    <option value="<?= $opt ?>" <?= $isSelected ? 'selected' : '' ?>>
                                        <?= $opt ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-hint">Altere o status conforme necessário</div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label-ci" for="observations">
                                <i class="fas fa-sticky-note"></i> Observações
                            </label>
                            <textarea class="form-input-ci" id="observations" name="observations" rows="3"><?= old('observations', $enrollment->observations ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Campo hidden para o nível (se veio da pré-matrícula) -->
            <?php if ($enrollment && $enrollment['status'] == 'Pendente' && !isset($enrollment['grade_level_name'])): ?>
                <input type="hidden" name="grade_level_id" value="<?= $enrollment['grade_level_id'] ?>">
            <?php endif; ?>
            
            <!-- Alertas de Status -->
            <?php if ($enrollment && $enrollment['status'] == 'Pendente'): ?>
                <div class="alert-ci success mt-3">
                    <i class="fas fa-check-circle me-1"></i>
                    <strong>Concluir Matrícula:</strong> Após selecionar a turma e salvar, a matrícula será ativada.
                </div>
            <?php endif; ?>
            
            <?php if ($enrollment && $enrollment['status'] == 'Ativo'): ?>
                <div class="alert-ci success mt-3">
                    <i class="fas fa-check-circle me-1"></i>
                    <strong>Matrícula Ativa</strong> - Esta matrícula está ativa e o aluno está regularmente matriculado.
                </div>
            <?php endif; ?>
            
            <hr class="my-4">
            
            <!-- Form Footer -->
            <div class="form-footer">
                <div class="d-flex">
                    <a href="<?= route_to('students.enrollments') ?>" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
                <div>
                    <?php if ($enrollment && $enrollment['status'] == 'Pendente'): ?>
                        <button type="submit" class="btn-save" style="background: var(--success);">
                            <i class="fas fa-check-circle"></i> Concluir e Ativar Matrícula
                        </button>
                    <?php elseif ($enrollment && $enrollment['status'] == 'Ativo'): ?>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Atualizar Matrícula
                        </button>
                    <?php else: ?>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Salvar Matrícula
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Configuração CSRF para AJAX
const csrfToken = '<?= csrf_token() ?>';
const csrfHash = '<?= csrf_hash() ?>';

document.addEventListener('DOMContentLoaded', function() {
    // Elementos do formulário
    const gradeLevelSelect = document.getElementById('grade_level_id');
    const courseField = document.getElementById('courseField');
    const classSelect = document.getElementById('class_id');
    const yearSelect = document.getElementById('academic_year_id');
    const classInfo = document.getElementById('classInfo');
    const statusSelect = document.getElementById('status');
    const enrollmentForm = document.getElementById('enrollmentForm');

    // Função para mostrar/esconder campo de curso baseado no nível
    function toggleCourseField() {
        const levelId = parseInt(gradeLevelSelect.value);
        // Níveis do Ensino Médio são IDs 13-16
        if (levelId >= 13 && levelId <= 16) {
            courseField.style.display = 'flex';
        } else {
            courseField.style.display = 'none';
            document.getElementById('course_id').value = ''; // Limpar seleção
        }
    }

    // Adicionar evento change ao select de nível
    gradeLevelSelect.addEventListener('change', toggleCourseField);

    // Verificar estado inicial
    if (gradeLevelSelect.value) {
        toggleCourseField();
    }

    // Atualizar informações da turma quando selecionada
    classSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            const capacity = selected.dataset.capacity;
            const level = selected.dataset.level;
            const available = selected.dataset.available;
            classInfo.textContent = `Capacidade: ${capacity} alunos | Nível: ${level} | Vagas: ${available}`;
        } else {
            classInfo.textContent = '';
        }
    });

    // Verificar disponibilidade de vagas em tempo real
    classSelect.addEventListener('change', function() {
        const classId = this.value;
        if (classId) {
            fetch(`<?= site_url('admin/classes/check-availability/') ?>/${classId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfHash
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.available <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atenção!',
                        text: 'Esta turma não possui vagas disponíveis!',
                        confirmButtonColor: 'var(--warning)'
                    });
                    this.value = '';
                    classInfo.textContent = '';
                    
                    // Recarregar turmas
                    if (gradeLevelSelect.value) {
                        gradeLevelSelect.dispatchEvent(new Event('change'));
                    }
                }
            })
            .catch(error => console.error('Erro:', error));
        }
    });

    // Carregar turmas baseadas no nível e ano selecionados
    function loadClassesByLevelAndYear() {
        const levelId = gradeLevelSelect.value;
        const yearId = yearSelect.value;
        
        if (levelId && yearId) {
            fetch(`<?= site_url('admin/classes/get-by-level-and-year/') ?>${levelId}/${yearId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfHash
                }
            })
            .then(response => response.json())
            .then(classes => {
                // Guardar o valor atual selecionado
                const currentSelectedValue = classSelect.value;
                
                // Limpar o select
                classSelect.innerHTML = '<option value="">Selecione a turma...</option>';
                
                if (classes.length === 0) {
                    classSelect.innerHTML += '<option value="" disabled>Nenhuma turma disponível para este nível</option>';
                    classInfo.textContent = '';
                } else {
                    classes.forEach(cls => {
                        // Calcular vagas disponíveis
                        const capacity = cls.capacity || 0;
                        const enrolled = cls.enrolled_count || 0;
                        const available = capacity - enrolled;
                        const disabled = available <= 0 ? 'disabled' : '';
                        
                        // Verificar se é a turma atualmente selecionada
                        const isSelected = (cls.id == currentSelectedValue) ? 'selected' : '';
                        
                        classSelect.innerHTML += `<option value="${cls.id}" 
                            data-capacity="${capacity}"
                            data-level="${cls.level_name || ''}"
                            data-available="${available}"
                            ${isSelected}
                            ${disabled}>
                            ${cls.class_name || ''} ${cls.class_code ? '(' + cls.class_code + ')' : ''} - ${cls.class_shift || 'N/A'} ${cls.level_name ? '- ' + cls.level_name : ''}
                            (${available} vagas)
                        </option>`;
                    });
                }
                
                // Disparar evento change para atualizar info
                if (classSelect.value) {
                    classSelect.dispatchEvent(new Event('change'));
                } else {
                    classInfo.textContent = '';
                }
            })
            .catch(error => console.error('Erro ao carregar turmas:', error));
        }
    }

    // Evento change do nível
    gradeLevelSelect.addEventListener('change', loadClassesByLevelAndYear);

    // Evento change do ano (recarregar turmas se necessário)
    yearSelect.addEventListener('change', function() {
        if (gradeLevelSelect.value) {
            loadClassesByLevelAndYear();
        }
    });

    // Disparar change inicial para carregar turmas se já houver nível selecionado
    if (gradeLevelSelect.value) {
        console.log('Carregando turmas para o nível:', gradeLevelSelect.value);
        gradeLevelSelect.dispatchEvent(new Event('change'));
    }

    // Se já houver uma turma selecionada (edição de matrícula ativa)
    if (classSelect.value) {
        console.log('Turma selecionada:', classSelect.value);
        classSelect.dispatchEvent(new Event('change'));
    }

    // Validação do formulário antes de enviar
    enrollmentForm.addEventListener('submit', function(e) {
        const status = statusSelect.value;
        const classId = classSelect.value;
        
        if (status == 'Ativo' && !classId) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Atenção!',
                text: 'Para ativar a matrícula, é necessário selecionar uma turma.',
                confirmButtonColor: 'var(--warning)'
            });
        }
        
        // Log para debug
        console.log('Enviando formulário com status:', status);
    });

    // Função para confirmar cancelamento
    window.confirmCancel = function() {
        return Swal.fire({
            title: 'Confirmar Cancelamento',
            text: 'Tem certeza que deseja cancelar esta matrícula? Esta ação não pode ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--danger)',
            cancelButtonColor: 'var(--border)',
            confirmButtonText: 'Sim, cancelar',
            cancelButtonText: 'Não, voltar'
        }).then((result) => {
            return result.isConfirmed;
        });
    };
});

// Função para verificar elegibilidade
function checkEligibility() {
    const studentId = document.getElementById('student_id').value;
    const academicYearId = document.getElementById('academic_year_id').value;
    
    if (studentId && academicYearId) {
        fetch(`<?= site_url('admin/academic-records/check-eligibility') ?>/${studentId}/${academicYearId}`)
            .then(response => response.json())
            .then(data => {
                if (!data.eligible) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Alerta!',
                        text: data.message,
                        confirmButtonColor: 'var(--warning)'
                    });
                }
            })
            .catch(error => console.error('Erro ao verificar elegibilidade:', error));
    }
}
</script>
<?= $this->endSection() ?>