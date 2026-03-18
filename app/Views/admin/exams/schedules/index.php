<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-calendar-check me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Agendamentos</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/exams/schedules/create') ?>" class="hdr-btn primary">
                <i class="fas fa-plus me-1"></i> Novo Agendamento
            </a>
        </div>
    </div>
    <!-- Mostrar o ano atual -->
    <div class="alert-ci info mt-3">
        <i class="fas fa-calendar-alt me-2"></i>
        Mostrando exames do ano letivo: <strong><?= $currentYearId ?></strong>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros Avançados com estilo do sistema -->
<div class="ci-card mb-4">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-filter"></i>
            <span>Filtrar Agendamentos</span>
        </div>
    </div>
    <div class="ci-card-body">
        <form method="get" class="filter-grid">
            <div>
                <label class="filter-label">Período de Exame</label>
                <select class="filter-select" name="period_id" onchange="this.form.submit()">
                    <option value="">Todos os períodos</option>
                    <?php foreach ($periods as $period): ?>
                        <option value="<?= $period['id'] ?>" <?= request()->getGet('period_id') == $period['id'] ? 'selected' : '' ?>>
                            <?= $period['period_name'] ?> (<?= $period['period_type'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="filter-label">Turma</label>
                <select class="filter-select" name="class_id" onchange="this.form.submit()">
                    <option value="">Todas as turmas</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['id'] ?>" <?= request()->getGet('class_id') == $class['id'] ? 'selected' : '' ?>>
                            <?= $class['class_name'] ?> (<?= $class['class_code'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="filter-label">Data</label>
                <input type="date" class="filter-select" name="date" value="<?= request()->getGet('date') ?>" onchange="this.form.submit()">
            </div>
            
            <div class="filter-actions" style="grid-column: -1 / 1;">
                <a href="<?= site_url('admin/exams/schedules') ?>" class="btn-filter clear">
                    <i class="fas fa-undo me-1"></i> Limpar Filtros
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Calendário/Lista de Exames -->
<div class="ci-card">
    <div class="ci-card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#list-view">
                    <i class="fas fa-list me-2"></i>Lista
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#calendar-view">
                    <i class="fas fa-calendar-alt me-2"></i>Calendário
                </a>
            </li>
        </ul>
    </div>
    
    <div class="ci-card-body p0">
        <div class="tab-content">
            <!-- Visualização em Lista com DataTable -->
            <div class="tab-pane active" id="list-view">
                <?php if (!empty($schedules)): ?>
                    <div class="table-responsive">
                        <table id="schedulesTable" class="ci-table">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Período</th>
                                    <th>Turma</th>
                                    <th>Disciplina</th>
                                    <th>Tipo</th>
                                    <th>Sala</th>
                                    <th class="center">Status</th>
                                    <th class="center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedules as $schedule): ?>
                                    <?php
                                    $today = date('Y-m-d');
                                    $statusColors = [
                                        'Agendado' => 'secondary',
                                        'Realizado' => 'success',
                                        'Cancelado' => 'danger',
                                        'Adiado' => 'warning'
                                    ];
                                    $statusColor = $statusColors[$schedule['status']] ?? 'secondary';
                                    
                                    $isPast = $schedule['exam_date'] < $today && $schedule['status'] == 'Agendado';
                                    if ($isPast) {
                                        $statusColor = 'danger';
                                        $statusText = 'Atrasado';
                                    } else {
                                        $statusText = $schedule['status'];
                                    }
                                    ?>
                                    <tr class="<?= $isPast ? 'row-warning' : '' ?>">
                                        <td>
                                            <div class="fw-semibold"><?= date('d/m/Y', strtotime($schedule['exam_date'])) ?></div>
                                            <small class="text-muted"><?= $schedule['exam_time'] ? substr($schedule['exam_time'], 0, 5) : 'Horário não definido' ?></small>
                                        </td>
                                        <td>
                                            <span class="fw-medium"><?= $schedule['period_name'] ?></span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold"><?= $schedule['class_name'] ?></span>
                                        </td>
                                        <td>
                                            <?= $schedule['discipline_name'] ?>
                                            <br>
                                            <small class="text-muted"><?= $schedule['board_name'] ?></small>
                                        </td>
                                        <td>
                                            <span class="badge-ci info"><?= $schedule['board_type'] ?></span>
                                        </td>
                                        <td><?= $schedule['exam_room'] ?: '—' ?></td>
                                        <td class="center">
                                            <span class="badge-ci <?= $statusColor ?> p-2">
                                                <?= $statusText ?>
                                            </span>
                                            <?php if ($schedule['status'] == 'Realizado'): ?>
                                                <br>
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle"></i> Concluído
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="center">
                                            <div class="action-group">
                                                <a href="<?= site_url('admin/exams/schedules/view/' . $schedule['id']) ?>" 
                                                   class="row-btn view" title="Ver detalhes">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($schedule['status'] == 'Agendado' && !$isPast): ?>
                                                    <a href="<?= site_url('admin/exams/schedules/attendance/' . $schedule['id']) ?>" 
                                                       class="row-btn success" title="Registar presenças">
                                                        <i class="fas fa-user-check"></i>
                                                    </a>
                                                    <a href="<?= site_url('admin/exams/schedules/results/' . $schedule['id']) ?>" 
                                                       class="row-btn warning" title="Registar notas">
                                                        <i class="fas fa-graduation-cap"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?= site_url('admin/exams/schedules/edit/' . $schedule['id']) ?>" 
                                                   class="row-btn edit" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
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
                            Comece por agendar um novo exame ou gerar calendário automático.
                        </p>
                        <a href="<?= site_url('admin/exams/schedules/create') ?>" class="btn-ci primary">
                            <i class="fas fa-plus me-2"></i>Agendar Exame
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Visualização em Calendário -->
            <div class="tab-pane" id="calendar-view">
                <div class="p-3">
                    <!-- Legenda de cores -->
                    <div class="mb-3 d-flex gap-3 align-items-center flex-wrap">
                        <div class="d-flex align-items-center">
                            <span class="badge-ci secondary me-1" style="width: 20px; height: 20px;"></span>
                            <small>Agendado</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge-ci success me-1" style="width: 20px; height: 20px;"></span>
                            <small>Realizado</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge-ci danger me-1" style="width: 20px; height: 20px;"></span>
                            <small>Cancelado</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge-ci warning me-1" style="width: 20px; height: 20px;"></span>
                            <small>Adiado</small>
                        </div>
                    </div>
                    
                    <!-- Container do Calendário -->
                    <div id="examCalendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Delete com estilo do sistema -->
<div class="modal fade ci-modal" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header danger-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar este agendamento?</p>
                <div class="alert-ci danger">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-filter clear" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <a href="#" id="deleteConfirmBtn" class="btn-filter danger">
                    <i class="fas fa-trash me-2"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>


<script>
function confirmDelete(id) {
    document.getElementById('deleteConfirmBtn').href = '<?= site_url('admin/exams/schedules/delete') ?>/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Auto-submit filters on change
document.querySelectorAll('.filter-select').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});

// DataTable initialization
$(document).ready(function() {
    $('#schedulesTable').DataTable({
        
    });
    
    // Inicializar tooltips do Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Script do Calendário
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('examCalendar');
    
    if (calendarEl) {
        // Buscar eventos via AJAX
        fetch('<?= site_url('admin/exams/schedules/calendar-events') ?>')
            .then(response => response.json())
            .then(events => {
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    locale: 'pt-br',
                    buttonText: {
                        today: 'Hoje',
                        month: 'Mês',
                        week: 'Semana',
                        day: 'Dia'
                    },
                    events: events,
                    eventClick: function(info) {
                        window.location.href = '<?= site_url('admin/exams/schedules/view/') ?>/' + info.event.id;
                    },
                    eventDidMount: function(info) {
                        info.el.setAttribute('data-bs-toggle', 'tooltip');
                        info.el.setAttribute('data-bs-placement', 'top');
                        info.el.setAttribute('title', info.event.title + ' - ' + (info.event.extendedProps?.room || 'Sala não definida'));
                        
                        if (typeof bootstrap !== 'undefined') {
                            new bootstrap.Tooltip(info.el);
                        }
                    },
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    },
                    displayEventTime: true
                });
                
                calendar.render();
                window.examCalendar = calendar;
            })
            .catch(error => {
                console.error('Erro ao carregar eventos:', error);
                calendarEl.innerHTML = '<div class="alert-ci danger">Erro ao carregar calendário. Tente novamente mais tarde.</div>';
            });
    }
});

// Atualizar calendário quando a tab for clicada
document.querySelector('a[href="#calendar-view"]')?.addEventListener('shown.bs.tab', function(e) {
    if (window.examCalendar) {
        window.examCalendar.updateSize();
    }
});
</script>

<style>
/* Estilos adicionais específicos para esta página */
.nav-tabs .nav-link {
    border: none;
    color: var(--text-secondary);
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    font-size: 0.82rem;
    transition: all 0.18s;
}

.nav-tabs .nav-link:hover {
    color: var(--accent);
    background: rgba(59,127,232,0.05);
}

.nav-tabs .nav-link.active {
    color: var(--accent);
    background: none;
    border-bottom: 2px solid var(--accent);
}

/* Linhas de aviso (atrasadas) */
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

/* FullCalendar customizations */
.fc-event {
    cursor: pointer;
    border-radius: 4px;
    border: none;
    padding: 2px 4px;
}

.fc-event-title {
    font-weight: 500;
    font-size: 0.85rem;
}

.fc .fc-button-primary {
    background: var(--surface);
    border-color: var(--border);
    color: var(--text-secondary);
    font-size: 0.8rem;
    padding: 0.3rem 0.8rem;
    transition: all 0.18s;
}

.fc .fc-button-primary:hover {
    background: var(--surface);
    border-color: var(--accent);
    color: var(--accent);
}

.fc .fc-button-primary:not(:disabled).fc-button-active,
.fc .fc-button-primary:not(:disabled):active {
    background: var(--accent);
    border-color: var(--accent);
    color: #fff;
}

.fc-toolbar-title {
    font-size: 1.2rem !important;
    font-weight: 600;
    color: var(--text-primary);
}

/* Badge-ci como quadrado para legenda */
.badge-ci[style*="width: 20px"] {
    border-radius: 4px;
    display: inline-block;
}

/* Responsividade */
@media (max-width: 768px) {
    .nav-tabs .nav-link {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
    
    .dataTables_wrapper {
        padding: 0.5rem;
    }
    
    .action-group {
        flex-wrap: wrap;
    }
}
</style>
<?= $this->endSection() ?>