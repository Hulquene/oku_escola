<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-file-alt me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Documentos Acadêmicos</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body text-center p-5">
                <i class="fas fa-certificate fa-4x text-primary mb-3"></i>
                <h3>Certificados</h3>
                <p class="text-muted">Gerar certificados para alunos que concluíram ciclos ou cursos</p>
                <a href="<?= site_url('admin/academic-documents/eligible-certificates') ?>" class="btn btn-primary">
                    <i class="fas fa-certificate me-1"></i> Gerar Certificados
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body text-center p-5">
                <i class="fas fa-file-alt fa-4x text-success mb-3"></i>
                <h3>Declarações</h3>
                <p class="text-muted">Gerar declarações de conclusão de ano letivo</p>
                <a href="<?= site_url('admin/academic-documents/eligible-declarations') ?>" class="btn btn-success">
                    <i class="fas fa-file-alt me-1"></i> Gerar Declarações
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-body text-center p-5">
                <i class="fas fa-history fa-4x text-info mb-3"></i>
                <h3>Histórico</h3>
                <p class="text-muted">Visualizar todos os documentos já emitidos</p>
                <a href="<?= site_url('admin/academic-documents/history') ?>" class="btn btn-info text-white">
                    <i class="fas fa-history me-1"></i> Ver Histórico
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>