<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb e Header -->
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('admin/academic-records') ?>" class="text-muted">Pautas</a></li>
                <li class="breadcrumb-item active fw-semibold"><?= $class->class_name ?></li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-semibold mb-1">Pauta da Turma</h2>
                <p class="text-muted mb-0">
                    <i class="fas fa-building me-2"></i><?= $class->class_name ?> • <?= $class->year_name ?>
                </p>
            </div>
            <div class="d-flex gap-2">
                <?php if (!empty($disciplines)): ?>
                    <button class="btn btn-light" onclick="exportPauta()">
                        <i class="fas fa-download me-2"></i>Exportar
                    </button>
                <?php endif; ?>
                <button class="btn btn-light" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Card de Informações da Turma -->
<div class="card mb-4">
    <div class="card-body p-3">
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="fas fa-school fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h4 class="fw-semibold mb-2"><?= $class->class_name ?></h4>
                        <div class="d-flex flex-wrap gap-3">
                            <span class="badge bg-light text-dark p-2">
                                <i class="fas fa-layer-group me-1 text-primary"></i> 
                                <?= $class->level_name ?? 'Nível não definido' ?>
                            </span>
                            <?php if (isset($class->course_name)): ?>
                                <span class="badge bg-primary p-2">
                                    <i class="fas fa-graduation-cap me-1"></i> 
                                    <?= $class->course_name ?>
                                </span>
                            <?php endif; ?>
                            <span class="badge bg-info p-2">
                                <i class="fas fa-clock me-1"></i> 
                                <?= $class->class_shift ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="border-start ps-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Semestre:</span>
                        <?php if (!empty($semesters)): ?>
                            <select class="form-select form-select-sm w-auto border-0 bg-light" 
                                    id="semester" onchange="changeSemester(this.value)">
                                <option value="">Todos os semestres</option>
                                <?php foreach ($semesters as $sem): ?>
                                    <option value="<?= $sem->id ?>" <?= $selectedSemester == $sem->id ? 'selected' : '' ?>>
                                        <?= $sem->semester_name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <span class="text-muted">Nenhum semestre disponível</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Professor:</span>
                        <span class="fw-semibold">
                            <?= $class->teacher_first_name ?? 'Não atribuído' ?> 
                            <?= $class->teacher_last_name ?? '' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cards de Métricas -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total de Alunos</h6>
                        <h3 class="mb-0 fw-semibold"><?= $stats['total'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 p-2 rounded">
                            <i class="fas fa-check text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Aprovados</h6>
                        <h3 class="mb-0 fw-semibold"><?= $stats['aprovados'] ?? 0 ?></h3>
                        <small class="text-success"><?= $stats['aprovacao'] ?? 0 ?>%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                            <i class="fas fa-exclamation text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Recurso</h6>
                        <h3 class="mb-0 fw-semibold"><?= $stats['recurso'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-danger bg-opacity-10 p-2 rounded">
                            <i class="fas fa-times text-danger"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Reprovados</h6>
                        <h3 class="mb-0 fw-semibold"><?= $stats['reprovados'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Resultados -->
<div class="card">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-table me-2 text-primary"></i>
                Resultados Acadêmicos
                <?php if (empty($disciplines)): ?>
                    <span class="badge bg-warning text-dark ms-3">Nenhuma disciplina cadastrada para este semestre</span>
                <?php endif; ?>
            </h5>
            <?php if (!empty($disciplines)): ?>
                <div style="width: 300px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 ps-0" 
                               id="searchInput" placeholder="Buscar aluno..." onkeyup="filterTable()">
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card-body p-0">
        <?php if (!empty($disciplines)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="resultsTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 ps-3" width="50">#</th>
                            <th class="border-0 py-3">Aluno</th>
                            <th class="border-0 py-3">Matrícula</th>
                            <?php foreach ($disciplines as $disc): ?>
                                <th class="border-0 py-3 text-center" colspan="3">
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold"><?= $disc->discipline_name ?></span>
                                        <small class="text-muted fw-normal"><?= $disc->discipline_code ?></small>
                                    </div>
                                </th>
                            <?php endforeach; ?>
                            <th class="border-0 py-3 text-center">Média</th>
                            <th class="border-0 py-3 text-center pe-3">Resultado</th>
                        </tr>
                        <tr class="bg-light border-top">
                            <th class="border-0 ps-3"></th>
                            <th class="border-0"></th>
                            <th class="border-0"></th>
                            <?php foreach ($disciplines as $disc): ?>
                                <th class="border-0 text-center fw-normal text-muted">AC</th>
                                <th class="border-0 text-center fw-normal text-muted">EX</th>
                                <th class="border-0 text-center fw-normal text-muted">MF</th>
                            <?php endforeach; ?>
                            <th class="border-0"></th>
                            <th class="border-0 pe-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($students)): ?>
                            <?php $i = 1; ?>
                            <?php foreach ($students as $student): ?>
                                <tr class="student-row align-middle">
                                    <td class="ps-3 text-muted"><?= $i++ ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 32px; height: 32px;">
                                                <span class="fw-semibold text-primary">
                                                    <?= strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) ?>
                                                </span>
                                            </div>
                                            <div>
                                                <span class="fw-medium student-name"><?= $student->first_name ?> <?= $student->last_name ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted"><?= $student->student_number ?></span>
                                    </td>
                                    
                                    <?php foreach ($disciplines as $disc): ?>
                                        <?php 
                                        $grade = $student->grades[$disc->id] ?? null;
                                        $hasGrade = $grade && ($grade['ac_score'] > 0 || $grade['exam_score'] > 0 || $grade['final_score'] > 0);
                                        ?>
                                        <td class="text-center">
                                            <?php if ($hasGrade && $grade['ac_score'] > 0): ?>
                                                <span class="fw-medium"><?= number_format($grade['ac_score'], 1) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($hasGrade && $grade['exam_score'] > 0): ?>
                                                <span class="fw-medium"><?= number_format($grade['exam_score'], 1) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($hasGrade && $grade['final_score'] > 0): ?>
                                                <span class="fw-bold <?= $grade['final_score'] >= 10 ? 'text-success' : 'text-warning' ?>">
                                                    <?= number_format($grade['final_score'], 1) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                    
                                    <td class="text-center">
                                        <?php if ($student->overall_average > 0): ?>
                                            <span class="fw-bold <?= $student->overall_average >= 10 ? 'text-success' : 'text-warning' ?>">
                                                <?= number_format($student->overall_average, 1) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-3">
                                        <?php
                                        $result = $student->final_result ?? 'Em Andamento';
                                        $resultClass = [
                                            'Aprovado' => 'success',
                                            'Recurso' => 'warning',
                                            'Reprovado' => 'danger',
                                            'Em Andamento' => 'secondary'
                                        ][$result] ?? 'secondary';
                                        
                                        $resultIcon = [
                                            'Aprovado' => 'fa-check',
                                            'Recurso' => 'fa-exclamation',
                                            'Reprovado' => 'fa-times',
                                            'Em Andamento' => 'fa-clock'
                                        ][$result] ?? 'fa-circle';
                                        ?>
                                        <span class="badge bg-<?= $resultClass ?>-subtle text-<?= $resultClass ?> px-3 py-2">
                                            <i class="fas <?= $resultIcon ?> me-1"></i>
                                            <?= $result ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= 4 + (count($disciplines) * 3) ?>" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Nenhum aluno ativo nesta turma</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Rodapé da Tabela -->
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-3">
                        <span class="text-muted">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            Aprovados: <?= $stats['aprovados'] ?? 0 ?>
                        </span>
                        <span class="text-muted">
                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                            Recurso: <?= $stats['recurso'] ?? 0 ?>
                        </span>
                        <span class="text-muted">
                            <i class="fas fa-times-circle text-danger me-1"></i>
                            Reprovados: <?= $stats['reprovados'] ?? 0 ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-muted">
                            Média de aprovação: 
                            <span class="fw-semibold <?= ($stats['aprovacao'] ?? 0) >= 50 ? 'text-success' : 'text-warning' ?>">
                                <?= $stats['aprovacao'] ?? 0 ?>%
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma disciplina cadastrada</h5>
                <p class="text-muted mb-3">
                    Não existem disciplinas associadas a esta turma para o semestre selecionado.
                </p>
                <a href="<?= site_url('admin/classes/class-subjects/assign?class_id=' . $class->id) ?>" 
                   class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Associar Disciplinas
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Botão Finalizar (se aplicável) -->
<?php
$isFinalized = true;
foreach ($students as $student) {
    if (empty($student->final_result) || $student->final_result == 'Em Andamento') {
        $isFinalized = false;
        break;
    }
}
?>

<?php if (!$isFinalized && !empty($students) && !empty($disciplines) && (isStaff() || isAdmin())): ?>
<div class="row mt-4">
    <div class="col-12 text-end">
        <button type="button" class="btn btn-warning px-4" data-bs-toggle="modal" data-bs-target="#finalizeModal">
            <i class="fas fa-check-double me-2"></i> Finalizar Pauta
        </button>
    </div>
</div>
<?php endif; ?>

<!-- Modal de Finalização -->
<div class="modal fade" id="finalizeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Confirmar Finalização
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Deseja finalizar a pauta da turma <span class="fw-semibold"><?= $class->class_name ?></span> para o semestre selecionado?</p>
                
                <div class="bg-light p-3 rounded">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total de Alunos:</span>
                        <span class="fw-semibold"><?= $stats['total'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Aprovados:</span>
                        <span class="fw-semibold text-success"><?= $stats['aprovados'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Recurso:</span>
                        <span class="fw-semibold text-warning"><?= $stats['recurso'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Reprovados:</span>
                        <span class="fw-semibold text-danger"><?= $stats['reprovados'] ?></span>
                    </div>
                </div>
                
                <p class="mt-3 mb-0 small text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Após a finalização, as notas não poderão ser alteradas e os históricos serão gerados.
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <form action="<?= site_url('admin/academic-records/finalize-year') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="class_id" value="<?= $class->id ?>">
                    <input type="hidden" name="semester_id" value="<?= $selectedSemester ?? '' ?>">
                    <button type="submit" class="btn btn-warning px-4">
                        <i class="fas fa-check-double me-2"></i>Confirmar Finalização
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.table thead th {
    background-color: #f8f9fa;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-weight: 500;
    font-size: 0.875rem;
}

.bg-success-subtle {
    background-color: #d1e7dd;
}

.bg-warning-subtle {
    background-color: #fff3cd;
}

.bg-danger-subtle {
    background-color: #f8d7da;
}

.card {
    border: 1px solid rgba(0,0,0,.125);
    box-shadow: 0 2px 4px rgba(0,0,0,.02);
}

@media (max-width: 768px) {
    .table {
        font-size: 0.875rem;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
    }
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function changeSemester(semesterId) {
    if (semesterId) {
        window.location.href = '<?= site_url('admin/academic-records/class/' . $class->id) ?>?semester=' + semesterId;
    } else {
        window.location.href = '<?= site_url('admin/academic-records/class/' . $class->id) ?>';
    }
}

function filterTable() {
    const input = document.getElementById('searchInput');
    if (!input) return;
    
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('.student-row');
    
    rows.forEach(row => {
        const nameElement = row.querySelector('.student-name');
        if (nameElement) {
            const text = nameElement.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        }
    });
}

function exportPauta() {
    const classId = '<?= $class->id ?>';
    const semesterId = '<?= $selectedSemester ?? '' ?>';
    window.location.href = '<?= site_url('admin/academic-records/export?class=') ?>' + classId + '&semester=' + semesterId;
}

// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
<?= $this->endSection() ?>