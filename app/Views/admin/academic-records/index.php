<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema (padrão) -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-clipboard-list me-2" style="color: var(--accent);"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pautas Acadêmicas</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/academic-records/export') ?>" class="hdr-btn secondary">
                <i class="fas fa-download me-2"></i>Exportar
            </a>
            <button class="hdr-btn secondary" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
        </div>
    </div>
    <div class="mt-2">
        <span class="badge-ci info">
            <i class="fas fa-info-circle me-1"></i>
            Gerencie e finalize as pautas das turmas
        </span>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Métricas com estilo do sistema -->
<div class="stat-grid mb-4">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-school"></i>
        </div>
        <div>
            <div class="stat-label">Total de Turmas</div>
            <div class="stat-value"><?= $totalClasses ?? 0 ?></div>
            <div class="stat-sub">com pautas ativas</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="stat-label">Pautas Finalizadas</div>
            <div class="stat-value"><?= $finalizedClasses ?? 0 ?></div>
            <div class="stat-sub text-success"><?= $totalClasses > 0 ? round(($finalizedClasses/$totalClasses)*100) : 0 ?>% do total</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <div class="stat-label">Total de Alunos</div>
            <div class="stat-value"><?= $totalStudents ?? 0 ?></div>
            <div class="stat-sub">matriculados ativos</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <div class="stat-label">Pautas Pendentes</div>
            <div class="stat-value"><?= $pendingClasses ?? 0 ?></div>
            <div class="stat-sub text-warning">aguardando finalização</div>
        </div>
    </div>
</div>

<!-- Painel de Filtros Avançado com estilo do sistema -->
<div class="ci-card mb-4">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-sliders-h"></i>
            <span>Filtros Avançados</span>
        </div>
        <span class="badge-ci primary" id="filterCount">0 filtros ativos</span>
    </div>
    
    <div class="ci-card-body">
        <form method="get" id="filterForm" class="filter-grid">
            <!-- Linha 1 de Filtros -->
            <div>
                <label class="filter-label">Ano Letivo</label>
                <select class="filter-select" id="academic_year" name="academic_year">
                    <option value="">Todos os anos</option>
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <?php 
                            $isSelected = ($selectedYear == $year['id']) || 
                                         (empty($selectedYear) && $year['id'] == current_academic_year());
                            ?>
                            <option value="<?= $year['id'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                                <?= $year['year_name'] ?> <?= $year['id'] == current_academic_year() ? '📅' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div>
                <label class="filter-label">Curso</label>
                <select class="filter-select" id="course" name="course">
                    <option value="">Todos os cursos</option>
                    <option value="0" <?= ($selectedCourse === '0' || $selectedCourse === 0) ? 'selected' : '' ?>>
                        🏫 Ensino Geral
                    </option>
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course['id'] ?>" <?= $selectedCourse == $course['id'] ? 'selected' : '' ?>>
                                <?= $course['course_name'] ?> (<?= $course['course_code'] ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div>
                <label class="filter-label">Nível</label>
                <select class="filter-select" id="level" name="level">
                    <option value="">Todos</option>
                    <?php if (!empty($gradeLevels)): ?>
                        <?php foreach ($gradeLevels as $level): ?>
                            <option value="<?= $level['id'] ?>" <?= $selectedLevel == $level['id'] ? 'selected' : '' ?>>
                                <?= $level['level_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div>
                <label class="filter-label">Turno</label>
                <select class="filter-select" id="shift" name="shift">
                    <option value="">Todos</option>
                    <option value="Manhã" <?= $selectedShift == 'Manhã' ? 'selected' : '' ?>>🌅 Manhã</option>
                    <option value="Tarde" <?= $selectedShift == 'Tarde' ? 'selected' : '' ?>>☀️ Tarde</option>
                    <option value="Noite" <?= $selectedShift == 'Noite' ? 'selected' : '' ?>>🌙 Noite</option>
                    <option value="Integral" <?= $selectedShift == 'Integral' ? 'selected' : '' ?>>📚 Integral</option>
                </select>
            </div>
            
            <div>
                <label class="filter-label">Status</label>
                <select class="filter-select" id="status" name="status">
                    <option value="">Todas</option>
                    <option value="finalized" <?= $selectedStatus == 'finalized' ? 'selected' : '' ?>>✅ Finalizadas</option>
                    <option value="pending" <?= $selectedStatus == 'pending' ? 'selected' : '' ?>>⏳ Pendentes</option>
                </select>
            </div>
            
            <!-- Barra de Ações -->
            <div class="filter-actions" style="grid-column: -1 / 1;">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <span class="badge-ci info">
                            <i class="fas fa-info-circle me-1"></i>
                            <?= $totalFiltered ?? count($classes) ?> turmas encontradas
                        </span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn-filter clear" onclick="resetFilters()">
                            <i class="fas fa-undo me-1"></i>Limpar Filtros
                        </button>
                        <button type="submit" class="btn-filter apply">
                            <i class="fas fa-search me-1"></i>Aplicar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </form>
        
        <!-- Filtros Ativos em Tags -->
        <div class="mt-3 pt-3" style="border-top: 1px solid var(--border);" id="activeFilters">
            <?php 
            $activeFilters = 0;
            ob_start(); 
            ?>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted small me-2"><i class="fas fa-filter"></i> Filtros:</span>
                
                <?php if (!empty($selectedYear)): ?>
                    <?php foreach ($academicYears as $year): ?>
                        <?php if ($year['id'] == $selectedYear): ?>
                            <?php $activeFilters++; ?>
                            <span class="badge-ci primary p-2">
                                <i class="fas fa-calendar me-1"></i>Ano: <?= $year['year_name'] ?>
                                <a href="<?= site_url('admin/academic-records?remove=year') ?>" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if (!empty($selectedCourse) || $selectedCourse === '0'): ?>
                    <?php $activeFilters++; ?>
                    <span class="badge-ci primary p-2">
                        <i class="fas fa-graduation-cap me-1"></i>
                        Curso: <?= $selectedCourse === '0' ? 'Ensino Geral' : '' ?>
                        <?php if ($selectedCourse !== '0'): ?>
                            <?php foreach ($courses as $course): ?>
                                <?php if ($course['id'] == $selectedCourse): ?>
                                    <?= $course['course_name'] ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <a href="<?= site_url('admin/academic-records?remove=course') ?>" class="text-white ms-1">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                <?php endif; ?>
                
                <?php if (!empty($selectedLevel)): ?>
                    <?php $activeFilters++; ?>
                    <span class="badge-ci primary p-2">
                        <i class="fas fa-layer-group me-1"></i>
                        Nível: 
                        <?php foreach ($gradeLevels as $level): ?>
                            <?php if ($level['id'] == $selectedLevel): ?>
                                <?= $level['level_name'] ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <a href="<?= site_url('admin/academic-records?remove=level') ?>" class="text-white ms-1">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                <?php endif; ?>
                
                <?php if (!empty($selectedShift)): ?>
                    <?php $activeFilters++; ?>
                    <span class="badge-ci primary p-2">
                        <i class="fas fa-clock me-1"></i>Turno: <?= $selectedShift ?>
                        <a href="<?= site_url('admin/academic-records?remove=shift') ?>" class="text-white ms-1">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                <?php endif; ?>
                
                <?php if (!empty($selectedStatus)): ?>
                    <?php $activeFilters++; ?>
                    <span class="badge-ci primary p-2">
                        <i class="fas fa-<?= $selectedStatus == 'finalized' ? 'check-circle' : 'clock' ?> me-1"></i>
                        Status: <?= $selectedStatus == 'finalized' ? 'Finalizadas' : 'Pendentes' ?>
                        <a href="<?= site_url('admin/academic-records?remove=status') ?>" class="text-white ms-1">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                <?php endif; ?>
                
                <?php if ($activeFilters == 0): ?>
                    <span class="text-muted small">Nenhum filtro aplicado</span>
                <?php endif; ?>
            </div>
            <?php 
            $filterHtml = ob_get_clean();
            echo $filterHtml;
            ?>
        </div>
    </div>
</div>

<!-- Lista de Turmas em Grid com estilo do sistema -->
<div class="row g-4">
    <?php if (!empty($classes)): ?>
        <?php foreach ($classes as $class): ?>
            <div class="col-lg-4 col-md-6">
                <div class="ci-card class-card h-100">
                    <!-- Header do Card com Status -->
                    <div class="class-card-header <?= isset($class['is_finalized']) && $class['is_finalized'] ? 'finalized' : 'pending' ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="class-title mb-1"><?= $class['class_name'] ?></h5>
                                <span class="class-code"><?= $class['class_code'] ?></span>
                            </div>
                            <span class="badge-ci <?= isset($class['is_finalized']) && $class['is_finalized'] ? 'success' : 'warning' ?>">
                                <?php if (isset($class['is_finalized']) && $class['is_finalized']): ?>
                                    <i class="fas fa-check-circle me-1"></i>Finalizada
                                <?php else: ?>
                                    <i class="fas fa-clock me-1"></i>Pendente
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Corpo do Card -->
                    <div class="ci-card-body">
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">Nível:</span>
                                <span class="info-value"><?= $class['level_name'] ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Curso:</span>
                                <span class="info-value">
                                    <?php if (isset($class['course_name']) && $class['course_name']): ?>
                                        <?= $class['course_name'] ?>
                                    <?php else: ?>
                                        <span class="text-muted">Ensino Geral</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Turno:</span>
                                <span class="info-value"><?= $class['class_shift'] ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Ano Letivo:</span>
                                <span class="info-value"><?= $class['year_name'] ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Professor:</span>
                                <span class="info-value">
                                    <?php if ($class['class_teacher_id']): ?>
                                        <?= $class['teacher_first_name'] ?? '' ?> <?= $class['teacher_last_name'] ?? '' ?>
                                    <?php else: ?>
                                        <span class="text-muted">Não atribuído</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Estatísticas da Turma -->
                        <div class="class-stats mt-3">
                            <div class="stat-item">
                                <div class="stat-value"><?= $class['total_alunos'] ?? 0 ?></div>
                                <div class="stat-label">Alunos</div>
                            </div>
                            <?php if (!isset($class['is_finalized']) || !$class['is_finalized']): ?>
                                <div class="stat-item">
                                    <div class="stat-value"><?= $class['progress_percentage'] ?? 0 ?>%</div>
                                    <div class="stat-label">Progresso</div>
                                </div>
                            <?php else: ?>
                                <div class="stat-item">
                                    <div class="stat-value text-success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="stat-label">Concluído</div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Barra de Progresso (se pendente) -->
                        <?php if (!isset($class['is_finalized']) || !$class['is_finalized']): ?>
                            <div class="progress mt-3" style="height: 4px; background: var(--surface);">
                                <div class="progress-bar" 
                                     role="progressbar" 
                                     style="width: <?= $class['progress_percentage'] ?? 0 ?>%; background: var(--accent); border-radius: 2px;">
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Footer com Ações -->
                    <div class="ci-card-footer" style="background: var(--surface);">
                        <div class="d-flex gap-2">
                            <a href="<?= site_url('admin/academic-records/class/' . $class['id']) ?>" 
                               class="btn-ci outline flex-grow-1" style="justify-content: center;">
                                <i class="fas fa-eye me-1"></i> Ver Pauta
                            </a>
                            
                            <?php if (!isset($class['is_finalized']) || !$class['is_finalized']): ?>
                                <button type="button" 
                                        class="row-btn warning" 
                                        onclick="confirmFinalize(<?= $class['id'] ?>, '<?= $class['class_name'] ?>')"
                                        title="Finalizar pauta">
                                    <i class="fas fa-check-double"></i>
                                </button>
                            <?php else: ?>
                                <button type="button" 
                                        class="row-btn secondary" 
                                        disabled
                                        title="Pauta já finalizada">
                                    <i class="fas fa-lock"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- Estado Vazio com estilo do sistema -->
        <div class="col-12">
            <div class="empty-state">
                <i class="fas fa-school"></i>
                <h5>Nenhuma turma encontrada</h5>
                <p class="text-muted">
                    Não encontramos turmas com os filtros selecionados.<br>
                    Tente ajustar os critérios de busca ou limpar os filtros.
                </p>
                <a href="<?= site_url('admin/academic-records') ?>" class="btn-ci primary">
                    <i class="fas fa-undo me-2"></i>Limpar Filtros
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Paginação -->
<?php if (!empty($classes) && isset($pager)): ?>
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="badge-ci info">
            Mostrando <span class="fw-semibold"><?= count($classes) ?></span> de 
            <span class="fw-semibold"><?= $totalFiltered ?? $pager->getTotal() ?></span> turmas
        </div>
        <div>
            <?= $pager->links() ?>
        </div>
    </div>
<?php endif; ?>

<!-- Modal de Finalização com estilo do sistema -->
<div class="modal fade ci-modal" id="finalizeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header warning-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Finalização
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <p class="mb-3">
                    Deseja finalizar a pauta da turma 
                    <span class="fw-semibold" id="finalizeClassName"></span>?
                </p>
                
                <div class="alert-ci warning small">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Atenção:</strong> Após a finalização, as notas não poderão ser alteradas e os históricos serão gerados automaticamente.
                </div>
                
                <div class="ci-card mt-3">
                    <div class="ci-card-body p-2">
                        <h6 class="fw-semibold mb-2">Resumo da operação:</h6>
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-1">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Resultados consolidados
                            </li>
                            <li class="mb-1">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Matrículas atualizadas
                            </li>
                            <li class="mb-1">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Históricos gerados
                            </li>
                            <li>
                                <i class="fas fa-lock text-warning me-2"></i>
                                Notas bloqueadas para edição
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-filter clear" data-bs-dismiss="modal">Cancelar</button>
                <form action="<?= site_url('admin/academic-records/finalize-year') ?>" method="post" id="finalizeForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="class_id" id="finalizeClassId" value="">
                    <input type="hidden" name="semester_id" value="<?= $selectedSemester ?? '' ?>">
                    <button type="submit" class="btn-filter warning px-4">
                        <i class="fas fa-check-double me-2"></i>Confirmar Finalização
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Contador de filtros ativos
function updateFilterCount() {
    const count = <?= $activeFilters ?? 0 ?>;
    document.getElementById('filterCount').textContent = count + ' filtro' + (count !== 1 ? 's' : '') + ' ativo' + (count !== 1 ? 's' : '');
}

// Resetar filtros
function resetFilters() {
    window.location.href = '<?= site_url('admin/academic-records') ?>';
}

// Confirmar finalização
function confirmFinalize(id, name) {
    document.getElementById('finalizeClassName').textContent = name;
    document.getElementById('finalizeClassId').value = id;
    new bootstrap.Modal(document.getElementById('finalizeModal')).show();
}

// Auto-submit dos filtros (opcional)
let filterTimeout;
$('.filter-select').on('change', function() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        $('#filterForm').submit();
    }, 800);
});

// Inicializar contador
document.addEventListener('DOMContentLoaded', function() {
    updateFilterCount();
    
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<style>
/* Estilos adicionais específicos para esta página */
.breadcrumb {
    background-color: transparent;
    padding: 0;
    margin-bottom: 0.5rem;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: var(--text-muted);
}

/* Cards de turma */
.class-card {
    border: none;
    overflow: hidden;
}

.class-card-header {
    padding: 1rem 1rem 0.75rem 1rem;
    border-bottom: 1px solid var(--border);
}

.class-card-header.finalized {
    background: linear-gradient(135deg, rgba(22,168,125,0.05) 0%, rgba(22,168,125,0.1) 100%);
}

.class-card-header.pending {
    background: linear-gradient(135deg, rgba(232,160,32,0.05) 0%, rgba(232,160,32,0.1) 100%);
}

.class-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
    color: var(--text-primary);
}

.class-code {
    font-size: 0.8rem;
    color: var(--text-muted);
}

/* Informações da turma */
.info-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.85rem;
}

.info-label {
    color: var(--text-muted);
    font-weight: 400;
}

.info-value {
    color: var(--text-primary);
    font-weight: 500;
}

/* Estatísticas */
.class-stats {
    display: flex;
    justify-content: space-around;
    padding: 0.75rem 0;
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.2;
}

.stat-label {
    font-size: 0.7rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Progress bar */
.progress {
    background: var(--surface);
    border-radius: 2px;
    overflow: hidden;
}

.progress-bar {
    background: var(--accent);
    border-radius: 2px;
    transition: width 0.3s ease;
}

/* Animações */
.col-lg-4 {
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
    
    .class-stats {
        gap: 0.5rem;
    }
    
    .stat-value {
        font-size: 1rem;
    }
    
    .hdr-actions {
        flex-wrap: wrap;
    }
}
</style>
<?= $this->endSection() ?>