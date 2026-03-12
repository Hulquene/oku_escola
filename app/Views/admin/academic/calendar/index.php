<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
/* Estilos específicos para o calendário */
:root {
    --fc-border-color: var(--border);
    --fc-today-bg-color: rgba(59,127,232,0.05);
    --fc-page-bg-color: var(--surface-card);
    --fc-neutral-bg-color: var(--surface);
    --fc-list-event-hover-bg-color: var(--surface);
    --fc-event-border-color: transparent;
}

/* Container do calendário */
.calendar-container {
    background: var(--surface-card);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: box-shadow .2s;
}

.calendar-container:hover {
    box-shadow: var(--shadow-md);
}

/* Header do calendário */
.fc .fc-toolbar {
    padding: 1.25rem 1.5rem;
    margin: 0 !important;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
}

.fc .fc-toolbar-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--text-primary);
}

.fc .fc-button {
    background: var(--surface-card);
    border: 1.5px solid var(--border);
    color: var(--text-secondary);
    font-weight: 600;
    font-size: 0.85rem;
    padding: 0.45rem 1rem;
    border-radius: var(--radius-sm);
    transition: all .18s;
    text-transform: capitalize;
    box-shadow: none !important;
}

.fc .fc-button:hover {
    background: var(--surface);
    border-color: var(--accent);
    color: var(--accent);
}

.fc .fc-button-primary {
    background: var(--accent);
    border-color: var(--accent);
    color: #fff;
}

.fc .fc-button-primary:hover {
    background: var(--accent-hover);
    border-color: var(--accent-hover);
    color: #fff;
}

.fc .fc-button-active {
    background: var(--accent) !important;
    border-color: var(--accent) !important;
    color: #fff !important;
}

/* Cabeçalho dos dias */
.fc .fc-col-header-cell {
    background: var(--surface);
    border-color: var(--border);
    padding: 0.75rem 0;
}

.fc .fc-col-header-cell-cushion {
    font-weight: 700;
    font-size: 0.85rem;
    color: var(--text-primary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    text-decoration: none;
}

/* Células do calendário */
.fc .fc-daygrid-day {
    border-color: var(--border);
    transition: background .12s;
}

.fc .fc-daygrid-day:hover {
    background: rgba(59,127,232,0.02);
}

.fc .fc-daygrid-day-number {
    font-size: 0.85rem;
    color: var(--text-primary);
    text-decoration: none;
    padding: 0.5rem 0.5rem 0 0;
}

.fc .fc-day-today {
    background: rgba(59,127,232,0.03) !important;
}

.fc .fc-day-today .fc-daygrid-day-number {
    font-weight: 700;
    color: var(--accent);
}

/* Eventos */
.fc-event {
    border-radius: 6px;
    padding: 2px 6px;
    margin: 1px 4px;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: transform .15s, box-shadow .15s;
    border: none !important;
}

.fc-event:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.fc-event-title {
    font-weight: 600;
    line-height: 1.3;
}

/* Eventos por tipo (cores personalizadas) */
.fc-event.feriado {
    background: linear-gradient(135deg, #6B7A99 0%, #5A6988 100%);
    color: #fff;
}

.fc-event.reuniao {
    background: linear-gradient(135deg, #3B7FE8 0%, #2C6FD4 100%);
    color: #fff;
}

.fc-event.prova {
    background: linear-gradient(135deg, #E84646 0%, #D12E2E 100%);
    color: #fff;
}

.fc-event.entrega {
    background: linear-gradient(135deg, #16A87D 0%, #0E8A64 100%);
    color: #fff;
}

.fc-event.matricula {
    background: linear-gradient(135deg, #E8A020 0%, #D18C1C 100%);
    color: #fff;
}

.fc-event.inscricao {
    background: linear-gradient(135deg, #7C4DFF 0%, #6A3FCC 100%);
    color: #fff;
}

.fc-event.outro {
    background: linear-gradient(135deg, #6B7A99 0%, #5A6988 100%);
    color: #fff;
}

/* Fim de semestre (eventos especiais) */
.fc-event.semestre-concluido {
    background: linear-gradient(135deg, #16A87D 0%, #0E8A64 100%);
    border-left: 3px solid #fff !important;
}

.fc-event.semestre-processado {
    background: linear-gradient(135deg, #E8A020 0%, #D18C1C 100%);
    border-left: 3px solid #fff !important;
}

.fc-event.semestre-inativo {
    background: linear-gradient(135deg, #9AA5BE 0%, #8A97B2 100%);
    border-left: 3px solid #fff !important;
}

.fc-event.semestre-atual {
    background: linear-gradient(135deg, #3B7FE8 0%, #2C6FD4 100%);
    border-left: 3px solid #fff !important;
}

/* Modal personalizado */
.calendar-modal .modal-content {
    border: none;
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

.calendar-modal .modal-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border: none;
    padding: 1.25rem 1.5rem;
}

.calendar-modal .modal-title {
    color: #fff;
    font-weight: 700;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.calendar-modal .modal-title i {
    font-size: 0.9rem;
    opacity: 0.8;
}

.calendar-modal .btn-close-white {
    filter: brightness(0) invert(1);
    opacity: 0.8;
}

.calendar-modal .btn-close-white:hover {
    opacity: 1;
}

.calendar-modal .modal-body {
    padding: 1.5rem;
}

.calendar-modal .modal-footer {
    border-top: 1px solid var(--border);
    padding: 1rem 1.5rem;
}

/* Formulário */
.calendar-modal .form-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
    margin-bottom: 0.3rem;
}

.calendar-modal .form-control,
.calendar-modal .form-select {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
    color: var(--text-primary);
    transition: border-color .18s;
}

.calendar-modal .form-control:focus,
.calendar-modal .form-select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,0.1);
    outline: none;
}

.calendar-modal .form-check-input {
    border: 1.5px solid var(--border);
    border-radius: 4px;
}

.calendar-modal .form-check-input:checked {
    background-color: var(--accent);
    border-color: var(--accent);
}

/* Botões do modal */
.calendar-modal .btn {
    padding: 0.5rem 1.2rem;
    font-size: 0.85rem;
    font-weight: 600;
    border-radius: var(--radius-sm);
    transition: all .18s;
}

.calendar-modal .btn-primary {
    background: var(--accent);
    border: none;
    color: #fff;
    box-shadow: 0 3px 10px rgba(59,127,232,0.28);
}

.calendar-modal .btn-primary:hover {
    background: var(--accent-hover);
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(59,127,232,0.35);
}

.calendar-modal .btn-danger {
    background: var(--danger);
    border: none;
    color: #fff;
    box-shadow: 0 3px 10px rgba(232,70,70,0.28);
}

.calendar-modal .btn-danger:hover {
    background: #c03939;
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(232,70,70,0.35);
}

.calendar-modal .btn-secondary {
    background: var(--surface);
    border: 1.5px solid var(--border);
    color: var(--text-secondary);
}

.calendar-modal .btn-secondary:hover {
    background: #fff;
    border-color: var(--accent);
    color: var(--accent);
}

/* Legenda */
.legend-container {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.75rem 1rem;
}

.legend-item {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    margin-right: 1.5rem;
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 3px;
}

/* Filtro */
.filter-card {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}

.filter-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-secondary);
    margin-right: 0.5rem;
}

.filter-select {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.4rem 2rem 0.4rem 0.8rem;
    font-size: 0.85rem;
    color: var(--text-primary);
    background: var(--surface);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B7A99' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.6rem center;
    cursor: pointer;
}

.filter-select:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,0.1);
}

/* Page Header customizado */
.calendar-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 60%, #2D4A7A 100%);
    border-radius: var(--radius);
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

.calendar-header::before {
    content: '';
    position: absolute;
    top: -60px;
    right: -60px;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
    pointer-events: none;
}

.calendar-header::after {
    content: '';
    position: absolute;
    bottom: -40px;
    right: 100px;
    width: 130px;
    height: 130px;
    border-radius: 50%;
    background: rgba(59,127,232,0.15);
    pointer-events: none;
}

.calendar-header h1 {
    font-size: 1.4rem;
    font-weight: 700;
    color: #fff;
    margin: 0 0 0.2rem;
    letter-spacing: -0.3px;
    position: relative;
    z-index: 1;
}

.calendar-header .breadcrumb {
    margin: 0;
    padding: 0;
    background: transparent;
    position: relative;
    z-index: 1;
}

.calendar-header .breadcrumb-item a {
    color: rgba(255,255,255,0.6);
    text-decoration: none;
    font-size: 0.8rem;
}

.calendar-header .breadcrumb-item a:hover {
    color: #fff;
}

.calendar-header .breadcrumb-item.active {
    color: rgba(255,255,255,0.4);
}

/* Responsive */
@media (max-width: 768px) {
    .fc .fc-toolbar {
        flex-direction: column;
        gap: 1rem;
    }
    
    .fc .fc-toolbar .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
        width: 100%;
    }
    
    .filter-card {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .legend-item {
        margin-right: 1rem;
    }
}
</style>

<!-- Page Header -->
<div class="calendar-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>
                <i class="fas fa-calendar-alt me-2" style="opacity:.8;"></i>
                <?= $title ?>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/academic/years') ?>">Académico</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Calendário</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eventModal">
            <i class="fas fa-plus-circle me-1"></i> Novo Evento
        </button>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtro e Legenda -->
<div class="filter-card">
    <div class="d-flex align-items-center gap-3">
        <div>
            <span class="filter-label">Ano Letivo</span>
            <select class="filter-select" id="academicYearFilter">
                <option value="">Todos os Anos</option>
                <?php if (!empty($academicYears)): ?>
                    <?php foreach ($academicYears as $year): ?>
                        <option value="<?= $year['id'] ?>" <?= $selectedYear == $year['id'] ? 'selected' : '' ?>>
                            <?= $year['year_name'] ?> <?= $year['id'] == current_academic_year() ? '(Atual)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        
        <div class="vr" style="height: 30px;"></div>
        
        <div class="legend-container">
            <span class="legend-item">
                <span class="legend-color" style="background: linear-gradient(135deg, #3B7FE8 0%, #2C6FD4 100%);"></span>
                Eventos Manuais
            </span>
            <span class="legend-item">
                <span class="legend-color" style="background: linear-gradient(135deg, #E84646 0%, #D12E2E 100%);"></span>
                Exames
            </span>
            <span class="legend-item">
                <span class="legend-color" style="background: linear-gradient(135deg, #16A87D 0%, #0E8A64 100%);"></span>
                Concluído
            </span>
            <span class="legend-item">
                <span class="legend-color" style="background: linear-gradient(135deg, #E8A020 0%, #D18C1C 100%);"></span>
                Processado
            </span>
            <span class="legend-item">
                <span class="legend-color" style="background: linear-gradient(135deg, #6B7A99 0%, #5A6988 100%);"></span>
                Inativo
            </span>
            <span class="legend-item">
                <span class="legend-color" style="background: linear-gradient(135deg, #7C4DFF 0%, #6A3FCC 100%);"></span>
                Atual
            </span>
        </div>
    </div>
</div>

<!-- Calendar Container -->
<div class="calendar-container">
    <div id="calendar"></div>
</div>

<!-- Event Modal -->
<div class="modal fade calendar-modal" id="eventModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="eventForm" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="eventId">
                
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-plus" id="modalIcon"></i>
                        <span id="modalTitle">Novo Evento</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="academic_year_id" class="form-label">Ano Letivo</label>
                            <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                                <option value="">Selecione...</option>
                                <?php if (!empty($academicYears)): ?>
                                    <?php foreach ($academicYears as $year): ?>
                                        <option value="<?= $year['id'] ?>" <?= $selectedYear == $year['id'] ? 'selected' : '' ?>>
                                            <?= $year['year_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <label for="event_title" class="form-label">Título do Evento</label>
                            <input type="text" class="form-control" id="event_title" name="event_title" 
                                   placeholder="Ex: Reunião de Pais" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="event_type" class="form-label">Tipo</label>
                            <select class="form-select" id="event_type" name="event_type" required>
                                <option value="">Selecione...</option>
                                <option value="Feriado">Feriado</option>
                                <option value="Reunião">Reunião</option>
                                <option value="Prova">Prova</option>
                                <option value="Entrega de Notas">Entrega de Notas</option>
                                <option value="Matrícula">Matrícula</option>
                                <option value="Inscrição">Inscrição</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="color" class="form-label">Cor</label>
                            <select class="form-select" id="color" name="color">
                                <option value="#3788d8">Azul</option>
                                <option value="#28a745">Verde</option>
                                <option value="#dc3545">Vermelho</option>
                                <option value="#ffc107">Amarelo</option>
                                <option value="#6f42c1">Roxo</option>
                                <option value="#fd7e14">Laranja</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Data Início</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">Data Fim</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                            <small class="text-muted">Deixe em branco para 1 dia</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="start_time" class="form-label">Hora Início</label>
                            <input type="time" class="form-control" id="start_time" name="start_time">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="end_time" class="form-label">Hora Fim</label>
                            <input type="time" class="form-control" id="end_time" name="end_time">
                        </div>
                        
                        <div class="col-12">
                            <label for="location" class="form-label">Local</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   placeholder="Ex: Auditório Principal">
                        </div>
                        
                        <div class="col-12">
                            <label for="event_description" class="form-label">Descrição</label>
                            <textarea class="form-control" id="event_description" name="event_description" 
                                      rows="3" placeholder="Detalhes adicionais..."></textarea>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="all_day" name="all_day" value="1">
                                <label class="form-check-label" for="all_day">
                                    Evento de dia inteiro
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="deleteEventBtn" style="display: none;">
                        <i class="fas fa-trash me-1"></i> Eliminar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- FullCalendar -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // CSRF Token
    const csrfToken = '<?= csrf_token() ?>';
    const csrfHash = '<?= csrf_hash() ?>';
    
    const calendarEl = document.getElementById('calendar');
    const academicYearFilter = document.getElementById('academicYearFilter');
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'pt',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        buttonText: {
            today: 'Hoje',
            month: 'Mês',
            week: 'Semana',
            day: 'Dia',
            list: 'Lista'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch('<?= site_url('admin/academic/calendar/get-events') ?>?' + new URLSearchParams({
                start: fetchInfo.startStr,
                end: fetchInfo.endStr,
                academic_year: academicYearFilter.value
            }), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Processar dados para adicionar classes CSS baseado no tipo
                data.forEach(event => {
                    // Adicionar classe baseada no tipo
                    if (event.extendedProps?.event_type) {
                        event.className = event.extendedProps.event_type.toLowerCase().replace(' ', '-');
                    }
                });
                successCallback(data);
            })
            .catch(error => failureCallback(error));
        },
        eventClick: function(info) {
            editEvent(info.event);
        },
        dateClick: function(info) {
            createEvent(info.dateStr);
        },
        editable: true,
        eventDrop: function(info) {
            updateEventDate(info.event);
        },
        eventResize: function(info) {
            updateEventDate(info.event);
        },
        eventDidMount: function(info) {
            // Adicionar tooltip
            const eventEl = info.el;
            const event = info.event;
            
            if (event.extendedProps?.description) {
                eventEl.title = event.extendedProps.description;
                eventEl.setAttribute('data-bs-toggle', 'tooltip');
                eventEl.setAttribute('data-bs-placement', 'top');
            }
        },
        loading: function(isLoading) {
            if (isLoading) {
                // Mostrar loading
            } else {
                // Esconder loading
            }
        }
    });
    
    calendar.render();
    
    // Filtrar por ano letivo
    academicYearFilter.addEventListener('change', function() {
        calendar.refetchEvents();
    });
    
    // Form submission
    document.getElementById('eventForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveEvent();
    });
    
    // Delete button
    document.getElementById('deleteEventBtn').addEventListener('click', function() {
        Swal.fire({
            title: 'Confirmar Eliminação',
            text: 'Tem certeza que deseja eliminar este evento?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#E84646',
            cancelButtonColor: '#6B7A99',
            confirmButtonText: 'Sim, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteEvent();
            }
        });
    });
    
    // Reset modal when hidden
    document.getElementById('eventModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('eventForm').reset();
        document.getElementById('eventId').value = '';
        document.getElementById('deleteEventBtn').style.display = 'none';
    });
});

function createEvent(date) {
    document.getElementById('modalIcon').className = 'fas fa-calendar-plus';
    document.getElementById('modalTitle').textContent = 'Novo Evento';
    document.getElementById('eventForm').reset();
    document.getElementById('eventId').value = '';
    document.getElementById('deleteEventBtn').style.display = 'none';
    document.getElementById('start_date').value = date;
    document.getElementById('end_date').value = date;
    
    const modal = new bootstrap.Modal(document.getElementById('eventModal'));
    modal.show();
}

function editEvent(event) {
    document.getElementById('modalIcon').className = 'fas fa-calendar-edit';
    document.getElementById('modalTitle').textContent = 'Editar Evento';
    document.getElementById('eventId').value = event.id;
    document.getElementById('academic_year_id').value = event.extendedProps.academic_year_id;
    document.getElementById('event_title').value = event.title;
    document.getElementById('event_type').value = event.extendedProps.event_type;
    document.getElementById('start_date').value = event.startStr.split('T')[0];
    document.getElementById('end_date').value = event.end ? event.endStr.split('T')[0] : event.startStr.split('T')[0];
    document.getElementById('start_time').value = event.extendedProps.start_time || '';
    document.getElementById('end_time').value = event.extendedProps.end_time || '';
    document.getElementById('location').value = event.extendedProps.location || '';
    document.getElementById('event_description').value = event.extendedProps.description || '';
    document.getElementById('all_day').checked = event.allDay;
    document.getElementById('color').value = event.backgroundColor || '#3788d8';
    
    document.getElementById('deleteEventBtn').style.display = 'inline-block';
    
    const modal = new bootstrap.Modal(document.getElementById('eventModal'));
    modal.show();
}

function saveEvent() {
    const form = document.getElementById('eventForm');
    const formData = new FormData(form);
    
    fetch('<?= site_url('admin/academic/calendar/save-event') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('eventModal'));
            modal.hide();
            
            const calendarEl = document.getElementById('calendar');
            const calendar = FullCalendar.getCalendar(calendarEl);
            calendar.refetchEvents();
            
            showNotification(data.message, 'success');
        } else {
            if (data.errors) {
                let errorMsg = '';
                for (let field in data.errors) {
                    errorMsg += data.errors[field] + '\n';
                }
                showNotification(errorMsg, 'danger');
            } else {
                showNotification(data.message || 'Erro ao salvar evento', 'danger');
            }
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showNotification('Erro ao salvar evento. Verifique o console.', 'danger');
    });
}

function deleteEvent() {
    const eventId = document.getElementById('eventId').value;
    
    const formData = new FormData();
    formData.append(csrfToken, csrfHash);
    formData.append('_method', 'DELETE');
    
    fetch('<?= site_url('admin/academic/calendar/delete-event') ?>/' + eventId, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('eventModal'));
            modal.hide();
            
            const calendarEl = document.getElementById('calendar');
            const calendar = FullCalendar.getCalendar(calendarEl);
            calendar.refetchEvents();
            
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showNotification('Erro ao eliminar evento.', 'danger');
    });
}

function updateEventDate(event) {
    const formData = new FormData();
    formData.append(csrfToken, csrfHash);
    formData.append('id', event.id);
    formData.append('start_date', event.startStr.split('T')[0]);
    formData.append('end_date', event.end ? event.endStr.split('T')[0] : event.startStr.split('T')[0]);
    formData.append('all_day', event.allDay ? 1 : 0);
    
    fetch('<?= site_url('admin/academic/calendar/update-date') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            showNotification('Erro ao atualizar data', 'danger');
            const calendarEl = document.getElementById('calendar');
            const calendar = FullCalendar.getCalendar(calendarEl);
            calendar.refetchEvents();
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showNotification('Erro ao atualizar data.', 'danger');
    });
}

function showNotification(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.style.maxWidth = '400px';
    alertDiv.style.boxShadow = 'var(--shadow-lg)';
    alertDiv.style.borderLeft = `3px solid ${type === 'success' ? 'var(--success)' : 'var(--danger)'}`;
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center gap-2">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} fs-5"></i>
            <div style="white-space: pre-line;">${message}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}
</script>
<?= $this->endSection() ?>