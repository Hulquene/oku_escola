<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/teachers') ?>">Professores</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-<?= $teacher ? 'edit' : 'user-plus' ?>"></i> <?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/teachers/save') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <?php if ($teacher): ?>
                <input type="hidden" name="id" value="<?= $teacher->id ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= session('errors.first_name') ? 'is-invalid' : '' ?>" 
                               id="first_name" 
                               name="first_name" 
                               value="<?= old('first_name', $teacher->first_name ?? '') ?>"
                               required>
                        <?php if (session('errors.first_name')): ?>
                            <div class="invalid-feedback"><?= session('errors.first_name') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Sobrenome <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= session('errors.last_name') ? 'is-invalid' : '' ?>" 
                               id="last_name" 
                               name="last_name" 
                               value="<?= old('last_name', $teacher->last_name ?? '') ?>"
                               required>
                        <?php if (session('errors.last_name')): ?>
                            <div class="invalid-feedback"><?= session('errors.last_name') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" 
                               class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                               id="email" 
                               name="email" 
                               value="<?= old('email', $teacher->email ?? '') ?>"
                               placeholder="professor@escola.ao"
                               required>
                        <?php if (session('errors.email')): ?>
                            <div class="invalid-feedback"><?= session('errors.email') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefone <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= session('errors.phone') ? 'is-invalid' : '' ?>" 
                               id="phone" 
                               name="phone" 
                               value="<?= old('phone', $teacher->phone ?? '') ?>"
                               placeholder="+244 000 000 000"
                               required>
                        <?php if (session('errors.phone')): ?>
                            <div class="invalid-feedback"><?= session('errors.phone') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <?= $teacher ? 'Nova Senha (deixar em branco para manter)' : 'Senha <span class="text-danger">*</span>' ?>
                        </label>
                        <input type="password" 
                               class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" 
                               id="password" 
                               name="password" 
                               <?= !$teacher ? 'required' : '' ?>
                               minlength="6">
                        <?php if (session('errors.password')): ?>
                            <div class="invalid-feedback"><?= session('errors.password') ?></div>
                        <?php endif; ?>
                        <small class="text-muted">Mínimo de 6 caracteres</small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="photo" class="form-label">Foto</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <small class="text-muted">Formatos: JPG, PNG (máx. 2MB)</small>
                    </div>
                </div>
            </div>
            
            <!-- Endereço -->
            <h5 class="mb-3 mt-4">Endereço</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="address" class="form-label">Endereço</label>
                        <input type="text" 
                               class="form-control" 
                               id="address" 
                               name="address" 
                               value="<?= old('address', $teacher->address ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <!-- Foto atual -->
            <?php if ($teacher && $teacher->photo): ?>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label class="form-label">Foto Atual</label>
                        <div>
                            <img src="<?= base_url('uploads/teachers/' . $teacher->photo) ?>" 
                                 alt="Foto do Professor" 
                                 class="img-thumbnail" 
                                 style="max-width: 150px;">
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Status -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   <?= (old('is_active', $teacher->is_active ?? true)) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Professor Ativo</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/teachers') ?>" class="btn btn-secondary">
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