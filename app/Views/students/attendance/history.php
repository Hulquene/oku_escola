<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1 class="display-5 fw-bold mb-0">
            <i class="fas fa-history me-3 text-primary"></i><?= $title ?? 'Histórico de Presenças' ?>
        </h1>
        <div class="mt-2 mt-md-0">
            <a href="<?= site_url('students/attendance') ?>" class="btn btn-outline-primary">
                <i class="fas fa-calendar-alt"></i> Presenças Mensais
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('students/attendance') ?>">Presenças</a></li>
            <li class="breadcrumb-item active" aria-current="page">Histórico</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros Avançados -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-0 pt-4">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2 text-primary"></i>
            Filtros do Histórico
        </h5>
    </div>
    <div class="card-body">
        <form method="get" id="filterForm" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold text-muted small">SEMESTRE</label>
                <select class="form-select" name="semester">
                    <option value="">Todos os semestres</option>
                    <?php if (!empty($semesters)): ?>
                        <?php foreach ($semesters as $sem): ?>
                            <option value="<?= $sem->id ?>" <?= $selectedSemester == $sem->id ? 'selected' : '' ?>>
                                <?= $sem->semester_name ?> (<?= date('Y', strtotime($sem->start_date)) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-semibold text-muted small">DISCIPLINA</label>
                <select class="form-select" name="discipline">
                    <option value="">Todas as disciplinas</option>
                    <?php 
                    $uniqueDisciplines = [];
                    foreach ($attendances ?? [] as $att) {
                        if (!empty($att->discipline_name) && !in_array($att->discipline_name, $uniqueDisciplines)) {
                            $uniqueDisciplines[] = $att->discipline_name;
                        }
                    }
                    sort($uniqueDisciplines);
                    ?>
                    <?php foreach ($uniqueDisciplines as $disciplineName): ?>
                        <option value="<?= $disciplineName ?>" <?= $selectedDiscipline == $disciplineName ? 'selected' : '' ?>>
                            <?= $disciplineName ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-semibold text-muted small">STATUS</label>
                <select class="form-select" name="status">
                    <option value="">Todos</option>
                    <option value="Presente" <?= $selectedStatus == 'Presente' ? 'selected' : '' ?>>Presente</option>
                    <option value="Atrasado" <?= $selectedStatus == 'Atrasado' ? 'selected' : '' ?>>Atrasado</option>
                    <option value="Falta Justificada" <?= $selectedStatus == 'Falta Justificada' ? 'selected' : '' ?>>Justificado</option>
                    <option value="Ausente" <?= $selectedStatus == 'Ausente' ? 'selected' : '' ?>>Ausente</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-semibold text-muted small">ANO</label>
                <select class="form-select" name="year">
                    <option value="">Todos</option>
                    <?php 
                    $currentYear = date('Y');
                    for ($y = $currentYear; $y >= $currentYear - 3; $y--): 
                    ?>
                        <option value="<?= $y ?>" <?= $selectedYear == $y ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tabela de Presenças -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0 pt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-table me-2 text-primary"></i>
                Registros de Presença
                <span class="badge bg-secondary ms-2"><?= count($attendances) ?> registros</span>
            </h5>
            <button class="btn btn-sm btn-outline-primary" onclick="exportToExcel()">
                <i class="fas fa-file-excel me-1"></i>Exportar
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($attendances)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="attendanceTable">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Dia</th>
                            <th>Disciplina</th>
                            <th>Status</th>
                            <th>Justificação</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendances as $att): 
                            $statusClass = [
                                'Presente' => 'success',
                                'Atrasado' => 'warning',
                                'Falta Justificada' => 'info',
                                'Ausente' => 'danger'
                            ][$att->status] ?? 'secondary';
                            
                            $statusIcon = [
                                'Presente' => 'check-circle',
                                'Atrasado' => 'clock',
                                'Falta Justificada' => 'file-alt',
                                'Ausente' => 'times-circle'
                            ][$att->status] ?? 'circle';
                            
                            $dayOfWeek = date('l', strtotime($att->attendance_date));
                            $days = [
                                'Sunday' => 'Domingo',
                                'Monday' => 'Segunda',
                                'Tuesday' => 'Terça',
                                'Wednesday' => 'Quarta',
                                'Thursday' => 'Quinta',
                                'Friday' => 'Sexta',
                                'Saturday' => 'Sábado'
                            ];
                            $dayName = $days[$dayOfWeek] ?? $dayOfWeek;
                        ?>
                            <tr>
                                <td>
                                    <span class="fw-bold"><?= date('d/m/Y', strtotime($att->attendance_date)) ?></span>
                                </td>
                                <td><?= $dayName ?></td>
                                <td>
                                    <?php if ($att->discipline_name): ?>
                                        <span class="badge bg-info"><?= $att->discipline_name ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Geral</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $statusClass ?> p-2">
                                        <i class="fas fa-<?= $statusIcon ?> me-1"></i>
                                        <?= $att->status ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($att->justification): ?>
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;">
                                            <?= $att->justification ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-info rounded-circle"
                                            onclick='showAttendanceDetails(<?= json_encode($att) ?>)'
                                            data-bs-toggle="modal" 
                                            data-bs-target="#attendanceModal">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                    <i class="fas fa-calendar-times fa-3x text-muted"></i>
                </div>
                <h5 class="text-muted">Nenhum registro encontrado</h5>
                <p class="text-muted">Tente ajustar os filtros para ver mais resultados.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Único de Detalhes (reutilizado) -->
<div class="modal fade" id="attendanceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-check me-2"></i>
                    Detalhes da Presença
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="attendanceModalBody">
                <!-- Conteúdo preenchido via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
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
$(document).ready(function() {
    $('#attendanceTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true
    });
});

// Função para mostrar detalhes no modal (mesma da view index)
function showAttendanceDetails(attendance) {
    const statusColors = {
        'Presente': 'success',
        'Atrasado': 'warning',
        'Falta Justificada': 'info',
        'Ausente': 'danger'
    };
    
    const statusIcons = {
        'Presente': 'check-circle',
        'Atrasado': 'clock',
        'Falta Justificada': 'file-alt',
        'Ausente': 'times-circle'
    };
    
    const html = `
        <div class="text-center mb-4">
            <div class="bg-${statusColors[attendance.status]} bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                <i class="fas fa-${statusIcons[attendance.status]} fa-2x text-${statusColors[attendance.status]}"></i>
            </div>
            <h4>${attendance.formatted_date || new Date(attendance.attendance_date).toLocaleDateString('pt-PT')}</h4>
        </div>
        
        <div class="row g-3">
            <div class="col-6">
                <div class="bg-light rounded-3 p-3 text-center">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge bg-${statusColors[attendance.status]} p-2">
                        <i class="fas fa-${statusIcons[attendance.status]} me-1"></i>
                        ${attendance.status}
                    </span>
                </div>
            </div>
            
            <div class="col-6">
                <div class="bg-light rounded-3 p-3 text-center">
                    <small class="text-muted d-block">Disciplina</small>
                    <strong>${attendance.discipline_name || 'Geral'}</strong>
                </div>
            </div>
            
            ${attendance.justification ? `
            <div class="col-12">
                <div class="bg-light rounded-3 p-3">
                    <small class="text-muted d-block">Justificação</small>
                    <p class="mb-0">${attendance.justification}</p>
                </div>
            </div>
            ` : ''}
            
            <div class="col-12">
                <div class="bg-light rounded-3 p-3">
                    <small class="text-muted d-block">Informações Adicionais</small>
                    <p class="mb-1 small">
                        <i class="fas fa-clock text-muted me-2"></i>
                        Registado em: ${new Date(attendance.created_at).toLocaleString('pt-PT')}
                    </p>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('attendanceModalBody').innerHTML = html;
}

// Exportar para Excel
function exportToExcel() {
    const table = document.getElementById('attendanceTable');
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
    link.setAttribute('download', 'historico_presencas_' + new Date().toISOString().split('T')[0] + '.csv');
    link.click();
}
</script>

<style>
/* Reutilizar estilos da view index */
.stat-card {
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

/* Estilo para impressão */
@media print {
    .btn, .page-header .btn, .breadcrumb, .navbar, footer, .modal-footer {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
<?= $this->endSection() ?>