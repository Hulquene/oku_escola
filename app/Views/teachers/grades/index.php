<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Lançar Notas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-5">
                <label for="class" class="form-label">Turma <span class="text-danger">*</span></label>
                <select class="form-select" id="class" name="class" required>
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
            
            <div class="col-md-5">
                <label for="discipline" class="form-label">Disciplina <span class="text-danger">*</span></label>
                <select class="form-select" id="discipline" name="discipline" required>
                    <option value="">Selecione...</option>
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-search"></i> Carregar
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($students) && !empty($students) && isset($currentSemester)): ?>
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle"></i>
        <strong>Semestre Atual:</strong> <?= $currentSemester->semester_name ?? 'Não definido' ?>
        <?php if (!$currentSemester): ?>
            <br><small class="text-warning">Nenhum semestre ativo definido. Contacte a administração.</small>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <div class="card-header bg-success text-white">
            <i class="fas fa-star"></i> Avaliações Contínuas
        </div>
        <div class="card-body">
            <form action="<?= site_url('teachers/grades/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="class_id" value="<?= $selectedClass ?>">
                <input type="hidden" name="discipline_id" value="<?= $selectedDiscipline ?>">
                <input type="hidden" name="semester_id" value="<?= $currentSemester->id ?? '' ?>">
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nº Matrícula</th>
                                <th>Aluno</th>
                                <th class="text-center">AC1</th>
                                <th class="text-center">AC2</th>
                                <th class="text-center">AC3</th>
                                <th class="text-center">Média</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): 
                                $ac1 = $student->assessments['AC1']->score ?? '';
                                $ac2 = $student->assessments['AC2']->score ?? '';
                                $ac3 = $student->assessments['AC3']->score ?? '';
                                
                                $scores = array_filter([$ac1, $ac2, $ac3]);
                                $average = !empty($scores) ? round(array_sum($scores) / count($scores), 1) : 0;
                            ?>
                                <tr>
                                    <td><?= $student->student_number ?></td>
                                    <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                                    <td class="text-center">
                                        <input type="number" class="form-control form-control-sm text-center" 
                                               name="ac1[<?= $student->enrollment_id ?>]" 
                                               value="<?= $ac1 ?>" step="0.1" min="0" max="20"
                                               style="width: 80px; margin: 0 auto;">
                                    </td>
                                    <td class="text-center">
                                        <input type="number" class="form-control form-control-sm text-center" 
                                               name="ac2[<?= $student->enrollment_id ?>]" 
                                               value="<?= $ac2 ?>" step="0.1" min="0" max="20"
                                               style="width: 80px; margin: 0 auto;">
                                    </td>
                                    <td class="text-center">
                                        <input type="number" class="form-control form-control-sm text-center" 
                                               name="ac3[<?= $student->enrollment_id ?>]" 
                                               value="<?= $ac3 ?>" step="0.1" min="0" max="20"
                                               style="width: 80px; margin: 0 auto;">
                                    </td>
                                    <td class="text-center fw-bold"><?= number_format($average, 1) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between">
                    <a href="<?= site_url('teachers/grades') ?>" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Limpar
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Salvar Avaliações
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-bolt"></i> Ações Rápidas
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="fillAllAC1()">
                        <i class="fas fa-magic"></i> Preencher AC1 com 14
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="fillAllAC2()">
                        <i class="fas fa-magic"></i> Preencher AC2 com 14
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="fillAllAC3()">
                        <i class="fas fa-magic"></i> Preencher AC3 com 14
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAll()">
                        <i class="fas fa-eraser"></i> Limpar Todos
                    </button>
                </div>
            </div>
        </div>
    </div>
    
<?php elseif ($selectedClass && $selectedDiscipline): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> Nenhum aluno encontrado para esta turma/disciplina.
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class');
    const disciplineSelect = document.getElementById('discipline');
    const selectedDiscipline = '<?= $selectedDiscipline ?? '' ?>';
    
    classSelect.addEventListener('change', function() {
        const classId = this.value;
        
        if (classId) {
            // Mostrar loading
            disciplineSelect.innerHTML = '<option value="">Carregando...</option>';
            disciplineSelect.disabled = true;
            
            // CORRIGIDO: Usar a URL correta
            fetch(`<?= site_url('teachers/grades/get-disciplines/') ?>${classId}`)
                .then(response => response.json())
                .then(data => {
                    disciplineSelect.innerHTML = '<option value="">Selecione a disciplina...</option>';
                    disciplineSelect.disabled = false;
                    
                    if (data && data.length > 0) {
                        data.forEach(discipline => {
                            const option = document.createElement('option');
                            option.value = discipline.id;
                            option.textContent = `${discipline.discipline_name} (${discipline.discipline_code})`;
                            
                            if (discipline.id == selectedDiscipline) {
                                option.selected = true;
                            }
                            
                            disciplineSelect.appendChild(option);
                        });
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Nenhuma disciplina disponível';
                        option.disabled = true;
                        disciplineSelect.appendChild(option);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    disciplineSelect.innerHTML = '<option value="">Erro ao carregar disciplinas</option>';
                    disciplineSelect.disabled = false;
                    alert('Erro ao carregar disciplinas. Tente novamente.');
                });
        } else {
            disciplineSelect.innerHTML = '<option value="">Selecione a turma primeiro...</option>';
            disciplineSelect.disabled = true;
        }
    });
    
    // Trigger change if class is selected
    <?php if ($selectedClass): ?>
    if (classSelect) {
        classSelect.dispatchEvent(new Event('change'));
    }
    <?php endif; ?>
});

// Helper functions
function fillAllAC1() {
    document.querySelectorAll('input[name^="ac1"]').forEach(input => {
        input.value = '14';
    });
}

function fillAllAC2() {
    document.querySelectorAll('input[name^="ac2"]').forEach(input => {
        input.value = '14';
    });
}

function fillAllAC3() {
    document.querySelectorAll('input[name^="ac3"]').forEach(input => {
        input.value = '14';
    });
}

function clearAll() {
    if (confirm('Limpar todas as notas?')) {
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.value = '';
        });
    }
}
</script>
<?= $this->endSection() ?>