<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Pauta Trimestral - <?= $class->class_name ?></h1>
        <div>
            <a href="<?= site_url('admin/mini-grade-sheet/exportTrimestral/' . $class->id . '?semester=' . $semester->id) ?>" 
               class="btn btn-success me-2">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
            <a href="<?= site_url('admin/mini-grade-sheet/trimestral') ?>" class="btn btn-info">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/mini-grade-sheet') ?>">Mini Pautas</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/mini-grade-sheet/trimestral') ?>">Pautas Trimestrais</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $class->class_name ?></li>
        </ol>
    </nav>
</div>

<!-- Informações -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informações</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Ano Letivo:</strong>
                        <p><?= $class->year_name ?></p>
                    </div>
                    <div class="col-md-3">
                        <strong>Turma:</strong>
                        <p><?= $class->class_name ?></p>
                    </div>
                    <div class="col-md-3">
                        <strong>Nível:</strong>
                        <p><?= $class->level_name ?></p>
                    </div>
                    <div class="col-md-3">
                        <strong>Trimestre:</strong>
                        <p><?= $semester->semester_name ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Alunos por Disciplina -->
<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-table"></i> Notas por Disciplina</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="align-middle text-center">Nº</th>
                        <th rowspan="2" class="align-middle">Nome do Aluno</th>
                        <th colspan="<?= count($disciplinas) ?>" class="text-center">Disciplinas</th>
                        <th rowspan="2" class="align-middle text-center">Média Geral</th>
                    </tr>
                    <tr>
                        <?php foreach ($disciplinas as $disc): ?>
                            <th class="text-center" title="<?= $disc->discipline_name ?>">
                                <?= $disc->discipline_code ?><br>
                                <small><?= $disc->teacher_first_name ? substr($disc->teacher_first_name, 0, 1) . '.' : '' ?></small>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($alunos)): ?>
                        <?php $counter = 1; ?>
                        <?php foreach ($alunos as $aluno): ?>
                            <tr>
                                <td class="text-center"><?= $counter++ ?></td>
                                <td>
                                    <?= $aluno->full_name ?>
                                    <br>
                                    <small class="text-muted">Nº: <?= $aluno->student_number ?></small>
                                </td>
                                
                                <?php foreach ($disciplinas as $disc): ?>
                                    <?php $nota = $aluno->notas[$disc->discipline_id] ?? '—'; ?>
                                    <td class="text-center <?= $nota !== '—' ? ($nota >= 10 ? 'table-success' : ($nota >= 7 ? 'table-warning' : 'table-danger')) : '' ?>">
                                        <strong><?= $nota ?></strong>
                                    </td>
                                <?php endforeach; ?>
                                
                                <td class="text-center">
                                    <strong><?= $aluno->media_geral ?></strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= 3 + count($disciplinas) ?>" class="text-center py-4">
                                <p class="text-muted">Nenhum aluno encontrado.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>