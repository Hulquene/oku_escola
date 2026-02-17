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
               onclick="return confirm('Tem certeza que deseja limpar todos os logs?')">
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
                <h6 class="card-title">Hoje</h6>
                <h3><?= $totalToday ?></h3>
                <small>Registos de hoje</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Últimos 7 dias</h6>
                <h3><?= $totalWeek ?></h3>
                <small>Registos da semana</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Este mês</h6>
                <h3><?= $totalMonth ?></h3>
                <small>Registos do mês</small>
            </div>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-chart-line"></i> Atividade dos Últimos 7 Dias
    </div>
    <div class="card-body">
        <canvas id="logsChart" style="height: 300px;"></canvas>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <label for="user_id" class="form-label">Usuário</label>
                <select class="form-select" id="user_id" name="user_id">
                    <option value="">Todos</option>
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
                    <option value="">Todas</option>
                    <?php if (!empty($actions)): ?>
                        <?php foreach ($actions as $act): ?>
                            <option value="<?= $act ?>" <?= $selectedAction == $act ? 'selected' : '' ?>>
                                <?= ucfirst($act) ?>
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
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/tools/logs') ?>" class="btn btn-secondary me-2">
                    <i class="fas fa-undo"></i>
                </a>
                <button type="button" class="btn btn-danger" onclick="deleteSelected()">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Logs Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <span><i class="fas fa-history"></i> Registos de Atividades</span>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                    Selecionar Todos
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                    Limpar Seleção
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="logsTable">
                <thead>
                    <tr>
                        <th width="30">
                            <input type="checkbox" id="selectAll" onclick="toggleSelectAll()">
                        </th>
                        <th>Data/Hora</th>
                        <th>Usuário</th>
                        <th>Ação</th>
                        <th>IP Address</th>
                        <th>User Agent</th>
                        <th width="100">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="log-checkbox" value="<?= $log->id ?>">
                                </td>
                                <td><?= date('d/m/Y H:i:s', strtotime($log->created_at)) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if ($log->photo): ?>
                                            <img src="<?= base_url('uploads/users/' . $log->photo) ?>" 
                                                 class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white me-2"
                                                 style="width: 30px; height: 30px;">
                                                <?= strtoupper(substr($log->first_name, 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?= $log->first_name ?> <?= $log->last_name ?></strong>
                                            <br><small><?= $log->username ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= getActionColor($log->action) ?>">
                                        <?= ucfirst($log->action) ?>
                                    </span>
                                </td>
                                <td><?= $log->ip_address ?: '-' ?></td>
                                <td>
                                    <?php 
                                    $ua = $log->user_agent ?: '-';
                                    echo strlen($ua) > 50 ? substr($ua, 0, 50) . '...' : $ua;
                                    ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('admin/tools/logs/view/' . $log->id) ?>" 
                                       class="btn btn-sm btn-info" title="Ver Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Nenhum registo encontrado</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?= $pager->links() ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('functions') ?>
<?php
function getActionColor($action) {
    $colors = [
        'insert' => 'success',
        'update' => 'info',
        'delete' => 'danger',
        'login' => 'primary',
        'logout' => 'secondary',
        'view' => 'light'
    ];
    return $colors[$action] ?? 'secondary';
}
?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
                        label: 'Atividades',
                        data: data.data,
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    });

// Select/Deselect all
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
        alert('Selecione pelo menos um registo');
        return;
    }
    
    if (!confirm('Tem certeza que deseja eliminar os registos selecionados?')) {
        return;
    }
    
    fetch('<?= site_url('admin/tools/logs/delete') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            ids: selected
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao eliminar registos: ' + data.message);
        }
    })
    .catch(error => {
        alert('Erro ao eliminar registos');
    });
}

// Initialize DataTable
$(document).ready(function() {
    $('#logsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'desc']],
        pageLength: 50,
        searching: false,
        columnDefs: [
            { orderable: false, targets: [0, 6] }
        ]
    });
});
</script>
<?= $this->endSection() ?>