<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
:root {
    --primary: #1B2B4B;
    --accent: #3B7FE8;
    --success: #16A87D;
    --warning: #E8A020;
    --danger: #E84646;
    --surface: #F5F7FC;
    --border: #E2E8F4;
}

.page-header {
    background: linear-gradient(135deg, var(--primary) 0%, #243761 100%);
    border-radius: 12px;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    color: white;
}

.page-header h1 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.page-header .breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
}

.page-header .breadcrumb-item a {
    color: rgba(255,255,255,0.7);
    text-decoration: none;
}

.page-header .breadcrumb-item.active {
    color: white;
}

.class-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    height: 100%;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.class-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(27,43,75,.10);
    border-color: var(--accent);
}

.class-card.ready {
    border-left: 4px solid var(--success);
}

.class-card.pending {
    border-left: 4px solid var(--warning);
}

.class-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary);
    margin-bottom: 0.25rem;
}

.class-code {
    color: var(--accent);
    font-weight: 500;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin: 1rem 0;
}

.stat-item {
    text-align: center;
    padding: 0.75rem;
    background: var(--surface);
    border-radius: 8px;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary);
    line-height: 1.2;
}

.stat-label {
    font-size: 0.7rem;
    color: #6B7A99;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.progress-container {
    margin: 1rem 0;
}

.progress-bar {
    height: 8px;
    background: var(--surface);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-fill {
    height: 100%;
    background: var(--accent);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.progress-fill.complete {
    background: var(--success);
}

.progress-fill.partial {
    background: var(--warning);
}

.btn-approve {
    background: var(--accent);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    width: 100%;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-approve:hover:not(:disabled) {
    background: #2C6FD4;
    transform: translateY(-1px);
}

.btn-approve:disabled {
    background: #ccc;
    cursor: not-allowed;
    opacity: 0.7;
}

.status-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-badge.ready {
    background: rgba(22,168,125,.1);
    color: var(--success);
}

.status-badge.pending {
    background: rgba(232,160,32,.1);
    color: var(--warning);
}

.info-icon {
    color: var(--accent);
    margin-right: 0.5rem;
}
</style>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-check-circle me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/academic-records') ?>">Pautas</a></li>
                    <li class="breadcrumb-item active">Aprovações</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold">Ano Letivo</label>
                <select name="academic_year" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($academicYears as $year): ?>
                    <option value="<?= $year['id'] ?>" <?= $selectedYear == $year['id'] ? 'selected' : '' ?>>
                        <?= $year['year_name'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-8 text-end">
                <a href="<?= site_url('admin/academic-records/promotion-results?academic_year=' . $selectedYear) ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-up me-1"></i> Ver Resultados da Progressão
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Turmas -->
<?php if (empty($classes)): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    Nenhuma turma com alunos ativos encontrada para o ano letivo selecionado.
</div>
<?php else: ?>
<div class="row">
    <?php foreach ($classes as $class): 
        $progress = $class->total_alunos > 0 ? round(($class->alunos_com_notas / $class->total_alunos) * 100) : 0;
        $canApprove = ($class->total_alunos > 0 && $class->alunos_com_notas == $class->total_alunos);
        $statusClass = $canApprove ? 'ready' : 'pending';
        $statusText = $canApprove ? 'Pronto para aprovação' : 'Aguardando notas';
    ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="class-card <?= $statusClass ?>" 
             onclick="window.location.href='<?= site_url('admin/academic-records/approvals/' . $class['id']) ?>'">
            
            <div class="status-badge <?= $statusClass ?>">
                <i class="fas fa-<?= $canApprove ? 'check-circle' : 'clock' ?> me-1"></i>
                <?= $canApprove ? 'Pronto' : 'Pendente' ?>
            </div>
            
            <div class="class-title"><?= $class['class_name'] ?></div>
            <div class="class-code"><?= $class['class_code'] ?></div>
            
            <div class="mb-2">
                <i class="fas fa-layer-group info-icon"></i> <?= $class->level_name ?>
            </div>
            
            <?php if ($class->course_name): ?>
            <div class="mb-3">
                <i class="fas fa-graduation-cap info-icon"></i> <?= $class->course_name ?>
            </div>
            <?php endif; ?>
            
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?= $class->total_alunos ?></div>
                    <div class="stat-label">Total Alunos</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $class->alunos_com_notas ?></div>
                    <div class="stat-label">Com Notas</div>
                </div>
            </div>
            
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-fill <?= $progress == 100 ? 'complete' : ($progress > 0 ? 'partial' : '') ?>" 
                         style="width: <?= $progress ?>%"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Progresso: <?= $progress ?>%</small>
                    <?php if (!$canApprove && $class->alunos_com_notas < $class->total_alunos): ?>
                    <small class="text-warning">
                        <i class="fas fa-clock me-1"></i> <?= $class->total_alunos - $class->alunos_com_notas ?> pendentes
                    </small>
                    <?php endif; ?>
                </div>
            </div>
            
            <button class="btn-approve" <?= !$canApprove ? 'disabled' : '' ?>>
                <i class="fas fa-check-circle me-1"></i>
                Processar Aprovações
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Resumo -->
<div class="card mt-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 col-6">
                <div class="text-center">
                    <h5 class="fw-bold text-primary"><?= count($classes) ?></h5>
                    <small class="text-muted">Total de Turmas</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="text-center">
                    <?php 
                    $prontas = 0;
                    foreach ($classes as $c) {
                        if ($c->total_alunos > 0 && $c->alunos_com_notas == $c->total_alunos) $prontas++;
                    }
                    ?>
                    <h5 class="fw-bold text-success"><?= $prontas ?></h5>
                    <small class="text-muted">Prontas</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="text-center">
                    <?php 
                    $pendentes = 0;
                    foreach ($classes as $c) {
                        if ($c->total_alunos > 0 && $c->alunos_com_notas < $c->total_alunos) $pendentes++;
                    }
                    ?>
                    <h5 class="fw-bold text-warning"><?= $pendentes ?></h5>
                    <small class="text-muted">Pendentes</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="text-center">
                    <?php 
                    $totalAlunos = 0;
                    $totalComNotas = 0;
                    foreach ($classes as $c) {
                        $totalAlunos += $c->total_alunos;
                        $totalComNotas += $c->alunos_com_notas;
                    }
                    $mediaProgresso = $totalAlunos > 0 ? round(($totalComNotas / $totalAlunos) * 100) : 0;
                    ?>
                    <h5 class="fw-bold text-info"><?= $mediaProgresso ?>%</h5>
                    <small class="text-muted">Progresso Geral</small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>