<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb e Header Simplificado -->
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active fw-semibold">Pautas Acadêmicas</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-semibold mb-1"><?= $title ?></h2>
                <p class="text-muted mb-0">Gerencie e finalize as pautas das turmas</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= site_url('admin/academic-records/export') ?>" class="btn btn-light">
                    <i class="fas fa-download me-2"></i>Exportar
                </a>
                <button class="btn btn-light" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Métricas -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                            <i class="fas fa-school text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total de Turmas</h6>
                        <h3 class="mb-0 fw-semibold"><?= $totalClasses ?? 0 ?></h3>
                        <small class="text-muted">com pautas ativas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 p-2 rounded">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Pautas Finalizadas</h6>
                        <h3 class="mb-0 fw-semibold"><?= $finalizedClasses ?? 0 ?></h3>
                        <small class="text-success"><?= $totalClasses > 0 ? round(($finalizedClasses/$totalClasses)*100) : 0 ?>% do total</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 p-2 rounded">
                            <i class="fas fa-users text-info"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total de Alunos</h6>
                        <h3 class="mb-0 fw-semibold"><?= $totalStudents ?? 0 ?></h3>
                        <small class="text-muted">matriculados ativos</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Pautas Pendentes</h6>
                        <h3 class="mb-0 fw-semibold"><?= $pendingClasses ?? 0 ?></h3>
                        <small class="text-warning">aguardando finalização</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Painel de Filtros Avançado -->
<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <div class="d-flex align-items-center">
            <i class="fas fa-sliders-h text-primary me-2"></i>
            <h5 class="mb-0 fw-semibold">Filtros Avançados</h5>
            <span class="badge bg-light text-dark ms-3" id="filterCount">0 filtros ativos</span>
        </div>
    </div>
    
    <div class="card-body">
        <form method="get" id="filterForm" class="row g-3">
            <!-- Linha 1 de Filtros -->
            <div class="col-md-3">
                <label class="form-label fw-medium text-muted small text-uppercase">Ano Letivo</label>
                <select class="form-select form-select-sm filter-select" id="academic_year" name="academic_year">
                    <option value="">Todos os anos</option>
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <?php 
                            $isSelected = ($selectedYear == $year->id) || 
                                         (empty($selectedYear) && $year->is_current == 1);
                            ?>
                            <option value="<?= $year->id ?>" <?= $isSelected ? 'selected' : '' ?> 
                                    data-current="<?= $year->is_current ?>">
                                <?= $year->year_name ?> <?= $year->is_current ? '📅' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-medium text-muted small text-uppercase">Curso</label>
                <select class="form-select form-select-sm filter-select" id="course" name="course">
                    <option value="">Todos os cursos</option>
                    <option value="0" <?= ($selectedCourse === '0' || $selectedCourse === 0) ? 'selected' : '' ?>>
                        🏫 Ensino Geral
                    </option>
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course->id ?>" <?= $selectedCourse == $course->id ? 'selected' : '' ?>>
                                <?= $course->course_name ?> (<?= $course->course_code ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-medium text-muted small text-uppercase">Nível</label>
                <select class="form-select form-select-sm filter-select" id="level" name="level">
                    <option value="">Todos</option>
                    <?php if (!empty($gradeLevels)): ?>
                        <?php foreach ($gradeLevels as $level): ?>
                            <option value="<?= $level->id ?>" <?= $selectedLevel == $level->id ? 'selected' : '' ?>>
                                <?= $level->level_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-medium text-muted small text-uppercase">Turno</label>
                <select class="form-select form-select-sm filter-select" id="shift" name="shift">
                    <option value="">Todos</option>
                    <option value="Manhã" <?= $selectedShift == 'Manhã' ? 'selected' : '' ?>>🌅 Manhã</option>
                    <option value="Tarde" <?= $selectedShift == 'Tarde' ? 'selected' : '' ?>>☀️ Tarde</option>
                    <option value="Noite" <?= $selectedShift == 'Noite' ? 'selected' : '' ?>>🌙 Noite</option>
                    <option value="Integral" <?= $selectedShift == 'Integral' ? 'selected' : '' ?>>📚 Integral</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-medium text-muted small text-uppercase">Status</label>
                <select class="form-select form-select-sm filter-select" id="status" name="status">
                    <option value="">Todas</option>
                    <option value="finalized" <?= $selectedStatus == 'finalized' ? 'selected' : '' ?>>✅ Finalizadas</option>
                    <option value="pending" <?= $selectedStatus == 'pending' ? 'selected' : '' ?>>⏳ Pendentes</option>
                </select>
            </div>
            
            <!-- Barra de Ações -->
            <div class="col-12 mt-3">
                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            <?= $totalFiltered ?? count($classes) ?> turmas encontradas
                        </span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light btn-sm" onclick="resetFilters()">
                            <i class="fas fa-undo me-1"></i>Limpar Filtros
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            <i class="fas fa-search me-1"></i>Aplicar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </form>
        
        <!-- Filtros Ativos em Tags -->
        <div class="mt-3" id="activeFilters">
            <?php 
            $activeFilters = 0;
            ob_start(); 
            ?>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted small me-2"><i class="fas fa-filter"></i> Filtros:</span>
                
                <?php if (!empty($selectedYear)): ?>
                    <?php foreach ($academicYears as $year): ?>
                        <?php if ($year->id == $selectedYear): ?>
                            <?php $activeFilters++; ?>
                            <span class="filter-tag">
                                <i class="fas fa-calendar me-1"></i>Ano: <?= $year->year_name ?>
                                <a href="<?= site_url('admin/academic-records?remove=year') ?>" class="remove-filter">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if (!empty($selectedCourse) || $selectedCourse === '0'): ?>
                    <?php $activeFilters++; ?>
                    <span class="filter-tag">
                        <i class="fas fa-graduation-cap me-1"></i>
                        Curso: <?= $selectedCourse === '0' ? 'Ensino Geral' : '' ?>
                        <?php if ($selectedCourse !== '0'): ?>
                            <?php foreach ($courses as $course): ?>
                                <?php if ($course->id == $selectedCourse): ?>
                                    <?= $course->course_name ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <a href="<?= site_url('admin/academic-records?remove=course') ?>" class="remove-filter">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                <?php endif; ?>
                
                <?php if (!empty($selectedLevel)): ?>
                    <?php $activeFilters++; ?>
                    <span class="filter-tag">
                        <i class="fas fa-layer-group me-1"></i>
                        Nível: 
                        <?php foreach ($gradeLevels as $level): ?>
                            <?php if ($level->id == $selectedLevel): ?>
                                <?= $level->level_name ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <a href="<?= site_url('admin/academic-records?remove=level') ?>" class="remove-filter">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                <?php endif; ?>
                
                <?php if (!empty($selectedShift)): ?>
                    <?php $activeFilters++; ?>
                    <span class="filter-tag">
                        <i class="fas fa-clock me-1"></i>Turno: <?= $selectedShift ?>
                        <a href="<?= site_url('admin/academic-records?remove=shift') ?>" class="remove-filter">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                <?php endif; ?>
                
                <?php if (!empty($selectedStatus)): ?>
                    <?php $activeFilters++; ?>
                    <span class="filter-tag">
                        <i class="fas fa-<?= $selectedStatus == 'finalized' ? 'check-circle' : 'clock' ?> me-1"></i>
                        Status: <?= $selectedStatus == 'finalized' ? 'Finalizadas' : 'Pendentes' ?>
                        <a href="<?= site_url('admin/academic-records?remove=status') ?>" class="remove-filter">
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

<!-- Lista de Turmas em Grid -->
<div class="row g-4">
    <?php if (!empty($classes)): ?>
        <?php foreach ($classes as $class): ?>
            <div class="col-lg-4 col-md-6">
                <div class="class-card card h-100">
                    <!-- Header do Card com Status -->
                    <div class="class-card-header <?= isset($class->is_finalized) && $class->is_finalized ? 'finalized' : 'pending' ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="class-title mb-1"><?= $class->class_name ?></h5>
                                <span class="class-code"><?= $class->class_code ?></span>
                            </div>
                            <span class="status-badge <?= isset($class->is_finalized) && $class->is_finalized ? 'finalized' : 'pending' ?>">
                                <?php if (isset($class->is_finalized) && $class->is_finalized): ?>
                                    <i class="fas fa-check-circle me-1"></i>Finalizada
                                <?php else: ?>
                                    <i class="fas fa-clock me-1"></i>Pendente
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Corpo do Card -->
                    <div class="card-body">
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">Nível:</span>
                                <span class="info-value"><?= $class->level_name ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Curso:</span>
                                <span class="info-value">
                                    <?php if (isset($class->course_name) && $class->course_name): ?>
                                        <?= $class->course_name ?>
                                    <?php else: ?>
                                        <span class="text-muted">Ensino Geral</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Turno:</span>
                                <span class="info-value"><?= $class->class_shift ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Ano Letivo:</span>
                                <span class="info-value"><?= $class->year_name ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Professor:</span>
                                <span class="info-value">
                                    <?php if ($class->class_teacher_id): ?>
                                        <?= $class->teacher_first_name ?? '' ?> <?= $class->teacher_last_name ?? '' ?>
                                    <?php else: ?>
                                        <span class="text-muted">Não atribuído</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Estatísticas da Turma -->
                        <div class="class-stats mt-3">
                            <div class="stat-item">
                                <div class="stat-value"><?= $class->total_alunos ?? 0 ?></div>
                                <div class="stat-label">Alunos</div>
                            </div>
                            <?php if (!isset($class->is_finalized) || !$class->is_finalized): ?>
                                <div class="stat-item">
                                    <div class="stat-value"><?= $class->progress_percentage ?? 0 ?>%</div>
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
                        <?php if (!isset($class->is_finalized) || !$class->is_finalized): ?>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-info" 
                                     role="progressbar" 
                                     style="width: <?= $class->progress_percentage ?? 0 ?>%">
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Footer com Ações -->
                    <div class="card-footer bg-white">
                        <div class="d-flex gap-2">
                            <a href="<?= site_url('admin/academic-records/class/' . $class->id) ?>" 
                               class="btn btn-outline-primary btn-sm flex-grow-1">
                                <i class="fas fa-eye me-1"></i> Ver Pauta
                            </a>
                            
                            <?php if (!isset($class->is_finalized) || !$class->is_finalized): ?>
                                <button type="button" 
                                        class="btn btn-outline-warning btn-sm" 
                                        onclick="confirmFinalize(<?= $class->id ?>, '<?= $class->class_name ?>')"
                                        title="Finalizar pauta">
                                    <i class="fas fa-check-double"></i>
                                </button>
                            <?php else: ?>
                                <button type="button" 
                                        class="btn btn-outline-secondary btn-sm" 
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
        <!-- Estado Vazio -->
        <div class="col-12">
            <div class="empty-state">
                <i class="fas fa-school empty-state-icon"></i>
                <h5 class="empty-state-title">Nenhuma turma encontrada</h5>
                <p class="empty-state-text">
                    Não encontramos turmas com os filtros selecionados.<br>
                    Tente ajustar os critérios de busca ou limpar os filtros.
                </p>
                <a href="<?= site_url('admin/academic-records') ?>" class="btn btn-primary">
                    <i class="fas fa-undo me-2"></i>Limpar Filtros
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Paginação -->
<?php if (!empty($classes) && isset($pager)): ?>
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted small">
            Mostrando <span class="fw-semibold"><?= count($classes) ?></span> de 
            <span class="fw-semibold"><?= $totalFiltered ?? $pager->getTotal() ?></span> turmas
        </div>
        <div>
            <?= $pager->links() ?>
        </div>
    </div>
<?php endif; ?>

<!-- Modal de Finalização -->
<div class="modal fade" id="finalizeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">
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
                
                <div class="alert alert-warning bg-warning bg-opacity-10 border-0 small">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Atenção:</strong> Após a finalização, as notas não poderão ser alteradas e os históricos serão gerados automaticamente.
                </div>
                
                <div class="bg-light p-3 rounded">
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
            
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <form action="<?= site_url('admin/academic-records/finalize-year') ?>" method="post" id="finalizeForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="class_id" id="finalizeClassId" value="">
                    <input type="hidden" name="semester_id" value="<?= $selectedSemester ?? '' ?>">
                    <button type="submit" class="btn btn-warning px-4">
                        <i class="fas fa-check-double me-2"></i>Confirmar Finalização
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos corporativos */
.breadcrumb {
    background-color: transparent;
    padding: 0;
    margin-bottom: 0.5rem;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #adb5bd;
}

/* Cards de métricas */
.bg-opacity-10 {
    --bg-opacity: 0.1;
}

.rounded {
    border-radius: 8px !important;
}

/* Filtros */
.filter-select {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    transition: all 0.2s;
}

.filter-select:focus {
    background-color: #fff;
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.2rem rgba(13,110,253,.1);
}

.filter-tag {
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.75rem;
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 20px;
    font-size: 0.875rem;
    color: #495057;
}

.remove-filter {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    margin-left: 0.5rem;
    background-color: #dee2e6;
    border-radius: 50%;
    color: #495057;
    text-decoration: none;
    transition: all 0.2s;
}

.remove-filter:hover {
    background-color: #adb5bd;
    color: #fff;
}

/* Cards de turma */
.class-card {
    border: 1px solid #e9ecef;
    transition: all 0.2s;
    overflow: hidden;
}

.class-card:hover {
    box-shadow: 0 8px 16px rgba(0,0,0,0.05);
    transform: translateY(-2px);
}

.class-card-header {
    padding: 1rem 1rem 0.75rem 1rem;
    border-bottom: 1px solid #e9ecef;
}

.class-card-header.finalized {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.class-card-header.pending {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.class-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
    color: #212529;
}

.class-code {
    font-size: 0.8rem;
    color: #6c757d;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.finalized {
    background-color: #d1e7dd;
    color: #0f5132;
}

.status-badge.pending {
    background-color: #fff3cd;
    color: #856404;
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
    font-size: 0.9rem;
}

.info-label {
    color: #6c757d;
    font-weight: 400;
}

.info-value {
    color: #212529;
    font-weight: 500;
}

/* Estatísticas */
.class-stats {
    display: flex;
    justify-content: space-around;
    padding: 0.75rem 0;
    border-top: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 600;
    color: #212529;
    line-height: 1.2;
}

.stat-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Estado vazio */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: #fff;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.empty-state-icon {
    font-size: 3rem;
    color: #dee2e6;
    margin-bottom: 1rem;
}

.empty-state-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.empty-state-text {
    color: #6c757d;
    margin-bottom: 1.5rem;
}

/* Paginação */
.pagination {
    margin: 0;
    gap: 0.25rem;
}

.page-link {
    border: 1px solid #e9ecef;
    border-radius: 6px !important;
    color: #495057;
    padding: 0.375rem 0.75rem;
    transition: all 0.2s;
}

.page-link:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #212529;
}

.page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}

/* Animações */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.col-lg-4 {
    animation: fadeIn 0.3s ease-out;
}

/* Responsividade */
@media (max-width: 768px) {
    .filter-tag {
        font-size: 0.8rem;
    }
    
    .class-stats {
        gap: 0.5rem;
    }
    
    .stat-value {
        font-size: 1rem;
    }
}
</style>

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
});
</script>
<?= $this->endSection() ?>