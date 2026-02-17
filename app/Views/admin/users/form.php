<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/users') ?>">Utilizadores</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-<?= $user ? 'edit' : 'user-plus' ?>"></i> <?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/users/save') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <?php if ($user): ?>
                <input type="hidden" name="id" value="<?= $user->id ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= session('errors.first_name') ? 'is-invalid' : '' ?>" 
                               id="first_name" 
                               name="first_name" 
                               value="<?= old('first_name', $user->first_name ?? '') ?>"
                               required>
                        <?php if (session('errors.first_name')): ?>
                            <div class="invalid-feedback"><?= session('errors.first_name') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Sobrenome <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= session('errors.last_name') ? 'is-invalid' : '' ?>" 
                               id="last_name" 
                               name="last_name" 
                               value="<?= old('last_name', $user->last_name ?? '') ?>"
                               required>
                        <?php if (session('errors.last_name')): ?>
                            <div class="invalid-feedback"><?= session('errors.last_name') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>" 
                               id="username" 
                               name="username" 
                               value="<?= old('username', $user->username ?? '') ?>"
                               <?= $user ? 'readonly' : '' ?>
                               required>
                        <?php if (session('errors.username')): ?>
                            <div class="invalid-feedback"><?= session('errors.username') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" 
                               class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                               id="email" 
                               name="email" 
                               value="<?= old('email', $user->email ?? '') ?>"
                               required>
                        <?php if (session('errors.email')): ?>
                            <div class="invalid-feedback"><?= session('errors.email') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <?= $user ? 'Nova Senha (deixar em branco para manter)' : 'Senha <span class="text-danger">*</span>' ?>
                        </label>
                        <input type="password" 
                               class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" 
                               id="password" 
                               name="password" 
                               <?= !$user ? 'required' : '' ?>
                               minlength="6">
                        <?php if (session('errors.password')): ?>
                            <div class="invalid-feedback"><?= session('errors.password') ?></div>
                        <?php endif; ?>
                        <small class="text-muted">Mínimo de 6 caracteres</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefone</label>
                        <input type="text" 
                               class="form-control" 
                               id="phone" 
                               name="phone" 
                               value="<?= old('phone', $user->phone ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Perfil de Acesso <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.role_id') ? 'is-invalid' : '' ?>" 
                                id="role_id" 
                                name="role_id" 
                                required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($roles)): ?>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role->id ?>" 
                                        <?= (old('role_id', $user->role_id ?? '') == $role->id) ? 'selected' : '' ?>>
                                        <?= $role->role_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.role_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.role_id') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="user_type" class="form-label">Tipo de Utilizador</label>
                        <select class="form-select" id="user_type" name="user_type">
                            <option value="staff" <?= (old('user_type', $user->user_type ?? '') == 'staff') ? 'selected' : '' ?>>Staff</option>
                            <option value="admin" <?= (old('user_type', $user->user_type ?? '') == 'admin') ? 'selected' : '' ?>>Administrador</option>
                            <option value="teacher" <?= (old('user_type', $user->user_type ?? '') == 'teacher') ? 'selected' : '' ?>>Professor</option>
                            <option value="student" <?= (old('user_type', $user->user_type ?? '') == 'student') ? 'selected' : '' ?>>Aluno</option>
                            <option value="guardian" <?= (old('user_type', $user->user_type ?? '') == 'guardian') ? 'selected' : '' ?>>Encarregado</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Endereço</label>
                <textarea class="form-control" id="address" name="address" rows="2"><?= old('address', $user->address ?? '') ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="photo" class="form-label">Foto</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <small class="text-muted">Formatos: JPG, PNG. Máx: 2MB</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   <?= (old('is_active', $user->is_active ?? true)) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Utilizador Ativo</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($user && $user->photo): ?>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label class="form-label">Foto Atual</label>
                        <div>
                            <img src="<?= base_url('uploads/users/' . $user->photo) ?>" 
                                 alt="Foto" 
                                 class="img-thumbnail" 
                                 style="max-width: 100px;">
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>