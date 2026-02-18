<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/documents') ?>">Central de Documentos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Documentos Verificados</li>
        </ol>
    </nav>
</div>

<!-- Filtros -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-2">
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
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="1" <?= ($_GET['status'] ?? '') == '1' ? 'selected' : '' ?>>Verificado</option>
                    <option value="2" <?= ($_GET['status'] ?? '') == '2' ? 'selected' : '' ?>>Rejeitado</option>
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
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Documentos Verificados -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-check-circle text-success me-2"></i>
            Documentos Verificados
        </h5>
        <span class="badge bg-success"><?= count($documents) ?> documento(s)</span>
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
                            <th>Verificado em</th>
                            <th>Verificado por</th>
                            <th>Status</th>
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
                                            <span class="fw-semibold d-block"><?= $doc->user_fullname ?? 'N/A' ?></span>
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
                                </td>
                                <td><?= date('d/m/Y', strtotime($doc->created_at)) ?></td>
                                <td><?= $doc->verified_at ? date('d/m/Y', strtotime($doc->verified_at)) : '-' ?></td>
                                <td><?= $doc->verified_by_name ?? '-' ?></td>
                                <td>
                                    <?php if ($doc->is_verified == 1): ?>
                                        <span class="badge bg-success">Verificado</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Rejeitado</span>
                                        <?php if ($doc->verification_notes): ?>
                                            <br><small class="text-danger"><?= $doc->verification_notes ?></small>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info" 
                                                onclick="window.open('<?= site_url('admin/documents/view/' . $doc->id) ?>', '_blank')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="<?= site_url('admin/documents/download/' . $doc->id) ?>" 
                                           class="btn btn-outline-success">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php if ($doc->verification_notes && $doc->is_verified == 2): ?>
                            <tr class="table-danger">
                                <td colspan="8" class="py-1">
                                    <small><i class="fas fa-exclamation-triangle me-1"></i> <?= $doc->verification_notes ?></small>
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
                <h5>Nenhum documento encontrado</h5>
                <p>Não há documentos verificados com os filtros selecionados.</p>
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