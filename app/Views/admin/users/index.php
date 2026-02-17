<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/users/form-add') ?>" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Novo Utilizador
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Utilizadores</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-users"></i> Lista de Utilizadores
    </div>
    <div class="card-body">
        <table id="usersTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Nome</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Perfil</th>
                    <th>Tipo</th>
                    <th>Último Acesso</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user->id ?></td>
                            <td>
                                <?php if ($user->photo): ?>
                                    <img src="<?= base_url('uploads/users/' . $user->photo) ?>" 
                                         alt="Foto" 
                                         class="rounded-circle"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center text-white"
                                         style="width: 40px; height: 40px;">
                                        <?= strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= $user->first_name ?> <?= $user->last_name ?></td>
                            <td><?= $user->username ?></td>
                            <td><?= $user->email ?></td>
                            <td><span class="badge bg-info"><?= $user->role_name ?></span></td>
                            <td>
                                <?php
                                $typeClass = [
                                    'admin' => 'danger',
                                    'teacher' => 'success',
                                    'student' => 'primary',
                                    'guardian' => 'warning',
                                    'staff' => 'secondary'
                                ][$user->user_type] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $typeClass ?>"><?= ucfirst($user->user_type) ?></span>
                            </td>
                            <td><?= $user->last_login ? date('d/m/Y H:i', strtotime($user->last_login)) : '-' ?></td>
                            <td>
                                <?php if ($user->is_active): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('admin/users/view/' . $user->id) ?>" 
                                   class="btn btn-sm btn-success" title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= site_url('admin/users/form-edit/' . $user->id) ?>" 
                                   class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($user->id != session()->get('user_id')): ?>
                                    <a href="<?= site_url('admin/users/delete/' . $user->id) ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Tem certeza que deseja eliminar este utilizador?')"
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">Nenhum utilizador encontrado</td>
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
    $('#usersTable').DataTable({
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