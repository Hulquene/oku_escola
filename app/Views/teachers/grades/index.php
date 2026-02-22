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
            <i class="fas fa-star"></i> Avaliações Contínuas (MAC)
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
                                <?php for ($i = 1; $i <= ($acCount ?? 6); $i++): ?>
                                    <th class="text-center">AC<?= $i ?></th>
                                <?php endfor; ?>
                                <th class="text-center">Média</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): 
                                $scores = [];
                                $total = 0;
                                $count = 0;
                            ?>
                                <tr>
                                    <td><?= $student->student_number ?></td>
                                    <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                                    
                                    <?php for ($i = 1; $i <= ($acCount ?? 6); $i++): 
                                        $score = $student->assessments[$i]->score ?? '';
                                        if ($score !== '') {
                                            $scores[] = $score;
                                            $total += $score;
                                            $count++;
                                        }
                                    ?>
                                        <td class="text-center">
                                            <input type="number" class="form-control form-control-sm text-center" 
                                                   name="ac[<?= $i ?>][<?= $student->enrollment_id ?>]" 
                                                   value="<?= $score ?>" step="0.1" min="0" max="20"
                                                   style="width: 80px; margin: 0 auto;">
                                        </td>
                                    <?php endfor; ?>
                                    
                                    <?php 
                                    $average = $count > 0 ? round($total / $count, 1) : 0;
                                    $status = $average >= 10 ? 'Aprovado' : ($average > 0 ? 'Reprovado' : 'Pendente');
                                    $statusClass = $average >= 10 ? 'success' : ($average > 0 ? 'danger' : 'secondary');
                                    ?>
                                    <td class="text-center fw-bold"><?= number_format($average, 1) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $statusClass ?>"><?= $status ?></span>
                                    </td>
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
                    <div class="btn-group flex-wrap" role="group">
                        <?php for ($i = 1; $i <= ($acCount ?? 6); $i++): ?>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillAllAC(<?= $i ?>)">
                                <i class="fas fa-magic"></i> Preencher AC<?= $i ?>
                            </button>
                        <?php endfor; ?>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="fillAllWithValue(14)">
                            <i class="fas fa-magic"></i> Todas com 14
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearAll()">
                            <i class="fas fa-eraser"></i> Limpar Todos
                        </button>
                    </div>
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
            disciplineSelect.innerHTML = '<option value="">Carregando...</option>';
            disciplineSelect.disabled = true;
            
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
                        disciplineSelect.innerHTML = '<option value="" disabled>Nenhuma disciplina disponível</option>';
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    disciplineSelect.innerHTML = '<option value="" disabled>Erro ao carregar disciplinas</option>';
                    disciplineSelect.disabled = false;
                    alert('Erro ao carregar disciplinas. Tente novamente.');
                });
        } else {
            disciplineSelect.innerHTML = '<option value="">Selecione a turma primeiro...</option>';
            disciplineSelect.disabled = true;
        }
    });
    
    <?php if ($selectedClass): ?>
    if (classSelect) {
        classSelect.dispatchEvent(new Event('change'));
    }
    <?php endif; ?>
});

function fillAllAC(acNumber) {
    document.querySelectorAll(`input[name^="ac[${acNumber}]"]`).forEach(input => {
        input.value = '14';
    });
}

function fillAllWithValue(value) {
    if (confirm(`Preencher todas as notas com ${value}?`)) {
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.value = value;
        });
    }
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