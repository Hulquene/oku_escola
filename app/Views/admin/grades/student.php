<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">Histórico completo de notas do aluno</p>
        </div>
        <div>
            <a href="<?= site_url('admin/students/view/' . $student->id) ?>" class="btn btn-info me-2">
                <i class="fas fa-user-graduate me-1"></i> Ver Aluno
            </a>
            <a href="<?= site_url('admin/grades/report?student=' . $student->id) ?>" class="btn btn-primary me-2">
                <i class="fas fa-file-pdf me-1"></i> Boletim
            </a>
            <a href="<?= site_url('admin/grades') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/grades') ?>">Notas</a></li>
            <li class="breadcrumb-item active">Histórico do Aluno</li>
        </ol>
    </nav>
</div>

<!-- Informações do Aluno -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <?php if ($student->photo): ?>
                        <img src="<?= base_url('uploads/students/' . $student->photo) ?>" 
                             alt="Foto" class="rounded-circle me-3" style="width: 80px; height: 80px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-3"
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            <?= strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    
                    <div>
                        <h3 class="mb-1"><?= $student->first_name ?> <?= $student->last_name ?></h3>
                        <p class="mb-1">
                            <span class="badge bg-info">Nº: <?= $student->student_number ?></span>
                            <span class="badge bg-secondary ms-2"><?= $student->email ?></span>
                        </p>
                        <p class="mb-0 text-muted">
                            <i class="fas fa-phone me-1"></i> <?= $student->phone ?: 'Não informado' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Resumo Acadêmico</h5>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Total Matrículas</small>
                        <h4><?= count($enrollments) ?></h4>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Média Geral</small>
                        <h4>
                            <?php
                            $totalMediaGeral = 0;
                            $totalDisciplinas = 0;
                            foreach ($enrollments as $enrollment) {
                                if (isset($enrollment->grades)) {
                                    foreach ($enrollment->grades as $disciplina) {
                                        foreach ($disciplina as $semestreNotas) {
                                            $notas = array_filter(array_column($semestreNotas, 'score'));
                                            if (!empty($notas)) {
                                                $totalMediaGeral += array_sum($notas) / count($notas);
                                                $totalDisciplinas++;
                                            }
                                        }
                                    }
                                }
                            }
                            echo $totalDisciplinas > 0 ? number_format($totalMediaGeral / $totalDisciplinas, 1) : '-';
                            ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Histórico por Ano -->
<?php if (!empty($enrollments)): ?>
    <?php foreach ($enrollments as $enrollment): ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar me-2"></i>
                        <?= $enrollment->year_name ?> - <?= $enrollment->level_name ?>
                        <span class="badge bg-light text-primary ms-2"><?= $enrollment->class_name ?></span>
                    </h5>
                    <span class="badge bg-<?= $enrollment->status == 'Ativo' ? 'success' : 'secondary' ?>">
                        <?= $enrollment->status ?>
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Disciplina</th>
                                <?php
                                $semestres = [];
                                if (isset($enrollment->grades)) {
                                    foreach (array_keys($enrollment->grades) as $discId) {
                                        foreach (array_keys($enrollment->grades[$discId]) as $semId) {
                                            $semestres[$semId] = true;
                                        }
                                    }
                                }
                                ksort($semestres);
                                
                                $semesterNames = [];
                                $semesterModel = new \App\Models\SemesterModel();
                                foreach (array_keys($semestres) as $semId) {
                                    $sem = $semesterModel->find($semId);
                                    $semesterNames[$semId] = $sem ? $sem->semester_name : 'Semestre ' . $semId;
                                }
                                ?>
                                <?php foreach ($semesterNames as $semName): ?>
                                    <th colspan="3" class="text-center"><?= $semName ?></th>
                                <?php endforeach; ?>
                                <th class="text-center">Média</th>
                            </tr>
                            <tr class="table-secondary">
                                <th></th>
                                <?php foreach ($semesterNames as $semName): ?>
                                    <th class="text-center">AC1</th>
                                    <th class="text-center">AC2</th>
                                    <th class="text-center">AC3</th>
                                <?php endforeach; ?>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $disciplines = [];
                            if (isset($enrollment->grades)) {
                                foreach (array_keys($enrollment->grades) as $discId) {
                                    $discModel = new \App\Models\DisciplineModel();
                                    $disc = $discModel->find($discId);
                                    $disciplines[$discId] = $disc ? $disc->discipline_name : 'Disciplina ' . $discId;
                                }
                            }
                            ?>
                            
                            <?php foreach ($disciplines as $discId => $discName): ?>
                                <tr>
                                    <td><strong><?= $discName ?></strong></td>
                                    
                                    <?php
                                    $totalDisciplina = 0;
                                    $countDisciplina = 0;
                                    ?>
                                    
                                    <?php foreach (array_keys($semesterNames) as $semId): ?>
                                        <?php
                                        $ac1 = $enrollment->grades[$discId][$semId]['AC1']->score ?? null;
                                        $ac2 = $enrollment->grades[$discId][$semId]['AC2']->score ?? null;
                                        $ac3 = $enrollment->grades[$discId][$semId]['AC3']->score ?? null;
                                        
                                        $notasSem = array_filter([$ac1, $ac2, $ac3]);
                                        if (!empty($notasSem)) {
                                            $totalDisciplina += array_sum($notasSem) / count($notasSem);
                                            $countDisciplina++;
                                        }
                                        ?>
                                        
                                        <td class="text-center <?= $ac1 ? 'bg-light-success' : '' ?>">
                                            <?php if ($ac1): ?>
                                                <span class="fw-bold <?= $ac1 >= 10 ? 'text-success' : 'text-danger' ?>">
                                                    <?= number_format($ac1, 1) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center <?= $ac2 ? 'bg-light-success' : '' ?>">
                                            <?php if ($ac2): ?>
                                                <span class="fw-bold <?= $ac2 >= 10 ? 'text-success' : 'text-danger' ?>">
                                                    <?= number_format($ac2, 1) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center <?= $ac3 ? 'bg-light-success' : '' ?>">
                                            <?php if ($ac3): ?>
                                                <span class="fw-bold <?= $ac3 >= 10 ? 'text-success' : 'text-danger' ?>">
                                                    <?= number_format($ac3, 1) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                    
                                    <td class="text-center">
                                        <?php if ($countDisciplina > 0): ?>
                                            <?php $media = $totalDisciplina / $countDisciplina; ?>
                                            <span class="badge bg-<?= $media >= 10 ? 'success' : 'warning' ?> p-2">
                                                <?= number_format($media, 1) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
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
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-history fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Nenhum histórico encontrado</h4>
            <p class="text-muted">Este aluno ainda não possui matrículas no sistema</p>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>