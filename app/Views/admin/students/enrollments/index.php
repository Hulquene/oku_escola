<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/students/enrollments/form-add') ?>" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Nova Matrícula
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students') ?>">Alunos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Matrículas</li>
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
                <label for="academic_year" class="form-label">Ano Letivo</label>
                <select class="form-select" id="academic_year" name="academic_year">
                    <option value="">Todos</option>
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= $year->id ?>" <?= $selectedYear == $year->id ? 'selected' : '' ?>>
                                <?= $year->year_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
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
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="Ativo" <?= $selectedStatus == 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                    <option value="Pendente" <?= $selectedStatus == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                    <option value="Concluído" <?= $selectedStatus == 'Concluído' ? 'selected' : '' ?>>Concluído</option>
                    <option value="Transferido" <?= $selectedStatus == 'Transferido' ? 'selected' : '' ?>>Transferido</option>
                    <option value="Anulado" <?= $selectedStatus == 'Anulado' ? 'selected' : '' ?>>Anulado</option>
                </select>
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/students/enrollments') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-file-signature"></i> Lista de Matrículas
    </div>
    <div class="card-body">
        <table id="enrollmentsTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Nº Matrícula</th>
                    <th>Aluno</th>
                    <th>Turma</th>
                    <th>Ano Letivo</th>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($enrollments)): ?>
                    <?php foreach ($enrollments as $enrollment): ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?= $enrollment->enrollment_number ?></span></td>
                            <td><?= $enrollment->first_name ?> <?= $enrollment->last_name ?></td>
                            <td><?= $enrollment->class_name ?></td>
                            <td><?= $enrollment->year_name ?></td>
                            <td><?= date('d/m/Y', strtotime($enrollment->enrollment_date)) ?></td>
                            <td><?= $enrollment->enrollment_type ?></td>
                            <td>
                                <?php
                                $statusClass = [
                                    'Ativo' => 'success',
                                    'Pendente' => 'warning',
                                    'Concluído' => 'info',
                                    'Transferido' => 'primary',
                                    'Anulado' => 'danger'
                                ][$enrollment->status] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $statusClass ?>"><?= $enrollment->status ?></span>
                            </td>
                            <td>
                                <a href="<?= site_url('admin/students/enrollments/view/' . $enrollment->id) ?>" 
                                   class="btn btn-sm btn-success" title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= site_url('admin/students/view/' . $enrollment->student_id) ?>" 
                                   class="btn btn-sm btn-info" title="Ver Aluno">
                                    <i class="fas fa-user-graduate"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Nenhuma matrícula encontrada</td>
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
    $('#enrollmentsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[4, 'desc']],
        pageLength: 25,
        searching: false
    });
});
</script>
<?= $this->endSection() ?>