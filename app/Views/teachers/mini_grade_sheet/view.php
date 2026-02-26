<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Mini Pauta: <?= $miniPauta->discipline_name ?></h1>
        <div>
            <a href="<?= site_url('teachers/mini-pauta/print/' . $miniPauta->id) ?>" class="btn btn-secondary me-2" target="_blank">
                <i class="fas fa-print"></i> Imprimir
            </a>
            <a href="<?= site_url('teachers/exams/grade/' . $miniPauta->id) ?>" class="btn btn-success me-2">
                <i class="fas fa-star"></i> Lançar Notas
            </a>
            <a href="<?= site_url('teachers/mini-pauta') ?>" class="btn btn-info">
                <i class="fas fa-list"></i> Todas
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/mini-pauta') ?>">Mini Pautas</a></li>
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
                        <p><?= $miniPauta->course_name ?> (<?= $miniPauta->course_code ?>)</p>
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
                        <strong>Período:</strong>
                        <p><?= $miniPauta->period_name ?> (<?= $miniPauta->period_type ?>)</p>
                    </div>
                    <div class="col-md-2">
                        <strong>Tipo Exame:</strong>
                        <p><?= $miniPauta->board_name ?> (<?= $miniPauta->board_code ?>)</p>
                    </div>
                    <div class="col-md-2">
                        <strong>Data:</strong>
                        <p><?= date('d/m/Y', strtotime($miniPauta->exam_date)) ?></p>
                    </div>
                    <div class="col-md-2">
                        <strong>Hora:</strong>
                        <p><?= $miniPauta->exam_time ? date('H:i', strtotime($miniPauta->exam_time)) : '--' ?></p>
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
                <?php if (!empty($miniPauta->observations)): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <strong>Observações:</strong>
                            <p class="mb-0"><?= $miniPauta->observations ?></p>
                        </div>
                    </div>
                <?php endif; ?>
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
                <h3><?= $totalAlunos ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6>Notas Lançadas</h6>
                <h3><?= $totalResultados ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6>Média da Turma</h6>
                <h3><?= number_format($estatisticas->average ?? 0, 1) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6>Taxa Aprovação</h6>
                <h3><?= number_format($estatisticas->approval_rate ?? 0, 1) ?>%</h3>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Alunos e Notas -->
<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Alunos e Notas</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($alunos)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nº</th>
                            <th>Nº Estudante</th>
                            <th>Nome do Aluno</th>
                            <th class="text-center">Presença</th>
                            <th class="text-center">Nota</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alunos as $index => $aluno): ?>
                            <?php 
                            $resultado = $resultados[$aluno->enrollment_id] ?? null;
                            $presenca = $presencas[$aluno->enrollment_id] ?? null;
                            $presente = $presenca ? $presenca->attended : ($resultado ? !$resultado->is_absent : true);
                            $nota = $resultado ? $resultado->score : null;
                            ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $aluno->student_number ?></td>
                                <td><?= $aluno->full_name ?></td>
                                <td class="text-center">
                                    <?php if ($presente): ?>
                                        <span class="badge bg-success">Presente</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Falta</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($nota !== null): ?>
                                        <strong><?= number_format($nota, 1) ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">--</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($nota !== null): ?>
                                        <?php if ($nota >= ($miniPauta->approval_score ?? 10)): ?>
                                            <span class="badge bg-success">Aprovado</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Reprovado</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Pendente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-3">Nenhum aluno encontrado para esta turma.</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>