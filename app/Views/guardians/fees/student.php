<?= $this->extend('guardians/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-money-bill me-2" style="color: var(--primary);"></i>Propinas de <?= $student['first_name'] ?> <?= $student['last_name'] ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/fees') ?>">Propinas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('guardians/fees') ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <div class="mt-2">
        <span class="badge-ci info">
            <i class="fas fa-user-graduate me-1"></i> <?= $student['student_number'] ?>
        </span>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

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
                                <td class="right">
                                    <?php if (in_array($fee['status'], ['Pendente', 'Vencido'])): ?>
                                        <a href="<?= site_url('guardians/fees/pay/' . $fee['id']) ?>" 
                                           class="row-btn success" title="Pagar Online">
                                            <i class="fas fa-credit-card"></i>
                                        </a>
                                        <button type="button" class="row-btn primary" 
                                                onclick="generateBarcode(<?= $fee['id'] ?>)"
                                                title="Gerar Referência">
                                            <i class="fas fa-barcode"></i>
                                        </button>
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

<?= $this->section('scripts') ?>
<script>
function generateBarcode(feeId) {
    Swal.fire({
        title: 'Gerar Referência',
        html: `
            <div class="text-center">
                <i class="fas fa-barcode fa-3x text-primary mb-3"></i>
                <p>Gerar referência para pagamento?</p>
                <div class="alert-ci info small">
                    <i class="fas fa-info-circle"></i>
                    A referência será enviada por SMS e Email.
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: 'var(--success)',
        cancelButtonColor: 'var(--border)',
        confirmButtonText: '<i class="fas fa-barcode me-2"></i>Gerar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Referência Gerada!',
                html: `
                    <strong>Entidade: 12345</strong><br>
                    <strong>Referência: 123 456 789</strong><br>
                    <strong>Valor: 15.000,00 KZ</strong><br>
                    <small class="text-muted">Válida até 25/03/2026</small>
                `,
                confirmButtonText: 'OK'
            });
        }
    });
}
</script>
<?= $this->endSection() ?>