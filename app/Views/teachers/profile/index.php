<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header bg-gradient-primary py-3 mb-4 rounded-3">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="text-white mb-1"><?= $title ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb text-white-50 mb-0">
                        <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>" class="text-white">Início</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Meu Perfil</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <span class="badge bg-white text-primary p-2">
                    <i class="fas fa-calendar-alt me-1"></i> <?= date('d/m/Y') ?>
                </span>
            </div>
        </div>
    </div>
</div>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> 
        <strong>Por favor, corrija os seguintes erros:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (session('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Profile Header -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-cover" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); height: 120px;"></div>
            <div class="card-body position-relative" style="margin-top: -60px;">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <div class="profile-photo mb-3">
                            <div class="position-relative d-inline-block">
                                <?php if ($user->photo): ?>
                                    <img src="<?= base_url('uploads/teachers/' . $user->photo) ?>" 
                                         alt="Foto do Professor" 
                                         class="rounded-circle border border-4 border-white shadow" 
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-gradient-success rounded-circle d-flex align-items-center justify-content-center text-white border border-4 border-white shadow"
                                         style="width: 150px; height: 150px; font-size: 4rem;">
                                        <?= strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Upload Photo Button -->
                                <button type="button" class="btn btn-sm btn-light rounded-circle position-absolute bottom-0 end-0 shadow-sm" 
                                        data-bs-toggle="modal" data-bs-target="#uploadPhotoModal"
                                        style="width: 40px; height: 40px;">
                                    <i class="fas fa-camera text-success"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-5">
                        <h2 class="mb-2"><?= $user->first_name . ' ' . $user->last_name ?></h2>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-success-soft text-success px-3 py-2">
                                <i class="fas fa-chalkboard-teacher me-1"></i> Professor
                            </span>
                            <span class="badge bg-info-soft text-info px-3 py-2">
                                <i class="fas fa-school me-1"></i> <?= $stats['total_disciplines'] ?> Disciplinas
                            </span>
                        </div>
                        
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center text-muted">
                                    <i class="fas fa-envelope me-2" style="width: 20px;"></i>
                                    <span><?= $user->email ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center text-muted">
                                    <i class="fas fa-phone me-2" style="width: 20px;"></i>
                                    <span><?= $user->phone ?? 'Telefone não informado' ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-success rounded-circle p-2">
                                            <i class="fas fa-graduation-cap text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">Resumo Académico</h6>
                                    </div>
                                </div>
                                
                                <div class="row g-2 text-center">
                                    <div class="col-4">
                                        <div class="bg-white rounded p-2">
                                            <small class="text-muted d-block">Turmas</small>
                                            <strong><?= $stats['total_classes'] ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="bg-white rounded p-2">
                                            <small class="text-muted d-block">Alunos</small>
                                            <strong><?= $stats['total_students'] ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="bg-white rounded p-2">
                                            <small class="text-muted d-block">Exames</small>
                                            <strong><?= $stats['upcoming_exams_count'] ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-success text-white h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-school fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white-50 mb-1">Turmas</h6>
                        <h3 class="mb-0"><?= $stats['total_classes'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-info text-white h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-book fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white-50 mb-1">Disciplinas</h6>
                        <h3 class="mb-0"><?= $stats['total_disciplines'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white-50 mb-1">Alunos</h6>
                        <h3 class="mb-0"><?= $stats['total_students'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-orange text-white h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-pencil-alt fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white-50 mb-1">Próx. Exames</h6>
                        <h3 class="mb-0"><?= $stats['upcoming_exams_count'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile Tabs -->
<div class="row">
    <div class="col-md-12">
        <!-- Navigation Tabs -->
        <ul class="nav nav-pills mb-4" id="profileTabs" role="tablist">
            <li class="nav-item me-2" role="presentation">
                <button class="nav-link active rounded-pill px-4" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" 
                        type="button" role="tab" aria-controls="personal" aria-selected="true">
                    <i class="fas fa-user me-2"></i> Dados Pessoais
                </button>
            </li>
            <li class="nav-item me-2" role="presentation">
                <button class="nav-link rounded-pill px-4" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" 
                        type="button" role="tab" aria-controls="contact" aria-selected="false">
                    <i class="fas fa-address-book me-2"></i> Contacto
                </button>
            </li>
            <li class="nav-item me-2" role="presentation">
                <button class="nav-link rounded-pill px-4" id="professional-tab" data-bs-toggle="tab" data-bs-target="#professional" 
                        type="button" role="tab" aria-controls="professional" aria-selected="false">
                    <i class="fas fa-briefcase me-2"></i> Profissional
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank" 
                        type="button" role="tab" aria-controls="bank" aria-selected="false">
                    <i class="fas fa-university me-2"></i> Dados Bancários
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="profileTabsContent">
            <!-- Personal Information Tab -->
            <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-circle text-success me-2"></i>
                            Informações Pessoais
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= site_url('teachers/profile/update') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label fw-semibold">Primeiro Nome <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="first_name" name="first_name" 
                                               value="<?= old('first_name', $user->first_name) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label fw-semibold">Último Nome <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="last_name" name="last_name" 
                                               value="<?= old('last_name', $user->last_name) ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-3 mt-2">
                                <div class="col-md-4">
                                    <label for="birth_date" class="form-label fw-semibold">Data de Nascimento</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-calendar text-muted"></i></span>
                                        <input type="date" class="form-control border-start-0" id="birth_date" name="birth_date" 
                                               value="<?= old('birth_date', $teacher->birth_date ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="gender" class="form-label fw-semibold">Gênero</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-venus-mars text-muted"></i></span>
                                        <select class="form-select border-start-0" id="gender" name="gender">
                                            <option value="">Selecione...</option>
                                            <option value="Masculino" <?= old('gender', $teacher->gender ?? '') == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                                            <option value="Feminino" <?= old('gender', $teacher->gender ?? '') == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="nationality" class="form-label fw-semibold">Nacionalidade</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-globe text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="nationality" name="nationality" 
                                               value="<?= old('nationality', $teacher->nationality ?? 'Angolana') ?>" placeholder="Ex: Angolana">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-3 mt-2">
                                <div class="col-md-4">
                                    <label for="identity_type" class="form-label fw-semibold">Tipo de Documento</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-id-card text-muted"></i></span>
                                        <select class="form-select border-start-0" id="identity_type" name="identity_type">
                                            <option value="">Selecione...</option>
                                            <option value="BI" <?= old('identity_type', $teacher->identity_type ?? '') == 'BI' ? 'selected' : '' ?>>BI</option>
                                            <option value="Passaporte" <?= old('identity_type', $teacher->identity_type ?? '') == 'Passaporte' ? 'selected' : '' ?>>Passaporte</option>
                                            <option value="Cédula" <?= old('identity_type', $teacher->identity_type ?? '') == 'Cédula' ? 'selected' : '' ?>>Cédula</option>
                                            <option value="Outro" <?= old('identity_type', $teacher->identity_type ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="identity_document" class="form-label fw-semibold">Nº do Documento</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-hashtag text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="identity_document" name="identity_document" 
                                               value="<?= old('identity_document', $teacher->identity_document ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="nif" class="form-label fw-semibold">NIF</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-barcode text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="nif" name="nif" 
                                               value="<?= old('nif', $teacher->nif ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-success px-5">
                                    <i class="fas fa-save me-2"></i> Guardar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Contact Tab -->
            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-address-card text-success me-2"></i>
                            Contacto e Endereço
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= site_url('teachers/profile/update') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <!-- Inputs hidden para campos obrigatórios -->
                            <input type="hidden" name="first_name" value="<?= old('first_name', $user->first_name) ?>">
                            <input type="hidden" name="last_name" value="<?= old('last_name', $user->last_name) ?>">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-semibold">Telefone Principal</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone-alt text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="phone" name="phone" 
                                               value="<?= old('phone', $user->phone) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                        <input type="email" class="form-control border-start-0" id="email" value="<?= $user->email ?>" disabled>
                                    </div>
                                    <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Para alterar o email, contacte a secretaria</small>
                                </div>
                            </div>
                            
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="emergency_contact_name" class="form-label fw-semibold">Contacto de Emergência - Nome</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user-shield text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="emergency_contact_name" name="emergency_contact_name" 
                                               value="<?= old('emergency_contact_name', $teacher->emergency_contact_name ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="emergency_contact" class="form-label fw-semibold">Contacto de Emergência - Telefone</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="emergency_contact" name="emergency_contact" 
                                               value="<?= old('emergency_contact', $teacher->emergency_contact ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <label for="address" class="form-label fw-semibold">Endereço</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                    <textarea class="form-control border-start-0" id="address" name="address" rows="2"><?= old('address', $user->address) ?></textarea>
                                </div>
                            </div>
                            
                            <div class="row g-3 mt-2">
                                <div class="col-md-4">
                                    <label for="city" class="form-label fw-semibold">Cidade</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-city text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="city" name="city" 
                                               value="<?= old('city', $teacher->city ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="municipality" class="form-label fw-semibold">Município</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-map text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="municipality" name="municipality" 
                                               value="<?= old('municipality', $teacher->municipality ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="province" class="form-label fw-semibold">Província</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-globe-africa text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="province" name="province" 
                                               value="<?= old('province', $teacher->province ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-success px-5">
                                    <i class="fas fa-save me-2"></i> Guardar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Professional Tab -->
            <div class="tab-pane fade" id="professional" role="tabpanel" aria-labelledby="professional-tab">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-briefcase text-success me-2"></i>
                            Informação Profissional
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= site_url('teachers/profile/update') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <!-- Inputs hidden para campos obrigatórios -->
                            <input type="hidden" name="first_name" value="<?= old('first_name', $user->first_name) ?>">
                            <input type="hidden" name="last_name" value="<?= old('last_name', $user->last_name) ?>">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="qualifications" class="form-label fw-semibold">Habilitações Literárias</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-graduation-cap text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="qualifications" name="qualifications" 
                                               value="<?= old('qualifications', $teacher->qualifications ?? '') ?>" placeholder="Ex: Licenciatura em Matemática">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="specialization" class="form-label fw-semibold">Especialização</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-certificate text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="specialization" name="specialization" 
                                               value="<?= old('specialization', $teacher->specialization ?? '') ?>" placeholder="Ex: Ensino de Matemática">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="admission_date" class="form-label fw-semibold">Data de Admissão</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-calendar-plus text-muted"></i></span>
                                        <input type="date" class="form-control border-start-0" id="admission_date" name="admission_date" 
                                               value="<?= old('admission_date', $teacher->admission_date ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-success px-5">
                                    <i class="fas fa-save me-2"></i> Guardar Informações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Bank Tab -->
            <div class="tab-pane fade" id="bank" role="tabpanel" aria-labelledby="bank-tab">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-university text-success me-2"></i>
                            Dados Bancários
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= site_url('teachers/profile/update') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <!-- Inputs hidden para campos obrigatórios -->
                            <input type="hidden" name="first_name" value="<?= old('first_name', $user->first_name) ?>">
                            <input type="hidden" name="last_name" value="<?= old('last_name', $user->last_name) ?>">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="bank_name" class="form-label fw-semibold">Nome do Banco</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-building text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="bank_name" name="bank_name" 
                                               value="<?= old('bank_name', $teacher->bank_name ?? '') ?>" placeholder="Ex: Banco BAI">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="bank_account" class="form-label fw-semibold">Número da Conta</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-credit-card text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="bank_account" name="bank_account" 
                                               value="<?= old('bank_account', $teacher->bank_account ?? '') ?>" placeholder="Ex: 123456789">
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-success px-5">
                                    <i class="fas fa-save me-2"></i> Guardar Informações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Photo Modal -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fas fa-camera text-success me-2"></i>
                    Alterar Foto de Perfil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('teachers/profile/update-photo') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="current-photo mb-3">
                            <?php if ($user->photo): ?>
                                <img src="<?= base_url('uploads/teachers/' . $user->photo) ?>" 
                                     alt="Foto Atual" class="rounded-circle border" style="width: 100px; height: 100px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center text-white"
                                     style="width: 100px; height: 100px; font-size: 2.5rem;">
                                    <?= strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <p class="text-muted">Foto atual</p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="photo" class="form-label fw-semibold">Selecione uma nova foto</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i> Formatos: JPG, JPEG, PNG. Máximo: 2MB
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload me-1"></i> Fazer Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CSS adicional -->
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.bg-gradient-success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}
.bg-gradient-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}
.bg-gradient-warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}
.bg-gradient-orange {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
.bg-success-soft {
    background-color: rgba(67, 233, 123, 0.1);
}
.bg-info-soft {
    background-color: rgba(79, 172, 254, 0.1);
}
.nav-pills .nav-link {
    color: #495057;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}
.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    border-color: transparent;
    color: white;
}
.card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}
</style>

<?= $this->endSection() ?>