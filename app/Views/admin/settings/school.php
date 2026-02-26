<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/settings') ?>" class="btn btn-info">
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
    <!-- General Settings -->
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
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="school_name" class="form-label">Nome da Escola <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= session('errors.school_name') ? 'is-invalid' : '' ?>" 
                                           id="school_name" name="school_name" 
                                           value="<?= old('school_name', $settings['school_name'] ?? '') ?>" required>
                                    <?php if (session('errors.school_name')): ?>
                                        <div class="invalid-feedback"><?= session('errors.school_name') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="school_acronym" class="form-label">Sigla</label>
                                    <input type="text" class="form-control" id="school_acronym" name="school_acronym" 
                                           value="<?= old('school_acronym', $settings['school_acronym'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="school_address" class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="school_address" name="school_address" 
                                       value="<?= old('school_address', $settings['school_address'] ?? '') ?>">
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="school_city" class="form-label">Cidade</label>
                                    <input type="text" class="form-control" id="school_city" name="school_city" 
                                           value="<?= old('school_city', $settings['school_city'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="school_province" class="form-label">Província</label>
                                    <input type="text" class="form-control" id="school_province" name="school_province" 
                                           value="<?= old('school_province', $settings['school_province'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="school_founding_year" class="form-label">Ano de Fundação</label>
                                    <input type="number" class="form-control" id="school_founding_year" name="school_founding_year" 
                                           value="<?= old('school_founding_year', $settings['school_founding_year'] ?? date('Y')) ?>">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="school_phone" class="form-label">Telefone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= session('errors.school_phone') ? 'is-invalid' : '' ?>" 
                                           id="school_phone" name="school_phone" 
                                           value="<?= old('school_phone', $settings['school_phone'] ?? '') ?>" required>
                                    <?php if (session('errors.school_phone')): ?>
                                        <div class="invalid-feedback"><?= session('errors.school_phone') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="school_alt_phone" class="form-label">Telefone Alternativo</label>
                                    <input type="text" class="form-control" id="school_alt_phone" name="school_alt_phone" 
                                           value="<?= old('school_alt_phone', $settings['school_alt_phone'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="school_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?= session('errors.school_email') ? 'is-invalid' : '' ?>" 
                                           id="school_email" name="school_email" 
                                           value="<?= old('school_email', $settings['school_email'] ?? '') ?>" required>
                                    <?php if (session('errors.school_email')): ?>
                                        <div class="invalid-feedback"><?= session('errors.school_email') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="school_website" class="form-label">Website</label>
                                    <input type="url" class="form-control" id="school_website" name="school_website" 
                                           value="<?= old('school_website', $settings['school_website'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="school_nif" class="form-label">NIF</label>
                                <input type="text" class="form-control" id="school_nif" name="school_nif" 
                                       value="<?= old('school_nif', $settings['school_nif'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <i class="fas fa-image"></i> Logo da Escola
                                </div>
                                <div class="card-body text-center">
                                    <?php if (!empty($settings['school_logo'])): ?>
                                        <img src="<?= base_url('uploads/school/' . $settings['school_logo']) ?>" 
                                             alt="Logo" 
                                             class="img-fluid mb-3"
                                             style="max-height: 150px;">
                                        <div class="mb-3">
                                            <a href="<?= site_url('admin/settings/remove-logo') ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Remover logo?')">
                                                <i class="fas fa-trash"></i> Remover
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center mb-3" 
                                             style="height: 150px; border: 2px dashed #ccc;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <label for="school_logo" class="form-label">Alterar Logo</label>
                                    <input type="file" class="form-control" id="school_logo" name="school_logo" 
                                           accept="image/*">
                                    <small class="text-muted">Formatos: JPG, PNG, GIF. Máx: 2MB</small>
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
    </div>
    
    <!-- Academic Settings -->
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
                                <label for="academic_year_id" class="form-label">Ano Letivo Atual <span class="text-danger">*</span></label>
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
                                <label for="semester_id" class="form-label">Semestre Atual <span class="text-danger">*</span></label>
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
                                <label for="grading_system" class="form-label">Sistema de Avaliação <span class="text-danger">*</span></label>
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
                                        <input type="number" class="form-control" id="min_grade" name="min_grade" 
                                               step="0.1" value="<?= old('min_grade', $settings['min_grade'] ?? 0) ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="max_grade" class="form-label">Nota Máxima</label>
                                        <input type="number" class="form-control" id="max_grade" name="max_grade" 
                                               step="0.1" value="<?= old('max_grade', $settings['max_grade'] ?? 20) ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="approval_grade" class="form-label">Nota de Aprovação</label>
                                        <input type="number" class="form-control" id="approval_grade" name="approval_grade" 
                                               step="0.1" value="<?= old('approval_grade', $settings['approval_grade'] ?? 10) ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="attendance_threshold" class="form-label">Percentagem Mínima de Presenças (%)</label>
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
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Configurações Académicas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Fees Settings -->
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
                                <label for="default_currency" class="form-label">Moeda Padrão <span class="text-danger">*</span></label>
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
                                        <label for="late_fee_percentage" class="form-label">Multa por Atraso (%)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="late_fee_percentage" name="late_fee_percentage" 
                                                   step="0.1" min="0" max="100" value="<?= old('late_fee_percentage', $settings['late_fee_percentage'] ?? 0) ?>">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="discount_early_payment" class="form-label">Desconto p/ Pagamento Antecipado (%)</label>
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
                                    <h6>Opções de Pagamento</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="enable_multiple_payments" name="payment_enable_multiple" value="1"
                                               <?= (old('payment_enable_multiple', $settings['payment_enable_multiple'] ?? 0) == 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="enable_multiple_payments">
                                            Permitir pagamentos múltiplos por fatura
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="enable_partial_payments" name="payment_enable_partial" value="1"
                                               <?= (old('payment_enable_partial', $settings['payment_enable_partial'] ?? 0) == 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="enable_partial_payments">
                                            Permitir pagamentos parciais
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="enable_fines" name="payment_enable_fines" value="1"
                                               <?= (old('payment_enable_fines', $settings['payment_enable_fines'] ?? 0) == 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="enable_fines">
                                            Aplicar multas por atraso automaticamente
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Configurações de Propinas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- System Settings -->
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
                                <h6 class="mb-0">Informações do Sistema</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <th>Versão do Sistema:</th>
                                        <td>1.0.0</td>
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
                                        <td><span class="badge bg-info"><?= ENVIRONMENT ?></span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Ações do Sistema</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="<?= site_url('admin/settings/clear-cache') ?>" class="btn btn-outline-warning" onclick="return confirm('Esta ação limpará todos os caches. Continuar?')">
                                        <i class="fas fa-trash"></i> Limpar Cache
                                    </a>
                                    <button class="btn btn-outline-danger" onclick="return confirm('Tem certeza? Esta ação não pode ser desfeita.')">
                                        <i class="fas fa-database"></i> Backup do Banco de Dados
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="return confirm('Restaurar configurações padrão?')">
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
                
                <!-- Statistics -->
                <div class="row mt-3">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6>Níveis de Ensino</h6>
                                <h3><?= count($gradeLevels) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6>Disciplinas</h6>
                                <h3><?= count($disciplines) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6>Tipos de Taxas</h6>
                                <h3><?= count($feeTypes) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body">
                                <h6>Moedas</h6>
                                <h3><?= count($currencies) ?></h3>
                            </div>
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
        const reader = new FileReader();
        reader.onload = function(e) {
            // You can add preview functionality here
            console.log('Logo selected:', file.name);
        }
        reader.readAsDataURL(file);
    }
});
</script>
<?= $this->endSection() ?>