<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('teachers/exams/form-add') ?>" class="btn btn-success">
            <i class="fas fa-plus-circle"></i> Novo Exame
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Meus Exames</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="class" class="form-label">Turma</label>
                <select class="form-select" id="class" name="class">
                    <option value="">Todas</option>
                    <?php 
                    $classDisciplineModel = new \App\Models\ClassDisciplineModel();
                    $teacherId = session()->get('user_id');
                    $classes = $classDisciplineModel
                        ->select('tbl_classes.id, tbl_classes.class_name')
                        ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
                        ->where('tbl_class_disciplines.teacher_id', $teacherId)
                        ->distinct()
                        ->findAll();
                    ?>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class->id ?>" <?= ($filters['class'] ?? '') == $class->id ? 'selected' : '' ?>>
                                <?= $class->class_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-4">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="pending" <?= ($filters['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Pendentes</option>
                    <option value="completed" <?= ($filters['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Realizados</option>
                </select>
            </div>
            
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-success me-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('teachers/exams') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Exams Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-pencil-alt"></i> Lista de Exames
    </div>
    <div class="card-body">
        <?php if (!empty($exams)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Exame</th>
                            <th>Turma</th>
                            <th>Disciplina</th>
                            <th>Tipo</th>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Sala</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exams as $exam): ?>
                            <tr>
                                <td><?= $exam->exam_name ?></td>
                                <td><?= $exam->class_name ?></td>
                                <td><?= $exam->discipline_name ?></td>
                                <td><span class="badge bg-info"><?= $exam->board_name ?></span></td>
                                <td><?= date('d/m/Y', strtotime($exam->exam_date)) ?></td>
                                <td><?= $exam->exam_time ? date('H:i', strtotime($exam->exam_time)) : '-' ?></td>
                                <td><?= $exam->exam_room ?: '-' ?></td>
                                <td>
                                    <?php if ($exam->exam_date < date('Y-m-d')): ?>
                                        <span class="badge bg-secondary">Realizado</span>
                                    <?php elseif ($exam->exam_date == date('Y-m-d')): ?>
                                        <span class="badge bg-success">Hoje</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Agendado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('teachers/exams/grade/' . $exam->id) ?>" 
                                       class="btn btn-sm btn-success" title="Lançar Notas">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <?php if ($exam->exam_date >= date('Y-m-d')): ?>
                                        <a href="<?= site_url('teachers/exams/edit/' . $exam->id) ?>" 
                                           class="btn btn-sm btn-info" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Nenhum exame encontrado.</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>