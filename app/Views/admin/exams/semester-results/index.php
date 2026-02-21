<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">
                <i class="fas fa-chart-pie me-1"></i>
                Visualize os resultados semestrais dos alunos
            </p>
        </div>
        <div>
            <a href="<?= site_url('admin/exams') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active">Resultados Semestrais</li>
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
            Filtrar Resultados
        </h5>
    </div>
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
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
            
            <div class="col-md-4">
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
            
            <div class="col-md-4 d-flex align-items-end">
                <a href="<?= site_url('admin/semester-results') ?>" class="btn btn-secondary w-100">
                    <i class="fas fa-undo me-1"></i> Limpar Filtros
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Total de Resultados</h6>
                        <h2 class="mb-0"><?= $totalResults ?></h2>
                    </div>
                    <div class="fs-1">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Resultados deste Semestre</h6>
                        <h2 class="mb-0"><?= $resultsThisSemester ?></h2>
                    </div>
                    <div class="fs-1">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resultados Recentes -->
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-history me-2 text-primary"></i>
            Resultados Recentes
        </h5>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($recentResults)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 ps-3">Aluno</th>
                            <th class="border-0 py-3">Nº Matrícula</th>
                            <th class="border-0 py-3">Turma</th>
                            <th class="border-0 py-3">Semestre</th>
                            <th class="border-0 py-3 text-center">Média Final</th>
                            <th class="border-0 py-3 text-center">Status</th>
                            <th class="border-0 py-3 text-center pe-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentResults as $result): ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-semibold"><?= $result->first_name . ' ' . $result->last_name ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= $result->student_number ?></span>
                                </td>
                                <td><?= $result->class_name ?></td>
                                <td><?= $result->semester_name ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $result->overall_average >= 10 ? 'success' : 'danger' ?> p-2 fs-6">
                                        <?= number_format($result->overall_average, 1) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $statusClass = [
                                        'Aprovado' => 'success',
                                        'Recurso' => 'warning',
                                        'Reprovado' => 'danger'
                                    ][$result->status] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>">
                                        <?= $result->status ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('admin/semester-results/student/' . $result->enrollment_id . '/' . $result->semester_id) ?>" 
                                           class="btn btn-outline-primary" title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($result->overall_average > 0): ?>
                                            <a href="<?= site_url('admin/semester-results/export/' . $result->enrollment_id . '/' . $result->semester_id . '?type=pdf') ?>" 
                                               class="btn btn-outline-success" title="Exportar PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Ações em massa -->
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-end gap-2">
                    <?php if ($selectedClass && $selectedSemester): ?>
                        <a href="<?= site_url('admin/semester-results/class/' . $selectedClass . '/' . $selectedSemester) ?>" 
                           class="btn btn-primary">
                            <i class="fas fa-users me-2"></i>Ver Resultados da Turma
                        </a>
                        <a href="<?= site_url('admin/semester-results/export/' . $selectedClass . '/' . $selectedSemester . '?type=excel') ?>" 
                           class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i>Exportar Excel
                        </a>
                        <a href="<?= site_url('admin/semester-results/export/' . $selectedClass . '/' . $selectedSemester . '?type=pdf') ?>" 
                           class="btn btn-danger">
                            <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                        </a>
                        <a href="<?= site_url('admin/semester-results/generate-report-cards/' . $selectedClass . '/' . $selectedSemester) ?>" 
                           class="btn btn-warning">
                            <i class="fas fa-print me-2"></i>Gerar Pautas
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-chart-pie fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum resultado encontrado</h5>
                <p class="text-muted mb-0">
                    Os resultados semestrais aparecerão aqui após o cálculo das médias.
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