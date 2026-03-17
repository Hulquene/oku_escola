<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-exclamation-triangle me-2" style="color: var(--warning);"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Recursos</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <button class="hdr-btn warning" data-bs-toggle="modal" data-bs-target="#generateModal">
                <i class="fas fa-calendar-plus me-1"></i> Gerar Exames de Recurso
            </button>
            <a href="<?= site_url('admin/exams/appeals/export/' . $selectedSemester) ?>?type=excel" 
               class="hdr-btn success">
                <i class="fas fa-file-excel me-1"></i> Exportar
            </a>
        </div>
    </div>
    <div class="mt-2">
        <span class="badge-ci warning">
            <i class="fas fa-exclamation-triangle me-1"></i>
            Alunos com notas entre 7 e 9.9 valores
        </span>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros com estilo do sistema -->
<div class="ci-card mb-4">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-filter"></i>
            <span>Filtrar Recursos</span>
        </div>
    </div>
    <div class="ci-card-body">
        <form method="get" class="filter-grid">
            <div>
                <label class="filter-label">Semestre</label>
                <select class="filter-select" name="semester" onchange="this.form.submit()">
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= $sem['id'] ?>" <?= $selectedSemester == $sem['id'] ? 'selected' : '' ?>>
                            <?= $sem['semester_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="filter-label">Turma</label>
                <select class="filter-select" name="class_id" onchange="this.form.submit()">
                    <option value="">Todas as turmas</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['id'] ?>" <?= $selectedClass == $class['id'] ? 'selected' : '' ?>>
                            <?= $class['class_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-actions" style="grid-column: -1 / 1;">
                <a href="<?= site_url('admin/exams/appeals') ?>" class="btn-filter clear w-100">
                    <i class="fas fa-undo me-1"></i> Limpar Filtros
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Estatísticas com estilo do sistema -->
<div class="stat-grid mb-4">
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <div class="stat-label">Alunos em Recurso</div>
            <div class="stat-value"><?= $stats['total_students'] ?></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-book-open"></i>
        </div>
        <div>
            <div class="stat-label">Disciplinas em Recurso</div>
            <div class="stat-value"><?= $stats['total_disciplines'] ?></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon navy">
            <i class="fas fa-school"></i>
        </div>
        <div>
            <div class="stat-label">Turmas Envolvidas</div>
            <div class="stat-value"><?= $stats['total_classes'] ?></div>
        </div>
    </div>
</div>

<!-- Lista de Alunos por Turma -->
<?php if (!empty($groupedAppeals)): ?>
    <?php foreach ($groupedAppeals as $group): ?>
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-users"></i>
                    <span>Turma: <?= $group['class_name'] ?></span>
                </div>
                <span class="badge-ci warning ms-3">
                    <?= count($group['students']) ?> disciplina(s)
                </span>
            </div>
            <div class="ci-card-body p0">
                <div class="table-responsive">
                    <table class="ci-table">
                        <thead>
                            <tr>
                                <th class="ps-3">Nº Aluno</th>
                                <th>Nome do Aluno</th>
                                <th>Disciplina</th>
                                <th class="center">Nota Atual</th>
                                <th class="center">Status</th>
                                <th class="center pe-3">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($group['students'] as $appeal): ?>
                                <tr>
                                    <td class="ps-3">
                                        <span class="code-badge"><?= $appeal->student_number ?></span>
                                    </td>
                                    <td class="fw-semibold"><?= $appeal->first_name ?> <?= $appeal->last_name ?></td>
                                    <td>
                                        <?= $appeal->discipline_name ?>
                                        <br>
                                        <small class="text-muted"><?= $appeal->discipline_code ?></small>
                                    </td>
                                    <td class="center">
                                        <span class="num-chip warning">
                                            <?= number_format($appeal->final_score, 1) ?>
                                        </span>
                                    </td>
                                    <td class="center">
                                        <span class="badge-ci warning p-2">Recurso</span>
                                    </td>
                                    <td class="center pe-3">
                                        <div class="action-group">
                                            <a href="<?= site_url('admin/students/view/' . $appeal->student_id) ?>" 
                                               class="row-btn view" title="Ver aluno">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-check-circle" style="color: var(--success); font-size: 3rem;"></i>
        <h5>Nenhum aluno em situação de recurso</h5>
        <p class="text-muted">
            Não existem alunos com notas entre 7 e 9.9 valores neste semestre.
        </p>
    </div>
<?php endif; ?>

<!-- Modal de Geração de Exames com estilo do sistema -->
<div class="modal fade ci-modal" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header warning-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus me-2"></i>
                    Gerar Exames de Recurso
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('admin/exams/appeals/generate') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="filter-label">Semestre <span class="req">*</span></label>
                        <select class="filter-select" name="semester_id" required>
                            <option value="">Selecione</option>
                            <?php foreach ($semesters as $sem): ?>
                                <option value="<?= $sem['id'] ?>" <?= $selectedSemester == $sem['id'] ? 'selected' : '' ?>>
                                    <?= $sem['semester_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="filter-label">Turmas (opcional)</label>
                        <div class="ci-card" style="max-height: 200px; overflow-y: auto; padding: 0;">
                            <div class="ci-card-body p-2">
                                <div class="toggle-row mb-2" id="selectAllRow" style="cursor: pointer;">
                                    <div class="tl-info">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Selecionar Todas
                                    </div>
                                    <div class="ci-switch" style="width: 42px;">
                                        <input type="checkbox" id="selectAllClasses">
                                        <label class="ci-switch-track" for="selectAllClasses"></label>
                                    </div>
                                </div>
                                <?php foreach ($classes as $class): ?>
                                    <div class="toggle-row ms-3 mb-1" style="cursor: pointer; padding: 0.3rem 0.5rem;">
                                        <div class="tl-info small">
                                            <?= $class['class_name'] ?>
                                        </div>
                                        <div class="ci-switch" style="width: 42px;">
                                            <input class="class-checkbox" 
                                                   type="checkbox" 
                                                   name="class_ids[]" 
                                                   value="<?= $class['id'] ?>" 
                                                   id="class_<?= $class['id'] ?>">
                                            <label class="ci-switch-track" for="class_<?= $class['id'] ?>"></label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="form-hint">Deixe em branco para gerar para todas as turmas</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="filter-label">Data do Exame <span class="req">*</span></label>
                        <input type="date" class="form-input-ci" name="exam_date" 
                               min="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-filter clear" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn-filter warning">
                        <i class="fas fa-calendar-plus me-2"></i>Gerar Exames
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Select all classes
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllClasses');
    const classCheckboxes = document.querySelectorAll('.class-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function(e) {
            classCheckboxes.forEach(cb => {
                cb.checked = e.target.checked;
                // Atualizar visual do switch
                const track = cb.nextElementSibling;
                if (track && track.classList.contains('ci-switch-track')) {
                    if (cb.checked) {
                        track.classList.add('checked');
                    } else {
                        track.classList.remove('checked');
                    }
                }
            });
        });
        
        // Adicionar evento para a linha de seleção
        document.getElementById('selectAllRow').addEventListener('click', function(e) {
            if (!e.target.classList.contains('ci-switch-track') && !e.target.classList.contains('ci-switch')) {
                selectAllCheckbox.checked = !selectAllCheckbox.checked;
                selectAllCheckbox.dispatchEvent(new Event('change'));
            }
        });
    }
    
    // Adicionar evento para cada linha de classe
    document.querySelectorAll('.toggle-row').forEach(row => {
        if (row.id !== 'selectAllRow') {
            const checkbox = row.querySelector('.class-checkbox');
            if (checkbox) {
                row.addEventListener('click', function(e) {
                    if (!e.target.classList.contains('ci-switch-track') && !e.target.classList.contains('ci-switch')) {
                        checkbox.checked = !checkbox.checked;
                        checkbox.dispatchEvent(new Event('change'));
                        
                        // Atualizar visual do switch
                        const track = checkbox.nextElementSibling;
                        if (track && track.classList.contains('ci-switch-track')) {
                            if (checkbox.checked) {
                                track.classList.add('checked');
                            } else {
                                track.classList.remove('checked');
                            }
                        }
                    }
                });
            }
        }
    });
    
    // Inicializar switches
    document.querySelectorAll('.ci-switch-track').forEach(track => {
        const input = track.previousElementSibling;
        if (input && input.type === 'checkbox' && input.checked) {
            track.classList.add('checked');
        }
    });
});

// Auto-submit filters on change
document.querySelectorAll('.filter-select').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});
</script>

<style>
/* Estilos adicionais específicos para esta página */
.num-chip.warning {
    background: rgba(232,160,32,0.15);
    color: var(--warning);
    font-family: var(--font-mono);
    font-size: 1rem;
    font-weight: 700;
    padding: 0.2rem 0.6rem;
    border-radius: 5px;
    display: inline-block;
}

.code-badge {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 600;
    background: rgba(59,127,232,0.1);
    color: var(--accent);
    padding: 0.25rem 0.5rem;
    border-radius: 5px;
    display: inline-block;
}

/* Cabeçalho do modal */
.modal-header.warning-header {
    background: var(--warning);
    color: #fff;
}

/* Ajustes para o toggle-row */
.toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.5rem 1rem;
    transition: border-color 0.18s;
}

.toggle-row:hover {
    border-color: var(--warning);
}

.toggle-row .tl-info {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--text-primary);
}

.toggle-row .tl-info.small {
    font-size: 0.75rem;
}

/* Responsividade */
@media (max-width: 768px) {
    .filter-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-grid {
        grid-template-columns: 1fr;
    }
    
    .ci-table td {
        min-width: 120px;
    }
    
    .hdr-actions {
        flex-wrap: wrap;
    }
    
    .hdr-btn {
        width: 100%;
    }
}
</style>
<?= $this->endSection() ?>