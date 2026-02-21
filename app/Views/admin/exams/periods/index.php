<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-alt me-1"></i>
                Gerencie os períodos de exames e avaliações
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/exams/periods/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Novo Período
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active">Períodos</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Ano Letivo</label>
                <select class="form-select" name="academic_year" onchange="this.form.submit()">
                    <option value="">Todos os anos</option>
                    <?php foreach ($academicYears as $year): ?>
                        <option value="<?= $year->id ?>" <?= $selectedYear == $year->id ? 'selected' : '' ?>>
                            <?= $year->year_name ?> <?= $year->is_current ? '(Atual)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <a href="<?= site_url('admin/exams/periods') ?>" class="btn btn-secondary">
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
                'Planejado' => ['bg' => 'secondary', 'icon' => 'fa-calendar'],
                'Em Andamento' => ['bg' => 'success', 'icon' => 'fa-play'],
                'Concluído' => ['bg' => 'info', 'icon' => 'fa-check-circle'],
                'Cancelado' => ['bg' => 'danger', 'icon' => 'fa-times-circle']
            ];
            
            $statusColor = $statusColors[$period->status] ?? ['bg' => 'secondary', 'icon' => 'fa-calendar'];
            
            // Calcular dias restantes ou passados
            $today = new DateTime();
            $start = new DateTime($period->start_date);
            $end = new DateTime($period->end_date);
            
            if ($period->status == 'Em Andamento') {
                $daysLeft = $today->diff($end)->days;
                $daysText = $daysLeft > 0 ? "$daysLeft dias restantes" : "Último dia hoje";
            } elseif ($period->status == 'Planejado') {
                $daysToStart = $today->diff($start)->days;
                $daysText = $today < $start ? "Inicia em $daysToStart dias" : "A iniciar hoje";
            } else {
                $daysText = '';
            }
            ?>
            
            <div class="col-md-4">
                <div class="card h-100 period-card">
                    <!-- Cabeçalho com status -->
                    <div class="card-header bg-<?= $statusColor['bg'] ?> text-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas <?= $statusColor['icon'] ?> me-2"></i>
                                <?= $period->period_name ?>
                            </h5>
                            <span class="badge bg-light text-dark">
                                <?= $period->total_exams ?? 0 ?> exames
                            </span>
                        </div>
                        <small class="d-block mt-2">
                            <?= $period->period_type ?> • <?= $period->semester_name ?>
                        </small>
                    </div>
                    
                    <!-- Corpo do card -->
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i> Início:
                                </span>
                                <strong><?= date('d/m/Y', strtotime($period->start_date)) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">
                                    <i class="fas fa-calendar-check me-1"></i> Fim:
                                </span>
                                <strong><?= date('d/m/Y', strtotime($period->end_date)) ?></strong>
                            </div>
                            <?php if ($daysText): ?>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">
                                        <i class="fas fa-hourglass-half me-1"></i> Status:
                                    </span>
                                    <span class="badge bg-<?= $statusColor['bg'] ?> bg-opacity-10 text-<?= $statusColor['bg'] ?>">
                                        <?= $daysText ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($period->description): ?>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                <?= $period->description ?>
                            </p>
                        <?php endif; ?>
                        
                        <!-- Progresso (se aplicável) -->
                        <?php if ($period->status == 'Em Andamento' && ($period->total_exams ?? 0) > 0): ?>
                            <?php
                            $completedExams = 0; // Você pode calcular isso no model
                            $progress = $period->total_exams > 0 ? round(($completedExams / $period->total_exams) * 100) : 0;
                            ?>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Progresso</span>
                                    <span><?= $progress ?>%</span>
                                </div>
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar bg-success" style="width: <?= $progress ?>%"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Footer com ações -->
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between gap-2">
                            <a href="<?= site_url('admin/exams/periods/view/' . $period->id) ?>" 
                               class="btn btn-sm btn-outline-primary flex-grow-1">
                                <i class="fas fa-eye me-1"></i> Detalhes
                            </a>
                            
                            <?php if ($period->status != 'Concluído' && $period->status != 'Cancelado'): ?>
                                <a href="<?= site_url('admin/exams/periods/generate-schedule/' . $period->id) ?>" 
                                   class="btn btn-sm btn-outline-success" 
                                   title="Gerar calendário">
                                    <i class="fas fa-calendar-plus"></i>
                                </a>
                            <?php endif; ?>
                            
                            <div class="btn-group">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                        data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="<?= site_url('admin/exams/periods/edit/' . $period->id) ?>">
                                            <i class="fas fa-edit me-2 text-primary"></i> Editar
                                        </a>
                                    </li>
                                    <!-- No dropdown menu, substitua os links -->
                                    <?php if ($period->status == 'Planejado'): ?>
                                        <li>
                                            <a class="dropdown-item text-success" href="<?= site_url('admin/exams/periods/start/' . $period->id) ?>" 
                                              onclick="return confirm('Iniciar este período de exames?')">
                                                <i class="fas fa-play me-2"></i> Iniciar Período
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if ($period->status == 'Em Andamento'): ?>
                                        <li>
                                            <a class="dropdown-item text-warning" href="<?= site_url('admin/exams/periods/pause/' . $period->id) ?>" 
                                              onclick="return confirm('Pausar este período de exames?')">
                                                <i class="fas fa-pause me-2"></i> Pausar
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-success" href="<?= site_url('admin/exams/periods/complete/' . $period->id) ?>" 
                                              onclick="return confirm('Concluir este período de exames?')">
                                                <i class="fas fa-check-circle me-2"></i> Concluir
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <li>
                                        <a class="dropdown-item text-danger" href="<?= site_url('admin/exams/periods/cancel/' . $period->id) ?>" 
                                          onclick="return confirm('Tem certeza que deseja cancelar este período?')">
                                            <i class="fas fa-times-circle me-2"></i> Cancelar
                                        </a>
                                    </li>
                                    <?php if ($period->status != 'Concluído'): ?>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" 
                                               href="#" 
                                               onclick="confirmDelete(<?= $period->id ?>, '<?= $period->period_name ?>')">
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
    <!-- Estado vazio -->
    <div class="empty-state">
        <i class="fas fa-calendar-times empty-state-icon"></i>
        <h5 class="empty-state-title">Nenhum período de exame encontrado</h5>
        <p class="empty-state-text">
            Comece por criar um novo período de exames para organizar as avaliações.
        </p>
        <a href="<?= site_url('admin/exams/periods/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Criar Período
        </a>
    </div>
<?php endif; ?>

<!-- Modal de Confirmação de Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar o período <strong id="deleteItemName"></strong>?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita. Todos os agendamentos associados serão removidos.
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="deleteConfirmBtn" class="btn btn-danger">
                    <i class="fas fa-trash me-2"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.period-card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.period-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: #fff;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.empty-state-icon {
    font-size: 4rem;
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

.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}

.card-header {
    border-bottom: none;
}

.card-footer {
    border-top: 1px solid rgba(0,0,0,.05);
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.col-md-4 {
    animation: fadeIn 0.3s ease-out;
}
</style>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteConfirmBtn').href = '<?= site_url('admin/exams/periods/delete') ?>/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?= $this->endSection() ?>