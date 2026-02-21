<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Alunos com notas entre 7 e 9.9 valores
            </p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#generateModal">
                <i class="fas fa-calendar-plus me-1"></i> Gerar Exames de Recurso
            </button>
            <a href="<?= site_url('admin/exams/appeals/export/' . $selectedSemester) ?>?type=excel" 
               class="btn btn-success">
                <i class="fas fa-file-excel me-1"></i> Exportar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active">Recursos</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Semestre</label>
                <select class="form-select" name="semester" onchange="this.form.submit()">
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= $sem->id ?>" <?= $selectedSemester == $sem->id ? 'selected' : '' ?>>
                            <?= $sem->semester_name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Turma</label>
                <select class="form-select" name="class_id" onchange="this.form.submit()">
                    <option value="">Todas as turmas</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class->id ?>" <?= $selectedClass == $class->id ? 'selected' : '' ?>>
                            <?= $class->class_name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <a href="<?= site_url('admin/exams/appeals') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo me-1"></i> Limpar Filtros
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                            <i class="fas fa-users text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Alunos em Recurso</h6>
                        <h3 class="mb-0"><?= $stats['total_students'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 p-2 rounded">
                            <i class="fas fa-book-open text-info"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Disciplinas em Recurso</h6>
                        <h3 class="mb-0"><?= $stats['total_disciplines'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                            <i class="fas fa-school text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Turmas Envolvidas</h6>
                        <h3 class="mb-0"><?= $stats['total_classes'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Alunos por Turma -->
<?php if (!empty($groupedAppeals)): ?>
    <?php foreach ($groupedAppeals as $group): ?>
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-users me-2 text-primary"></i>
                    Turma: <?= $group['class_name'] ?>
                    <span class="badge bg-warning text-dark ms-3">
                        <?= count($group['students']) ?> disciplina(s)
                    </span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 py-3 ps-3">Nº Aluno</th>
                                <th class="border-0 py-3">Nome do Aluno</th>
                                <th class="border-0 py-3">Disciplina</th>
                                <th class="border-0 py-3 text-center">Nota Atual</th>
                                <th class="border-0 py-3 text-center">Status</th>
                                <th class="border-0 py-3 text-center pe-3">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($group['students'] as $appeal): ?>
                                <tr>
                                    <td class="ps-3">
                                        <span class="badge bg-info bg-opacity-10 text-info p-2">
                                            <?= $appeal->student_number ?>
                                        </span>
                                    </td>
                                    <td class="fw-semibold"><?= $appeal->first_name ?> <?= $appeal->last_name ?></td>
                                    <td>
                                        <?= $appeal->discipline_name ?>
                                        <br>
                                        <small class="text-muted"><?= $appeal->discipline_code ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-warning">
                                            <?= number_format($appeal->final_score, 1) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning p-2">Recurso</span>
                                    </td>
                                    <td class="text-center pe-3">
                                        <a href="<?= site_url('admin/students/view/' . $appeal->student_id) ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Ver aluno">
                                            <i class="fas fa-eye"></i>
                                        </a>
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
    <div class="empty-state">
        <i class="fas fa-check-circle empty-state-icon text-success"></i>
        <h5 class="empty-state-title">Nenhum aluno em situação de recurso</h5>
        <p class="empty-state-text">
            Não existem alunos com notas entre 7 e 9.9 valores neste semestre.
        </p>
    </div>
<?php endif; ?>

<!-- Modal de Geração de Exames -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark">
                    <i class="fas fa-calendar-plus me-2"></i>
                    Gerar Exames de Recurso
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('admin/exams/appeals/generate') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Semestre <span class="text-danger">*</span></label>
                        <select class="form-select" name="semester_id" required>
                            <option value="">Selecione</option>
                            <?php foreach ($semesters as $sem): ?>
                                <option value="<?= $sem->id ?>" <?= $selectedSemester == $sem->id ? 'selected' : '' ?>>
                                    <?= $sem->semester_name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Turmas (opcional)</label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="selectAllClasses">
                                <label class="form-check-label fw-bold" for="selectAllClasses">
                                    Selecionar Todas
                                </label>
                            </div>
                            <?php foreach ($classes as $class): ?>
                                <div class="form-check ms-3">
                                    <input class="form-check-input class-checkbox" 
                                           type="checkbox" 
                                           name="class_ids[]" 
                                           value="<?= $class->id ?>" 
                                           id="class_<?= $class->id ?>">
                                    <label class="form-check-label" for="class_<?= $class->id ?>">
                                        <?= $class->class_name ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="text-muted">Deixe em branco para gerar para todas as turmas</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Data do Exame <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="exam_date" 
                               min="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-calendar-plus me-2"></i>Gerar Exames
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: #fff;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.empty-state-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.empty-state-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.empty-state-text {
    color: #6c757d;
    margin-bottom: 1.5rem;
}
</style>

<script>
// Select all classes
document.getElementById('selectAllClasses').addEventListener('change', function(e) {
    document.querySelectorAll('.class-checkbox').forEach(cb => {
        cb.checked = e.target.checked;
    });
});
</script>

<?= $this->endSection() ?>