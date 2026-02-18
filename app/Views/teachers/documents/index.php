<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header bg-gradient-success py-3 mb-4 rounded-3">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="text-white mb-1"><?= $title ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb text-white-50 mb-0">
                        <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>" class="text-white">Início</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Meus Documentos</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <span class="badge bg-white text-success p-2">
                    <i class="fas fa-calendar-alt me-1"></i> <?= date('d/m/Y') ?>
                </span>
            </div>
        </div>
    </div>
</div>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Estatísticas Rápidas -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-alt fa-3x opacity-50"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white-50 mb-1">Total Documentos</h6>
                        <h3 class="mb-0"><?= $stats->total ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock fa-3x opacity-50"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white-50 mb-1">Pendentes</h6>
                        <h3 class="mb-0"><?= $stats->pending ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white-50 mb-1">Verificados</h6>
                        <h3 class="mb-0"><?= $stats->verified ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-times-circle fa-3x opacity-50"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white-50 mb-1">Rejeitados</h6>
                        <h3 class="mb-0"><?= $stats->rejected ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload de Novo Documento -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0">
            <i class="fas fa-upload text-success me-2"></i>
            Enviar Novo Documento
        </h5>
    </div>
    <div class="card-body">
        <form action="<?= site_url('teachers/documents/upload') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="document_type" class="form-label fw-semibold">Tipo de Documento <span class="text-danger">*</span></label>
                    <select class="form-select" id="document_type" name="document_type" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($documentTypes as $type): ?>
                            <option value="<?= $type->type_code ?>" 
                                    data-extensions="<?= $type->allowed_extensions ?>"
                                    data-maxsize="<?= $type->max_size ?>">
                                <?= $type->type_name ?>
                                <?php if ($type->is_required): ?> 
                                    <span class="text-danger">*</span>
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="document_file" class="form-label fw-semibold">Arquivo <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="document_file" name="document_file" required>
                    <small class="text-muted" id="file-hint">Formatos: JPG, PNG, PDF. Máx: 5MB</small>
                </div>
                <div class="col-md-4">
                    <label for="description" class="form-label fw-semibold">Descrição (opcional)</label>
                    <input type="text" class="form-control" id="description" name="description" 
                           placeholder="Ex: Diploma atualizado">
                </div>
            </div>
            
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-upload me-1"></i> Enviar Documento
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Documentos -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-list text-success me-2"></i>
            Meus Documentos
        </h5>
        <div>
            <a href="<?= site_url('teachers/documents/contract') ?>" class="btn btn-sm btn-outline-primary me-2">
                <i class="fas fa-file-contract"></i> Contratuais
            </a>
            <a href="<?= site_url('teachers/documents/archive') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-archive"></i> Arquivo
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($documents)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Nome do Arquivo</th>
                            <th>Data Envio</th>
                            <th>Tamanho</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $doc): ?>
                            <?php 
                            $typeInfo = null;
                            foreach ($documentTypes as $type) {
                                if ($type->type_code == $doc->document_type) {
                                    $typeInfo = $type;
                                    break;
                                }
                            }
                            ?>
                            <tr>
                                <td>
                                    <span class="fw-semibold"><?= $typeInfo->type_name ?? $doc->document_type ?></span>
                                    <?php if ($typeInfo && $typeInfo->is_required): ?>
                                        <span class="badge bg-warning text-dark ms-1" title="Obrigatório">*</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <i class="fas fa-file-<?= strpos($doc->document_mime, 'pdf') !== false ? 'pdf' : 'image' ?> text-muted me-1"></i>
                                    <?= $doc->document_name ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($doc->created_at)) ?></td>
                                <td><?= round($doc->document_size / 1024, 1) ?> KB</td>
                                <td>
                                    <?php if ($doc->is_verified == 0): ?>
                                        <span class="badge bg-warning">Pendente</span>
                                    <?php elseif ($doc->is_verified == 1): ?>
                                        <span class="badge bg-success">Verificado</span>
                                        <?php if ($doc->verified_at): ?>
                                            <br><small class="text-muted"><?= date('d/m/Y', strtotime($doc->verified_at)) ?></small>
                                        <?php endif; ?>
                                    <?php elseif ($doc->is_verified == 2): ?>
                                        <span class="badge bg-danger">Rejeitado</span>
                                        <?php if ($doc->verification_notes): ?>
                                            <br><small class="text-danger"><?= $doc->verification_notes ?></small>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('teachers/documents/view/' . $doc->id) ?>" 
                                           class="btn btn-outline-info" target="_blank" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('teachers/documents/download/' . $doc->id) ?>" 
                                           class="btn btn-outline-success" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <?php if ($doc->is_verified == 0): ?>
                                            <a href="<?= site_url('teachers/documents/delete/' . $doc->id) ?>" 
                                               class="btn btn-outline-danger" 
                                               onclick="return confirm('Tem certeza que deseja remover este documento?')"
                                               title="Remover">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php if ($doc->description): ?>
                            <tr class="table-light">
                                <td colspan="6" class="py-1">
                                    <small><i class="fas fa-comment me-1 text-muted"></i> <?= $doc->description ?></small>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center py-4">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h5>Nenhum documento enviado</h5>
                <p>Utilize o formulário acima para enviar seus documentos.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('document_type').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const extensions = selected.dataset.extensions || 'jpg,jpeg,png,pdf';
    const maxSize = selected.dataset.maxsize || 5120;
    
    document.getElementById('file-hint').textContent = 
        `Formatos: ${extensions.toUpperCase().replace(/,/g, ', ')}. Máx: ${maxSize/1024}MB`;
});
</script>

<?= $this->endSection() ?>