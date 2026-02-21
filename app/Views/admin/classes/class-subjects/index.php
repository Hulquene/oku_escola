<!-- No cabeçalho da tabela, adicione uma nova coluna de ações em grupo -->
<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">Gerencie as disciplinas atribuídas a cada turma</p>
        </div>
        <div>
            <!-- Botão para atribuição em massa de professores -->
            <?php if (isset($selectedClass) && $selectedClass): ?>
                <a href="<?= site_url('admin/classes/class-subjects/assign-teachers/' . $selectedClass) ?>" 
                   class="btn btn-success me-2"
                   title="Atribuir professores a todas as disciplinas">
                    <i class="fas fa-chalkboard-teacher me-1"></i> Atribuir Professores
                </a>
            <?php endif; ?>
            <a href="<?= site_url('admin/classes/class-subjects/assign?class=' . ($selectedClass ?? '')) ?>" 
               class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Nova Alocação
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">
                <i class="fas fa-home me-1"></i>Dashboard
            </a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes') ?>">Classes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Disciplinas por Turma</li>
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
                        <h6 class="text-white-50 mb-1">Total Atribuições</h6>
                        <h2 class="mb-0"><?= $totalAssignments ?? count($assignments) ?></h2>
                        <small>Disciplinas atribuídas</small>
                    </div>
                    <i class="fas fa-link fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Com Professor</h6>
                        <h2 class="mb-0"><?= $withTeacherCount ?? 0 ?></h2>
                        <small>Disciplinas com professor</small>
                    </div>
                    <i class="fas fa-chalkboard-teacher fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Sem Professor</h6>
                        <h2 class="mb-0"><?= $withoutTeacherCount ?? 0 ?></h2>
                        <small>Pendentes de atribuição</small>
                    </div>
                    <i class="fas fa-user-slash fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Turmas</h6>
                        <h2 class="mb-0"><?= $totalClasses ?? 0 ?></h2>
                        <small>Com disciplinas</small>
                    </div>
                    <i class="fas fa-school fa-3x text-white-50"></i>
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
            <div class="col-md-3">
                <label for="academic_year" class="form-label fw-semibold">Ano Letivo</label>
                <select class="form-select" id="academic_year" name="academic_year">
                    <option value="">Todos os anos</option>
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <?php 
                            // Verificar se é o ano atual OU se está selecionado
                            $isSelected = ($selectedYear == $year->id) || 
                                         (empty($selectedYear) && !empty($year->is_current) && $year->is_current == 1);
                            ?>
                            <option value="<?= $year->id ?>" <?= $isSelected ? 'selected' : '' ?>>
                                <?= $year->year_name ?> <?= !empty($year->is_current) && $year->is_current ? '(Atual)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="class_id" class="form-label fw-semibold">Turma</label>
                <select class="form-select" id="class_id" name="class">
                    <option value="">Todas as turmas</option>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class->id ?>" <?= $selectedClass == $class->id ? 'selected' : '' ?>>
                                <?= $class->class_name ?> (<?= $class->class_code ?>) - <?= $class->class_shift ?> - <?= $class->year_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- FILTRO: Curso -->
           <div class="col-md-2">
                <label for="course" class="form-label fw-semibold">Curso</label>
                <select class="form-select" id="course" name="course">
                    <option value="">Todos</option>
                    <option value="0" <?= ($selectedCourse === '0' || $selectedCourse === 0) ? 'selected' : '' ?>>Ensino Geral</option>
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course->id ?>" <?= ($selectedCourse == $course->id) ? 'selected' : '' ?>>
                                <?= $course->course_name ?> (<?= $course->course_code ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- FILTRO: Disciplina -->
            <div class="col-md-2">
                <label for="discipline" class="form-label fw-semibold">Disciplina</label>
                <select class="form-select" id="discipline" name="discipline">
                    <option value="">Todas</option>
                    <?php if (!empty($disciplines)): ?>
                        <?php foreach ($disciplines as $discipline): ?>
                            <option value="<?= $discipline->id ?>" <?= $selectedDiscipline == $discipline->id ? 'selected' : '' ?>>
                                <?= $discipline->discipline_name ?> (<?= $discipline->discipline_code ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- FILTRO: Professor -->
            <div class="col-md-2">
                <label for="teacher" class="form-label fw-semibold">Professor</label>
                <select class="form-select" id="teacher" name="teacher">
                    <option value="">Todos</option>
                    <option value="without" <?= $selectedTeacher == 'without' ? 'selected' : '' ?>>Sem professor</option>
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
                <a href="<?= site_url('admin/classes/class-subjects') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo me-1"></i>Limpar Filtros
                </a>
            </div>
        </form>
        
        <!-- Badges de filtros ativos -->
        <?php 
        $hasActiveFilters = !empty($selectedYear) || !empty($selectedClass) || 
                            (!empty($selectedCourse) && $selectedCourse != '') || 
                            !empty($selectedDiscipline) || !empty($selectedTeacher);
        ?>
        <?php if ($hasActiveFilters): ?>
            <div class="mt-3">
                <hr>
                <div class="d-flex flex-wrap gap-2">
                    <span class="text-muted me-2"><i class="fas fa-filter"></i> Filtros ativos:</span>
                    
                    <?php if (!empty($selectedYear)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <?php if ($year->id == $selectedYear): ?>
                                <span class="badge bg-primary p-2">
                                    <i class="fas fa-calendar me-1"></i>Ano: <?= $year->year_name ?>
                                    <a href="<?= site_url('admin/classes/class-subjects?remove=year') ?>" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($selectedClass)): ?>
                        <?php foreach ($classes as $class): ?>
                            <?php if ($class->id == $selectedClass): ?>
                                <span class="badge bg-success p-2">
                                    <i class="fas fa-school me-1"></i>Turma: <?= $class->class_name ?>
                                    <a href="<?= site_url('admin/classes/class-subjects?remove=class') ?>" class="text-white ms-1">
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
                                    <a href="<?= site_url('admin/classes/class-subjects?remove=course') ?>" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($selectedDiscipline)): ?>
                        <?php foreach ($disciplines as $discipline): ?>
                            <?php if ($discipline->id == $selectedDiscipline): ?>
                                <span class="badge bg-warning text-dark p-2">
                                    <i class="fas fa-book me-1"></i>Disciplina: <?= $discipline->discipline_name ?>
                                    <a href="<?= site_url('admin/classes/class-subjects?remove=discipline') ?>" class="text-dark ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($selectedTeacher)): ?>
                        <?php if ($selectedTeacher == 'without'): ?>
                            <span class="badge bg-danger p-2">
                                <i class="fas fa-user-slash me-1"></i>Sem professor
                                <a href="<?= site_url('admin/classes/class-subjects?remove=teacher') ?>" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        <?php else: ?>
                            <?php foreach ($teachers as $teacher): ?>
                                <?php if ($teacher->id == $selectedTeacher): ?>
                                    <span class="badge bg-secondary p-2">
                                        <i class="fas fa-chalkboard-teacher me-1"></i>Professor: <?= $teacher->first_name ?>
                                        <a href="<?= site_url('admin/classes/class-subjects?remove=teacher') ?>" class="text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
                Disciplinas Atribuídas por Turma
                <span class="badge bg-secondary ms-2"><?= $totalFiltered ?? count($assignments) ?> registros</span>
            </h5>
            <div>
                <button class="btn btn-sm btn-outline-secondary me-2" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>Imprimir
                </button>
                <a href="<?= site_url('admin/classes/class-subjects/export?' . $_SERVER['QUERY_STRING']) ?>" 
                   class="btn btn-sm btn-outline-success me-2">
                    <i class="fas fa-file-excel me-1"></i>Exportar
                </a>
                
                <!-- Botão de atribuição em massa dentro do card também -->
                <?php if (isset($selectedClass) && $selectedClass): ?>
                    <a href="<?= site_url('admin/classes/class-subjects/assign-teachers/' . $selectedClass) ?>" 
                       class="btn btn-sm btn-success">
                        <i class="fas fa-chalkboard-teacher me-1"></i>Atribuir Professores em Massa
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($assignments)): ?>
            <?php 
            $currentClass = null;
            $classTotals = [];
            
            // Agrupar por turma para mostrar totais
            foreach ($assignments as $assignment):
                if (!isset($classTotals[$assignment->class_id])) {
                    $classTotals[$assignment->class_id] = [
                        'total' => 0,
                        'with_teacher' => 0,
                        'without_semester' => 0
                    ];
                }
                $classTotals[$assignment->class_id]['total']++;
                if ($assignment->teacher_id) {
                    $classTotals[$assignment->class_id]['with_teacher']++;
                }
                if (empty($assignment->semester_id)) {
                    $classTotals[$assignment->class_id]['without_semester']++;
                }
            endforeach;
            
            foreach ($assignments as $assignment): 
                if ($currentClass != $assignment->class_name):
                    if ($currentClass !== null) echo '</tbody></table>';
                    $currentClass = $assignment->class_name;
                    $classTotal = $classTotals[$assignment->class_id] ?? ['total' => 0, 'with_teacher' => 0, 'without_semester' => 0];
            ?>
                <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                    <div>
                        <h5>
                            <span class="badge bg-primary p-2">
                                <i class="fas fa-school me-1"></i> <?= $assignment->class_name ?> 
                                (<?= $assignment->class_code ?>) - <?= $assignment->class_shift ?>
                            </span>
                        </h5>
                        <small class="text-muted">
                            <i class="fas fa-check-circle text-success me-1"></i><?= $classTotal['with_teacher'] ?> com professor |
                            <i class="fas fa-clock text-warning me-1"></i><?= $classTotal['total'] - $classTotal['with_teacher'] ?> pendentes |
                            <?php if ($classTotal['without_semester'] > 0): ?>
                                <i class="fas fa-exclamation-triangle text-danger me-1"></i><span class="text-danger"><?= $classTotal['without_semester'] ?> sem período</span>
                            <?php endif; ?>
                        </small>
                    </div>
                    <div>
                        <a href="<?= site_url('admin/classes/class-subjects/assign-teachers/' . $assignment->class_id) ?>" 
                           class="btn btn-sm btn-success me-2">
                            <i class="fas fa-chalkboard-teacher me-1"></i>Atribuir Professores
                        </a>
                        <a href="<?= site_url('admin/classes/class-subjects/assign?class=' . $assignment->class_id) ?>" 
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-plus-circle me-1"></i>Nova Disciplina
                        </a>
                    </div>
                </div>
                <table class="table table-striped table-hover mb-4">
                    <thead class="table-light">
                        <tr>
                            <th>Disciplina</th>
                            <th>Código</th>
                            <th>Professor</th>
                            <th>Carga Horária</th>
                            <th>Semestre/Período</th>
                            <th>Status</th>
                            <th width="120">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
            <?php endif; ?>
                        <?php
                        // Determinar classe CSS baseada no semestre
                        $rowClass = '';
                        $semesterDisplay = '';
                        
                        if (empty($assignment->semester_id)) {
                            $rowClass = 'table-warning'; // Amarelo claro para destacar
                            $semesterDisplay = '<span class="badge bg-warning text-dark" title="Período não definido">
                                <i class="fas fa-exclamation-triangle me-1"></i>Não definido
                            </span>';
                        } elseif (!empty($assignment->semester_name)) {
                            if (strpos($assignment->semester_name, '1º') !== false) {
                                $semesterDisplay = '<span class="badge bg-primary"><i class="fas fa-sun me-1"></i>' . $assignment->semester_name . '</span>';
                            } elseif (strpos($assignment->semester_name, '2º') !== false) {
                                $semesterDisplay = '<span class="badge bg-warning text-dark"><i class="fas fa-cloud-sun me-1"></i>' . $assignment->semester_name . '</span>';
                            } elseif (strpos($assignment->semester_name, '3º') !== false) {
                                $semesterDisplay = '<span class="badge bg-info"><i class="fas fa-cloud-rain me-1"></i>' . $assignment->semester_name . '</span>';
                            } else {
                                $semesterDisplay = '<span class="badge bg-secondary">' . $assignment->semester_name . '</span>';
                            }
                        } else {
                            $semesterDisplay = '<span class="badge bg-success"><i class="fas fa-calendar-alt me-1"></i>Anual</span>';
                        }
                        ?>
                        <tr class="<?= $rowClass ?>" data-semester-id="<?= $assignment->semester_id ?? '' ?>">
                            <td><strong><?= $assignment->discipline_name ?></strong></td>
                            <td><span class="badge bg-info"><?= $assignment->discipline_code ?></span></td>
                            <td>
                                <?php if ($assignment->teacher_id): ?>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success rounded-circle text-white d-flex align-items-center justify-content-center me-2"
                                             style="width: 30px; height: 30px; font-size: 0.8rem;">
                                            <?= strtoupper(substr($assignment->teacher_first_name ?? '', 0, 1) . substr($assignment->teacher_last_name ?? '', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong><?= $assignment->teacher_first_name ?? '' ?> <?= $assignment->teacher_last_name ?? '' ?></strong>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Não atribuído</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($assignment->workload_hours): ?>
                                    <span class="badge bg-secondary"><?= $assignment->workload_hours ?>h</span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $semesterDisplay ?>
                                <?php if (empty($assignment->semester_id)): ?>
                                    <br><small class="text-warning">
                                        <i class="fas fa-info-circle me-1"></i>Configure o período
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($assignment->is_active): ?>
                                    <span class="badge bg-success">Ativa</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= site_url('admin/classes/class-subjects/assign?class=' . $assignment->class_id . '&edit=' . $assignment->id) ?>" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Editar"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($assignment->teacher_id): ?>
                                        <a href="<?= site_url('admin/teachers/view/' . $assignment->teacher_id) ?>" 
                                           class="btn btn-sm btn-outline-success" 
                                           title="Ver Professor"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-user"></i>
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            onclick="showDeleteModal(<?= $assignment->id ?>, '<?= $assignment->discipline_name ?>', '<?= $assignment->class_name ?>')"
                                            title="Remover"
                                            data-bs-toggle="tooltip">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
            <?php endforeach; ?>
                    </tbody>
                </table>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma disciplina atribuída encontrada</h5>
                <p class="text-muted mb-3">
                    <?= isset($selectedClass) ? 'Tente ajustar os filtros de busca' : 'Comece atribuindo disciplinas às turmas' ?>
                </p>
                <?php if (isset($selectedClass) && $selectedClass): ?>
                    <a href="<?= site_url('admin/classes/class-subjects/assign?class=' . $selectedClass) ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Atribuir Primeira Disciplina
                    </a>
                <?php else: ?>
                    <a href="<?= site_url('admin/classes/class-subjects/assign') ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Nova Atribuição
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Paginação -->
    <?php if (!empty($assignments) && isset($pager)): ?>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Mostrando <strong><?= count($assignments) ?></strong> de <strong><?= $totalFiltered ?? $pager->getTotal() ?></strong> registros
                </div>
                <div>
                    <?= $pager->links() ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Remoção de Disciplina
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <h6 class="alert-heading fw-bold">
                        <i class="fas fa-info-circle me-2"></i>
                        Atenção! Esta ação pode causar transtornos no sistema.
                    </h6>
                    <p class="mb-0">Está prestes a remover a disciplina <strong id="disciplineName"></strong> da turma <strong id="className"></strong>.</p>
                </div>
                
                <div class="card mb-3 border-danger">
                    <div class="card-header bg-danger text-white">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Consequências desta ação:
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center">
                                <span class="badge bg-danger me-3">1</span>
                                <div>
                                    <strong>Notas e Avaliações:</strong> Todas as notas associadas a esta disciplina para os alunos da turma serão perdidas.
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <span class="badge bg-danger me-3">2</span>
                                <div>
                                    <strong>Presenças:</strong> O registo de presenças desta disciplina será eliminado.
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <span class="badge bg-danger me-3">3</span>
                                <div>
                                    <strong>Exames:</strong> Qualquer exame ou avaliação agendada para esta disciplina será cancelado.
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <span class="badge bg-danger me-3">4</span>
                                <div>
                                    <strong>Histórico Académico:</strong> O histórico dos alunos pode ficar inconsistente.
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <span class="badge bg-danger me-3">5</span>
                                <div>
                                    <strong>Alocação de Professor:</strong> O professor associado a esta disciplina deixará de estar alocado a esta turma.
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Sugestão:</strong> Em vez de remover, considere apenas <strong>desativar</strong> a disciplina. Isto preserva os dados históricos mas impede novos registos.
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="confirmCheck" onchange="toggleDeleteButton()">
                    <label class="form-check-label fw-bold" for="confirmCheck">
                        Compreendo as consequências e quero prosseguir com a remoção
                    </label>
                </div>
                
                <div class="alert alert-secondary">
                    <small>
                        <i class="fas fa-clock me-1"></i>
                        Esta ação é <strong>irreversível</strong> e afetará todos os dados relacionados.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger disabled">
                    <i class="fas fa-trash me-1"></i>Remover Permanentemente
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
    
    // Auto-submit dos filtros
    let filterTimeout;
    $('#academic_year, #class_id, #course, #discipline, #teacher').on('change', function() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            $(this).closest('form').submit();
        }, 500);
    });
    
    // Destacar linhas com semestre não definido
    highlightMissingSemester();
});

// Função para destacar linhas com semestre não definido
function highlightMissingSemester() {
    $('tr[data-semester-id=""]').each(function() {
        // Adicionar tooltip à célula do período
        const periodCell = $(this).find('td:nth-child(5)');
        periodCell.find('small.text-warning').css('font-weight', 'bold');
        
        // Adicionar borda esquerda colorida para destacar
        $(this).css('border-left', '3px solid #ffc107');
    });
}

// Variável para armazenar o ID da disciplina a ser removida
let deleteId = null;

// Função para mostrar o modal de confirmação
function showDeleteModal(id, disciplineName, className) {
    deleteId = id;
    
    // Preencher os dados no modal
    document.getElementById('disciplineName').textContent = disciplineName;
    document.getElementById('className').textContent = className;
    
    // Resetar o checkbox e o botão
    document.getElementById('confirmCheck').checked = false;
    document.getElementById('confirmDeleteBtn').classList.add('disabled');
    
    // Mostrar o modal
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Função para habilitar/desabilitar o botão de confirmação
function toggleDeleteButton() {
    const isChecked = document.getElementById('confirmCheck').checked;
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    
    if (isChecked) {
        deleteBtn.classList.remove('disabled');
    } else {
        deleteBtn.classList.add('disabled');
    }
}

// Configurar o botão de confirmação quando o modal for aberto
document.getElementById('confirmDeleteBtn').addEventListener('click', function(e) {
    e.preventDefault();
    
    if (!this.classList.contains('disabled') && deleteId) {
        // Adicionar efeito de loading
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Removendo...';
        
        // Redirecionar para a URL de exclusão
        window.location.href = '<?= site_url('admin/classes/class-subjects/delete/') ?>' + deleteId;
    }
});

// Adicionar atalhos de teclado
document.addEventListener('keydown', function(e) {
    // Ctrl + F para focar no filtro de busca
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        document.getElementById('class_id').focus();
    }
    
    // Esc para limpar filtros
    if (e.key === 'Escape') {
        window.location.href = '<?= site_url('admin/classes/class-subjects') ?>';
    }
});
</script>

<style>
/* Estilos adicionais para o modal */
.modal-lg {
    max-width: 700px;
}

.list-group-item {
    border-left: none;
    border-right: none;
    padding: 1rem 1rem;
}

.list-group-item .badge {
    font-size: 1rem;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.disabled {
    pointer-events: none;
    opacity: 0.65;
}

#confirmDeleteBtn:not(.disabled):hover {
    background-color: #dc3545;
    border-color: #dc3545;
    transform: scale(1.02);
    transition: all 0.2s;
}

.modal-header.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #bb2d3b 100%);
}

/* Estilo para as badges de status */
.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}

/* Avatar do professor */
.bg-success.rounded-circle {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
}

/* Animações */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.3s ease-out;
}

/* Estilo para linhas com semestre não definido */
tr[data-semester-id=""] {
    border-left: 3px solid #ffc107 !important;
}

tr[data-semester-id=""] td:first-child {
    position: relative;
}

tr[data-semester-id=""] td:first-child::before {
    content: "⚠️";
    position: absolute;
    left: -8px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    opacity: 0.8;
}

/* Hover effect para linhas com problema */
tr[data-semester-id=""]:hover {
    background-color: #fff3cd !important;
    transition: background-color 0.2s;
}
</style>
<?= $this->endSection() ?>