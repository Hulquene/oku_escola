<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- DEBUG: Mostrar o conteúdo de $role -->
<?php if (isset($role)): ?>
   <!--  <div class="alert alert-info">
        <strong>DEBUG:</strong> Role encontrado: 
        ID: <?= $role->id ?? 'não definido' ?>, 
        Nome: <?= $role->role_name ?? 'não definido' ?>, 
        Descrição: <?= $role->role_description ?? 'não definido' ?>
    </div> -->
<?php else: ?>
  <!--   <div class="alert alert-warning">
        <strong>DEBUG:</strong> Role não definido (modo criação)
    </div> -->
<?php endif; ?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">
                <?= isset($role) && $role ? 'Edite as informações do perfil de acesso' : 'Preencha os dados para criar um novo perfil' ?>
            </p>
        </div>
        <div>
            <a href="<?= site_url('admin/roles') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/roles') ?>">Perfis</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= isset($role) && $role ? 'Editar' : 'Novo' ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas <?= isset($role) && $role ? 'fa-edit' : 'fa-plus-circle' ?> me-2 text-primary"></i>
                    <?= isset($role) && $role ? 'Editar Perfil' : 'Novo Perfil' ?>
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= site_url('admin/roles/save' . (isset($role) && $role ? '/' . $role->id : '')) ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="role_name" class="form-label">Nome do Perfil <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= session('errors.role_name') ? 'is-invalid' : '' ?>" 
                               id="role_name" 
                               name="role_name" 
                               value="<?= old('role_name', (isset($role) && $role ? $role->role_name : '')) ?>"
                               placeholder="Ex: Administrador, Professor, Secretário..."
                               required>
                        <?php if (session('errors.role_name')): ?>
                            <div class="invalid-feedback"><?= session('errors.role_name') ?></div>
                        <?php endif; ?>
                        <small class="text-muted">O nome deve ser único e descritivo</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role_description" class="form-label">Descrição</label>
                        <textarea class="form-control <?= session('errors.role_description') ? 'is-invalid' : '' ?>" 
                                  id="role_description" 
                                  name="role_description" 
                                  rows="4"
                                  placeholder="Descreva as responsabilidades e acesso deste perfil..."><?= old('role_description', (isset($role) && $role ? $role->role_description : '')) ?></textarea>
                        <?php if (session('errors.role_description')): ?>
                            <div class="invalid-feedback"><?= session('errors.role_description') ?></div>
                        <?php endif; ?>
                        <small class="text-muted">Uma breve descrição das responsabilidades do perfil</small>
                    </div>
                    
                    <?php if (isset($role) && $role && isset($role->created_at)): ?>
                    <div class="mb-3">
                        <label class="form-label">Informações do Sistema</label>
                        <div class="bg-light p-3 rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Criado em:</small>
                                    <strong><?= date('d/m/Y H:i', strtotime($role->created_at)) ?></strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Última atualização:</small>
                                    <strong><?= isset($role->updated_at) ? date('d/m/Y H:i', strtotime($role->updated_at)) : 'Nunca' ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= site_url('admin/roles') ?>" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> <?= isset($role) && $role ? 'Salvar Alterações' : 'Criar Perfil' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Dicas e Informações -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2 text-warning"></i>
                    Dicas
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <strong>Nomes descritivos</strong> ajudam a identificar facilmente o perfil
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        A <strong>descrição</strong> deve esclarecer as responsabilidades
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Após criar, configure as <strong>permissões</strong> no botão abaixo
                    </li>
                    <li>
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Perfis com usuários associados não podem ser eliminados
                    </li>
                </ul>
            </div>
        </div>
        
        <?php if (isset($role) && $role): ?>
        <!-- Ações Rápidas (apenas em edição) -->
        <div class="card mt-3">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2 text-info"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= site_url('admin/roles/permission/' . $role->id) ?>" class="btn btn-outline-primary">
                        <i class="fas fa-key me-2"></i> Gerir Permissões
                    </a>
                    
                    <?php if ($role->id != 1 && has_permission('roles.delete')): ?>
                        <button type="button" 
                                class="btn btn-outline-danger" 
                                onclick="confirmDelete(<?= $role->id ?>, '<?= $role->role_name ?>')">
                            <i class="fas fa-trash me-2"></i> Eliminar Perfil
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Confirmação de Eliminação (apenas se estiver editando) -->
<?php if (isset($role) && $role && $role->id != 1 && has_permission('roles.delete')): ?>
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar o perfil <strong id="deleteRoleName"></strong>?</p>
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita. Utilizadores com este perfil serão afetados.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="<?= site_url('admin/roles/delete/' . $role->id) ?>" method="post" id="deleteForm" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteRoleName').textContent = name;
    document.getElementById('deleteForm').action = '<?= site_url('admin/roles/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
<?php endif; ?>

<?= $this->endSection() ?>