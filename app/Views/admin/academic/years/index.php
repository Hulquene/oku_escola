<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/academic/years/form-add') ?>" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Novo Ano Letivo
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Anos Letivos</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i> Lista de Anos Letivos
    </div>
    <div class="card-body">
        <table id="yearsTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ano Letivo</th>
                    <th>Data Início</th>
                    <th>Data Fim</th>
                    <th>Status</th>
                    <th>Atual</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($years)): ?>
                    <?php foreach ($years as $year): ?>
                        <tr>
                            <td><?= $year->id ?></td>
                            <td><?= $year->year_name ?></td>
                            <td><?= date('d/m/Y', strtotime($year->start_date)) ?></td>
                            <td><?= date('d/m/Y', strtotime($year->end_date)) ?></td>
                            <td>
                                <?php if ($year->is_active): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($year->is_current): ?>
                                    <span class="badge bg-primary">Atual</span>
                                <?php else: ?>
                                    <a href="<?= site_url('admin/academic/years/set-current/' . $year->id) ?>" 
                                       class="btn btn-sm btn-outline-primary"
                                       onclick="return confirm('Definir este ano como atual?')">
                                        <i class="fas fa-check-circle"></i> Definir como Atual
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('admin/academic/years/form-edit/' . $year->id) ?>" 
                                   class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= site_url('admin/academic/years/view/' . $year->id) ?>" 
                                   class="btn btn-sm btn-success" title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (!$year->is_current): ?>
                                    <a href="<?= site_url('admin/academic/years/delete/' . $year->id) ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Tem certeza que deseja eliminar este ano letivo?')"
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Nenhum ano letivo encontrado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#yearsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'desc']]
    });
});
</script>
<?= $this->endSection() ?>