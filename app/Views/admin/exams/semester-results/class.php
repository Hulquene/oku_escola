<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Resultados Semestrais da Turma</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-users me-1"></i>
                <?= $class->class_name ?> • <?= $class->level_name ?> • <?= $semester->semester_name ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" onclick="exportToExcel()">
                <i class="fas fa-file-excel me-1"></i> Excel
            </button>
            <button class="btn btn-danger" onclick="exportToPDF()">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </button>
            <button class="btn btn-warning" onclick="generateReportCards()">
                <i class="fas fa-print me-1"></i> Pautas
            </button>
            <a href="<?= site_url('admin/classes/view/' . $class->id) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes') ?>">Turmas</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes/view/' . $class->id) ?>"><?= $class->class_name ?></a></li>
            <li class="breadcrumb-item active">Resultados - <?= $semester->semester_name ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Resumo -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Alunos</h6>
                        <h3 class="mb-0"><?= $summary->total_students ?? 0 ?></h3>
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
                        <h6 class="text-muted mb-1">Aprovados</h6>
                        <h3 class="mb-0"><?= $summary->approved ?? 0 ?></h3>
                        <small class="text-success">
                            <?= ($summary->total_students ?? 0) > 0 ? round(($summary->approved / $summary->total_students) * 100) : 0 ?>%
                        </small>
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
                        <h3 class="mb-0"><?= $summary->appeal ?? 0 ?></h3>
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
                        <h6 class="text-muted mb-1">Reprovados</h6>
                        <h3 class="mb-0"><?= $summary->failed ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas Adicionais -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-2">Média da Turma</h6>
                <div class="d-flex align-items-end">
                    <h2 class="mb-0 <?= ($summary->class_average ?? 0) >= 10 ? 'text-success' : 'text-warning' ?>">
                        <?= number_format($summary->class_average ?? 0, 2) ?>
                    </h2>
                    <small class="text-muted ms-2">/ 20</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-2">Taxa de Aprovação</h6>
                <div class="d-flex align-items-end">
                    <h2 class="mb-0 text-success">
                        <?= ($summary->total_students ?? 0) > 0 ? round(($summary->approved / $summary->total_students) * 100, 1) : 0 ?>%
                    </h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-2">Pendentes</h6>
                <div class="d-flex align-items-end">
                    <h2 class="mb-0 text-warning"><?= $summary->pending ?? 0 ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Resultados -->
<div class="card">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-table me-2 text-primary"></i>
                Resultados por Aluno
            </h5>
            <div>
                <input type="text" class="form-control form-control-sm" id="searchInput" 
                       placeholder="Buscar aluno..." style="width: 250px;">
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($results)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="resultsTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 ps-3" width="50">#</th>
                            <th class="border-0 py-3">Nº Aluno</th>
                            <th class="border-0 py-3">Nome do Aluno</th>
                            <th class="border-0 py-3 text-center">Média</th>
                            <th class="border-0 py-3 text-center">Aprov.</th>
                            <th class="border-0 py-3 text-center">Rec.</th>
                            <th class="border-0 py-3 text-center">Rep.</th>
                            <th class="border-0 py-3 text-center">Status</th>
                            <th class="border-0 py-3 text-center pe-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($results as $result): ?>
                            <?php
                            $statusColors = [
                                'Aprovado' => 'success',
                                'Recurso' => 'warning',
                                'Reprovado' => 'danger',
                                'Em Andamento' => 'secondary'
                            ];
                            $statusColor = $statusColors[$result['status']] ?? 'secondary';
                            ?>
                            <tr>
                                <td class="ps-3"><?= $i++ ?></td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info p-2">
                                        <?= $result['student_number'] ?>
                                    </span>
                                </td>
                                <td class="fw-semibold"><?= $result['student_name'] ?></td>
                                <td class="text-center">
                                    <span class="fw-bold <?= $result['overall_average'] >= 10 ? 'text-success' : 'text-warning' ?>">
                                        <?= number_format($result['overall_average'], 2) ?>
                                    </span>
                                </td>
                                <td class="text-center"><?= $result['approved'] ?></td>
                                <td class="text-center"><?= $result['appeal'] ?></td>
                                <td class="text-center"><?= $result['failed'] ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $statusColor ?> p-2">
                                        <?= $result['status'] ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('admin/semester-results/student/' . $result['enrollment_id'] . '/' . $semester->id) ?>" 
                                           class="btn btn-outline-primary" title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('admin/discipline-averages/student/' . $result['enrollment_id'] . '/' . $semester->id) ?>" 
                                           class="btn btn-outline-info" title="Ver médias">
                                            <i class="fas fa-chart-line"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-graduation-cap fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum resultado encontrado</h5>
                <p class="text-muted mb-3">
                    Ainda não existem resultados calculados para esta turma.
                </p>
                <button class="btn btn-primary" onclick="calculateClassResults()">
                    <i class="fas fa-calculator me-2"></i>Calcular Resultados
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

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
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    let searchText = this.value.toLowerCase();
    let rows = document.querySelectorAll('#resultsTable tbody tr');
    
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchText) ? '' : 'none';
    });
});

// Export functions
function exportToExcel() {
    window.location.href = '<?= site_url('admin/semester-results/export/' . $class->id . '/' . $semester->id) ?>?type=excel';
}

function exportToPDF() {
    window.location.href = '<?= site_url('admin/semester-results/export/' . $class->id . '/' . $semester->id) ?>?type=pdf';
}

function generateReportCards() {
    window.location.href = '<?= site_url('admin/semester-results/generate-report-cards/' . $class->id . '/' . $semester->id) ?>';
}

function calculateClassResults() {
    $.ajax({
        url: '<?= site_url('admin/semester-results/calculate-class/' . $class->id . '/' . $semester->id) ?>',
        method: 'POST',
        data: {
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                toastr.success('Resultados calculados com sucesso!');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                toastr.error(response.message);
            }
        }
    });
}

// Auto-refresh every 30 seconds if there are pending results
<?php if (($summary->pending ?? 0) > 0): ?>
setTimeout(() => {
    location.reload();
}, 30000);
<?php endif; ?>
</script>

<?= $this->endSection() ?>