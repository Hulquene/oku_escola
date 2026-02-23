<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/tools/logs/export?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-success me-2">
                <i class="fas fa-download"></i> Exportar
            </a>
            <a href="<?= site_url('admin/tools/logs/clear') ?>" class="btn btn-danger" 
               onclick="return confirm('Tem certeza que deseja limpar todos os logs? Esta ação não pode ser desfeita.')">
                <i class="fas fa-trash"></i> Limpar Logs
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/tools') ?>">Ferramentas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Logs do Sistema</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Hoje</h6>
                        <h2 class="mb-0"><?= $totalToday ?></h2>
                    </div>
                    <i class="fas fa-calendar-day fa-3x text-white-50"></i>
                </div>
                <small class="text-white-50">Registos de hoje</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Últimos 7 dias</h6>
                        <h2 class="mb-0"><?= $totalWeek ?></h2>
                    </div>
                    <i class="fas fa-calendar-week fa-3x text-white-50"></i>
                </div>
                <small class="text-white-50">Registos da semana</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Este mês</h6>
                        <h2 class="mb-0"><?= $totalMonth ?></h2>
                    </div>
                    <i class="fas fa-calendar-alt fa-3x text-white-50"></i>
                </div>
                <small class="text-white-50">Registos do mês</small>
            </div>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="fas fa-chart-line me-2 text-primary"></i>
            Atividade dos Últimos 7 Dias
        </h5>
    </div>
    <div class="card-body">
        <canvas id="logsChart" style="height: 300px; width: 100%;"></canvas>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2 text-secondary"></i>
            Filtros
        </h5>
    </div>
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <label for="user_id" class="form-label">Usuário</label>
                <select class="form-select" id="user_id" name="user_id">
                    <option value="">Todos os usuários</option>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user->id ?>" <?= $selectedUser == $user->id ? 'selected' : '' ?>>
                                <?= $user->first_name ?> <?= $user->last_name ?> (<?= $user->username ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="action" class="form-label">Ação</label>
                <select class="form-select" id="action" name="action">
                    <option value="">Todas as ações</option>
                    <?php if (!empty($actions)): ?>
                        <?php foreach ($actions as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $selectedAction == $key ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="start_date" class="form-label">Data Início</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $selectedStartDate ?>">
            </div>
            
            <div class="col-md-2">
                <label for="end_date" class="form-label">Data Fim</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $selectedEndDate ?>">
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/tools/logs') ?>" class="btn btn-secondary me-2" title="Limpar filtros">
                    <i class="fas fa-undo"></i>
                </a>
                <button type="button" class="btn btn-danger" onclick="deleteSelected()" title="Eliminar selecionados">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Logs Table -->
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-history me-2 text-info"></i>
                Registos de Atividades
            </h5>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="selectAll()">
                    <i class="fas fa-check-double me-1"></i> Selecionar Todos
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                    <i class="fas fa-times me-1"></i> Limpar Seleção
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="logsTable">
                <thead class="table-light">
                    <tr>
                        <th width="40">
                            <input type="checkbox" id="selectAll" onclick="toggleSelectAll()" class="form-check-input">
                        </th>
                        <th>Data/Hora</th>
                        <th>Usuário</th>
                        <th>Ação</th>
                        <th>Descrição</th>
                        <th>IP Address</th>
                        <th>Target</th>
                        <th width="80">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="log-checkbox form-check-input" value="<?= $log->id ?>">
                                </td>
                                <td>
                                    <i class="far fa-clock me-1 text-muted"></i>
                                    <?= date('d/m/Y H:i:s', strtotime($log->created_at)) ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($log->photo)): ?>
                                            <img src="<?= base_url('uploads/users/' . $log->photo) ?>" 
                                                 class="rounded-circle me-2" style="width: 35px; height: 35px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white me-2"
                                                 style="width: 35px; height: 35px; font-size: 14px;">
                                                <?= strtoupper(substr($log->first_name ?? 'S', 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?= $log->first_name ?? 'Sistema' ?> <?= $log->last_name ?? '' ?></strong>
                                            <br><small class="text-muted"><?= $log->username ?? 'sistema' ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?= get_action_badge($log->action) ?>
                                </td>
                                <td>
                                    <?php 
                                    $description = $log->description ?? '-';
                                    echo strlen($description) > 60 ? substr($description, 0, 60) . '...' : $description;
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= $log->ip_address ?: '-' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($log->target_type): ?>
                                        <span class="badge bg-secondary">
                                            <?= $log->target_type ?><?= $log->target_id ? '#' . $log->target_id : '' ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('admin/tools/logs/view/' . $log->id) ?>" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Ver Detalhes"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum registo encontrado</h5>
                                <p class="text-muted mb-0">Tente ajustar os filtros ou limpar a busca.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($logs) && isset($pager)): ?>
            <div class="mt-4">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- DataTables (opcional, pode remover se preferir usar a paginação normal) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
// Load chart data
fetch('<?= site_url('admin/tools/logs/chart-data') ?>')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const ctx = document.getElementById('logsChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Número de Atividades',
                        data: data.data,
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        borderWidth: 2,
                        pointBackgroundColor: '#4e73df',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#4e73df',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Atividades: ${context.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
                                    return value;
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    })
    .catch(error => {
        console.error('Erro ao carregar gráfico:', error);
    });

// Select/Deselect all functions
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    document.querySelectorAll('.log-checkbox').forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function selectAll() {
    document.querySelectorAll('.log-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    document.getElementById('selectAll').checked = true;
}

function deselectAll() {
    document.querySelectorAll('.log-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAll').checked = false;
}

// Delete selected logs
function deleteSelected() {
    const selected = [];
    document.querySelectorAll('.log-checkbox:checked').forEach(checkbox => {
        selected.push(checkbox.value);
    });
    
    if (selected.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Atenção',
            text: 'Selecione pelo menos um registo para eliminar.',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
    
    Swal.fire({
        title: 'Confirmar eliminação',
        html: `Tem certeza que deseja eliminar <strong>${selected.length}</strong> registo(s)?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= site_url('admin/tools/logs/delete') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                    ids: selected
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: data.message || 'Registos eliminados com sucesso.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: data.message || 'Erro ao eliminar registos.',
                        confirmButtonColor: '#3085d6'
                    });
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao comunicar com o servidor.',
                    confirmButtonColor: '#3085d6'
                });
            });
        }
    });
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Inicializar DataTable (opcional - pode comentar se não quiser usar)
$(document).ready(function() {
    if ($('#logsTable tbody tr').length > 1) { // Só inicializa se houver mais de uma linha
        $('#logsTable').DataTable({
            language: {
                "sEmptyTable": "Nenhum dado disponível na tabela",
                "sInfo": "Mostrando _START_ até _END_ de _TOTAL_ registos",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registos",
                "sInfoFiltered": "(filtrado de _MAX_ registos no total)",
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": "Mostrar _MENU_ registos por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sSearch": "Pesquisar:",
                "sZeroRecords": "Nenhum registo encontrado",
                "oPaginate": {
                    "sFirst": "Primeiro",
                    "sLast": "Último",
                    "sNext": "Seguinte",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": ativar para ordenar a coluna de forma ascendente",
                    "sSortDescending": ": ativar para ordenar a coluna de forma descendente"
                }
            },
            order: [[1, 'desc']],
            pageLength: 25,
            searching: true,
            ordering: true,
            columnDefs: [
                { orderable: false, targets: [0, 7] } // Colunas do checkbox e ações não ordenáveis
            ],
            initComplete: function() {
                // Desabilitar a pesquisa se não quiser
                // $('.dataTables_filter').hide();
            }
        });
    }
});
</script>

<style>
/* Estilos adicionais */
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}

.card {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Estilo para o scroll horizontal da tabela */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Ajuste para telas pequenas */
@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    
    .col-md-3.d-flex {
        flex-direction: column;
        align-items: stretch !important;
    }
    
    .col-md-3.d-flex .btn {
        margin: 2px 0 !important;
        width: 100%;
    }
}
</style>
<?= $this->endSection() ?>