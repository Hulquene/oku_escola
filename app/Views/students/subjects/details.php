<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= $subject->discipline_name ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('students/subjects') ?>">Minhas Disciplinas</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $subject->discipline_name ?></li>
        </ol>
    </nav>
</div>

<!-- Informações da Disciplina -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Informações da Disciplina
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Código:</th>
                                <td><?= $subject->discipline_code ?></td>
                            </tr>
                            <tr>
                                <th>Tipo:</th>
                                <td>
                                    <span class="badge bg-info"><?= $subject->discipline_type ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Carga Horária:</th>
                                <td><?= $subject->workload_hours ?> horas</td>
                            </tr>
                            <tr>
                                <th>Nota Mínima:</th>
                                <td><?= number_format($subject->min_grade, 1, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <th>Nota Máxima:</th>
                                <td><?= number_format($subject->max_grade, 1, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <th>Nota de Aprovação:</th>
                                <td><?= number_format($subject->approval_grade, 1, ',', '.') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="text-muted text-center">Professor</h6>
                                <h5 class="text-center"><?= $teacher_name ?? 'Não atribuído' ?></h5>
                                <?php if (isset($teacher) && $teacher): ?>
                                    <hr>
                                    <p><i class="fas fa-envelope"></i> <?= $teacher->email ?></p>
                                    <?php if ($teacher->phone): ?>
                                        <p><i class="fas fa-phone"></i> <?= $teacher->phone ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($subject->description): ?>
                            <div class="mt-3">
                                <h6>Descrição:</h6>
                                <p class="text-muted"><?= $subject->description ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-line"></i> Resumo de Desempenho
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <h2 class="<?= $finalGrade && $finalGrade->final_score >= $subject->approval_grade ? 'text-success' : 'text-danger' ?>">
                        <?= $finalGradeFormatted ?>
                    </h2>
                    <p class="text-muted">Nota Final</p>
                </div>
                
                <div class="mb-3">
                    <h3><?= $averageGradeFormatted ?></h3>
                    <p class="text-muted">Média das Avaliações</p>
                </div>
                
                <div class="mb-3">
                    <h3><?= $attendancePercentage ?>%</h3>
                    <p class="text-muted">Presença</p>
                </div>
                
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-info" role="progressbar" 
                         style="width: <?= $attendancePercentage ?>%" 
                         aria-valuenow="<?= $attendancePercentage ?>" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                
                <?php if ($finalGrade && $finalGrade->status): ?>
                <div class="mt-3">
                    <span class="badge bg-<?= $finalGrade->status == 'Aprovado' ? 'success' : 'danger' ?> p-2">
                        <?= $finalGrade->status ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Avaliações Contínuas -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-star"></i> Avaliações Contínuas
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
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assessments as $assessment): ?>
                                    <tr>
                                        <td><?= $assessment->formatted_date ?></td>
                                        <td><?= $assessment->assessment_type ?></td>
                                        <td class="fw-bold <?= $assessment->score >= 10 ? 'text-success' : 'text-danger' ?>">
                                            <?= number_format($assessment->score, 1, ',', '.') ?>
                                        </td>
                                        <td>
                                            <?php if ($assessment->score >= 10): ?>
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
                    <p class="text-muted text-center">Nenhuma avaliação contínua registrada ainda.</p>
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
            <div class="card-header">
                <i class="fas fa-pencil-alt"></i> Resultados de Exames
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
                                    <td><?= $result->formatted_date ?></td>
                                    <td><?= $result->exam_name ?></td>
                                    <td><?= $result->board_type ?></td>
                                    <td><?= $result->weight ?>%</td>
                                    <td class="fw-bold <?= $result->score >= 10 ? 'text-success' : 'text-danger' ?>">
                                        <?= number_format($result->score, 1, ',', '.') ?>
                                    </td>
                                    <td>
                                        <?php if ($result->grade): ?>
                                            <span class="badge bg-info"><?= $result->grade ?></span>
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
            <div class="card-header">
                <i class="fas fa-calendar-check"></i> Registro de Presenças
            </div>
            <div class="card-body">
                <?php if (!empty($attendance)): ?>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5>Total de Aulas</h5>
                                    <h3><?= $attendanceStats['total'] ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5>Presenças</h5>
                                    <h3 class="text-success"><?= $attendanceStats['present'] ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5>Faltas</h5>
                                    <h3 class="text-danger"><?= $attendanceStats['absent'] ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
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
                                        <td><?= $a->formatted_date ?></td>
                                        <td>
                                            <span class="badge bg-<?= $a->badge_color ?>"><?= $a->status ?></span>
                                        </td>
                                        <td><?= $a->justification ?? '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Nenhuma presença registrada ainda.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Próximos Exames -->
<?php if (!empty($upcomingExams)): ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-calendar-alt"></i> Próximos Exames
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
                                    <td><?= $exam->formatted_date ?></td>
                                    <td><?= $exam->formatted_time ?? '--:--' ?></td>
                                    <td><?= $exam->exam_name ?></td>
                                    <td><?= $exam->board_type ?></td>
                                    <td><?= $exam->weight ?>%</td>
                                    <td><?= $exam->exam_room ?? 'Não definida' ?></td>
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
        <a href="<?= site_url('students/subjects') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar para Lista
        </a>
        <a href="<?= site_url('students/grades') ?>" class="btn btn-primary">
            <i class="fas fa-star"></i> Ver Todas as Notas
        </a>
        <a href="<?= site_url('students/attendance') ?>" class="btn btn-info">
            <i class="fas fa-calendar-check"></i> Ver Todas as Presenças
        </a>
        <a href="<?= site_url('students/exams') ?>" class="btn btn-warning">
            <i class="fas fa-pencil-alt"></i> Calendário de Exames
        </a>
    </div>
</div>

<?= $this->endSection() ?>