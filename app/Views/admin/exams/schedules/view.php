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
             
            <!-- Botão para mudar status -->
            <?php if ($schedule->status != 'Realizado' && $schedule->status != 'Cancelado'): ?>
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#statusModal">
                    <i class="fas fa-sync-alt me-1"></i> Mudar Status
                </button>
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
    </div>
    
    <div class="col-md-6">
        <!-- Lista de Resultados com Presenças e Notas na mesma tabela -->
        <div class="card mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-graduation-cap me-2 text-warning"></i>
                    Resultados e Presenças
                </h5>
                <?php if (!empty($combinedData)): ?>
                    <?php
                    $scores = array_filter(array_column($combinedData, 'score'), function($v) { return $v !== null; });
                    if (!empty($scores)):
                        $avgScore = array_sum($scores) / count($scores);
                        $maxScore = max($scores);
                        $minScore = min($scores);
                    ?>
                    <div class="text-end">
                        <span class="badge bg-light text-dark me-1">Média: <?= number_format($avgScore, 1) ?></span>
                        <span class="badge bg-success me-1">Max: <?= number_format($maxScore, 1) ?></span>
                        <span class="badge bg-danger">Min: <?= number_format($minScore, 1) ?></span>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($combinedData)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-2 ps-3">Aluno</th>
                                    <th class="border-0 py-2 text-center">Processo</th>
                                    <th class="border-0 py-2 text-center">Presença</th>
                                    <th class="border-0 py-2 text-center">Nota</th>
                                    <th class="border-0 py-2 text-center pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($combinedData as $item): ?>
                                    <?php
                                    // Determinar classe da nota
                                    $scoreClass = '';
                                    $scoreText = '—';
                                    
                                    if ($item->score !== null) {
                                        if ($item->score >= 10) {
                                            $scoreClass = 'success';
                                            $scoreText = 'Aprovado';
                                        } elseif ($item->score >= 7) {
                                            $scoreClass = 'warning';
                                            $scoreText = 'Recurso';
                                        } else {
                                            $scoreClass = 'danger';
                                            $scoreText = 'Reprovado';
                                        }
                                    }
                                    
                                    // Classe da presença
                                    $attendanceClass = $item->attended ? 'success' : 'danger';
                                    $attendanceIcon = $item->attended ? 'fa-check' : 'fa-times';
                                    $attendanceText = $item->attended ? 'Presente' : 'Ausente';
                                    ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-2">
                                                    <?= strtoupper(substr($item->first_name, 0, 1) . substr($item->last_name, 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <span class="fw-medium"><?= $item->first_name ?> <?= $item->last_name ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge bg-light text-dark"><?= $item->student_number ?></span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge bg-<?= $attendanceClass ?>">
                                                <i class="fas <?= $attendanceIcon ?> me-1"></i>
                                                <?= $attendanceText ?>
                                            </span>
                                            <?php if ($item->check_in_time): ?>
                                                <br>
                                                <small class="text-muted"><?= date('H:i', strtotime($item->check_in_time)) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php if ($item->score !== null): ?>
                                                <span class="fw-bold fs-5"><?= number_format($item->score, 1) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle pe-3">
                                            <?php if ($item->score !== null): ?>
                                                <span class="badge bg-<?= $scoreClass ?>">
                                                    <?= $scoreText ?>
                                                </span>
                                            <?php elseif ($item->attended): ?>
                                                <span class="badge bg-secondary">Aguardando</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhum aluno encontrado</p>
                        <?php if ($schedule->status == 'Agendado'): ?>
                            <div class="mt-2">
                                <a href="<?= site_url('admin/exams/schedules/attendance/' . $schedule->id) ?>" 
                                   class="btn btn-sm btn-success me-2">
                                    <i class="fas fa-user-check me-1"></i> Registar Presenças
                                </a>
                                <a href="<?= site_url('admin/exams/schedules/results/' . $schedule->id) ?>" 
                                   class="btn btn-sm btn-warning">
                                    <i class="fas fa-graduation-cap me-1"></i> Registar Notas
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mudar status -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-sync-alt me-2"></i>
                    Alterar Status do Exame
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('admin/exams/schedules/update-status/' . $schedule->id) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status Atual</label>
                        <div>
                            <span class="badge bg-<?= $statusColor ?> p-2 fs-6">
                                <?= $schedule->status ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Novo Status</label>
                        <select name="status" class="form-select" required>
                            <option value="">Selecione...</option>
                            <option value="Agendado">Agendado</option>
                            <option value="Realizado">Realizado</option>
                            <option value="Cancelado">Cancelado</option>
                            <option value="Adiado">Adiado</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Observações</label>
                        <textarea name="observations" class="form-control" rows="2" 
                                  placeholder="Motivo da alteração (opcional)"></textarea>
                    </div>
                    
                    <?php if ($schedule->status != 'Realizado'): ?>
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle me-2"></i>
                        Ao marcar como <strong>Realizado</strong>, o exame será finalizado e não poderá mais ser editado.
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($schedule->status == 'Cancelado'): ?>
                    <div class="alert alert-danger small">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Ao cancelar o exame, todas as presenças e notas serão mantidas, mas o exame ficará inativo.
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save me-1"></i> Salvar Alteração
                    </button>
                </div>
            </form>
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

.table tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
    background-color: #f8f9fa;
}
</style>

<?= $this->endSection() ?>