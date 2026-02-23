<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">Visualize os detalhes do ano letivo</p>
        </div>
        <div>
            <a href="<?= site_url('admin/academic/years') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
            <?php if (has_permission('settings.academic_years')): ?>
                <a href="<?= site_url('admin/academic/years/form/' . $year->id) ?>" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
            <?php endif; ?>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/academic/years') ?>">Anos Letivos</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $year->year_name ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações do Ano Letivo -->
<div class="row">
    <div class="col-md-8">
        <!-- Card Principal -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i>
                    Informações Gerais
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted d-block mb-1">Nome do Ano Letivo</label>
                        <h4><?= $year->year_name ?></h4>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted d-block mb-1">Status</label>
                        <div>
                            <?php if ($year->is_current): ?>
                                <span class="badge bg-success me-2">Atual</span>
                            <?php endif; ?>
                            <?php if ($year->is_active): ?>
                                <span class="badge bg-primary">Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inativo</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted d-block mb-1">Data de Início</label>
                        <p class="h5">
                            <i class="far fa-calendar-check me-2 text-success"></i>
                            <?= date('d/m/Y', strtotime($year->start_date)) ?>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted d-block mb-1">Data de Fim</label>
                        <p class="h5">
                            <i class="far fa-calendar-times me-2 text-danger"></i>
                            <?= date('d/m/Y', strtotime($year->end_date)) ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white">
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    Criado em: <?= date('d/m/Y H:i', strtotime($year->created_at)) ?>
                    <?php if ($year->updated_at && $year->updated_at != $year->created_at): ?>
                        | Última atualização: <?= date('d/m/Y H:i', strtotime($year->updated_at)) ?>
                    <?php endif; ?>
                </small>
            </div>
        </div>
        
        <!-- Semestres/Trimestres -->
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-layer-group me-2 text-info"></i>
                    Semestres / Trimestres
                </h5>
                <?php if (has_permission('settings.semesters')): ?>
                    <a href="<?= site_url('admin/academic/semesters/form?year_id=' . $year->id) ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i> Novo Semestre
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!empty($semesters)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Tipo</th>
                                    <th>Período</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($semesters as $semester): ?>
                                    <tr>
                                        <td><?= $semester->semester_name ?></td>
                                        <td><?= $semester->semester_type ?></td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($semester->start_date)) ?> - 
                                            <?= date('d/m/Y', strtotime($semester->end_date)) ?>
                                        </td>
                                        <td>
                                            <?php if ($semester->is_current): ?>
                                                <span class="badge bg-success">Atual</span>
                                            <?php endif; ?>
                                            <span class="badge bg-<?= $semester->status == 'ativo' ? 'primary' : 'secondary' ?>">
                                                <?= ucfirst($semester->status) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= site_url('admin/academic/semesters/view/' . $semester->id) ?>" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Nenhum semestre cadastrado para este ano letivo.
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Turmas -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2 text-success"></i>
                    Turmas
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($classes)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Turma</th>
                                    <th>Código</th>
                                    <th>Turno</th>
                                    <th>Alunos</th>
                                    <th>Capacidade</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($classes as $class): ?>
                                    <tr>
                                        <td><?= $class->class_name ?></td>
                                        <td><?= $class->class_code ?></td>
                                        <td><?= $class->class_shift ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= $class->student_count ?? 0 ?> alunos
                                            </span>
                                        </td>
                                        <td><?= $class->capacity ?? 'N/A' ?></td>
                                        <td>
                                            <?php if ($class->is_active): ?>
                                                <span class="badge bg-success">Ativo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inativo</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Nenhuma turma cadastrada para este ano letivo.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Sidebar com Estatísticas -->
    <div class="col-md-4">
        <!-- Cards de Estatísticas -->
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total de Matrículas</h6>
                        <h2 class="mb-0"><?= $total_enrollments ?></h2>
                    </div>
                    <i class="fas fa-user-graduate fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
        
        <div class="card bg-success text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Matrículas Ativas</h6>
                        <h2 class="mb-0"><?= $active_enrollments ?></h2>
                    </div>
                    <i class="fas fa-check-circle fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
        
        <div class="card bg-info text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total de Turmas</h6>
                        <h2 class="mb-0"><?= count($classes) ?></h2>
                    </div>
                    <i class="fas fa-door-open fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
        
        <div class="card bg-warning text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Semestres</h6>
                        <h2 class="mb-0"><?= count($semesters) ?></h2>
                    </div>
                    <i class="fas fa-layer-group fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
        
        <!-- Ações Rápidas -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2 text-warning"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if (!$year->is_current && has_permission('settings.academic_years')): ?>
                        <a href="<?= site_url('admin/academic/years/setCurrent/' . $year->id) ?>" 
                           class="btn btn-outline-primary"
                           onclick="return confirm('Tem certeza que deseja definir <?= $year->year_name ?> como ano letivo atual?')">
                            <i class="fas fa-star me-2"></i> Definir como Atual
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?= site_url('admin/reports/academic?year_id=' . $year->id) ?>" 
                       class="btn btn-outline-success">
                        <i class="fas fa-chart-bar me-2"></i> Gerar Relatório
                    </a>
                    
                    <!-- Botão Desativar - CORRIGIDO -->
                    <?php if (!$year->is_current && $year->is_active && has_permission('settings.academic_years')): ?>
                        <form action="<?= site_url('admin/academic/years/toggle-active/' . $year->id) ?>" 
                              method="post" 
                              onsubmit="return confirm('ATENÇÃO: Desativar este ano letivo irá afetar todas as turmas, matrículas e semestres associados. Tem certeza que deseja desativar <?= $year->year_name ?>?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-warning w-100">
                                <i class="fas fa-ban me-2"></i> Desativar Ano Letivo
                            </button>
                        </form>
                    <?php elseif ($year->is_current): ?>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            O ano letivo atual não pode ser desativado.
                        </div>
                    <?php elseif (!$year->is_active): ?>
                        <div class="alert alert-secondary mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Este ano letivo já está desativado.
                        </div>
                    <?php endif; ?>
                    
                    <!-- Botão Ativar (se estiver inativo) -->
                    <?php if (!$year->is_active && has_permission('settings.academic_years')): ?>
                        <form action="<?= site_url('admin/academic/years/toggle-active/' . $year->id) ?>" 
                              method="post" 
                              class="mt-2"
                              onsubmit="return confirm('Tem certeza que deseja ativar <?= $year->year_name ?>?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-success w-100">
                                <i class="fas fa-check me-2"></i> Ativar Ano Letivo
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>