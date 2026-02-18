<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <h1><?= $title ?></h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestableModal">
        <i class="fas fa-plus me-2"></i> Novo Documento Solicitável
    </button>
</div>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Lista de Documentos Solicitáveis -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Taxa</th>
                        <th>Prazo (dias)</th>
                        <th>Disponível para</th>
                        <th>Aprovação</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($types as $type): ?>
                        <tr>
                            <td><?= $type->id ?></td>
                            <td><code><?= $type->document_code ?></code></td>
                            <td class="fw-semibold"><?= $type->document_name ?></td>
                            <td>
                                <?= $categories[$type->category] ?? $type->category ?>
                            </td>
                            <td><?= number_format($type->fee_amount, 2, ',', '.') ?> Kz</td>
                            <td><?= $type->processing_days ?></td>
                            <td>
                                <?php
                                $availableLabels = [
                                    'all' => 'Todos',
                                    'students' => 'Alunos',
                                    'teachers' => 'Professores',
                                    'guardians' => 'Encarregados',
                                    'staff' => 'Funcionários'
                                ];
                                echo $availableLabels[$type->available_for] ?? $type->available_for;
                                ?>
                            </td>
                            <td>
                                <?php if ($type->requires_approval): ?>
                                    <span class="badge bg-warning">Sim</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Não</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($type->is_active): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" 
                                            onclick="editRequestableType(<?= htmlspecialchars(json_encode($type)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="<?= site_url('admin/documents/requestable/delete/' . $type->id) ?>" 
                                       class="btn btn-outline-danger" 
                                       onclick="return confirm('Tem certeza que deseja excluir este tipo?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php if ($type->description): ?>
                        <tr class="table-light">
                            <td colspan="10" class="py-1">
                                <small><i class="fas fa-comment me-1 text-muted"></i> <?= $type->description ?></small>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Documento Solicitável -->
<div class="modal fade" id="requestableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= site_url('admin/documents/requestable/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="type_id">
                <input type="hidden" name="original_code" id="original_code">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Novo Documento Solicitável</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="document_code" class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="document_code" name="document_code" required 
                                   placeholder="Ex: CERT_MATRICULA">
                        </div>
                        <div class="col-md-6">
                            <label for="document_name" class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="document_name" name="document_name" required 
                                   placeholder="Ex: Certificado de Matrícula">
                        </div>
                        <div class="col-md-6">
                            <label for="category" class="form-label fw-semibold">Categoria <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($categories as $key => $cat): ?>
                                    <option value="<?= $key ?>"><?= $cat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="available_for" class="form-label fw-semibold">Disponível para <span class="text-danger">*</span></label>
                            <select class="form-select" id="available_for" name="available_for" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($availableFor as $key => $label): ?>
                                    <option value="<?= $key ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="fee_amount" class="form-label fw-semibold">Taxa (Kz)</label>
                            <input type="number" class="form-control" id="fee_amount" name="fee_amount" value="0" min="0">
                        </div>
                        <div class="col-md-4">
                            <label for="processing_days" class="form-label fw-semibold">Prazo (dias)</label>
                            <input type="number" class="form-control" id="processing_days" name="processing_days" value="3" min="1" max="30">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">&nbsp;</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="requires_approval" name="requires_approval" value="1">
                                <label class="form-check-label" for="requires_approval">
                                    Requer aprovação
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    Ativo
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editRequestableType(type) {
    document.getElementById('modalTitle').textContent = 'Editar Documento Solicitável';
    document.getElementById('type_id').value = type.id;
    document.getElementById('original_code').value = type.document_code;
    document.getElementById('document_code').value = type.document_code;
    document.getElementById('document_name').value = type.document_name;
    document.getElementById('category').value = type.category;
    document.getElementById('available_for').value = type.available_for;
    document.getElementById('fee_amount').value = type.fee_amount;
    document.getElementById('processing_days').value = type.processing_days;
    document.getElementById('requires_approval').checked = type.requires_approval == 1;
    document.getElementById('description').value = type.description || '';
    document.getElementById('is_active').checked = type.is_active == 1;
    
    new bootstrap.Modal(document.getElementById('requestableModal')).show();
}

// Reset modal quando abrir para novo
document.getElementById('requestableModal').addEventListener('show.bs.modal', function (event) {
    if (!event.relatedTarget) {
        document.getElementById('modalTitle').textContent = 'Novo Documento Solicitável';
        document.getElementById('type_id').value = '';
        document.getElementById('original_code').value = '';
        document.getElementById('document_code').value = '';
        document.getElementById('document_name').value = '';
        document.getElementById('category').value = '';
        document.getElementById('available_for').value = '';
        document.getElementById('fee_amount').value = '0';
        document.getElementById('processing_days').value = '3';
        document.getElementById('requires_approval').checked = false;
        document.getElementById('description').value = '';
        document.getElementById('is_active').checked = true;
    }
});
</script>

<?= $this->endSection() ?>