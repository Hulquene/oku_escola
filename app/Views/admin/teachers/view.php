<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/teachers/schedule/' . $teacher->id) ?>" class="btn btn-success me-2">
                <i class="fas fa-calendar-alt"></i> Ver Horário
            </a>
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
            <div class="card-header bg-info text-white">
                <i class="fas fa-id-card"></i> Informações Pessoais
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 120px;">Nome Completo:</th>
                                <td><strong><?= $teacher->first_name ?> <?= $teacher->last_name ?></strong></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?= $teacher->email ?></td>
                            </tr>
                            <tr>
                                <th>Telefone:</th>
                                <td><?= $teacher->phone ?: '-' ?></td>
                            </tr>
                            <tr>
                                <th>Gênero:</th>
                                <td><?= $teacher->gender ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Data Nasc.:</th>
                                <td><?= $teacher->birth_date ? date('d/m/Y', strtotime($teacher->birth_date)) : '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 120px;">Nacionalidade:</th>
                                <td><?= $teacher->nationality ?? 'Angolana' ?></td>
                            </tr>
                            <tr>
                                <th>Documento:</th>
                                <td><?= $teacher->identity_type ?? 'BI' ?>: <?= $teacher->identity_document ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>NIF:</th>
                                <td><?= $teacher->nif ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Endereço:</th>
                                <td><?= $teacher->address ?: '-' ?></td>
                            </tr>
                            <tr>
                                <th>Localidade:</th>
                                <td><?= $teacher->city ?: '-' ?>/<?= $teacher->municipality ?: '-' ?>/<?= $teacher->province ?: '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informações Profissionais -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <i class="fas fa-graduation-cap"></i> Informações Profissionais
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 120px;">Habilitações:</th>
                                <td><?= $teacher->qualifications ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Especialização:</th>
                                <td><?= $teacher->specialization ?? '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 120px;">Admissão:</th>
                                <td><?= $teacher->admission_date ? date('d/m/Y', strtotime($teacher->admission_date)) : '-' ?></td>
                            </tr>
                            <tr>
                                <th>Contacto Emerg.:</th>
                                <td><?= $teacher->emergency_contact ?: '-' ?> (<?= $teacher->emergency_contact_name ?: '-' ?>)</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informações Bancárias -->
        <?php if ($teacher->bank_name || $teacher->bank_account): ?>
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <i class="fas fa-university"></i> Informações Bancárias
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 120px;">Banco:</th>
                                <td><?= $teacher->bank_name ?? '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 120px;">Nº Conta:</th>
                                <td><?= $teacher->bank_account ?? '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Sistema -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <i class="fas fa-clock"></i> Informações do Sistema
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 120px;">Cadastrado em:</th>
                                <td><?= isset($teacher->user_created_at) ? date('d/m/Y H:i', strtotime($teacher->user_created_at)) : date('d/m/Y H:i', strtotime($teacher->created_at)) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 120px;">Último Acesso:</th>
                                <td>
                                    <?php if (isset($teacher->last_login) && $teacher->last_login): ?>
                                        <?= date('d/m/Y H:i', strtotime($teacher->last_login)) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Nunca acedeu</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Turmas e Disciplinas - ORGANIZADO POR ANO LETIVO E CURSO -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-school"></i> Turmas e Disciplinas</span>
                    <div>
                        <a href="<?= site_url('admin/teachers/schedule/' . $teacher->id) ?>" class="btn btn-sm btn-light me-2">
                            <i class="fas fa-calendar-alt"></i> Ver Horário
                        </a>
                        <a href="<?= site_url('admin/teachers/assign-class/' . $teacher->id) ?>" class="btn btn-sm btn-light">
                            <i class="fas fa-plus-circle"></i> Atribuir
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($assignmentsByYear)): ?>
                    
                    <!-- Navegação por abas de anos letivos -->
                    <ul class="nav nav-tabs mb-3" id="yearTabs" role="tablist">
                        <?php 
                        $yearCount = 0;
                        foreach ($assignmentsByYear as $yearId => $yearData): 
                            $activeClass = ($yearCount === 0) ? 'active' : '';
                            $yearCount++;
                        ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?= $activeClass ?>" 
                                        id="year-<?= $yearId ?>-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#year-<?= $yearId ?>" 
                                        type="button" 
                                        role="tab">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?= $yearData['year_name'] ?>
                                    <span class="badge bg-secondary ms-1"><?= $yearData['total'] ?></span>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <!-- Conteúdo das abas -->
                    <div class="tab-content" id="yearTabsContent">
                        <?php 
                        $yearCount = 0;
                        foreach ($assignmentsByYear as $yearId => $yearData): 
                            $activeClass = ($yearCount === 0) ? 'show active' : '';
                            $yearCount++;
                        ?>
                            <div class="tab-pane fade <?= $activeClass ?>" 
                                 id="year-<?= $yearId ?>" 
                                 role="tabpanel">
                                
                                <?php if (!empty($yearData['byCourse'])): ?>
                                    <?php foreach ($yearData['byCourse'] as $courseId => $courseData): ?>
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0">
                                                        <?php if ($courseId > 0): ?>
                                                            <i class="fas fa-graduation-cap text-primary me-2"></i>
                                                            <strong><?= $courseData['course_name'] ?></strong>
                                                            <small class="text-muted ms-2">(<?= $courseData['course_code'] ?>)</small>
                                                        <?php else: ?>
                                                            <i class="fas fa-school text-secondary me-2"></i>
                                                            <strong>Ensino Geral</strong>
                                                        <?php endif; ?>
                                                    </h6>
                                                    <span class="badge bg-info"><?= count($courseData['items']) ?> disciplinas</span>
                                                </div>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Turma</th>
                                                                <th>Código</th>
                                                                <th>Disciplina</th>
                                                                <th>Carga Horária</th>
                                                                <th>Turno</th>
                                                                <th>Sala</th>
                                                                <th>Semestre</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($courseData['items'] as $item): ?>
                                                                <tr>
                                                                    <td><strong><?= $item->class_name ?></strong></td>
                                                                    <td><span class="badge bg-secondary"><?= $item->class_code ?></span></td>
                                                                    <td>
                                                                        <?= $item->discipline_name ?>
                                                                        <br>
                                                                        <small class="text-muted"><?= $item->discipline_code ?></small>
                                                                    </td>
                                                                    <td><?= $item->workload_hours ?: '-' ?> h</td>
                                                                    <td><?= $item->class_shift ?></td>
                                                                    <td><?= $item->class_room ?: '-' ?></td>
                                                                    <td>
                                                                        <?php if ($item->semester_name): ?>
                                                                            <span class="badge bg-info"><?= $item->semester_name ?></span>
                                                                        <?php else: ?>
                                                                            <span class="badge bg-secondary">Anual</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php if ($item->is_active): ?>
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
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <!-- Resumo do ano -->
                                    <div class="alert alert-info mt-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Total de Turmas</small>
                                                <strong><?= $yearData['total_classes'] ?></strong>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Total de Disciplinas</small>
                                                <strong><?= $yearData['total'] ?></strong>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Carga Horária Total</small>
                                                <strong><?= $yearData['total_workload'] ?>h</strong>
                                            </div>
                                        </div>
                                    </div>
                                    
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-school fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Nenhuma disciplina atribuída neste ano letivo.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
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
        
        <!-- Estatísticas Gerais -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <i class="fas fa-chart-pie"></i> Estatísticas Gerais
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-light">
                            <h3 class="text-primary"><?= $totalAssignments ?? 0 ?></h3>
                            <small class="text-muted">Total Atribuições</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-light">
                            <h3 class="text-success"><?= $totalClasses ?? 0 ?></h3>
                            <small class="text-muted">Turmas</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-light">
                            <h3 class="text-warning"><?= $totalWorkload ?? 0 ?>h</h3>
                            <small class="text-muted">Carga Horária</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-light">
                            <h3 class="text-info"><?= $totalStudents ?? 0 ?></h3>
                            <small class="text-muted">Alunos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
<?= $this->endSection() ?>