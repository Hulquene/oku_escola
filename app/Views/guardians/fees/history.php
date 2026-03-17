<?= $this->extend('guardians/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-history me-2" style="color: var(--info);"></i>Histórico de Pagamentos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/fees') ?>">Propinas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Histórico</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('guardians/fees') ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Lista de Pagamentos -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-list"></i>
            <span>Histórico de Pagamentos</span>
        </div>
        <span class="badge-ci primary">
            <i class="fas fa-list me-1"></i> <?= count($payments) ?> registos
        </span>
    </div>
    
    <div class="ci-card-body p0">
        <?php if (!empty($payments)): ?>
            <div class="table-responsive">
                <table class="ci-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Aluno</th>
                            <th>Fatura</th>
                            <th class="right">Valor</th>
                            <th>Forma Pagamento</th>
                            <th class="center">Status</th>
                            <th class="center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($payment['payment_date'])) ?></td>
                                <td>
                                    <strong><?= $payment['first_name'] ?> <?= $payment['last_name'] ?></strong>
                                    <br>
                                    <small class="text-muted"><?= $payment['student_number'] ?></small>
                                </td>
                                <td>
                                    <span class="code-badge"><?= $payment['invoice_number'] ?></span>
                                </td>
                                <td class="right fw-bold"><?= number_format($payment['total_amount'], 2) ?> KZ</td>
                                <td><?= $payment['payment_method'] ?? 'Transferência' ?></td>
                                <td class="center">
                                    <span class="badge-ci success">Pago</span>
                                </td>
                                <td class="center">
                                    <div class="action-group">
                                        <a href="#" class="row-btn view" title="Ver Recibo">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                        <a href="#" class="row-btn secondary" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-history"></i>
                <h5>Nenhum pagamento encontrado</h5>
                <p class="text-muted">Não há histórico de pagamentos registado.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>