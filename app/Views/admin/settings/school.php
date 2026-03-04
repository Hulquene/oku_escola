<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
/* Estilos adicionais para melhorar o layout */
.school-logo-preview {
    width: 100%;
    height: 160px;
    background: var(--surface);
    border: 2px dashed var(--border);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    overflow: hidden;
    transition: all 0.2s;
}

.school-logo-preview.has-image {
    border: 2px solid var(--accent);
    background: #fff;
}

.school-logo-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.school-logo-preview .placeholder {
    text-align: center;
    color: var(--text-muted);
}

.school-logo-preview .placeholder i {
    font-size: 3rem;
    margin-bottom: 0.5rem;
    opacity: 0.3;
}

.school-logo-preview .placeholder p {
    font-size: 0.8rem;
    margin: 0;
}

.management-card {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: #fff;
    border-radius: var(--radius-sm);
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.management-card i {
    opacity: 0.8;
    margin-right: 0.5rem;
}

.section-divider {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 1.5rem 0 1rem;
    color: var(--text-secondary);
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.section-divider-line {
    flex: 1;
    height: 1px;
    background: var(--border);
}

.section-divider i {
    color: var(--accent);
}

.form-control, .form-select {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
    transition: all 0.2s;
}

.form-control:focus, .form-select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,0.1);
}

.form-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 0.3rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.form-label i {
    color: var(--accent);
    margin-right: 0.3rem;
    font-size: 0.75rem;
}

.input-group-text {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-muted);
    font-size: 0.85rem;
}

.card-stats {
    background: var(--surface);
    border-radius: var(--radius-sm);
    padding: 1rem;
    text-align: center;
    border: 1px solid var(--border);
    transition: transform 0.2s;
}

.card-stats:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.card-stats .value {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--primary);
    line-height: 1.2;
}

.card-stats .label {
    font-size: 0.75rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.btn-save {
    background: var(--accent);
    border: none;
    color: #fff;
    padding: 0.7rem 2rem;
    border-radius: var(--radius-sm);
    font-weight: 600;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.btn-save:hover {
    background: var(--accent-hover);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59,127,232,0.3);
}
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-cog me-2"></i><?= $title ?></h1>
        <a href="<?= site_url('admin/settings') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/settings') ?>">Configurações</a></li>
            <li class="breadcrumb-item active" aria-current="page">Configurações da Escola</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Settings Tabs -->
<ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
            <i class="fas fa-building"></i> Gerais
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="management-tab" data-bs-toggle="tab" data-bs-target="#management" type="button" role="tab">
            <i class="fas fa-users-cog"></i> Gestão
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic" type="button" role="tab">
            <i class="fas fa-graduation-cap"></i> Académicas
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="fees-tab" data-bs-toggle="tab" data-bs-target="#fees" type="button" role="tab">
            <i class="fas fa-coins"></i> Propinas
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab">
            <i class="fas fa-cog"></i> Sistema
        </button>
    </li>
</ul>

<div class="tab-content" id="settingsTabContent">
    
    <!-- ==================== GENERAL SETTINGS ==================== -->
    <div class="tab-pane fade show active" id="general" role="tabpanel">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-building"></i> Informações Gerais da Escola
            </div>
            <div class="card-body">
                <form action="<?= site_url('admin/settings/save-school') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Nome e Sigla -->
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="school_name" class="form-label">
                                        <i class="fas fa-school"></i> Nome da Escola <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control <?= session('errors.school_name') ? 'is-invalid' : '' ?>" 
                                           id="school_name" name="school_name" 
                                           value="<?= old('school_name', $settings['school_name'] ?? '') ?>" 
                                           placeholder="Ex: Escola do Ensino Primário 1º de Maio" required>
                                    <?php if (session('errors.school_name')): ?>
                                        <div class="invalid-feedback"><?= session('errors.school_name') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <label for="school_acronym" class="form-label">
                                        <i class="fas fa-tag"></i> Sigla
                                    </label>
                                    <input type="text" class="form-control" id="school_acronym" name="school_acronym" 
                                           value="<?= old('school_acronym', $settings['school_acronym'] ?? '') ?>"
                                           placeholder="Ex: EEP1M">
                                    <small class="text-muted">Usada em documentos oficiais</small>
                                </div>
                            </div>
                            
                            <!-- Endereço -->
                            <div class="mb-3">
                                <label for="school_address" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i> Endereço Completo
                                </label>
                                <input type="text" class="form-control" id="school_address" name="school_address" 
                                       value="<?= old('school_address', $settings['school_address'] ?? '') ?>"
                                       placeholder="Rua, Bairro, Nº">
                            </div>
                            
                            <!-- Cidade, Província, Fundação -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="school_city" class="form-label">
                                        <i class="fas fa-city"></i> Cidade/Município
                                    </label>
                                    <input type="text" class="form-control" id="school_city" name="school_city" 
                                           value="<?= old('school_city', $settings['school_city'] ?? '') ?>"
                                           placeholder="Ex: Luanda">
                                </div>
                                <div class="col-md-4">
                                    <label for="school_province" class="form-label">
                                        <i class="fas fa-map"></i> Província
                                    </label>
                                    <input type="text" class="form-control" id="school_province" name="school_province" 
                                           value="<?= old('school_province', $settings['school_province'] ?? '') ?>"
                                           placeholder="Ex: Luanda">
                                </div>
                                <div class="col-md-4">
                                    <label for="school_founding_year" class="form-label">
                                        <i class="fas fa-calendar-alt"></i> Ano de Fundação
                                    </label>
                                    <input type="number" class="form-control" id="school_founding_year" name="school_founding_year" 
                                           value="<?= old('school_founding_year', $settings['school_founding_year'] ?? date('Y')) ?>"
                                           min="1900" max="<?= date('Y') ?>">
                                </div>
                            </div>
                            
                            <!-- Contactos -->
                            <div class="section-divider">
                                <i class="fas fa-phone-alt"></i>
                                <span>Contactos</span>
                                <div class="section-divider-line"></div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="school_phone" class="form-label">
                                        <i class="fas fa-phone"></i> Telefone Principal <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control <?= session('errors.school_phone') ? 'is-invalid' : '' ?>" 
                                           id="school_phone" name="school_phone" 
                                           value="<?= old('school_phone', $settings['school_phone'] ?? '') ?>" 
                                           placeholder="+244 999 999 999" required>
                                    <?php if (session('errors.school_phone')): ?>
                                        <div class="invalid-feedback"><?= session('errors.school_phone') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="school_alt_phone" class="form-label">
                                        <i class="fas fa-phone-alt"></i> Telefone Alternativo
                                    </label>
                                    <input type="text" class="form-control" id="school_alt_phone" name="school_alt_phone" 
                                           value="<?= old('school_alt_phone', $settings['school_alt_phone'] ?? '') ?>"
                                           placeholder="+244 999 999 998">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="school_email" class="form-label">
                                        <i class="fas fa-envelope"></i> Email Institucional <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control <?= session('errors.school_email') ? 'is-invalid' : '' ?>" 
                                           id="school_email" name="school_email" 
                                           value="<?= old('school_email', $settings['school_email'] ?? '') ?>" 
                                           placeholder="info@escola.ao" required>
                                    <?php if (session('errors.school_email')): ?>
                                        <div class="invalid-feedback"><?= session('errors.school_email') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="school_website" class="form-label">
                                        <i class="fas fa-globe"></i> Website
                                    </label>
                                    <input type="url" class="form-control" id="school_website" name="school_website" 
                                           value="<?= old('school_website', $settings['school_website'] ?? '') ?>"
                                           placeholder="www.escola.ao">
                                </div>
                            </div>
                            
                            <!-- NIF -->
                            <div class="mb-3">
                                <label for="school_nif" class="form-label">
                                    <i class="fas fa-id-card"></i> NIF
                                </label>
                                <input type="text" class="form-control" id="school_nif" name="school_nif" 
                                       value="<?= old('school_nif', $settings['school_nif'] ?? '') ?>"
                                       placeholder="Ex: 5000000000">
                                <small class="text-muted">Número de Identificação Fiscal</small>
                            </div>
                        </div>
                        
                        <!-- Logo Section -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <i class="fas fa-image"></i> Logo da Escola
                                </div>
                                <div class="card-body">
                                    <div class="school-logo-preview <?= !empty($settings['school_logo']) ? 'has-image' : '' ?>" id="logoPreview">
                                        <?php if (!empty($settings['school_logo'])): ?>
                                            <img src="<?= base_url('uploads/school/' . $settings['school_logo']) ?>" 
                                                 alt="Logo da Escola" id="previewImage">
                                        <?php else: ?>
                                            <div class="placeholder">
                                                <i class="fas fa-image"></i>
                                                <p>Nenhum logo selecionado</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <label for="school_logo" class="form-label">Alterar Logo</label>
                                    <input type="file" class="form-control" id="school_logo" name="school_logo" 
                                           accept="image/jpeg,image/png,image/gif">
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle"></i> Formatos: JPG, PNG, GIF. Máx: 2MB
                                    </small>
                                    
                                    <?php if (!empty($settings['school_logo'])): ?>
                                        <div class="mt-3">
                                            <a href="<?= site_url('admin/settings/remove-logo') ?>" 
                                               class="btn btn-sm btn-outline-danger w-100"
                                               onclick="return confirm('Tem certeza que deseja remover o logo?')">
                                                <i class="fas fa-trash"></i> Remover Logo
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-end">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Salvar Configurações Gerais
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- ==================== MANAGEMENT SETTINGS (NOVO) ==================== -->
    <div class="tab-pane fade" id="management" role="tabpanel">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-users-cog"></i> Gestão Escolar
            </div>
            <div class="card-body">
                <!-- Informação sobre direção -->
                <div class="management-card">
                    <i class="fas fa-info-circle"></i>
                    Informações dos responsáveis pela escola para documentos oficiais
                </div>
                
                <form action="<?= site_url('admin/settings/save-management') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <!-- Diretor -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-user-tie text-primary"></i> Diretor(a) da Escola</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="director_name" class="form-label">
                                            <i class="fas fa-user"></i> Nome Completo
                                        </label>
                                        <input type="text" class="form-control" id="director_name" name="director_name" 
                                               value="<?= old('director_name', $settings['director_name'] ?? '') ?>"
                                               placeholder="Ex: Dr. António Manuel Santos">
                                        <small class="text-muted">Nome do Diretor para assinaturas e documentos</small>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="director_title" class="form-label">Título/Tratamento</label>
                                            <select class="form-select" id="director_title" name="director_title">
                                                <option value="Dr." <?= (old('director_title', $settings['director_title'] ?? '') == 'Dr.') ? 'selected' : '' ?>>Dr.</option>
                                                <option value="Dra." <?= (old('director_title', $settings['director_title'] ?? '') == 'Dra.') ? 'selected' : '' ?>>Dra.</option>
                                                <option value="Prof. Dr." <?= (old('director_title', $settings['director_title'] ?? '') == 'Prof. Dr.') ? 'selected' : '' ?>>Prof. Dr.</option>
                                                <option value="Prof. Dra." <?= (old('director_title', $settings['director_title'] ?? '') == 'Prof. Dra.') ? 'selected' : '' ?>>Prof. Dra.</option>
                                                <option value="Professor" <?= (old('director_title', $settings['director_title'] ?? '') == 'Professor') ? 'selected' : '' ?>>Professor</option>
                                                <option value="Professora" <?= (old('director_title', $settings['director_title'] ?? '') == 'Professora') ? 'selected' : '' ?>>Professora</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="director_degree" class="form-label">Grau Académico</label>
                                            <select class="form-select" id="director_degree" name="director_degree">
                                                <option value="Licenciado" <?= (old('director_degree', $settings['director_degree'] ?? '') == 'Licenciado') ? 'selected' : '' ?>>Licenciado</option>
                                                <option value="Mestre" <?= (old('director_degree', $settings['director_degree'] ?? '') == 'Mestre') ? 'selected' : '' ?>>Mestre</option>
                                                <option value="Doutor" <?= (old('director_degree', $settings['director_degree'] ?? '') == 'Doutor') ? 'selected' : '' ?>>Doutor</option>
                                                <option value="Pós-Graduado" <?= (old('director_degree', $settings['director_degree'] ?? '') == 'Pós-Graduado') ? 'selected' : '' ?>>Pós-Graduado</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Subdiretor Pedagógico -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-chalkboard-teacher text-success"></i> Subdiretor(a) Pedagógico(a)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="pedagogical_director_name" class="form-label">
                                            <i class="fas fa-user"></i> Nome Completo
                                        </label>
                                        <input type="text" class="form-control" id="pedagogical_director_name" name="pedagogical_director_name" 
                                               value="<?= old('pedagogical_director_name', $settings['pedagogical_director_name'] ?? '') ?>"
                                               placeholder="Ex: Dra. Maria Fernanda Costa">
                                        <small class="text-muted">Responsável pela área pedagógica</small>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="pedagogical_title" class="form-label">Título/Tratamento</label>
                                            <select class="form-select" id="pedagogical_title" name="pedagogical_title">
                                                <option value="Dr." <?= (old('pedagogical_title', $settings['pedagogical_title'] ?? '') == 'Dr.') ? 'selected' : '' ?>>Dr.</option>
                                                <option value="Dra." <?= (old('pedagogical_title', $settings['pedagogical_title'] ?? '') == 'Dra.') ? 'selected' : '' ?>>Dra.</option>
                                                <option value="Prof. Dr." <?= (old('pedagogical_title', $settings['pedagogical_title'] ?? '') == 'Prof. Dr.') ? 'selected' : '' ?>>Prof. Dr.</option>
                                                <option value="Prof. Dra." <?= (old('pedagogical_title', $settings['pedagogical_title'] ?? '') == 'Prof. Dra.') ? 'selected' : '' ?>>Prof. Dra.</option>
                                                <option value="Professor" <?= (old('pedagogical_title', $settings['pedagogical_title'] ?? '') == 'Professor') ? 'selected' : '' ?>>Professor</option>
                                                <option value="Professora" <?= (old('pedagogical_title', $settings['pedagogical_title'] ?? '') == 'Professora') ? 'selected' : '' ?>>Professora</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="pedagogical_degree" class="form-label">Grau Académico</label>
                                            <select class="form-select" id="pedagogical_degree" name="pedagogical_degree">
                                                <option value="Licenciado" <?= (old('pedagogical_degree', $settings['pedagogical_degree'] ?? '') == 'Licenciado') ? 'selected' : '' ?>>Licenciado</option>
                                                <option value="Mestre" <?= (old('pedagogical_degree', $settings['pedagogical_degree'] ?? '') == 'Mestre') ? 'selected' : '' ?>>Mestre</option>
                                                <option value="Doutor" <?= (old('pedagogical_degree', $settings['pedagogical_degree'] ?? '') == 'Doutor') ? 'selected' : '' ?>>Doutor</option>
                                                <option value="Pós-Graduado" <?= (old('pedagogical_degree', $settings['pedagogical_degree'] ?? '') == 'Pós-Graduado') ? 'selected' : '' ?>>Pós-Graduado</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informações para documentos -->
                    <div class="section-divider">
                        <i class="fas fa-file-alt"></i>
                        <span>Informações para Documentos Oficiais</span>
                        <div class="section-divider-line"></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="document_city" class="form-label">
                                    <i class="fas fa-city"></i> Cidade para Documentos
                                </label>
                                <input type="text" class="form-control" id="document_city" name="document_city" 
                                       value="<?= old('document_city', $settings['document_city'] ?? $settings['school_city'] ?? '') ?>"
                                       placeholder="Ex: Luanda">
                                <small class="text-muted">Aparecerá em cabeçalhos de documentos</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="signature_title" class="form-label">
                                    <i class="fas fa-signature"></i> Título para Assinaturas
                                </label>
                                <input type="text" class="form-control" id="signature_title" name="signature_title" 
                                       value="<?= old('signature_title', $settings['signature_title'] ?? 'O Director') ?>">
                                <small class="text-muted">Ex: O Director, A Directora, O Subdirector Pedagógico</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pré-visualização de assinatura -->
                    <div class="card bg-light mt-3">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="fas fa-eye"></i> Pré-visualização de Assinatura</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Director:</strong></p>
                                    <p class="mb-0" style="font-family: 'Brush Script MT', cursive; font-size: 1.2rem;">
                                        <?= old('director_title', $settings['director_title'] ?? 'Dr.') ?> 
                                        <?= old('director_name', $settings['director_name'] ?? '[Nome do Director]') ?>
                                    </p>
                                    <p class="text-muted small"><?= old('signature_title', $settings['signature_title'] ?? 'O Director') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Subdirector Pedagógico:</strong></p>
                                    <p class="mb-0" style="font-family: 'Brush Script MT', cursive; font-size: 1.2rem;">
                                        <?= old('pedagogical_title', $settings['pedagogical_title'] ?? 'Dra.') ?> 
                                        <?= old('pedagogical_director_name', $settings['pedagogical_director_name'] ?? '[Nome do Subdirector]') ?>
                                    </p>
                                    <p class="text-muted small">O Subdirector Pedagógico</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-end">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Salvar Informações de Gestão
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- ==================== ACADEMIC SETTINGS ==================== -->
    <div class="tab-pane fade" id="academic" role="tabpanel">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-graduation-cap"></i> Configurações Académicas
            </div>
            <div class="card-body">
                <form action="<?= site_url('admin/settings/save-academic') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="academic_year_id" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Ano Letivo Atual <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= session('errors.academic_year_id') ? 'is-invalid' : '' ?>" 
                                        id="academic_year_id" name="academic_year_id" required>
                                    <option value="">Selecione...</option>
                                    <?php if (!empty($academicYears)): ?>
                                        <?php foreach ($academicYears as $year): ?>
                                            <option value="<?= $year->id ?>" 
                                                <?= (old('academic_year_id', $settings['current_academic_year'] ?? '') == $year->id) ? 'selected' : '' ?>>
                                                <?= $year->year_name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if (session('errors.academic_year_id')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.academic_year_id') ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="semester_id" class="form-label">
                                    <i class="fas fa-calendar-week"></i> Semestre Atual <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= session('errors.semester_id') ? 'is-invalid' : '' ?>" 
                                        id="semester_id" name="semester_id" required>
                                    <option value="">Selecione...</option>
                                    <?php if (!empty($semesters)): ?>
                                        <?php foreach ($semesters as $sem): ?>
                                            <option value="<?= $sem->id ?>" 
                                                <?= (old('semester_id', $settings['current_semester'] ?? '') == $sem->id) ? 'selected' : '' ?>>
                                                <?= $sem->semester_name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if (session('errors.semester_id')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.semester_id') ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="grading_system" class="form-label">
                                    <i class="fas fa-chart-line"></i> Sistema de Avaliação <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= session('errors.grading_system') ? 'is-invalid' : '' ?>" 
                                        id="grading_system" name="grading_system" required>
                                    <option value="0-20" <?= (old('grading_system', $settings['grading_system'] ?? '0-20') == '0-20') ? 'selected' : '' ?>>0 a 20 valores</option>
                                    <option value="0-100" <?= (old('grading_system', $settings['grading_system'] ?? '') == '0-100') ? 'selected' : '' ?>>0 a 100%</option>
                                    <option value="A-F" <?= (old('grading_system', $settings['grading_system'] ?? '') == 'A-F') ? 'selected' : '' ?>>A a F (Letras)</option>
                                </select>
                                <?php if (session('errors.grading_system')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.grading_system') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="min_grade" class="form-label">Nota Mínima</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="min_grade" name="min_grade" 
                                                   step="0.1" value="<?= old('min_grade', $settings['min_grade'] ?? 0) ?>">
                                            <span class="input-group-text">val</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="max_grade" class="form-label">Nota Máxima</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="max_grade" name="max_grade" 
                                                   step="0.1" value="<?= old('max_grade', $settings['max_grade'] ?? 20) ?>">
                                            <span class="input-group-text">val</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="approval_grade" class="form-label">Nota de Aprovação</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="approval_grade" name="approval_grade" 
                                                   step="0.1" value="<?= old('approval_grade', $settings['approval_grade'] ?? 10) ?>">
                                            <span class="input-group-text">val</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="attendance_threshold" class="form-label">Percentagem Mínima de Presenças</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="attendance_threshold" name="attendance_threshold" 
                                           min="0" max="100" value="<?= old('attendance_threshold', $settings['attendance_threshold'] ?? 75) ?>">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="enrollment_deadline" class="form-label">Prazo de Matrícula</label>
                                <input type="date" class="form-control" id="enrollment_deadline" name="enrollment_deadline" 
                                       value="<?= old('enrollment_deadline', $settings['enrollment_deadline'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="academic_calendar" class="form-label">Tipo de Calendário</label>
                                <select class="form-select" id="academic_calendar" name="academic_calendar">
                                    <option value="semester" <?= (old('academic_calendar', $settings['academic_calendar'] ?? 'semester') == 'semester') ? 'selected' : '' ?>>Semestral</option>
                                    <option value="trimester" <?= (old('academic_calendar', $settings['academic_calendar'] ?? '') == 'trimester') ? 'selected' : '' ?>>Trimestral</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-end">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Salvar Configurações Académicas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- ==================== FEES SETTINGS ==================== -->
    <div class="tab-pane fade" id="fees" role="tabpanel">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-coins"></i> Configurações de Propinas
            </div>
            <div class="card-body">
                <form action="<?= site_url('admin/settings/save-payment') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="default_currency" class="form-label">
                                    <i class="fas fa-money-bill-wave"></i> Moeda Padrão <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= session('errors.default_currency') ? 'is-invalid' : '' ?>" 
                                        id="default_currency" name="default_currency" required>
                                    <option value="">Selecione...</option>
                                    <?php if (!empty($currencies)): ?>
                                        <?php foreach ($currencies as $currency): ?>
                                            <option value="<?= $currency->id ?>" 
                                                <?= (old('default_currency', $settings['default_currency'] ?? '') == $currency->id) ? 'selected' : '' ?>>
                                                <?= $currency->currency_name ?> (<?= $currency->currency_code ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if (session('errors.default_currency')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.default_currency') ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="late_fee_percentage" class="form-label">Multa por Atraso</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="late_fee_percentage" name="late_fee_percentage" 
                                                   step="0.1" min="0" max="100" value="<?= old('late_fee_percentage', $settings['late_fee_percentage'] ?? 0) ?>">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="discount_early_payment" class="form-label">Desconto Pagamento Antecipado</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="discount_early_payment" name="discount_early_payment" 
                                                   step="0.1" min="0" max="100" value="<?= old('discount_early_payment', $settings['discount_early_payment'] ?? 0) ?>">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="payment_deadline_days" class="form-label">Dias para Vencimento</label>
                                <input type="number" class="form-control" id="payment_deadline_days" name="payment_deadline_days" 
                                       min="1" value="<?= old('payment_deadline_days', $settings['payment_deadline_days'] ?? 30) ?>">
                                <small class="text-muted">Número de dias após emissão da fatura</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="invoice_prefix" class="form-label">Prefixo da Fatura</label>
                                        <input type="text" class="form-control" id="invoice_prefix" name="payment_invoice_prefix" 
                                               value="<?= old('payment_invoice_prefix', $settings['payment_invoice_prefix'] ?? 'INV') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="receipt_prefix" class="form-label">Prefixo do Recibo</label>
                                        <input type="text" class="form-control" id="receipt_prefix" name="payment_receipt_prefix" 
                                               value="<?= old('payment_receipt_prefix', $settings['payment_receipt_prefix'] ?? 'REC') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="mb-3">Opções de Pagamento</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="enable_multiple_payments" name="payment_enable_multiple" value="1"
                                               <?= (old('payment_enable_multiple', $settings['payment_enable_multiple'] ?? 0) == 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="enable_multiple_payments">
                                            <i class="fas fa-check-circle text-success"></i> Permitir pagamentos múltiplos por fatura
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="enable_partial_payments" name="payment_enable_partial" value="1"
                                               <?= (old('payment_enable_partial', $settings['payment_enable_partial'] ?? 0) == 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="enable_partial_payments">
                                            <i class="fas fa-percent text-info"></i> Permitir pagamentos parciais
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="enable_fines" name="payment_enable_fines" value="1"
                                               <?= (old('payment_enable_fines', $settings['payment_enable_fines'] ?? 0) == 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="enable_fines">
                                            <i class="fas fa-exclamation-triangle text-warning"></i> Aplicar multas por atraso automaticamente
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-end">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Salvar Configurações de Propinas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- ==================== SYSTEM SETTINGS ==================== -->
    <div class="tab-pane fade" id="system" role="tabpanel">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-cog"></i> Configurações do Sistema
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informações do Sistema</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <th>Versão do Sistema:</th>
                                        <td><span class="badge bg-primary">1.0.0</span></td>
                                    </tr>
                                    <tr>
                                        <th>CodeIgniter:</th>
                                        <td><?= \CodeIgniter\CodeIgniter::CI_VERSION ?></td>
                                    </tr>
                                    <tr>
                                        <th>PHP:</th>
                                        <td><?= PHP_VERSION ?></td>
                                    </tr>
                                    <tr>
                                        <th>Banco de Dados:</th>
                                        <td>MySQL</td>
                                    </tr>
                                    <tr>
                                        <th>Ambiente:</th>
                                        <td><span class="badge bg-<?= ENVIRONMENT == 'production' ? 'success' : 'warning' ?>"><?= ENVIRONMENT ?></span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-tools"></i> Ações do Sistema</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="<?= site_url('admin/settings/clear-cache') ?>" class="btn btn-outline-warning" onclick="return confirm('Esta ação limpará todos os caches. Continuar?')">
                                        <i class="fas fa-trash"></i> Limpar Cache
                                    </a>
                                    <a href="<?= site_url('admin/settings/backup') ?>" class="btn btn-outline-danger" onclick="return confirm('Gerar backup do banco de dados?')">
                                        <i class="fas fa-database"></i> Backup do Banco de Dados
                                    </a>
                                    <button class="btn btn-outline-secondary" onclick="return confirm('Restaurar configurações padrão? Esta ação não pode ser desfeita.')">
                                        <i class="fas fa-undo"></i> Restaurar Padrões
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Atenção:</strong> Alterações nestas configurações podem afetar todo o sistema.
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="section-divider mt-4">
                    <i class="fas fa-chart-pie"></i>
                    <span>Estatísticas do Sistema</span>
                    <div class="section-divider-line"></div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card-stats">
                            <div class="value"><?= count($gradeLevels ?? []) ?></div>
                            <div class="label">Níveis de Ensino</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card-stats">
                            <div class="value"><?= count($disciplines ?? []) ?></div>
                            <div class="label">Disciplinas</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card-stats">
                            <div class="value"><?= count($feeTypes ?? []) ?></div>
                            <div class="label">Tipos de Taxas</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card-stats">
                            <div class="value"><?= count($currencies ?? []) ?></div>
                            <div class="label">Moedas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Preview logo before upload
document.getElementById('school_logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Verificar tamanho (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('O ficheiro é muito grande. Tamanho máximo: 2MB');
            this.value = '';
            return;
        }
        
        // Verificar tipo
        if (!file.type.match('image.*')) {
            alert('Por favor, selecione uma imagem válida (JPG, PNG, GIF)');
            this.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logoPreview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" id="previewImage">`;
            preview.classList.add('has-image');
        }
        reader.readAsDataURL(file);
    }
});

// Atualizar preview de assinatura em tempo real
document.getElementById('director_name')?.addEventListener('input', updateSignaturePreview);
document.getElementById('director_title')?.addEventListener('change', updateSignaturePreview);
document.getElementById('pedagogical_director_name')?.addEventListener('input', updateSignaturePreview);
document.getElementById('pedagogical_title')?.addEventListener('change', updateSignaturePreview);
document.getElementById('signature_title')?.addEventListener('input', updateSignaturePreview);

function updateSignaturePreview() {
    // Esta função pode ser implementada para atualizar a pré-visualização em tempo real
    console.log('Preview atualizado');
}

// Confirmação antes de sair com alterações não salvas
let formModified = false;

document.querySelectorAll('form').forEach(form => {
    form.addEventListener('change', () => {
        formModified = true;
    });
    
    form.addEventListener('submit', () => {
        formModified = false;
    });
});

window.addEventListener('beforeunload', (e) => {
    if (formModified) {
        e.preventDefault();
        e.returnValue = 'Existem alterações não salvas. Deseja realmente sair?';
    }
});
</script>
<?= $this->endSection() ?>