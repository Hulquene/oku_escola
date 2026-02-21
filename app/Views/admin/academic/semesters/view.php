<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <?php if (!isset($semester->has_results) || $semester->has_results == 0): ?>
                <a href="<?= site_url('admin/academic/semesters/form-edit/' . $semester->id) ?>" class="btn btn-info">
                    <i class="fas fa-edit"></i> Editar
                </a>
            <?php else: ?>
                <span class="btn btn-secondary disabled" title="Não pode editar (já processado)">
                    <i class="fas fa-edit"></i> Editar
                </span>
            <?php endif; ?>
            <a href="<?= site_url('admin/academic/semesters') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/academic/semesters') ?>">Semestres/Trimestres</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Resumo -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total de Exames</h5>
                <h2><?= $total_exams ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Resultados Processados</h5>
                <h2><?= $total_results ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Status</h5>
                <h2>
                    <?php if ($semester->is_current): ?>
                        <span class="badge bg-light text-dark">Atual</span>
                    <?php endif; ?>
                    <?php if ($semester->is_active): ?>
                        <span class="badge bg-light text-dark">Ativo</span>
                    <?php else: ?>
                        <span class="badge bg-light text-dark">Inativo</span>
                    <?php endif; ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<!-- Details Card -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-info-circle"></i> Informações do Período
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">ID</th>
                        <td><?= $semester->id ?></td>
                    </tr>
                    <tr>
                        <th>Nome</th>
                        <td><?= $semester->semester_name ?></td>
                    </tr>
                    <tr>
                        <th>Tipo</th>
                        <td>
                            <span class="badge bg-info"><?= $semester->semester_type ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Ano Letivo</th>
                        <td><?= $semester->year_name ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">Data Início</th>
                        <td><?= date('d/m/Y', strtotime($semester->start_date)) ?></td>
                    </tr>
                    <tr>
                        <th>Data Fim</th>
                        <td><?= date('d/m/Y', strtotime($semester->end_date)) ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php
                            $statusLabels = [
                                'ativo' => ['badge' => 'success', 'text' => 'Ativo'],
                                'inativo' => ['badge' => 'secondary', 'text' => 'Inativo'],
                                'processado' => ['badge' => 'warning', 'text' => 'Processado'],
                                'concluido' => ['badge' => 'dark', 'text' => 'Concluído']
                            ];
                            $status = $semester->status ?? ($semester->is_active ? 'ativo' : 'inativo');
                            $label = $statusLabels[$status] ?? ['badge' => 'secondary', 'text' => $status];
                            ?>
                            <span class="badge bg-<?= $label['badge'] ?>"><?= $label['text'] ?></span>
                            
                            <?php if ($semester->is_current): ?>
                                <span class="badge bg-primary ms-2">Período Atual</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Criado em</th>
                        <td><?= date('d/m/Y H:i', strtotime($semester->created_at)) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Exams in this semester -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-pencil-alt"></i> Exames neste Período
    </div>
    <div class="card-body">
        <?php if (!empty($exams)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Exame</th>
                            <th>Tipo</th>
                            <th>Turma</th>
                            <th>Disciplina</th>
                            <th>Sala</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exams as $exam): 
                            $today = date('Y-m-d');
                            $isPast = $exam->exam_date < $today;
                            $isToday = $exam->exam_date == $today;
                            
                            if ($isToday) {
                                $statusClass = 'warning';
                                $statusText = 'Hoje';
                            } elseif ($isPast) {
                                $statusClass = 'secondary';
                                $statusText = 'Realizado';
                            } else {
                                $statusClass = 'success';
                                $statusText = 'Agendado';
                            }
                        ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($exam->exam_date)) ?></td>
                                <td><?= $exam->exam_time ? date('H:i', strtotime($exam->exam_time)) : '--:--' ?></td>
                                <td><?= $exam->board_name ?? 'Exame' ?></td>
                                <td><span class="badge bg-info"><?= $exam->board_type ?? 'Normal' ?></span></td>
                                <td><?= $exam->class_name ?></td>
                                <td><?= $exam->discipline_name ?></td>
                                <td><?= $exam->exam_room ?: '-' ?></td>
                                <td>
                                    <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhum exame agendado para este período.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Logs (se existirem) -->
<?php if (!empty($logs)): ?>
<div class="card mt-4">
    <div class="card-header">
        <i class="fas fa-history"></i> Histórico de Atividades
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Usuário</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($log->created_at)) ?></td>
                            <td><?= $log->user_name ?? 'Sistema' ?></td>
                            <td><?= $log->action ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>