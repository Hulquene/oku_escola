<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-book-open me-2"></i>Pautas por Disciplina</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/mini-grade-sheet') ?>">Mini Pautas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pautas por Disciplina</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/mini-grade-sheet') ?>" class="hdr-btn info">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros -->
<div class="ci-card mb-4">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-filter"></i>
            <span>Selecionar Turma e Disciplina</span>
        </div>
    </div>
    <div class="ci-card-body">
        <form action="<?= site_url('admin/mini-grade-sheet/disciplina') ?>" method="get" id="filterForm">
            <div class="filter-grid">
                <div>
                    <label class="filter-label">Ano Letivo</label>
                    <select name="academic_year" class="filter-select" id="academicYear">
                        <option value="">Todos</option>
                        <?php foreach ($academicYears as $year): ?>
                            <?php if (is_array($year)): ?>
                            <option value="<?= $year['id'] ?? '' ?>" <?= ($selectedYear ?? '') == ($year['id'] ?? '') ? 'selected' : '' ?>>
                                <?= esc($year['year_name'] ?? '') ?>
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="filter-label">Turma <span class="req">*</span></label>
                    <select name="class" class="filter-select" id="class" required>
                        <option value="">Selecione uma turma</option>
                        <?php foreach ($classes as $class): ?>
                            <?php if (is_array($class)): ?>
                            <option value="<?= $class['id'] ?? '' ?>" <?= ($selectedClass ?? '') == ($class['id'] ?? '') ? 'selected' : '' ?>>
                                <?= esc($class['class_name'] ?? '') ?> (<?= esc($class['year_name'] ?? '') ?>)
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="filter-label">Disciplina <span class="req">*</span></label>
                    <select name="discipline" class="filter-select" id="discipline" required>
                        <option value="">Selecione uma disciplina</option>
                    </select>
                </div>
            </div>
            
            <div class="filter-actions mt-3">
                <button type="submit" class="btn-filter apply" id="btnVisualizar">
                    <i class="fas fa-search me-1"></i> Visualizar Pauta
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (!$selectedClass): ?>
    <div class="alert-ci info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Instrução:</strong> Selecione uma turma e disciplina para visualizar a pauta completa.
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
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
        
        // Estilo visual para botão desabilitado
        if (btnVisualizar.disabled) {
            btnVisualizar.classList.add('opacity-50');
        } else {
            btnVisualizar.classList.remove('opacity-50');
        }
    }
    
    // Quando a turma mudar
    classSelect.addEventListener('change', function() {
        const classId = this.value;
        
        if (classId) {
            disciplineSelect.innerHTML = '<option value="">Carregando...</option>';
            disciplineSelect.disabled = true;
            btnVisualizar.disabled = true;
            btnVisualizar.classList.add('opacity-50');
            
            fetch('<?= site_url('admin/classes/class-subjects/get-by-class/') ?>' + classId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na resposta do servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    disciplineSelect.innerHTML = '<option value="">Selecione uma disciplina</option>';
                    disciplineSelect.disabled = false;
                    
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
                    disciplineSelect.disabled = false;
                    console.error('Erro:', error);
                    btnVisualizar.disabled = true;
                    btnVisualizar.classList.add('opacity-50');
                    
                    // Mostrar mensagem de erro com SweetAlert se disponível
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Não foi possível carregar as disciplinas. Tente novamente.'
                        });
                    }
                });
        } else {
            disciplineSelect.innerHTML = '<option value="">Selecione uma disciplina</option>';
            disciplineSelect.disabled = true;
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
    setTimeout(function() {
        const event = new Event('change');
        classSelect.dispatchEvent(event);
        
        // Selecionar a disciplina se já estiver definida
        <?php if ($selectedDiscipline): ?>
        setTimeout(function() {
            disciplineSelect.value = '<?= $selectedDiscipline ?>';
            checkButtonStatus();
        }, 500);
        <?php endif; ?>
    }, 100);
    <?php endif; ?>
});
</script>

<style>
/* Estilos adicionais específicos para esta página */
.req {
    color: var(--danger);
    font-weight: normal;
    margin-left: 2px;
}

.filter-select[disabled] {
    background-color: var(--surface);
    opacity: 0.7;
    cursor: not-allowed;
}

.btn-filter.apply:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

.btn-filter.apply:disabled:hover {
    transform: none;
    box-shadow: none;
}

/* Animação para o card */
.ci-card {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsividade */
@media (max-width: 768px) {
    .filter-grid {
        grid-template-columns: 1fr;
    }
}
</style>
<?= $this->endSection() ?>