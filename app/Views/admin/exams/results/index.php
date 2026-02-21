<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">
                <i class="fas fa-chart-line me-1"></i>
                Visualize todos os resultados de exames
            </p>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active">Resultados</li>
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
            <div class="col-md-3">
                <label class="form-label fw-semibold">Exame</label>
                <select class="form-select" name="exam" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <?php foreach ($exams as $exam): ?>
                        <option value="<?= $exam->id ?>" <?= $selectedExam == $exam->id ? 'selected' : '' ?>>
                            <?= date('d/m/Y', strtotime($exam->exam_date)) ?> - <?= $exam->discipline_name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Turma</label>
                <select class="form-select" name="class" onchange="this.form.submit()">
                    <option value="">Todas</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class->id ?>" <?= $selectedClass == $class->id ? 'selected' : '' ?>>
                            <?= $class->class_name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Disciplina</label>
                <select class="form-select" name="discipline" onchange="this.form.submit()">
                    <option value="">Todas</option>
                    <?php foreach ($disciplines as $disc): ?>
                        <option value="<?= $disc->id ?>" <?= $selectedDiscipline == $disc->id ? 'selected' : '' ?>>
                            <?= $disc->discipline_name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Semestre</label>
                <select class="form-select" name="semester" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= $sem->id ?>" <?= $selectedSemester == $sem->id ? 'selected' : '' ?>>
                            <?= $sem->semester_name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Tabela de Resultados -->
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-table me-2 text-primary"></i>
            Lista de Resultados
        </h5>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($results)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 ps-3">Data</th>
                            <th class="border-0 py-3">Aluno</th>
                            <th class="border-0 py-3">Turma</th>
                            <th class="border-0 py-3">Disciplina</th>
                            <th class="border-0 py-3">Tipo</th>
                            <th class="border-0 py-3 text-center">Nota</th>
                            <th class="border-0 py-3 text-center">Status</th>
                            <th class="border-0 py-3 text-center pe-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $result): ?>
                            <?php
                            $statusClass = $result->score >= 10 ? 'success' : ($result->score >= 7 ? 'warning' : 'danger');
                            $statusText = $result->score >= 10 ? 'Aprovado' : ($result->score >= 7 ? 'Recurso' : 'Reprovado');
                            ?>
                            <tr>
                                <td class="ps-3">
                                    <?= date('d/m/Y', strtotime($result->exam_date)) ?>
                                    <?php if ($result->exam_time): ?>
                                        <br><small class="text-muted"><?= substr($result->exam_time, 0, 5) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-2">
                                            <?= strtoupper(substr($result->first_name, 0, 1) . substr($result->last_name, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <span class="fw-semibold"><?= $result->first_name ?> <?= $result->last_name ?></span>
                                            <br>
                                            <small class="text-muted"><?= $result->student_number ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $result->class_name ?></td>
                                <td>
                                    <?= $result->discipline_name ?>
                                    <br>
                                    <small class="text-muted"><?= $result->board_name ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= $result->board_type ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold fs-5 <?= $statusClass == 'success' ? 'text-success' : ($statusClass == 'warning' ? 'text-warning' : 'text-danger') ?>">
                                        <?= number_format($result->score, 1) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $statusClass ?> p-2">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('admin/students/view/' . $result->student_id) ?>" 
                                           class="btn btn-outline-primary" title="Ver aluno">
                                            <i class="fas fa-user"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            <?php if (isset($pager)): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum resultado encontrado</h5>
                <p class="text-muted">
                    Tente ajustar os filtros ou registe notas de exames.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
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
</style>

<?= $this->endSection() ?>