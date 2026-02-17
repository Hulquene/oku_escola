<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/teachers/form-edit/' . $teacher->id) ?>" class="btn btn-info">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="<?= site_url('admin/teachers/assign-class/' . $teacher->id) ?>" class="btn btn-primary">
                <i class="fas fa-tasks"></i> Atribuir Turmas
            </a>
            <a href="<?= site_url('admin/teachers') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/teachers') ?>">Professores</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes do Professor</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row">
    <!-- Foto e Informações Básicas -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chalkboard-teacher"></i> Foto do Professor
            </div>
            <div class="card-body text-center">
                <?php if ($teacher->photo): ?>
                    <img src="<?= base_url('uploads/teachers/' . $teacher->photo) ?>" 
                         alt="Foto do Professor" 
                         class="img-fluid rounded-circle mb-3"
                         style="width: 150px; height: 150px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-3" 
                         style="width: 150px; height: 150px; font-size: 3rem;">
                        <?= strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) ?>
                    </div>
                <?php endif; ?>
                
                <h4><?= $teacher->first_name ?> <?= $teacher->last_name ?></h4>
                <p class="text-muted"><?= $teacher->email ?></p>
                
                <div class="mt-3">
                    <?php if ($teacher->is_active): ?>
                        <span class="badge bg-success p-2">Ativo</span>
                    <?php else: ?>
                        <span class="badge bg-danger p-2">Inativo</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Contactos -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-address-book"></i> Contactos
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th><i class="fas fa-envelope"></i> Email:</th>
                        <td><?= $teacher->email ?></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-phone"></i> Telefone:</th>
                        <td><?= $teacher->phone ?: '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Informações Detalhadas -->
    <div class="col-md-8">
        <!-- Informações Pessoais -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-id-card"></i> Informações Pessoais
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th>Nome Completo:</th>
                                <td><?= $teacher->first_name ?> <?= $teacher->last_name ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?= $teacher->email ?></td>
                            </tr>
                            <tr>
                                <th>Telefone:</th>
                                <td><?= $teacher->phone ?: '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th>Endereço:</th>
                                <td><?= $teacher->address ?: '-' ?></td>
                            </tr>
                            <tr>
                                <th>Data de Cadastro:</th>
                                <td><?= date('d/m/Y', strtotime($teacher->created_at)) ?></td>
                            </tr>
                            <tr>
                                <th>Último Acesso:</th>
                                <td><?= $teacher->last_login ? date('d/m/Y H:i', strtotime($teacher->last_login)) : '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Turmas e Disciplinas -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-school"></i> Turmas e Disciplinas</span>
                    <a href="<?= site_url('admin/teachers/assign-class/' . $teacher->id) ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle"></i> Atribuir
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($assignments)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Turma</th>
                                    <th>Disciplina</th>
                                    <th>Carga Horária</th>
                                    <th>Semestre</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignments as $assignment): ?>
                                    <tr>
                                        <td><?= $assignment->class_name ?></td>
                                        <td><?= $assignment->discipline_name ?></td>
                                        <td><?= $assignment->workload_hours ?: '-' ?> h</td>
                                        <td>
                                            <?php
                                            if ($assignment->semester_id) {
                                                $semesterModel = new \App\Models\SemesterModel();
                                                $semester = $semesterModel->find($assignment->semester_id);
                                                echo $semester ? $semester->semester_name : '-';
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($assignment->is_active): ?>
                                                <span class="badge bg-success">Ativo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inativo</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Total de <strong><?= count($assignments) ?></strong> atribuições.
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-school fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Este professor ainda não tem turmas atribuídas.</p>
                        <a href="<?= site_url('admin/teachers/assign-class/' . $teacher->id) ?>" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Atribuir Turmas
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Estatísticas -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-pie"></i> Estatísticas
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h3 class="text-primary"><?= count($assignments) ?></h3>
                            <small class="text-muted">Turmas/Disciplinas</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h3 class="text-success">
                                <?php
                                $totalStudents = 0;
                                foreach ($assignments as $assignment) {
                                    $enrollmentModel = new \App\Models\EnrollmentModel();
                                    $totalStudents += $enrollmentModel
                                        ->where('class_id', $assignment->class_id)
                                        ->where('status', 'Ativo')
                                        ->countAllResults();
                                }
                                echo $totalStudents;
                                ?>
                            </h3>
                            <small class="text-muted">Total de Alunos</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h3 class="text-info">
                                <?php
                                $totalHours = 0;
                                foreach ($assignments as $assignment) {
                                    $totalHours += $assignment->workload_hours ?: 0;
                                }
                                echo $totalHours . 'h';
                                ?>
                            </h3>
                            <small class="text-muted">Carga Horária Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>