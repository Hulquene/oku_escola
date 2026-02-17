<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Presenças</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" id="attendanceForm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="class" class="form-label">Turma</label>
                    <select class="form-select" id="class" name="class_id" required>
                        <option value="">Selecione...</option>
                        <?php if (!empty($classes)): ?>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= $class->id ?>" <?= $selectedClass == $class->id ? 'selected' : '' ?>>
                                    <?= $class->class_name ?> (<?= $class->class_code ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="discipline" class="form-label">Disciplina</label>
                    <select class="form-select" id="discipline" name="discipline_id">
                        <option value="">Todas</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="date" class="form-label">Data</label>
                    <input type="date" class="form-control" id="date" name="date" 
                           value="<?= $selectedDate ?>" required>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-search"></i> Carregar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($selectedClass): ?>
    <?php if (!empty($students)): ?>
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <span>
                        <i class="fas fa-calendar-check"></i> 
                        Presenças - <?= date('d/m/Y', strtotime($selectedDate)) ?>
                    </span>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-success me-2" onclick="markAll('Presente')">
                            <i class="fas fa-check-circle"></i> Todos Presentes
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAll('Ausente')">
                            <i class="fas fa-times-circle"></i> Todos Ausentes
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="<?= site_url('teachers/attendance/save') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="class_id" value="<?= $selectedClass ?>">
                    <input type="hidden" name="discipline_id" value="<?= $selectedDiscipline ?>">
                    <input type="hidden" name="attendance_date" value="<?= $selectedDate ?>">
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nº Matrícula</th>
                                    <th>Aluno</th>
                                    <th>Status</th>
                                    <th>Justificação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <?php $attendance = $attendances[$student->enrollment_id] ?? null; ?>
                                    <tr>
                                        <td><?= $student->student_number ?></td>
                                        <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                                        <td style="width: 200px;">
                                            <select class="form-select status-select" name="attendance[<?= $student->enrollment_id ?>][status]">
                                                <option value="Presente" <?= ($attendance && $attendance->status == 'Presente') ? 'selected' : '' ?>>Presente</option>
                                                <option value="Ausente" <?= ($attendance && $attendance->status == 'Ausente') ? 'selected' : '' ?>>Ausente</option>
                                                <option value="Atrasado" <?= ($attendance && $attendance->status == 'Atrasado') ? 'selected' : '' ?>>Atrasado</option>
                                                <option value="Falta Justificada" <?= ($attendance && $attendance->status == 'Falta Justificada') ? 'selected' : '' ?>>Falta Justificada</option>
                                                <option value="Dispensado" <?= ($attendance && $attendance->status == 'Dispensado') ? 'selected' : '' ?>>Dispensado</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="attendance[<?= $student->enrollment_id ?>][justification]" 
                                                   value="<?= $attendance ? $attendance->justification : '' ?>" placeholder="Justificação (opcional)">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <hr>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Salvar Presenças
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6 class="card-title">Presentes</h6>
                        <h2 id="statPresent">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6 class="card-title">Ausentes</h6>
                        <h2 id="statAbsent">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h6 class="card-title">Atrasados</h6>
                        <h2 id="statLate">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6 class="card-title">Justificados</h6>
                        <h2 id="statJustified">0</h2>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> Nenhum aluno encontrado para esta turma.
        </div>
    <?php endif; ?>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Load disciplines when class changes
document.getElementById('class').addEventListener('change', function() {
    const classId = this.value;
    const disciplineSelect = document.getElementById('discipline');
    
    if (classId) {
        fetch(`<?= site_url('teachers/exams/get-disciplines/') ?>/${classId}`)
            .then(response => response.json())
            .then(data => {
                disciplineSelect.innerHTML = '<option value="">Todas as disciplinas</option>';
                data.forEach(discipline => {
                    disciplineSelect.innerHTML += `<option value="${discipline.id}">${discipline.discipline_name}</option>`;
                });
            });
    } else {
        disciplineSelect.innerHTML = '<option value="">Todas as disciplinas</option>';
    }
});

// Trigger change if class is selected
<?php if ($selectedClass): ?>
document.getElementById('class').dispatchEvent(new Event('change'));
<?php endif; ?>

// Mark all with same status
function markAll(status) {
    document.querySelectorAll('.status-select').forEach(select => {
        select.value = status;
    });
    updateStats();
}

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

// Update stats on page load
window.addEventListener('load', updateStats);

// Update stats when status changes
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', updateStats);
});
</script>
<?= $this->endSection() ?>