<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/teachers/form-add') ?>" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Novo Professor
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Professores</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-chalkboard-teacher"></i> Lista de Professores
    </div>
    <div class="card-body">
        <table id="teachersTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Turmas</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($teachers)): ?>
                    <?php foreach ($teachers as $teacher): ?>
                        <tr>
                            <td><?= $teacher->id ?></td>
                            <td>
                                <?php if ($teacher->photo): ?>
                                    <img src="<?= base_url('uploads/teachers/' . $teacher->photo) ?>" 
                                         alt="Foto" 
                                         class="rounded-circle"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center text-white"
                                         style="width: 40px; height: 40px;">
                                        <?= strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= $teacher->first_name ?> <?= $teacher->last_name ?></td>
                            <td><?= $teacher->email ?></td>
                            <td><?= $teacher->phone ?: '-' ?></td>
                            <td>
                                <?php
                                $classDisciplineModel = new \App\Models\ClassDisciplineModel();
                                $totalClasses = $classDisciplineModel
                                    ->where('teacher_id', $teacher->id)
                                    ->countAllResults();
                                ?>
                                <span class="badge bg-info"><?= $totalClasses ?> turmas</span>
                            </td>
                            <td>
                                <?php if ($teacher->is_active): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('admin/teachers/view/' . $teacher->id) ?>" 
                                   class="btn btn-sm btn-success" title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= site_url('admin/teachers/form-edit/' . $teacher->id) ?>" 
                                   class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= site_url('admin/teachers/assign-class/' . $teacher->id) ?>" 
                                   class="btn btn-sm btn-primary" title="Atribuir Turmas">
                                    <i class="fas fa-tasks"></i>
                                </a>
                                <?php if ($teacher->is_active): ?>
                                    <a href="<?= site_url('admin/teachers/delete/' . $teacher->id) ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Tem certeza que deseja desativar este professor?')"
                                       title="Desativar">
                                        <i class="fas fa-user-slash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Nenhum professor encontrado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?= $pager->links() ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#teachersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[2, 'asc']],
        pageLength: 25,
        searching: false
    });
});
</script>
<?= $this->endSection() ?>