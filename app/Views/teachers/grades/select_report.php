<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1>Selecionar Relatório de Notas</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/grades') ?>">Notas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Relatórios</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Select Class -->
<div class="card">
    <div class="card-header bg-success text-white">
        <i class="fas fa-chart-bar"></i> Selecione a Turma para o Relatório
    </div>
    <div class="card-body">
        <?php if (!empty($classes)): ?>
            <div class="row">
                <?php foreach ($classes as $class): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-success">
                            <div class="card-header bg-success text-white text-center py-3">
                                <h5 class="mb-0"><?= $class->class_name ?></h5>
                                <small><?= $class->class_code ?></small>
                            </div>
                            <div class="card-body text-center">
                                <i class="fas fa-school fa-4x text-success mb-3"></i>
                                <p class="text-muted">Clique abaixo para ver o relatório completo de notas desta turma.</p>
                            </div>
                            <div class="card-footer bg-white text-center">
                                <a href="<?= site_url('teachers/grades/report/' . $class->id) ?>" 
                                   class="btn btn-success w-100">
                                    <i class="fas fa-chart-bar"></i> Ver Relatório
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> Nenhuma turma encontrada.
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>