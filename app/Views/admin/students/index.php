<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>


<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-user-graduate me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Alunos</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <button class="hdr-btn success" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
            <a href="<?= site_url('admin/students/form-add') ?>" class="hdr-btn primary">
                <i class="fas fa-plus-circle"></i> Novo Aluno
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
            <i class="fas fa-user-graduate"></i>
        </div>
        <div>
            <div class="stat-label">Total Alunos</div>
            <div class="stat-value" id="totalStudents">0</div>
            <div class="stat-sub">Cadastrados</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="stat-label">Matriculados</div>
            <div class="stat-value" id="enrolledStudents">0</div>
            <div class="stat-sub">Ativos</div>
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
            <i class="fas fa-venus-mars"></i>
        </div>
        <div>
            <div class="stat-label">Masculino/Feminino</div>
            <div class="stat-value" id="genderStats">0/0</div>
            <div class="stat-sub">Distribuição</div>
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
            <!-- Busca geral -->
            <div>
                <label class="filter-label">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="filter-select border-start-0" id="search" 
                           placeholder="Nome, nº matrícula, email...">
                </div>
            </div>
            
            <!-- Ano Letivo -->
            <div>
                <label class="filter-label">Ano Letivo</label>
                <select class="filter-select" id="academic_year">
                    <option value="">Todos</option>
                    <?php foreach ($academicYears as $year): ?>
                        <option value="<?= $year['id'] ?>" <?= current_academic_year() == $year['id'] ? 'selected' : '' ?>>
                            <?= $year['year_name'] ?> <?= current_academic_year() == $year['id'] ? '(Atual)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Curso -->
            <div>
                <label class="filter-label">Curso</label>
                <select class="filter-select" id="course_id">
                    <option value="">Todos os cursos</option>
                    <option value="0">Ensino Geral</option>
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course['id'] ?>" <?= ($selectedCourse ?? '') == $course['id'] ? 'selected' : '' ?>>
                                <?= esc($course['course_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- Status Matrícula -->
            <div>
                <label class="filter-label">Status Matrícula</label>
                <select class="filter-select" id="enrollment_status">
                    <option value="">Todos</option>
                    <option value="Ativo">Matriculado</option>
                    <option value="Pendente">Pendente</option>
                    <option value="Concluído">Concluído</option>
                    <option value="Transferido">Transferido</option>
                    <option value="nao_matriculado">Não Matriculado</option>
                </select>
            </div>
            
            <!-- Gênero -->
            <div>
                <label class="filter-label">Gênero</label>
                <select class="filter-select" id="gender">
                    <option value="">Todos</option>
                    <option value="Masculino">Masculino</option>
                    <option value="Feminino">Feminino</option>
                </select>
            </div>
            
            <!-- Status Aluno -->
            <div>
                <label class="filter-label">Status Aluno</label>
                <select class="filter-select" id="status">
                    <option value="">Todos</option>
                    <option value="active">Ativo</option>
                    <option value="inactive">Inativo</option>
                </select>
            </div>
            
            <!-- Data Cadastro -->
            <div>
                <label class="filter-label">Data Cadastro</label>
                <input type="date" class="filter-select" id="created_date" value="<?= $selectedCreatedDate ?? '' ?>">
            </div>
            
            <!-- Mês/Ano Cadastro -->
            <div>
                <label class="filter-label">Mês/Ano</label>
                <input type="month" class="filter-select" id="created_month" value="<?= $selectedCreatedMonth ?? '' ?>">
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
        <div class="ci-card-title"><i class="fas fa-list"></i> Lista de Alunos</div>
        <span class="badge bg-primary" id="recordCount">0 registros</span>
    </div>
    <div style="overflow-x:auto; padding: 0 1.25rem 1.25rem;">
        <table id="studentsTable" class="ci-table" style="width:100%">
            <thead>
                <tr>
                    <th>Nº Matrícula</th>
                    <th>Foto</th>
                    <th>Nome Completo / Contacto</th>
                    <th>Curso</th>
                    <th>Nível / Ano Letivo</th>
                    <th>Gênero</th>
                    <th>Status</th>
                    <th>Data Cadastro</th>
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
                <p>Tem certeza que deseja eliminar este aluno?</p>
                <div class="warning-alert" style="background:rgba(232,160,32,.07); border-left:3px solid var(--warning); padding:.7rem 1rem; border-radius:var(--radius-sm);">
                    <i class="fas fa-info-circle me-1" style="color:var(--warning);"></i>
                    Esta ação não pode ser desfeita. O aluno será desativado do sistema.
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
<!-- DataTables -->
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script> -->

<script>
$(document).ready(function() {
    // Carregar estatísticas iniciais
    loadStats();
    
    // Inicializar DataTable
    var table = $('#studentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= site_url('admin/students/get-table-data') ?>',
            type: 'POST',
            data: function(d) {
                d.academic_year = $('#academic_year').val();
                d.course_id = $('#course_id').val();
                d.enrollment_status = $('#enrollment_status').val();
                d.gender = $('#gender').val();
                d.status = $('#status').val();
                d.created_date = $('#created_date').val();
                d.created_month = $('#created_month').val();
                d.search = $('#search').val();
                d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
            },
            error: function(xhr, error, thrown) {
                console.log('Erro AJAX:', error);
                console.log('Resposta:', xhr.responseText);
            }
        },
        columns: [
            { 
                data: 'student_number',
                render: function(data) {
                    return '<span class="student-number">' + (data || '') + '</span>';
                }
            },
            { data: 'photo_html' },
            { data: 'name_html' },
            { data: 'course_html' },
            { data: 'level_html' },
            { data: 'gender' },
            { data: 'status_badge' },
            { data: 'created_at_html' },
            { data: 'actions' }
        ],
        
        order: [[2, 'asc']],
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
    
    // Filtrar ao mudar selects com debounce
    let filterTimer;
    $('#academic_year, #course_id, #enrollment_status, #gender, #status, #created_date, #created_month').on('change', function() {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(() => {
            table.ajax.reload();
        }, 500);
    });
    
    // Busca com debounce
    let searchTimer;
    $('#search').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            table.ajax.reload();
        }, 500);
    });
    
    // Limpar filtros
    $('#btnClear').on('click', function() {
        $('#search').val('');
        $('#academic_year').val('');
        $('#course_id').val('');
        $('#enrollment_status').val('');
        $('#gender').val('');
        $('#status').val('');
        $('#created_date').val('');
        $('#created_month').val('');
        table.ajax.reload();
    });
    
    // Função para carregar estatísticas
    function loadStats() {
        $.ajax({
            url: '<?= site_url('admin/students/get-stats') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#totalStudents').text(data.totalStudents || 0);
                $('#enrolledStudents').text(data.enrolledStudents || 0);
                $('#pendingEnrollments').text(data.pendingEnrollments || 0);
                $('#genderStats').text((data.maleCount || 0) + '/' + (data.femaleCount || 0));
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
    $('#confirmDeleteBtn').attr('href', '<?= site_url('admin/students/delete/') ?>' + id);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Exportar para Excel
function exportToExcel() {
    const table = document.getElementById('studentsTable');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    let csv = [];
    
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        const filteredCells = cells.filter((_, index) => index !== 1 && index !== cells.length - 1);
        const rowData = filteredCells.map(cell => cell.innerText.trim().replace(/,/g, ';'));
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'alunos_' + new Date().toISOString().split('T')[0] + '.csv');
    link.click();
}
</script>
<?= $this->endSection() ?>