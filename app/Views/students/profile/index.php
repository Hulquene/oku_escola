<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo melhorado -->
<div class="page-header bg-gradient-primary py-3 mb-4 rounded-3">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="text-white mb-1"><?= $title ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb text-white-50 mb-0">
                        <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>" class="text-white">Início</a></li>
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

<!-- Profile Header - Card Moderno -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-cover" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 120px;"></div>
            <div class="card-body position-relative" style="margin-top: -60px;">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <div class="profile-photo mb-3">
                            <div class="position-relative d-inline-block">
                                <?php if ($student->photo): ?>
                                    <img src="<?= base_url('uploads/students/' . $student->photo) ?>" 
                                         alt="Foto do Aluno" 
                                         class="rounded-circle border border-4 border-white shadow" 
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center text-white border border-4 border-white shadow"
                                         style="width: 150px; height: 150px; font-size: 4rem;">
                                        <?= strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Upload Photo Button -->
                                <button type="button" class="btn btn-sm btn-light rounded-circle position-absolute bottom-0 end-0 shadow-sm" 
                                        data-bs-toggle="modal" data-bs-target="#uploadPhotoModal"
                                        style="width: 40px; height: 40px;">
                                    <i class="fas fa-camera text-primary"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-5">
                        <h2 class="mb-2"><?= $student->full_name ?? $student->first_name . ' ' . $student->last_name ?></h2>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-primary-soft text-primary px-3 py-2">
                                <i class="fas fa-id-card me-1"></i> Nº: <?= $student->student_number ?>
                            </span>
                            <span class="badge bg-success-soft text-success px-3 py-2">
                                <i class="fas fa-venus-mars me-1"></i> <?= $student->gender ?? 'Não informado' ?>
                            </span>
                            <span class="badge bg-info-soft text-info px-3 py-2">
                                <i class="fas fa-calendar-alt me-1"></i> <?= $student->birth_date ? date('d/m/Y', strtotime($student->birth_date)) : 'Nascimento não informado' ?>
                            </span>
                        </div>
                        
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center text-muted">
                                    <i class="fas fa-envelope me-2" style="width: 20px;"></i>
                                    <span><?= $student->email ?? $student->user_email ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center text-muted">
                                    <i class="fas fa-phone me-2" style="width: 20px;"></i>
                                    <span><?= $student->phone ?? $student->user_phone ?? 'Telefone não informado' ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <?php if ($currentEnrollment): ?>
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary rounded-circle p-2">
                                                <i class="fas fa-graduation-cap text-white"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">Matrícula Ativa</h6>
                                            <small class="text-muted"><?= $currentEnrollment->enrollment_number ?></small>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-2 text-center">
                                        <div class="col-4">
                                            <div class="bg-white rounded p-2">
                                                <small class="text-muted d-block">Turma</small>
                                                <strong><?= $currentEnrollment->class_name ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="bg-white rounded p-2">
                                                <small class="text-muted d-block">Ano</small>
                                                <strong><?= $currentEnrollment->year_name ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="bg-white rounded p-2">
                                                <small class="text-muted d-block">Turno</small>
                                                <strong><?= $currentEnrollment->class_shift ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Cards Melhorados -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-orange text-white h-100">
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
        <div class="card border-0 shadow-sm bg-gradient-success text-white h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white-50 mb-1">Presença</h6>
                        <h3 class="mb-0"><?= $stats['attendance_rate'] ?>%</h3>
                        <small class="text-white-50"><?= $stats['total_attendance'] ?? 0 ?> aulas</small>
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
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white-50 mb-1">Média Geral</h6>
                        <h3 class="mb-0"><?= getStudentAverageGrade() ?></h3>
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
                            <i class="fas fa-money-bill fa-2x"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white-50 mb-1">Propinas Pendentes</h6>
                        <h3 class="mb-0"><?= $stats['pending_fees'] ?></h3>
                        <?php if ($stats['pending_fees_amount'] > 0): ?>
                            <small class="text-white-50"><?= number_format($stats['pending_fees_amount'], 2, ',', '.') ?> Kz</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile Tabs com Design Moderno -->
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
                <button class="nav-link rounded-pill px-4" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic" 
                        type="button" role="tab" aria-controls="academic" aria-selected="false">
                    <i class="fas fa-graduation-cap me-2"></i> Académico
                </button>
            </li>
            <li class="nav-item me-2" role="presentation">
                <button class="nav-link rounded-pill px-4" id="health-tab" data-bs-toggle="tab" data-bs-target="#health" 
                        type="button" role="tab" aria-controls="health" aria-selected="false">
                    <i class="fas fa-heartbeat me-2"></i> Saúde
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4" id="guardians-tab" data-bs-toggle="tab" data-bs-target="#guardians" 
                        type="button" role="tab" aria-controls="guardians" aria-selected="false">
                    <i class="fas fa-users me-2"></i> Encarregados
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="profileTabsContent">
            <!-- Personal Information Tab -->
            <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-circle text-primary me-2"></i>
                            Informações Pessoais
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= site_url('students/profile/update') ?>" method="post">
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
                                               value="<?= old('birth_date', $student->birth_date) ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="gender" class="form-label fw-semibold">Gênero <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-venus-mars text-muted"></i></span>
                                        <select class="form-select border-start-0" id="gender" name="gender" required>
                                            <option value="">Selecione...</option>
                                            <option value="Masculino" <?= old('gender', $student->gender) == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                                            <option value="Feminino" <?= old('gender', $student->gender) == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="nationality" class="form-label fw-semibold">Nacionalidade</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-globe text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="nationality" name="nationality" 
                                               value="<?= old('nationality', $student->nationality) ?>" placeholder="Ex: Angolana">
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
                                            <option value="BI" <?= old('identity_type', $student->identity_type) == 'BI' ? 'selected' : '' ?>>BI</option>
                                            <option value="Passaporte" <?= old('identity_type', $student->identity_type) == 'Passaporte' ? 'selected' : '' ?>>Passaporte</option>
                                            <option value="Cédula" <?= old('identity_type', $student->identity_type) == 'Cédula' ? 'selected' : '' ?>>Cédula</option>
                                            <option value="Outro" <?= old('identity_type', $student->identity_type) == 'Outro' ? 'selected' : '' ?>>Outro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="identity_document" class="form-label fw-semibold">Nº do Documento</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-hashtag text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="identity_document" name="identity_document" 
                                               value="<?= old('identity_document', $student->identity_document) ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="nif" class="form-label fw-semibold">NIF</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-barcode text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="nif" name="nif" 
                                               value="<?= old('nif', $student->nif) ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="birth_place" class="form-label fw-semibold">Local de Nascimento</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-map-pin text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="birth_place" name="birth_place" 
                                               value="<?= old('birth_place', $student->birth_place) ?>" placeholder="Província/Município">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="blood_type" class="form-label fw-semibold">Tipo Sanguíneo</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-tint text-muted"></i></span>
                                        <select class="form-select border-start-0" id="blood_type" name="blood_type">
                                            <option value="">Não informado</option>
                                            <option value="A+" <?= old('blood_type', $student->blood_type) == 'A+' ? 'selected' : '' ?>>A+</option>
                                            <option value="A-" <?= old('blood_type', $student->blood_type) == 'A-' ? 'selected' : '' ?>>A-</option>
                                            <option value="B+" <?= old('blood_type', $student->blood_type) == 'B+' ? 'selected' : '' ?>>B+</option>
                                            <option value="B-" <?= old('blood_type', $student->blood_type) == 'B-' ? 'selected' : '' ?>>B-</option>
                                            <option value="AB+" <?= old('blood_type', $student->blood_type) == 'AB+' ? 'selected' : '' ?>>AB+</option>
                                            <option value="AB-" <?= old('blood_type', $student->blood_type) == 'AB-' ? 'selected' : '' ?>>AB-</option>
                                            <option value="O+" <?= old('blood_type', $student->blood_type) == 'O+' ? 'selected' : '' ?>>O+</option>
                                            <option value="O-" <?= old('blood_type', $student->blood_type) == 'O-' ? 'selected' : '' ?>>O-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-5">
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
                            <i class="fas fa-address-card text-primary me-2"></i>
                            Contacto e Endereço
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= site_url('students/profile/update') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            
                            <div class="row g-3">

                                            <!-- INPUTS HIDDEN PARA CAMPOS OBRIGATÓRIOS -->
                                <input type="hidden" name="first_name" value="<?= old('first_name', $user->first_name) ?>">
                                <input type="hidden" name="last_name" value="<?= old('last_name', $user->last_name) ?>">
                                <input type="hidden" name="gender" value="<?= old('gender', $student->gender) ?>">

                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-semibold">Telefone Principal</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone-alt text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="phone" name="phone" 
                                               value="<?= old('phone', $student->phone ?? $user->phone) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                        <input type="email" class="form-control border-start-0" id="email" value="<?= $student->email ?? $user->email ?>" disabled>
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
                                               value="<?= old('emergency_contact_name', $student->emergency_contact_name) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="emergency_contact" class="form-label fw-semibold">Contacto de Emergência - Telefone</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="emergency_contact" name="emergency_contact" 
                                               value="<?= old('emergency_contact', $student->emergency_contact) ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <label for="address" class="form-label fw-semibold">Endereço</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                    <textarea class="form-control border-start-0" id="address" name="address" rows="2"><?= old('address', $student->address ?? $user->address) ?></textarea>
                                </div>
                            </div>
                            
                            <div class="row g-3 mt-2">
                                <div class="col-md-4">
                                    <label for="city" class="form-label fw-semibold">Cidade</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-city text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="city" name="city" 
                                               value="<?= old('city', $student->city) ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="municipality" class="form-label fw-semibold">Município</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-map text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="municipality" name="municipality" 
                                               value="<?= old('municipality', $student->municipality) ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="province" class="form-label fw-semibold">Província</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-globe-africa text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="province" name="province" 
                                               value="<?= old('province', $student->province) ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-5">
                                    <i class="fas fa-save me-2"></i> Guardar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Academic Tab -->
            <div class="tab-pane fade" id="academic" role="tabpanel" aria-labelledby="academic-tab">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history text-primary me-2"></i>
                            Histórico Académico
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= site_url('students/profile/update') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="row g-3">

                                            <!-- INPUTS HIDDEN PARA CAMPOS OBRIGATÓRIOS -->
                                <input type="hidden" name="first_name" value="<?= old('first_name', $user->first_name) ?>">
                                <input type="hidden" name="last_name" value="<?= old('last_name', $user->last_name) ?>">
                                <input type="hidden" name="gender" value="<?= old('gender', $student->gender) ?>">

                                <div class="col-md-6">
                                    <label for="previous_school" class="form-label fw-semibold">Escola Anterior</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-school text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="previous_school" name="previous_school" 
                                               value="<?= old('previous_school', $student->previous_school) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="previous_grade" class="form-label fw-semibold">Ano/Classe Anterior</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-layer-group text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0" id="previous_grade" name="previous_grade" 
                                               value="<?= old('previous_grade', $student->previous_grade) ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mt-4 d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle fa-2x me-3"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Informação Académica Detalhada</strong><br>
                                    Consulte o seu histórico académico completo, incluindo notas por semestre e ano letivo.
                                    <div class="mt-2">
                                        <a href="<?= site_url('students/profile/academic-history') ?>" class="btn btn-sm btn-info text-white">
                                            <i class="fas fa-chart-line me-1"></i> Ver Histórico Completo
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-5">
                                    <i class="fas fa-save me-2"></i> Guardar Informações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Health Tab -->
            <div class="tab-pane fade" id="health" role="tabpanel" aria-labelledby="health-tab">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-heartbeat text-primary me-2"></i>
                            Informação de Saúde
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= site_url('students/profile/update') ?>" method="post">
                            <?= csrf_field() ?>
                            

                            <div class="mb-3">
                                              <!-- INPUTS HIDDEN PARA CAMPOS OBRIGATÓRIOS -->
                                <input type="hidden" name="first_name" value="<?= old('first_name', $user->first_name) ?>">
                                <input type="hidden" name="last_name" value="<?= old('last_name', $user->last_name) ?>">
                                <input type="hidden" name="gender" value="<?= old('gender', $student->gender) ?>">
                            </div>

                            <div class="mb-3">
                                <label for="health_conditions" class="form-label fw-semibold">Condições de Saúde</label>
                                <textarea class="form-control" id="health_conditions" name="health_conditions" rows="3"><?= old('health_conditions', $student->health_conditions) ?></textarea>
                                <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Alergias, doenças crónicas, etc.</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="special_needs" class="form-label fw-semibold">Necessidades Especiais</label>
                                <textarea class="form-control" id="special_needs" name="special_needs" rows="3"><?= old('special_needs', $student->special_needs) ?></textarea>
                                <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Necessidades educativas especiais</small>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-5">
                                    <i class="fas fa-save me-2"></i> Guardar Informações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Guardians Tab -->
            <div class="tab-pane fade" id="guardians" role="tabpanel" aria-labelledby="guardians-tab">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users text-primary me-2"></i>
                            Meus Encarregados de Educação
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($guardians)): ?>
                            <div class="row g-4">
                                <?php foreach ($guardians as $guardian): ?>
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="flex-shrink-0">
                                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                                            <i class="fas fa-user-tie fa-2x text-primary"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h5 class="mb-1"><?= $guardian->full_name ?></h5>
                                                        <span class="badge bg-primary-soft text-primary"><?= $guardian->guardian_type ?></span>
                                                        <?php if ($guardian->relationship): ?>
                                                            <span class="badge bg-secondary-soft text-secondary ms-1"><?= $guardian->relationship ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <small class="text-muted d-block"><i class="fas fa-phone me-1"></i> Telefone</small>
                                                        <strong><?= $guardian->phone ?></strong>
                                                    </div>
                                                    <?php if ($guardian->email): ?>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block"><i class="fas fa-envelope me-1"></i> Email</small>
                                                        <strong><?= $guardian->email ?></strong>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <?php if ($guardian->profession): ?>
                                                <div class="mt-2">
                                                    <small class="text-muted d-block"><i class="fas fa-briefcase me-1"></i> Profissão</small>
                                                    <strong><?= $guardian->profession ?></strong>
                                                    <?php if ($guardian->workplace): ?>
                                                        <small class="text-muted d-block">Local: <?= $guardian->workplace ?></small>
                                                    <?php endif; ?>
                                                </div>
                                                <?php endif; ?>
                                                
                                                <?php if ($guardian->address): ?>
                                                <hr class="my-2">
                                                <small class="text-muted d-block"><i class="fas fa-map-marker-alt me-1"></i> Endereço</small>
                                                <p class="mb-0"><?= $guardian->address ?>, <?= $guardian->city ?? '' ?> <?= $guardian->province ?? '' ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="text-center mt-4">
                                <a href="<?= site_url('students/profile/guardians') ?>" class="btn btn-outline-primary px-4">
                                    <i class="fas fa-eye me-2"></i> Ver Todos os Detalhes
                                </a>
                            </div>
                            
                        <?php else: ?>
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                                <div>
                                    <strong>Nenhum encarregado registado</strong><br>
                                    Não há encarregados de educação associados ao seu perfil.
                                </div>
                            </div>
                        <?php endif; ?>
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
                    <i class="fas fa-camera text-primary me-2"></i>
                    Alterar Foto de Perfil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('students/profile/update-photo') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="current-photo mb-3">
                            <?php if ($student->photo): ?>
                                <img src="<?= base_url('uploads/students/' . $student->photo) ?>" 
                                     alt="Foto Atual" class="rounded-circle border" style="width: 100px; height: 100px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white"
                                     style="width: 100px; height: 100px; font-size: 2.5rem;">
                                    <?= strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) ?>
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
                    <button type="submit" class="btn btn-primary">
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
.bg-gradient-orange {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
.bg-gradient-success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}
.bg-gradient-info {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}
.bg-gradient-warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}
.bg-primary-soft {
    background-color: rgba(102, 126, 234, 0.1);
}
.bg-success-soft {
    background-color: rgba(79, 172, 254, 0.1);
}
.bg-info-soft {
    background-color: rgba(67, 233, 123, 0.1);
}
.bg-secondary-soft {
    background-color: rgba(108, 117, 125, 0.1);
}
.text-primary-soft {
    color: #667eea;
}
.nav-pills .nav-link {
    color: #495057;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}
.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: transparent;
}
.card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}
</style>

<?= $this->endSection() ?>