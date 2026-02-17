<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/exams/form-add') ?>" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Novo Exame
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active" aria-current="page">Calendário</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <label for="class" class="form-label">Turma</label>
                <select class="form-select" id="class" name="class">
                    <option value="">Todas</option>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class->id ?>" <?= $selectedClass == $class->id ? 'selected' : '' ?>>
                                <?= $class->class_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="start_date" class="form-label">Data Início</label>
                <input type="date" class="form-control" id="start_date" name="start_date" 
                    value="<?= $selectedStartDate ?>">
            </div>

            <div class="col-md-3">
                <label for="end_date" class="form-label">Data Fim</label>
                <input type="date" class="form-control" id="end_date" name="end_date" 
                    value="<?= $selectedEndDate ?>">
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/exams/schedule') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Calendar View -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-calendar-alt"></i> Calendário de Exames
    </div>
    <div class="card-body">
        <?php if (!empty($exams)): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Turma</th>
                            <th>Disciplina</th>
                            <th>Tipo</th>
                            <th>Sala</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $currentDate = '';
                        foreach ($exams as $exam): 
                            $examDate = date('Y-m-d', strtotime($exam->exam_date));
                            if ($currentDate != $examDate):
                                $currentDate = $examDate;
                        ?>
                            <tr class="table-secondary">
                                <td colspan="7" class="fw-bold">
                                    <i class="fas fa-calendar-day"></i> 
                                    <?= date('d/m/Y', strtotime($exam->exam_date)) ?> 
                                    (<?= getDayOfWeek($exam->exam_date) ?>)
                                </td>
                            </tr>
                        <?php endif; ?>
                            <tr>
                                <td></td>
                                <td><?= $exam->exam_time ? date('H:i', strtotime($exam->exam_time)) : '-' ?></td>
                                <td><?= $exam->class_name ?></td>
                                <td><?= $exam->discipline_name ?></td>
                                <td><span class="badge bg-info"><?= $exam->board_name ?></span></td>
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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhum exame agendado para o período selecionado.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>