<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Minhas Turmas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Classes Grid -->
<div class="row">
    <?php if (!empty($classes)): ?>
        <?php foreach ($classes as $class): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><?= $class->class_name ?></h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <i class="fas fa-book text-success"></i> 
                            <strong>Disciplina:</strong> <?= $class->discipline_name ?>
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-clock text-success"></i> 
                            <strong>Turno:</strong> <?= $class->class_shift ?>
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-door-open text-success"></i> 
                            <strong>Sala:</strong> <?= $class->class_room ?: 'Não definida' ?>
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-users text-success"></i> 
                            <strong>Alunos:</strong> 
                            <?php
                            $enrollmentModel = new \App\Models\EnrollmentModel();
                            echo $enrollmentModel
                                ->where('class_id', $class->class_id)
                                ->where('status', 'Ativo')
                                ->countAllResults();
                            ?>
                        </p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="<?= site_url('teachers/classes/students/' . $class->id) ?>" 
                           class="btn btn-sm btn-success w-100">
                            <i class="fas fa-users"></i> Ver Alunos
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> Nenhuma turma atribuída.
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>