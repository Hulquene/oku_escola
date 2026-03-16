<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-graduation-cap me-2" style="opacity:.7;font-size:1.1rem;"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= route_to('admin.dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Anos Letivos</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= route_to('academic.years.form') ?>" class="hdr-btn primary">
                <i class="fas fa-plus-circle"></i> Novo Ano Letivo
            </a>
        </div>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- Data Table -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-list"></i> Lista de Anos Letivos
        </div>
    </div>
    <div style="overflow-x:auto; padding: 0 1.25rem 1.25rem;">
        <table id="yearsTable" class="ci-table" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ano Letivo</th>
                    <th>Início</th>
                    <th>Fim</th>
                    <th>Semestres</th>
                    <th>Matrículas</th>
                    <th>Estado</th>
                    <th class="text-center">Atual</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($years)): ?>
                    <?php foreach ($years as $year): ?>
                    <tr>
                        <td><span class="row-id"><?= $year['id'] ?></span></td>
                        <td><span class="year-name"><?= esc($year['year_name']) ?></span></td>
                        <td><span class="period-text"><?= date('d/m/Y', strtotime($year['start_date'])) ?></span></td>
                        <td><span class="period-text"><?= date('d/m/Y', strtotime($year['end_date'])) ?></span></td>
                        <td class="text-center">
                            <span class="count-chip neutral"><?= $year['total_semesters'] ?? 0 ?></span>
                        </td>
                        <td class="text-center">
                            <span class="count-chip <?= ($year['total_enrollments'] ?? 0) > 0 ? 'has' : 'neutral' ?>">
                                <?= $year['total_enrollments'] ?? 0 ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($year['is_active']): ?>
                                <span class="status-badge ativo"><span class="status-dot"></span> Ativo</span>
                            <?php else: ?>
                                <span class="status-badge inativo"><span class="status-dot"></span> Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($year['id'] == $currentYearId): ?>
                                <span class="current-badge"><i class="fas fa-star" style="font-size:.6rem;"></i> Atual</span>
                            <?php else: ?>
                                <a href="<?= route_to('academic.years.setCurrent', $year['id']) ?>" 
                                   class="btn-set-current" 
                                   title="Definir como atual"
                                   onclick="return confirm('Definir este ano como atual?')">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="action-group">
                                <a href="<?= route_to('academic.years.view', $year['id']) ?>" 
                                   class="row-btn view" 
                                   title="Ver Detalhes"
                                   data-bs-toggle="tooltip">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= route_to('academic.years.form.edit', $year['id']) ?>" 
                                   class="row-btn edit" 
                                   title="Editar"
                                   data-bs-toggle="tooltip">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($year['id'] != $currentYearId): ?>
                                    <a href="<?= route_to('academic.years.delete', $year['id']) ?>" 
                                       class="row-btn del" 
                                       title="Eliminar"
                                       data-bs-toggle="tooltip"
                                       onclick="return confirm('Tem certeza que deseja eliminar este ano letivo?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="row-btn disabled-btn" 
                                          title="Não pode eliminar o ano atual"
                                          data-bs-toggle="tooltip">
                                        <i class="fas fa-trash"></i>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h5>Nenhum ano letivo encontrado</h5>
                                <p>Clique em "Novo Ano Letivo" para começar.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function () {
    // Inicializar tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
<?= $this->endSection() ?>