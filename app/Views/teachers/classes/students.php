<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1>Alunos da Turma: <?= $class->class_name ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/classes') ?>">Minhas Turmas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Alunos</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações da Turma -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-light">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <small class="text-muted">Turma</small>
                        <h5><?= $class->class_name ?></h5>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Código</small>
                        <h5><?= $class->class_code ?></h5>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted">Turno</small>
                        <h5><?= $class->class_shift ?></h5>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted">Sala</small>
                        <h5><?= $class->class_room ?: 'N/A' ?></h5>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted">Total Alunos</small>
                        <h5><?= count($students) ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas da Turma -->
<div class="row mb-4">
    <div class="col-12">
        <div class="btn-group">
            <a href="<?= site_url('teachers/attendance?class_id=' . $class->class_id) ?>" 
               class="btn btn-info">
                <i class="fas fa-calendar-check"></i> Registrar Presenças
            </a>
            <a href="<?= site_url('teachers/grades?class=' . $class->class_id) ?>" 
               class="btn btn-warning">
                <i class="fas fa-star"></i> Lançar Notas
            </a>
            <a href="<?= site_url('teachers/exams?class=' . $class->class_id) ?>" 
               class="btn btn-success">
                <i class="fas fa-file-alt"></i> Ver Exames
            </a>
        </div>
    </div>
</div>

<!-- Lista de Alunos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-users"></i> Lista de Alunos
                <div class="float-end">
                    <input type="text" id="searchInput" class="form-control form-control-sm" 
                           placeholder="Buscar aluno..." style="width: 250px;">
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($students)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover" id="studentsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>Nº Processo</th>
                                    <th>Email</th>
                                    <th>Telefone</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $index => $student): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <strong><?= $student->first_name ?> <?= $student->last_name ?></strong>
                                    </td>
                                    <td><span class="badge bg-info"><?= $student->student_number ?></span></td>
                                    <td><?= $student->email ?? '-' ?></td>
                                    <td><?= $student->phone ?? '-' ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= site_url('teachers/attendance?class_id=' . $class->class_id . '&student=' . $student->id) ?>" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Ver Presenças">
                                                <i class="fas fa-calendar-check"></i>
                                            </a>
                                            <a href="<?= site_url('teachers/grades?class=' . $class->class_id . '&student=' . $student->id) ?>" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Ver Notas">
                                                <i class="fas fa-star"></i>
                                            </a>
                                            <a href="<?= site_url('teachers/students/profile/' . $student->id) ?>" 
                                               class="btn btn-sm btn-outline-secondary" 
                                               title="Perfil">
                                                <i class="fas fa-user"></i>
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
                        <i class="fas fa-users-slash fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum aluno matriculado nesta turma</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('searchInput')?.addEventListener('keyup', function() {
    let searchText = this.value.toLowerCase();
    let table = document.getElementById('studentsTable');
    let rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let row of rows) {
        let name = row.cells[1].textContent.toLowerCase();
        let number = row.cells[2].textContent.toLowerCase();
        
        if (name.includes(searchText) || number.includes(searchText)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

// Ordenação simples
document.querySelectorAll('#studentsTable thead th').forEach((th, index) => {
    if (index < 2) { // Só ordenar pelas primeiras colunas
        th.addEventListener('click', () => {
            sortTable(index);
        });
        th.style.cursor = 'pointer';
        th.title = 'Clique para ordenar';
    }
});

let sortDirection = true;
function sortTable(colIndex) {
    let table = document.getElementById('studentsTable');
    let tbody = table.getElementsByTagName('tbody')[0];
    let rows = Array.from(tbody.getElementsByTagName('tr'));
    
    rows.sort((a, b) => {
        let aVal = a.cells[colIndex].textContent.toLowerCase();
        let bVal = b.cells[colIndex].textContent.toLowerCase();
        
        if (sortDirection) {
            return aVal.localeCompare(bVal);
        } else {
            return bVal.localeCompare(aVal);
        }
    });
    
    sortDirection = !sortDirection;
    
    while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
    }
    
    rows.forEach(row => tbody.appendChild(row));
}
</script>
<?= $this->endSection() ?>