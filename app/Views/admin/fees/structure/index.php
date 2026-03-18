<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-table me-2" style="color: var(--accent);"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/fees') ?>">Propinas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Estrutura de Propinas</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <button class="hdr-btn success" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
            <button type="button" class="hdr-btn secondary" data-bs-toggle="modal" data-bs-target="#bulkModal">
                <i class="fas fa-layer-group"></i> Criação em Massa
            </button>
            <button type="button" class="hdr-btn primary" data-bs-toggle="modal" data-bs-target="#structureModal">
                <i class="fas fa-plus-circle"></i> Nova Estrutura
            </button>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Estatísticas -->
<div class="stat-grid mb-4">
    <?php
    $totalStructures = count($structures ?? []);
    $activeStructures = array_filter($structures ?? [], fn($s) => ($s->is_active ?? 0) == 1);
    $mandatoryStructures = array_filter($structures ?? [], fn($s) => ($s->is_mandatory ?? 0) == 1);
    $totalAmount = array_sum(array_column($structures ?? [], 'amount'));
    ?>
    
    <div class="stat-card">
        <div class="stat-icon navy">
            <i class="fas fa-table"></i>
        </div>
        <div>
            <div class="stat-label">Total</div>
            <div class="stat-value"><?= $totalStructures ?></div>
            <div class="stat-sub">Estruturas cadastradas</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="stat-label">Ativas</div>
            <div class="stat-value"><?= count($activeStructures) ?></div>
            <div class="stat-sub"><?= $totalStructures > 0 ? round((count($activeStructures)/$totalStructures)*100) : 0 ?>% do total</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div>
            <div class="stat-label">Obrigatórias</div>
            <div class="stat-value"><?= count($mandatoryStructures) ?></div>
            <div class="stat-sub">Propinas obrigatórias</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div>
            <div class="stat-label">Valor Total</div>
            <div class="stat-value"><?= number_format($totalAmount, 0, ',', '.') ?> Kz</div>
            <div class="stat-sub">Soma das propinas</div>
        </div>
    </div>
</div>

<!-- Filter Card com estilo do sistema -->
<div class="ci-card mb-4">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-filter"></i>
            <span>Filtros</span>
        </div>
        <span class="badge-ci primary" id="filterCount">0 filtros ativos</span>
    </div>
    
    <div class="ci-card-body">
        <form method="get" id="filterForm" class="filter-grid">
            <!-- Ano Letivo -->
            <div>
                <label class="filter-label">Ano Letivo</label>
                <select class="filter-select" id="academic_year" name="academic_year">
                    <option value="">Todos</option>
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= $year['id'] ?>" <?= $currentYearId == $year['id'] ? 'selected' : '' ?>>
                                <?= $year['year_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- Nível -->
            <div>
                <label class="filter-label">Nível de Ensino</label>
                <select class="filter-select" id="grade_level" name="grade_level">
                    <option value="">Todos</option>
                    <?php if (!empty($gradeLevels)): ?>
                        <?php foreach ($gradeLevels as $level): ?>
                            <option value="<?= $level['id'] ?>"><?= $level['level_name'] ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- Categoria -->
            <div>
                <label class="filter-label">Categoria</label>
                <select class="filter-select" id="category" name="category">
                    <option value="">Todas</option>
                    <option value="Propina">Propina</option>
                    <option value="Matrícula">Matrícula</option>
                    <option value="Inscrição">Inscrição</option>
                    <option value="Taxa de Exame">Taxa de Exame</option>
                </select>
            </div>
            
            <!-- Status -->
            <div>
                <label class="filter-label">Status</label>
                <select class="filter-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="1">Ativos</option>
                    <option value="0">Inativos</option>
                </select>
            </div>
            
            <!-- Barra de Ações -->
            <div class="filter-actions" style="grid-column: -1 / 1;">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <span class="badge-ci info">
                            <i class="fas fa-info-circle me-1"></i>
                            <?= $totalStructures ?> estruturas encontradas
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
    </div>
</div>

<!-- Data Table -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-table"></i>
            <span>Estrutura de Propinas</span>
        </div>
        <span class="badge-ci primary" id="recordCount">
            <i class="fas fa-database me-1"></i> <?= count($structures) ?> registros
        </span>
    </div>
    
    <div class="ci-card-body p0">
        <?php if (!empty($structures)): ?>
            <div class="table-responsive">
                <table id="structuresTable" class="ci-table">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>Nível</th>
                            <th>Tipo de Taxa</th>
                            <th>Categoria</th>
                            <th class="text-end">Valor</th>
                            <th class="text-center">Moeda</th>
                            <th class="text-center">Dia Venc.</th>
                            <th class="text-center">Obrig.</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($structures as $item): ?>
                            <tr>
                                <td><span class="id-chip">#<?= str_pad($item->id, 4, '0', STR_PAD_LEFT) ?></span></td>
                                <td><?= esc($item->level_name) ?></td>
                                <td><?= esc($item->type_name) ?></td>
                                <td>
                                    <?php
                                    $categoryClass = match($item->type_category) {
                                        'Propina' => 'primary',
                                        'Matrícula' => 'success',
                                        'Inscrição' => 'info',
                                        'Taxa de Exame' => 'warning',
                                        'Taxa Administrativa' => 'secondary',
                                        'Multa' => 'danger',
                                        default => 'dark'
                                    };
                                    ?>
                                    <span class="badge-ci <?= $categoryClass ?>">
                                        <?= esc($item->type_category) ?>
                                    </span>
                                </td>
                                <td class="text-end fw-semibold">
                                    <?= number_format($item->amount, 2, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <span class="code-badge"><?= esc($item->currency_code) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-ci info"><?= $item->due_day ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($item->is_mandatory): ?>
                                        <span class="status-badge active">
                                            <i class="fas fa-check-circle me-1"></i>Sim
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge inactive">
                                            <i class="fas fa-times-circle me-1"></i>Não
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($item->is_active): ?>
                                        <span class="status-badge active">
                                            <i class="fas fa-circle me-1" style="font-size: 6px;"></i>Ativo
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge inactive">
                                            <i class="fas fa-circle me-1" style="font-size: 6px;"></i>Inativo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="action-group">
                                        <button type="button" class="row-btn edit edit-structure" 
                                                data-id="<?= $item->id ?>"
                                                data-academic="<?= $item->academic_year_id ?>"
                                                data-grade="<?= $item->grade_level_id ?>"
                                                data-fee-type="<?= $item->fee_type_id ?>"
                                                data-amount="<?= $item->amount ?>"
                                                data-currency="<?= $item->currency_id ?>"
                                                data-due-day="<?= $item->due_day ?>"
                                                data-description="<?= esc($item->description) ?>"
                                                data-mandatory="<?= $item->is_mandatory ?>"
                                                data-active="<?= $item->is_active ?>"
                                                data-bs-toggle="tooltip" 
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($item->is_active): ?>
                                            <button type="button" class="row-btn toggle-status" 
                                                    data-id="<?= $item->id ?>"
                                                    data-name="<?= esc($item->type_name . ' - ' . $item->level_name) ?>"
                                                    data-status="1"
                                                    data-bs-toggle="tooltip" 
                                                    title="Desativar">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="row-btn toggle-status" 
                                                    data-id="<?= $item->id ?>"
                                                    data-name="<?= esc($item->type_name . ' - ' . $item->level_name) ?>"
                                                    data-status="0"
                                                    data-bs-toggle="tooltip" 
                                                    title="Ativar">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        <?php endif; ?>
                                        <a href="<?= site_url('admin/financial/fee-types/delete/' . $item->id) ?>" 
                                           class="row-btn del" 
                                           onclick="return confirm('Tem certeza que deseja eliminar esta estrutura?')"
                                           data-bs-toggle="tooltip" 
                                           title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state py-5">
                <i class="fas fa-table fa-4x mb-3" style="opacity: 0.2;"></i>
                <h5 class="fw-semibold mb-2">Nenhuma estrutura encontrada</h5>
                <p class="text-muted mb-4">Nenhuma estrutura de propina cadastrada para o ano letivo selecionado.</p>
                <button type="button" class="btn-ci primary" data-bs-toggle="modal" data-bs-target="#structureModal">
                    <i class="fas fa-plus-circle me-2"></i>Criar Primeira Estrutura
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para Adicionar/Editar Estrutura (estilo do sistema) -->
<div class="modal fade modal-custom" id="structureModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="<?= site_url('admin/financial/fee-structures/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="structureId">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="structureModalTitle">
                        <i class="fas fa-plus-circle me-2"></i>
                        <span>Nova Estrutura de Propina</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Ano Letivo -->
                    <div class="mb-3">
                        <label for="academic_year_id" class="form-label-ci">
                            <i class="fas fa-calendar-alt"></i> Ano Letivo <span class="req">*</span>
                        </label>
                        <select class="form-select-ci" id="academic_year_id" name="academic_year_id" required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($academicYears)): ?>
                                <?php foreach ($academicYears as $year): ?>
                                    <option value="<?= $year['id'] ?>" <?= $currentYearId == $year['id'] ? 'selected' : '' ?>>
                                        <?= $year['year_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <!-- Nível de Ensino -->
                    <div class="mb-3">
                        <label for="grade_level_id" class="form-label-ci">
                            <i class="fas fa-layer-group"></i> Nível de Ensino <span class="req">*</span>
                        </label>
                        <select class="form-select-ci" id="grade_level_id" name="grade_level_id" required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($gradeLevels)): ?>
                                <?php foreach ($gradeLevels as $level): ?>
                                    <option value="<?= $level['id'] ?>">
                                        <?= $level['level_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <!-- Tipo de Taxa -->
                    <div class="mb-3">
                        <label for="fee_type_id" class="form-label-ci">
                            <i class="fas fa-tag"></i> Tipo de Taxa <span class="req">*</span>
                        </label>
                        <select class="form-select-ci" id="fee_type_id" name="fee_type_id" required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($feeTypes)): ?>
                                <?php foreach ($feeTypes as $type): ?>
                                    <option value="<?= $type['id'] ?>">
                                        <?= $type['type_name'] ?> (<?= $type['type_category'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <!-- Valor e Moeda -->
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="amount" class="form-label-ci">
                                    <i class="fas fa-coins"></i> Valor <span class="req">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                                    <input type="number" class="form-input-ci" id="amount" name="amount" 
                                           step="0.01" min="0" required>
                                    <span class="input-group-text">Kz</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="currency_id" class="form-label-ci">
                                    <i class="fas fa-coins"></i> Moeda
                                </label>
                                <select class="form-select-ci" id="currency_id" name="currency_id">
                                    <?php if (!empty($currencies)): ?>
                                        <?php foreach ($currencies as $currency): ?>
                                            <option value="<?= $currency['id'] ?>" <?= $currency['is_default'] ? 'selected' : '' ?>>
                                                <?= $currency['currency_code'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dia Vencimento -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="due_day" class="form-label-ci">
                                    <i class="fas fa-calendar-day"></i> Dia de Vencimento
                                </label>
                                <input type="number" class="form-input-ci" id="due_day" name="due_day" 
                                       min="1" max="31" value="10">
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i>
                                    Dia do mês para vencimento
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label-ci">&nbsp;</label>
                                <div class="toggle-row" id="mandatoryToggle">
                                    <div>
                                        <div class="tl-info">Obrigatório</div>
                                        <div class="tl-sub">Propina obrigatória para todos</div>
                                    </div>
                                    <div class="ci-switch">
                                        <input type="checkbox" id="is_mandatory" name="is_mandatory" value="1" checked>
                                        <label class="ci-switch-track" for="is_mandatory"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Descrição -->
                    <div class="mb-3">
                        <label for="description" class="form-label-ci">
                            <i class="fas fa-align-left"></i> Descrição
                        </label>
                        <textarea class="form-input-ci" id="description" name="description" rows="2" 
                                  placeholder="Descrição da estrutura..."></textarea>
                    </div>
                    
                    <!-- Status -->
                    <div class="toggle-row" id="statusToggle">
                        <div>
                            <div class="tl-info">Ativo</div>
                            <div class="tl-sub">Disponível para uso no sistema</div>
                        </div>
                        <div class="ci-switch">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="ci-switch-track" for="is_active"></label>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-filter clear" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn-filter apply">
                        <i class="fas fa-save me-2"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Criação em Massa (estilo do sistema) -->
<div class="modal fade modal-custom" id="bulkModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="<?= site_url('admin/fees/structure/bulk-create') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="modal-header" style="background: var(--success);">
                    <h5 class="modal-title">
                        <i class="fas fa-layer-group me-2"></i>
                        Criação em Massa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Ano Letivo -->
                    <div class="mb-3">
                        <label for="bulk_academic_year" class="form-label-ci">
                            <i class="fas fa-calendar-alt"></i> Ano Letivo <span class="req">*</span>
                        </label>
                        <select class="form-select-ci" id="bulk_academic_year" name="academic_year_id" required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($academicYears)): ?>
                                <?php foreach ($academicYears as $year): ?>
                                    <option value="<?= $year['id'] ?>" <?= $currentYearId == $year['id'] ? 'selected' : '' ?>>
                                        <?= $year['year_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <!-- Níveis de Ensino -->
                    <div class="mb-3">
                        <label class="form-label-ci">
                            <i class="fas fa-layer-group"></i> Níveis de Ensino <span class="req">*</span>
                        </label>
                        <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                            <?php if (!empty($gradeLevels)): ?>
                                <?php foreach ($gradeLevels as $level): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" 
                                               name="grade_level_ids[]" 
                                               value="<?= $level['id'] ?>"
                                               id="level_<?= $level['id'] ?>">
                                        <label class="form-check-label" for="level_<?= $level['id'] ?>">
                                            <?= $level['level_name'] ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="form-hint mt-2">
                            <i class="fas fa-info-circle"></i>
                            Selecione um ou mais níveis
                        </div>
                    </div>
                    
                    <!-- Tipos de Taxa -->
                    <div class="mb-3">
                        <label class="form-label-ci">
                            <i class="fas fa-tags"></i> Tipos de Taxa <span class="req">*</span>
                        </label>
                        <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                            <?php if (!empty($feeTypes)): ?>
                                <?php foreach ($feeTypes as $type): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" 
                                               name="fee_type_ids[]" 
                                               value="<?= $type['id'] ?>"
                                               id="type_<?= $type['id'] ?>">
                                        <label class="form-check-label" for="type_<?= $type['id'] ?>">
                                            <?= $type['type_name'] ?> (<?= $type['type_category'] ?>)
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Valor Padrão -->
                    <div class="mb-3">
                        <label for="bulk_amount" class="form-label-ci">
                            <i class="fas fa-coins"></i> Valor Padrão <span class="req">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                            <input type="number" class="form-input-ci" id="bulk_amount" name="amount" 
                                   step="0.01" min="0" required>
                            <span class="input-group-text">Kz</span>
                        </div>
                    </div>
                    
                    <!-- Opções adicionais -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="bulk_due_day" class="form-label-ci">
                                    <i class="fas fa-calendar-day"></i> Dia Vencimento
                                </label>
                                <input type="number" class="form-input-ci" id="bulk_due_day" name="due_day" 
                                       min="1" max="31" value="10">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label-ci">&nbsp;</label>
                                <div class="toggle-row">
                                    <div>
                                        <div class="tl-info">Obrigatório</div>
                                    </div>
                                    <div class="ci-switch">
                                        <input type="checkbox" id="bulk_mandatory" name="is_mandatory" value="1" checked>
                                        <label class="ci-switch-track" for="bulk_mandatory"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert-ci info">
                        <i class="fas fa-lightbulb me-2"></i>
                        Serão criadas <strong id="previewCount">0</strong> estruturas (Níveis × Tipos)
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-filter clear" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn-filter apply" style="background: var(--success);">
                        <i class="fas fa-layer-group me-2"></i>Criar Estruturas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Status -->
<div class="modal fade modal-custom" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--warning);">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt me-2"></i>
                    Alterar Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body text-center">
                <p>Deseja <span id="statusAction"></span> a estrutura <br>
                <strong id="statusItemName"></strong>?</p>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-filter clear" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmStatusBtn" class="btn-filter" style="background: var(--warning); color:#fff;">
                    <i class="fas fa-check me-1"></i>Confirmar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#structuresTable').DataTable({
        order: [[1, 'asc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        language: {
            url: '<?= base_url('assets/js/vendor/datatables.pt-br.json') ?>'
        },
        columnDefs: [
            { targets: [9], orderable: false }, // Ações não ordenáveis
            { targets: [5,6,7,8], className: 'text-center' }
        ],
        drawCallback: function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
    
    // Atualizar contador de registros
    $('#recordCount').html('<i class="fas fa-database me-1"></i> ' + table.page.info().recordsDisplay + ' registros');
    
    // Filtros
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        
        var year = $('#academic_year').val();
        var level = $('#grade_level').val();
        var category = $('#category').val();
        var status = $('#status').val();
        
        table.columns().search('');
        
        if (year) table.column(0).search(year).draw();
        if (level) table.column(1).search(level).draw();
        if (category) table.column(3).search(category).draw();
        if (status !== '') table.column(8).search(status === '1' ? 'Ativo' : 'Inativo').draw();
        
        updateFilterCount();
    });
    
    function updateFilterCount() {
        var count = 0;
        if ($('#academic_year').val()) count++;
        if ($('#grade_level').val()) count++;
        if ($('#category').val()) count++;
        if ($('#status').val()) count++;
        $('#filterCount').text(count + ' filtro' + (count !== 1 ? 's' : '') + ' ativo' + (count !== 1 ? 's' : ''));
    }
    
    // Toggle switch styling
    $('.ci-switch input').change(function() {
        $(this).siblings('.ci-switch-track').toggleClass('checked', this.checked);
    });
    
    // Edit structure
    $('.edit-structure').click(function() {
        $('#structureModalTitle span').text('Editar Estrutura de Propina');
        $('#structureId').val($(this).data('id'));
        $('#academic_year_id').val($(this).data('academic'));
        $('#grade_level_id').val($(this).data('grade'));
        $('#fee_type_id').val($(this).data('fee-type'));
        $('#amount').val($(this).data('amount'));
        $('#currency_id').val($(this).data('currency'));
        $('#due_day').val($(this).data('due-day'));
        $('#description').val($(this).data('description'));
        
        const isMandatory = $(this).data('mandatory') == 1;
        $('#is_mandatory').prop('checked', isMandatory);
        $('#mandatoryToggle .ci-switch-track').toggleClass('checked', isMandatory);
        
        const isActive = $(this).data('active') == 1;
        $('#is_active').prop('checked', isActive);
        $('#statusToggle .ci-switch-track').toggleClass('checked', isActive);
        
        $('#structureModal').modal('show');
    });
    
    // New structure button
    $('[data-bs-target="#structureModal"]:not(.edit-structure)').click(function() {
        $('#structureModalTitle span').text('Nova Estrutura de Propina');
        $('#structureId').val('');
        $('#structureModal form')[0].reset();
        $('#due_day').val('10');
        $('#is_mandatory').prop('checked', true);
        $('#is_active').prop('checked', true);
        $('#mandatoryToggle .ci-switch-track').addClass('checked');
        $('#statusToggle .ci-switch-track').addClass('checked');
    });
    
    // Reset modal on close
    $('#structureModal').on('hidden.bs.modal', function() {
        if (!$('#structureId').val()) {
            $(this).find('form')[0].reset();
            $('#due_day').val('10');
            $('#is_mandatory').prop('checked', true);
            $('#is_active').prop('checked', true);
        }
    });
    
    // Preview count for bulk creation
    function updatePreviewCount() {
        var levels = $('input[name="grade_level_ids[]"]:checked').length;
        var types = $('input[name="fee_type_ids[]"]:checked').length;
        $('#previewCount').text(levels * types);
    }
    
    $('input[name="grade_level_ids[]"], input[name="fee_type_ids[]"]').change(updatePreviewCount);
    
    // Toggle status
    $('.toggle-status').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const currentStatus = $(this).data('status');
        const action = currentStatus ? 'desativar' : 'ativar';
        
        $('#statusAction').text(action);
        $('#statusItemName').text(name);
        $('#confirmStatusBtn').attr('href', '<?= site_url('admin/fees/structure/toggle-status/') ?>' + id);
        new bootstrap.Modal(document.getElementById('statusModal')).show();
    });
});

// Reset filters
function resetFilters() {
    window.location.href = '<?= site_url('admin/financial/fee-structures') ?>';
}

// Export to Excel
function exportToExcel() {
    const data = [];
    const headers = [];
    
    $('#structuresTable thead th').each(function(index) {
        if (index < 9) headers.push($(this).text().trim());
    });
    data.push(headers);
    
    $('#structuresTable tbody tr').each(function() {
        const row = [];
        $(this).find('td').each(function(index) {
            if (index < 9) {
                let cellText = $(this).text().trim();
                row.push(cellText);
            }
        });
        if (row.length) data.push(row);
    });
    
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(data);
    XLSX.utils.book_append_sheet(wb, ws, 'Estrutura Propinas');
    XLSX.writeFile(wb, `estrutura_propinas_${new Date().toISOString().slice(0,10)}.xlsx`);
}
</script>
<?= $this->endSection() ?>