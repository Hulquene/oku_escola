<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Mini Pauta: <?= $miniPauta->discipline_name ?></h1>
        <div>
            <a href="<?= site_url('admin/mini-grade-sheet/print/' . $miniPauta->id) ?>" class="btn btn-secondary me-2" target="_blank">
                <i class="fas fa-print"></i> Imprimir
            </a>
            <a href="<?= site_url('admin/mini-grade-sheet/export/' . $miniPauta->id) ?>" class="btn btn-success me-2">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
            <a href="<?= site_url('admin/mini-grade-sheet') ?>" class="btn btn-info">
                <i class="fas fa-list"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/mini-grade-sheet') ?>">Mini Pautas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Visualizar</li>
        </ol>
    </nav>
</div>

<!-- Informações da Pauta -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações da Pauta</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Ano Letivo:</strong>
                        <p><?= $miniPauta->year_name ?? 'N/A' ?></p>
                    </div>
                    <div class="col-md-3">
                        <strong>Curso:</strong>
                        <p><?= $miniPauta->course_name ?? 'Ensino Geral' ?></p>
                    </div>
                    <div class="col-md-2">
                        <strong>Nível:</strong>
                        <p><?= $miniPauta->level_name ?></p>
                    </div>
                    <div class="col-md-2">
                        <strong>Turma:</strong>
                        <p><?= $miniPauta->class_name ?></p>
                    </div>
                    <div class="col-md-2">
                        <strong>Disciplina:</strong>
                        <p><?= $miniPauta->discipline_name ?> (<?= $miniPauta->discipline_code ?>)</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <strong>Professor:</strong>
                        <p><?= ($miniPauta->teacher_first_name ?? 'Não') . ' ' . ($miniPauta->teacher_last_name ?? 'atribuído') ?></p>
                    </div>
                    <div class="col-md-2">
                        <strong>Período:</strong>
                        <p><?= $miniPauta->period_name ?></p>
                    </div>
                    <div class="col-md-2">
                        <strong>Tipo Exame:</strong>
                        <p><?= $miniPauta->board_name ?> (<?= $miniPauta->board_code ?>)</p>
                    </div>
                    <div class="col-md-2">
                        <strong>Data:</strong>
                        <p><?= date('d/m/Y', strtotime($miniPauta->exam_date)) ?></p>
                    </div>
                    <div class="col-md-3">
                        <strong>Status:</strong>
                        <p>
                            <?php
                            $statusClass = [
                                'Agendado' => 'primary',
                                'Realizado' => 'success',
                                'Cancelado' => 'danger',
                                'Adiado' => 'warning'
                            ][$miniPauta->status] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $statusClass ?>"><?= $miniPauta->status ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6>Total Alunos</h6>
                <h3><?= $estatisticas['totalAlunos'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6>Aprovados</h6>
                <h3><?= $estatisticas['aprovados'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6>Recurso</h6>
                <h3><?= $estatisticas['recurso'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6>Reprovados</h6>
                <h3><?= $estatisticas['reprovados'] ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6>Média da Turma</h6>
                <h3><?= $estatisticas['mediaTurma'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <h6>Taxa de Aprovação</h6>
                <h3><?= $estatisticas['taxaAprovacao'] ?>%</h3>
            </div>
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
                            <?php $mediasAluno = $medias[$aluno->enrollment_id] ?? []; ?>
                            <tr>
                                <td class="text-center"><?= $counter++ ?></td>
                                <td>
                                    <strong><?= $aluno->full_name ?? $aluno->first_name . ' ' . $aluno->last_name ?></strong>
                                </td>
                                <td class="text-center"><?= $aluno->student_number ?? '—' ?></td>
                                
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