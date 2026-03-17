<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-file-alt me-2"></i>Pauta Trimestral - <?= $class['class_name'] ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/mini-grade-sheet') ?>">Mini Pautas</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/mini-grade-sheet/trimestral') ?>">Pautas Trimestrais</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $class['class_name'] ?></li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/mini-grade-sheet/exportTrimestral/' . $class['id'] . '?semester=' . $semester['id']) ?>" 
               class="hdr-btn success">
                <i class="fas fa-file-excel me-1"></i> Exportar Excel
            </a>
            <a href="<?= site_url('admin/mini-grade-sheet/trimestral') ?>" class="hdr-btn info">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <div class="mt-2">
        <span class="badge-ci info">
            <i class="fas fa-calendar-alt me-1"></i>
            <?= $semester['semester_name'] ?> • <?= $class['year_name'] ?>
        </span>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações da Pauta -->
<div class="ci-card mb-4">
    <div class="ci-card-header" style="background: var(--primary);">
        <div class="ci-card-title" style="color: #fff;">
            <i class="fas fa-info-circle me-2"></i>
            <span>Informações da Pauta</span>
        </div>
    </div>
    <div class="ci-card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="info-label">Ano Letivo</div>
                <div class="info-value fw-semibold"><?= $class['year_name'] ?></div>
            </div>
            <div class="col-md-3">
                <div class="info-label">Turma</div>
                <div class="info-value fw-semibold"><?= $class['class_name'] ?></div>
            </div>
            <div class="col-md-3">
                <div class="info-label">Nível</div>
                <div class="info-value fw-semibold"><?= $class['level_name'] ?></div>
            </div>
            <div class="col-md-3">
                <div class="info-label">Trimestre</div>
                <div class="info-value fw-semibold"><?= $semester['semester_name'] ?></div>
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
                            <th class="text-center" title="<?= $disc['discipline_name'] ?>">
                                <?= $disc['discipline_code'] ?><br>
                                <small><?= $disc['teacher_first_name'] ? substr($disc['teacher_first_name'], 0, 1) . '.' : '' ?></small>
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
                                    <?= $aluno['full_name'] ?>
                                    <br>
                                    <small class="text-muted">Nº: <?= $aluno['student_number'] ?></small>
                                </td>
                                
                                <?php foreach ($disciplinas as $disc): ?>
                                    <?php $nota = $aluno['notas'][$disc['discipline_id']] ?? '—'; ?>
                                    <td class="text-center <?= $nota !== '—' ? ($nota >= 10 ? 'table-success' : ($nota >= 7 ? 'table-warning' : 'table-danger')) : '' ?>">
                                        <strong><?= $nota ?></strong>
                                    </td>
                                <?php endforeach; ?>
                                
                                <td class="text-center">
                                    <strong><?= $aluno['media_geral'] ?></strong>
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