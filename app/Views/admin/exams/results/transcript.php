<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/students/view/' . $student->id) ?>" class="btn btn-info">
                <i class="fas fa-user-graduate"></i> Ver Aluno
            </a>
            <button onclick="window.print()" class="btn btn-success">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/results') ?>">Resultados</a></li>
            <li class="breadcrumb-item active" aria-current="page">Histórico Escolar</li>
        </ol>
    </nav>
</div>

<!-- Transcript -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-history"></i> Histórico Escolar Completo
    </div>
    <div class="card-body">
        <!-- Student Info -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h4><?= $student->first_name ?> <?= $student->last_name ?></h4>
                <p class="mb-1"><strong>Nº de Matrícula:</strong> <?= $student->student_number ?></p>
                <p class="mb-1"><strong>Email:</strong> <?= $student->email ?></p>
                <p class="mb-0"><strong>Data de Nascimento:</strong> <?= date('d/m/Y', strtotime($student->birth_date)) ?></p>
            </div>
            <div class="col-md-4 text-end">
                <?php if ($student->photo): ?>
                    <img src="<?= base_url('uploads/students/' . $student->photo) ?>" 
                         alt="Foto" 
                         class="img-thumbnail"
                         style="max-width: 100px;">
                <?php endif; ?>
            </div>
        </div>
        
        <hr>
        
        <!-- Academic History -->
        <?php if (!empty($transcript)): ?>
            <?php foreach ($transcript as $yearData): ?>
                <div class="mb-4">
                    <h5 class="bg-light p-2">
                        <i class="fas fa-calendar-alt"></i> 
                        Ano Letivo: <?= $yearData['year']->year_name ?> 
                        (<?= date('Y', strtotime($yearData['year']->start_date)) ?>)
                        <small class="text-muted">Turma: <?= $yearData['year']->class_name ?></small>
                    </h5>
                    
                    <?php if (!empty($yearData['semesters'])): ?>
                        <?php foreach ($yearData['semesters'] as $semesterData): ?>
                            <?php if (!empty($semesterData['grades'])): ?>
                                <div class="ms-3 mb-3">
                                    <h6 class="text-primary"><?= $semesterData['semester']->semester_name ?></h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Disciplina</th>
                                                    <th class="text-center" style="width: 80px;">Média</th>
                                                    <th class="text-center" style="width: 80px;">Exame</th>
                                                    <th class="text-center" style="width: 80px;">Final</th>
                                                    <th class="text-center" style="width: 100px;">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($semesterData['grades'] as $grade): ?>
                                                    <tr>
                                                        <td><?= $grade->discipline_name ?></td>
                                                        <td class="text-center"><?= number_format($grade->average_score, 1) ?></td>
                                                        <td class="text-center"><?= $grade->exam_score ? number_format($grade->exam_score, 1) : '-' ?></td>
                                                        <td class="text-center fw-bold"><?= number_format($grade->final_score, 1) ?></td>
                                                        <td class="text-center">
                                                            <?php
                                                            $statusClass = [
                                                                'Aprovado' => 'success',
                                                                'Reprovado' => 'danger',
                                                                'Recurso' => 'warning',
                                                                'Dispensado' => 'info'
                                                            ][$grade->status] ?? 'secondary';
                                                            ?>
                                                            <span class="badge bg-<?= $statusClass ?>"><?= $grade->status ?></span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <!-- Year Summary -->
                        <?php
                        $yearGrades = [];
                        foreach ($yearData['semesters'] as $semesterData) {
                            $yearGrades = array_merge($yearGrades, $semesterData['grades']);
                        }
                        $total = 0;
                        $count = 0;
                        $approved = 0;
                        $failed = 0;
                        foreach ($yearGrades as $grade) {
                            if ($grade->final_score) {
                                $total += $grade->final_score;
                                $count++;
                            }
                            if ($grade->status == 'Aprovado') $approved++;
                            if ($grade->status == 'Reprovado') $failed++;
                        }
                        $yearAverage = $count > 0 ? round($total / $count, 1) : 0;
                        ?>
                        <div class="ms-3 alert alert-info py-2">
                            <strong>Resumo do Ano:</strong> 
                            Média: <?= number_format($yearAverage, 1) ?> | 
                            Aprovado: <?= $approved ?> | 
                            Reprovado: <?= $failed ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted ms-3">Nenhum resultado registado para este ano letivo.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <!-- Overall Summary -->
            <?php
            $allGrades = [];
            foreach ($transcript as $yearData) {
                foreach ($yearData['semesters'] as $semesterData) {
                    $allGrades = array_merge($allGrades, $semesterData['grades']);
                }
            }
            $total = 0;
            $count = 0;
            foreach ($allGrades as $grade) {
                if ($grade->final_score) {
                    $total += $grade->final_score;
                    $count++;
                }
            }
            $overallAverage = $count > 0 ? round($total / $count, 1) : 0;
            ?>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Total de Disciplinas Cursadas:</strong> <?= count($allGrades) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Média Geral do Histórico:</strong> <?= number_format($overallAverage, 1) ?></p>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhum histórico escolar encontrado para este aluno.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="card-footer text-muted">
        <small>Histórico gerado em <?= date('d/m/Y H:i') ?></small>
    </div>
</div>

<style media="print">
    .btn, .page-header .btn, .breadcrumb {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
</style>

<?= $this->endSection() ?>