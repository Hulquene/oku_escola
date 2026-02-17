<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Relatório de Notas - <?= $class->class_name ?? '' ?></h1>
        <div>
            <a href="<?= site_url('teachers/grades/report/select') ?>" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <button onclick="window.print()" class="btn btn-success">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/grades') ?>">Notas</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/grades/report/select') ?>">Relatórios</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $class->class_name ?? '' ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Info Card -->
<div class="alert alert-info mb-4">
    <div class="row">
        <div class="col-md-4">
            <strong>Turma:</strong> <?= $class->class_name ?? 'N/A' ?>
        </div>
        <div class="col-md-4">
            <strong>Disciplina:</strong> <?= $discipline->discipline_name ?? 'N/A' ?>
        </div>
        <div class="col-md-4">
            <strong>Data:</strong> <?= date('d/m/Y') ?>
        </div>
    </div>
</div>

<!-- Grades Table -->
<div class="card">
    <div class="card-header bg-success text-white">
        <i class="fas fa-star"></i> Relatório de Avaliações Contínuas
    </div>
    <div class="card-body">
        <?php if (!empty($students)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Nº Matrícula</th>
                            <th>Aluno</th>
                            <th class="text-center">AC1</th>
                            <th class="text-center">AC2</th>
                            <th class="text-center">AC3</th>
                            <th class="text-center">Média</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalAverage = 0;
                        $count = 0;
                        $approved = 0;
                        $failed = 0;
                        
                        foreach ($students as $student): 
                            $scores = array_filter([$student->ac1, $student->ac2, $student->ac3]);
                            $average = !empty($scores) ? round(array_sum($scores) / count($scores), 1) : 0;
                            
                            if ($average > 0) {
                                $totalAverage += $average;
                                $count++;
                                if ($average >= 10) {
                                    $approved++;
                                } else {
                                    $failed++;
                                }
                            }
                            
                            $status = $average >= 10 ? 'Aprovado' : ($average > 0 ? 'Reprovado' : 'Pendente');
                            $statusClass = $average >= 10 ? 'success' : ($average > 0 ? 'danger' : 'secondary');
                        ?>
                            <tr>
                                <td><?= $student->student_number ?></td>
                                <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                                <td class="text-center"><?= $student->ac1 ? number_format($student->ac1, 1) : '-' ?></td>
                                <td class="text-center"><?= $student->ac2 ? number_format($student->ac2, 1) : '-' ?></td>
                                <td class="text-center"><?= $student->ac3 ? number_format($student->ac3, 1) : '-' ?></td>
                                <td class="text-center fw-bold"><?= number_format($average, 1) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $statusClass ?>"><?= $status ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Statistics -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h6>Total Alunos</h6>
                            <h3><?= count($students) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h6>Com Notas</h6>
                            <h3><?= $count ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h6>Aprovados</h6>
                            <h3><?= $approved ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h6>Reprovados</h6>
                            <h3><?= $failed ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6 offset-md-3">
                    <div class="card bg-warning">
                        <div class="card-body text-center">
                            <h5>Média Geral da Turma</h5>
                            <h2><?= $count > 0 ? number_format($totalAverage / $count, 1) : '0.0' ?></h2>
                            <small>Taxa de Aprovação: <?= $count > 0 ? round(($approved / $count) * 100, 1) : 0 ?>%</small>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php else: ?>
            <p class="text-muted text-center">Nenhum aluno encontrado.</p>
        <?php endif; ?>
    </div>
    
    <div class="card-footer text-muted">
        <small>Relatório gerado em <?= date('d/m/Y H:i') ?></small>
    </div>
</div>

<style media="print">
    .btn, .page-header .btn, .breadcrumb, .navbar, .sidebar, footer, .card-header .btn {
        display: none !important;
    }
    .content {
        margin-left: 0 !important;
    }
    .main-content {
        padding: 0 !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .table {
        border: 1px solid #ddd !important;
    }
</style>

<?= $this->endSection() ?>