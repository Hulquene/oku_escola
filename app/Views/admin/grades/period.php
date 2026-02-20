<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Relatório por Período</h1>
            <p class="text-muted mb-0"><?= $semester->semester_name ?> - <?= $academicYear->year_name ?></p>
        </div>
        <div>
            <a href="<?= site_url('admin/grades/report') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/grades') ?>">Notas</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/grades/report') ?>">Relatórios</a></li>
            <li class="breadcrumb-item active">Por Período</li>
        </ol>
    </nav>
</div>

<!-- Cards de Resumo -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="text-white-50">Total Alunos</h6>
                <h2 class="mb-0"><?= $resumo['total_alunos'] ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="text-white-50">Aprovados</h6>
                <h2 class="mb-0"><?= $resumo['aprovados'] ?></h2>
                <small><?= $resumo['total_alunos'] > 0 ? round(($resumo['aprovados'] / $resumo['total_alunos']) * 100, 1) : 0 ?>%</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="text-white-50">Recurso</h6>
                <h2 class="mb-0"><?= $resumo['recurso'] ?></h2>
                <small><?= $resumo['total_alunos'] > 0 ? round(($resumo['recurso'] / $resumo['total_alunos']) * 100, 1) : 0 ?>%</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="text-white-50">Reprovados</h6>
                <h2 class="mb-0"><?= $resumo['reprovados'] ?></h2>
                <small><?= $resumo['total_alunos'] > 0 ? round(($resumo['reprovados'] / $resumo['total_alunos']) * 100, 1) : 0 ?>%</small>
            </div>
        </div>
    </div>
</div>

<!-- Relatório por Turma -->
<?php foreach ($relatorio as $dados): ?>
<div class="card mb-4">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-school me-2 text-primary"></i>
                <?= $dados['turma']->class_name ?> (<?= $dados['turma']->class_code ?>)
            </h5>
            <div>
                <span class="badge bg-info me-2">Total: <?= $dados['total_alunos'] ?></span>
                <span class="badge bg-success me-2">Aprovados: <?= $dados['aprovados'] ?></span>
                <span class="badge bg-warning me-2">Recurso: <?= $dados['recurso'] ?></span>
                <span class="badge bg-danger">Reprovados: <?= $dados['reprovados'] ?></span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Aluno</th>
                        <th>Nº Matrícula</th>
                        <th>Média</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dados['alunos'] as $aluno): ?>
                        <tr>
                            <td><?= $aluno['nome'] ?></td>
                            <td><?= $aluno['numero'] ?></td>
                            <td>
                                <?php if ($aluno['media']): ?>
                                    <span class="badge bg-<?= $aluno['media'] >= 10 ? 'success' : 'warning' ?> p-2">
                                        <?= number_format($aluno['media'], 1) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $statusClass = [
                                    'Aprovado' => 'success',
                                    'Recurso' => 'warning',
                                    'Reprovado' => 'danger',
                                    'Sem notas' => 'secondary'
                                ][$aluno['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $statusClass ?>"><?= $aluno['status'] ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?= $this->endSection() ?>