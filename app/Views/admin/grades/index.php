<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">Visualize todas as notas e avaliações por turma</p>
        </div>
        <div>
            <a href="<?= site_url('admin/grades/report') ?>" class="btn btn-info me-2">
                <i class="fas fa-chart-bar me-1"></i> Relatórios
            </a>
            <a href="<?= site_url('admin/grades/export') ?>" class="btn btn-success">
                <i class="fas fa-file-excel me-1"></i> Exportar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">
                <i class="fas fa-home me-1"></i>Dashboard
            </a></li>
            <li class="breadcrumb-item active" aria-current="page">Notas e Avaliações</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filtros</h5>
    </div>
    <div class="card-body">
        <form method="get" id="filterForm" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Ano Letivo</label>
                <select class="form-select" name="academic_year">
                    <option value="">Todos os anos</option>
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= $year->id ?>" <?= ($selectedYear ?? '') == $year->id ? 'selected' : '' ?>>
                                <?= $year->year_name ?> <?= !empty($year->is_current) && $year->is_current ? '(Atual)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-semibold">Turma</label>
                <select class="form-select" name="class" id="classSelect">
                    <option value="">Selecione uma turma...</option>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class->id ?>" <?= ($selectedClass ?? '') == $class->id ? 'selected' : '' ?>>
                                <?= $class->class_name ?> (<?= $class->class_code ?>) - <?= $class->level_name ?> - <?= $class->year_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($class) && $class): ?>
    <!-- Informações da Turma -->
    <div class="alert alert-info d-flex align-items-center mb-4">
        <i class="fas fa-school fa-2x me-3"></i>
        <div>
            <h5 class="mb-1"><?= $class->class_name ?> (<?= $class->class_code ?>)</h5>
            <p class="mb-0">
                <strong>Professor:</strong> <?= $class->teacher_first_name ?? 'Não atribuído' ?> <?= $class->teacher_last_name ?? '' ?> |
                <strong>Turno:</strong> <?= $class->class_shift ?> |
                <strong>Sala:</strong> <?= $class->class_room ?: 'Não definida' ?>
            </p>
        </div>
    </div>
    
    <!-- Cards de Resumo -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Total Alunos</h6>
                    <h3 class="mb-0"><?= count($students ?? []) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Disciplinas</h6>
                    <h3 class="mb-0"><?= count($disciplines ?? []) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Semestre</h6>
                    <h3 class="mb-0"><?= $currentSemester->semester_name ?? 'N/A' ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Média da Turma</h6>
                    <h3 class="mb-0">
                        <?php
                        $totalMedia = 0;
                        $count = 0;
                        if (!empty($students)) {
                            foreach ($students as $student) {
                                foreach ($disciplines ?? [] as $disc) {
                                    $notas = array_filter([
                                        $student->grades[$disc->id]['ac1'] ?? null,
                                        $student->grades[$disc->id]['ac2'] ?? null,
                                        $student->grades[$disc->id]['ac3'] ?? null
                                    ]);
                                    if (!empty($notas)) {
                                        $totalMedia += array_sum($notas) / count($notas);
                                        $count++;
                                    }
                                }
                            }
                        }
                        echo $count > 0 ? number_format($totalMedia / $count, 1) : '0';
                        ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabela de Notas -->
    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-table me-2 text-primary"></i>Notas dos Alunos</h5>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="expandAll()">
                        <i class="fas fa-expand-alt"></i> Expandir
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i> Exportar
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">#</th>
                            <th>Aluno</th>
                            <th>Nº Matrícula</th>
                            <?php if (!empty($disciplines)): ?>
                                <?php foreach ($disciplines as $discipline): ?>
                                    <th colspan="3" class="text-center bg-primary">
                                        <?= $discipline->discipline_name ?>
                                        <small class="d-block text-white-50"><?= $discipline->discipline_code ?></small>
                                    </th>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <th width="100">Média</th>
                        </tr>
                        <tr class="table-secondary">
                            <th></th>
                            <th></th>
                            <th></th>
                            <?php if (!empty($disciplines)): ?>
                                <?php foreach ($disciplines as $discipline): ?>
                                    <th class="text-center">AC1</th>
                                    <th class="text-center">AC2</th>
                                    <th class="text-center">AC3</th>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($students)): ?>
                            <?php $i = 1; ?>
                            <?php foreach ($students as $student): ?>
                                <?php 
                                $mediaAluno = 0;
                                $totalNotas = 0;
                                ?>
                                <tr>
                                    <td class="text-center"><?= $i++ ?></td>
                                    <td>
                                        <strong><?= $student->first_name ?> <?= $student->last_name ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= $student->student_number ?></span>
                                    </td>
                                    
                                    <?php if (!empty($disciplines)): ?>
                                        <?php foreach ($disciplines as $discipline): ?>
                                            <?php 
                                            $ac1 = $student->grades[$discipline->id]['ac1'] ?? null;
                                            $ac2 = $student->grades[$discipline->id]['ac2'] ?? null;
                                            $ac3 = $student->grades[$discipline->id]['ac3'] ?? null;
                                            
                                            $notasDisciplina = array_filter([$ac1, $ac2, $ac3]);
                                            if (!empty($notasDisciplina)) {
                                                $mediaDisciplina = array_sum($notasDisciplina) / count($notasDisciplina);
                                                $mediaAluno += $mediaDisciplina;
                                                $totalNotas++;
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
                                    <?php endif; ?>
                                    
                                    <td class="text-center">
                                        <?php if ($totalNotas > 0): ?>
                                            <?php $mediaFinal = $mediaAluno / $totalNotas; ?>
                                            <span class="badge bg-<?= $mediaFinal >= 10 ? 'success' : 'warning' ?> p-2">
                                                <?= number_format($mediaFinal, 1) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= 4 + (count($disciplines ?? []) * 3) ?>" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Nenhum aluno encontrado nesta turma</h5>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
<?php else: ?>
    <!-- Mensagem quando nenhuma turma é selecionada -->
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Selecione uma turma para visualizar as notas</h4>
            <p class="text-muted mb-0">Use os filtros acima para escolher uma turma específica</p>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function expandAll() {
    // Função para expandir/colapsar (se necessário)
    console.log('Expandir tudo');
}

function exportToExcel() {
    const classId = $('#classSelect').val();
    if (classId) {
        window.location.href = '<?= site_url('admin/grades/export/') ?>' + classId;
    } else {
        alert('Selecione uma turma primeiro');
    }
}

// Auto-submit do filtro
$('#classSelect').change(function() {
    $(this).closest('form').submit();
});

// Tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
</script>

<style>
.bg-light-success {
    background-color: rgba(40, 167, 69, 0.1);
}
.table th {
    vertical-align: middle;
}
</style>
<?= $this->endSection() ?>