<?= $this->extend('guardians/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-money-bill me-2" style="color: var(--primary);"></i>Propinas do Aluno</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/students') ?>">Meus Alunos</a></li>
                    <li class="breadcrumb-item active">Propinas</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('guardians/students/view/' . $student['id']) ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <div class="mt-2">
        <span class="badge-ci info">
            <i class="fas fa-user-graduate me-1"></i> <?= $student['first_name'] ?> <?= $student['last_name'] ?> (<?= $student['student_number'] ?>)
        </span>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Resumo Financeiro -->
<div class="stat-grid mb-4">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-coins"></i>
        </div>
        <div>
            <div class="stat-label">Total</div>
            <div class="stat-value"><?= number_format($summary['total'], 2) ?> KZ</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="stat-label">Pago</div>
            <div class="stat-value"><?= number_format($summary['paid'], 2) ?> KZ</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <div class="stat-label">Pendente</div>
            <div class="stat-value"><?= number_format($summary['pending'], 2) ?> KZ</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon navy">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <div class="stat-label">Vencidas</div>
            <div class="stat-value"><?= $summary['overdue'] ?></div>
        </div>
    </div>
</div>

<!-- Lista de Propinas -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-list"></i>
            <span>Detalhamento de Propinas</span>
        </div>
        <span class="badge-ci primary">
            <i class="fas fa-list me-1"></i> <?= count($fees) ?> registos
        </span>
    </div>
    
    <div class="ci-card-body p0">
        <?php if (!empty($fees)): ?>
            <div class="table-responsive">
                <table class="ci-table">
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Vencimento</th>
                            <th class="right">Valor</th>
                            <th class="center">Status</th>
                            <th class="center">Data Pagamento</th>
                            <th class="right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fees as $fee): ?>
                            <?php
                            $statusClass = [
                                'Pago' => 'success',
                                'Pendente' => 'warning',
                                'Vencido' => 'danger',
                                'Cancelado' => 'secondary'
                            ][$fee['status']] ?? 'secondary';
                            ?>
                            <tr>
                                <td>
                                    <strong><?= $fee['fee_name'] ?></strong>
                                    <br>
                                    <small class="text-muted"><?= $fee['type_name'] ?></small>
                                </td>
                                <td><?= $fee['formatted_due_date'] ?></td>
                                <td class="right fw-bold"><?= number_format($fee['total_amount'], 2) ?> KZ</td>
                                <td class="center">
                                    <span class="badge-ci <?= $statusClass ?>">
                                        <?= $fee['status'] ?>
                                    </span>
                                </td>
                                <td class="center"><?= $fee['formatted_payment_date'] ?? '-' ?></td>
                                <td class="right">
                                    <?php if (in_array($fee['status'], ['Pendente', 'Vencido'])): ?>
                                        <a href="<?= site_url('guardians/fees/pay/' . $fee['id']) ?>" 
                                           class="row-btn success" title="Pagar">
                                            <i class="fas fa-credit-card"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-money-bill"></i>
                <h5>Nenhuma propina encontrada</h5>
                <p class="text-muted">Este aluno não possui propinas registadas.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>