<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/courses') ?>">Cursos</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/courses/view/' . $course->id) ?>"><?= $course->course_name ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Currículo</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações do Curso -->
<div class="alert alert-info">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-info-circle me-2"></i>
            <strong><?= $course->course_name ?></strong> (<?= $course->course_code ?>) - 
            <span class="badge bg-<?= $course->is_active ? 'success' : 'secondary' ?>"><?= $course->is_active ? 'Ativo' : 'Inativo' ?></span>
        </div>
        <div>
            <a href="<?= site_url('admin/courses/view/' . $course->id) ?>" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar ao Curso
            </a>
        </div>
    </div>
</div>

<!-- Tabs por Nível -->
<div class="row">
    <div class="col-md-3 mb-4">
        <!-- Menu de Níveis -->
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Níveis do Curso</h5>
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($levels as $index => $level): ?>
                    <a href="#level-<?= $level->id ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-layer-group me-2 text-primary"></i>
                            <?= $level->level_name ?>
                        </span>
                        <?php if (isset($curriculum[$level->id]) && !empty($curriculum[$level->id]['disciplines'])): ?>
                            <span class="badge bg-primary rounded-pill"><?= count($curriculum[$level->id]['disciplines']) ?></span>
                        <?php else: ?>
                            <span class="badge bg-secondary rounded-pill">0</span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Resumo -->
            <div class="card-footer bg-light">
                <div class="small">
                    <div class="d-flex justify-content-between">
                        <span>Total Disciplinas:</span>
                        <strong>
                            <?php 
                            $total = 0;
                            foreach ($curriculum as $c) {
                                $total += count($c['disciplines']);
                            }
                            echo $total;
                            ?>
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <?php foreach ($levels as $index => $level): ?>
            <div class="card mb-4" id="level-<?= $level->id ?>">
                <div class="card-header bg-<?= $index % 2 == 0 ? 'primary' : 'secondary' ?> text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        <?= $level->level_name ?>
                    </h5>
                    <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addDisciplineModal" 
                            onclick="setCourseLevel(<?= $course->id ?>, <?= $level->id ?>)">
                        <i class="fas fa-plus-circle me-1"></i>Adicionar Disciplina
                    </button>
                </div>
                <div class="card-body">
                    <?php if (isset($curriculum[$level->id]) && !empty($curriculum[$level->id]['disciplines'])): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Código</th>
                                        <th>Disciplina</th>
                                        <th class="text-center">Carga Horária</th>
                                        <th class="text-center">Semestre</th>
                                        <th class="text-center">Obrigatória</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $levelWorkload = 0;
                                    foreach ($curriculum[$level->id]['disciplines'] as $discipline): 
                                        $workload = $discipline->workload_hours ?? $discipline->default_workload ?? 0;
                                        $levelWorkload += $workload;
                                    ?>
                                        <tr>
                                            <td><code><?= $discipline->discipline_code ?></code></td>
                                            <td>
                                                <strong><?= $discipline->discipline_name ?></strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info"><?= $workload ?>h</span>
                                                <?php if ($discipline->workload_hours && $discipline->workload_hours != $discipline->default_workload): ?>
                                                    <br><small class="text-muted">(Padrão: <?= $discipline->default_workload ?>h)</small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary"><?= $discipline->semester ?? 'Anual' ?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($discipline->is_mandatory): ?>
                                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Sim</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning"><i class="fas fa-times me-1"></i>Não</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= site_url('admin/courses/curriculum/edit/' . $discipline->id) ?>" 
                                                       class="btn btn-outline-info" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger" 
                                                            onclick="confirmRemoveDiscipline(<?= $discipline->id ?>, '<?= $discipline->discipline_name ?>')"
                                                            title="Remover">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="2">Total do Nível</th>
                                        <th class="text-center">
                                            <span class="badge bg-success"><?= $levelWorkload ?>h</span>
                                        </th>
                                        <th colspan="3"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center py-4">
                            <i class="fas fa-info-circle fa-3x mb-3"></i>
                            <h5>Nenhuma disciplina para este nível</h5>
                            <p>Clique no botão "Adicionar Disciplina" para começar.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Adicionar Disciplina -->
<div class="modal fade" id="addDisciplineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= site_url('admin/courses/curriculum/add-discipline') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="course_id" id="modal_course_id">
                <input type="hidden" name="grade_level_id" id="modal_grade_level_id">
                
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle text-primary me-2"></i>
                        Adicionar Disciplina ao Currículo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="discipline_id" class="form-label fw-semibold">Disciplina <span class="text-danger">*</span></label>
                            <select class="form-select" id="discipline_id" name="discipline_id" required>
                                <option value="">Selecione uma disciplina...</option>
                                <?php
                                $disciplineModel = new \App\Models\DisciplineModel();
                                $disciplines = $disciplineModel->where('is_active', 1)->orderBy('discipline_name', 'ASC')->findAll();
                                foreach ($disciplines as $discipline):
                                ?>
                                    <option value="<?= $discipline->id ?>" 
                                            data-workload="<?= $discipline->workload_hours ?>"
                                            data-approval="<?= $discipline->approval_grade ?>">
                                        <?= $discipline->discipline_name ?> (<?= $discipline->discipline_code ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="workload_hours" class="form-label fw-semibold">Carga Horária</label>
                            <input type="number" class="form-control" id="workload_hours" name="workload_hours" 
                                   placeholder="Deixar vazio para usar o padrão">
                            <small class="text-muted" id="workload_hint"></small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="semester" class="form-label fw-semibold">Semestre</label>
                            <select class="form-select" id="semester" name="semester">
                                <option value="Anual">Anual</option>
                                <option value="1º">1º Semestre</option>
                                <option value="2º">2º Semestre</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_mandatory" name="is_mandatory" value="1" checked>
                                <label class="form-check-label" for="is_mandatory">
                                    Disciplina Obrigatória
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-2" id="disciplineInfo" style="display: none;">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="disciplineInfoText"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Adicionar ao Currículo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Remover Disciplina -->
<div class="modal fade" id="removeDisciplineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Remoção
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja remover a disciplina <strong id="removeDisciplineName"></strong> do currículo?</p>
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita. A disciplina será removida apenas deste nível.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmRemoveBtn" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>Remover
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// CSRF para AJAX
const csrfToken = '<?= csrf_token() ?>';
const csrfHash = '<?= csrf_hash() ?>';

// Configurar modal para adicionar disciplina
function setCourseLevel(courseId, levelId) {
    document.getElementById('modal_course_id').value = courseId;
    document.getElementById('modal_grade_level_id').value = levelId;
    
    // Resetar formulário
    document.getElementById('discipline_id').value = '';
    document.getElementById('workload_hours').value = '';
    document.getElementById('semester').value = 'Anual';
    document.getElementById('is_mandatory').checked = true;
    document.getElementById('disciplineInfo').style.display = 'none';
}

// Mostrar informações da disciplina selecionada
document.getElementById('discipline_id')?.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const workload = selected.dataset.workload;
    const approval = selected.dataset.approval;
    
    if (selected.value) {
        document.getElementById('workload_hint').textContent = `Padrão: ${workload || 0} horas`;
        document.getElementById('disciplineInfoText').innerHTML = `
            <strong>Informações da Disciplina:</strong><br>
            Carga Horária Padrão: ${workload || 0} horas<br>
            Nota de Aprovação: ${approval || 10} valores
        `;
        document.getElementById('disciplineInfo').style.display = 'block';
    } else {
        document.getElementById('workload_hint').textContent = '';
        document.getElementById('disciplineInfo').style.display = 'none';
    }
});

// Confirmar remoção de disciplina
function confirmRemoveDiscipline(id, name) {
    document.getElementById('removeDisciplineName').textContent = name;
    document.getElementById('confirmRemoveBtn').href = '<?= site_url('admin/courses/curriculum/remove/') ?>' + id;
    new bootstrap.Modal(document.getElementById('removeDisciplineModal')).show();
}

// Atualizar lista de disciplinas disponíveis via AJAX (opcional)
document.getElementById('grade_level_id')?.addEventListener('change', function() {
    const courseId = document.getElementById('modal_course_id').value;
    const levelId = this.value;
    
    if (courseId && levelId) {
        fetch(`<?= site_url('admin/courses/curriculum/get-available/') ?>${courseId}/${levelId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash
            }
        })
        .then(response => response.json())
        .then(disciplines => {
            const select = document.getElementById('discipline_id');
            select.innerHTML = '<option value="">Selecione uma disciplina...</option>';
            
            disciplines.forEach(d => {
                select.innerHTML += `<option value="${d.id}" data-workload="${d.workload_hours}" data-approval="${d.approval_grade}">
                    ${d.discipline_name} (${d.discipline_code})
                </option>`;
            });
        })
        .catch(error => console.error('Erro:', error));
    }
});
</script>

<style>
.sticky-top {
    z-index: 100;
}
.card .list-group-item.active {
    background-color: #0d6efd;
    color: white;
}
</style>

<?= $this->endSection() ?>