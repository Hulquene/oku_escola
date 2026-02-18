<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <h1><?= $title ?></h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#typeModal">
        <i class="fas fa-plus me-2"></i> Novo Tipo
    </button>
</div>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Lista de Tipos -->
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
                        <th>Extensões</th>
                        <th>Tamanho Máx</th>
                        <th>Obrigatório</th>
                        <th>Disponível para</th>
                        <th>Ordem</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($types as $type): ?>
                        <tr>
                            <td><?= $type->id ?></td>
                            <td><code><?= $type->type_code ?></code></td>
                            <td class="fw-semibold"><?= $type->type_name ?></td>
                            <td>
                                <?php
                                $categoryLabels = [
                                    'identificacao' => 'Identificação',
                                    'academico' => 'Acadêmico',
                                    'pessoal' => 'Pessoal',
                                    'outro' => 'Outro'
                                ];
                                echo $categoryLabels[$type->type_category] ?? $type->type_category;
                                ?>
                            </td>
                            <td><?= strtoupper($type->allowed_extensions) ?></td>
                            <td><?= $type->max_size / 1024 ?> MB</td>
                            <td>
                                <?php if ($type->is_required): ?>
                                    <span class="badge bg-warning">Sim</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Não</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $available = [];
                                if ($type->for_student) $available[] = 'Alunos';
                                if ($type->for_teacher) $available[] = 'Professores';
                                echo implode(', ', $available) ?: '-';
                                ?>
                            </td>
                            <td><?= $type->sort_order ?></td>
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
                                            onclick="editType(<?= htmlspecialchars(json_encode($type)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="<?= site_url('admin/documents/types/delete/' . $type->id) ?>" 
                                       class="btn btn-outline-danger" 
                                       onclick="return confirm('Tem certeza que deseja excluir este tipo?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Tipo -->
<div class="modal fade" id="typeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= site_url('admin/documents/types/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="type_id">
                <input type="hidden" name="original_code" id="original_code">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Novo Tipo de Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="type_code" class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="type_code" name="type_code" required 
                                   placeholder="Ex: BI, DIPLOMA, CERTIDAO">
                        </div>
                        <div class="col-md-6">
                            <label for="type_name" class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="type_name" name="type_name" required 
                                   placeholder="Ex: Bilhete de Identidade">
                        </div>
                        <div class="col-md-6">
                            <label for="type_category" class="form-label fw-semibold">Categoria <span class="text-danger">*</span></label>
                            <select class="form-select" id="type_category" name="type_category" required>
                                <option value="">Selecione...</option>
                                <option value="identificacao">Identificação</option>
                                <option value="academico">Acadêmico</option>
                                <option value="pessoal">Pessoal</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="sort_order" class="form-label fw-semibold">Ordem de Exibição</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="0">
                        </div>
                        <div class="col-md-6">
                            <label for="allowed_extensions" class="form-label fw-semibold">Extensões Permitidas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="allowed_extensions" name="allowed_extensions" required 
                                   placeholder="jpg,jpeg,png,pdf">
                            <small class="text-muted">Separadas por vírgula</small>
                        </div>
                        <div class="col-md-6">
                            <label for="max_size" class="form-label fw-semibold">Tamanho Máximo (KB)</label>
                            <input type="number" class="form-control" id="max_size" name="max_size" value="5120">
                            <small class="text-muted">5MB = 5120 KB</small>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="mb-3">Configurações de Disponibilidade</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_required" name="is_required" value="1">
                                                <label class="form-check-label" for="is_required">
                                                    Documento Obrigatório
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="for_student" name="for_student" value="1" checked>
                                                <label class="form-check-label" for="for_student">
                                                    Disponível para Alunos
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="for_teacher" name="for_teacher" value="1" checked>
                                                <label class="form-check-label" for="for_teacher">
                                                    Disponível para Professores
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
function editType(type) {
    document.getElementById('modalTitle').textContent = 'Editar Tipo de Documento';
    document.getElementById('type_id').value = type.id;
    document.getElementById('original_code').value = type.type_code;
    document.getElementById('type_code').value = type.type_code;
    document.getElementById('type_name').value = type.type_name;
    document.getElementById('type_category').value = type.type_category;
    document.getElementById('sort_order').value = type.sort_order;
    document.getElementById('allowed_extensions').value = type.allowed_extensions;
    document.getElementById('max_size').value = type.max_size;
    document.getElementById('description').value = type.description || '';
    document.getElementById('is_required').checked = type.is_required == 1;
    document.getElementById('for_student').checked = type.for_student == 1;
    document.getElementById('for_teacher').checked = type.for_teacher == 1;
    document.getElementById('is_active').checked = type.is_active == 1;
    
    new bootstrap.Modal(document.getElementById('typeModal')).show();
}

// Reset modal quando abrir para novo
document.getElementById('typeModal').addEventListener('show.bs.modal', function (event) {
    if (!event.relatedTarget) {
        document.getElementById('modalTitle').textContent = 'Novo Tipo de Documento';
        document.getElementById('type_id').value = '';
        document.getElementById('original_code').value = '';
        document.getElementById('type_code').value = '';
        document.getElementById('type_name').value = '';
        document.getElementById('type_category').value = '';
        document.getElementById('sort_order').value = '0';
        document.getElementById('allowed_extensions').value = 'jpg,jpeg,png,pdf';
        document.getElementById('max_size').value = '5120';
        document.getElementById('description').value = '';
        document.getElementById('is_required').checked = false;
        document.getElementById('for_student').checked = true;
        document.getElementById('for_teacher').checked = true;
        document.getElementById('is_active').checked = true;
    }
});
</script>

<?= $this->endSection() ?>