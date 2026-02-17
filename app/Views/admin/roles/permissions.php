<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?>: <?= $role->role_name ?></h1>
        <a href="<?= site_url('admin/roles') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/roles') ?>">Perfis</a></li>
            <li class="breadcrumb-item active" aria-current="page">Permissões</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Info Card -->
<div class="alert alert-info mb-4">
    <i class="fas fa-info-circle"></i>
    <strong>Perfil:</strong> <?= $role->role_name ?> - 
    <strong>Descrição:</strong> <?= $role->role_description ?: 'Sem descrição' ?>
</div>

<!-- Permissions Form -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-key"></i> Gerenciar Permissões
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/roles/update-permissions') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="role_id" value="<?= $role->id ?>">
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <button type="button" class="btn btn-sm btn-outline-success me-2" onclick="selectAll()">
                        <i class="fas fa-check-double"></i> Selecionar Todos
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="deselectAll()">
                        <i class="fas fa-times"></i> Desmarcar Todos
                    </button>
                </div>
            </div>
            
            <?php
            $currentModule = '';
            $permissionsByModule = [];
            foreach ($permissions as $perm) {
                $permissionsByModule[$perm->module][] = $perm;
            }
            ?>
            
            <div class="row">
                <?php foreach ($permissionsByModule as $module => $perms): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-cog"></i> 
                                    <?= ucfirst($module) ?>
                                    <small>
                                        <a href="#" class="float-end" onclick="toggleModule('<?= $module ?>'); return false;">
                                            (selecionar)
                                        </a>
                                    </small>
                                </h6>
                            </div>
                            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                <?php foreach ($perms as $perm): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input module-<?= $module ?>" 
                                               type="checkbox" 
                                               name="permissions[]" 
                                               value="<?= $perm->id ?>"
                                               id="perm_<?= $perm->id ?>"
                                               <?= in_array($perm->id, $rolePermissions) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="perm_<?= $perm->id ?>">
                                            <?= $perm->permission_name ?>
                                            <br>
                                            <small class="text-muted"><?= $perm->permission_key ?></small>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/roles') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Permissões
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function selectAll() {
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAll() {
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function toggleModule(module) {
    const checkboxes = document.querySelectorAll('.module-' + module);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });
}

// Keyboard shortcut: Ctrl+A to select all
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'a') {
        e.preventDefault();
        selectAll();
    }
});
</script>
<?= $this->endSection() ?>