<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/classes/classes/form-edit/' . $class->id) ?>" class="btn btn-info">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="<?= site_url('admin/classes/classes/list-students/' . $class->id) ?>" class="btn btn-primary">
                <i class="fas fa-users"></i> Ver Alunos
            </a>
            <a href="<?= site_url('admin/classes/classes') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes/classes') ?>">Turmas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Class Info Card -->
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Informações da Turma
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">ID</th>
                        <td><?= $class->id ?></td>
                    </tr>
                    <tr>
                        <th>Nome da Turma</th>
                        <td><?= $class->class_name ?></td>
                    </tr>
                    <tr>
                        <th>Código</th>
                        <td><span class="badge bg-info"><?= $class->class_code ?></span></td>
                    </tr>
                    <tr>
                        <th>Nível de Ensino</th>
                        <td><?= $class->level_name ?></td>
                    </tr>
                    <tr>
                        <th>Ano Letivo</th>
                        <td><?= $class->year_name ?></td>
                    </tr>
                    <tr>
                        <th>Turno</th>
                        <td><?= $class->class_shift ?></td>
                    </tr>
                    <tr>
                        <th>Sala</th>
                        <td><?= $class->class_room ?: '-' ?></td>
                    </tr>
                    <tr>
                        <th>Capacidade</th>
                        <td><?= $class->capacity ?> alunos</td>
                    </tr>
                    <tr>
                        <th>Vagas Disponíveis</th>
                        <td>
                            <span class="badge bg-<?= $availableSeats > 0 ? 'success' : 'danger' ?>">
                                <?= $availableSeats ?> vagas
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Professor Responsável</th>
                        <td>
                            <?php if ($class->class_teacher_id): ?>
                                <?= $class->teacher_first_name ?> <?= $class->teacher_last_name ?>
                                <br>
                                <small class="text-muted"><?= $class->teacher_email ?></small>
                            <?php else: ?>
                                <span class="text-muted">Não atribuído</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php if ($class->is_active): ?>
                                <span class="badge bg-success">Ativa</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inativa</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Criado em</th>
                        <td><?= date('d/m/Y H:i', strtotime($class->created_at)) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- Disciplines Card -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-book"></i> Disciplinas da Turma
            </div>
            <div class="card-body">
                <?php
                $disciplineModel = new \App\Models\DisciplineModel();
                $disciplines = $disciplineModel->getByClass($class->id);
                ?>
                
                <?php if (!empty($disciplines)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Disciplina</th>
                                    <th>Código</th>
                                    <th>Professor</th>
                                    <th>Carga Horária</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($disciplines as $discipline): ?>
                                    <tr>
                                        <td><?= $discipline->discipline_name ?></td>
                                        <td><span class="badge bg-secondary"><?= $discipline->discipline_code ?></span></td>
                                        <td>
                                            <?php if ($discipline->teacher_id): ?>
                                                <?php
                                                $teacherModel = new \App\Models\UserModel();
                                                $teacher = $teacherModel->find($discipline->teacher_id);
                                                echo $teacher ? $teacher->first_name . ' ' . $teacher->last_name : '-';
                                                ?>
                                            <?php else: ?>
                                                <span class="text-muted">Não atribuído</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $discipline->workload_hours ?: '-' ?> h</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Nenhuma disciplina atribuída a esta turma.</p>
                <?php endif; ?>
                
                <div class="mt-3">
                    <a href="<?= site_url('admin/classes/class-subjects?class=' . $class->id) ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-cog"></i> Gerenciar Disciplinas
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Statistics Card -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-pie"></i> Estatísticas
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h3><?= count($students) ?></h3>
                            <small class="text-muted">Alunos Matriculados</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h3><?= count($disciplines) ?></h3>
                            <small class="text-muted">Disciplinas</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <h3><?= $availableSeats ?></h3>
                            <small class="text-muted">Vagas Disponíveis</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <h3><?= $class->capacity ?></h3>
                            <small class="text-muted">Capacidade Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>