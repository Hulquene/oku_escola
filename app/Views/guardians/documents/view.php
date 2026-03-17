<?= $this->extend('guardians/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-file-alt me-2"></i>Visualizar Documento</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/documents') ?>">Documentos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Visualizar</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('guardians/documents/download/' . $document['id']) ?>" class="hdr-btn success">
                <i class="fas fa-download me-1"></i> Download
            </a>
            <a href="<?= site_url('guardians/documents') ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-info-circle"></i>
                    <span>Informações do Documento</span>
                </div>
            </div>
            <div class="ci-card-body">
                <table class="ci-table table-borderless">
                    <tr>
                        <td class="text-muted">Nome:</td>
                        <td class="fw-semibold"><?= $document['document_name'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Arquivo:</td>
                        <td class="fw-semibold"><?= $document['file_name'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tipo:</td>
                        <td class="fw-semibold"><?= strtoupper(pathinfo($document['file_name'], PATHINFO_EXTENSION)) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tamanho:</td>
                        <td class="fw-semibold">
                            <?php
                            $size = filesize(FCPATH . $document['file_path']);
                            echo $size < 1024 ? $size . ' B' : ($size < 1048576 ? round($size/1024, 1) . ' KB' : round($size/1048576, 1) . ' MB');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Data Upload:</td>
                        <td class="fw-semibold"><?= date('d/m/Y H:i', strtotime($document['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status:</td>
                        <td class="fw-semibold">
                            <?php if ($document['is_verified']): ?>
                                <span class="badge-ci success">Verificado</span>
                            <?php else: ?>
                                <span class="badge-ci warning">Pendente</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-eye"></i>
                    <span>Pré-visualização</span>
                </div>
            </div>
            <div class="ci-card-body">
                <?php
                $ext = strtolower(pathinfo($document['file_name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                    <img src="<?= base_url($document['file_path']) ?>" class="img-fluid" alt="Documento">
                <?php elseif ($ext == 'pdf'): ?>
                    <iframe src="<?= base_url($document['file_path']) ?>" style="width: 100%; height: 600px;" frameborder="0"></iframe>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-file fa-4x text-muted mb-3"></i>
                        <h5>Pré-visualização não disponível</h5>
                        <p class="text-muted">Faça o download para visualizar o documento.</p>
                        <a href="<?= site_url('guardians/documents/download/' . $document['id']) ?>" class="btn-ci primary">
                            <i class="fas fa-download me-2"></i>Download
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>