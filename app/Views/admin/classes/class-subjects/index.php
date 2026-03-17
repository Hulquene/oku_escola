<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-book-open me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/classes') ?>">Classes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Disciplinas por Turma</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <?php if (isset($selectedClass) && $selectedClass): ?>
                <a href="<?= site_url('admin/classes/class-subjects/assign-teachers/' . $selectedClass) ?>" 
                   class="hdr-btn success"
                   title="Atribuir professores a todas as disciplinas">
                    <i class="fas fa-chalkboard-teacher me-1"></i> Atribuir Professores
                </a>
            <?php endif; ?>
            <a href="<?= site_url('admin/classes/class-subjects/assign?class=' . ($selectedClass ?? '')) ?>" 
               class="hdr-btn primary">
                <i class="fas fa-plus-circle me-1"></i> Nova Alocação
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Stats Cards com estilo do sistema -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-link"></i>
        </div>
        <div>
            <div class="stat-label">Total Atribuições</div>
            <div class="stat-value"><?= $totalAssignments ?></div>
            <div class="stat-sub">Disciplinas atribuídas</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div>
            <div class="stat-label">Com Professor</div>
            <div class="stat-value"><?= $withTeacherCount ?></div>
            <div class="stat-sub">ID da tabela teachers</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-user-slash"></i>
        </div>
        <div>
            <div class="stat-label">Sem Professor</div>
            <div class="stat-value"><?= $withoutTeacherCount ?></div>
            <div class="stat-sub">Pendentes</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon navy">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div>
            <div class="stat-label">Por Período</div>
            <div class="stat-sub">
                <span class="badge-ci success me-1">A: <?= $periodStats['Anual'] ?></span>
                <span class="badge-ci primary me-1">1º: <?= $periodStats['1º Semestre'] ?></span>
                <span class="badge-ci warning">2º: <?= $periodStats['2º Semestre'] ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Filtros Avançados com estilo do sistema -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-filter"></i>
            <span>Filtros Avançados</span>
        </div>
    </div>
    <div class="ci-card-body">
        <form method="get" class="filter-grid">
            <div>
                <label class="filter-label">Ano Letivo</label>
                <select class="filter-select" id="academic_year" name="academic_year">
                    <option value="">Todos os anos</option>
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <?php 
                            $isSelected = ($selectedYear == $year['id']) || 
                                         (empty($selectedYear) && !empty($year['id'] == current_academic_year()) && $year['id'] == current_academic_year());
                            ?>
                            <option value="<?= $year['id'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                                <?= $year['year_name'] ?> <?= !empty($year['id'] == current_academic_year()) && $year['id'] == current_academic_year() ? '(Atual)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div>
                <label class="filter-label">Turma</label>
                <select class="filter-select" id="class_id" name="class">
                    <option value="">Todas as turmas</option>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class['id'] ?>" <?= $selectedClass == $class['id'] ? 'selected' : '' ?>>
                                <?= $class['class_name'] ?> (<?= $class['class_code'] ?>) - <?= $class['class_shift'] ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div>
                <label class="filter-label">Curso</label>
                <select class="filter-select" id="course" name="course">
                    <option value="">Todos</option>
                    <option value="0" <?= ($selectedCourse === '0' || $selectedCourse === 0) ? 'selected' : '' ?>>Ensino Geral</option>
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course['id'] ?>" <?= ($selectedCourse == $course['id']) ? 'selected' : '' ?>>
                                <?= $course['course_name'] ?> (<?= $course['course_code'] ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div>
                <label class="filter-label">Disciplina</label>
                <select class="filter-select" id="discipline" name="discipline">
                    <option value="">Todas</option>
                    <?php if (!empty($disciplines)): ?>
                        <?php foreach ($disciplines as $discipline): ?>
                            <option value="<?= $discipline['id'] ?>" <?= $selectedDiscipline == $discipline['id'] ? 'selected' : '' ?>>
                                <?= $discipline['discipline_name'] ?> (<?= $discipline['discipline_code'] ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div>
                <label class="filter-label">Professor</label>
                <select class="filter-select" id="teacher" name="teacher">
                    <option value="">Todos</option>
                    <option value="without" <?= $selectedTeacher == 'without' ? 'selected' : '' ?>>Sem professor</option>
                    <?php if (!empty($teachers)): ?>
                        <?php foreach ($teachers as $teacher): ?>
                            <option value="<?= $teacher['teacher_id'] ?>" <?= $selectedTeacher == $teacher['teacher_id'] ? 'selected' : '' ?>>
                                <?= $teacher['first_name'] ?> <?= $teacher['last_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="filter-actions" style="grid-column: -1 / 1;">
                <button type="submit" class="btn-filter apply">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/classes/class-subjects') ?>" class="btn-filter clear">
                    <i class="fas fa-undo"></i> Limpar Filtros
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
            <div class="mt-3 pt-3" style="border-top: 1px solid var(--border);">
                <div class="d-flex flex-wrap gap-2">
                    <span class="text-muted me-2"><i class="fas fa-filter"></i> Filtros ativos:</span>
                    
                    <?php if (!empty($selectedYear)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <?php if ($year['id'] == $selectedYear): ?>
                                <span class="badge-ci primary p-2">
                                    <i class="fas fa-calendar me-1"></i>Ano: <?= $year['year_name'] ?>
                                    <a href="<?= site_url('admin/classes/class-subjects?remove=year') ?>" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($selectedClass)): ?>
                        <?php foreach ($classes as $class): ?>
                            <?php if ($class['id'] == $selectedClass): ?>
                                <span class="badge-ci success p-2">
                                    <i class="fas fa-school me-1"></i>Turma: <?= $class['class_name'] ?>
                                    <a href="<?= site_url('admin/classes/class-subjects?remove=class') ?>" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($selectedCourse) && $selectedCourse != 0): ?>
                        <?php foreach ($courses as $course): ?>
                            <?php if ($course['id'] == $selectedCourse): ?>
                                <span class="badge-ci info p-2">
                                    <i class="fas fa-graduation-cap me-1"></i>Curso: <?= $course['course_name'] ?>
                                    <a href="<?= site_url('admin/classes/class-subjects?remove=course') ?>" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($selectedDiscipline)): ?>
                        <?php foreach ($disciplines as $discipline): ?>
                            <?php if ($discipline['id'] == $selectedDiscipline): ?>
                                <span class="badge-ci warning p-2" style="color: var(--primary);">
                                    <i class="fas fa-book me-1"></i>Disciplina: <?= $discipline['discipline_name'] ?>
                                    <a href="<?= site_url('admin/classes/class-subjects?remove=discipline') ?>" class="text-dark ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($selectedTeacher)): ?>
                        <?php if ($selectedTeacher == 'without'): ?>
                            <span class="badge-ci danger p-2">
                                <i class="fas fa-user-slash me-1"></i>Sem professor
                                <a href="<?= site_url('admin/classes/class-subjects?remove=teacher') ?>" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        <?php else: ?>
                            <?php foreach ($teachers as $teacher): ?>
                                <?php if ($teacher['teacher_id'] == $selectedTeacher): ?>
                                    <span class="badge-ci secondary p-2">
                                        <i class="fas fa-chalkboard-teacher me-1"></i>Professor: <?= $teacher['first_name'] ?>
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

<!-- Data Table com estilo do sistema -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-list"></i>
            <span>Disciplinas Atribuídas por Turma</span>
        </div>
        <div class="d-flex gap-2">
            <span class="badge-ci primary">
                <i class="fas fa-list me-1"></i> <?= $totalFiltered ?? count($assignments) ?> registros
            </span>
            <button class="hdr-btn secondary" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Imprimir
            </button>
            <a href="<?= site_url('admin/classes/class-subjects/export?' . $_SERVER['QUERY_STRING']) ?>" 
               class="hdr-btn secondary">
                <i class="fas fa-file-excel me-1"></i>Exportar
            </a>
            
            <?php if (isset($selectedClass) && $selectedClass): ?>
                <a href="<?= site_url('admin/classes/class-subjects/assign-teachers/' . $selectedClass) ?>" 
                   class="hdr-btn success">
                    <i class="fas fa-chalkboard-teacher me-1"></i>Atribuir Professores em Massa
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="ci-card-body p0">
        <?php if (!empty($assignments)): ?>
            <?php 
            $currentClass = null;
            $classTotals = [];
            
            // Agrupar por turma para mostrar totais
            foreach ($assignments as $assignment):
                if (!isset($classTotals[$assignment['class_id']])) {
                    $classTotals[$assignment['class_id']] = [
                        'total' => 0,
                        'with_teacher' => 0,
                        'without_period' => 0
                    ];
                }
                $classTotals[$assignment['class_id']]['total']++;
                if ($assignment['teacher_id']) {
                    $classTotals[$assignment['class_id']]['with_teacher']++;
                }
                if (empty($assignment['period_type']) || !in_array($assignment['period_type'], ['Anual', '1º Semestre', '2º Semestre'])) {
                    $classTotals[$assignment['class_id']]['without_period']++;
                }
            endforeach;
            
            // Loop principal usando sintaxe padrão
            foreach ($assignments as $assignment): 
                if ($currentClass != $assignment['class_name']):
                    if ($currentClass !== null) {
                        echo '</tbody></table>';
                    }
                    $currentClass = $assignment['class_name'];
                    $classTotal = $classTotals[$assignment['class_id']] ?? ['total' => 0, 'with_teacher' => 0, 'without_period' => 0];
            ?>
                <div class="d-flex justify-content-between align-items-center mt-4 mb-3 px-4">
                    <div>
                        <h5 class="mb-0">
                            <span class="badge-ci primary p-2" style="font-size: 0.9rem;">
                                <i class="fas fa-school me-1"></i> <?= $assignment['class_name'] ?> 
                                (<?= $assignment['class_code'] ?>) - <?= $assignment['class_shift'] ?>
                            </span>
                        </h5>
                        <small class="text-muted d-block mt-1">
                            <span class="text-success me-2"><i class="fas fa-check-circle"></i> <?= $classTotal['with_teacher'] ?> com professor</span>
                            <span class="text-warning me-2"><i class="fas fa-clock"></i> <?= $classTotal['total'] - $classTotal['with_teacher'] ?> pendentes</span>
                            <?php if ($classTotal['without_period'] > 0): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?= $classTotal['without_period'] ?> sem período</span>
                            <?php endif; ?>
                        </small>
                    </div>
                    <div>
                        <a href="<?= site_url('admin/classes/class-subjects/assign-teachers/' . $assignment['class_id']) ?>" 
                           class="hdr-btn success me-2">
                            <i class="fas fa-chalkboard-teacher me-1"></i>Atribuir Professores
                        </a>
                        <a href="<?= site_url('admin/classes/class-subjects/assign?class=' . $assignment['class_id']) ?>" 
                           class="hdr-btn primary">
                            <i class="fas fa-plus-circle me-1"></i>Nova Disciplina
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="ci-table">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Turma</th>
                                <th>Disciplina</th>
                                <th>Código</th>
                                <th>Período</th>
                                <th class="center">Carga Horária</th>
                                <th>Professor</th>
                                <th class="center">Status</th>
                                <th class="center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
            <?php 
                endif; 
                
                // Para cada disciplina da turma atual
                $periodColors = [
                    'Anual' => 'success',
                    '1º Semestre' => 'primary',
                    '2º Semestre' => 'warning'
                ];
                $periodColor = $periodColors[$assignment['period_type']] ?? 'secondary';
                
                $teacherStatus = $assignment['teacher_id'] ? 'Atribuído' : 'Pendente';
                $teacherStatusClass = $assignment['teacher_id'] ? 'success' : 'warning';
            ?>
                <tr>
                    <td><?= $assignment['created_at'] ? date('d/m/Y', strtotime($assignment['created_at'])) : '-' ?></td>
                    <td>
                        <span class="fw-semibold"><?= $assignment['class_name'] ?></span>
                        <br>
                        <small class="text-muted"><?= $assignment['class_code'] ?> - <?= $assignment['class_shift'] ?></small>
                    </td>
                    <td>
                        <strong><?= $assignment['discipline_name'] ?></strong>
                        <?php if ($assignment['course_name']): ?>
                            <br><small class="text-muted"><?= $assignment['course_name'] ?></small>
                        <?php endif; ?>
                    </td>
                    <td><span class="code-badge"><?= $assignment['discipline_code'] ?></span></td>
                    <td>
                        <span class="badge-ci <?= $periodColor ?>">
                            <?= $assignment['period_type'] ?? 'Não definido' ?>
                        </span>
                    </td>
                    <td class="center">
                        <?php if ($assignment['workload_hours']): ?>
                            <span class="num-chip"><?= $assignment['workload_hours'] ?>h</span>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($assignment['teacher_id']): ?>
                            <div class="d-flex align-items-center">
                                <div class="teacher-initials me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                    <?= strtoupper(substr($assignment['teacher_first_name'] ?? '', 0, 1) . substr($assignment['teacher_last_name'] ?? '', 0, 1)) ?>
                                </div>
                                <div>
                                    <strong><?= $assignment['teacher_first_name'] ?> <?= $assignment['teacher_last_name'] ?></strong>
                                    <br>
                                    <small class="text-muted">ID: <?= $assignment['teacher_id'] ?></small>
                                </div>
                            </div>
                        <?php else: ?>
                            <span class="badge-ci warning">Não atribuído</span>
                        <?php endif; ?>
                    </td>
                    <td class="center">
                        <span class="badge-ci <?= $teacherStatusClass ?>"><?= $teacherStatus ?></span>
                    </td>
                    <td class="center">
                        <div class="action-group">
                            <a href="<?= site_url('admin/classes/class-subjects/assign?edit=' . $assignment['id']) ?>" 
                               class="row-btn edit" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($assignment['teacher_id']): ?>
                                <a href="<?= site_url('admin/teachers/view/' . $assignment['teacher_id']) ?>" 
                                   class="row-btn view" title="Ver Professor">
                                    <i class="fas fa-user"></i>
                                </a>
                            <?php endif; ?>
                            <button type="button" class="row-btn del" 
                                    onclick="showDeleteModal(<?= $assignment['id'] ?>, '<?= $assignment['discipline_name'] ?>', '<?= $assignment['class_name'] ?>')"
                                    title="Remover">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <h5>Nenhuma disciplina atribuída encontrada</h5>
                <p class="text-muted mb-3">
                    <?= isset($selectedClass) ? 'Tente ajustar os filtros de busca' : 'Comece atribuindo disciplinas às turmas' ?>
                </p>
                <?php if (isset($selectedClass) && $selectedClass): ?>
                    <a href="<?= site_url('admin/classes/class-subjects/assign?class=' . $selectedClass) ?>" class="btn-ci primary">
                        <i class="fas fa-plus-circle me-2"></i>Atribuir Primeira Disciplina
                    </a>
                <?php else: ?>
                    <a href="<?= site_url('admin/classes/class-subjects/assign') ?>" class="btn-ci primary">
                        <i class="fas fa-plus-circle me-2"></i>Nova Atribuição
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($assignments) && isset($pager)): ?>
        <div class="ci-card-footer">
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

<!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO com estilo do sistema -->
<div class="modal fade ci-modal" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header danger-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Remoção de Disciplina
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert-ci warning mb-3">
                    <h6 class="alert-heading fw-bold">
                        <i class="fas fa-info-circle me-2"></i>
                        Atenção! Esta ação pode causar transtornos no sistema.
                    </h6>
                    <p class="mb-0">Está prestes a remover a disciplina <strong id="disciplineName"></strong> da turma <strong id="className"></strong>.</p>
                </div>
                
                <div class="ci-card mb-3" style="border-color: var(--danger);">
                    <div class="ci-card-header" style="background: var(--danger); color: #fff;">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Consequências desta ação:
                    </div>
                    <div class="ci-card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2 d-flex align-items-center">
                                <span class="badge-ci danger me-3" style="width: 30px; height: 30px; border-radius: 50%;">1</span>
                                <div><strong>Notas e Avaliações:</strong> Todas as notas associadas a esta disciplina para os alunos da turma serão perdidas.</div>
                            </li>
                            <li class="mb-2 d-flex align-items-center">
                                <span class="badge-ci danger me-3" style="width: 30px; height: 30px; border-radius: 50%;">2</span>
                                <div><strong>Presenças:</strong> O registo de presenças desta disciplina será eliminado.</div>
                            </li>
                            <li class="mb-2 d-flex align-items-center">
                                <span class="badge-ci danger me-3" style="width: 30px; height: 30px; border-radius: 50%;">3</span>
                                <div><strong>Exames:</strong> Qualquer exame ou avaliação agendada para esta disciplina será cancelado.</div>
                            </li>
                            <li class="mb-2 d-flex align-items-center">
                                <span class="badge-ci danger me-3" style="width: 30px; height: 30px; border-radius: 50%;">4</span>
                                <div><strong>Histórico Académico:</strong> O histórico dos alunos pode ficar inconsistente.</div>
                            </li>
                            <li class="mb-2 d-flex align-items-center">
                                <span class="badge-ci danger me-3" style="width: 30px; height: 30px; border-radius: 50%;">5</span>
                                <div><strong>Alocação de Professor:</strong> O professor associado a esta disciplina deixará de estar alocado a esta turma.</div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="alert-ci info mb-3">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Sugestão:</strong> Em vez de remover, considere apenas <strong>desativar</strong> a disciplina. Isto preserva os dados históricos mas impede novos registos.
                </div>
                
                <div class="toggle-row" id="confirmToggle" style="cursor: pointer;">
                    <div class="tl-info">
                        <i class="fas fa-check-circle me-2"></i>
                        Compreendo as consequências e quero prosseguir com a remoção
                    </div>
                    <div class="ci-switch">
                        <input type="checkbox" id="confirmCheck" onchange="toggleDeleteButton()">
                        <label class="ci-switch-track" for="confirmCheck"></label>
                    </div>
                </div>
                
                <div class="alert-ci secondary mt-3">
                    <small>
                        <i class="fas fa-clock me-1"></i>
                        Esta ação é <strong>irreversível</strong> e afetará todos os dados relacionados.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-filter clear" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <a href="#" id="confirmDeleteBtn" class="btn-filter danger disabled">
                    <i class="fas fa-trash me-2"></i>Remover Permanentemente
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
    
    // Auto-submit nos filtros
    let filterTimeout;
    $('#academic_year, #class_id, #course, #discipline, #teacher').on('change', function() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            $(this).closest('form').submit();
        }, 500);
    });
    
    // Destacar períodos faltantes
    highlightMissingPeriod();
});

function highlightMissingPeriod() {
    $('tr[data-period-type=""]').each(function() {
        const periodCell = $(this).find('td:nth-child(5)');
        periodCell.find('small.text-warning').css('font-weight', 'bold');
        $(this).css('border-left', '3px solid #ffc107');
    });
}

let deleteId = null;

function showDeleteModal(id, disciplineName, className) {
    deleteId = id;
    
    document.getElementById('disciplineName').textContent = disciplineName;
    document.getElementById('className').textContent = className;
    
    document.getElementById('confirmCheck').checked = false;
    document.getElementById('confirmDeleteBtn').classList.add('disabled');
    
    // Atualizar estilo do switch
    $('.ci-switch-track').removeClass('checked');
    
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function toggleDeleteButton() {
    const isChecked = document.getElementById('confirmCheck').checked;
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    
    if (isChecked) {
        deleteBtn.classList.remove('disabled');
        $('.ci-switch-track').addClass('checked');
        $('.toggle-row').addClass('checked');
    } else {
        deleteBtn.classList.add('disabled');
        $('.ci-switch-track').removeClass('checked');
        $('.toggle-row').removeClass('checked');
    }
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function(e) {
    e.preventDefault();
    
    if (!this.classList.contains('disabled') && deleteId) {
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Removendo...';
        window.location.href = '<?= site_url('admin/classes/class-subjects/delete/') ?>' + deleteId;
    }
});

// Atalhos de teclado
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        document.getElementById('class_id').focus();
    }
    
    if (e.key === 'Escape') {
        window.location.href = '<?= site_url('admin/classes/class-subjects') ?>';
    }
});

// Inicializar switches
$(document).ready(function() {
    $('.ci-switch-track').each(function() {
        var input = $(this).prev('input');
        if (input.is(':checked')) {
            $(this).addClass('checked');
        }
    });
    
    $('.toggle-row').on('click', function(e) {
        if (!$(e.target).is('input[type="checkbox"]')) {
            var checkbox = $(this).find('input[type="checkbox"]');
            checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
            toggleDeleteButton();
        }
    });
});
</script>

<style>
/* Estilos adicionais específicos para esta página */
.ci-card-footer {
    padding: 0.85rem 1.25rem;
    background: var(--surface);
    border-top: 1px solid var(--border);
}

.ci-modal .modal-header.danger-header {
    background: var(--danger);
    color: #fff;
}

.btn-filter.danger {
    background: var(--danger);
    color: #fff;
}

.btn-filter.danger:hover:not(.disabled) {
    background: #CC3535;
    color: #fff;
}

.btn-filter.danger.disabled {
    opacity: 0.5;
    pointer-events: none;
}

.num-chip {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 600;
    background: rgba(107,122,153,0.1);
    color: var(--text-secondary);
    padding: 0.15rem 0.45rem;
    border-radius: 5px;
}

.teacher-initials {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: var(--accent);
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.2rem;
    text-transform: uppercase;
}

/* Animação para os cards */
.ci-card {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Estilo para linhas com período faltante */
tr[data-period-type=""] {
    border-left: 3px solid var(--warning) !important;
}

tr[data-period-type=""]:hover {
    background: rgba(232,160,32,0.05) !important;
}

/* Ajustes para badges ativos nos filtros */
.badge-ci a:hover {
    opacity: 0.8;
}
</style>
<?= $this->endSection() ?>