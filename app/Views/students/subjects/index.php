<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Minhas Disciplinas</li>
        </ol>
    </nav>
</div>

<!-- Informação da Turma Atual -->
<?php if (isset($currentClass)): ?>
<div class="alert alert-info mb-4">
    <i class="fas fa-info-circle"></i> 
    Você está matriculado na turma <strong><?= $currentClass->class_name ?></strong> - 
    <strong><?= $currentClass->level_name ?></strong> - 
    Ano Letivo: <strong><?= $currentClass->year_name ?></strong> - 
    Turno: <strong><?= $currentClass->class_shift ?></strong>
</div>
<?php endif; ?>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-filter"></i> Filtrar Disciplinas
        <a href="<?= site_url('students/subjects') ?>" class="btn btn-sm btn-outline-secondary float-end">
            <i class="fas fa-undo"></i> Limpar Filtros
        </a>
    </div>
    <div class="card-body">
        <form action="<?= site_url('students/subjects') ?>" method="get" class="row g-3">
            <div class="col-md-4">
                <label for="status" class="form-label">Status da Disciplina</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Todas</option>
                    <option value="Em curso" <?= ($currentFilters['status'] ?? '') == 'Em curso' ? 'selected' : '' ?>>Em curso</option>
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
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Total Disciplinas</h5>
                <h2><?= $stats['total'] ?></h2>
                <small><?= $stats['total_workload'] ?>h carga horária</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Aprovadas</h5>
                <h2><?= $stats['approved'] ?></h2>
                <small><?= $stats['approval_rate'] ?>%</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Reprovadas</h5>
                <h2><?= $stats['failed'] ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Em Curso</h5>
                <h2><?= $stats['in_progress'] ?></h2>
                <small>Presença: <?= $stats['average_attendance'] ?>%</small>
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><?= $subject->discipline_name ?></h5>
                        <span class="badge bg-<?= $subject->status_color ?>"><?= $subject->status ?></span>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <i class="fas fa-code text-muted"></i> Código: <?= $subject->discipline_code ?><br>
                            <i class="fas fa-clock text-muted"></i> Carga Horária: <?= $subject->workload ?>h<br>
                            <i class="fas fa-user text-muted"></i> Professor: <?= $subject->teacher_name ?? 'Não atribuído' ?><br>
                            <i class="fas fa-layer-group text-muted"></i> Tipo: <?= $subject->discipline_type ?>
                        </p>
                        
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Nota Final:</span>
                                <strong class="<?= $subject->final_grade && $subject->final_grade >= $subject->approval_grade ? 'text-success' : 'text-danger' ?>">
                                    <?= $subject->final_grade_formatted ?>
                                </strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Avaliações:</span>
                                <strong><?= $subject->total_assessments ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Presença:</span>
                                <strong><?= $subject->attendance_percentage ?>%</strong>
                            </div>
                            <div class="progress mb-2" style="height: 5px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: <?= $subject->attendance_percentage ?>%" 
                                     aria-valuenow="<?= $subject->attendance_percentage ?>" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-2 text-muted small">
                            <i class="fas fa-info-circle"></i> Aprovação: ≥ <?= number_format($subject->approval_grade, 1, ',', '.') ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="<?= site_url('students/subjects/details/' . $subject->discipline_id) ?>" 
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
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <h5>Nenhuma disciplina encontrada</h5>
                <p>Não há disciplinas disponíveis com os filtros selecionados.</p>
                <a href="<?= site_url('students/subjects') ?>" class="btn btn-primary">
                    <i class="fas fa-undo"></i> Limpar Filtros
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.subject-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.subject-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.subject-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.subject-card .card-header .badge {
    font-size: 0.8rem;
}
</style>

<?= $this->endSection() ?>