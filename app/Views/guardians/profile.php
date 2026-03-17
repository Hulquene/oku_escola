<?= $this->extend('guardians/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-user-circle me-2"></i>Meu Perfil</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Perfil</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <button type="button" class="hdr-btn primary" onclick="enableEdit()">
                <i class="fas fa-edit me-1"></i> Editar Perfil
            </button>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row g-4">
    <div class="col-md-4">
        <!-- Card de Foto -->
        <div class="ci-card text-center">
            <div class="ci-card-body">
                <div class="position-relative mb-3">
                    <div class="avatar-circle" style="width: 120px; height: 120px; font-size: 2.5rem; margin: 0 auto; background: var(--accent);">
                        <?= strtoupper(substr($guardian['full_name'], 0, 1)) ?>
                    </div>
                    <button type="button" class="btn-ci primary btn-sm" style="position: absolute; bottom: 0; right: 50%; transform: translateX(50%);" onclick="uploadPhoto()">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
                <h4 class="fw-semibold mb-1"><?= $guardian['full_name'] ?></h4>
                <p class="text-muted small"><?= $guardian['guardian_type'] ?></p>
            </div>
            
            <div class="ci-card-footer bg-light">
                <div class="d-flex justify-content-center gap-2">
                    <span class="badge-ci <?= $guardian['is_active'] ? 'success' : 'danger' ?>">
                        <i class="fas fa-circle me-1" style="font-size: 6px;"></i>
                        <?= $guardian['is_active'] ? 'Ativo' : 'Inativo' ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Card de Informações -->
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-info-circle"></i>
                    <span>Informações Pessoais</span>
                </div>
            </div>
            <div class="ci-card-body">
                <form id="profileForm">
                    <?= csrf_field() ?>
                    
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="filter-label">Nome Completo</label>
                            <input type="text" class="form-input-ci" name="full_name" value="<?= $guardian['full_name'] ?>" disabled>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="filter-label">Email</label>
                            <input type="email" class="form-input-ci" name="email" value="<?= $guardian['email'] ?>" disabled>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="filter-label">Telefone</label>
                            <input type="text" class="form-input-ci" name="phone" value="<?= $guardian['phone'] ?>" disabled>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="filter-label">Telefone Alternativo</label>
                            <input type="text" class="form-input-ci" name="phone2" value="<?= $guardian['phone2'] ?? '' ?>" disabled>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="filter-label">Tipo</label>
                            <input type="text" class="form-input-ci" value="<?= $guardian['guardian_type'] ?>" disabled>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="filter-label">BI/Passaporte</label>
                            <input type="text" class="form-input-ci" value="<?= $guardian['identity_document'] ?? '-' ?>" disabled>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="filter-label">NIF</label>
                            <input type="text" class="form-input-ci" value="<?= $guardian['nif'] ?? '-' ?>" disabled>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="filter-label">Data Nascimento</label>
                            <input type="text" class="form-input-ci" value="<?= $guardian['birth_date'] ? date('d/m/Y', strtotime($guardian['birth_date'])) : '-' ?>" disabled>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="filter-label">Endereço</label>
                            <textarea class="form-input-ci" name="address" rows="2" disabled><?= $guardian['address'] ?? '' ?></textarea>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="filter-label">Cidade</label>
                            <input type="text" class="form-input-ci" name="city" value="<?= $guardian['city'] ?? '' ?>" disabled>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="filter-label">Município</label>
                            <input type="text" class="form-input-ci" name="municipality" value="<?= $guardian['municipality'] ?? '' ?>" disabled>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="filter-label">Província</label>
                            <input type="text" class="form-input-ci" name="province" value="<?= $guardian['province'] ?? '' ?>" disabled>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="filter-label">Profissão</label>
                            <input type="text" class="form-input-ci" value="<?= $guardian['profession'] ?? '-' ?>" disabled>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="filter-label">Local de Trabalho</label>
                            <input type="text" class="form-input-ci" value="<?= $guardian['workplace'] ?? '-' ?>" disabled>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Card de Informações da Conta -->
        <div class="ci-card mt-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-lock"></i>
                    <span>Informações da Conta</span>
                </div>
            </div>
            <div class="ci-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="filter-label">Nome de Usuário</label>
                        <input type="text" class="form-input-ci" value="<?= $user['username'] ?? '' ?>" disabled>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="filter-label">Último Acesso</label>
                        <input type="text" class="form-input-ci" value="<?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : '-' ?>" disabled>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="<?= site_url('auth/forgetpassword') ?>" class="btn-filter clear">
                        <i class="fas fa-key me-1"></i> Alterar Senha
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição -->
<div class="modal fade ci-modal" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--primary);">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>
                    Editar Perfil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editProfileForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="filter-label">Nome Completo <span class="req">*</span></label>
                            <input type="text" class="form-input-ci" name="full_name" value="<?= $guardian['full_name'] ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="filter-label">Email <span class="req">*</span></label>
                            <input type="email" class="form-input-ci" name="email" value="<?= $guardian['email'] ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="filter-label">Telefone <span class="req">*</span></label>
                            <input type="text" class="form-input-ci" name="phone" value="<?= $guardian['phone'] ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="filter-label">Telefone Alternativo</label>
                            <input type="text" class="form-input-ci" name="phone2" value="<?= $guardian['phone2'] ?? '' ?>">
                        </div>
                        
                        <div class="col-md-12">
                            <label class="filter-label">Endereço</label>
                            <textarea class="form-input-ci" name="address" rows="2"><?= $guardian['address'] ?? '' ?></textarea>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="filter-label">Cidade</label>
                            <input type="text" class="form-input-ci" name="city" value="<?= $guardian['city'] ?? '' ?>">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="filter-label">Município</label>
                            <input type="text" class="form-input-ci" name="municipality" value="<?= $guardian['municipality'] ?? '' ?>">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="filter-label">Província</label>
                            <input type="text" class="form-input-ci" name="province" value="<?= $guardian['province'] ?? '' ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-filter clear" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn-filter apply">
                        <i class="fas fa-save me-2"></i>Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function enableEdit() {
    $('#editProfileModal').modal('show');
}

function uploadPhoto() {
    // Implementar upload de foto
    Swal.fire({
        icon: 'info',
        title: 'Em desenvolvimento',
        text: 'Funcionalidade de upload de foto em breve.'
    });
}

$('#editProfileForm').on('submit', function(e) {
    e.preventDefault();
    
    var form = $(this);
    var data = form.serialize();
    
    $.ajax({
        url: '<?= site_url('guardians/profile/update') ?>',
        type: 'POST',
        data: data,
        dataType: 'json',
        beforeSend: function() {
            $('#editProfileModal .btn-filter.apply').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Salvando...');
        },
        success: function(response) {
            if (response.success) {
                $('#editProfileModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message,
                    timer: 2000
                }).then(() => {
                    location.reload();
                });
            } else {
                let errors = '';
                if (response.errors) {
                    for (let key in response.errors) {
                        errors += response.errors[key] + '<br>';
                    }
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    html: response.message + '<br>' + errors
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Erro ao processar requisição.'
            });
        },
        complete: function() {
            $('#editProfileModal .btn-filter.apply').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Salvar Alterações');
        }
    });
});
</script>
<?= $this->endSection() ?>