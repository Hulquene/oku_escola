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
            <a href="<?= site_url('teachers/grades/export/' . $class->id . '?discipline=' . $discipline->id) ?>" 
               class="btn btn-primary">
                <i class="fas fa-file-excel"></i> Exportar
            </a>
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
        <div class="col-md-3">
            <strong>Turma:</strong> <?= $class->class_name ?? 'N/A' ?>
        </div>
        <div class="col-md-3">
            <strong>Disciplina:</strong> <?= $discipline->discipline_name ?? 'N/A' ?>
        </div>
        <div class="col-md-3">
            <strong>Código:</strong> <?= $discipline->discipline_code ?? 'N/A' ?>
        </div>
        <div class="col-md-3">
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
                            <?php for ($i = 1; $i <= 6; $i++): ?>
                                <th class="text-center">AC<?= $i ?></th>
                            <?php endfor; ?>
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
                            $scores = [];
                            $total = 0;
                            $scoreCount = 0;
                            
                            for ($i = 1; $i <= 6; $i++) {
                                $score = $student->ac_scores["ac{$i}"] ?? '-';
                                if ($score !== '-' && $score !== '') {
                                    $scores[] = $score;
                                    $total += $score;
                                    $scoreCount++;
                                }
                            }
                            
                            $average = $scoreCount > 0 ? round($total / $scoreCount, 1) : 0;
                            
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
                                
                                <?php for ($i = 1; $i <= 6; $i++): 
                                    $score = $student->ac_scores["ac{$i}"] ?? '-';
                                ?>
                                    <td class="text-center"><?= $score !== '-' ? number_format($score, 1) : '-' ?></td>
                                <?php endfor; ?>
                                
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
            
            <!-- Histograma simples -->
            <?php if ($count > 0): ?>
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <i class="fas fa-chart-bar"></i> Distribuição das Notas
                        </div>
                        <div class="card-body">
                            <?php
                            $ranges = [
                                '0-4' => 0,
                                '5-7' => 0,
                                '8-9' => 0,
                                '10-13' => 0,
                                '14-16' => 0,
                                '17-20' => 0
                            ];
                            
                            foreach ($students as $student) {
                                $avg = 0;
                                $total = 0;
                                $sc = 0;
                                for ($i = 1; $i <= 6; $i++) {
                                    $score = $student->ac_scores["ac{$i}"] ?? null;
                                    if ($score && $score !== '') {
                                        $total += $score;
                                        $sc++;
                                    }
                                }
                                if ($sc > 0) {
                                    $avg = $total / $sc;
                                    if ($avg <= 4) $ranges['0-4']++;
                                    elseif ($avg <= 7) $ranges['5-7']++;
                                    elseif ($avg <= 9) $ranges['8-9']++;
                                    elseif ($avg <= 13) $ranges['10-13']++;
                                    elseif ($avg <= 16) $ranges['14-16']++;
                                    else $ranges['17-20']++;
                                }
                            }
                            ?>
                            
                            <div class="row text-center">
                                <?php foreach ($ranges as $range => $countRange): ?>
                                    <div class="col-2">
                                        <div class="p-2 border rounded">
                                            <strong><?= $range ?></strong><br>
                                            <span class="badge bg-info"><?= $countRange ?> alunos</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
        <?php else: ?>
            <p class="text-muted text-center">Nenhum aluno encontrado.</p>
        <?php endif; ?>
    </div>
    
    <div class="card-footer text-muted">
        <small>
            <i class="fas fa-clock"></i> Relatório gerado em <?= date('d/m/Y H:i') ?> | 
            <i class="fas fa-info-circle"></i> Total de Avaliações Contínuas: <?= $acCount ?? 6 ?>
        </small>
    </div>
</div>

<style media="print">
    .btn, .page-header .btn, .breadcrumb, .navbar, .sidebar, footer, .card-header .btn,
    .stat-card, .quick-actions, .actions, .btn-group {
        display: none !important;
    }
    .content, .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
    .table {
        border: 1px solid #000 !important;
    }
    .table th {
        background-color: #f2f2f2 !important;
        color: #000 !important;
    }
    @page {
        size: landscape;
        margin: 1cm;
    }
</style>

<?= $this->endSection() ?>