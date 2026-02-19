<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">
                <?= $teacher ? 'Edite as informações do professor' : 'Preencha os dados para cadastrar um novo professor' ?>
            </p>
        </div>
        <a href="<?= site_url('admin/teachers') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar à Lista
        </a>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">
                <i class="fas fa-home me-1"></i>Dashboard
            </a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/teachers') ?>">Professores</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Progress Bar -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
            <span class="fw-bold">Progresso do Cadastro</span>
            <span class="text-primary" id="progressPercentage">0%</span>
        </div>
        <div class="progress" style="height: 8px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                 id="progressBar" 
                 role="progressbar" 
                 style="width: 0%">
            </div>
        </div>
    </div>
</div>

<!-- Form -->
<div class="card">
    <div class="card-header bg-white">
        <ul class="nav nav-tabs card-header-tabs" id="formTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" 
                        type="button" role="tab" aria-controls="basic" aria-selected="true">
                    <i class="fas fa-user me-2"></i>Dados Básicos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" 
                        type="button" role="tab" aria-controls="personal" aria-selected="false">
                    <i class="fas fa-id-card me-2"></i>Dados Pessoais
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="professional-tab" data-bs-toggle="tab" data-bs-target="#professional" 
                        type="button" role="tab" aria-controls="professional" aria-selected="false">
                    <i class="fas fa-graduation-cap me-2"></i>Dados Profissionais
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank" 
                        type="button" role="tab" aria-controls="bank" aria-selected="false">
                    <i class="fas fa-university me-2"></i>Dados Bancários
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/teachers/save') ?>" method="post" enctype="multipart/form-data" id="teacherForm">
            <?= csrf_field() ?>
            
            <?php if ($teacher): ?>
                <input type="hidden" name="id" value="<?= $teacher->id ?>">
                <input type="hidden" name="	user_id " value="<?= $teacher->	user_id  ?>">
            <?php endif; ?>
            
            <div class="tab-content" id="formTabsContent">
                <!-- TAB 1: Dados Básicos -->
                <div class="tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab">
                    <div class="row">
                        <!-- Foto -->
                        <div class="col-md-3 text-center mb-4">
                            <div class="photo-upload-container">
                                <div class="photo-preview mb-3" id="photoPreview">
                                    <?php if ($teacher && $teacher->photo): ?>
                                        <img src="<?= base_url('uploads/teachers/' . $teacher->photo) ?>" 
                                             alt="Foto do Professor" 
                                             class="img-thumbnail rounded-circle"
                                             style="width: 150px; height: 150px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center border"
                                             style="width: 150px; height: 150px;">
                                            <i class="fas fa-camera fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="photo" class="btn btn-outline-primary">
                                        <i class="fas fa-upload me-2"></i>Escolher Foto
                                    </label>
                                    <input type="file" class="d-none" id="photo" name="photo" accept="image/*" onchange="previewImage(this)">
                                    <small class="d-block text-muted mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Formatos: JPG, PNG. Máx: 2MB
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Campos básicos -->
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label fw-semibold">
                                        Nome <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" 
                                               class="form-control <?= session('errors.first_name') ? 'is-invalid' : '' ?>" 
                                               id="first_name" 
                                               name="first_name" 
                                               value="<?= old('first_name', $teacher->first_name ?? '') ?>"
                                               placeholder="Ex: João"
                                               required>
                                    </div>
                                    <?php if (session('errors.first_name')): ?>
                                        <div class="invalid-feedback d-block"><?= session('errors.first_name') ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label fw-semibold">
                                        Sobrenome <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" 
                                               class="form-control <?= session('errors.last_name') ? 'is-invalid' : '' ?>" 
                                               id="last_name" 
                                               name="last_name" 
                                               value="<?= old('last_name', $teacher->last_name ?? '') ?>"
                                               placeholder="Ex: Silva"
                                               required>
                                    </div>
                                    <?php if (session('errors.last_name')): ?>
                                        <div class="invalid-feedback d-block"><?= session('errors.last_name') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" 
                                               class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                                               id="email" 
                                               name="email" 
                                               value="<?= old('email', $teacher->email ?? '') ?>"
                                               placeholder="professor@escola.ao"
                                               required>
                                    </div>
                                    <?php if (session('errors.email')): ?>
                                        <div class="invalid-feedback d-block"><?= session('errors.email') ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold">
                                        Telefone <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="text" 
                                               class="form-control <?= session('errors.phone') ? 'is-invalid' : '' ?> phone-mask" 
                                               id="phone" 
                                               name="phone" 
                                               value="<?= old('phone', $teacher->phone ?? '') ?>"
                                               placeholder="+244 000 000 000"
                                               required>
                                    </div>
                                    <?php if (session('errors.phone')): ?>
                                        <div class="invalid-feedback d-block"><?= session('errors.phone') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label fw-semibold">
                                        <?= $teacher ? 'Nova Senha' : 'Senha <span class="text-danger">*</span>' ?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" 
                                               class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" 
                                               id="password" 
                                               name="password" 
                                               <?= !$teacher ? 'required' : '' ?>
                                               minlength="6"
                                               placeholder="<?= $teacher ? 'Deixar em branco para manter' : 'Mínimo 6 caracteres' ?>">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                        </button>
                                    </div>
                                    <?php if (session('errors.password')): ?>
                                        <div class="invalid-feedback d-block"><?= session('errors.password') ?></div>
                                    <?php endif; ?>
                                    <small class="text-muted">A senha deve ter no mínimo 6 caracteres</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label fw-semibold">
                                        Confirmar Senha
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" 
                                               class="form-control" 
                                               id="confirm_password" 
                                               name="confirm_password" 
                                               placeholder="Digite a senha novamente">
                                    </div>
                                    <div id="passwordMatch" class="form-text text-danger d-none">As senhas não coincidem</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- TAB 2: Dados Pessoais -->
                <div class="tab-pane fade" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="birth_date" class="form-label fw-semibold">Data de Nascimento</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" 
                                       class="form-control" 
                                       id="birth_date" 
                                       name="birth_date" 
                                       value="<?= old('birth_date', $teacher->birth_date ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="gender" class="form-label fw-semibold">Gênero</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Selecione...</option>
                                <option value="Masculino" <?= old('gender', $teacher->gender ?? '') == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                                <option value="Feminino" <?= old('gender', $teacher->gender ?? '') == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="nationality" class="form-label fw-semibold">Nacionalidade</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nationality" 
                                   name="nationality" 
                                   value="<?= old('nationality', $teacher->nationality ?? 'Angolana') ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="identity_type" class="form-label fw-semibold">Tipo de Documento</label>
                            <select class="form-select" id="identity_type" name="identity_type">
                                <option value="">Selecione...</option>
                                <option value="BI" <?= old('identity_type', $teacher->identity_type ?? '') == 'BI' ? 'selected' : '' ?>>Bilhete de Identidade</option>
                                <option value="Passaporte" <?= old('identity_type', $teacher->identity_type ?? '') == 'Passaporte' ? 'selected' : '' ?>>Passaporte</option>
                                <option value="Cédula" <?= old('identity_type', $teacher->identity_type ?? '') == 'Cédula' ? 'selected' : '' ?>>Cédula</option>
                                <option value="Outro" <?= old('identity_type', $teacher->identity_type ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="identity_document" class="form-label fw-semibold">Número do Documento</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="identity_document" 
                                   name="identity_document" 
                                   value="<?= old('identity_document', $teacher->identity_document ?? '') ?>">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="nif" class="form-label fw-semibold">NIF</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nif" 
                                   name="nif" 
                                   value="<?= old('nif', $teacher->nif ?? '') ?>"
                                   placeholder="000000000">
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Endereço</h5>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="province" class="form-label fw-semibold">Província</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="province" 
                                   name="province" 
                                   value="<?= old('province', $teacher->province ?? '') ?>"
                                   placeholder="Ex: Luanda">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="municipality" class="form-label fw-semibold">Município</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="municipality" 
                                   name="municipality" 
                                   value="<?= old('municipality', $teacher->municipality ?? '') ?>"
                                   placeholder="Ex: Viana">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label fw-semibold">Cidade</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="city" 
                                   name="city" 
                                   value="<?= old('city', $teacher->city ?? '') ?>"
                                   placeholder="Ex: Luanda">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="address" class="form-label fw-semibold">Endereço Completo</label>
                            <textarea class="form-control" 
                                      id="address" 
                                      name="address" 
                                      rows="2"
                                      placeholder="Rua, número, bairro..."><?= old('address', $teacher->address ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- TAB 3: Dados Profissionais -->
                <div class="tab-pane fade" id="professional" role="tabpanel" aria-labelledby="professional-tab">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="qualifications" class="form-label fw-semibold">Habilitações Literárias</label>
                            <select class="form-select" id="qualifications" name="qualifications">
                                <option value="">Selecione...</option>
                                <option value="Licenciatura" <?= old('qualifications', $teacher->qualifications ?? '') == 'Licenciatura' ? 'selected' : '' ?>>Licenciatura</option>
                                <option value="Mestrado" <?= old('qualifications', $teacher->qualifications ?? '') == 'Mestrado' ? 'selected' : '' ?>>Mestrado</option>
                                <option value="Doutoramento" <?= old('qualifications', $teacher->qualifications ?? '') == 'Doutoramento' ? 'selected' : '' ?>>Doutoramento</option>
                                <option value="Bacharelato" <?= old('qualifications', $teacher->qualifications ?? '') == 'Bacharelato' ? 'selected' : '' ?>>Bacharelato</option>
                                <option value="Ensino Médio" <?= old('qualifications', $teacher->qualifications ?? '') == 'Ensino Médio' ? 'selected' : '' ?>>Ensino Médio</option>
                                <option value="Outro" <?= old('qualifications', $teacher->qualifications ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="specialization" class="form-label fw-semibold">Especialização/Área</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="specialization" 
                                   name="specialization" 
                                   value="<?= old('specialization', $teacher->specialization ?? '') ?>"
                                   placeholder="Ex: Matemática, Física, Português">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="admission_date" class="form-label fw-semibold">Data de Admissão</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                <input type="date" 
                                       class="form-control" 
                                       id="admission_date" 
                                       name="admission_date" 
                                       value="<?= old('admission_date', $teacher->admission_date ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="contract_type" class="form-label fw-semibold">Tipo de Contrato</label>
                            <select class="form-select" id="contract_type" name="contract_type">
                                <option value="">Selecione...</option>
                                <option value="Efetivo" <?= old('contract_type', $teacher->contract_type ?? '') == 'Efetivo' ? 'selected' : '' ?>>Efetivo</option>
                                <option value="Contratado" <?= old('contract_type', $teacher->contract_type ?? '') == 'Contratado' ? 'selected' : '' ?>>Contratado</option>
                                <option value="Substituto" <?= old('contract_type', $teacher->contract_type ?? '') == 'Substituto' ? 'selected' : '' ?>>Substituto</option>
                                <option value="Estagiário" <?= old('contract_type', $teacher->contract_type ?? '') == 'Estagiário' ? 'selected' : '' ?>>Estagiário</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="emergency_contact" class="form-label fw-semibold">Contacto de Emergência</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                <input type="text" 
                                       class="form-control phone-mask" 
                                       id="emergency_contact" 
                                       name="emergency_contact" 
                                       value="<?= old('emergency_contact', $teacher->emergency_contact ?? '') ?>"
                                       placeholder="+244 000 000 000">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="emergency_contact_name" class="form-label fw-semibold">Nome do Contacto</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="emergency_contact_name" 
                                   name="emergency_contact_name" 
                                   value="<?= old('emergency_contact_name', $teacher->emergency_contact_name ?? '') ?>"
                                   placeholder="Nome da pessoa a contactar">
                        </div>
                    </div>
                </div>
                
                <!-- TAB 4: Dados Bancários -->
                <div class="tab-pane fade" id="bank" role="tabpanel" aria-labelledby="bank-tab">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bank_name" class="form-label fw-semibold">Banco</label>
                            <select class="form-select" id="bank_name" name="bank_name">
                                <option value="">Selecione...</option>
                                <option value="BAI" <?= old('bank_name', $teacher->bank_name ?? '') == 'BAI' ? 'selected' : '' ?>>BAI - Banco Angolano de Investimentos</option>
                                <option value="BFA" <?= old('bank_name', $teacher->bank_name ?? '') == 'BFA' ? 'selected' : '' ?>>BFA - Banco de Fomento Angola</option>
                                <option value="BIC" <?= old('bank_name', $teacher->bank_name ?? '') == 'BIC' ? 'selected' : '' ?>>BIC - Banco BIC</option>
                                <option value="BCI" <?= old('bank_name', $teacher->bank_name ?? '') == 'BCI' ? 'selected' : '' ?>>BCI - Banco de Comércio e Indústria</option>
                                <option value="KEVE" <?= old('bank_name', $teacher->bank_name ?? '') == 'KEVE' ? 'selected' : '' ?>>Banco Keve</option>
                                <option value="SOL" <?= old('bank_name', $teacher->bank_name ?? '') == 'SOL' ? 'selected' : '' ?>>Banco Sol</option>
                                <option value="Outro" <?= old('bank_name', $teacher->bank_name ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="bank_account" class="form-label fw-semibold">Número da Conta</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="bank_account" 
                                   name="bank_account" 
                                   value="<?= old('bank_account', $teacher->bank_account ?? '') ?>"
                                   placeholder="0000000000000">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bank_iban" class="form-label fw-semibold">IBAN</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="bank_iban" 
                                   name="bank_iban" 
                                   value="<?= old('bank_iban', $teacher->bank_iban ?? '') ?>"
                                   placeholder="AO00000000000000000000000">
                            <small class="text-muted">Código IBAN internacional (opcional)</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="bank_swift" class="form-label fw-semibold">SWIFT/BIC</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="bank_swift" 
                                   name="bank_swift" 
                                   value="<?= old('bank_swift', $teacher->bank_swift ?? '') ?>"
                                   placeholder="AAAAAOAXXXX">
                            <small class="text-muted">Código SWIFT do banco (opcional)</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <!-- Status e Botões -->
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   <?= (old('is_active', $teacher->is_active ?? true)) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="is_active">
                                Professor Ativo
                            </label>
                            <small class="d-block text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Se ativo, o professor poderá aceder ao sistema e ser atribuído a turmas
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/teachers') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Cancelar
                </a>
                <div>
                    <button type="button" class="btn btn-outline-primary me-2" onclick="saveAndNew()">
                        <i class="fas fa-save me-2"></i>Salvar e Novo
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Salvar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Input Mask -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
$(document).ready(function() {
    // Máscaras para telefone
    $('.phone-mask').mask('+244 000 000 000');
    
    // Inicializar tabs
    var triggerTabList = [].slice.call(document.querySelectorAll('#formTabs button'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
        })
    })
    
    // Calcular progresso do formulário
    updateProgress();
    
    $('#teacherForm input, #teacherForm select, #teacherForm textarea').on('change keyup', function() {
        updateProgress();
    });
    
    // Validação de senha
    $('#password, #confirm_password').on('keyup', function() {
        var password = $('#password').val();
        var confirm = $('#confirm_password').val();
        
        if (confirm.length > 0) {
            if (password === confirm) {
                $('#passwordMatch').addClass('d-none');
                $('#confirm_password').removeClass('is-invalid').addClass('is-valid');
            } else {
                $('#passwordMatch').removeClass('d-none');
                $('#confirm_password').removeClass('is-valid').addClass('is-invalid');
            }
        } else {
            $('#passwordMatch').addClass('d-none');
            $('#confirm_password').removeClass('is-invalid is-valid');
        }
    });
    
    // Auto-salvar rascunho (opcional)
    setInterval(function() {
        autoSaveDraft();
    }, 30000); // A cada 30 segundos
});

// Função para atualizar barra de progresso
function updateProgress() {
    var totalFields = $('#teacherForm input:visible, #teacherForm select:visible, #teacherForm textarea:visible').length;
    var filledFields = 0;
    
    $('#teacherForm input:visible, #teacherForm select:visible, #teacherForm textarea:visible').each(function() {
        if ($(this).val() && $(this).val() !== '') {
            filledFields++;
        }
    });
    
    var progress = Math.round((filledFields / totalFields) * 100);
    $('#progressBar').css('width', progress + '%');
    $('#progressPercentage').text(progress + '%');
    
    if (progress === 100) {
        $('#progressBar').removeClass('bg-primary').addClass('bg-success');
    } else {
        $('#progressBar').removeClass('bg-success').addClass('bg-primary');
    }
}

// Preview da imagem
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            $('#photoPreview').html(
                '<img src="' + e.target.result + '" class="img-thumbnail rounded-circle" ' +
                'style="width: 150px; height: 150px; object-fit: cover;">'
            );
        }
        
        reader.readAsDataURL(input.files[0]);
        
        // Validar tamanho do arquivo
        if (input.files[0].size > 2 * 1024 * 1024) {
            alert('A foto não pode ter mais de 2MB');
            input.value = '';
        }
    }
}

// Mostrar/esconder senha
function togglePassword() {
    var passwordInput = $('#password');
    var icon = $('#togglePasswordIcon');
    
    if (passwordInput.attr('type') === 'password') {
        passwordInput.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        passwordInput.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
}

// Salvar e novo
function saveAndNew() {
    var form = $('#teacherForm');
    form.append('<input type="hidden" name="save_and_new" value="1">');
    form.submit();
}

// Auto-salvar rascunho
function autoSaveDraft() {
    var formData = $('#teacherForm').serialize();
    
    $.ajax({
        url: '<?= site_url('admin/teachers/auto-save-draft') ?>',
        type: 'POST',
        data: formData,
        success: function(response) {
            // Mostrar toast de confirmação
            showToast('Rascunho salvo automaticamente');
        }
    });
}

// Toast notification
function showToast(message) {
    // Implementar toast se necessário
    console.log(message);
}

// Validação antes de enviar
$('#teacherForm').on('submit', function(e) {
    var password = $('#password').val();
    var confirm = $('#confirm_password').val();
    
    if (confirm.length > 0 && password !== confirm) {
        e.preventDefault();
        alert('As senhas não coincidem!');
        return false;
    }
    
    return true;
});
</script>

<style>
/* Estilo para a barra de progresso */
.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-bar {
    transition: width 0.3s ease;
    border-radius: 10px;
}

/* Estilo para as tabs */
.nav-tabs .nav-link {
    color: #495057;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    font-weight: 600;
}

/* Estilo para o upload de foto */
.photo-upload-container {
    text-align: center;
}

.photo-preview {
    transition: all 0.3s ease;
}

.photo-preview:hover {
    transform: scale(1.02);
}

/* Animações */
.tab-pane {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0.5; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Estilo para campos obrigatórios */
label[for$="*"]::after {
    content: " *";
    color: red;
}
</style>
<?= $this->endSection() ?>