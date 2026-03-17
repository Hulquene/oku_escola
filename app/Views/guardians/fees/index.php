<?= $this->extend('guardians/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-money-bill me-2" style="color: var(--primary);"></i>Propinas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Propinas</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Resumo por Aluno -->
<?php if (!empty($students)): ?>
    <div class="row g-4">
        <?php foreach ($students as $student): ?>
            <div class="col-md-6">
                <div class="ci-card">
                    <div class="ci-card-header" style="background: var(--primary);">
                        <div class="ci-card-title" style="color: #fff;">
                            <i class="fas fa-user-graduate me-2"></i>
                            <span><?= $student['first_name'] ?> <?= $student['last_name'] ?></span>
                        </div>
                        <span class="badge-ci light"><?= $student['student_number'] ?></span>
                    </div>
                    
                    <div class="ci-card-body">
                        <div class="row g-3">
                            <div class="col-4 text-center">
                                <div class="small text-muted">Total</div>
                                <div class="fw-bold"><?= number_format($student['total'], 2) ?> KZ</div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="small text-muted">Pago</div>
                                <div class="fw-bold text-success"><?= number_format($student['paid'], 2) ?> KZ</div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="small text-muted">Pendente</div>
                                <div class="fw-bold text-<?= $student['pending'] > 0 ? 'warning' : 'secondary' ?>">
                                    <?= number_format($student['pending'], 2) ?> KZ
                                </div>
                            </div>
                        </div>
                        
                        <div class="progress mt-3" style="height: 8px; background: var(--surface);">
                            <?php $percentage = $student['total'] > 0 ? ($student['paid'] / $student['total']) * 100 : 0; ?>
                            <div class="progress-bar" style="width: <?= $percentage ?>%; background: var(--success);"></div>
                        </div>
                        
                        <div class="text-end mt-2">
                            <small class="text-muted"><?= round($percentage) ?>% pago</small>
                        </div>
                    </div>
                    
                    <div class="ci-card-footer">
                        <a href="<?= site_url('guardians/fees/student/' . $student['student_id']) ?>" 
                           class="btn-ci outline w-100">
                            <i class="fas fa-eye me-1"></i> Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="mt-4">
        <a href="<?= site_url('guardians/fees/history') ?>" class="btn-ci primary">
            <i class="fas fa-history me-2"></i>Ver Histórico de Pagamentos
        </a>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-money-bill"></i>
        <h5>Nenhum aluno associado</h5>
        <p class="text-muted">Você não tem alunos associados para visualizar propinas.</p>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>