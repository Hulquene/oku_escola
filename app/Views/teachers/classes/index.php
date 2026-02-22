<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Minhas Turmas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total de Turmas</h6>
                        <h2 class="mb-0"><?= count(array_unique(array_column($classes, 'class_id'))) ?></h2>
                    </div>
                    <i class="fas fa-school fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Disciplinas</h6>
                        <h2 class="mb-0"><?= count($classes) ?></h2>
                    </div>
                    <i class="fas fa-book fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Alunos</h6>
                        <h2 class="mb-0"><?= $totalStudents ?? 0 ?></h2>
                    </div>
                    <i class="fas fa-users fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Carga Horária</h6>
                        <h2 class="mb-0"><?= $totalWorkload ?? 0 ?>h</h2>
                    </div>
                    <i class="fas fa-clock fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Minhas Turmas -->
<div class="row">
    <?php 
    $enrollmentModel = new \App\Models\EnrollmentModel();
    $processedClasses = [];
    
    foreach ($classes as $class):
        $classId = $class->class_id;
        
        if (!isset($processedClasses[$classId])):
            $processedClasses[$classId] = true;
            
            // Contar alunos da turma
            $studentCount = $enrollmentModel
                ->where('class_id', $classId)
                ->where('status', 'Ativo')
                ->countAllResults();
            
            // Contar disciplinas da turma
            $disciplineCount = array_count_values(array_column(
                array_filter($classes, fn($c) => $c->class_id == $classId),
                'discipline_id'
            ));
            $disciplineCount = count($disciplineCount);
    ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><?= $class->class_name ?></h5>
                    <small><?= $class->class_code ?></small>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-info me-1"><?= $class->class_shift ?></span>
                        <span class="badge bg-secondary">Sala: <?= $class->class_room ?: 'N/A' ?></span>
                    </div>
                    
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <h3 class="text-success"><?= $disciplineCount ?></h3>
                            <small class="text-muted">Disciplinas</small>
                        </div>
                        <div class="col-6">
                            <h3 class="text-primary"><?= $studentCount ?></h3>
                            <small class="text-muted">Alunos</small>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6 class="text-muted"><i class="fas fa-book-open"></i> Disciplinas:</h6>
                        <div class="list-group list-group-flush">
                            <?php 
                            $disciplines = array_filter($classes, fn($c) => $c->class_id == $classId);
                            foreach ($disciplines as $disc): 
                            ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= $disc->discipline_name ?></strong>
                                            <br>
                                            <small class="text-muted">Cód: <?= $disc->discipline_code ?></small>
                                        </div>
                                        <div>
                                            <span class="badge bg-info"><?= $disc->workload_hours ?>h</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="btn-group w-100">
                        <a href="<?= site_url('teachers/classes/students/' . $classId) ?>" 
                           class="btn btn-sm btn-success">
                            <i class="fas fa-users"></i> Alunos
                        </a>
                        <a href="<?= site_url('teachers/attendance?class_id=' . $classId) ?>" 
                           class="btn btn-sm btn-info">
                            <i class="fas fa-calendar-check"></i> Presenças
                        </a>
                        <a href="<?= site_url('teachers/grades?class=' . $classId) ?>" 
                           class="btn btn-sm btn-warning">
                            <i class="fas fa-star"></i> Notas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php 
        endif;
    endforeach; 
    ?>
</div>

<!-- Tabela Detalhada (opcional) -->
<?php if (!empty($classes)): ?>
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-table"></i> Detalhamento de Disciplinas
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Turma</th>
                                <th>Disciplina</th>
                                <th>Código</th>
                                <th>Carga Horária</th>
                                <th>Turno</th>
                                <th>Sala</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($classes as $class): ?>
                            <tr>
                                <td><?= $class->class_name ?></td>
                                <td><?= $class->discipline_name ?></td>
                                <td><span class="badge bg-info"><?= $class->discipline_code ?></span></td>
                                <td><?= $class->workload_hours ?? 'N/A' ?>h</td>
                                <td><?= $class->class_shift ?></td>
                                <td><?= $class->class_room ?: '-' ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= site_url('teachers/grades?class=' . $class->class_id . '&discipline=' . $class->discipline_id) ?>" 
                                           class="btn btn-sm btn-outline-success" 
                                           title="Lançar Notas">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a href="<?= site_url('teachers/attendance?class_id=' . $class->class_id . '&discipline_id=' . $class->discipline_id) ?>" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Registrar Presenças">
                                            <i class="fas fa-calendar-check"></i>
                                        </a>
                                        <a href="<?= site_url('teachers/exams?class=' . $class->class_id . '&discipline=' . $class->discipline_id) ?>" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Ver Exames">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>