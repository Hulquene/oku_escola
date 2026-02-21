<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Calculadora de Médias</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calculator me-1"></i>
                Calcule médias disciplinares e resultados semestrais
            </p>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active">Calculadora</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Opções -->
<div class="row g-4">
    <!-- Card: Cálculo por Disciplina -->
    <div class="col-md-4">
        <div class="card calculator-card">
            <div class="card-body text-center">
                <div class="calculator-icon bg-primary bg-opacity-10 text-primary mb-3">
                    <i class="fas fa-book-open"></i>
                </div>
                <h4 class="fw-semibold mb-2">Por Disciplina</h4>
                <p class="text-muted mb-3">
                    Calcular média de uma disciplina específica para um aluno
                </p>
                <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#disciplineModal">
                    <i class="fas fa-calculator me-2"></i>Calcular
                </button>
            </div>
        </div>
    </div>

    <!-- Card: Cálculo por Aluno -->
    <div class="col-md-4">
        <div class="card calculator-card">
            <div class="card-body text-center">
                <div class="calculator-icon bg-success bg-opacity-10 text-success mb-3">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h4 class="fw-semibold mb-2">Por Aluno</h4>
                <p class="text-muted mb-3">
                    Calcular todas as médias de um aluno no semestre
                </p>
                <button class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#studentModal">
                    <i class="fas fa-calculator me-2"></i>Calcular
                </button>
            </div>
        </div>
    </div>

    <!-- Card: Cálculo por Turma -->
    <div class="col-md-4">
        <div class="card calculator-card">
            <div class="card-body text-center">
                <div class="calculator-icon bg-warning bg-opacity-10 text-warning mb-3">
                    <i class="fas fa-users"></i>
                </div>
                <h4 class="fw-semibold mb-2">Por Turma</h4>
                <p class="text-muted mb-3">
                    Calcular todas as médias de todos os alunos da turma
                </p>
                <button class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#classModal">
                    <i class="fas fa-calculator me-2"></i>Calcular
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Status de Cálculos Recentes -->
<div class="card mt-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-chart-line me-2 text-primary"></i>
            Status dos Cálculos
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <select class="form-select" id="statusSemester">
                    <option value="">Selecione o semestre</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= $sem->id ?>"><?= $sem->semester_name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" id="statusClass">
                    <option value="">Selecione a turma</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class->id ?>"><?= $class->class_name ?> (<?= $class->level_name ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary w-100" onclick="checkStatus()">
                    <i class="fas fa-search me-2"></i>Verificar Status
                </button>
            </div>
        </div>

        <div id="statusResult" class="mt-4" style="display: none;">
            <!-- Status will be loaded here via AJAX -->
        </div>
    </div>
</div>

<!-- Modal: Cálculo por Disciplina -->
<div class="modal fade" id="disciplineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-book-open me-2"></i>
                    Calcular Média por Disciplina
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('admin/exams/calculate/run') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="calculation_type" value="discipline">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Semestre <span class="text-danger">*</span></label>
                        <select class="form-select" name="semester_id" required>
                            <option value="">Selecione</option>
                            <?php foreach ($semesters as $sem): ?>
                                <option value="<?= $sem->id ?>"><?= $sem->semester_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Turma <span class="text-danger">*</span></label>
                        <select class="form-select" id="disciplineClass" required>
                            <option value="">Selecione</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= $class->id ?>"><?= $class->class_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Aluno <span class="text-danger">*</span></label>
                        <select class="form-select" name="enrollment_id" id="disciplineStudent" required>
                            <option value="">Primeiro selecione a turma</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Disciplina <span class="text-danger">*</span></label>
                        <select class="form-select" name="discipline_id" id="disciplineSelect" required>
                            <option value="">Primeiro selecione o aluno</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calculator me-2"></i>Calcular Média
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Cálculo por Aluno -->
<div class="modal fade" id="studentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-graduate me-2"></i>
                    Calcular Médias por Aluno
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('admin/exams/calculate/run') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="calculation_type" value="student">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Semestre <span class="text-danger">*</span></label>
                        <select class="form-select" name="semester_id" required>
                            <option value="">Selecione</option>
                            <?php foreach ($semesters as $sem): ?>
                                <option value="<?= $sem->id ?>"><?= $sem->semester_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Turma <span class="text-danger">*</span></label>
                        <select class="form-select" id="studentClass" required>
                            <option value="">Selecione</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= $class->id ?>"><?= $class->class_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Aluno <span class="text-danger">*</span></label>
                        <select class="form-select" name="enrollment_id" id="studentSelect" required>
                            <option value="">Primeiro selecione a turma</option>
                        </select>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Serão calculadas todas as disciplinas deste aluno no semestre selecionado.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-calculator me-2"></i>Calcular Médias
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Cálculo por Turma -->
<div class="modal fade" id="classModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-users me-2"></i>
                    Calcular Médias por Turma
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('admin/exams/calculate/run') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="calculation_type" value="class">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Semestre <span class="text-danger">*</span></label>
                        <select class="form-select" name="semester_id" required>
                            <option value="">Selecione</option>
                            <?php foreach ($semesters as $sem): ?>
                                <option value="<?= $sem->id ?>"><?= $sem->semester_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Turma <span class="text-danger">*</span></label>
                        <select class="form-select" name="class_id" required>
                            <option value="">Selecione</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= $class->id ?>"><?= $class->class_name ?> (<?= $class->level_name ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção:</strong> Esta operação pode demorar alguns minutos dependendo do tamanho da turma.
                        Serão calculadas todas as disciplinas de todos os alunos.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Iniciar cálculo para toda a turma?')">
                        <i class="fas fa-calculator me-2"></i>Calcular Turma
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Progresso -->
<div class="modal fade" id="progressModal" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    Processando Cálculo
                </h5>
            </div>
            <div class="modal-body text-center py-4">
                <div class="progress mb-3" style="height: 20px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         id="calculationProgress" 
                         style="width: 0%">0%</div>
                </div>
                <p id="progressMessage">Iniciando cálculo...</p>
            </div>
        </div>
    </div>
</div>

<style>
.calculator-card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    height: 100%;
}

.calculator-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.calculator-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 2.5rem;
}

.card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
</style>

<script>
// Load students when class is selected (for discipline modal)
$('#disciplineClass').change(function() {
    let classId = $(this).val();
    let $studentSelect = $('#disciplineStudent');
    
    if (classId) {
        $.get('<?= site_url('admin/exams/calculate/get-students/') ?>/' + classId, function(students) {
            $studentSelect.empty();
            $studentSelect.append('<option value="">Selecione o aluno</option>');
            
            students.forEach(student => {
                $studentSelect.append('<option value="' + student.enrollment_id + '">' + 
                    student.first_name + ' ' + student.last_name + ' (' + student.student_number + ')</option>');
            });
        });
    } else {
        $studentSelect.empty();
        $studentSelect.append('<option value="">Primeiro selecione a turma</option>');
    }
});

// Load disciplines when student is selected (for discipline modal)
$('#disciplineStudent').change(function() {
    let enrollmentId = $(this).val();
    let semesterId = $('select[name="semester_id"]').val();
    let $disciplineSelect = $('#disciplineSelect');
    
    if (enrollmentId && semesterId) {
        // Get class from student
        $.get('<?= site_url('admin/exams/calculate/get-student-class/') ?>/' + enrollmentId, function(data) {
            if (data.class_id) {
                $.get('<?= site_url('admin/exams/calculate/get-disciplines/') ?>/' + data.class_id + '/' + semesterId, function(disciplines) {
                    $disciplineSelect.empty();
                    $disciplineSelect.append('<option value="">Selecione a disciplina</option>');
                    
                    disciplines.forEach(discipline => {
                        $disciplineSelect.append('<option value="' + discipline.id + '">' + 
                            discipline.discipline_name + ' (' + discipline.discipline_code + ')</option>');
                    });
                });
            }
        });
    } else {
        $disciplineSelect.empty();
        $disciplineSelect.append('<option value="">Primeiro selecione aluno e semestre</option>');
    }
});

// Load students for student modal
$('#studentClass').change(function() {
    let classId = $(this).val();
    let $studentSelect = $('#studentSelect');
    
    if (classId) {
        $.get('<?= site_url('admin/exams/calculate/get-students/') ?>/' + classId, function(students) {
            $studentSelect.empty();
            $studentSelect.append('<option value="">Selecione o aluno</option>');
            
            students.forEach(student => {
                $studentSelect.append('<option value="' + student.enrollment_id + '">' + 
                    student.first_name + ' ' + student.last_name + ' (' + student.student_number + ')</option>');
            });
        });
    } else {
        $studentSelect.empty();
        $studentSelect.append('<option value="">Primeiro selecione a turma</option>');
    }
});

// Check calculation status
function checkStatus() {
    let semesterId = $('#statusSemester').val();
    let classId = $('#statusClass').val();
    
    if (!semesterId || !classId) {
        toastr.warning('Selecione o semestre e a turma');
        return;
    }
    
    $('#statusResult').show().html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><p class="mt-2">Verificando status...</p></div>');
    
    $.get('<?= site_url('admin/exams/calculate/get-status/') ?>/' + classId + '/' + semesterId, function(response) {
        if (response.success) {
            let data = response.data;
            let statusClass = data.status === 'complete' ? 'success' : (data.status === 'in_progress' ? 'warning' : 'secondary');
            let statusText = data.status === 'complete' ? 'Completo' : (data.status === 'in_progress' ? 'Em Progresso' : 'Pendente');
            
            let html = `
                <div class="alert alert-${statusClass}">
                    <h6 class="fw-semibold mb-3">Status do Cálculo</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Status:</strong> <span class="badge bg-${statusClass}">${statusText}</span></p>
                            <p><strong>Progresso:</strong> ${data.progress}%</p>
                            <p><strong>Total Alunos:</strong> ${data.total_students}</p>
                            <p><strong>Alunos com Resultados:</strong> ${data.students_with_results}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Disciplinas:</strong> ${data.total_disciplines}</p>
                            <p><strong>Disciplinas Calculadas:</strong> ${data.disciplines_with_averages}</p>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 10px;">
                        <div class="progress-bar bg-${statusClass}" style="width: ${data.progress}%"></div>
                    </div>
                </div>
            `;
            
            $('#statusResult').html(html);
        } else {
            $('#statusResult').html('<div class="alert alert-danger">Erro ao verificar status</div>');
        }
    });
}

// Handle form submissions with progress modal
$('form').submit(function(e) {
    if ($(this).attr('action') === '<?= site_url('admin/exams/calculate/run') ?>') {
        let calcType = $(this).find('input[name="calculation_type"]').val();
        
        if (calcType === 'class') {
            $('#progressModal').modal('show');
            
            // Simulate progress (in real implementation, you would use AJAX polling)
            let progress = 0;
            let interval = setInterval(() => {
                progress += 10;
                $('#calculationProgress').css('width', progress + '%').text(progress + '%');
                
                if (progress >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        $('#progressModal').modal('hide');
                    }, 1000);
                }
            }, 500);
        }
    }
});

// Validate weights before class calculation
$('#classModal form').submit(function(e) {
    let classId = $(this).find('select[name="class_id"]').val();
    let semesterId = $(this).find('select[name="semester_id"]').val();
    
    e.preventDefault();
    
    $.get('<?= site_url('admin/exams/calculate/validate-weights/') ?>/' + classId + '/' + semesterId, function(response) {
        if (response.success) {
            // Proceed with submission
            $('#classModal form').off('submit').submit();
        } else {
            let message = 'A configuração de pesos precisa ser revista:\n';
            response.data.missing_weights.forEach(item => {
                message += '\n• ' + item;
            });
            alert(message);
        }
    });
});
</script>

<?= $this->endSection() ?>