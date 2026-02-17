<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $class->class_name ?> - <?= $class->discipline_name ?></h1>
        <a href="<?= site_url('teachers/classes') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/classes') ?>">Minhas Turmas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Alunos</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Class Info -->
<div class="alert alert-info mb-4">
    <i class="fas fa-info-circle"></i>
    <strong>Turma:</strong> <?= $class->class_name ?> | 
    <strong>Disciplina:</strong> <?= $class->discipline_name ?> | 
    <strong>Turno:</strong> <?= $class->class_shift ?>
</div>

<!-- Students Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-users"></i> Lista de Alunos
    </div>
    <div class="card-body">
        <?php if (!empty($students)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nº Matrícula</th>
                            <th>Nome</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= $student->student_number ?></td>
                                <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                                <td>
                                    <a href="<?= site_url('teachers/grades?student=' . $student->id) ?>" 
                                       class="btn btn-sm btn-info" title="Ver Notas">
                                        <i class="fas fa-star"></i>
                                    </a>
                                    <a href="<?= site_url('teachers/attendance?student=' . $student->id) ?>" 
                                       class="btn btn-sm btn-success" title="Ver Presenças">
                                        <i class="fas fa-calendar-check"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Nenhum aluno encontrado.</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>