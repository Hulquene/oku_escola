<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">
                <i class="fas fa-chart-line me-1"></i>
                Visualize as médias disciplinares dos alunos
            </p>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active">Médias Disciplinares</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-filter me-2 text-primary"></i>
            Filtrar Médias
        </h5>
    </div>
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Semestre</label>
                <select class="form-select" name="semester" onchange="this.form.submit()">
                    <option value="">Todos os semestres</option>
                    <?php foreach ($semesters as $semester): ?>
                        <option value="<?= $semester->id ?>" <?= $selectedSemester == $semester->id ? 'selected' : '' ?>>
                            <?= $semester->semester_name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Turma</label>
                <select class="form-select" name="class_id" onchange="this.form.submit()">
                    <option value="">Todas as turmas</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class->id ?>" <?= $selectedClass == $class->id ? 'selected' : '' ?>>
                            <?= $class->class_name ?> (<?= $class->class_code ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Disciplina</label>
                <select class="form-select" name="discipline_id" onchange="this.form.submit()">
                    <option value="">Todas as disciplinas</option>
                    <?php foreach ($disciplines as $discipline): ?>
                        <option value="<?= $discipline->id ?>" <?= $selectedDiscipline == $discipline->id ? 'selected' : '' ?>>
                            <?= $discipline->discipline_name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <a href="<?= site_url('admin/discipline-averages') ?>" class="btn btn-secondary w-100">
                    <i class="fas fa-undo me-1"></i> Limpar Filtros
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Total de Médias</h6>
                        <h2 class="mb-0"><?= $totalAverages ?></h2>
                    </div>
                    <div class="fs-1">
                        <i class="fas fa-calculator"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Médias deste Semestre</h6>
                        <h2 class="mb-0"><?= $averagesThisSemester ?></h2>
                    </div>
                    <div class="fs-1">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Disciplinas Ativas</h6>
                        <h2 class="mb-0"><?= count($disciplines) ?></h2>
                    </div>
                    <div class="fs-1">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Médias Recentes -->
<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-history me-2 text-primary"></i>
            Médias Recentes
        </h5>
        <div class="btn-group">
            <a href="<?= site_url('admin/discipline-averages/class/' . ($selectedClass ?: 0) . '/' . ($selectedSemester ?: 0)) ?>" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-users me-1"></i> Ver por Turma
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($recentAverages)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 ps-3">Aluno</th>
                            <th class="border-0 py-3">Nº Matrícula</th>
                            <th class="border-0 py-3">Turma</th>
                            <th class="border-0 py-3">Disciplina</th>
                            <th class="border-0 py-3 text-center">Média Final</th>
                            <th class="border-0 py-3 text-center">Status</th>
                            <th class="border-0 py-3 text-center pe-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentAverages as $avg): ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-semibold"><?= $avg->first_name . ' ' . $avg->last_name ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= $avg->student_number ?></span>
                                </td>
                                <td><?= $avg->class_name ?></td>
                                <td><?= $avg->discipline_name ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $avg->final_score >= 10 ? 'success' : 'danger' ?> p-2 fs-6">
                                        <?= number_format($avg->final_score, 1) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $statusClass = [
                                        'Aprovado' => 'success',
                                        'Recurso' => 'warning',
                                        'Reprovado' => 'danger'
                                    ][$avg->status] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>">
                                        <?= $avg->status ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('admin/discipline-averages/student/' . $avg->enrollment_id . '/' . $avg->semester_id) ?>" 
                                           class="btn btn-outline-primary" title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma média encontrada</h5>
                <p class="text-muted mb-0">
                    As médias disciplinares aparecerão aqui após o registo de notas.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.table th {
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
}

.btn-group .btn:last-child {
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
}
</style>

<?= $this->endSection() ?>