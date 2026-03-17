<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-calendar-alt me-2"></i><?= $period['period_name'] ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/periods') ?>">Períodos</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $period['period_name'] ?></li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/exams/periods/generate-schedule/' . $period['id']) ?>" 
               class="hdr-btn success">
                <i class="fas fa-calendar-plus me-1"></i> Gerar Calendário
            </a>
            <a href="<?= site_url('admin/exams/periods/edit/' . $period['id']) ?>" 
               class="hdr-btn primary">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
            <a href="<?= site_url('admin/exams/periods') ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <div class="mt-2">
        <span class="badge-ci info">
            <i class="fas fa-calendar-alt me-1"></i>
            <?= $period['period_type'] ?> • <?= $period['semester_name'] ?> • <?= $period['year_name'] ?>
        </span>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Estatísticas com estilo do sistema -->
<div class="stat-grid mb-4">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-calendar"></i>
        </div>
        <div>
            <div class="stat-label">Período</div>
            <div class="stat-value"><?= date('d/m/Y', strtotime($period['start_date'])) ?></div>
            <div class="stat-sub">até <?= date('d/m/Y', strtotime($period['end_date'])) ?></div>
        </div>
    </div>
    
    <div class="stat-card">
        <?php
        $statusColors = [
            'Planejado' => 'secondary',
            'Em Andamento' => 'green',
            'Concluído' => 'blue',
            'Cancelado' => 'navy'
        ];
        $statusColor = $statusColors[$period['status']] ?? 'secondary';
        $statusIcon = [
            'Planejado' => 'fa-calendar',
            'Em Andamento' => 'fa-play',
            'Concluído' => 'fa-check-circle',
            'Cancelado' => 'fa-times-circle'
        ][$period['status']] ?? 'fa-circle';
        ?>
        <div class="stat-icon <?= $statusColor ?>">
            <i class="fas <?= $statusIcon ?>"></i>
        </div>
        <div>
            <div class="stat-label">Status</div>
            <div class="stat-value">
                <span class="badge-ci <?= $statusColor ?> p-2">
                    <?= $period['status'] ?>
                </span>
            </div>
            <?php
            $today = new DateTime();
            $end = new DateTime($period['end_date']);
            if ($period['status'] == 'Em Andamento' && $today <= $end) {
                $daysLeft = $today->diff($end)->days;
                echo '<div class="stat-sub">' . $daysLeft . ' dias restantes</div>';
            }
            ?>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-file-alt"></i>
        </div>
        <div>
            <div class="stat-label">Total Exames</div>
            <div class="stat-value"><?= $stats->total_exams ?? 0 ?></div>
            <div class="stat-sub">em <?= $stats->total_classes ?? 0 ?> turmas</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="stat-label">Realizados</div>
            <div class="stat-value"><?= $stats->completed_exams ?? 0 ?></div>
            <div class="stat-sub">
                <?php if (($stats->total_exams ?? 0) > 0): ?>
                    <?= round(($stats->completed_exams / $stats->total_exams) * 100) ?>% do total
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Descrição do Período -->
<?php if ($period['description']): ?>
    <div class="ci-card mb-4">
        <div class="ci-card-body">
            <h6 class="fw-semibold mb-2"><i class="fas fa-info-circle me-2 text-primary"></i>Descrição:</h6>
            <p class="mb-0"><?= $period['description'] ?></p>
        </div>
    </div>
<?php endif; ?>

<!-- Lista de Exames Agendados -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-table"></i>
            <span>Exames Agendados</span>
        </div>
        <div>
            <a href="<?= site_url('admin/exams/schedules/create?period_id=' . $period['id']) ?>" 
               class="hdr-btn primary" style="padding: 0.3rem 0.8rem; font-size: 0.75rem;">
                <i class="fas fa-plus me-1"></i> Adicionar Exame
            </a>
        </div>
    </div>
    <div class="ci-card-body p0">
        <?php if (!empty($exams)): ?>
            <div class="table-responsive">
                <table id="examsTable" class="ci-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Turma</th>
                            <th>Disciplina</th>
                            <th>Tipo</th>
                            <th>Sala</th>
                            <th class="center">Status</th>
                            <th class="center">Ações</th>
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
                            $examStatusColor = $examStatusColors[$exam['status']] ?? 'secondary';
                            
                            // Verificar se já passou
                            $today = date('Y-m-d');
                            $isPast = $exam['exam_date'] < $today && $exam['status'] == 'Agendado';
                            if ($isPast) {
                                $examStatusColor = 'danger';
                            }
                            ?>
                            <tr class="<?= $isPast ? 'row-warning' : '' ?>">
                                <td>
                                    <span class="fw-medium">
                                        <?= date('d/m/Y', strtotime($exam['exam_date'])) ?>
                                    </span>
                                    <?php if ($isPast): ?>
                                        <span class="badge-ci danger ms-2">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $exam['exam_time'] ? substr($exam['exam_time'], 0, 5) : '—' ?></td>
                                <td>
                                    <span class="fw-semibold"><?= $exam['class_name'] ?></span>
                                    <br>
                                    <small class="text-muted"><?= $exam['class_code'] ?></small>
                                </td>
                                <td>
                                    <?= $exam['discipline_name'] ?>
                                    <br>
                                    <small class="text-muted"><?= $exam['discipline_code'] ?></small>
                                </td>
                                <td>
                                    <span class="badge-ci info"><?= $exam['board_type'] ?></span>
                                </td>
                                <td><?= $exam['exam_room'] ?: '—' ?></td>
                                <td class="center">
                                    <span class="badge-ci <?= $examStatusColor ?> p-2">
                                        <?= $exam['status'] ?>
                                    </span>
                                </td>
                                <td class="center">
                                    <div class="action-group">
                                        <a href="<?= site_url('admin/exams/schedules/view/' . $exam['id']) ?>" 
                                           class="row-btn view" title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($exam['status'] == 'Agendado' && !$isPast): ?>
                                            <a href="<?= site_url('admin/exams/schedules/attendance/' . $exam['id']) ?>" 
                                               class="row-btn success" title="Registar presenças">
                                                <i class="fas fa-user-check"></i>
                                            </a>
                                            <a href="<?= site_url('admin/exams/schedules/results/' . $exam['id']) ?>" 
                                               class="row-btn warning" title="Registar notas">
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
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h5>Nenhum exame agendado</h5>
                <p class="text-muted mb-3">
                    Utilize a opção "Gerar Calendário" para agendar automaticamente<br>
                    ou adicione exames manualmente.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="<?= site_url('admin/exams/periods/generate-schedule/' . $period['id']) ?>" 
                       class="btn-ci success">
                        <i class="fas fa-calendar-plus me-2"></i>Gerar Calendário
                    </a>
                    <a href="<?= site_url('admin/exams/schedules/create?period_id=' . $period['id']) ?>" 
                       class="btn-ci primary">
                        <i class="fas fa-plus me-2"></i>Adicionar Manualmente
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar DataTable com configuração padrão
    if (typeof initDataTable !== 'undefined') {
        initDataTable('#examsTable', {
            order: [[0, 'asc']],
            pageLength: 25,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
            }
        });
    } else {
        // Fallback se a função global não existir
        $('#examsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
            },
            order: [[0, 'asc']],
            pageLength: 25,
            dom: 'rtip'
        });
    }
    
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<style>
/* Estilos adicionais específicos para esta página */
.row-warning {
    background: rgba(232,160,32,0.05);
    border-left: 3px solid var(--warning);
}

/* Ajustes para DataTables */
.dataTables_wrapper {
    padding: 0.85rem 1.25rem;
}

.dataTables_filter input,
.dataTables_length select {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.3rem 0.6rem;
    font-size: 0.8rem;
    font-family: var(--font);
    color: var(--text-primary);
    background: var(--surface);
}

.dataTables_filter input:focus,
.dataTables_length select:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,0.12);
}

.dataTables_info,
.dataTables_filter label,
.dataTables_length label {
    font-size: 0.78rem;
    color: var(--text-secondary);
}

.dataTables_paginate .paginate_button {
    border-radius: 6px !important;
    font-size: 0.78rem !important;
    font-family: var(--font) !important;
}

.dataTables_paginate .paginate_button.current {
    background: var(--accent) !important;
    color: #fff !important;
    border-color: var(--accent) !important;
}

/* Badge-ci pequeno para ícone de alerta */
.badge-ci.danger {
    background: rgba(232,70,70,0.1);
    color: var(--danger);
    padding: 0.2rem 0.4rem;
    font-size: 0.7rem;
}

/* Responsividade */
@media (max-width: 768px) {
    .stat-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .ci-table td {
        min-width: 120px;
    }
    
    .ci-table td:first-child {
        min-width: auto;
    }
}
</style>
<?= $this->endSection() ?>