<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Relatórios de Notas</h1>
            <p class="text-muted mb-0">Visualize estatísticas e relatórios consolidados</p>
        </div>
        <div>
            <a href="<?= site_url('admin/grades') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/grades') ?>">Notas</a></li>
            <li class="breadcrumb-item active">Relatórios</li>
        </ol>
    </nav>
</div>

<!-- Cards de Estatísticas Gerais -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="text-white-50">Total Alunos</h6>
                <h2 class="mb-0"><?= $totalAlunos ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="text-white-50">Aprovados</h6>
                <h2 class="mb-0"><?= $aprovados ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="text-white-50">Recurso</h6>
                <h2 class="mb-0"><?= $recurso ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="text-white-50">Reprovados</h6>
                <h2 class="mb-0"><?= $reprovados ?? 0 ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Seletor de Relatório -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Selecionar Relatório</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-school fa-3x text-primary mb-3"></i>
                        <h5>Por Turma</h5>
                        <p class="text-muted small">Visualize notas de uma turma específica</p>
                        <button class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#classModal">
                            Selecionar
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-graduate fa-3x text-success mb-3"></i>
                        <h5>Por Aluno</h5>
                        <p class="text-muted small">Histórico completo de um aluno</p>
                        <button class="btn btn-outline-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#studentModal">
                            Selecionar
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar fa-3x text-info mb-3"></i>
                        <h5>Por Período</h5>
                        <p class="text-muted small">Relatório por semestre/trimestre</p>
                        <button class="btn btn-outline-info btn-sm w-100" data-bs-toggle="modal" data-bs-target="#periodModal">
                            Selecionar
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line fa-3x text-warning mb-3"></i>
                        <h5>Desempenho Geral</h5>
                        <p class="text-muted small">Estatísticas da escola</p>
                        <a href="<?= site_url('admin/grades/statistics') ?>" class="btn btn-outline-warning btn-sm w-100">
                            Visualizar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Turma (CORRIGIDO) -->
<div class="modal fade" id="classModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Selecionar Turma</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Ano Letivo</label>
                    <select class="form-select" id="classYearSelect">
                        <option value="">Selecione...</option>
                        <?php if (!empty($academicYears)): ?>
                            <?php foreach ($academicYears as $year): ?>
                                <option value="<?= $year->id ?>"><?= $year->year_name ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Turma</label>
                    <select class="form-select" id="modalClassSelect" required disabled>
                        <option value="">Selecione primeiro o ano...</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="goToClassBtn" disabled>Gerar Relatório</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aluno (CORRIGIDO) -->
<div class="modal fade" id="studentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Selecionar Aluno</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Buscar Aluno</label>
                    <input type="text" class="form-control" id="studentSearch" placeholder="Digite o nome...">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Aluno</label>
                    <select class="form-select" id="studentSelect" required disabled>
                        <option value="">Primeiro busque...</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="goToStudentBtn" disabled>Gerar Histórico</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Período -->
<div class="modal fade" id="periodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Selecionar Período</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Ano Letivo</label>
                    <select class="form-select" id="periodYearSelect" required>
                        <option value="">Selecione...</option>
                        <?php if (!empty($academicYears)): ?>
                            <?php foreach ($academicYears as $year): ?>
                                <option value="<?= $year->id ?>"><?= $year->year_name ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Semestre/Trimestre</label>
                    <select class="form-select" id="modalSemesterSelect" required disabled>
                        <option value="">Selecione primeiro o ano...</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-info" id="goToPeriodBtn" disabled>Gerar Relatório</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // ===== MODAL TURMA =====
    // Carregar turmas por ano
    $('#classYearSelect').change(function() {
        const yearId = $(this).val();
        const $classSelect = $('#modalClassSelect');
        
        if (yearId) {
            $.get('<?= site_url('admin/classes/get-by-year/') ?>' + yearId, function(classes) {
                $classSelect.empty().append('<option value="">Selecione uma turma...</option>');
                $.each(classes, function(i, cls) {
                    $classSelect.append('<option value="' + cls.id + '">' + cls.class_name + ' (' + cls.class_code + ')</option>');
                });
                $classSelect.prop('disabled', false);
            });
        } else {
            $classSelect.empty().append('<option value="">Selecione primeiro o ano...</option>').prop('disabled', true);
            $('#goToClassBtn').prop('disabled', true);
        }
    });
    
    // Habilitar botão quando turma for selecionada
    $('#modalClassSelect').change(function() {
        $('#goToClassBtn').prop('disabled', !$(this).val());
    });
    
    // Redirecionar para a URL correta da turma
    $('#goToClassBtn').click(function() {
        const classId = $('#modalClassSelect').val();
        if (classId) {
            window.location.href = '<?= site_url('admin/grades/class/') ?>' + classId;
        }
    });
    
    // ===== MODAL ALUNO =====
    // Busca de alunos
    let searchTimeout;
    $('#studentSearch').on('keyup', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val();
        
        if (query.length < 3) {
            return;
        }
        
        searchTimeout = setTimeout(() => {
            $.get('<?= site_url('admin/students/search') ?>', { q: query }, function(students) {
                const $select = $('#studentSelect');
                $select.empty().append('<option value="">Selecione um aluno...</option>');
                $.each(students, function(i, student) {
                    $select.append('<option value="' + student.id + '">' + student.first_name + ' ' + student.last_name + ' (' + student.student_number + ')</option>');
                });
                $select.prop('disabled', false);
            });
        }, 500);
    });
    
    // Habilitar botão quando aluno for selecionado
    $('#studentSelect').change(function() {
        $('#goToStudentBtn').prop('disabled', !$(this).val());
    });
    
    // Redirecionar para a URL correta do aluno
    $('#goToStudentBtn').click(function() {
        const studentId = $('#studentSelect').val();
        if (studentId) {
            window.location.href = '<?= site_url('admin/grades/student/') ?>' + studentId;
        }
    });
    
    // ===== MODAL PERÍODO =====
// Carregar semestres por ano
$('#periodYearSelect').change(function() {
    const yearId = $(this).val();
    const $semesterSelect = $('#modalSemesterSelect');
    const $goButton = $('#goToPeriodBtn');
    
    if (yearId) {
        $.get('<?= site_url('admin/academic/semesters/get-by-year/') ?>' + yearId, function(response) {
            $semesterSelect.empty().append('<option value="">Selecione um semestre...</option>');
            
            // Verificar se a resposta tem a estrutura esperada
            const semesters = response.data || response;
            
            $.each(semesters, function(i, sem) {
                $semesterSelect.append('<option value="' + sem.id + '">' + sem.semester_name + '</option>');
            });
            $semesterSelect.prop('disabled', false);
            $goButton.prop('disabled', true); // Desabilitar até selecionar um semestre
        }).fail(function() {
            alert('Erro ao carregar semestres');
            $semesterSelect.empty().append('<option value="">Erro ao carregar...</option>').prop('disabled', true);
        });
    } else {
        $semesterSelect.empty().append('<option value="">Selecione primeiro o ano...</option>').prop('disabled', true);
        $goButton.prop('disabled', true);
    }
});

// Habilitar botão quando semestre for selecionado
$('#modalSemesterSelect').change(function() {
    $('#goToPeriodBtn').prop('disabled', !$(this).val());
});

// Redirecionar para a URL correta com parâmetros
$('#goToPeriodBtn').click(function() {
    const yearId = $('#periodYearSelect').val();
    const semesterId = $('#modalSemesterSelect').val();
    
    if (yearId && semesterId) {
        // Construir URL com parâmetros
        window.location.href = '<?= site_url('admin/grades/period') ?>?academic_year=' + yearId + '&semester=' + semesterId;
    }
});
});
</script>
<?= $this->endSection() ?>