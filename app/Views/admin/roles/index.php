<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Perfis de Acesso</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-shield-alt"></i> Lista de Perfis
    </div>
    <div class="card-body">
        <table id="rolesTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Perfil</th>
                    <th>Descrição</th>
                    <th>Data Criação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($roles)): ?>
                    <?php foreach ($roles as $role): ?>
                        <tr>
                            <td><?= $role->id ?></td>
                            <td><strong><?= $role->role_name ?></strong></td>
                            <td><?= $role->role_description ?: '-' ?></td>
                            <td><?= date('d/m/Y', strtotime($role->created_at)) ?></td>
                            <td>
                                <a href="<?= site_url('admin/roles/permission/' . $role->id) ?>" 
                                  class="btn btn-sm btn-info" title="Gerir Permissões">
                                    <i class="fas fa-key"></i> Permissões
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Nenhum perfil encontrado</td>
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
    $('#rolesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc']]
    });
});
</script>
<?= $this->endSection() ?>