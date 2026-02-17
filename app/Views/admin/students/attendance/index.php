<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students') ?>">Alunos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Presenças</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" id="attendanceForm">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="class_id" class="form-label">Turma</label>
                    <select class="form-select" id="class_id" name="class_id" required>
                        <option value="">Selecione...</option>
                        <?php if (!empty($classes)): ?>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= $class->id ?>" <?= $selectedClass == $class->id ? 'selected' : '' ?>>
                                    <?= $class->class_name ?> (<?= $class->class_code ?>) - <?= $class->class_shift ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="discipline_id" class="form-label">Disciplina</label>
                    <select class="form-select" id="discipline_id" name="discipline_id">
                        <option value="">Todas</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="attendance_date" class="form-label">Data</label>
                    <input type="date" class="form-control" id="attendance_date" name="attendance_date" 
                           value="<?= $selectedDate ?? date('Y-m-d') ?>" required>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Carregar
                    </button>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-success w-100" onclick="saveAttendance()">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Attendance Table -->
<?php if (!empty($students)): ?>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-calendar-check"></i> 
                    Registro de Presenças - <?= date('d/m/Y', strtotime($selectedDate)) ?>
                </span>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="markAll('Presente')">
                        <i class="fas fa-check-circle"></i> Todos Presentes
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAll('Ausente')">
                        <i class="fas fa-times-circle"></i> Todos Ausentes
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form id="attendanceGrid">
                <?= csrf_field() ?>
                <input type="hidden" name="class_id" value="<?= $selectedClass ?>">
                <input type="hidden" name="discipline_id" id="form_discipline_id" value="<?= $selectedDiscipline ?>">
                <input type="hidden" name="attendance_date" value="<?= $selectedDate ?>">
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nº Matrícula</th>
                                <th>Aluno</th>
                                <th>Status</th>
                                <th>Justificação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $index => $student): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $student->student_number ?></td>
                                    <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                                    <td style="width: 200px;">
                                        <select class="form-select status-select" 
                                                name="attendance[<?= $student->enrollment_id ?>][status]"
                                                data-enrollment="<?= $student->enrollment_id ?>">
                                            <option value="Presente" <?= ($student->attendance_status ?? '') == 'Presente' ? 'selected' : '' ?>>Presente</option>
                                            <option value="Ausente" <?= ($student->attendance_status ?? '') == 'Ausente' ? 'selected' : '' ?>>Ausente</option>
                                            <option value="Atrasado" <?= ($student->attendance_status ?? '') == 'Atrasado' ? 'selected' : '' ?>>Atrasado</option>
                                            <option value="Falta Justificada" <?= ($student->attendance_status ?? '') == 'Falta Justificada' ? 'selected' : '' ?>>Falta Justificada</option>
                                            <option value="Dispensado" <?= ($student->attendance_status ?? '') == 'Dispensado' ? 'selected' : '' ?>>Dispensado</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" 
                                               name="attendance[<?= $student->enrollment_id ?>][justification]"
                                               value="<?= $student->attendance_justification ?? '' ?>"
                                               placeholder="Justificação (opcional)">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
            
            <div class="mt-3 text-end">
                <button type="button" class="btn btn-primary" onclick="saveAttendance()">
                    <i class="fas fa-save"></i> Salvar Presenças
                </button>
            </div>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Presentes</h5>
                    <h2 id="statPresent">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Ausentes</h5>
                    <h2 id="statAbsent">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Atrasados</h5>
                    <h2 id="statLate">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Justificados</h5>
                    <h2 id="statJustified">0</h2>
                </div>
            </div>
        </div>
    </div>
<?php elseif ($selectedClass): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> Nenhum aluno encontrado para esta turma.
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Load disciplines when class is selected
document.getElementById('class_id').addEventListener('change', function() {
    const classId = this.value;
    const disciplineSelect = document.getElementById('discipline_id');
    
    if (classId) {
        fetch(`<?= site_url('admin/classes/class-subjects/get-by-class/') ?>/${classId}`)
            .then(response => response.json())
            .then(data => {
                disciplineSelect.innerHTML = '<option value="">Todas as disciplinas</option>';
                data.forEach(discipline => {
                    disciplineSelect.innerHTML += `<option value="${discipline.discipline_id}">${discipline.discipline_name}</option>`;
                });
            });
    }
});

// Update statistics
function updateStats() {
    let present = 0, absent = 0, late = 0, justified = 0;
    
    document.querySelectorAll('.status-select').forEach(select => {
        switch(select.value) {
            case 'Presente': present++; break;
            case 'Ausente': absent++; break;
            case 'Atrasado': late++; break;
            case 'Falta Justificada': justified++; break;
        }
    });
    
    document.getElementById('statPresent').textContent = present;
    document.getElementById('statAbsent').textContent = absent;
    document.getElementById('statLate').textContent = late;
    document.getElementById('statJustified').textContent = justified;
}

// Mark all students with same status
function markAll(status) {
    document.querySelectorAll('.status-select').forEach(select => {
        select.value = status;
    });
    updateStats();
}

// Save attendance
function saveAttendance() {
    const form = document.getElementById('attendanceGrid');
    const formData = new FormData(form);
    
    // Add filter values
    formData.append('class_id', document.getElementById('class_id').value);
    formData.append('discipline_id', document.getElementById('discipline_id').value);
    formData.append('attendance_date', document.getElementById('attendance_date').value);
    
    fetch('<?= site_url('admin/students/attendance/save') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Presenças salvas com sucesso!');
        } else {
            alert('Erro ao salvar presenças: ' + data.message);
        }
    })
    .catch(error => {
        alert('Erro ao salvar presenças');
        console.error(error);
    });
}

// Update stats when status changes
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', updateStats);
});

// Initial stats
updateStats();
</script>
<?= $this->endSection() ?>