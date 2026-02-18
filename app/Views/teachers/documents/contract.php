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
                        <li class="breadcrumb-item active text-white" aria-current="page">Documentos Contratuais</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Status do Contrato -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-2">Situação Contratual</h5>
                <?php if (empty($missing_docs)): ?>
                    <div class="alert alert-success mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        Todos os documentos contratuais foram enviados e estão em análise.
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Documentos pendentes:</strong> <?= implode(', ', $missing_docs) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?= site_url('teachers/documents') ?>" class="btn btn-success">
                    <i class="fas fa-upload me-2"></i> Enviar Documentos
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Checklist de Documentos Contratuais -->
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-contract text-success me-2"></i>
                    Documentos Necessários para Contrato
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <?php
                    $contractDocs = [
                        'BI' => 'Bilhete de Identidade',
                        'DIPLOMA' => 'Diploma/Certificado',
                        'CERTIFICADO_HABILITACOES' => 'Certificado de Habilitações',
                        'CURRICULO' => 'Currículo Vitae'
                    ];
                    
                    $submittedTypes = [];
                    $verifiedTypes = [];
                    foreach ($contractDocs as $code => $name) {
                        $found = false;
                        $verified = false;
                        foreach ($contractDocs ?? [] as $doc) {
                            if ($doc->document_type == $code) {
                                $found = true;
                                $verified = $doc->is_verified == 1;
                                break;
                            }
                        }
                        $submittedTypes[$code] = $found;
                        $verifiedTypes[$code] = $verified;
                    }
                    
                    foreach ($contractDocs as $code => $name):
                        $isSubmitted = $submittedTypes[$code] ?? false;
                        $isVerified = $verifiedTypes[$code] ?? false;
                    ?>
                        <div class="list-group-item list-group-item-action d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <?php if ($isVerified): ?>
                                    <span class="badge bg-success p-2"><i class="fas fa-check"></i></span>
                                <?php elseif ($isSubmitted): ?>
                                    <span class="badge bg-warning p-2"><i class="fas fa-clock"></i></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary p-2"><i class="fas fa-times"></i></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0"><?= $name ?></h6>
                                <?php 
                                $docInfo = null;
                                foreach ($contractDocs ?? [] as $doc) {
                                    if ($doc->document_type == $code) {
                                        $docInfo = $doc;
                                        break;
                                    }
                                }
                                ?>
                                <?php if ($docInfo): ?>
                                    <small class="text-muted">
                                        Enviado em: <?= date('d/m/Y', strtotime($docInfo->created_at)) ?>
                                        <?php if ($docInfo->is_verified == 1): ?>
                                            - <span class="text-success">Verificado</span>
                                        <?php elseif ($docInfo->is_verified == 2): ?>
                                            - <span class="text-danger">Rejeitado: <?= $docInfo->verification_notes ?></span>
                                        <?php else: ?>
                                            - <span class="text-warning">Aguardando verificação</span>
                                        <?php endif; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Informações do Contrato -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle text-success me-2"></i>
                    Sobre o Contrato
                </h5>
            </div>
            <div class="card-body">
                <p class="small mb-2">
                    <i class="fas fa-check-circle text-success me-1"></i>
                    Os documentos enviados serão verificados pela secretaria
                </p>
                <p class="small mb-2">
                    <i class="fas fa-clock text-warning me-1"></i>
                    Prazo de verificação: até 5 dias úteis
                </p>
                <p class="small mb-0">
                    <i class="fas fa-file-pdf text-danger me-1"></i>
                    Formatos aceitos: PDF, JPG, PNG
                </p>
            </div>
        </div>
        
        <!-- Ações -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tasks text-success me-2"></i>
                    Ações
                </h5>
            </div>
            <div class="card-body">
                <a href="<?= site_url('teachers/documents') ?>" class="btn btn-outline-success w-100 mb-2">
                    <i class="fas fa-upload me-2"></i> Enviar Documentos
                </a>
                <a href="<?= site_url('teachers/documents/requests') ?>" class="btn btn-outline-primary w-100">
                    <i class="fas fa-file-signature me-2"></i> Solicitar Documentos
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>