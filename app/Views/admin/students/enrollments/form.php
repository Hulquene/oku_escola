<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students/enrollments') ?>">Matrículas</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-<?= $enrollment ? 'edit' : 'plus-circle' ?>"></i> <?= $title ?>
        <?php if ($enrollment): ?>
            <?php if ($enrollment->status == 'Pendente'): ?>
                <span class="badge bg-warning ms-2">Passo 2 de 2 - Concluir Matrícula</span>
            <?php elseif ($enrollment->status == 'Ativo'): ?>
                <span class="badge bg-success ms-2">Matrícula Ativa</span>
            <?php elseif ($enrollment->status == 'Concluído'): ?>
                <span class="badge bg-info ms-2">Matrícula Concluída</span>
            <?php elseif ($enrollment->status == 'Cancelado'): ?>
                <span class="badge bg-danger ms-2">Matrícula Cancelada</span>
            <?php endif; ?>
        <?php else: ?>
            <span class="badge bg-info ms-2">Nova Matrícula</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/students/enrollments/save') ?>" method="post" id="enrollmentForm">
            <?= csrf_field() ?>
            
            <?php if ($enrollment): ?>
                <input type="hidden" name="id" value="<?= $enrollment->id ?>">
            <?php endif; ?>
            
            <!-- Dados da Pré-Matrícula (se for pendente) -->
            <?php if ($enrollment && $enrollment->status == 'Pendente' && isset($enrollment->grade_level_name)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Matrícula Pendente</strong> - Complete os dados abaixo para ativar a matrícula.
                </div>
                
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning text-white">
                        <i class="fas fa-clipboard-list"></i> Dados da Pré-Matrícula
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Aluno:</strong><br> <?= $enrollment->student_name ?? $enrollment->first_name . ' ' . $enrollment->last_name ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Nº Matrícula:</strong><br> <?= $enrollment->student_number ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Nível Pretendido:</strong><br> <?= $enrollment->grade_level_name ?? $enrollment->level_name ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Ano Letivo:</strong><br> <?= $enrollment->year_name ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Tipo:</strong><br> <?= $enrollment->enrollment_type ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Data Solicitação:</strong><br> <?= date('d/m/Y', strtotime($enrollment->created_at)) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Campo hidden para o nível (já definido na pré-matrícula) -->
                <input type="hidden" name="grade_level_id" value="<?= $enrollment->grade_level_id ?>">
            <?php endif; ?>
            
            <!-- Dados da Matrícula para Edição -->
            <?php if ($enrollment && $enrollment->status != 'Pendente'): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Modo Edição</strong> - Você está editando uma matrícula existente.
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Aluno <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.student_id') ? 'is-invalid' : '' ?>" 
                                id="student_id" 
                                name="student_id" 
                                <?= ($enrollment && ($enrollment->status == 'Pendente' || $enrollment->status == 'Ativo')) ? 'disabled' : 'required' ?>>
                            <option value="">Selecione o aluno...</option>
                            <?php if (!empty($students)): ?>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?= $student->id ?>" 
                                        <?= ($selectedStudent == $student->id) ? 'selected' : '' ?>>
                                        <?= $student->first_name ?> <?= $student->last_name ?> (<?= $student->student_number ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.student_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.student_id') ?></div>
                        <?php endif; ?>
                        <?php if ($enrollment && ($enrollment->status == 'Pendente' || $enrollment->status == 'Ativo')): ?>
                            <input type="hidden" name="student_id" value="<?= $enrollment->student_id ?>">
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="grade_level_id" class="form-label">Nível de Ensino <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.grade_level_id') ? 'is-invalid' : '' ?>" 
                                id="grade_level_id" 
                                name="grade_level_id" 
                                required
                                <?= ($enrollment && $enrollment->status == 'Ativo') ? 'disabled' : '' ?>>
                            <option value="">Selecione o nível...</option>
                            <?php if (!empty($gradeLevels)): ?>
                                <?php foreach ($gradeLevels as $level): ?>
                                    <option value="<?= $level->id ?>" 
                                        <?= ($selectedGradeLevel == $level->id) ? 'selected' : '' ?>>
                                        <?= $level->level_name ?> (<?= $level->education_level ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.grade_level_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.grade_level_id') ?></div>
                        <?php endif; ?>
                        <?php if ($enrollment && $enrollment->status == 'Ativo'): ?>
                            <input type="hidden" name="grade_level_id" value="<?= $enrollment->grade_level_id ?>">
                        <?php endif; ?>
                        <small class="text-muted">Nível de ensino da matrícula</small>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="class_id" class="form-label">Turma 
                            <?php if ($enrollment && $enrollment->status == 'Pendente'): ?>
                                <span class="text-danger">* (Obrigatório para concluir)</span>
                            <?php else: ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </label>
                        <select class="form-select <?= session('errors.class_id') ? 'is-invalid' : '' ?>" 
                                id="class_id" 
                                name="class_id" 
                                required>
                            <option value="">Selecione a turma...</option>
                            <?php if (!empty($classes)): ?>
                                <?php foreach ($classes as $class): ?>
                                    <?php 
                                    $availableSeats = $class->available_seats ?? ($class->capacity - ($class->enrolled_count ?? 0));
                                    $isDisabled = ($availableSeats <= 0 && $selectedClass != $class->id) ? 'disabled' : '';
                                    ?>
                                    <option value="<?= $class->id ?>" 
                                        data-capacity="<?= $class->capacity ?>"
                                        data-level="<?= $class->level_name ?>"
                                        data-available="<?= $availableSeats ?>"
                                        <?= ($selectedClass == $class->id) ? 'selected' : '' ?>
                                        <?= $isDisabled ?>>
                                        <?= $class->class_name ?> (<?= $class->class_code ?>) - <?= $class->class_shift ?> - <?= $class->level_name ?>
                                        (<?= $availableSeats ?> vagas)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.class_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.class_id') ?></div>
                        <?php endif; ?>
                        <small class="text-muted" id="classInfo"></small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="academic_year_id" class="form-label">Ano Letivo <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.academic_year_id') ? 'is-invalid' : '' ?>" 
                                id="academic_year_id" 
                                name="academic_year_id" 
                                <?= ($enrollment && ($enrollment->status == 'Pendente' || $enrollment->status == 'Ativo')) ? 'disabled' : 'required' ?>>
                            <option value="">Selecione...</option>
                            <?php if (!empty($academicYears)): ?>
                                <?php foreach ($academicYears as $year): ?>
                                    <option value="<?= $year->id ?>" 
                                        <?= ($selectedYear == $year->id) ? 'selected' : '' ?>>
                                        <?= $year->year_name ?> <?= $year->is_current ? '(Atual)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.academic_year_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.academic_year_id') ?></div>
                        <?php endif; ?>
                        <?php if ($enrollment && ($enrollment->status == 'Pendente' || $enrollment->status == 'Ativo')): ?>
                            <input type="hidden" name="academic_year_id" value="<?= $enrollment->academic_year_id ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="enrollment_date" class="form-label">Data da Matrícula <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control <?= session('errors.enrollment_date') ? 'is-invalid' : '' ?>" 
                               id="enrollment_date" 
                               name="enrollment_date" 
                               value="<?= old('enrollment_date', $enrollment->enrollment_date ?? date('Y-m-d')) ?>"
                               required>
                        <?php if (session('errors.enrollment_date')): ?>
                            <div class="invalid-feedback"><?= session('errors.enrollment_date') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="enrollment_type" class="form-label">Tipo de Matrícula <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.enrollment_type') ? 'is-invalid' : '' ?>" 
                                id="enrollment_type" 
                                name="enrollment_type" 
                                <?= ($enrollment && ($enrollment->status == 'Pendente' || $enrollment->status == 'Ativo')) ? 'disabled' : 'required' ?>>
                            <option value="">Selecione...</option>
                            <option value="Nova" <?= ($selectedEnrollmentType == 'Nova') ? 'selected' : '' ?>>Nova</option>
                            <option value="Renovação" <?= ($selectedEnrollmentType == 'Renovação') ? 'selected' : '' ?>>Renovação</option>
                            <option value="Transferência" <?= ($selectedEnrollmentType == 'Transferência') ? 'selected' : '' ?>>Transferência</option>
                        </select>
                        <?php if (session('errors.enrollment_type')): ?>
                            <div class="invalid-feedback"><?= session('errors.enrollment_type') ?></div>
                        <?php endif; ?>
                        <?php if ($enrollment && ($enrollment->status == 'Pendente' || $enrollment->status == 'Ativo')): ?>
                            <input type="hidden" name="enrollment_type" value="<?= $enrollment->enrollment_type ?>">
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="enrollment_number" class="form-label">Número da Matrícula</label>
                        <input type="text" 
                               class="form-control" 
                               id="enrollment_number" 
                               name="enrollment_number" 
                               value="<?= old('enrollment_number', $enrollment->enrollment_number ?? '(gerado automaticamente)') ?>"
                               readonly>
                        <small class="text-muted">Gerado automaticamente pelo sistema</small>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="previous_grade_id" class="form-label">Classe Anterior</label>
                        <select class="form-select" id="previous_grade_id" name="previous_grade_id">
                            <option value="">Selecione...</option>
                            <?php if (!empty($gradeLevels)): ?>
                                <?php foreach ($gradeLevels as $level): ?>
                                    <option value="<?= $level->id ?>" 
                                        <?= ($selectedPreviousGrade == $level->id) ? 'selected' : '' ?>>
                                        <?= $level->level_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Classe que o aluno concluiu anteriormente</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status da Matrícula</label>
                        <select class="form-select" id="status" name="status">
                            <option value="Pendente" <?= ($selectedStatus == 'Pendente') ? 'selected' : '' ?>>Pendente</option>
                            <option value="Ativo" <?= ($selectedStatus == 'Ativo') ? 'selected' : '' ?>>Ativo</option>
                            <option value="Concluído" <?= ($selectedStatus == 'Concluído') ? 'selected' : '' ?>>Concluído</option>
                            <option value="Cancelado" <?= ($selectedStatus == 'Cancelado') ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                        <small class="text-muted">Altere o status conforme necessário</small>
                    </div>
                </div>
            </div>
            
            <!-- Campo hidden para o nível (se veio da pré-matrícula) -->
            <?php if ($enrollment && $enrollment->status == 'Pendente' && !isset($enrollment->grade_level_name)): ?>
                <input type="hidden" name="grade_level_id" value="<?= $enrollment->grade_level_id ?>">
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="observations" class="form-label">Observações</label>
                <textarea class="form-control" id="observations" name="observations" rows="3"><?= old('observations', $enrollment->observations ?? '') ?></textarea>
            </div>
            
            <?php if ($enrollment && $enrollment->status == 'Pendente'): ?>
                <div class="alert alert-success mt-3">
                    <i class="fas fa-check-circle"></i>
                    <strong>Concluir Matrícula:</strong> Após selecionar a turma e salvar, a matrícula será ativada.
                </div>
            <?php endif; ?>
            
            <?php if ($enrollment && $enrollment->status == 'Ativo'): ?>
                <div class="alert alert-success mt-3">
                    <i class="fas fa-check-circle"></i>
                    <strong>Matrícula Ativa</strong> - Esta matrícula está ativa e o aluno está regularmente matriculado.
                </div>
            <?php endif; ?>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <div>
                    <a href="<?= site_url('admin/students/enrollments') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
                <div>
                    <?php if ($enrollment && $enrollment->status == 'Pendente'): ?>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check-circle"></i> Concluir e Ativar Matrícula
                        </button>
                    <?php elseif ($enrollment && $enrollment->status == 'Ativo'): ?>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Atualizar Matrícula
                        </button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-primary">
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
    // Atualizar informações da turma quando selecionada
    document.getElementById('class_id').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            const capacity = selected.dataset.capacity;
            const level = selected.dataset.level;
            const available = selected.dataset.available;
            document.getElementById('classInfo').textContent = `Capacidade: ${capacity} alunos | Nível: ${level} | Vagas: ${available}`;
        } else {
            document.getElementById('classInfo').textContent = '';
        }
    });

    // Verificar disponibilidade de vagas em tempo real
    document.getElementById('class_id').addEventListener('change', function() {
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
                    alert('Atenção: Esta turma não possui vagas disponíveis!');
                    this.value = '';
                    document.getElementById('classInfo').textContent = '';
                    
                    // Disparar evento change para atualizar selects
                    document.getElementById('grade_level_id').dispatchEvent(new Event('change'));
                }
            })
            .catch(error => console.error('Erro:', error));
        }
    });

    // Carregar turmas baseadas no nível selecionado
    document.getElementById('grade_level_id').addEventListener('change', function() {
        const levelId = this.value;
        const yearId = document.getElementById('academic_year_id').value;
        const classSelect = document.getElementById('class_id');
        
        if (levelId && yearId) {
            fetch(`<?= site_url('admin/classes/get-by-level-and-year/') ?>${levelId}/${yearId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfHash
                }
            })
            .then(response => response.json())
            .then(classes => {
                const currentValue = classSelect.value;
                classSelect.innerHTML = '<option value="">Selecione a turma...</option>';
                
                classes.forEach(cls => {
                    const available = cls.capacity - (cls.enrolled_count || 0);
                    const disabled = available <= 0 ? 'disabled' : '';
                    const selected = cls.id == currentValue ? 'selected' : '';
                    
                    classSelect.innerHTML += `<option value="${cls.id}" 
                        data-capacity="${cls.capacity}"
                        data-level="${cls.level_name}"
                        data-available="${available}"
                        ${selected}
                        ${disabled}>
                        ${cls.class_name} (${cls.class_code}) - ${cls.class_shift} - ${cls.level_name}
                        (${available} vagas)
                    </option>`;
                });
                
                // Disparar evento change para atualizar info
                if (classSelect.value) {
                    classSelect.dispatchEvent(new Event('change'));
                } else {
                    document.getElementById('classInfo').textContent = '';
                }
            })
            .catch(error => console.error('Erro:', error));
        }
    });

    // Disparar change inicial para carregar turmas se já houver nível selecionado
    const initialLevelId = document.getElementById('grade_level_id').value;
    if (initialLevelId) {
        document.getElementById('grade_level_id').dispatchEvent(new Event('change'));
    }

    // Validação do formulário antes de enviar
    document.getElementById('enrollmentForm').addEventListener('submit', function(e) {
        const status = document.getElementById('status').value;
        const classId = document.getElementById('class_id').value;
        
        if (status == 'Ativo' && !classId) {
            e.preventDefault();
            alert('Para ativar a matrícula, é necessário selecionar uma turma.');
        }
        
        // Log para debug
        console.log('Enviando formulário com status:', status);
    });
});

// Função para confirmar cancelamento
function confirmCancel() {
    return confirm('Tem certeza que deseja cancelar esta matrícula? Esta ação não pode ser desfeita.');
}
</script>
<?= $this->endSection() ?>