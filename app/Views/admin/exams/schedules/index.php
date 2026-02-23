<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-check me-1"></i>
                Visualize e gerencie todos os exames agendados
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/exams/schedules/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Novo Agendamento
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active">Agendamentos</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros Avançados -->
<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-filter me-2 text-primary"></i>
            Filtrar Agendamentos
        </h5>
    </div>
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Período de Exame</label>
                <select class="form-select" name="period_id" onchange="this.form.submit()">
                    <option value="">Todos os períodos</option>
                    <?php foreach ($periods as $period): ?>
                        <option value="<?= $period->id ?>" <?= request()->getGet('period_id') == $period->id ? 'selected' : '' ?>>
                            <?= $period->period_name ?> (<?= $period->period_type ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Turma</label>
                <select class="form-select" name="class_id" onchange="this.form.submit()">
                    <option value="">Todas as turmas</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class->id ?>" <?= request()->getGet('class_id') == $class->id ? 'selected' : '' ?>>
                            <?= $class->class_name ?> (<?= $class->class_code ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Data</label>
                <input type="date" class="form-control" name="date" value="<?= request()->getGet('date') ?>" onchange="this.form.submit()">
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <a href="<?= site_url('admin/exams/schedules') ?>" class="btn btn-secondary w-100">
                    <i class="fas fa-undo me-1"></i> Limpar Filtros
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Calendário/Lista de Exames -->
<div class="card">
    <div class="card-header bg-white py-3">
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
    
    <div class="card-body p-0">
        <div class="tab-content">
            <!-- Visualização em Lista -->
            <div class="tab-pane active" id="list-view">
                <?php if (!empty($schedules)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-3 ps-3">Data/Hora</th>
                                    <th class="border-0 py-3">Período</th>
                                    <th class="border-0 py-3">Turma</th>
                                    <th class="border-0 py-3">Disciplina</th>
                                    <th class="border-0 py-3">Tipo</th>
                                    <th class="border-0 py-3">Sala</th>
                                    <th class="border-0 py-3 text-center">Status</th>
                                    <th class="border-0 py-3 text-center pe-3">Ações</th>
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
                                    $statusColor = $statusColors[$schedule->status] ?? 'secondary';
                                    
                                    $isPast = $schedule->exam_date < $today && $schedule->status == 'Agendado';
                                    if ($isPast) {
                                        $statusColor = 'danger';
                                        $statusText = 'Atrasado';
                                    } else {
                                        $statusText = $schedule->status;
                                    }
                                    ?>
                                    <tr class="<?= $isPast ? 'bg-light' : '' ?>">
                                        <td class="ps-3">
                                            <div class="fw-semibold"><?= date('d/m/Y', strtotime($schedule->exam_date)) ?></div>
                                            <small class="text-muted"><?= $schedule->exam_time ? substr($schedule->exam_time, 0, 5) : 'Horário não definido' ?></small>
                                        </td>
                                        <td>
                                            <span class="fw-medium"><?= $schedule->period_name ?></span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold"><?= $schedule->class_name ?></span>
                                        </td>
                                        <td>
                                            <?= $schedule->discipline_name ?>
                                            <br>
                                            <small class="text-muted"><?= $schedule->board_name ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= $schedule->board_type ?></span>
                                        </td>
                                        <td><?= $schedule->exam_room ?: '—' ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-<?= $statusColor ?> p-2">
                                                <?= $statusText ?>
                                            </span>
                                            <?php if ($schedule->status == 'Realizado'): ?>
                                                <br>
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle"></i> Concluído
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center pe-3">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= site_url('admin/exams/schedules/view/' . $schedule->id) ?>" 
                                                   class="btn btn-outline-primary" title="Ver detalhes">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($schedule->status == 'Agendado' && !$isPast): ?>
                                                    <a href="<?= site_url('admin/exams/schedules/attendance/' . $schedule->id) ?>" 
                                                       class="btn btn-outline-success" title="Registar presenças">
                                                        <i class="fas fa-user-check"></i>
                                                    </a>
                                                    <a href="<?= site_url('admin/exams/schedules/results/' . $schedule->id) ?>" 
                                                       class="btn btn-outline-warning" title="Registar notas">
                                                        <i class="fas fa-graduation-cap"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?= site_url('admin/exams/schedules/edit/' . $schedule->id) ?>" 
                                                   class="btn btn-outline-secondary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginação -->
                    <?php if (isset($pager) && $pager): ?>
                        <div class="d-flex justify-content-center mt-4">
                            <?= $pager->links() ?>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum exame agendado</h5>
                        <p class="text-muted mb-3">
                            Comece por agendar um novo exame ou gerar calendário automático.
                        </p>
                        <a href="<?= site_url('admin/exams/schedules/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Agendar Exame
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Visualização em Calendário -->
            <!-- Visualização em Calendário -->
            <div class="tab-pane" id="calendar-view">
                <div class="p-3">
                    <!-- Legenda de cores -->
                    <div class="mb-3 d-flex gap-3 align-items-center flex-wrap">
                        <div class="d-flex align-items-center">
                            <span class="badge me-1" style="background-color: #0d6efd; width: 20px; height: 20px;"></span>
                            <small>Agendado</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge me-1" style="background-color: #198754; width: 20px; height: 20px;"></span>
                            <small>Realizado</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge me-1" style="background-color: #dc3545; width: 20px; height: 20px;"></span>
                            <small>Cancelado</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge me-1" style="background-color: #ffc107; width: 20px; height: 20px;"></span>
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

<!-- Modal de Confirmação de Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar este agendamento?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita.
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="deleteConfirmBtn" class="btn btn-danger">
                    <i class="fas fa-trash me-2"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Scripts do Calendário -->
<!-- FullCalendar CSS e JS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt-br.js'></script>

<style>
.card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    background: none;
    border-bottom: 2px solid #0d6efd;
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
</style>

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
</script>
<!-- Script do Calendário - VERSÃO CORRIGIDA -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips do Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

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
                        // Redirecionar para a página de detalhes
                        window.location.href = '<?= site_url('admin/exams/schedules/view/') ?>/' + info.event.id;
                    },
                    eventDidMount: function(info) {
                        // Adicionar tooltip
                        info.el.setAttribute('data-bs-toggle', 'tooltip');
                        info.el.setAttribute('data-bs-placement', 'top');
                        info.el.setAttribute('title', info.event.title + ' - ' + (info.event.extendedProps?.room || 'Sala não definida'));
                        
                        // Inicializar tooltip
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
                
                // Salvar referência para redimensionamento
                window.examCalendar = calendar;
            })
            .catch(error => {
                console.error('Erro ao carregar eventos:', error);
                calendarEl.innerHTML = '<div class="alert alert-danger">Erro ao carregar calendário. Tente novamente mais tarde.</div>';
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
<?= $this->endSection() ?>