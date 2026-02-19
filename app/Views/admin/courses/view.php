<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/courses') ?>">Cursos</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $course->course_name ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações do Curso -->
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações do Curso</h5>
                <div>
                    <a href="<?= site_url('admin/courses/form-edit/' . $course->id) ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Nome:</th>
                                <td><strong><?= $course->course_name ?></strong></td>
                            </tr>
                            <tr>
                                <th>Código:</th>
                                <td><span class="badge bg-info p-2"><?= $course->course_code ?></span></td>
                            </tr>
                            <tr>
                                <th>Tipo:</th>
                                <td>
                                    <?php
                                    $typeColors = [
                                        'Ciências' => 'primary',
                                        'Humanidades' => 'success',
                                        'Económico-Jurídico' => 'warning',
                                        'Técnico' => 'info',
                                        'Profissional' => 'secondary',
                                        'Outro' => 'dark'
                                    ];
                                    $color = $typeColors[$course->course_type] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $color ?> p-2"><?= $course->course_type ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Duração:</th>
                                <td><?= $course->duration_years ?> anos</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <?php if ($course->is_active): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inativo</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Nível Inicial:</th>
                                <td><?= $course->start_level_name ?></td>
                            </tr>
                            <tr>
                                <th>Nível Final:</th>
                                <td><?= $course->end_level_name ?></td>
                            </tr>
                            <tr>
                                <th>Níveis Abrangidos:</th>
                                <td>
                                    <?php
                                    $gradeLevelModel = new \App\Models\GradeLevelModel();
                                    $levels = $gradeLevelModel
                                        ->where('id >=', $course->start_grade_id)
                                        ->where('id <=', $course->end_grade_id)
                                        ->where('is_active', 1)
                                        ->orderBy('sort_order', 'ASC')
                                        ->findAll();
                                    ?>
                                    <?php foreach ($levels as $level): ?>
                                        <span class="badge bg-secondary me-1 mb-1"><?= $level->level_name ?></span>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Total Disciplinas:</th>
                                <td><span class="badge bg-primary"><?= $totalDisciplines ?></span></td>
                            </tr>
                            <tr>
                                <th>Carga Horária Total:</th>
                                <td><span class="badge bg-success"><?= $totalWorkload ?> horas</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php if ($course->description): ?>
                    <hr>
                    <h6>Descrição:</h6>
                    <p class="text-muted"><?= nl2br($course->description) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Ações Rápidas -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Ações Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= site_url('admin/courses/curriculum/' . $course->id) ?>" class="btn btn-primary">
                        <i class="fas fa-book-open me-2"></i>Gerir Currículo
                    </a>
                    <a href="<?= site_url('admin/courses/form-edit/' . $course->id) ?>" class="btn btn-info">
                        <i class="fas fa-edit me-2"></i>Editar Curso
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $course->id ?>, '<?= $course->course_name ?>')">
                        <i class="fas fa-trash me-2"></i>Eliminar Curso
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Estatísticas Rápidas -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Estatísticas</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-school me-2"></i>Turmas</span>
                        <?php
                        $classModel = new \App\Models\ClassModel();
                        $totalClasses = $classModel->where('course_id', $course->id)->countAllResults();
                        ?>
                        <span class="badge bg-primary rounded-pill"><?= $totalClasses ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-user-graduate me-2"></i>Alunos Matriculados</span>
                        <?php
                        $enrollmentModel = new \App\Models\EnrollmentModel();
                        $totalEnrollments = $enrollmentModel->where('course_id', $course->id)->countAllResults();
                        ?>
                        <span class="badge bg-success rounded-pill"><?= $totalEnrollments ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-book me-2"></i>Disciplinas</span>
                        <span class="badge bg-info rounded-pill"><?= $totalDisciplines ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-clock me-2"></i>Carga Horária</span>
                        <span class="badge bg-warning rounded-pill"><?= $totalWorkload ?>h</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Currículo do Curso -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-warning d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-book-open me-2"></i>Currículo do Curso por Nível</h5>
                <a href="<?= site_url('admin/courses/curriculum/' . $course->id) ?>" class="btn btn-sm btn-light">
                    <i class="fas fa-cog me-1"></i>Gerir Currículo
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($groupedCurriculum)): ?>
                    <div class="accordion" id="curriculumAccordion">
                        <?php 
                        $index = 0;
                        foreach ($groupedCurriculum as $levelId => $group): 
                        ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?= $levelId ?>">
                                    <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapse<?= $levelId ?>" 
                                            aria-expanded="<?= $index == 0 ? 'true' : 'false' ?>" 
                                            aria-controls="collapse<?= $levelId ?>">
                                        <strong><?= $group['level_name'] ?></strong>
                                        <span class="badge bg-primary ms-3"><?= count($group['disciplines']) ?> disciplinas</span>
                                    </button>
                                </h2>
                                <div id="collapse<?= $levelId ?>" 
                                     class="accordion-collapse collapse <?= $index == 0 ? 'show' : '' ?>" 
                                     aria-labelledby="heading<?= $levelId ?>" 
                                     data-bs-parent="#curriculumAccordion">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Código</th>
                                                        <th>Disciplina</th>
                                                        <th class="text-center">Carga Horária</th>
                                                        <th class="text-center">Semestre</th>
                                                        <th class="text-center">Obrigatória</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($group['disciplines'] as $discipline): ?>
                                                        <tr>
                                                            <td><code><?= $discipline->discipline_code ?></code></td>
                                                            <td><?= $discipline->discipline_name ?></td>
                                                            <td class="text-center">
                                                                <?php if ($discipline->workload_hours): ?>
                                                                    <span class="badge bg-info"><?= $discipline->workload_hours ?>h</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary"><?= $discipline->default_workload ?? 0 ?>h</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-secondary"><?= $discipline->semester ?? 'Anual' ?></span>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php if ($discipline->is_mandatory): ?>
                                                                    <i class="fas fa-check-circle text-success"></i>
                                                                <?php else: ?>
                                                                    <i class="fas fa-times-circle text-danger"></i>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <th colspan="2">Total do Nível</th>
                                                        <th class="text-center">
                                                            <?php
                                                            $levelWorkload = 0;
                                                            foreach ($group['disciplines'] as $d) {
                                                                $levelWorkload += $d->workload_hours ?? $d->default_workload ?? 0;
                                                            }
                                                            ?>
                                                            <span class="badge bg-success"><?= $levelWorkload ?>h</span>
                                                        </th>
                                                        <th colspan="2"></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            $index++;
                        endforeach; 
                        ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center py-4">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <h5>Nenhuma disciplina atribuída</h5>
                        <p>Este curso ainda não tem disciplinas no currículo.</p>
                        <a href="<?= site_url('admin/courses/curriculum/' . $course->id) ?>" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Adicionar Disciplinas
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Eliminação -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar o curso <strong id="deleteCourseName"></strong>?</p>
                <p class="text-danger small">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita. Todas as disciplinas associadas serão removidas.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id, name) {
    document.getElementById('deleteCourseName').textContent = name;
    document.getElementById('confirmDeleteBtn').href = '<?= site_url('admin/courses/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
<?= $this->endSection() ?>