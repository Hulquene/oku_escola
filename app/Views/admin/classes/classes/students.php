<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/students/enrollments/form-add?class=' . $class->id) ?>" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Adicionar Aluno
            </a>
            <a href="<?= site_url('admin/classes/classes/view/' . $class->id) ?>" class="btn btn-info">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes/classes') ?>">Turmas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Alunos da Turma</li>
        </ol>
    </nav>
</div>

<!-- Class Info -->
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> 
    <strong>Turma:</strong> <?= $class->class_name ?> (<?= $class->class_code ?>) | 
    <strong>Ano Letivo:</strong> <?= $class->year_name ?> | 
    <strong>Turno:</strong> <?= $class->class_shift ?> | 
    <strong>Vagas:</strong> <?= count($students) ?>/<?= $class->capacity ?>
</div>

<!-- Students List -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-users"></i> Lista de Alunos Matriculados
    </div>
    <div class="card-body">
        <table id="studentsTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Nº Matrícula</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Contacto</th>
                    <th>Data Matrícula</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?= $student->student_number ?></span></td>
                            <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                            <td><?= $student->email ?></td>
                            <td><?= $student->phone ?: '-' ?></td>
                            <td><?= date('d/m/Y', strtotime($student->enrollment_date)) ?></td>
                            <td>
                                <span class="badge bg-success">Ativo</span>
                            </td>
                            <td>
                                <a href="<?= site_url('admin/students/view/' . $student->student_id) ?>" 
                                   class="btn btn-sm btn-info" title="Ver Aluno">
                                    <i class="fas fa-user-graduate me-1"></i>
                                </a>
                                <a href="<?= site_url('admin/students/enrollments/view/' . $student->id) ?>" 
                                   class="btn btn-sm btn-info" title="Ver Matricula">
                                    <i class="fas fa-file-signature"></i>
                                </a>
                                <a href="<?= site_url('admin/students/enrollments/history/' . $student->student_id) ?>" 
                                   class="btn btn-sm btn-primary" title="Histórico">
                                    <i class="fas fa-history"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            Nenhum aluno matriculado nesta turma.
                            <br>
                            <a href="<?= site_url('admin/students/enrollments/form-add?class=' . $class->id) ?>" 
                               class="btn btn-sm btn-success mt-2">
                                <i class="fas fa-user-plus"></i> Matricular Aluno
                            </a>
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
$(document).ready(function() {
    $('#studentsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc']]
    });
});
</script>
<?= $this->endSection() ?>