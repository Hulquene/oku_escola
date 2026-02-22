<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('teachers/exams/schedule') ?>" class="btn btn-info me-2">
                <i class="fas fa-calendar-alt"></i> Calendário
            </a>
            <a href="<?= site_url('teachers/exams/form-add') ?>" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Agendar Exame
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Meus Exames</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros Avançados -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <i class="fas fa-filter me-2"></i>Filtros Avançados
        <button class="btn btn-sm btn-link float-end" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="filterCollapse">
        <div class="card-body">
            <form method="get" id="filterForm">
                <div class="row g-3">
                    <!-- Linha 1: Filtros principais -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Turma</label>
                        <select class="form-select" name="class" id="classFilter">
                            <option value="">Todas as turmas</option>
                            <?php if (!empty($classes)): ?>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class->id ?>" <?= ($filters['class'] ?? '') == $class->id ? 'selected' : '' ?>>
                                        <?= $class->class_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Disciplina</label>
                        <select class="form-select" name="discipline" id="disciplineFilter">
                            <option value="">Todas as disciplinas</option>
                            <!-- Será preenchido via AJAX baseado na turma -->
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Tipo de Exame</label>
                        <select class="form-select" name="board">
                            <option value="">Todos</option>
                            <?php if (!empty($boards)): ?>
                                <?php foreach ($boards as $board): ?>
                                    <option value="<?= $board->id ?>" <?= ($filters['board'] ?? '') == $board->id ? 'selected' : '' ?>>
                                        <?= $board->board_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="pending" <?= ($filters['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Pendentes</option>
                            <option value="completed" <?= ($filters['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Realizados</option>
                            <option value="today" <?= ($filters['status'] ?? '') == 'today' ? 'selected' : '' ?>>Hoje</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Período</label>
                        <select class="form-select" name="period">
                            <option value="">Todos</option>
                            <?php if (!empty($periods)): ?>
                                <?php foreach ($periods as $period): ?>
                                    <option value="<?= $period->id ?>" <?= ($filters['period'] ?? '') == $period->id ? 'selected' : '' ?>>
                                        <?= $period->period_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <div class="row g-3 mt-2">
                    <!-- Linha 2: Filtros de data -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Data Início</label>
                        <input type="date" class="form-control" name="start_date" 
                               value="<?= $filters['start_date'] ?? '' ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Data Fim</label>
                        <input type="date" class="form-control" name="end_date" 
                               value="<?= $filters['end_date'] ?? '' ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Com resultados</label>
                        <select class="form-select" name="has_results">
                            <option value="">Todos</option>
                            <option value="yes" <?= ($filters['has_results'] ?? '') == 'yes' ? 'selected' : '' ?>>Com notas lançadas</option>
                            <option value="no" <?= ($filters['has_results'] ?? '') == 'no' ? 'selected' : '' ?>>Sem notas</option>
                        </select>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-filter me-2"></i>Aplicar Filtros
                        </button>
                        <a href="<?= site_url('teachers/exams') ?>" class="btn btn-secondary">
                            <i class="fas fa-undo me-2"></i>Limpar Filtros
                        </a>
                        
                        <!-- Badges com resumo dos filtros ativos -->
                        <div class="d-inline-flex gap-2 ms-3">
                            <?php if (!empty($filters['class'])): ?>
                                <span class="badge bg-primary p-2">Turma selecionada</span>
                            <?php endif; ?>
                            <?php if (!empty($filters['status'])): ?>
                                <span class="badge bg-info p-2">Status: <?= $filters['status'] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cards de Estatísticas Rápidas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Total Exames</h6>
                        <h2 class="mb-0"><?= $totalExams ?? count($exams) ?></h2>
                    </div>
                    <i class="fas fa-pencil-alt fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Pendentes</h6>
                        <h2 class="mb-0"><?= $pendingCount ?? 0 ?></h2>
                    </div>
                    <i class="fas fa-clock fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Hoje</h6>
                        <h2 class="mb-0"><?= $todayCount ?? 0 ?></h2>
                    </div>
                    <i class="fas fa-calendar-day fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Com Notas</h6>
                        <h2 class="mb-0"><?= $withResultsCount ?? 0 ?></h2>
                    </div>
                    <i class="fas fa-check-circle fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Exams Table -->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-pencil-alt"></i> Lista de Exames
            <span class="badge bg-secondary ms-2"><?= count($exams) ?> registros</span>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-primary" onclick="exportTableToExcel()">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($exams)): ?>
            <div class="table-responsive">
                <table id="examsTable" class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Exame</th>
                            <th>Tipo</th>
                            <th>Turma</th>
                            <th>Disciplina</th>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Sala</th>
                            <th>Período</th>
                            <th>Notas</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exams as $exam): ?>
                            <?php 
                            $hasResults = ($exam->results_count ?? 0) > 0;
                            
                            // CORREÇÃO 1: Usar o status real da tabela
                            switch($exam->status) {
                                case 'Realizado':
                                    $statusClass = 'secondary';
                                    $statusText = 'Realizado';
                                    break;
                                case 'Cancelado':
                                    $statusClass = 'danger';
                                    $statusText = 'Cancelado';
                                    break;
                                case 'Adiado':
                                    $statusClass = 'warning';
                                    $statusText = 'Adiado';
                                    break;
                                default: // Agendado
                                    $statusClass = 'primary';
                                    $statusText = 'Agendado';
                            }
                            
                            // Se for hoje, destacar (apenas se ainda estiver agendado)
                            if ($exam->exam_date == date('Y-m-d') && $exam->status == 'Agendado') {
                                $statusClass = 'success';
                                $statusText = 'Hoje';
                            }
                            
                            // CORREÇÃO 2: Mapear board_type para classe do badge
                            $boardTypeClass = match($exam->board_type) {
                                'Avaliação Contínua' => 'info',
                                'Prova Professor' => 'primary',
                                'Prova Trimestral' => 'warning',
                                'Exame Final' => 'danger',
                                default => 'secondary'
                            };
                            ?>
                            <tr>
                                <td>
                                    <strong><?= $exam->exam_name ?? $exam->board_name ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $boardTypeClass ?>">
                                        <?= $exam->board_type ?? 'Normal' ?>
                                    </span>
                                </td>
                                <td><?= $exam->class_name ?></td>
                                <td><?= $exam->discipline_name ?></td>
                                <td><?= date('d/m/Y', strtotime($exam->exam_date)) ?></td>
                                <td><?= $exam->exam_time ? date('H:i', strtotime($exam->exam_time)) : '-' ?></td>
                                <td><?= $exam->exam_room ?: '-' ?></td>
                                <td>
                                    <span class="badge bg-secondary" title="<?= $exam->period_name ?? '' ?>">
                                        <?= $exam->period_type ?? 'Normal' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($hasResults): ?>
                                        <span class="badge bg-success" title="Notas lançadas">
                                            <i class="fas fa-check-circle"></i> <?= $exam->results_count ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Pendente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $statusClass ?> p-2">
                                        <i class="fas fa-<?= $statusClass == 'success' ? 'check-circle' : 
                                            ($statusClass == 'primary' ? 'calendar' : 
                                            ($statusClass == 'danger' ? 'times-circle' : 'history')) ?> me-1"></i>
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('teachers/exams/grade/' . $exam->id) ?>" 
                                           class="btn btn-sm btn-success" title="Lançar Notas">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <?php if ($exam->exam_date >= date('Y-m-d') || !$hasResults): ?>
                                            <a href="<?= site_url('teachers/exams/form-edit/' . $exam->id) ?>" 
                                               class="btn btn-sm btn-info" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!$hasResults && $exam->exam_date >= date('Y-m-d')): ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete(<?= $exam->id ?>)"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                        <a href="<?= site_url('teachers/exams/results/' . $exam->id) ?>" 
                                           class="btn btn-sm btn-primary" title="Ver Resultados">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                        <a href="<?= site_url('teachers/exams/attendance/' . $exam->id) ?>" 
                                           class="btn btn-sm btn-warning" title="Registrar Presenças">
                                            <i class="fas fa-user-check"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-pencil-alt fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhum exame encontrado.</p>
                <a href="<?= site_url('teachers/exams/form-add') ?>" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Criar Primeiro Exame
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Confirmação de Eliminação -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar este exame?</p>
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
// Configuração CSRF para AJAX
const csrfToken = '<?= csrf_token() ?>';
const csrfHash = '<?= csrf_hash() ?>';

$(document).ready(function() {
    // Inicializar DataTable
    $('#examsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[4, 'desc']], // Ordenar por data
        pageLength: 25,
        searching: true,
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [10] } // Desabilitar ordenação para ações
        ]
    });
    
    // Carregar disciplinas quando turma mudar
    $('#classFilter').change(function() {
        const classId = $(this).val();
        const disciplineSelect = $('#disciplineFilter');
        
        if (classId) {
            disciplineSelect.html('<option value="">Carregando...</option>');
            
            $.ajax({
                url: '<?= site_url('teachers/exams/get-disciplines/') ?>' + classId,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfHash
                },
                success: function(data) {
                    disciplineSelect.html('<option value="">Todas as disciplinas</option>');
                    data.forEach(function(discipline) {
                        disciplineSelect.append(`<option value="${discipline.id}">${discipline.discipline_name}</option>`);
                    });
                },
                error: function() {
                    disciplineSelect.html('<option value="">Erro ao carregar disciplinas</option>');
                }
            });
        } else {
            disciplineSelect.html('<option value="">Todas as disciplinas</option>');
        }
    });
    
    // Auto-submit quando filtros mudarem
    let filterTimeout;
    $('#filterForm select, #filterForm input').change(function() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => $('#filterForm').submit(), 500);
    });
});

// Confirmação de eliminação
function confirmDelete(id) {
    $('#confirmDeleteBtn').attr('href', '<?= site_url('teachers/exams/delete/') ?>' + id);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Exportar para Excel
function exportTableToExcel() {
    const table = document.getElementById('examsTable');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    let csv = [];
    
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        // Remover coluna de ações (última)
        const filteredCells = cells.filter((_, index) => index !== cells.length - 1);
        const rowData = filteredCells.map(cell => cell.innerText.trim().replace(/,/g, ';'));
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'exames_' + new Date().toISOString().split('T')[0] + '.csv');
    link.click();
}
</script>
<?= $this->endSection() ?>