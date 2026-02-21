<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Médias do Aluno</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-chart-line me-1"></i>
                <?= $enrollment->first_name ?> <?= $enrollment->last_name ?> • <?= $enrollment->class_name ?>
            </p>
        </div>
        <div>
            <a href="<?= site_url('admin/students/view/' . $enrollment->student_id) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar ao Aluno
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students') ?>">Alunos</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students/view/' . $enrollment->student_id) ?>"><?= $enrollment->first_name ?> <?= $enrollment->last_name ?></a></li>
            <li class="breadcrumb-item active">Médias - <?= $semester->semester_name ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                            <i class="fas fa-book text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Disciplinas</h6>
                        <h3 class="mb-0 fw-semibold"><?= $stats['total'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 p-2 rounded">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Aprovadas</h6>
                        <h3 class="mb-0 fw-semibold"><?= $stats['approved'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Recurso</h6>
                        <h3 class="mb-0 fw-semibold"><?= $stats['appeal'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-danger bg-opacity-10 p-2 rounded">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Reprovadas</h6>
                        <h3 class="mb-0 fw-semibold"><?= $stats['failed'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Informações do Semestre -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 p-2 rounded">
                            <i class="fas fa-calendar-alt text-info"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Semestre</h6>
                        <h5 class="mb-0"><?= $semester->semester_name ?> (<?= $semester->semester_type ?>)</h5>
                        <small class="text-muted">
                            <?= date('d/m/Y', strtotime($semester->start_date)) ?> a <?= date('d/m/Y', strtotime($semester->end_date)) ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-secondary bg-opacity-10 p-2 rounded">
                            <i class="fas fa-calculator text-secondary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Média Geral do Semestre</h6>
                        <h2 class="mb-0 <?= $stats['average'] >= 10 ? 'text-success' : 'text-danger' ?>">
                            <?= number_format($stats['average'], 2) ?>
                        </h2>
                        <small class="text-muted">
                            Melhor: <?= number_format($stats['best'], 1) ?> | Pior: <?= number_format($stats['worst'], 1) ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Médias por Disciplina -->
<div class="card">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-table me-2 text-primary"></i>
                Médias por Disciplina
            </h5>
            <div>
                <button class="btn btn-sm btn-outline-primary" onclick="recalculateAverages()">
                    <i class="fas fa-sync-alt me-1"></i> Recalcular
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 py-3 ps-3" width="50">#</th>
                        <th class="border-0 py-3">Disciplina</th>
                        <th class="border-0 py-3 text-center">Código</th>
                        <th class="border-0 py-3 text-center">Carga Horária</th>
                        <th class="border-0 py-3 text-center">Média AC</th>
                        <th class="border-0 py-3 text-center">Média Exame</th>
                        <th class="border-0 py-3 text-center">Nota Final</th>
                        <th class="border-0 py-3 text-center">Status</th>
                        <th class="border-0 py-3 text-center pe-3">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($averages)): ?>
                        <?php $i = 1; ?>
                        <?php foreach ($averages as $avg): ?>
                            <?php
                            $statusClass = [
                                'Aprovado' => 'success',
                                'Recurso' => 'warning',
                                'Reprovado' => 'danger'
                            ][$avg->status] ?? 'secondary';
                            ?>
                            <tr>
                                <td class="ps-3"><?= $i++ ?></td>
                                <td>
                                    <span class="fw-semibold"><?= $avg->discipline_name ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= $avg->discipline_code ?></span>
                                </td>
                                <td class="text-center"><?= $avg->workload_hours ?>h</td>
                                <td class="text-center">
                                    <?php if ($avg->ac_score > 0): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary p-2">
                                            <?= number_format($avg->ac_score, 1) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($avg->exam_score > 0): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info p-2">
                                            <?= number_format($avg->exam_score, 1) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold fs-5 <?= $avg->final_score >= 10 ? 'text-success' : 'text-warning' ?>">
                                        <?= number_format($avg->final_score, 1) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $statusClass ?> p-2">
                                        <?= $avg->status ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <button class="btn btn-sm btn-outline-info" 
                                            onclick="viewDetails(<?= $avg->discipline_id ?>)"
                                            title="Ver detalhes">
                                        <i class="fas fa-chart-bar"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhuma média encontrada</h5>
                                <p class="text-muted mb-3">
                                    As médias serão calculadas automaticamente à medida que as notas forem registadas.
                                </p>
                                <button class="btn btn-primary" onclick="recalculateAverages()">
                                    <i class="fas fa-sync-alt me-2"></i>Calcular Agora
                                </button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Gráfico de Desempenho -->
<div class="card mt-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-chart-pie me-2 text-success"></i>
            Distribuição das Notas
        </h5>
    </div>
    <div class="card-body">
        <canvas id="gradesChart" style="height: 300px;"></canvas>
    </div>
</div>

<!-- Modal de Detalhes -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-chart-bar me-2"></i>
                    Detalhes da Disciplina
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailsContent">
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-3x text-muted"></i>
                    <p class="mt-3">Carregando detalhes...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
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

.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}
</style>

<script>
// Chart initialization
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('gradesChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Aprovadas (≥10)', 'Recurso (7-9.9)', 'Reprovadas (<7)'],
            datasets: [{
                data: [<?= $stats['approved'] ?>, <?= $stats['appeal'] ?>, <?= $stats['failed'] ?>],
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});

// Recalculate averages
function recalculateAverages() {
    if (!confirm('Recalcular todas as médias deste aluno? Esta operação pode levar alguns segundos.')) {
        return;
    }
    
    $.ajax({
        url: '<?= site_url('admin/discipline-averages/recalculate/' . $enrollment->id . '/' . $semester->id) ?>',
        method: 'POST',
        data: {
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                toastr.error(response.message);
            }
        },
        error: function() {
            toastr.error('Erro ao recalcular médias');
        }
    });
}

// View discipline details
function viewDetails(disciplineId) {
    $('#detailsModal').modal('show');
    
    $.get('<?= site_url('admin/discipline-averages/get-details') ?>/' + 
          <?= $enrollment->id ?> + '/' + <?= $semester->id ?> + '/' + disciplineId,
        function(data) {
            $('#detailsContent').html(data);
        }
    );
}

// Tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
</script>

<?= $this->endSection() ?>