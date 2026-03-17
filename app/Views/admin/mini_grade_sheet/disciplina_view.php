<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-book-open me-2"></i>Pauta da Disciplina: <?= $discipline['discipline_name'] ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/mini-grade-sheet') ?>">Mini Pautas</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/mini-grade-sheet/disciplina') ?>">Pautas por Disciplina</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $discipline['discipline_code'] ?></li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/mini-grade-sheet/exportDisciplina/' . $class['id'] . '/' . $discipline['id']) ?>" 
               class="hdr-btn success">
                <i class="fas fa-file-excel me-1"></i> Exportar Excel
            </a>
            <a href="<?= site_url('admin/mini-grade-sheet/disciplina') ?>" class="hdr-btn info">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
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
                <div class="info-label">Disciplina</div>
                <div class="info-value fw-semibold"><?= $discipline['discipline_name'] ?> <span class="code-badge ms-1"><?= $discipline['discipline_code'] ?></span></div>
            </div>
        </div>
        <div class="row g-3 mt-2">
            <div class="col-md-6">
                <div class="info-label">Professor</div>
                <div class="info-value fw-semibold">
                    <?php if (!empty($teacher['first_name']) && !empty($teacher['last_name'])): ?>
                        <i class="fas fa-chalkboard-teacher text-accent me-1"></i>
                        <?= $teacher['first_name'] . ' ' . $teacher['last_name'] ?>
                    <?php else: ?>
                        <span class="badge-ci secondary">Não atribuído</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-label">Total de Alunos</div>
                <div class="info-value fw-semibold">
                    <span class="badge-ci primary"><?= count($alunos) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mini Pauta da Disciplina -->
<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-file-alt"></i> Mini Pauta - <?= $discipline['discipline_name'] ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="align-middle text-center" width="50">Nº</th>
                        <th rowspan="2" class="align-middle">Nome do Aluno</th>
                        <th rowspan="2" class="align-middle text-center" width="80">Nº Processo</th>
                        <th colspan="4" class="text-center">1º TRIMESTRE</th>
                        <th colspan="4" class="text-center">2º TRIMESTRE</th>
                        <th colspan="4" class="text-center">3º TRIMESTRE</th>
                        <th rowspan="2" class="align-middle text-center" width="60">MDF</th>
                        <th rowspan="2" class="align-middle text-center" width="100">Situação</th>
                    </tr>
                    <tr>
                        <th class="text-center" width="50">MAC</th>
                        <th class="text-center" width="50">NPP</th>
                        <th class="text-center" width="50">NPT</th>
                        <th class="text-center" width="50">MT</th>
                        <th class="text-center" width="50">MAC</th>
                        <th class="text-center" width="50">NPP</th>
                        <th class="text-center" width="50">NPT</th>
                        <th class="text-center" width="50">MT</th>
                        <th class="text-center" width="50">MAC</th>
                        <th class="text-center" width="50">NPP</th>
                        <th class="text-center" width="50">NPT</th>
                        <th class="text-center" width="50">MT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($alunos)): ?>
                        <?php $counter = 1; ?>
                        <?php foreach ($alunos as $aluno): ?>
                            <?php $mediasAluno = $medias[$aluno['enrollment_id']] ?? []; ?>
                            <tr>
                                <td class="text-center"><?= $counter++ ?></td>
                                <td>
                                    <strong><?= $aluno['full_name'] ?? $aluno['first_name'] . ' ' . $aluno['last_name'] ?></strong>
                                </td>
                                <td class="text-center"><?= $aluno['student_number'] ?? '—' ?></td>
                                
                                <!-- 1º Trimestre -->
                                <td class="text-center"><?= $mediasAluno['trimestres'][1]['AC'] ?? '—' ?></td>
                                <td class="text-center"><?= $mediasAluno['trimestres'][1]['NPP'] ?? '—' ?></td>
                                <td class="text-center"><?= $mediasAluno['trimestres'][1]['NPT'] ?? '—' ?></td>
                                <td class="text-center bg-light">
                                    <strong><?= $mediasAluno['trimestres'][1]['MT'] ?? '—' ?></strong>
                                </td>
                                
                                <!-- 2º Trimestre -->
                                <td class="text-center"><?= $mediasAluno['trimestres'][2]['AC'] ?? '—' ?></td>
                                <td class="text-center"><?= $mediasAluno['trimestres'][2]['NPP'] ?? '—' ?></td>
                                <td class="text-center"><?= $mediasAluno['trimestres'][2]['NPT'] ?? '—' ?></td>
                                <td class="text-center bg-light">
                                    <strong><?= $mediasAluno['trimestres'][2]['MT'] ?? '—' ?></strong>
                                </td>
                                
                                <!-- 3º Trimestre -->
                                <td class="text-center"><?= $mediasAluno['trimestres'][3]['AC'] ?? '—' ?></td>
                                <td class="text-center"><?= $mediasAluno['trimestres'][3]['NPP'] ?? '—' ?></td>
                                <td class="text-center"><?= $mediasAluno['trimestres'][3]['NPT'] ?? '—' ?></td>
                                <td class="text-center bg-light">
                                    <strong><?= $mediasAluno['trimestres'][3]['MT'] ?? '—' ?></strong>
                                </td>
                                
                                <!-- MDF -->
                                <td class="text-center">
                                    <?php if (($mediasAluno['MDF'] ?? '—') !== '—'): ?>
                                        <strong class="<?= $mediasAluno['MDF'] >= 10 ? 'text-success' : ($mediasAluno['MDF'] >= 7 ? 'text-warning' : 'text-danger') ?>">
                                            <?= $mediasAluno['MDF'] ?>
                                        </strong>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Situação -->
                                <td class="text-center">
                                    <span class="badge bg-<?= $mediasAluno['situacaoClass'] ?? 'secondary' ?>">
                                        <?= $mediasAluno['situacao'] ?? 'Pendente' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="19" class="text-center py-4">
                                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                <p class="text-muted">Nenhum aluno matriculado nesta turma.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-light">
        <div class="row">
            <div class="col-md-12 text-end">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i>
                    MAC = Média das Avaliações Contínuas | NPP = Nota Prova Professor | NPT = Nota Prova Trimestral | 
                    MT = Média Trimestral (MAC+NPP+NPT)/3 | MDF = Média Final (MT1+MT2+MT3)/3
                </small>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>