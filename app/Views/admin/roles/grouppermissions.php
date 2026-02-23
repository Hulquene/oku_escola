<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/settings') ?>">Configurações</a></li>
            <li class="breadcrumb-item active" aria-current="page">Permissões</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Role Selection -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-users-cog"></i> Selecionar Perfil
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <select class="form-select" id="roleSelect" onchange="loadPermissions()">
                    <option value="">Selecione um perfil...</option>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role->id ?>"><?= $role->role_name ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Permissions Container -->
<div id="permissionsContainer" style="display: none;">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-key"></i> <span id="roleName"></span> - Permissões
        </div>
        <div class="card-body">
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
            
            <div id="permissionsLoading" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2">Carregando permissões...</p>
            </div>
            
            <div id="permissionsContent" style="display: none;"></div>
            
            <hr>
            
            <div class="text-end">
                <button type="button" class="btn btn-primary" onclick="savePermissions()">
                    <i class="fas fa-save"></i> Salvar Permissões
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let currentRoleId = null;

function loadPermissions() {
    const roleId = document.getElementById('roleSelect').value;
    if (!roleId) {
        document.getElementById('permissionsContainer').style.display = 'none';
        return;
    }
    
    currentRoleId = roleId;
    document.getElementById('permissionsContainer').style.display = 'block';
    document.getElementById('permissionsLoading').style.display = 'block';
    document.getElementById('permissionsContent').style.display = 'none';
    
    // Get role name
    const roleSelect = document.getElementById('roleSelect');
    const roleName = roleSelect.options[roleSelect.selectedIndex].text;
    document.getElementById('roleName').textContent = roleName;
    
    // Load permissions via AJAX
    fetch('<?= site_url('admin/roles/get-role-permissions') ?>?role_id=' + roleId, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderPermissions(data.modules, data.permissions);
        } else {
            alert('Erro ao carregar permissões: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao carregar permissões');
    });
}

function renderPermissions(modules, rolePermissions) {
    const container = document.getElementById('permissionsContent');
    container.innerHTML = '';
    
    let html = '<div class="row">';
    
    modules.forEach(module => {
        html += `
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-cog"></i> ${capitalize(module.module)}
                            <small>
                                <a href="#" class="float-end" onclick="toggleModule('${module.module}'); return false;">
                                    (selecionar)
                                </a>
                            </small>
                        </h6>
                    </div>
                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
        `;
        
        module.permissions.forEach(perm => {
            const checked = rolePermissions.includes(perm.id) ? 'checked' : '';
            html += `
                <div class="form-check mb-2">
                    <input class="form-check-input module-${module.module}" 
                           type="checkbox" 
                           name="permissions[]" 
                           value="${perm.id}"
                           id="perm_${perm.id}"
                           ${checked}>
                    <label class="form-check-label" for="perm_${perm.id}">
                        ${perm.permission_name}
                        <br>
                        <small class="text-muted">${perm.permission_key}</small>
                    </label>
                </div>
            `;
        });
        
        html += `
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    
    container.innerHTML = html;
    document.getElementById('permissionsLoading').style.display = 'none';
    document.getElementById('permissionsContent').style.display = 'block';
}

function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function selectAll() {
    document.querySelectorAll('#permissionsContent input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAll() {
    document.querySelectorAll('#permissionsContent input[type="checkbox"]').forEach(checkbox => {
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

function savePermissions() {
    if (!currentRoleId) return;
    
    const permissions = [];
    document.querySelectorAll('#permissionsContent input[type="checkbox"]:checked').forEach(checkbox => {
        permissions.push(checkbox.value);
    });
    
    fetch('<?= site_url('admin/roles/update-permissions') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            role_id: currentRoleId,
            permissions: permissions
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Permissões salvas com sucesso!');
        } else {
            alert('Erro ao salvar permissões: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao salvar permissões');
    });
}

// Keyboard shortcut: Ctrl+A to select all
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'a' && document.getElementById('permissionsContainer').style.display !== 'none') {
        e.preventDefault();
        selectAll();
    }
});
</script>
<?= $this->endSection() ?>