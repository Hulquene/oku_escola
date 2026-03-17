<?= $this->extend('guardians/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-file-alt me-2" style="color: var(--accent);"></i>Meus Documentos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Documentos</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Lista de Documentos -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-list"></i>
            <span>Documentos Pessoais</span>
        </div>
        <span class="badge-ci primary">
            <i class="fas fa-file me-1"></i> <?= count($documents) ?> documento(s)
        </span>
    </div>
    
    <div class="ci-card-body p0">
        <?php if (!empty($documents)): ?>
            <div class="table-responsive">
                <table class="ci-table">
                    <thead>
                        <tr>
                            <th>Nome do Documento</th>
                            <th>Tipo</th>
                            <th>Data Upload</th>
                            <th class="center">Status</th>
                            <th class="center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $doc): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="file-icon me-2">
                                            <?php
                                            $ext = pathinfo($doc['file_name'], PATHINFO_EXTENSION);
                                            $icon = match(strtolower($ext)) {
                                                'pdf' => 'fa-file-pdf text-danger',
                                                'doc', 'docx' => 'fa-file-word text-primary',
                                                'xls', 'xlsx' => 'fa-file-excel text-success',
                                                'jpg', 'jpeg', 'png' => 'fa-file-image text-info',
                                                default => 'fa-file-alt text-secondary'
                                            };
                                            ?>
                                            <i class="fas <?= $icon ?> fa-lg"></i>
                                        </div>
                                        <div>
                                            <strong><?= $doc['document_name'] ?></strong>
                                            <br>
                                            <small class="text-muted"><?= $doc['file_name'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= strtoupper($ext) ?></td>
                                <td><?= date('d/m/Y', strtotime($doc['created_at'])) ?></td>
                                <td class="center">
                                    <?php if ($doc['is_verified']): ?>
                                        <span class="badge-ci success">
                                            <i class="fas fa-check-circle me-1"></i> Verificado
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-ci warning">
                                            <i class="fas fa-clock me-1"></i> Pendente
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="center">
                                    <div class="action-group">
                                        <a href="<?= site_url('guardians/documents/view/' . $doc['id']) ?>" 
                                           class="row-btn view" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('guardians/documents/download/' . $doc['id']) ?>" 
                                           class="row-btn success" title="Download">
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
                <i class="fas fa-file-alt"></i>
                <h5>Nenhum documento encontrado</h5>
                <p class="text-muted">Você ainda não possui documentos registados.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>