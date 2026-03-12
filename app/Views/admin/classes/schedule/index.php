<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-calendar-alt me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Horários</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Ano Letivo</label>
                <select class="form-select" id="academic_year">
                    <option value="">Todos</option>
                    <?php foreach ($academicYears as $year): ?>
                        <option value="<?= $year['id'] ?>" <?= current_academic_year() == $year['id'] ? 'selected' : '' ?>>
                            <?= $year['year_name'] ?> <?= current_academic_year() == $year['id'] ? '(Atual)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" id="btnFilter">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- DataTable Card -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Lista de Horários</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="schedulesTable" width="100%">
                <thead>
                    <tr>
                        <th>Turma</th>
                        <th>Código</th>
                        <th>Nível</th>
                        <th>Ano Letivo</th>
                        <th>Turno</th>
                        <th>Itens</th>
                        <th>Carga Hor.</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables preenche via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Visualização do Horário -->
<div class="modal fade" id="viewScheduleModal" tabindex="-1" aria-labelledby="viewScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewScheduleModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Horário da Turma: <span id="modalClassName"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Loading -->
                <div id="scheduleLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-3">Carregando horário...</p>
                </div>
                
                <!-- Conteúdo do Horário -->
                <div id="scheduleContent" style="display: none;">
                    <!-- Info Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light border-0 shadow-sm">
                                <div class="card-body">
                                    <small class="text-muted text-uppercase fw-bold">Total de Itens</small>
                                    <h3 class="mb-0 mt-2" id="totalItems">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-0 shadow-sm">
                                <div class="card-body">
                                    <small class="text-muted text-uppercase fw-bold">Carga Horária</small>
                                    <h3 class="mb-0 mt-2" id="totalHours">0h</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-0 shadow-sm">
                                <div class="card-body">
                                    <small class="text-muted text-uppercase fw-bold">Turno</small>
                                    <h3 class="mb-0 mt-2" id="classShift">-</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-0 shadow-sm">
                                <div class="card-body">
                                    <small class="text-muted text-uppercase fw-bold">Ano Letivo</small>
                                    <h3 class="mb-0 mt-2" id="academicYear">-</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabela de Horário -->
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="scheduleTable">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th style="width: 140px;">Período</th>
                                    <th>Segunda</th>
                                    <th>Terça</th>
                                    <th>Quarta</th>
                                    <th>Quinta</th>
                                    <th>Sexta</th>
                                    <th>Sábado</th>
                                </tr>
                            </thead>
                            <tbody id="scheduleTableBody">
                                <!-- Preenchido via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Legenda -->
                    <div class="mt-4 d-flex gap-3 justify-content-end">
                        <span class="badge bg-success px-3 py-2">Com professor</span>
                        <span class="badge bg-warning px-3 py-2">Sem professor</span>
                        <span class="badge bg-info px-3 py-2">Com sala</span>
                    </div>
                </div>
                
                <!-- Erro -->
                <div id="scheduleError" class="alert alert-danger text-center py-4" style="display: none;">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h5>Erro ao carregar horário</h5>
                    <p class="mb-0" id="errorMessage"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Fechar
                </button>
                <a href="#" id="editScheduleBtn" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>Editar Horário
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<style>
/* Estilos personalizados */
:root {
    --primary-color: #3B7FE8;
    --success-color: #16A87D;
    --warning-color: #E8A020;
    --danger-color: #E84646;
}

/* Estilo para a tabela de horário no modal */
#scheduleTable {
    font-size: 0.85rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

#scheduleTable th {
    background-color: #f8f9fa;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    padding: 0.75rem !important;
}

#scheduleTable td {
    padding: 0.75rem !important;
    vertical-align: top;
    min-width: 150px;
    background-color: #fff;
}

/* Estilo dos itens de horário */
.schedule-item {
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    border-left: 4px solid var(--primary-color);
    border-radius: 6px;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: transform 0.2s;
}

.schedule-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.schedule-item .discipline-name {
    font-weight: 600;
    color: #1B2B4B;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    display: block;
}

.schedule-item .badge {
    font-size: 0.7rem;
    padding: 0.35rem 0.5rem;
    margin-right: 0.25rem;
    border-radius: 4px;
}

.schedule-item .badge i {
    font-size: 0.65rem;
    margin-right: 0.2rem;
}

/* Estilo para células vazias */
.empty-schedule {
    color: #adb5bd;
    font-style: italic;
    text-align: center;
    padding: 1rem;
    background-color: #fafafa;
    border-radius: 4px;
    font-size: 0.8rem;
}

/* Cards de informação */
.card.bg-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%) !important;
    border-radius: 8px;
    transition: transform 0.2s;
}

.card.bg-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.card.bg-light .card-body {
    padding: 1rem;
}

.card.bg-light small {
    font-size: 0.7rem;
    letter-spacing: 0.5px;
}

.card.bg-light h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1B2B4B;
}

/* Modal customizado */
.modal-xl {
    max-width: 95%;
}

@media (min-width: 1400px) {
    .modal-xl {
        max-width: 1320px;
    }
}

.modal-content {
    border: none;
    border-radius: 12px;
    overflow: hidden;
}

.modal-header {
    border-bottom: none;
    padding: 1rem 1.5rem;
}

.modal-header .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
}

.modal-header .btn-close:hover {
    opacity: 1;
}

.modal-body {
    background-color: #f5f5f5;
    max-height: 70vh;
    overflow-y: auto;
}

.modal-footer {
    border-top: 1px solid #e9ecef;
    padding: 1rem 1.5rem;
}

/* Loading spinner */
.spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 0.25rem;
}

/* Botões */
.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background-color: #2c6fd4;
    border-color: #2c6fd4;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(59,127,232,0.3);
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #5a6268;
    transform: translateY(-1px);
}
</style>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#schedulesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= site_url('admin/classes/schedule/get-data') ?>',
            type: 'POST',
            data: function(d) {
                d.academic_year = $('#academic_year').val();
                d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
            }
        },
        columns: [
            { data: 'class_name' },
            { data: 'class_code' },
            { data: 'level_name' },
            { 
                data: 'year_name',
                render: function(data, type, row) {
                    return data + (row.is_current ? ' <span class="badge bg-info">Atual</span>' : '');
                }
            },
            { data: 'shift' },
            { data: 'total_items' },
            { data: 'hours' },
            { data: 'status' },
            { 
                data: 'actions',
                render: function(data, type, row) {
                    return '<div class="btn-group" role="group">' +
                           '<button class="btn btn-sm btn-outline-primary view-schedule" data-class-id="' + row.class_id + '" data-class-name="' + row.class_name + '" title="Ver horário"><i class="fas fa-eye"></i></button>' +
                           '<a href="<?= site_url('admin/classes/schedule/export-pdf/') ?>' + row.class_id + '" class="btn btn-sm btn-outline-secondary" title="Exportar PDF"><i class="fas fa-file-pdf"></i></a>' +
                           '</div>';
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[0, 'asc']],
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        drawCallback: function() {
            // Re-inicializar tooltips se necessário
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
    
    // Filtrar ao clicar no botão
    $('#btnFilter').on('click', function() {
        table.ajax.reload();
    });
    
    // Filtrar ao mudar o select
    $('#academic_year').on('change', function() {
        table.ajax.reload();
    });
    
    // Ver horário (modal)
    $(document).on('click', '.view-schedule', function(e) {
        e.preventDefault();
        
        var classId = $(this).data('class-id');
        var className = $(this).data('class-name');
        
        console.log('Abrindo modal para turma:', className, 'ID:', classId);
        
        // Resetar modal
        $('#modalClassName').text(className);
        $('#scheduleLoading').show();
        $('#scheduleContent').hide();
        $('#scheduleError').hide();
        $('#scheduleTableBody').empty();
        $('#editScheduleBtn').attr('href', '#');
        
        // Abrir modal
        $('#viewScheduleModal').modal('show');
        
        // Buscar dados do horário via AJAX
        $.ajax({
            url: '<?= site_url('admin/classes/schedule/get-schedule-data') ?>',
            method: 'POST',
            data: {
                class_id: classId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                console.log('Resposta recebida:', response);
                $('#scheduleLoading').hide();
                
                if (response.success) {
                    // Preencher informações
                    $('#totalItems').text(response.data.total_items || 0);
                    $('#totalHours').text(parseFloat(response.data.total_hours || 0).toFixed(1) + 'h');
                    $('#classShift').text(response.data.class_shift || '-');
                    $('#academicYear').text(response.data.year_name || '-');
                    
                    // Link de edição
                    $('#editScheduleBtn').attr('href', '<?= site_url('admin/classes/schedule/view/') ?>' + classId);
                    
                    // Preencher tabela de horário
                    if (renderScheduleTable(response.data.schedule)) {
                        $('#scheduleContent').show();
                        
                        // Forçar atualização do modal
                        setTimeout(function() {
                            $('#viewScheduleModal').modal('handleUpdate');
                            $(window).trigger('resize');
                        }, 100);
                    } else {
                        $('#errorMessage').text('Erro ao processar dados do horário');
                        $('#scheduleError').show();
                    }
                } else {
                    $('#errorMessage').text(response.message || 'Erro ao carregar horário');
                    $('#scheduleError').show();
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro AJAX:', error);
                console.error('Resposta:', xhr.responseText);
                $('#scheduleLoading').hide();
                $('#errorMessage').text('Erro ao comunicar com o servidor');
                $('#scheduleError').show();
            }
        });
    });
    
    // Função para renderizar a tabela de horário
    function renderScheduleTable(scheduleData) {
        console.log('Renderizando tabela com dados:', scheduleData);
        
        try {
            var tbody = $('#scheduleTableBody');
            tbody.empty();
            
            // Períodos fixos
            var periods = [
                { period: '1', time: '07:30 - 09:00', name: '1º Período' },
                { period: '2', time: '09:15 - 10:45', name: '2º Período' },
                { period: '3', time: '11:00 - 12:30', name: '3º Período' },
                { period: '4', time: '13:30 - 15:00', name: '4º Período' },
                { period: '5', time: '15:15 - 16:45', name: '5º Período' },
                { period: '6', time: '17:00 - 18:30', name: '6º Período' }
            ];
            
            // Dias da semana
            var days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            var dayNames = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
            
            // Garantir que scheduleData seja um objeto
            if (!scheduleData || typeof scheduleData !== 'object') {
                scheduleData = {};
            }
            
            // Para cada período
            periods.forEach(function(periodInfo) {
                var row = $('<tr>');
                
                // Coluna do período
                var periodCell = $('<td>').addClass('fw-bold bg-light text-center').html(
                    '<div>' + periodInfo.time + '</div>' +
                    '<small class="text-muted">' + periodInfo.name + '</small>'
                );
                row.append(periodCell);
                
                // Para cada dia
                days.forEach(function(day) {
                    var cell = $('<td>');
                    
                    // Verificar se existem itens para este dia/período
                    if (scheduleData[day] && 
                        scheduleData[day][periodInfo.period] && 
                        Array.isArray(scheduleData[day][periodInfo.period]) &&
                        scheduleData[day][periodInfo.period].length > 0) {
                        
                        // Iterar sobre os itens
                        scheduleData[day][periodInfo.period].forEach(function(item) {
                            var itemDiv = $('<div>').addClass('schedule-item');
                            
                            // Nome da disciplina
                            itemDiv.append($('<div>').addClass('discipline-name').text(item.discipline_name || 'N/A'));
                            
                            // Professor
                            var teacherClass = item.teacher_id ? 'bg-success' : 'bg-warning';
                            var teacherText = item.teacher_name || 'Não atribuído';
                            itemDiv.append($('<span>').addClass('badge ' + teacherClass).html(
                                '<i class="fas fa-user me-1"></i>' + teacherText
                            ));
                            
                            // Sala
                            if (item.room) {
                                itemDiv.append($('<span>').addClass('badge bg-info ms-1').html(
                                    '<i class="fas fa-door-open me-1"></i>' + item.room
                                ));
                            }
                            
                            cell.append(itemDiv);
                        });
                    } else {
                        cell.append($('<div>').addClass('empty-schedule').html('—'));
                    }
                    
                    row.append(cell);
                });
                
                tbody.append(row);
            });
            
            console.log('Tabela renderizada com sucesso. Linhas:', periods.length);
            return true;
            
        } catch (error) {
            console.error('Erro ao renderizar tabela:', error);
            return false;
        }
    }
    
    // Limpar modal quando fechado
    $('#viewScheduleModal').on('hidden.bs.modal', function() {
        $('#scheduleTableBody').empty();
        $('#scheduleLoading').show();
        $('#scheduleContent').hide();
        $('#scheduleError').hide();
    });
});
</script>
<?= $this->endSection() ?>