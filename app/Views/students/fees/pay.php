<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('students/fees') ?>">Propinas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pagamento</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row">
    <div class="col-md-8">
        <!-- Payment Information -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-info-circle"></i> Informações do Pagamento
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Importante:</strong> Após efetuar o pagamento, guarde o comprovativo para apresentar na secretaria.
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th>Referência:</th>
                                <td><?= $fee->reference_number ?></td>
                            </tr>
                            <tr>
                                <th>Tipo:</th>
                                <td><?= $fee->type_name ?></td>
                            </tr>
                            <tr>
                                <th>Valor:</th>
                                <td class="fw-bold"><?= number_format($fee->total_amount, 2, ',', '.') ?> Kz</td>
                            </tr>
                            <tr>
                                <th>Vencimento:</th>
                                <td><?= date('d/m/Y', strtotime($fee->due_date)) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th>Aluno:</th>
                                <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                            </tr>
                            <tr>
                                <th>Matrícula:</th>
                                <td><?= $student->student_number ?></td>
                            </tr>
                            <tr>
                                <th>Turma:</th>
                                <td><?= session()->get('class_name') ?></td>
                            </tr>
                            <tr>
                                <th>Ano Letivo:</th>
                                <td><?= session()->get('academic_year') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bank Transfer Details -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <i class="fas fa-university"></i> Dados para Transferência Bancária
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Banco:</strong> <?= $school->bank_name ?></p>
                        <p><strong>Nº de Conta:</strong> <?= $school->bank_account ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>IBAN:</strong> <?= $school->bank_iban ?></p>
                        <p><strong>SWIFT/BIC:</strong> <?= $school->bank_swift ?></p>
                    </div>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i>
                    <strong>Referência para transferência:</strong> <?= $fee->reference_number ?>
                </div>
            </div>
        </div>
        
        <!-- Payment Confirmation -->
        <div class="card">
            <div class="card-header bg-warning">
                <i class="fas fa-check-circle"></i> Confirmar Pagamento
            </div>
            <div class="card-body">
                <p>Após realizar o pagamento, dirija-se à secretaria com o comprovativo para regularizar a sua situação.</p>
                <p>Alternativamente, pode enviar o comprovativo por email para:</p>
                <p class="fw-bold"><?= $school->email ?></p>
                
                <hr>
                
                <div class="text-center">
                    <a href="<?= site_url('students/fees') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <a href="#" class="btn btn-success" onclick="window.print()">
                        <i class="fas fa-print"></i> Imprimir Dados
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- QR Code (opcional) -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-qrcode"></i> Código QR
            </div>
            <div class="card-body text-center">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode($fee->reference_number) ?>" 
                     alt="QR Code" class="img-fluid">
                <p class="mt-2">Escaneie para pagamento rápido</p>
            </div>
        </div>
        
        <!-- Payment Methods -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-credit-card"></i> Formas de Pagamento
            </div>
            <div class="list-group list-group-flush">
                <div class="list-group-item">
                    <i class="fas fa-university text-primary"></i> Transferência Bancária
                </div>
                <div class="list-group-item">
                    <i class="fas fa-money-bill text-success"></i> Dinheiro (Secretaria)
                </div>
                <div class="list-group-item">
                    <i class="fas fa-credit-card text-info"></i> Multicaixa (Secretaria)
                </div>
                <div class="list-group-item">
                    <i class="fas fa-mobile-alt text-warning"></i> Pagamento via App
                </div>
            </div>
        </div>
    </div>
</div>

<style media="print">
    .btn, .page-header .btn, .breadcrumb, .navbar, .sidebar, footer {
        display: none !important;
    }
    .content {
        margin-left: 0 !important;
    }
    .main-content {
        padding: 0 !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
</style>

<?= $this->endSection() ?>