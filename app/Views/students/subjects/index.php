<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= $title ?? 'Minhas Disciplinas' ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Minhas Disciplinas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?= session('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Informação da Turma Atual -->
<?php if (isset($currentClass) && $currentClass): ?>
<div class="alert alert-info mb-4">
    <i class="fas fa-info-circle"></i> 
    Você está matriculado na turma <strong><?= $currentClass->class_name ?? '' ?></strong> - 
    <strong><?= $currentClass->level_name ?? '' ?></strong> - 
    Ano Letivo: <strong><?= $currentClass->year_name ?? date('Y') ?></strong> - 
    Turno: <strong><?= $currentClass->class_shift ?? 'N/A' ?></strong>
</div>
<?php endif; ?>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <i class="fas fa-filter"></i> Filtrar Disciplinas
        <a href="<?= site_url('students/subjects') ?>" class="btn btn-sm btn-light float-end">
            <i class="fas fa-undo"></i> Limpar Filtros
        </a>
    </div>
    <div class="card-body">
        <form action="<?= site_url('students/subjects') ?>" method="get" class="row g-3">
            <div class="col-md-4">
                <label for="status" class="form-label">Status da Disciplina</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Todas</option>
                    <option value="Em andamento" <?= ($currentFilters['status'] ?? '') == 'Em andamento' ? 'selected' : '' ?>>Em andamento</option>
                    <option value="Aprovado" <?= ($currentFilters['status'] ?? '') == 'Aprovado' ? 'selected' : '' ?>>Aprovado</option>
                    <option value="Reprovado" <?= ($currentFilters['status'] ?? '') == 'Reprovado' ? 'selected' : '' ?>>Reprovado</option>
                    <option value="Sem avaliações" <?= ($currentFilters['status'] ?? '') == 'Sem avaliações' ? 'selected' : '' ?>>Sem avaliações</option>
                </select>
            </div>
            
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Estatísticas Rápidas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Total Disciplinas</h5>
                <h2><?= $stats['total'] ?? 0 ?></h2>
                <small><?= $stats['total_workload'] ?? 0 ?>h carga horária</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Aprovadas</h5>
                <h2><?= $stats['approved'] ?? 0 ?></h2>
                <small><?= $stats['approval_rate'] ?? 0 ?>% taxa</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Reprovadas</h5>
                <h2><?= $stats['failed'] ?? 0 ?></h2>
                <small>Precisa melhorar</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Em Curso</h5>
                <h2><?= $stats['in_progress'] ?? 0 ?></h2>
                <small>Presença: <?= $stats['average_attendance'] ?? 0 ?>%</small>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Disciplinas -->
<div class="row">
    <?php if (!empty($subjects)): ?>
        <?php foreach ($subjects as $subject): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 subject-card">
                    <div class="card-header d-flex justify-content-between align-items-center"
                         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <h5 class="card-title mb-0"><?= esc($subject->discipline_name) ?></h5>
                        <span class="badge bg-<?= $subject->status_color ?? 'secondary' ?>"><?= $subject->status ?? 'N/A' ?></span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><i class="fas fa-code text-muted"></i> Código:</td>
                                    <td><strong><?= esc($subject->discipline_code ?? 'N/A') ?></strong></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-clock text-muted"></i> Carga Horária:</td>
                                    <td><strong><?= $subject->workload ?? $subject->workload_hours ?? 0 ?>h</strong></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-user text-muted"></i> Professor:</td>
                                    <td><strong><?= esc($subject->teacher_name ?? 'Não atribuído') ?></strong></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-layer-group text-muted"></i> Tipo:</td>
                                    <td><span class="badge bg-info"><?= esc($subject->discipline_type ?? 'Obrigatória') ?></span></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Nota Final:</span>
                                <strong class="<?= ($subject->final_grade ?? 0) >= ($subject->approval_grade ?? 10) ? 'text-success' : 'text-danger' ?>">
                                    <?= $subject->final_grade ? number_format($subject->final_grade, 1, ',', '.') : '--' ?>
                                </strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Avaliações:</span>
                                <strong><?= $subject->total_grades ?? $subject->total_assessments ?? 0 ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Presença:</span>
                                <strong><?= $subject->attendance_percentage ?? 0 ?>%</strong>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: <?= $subject->attendance_percentage ?? 0 ?>%" 
                                     aria-valuenow="<?= $subject->attendance_percentage ?? 0 ?>" 
                                     aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-2 text-muted small">
                            <i class="fas fa-info-circle"></i> 
                            Aprovação: ≥ <?= number_format($subject->approval_grade ?? 10, 1, ',', '.') ?>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="<?= site_url('students/subjects/details/' . ($subject->discipline_id ?? $subject->id)) ?>" 
                           class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-eye"></i> Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h5>Nenhuma disciplina encontrada</h5>
                <p class="mb-3">Não há disciplinas disponíveis com os filtros selecionados.</p>
                <a href="<?= site_url('students/subjects') ?>" class="btn btn-primary">
                    <i class="fas fa-undo"></i> Limpar Filtros
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Se não houver matrícula ativa -->
<?php if (empty($subjects) && !isset($currentClass)): ?>
<div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
            <h5>Matrícula não encontrada</h5>
            <p>Você não possui uma matrícula ativa para o ano letivo atual.</p>
            <p class="small">Entre em contato com a secretaria para regularizar sua situação.</p>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.subject-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.subject-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
.subject-card .card-header .badge {
    font-size: 0.8rem;
    padding: 0.5rem;
}
</style>

<?= $this->endSection() ?>