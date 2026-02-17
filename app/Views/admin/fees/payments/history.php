<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/students/view/' . $student->id) ?>" class="btn btn-info">
            <i class="fas fa-user-graduate"></i> Ver Aluno
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/fees/payments') ?>">Pagamentos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Histórico do Aluno</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Student Info -->
<div class="alert alert-info mb-4">
    <div class="d-flex align-items-center">
        <?php if ($student->photo): ?>
            <img src="<?= base_url('uploads/students/' . $student->photo) ?>" 
                 alt="Foto" 
                 class="rounded-circle me-3"
                 style="width: 50px; height: 50px; object-fit: cover;">
        <?php else: ?>
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-3"
                 style="width: 50px; height: 50px;">
                <?= strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) ?>
            </div>
        <?php endif; ?>
        <div>
            <h5 class="mb-0"><?= $student->first_name ?> <?= $student->last_name ?></h5>
            <small><?= $student->student_number ?> | <?= $student->email ?></small>
        </div>
    </div>
</div>

<!-- Payment History -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-history"></i> Histórico Completo de Pagamentos
    </div>
    <div class="card-body">
        <?php if (!empty($payments)): ?>
            <?php
            $totalPaid = 0;
            foreach ($payments as $payment) {
                $totalPaid += $payment->amount_paid;
            }
            ?>
            
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">Total Pago</h6>
                            <h3><?= number_format($totalPaid, 2, ',', '.') ?> Kz</h3>
                            <small><?= count($payments) ?> pagamentos</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nº Recibo</th>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Ano Letivo</th>
                            <th>Valor</th>
                            <th>Método</th>
                            <th>Referência</th>
                            <th>Recebido por</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= $payment->payment_number ?></span></td>
                                <td><?= date('d/m/Y', strtotime($payment->payment_date)) ?></td>
                                <td><?= $payment->type_name ?></td>
                                <td><?= $payment->year_name ?></td>
                                <td class="text-end fw-bold"><?= number_format($payment->amount_paid, 2, ',', '.') ?> Kz</td>
                                <td><?= $payment->payment_method ?></td>
                                <td><?= $payment->payment_reference ?: '-' ?></td>
                                <td>
                                    <?php
                                    $userModel = new \App\Models\UserModel();
                                    $receiver = $userModel->find($payment->received_by);
                                    echo $receiver ? $receiver->first_name . ' ' . $receiver->last_name : 'Sistema';
                                    ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('admin/fees/payments/receipt/' . $payment->id) ?>" 
                                       class="btn btn-sm btn-success" target="_blank" title="Ver Recibo">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhum pagamento encontrado para este aluno.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'desc']]
    });
});
</script>
<?= $this->endSection() ?>