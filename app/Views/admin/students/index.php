<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/students/form-add') ?>" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Novo Aluno
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Alunos</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Search Box -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" name="search" 
                           placeholder="Buscar por nome, número de matrícula, email ou documento..." 
                           value="<?= $search ?? '' ?>">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-user-graduate"></i> Lista de Alunos
    </div>
    <div class="card-body">
        <table id="studentsTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nº Matrícula</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Contacto</th>
                    <th>Data Nasc.</th>
                    <th>Gênero</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= $student->id ?></td>
                            <td><span class="badge bg-info"><?= $student->student_number ?></span></td>
                            <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                            <td><?= $student->email ?></td>
                            <td><?= $student->phone ?: '-' ?></td>
                            <td><?= date('d/m/Y', strtotime($student->birth_date)) ?></td>
                            <td><?= $student->gender ?></td>
                            <td>
                                <?php if ($student->is_active): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('admin/students/view/' . $student->id) ?>" 
                                   class="btn btn-sm btn-success" title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= site_url('admin/students/form-edit/' . $student->id) ?>" 
                                   class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= site_url('admin/students/enrollments/history/' . $student->id) ?>" 
                                   class="btn btn-sm btn-primary" title="Histórico">
                                    <i class="fas fa-history"></i>
                                </a>
                                <?php if ($student->is_active): ?>
                                    <a href="<?= site_url('admin/students/delete/' . $student->id) ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Tem certeza que deseja eliminar este aluno?')"
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Nenhum aluno encontrado</td>
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
    $('#studentsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[2, 'asc']],
        pageLength: 25,
        searching: false // Desabilitar search do DataTable pois já temos o filtro externo
    });
});
</script>
<?= $this->endSection() ?>