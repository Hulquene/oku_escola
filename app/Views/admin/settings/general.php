<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/settings') ?>">Configurações</a></li>
            <li class="breadcrumb-item active" aria-current="page">Gerais</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- General Settings -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-sliders-h"></i> Configurações Gerais
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/settings/save-general') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="app_name" class="form-label">Nome da Aplicação <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="app_name" name="app_name" 
                               value="<?= $settings['app_name'] ?? 'Sistema Escolar' ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="app_timezone" class="form-label">Fuso Horário <span class="text-danger">*</span></label>
                        <select class="form-select" id="app_timezone" name="app_timezone" required>
                            <option value="Africa/Luanda" <?= ($settings['app_timezone'] ?? 'Africa/Luanda') == 'Africa/Luanda' ? 'selected' : '' ?>>Africa/Luanda (Angola)</option>
                            <option value="Africa/Lagos" <?= ($settings['app_timezone'] ?? '') == 'Africa/Lagos' ? 'selected' : '' ?>>Africa/Lagos</option>
                            <option value="Africa/Johannesburg" <?= ($settings['app_timezone'] ?? '') == 'Africa/Johannesburg' ? 'selected' : '' ?>>Africa/Johannesburg</option>
                            <option value="UTC" <?= ($settings['app_timezone'] ?? '') == 'UTC' ? 'selected' : '' ?>>UTC</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="app_date_format" class="form-label">Formato de Data <span class="text-danger">*</span></label>
                        <select class="form-select" id="app_date_format" name="app_date_format" required>
                            <option value="d/m/Y" <?= ($settings['app_date_format'] ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : '' ?>>31/12/2024</option>
                            <option value="Y-m-d" <?= ($settings['app_date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' ?>>2024-12-31</option>
                            <option value="d/m/y" <?= ($settings['app_date_format'] ?? '') == 'd/m/y' ? 'selected' : '' ?>>31/12/24</option>
                            <option value="d M Y" <?= ($settings['app_date_format'] ?? '') == 'd M Y' ? 'selected' : '' ?>>31 Dez 2024</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="app_time_format" class="form-label">Formato de Hora</label>
                        <select class="form-select" id="app_time_format" name="app_time_format">
                            <option value="H:i" <?= ($settings['app_time_format'] ?? 'H:i') == 'H:i' ? 'selected' : '' ?>>14:30 (24h)</option>
                            <option value="h:i A" <?= ($settings['app_time_format'] ?? '') == 'h:i A' ? 'selected' : '' ?>>02:30 PM (12h)</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="app_locale" class="form-label">Idioma</label>
                        <select class="form-select" id="app_locale" name="app_locale">
                            <option value="pt" <?= ($settings['app_locale'] ?? 'pt') == 'pt' ? 'selected' : '' ?>>Português</option>
                            <option value="en" <?= ($settings['app_locale'] ?? '') == 'en' ? 'selected' : '' ?>>English</option>
                            <option value="es" <?= ($settings['app_locale'] ?? '') == 'es' ? 'selected' : '' ?>>Español</option>
                            <option value="fr" <?= ($settings['app_locale'] ?? '') == 'fr' ? 'selected' : '' ?>>Français</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="app_theme" class="form-label">Tema</label>
                        <select class="form-select" id="app_theme" name="app_theme">
                            <option value="light" <?= ($settings['app_theme'] ?? 'light') == 'light' ? 'selected' : '' ?>>Claro</option>
                            <option value="dark" <?= ($settings['app_theme'] ?? '') == 'dark' ? 'selected' : '' ?>>Escuro</option>
                            <option value="auto" <?= ($settings['app_theme'] ?? '') == 'auto' ? 'selected' : '' ?>>Automático</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="items_per_page" class="form-label">Itens por Página</label>
                        <input type="number" class="form-control" id="items_per_page" name="items_per_page" 
                               min="5" max="100" value="<?= $settings['items_per_page'] ?? 15 ?>">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label d-block">Opções</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enable_debug" name="enable_debug" value="1"
                                   <?= ($settings['enable_debug'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="enable_debug">Modo Debug</label>
                        </div>
                        
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1"
                                   <?= ($settings['maintenance_mode'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="maintenance_mode">Modo Manutenção</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Configurações Gerais
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Company Settings -->
<div class="card" id="company">
    <div class="card-header">
        <i class="fas fa-building"></i> Dados da Empresa
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/settings/save-company') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="company_name" class="form-label">Nome da Empresa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="company_name" name="company_name" 
                               value="<?= $settings['company_name'] ?? '' ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="company_nif" class="form-label">NIF</label>
                        <input type="text" class="form-control" id="company_nif" name="company_nif" 
                               value="<?= $settings['company_nif'] ?? '' ?>">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="company_address" class="form-label">Endereço</label>
                <input type="text" class="form-control" id="company_address" name="company_address" 
                       value="<?= $settings['company_address'] ?? '' ?>">
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="company_city" class="form-label">Cidade</label>
                        <input type="text" class="form-control" id="company_city" name="company_city" 
                               value="<?= $settings['company_city'] ?? '' ?>">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="company_province" class="form-label">Província</label>
                        <input type="text" class="form-control" id="company_province" name="company_province" 
                               value="<?= $settings['company_province'] ?? '' ?>">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="company_phone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="company_phone" name="company_phone" 
                               value="<?= $settings['company_phone'] ?? '' ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="company_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="company_email" name="company_email" 
                               value="<?= $settings['company_email'] ?? '' ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="company_website" class="form-label">Website</label>
                        <input type="url" class="form-control" id="company_website" name="company_website" 
                               value="<?= $settings['company_website'] ?? '' ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="company_logo_file" class="form-label">Logo da Empresa</label>
                        <input type="file" class="form-control" id="company_logo_file" name="company_logo_file" accept="image/*">
                        <small class="text-muted">Formatos: JPG, PNG, GIF. Máx: 2MB</small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <?php if (!empty($settings['company_logo'])): ?>
                        <div class="text-center">
                            <img src="<?= base_url('uploads/company/' . $settings['company_logo']) ?>" 
                                 alt="Logo" class="img-fluid" style="max-height: 60px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <hr>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Dados da Empresa
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>