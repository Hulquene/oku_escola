<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/documents') ?>">Central de Documentos</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/documents/pending') ?>">Pendentes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Verificar Documento</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Visualização do Documento -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    Visualização do Documento
                </h5>
                <div>
                    <a href="<?= site_url('admin/documents/download/' . $document->id) ?>" class="btn btn-sm btn-success">
                        <i class="fas fa-download me-1"></i> Download
                    </a>
                </div>
            </div>
            <div class="card-body text-center">
                <?php
                $isPDF = strpos($document->document_mime, 'pdf') !== false;
                $isImage = strpos($document->document_mime, 'image') !== false;
                $viewUrl = site_url('admin/documents/serve/' . $document->id);
                ?>
                
                <?php if ($isPDF): ?>
                    <div class="pdf-container">
                        <iframe src="<?= $viewUrl ?>" 
                                style="width: 100%; height: 600px; border: 1px solid #ddd; border-radius: 4px;" 
                                frameborder="0">
                        </iframe>
                    </div>
                    <div class="mt-2 text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        Se o PDF não carregar, faça o <a href="<?= site_url('admin/documents/download/' . $document->id) ?>" class="text-primary">download</a>.
                    </div>
                <?php elseif ($isImage): ?>
                    <div class="image-container">
                        <img src="<?= $viewUrl ?>" 
                             alt="Documento" 
                             class="img-fluid border rounded" 
                             style="max-height: 600px; max-width: 100%;"
                             onerror="this.onerror=null; this.src='<?= base_url('assets/img/document-placeholder.png') ?>'; this.alt='Erro ao carregar imagem';">
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h5>Visualização não disponível</h5>
                        <p>Este tipo de arquivo não pode ser visualizado diretamente no navegador.</p>
                        <a href="<?= site_url('admin/documents/download/' . $document->id) ?>" class="btn btn-primary mt-2">
                            <i class="fas fa-download me-2"></i> Baixar Arquivo
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Informações do Documento -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Informações
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Usuário:</th>
                        <td>
                            <span class="fw-semibold"><?= $document->user_fullname ?></span><br>
                            <small class="text-muted"><?= $document->email ?></small>
                        </td>
                    </tr>
                    <tr>
                        <th>Tipo:</th>
                        <td><?= $document->user_type == 'student' ? 'Aluno' : 'Professor' ?></td>
                    </tr>
                    <tr>
                        <th>Documento:</th>
                        <td><?= $document->document_type ?></td>
                    </tr>
                    <tr>
                        <th>Nome Arquivo:</th>
                        <td><?= $document->document_name ?></td>
                    </tr>
                    <tr>
                        <th>Data Envio:</th>
                        <td><?= date('d/m/Y H:i', strtotime($document->created_at)) ?></td>
                    </tr>
                    <tr>
                        <th>Tamanho:</th>
                        <td><?= round($document->document_size / 1024, 1) ?> KB</td>
                    </tr>
                    <?php if ($document->description): ?>
                    <tr>
                        <th>Descrição:</th>
                        <td><?= $document->description ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        
        <!-- Ações de Verificação -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tasks text-primary me-2"></i>
                    Verificação
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= site_url('admin/documents/verify/save/' . $document->id) ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status da Verificação</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusApprove" value="approved" checked>
                                <label class="form-check-label" for="statusApprove">
                                    <span class="badge bg-success p-2">Aprovar</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusReject" value="rejected">
                                <label class="form-check-label" for="statusReject">
                                    <span class="badge bg-danger p-2">Rejeitar</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Observações</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Motivo da rejeição ou observações adicionais..."></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Salvar Verificação
                        </button>
                        <a href="<?= site_url('admin/documents/pending') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.pdf-container {
    background: #f5f5f5;
    border-radius: 4px;
    overflow: hidden;
}

.image-container {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 4px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.image-container img {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>

<?= $this->endSection() ?>