<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/reports') ?>">Relatórios</a></li>
            <li class="breadcrumb-item active" aria-current="page">Alunos</li>
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
                <select class="form-select" id="academic_year" name="academic_year" onchange="this.form.submit()">
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
                    <option value="Ativo" <?= $selectedStatus == 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                    <option value="Concluído" <?= $selectedStatus == 'Concluído' ? 'selected' : '' ?>>Concluído</option>
                    <option value="Transferido" <?= $selectedStatus == 'Transferido' ? 'selected' : '' ?>>Transferido</option>
                </select>
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/reports/students') ?>" class="btn btn-secondary ms-2">
                    <i class="fas fa-undo"></i>
                </a>
                <button class="btn btn-success ms-2" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i>
                </button>
                <button class="btn btn-warning ms-2" onclick="window.print()">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Alunos</h6>
                <h3><?= count($students) ?></h3>
                <small>No período selecionado</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Masculino</h6>
                <h3><?= $totalMale ?></h3>
                <small><?= count($students) > 0 ? round(($totalMale / count($students)) * 100, 1) : 0 ?>%</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Feminino</h6>
                <h3><?= $totalFemale ?></h3>
                <small><?= count($students) > 0 ? round(($totalFemale / count($students)) * 100, 1) : 0 ?>%</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6 class="card-title">Turmas</h6>
                <h3><?= count($classes) ?></h3>
                <small>Com alunos matriculados</small>
            </div>
        </div>
    </div>
</div>

<!-- Students List -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i> Lista de Alunos
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="studentsTable">
                <thead>
                    <tr>
                        <th>Nº Matrícula</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Data Nasc.</th>
                        <th>Género</th>
                        <th>Turma</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= $student->student_number ?></td>
                                <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                                <td><?= $student->email ?></td>
                                <td><?= $student->phone ?: '-' ?></td>
                                <td><?= date('d/m/Y', strtotime($student->birth_date)) ?></td>
                                <td><?= $student->gender ?></td>
                                <td><?= $student->class_name ?></td>
                                <td>
                                    <span class="badge bg-success"><?= $student->status ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Nenhum aluno encontrado</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#studentsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc']],
        pageLength: 50
    });
});

function exportToExcel() {
    // Get current filters
    const year = document.getElementById('academic_year').value;
    const classId = document.getElementById('class').value;
    const status = document.getElementById('status').value;
    
    window.location.href = '<?= site_url('admin/reports/export/students') ?>?' + 
        'academic_year=' + year + 
        '&class=' + classId + 
        '&status=' + status;
}
</script>
<?= $this->endSection() ?>