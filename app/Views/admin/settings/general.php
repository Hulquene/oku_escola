<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Configurações Gerais</h1>
        <a href="<?= site_url('admin/settings') ?>" class="btn btn-info">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/settings') ?>">Configurações</a></li>
            <li class="breadcrumb-item active" aria-current="page">Configurações Gerais</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-sliders-h me-2"></i>Configurações Gerais</h5>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/settings/save-general') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nome da Aplicação <span class="text-danger">*</span></label>
                    <input type="text" name="app_name" class="form-control <?= session('errors.app_name') ? 'is-invalid' : '' ?>" 
                           value="<?= old('app_name', $settings['app_name'] ?? 'Sistema Escolar') ?>" required>
                    <?php if (session('errors.app_name')): ?>
                        <div class="invalid-feedback"><?= session('errors.app_name') ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Timezone <span class="text-danger">*</span></label>
                    <select name="app_timezone" class="form-select <?= session('errors.app_timezone') ? 'is-invalid' : '' ?>" required>
                        <?php foreach ($timezones as $key => $label): ?>
                            <option value="<?= $key ?>" <?= (old('app_timezone', $settings['app_timezone'] ?? 'Africa/Luanda') == $key) ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (session('errors.app_timezone')): ?>
                        <div class="invalid-feedback"><?= session('errors.app_timezone') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Formato de Data <span class="text-danger">*</span></label>
                    <select name="app_date_format" class="form-select <?= session('errors.app_date_format') ? 'is-invalid' : '' ?>" required>
                        <?php foreach ($dateFormats as $key => $label): ?>
                            <option value="<?= $key ?>" <?= (old('app_date_format', $settings['app_date_format'] ?? 'd/m/Y') == $key) ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (session('errors.app_date_format')): ?>
                        <div class="invalid-feedback"><?= session('errors.app_date_format') ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Formato de Hora</label>
                    <input type="text" name="app_time_format" class="form-control" 
                           value="<?= old('app_time_format', $settings['app_time_format'] ?? 'H:i') ?>" placeholder="Ex: H:i">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Locale</label>
                    <select name="app_locale" class="form-select">
                        <option value="pt" <?= (old('app_locale', $settings['app_locale'] ?? 'pt') == 'pt') ? 'selected' : '' ?>>Português</option>
                        <option value="en" <?= (old('app_locale', $settings['app_locale'] ?? 'pt') == 'en') ? 'selected' : '' ?>>English</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tema</label>
                    <select name="app_theme" class="form-select">
                        <option value="light" <?= (old('app_theme', $settings['app_theme'] ?? 'light') == 'light') ? 'selected' : '' ?>>Claro</option>
                        <option value="dark" <?= (old('app_theme', $settings['app_theme'] ?? 'light') == 'dark') ? 'selected' : '' ?>>Escuro</option>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Itens por Página</label>
                    <input type="number" name="items_per_page" class="form-control" min="5" max="100"
                           value="<?= old('items_per_page', $settings['items_per_page'] ?? 15) ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input type="hidden" name="enable_debug" value="0">
                        <input type="checkbox" name="enable_debug" class="form-check-input" id="enableDebug" value="1"
                               <?= (old('enable_debug', $settings['enable_debug'] ?? 0) == 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="enableDebug">Modo Debug</label>
                        <small class="text-muted d-block">Ativar modo de depuração para desenvolvimento</small>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input type="hidden" name="maintenance_mode" value="0">
                        <input type="checkbox" name="maintenance_mode" class="form-check-input" id="maintenanceMode" value="1"
                               <?= (old('maintenance_mode', $settings['maintenance_mode'] ?? 0) == 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="maintenanceMode">Modo Manutenção</label>
                        <small class="text-muted d-block">Bloquear acesso público ao sistema</small>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/settings') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Configurações
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>