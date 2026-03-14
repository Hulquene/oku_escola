<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Pautas por Disciplina</h1>
        <a href="<?= site_url('admin/mini-grade-sheet') ?>" class="btn btn-info">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/mini-grade-sheet') ?>">Mini Pautas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pautas por Disciplina</li>
        </ol>
    </nav>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Selecionar Turma e Disciplina</h5>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/mini-grade-sheet/disciplina') ?>" method="get">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ano Letivo</label>
                    <select name="academic_year" class="form-select" id="academicYear">
                        <option value="">Todos</option>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= $year['id'] ?>" <?= ($selectedYear ?? '') == $year['id'] ? 'selected' : '' ?>>
                                <?= $year['year_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Turma</label>
                    <select name="class" class="form-select" id="class" required>
                        <option value="">Selecione uma turma</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class->id ?>" <?= ($selectedClass ?? '') == $class->id ? 'selected' : '' ?>>
                                <?= $class->class_name ?> (<?= $class->year_name ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Disciplina</label>
                    <select name="discipline" class="form-select" id="discipline" required>
                        <option value="">Selecione uma disciplina</option>
                    </select>
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary" id="btnVisualizar">
                    <i class="fas fa-search"></i> Visualizar Pauta
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (!$selectedClass): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Selecione uma turma e disciplina para visualizar a pauta completa.
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class');
    const disciplineSelect = document.getElementById('discipline');
    const btnVisualizar = document.getElementById('btnVisualizar');
    const academicYearSelect = document.getElementById('academicYear');
    
    // Função para verificar se pode habilitar o botão
    function checkButtonStatus() {
        const hasClass = classSelect.value !== '';
        const hasDiscipline = disciplineSelect.value !== '';
        btnVisualizar.disabled = !(hasClass && hasDiscipline);
    }
    
    // Quando a turma mudar
    classSelect.addEventListener('change', function() {
        const classId = this.value;
        
        if (classId) {
            disciplineSelect.innerHTML = '<option value="">Carregando...</option>';
            btnVisualizar.disabled = true;
            
            fetch('<?= site_url('admin/classes/class-subjects/get-by-class/') ?>' + classId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na resposta do servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    disciplineSelect.innerHTML = '<option value="">Selecione uma disciplina</option>';
                    
                    if (data.length === 0) {
                        disciplineSelect.innerHTML += '<option value="" disabled>Nenhuma disciplina encontrada</option>';
                    } else {
                        data.forEach(disc => {
                            disciplineSelect.innerHTML += `<option value="${disc.id}">${disc.discipline_name}</option>`;
                        });
                    }
                    
                    checkButtonStatus();
                })
                .catch(error => {
                    disciplineSelect.innerHTML = '<option value="">Erro ao carregar disciplinas</option>';
                    console.error('Erro:', error);
                    btnVisualizar.disabled = true;
                });
        } else {
            disciplineSelect.innerHTML = '<option value="">Selecione uma disciplina</option>';
            checkButtonStatus();
        }
    });
    
    // Quando a disciplina mudar
    disciplineSelect.addEventListener('change', checkButtonStatus);
    
    // Verificar estado inicial
    checkButtonStatus();
    
    // Se já tiver turma selecionada (quando a página carrega com parâmetros)
    <?php if ($selectedClass): ?>
    // Disparar o evento change para carregar as disciplinas
    const event = new Event('change');
    classSelect.dispatchEvent(event);
    <?php endif; ?>
});
</script>

<?= $this->endSection() ?>