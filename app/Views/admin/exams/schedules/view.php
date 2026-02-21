<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Detalhes do Exame</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-check me-1"></i>
                <?= $schedule->discipline_name ?> • <?= $schedule->class_name ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <?php if ($schedule->status == 'Agendado'): ?>
                <a href="<?= site_url('admin/exams/schedules/attendance/' . $schedule->id) ?>" 
                   class="btn btn-success">
                    <i class="fas fa-user-check me-1"></i> Presenças
                </a>
                <a href="<?= site_url('admin/exams/schedules/results/' . $schedule->id) ?>" 
                   class="btn btn-warning">
                    <i class="fas fa-graduation-cap me-1"></i> Notas
                </a>
            <?php endif; ?>
            <a href="<?= site_url('admin/exams/schedules/edit/' . $schedule->id) ?>" 
               class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
            <a href="<?= site_url('admin/exams/schedules') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/schedules') ?>">Agendamentos</a></li>
            <li class="breadcrumb-item active"><?= $schedule->discipline_name ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações do Exame -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                            <i class="fas fa-calendar text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Data/Hora</h6>
                        <h5 class="mb-0"><?= date('d/m/Y', strtotime($schedule->exam_date)) ?></h5>
                        <small class="text-muted"><?= $schedule->exam_time ? substr($schedule->exam_time, 0, 5) : 'Horário não definido' ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 p-2 rounded">
                            <i class="fas fa-clock text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Status</h6>
                        <?php
                        $statusColors = [
                            'Agendado' => 'secondary',
                            'Realizado' => 'success',
                            'Cancelado' => 'danger',
                            'Adiado' => 'warning'
                        ];
                        $statusColor = $statusColors[$schedule->status] ?? 'secondary';
                        ?>
                        <h5 class="mb-0">
                            <span class="badge bg-<?= $statusColor ?> p-2">
                                <?= $schedule->status ?>
                            </span>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 p-2 rounded">
                            <i class="fas fa-users text-info"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Presenças</h6>
                        <h5 class="mb-0"><?= $attendanceStats->present ?? 0 ?>/<?= $attendanceStats->total ?? 0 ?></h5>
                        <small class="text-muted"><?= $attendanceStats->percentage ?? 0 ?>%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                            <i class="fas fa-graduation-cap text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Notas Registadas</h6>
                        <h5 class="mb-0"><?= count($results) ?>/<?= $attendanceStats->present ?? 0 ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <!-- Detalhes do Exame -->
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    Informações do Exame
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">Período:</th>
                        <td><?= $schedule->period_name ?></td>
                    </tr>
                    <tr>
                        <th>Ano Letivo:</th>
                        <td><?= $schedule->year_name ?></td>
                    </tr>
                    <tr>
                        <th>Semestre:</th>
                        <td><?= $schedule->semester_name ?></td>
                    </tr>
                    <tr>
                        <th>Turma:</th>
                        <td><?= $schedule->class_name ?> (<?= $schedule->class_code ?>)</td>
                    </tr>
                    <tr>
                        <th>Disciplina:</th>
                        <td><?= $schedule->discipline_name ?> (<?= $schedule->discipline_code ?>)</td>
                    </tr>
                    <tr>
                        <th>Tipo:</th>
                        <td>
                            <span class="badge bg-info"><?= $schedule->board_name ?></span>
                            <span class="badge bg-secondary ms-1">Peso: <?= $schedule->weight ?>x</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Sala:</th>
                        <td><?= $schedule->exam_room ?: 'Não definida' ?></td>
                    </tr>
                    <tr>
                        <th>Duração:</th>
                        <td><?= $schedule->duration_minutes ?> minutos</td>
                    </tr>
                    <?php if ($schedule->observations): ?>
                        <tr>
                            <th>Observações:</th>
                            <td><?= $schedule->observations ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        
        <!-- Lista de Presenças -->
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-user-check me-2 text-success"></i>
                    Presenças Registadas
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($attendance)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-2 ps-3">Aluno</th>
                                    <th class="border-0 py-2 text-center">Status</th>
                                    <th class="border-0 py-2 text-center pe-3">Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attendance as $att): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-2">
                                                    <?= strtoupper(substr($att->first_name, 0, 1) . substr($att->last_name, 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <span class="fw-medium"><?= $att->first_name ?> <?= $att->last_name ?></span>
                                                    <br>
                                                    <small class="text-muted"><?= $att->student_number ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($att->attended): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i> Presente
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i> Ausente
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center pe-3">
                                            <?= $att->check_in_time ? date('H:i', strtotime($att->check_in_time)) : '—' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-user-clock fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhuma presença registada</p>
                        <a href="<?= site_url('admin/exams/schedules/attendance/' . $schedule->id) ?>" 
                           class="btn btn-sm btn-success">
                            <i class="fas fa-user-check me-1"></i> Registar Presenças
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- Lista de Resultados -->
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-graduation-cap me-2 text-warning"></i>
                    Resultados
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($results)): ?>
                    <?php
                    $scores = array_column($results, 'score');
                    $avgScore = array_sum($scores) / count($scores);
                    $maxScore = max($scores);
                    $minScore = min($scores);
                    ?>
                    <div class="bg-light p-3 border-bottom">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="text-muted small">Média</div>
                                <div class="fw-bold fs-5"><?= number_format($avgScore, 1) ?></div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Máxima</div>
                                <div class="fw-bold fs-5 text-success"><?= number_format($maxScore, 1) ?></div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Mínima</div>
                                <div class="fw-bold fs-5 text-danger"><?= number_format($minScore, 1) ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-2 ps-3">Aluno</th>
                                    <th class="border-0 py-2 text-center">Nota</th>
                                    <th class="border-0 py-2 text-center pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $result): ?>
                                    <?php
                                    $resultClass = $result->score >= 10 ? 'success' : ($result->score >= 7 ? 'warning' : 'danger');
                                    $resultText = $result->score >= 10 ? 'Aprovado' : ($result->score >= 7 ? 'Recurso' : 'Reprovado');
                                    ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-2">
                                                    <?= strtoupper(substr($result->first_name, 0, 1) . substr($result->last_name, 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <span class="fw-medium"><?= $result->first_name ?> <?= $result->last_name ?></span>
                                                    <br>
                                                    <small class="text-muted"><?= $result->student_number ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold fs-5"><?= number_format($result->score, 1) ?></span>
                                        </td>
                                        <td class="text-center pe-3">
                                            <span class="badge bg-<?= $resultClass ?>">
                                                <?= $resultText ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhuma nota registada</p>
                        <?php if (($attendanceStats->present ?? 0) > 0): ?>
                            <a href="<?= site_url('admin/exams/schedules/results/' . $schedule->id) ?>" 
                               class="btn btn-sm btn-warning">
                                <i class="fas fa-graduation-cap me-1"></i> Registar Notas
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
}

.table-borderless th,
.table-borderless td {
    padding: 0.75rem;
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}
</style>

<?= $this->endSection() ?>