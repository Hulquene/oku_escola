<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">Gerencie todas as turmas da instituição</p>
        </div>
        <div>
            <a href="<?= site_url('admin/classes/export') ?>" class="btn btn-success me-2" title="Exportar lista">
                <i class="fas fa-file-excel"></i> Exportar
            </a>
            <a href="<?= site_url('admin/classes/classes/form-add') ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nova Turma
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">
                <i class="fas fa-home me-1"></i>Dashboard
            </a></li>
            <li class="breadcrumb-item active" aria-current="page">Turmas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total de Turmas</h6>
                        <h2 class="mb-0"><?= $totalClasses ?? 0 ?></h2>
                        <small>Cadastradas no sistema</small>
                    </div>
                    <i class="fas fa-school fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Turmas Ativas</h6>
                        <h2 class="mb-0"><?= $activeClasses ?? 0 ?></h2>
                        <small>Em funcionamento</small>
                    </div>
                    <i class="fas fa-check-circle fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total de Alunos</h6>
                        <h2 class="mb-0"><?= $totalStudents ?? 0 ?></h2>
                        <small>Matriculados</small>
                    </div>
                    <i class="fas fa-users fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Vagas Disponíveis</h6>
                        <h2 class="mb-0"><?= $availableSeats ?? 0 ?></h2>
                        <small>Em todas as turmas</small>
                    </div>
                    <i class="fas fa-chair fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros Avançados -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2 text-primary"></i>
            Filtros Avançados
        </h5>
    </div>
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-2">
                <label for="academic_year" class="form-label fw-semibold">Ano Letivo</label>
                <select class="form-select" id="academic_year" name="academic_year">
                    <option value="">Todos</option>
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= $year->id ?>" <?= $selectedYear == $year->id ? 'selected' : '' ?>>
                                <?= $year->year_name ?> <?= $year->is_current ? '(Atual)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="grade_level" class="form-label fw-semibold">Nível de Ensino</label>
                <select class="form-select" id="grade_level" name="grade_level">
                    <option value="">Todos</option>
                    <?php if (!empty($gradeLevels)): ?>
                        <?php foreach ($gradeLevels as $level): ?>
                            <option value="<?= $level->id ?>" <?= $selectedLevel == $level->id ? 'selected' : '' ?>>
                                <?= $level->level_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- FILTRO: Curso (aparece apenas para Ensino Médio) -->
            <div class="col-md-2" id="courseFilterContainer" style="<?= (isset($selectedLevel) && $selectedLevel >= 13 && $selectedLevel <= 16) ? 'display:block' : 'display:none' ?>">
                <label for="course" class="form-label fw-semibold">Curso</label>
                <select class="form-select" id="course" name="course">
                    <option value="">Todos os cursos</option>
                    <option value="0" <?= ($selectedCourse === '0' || $selectedCourse === 0) ? 'selected' : '' ?>>Ensino Geral</option>
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course->id ?>" <?= $selectedCourse == $course->id ? 'selected' : '' ?>>
                                <?= $course->course_name ?> (<?= $course->course_code ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="shift" class="form-label fw-semibold">Turno</label>
                <select class="form-select" id="shift" name="shift">
                    <option value="">Todos</option>
                    <option value="Manhã" <?= $selectedShift == 'Manhã' ? 'selected' : '' ?>>Manhã</option>
                    <option value="Tarde" <?= $selectedShift == 'Tarde' ? 'selected' : '' ?>>Tarde</option>
                    <option value="Noite" <?= $selectedShift == 'Noite' ? 'selected' : '' ?>>Noite</option>
                    <option value="Integral" <?= $selectedShift == 'Integral' ? 'selected' : '' ?>>Integral</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="status" class="form-label fw-semibold">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="active" <?= $selectedStatus == 'active' ? 'selected' : '' ?>>Ativas</option>
                    <option value="inactive" <?= $selectedStatus == 'inactive' ? 'selected' : '' ?>>Inativas</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="teacher" class="form-label fw-semibold">Professor</label>
                <select class="form-select" id="teacher" name="teacher">
                    <option value="">Todos</option>
                    <?php if (!empty($teachers)): ?>
                        <?php foreach ($teachers as $teacher): ?>
                            <option value="<?= $teacher->id ?>" <?= $selectedTeacher == $teacher->id ? 'selected' : '' ?>>
                                <?= $teacher->first_name ?> <?= $teacher->last_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-12 d-flex align-items-end justify-content-end mt-3">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
                <a href="<?= site_url('admin/classes/classes') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo me-1"></i>Limpar Filtros
                </a>
            </div>
        </form>
        
        <!-- Badges de filtros ativos -->
        <?php if (!empty($selectedYear) || !empty($selectedLevel) || !empty($selectedCourse) || !empty($selectedShift) || !empty($selectedStatus) || !empty($selectedTeacher)): ?>
            <div class="mt-3">
                <hr>
                <div class="d-flex flex-wrap gap-2">
                    <span class="text-muted me-2"><i class="fas fa-filter"></i> Filtros ativos:</span>
                    
                    <?php if (!empty($selectedYear)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <?php if ($year->id == $selectedYear): ?>
                                <span class="badge bg-primary p-2">
                                    <i class="fas fa-calendar me-1"></i>Ano: <?= $year->year_name ?>
                                    <a href="<?= site_url('admin/classes/classes?remove=year') ?>" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($selectedLevel)): ?>
                        <?php foreach ($gradeLevels as $level): ?>
                            <?php if ($level->id == $selectedLevel): ?>
                                <span class="badge bg-success p-2">
                                    <i class="fas fa-layer-group me-1"></i>Nível: <?= $level->level_name ?>
                                    <a href="<?= site_url('admin/classes/classes?remove=level') ?>" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($selectedCourse) && $selectedCourse != 0): ?>
                        <?php foreach ($courses as $course): ?>
                            <?php if ($course->id == $selectedCourse): ?>
                                <span class="badge bg-info p-2">
                                    <i class="fas fa-graduation-cap me-1"></i>Curso: <?= $course->course_name ?>
                                    <a href="<?= site_url('admin/classes/classes?remove=course') ?>" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($selectedShift)): ?>
                        <span class="badge bg-warning text-dark p-2">
                            <i class="fas fa-clock me-1"></i>Turno: <?= $selectedShift ?>
                            <a href="<?= site_url('admin/classes/classes?remove=shift') ?>" class="text-dark ms-1">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                    
                    <?php if (!empty($selectedStatus)): ?>
                        <span class="badge bg-secondary p-2">
                            <i class="fas fa-power-off me-1"></i>Status: <?= $selectedStatus == 'active' ? 'Ativas' : 'Inativas' ?>
                            <a href="<?= site_url('admin/classes/classes?remove=status') ?>" class="text-white ms-1">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2 text-primary"></i>
                Lista de Turmas
                <span class="badge bg-secondary ms-2"><?= $totalFiltered ?? count($classes) ?> registros</span>
            </h5>
            <div>
                <button class="btn btn-sm btn-outline-secondary me-2" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>Imprimir
                </button>
                
                <!-- Botão para atribuição rápida (quando uma turma está selecionada no filtro) -->
                <?php if (isset($selectedClassId) && $selectedClassId): ?>
                    <a href="<?= site_url('admin/classes/class-subjects/assign-teachers/' . $selectedClassId) ?>" 
                       class="btn btn-sm btn-success">
                        <i class="fas fa-chalkboard-teacher me-1"></i>Atribuir Professores
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="classesTable" class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Turma</th>
                        <th>Código</th>
                        <th>Nível</th>
                        <th>Curso</th>
                        <th>Ano Letivo</th>
                        <th>Turno</th>
                        <th>Sala</th>
                        <th>Capacidade</th>
                        <th>Alunos</th>
                        <th>Vagas</th>
                        <th>Professor</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): 
                            // Calcular ocupação
                            $enrolledCount = $class->enrolled_count ?? 0;
                            $occupancyPercentage = $class->capacity > 0 ? round(($enrolledCount / $class->capacity) * 100) : 0;
                            $availableSeats = $class->capacity - $enrolledCount;
                        ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= $class->id ?></span></td>
                                <td>
                                    <strong><?= $class->class_name ?></strong>
                                </td>
                                <td><span class="badge bg-info"><?= $class->class_code ?></span></td>
                                <td><?= $class->level_name ?></td>
                                <td>
                                    <?php if (isset($class->course_name) && $class->course_name): ?>
                                        <span class="badge bg-primary" title="<?= $class->course_type ?? '' ?>">
                                            <?= $class->course_name ?>
                                        </span>
                                        <br>
                                        <small class="text-muted"><?= $class->course_code ?></small>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Ensino Geral</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $class->year_name ?></td>
                                <td><?= $class->class_shift ?></td>
                                <td><?= $class->class_room ?: '-' ?></td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?= $class->capacity ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= $enrolledCount ?></span>
                                </td>
                                <td>
                                    <?php if ($availableSeats > 0): ?>
                                        <span class="badge bg-success"><?= $availableSeats ?> vagas</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Lotada</span>
                                    <?php endif; ?>
                                    <br>
                                    <small class="text-muted"><?= $occupancyPercentage ?>% ocupada</small>
                                </td>
                                <td>
                                    <?php if ($class->class_teacher_id): ?>
                                        <strong><?= $class->teacher_first_name ?> <?= $class->teacher_last_name ?></strong>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Não atribuído</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($class->is_active): ?>
                                        <span class="badge bg-success">Ativa</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inativa</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('admin/classes/classes/view/' . $class->id) ?>" 
                                           class="btn btn-sm btn-outline-success" 
                                           title="Ver Detalhes"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('admin/classes/classes/form-edit/' . $class->id) ?>" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Editar"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= site_url('admin/classes/classes/list-students/' . $class->id) ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Listar Alunos"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-users"></i>
                                        </a>
                                        <a href="<?= site_url('admin/classes/class-subjects?class=' . $class->id) ?>" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Disciplinas"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-book"></i>
                                        </a>
                                        <a href="<?= site_url('admin/classes/class-subjects/assign-teachers/' . $class->id) ?>" 
                                           class="btn btn-sm btn-outline-success" 
                                           title="Atribuir Professores"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-chalkboard-teacher"></i>
                                        </a>
                                        <a href="<?= site_url('admin/classes/schedule/' . $class->id) ?>" 
                                           class="btn btn-sm btn-outline-secondary" 
                                           title="Horário"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-calendar-alt"></i>
                                        </a>
                                        <?php if ($class->is_active): ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDeactivate(<?= $class->id ?>, '<?= $class->class_name ?>')"
                                                    title="Desativar"
                                                    data-bs-toggle="tooltip">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <a href="<?= site_url('admin/classes/classes/activate/' . $class->id) ?>" 
                                               class="btn btn-sm btn-outline-success" 
                                               title="Reativar"
                                               data-bs-toggle="tooltip"
                                               onclick="return confirm('Tem certeza que deseja reativar esta turma?')">
                                                <i class="fas fa-check-circle"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="14" class="text-center py-5">
                                <i class="fas fa-school fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhuma turma encontrada</h5>
                                <p class="text-muted mb-3">
                                    <?= isset($search) ? 'Tente ajustar os filtros de busca' : 'Comece cadastrando a primeira turma' ?>
                                </p>
                                <?php if (!isset($search)): ?>
                                    <a href="<?= site_url('admin/classes/classes/form-add') ?>" class="btn btn-primary">
                                        <i class="fas fa-plus-circle me-2"></i>Criar Nova Turma
                                    </a>
                                <?php else: ?>
                                    <a href="<?= site_url('admin/classes/classes') ?>" class="btn btn-secondary">
                                        <i class="fas fa-undo me-2"></i>Limpar Filtros
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginação com informações -->
        <?php if (!empty($classes) && isset($pager)): ?>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Mostrando <strong><?= count($classes) ?></strong> de <strong><?= $totalFiltered ?? $pager->getTotal() ?></strong> registros
                </div>
                <div>
                    <?= $pager->links() ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Confirmação de Desativação -->
<div class="modal fade" id="deactivateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Desativação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja desativar a turma <strong id="deactivateClassName"></strong>?</p>
                
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Ao desativar uma turma:</strong>
                    <ul class="mt-2 mb-0">
                        <li>Os alunos não poderão ser matriculados nesta turma</li>
                        <li>As disciplinas atribuídas serão desativadas</li>
                        <li>Os dados históricos serão preservados</li>
                        <li>Não será possível lançar novas notas ou presenças</li>
                    </ul>
                </div>
                
                <p class="text-muted small mt-3">
                    <i class="fas fa-clock me-1"></i>
                    Esta ação pode ser revertida posteriormente.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeactivateBtn" class="btn btn-warning">
                    <i class="fas fa-trash me-1"></i>Desativar Turma
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Inicializar DataTable (apenas para ordenação e busca, não para paginação)
    $('#classesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc']],
        pageLength: 25,
        paging: false, // Usar paginação do CI
        info: false,
        columnDefs: [
            { orderable: false, targets: [13] } // Coluna de ações
        ]
    });
});

// Script para mostrar/esconder campo de curso baseado no nível selecionado
document.getElementById('grade_level')?.addEventListener('change', function() {
    const levelId = parseInt(this.value);
    const courseContainer = document.getElementById('courseFilterContainer');
    
    // Verificar se o nível é do Ensino Médio (IDs 13-16)
    if (levelId >= 13 && levelId <= 16) {
        courseContainer.style.display = 'block';
    } else {
        courseContainer.style.display = 'none';
        document.getElementById('course').value = ''; // Limpar seleção
    }
});

// Auto-submit dos filtros
let filterTimeout;
$('#academic_year, #grade_level, #course, #shift, #status, #teacher').on('change', function() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        $(this).closest('form').submit();
    }, 500);
});

// Função para confirmar desativação
function confirmDeactivate(id, name) {
    document.getElementById('deactivateClassName').textContent = name;
    document.getElementById('confirmDeactivateBtn').href = '<?= site_url('admin/classes/classes/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deactivateModal')).show();
}

// Atualizar estatísticas via AJAX (opcional)
function refreshStats() {
    $.get('<?= site_url('admin/classes/get-stats') ?>', function(data) {
        $('#totalClasses').text(data.totalClasses);
        $('#activeClasses').text(data.activeClasses);
        $('#totalStudents').text(data.totalStudents);
        $('#availableSeats').text(data.availableSeats);
    });
}

// Chamar refresh a cada 30 segundos (opcional)
// setInterval(refreshStats, 30000);
</script>

<style>
/* Animações */
.page-header {
    animation: fadeIn 0.3s ease-out;
}

.card {
    transition: all 0.2s;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Estilo para os botões de ação */
.btn-group .btn {
    transition: all 0.2s;
    margin: 0 2px;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    z-index: 10;
}

/* Badges personalizados */
.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}

/* Tabela responsiva */
.table td {
    vertical-align: middle;
}

/* Filtros ativos */
.badge a {
    text-decoration: none;
}

.badge a:hover {
    opacity: 0.8;
}

/* Loading state */
.loading {
    opacity: 0.5;
    pointer-events: none;
}

/* Animações para as linhas da tabela */
@keyframes rowFadeIn {
    from { opacity: 0; transform: translateX(-10px); }
    to { opacity: 1; transform: translateX(0); }
}

tbody tr {
    animation: rowFadeIn 0.2s ease-out;
}
</style>
<?= $this->endSection() ?>