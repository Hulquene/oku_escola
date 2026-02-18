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
                        <li class="breadcrumb-item"><a href="<?= site_url('teachers/documents') ?>" class="text-white">Documentos</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Arquivo</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="year" class="form-label fw-semibold">Ano</label>
                <select class="form-select" id="year" name="year">
                    <option value="">Todos</option>
                    <?php 
                    $currentYear = date('Y');
                    for ($y = $currentYear; $y >= $currentYear - 5; $y--): 
                    ?>
                        <option value="<?= $y ?>" <?= ($_GET['year'] ?? '') == $y ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="type" class="form-label fw-semibold">Tipo de Documento</label>
                <select class="form-select" id="type" name="type">
                    <option value="">Todos</option>
                    <?php foreach ($documentTypes as $type): ?>
                        <option value="<?= $type->type_code ?>" <?= ($_GET['type'] ?? '') == $type->type_code ? 'selected' : '' ?>>
                            <?= $type->type_name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-search me-2"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Documentos do Arquivo -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0">
            <i class="fas fa-archive text-success me-2"></i>
            Documentos Arquivados
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($archive)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Nome do Arquivo</th>
                            <th>Data Envio</th>
                            <th>Data Arquivamento</th>
                            <th>Tamanho</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($archive as $doc): ?>
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
                                </td>
                                <td>
                                    <i class="fas fa-file-<?= strpos($doc->document_mime, 'pdf') !== false ? 'pdf' : 'image' ?> text-muted me-1"></i>
                                    <?= $doc->document_name ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($doc->created_at)) ?></td>
                                <td><?= date('d/m/Y', strtotime('+1 year', strtotime($doc->created_at))) ?></td>
                                <td><?= round($doc->document_size / 1024, 1) ?> KB</td>
                                <td>
                                    <?php if ($doc->is_verified == 1): ?>
                                        <span class="badge bg-success">Verificado</span>
                                    <?php elseif ($doc->is_verified == 2): ?>
                                        <span class="badge bg-danger">Rejeitado</span>
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
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center py-4">
                <i class="fas fa-archive fa-3x mb-3"></i>
                <h5>Nenhum documento no arquivo</h5>
                <p>Documentos com mais de 1 ano são movidos automaticamente para o arquivo.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>