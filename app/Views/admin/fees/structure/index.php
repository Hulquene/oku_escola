<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#bulkModal">
                <i class="fas fa-layer-group"></i> Criação em Massa
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#structureModal">
                <i class="fas fa-plus-circle"></i> Nova Estrutura
            </button>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/fees') ?>">Propinas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Estrutura de Propinas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="academic_year" class="form-label">Ano Letivo</label>
                <select class="form-select" id="academic_year" name="academic_year">
                    <option value="">Selecione...</option>
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= $year->id ?>" <?= $currentYearId == $year->id ? 'selected' : '' ?>>
                                <?= $year->year_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-table"></i> Estrutura de Propinas
    </div>
    <div class="card-body">
        <?php if (!empty($structures)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nível</th>
                            <th>Tipo de Taxa</th>
                            <th>Categoria</th>
                            <th>Valor</th>
                            <th>Moeda</th>
                            <th>Dia Venc.</th>
                            <th>Obrigatório</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($structures as $item): ?>
                            <tr>
                                <td><?= $item->id ?></td>
                                <td><?= $item->level_name ?></td>
                                <td><?= $item->type_name ?></td>
                                <td><span class="badge bg-info"><?= $item->type_category ?></span></td>
                                <td class="text-end"><?= number_format($item->amount, 2, ',', '.') ?></td>
                                <td><?= $item->currency_code ?></td>
                                <td class="text-center"><?= $item->due_day ?></td>
                                <td class="text-center">
                                    <?php if ($item->is_mandatory): ?>
                                        <span class="badge bg-success">Sim</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Não</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item->is_active): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info edit-structure" 
                                            data-id="<?= $item->id ?>"
                                            data-academic="<?= $item->academic_year_id ?>"
                                            data-grade="<?= $item->grade_level_id ?>"
                                            data-fee-type="<?= $item->fee_type_id ?>"
                                            data-amount="<?= $item->amount ?>"
                                            data-currency="<?= $item->currency_id ?>"
                                            data-due-day="<?= $item->due_day ?>"
                                            data-description="<?= $item->description ?>"
                                            data-mandatory="<?= $item->is_mandatory ?>"
                                            data-active="<?= $item->is_active ?>"
                                            title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="<?= site_url('admin/fees/structure/delete/' . $item->id) ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Tem certeza que deseja eliminar esta estrutura?')"
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-table fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhuma estrutura de propina encontrada para o ano letivo selecionado.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#structureModal">
                    <i class="fas fa-plus-circle"></i> Criar Primeira Estrutura
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para Adicionar/Editar Estrutura -->
<div class="modal fade" id="structureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/fees/structure/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="structureId">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="structureModalTitle">Nova Estrutura de Propina</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="academic_year_id" class="form-label">Ano Letivo <span class="text-danger">*</span></label>
                        <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($academicYears)): ?>
                                <?php foreach ($academicYears as $year): ?>
                                    <option value="<?= $year->id ?>" <?= $currentYearId == $year->id ? 'selected' : '' ?>>
                                        <?= $year->year_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="grade_level_id" class="form-label">Nível de Ensino <span class="text-danger">*</span></label>
                        <select class="form-select" id="grade_level_id" name="grade_level_id" required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($gradeLevels)): ?>
                                <?php foreach ($gradeLevels as $level): ?>
                                    <option value="<?= $level->id ?>">
                                        <?= $level->level_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fee_type_id" class="form-label">Tipo de Taxa <span class="text-danger">*</span></label>
                        <select class="form-select" id="fee_type_id" name="fee_type_id" required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($feeTypes)): ?>
                                <?php foreach ($feeTypes as $type): ?>
                                    <option value="<?= $type->id ?>">
                                        <?= $type->type_name ?> (<?= $type->type_category ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Valor <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="amount" name="amount" 
                                           step="0.01" min="0" required>
                                    <span class="input-group-text">Kz</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency_id" class="form-label">Moeda</label>
                                <select class="form-select" id="currency_id" name="currency_id">
                                    <?php if (!empty($currencies)): ?>
                                        <?php foreach ($currencies as $currency): ?>
                                            <option value="<?= $currency->id ?>" <?= $currency->is_default ? 'selected' : '' ?>>
                                                <?= $currency->currency_code ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="due_day" class="form-label">Dia de Vencimento</label>
                                <input type="number" class="form-control" id="due_day" name="due_day" 
                                       min="1" max="31" value="10">
                                <small class="text-muted">Dia do mês para vencimento</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_mandatory" name="is_mandatory" value="1" checked>
                                    <label class="form-check-label" for="is_mandatory">
                                        Obrigatório
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Ativo</label>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Criação em Massa -->
<div class="modal fade" id="bulkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/fees/structure/bulk-create') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="modal-header">
                    <h5 class="modal-title">Criação em Massa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bulk_academic_year" class="form-label">Ano Letivo <span class="text-danger">*</span></label>
                        <select class="form-select" id="bulk_academic_year" name="academic_year_id" required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($academicYears)): ?>
                                <?php foreach ($academicYears as $year): ?>
                                    <option value="<?= $year->id ?>" <?= $currentYearId == $year->id ? 'selected' : '' ?>>
                                        <?= $year->year_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Níveis de Ensino <span class="text-danger">*</span></label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            <?php if (!empty($gradeLevels)): ?>
                                <?php foreach ($gradeLevels as $level): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="grade_level_ids[]" 
                                               value="<?= $level->id ?>"
                                               id="level_<?= $level->id ?>">
                                        <label class="form-check-label" for="level_<?= $level->id ?>">
                                            <?= $level->level_name ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tipos de Taxa <span class="text-danger">*</span></label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            <?php if (!empty($feeTypes)): ?>
                                <?php foreach ($feeTypes as $type): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="fee_type_ids[]" 
                                               value="<?= $type->id ?>"
                                               id="type_<?= $type->id ?>">
                                        <label class="form-check-label" for="type_<?= $type->id ?>">
                                            <?= $type->type_name ?> (<?= $type->type_category ?>)
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bulk_amount" class="form-label">Valor Padrão <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="bulk_amount" name="amount" 
                                   step="0.01" min="0" required>
                            <span class="input-group-text">Kz</span>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Criar Estruturas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Edit structure
    $('.edit-structure').click(function() {
        $('#structureModalTitle').text('Editar Estrutura de Propina');
        $('#structureId').val($(this).data('id'));
        $('#academic_year_id').val($(this).data('academic'));
        $('#grade_level_id').val($(this).data('grade'));
        $('#fee_type_id').val($(this).data('fee-type'));
        $('#amount').val($(this).data('amount'));
        $('#currency_id').val($(this).data('currency'));
        $('#due_day').val($(this).data('due-day'));
        $('#description').val($(this).data('description'));
        $('#is_mandatory').prop('checked', $(this).data('mandatory') == 1);
        $('#is_active').prop('checked', $(this).data('active') == 1);
        
        $('#structureModal').modal('show');
    });
    
    // Reset modal on new
    $('#structureModal').on('hidden.bs.modal', function() {
        if (!$('#structureId').val()) {
            $(this).find('form')[0].reset();
            $('#due_day').val('10');
            $('#is_mandatory').prop('checked', true);
            $('#is_active').prop('checked', true);
        }
    });
    
    // New structure button
    $('[data-bs-target="#structureModal"]').click(function() {
        $('#structureModalTitle').text('Nova Estrutura de Propina');
        $('#structureId').val('');
        $(this).find('form')[0].reset();
        $('#due_day').val('10');
        $('#is_mandatory').prop('checked', true);
        $('#is_active').prop('checked', true);
    });
    
    // Select all/none buttons for bulk modal
    $('#bulkModal').on('show.bs.modal', function() {
        // You could add "Select All" buttons here if needed
    });
});
</script>
<?= $this->endSection() ?>