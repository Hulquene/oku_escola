<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('students/profile') ?>">Meu Perfil</a></li>
            <li class="breadcrumb-item active" aria-current="page">Histórico Académico</li>
        </ol>
    </nav>
</div>

<?php if (!empty($academicRecords)): ?>
    <div class="accordion" id="academicHistoryAccordion">
        <?php foreach ($academicRecords as $index => $record): ?>
            <div class="accordion-item mb-3">
                <h2 class="accordion-header" id="heading<?= $index ?>">
                    <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" 
                            data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" 
                            aria-expanded="<?= $index == 0 ? 'true' : 'false' ?>" 
                            aria-controls="collapse<?= $index ?>">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-calendar-alt me-2"></i>
                                <strong><?= $record['enrollment']->year_name ?></strong>
                                <span class="badge bg-<?= $record['enrollment']->status == 'Ativo' ? 'success' : 'secondary' ?> ms-2">
                                    <?= $record['enrollment']->status ?>
                                </span>
                            </div>
                            <div class="me-3">
                                <span class="text-muted">Turma: <?= $record['enrollment']->class_name ?></span>
                                <span class="mx-2">|</span>
                                <span class="text-muted">Turno: <?= $record['enrollment']->class_shift ?></span>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= $index == 0 ? 'show' : '' ?>" 
                     aria-labelledby="heading<?= $index ?>" data-bs-parent="#academicHistoryAccordion">
                    <div class="accordion-body">
                        <?php if (!empty($record['semesters'])): ?>
                            <?php foreach ($record['semesters'] as $semesterData): ?>
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-chart-line"></i> 
                                            <?= $semesterData['semester']->semester_name ?>
                                            <span class="badge bg-info float-end">
                                                Média: <?= $semesterData['average'] ?> valores
                                            </span>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Disciplina</th>
                                                        <th class="text-center">Nota Final</th>
                                                        <th class="text-center">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($semesterData['grades'] as $grade): ?>
                                                        <tr>
                                                            <td><?= $grade->discipline_name ?></td>
                                                            <td class="text-center">
                                                                <span class="badge <?= $grade->final_score >= 10 ? 'bg-success' : 'bg-danger' ?>">
                                                                    <?= number_format($grade->final_score, 1, ',', '.') ?>
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php if ($grade->status == 'Aprovado'): ?>
                                                                    <span class="badge bg-success">Aprovado</span>
                                                                <?php elseif ($grade->status == 'Reprovado'): ?>
                                                                    <span class="badge bg-danger">Reprovado</span>
                                                                <?php elseif ($grade->status == 'Recurso'): ?>
                                                                    <span class="badge bg-warning">Recurso</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary"><?= $grade->status ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Nenhum registo de notas disponível para este ano letivo.
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-2 text-muted small">
                            <i class="fas fa-info-circle"></i> 
                            Matrícula: <?= $record['enrollment']->enrollment_number ?> | 
                            Data: <?= date('d/m/Y', strtotime($record['enrollment']->enrollment_date)) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info text-center">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <h5>Nenhum histórico académico encontrado</h5>
        <p>Não há registos de matrículas anteriores.</p>
    </div>
<?php endif; ?>

<div class="row mt-4">
    <div class="col-12">
        <a href="<?= site_url('students/profile') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar ao Perfil
        </a>
    </div>
</div>

<?= $this->endSection() ?>