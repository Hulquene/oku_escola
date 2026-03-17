<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-calendar-alt me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Períodos</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/exams/periods/create') ?>" class="hdr-btn primary">
                <i class="fas fa-plus me-1"></i> Novo Período
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
            <span>Filtros</span>
        </div>
    </div>
    <div class="ci-card-body">
        <form method="get" class="filter-grid">
            <div>
                <label class="filter-label">Ano Letivo</label>
                <select class="filter-select" name="academic_year" onchange="this.form.submit()">
                    <option value="">Todos os anos</option>
                    <?php foreach ($academicYears as $year): ?>
                        <option value="<?= $year['id'] ?>" <?= current_academic_year() == $year['id'] ? 'selected' : '' ?>>
                            <?= $year['year_name'] ?> <?= current_academic_year() == $year['id'] ? '(Atual)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-actions" style="grid-column: -1 / 1;">
                <a href="<?= site_url('admin/exams/periods') ?>" class="btn-filter clear">
                    <i class="fas fa-undo me-1"></i> Limpar Filtros
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Períodos -->
<?php if (!empty($periods)): ?>
    <div class="row g-4">
        <?php foreach ($periods as $period): ?>
            <?php 
            // Determinar cores com base no status
            $statusColors = [
                'Planejado' => ['class' => 'secondary', 'icon' => 'fa-calendar'],
                'Em Andamento' => ['class' => 'success', 'icon' => 'fa-play'],
                'Concluído' => ['class' => 'info', 'icon' => 'fa-check-circle'],
                'Cancelado' => ['class' => 'danger', 'icon' => 'fa-times-circle']
            ];
            
            $statusColor = $statusColors[$period['status']] ?? ['class' => 'secondary', 'icon' => 'fa-calendar'];
            
            // Calcular dias restantes ou passados
            $today = new DateTime();
            $start = new DateTime($period['start_date']);
            $end = new DateTime($period['end_date']);
            
            if ($period['status'] == 'Em Andamento') {
                $daysLeft = $today->diff($end)->days;
                $daysText = $daysLeft > 0 ? "$daysLeft dias restantes" : "Último dia hoje";
            } elseif ($period['status'] == 'Planejado') {
                $daysToStart = $today->diff($start)->days;
                $daysText = $today < $start ? "Inicia em $daysToStart dias" : "A iniciar hoje";
            } else {
                $daysText = '';
            }
            ?>
            
            <div class="col-md-4">
                <div class="ci-card period-card h-100">
                    <!-- Cabeçalho com status -->
                    <div class="ci-card-header" style="background: var(--<?= $statusColor['class'] ?>); color: #030303;">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="ci-card-title" style="color: #070707;">
                                <i class="fas <?= $statusColor['icon'] ?> me-2"></i>
                                <span><?= $period['period_name'] ?></span>
                            </div>
                            <span class="badge-ci black">
                                <?= $period['total_exams'] ?? 0 ?> exames
                            </span>
                        </div>
                        <small class="d-block mt-2" style="opacity: 0.9;">
                            <?= $period['period_type'] ?> • <?= $period['semester_name'] ?>
                        </small>
                    </div>
                    
                    <!-- Corpo do card -->
                    <div class="ci-card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i> Início:
                                </span>
                                <strong><?= date('d/m/Y', strtotime($period['start_date'])) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">
                                    <i class="fas fa-calendar-check me-1"></i> Fim:
                                </span>
                                <strong><?= date('d/m/Y', strtotime($period['end_date'])) ?></strong>
                            </div>
                            <?php if ($daysText): ?>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">
                                        <i class="fas fa-hourglass-half me-1"></i> Status:
                                    </span>
                                    <span class="badge-ci <?= $statusColor['class'] ?>" style="background: rgba(var(--<?= $statusColor['class'] ?>-rgb), 0.1);">
                                        <?= $daysText ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($period['description']): ?>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                <?= $period['description'] ?>
                            </p>
                        <?php endif; ?>
                        
                        <!-- Progresso (se aplicável) -->
                        <?php if ($period['status'] == 'Em Andamento' && ($period['total_exams'] ?? 0) > 0): ?>
                            <?php
                            $completedExams = 0; // Você pode calcular isso no model
                            $progress = $period['total_exams'] > 0 ? round(($completedExams / $period['total_exams']) * 100) : 0;
                            ?>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Progresso</span>
                                    <span><?= $progress ?>%</span>
                                </div>
                                <div class="progress" style="height: 5px; background: var(--surface);">
                                    <div class="progress-bar bg-success" style="width: <?= $progress ?>%; height: 5px; border-radius: 3px;"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Footer com ações -->
                    <div class="ci-card-footer" style="background: var(--surface);">
                        <div class="d-flex justify-content-between gap-2">
                            <a href="<?= site_url('admin/exams/periods/view/' . $period['id']) ?>" 
                               class="btn-ci outline flex-grow-1">
                                <i class="fas fa-eye me-1"></i> Detalhes
                            </a>
                            
                            <?php if ($period['status'] != 'Concluído' && $period['status'] != 'Cancelado'): ?>
                                <a href="<?= site_url('admin/exams/periods/generate-schedule/' . $period['id']) ?>" 
                                   class="btn-ci outline" 
                                   title="Gerar calendário">
                                    <i class="fas fa-calendar-plus"></i>
                                </a>
                            <?php endif; ?>
                            
                            <div class="btn-group">
                                <button type="button" 
                                        class="row-btn" 
                                        data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="<?= site_url('admin/exams/periods/edit/' . $period['id']) ?>">
                                            <i class="fas fa-edit me-2 text-primary"></i> Editar
                                        </a>
                                    </li>
                                    <?php if ($period['status'] == 'Planejado'): ?>
                                        <li>
                                            <a class="dropdown-item text-success" href="<?= site_url('admin/exams/periods/start/' . $period['id']) ?>" 
                                              onclick="return confirm('Iniciar este período de exames?')">
                                                <i class="fas fa-play me-2"></i> Iniciar Período
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if ($period['status'] == 'Em Andamento'): ?>
                                        <li>
                                            <a class="dropdown-item text-warning" href="<?= site_url('admin/exams/periods/pause/' . $period['id']) ?>" 
                                              onclick="return confirm('Pausar este período de exames?')">
                                                <i class="fas fa-pause me-2"></i> Pausar
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-success" href="<?= site_url('admin/exams/periods/complete/' . $period['id']) ?>" 
                                              onclick="return confirm('Concluir este período de exames?')">
                                                <i class="fas fa-check-circle me-2"></i> Concluir
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <li>
                                        <a class="dropdown-item text-danger" href="<?= site_url('admin/exams/periods/cancel/' . $period['id']) ?>" 
                                          onclick="return confirm('Tem certeza que deseja cancelar este período?')">
                                            <i class="fas fa-times-circle me-2"></i> Cancelar
                                        </a>
                                    </li>
                                    <?php if ($period['status'] != 'Concluído'): ?>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" 
                                               href="#" 
                                               onclick="confirmDelete(<?= $period['id'] ?>, '<?= $period['period_name'] ?>')">
                                                <i class="fas fa-trash me-2"></i> Eliminar
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Paginação (se houver) -->
    <?php if (isset($pager)): ?>
        <div class="d-flex justify-content-center mt-4">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
    
<?php else: ?>
    <!-- Estado vazio com estilo do sistema -->
    <div class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <h5>Nenhum período de exame encontrado</h5>
        <p class="text-muted">
            Comece por criar um novo período de exames para organizar as avaliações.
        </p>
        <a href="<?= site_url('admin/exams/periods/create') ?>" class="btn-ci primary">
            <i class="fas fa-plus me-2"></i> Criar Período
        </a>
    </div>
<?php endif; ?>

<!-- Modal de Confirmação de Delete com estilo do sistema -->
<div class="modal fade ci-modal" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header danger-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar o período <strong id="deleteItemName"></strong>?</p>
                <div class="alert-ci danger mt-3">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita. Todos os agendamentos associados serão removidos.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-filter clear" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <a href="#" id="deleteConfirmBtn" class="btn-filter danger">
                    <i class="fas fa-trash me-2"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id, name) {
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteConfirmBtn').href = '<?= site_url('admin/exams/periods/delete') ?>/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

$(document).ready(function() {
    // Inicializar tooltips se existirem
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>

<style>
/* Estilos adicionais específicos para esta página */
.period-card {
    border: none;
    transition: all 0.3s ease;
    overflow: hidden;
}

.period-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.period-card .ci-card-header {
    border-bottom: none;
}

.period-card .ci-card-footer {
    border-top: 1px solid var(--border);
}

/* Badge light para fundos escuros */
.badge-ci.light {
    background: rgba(255,255,255,0.15);
    color: #fff;
}

/* Progress bar customizada */
.progress {
    background: var(--surface);
    border-radius: 3px;
    overflow: hidden;
}

.progress-bar {
    border-radius: 3px;
    transition: width 0.3s ease;
}

.progress-bar.bg-success {
    background: var(--success) !important;
}

/* Cores de texto para badges */
.text-success { color: var(--success) !important; }
.text-warning { color: var(--warning) !important; }
.text-danger { color: var(--danger) !important; }
.text-primary { color: var(--accent) !important; }
.text-info { color: var(--accent) !important; }

/* Dropdown menu */
.dropdown-menu {
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    box-shadow: var(--shadow-md);
    padding: 0.5rem;
}

.dropdown-item {
    border-radius: 5px;
    padding: 0.5rem 1rem;
    font-size: 0.82rem;
    transition: all 0.18s;
}

.dropdown-item:hover {
    background: var(--surface);
}

.dropdown-divider {
    border-top: 1px solid var(--border);
    margin: 0.5rem 0;
}

/* Botão de ellipsis */
.row-btn {
    width: 32px;
    height: 32px;
    border-radius: 7px;
    border: 1.5px solid var(--border);
    background: #fff;
    color: var(--text-secondary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .75rem;
    text-decoration: none;
    cursor: pointer;
    transition: all .18s;
}

.row-btn:hover {
    background: var(--surface);
    color: var(--primary);
    border-color: var(--primary);
}

/* Animações */
.col-md-4 {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsividade */
@media (max-width: 768px) {
    .btn-ci.outline {
        padding: 0.35rem 0.8rem;
        font-size: 0.75rem;
    }
    
    .row-btn {
        width: 28px;
        height: 28px;
    }
    
    .period-card .ci-card-footer .d-flex {
        flex-wrap: wrap;
    }
}
</style>
<?= $this->endSection() ?>