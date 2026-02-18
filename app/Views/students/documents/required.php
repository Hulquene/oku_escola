<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header bg-gradient-primary py-3 mb-4 rounded-3">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="text-white mb-1"><?= $title ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb text-white-50 mb-0">
                        <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>" class="text-white">Início</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('students/documents') ?>" class="text-white">Documentos</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Documentos Obrigatórios</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Progresso -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">Progresso da Documentação</h5>
        <div class="progress mb-2" style="height: 25px;">
            <?php 
            $totalRequired = count($requiredDocs);
            $submittedCount = 0;
            foreach ($requiredDocs as $doc) {
                if ($doc->is_submitted) $submittedCount++;
            }
            $percentage = $totalRequired > 0 ? round(($submittedCount / $totalRequired) * 100) : 0;
            ?>
            <div class="progress-bar bg-success" role="progressbar" 
                 style="width: <?= $percentage ?>%;" 
                 aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100">
                <?= $percentage ?>%
            </div>
        </div>
        <p class="text-muted mb-0">
            <strong><?= $submittedCount ?></strong> de <strong><?= $totalRequired ?></strong> documentos obrigatórios enviados
        </p>
    </div>
</div>

<!-- Lista de Documentos Obrigatórios -->
<div class="row g-4">
    <?php foreach ($requiredDocs as $doc): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 <?= $doc->is_submitted ? 'border-success' : 'border-warning' ?>">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3 <?= $doc->is_submitted ? 'bg-success bg-opacity-10' : 'bg-warning bg-opacity-10' ?>">
                                <i class="fas fa-file-alt fa-2x <?= $doc->is_submitted ? 'text-success' : 'text-warning' ?>"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1"><?= $doc->type_name ?></h5>
                            <p class="text-muted mb-0">
                                <small>Formatos: <?= strtoupper(str_replace(',', ', ', $doc->allowed_extensions)) ?></small>
                            </p>
                        </div>
                        <div>
                            <?php if ($doc->is_submitted): ?>
                                <span class="badge bg-success p-2">
                                    <i class="fas fa-check me-1"></i> Enviado
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning p-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Pendente
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($doc->description): ?>
                        <p class="text-muted small mb-3"><?= $doc->description ?></p>
                    <?php endif; ?>
                    
                    <?php if (!$doc->is_submitted): ?>
                        <button type="button" class="btn btn-primary btn-sm" 
                                data-bs-toggle="modal" data-bs-target="#uploadModal<?= $doc->id ?>">
                            <i class="fas fa-upload me-1"></i> Enviar Documento
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Modal de Upload para cada documento -->
        <div class="modal fade" id="uploadModal<?= $doc->id ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="<?= site_url('students/documents/upload') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="document_type" value="<?= $doc->type_code ?>">
                        
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-upload text-primary me-2"></i>
                                Enviar <?= $doc->type_name ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="file<?= $doc->id ?>" class="form-label fw-semibold">Selecione o arquivo</label>
                                <input type="file" class="form-control" id="file<?= $doc->id ?>" name="document_file" 
                                       accept="<?= '.' . str_replace(',', ',.', $doc->allowed_extensions) ?>" required>
                                <small class="text-muted">
                                    Formatos permitidos: <?= strtoupper(str_replace(',', ', ', $doc->allowed_extensions)) ?><br>
                                    Tamanho máximo: <?= $doc->max_size / 1024 ?>MB
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description<?= $doc->id ?>" class="form-label fw-semibold">Descrição (opcional)</label>
                                <input type="text" class="form-control" id="description<?= $doc->id ?>" name="description"
                                       placeholder="Ex: Documento atualizado">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Alerta de conclusão -->
<?php if ($all_submitted): ?>
    <div class="alert alert-success text-center mt-4">
        <i class="fas fa-check-circle fa-2x mb-3"></i>
        <h5>Parabéns!</h5>
        <p>Todos os documentos obrigatórios foram enviados e estão aguardando verificação.</p>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>