<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('students/exams/schedule') ?>" class="btn btn-info me-2">
                <i class="fas fa-calendar-alt"></i> Calendário
            </a>
            <a href="<?= site_url('students/exams/results') ?>" class="btn btn-success">
                <i class="fas fa-chart-bar"></i> Resultados
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Meus Exames</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Total de Exames</h6>
                        <h2 class="mb-0"><?= count($upcomingExams) + count($pastExams) ?></h2>
                    </div>
                    <i class="fas fa-pencil-alt fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Próximos Exames</h6>
                        <h2 class="mb-0"><?= count($upcomingExams) ?></h2>
                    </div>
                    <i class="fas fa-clock fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Turma</h6>
                        <h2 class="mb-0"><?= $enrollment->class_name ?? 'N/A' ?></h2>
                    </div>
                    <i class="fas fa-school fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Exams -->
<div class="card mb-4">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-calendar-alt me-2"></i> Próximos Exames
            <?php if (!empty($upcomingExams)): ?>
                <span class="badge bg-light text-success ms-2"><?= count($upcomingExams) ?> agendados</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($upcomingExams)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Exame</th>
                            <th>Disciplina</th>
                            <th>Tipo</th>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Sala</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $today = date('Y-m-d');
                        foreach ($upcomingExams as $exam): 
                            $isToday = $exam->exam_date == $today;
                            $isTomorrow = $exam->exam_date == date('Y-m-d', strtotime('+1 day'));
                        ?>
                            <tr class="<?= $isToday ? 'table-warning' : '' ?>">
                                <td>
                                    <strong><?= $exam->exam_name ?></strong>
                                </td>
                                <td><?= $exam->discipline_name ?></td>
                                <td><span class="badge bg-info"><?= $exam->board_name ?></span></td>
                                <td>
                                    <?= date('d/m/Y', strtotime($exam->exam_date)) ?>
                                    <?php if ($isToday): ?>
                                        <span class="badge bg-danger ms-1">Hoje!</span>
                                    <?php elseif ($isTomorrow): ?>
                                        <span class="badge bg-warning ms-1">Amanhã</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $exam->exam_time ? date('H:i', strtotime($exam->exam_time)) : '-' ?></td>
                                <td><?= $exam->exam_room ?: '-' ?></td>
                                <td>
                                    <?php if ($isToday): ?>
                                        <span class="badge bg-danger">Hoje</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Agendado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-check fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum exame agendado</h5>
                <p class="text-muted">Não há exames programados para os próximos dias.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Past Exams -->
<div class="card">
    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-history me-2"></i> Exames Realizados
            <?php if (!empty($pastExams)): ?>
                <span class="badge bg-light text-secondary ms-2"><?= count($pastExams) ?> realizados</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($pastExams)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Exame</th>
                            <th>Disciplina</th>
                            <th>Data</th>
                            <th>Nota</th>
                            <th>Percentual</th>
                            <th>Status</th>
                            <th>Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $aprovados = 0;
                        $totalNotas = 0;
                        foreach ($pastExams as $exam): 
                            $result = $results[$exam->id] ?? null;
                            $nota = $result ? $result->score : null;
                            $percentual = $nota ? round(($nota / $exam->max_score) * 100, 1) : 0;
                            $statusClass = !$nota ? 'warning' : ($nota >= 10 ? 'success' : 'danger');
                            $statusText = !$nota ? 'Aguardando' : ($nota >= 10 ? 'Aprovado' : 'Reprovado');
                            
                            if ($nota && $nota >= 10) $aprovados++;
                            if ($nota) $totalNotas += $nota;
                        ?>
                            <tr>
                                <td><strong><?= $exam->exam_name ?></strong></td>
                                <td><?= $exam->discipline_name ?></td>
                                <td><?= date('d/m/Y', strtotime($exam->exam_date)) ?></td>
                                <td>
                                    <?php if ($nota): ?>
                                        <span class="fw-bold <?= $nota >= 10 ? 'text-success' : 'text-danger' ?>">
                                            <?= number_format($nota, 1) ?>
                                        </span> / <?= $exam->max_score ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($nota): ?>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-<?= $statusClass ?>" 
                                                     style="width: <?= $percentual ?>%"></div>
                                            </div>
                                            <span class="small"><?= $percentual ?>%</span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $statusClass ?> p-2">
                                        <i class="fas fa-<?= !$nota ? 'clock' : ($nota >= 10 ? 'check-circle' : 'times-circle') ?> me-1"></i>
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($nota): ?>
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#examModal<?= $exam->id ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Resumo de Desempenho -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3><?= $aprovados ?></h3>
                            <small>Exames Aprovados</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <?php 
                            $media = $totalNotas > 0 ? round($totalNotas / count(array_filter($pastExams, function($e) use ($results) { 
                                return isset($results[$e->id]); 
                            })), 1) : 0;
                            ?>
                            <h3><?= number_format($media, 1) ?></h3>
                            <small>Média Geral</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <?php 
                            $taxa = $aprovados > 0 ? round(($aprovados / count(array_filter($pastExams, function($e) use ($results) { 
                                return isset($results[$e->id]); 
                            }))) * 100, 1) : 0;
                            ?>
                            <h3><?= $taxa ?>%</h3>
                            <small>Taxa de Aprovação</small>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-history fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum exame realizado</h5>
                <p class="text-muted">Os exames realizados aparecerão aqui com suas respectivas notas.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modals para detalhes dos exames -->
<?php if (!empty($pastExams)): ?>
    <?php foreach ($pastExams as $exam): ?>
        <?php $result = $results[$exam->id] ?? null; ?>
        <?php if ($result): ?>
            <!-- Modal -->
            <div class="modal fade" id="examModal<?= $exam->id ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-info-circle me-2"></i>Detalhes do Exame
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-4">
                                <div class="rounded-circle bg-info d-inline-flex p-3 mb-3">
                                    <i class="fas fa-pencil-alt fa-2x text-white"></i>
                                </div>
                                <h4><?= $exam->exam_name ?></h4>
                                <span class="badge bg-info"><?= $exam->board_name ?></span>
                            </div>
                            
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 40%">Disciplina</th>
                                    <td><?= $exam->discipline_name ?></td>
                                </tr>
                                <tr>
                                    <th>Data</th>
                                    <td><?= date('d/m/Y', strtotime($exam->exam_date)) ?></td>
                                </tr>
                                <tr>
                                    <th>Hora</th>
                                    <td><?= $exam->exam_time ? date('H:i', strtotime($exam->exam_time)) : '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Sala</th>
                                    <td><?= $exam->exam_room ?: '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Nota Obtida</th>
                                    <td class="fw-bold <?= $result->score >= 10 ? 'text-success' : 'text-danger' ?>">
                                        <?= number_format($result->score, 1) ?> / <?= $exam->max_score ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Percentual</th>
                                    <td>
                                        <?php $percent = round(($result->score / $exam->max_score) * 100, 1); ?>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 10px;">
                                                <div class="progress-bar bg-<?= $result->score >= 10 ? 'success' : 'danger' ?>" 
                                                     style="width: <?= $percent ?>%"></div>
                                            </div>
                                            <span><?= $percent ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge bg-<?= $result->score >= 10 ? 'success' : 'danger' ?> p-2">
                                            <i class="fas fa-<?= $result->score >= 10 ? 'check-circle' : 'times-circle' ?> me-1"></i>
                                            <?= $result->score >= 10 ? 'Aprovado' : 'Reprovado' ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            
                            <?php if ($exam->description): ?>
                                <div class="mt-3">
                                    <strong>Observações:</strong>
                                    <p class="text-muted"><?= $exam->description ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar DataTables para melhor visualização
    $('.table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        pageLength: 10,
        responsive: true,
        ordering: true
    });
});

// Auto-refresh a cada 5 minutos para verificar novos resultados
setTimeout(function() {
    location.reload();
}, 300000); // 5 minutos
</script>

<style>
/* Animações e estilos adicionais */
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.table-hover tbody tr:hover {
    background-color: rgba(23, 162, 184, 0.05);
}

.progress {
    border-radius: 10px;
    background-color: #f0f0f0;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.5s ease;
}

.badge {
    font-size: 0.85em;
    padding: 0.5em 0.75em;
}

.page-header {
    margin-bottom: 1.5rem;
}

/* Animações */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.3s ease-out;
}

/* Responsividade */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }
}
</style>

<?= $this->endSection() ?>