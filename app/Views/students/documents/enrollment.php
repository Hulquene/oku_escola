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
                        <li class="breadcrumb-item active text-white" aria-current="page">Documentos para Matrícula</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<?php if ($enrollment): ?>
    <!-- Status da Matrícula -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-2">Matrícula Atual</h5>
                    <p class="mb-1">
                        <strong>Turma:</strong> <?= $enrollment->class_name ?><br>
                        <strong>Ano Letivo:</strong> <?= $enrollment->year_name ?><br>
                        <strong>Nº Matrícula:</strong> <?= $enrollment->enrollment_number ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-success p-3">
                        <i class="fas fa-check-circle me-1"></i> Matrícula Ativa
                    </span>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Processo de Matrícula -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        Para efetuar a matrícula, é necessário enviar todos os documentos abaixo.
    </div>
<?php endif; ?>

<!-- Checklist de Documentos -->
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-check-double text-primary me-2"></i>
                    Documentos Necessários para Matrícula
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <?php
                    $requiredDocs = [
                        'BI' => 'Bilhete de Identidade',
                        'CERTIDAO_NASCIMENTO' => 'Certidão de Nascimento',
                        'FOTO' => 'Fotografia (3x4)',
                        'CERTIFICADO_CLASSES' => 'Certificado de Classes Anteriores'
                    ];
                    
                    $submittedTypes = [];
                    foreach ($enrollmentDocs as $doc) {
                        $submittedTypes[] = $doc->document_type;
                    }
                    
                    foreach ($requiredDocs as $code => $name):
                        $isSubmitted = in_array($code, $submittedTypes);
                        $isVerified = false;
                        $docInfo = null;
                        
                        if ($isSubmitted) {
                            foreach ($enrollmentDocs as $doc) {
                                if ($doc->document_type == $code) {
                                    $isVerified = $doc->is_verified == 1;
                                    $docInfo = $doc;
                                    break;
                                }
                            }
                        }
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
                            <div class="flex-shrink-0">
                                <?php if (!$isVerified): ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" data-bs-target="#uploadModal<?= $code ?>">
                                        <i class="fas fa-upload"></i> Enviar
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Modal de Upload -->
                        <div class="modal fade" id="uploadModal<?= $code ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="<?= site_url('students/documents/upload') ?>" method="post" enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="document_type" value="<?= $code ?>">
                                        
                                        <div class="modal-header">
                                            <h5 class="modal-title">Enviar <?= $name ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Arquivo</label>
                                                <input type="file" class="form-control" name="document_file" required
                                                       accept=".jpg,.jpeg,.png,.pdf">
                                                <small class="text-muted">Formatos: JPG, PNG, PDF. Máx: 5MB</small>
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
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Status do Processo -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie text-primary me-2"></i>
                    Status da Matrícula
                </h5>
            </div>
            <div class="card-body">
                <?php
                $totalDocs = count($requiredDocs);
                $submittedDocs = count($enrollmentDocs);
                $verifiedDocs = 0;
                foreach ($enrollmentDocs as $doc) {
                    if ($doc->is_verified == 1) $verifiedDocs++;
                }
                ?>
                
                <div class="text-center mb-4">
                    <div class="display-4 fw-bold text-primary"><?= $verifiedDocs ?>/<?= $totalDocs ?></div>
                    <p class="text-muted">Documentos Verificados</p>
                </div>
                
                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: <?= ($verifiedDocs / $totalDocs) * 100 ?>%"></div>
                </div>
                
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <?= $verifiedDocs ?> documentos verificados
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-clock text-warning me-2"></i>
                        <?= $submittedDocs - $verifiedDocs ?> aguardando verificação
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-times-circle text-danger me-2"></i>
                        <?= $totalDocs - $submittedDocs ?> pendentes
                    </li>
                </ul>
                
                <?php if ($verifiedDocs == $totalDocs && !$enrollment): ?>
                    <a href="<?= site_url('students/enrollments/form-add') ?>" class="btn btn-success w-100 mt-3">
                        <i class="fas fa-graduation-cap me-2"></i> Iniciar Matrícula
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Informações Adicionais -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Informações
                </h5>
            </div>
            <div class="card-body">
                <p class="small mb-2">
                    <i class="fas fa-check-circle text-success me-1"></i>
                    Documentos verificados pela secretaria
                </p>
                <p class="small mb-2">
                    <i class="fas fa-clock text-warning me-1"></i>
                    Aguardando verificação (prazo: 2 dias úteis)
                </p>
                <p class="small mb-0">
                    <i class="fas fa-times-circle text-danger me-1"></i>
                    Documento rejeitado - é necessário enviar novamente
                </p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>