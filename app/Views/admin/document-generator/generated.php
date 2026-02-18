<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/document-generator') ?>">Gerador de Documentos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Documentos Gerados</li>
        </ol>
    </nav>
</div>

<!-- Filtros -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <label for="user_type" class="form-label">Tipo de Usuário</label>
                <select class="form-select" id="user_type" name="user_type">
                    <option value="">Todos</option>
                    <option value="student" <?= ($_GET['user_type'] ?? '') == 'student' ? 'selected' : '' ?>>Alunos</option>
                    <option value="teacher" <?= ($_GET['user_type'] ?? '') == 'teacher' ? 'selected' : '' ?>>Professores</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="document_type" class="form-label">Tipo de Documento</label>
                <select class="form-select" id="document_type" name="document_type">
                    <option value="">Todos</option>
                    <?php 
                    $docTypes = [
                        'CERT_MATRICULA' => 'Certificado de Matrícula',
                        'DECL_FREQUENCIA' => 'Declaração de Frequência',
                        'HISTORICO_NOTAS' => 'Histórico de Notas',
                        'CERT_CONCLUSAO' => 'Certificado de Conclusão',
                        'DECL_APROVEITAMENTO' => 'Declaração de Aproveitamento',
                        'ATESTADO_MATRICULA' => 'Atestado de Matrícula',
                        'DECL_SERVICO' => 'Declaração de Serviço',
                        'CERT_TRABALHO' => 'Certificado de Trabalho',
                        'DECL_VENCIMENTO' => 'Declaração de Vencimento'
                    ];
                    foreach ($docTypes as $code => $name): 
                    ?>
                        <option value="<?= $code ?>" <?= ($_GET['document_type'] ?? '') == $code ? 'selected' : '' ?>>
                            <?= $name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">Data Inicial</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="<?= $_GET['date_from'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">Data Final</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="<?= $_GET['date_to'] ?? '' ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Documentos Gerados -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">
            <i class="fas fa-file-pdf text-success me-2"></i>
            Documentos Gerados (<?= count($documents) ?>)
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($documents)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nº Solicitação</th>
                            <th>Solicitante</th>
                            <th>Documento</th>
                            <th>Arquivo</th>
                            <th>Data Geração</th>
                            <th>Downloads</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $doc): ?>
                            <tr>
                                <td><code><?= $doc->request_number ?></code></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-<?= $doc->user_type == 'student' ? 'primary' : 'success' ?> rounded-circle me-2">
                                            <?= strtoupper(substr($doc->user_type, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <span class="fw-semibold d-block"><?= $doc->user_name ?></span>
                                            <small class="text-muted">
                                                <?= $doc->user_type == 'student' ? 'Aluno' : 'Professor' ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold"><?= $doc->document_type ?></span>
                                </td>
                                <td>
                                    <i class="fas fa-file-pdf text-danger me-1"></i>
                                    <?= $doc->document_name ?>
                                    <br>
                                    <small class="text-muted"><?= round($doc->document_size / 1024, 1) ?> KB</small>
                                </td>
                                <td>
                                    <?= date('d/m/Y H:i', strtotime($doc->generated_at)) ?>
                                    <br>
                                    <small class="text-muted">Por: <?= $doc->generated_by_name ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?= $doc->download_count ?></span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('admin/document-generator/download/' . $doc->id) ?>" 
                                           class="btn btn-outline-success" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-info" 
                                                onclick="window.open('<?= site_url('admin/document-generator/download/' . $doc->id) ?>', '_blank')"
                                                title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center py-4">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h5>Nenhum documento gerado</h5>
                <p>Os documentos gerados aparecerão aqui.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.avatar {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
}
</style>

<?= $this->endSection() ?>