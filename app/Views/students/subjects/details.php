<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= esc($subject->discipline_name ?? 'Detalhes da Disciplina') ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('students/subjects') ?>">Minhas Disciplinas</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($subject->discipline_name ?? 'Detalhes') ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?= session('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Informações da Disciplina -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-info-circle"></i> Informações da Disciplina
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Código:</th>
                                <td><strong><?= esc($subject->discipline_code ?? 'N/A') ?></strong></td>
                            </tr>
                            <tr>
                                <th>Tipo:</th>
                                <td>
                                    <span class="badge bg-info"><?= esc($subject->discipline_type ?? 'Obrigatória') ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Carga Horária:</th>
                                <td><strong><?= $subject->workload_hours ?? 0 ?> horas</strong></td>
                            </tr>
                            <tr>
                                <th>Nota Mínima:</th>
                                <td><?= number_format($subject->min_grade ?? 0, 1, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <th>Nota Máxima:</th>
                                <td><?= number_format($subject->max_grade ?? 20, 1, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <th>Nota de Aprovação:</th>
                                <td><strong><?= number_format($subject->approval_grade ?? 10, 1, ',', '.') ?></strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="text-muted text-center mb-3">
                                    <i class="fas fa-chalkboard-teacher"></i> Professor
                                </h6>
                                <h5 class="text-center"><?= esc($teacher_name ?? 'Não atribuído') ?></h5>
                                
                                <?php if (isset($teacher) && $teacher): ?>
                                    <hr>
                                    <p class="mb-1"><i class="fas fa-envelope text-muted"></i> 
                                        <a href="mailto:<?= $teacher->email ?>"><?= $teacher->email ?></a>
                                    </p>
                                    <?php if ($teacher->phone ?? false): ?>
                                        <p class="mb-0"><i class="fas fa-phone text-muted"></i> 
                                            <a href="tel:<?= $teacher->phone ?>"><?= $teacher->phone ?></a>
                                        </p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($subject->description ?? false): ?>
                            <div class="mt-3">
                                <h6><i class="fas fa-align-left"></i> Descrição:</h6>
                                <div class="p-3 bg-light rounded">
                                    <?= nl2br(esc($subject->description)) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <i class="fas fa-chart-line"></i> Resumo de Desempenho
            </div>
            <div class="card-body text-center">
                <!-- Nota Final -->
                <div class="mb-4">
                    <span class="text-muted">Nota Final</span>
                    <h2 class="display-4 <?= ($finalGrade->final_score ?? 0) >= ($subject->approval_grade ?? 10) ? 'text-success' : 'text-danger' ?>">
                        <?= isset($finalGrade) ? number_format($finalGrade->final_score, 1, ',', '.') : '--' ?>
                    </h2>
                    <?php if (isset($finalGrade) && $finalGrade->status): ?>
                        <span class="badge bg-<?= $finalGrade->status == 'Aprovado' ? 'success' : 'danger' ?> p-2">
                            <?= $finalGrade->status ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <!-- Média das Avaliações -->
                <div class="mb-4">
                    <span class="text-muted">Média das Avaliações</span>
                    <h3><?= $averageGradeFormatted ?? '-' ?></h3>
                </div>
                
                <!-- Presença -->
                <div class="mb-4">
                    <span class="text-muted">Presença</span>
                    <h3><?= $attendancePercentage ?? 0 ?>%</h3>
                    <div class="progress mt-2" style="height: 10px;">
                        <div class="progress-bar bg-info" role="progressbar" 
                             style="width: <?= $attendancePercentage ?? 0 ?>%" 
                             aria-valuenow="<?= $attendancePercentage ?? 0 ?>" 
                             aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>
                
                <!-- Estatísticas Rápidas -->
                <div class="row g-2">
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <small class="text-muted">Avaliações</small>
                            <h5><?= $totalAssessments ?? 0 ?></h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <small class="text-muted">Exames</small>
                            <h5><?= $totalExams ?? 0 ?></h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <small class="text-muted">Presenças</small>
                            <h5><?= $attendanceStats['present'] ?? 0 ?></h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <small class="text-muted">Faltas</small>
                            <h5><?= $attendanceStats['absent'] ?? 0 ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Avaliações Contínuas -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <i class="fas fa-star"></i> Avaliações Contínuas
                <span class="badge bg-light text-dark float-end">Total: <?= count($assessments ?? []) ?></span>
            </div>
            <div class="card-body">
                <?php if (!empty($assessments)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo de Avaliação</th>
                                    <th>Nota</th>
                                    <th>Classificação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assessments as $assessment): ?>
                                    <tr>
                                        <td><?= $assessment->formatted_date ?? date('d/m/Y', strtotime($assessment->assessment_date ?? 'now')) ?></td>
                                        <td><?= esc($assessment->assessment_type ?? 'Avaliação') ?></td>
                                        <td class="fw-bold <?= ($assessment->score ?? 0) >= 10 ? 'text-success' : 'text-danger' ?>">
                                            <?= number_format($assessment->score ?? 0, 1, ',', '.') ?>
                                        </td>
                                        <td>
                                            <?php if (($assessment->score ?? 0) >= 10): ?>
                                                <span class="badge bg-success">Aprovado</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Reprovado</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhuma avaliação contínua registrada ainda.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Resultados de Exames -->
<?php if (!empty($examResults)): ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <i class="fas fa-pencil-alt"></i> Resultados de Exames
                <span class="badge bg-light text-dark float-end">Total: <?= count($examResults) ?></span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Exame</th>
                                <th>Tipo</th>
                                <th>Peso</th>
                                <th>Nota</th>
                                <th>Classificação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($examResults as $result): ?>
                                <tr>
                                    <td><?= $result->formatted_date ?? date('d/m/Y', strtotime($result->exam_date ?? 'now')) ?></td>
                                    <td><?= esc($result->exam_name ?? $result->board_name ?? 'Exame') ?></td>
                                    <td><?= esc($result->board_type ?? 'Normal') ?></td>
                                    <td><?= $result->weight ?? 1 ?>x</td>
                                    <td class="fw-bold <?= ($result->score ?? 0) >= ($result->approval_score ?? 10) ? 'text-success' : 'text-danger' ?>">
                                        <?= number_format($result->score ?? 0, 1, ',', '.') ?>
                                    </td>
                                    <td>
                                        <?php if ($result->grade ?? false): ?>
                                            <span class="badge bg-info"><?= $result->grade ?></span>
                                        <?php elseif (($result->score ?? 0) >= ($result->approval_score ?? 10)): ?>
                                            <span class="badge bg-success">Aprovado</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Reprovado</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Presenças -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <i class="fas fa-calendar-check"></i> Registro de Presenças
                <span class="badge bg-light text-dark float-end">Total: <?= count($attendance ?? []) ?></span>
            </div>
            <div class="card-body">
                <?php if (!empty($attendance)): ?>
                    <!-- Cards de Estatísticas de Presença -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center py-2">
                                    <h6>Presenças</h6>
                                    <h3><?= $attendanceStats['present'] ?? 0 ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center py-2">
                                    <h6>Faltas</h6>
                                    <h3><?= $attendanceStats['absent'] ?? 0 ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center py-2">
                                    <h6>Total Aulas</h6>
                                    <h3><?= $attendanceStats['total'] ?? 0 ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabela de Presenças -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Justificação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attendance as $a): ?>
                                    <tr>
                                        <td><?= $a->formatted_date ?? date('d/m/Y', strtotime($a->attendance_date ?? 'now')) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $a->badge_color ?? ($a->status == 'Presente' ? 'success' : 'danger') ?>">
                                                <?= esc($a->status ?? 'N/A') ?>
                                            </span>
                                        </td>
                                        <td><?= esc($a->justification ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhuma presença registrada ainda.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Próximos Exames -->
<?php if (!empty($upcomingExams)): ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-white">
                <i class="fas fa-calendar-alt"></i> Próximos Exames
                <span class="badge bg-light text-dark float-end"><?= count($upcomingExams) ?> agendado(s)</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Horário</th>
                                <th>Exame</th>
                                <th>Tipo</th>
                                <th>Peso</th>
                                <th>Sala</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcomingExams as $exam): ?>
                                <tr>
                                    <td><strong><?= $exam->formatted_date ?? date('d/m/Y', strtotime($exam->exam_date ?? 'now')) ?></strong></td>
                                    <td><?= $exam->formatted_time ?? ($exam->exam_time ? date('H:i', strtotime($exam->exam_time)) : '--:--') ?></td>
                                    <td><?= esc($exam->exam_name ?? $exam->board_name ?? 'Exame') ?></td>
                                    <td><?= esc($exam->board_type ?? 'Normal') ?></td>
                                    <td><?= $exam->weight ?? 1 ?>x</td>
                                    <td><?= esc($exam->exam_room ?? 'Não definida') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Botões de Ação -->
<div class="row mt-4">
    <div class="col-12">
        <div class="btn-group" role="group">
            <a href="<?= site_url('students/subjects') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="<?= site_url('students/grades') ?>" class="btn btn-primary">
                <i class="fas fa-star"></i> Todas Notas
            </a>
            <a href="<?= site_url('students/attendance') ?>" class="btn btn-info">
                <i class="fas fa-calendar-check"></i> Presenças
            </a>
            <a href="<?= site_url('students/exams') ?>" class="btn btn-warning">
                <i class="fas fa-pencil-alt"></i> Exames
            </a>
            <a href="<?= site_url('students/grades/report-card') ?>" class="btn btn-success">
                <i class="fas fa-file-pdf"></i> Boletim
            </a>
        </div>
    </div>
</div>

<style>
.btn-group {
    flex-wrap: wrap;
    gap: 0.5rem;
}
.btn-group .btn {
    border-radius: 0.25rem !important;
    margin: 0;
}
</style>

<?= $this->endSection() ?>