<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Alunos em Recurso</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-users me-1"></i>
                <?= $exam->discipline_name ?> • <?= $exam->class_name ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/exams/schedules/attendance/' . $exam->id) ?>" 
               class="btn btn-success">
                <i class="fas fa-user-check me-1"></i> Registar Presenças
            </a>
            <a href="<?= site_url('admin/exams/schedules/results/' . $exam->id) ?>" 
               class="btn btn-warning">
                <i class="fas fa-graduation-cap me-1"></i> Registar Notas
            </a>
            <a href="<?= site_url('admin/exams/appeals/scheduled/' . $exam->semester_id) ?>" 
               class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/appeals') ?>">Recursos</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/appeals/scheduled/' . $exam->semester_id) ?>">Exames Agendados</a></li>
            <li class="breadcrumb-item active">Alunos</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações do Exame -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <p class="mb-1"><strong>Período:</strong></p>
                <p><?= $exam->period_name ?></p>
            </div>
            <div class="col-md-3">
                <p class="mb-1"><strong>Data:</strong></p>
                <p><?= date('d/m/Y', strtotime($exam->exam_date)) ?> às <?= $exam->exam_time ?></p>
            </div>
            <div class="col-md-3">
                <p class="mb-1"><strong>Turma:</strong></p>
                <p><?= $exam->class_name ?></p>
            </div>
            <div class="col-md-3">
                <p class="mb-1"><strong>Disciplina:</strong></p>
                <p><?= $exam->discipline_name ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Alunos -->
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-table me-2 text-primary"></i>
            Lista de Alunos Elegíveis para Recurso
        </h5>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($students)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 ps-3">#</th>
                            <th class="border-0 py-3">Nº Aluno</th>
                            <th class="border-0 py-3">Nome do Aluno</th>
                            <th class="border-0 py-3 text-center">Nota Anterior</th>
                            <th class="border-0 py-3 text-center">Presença</th>
                            <th class="border-0 py-3 text-center">Nova Nota</th>
                            <th class="border-0 py-3 text-center pe-3">Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($students as $student): ?>
                            <?php
                            $attended = isset($attendance[$student->enrollment_id]) ? 
                                        $attendance[$student->enrollment_id]->attended : null;
                            $newScore = isset($results[$student->enrollment_id]) ? 
                                        $results[$student->enrollment_id]->score : null;
                            
                            $newStatus = null;
                            if ($newScore) {
                                $newStatus = $newScore >= 10 ? 'Aprovado' : 'Reprovado';
                            }
                            ?>
                            <tr>
                                <td class="ps-3"><?= $i++ ?></td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info p-2">
                                        <?= $student->student_number ?>
                                    </span>
                                </td>
                                <td class="fw-semibold"><?= $student->first_name ?> <?= $student->last_name ?></td>
                                <td class="text-center">
                                    <span class="fw-bold text-warning">
                                        <?= number_format($student->previous_score, 1) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($attended === 1): ?>
                                        <span class="badge bg-success">Presente</span>
                                    <?php elseif ($attended === 0): ?>
                                        <span class="badge bg-danger">Ausente</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($newScore): ?>
                                        <span class="fw-bold <?= $newScore >= 10 ? 'text-success' : 'text-danger' ?>">
                                            <?= number_format($newScore, 1) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-3">
                                    <?php if ($newStatus): ?>
                                        <span class="badge bg-<?= $newStatus == 'Aprovado' ? 'success' : 'danger' ?> p-2">
                                            <?= $newStatus ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Resumo -->
            <div class="card-footer bg-white">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Total de Alunos:</strong> <?= count($students) ?></p>
                        <p class="mb-1"><strong>Presenças Registadas:</strong> 
                            <?= count(array_filter($attendance, fn($a) => $a->attended)) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Notas Registadas:</strong> <?= count($results) ?></p>
                        <p class="mb-1"><strong>Aprovados no Recurso:</strong> 
                            <?= count(array_filter($results, fn($r) => $r->score >= 10)) ?></p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum aluno encontrado</h5>
                <p class="text-muted">
                    Não existem alunos em situação de recurso para esta disciplina.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>