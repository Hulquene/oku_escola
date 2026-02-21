<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $period->period_name ?></h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-alt me-1"></i>
                <?= $period->period_type ?> • <?= $period->semester_name ?> • <?= $period->year_name ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/exams/periods/generate-schedule/' . $period->id) ?>" 
               class="btn btn-success">
                <i class="fas fa-calendar-plus me-1"></i> Gerar Calendário
            </a>
            <a href="<?= site_url('admin/exams/periods/edit/' . $period->id) ?>" 
               class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
            <a href="<?= site_url('admin/exams/periods') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/periods') ?>">Períodos</a></li>
            <li class="breadcrumb-item active"><?= $period->period_name ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Estatísticas -->
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
                        <h6 class="text-muted mb-1">Período</h6>
                        <h5 class="mb-0"><?= date('d/m/Y', strtotime($period->start_date)) ?></h5>
                        <small class="text-muted">até <?= date('d/m/Y', strtotime($period->end_date)) ?></small>
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
                            'Planejado' => 'secondary',
                            'Em Andamento' => 'success',
                            'Concluído' => 'info',
                            'Cancelado' => 'danger'
                        ];
                        $statusColor = $statusColors[$period->status] ?? 'secondary';
                        ?>
                        <h5 class="mb-0">
                            <span class="badge bg-<?= $statusColor ?> p-2">
                                <?= $period->status ?>
                            </span>
                        </h5>
                        <?php
                        $today = new DateTime();
                        $end = new DateTime($period->end_date);
                        if ($period->status == 'Em Andamento' && $today <= $end) {
                            $daysLeft = $today->diff($end)->days;
                            echo '<small class="text-muted">' . $daysLeft . ' dias restantes</small>';
                        }
                        ?>
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
                            <i class="fas fa-file-alt text-info"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Exames</h6>
                        <h2 class="mb-0"><?= $stats->total_exams ?? 0 ?></h2>
                        <small class="text-muted">em <?= $stats->total_classes ?? 0 ?> turmas</small>
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
                            <i class="fas fa-check-circle text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Realizados</h6>
                        <h2 class="mb-0"><?= $stats->completed_exams ?? 0 ?></h2>
                        <small class="text-muted">
                            <?php if (($stats->total_exams ?? 0) > 0): ?>
                                <?= round(($stats->completed_exams / $stats->total_exams) * 100) ?>% do total
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Descrição do Período -->
<?php if ($period->description): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-semibold mb-2">Descrição:</h6>
            <p class="mb-0"><?= $period->description ?></p>
        </div>
    </div>
<?php endif; ?>

<!-- Lista de Exames Agendados -->
<div class="card">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-table me-2 text-primary"></i>
                Exames Agendados
            </h5>
            <div>
                <a href="<?= site_url('admin/exams/schedules/create?period_id=' . $period->id) ?>" 
                   class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Adicionar Exame
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($exams)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 ps-3">Data</th>
                            <th class="border-0 py-3">Hora</th>
                            <th class="border-0 py-3">Turma</th>
                            <th class="border-0 py-3">Disciplina</th>
                            <th class="border-0 py-3">Tipo</th>
                            <th class="border-0 py-3">Sala</th>
                            <th class="border-0 py-3 text-center">Status</th>
                            <th class="border-0 py-3 text-center pe-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exams as $exam): ?>
                            <?php
                            $examStatusColors = [
                                'Agendado' => 'secondary',
                                'Realizado' => 'success',
                                'Cancelado' => 'danger',
                                'Adiado' => 'warning'
                            ];
                            $examStatusColor = $examStatusColors[$exam->status] ?? 'secondary';
                            
                            // Verificar se já passou
                            $today = date('Y-m-d');
                            $isPast = $exam->exam_date < $today && $exam->status == 'Agendado';
                            if ($isPast) {
                                $examStatusColor = 'danger';
                            }
                            ?>
                            <tr class="<?= $isPast ? 'bg-light' : '' ?>">
                                <td class="ps-3">
                                    <span class="fw-medium">
                                        <?= date('d/m/Y', strtotime($exam->exam_date)) ?>
                                    </span>
                                    <?php if ($isPast): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger ms-2">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $exam->exam_time ? substr($exam->exam_time, 0, 5) : '—' ?></td>
                                <td>
                                    <span class="fw-semibold"><?= $exam->class_name ?></span>
                                    <br>
                                    <small class="text-muted"><?= $exam->class_code ?></small>
                                </td>
                                <td>
                                    <?= $exam->discipline_name ?>
                                    <br>
                                    <small class="text-muted"><?= $exam->discipline_code ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= $exam->board_type ?></span>
                                </td>
                                <td><?= $exam->exam_room ?: '—' ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $examStatusColor ?> p-2">
                                        <?= $exam->status ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('admin/exams/schedules/view/' . $exam->id) ?>" 
                                           class="btn btn-outline-primary" title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($exam->status == 'Agendado' && !$isPast): ?>
                                            <a href="<?= site_url('admin/exams/schedules/attendance/' . $exam->id) ?>" 
                                               class="btn btn-outline-success" title="Registar presenças">
                                                <i class="fas fa-user-check"></i>
                                            </a>
                                            <a href="<?= site_url('admin/exams/schedules/results/' . $exam->id) ?>" 
                                               class="btn btn-outline-warning" title="Registar notas">
                                                <i class="fas fa-graduation-cap"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum exame agendado</h5>
                <p class="text-muted mb-3">
                    Utilize a opção "Gerar Calendário" para agendar automaticamente<br>
                    ou adicione exames manualmente.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="<?= site_url('admin/exams/periods/generate-schedule/' . $period->id) ?>" 
                       class="btn btn-success">
                        <i class="fas fa-calendar-plus me-2"></i>Gerar Calendário
                    </a>
                    <a href="<?= site_url('admin/exams/schedules/create?period_id=' . $period->id) ?>" 
                       class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Adicionar Manualmente
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}

.table th {
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
}

.btn-group .btn:last-child {
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
}
</style>

<?= $this->endSection() ?>