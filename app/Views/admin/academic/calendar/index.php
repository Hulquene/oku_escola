<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eventModal">
            <i class="fas fa-plus-circle"></i> Novo Evento
        </button>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/academic/years') ?>">Académico</a></li>
            <li class="breadcrumb-item active" aria-current="page">Calendário</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Calendar -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-calendar-alt"></i> Calendário Escolar
            </div>
            <div>
                <select class="form-select form-select-sm" id="academicYearFilter" style="width: auto;">
                    <option value="">Todos os Anos</option>
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= $year->id ?>" <?= $selectedYear == $year->id ? 'selected' : '' ?>>
                                <?= $year->year_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>

<!-- Event Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="eventForm" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="eventId">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Novo Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="academic_year_id" class="form-label">Ano Letivo</label>
                        <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($academicYears)): ?>
                                <?php foreach ($academicYears as $year): ?>
                                    <option value="<?= $year->id ?>" <?= $selectedYear == $year->id ? 'selected' : '' ?>>
                                        <?= $year->year_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="event_title" class="form-label">Título</label>
                        <input type="text" class="form-control" id="event_title" name="event_title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="event_type" class="form-label">Tipo de Evento</label>
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
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Data Início</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Data Fim</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                                <small class="text-muted">Deixe em branco para evento de um dia</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Hora Início</label>
                                <input type="time" class="form-control" id="start_time" name="start_time">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">Hora Fim</label>
                                <input type="time" class="form-control" id="end_time" name="end_time">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Local</label>
                        <input type="text" class="form-control" id="location" name="location">
                    </div>
                    
                    <div class="mb-3">
                        <label for="event_description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="event_description" name="event_description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="all_day" name="all_day" value="1">
                            <label class="form-check-label" for="all_day">Dia inteiro</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
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
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="deleteEventBtn" style="display: none;">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
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
// Configuração CSRF - APENAS PARA REFERÊNCIA, NÃO USAR PARA ADICIONAR MANUALMENTE
const csrfToken = '<?= csrf_token() ?>';
const csrfHash = '<?= csrf_hash() ?>';

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const academicYearFilter = document.getElementById('academicYearFilter');
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'pt',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Hoje',
            month: 'Mês',
            week: 'Semana',
            day: 'Dia'
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
            .then(data => successCallback(data))
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
        }
    });
    
    calendar.render();
    
    // Filter by academic year
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
        if (confirm('Tem certeza que deseja eliminar este evento?')) {
            deleteEvent();
        }
    });
});

function createEvent(date) {
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
            
            // Refresh calendar
            const calendarEl = document.getElementById('calendar');
            const calendar = FullCalendar.getCalendar(calendarEl);
            calendar.refetchEvents();
            
            showNotification(data.message, 'success');
        } else {
            if (data.errors) {
                let errorMsg = 'Erro ao salvar evento:\n';
                for (let field in data.errors) {
                    errorMsg += `- ${data.errors[field]}\n`;
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
    // ⚠️ Para DELETE, precisamos do CSRF porque não há formulário com campo hidden
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
    // ⚠️ Para updateDate, precisamos do CSRF porque não há formulário
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
    alertDiv.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
        ${message}
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