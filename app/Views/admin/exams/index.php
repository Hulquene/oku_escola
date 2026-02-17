<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/exams/schedule') ?>" class="btn btn-info me-2">
                <i class="fas fa-calendar-alt"></i> Calendário
            </a>
            <a href="<?= site_url('admin/exams/form-add') ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Novo Exame
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active" aria-current="page">Lista de Exames</li>
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
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class->id ?>" <?= $this->request->getGet('class') == $class->id ? 'selected' : '' ?>>
                                <?= $class->class_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-4">
                <label for="semester" class="form-label">Semestre</label>
                <select class="form-select" id="semester" name="semester">
                    <option value="">Todos</option>
                    <?php if (!empty($semesters)): ?>
                        <?php foreach ($semesters as $semester): ?>
                            <option value="<?= $semester->id ?>" <?= $this->request->getGet('semester') == $semester->id ? 'selected' : '' ?>>
                                <?= $semester->semester_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/exams') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-pencil-alt"></i> Lista de Exames
    </div>
    <div class="card-body">
        <table id="examsTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Exame</th>
                    <th>Turma</th>
                    <th>Disciplina</th>
                    <th>Tipo</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Sala</th>
                    <th>Publicado</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($exams)): ?>
                    <?php foreach ($exams as $exam): ?>
                        <tr>
                            <td><?= $exam->id ?></td>
                            <td><?= $exam->exam_name ?></td>
                            <td><?= $exam->class_name ?></td>
                            <td><?= $exam->discipline_name ?></td>
                            <td><span class="badge bg-info"><?= $exam->board_name ?></span></td>
                            <td><?= date('d/m/Y', strtotime($exam->exam_date)) ?></td>
                            <td><?= $exam->exam_time ? date('H:i', strtotime($exam->exam_time)) : '-' ?></td>
                            <td><?= $exam->exam_room ?: '-' ?></td>
                            <td>
                                <?php if ($exam->is_published): ?>
                                    <span class="badge bg-success">Sim</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Não</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('admin/exams/view/' . $exam->id) ?>" 
                                   class="btn btn-sm btn-success" title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= site_url('admin/exams/form-edit/' . $exam->id) ?>" 
                                   class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= site_url('admin/exams/delete/' . $exam->id) ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Tem certeza que deseja eliminar este exame?')"
                                   title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">Nenhum exame encontrado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?= $pager->links() ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#examsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[5, 'desc']],
        pageLength: 25,
        searching: false
    });
});
</script>
<?= $this->endSection() ?>