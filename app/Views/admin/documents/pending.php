<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/documents') ?>">Central de Documentos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pendentes</li>
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
                    <?php foreach ($documentTypes ?? [] as $type): ?>
                        <option value="<?= $type->type_code ?>" <?= ($_GET['document_type'] ?? '') == $type->type_code ? 'selected' : '' ?>>
                            <?= $type->type_name ?>
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

<!-- Lista de Documentos Pendentes -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-clock text-warning me-2"></i>
            Documentos Aguardando Verificação
        </h5>
        <span class="badge bg-warning"><?= count($documents) ?> pendente(s)</span>
    </div>
    <div class="card-body">
        <?php if (!empty($documents)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Usuário</th>
                            <th>Tipo</th>
                            <th>Documento</th>
                            <th>Data Envio</th>
                            <th>Tamanho</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $doc): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-<?= $doc->user_type == 'student' ? 'primary' : 'success' ?> rounded-circle me-2">
                                            <?= strtoupper(substr($doc->user_type, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <span class="fw-semibold d-block"><?= $doc->user_name ?? 'N/A' ?></span>
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
                                    <i class="fas fa-file-<?= strpos($doc->document_mime, 'pdf') !== false ? 'pdf' : 'image' ?> text-muted me-1"></i>
                                    <?= $doc->document_name ?>
                                    <?php if ($doc->description): ?>
                                        <br><small class="text-muted"><?= $doc->description ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= date('d/m/Y H:i', strtotime($doc->created_at)) ?>
                                </td>
                                <td><?= round($doc->document_size / 1024, 1) ?> KB</td>
                                <td>
                                    <a href="<?= site_url('admin/documents/verify/' . $doc->id) ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-check-circle me-1"></i> Verificar
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                                            onclick="window.open('<?= site_url('admin/documents/view/' . $doc->id) ?>', '_blank')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-success text-center py-4">
                <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                <h5>Nenhum documento pendente</h5>
                <p>Todos os documentos foram verificados.</p>
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