<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-file-alt me-2" style="color: var(--accent);"></i>Mini Pauta: <?= $miniPauta['discipline_name'] ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/mini-grade-sheet') ?>">Mini Pautas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Visualizar</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/mini-grade-sheet/print/' . $miniPauta['id']) ?>" class="hdr-btn secondary" target="_blank">
                <i class="fas fa-print me-1"></i> Imprimir
            </a>
            <a href="<?= site_url('admin/mini-grade-sheet/export/' . $miniPauta['id']) ?>" class="hdr-btn success">
                <i class="fas fa-file-excel me-1"></i> Exportar Excel
            </a>
            <a href="<?= site_url('admin/mini-grade-sheet') ?>" class="hdr-btn info">
                <i class="fas fa-list me-1"></i> Voltar
            </a>
        </div>
    </div>
    <div class="mt-2">
        <span class="badge-ci info">
            <i class="fas fa-info-circle me-1"></i>
            Visualização detalhada da mini pauta
        </span>
    </div>
</div>

<!-- Informações da Pauta com estilo do sistema -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="ci-card">
            <div class="ci-card-header" style="background: var(--primary);">
                <div class="ci-card-title" style="color: #fff;">
                    <i class="fas fa-info-circle me-2"></i>
                    <span>Informações da Pauta</span>
                </div>
            </div>
            <div class="ci-card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="info-label">Ano Letivo:</div>
                        <div class="info-value fw-semibold"><?= $miniPauta->year_name ?? 'N/A' ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Curso:</div>
                        <div class="info-value fw-semibold"><?= $miniPauta->course_name ?? 'Ensino Geral' ?></div>
                    </div>
                    <div class="col-md-2">
                        <div class="info-label">Nível:</div>
                        <div class="info-value fw-semibold"><?= $miniPauta['level_name'] ?></div>
                    </div>
                    <div class="col-md-2">
                        <div class="info-label">Turma:</div>
                        <div class="info-value fw-semibold"><?= $miniPauta['class_name'] ?></div>
                    </div>
                    <div class="col-md-2">
                        <div class="info-label">Disciplina:</div>
                        <div class="info-value fw-semibold"><?= $miniPauta['discipline_name'] ?> (<?= $miniPauta['discipline_code'] ?>)</div>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-3">
                        <div class="info-label">Professor:</div>
                        <div class="info-value fw-semibold"><?= ($miniPauta->teacher_first_name ?? 'Não') . ' ' . ($miniPauta['teacher_last_name'] ?? 'atribuído') ?></div>
                    </div>
                    <div class="col-md-2">
                        <div class="info-label">Período:</div>
                        <div class="info-value fw-semibold"><?= $miniPauta['period_name'] ?></div>
                    </div>
                    <div class="col-md-2">
                        <div class="info-label">Tipo Exame:</div>
                        <div class="info-value fw-semibold"><?= $miniPauta['board_name'] ?> (<?= $miniPauta['board_code'] ?>)</div>
                    </div>
                    <div class="col-md-2">
                        <div class="info-label">Data:</div>
                        <div class="info-value fw-semibold"><?= date('d/m/Y', strtotime($miniPauta['exam_date'])) ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Status:</div>
                        <div class="info-value fw-semibold">
                            <?php
                            $statusClass = [
                                'Agendado' => 'secondary',
                                'Realizado' => 'success',
                                'Cancelado' => 'danger',
                                'Adiado' => 'warning'
                            ][$miniPauta['status']] ?? 'secondary';
                            ?>
                            <span class="badge-ci <?= $statusClass ?>"><?= $miniPauta['status'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas com estilo do sistema -->
<div class="stat-grid mb-4">
    <div class="stat-card" style="background: var(--primary); color: #fff;">
        <div class="stat-icon" style="background: rgba(255,255,255,0.15); color: #fff;">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <div class="stat-label" style="color: rgba(255,255,255,0.7);">Total Alunos</div>
            <div class="stat-value" style="color: #fff;"><?= $estatisticas['totalAlunos'] ?></div>
        </div>
    </div>
    
    <div class="stat-card" style="background: var(--success); color: #fff;">
        <div class="stat-icon" style="background: rgba(255,255,255,0.15); color: #fff;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="stat-label" style="color: rgba(255,255,255,0.7);">Aprovados</div>
            <div class="stat-value" style="color: #fff;"><?= $estatisticas['aprovados'] ?></div>
        </div>
    </div>
    
    <div class="stat-card" style="background: var(--warning); color: #fff;">
        <div class="stat-icon" style="background: rgba(255,255,255,0.15); color: #fff;">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <div class="stat-label" style="color: rgba(255,255,255,0.7);">Recurso</div>
            <div class="stat-value" style="color: #fff;"><?= $estatisticas['recurso'] ?></div>
        </div>
    </div>
    
    <div class="stat-card" style="background: var(--danger); color: #fff;">
        <div class="stat-icon" style="background: rgba(255,255,255,0.15); color: #fff;">
            <i class="fas fa-times-circle"></i>
        </div>
        <div>
            <div class="stat-label" style="color: rgba(255,255,255,0.7);">Reprovados</div>
            <div class="stat-value" style="color: #fff;"><?= $estatisticas['reprovados'] ?></div>
        </div>
    </div>
</div>

<div class="stat-grid mb-4">
    <div class="stat-card" style="background: var(--accent); color: #fff;">
        <div class="stat-icon" style="background: rgba(255,255,255,0.15); color: #fff;">
            <i class="fas fa-chart-line"></i>
        </div>
        <div>
            <div class="stat-label" style="color: rgba(255,255,255,0.7);">Média da Turma</div>
            <div class="stat-value" style="color: #fff;"><?= $estatisticas['mediaTurma'] ?></div>
        </div>
    </div>
    
    <div class="stat-card" style="background: var(--primary); color: #fff;">
        <div class="stat-icon" style="background: rgba(255,255,255,0.15); color: #fff;">
            <i class="fas fa-percent"></i>
        </div>
        <div>
            <div class="stat-label" style="color: rgba(255,255,255,0.7);">Taxa de Aprovação</div>
            <div class="stat-value" style="color: #fff;"><?= $estatisticas['taxaAprovacao'] ?>%</div>
        </div>
    </div>
</div>

<!-- Lista de Alunos e Notas -->
<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Alunos e Notas</h5>
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
                        <th class="text-center" width="50" title="Média Avaliações Contínuas">MAC</th>
                        <th class="text-center" width="50" title="Nota Prova Professor">NPP</th>
                        <th class="text-center" width="50" title="Nota Prova Trimestral">NPT</th>
                        <th class="text-center" width="50" title="Média Trimestral">MT</th>
                        <th class="text-center" width="50" title="Média Avaliações Contínuas">MAC</th>
                        <th class="text-center" width="50" title="Nota Prova Professor">NPP</th>
                        <th class="text-center" width="50" title="Nota Prova Trimestral">NPT</th>
                        <th class="text-center" width="50" title="Média Trimestral">MT</th>
                        <th class="text-center" width="50" title="Média Avaliações Contínuas">MAC</th>
                        <th class="text-center" width="50" title="Nota Prova Professor">NPP</th>
                        <th class="text-center" width="50" title="Nota Prova Trimestral">NPT</th>
                        <th class="text-center" width="50" title="Média Trimestral">MT</th>
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
                                    <strong><?= $aluno['full_name'] ?? $aluno->first_name . ' ' . $aluno['last_name'] ?></strong>
                                </td>
                                <td class="text-center"><?= $aluno['student_number'] ?? '—' ?></td>
                                
                                <!-- 1º Trimestre -->
                                <td class="text-center"><?= $mediasAluno['trimestres'][1]['AC'] ?? '—' ?></td>
                                <td class="text-center"><?= $mediasAluno['trimestres'][1]['NPP'] ?? '—' ?></td>
                                <td class="text-center"><?= $mediasAluno['trimestres'][1]['NPT'] ?? '—' ?></td>
                                <td class="text-center <?= ($mediasAluno['trimestres'][1]['MT'] ?? '—') !== '—' ? 'bg-light' : '' ?>">
                                    <strong><?= $mediasAluno['trimestres'][1]['MT'] ?? '—' ?></strong>
                                </td>
                                
                                <!-- 2º Trimestre -->
                                <td class="text-center"><?= $mediasAluno['trimestres'][2]['AC'] ?? '—' ?></td>
                                <td class="text-center"><?= $mediasAluno['trimestres'][2]['NPP'] ?? '—' ?></td>
                                <td class="text-center"><?= $mediasAluno['trimestres'][2]['NPT'] ?? '—' ?></td>
                                <td class="text-center <?= ($mediasAluno['trimestres'][2]['MT'] ?? '—') !== '—' ? 'bg-light' : '' ?>">
                                    <strong><?= $mediasAluno['trimestres'][2]['MT'] ?? '—' ?></strong>
                                </td>
                                
                                <!-- 3º Trimestre -->
                                <td class="text-center"><?= $mediasAluno['trimestres'][3]['AC'] ?? '—' ?></td>
                                <td class="text-center"><?= $mediasAluno['trimestres'][3]['NPP'] ?? '—' ?></td>
                                <td class="text-center"><?= $mediasAluno['trimestres'][3]['NPT'] ?? '—' ?></td>
                                <td class="text-center <?= ($mediasAluno['trimestres'][3]['MT'] ?? '—') !== '—' ? 'bg-light' : '' ?>">
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