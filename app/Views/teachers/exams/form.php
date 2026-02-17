<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-<?= isset($exam) && $exam ? 'edit' : 'plus-circle' ?>"></i> <?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= site_url('teachers/exams/save') ?>" method="post" id="examForm">
            <?= csrf_field() ?>
            
            <?php if (isset($exam) && $exam): ?>
                <input type="hidden" name="id" value="<?= $exam->id ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="exam_name" class="form-label">Nome do Exame <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="exam_name" name="exam_name" 
                               value="<?= old('exam_name', $exam->exam_name ?? '') ?>" 
                               placeholder="Ex: Prova Trimestral de Matemática" required>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="exam_board_id" class="form-label">Tipo de Exame <span class="text-danger">*</span></label>
                        <select class="form-select" id="exam_board_id" name="exam_board_id" required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($boards)): ?>
                                <?php foreach ($boards as $board): ?>
                                    <option value="<?= $board->id ?>" <?= old('exam_board_id', $exam->exam_board_id ?? '') == $board->id ? 'selected' : '' ?>>
                                        <?= esc($board->board_name) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Nenhum tipo de exame cadastrado</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="class_id" class="form-label">Turma <span class="text-danger">*</span></label>
                        <select class="form-select" id="class_id" name="class_id" required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($classes)): ?>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class->id ?>" <?= old('class_id', $exam->class_id ?? '') == $class->id ? 'selected' : '' ?>>
                                        <?= esc($class->class_name) ?> (<?= esc($class->class_code) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Nenhuma turma atribuída</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="discipline_id" class="form-label">Disciplina <span class="text-danger">*</span></label>
                        <select class="form-select" id="discipline_id" name="discipline_id" required>
                            <option value="">Selecione...</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="semester_id" class="form-label">Semestre <span class="text-danger">*</span></label>
                        <select class="form-select" id="semester_id" name="semester_id" required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($semesters)): ?>
                                <?php foreach ($semesters as $semester): ?>
                                    <option value="<?= $semester->id ?>" <?= old('semester_id', $exam->semester_id ?? '') == $semester->id ? 'selected' : '' ?>>
                                        <?= esc($semester->semester_name) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Nenhum semestre ativo</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="exam_date" class="form-label">Data <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="exam_date" name="exam_date" 
                               value="<?= old('exam_date', $exam->exam_date ?? '') ?>" 
                               min="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="exam_time" class="form-label">Hora</label>
                        <input type="time" class="form-control" id="exam_time" name="exam_time" 
                               value="<?= old('exam_time', $exam->exam_time ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="exam_room" class="form-label">Sala</label>
                        <input type="text" class="form-control" id="exam_room" name="exam_room" 
                               value="<?= old('exam_room', $exam->exam_room ?? '') ?>"
                               placeholder="Ex: Sala 101">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="max_score" class="form-label">Valor Máximo</label>
                        <input type="number" class="form-control" id="max_score" name="max_score" 
                               step="0.01" min="0" max="100" value="<?= old('max_score', $exam->max_score ?? 20) ?>">
                        <small class="text-muted">Valor total da prova (padrão: 20)</small>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Descrição / Instruções</label>
                <textarea class="form-control" id="description" name="description" rows="3" 
                          placeholder="Instruções para os alunos, conteúdo da prova, etc."><?= old('description', $exam->description ?? '') ?></textarea>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('teachers/exams') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> <?= isset($exam) && $exam ? 'Atualizar Exame' : 'Salvar Exame' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Disciplina selecionada na edição
const selectedDiscipline = <?= isset($exam) && $exam ? $exam->discipline_id : 'null' ?>;

// Elementos do formulário
const classSelect = document.getElementById('class_id');
const disciplineSelect = document.getElementById('discipline_id');

// Função para carregar disciplinas
function loadDisciplines(classId) {
    // Validar se classId é válido
    if (!classId || classId === '') {
        disciplineSelect.innerHTML = '<option value="">Selecione...</option>';
        disciplineSelect.disabled = false;
        return;
    }
    
    // Mostrar loading
    disciplineSelect.innerHTML = '<option value="">Carregando...</option>';
    disciplineSelect.disabled = true;
    
    // Construir URL sem barra dupla
    const baseUrl = '<?= site_url('teachers/exams/get-disciplines/') ?>';
    const url = baseUrl + classId;
    
    console.log('Carregando disciplinas da URL:', url);
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Disciplinas carregadas:', data);
        
        disciplineSelect.innerHTML = '<option value="">Selecione...</option>';
        disciplineSelect.disabled = false;
        
        if (data.length === 0) {
            disciplineSelect.innerHTML += '<option value="" disabled>Nenhuma disciplina encontrada</option>';
        } else {
            data.forEach(discipline => {
                const selected = (selectedDiscipline == discipline.id) ? 'selected' : '';
                disciplineSelect.innerHTML += `<option value="${discipline.id}" ${selected}>${discipline.discipline_name}</option>`;
            });
        }
    })
    .catch(error => {
        console.error('Erro ao carregar disciplinas:', error);
        disciplineSelect.innerHTML = '<option value="">Erro ao carregar disciplinas</option>';
        disciplineSelect.disabled = false;
    });
}

// Evento change da turma
if (classSelect) {
    classSelect.addEventListener('change', function() {
        loadDisciplines(this.value);
    });
}

// Se estiver editando, carregar disciplinas da turma selecionada
<?php if (isset($exam) && $exam): ?>
document.addEventListener('DOMContentLoaded', function() {
    if (classSelect && classSelect.value) {
        console.log('Carregando disciplinas para turma:', classSelect.value);
        loadDisciplines(classSelect.value);
    }
});
<?php endif; ?>

// Validação do formulário antes de enviar
document.getElementById('examForm').addEventListener('submit', function(e) {
    const classId = classSelect.value;
    const disciplineId = disciplineSelect.value;
    
    if (!classId) {
        e.preventDefault();
        alert('Por favor, selecione uma turma.');
        return false;
    }
    
    if (!disciplineId) {
        e.preventDefault();
        alert('Por favor, selecione uma disciplina.');
        return false;
    }
    
    return true;
});
</script>
<?= $this->endSection() ?>