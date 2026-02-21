<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Histórico Escolar</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-history me-1"></i>
                <?= $student->first_name ?> <?= $student->last_name ?>
            </p>
        </div>
        <div>
            <a href="<?= site_url('admin/students/view/' . $student->id) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/academic-records') ?>">Pautas</a></li>
            <li class="breadcrumb-item active">Histórico</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações do Aluno -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5 class="fw-semibold mb-3">Dados do Aluno</h5>
                <table class="table table-borderless">
                    <tr>
                        <td width="120"><strong>Nome:</strong></td>
                        <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                    </tr>
                    <tr>
                        <td><strong>Nº Matrícula:</strong></td>
                        <td><?= $student->student_number ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td><?= $student->email ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5 class="fw-semibold mb-3">Resumo Acadêmico</h5>
                <table class="table table-borderless">
                    <tr>
                        <td width="120"><strong>Total Anos:</strong></td>
                        <td><?= count($enrollments) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Ano Atual:</strong></td>
                        <td>
                            <?php 
                            $currentEnrollment = array_filter($enrollments, function($e) {
                                return $e->status == 'Ativo';
                            });
                            echo !empty($currentEnrollment) ? $currentEnrollment[array_key_first($currentEnrollment)]->class_name : 'N/A';
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Histórico por Ano Letivo -->
<?php if (!empty($enrollments)): ?>
    <?php foreach ($enrollments as $enrollment): ?>
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                        <?= $enrollment->year_name ?> • <?= $enrollment->class_name ?> (<?= $enrollment->level_name ?>)
                    </h5>
                    <?php if ($enrollment->yearly): ?>
                        <span class="badge bg-<?= $enrollment->yearly->final_status == 'Aprovado' ? 'success' : 'danger' ?> p-3">
                            <?= $enrollment->yearly->final_status ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($enrollment->semesters)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-3 ps-3">Semestre</th>
                                    <th class="border-0 py-3 text-center">Média</th>
                                    <th class="border-0 py-3 text-center">Aprovadas</th>
                                    <th class="border-0 py-3 text-center">Recurso</th>
                                    <th class="border-0 py-3 text-center">Reprovadas</th>
                                    <th class="border-0 py-3 text-center pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($enrollment->semesters as $sem): ?>
                                    <tr>
                                        <td class="ps-3 fw-semibold"><?= $sem->semester_name ?></td>
                                        <td class="text-center">
                                            <span class="fw-bold <?= $sem->overall_average >= 10 ? 'text-success' : 'text-warning' ?>">
                                                <?= number_format($sem->overall_average, 2) ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><?= $sem->approved_disciplines ?></td>
                                        <td class="text-center"><?= $sem->appeal_disciplines ?></td>
                                        <td class="text-center"><?= $sem->failed_disciplines ?></td>
                                        <td class="text-center pe-3">
                                            <span class="badge bg-<?= $sem->status == 'Aprovado' ? 'success' : ($sem->status == 'Recurso' ? 'warning' : 'danger') ?>">
                                                <?= $sem->status ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <?php if ($enrollment->yearly): ?>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="5" class="text-end pe-3"><strong>Média Anual:</strong></td>
                                        <td class="text-center">
                                            <span class="fw-bold fs-5 <?= $enrollment->yearly->final_average >= 10 ? 'text-success' : 'text-warning' ?>">
                                                <?= number_format($enrollment->yearly->final_average, 2) ?>
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">Nenhum resultado registado para este ano letivo</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Nenhum histórico encontrado para este aluno.
    </div>
<?php endif; ?>

<?= $this->endSection() ?>