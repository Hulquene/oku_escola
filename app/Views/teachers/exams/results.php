<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('teachers/exams/grade/' . $exam->id) ?>" class="btn btn-success me-2">
                <i class="fas fa-pencil-alt"></i> Lançar/Editar Notas
            </a>
            <a href="<?= site_url('teachers/exams/attendance/' . $exam->id) ?>" class="btn btn-info me-2">
                <i class="fas fa-user-check"></i> Presenças
            </a>
            <a href="<?= site_url('teachers/exams') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active" aria-current="page">Resultados</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações do Exame -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <i class="fas fa-info-circle"></i> Informações do Exame
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <p><strong>Exame:</strong> <?= esc($exam->exam_name ?? $exam->board_name) ?></p>
                <p><strong>Tipo:</strong> <span class="badge bg-info"><?= esc($exam->board_type) ?></span></p>
            </div>
            <div class="col-md-4">
                <p><strong>Turma:</strong> <?= esc($exam->class_name) ?></p>
                <p><strong>Disciplina:</strong> <?= esc($exam->discipline_name) ?></p>
            </div>
            <div class="col-md-4">
                <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($exam->exam_date)) ?></p>
                <p><strong>Valor Máximo:</strong> <?= $exam->max_score ?> pontos</p>
            </div>
        </div>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Total Alunos</h6>
                        <h2 class="mb-0"><?= $totalStudents ?></h2>
                    </div>
                    <i class="fas fa-users fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Notas Lançadas</h6>
                        <h2 class="mb-0"><?= $statistics->total ?? 0 ?></h2>
                    </div>
                    <i class="fas fa-check-circle fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Média</h6>
                        <h2 class="mb-0"><?= number_format($statistics->average ?? 0, 1) ?></h2>
                    </div>
                    <i class="fas fa-chart-line fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Taxa Aprovação</h6>
                        <h2 class="mb-0"><?= $statistics->approval_rate ?? 0 ?>%</h2>
                    </div>
                    <i class="fas fa-percent fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Resultados -->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-table"></i> Resultados do Exame
            <span class="badge bg-secondary ms-2"><?= count($results) ?> registros</span>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-primary" onclick="exportTableToExcel()">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
            <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($results)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="resultsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Nº Matrícula</th>
                            <th>Aluno</th>
                            <th>Nota</th>
                            <th>Percentual</th>
                            <th>Falta</th>
                            <th>Status</th>
                            <th>Data Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $result): ?>
                            <?php 
                            $percentage = $exam->max_score > 0 ? ($result->score / $exam->max_score) * 100 : 0;
                            $statusClass = $result->is_absent ? 'secondary' : 
                                          ($percentage >= 50 ? 'success' : 'danger');
                            $statusText = $result->is_absent ? 'Falta' : 
                                         ($percentage >= 50 ? 'Aprovado' : 'Reprovado');
                            $dateField = $result->created_at ?? $result->recorded_at ?? null;
                            ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= $result->student_number ?></span></td>
                                <td><?= $result->first_name ?> <?= $result->last_name ?></td>
                                <td>
                                    <strong><?= number_format($result->score, 1) ?></strong> / <?= $exam->max_score ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar bg-<?= $result->is_absent ? 'secondary' : ($percentage >= 50 ? 'success' : 'danger') ?>" 
                                                 style="width: <?= $percentage ?>%"></div>
                                        </div>
                                        <span><?= number_format($percentage, 1) ?>%</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if ($result->is_absent): ?>
                                        <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Sim</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><i class="fas fa-check-circle"></i> Não</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $statusClass ?> p-2">
                                        <i class="fas fa-<?= $result->is_absent ? 'user-times' : ($percentage >= 50 ? 'check-circle' : 'times-circle') ?> me-1"></i>
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $dateField ? date('d/m/Y H:i', strtotime($dateField)) : '-' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="2">Totais</th>
                            <th><?= $statistics->total ?? 0 ?> alunos</th>
                            <th colspan="4">Média: <?= number_format($statistics->average ?? 0, 1) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Informações adicionais -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota de aprovação:</strong> Mínimo 50% (<?= $exam->max_score * 0.5 ?> pontos)
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-secondary mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        <strong>Aprovados:</strong> <?= $statistics->approved ?? 0 ?> | 
                        <strong>Reprovados:</strong> <?= $statistics->failed ?? 0 ?> |
                        <strong>Faltas:</strong> <?= $statistics->absent ?? 0 ?>
                    </div>
                </div>
            </div>
            
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum resultado lançado para este exame</h5>
                <p class="text-muted mb-4">Clique no botão abaixo para lançar as notas dos alunos.</p>
                <a href="<?= site_url('teachers/exams/grade/' . $exam->id) ?>" class="btn btn-success btn-lg">
                    <i class="fas fa-pencil-alt me-2"></i> Lançar Notas
                </a>
            </div>
        <?php endif; ?>
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
    $('#resultsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc']],
        pageLength: 25,
        responsive: true,
        columnDefs: [
            { type: 'num', targets: 2 }
        ]
    });
});

// Exportar para Excel
function exportTableToExcel() {
    const table = document.getElementById('resultsTable');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    let csv = [];
    
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        const rowData = cells.map(cell => cell.innerText.trim().replace(/,/g, ';'));
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'resultados_exame_<?= $exam->id ?>_' + new Date().toISOString().split('T')[0] + '.csv');
    link.click();
}
</script>
<?= $this->endSection() ?>