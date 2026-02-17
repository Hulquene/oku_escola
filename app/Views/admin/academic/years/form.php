<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/academic/years') ?>">Anos Letivos</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Form -->
<div class="card">
    <div class="card-header <?= $year ? 'bg-info text-white' : 'bg-primary text-white' ?>">
        <i class="fas fa-<?= $year ? 'edit' : 'plus-circle' ?>"></i> <?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/academic/years/save') ?>" method="post" id="yearForm">
            <?= csrf_field() ?>
            
            <?php if ($year): ?>
                <input type="hidden" name="id" value="<?= $year->id ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="year_name" class="form-label">Nome do Ano Letivo <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= session('errors.year_name') ? 'is-invalid' : '' ?>" 
                               id="year_name" 
                               name="year_name" 
                               value="<?= old('year_name', $year->year_name ?? '') ?>"
                               placeholder="Ex: 2024"
                               required
                               maxlength="50">
                        <?php if (session('errors.year_name')): ?>
                            <div class="invalid-feedback"><?= session('errors.year_name') ?></div>
                        <?php endif; ?>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Ex: 2024, 2024/2025, 2024-2025
                        </small>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Data de Início <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control <?= session('errors.start_date') ? 'is-invalid' : '' ?>" 
                               id="start_date" 
                               name="start_date" 
                               value="<?= old('start_date', $year->start_date ?? '') ?>"
                               required>
                        <?php if (session('errors.start_date')): ?>
                            <div class="invalid-feedback"><?= session('errors.start_date') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Data de Fim <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control <?= session('errors.end_date') ? 'is-invalid' : '' ?>" 
                               id="end_date" 
                               name="end_date" 
                               value="<?= old('end_date', $year->end_date ?? '') ?>"
                               required>
                        <?php if (session('errors.end_date')): ?>
                            <div class="invalid-feedback"><?= session('errors.end_date') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   <?= (old('is_active', $year->is_active ?? true)) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">
                                <i class="fas fa-toggle-on text-success"></i> Ano Letivo Ativo
                            </label>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-archive"></i> 
                            Desative para arquivar este ano letivo
                        </small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <?php if (!$year): ?>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_current" 
                                       name="is_current" 
                                       value="1">
                                <label class="form-check-label" for="is_current">
                                    <i class="fas fa-star text-warning"></i> Definir como Ano Letivo Atual
                                </label>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Marque se este for o ano letivo corrente
                            </small>
                        </div>
                    <?php else: ?>
                        <?php if ($year->is_current): ?>
                            <div class="alert alert-info py-2">
                                <i class="fas fa-star text-warning"></i> 
                                <strong>Este é o ano letivo atual</strong>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Informações adicionais quando em edição -->
            <?php if ($year): ?>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="alert alert-light border">
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Criado em:</small>
                                <strong><?= date('d/m/Y H:i', strtotime($year->created_at)) ?></strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Última atualização:</small>
                                <strong><?= $year->updated_at ? date('d/m/Y H:i', strtotime($year->updated_at)) : 'Nunca' ?></strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Status:</small>
                                <?php if ($year->is_active): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativo</span>
                                <?php endif; ?>
                                
                                <?php if ($year->is_current): ?>
                                    <span class="badge bg-warning text-dark">Ano Atual</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/academic/years') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-<?= $year ? 'info' : 'primary' ?>">
                    <i class="fas fa-save"></i> <?= $year ? 'Atualizar' : 'Salvar' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Validação de datas
document.getElementById('end_date').addEventListener('change', function() {
    const startDate = document.getElementById('start_date').value;
    const endDate = this.value;
    
    if (startDate && endDate && endDate < startDate) {
        alert('A data de fim não pode ser anterior à data de início');
        this.value = '';
    }
});

document.getElementById('start_date').addEventListener('change', function() {
    const endDate = document.getElementById('end_date').value;
    const startDate = this.value;
    
    if (endDate && startDate && endDate < startDate) {
        alert('A data de início não pode ser posterior à data de fim');
        this.value = '';
    }
});

// Validação do formulário antes de enviar
document.getElementById('yearForm').addEventListener('submit', function(e) {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (startDate && endDate && endDate < startDate) {
        e.preventDefault();
        alert('A data de fim não pode ser anterior à data de início');
        return false;
    }
    
    return true;
});

// Auto-formatação do nome do ano (opcional)
document.getElementById('year_name').addEventListener('blur', function() {
    let value = this.value.trim();
    if (value && !value.match(/^\d{4}$/) && !value.match(/^\d{4}\/\d{4}$/)) {
        // Não faz nada, apenas permite qualquer formato
    }
});
</script>
<?= $this->endSection() ?>