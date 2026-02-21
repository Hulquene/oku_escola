<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Registar Presenças</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-check me-1"></i>
                <?= $schedule->discipline_name ?> • <?= $schedule->class_name ?>
            </p>
        </div>
        <div>
            <a href="<?= site_url('admin/exams/schedules/view/' . $schedule->id) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/schedules') ?>">Agendamentos</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/schedules/view/' . $schedule->id) ?>">Detalhes</a></li>
            <li class="breadcrumb-item active">Presenças</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-user-check me-2 text-success"></i>
                    Lista de Presenças
                </h5>
            </div>
            <div class="card-body">
                <form method="post" id="attendanceForm">
                    <?= csrf_field() ?>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Nº Aluno</th>
                                    <th>Nome do Aluno</th>
                                    <th width="150" class="text-center">Presença</th>
                                    <th>Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($students)): ?>
                                    <?php 
                                    $i = 1;
                                    $existingMap = [];
                                    foreach ($existingAttendance as $att) {
                                        $existingMap[$att->enrollment_id] = $att;
                                    }
                                    ?>
                                    <?php foreach ($students as $student): ?>
                                        <?php 
                                        $existing = $existingMap[$student->enrollment_id] ?? null;
                                        $attended = $existing ? $existing->attended : 1;
                                        $observations = $existing ? $existing->observations : '';
                                        ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td>
                                                <span class="badge bg-info bg-opacity-10 text-info p-2">
                                                    <?= $student->student_number ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-2">
                                                        <?= strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) ?>
                                                    </div>
                                                    <span class="fw-semibold"><?= $student->first_name ?> <?= $student->last_name ?></span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <input type="radio" 
                                                           class="btn-check" 
                                                           name="attendance[<?= $student->enrollment_id ?>][attended]" 
                                                           id="present_<?= $student->enrollment_id ?>" 
                                                           value="1" 
                                                           <?= $attended ? 'checked' : '' ?>>
                                                    <label class="btn btn-outline-success btn-sm" for="present_<?= $student->enrollment_id ?>">
                                                        <i class="fas fa-check"></i> Presente
                                                    </label>
                                                    
                                                    <input type="radio" 
                                                           class="btn-check" 
                                                           name="attendance[<?= $student->enrollment_id ?>][attended]" 
                                                           id="absent_<?= $student->enrollment_id ?>" 
                                                           value="0"
                                                           <?= !$attended ? 'checked' : '' ?>>
                                                    <label class="btn btn-outline-danger btn-sm" for="absent_<?= $student->enrollment_id ?>">
                                                        <i class="fas fa-times"></i> Ausente
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" 
                                                       class="form-control form-control-sm" 
                                                       name="attendance[<?= $student->enrollment_id ?>][observations]" 
                                                       placeholder="Observações (opcional)"
                                                       value="<?= $observations ?>">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Nenhum aluno encontrado nesta turma</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= site_url('admin/exams/schedules/view/' . $schedule->id) ?>" class="btn btn-light">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i> Registar Presenças
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Card de Informações do Exame -->
        <div class="card mb-3">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-info-circle me-2 text-info"></i>
                    Informações do Exame
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Disciplina:</h6>
                    <p class="fw-semibold"><?= $schedule->discipline_name ?></p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Turma:</h6>
                    <p class="fw-semibold"><?= $schedule->class_name ?></p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Data:</h6>
                    <p class="fw-semibold"><?= date('d/m/Y', strtotime($schedule->exam_date)) ?></p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Hora:</h6>
                    <p class="fw-semibold"><?= $schedule->exam_time ? substr($schedule->exam_time, 0, 5) : 'Não definida' ?></p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Sala:</h6>
                    <p class="fw-semibold"><?= $schedule->exam_room ?: 'Não definida' ?></p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Período:</h6>
                    <p class="fw-semibold"><?= $schedule->period_name ?></p>
                </div>
            </div>
        </div>
        
        <!-- Card de Ações Rápidas -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-bolt me-2 text-warning"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="markAllPresent()">
                        <i class="fas fa-check-circle me-2"></i>Marcar Todos Presentes
                    </button>
                    <button class="btn btn-outline-danger" onclick="markAllAbsent()">
                        <i class="fas fa-times-circle me-2"></i>Marcar Todos Ausentes
                    </button>
                    <button class="btn btn-outline-success" onclick="toggleCheckboxes()">
                        <i class="fas fa-sync-alt me-2"></i>Inverter Seleções
                    </button>
                </div>
            </div>
        </div>
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

.btn-group {
    width: 200px;
}

.btn-group .btn {
    width: 50%;
}
</style>

<script>
function markAllPresent() {
    document.querySelectorAll('input[type="radio"][value="1"]').forEach(radio => {
        radio.checked = true;
    });
}

function markAllAbsent() {
    document.querySelectorAll('input[type="radio"][value="0"]').forEach(radio => {
        radio.checked = true;
    });
}

function toggleCheckboxes() {
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        if (radio.value === '1' && radio.checked) {
            let studentId = radio.name.match(/\d+/)[0];
            document.querySelector(`input[name="attendance[${studentId}][attended]"][value="0"]`).checked = true;
        } else if (radio.value === '0' && radio.checked) {
            let studentId = radio.name.match(/\d+/)[0];
            document.querySelector(`input[name="attendance[${studentId}][attended]"][value="1"]`).checked = true;
        }
    });
}

// Confirm before submit
document.getElementById('attendanceForm').addEventListener('submit', function(e) {
    if (!confirm('Confirmar registo de presenças?')) {
        e.preventDefault();
    }
});
</script>

<?= $this->endSection() ?>