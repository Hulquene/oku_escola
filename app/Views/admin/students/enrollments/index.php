<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>


<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-file-signature me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/students') ?>">Alunos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Matrículas</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/students/enrollments/pending') ?>" class="hdr-btn warning">
                <i class="fas fa-clock"></i> Pendentes
            </a>
            <a href="<?= site_url('admin/students/enrollments/form-add') ?>" class="hdr-btn primary">
                <i class="fas fa-plus-circle"></i> Nova Matrícula
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Estatísticas -->
<div class="stat-grid" id="statsContainer">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-file-signature"></i>
        </div>
        <div>
            <div class="stat-label">Total</div>
            <div class="stat-value" id="totalEnrollments">0</div>
            <div class="stat-sub">Matrículas</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="stat-label">Ativas</div>
            <div class="stat-value" id="activeEnrollments">0</div>
            <div class="stat-sub">Em andamento</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <div class="stat-label">Pendentes</div>
            <div class="stat-value" id="pendingEnrollments">0</div>
            <div class="stat-sub">Aguardando</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon navy">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div>
            <div class="stat-label">Concluídas</div>
            <div class="stat-value" id="completedEnrollments">0</div>
            <div class="stat-sub">Finalizadas</div>
        </div>
    </div>
</div>

<!-- Filtros Avançados -->
<div class="ci-card mb-4">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-filter"></i> Filtros</div>
    </div>
    <div class="ci-card-body">
        <div class="filter-grid">
            <!-- Ano Letivo -->
            <div>
                <label class="filter-label">Ano Letivo</label>
                <select class="filter-select" id="academic_year">
                    <option value="">Todos os anos</option>
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                        <option value="<?= $year['id'] ?>" <?= current_academic_year() == $year['id'] ? 'selected' : '' ?>>
                            <?= $year['year_name'] ?> <?= current_academic_year() == $year['id'] ? '(Atual)' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- Nível -->
            <div>
                <label class="filter-label">Nível</label>
                <select class="filter-select" id="grade_level">
                    <option value="">Todos os níveis</option>
                    <?php if (!empty($gradeLevels)): ?>
                        <?php foreach ($gradeLevels as $level): ?>
                            <option value="<?= $level->id ?>" <?= ($selectedGradeLevel ?? '') == $level->id ? 'selected' : '' ?>>
                                <?= $level->level_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- Curso -->
            <div>
                <label class="filter-label">Curso</label>
                <select class="filter-select" id="course">
                    <option value="">Todos os cursos</option>
                    <option value="0">Ensino Geral</option>
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course['id'] ?>" <?= ($selectedCourse ?? '') == $course['id'] ? 'selected' : '' ?>>
                                <?= $course['course_name'] ?> (<?= $course['course_code'] ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- Turma -->
            <div>
                <label class="filter-label">Turma</label>
                <select class="filter-select" id="class_id">
                    <option value="">Todas as turmas</option>
                </select>
            </div>
            
            <!-- Status -->
            <div>
                <label class="filter-label">Status</label>
                <select class="filter-select" id="status">
                    <option value="">Todos</option>
                    <option value="Ativo">Ativo</option>
                    <option value="Pendente">Pendente</option>
                    <option value="Concluído">Concluído</option>
                    <option value="Transferido">Transferido</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
            </div>
        </div>
        
        <div class="filter-actions">
            <button class="btn-filter apply" id="btnFilter"><i class="fas fa-filter"></i> Filtrar</button>
            <button class="btn-filter clear" id="btnClear"><i class="fas fa-undo"></i> Limpar</button>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-list"></i> Lista de Matrículas</div>
        <span class="badge bg-primary" id="recordCount">0 registros</span>
    </div>
    <div style="overflow-x:auto; padding: 0 1.25rem 1.25rem;">
        <table id="enrollmentsTable" class="ci-table" style="width:100%">
            <thead>
                <tr>
                    <th>Nº Matrícula</th>
                    <th>Aluno</th>
                    <th>Nível</th>
                    <th>Curso</th>
                    <th>Turma</th>
                    <th>Ano Letivo</th>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables preenche via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Confirmação de Eliminação -->
<div class="modal fade modal-custom" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--danger);">
                <h5 class="modal-title text-white">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar esta matrícula?</p>
                <div class="warning-alert" style="background:rgba(232,160,32,.07); border-left:3px solid var(--warning); padding:.7rem 1rem; border-radius:var(--radius-sm);">
                    <i class="fas fa-info-circle me-1" style="color:var(--warning);"></i>
                    Esta ação não pode ser desfeita.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-filter clear" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn-filter" style="background:var(--danger); color:#fff;">
                    <i class="fas fa-trash me-1"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
$(document).ready(function() {
    // Carregar estatísticas iniciais
    loadStats();
    
    // Carregar turmas baseado no ano letivo selecionado
    loadClasses($('#academic_year').val());
    
    // Inicializar DataTable
    var table = $('#enrollmentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= site_url('admin/students/enrollments/get-table-data') ?>',
            type: 'POST',
            data: function(d) {
                d.academic_year = $('#academic_year').val();
                d.grade_level = $('#grade_level').val();
                d.course = $('#course').val();
                d.class_id = $('#class_id').val();
                d.status = $('#status').val();
                d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
            },
            error: function(xhr, error, thrown) {
                console.log('Erro AJAX:', error);
                console.log('Resposta:', xhr.responseText);
            }
        },
        columns: [
            { data: 'enrollment_number_html' },
            { data: 'student_name_html' },
            { data: 'level_html' },
            { data: 'course_html' },
            { data: 'class_html' },
            { data: 'year_name' },
            { data: 'enrollment_date_html' },
            { data: 'enrollment_type_html' },
            { data: 'status_html' },
            { data: 'actions' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[6, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        drawCallback: function(settings) {
            // Atualizar contador de registros
            var info = settings.json;
            if (info) {
                $('#recordCount').text(info.recordsFiltered + ' registros');
            }
            
            // Re-inicializar tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
    
    // Filtrar ao clicar no botão
    $('#btnFilter').on('click', function() {
        table.ajax.reload();
    });
    
    // Filtrar ao mudar selects
    $('#academic_year, #grade_level, #course, #class_id, #status').on('change', function() {
        table.ajax.reload();
    });
    
    // Atualizar turmas quando ano letivo mudar
    $('#academic_year').change(function() {
        loadClasses($(this).val());
    });
    
    // Limpar filtros
    $('#btnClear').on('click', function() {
        $('#academic_year').val('');
        $('#grade_level').val('');
        $('#course').val('');
        $('#class_id').empty().append('<option value="">Todas as turmas</option>');
        $('#status').val('');
        table.ajax.reload();
    });
    
    // Função para carregar turmas
    function loadClasses(yearId) {
        var classSelect = $('#class_id');
        classSelect.empty().append('<option value="">Carregando...</option>');
        
        if (yearId) {
            $.get('<?= site_url('admin/students/enrollments/get-classes-by-year/') ?>' + yearId, function(classes) {
                classSelect.empty().append('<option value="">Todas as turmas</option>');
                $.each(classes, function(i, cls) {
                    classSelect.append('<option value="' + cls.id + '">' + cls.class_name + ' (' + cls.class_code + ') - ' + cls.class_shift + '</option>');
                });
            }).fail(function() {
                classSelect.empty().append('<option value="">Erro ao carregar turmas</option>');
            });
        } else {
            classSelect.empty().append('<option value="">Todas as turmas</option>');
        }
    }
    
    // Função para carregar estatísticas
    function loadStats() {
        $.ajax({
            url: '<?= site_url('admin/students/enrollments/get-stats') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#totalEnrollments').text(data.totalEnrollments || 0);
                $('#activeEnrollments').text(data.activeEnrollments || 0);
                $('#pendingEnrollments').text(data.pendingEnrollments || 0);
                $('#completedEnrollments').text(data.completedEnrollments || 0);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar estatísticas:', error);
            }
        });
    }
    
    // Atualizar estatísticas periodicamente
    setInterval(loadStats, 30000);
});

// Função para confirmar eliminação
function confirmDelete(id) {
    $('#confirmDeleteBtn').attr('href', '<?= site_url('admin/students/enrollments/delete/') ?>' + id);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
<?= $this->endSection() ?>