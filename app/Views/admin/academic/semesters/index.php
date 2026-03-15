<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- ── PAGE HEADER ─────────────────────────────────────── -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-calendar-week me-2" style="opacity:.7;font-size:1.1rem;"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/academic/years') ?>">Anos Letivos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Semestres/Trimestres</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <button type="button" class="hdr-btn success" data-bs-toggle="modal" data-bs-target="#processSemesterModal">
                <i class="fas fa-calculator"></i> Processar Semestre
            </button>
            <a href="<?= site_url('admin/academic/semesters/form-add') ?>" class="hdr-btn primary">
                <i class="fas fa-plus-circle"></i> Novo Semestre
            </a>
        </div>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- Cards de Estatísticas -->
<div class="stat-grid" id="statsContainer">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div>
            <div class="stat-label">Total Períodos</div>
            <div class="stat-value" id="totalSemesters">0</div>
            <div class="stat-sub">Cadastrados</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-play-circle"></i>
        </div>
        <div>
            <div class="stat-label">Ativos</div>
            <div class="stat-value" id="activeSemesters">0</div>
            <div class="stat-sub">Em andamento</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-calculator"></i>
        </div>
        <div>
            <div class="stat-label">Processados</div>
            <div class="stat-value" id="processedSemesters">0</div>
            <div class="stat-sub">Com resultados</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon navy">
            <i class="fas fa-check-double"></i>
        </div>
        <div>
            <div class="stat-label">Concluídos</div>
            <div class="stat-value" id="concludedSemesters">0</div>
            <div class="stat-sub">Finalizados</div>
        </div>
    </div>
</div>

<!-- ── FILTER CARD ──────────────────────────────────────── -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-filter"></i> Filtros</div>
    </div>
    <div class="ci-card-body">
        <div class="filter-grid">
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
            <div>
                <label class="filter-label">Status</label>
                <select class="filter-select" id="status">
                    <option value="">Todos</option>
                    <option value="ativo">Ativos</option>
                    <option value="inativo">Inativos</option>
                    <option value="processado">Processados</option>
                    <option value="concluido">Concluídos</option>
                </select>
            </div>
        </div>
        <div class="filter-actions">
            <button class="btn-filter apply" id="btnFilter"><i class="fas fa-filter"></i> Filtrar</button>
            <button class="btn-filter clear" id="btnClear"><i class="fas fa-undo"></i> Limpar</button>
        </div>
    </div>
</div>

<!-- ── TABLE ───────────────────────────────────────────── -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-list"></i> Lista de Semestres/Trimestres</div>
        <span class="badge bg-primary" id="recordCount">0 registros</span>
    </div>
    <div style="overflow-x:auto; padding: 0 1.25rem 1.25rem;">
        <table id="semestersTable" class="ci-table" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ano Letivo</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Período</th>
                    <th class="center">Exames</th>
                    <th class="center">Resultados</th>
                    <th>Status</th>
                    <th class="center">Atual</th>
                    <th class="center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables preenche via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- ── MODAL: PROCESSAR SEMESTRE ───────────────────────── -->
<div class="modal fade ci-modal" id="processSemesterModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="processForm">
                <?= csrf_field() ?>
                <div class="modal-header success-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calculator"></i> Processar Resultados do Semestre
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="info-alert">
                        <i class="fas fa-info-circle"></i>
                        Esta operação calcula as médias finais e gera os resultados para todos os alunos do semestre selecionado.
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-sm-8">
                            <label class="form-label-ci" for="process_semester_id">Semestre / Trimestre <span style="color:var(--danger);">*</span></label>
                            <select class="form-select-ci" id="process_semester_id" required>
                                <option value="">— Selecione um período —</option>
                                <?php foreach ($semesters ?? [] as $sem): ?>
                                    <option value="<?= $sem->id ?>"
                                        data-year="<?= esc($sem->year_name ?? '') ?>">
                                        <?= esc($sem->semester_name) ?> (<?= esc($sem->year_name ?? '') ?>)
                                        <?= ($sem->is_current ?? false) ? ' — Atual' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-hint">Selecione o período que deseja processar</div>
                        </div>
                        <div class="col-sm-4 d-flex align-items-end">
                            <button type="button" class="btn-filter apply w-100" onclick="checkExams()" style="padding:.57rem 1rem;">
                                <i class="fas fa-search"></i> Verificar Exames
                            </button>
                        </div>
                    </div>

                    <!-- Dynamic process info -->
                    <div class="process-info-box" id="processInfo">
                        <div class="process-info-header"><i class="fas fa-info-circle"></i> Informações do Período</div>
                        <div class="process-info-body" id="processInfoContent">
                            <div style="text-align:center;padding:.75rem;color:var(--text-muted);font-size:.82rem;">
                                <div class="ci-spinner" style="width:24px;height:24px;border-width:2px;margin:0 auto .4rem;"></div>
                                Carregando...
                            </div>
                        </div>
                    </div>

                    <!-- Options -->
                    <div style="margin-top:1.25rem;">
                        <div class="form-label-ci" style="margin-bottom:.6rem;">Opções de Processamento</div>
                        <label class="opt-row">
                            <input type="checkbox" name="generate_report_cards" value="1" checked>
                            <div class="opt-row-text">
                                <div class="ot-title"><i class="fas fa-file-pdf" style="color:var(--danger);"></i> Gerar boletins automaticamente</div>
                                <div class="ot-sub">Os boletins individuais serão gerados após o processamento</div>
                            </div>
                        </label>
                        <label class="opt-row">
                            <input type="checkbox" name="close_semester" value="1">
                            <div class="opt-row-text">
                                <div class="ot-title"><i class="fas fa-lock" style="color:var(--warning);"></i> Fechar semestre</div>
                                <div class="ot-sub">Impede novos lançamentos de notas neste período</div>
                            </div>
                        </label>
                    </div>

                    <!-- Warning -->
                    <div class="warning-alert mt-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Atenção!</strong> Esta operação:
                        <ul>
                            <li>Calcula as médias por disciplina para todos os alunos</li>
                            <li>Gera os resultados consolidados do semestre</li>
                            <li>Cria os boletins individuais</li>
                            <li>Não pode ser desfeita automaticamente</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-success-ci" id="processBtn">
                        <i class="fas fa-play"></i> Iniciar Processamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL: LOADING ──────────────────────────────────── -->
<div class="modal fade ci-modal" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content">
            <div class="loading-body">
                <div class="ci-spinner"></div>
                <div class="loading-title">Processando semestre...</div>
                <div class="loading-msg" id="loadingMessage">Calculando médias por disciplina</div>
                <div class="ci-loading-bar"><div class="ci-loading-bar-inner"></div></div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
$(document).ready(function () {
    // Carregar estatísticas iniciais
    loadStats();
    
    // Inicializar DataTable
    var table = $('#semestersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= site_url('admin/academic/semesters/get-table-data') ?>',
            type: 'POST',
            data: function(d) {
                d.academic_year = $('#academic_year').val();
                d.status = $('#status').val();
                d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
            },
            error: function(xhr, error, thrown) {
                console.log('Erro AJAX:', error);
                console.log('Resposta:', xhr.responseText);
            }
        },
        columns: [
            { data: 'id' },
            { data: 'year_name' },
            { data: 'semester_name' },
            { data: 'semester_type' },
            { 
                data: null,
                render: function(data) {
                    return '<span class="period-text">' +
                           formatDate(data.start_date) + ' → ' + formatDate(data.end_date) +
                           '</span>';
                }
            },
            { 
                data: 'total_exams',
                render: function(data) {
                    return '<span class="count-chip neutral">' + (data || 0) + '</span>';
                }
            },
            { 
                data: 'has_results',
                render: function(data) {
                    var cls = data > 0 ? 'has' : 'zero';
                    return '<span class="count-chip ' + cls + '">' + (data || 0) + '</span>';
                }
            },
            { data: 'status_badge' },
            { data: 'current_badge' },
            { data: 'actions' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc'], [4, 'asc']],
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
    $('#academic_year, #status').on('change', function() {
        table.ajax.reload();
    });
    
    // Limpar filtros
    $('#btnClear').on('click', function() {
        $('#academic_year').val('');
        $('#status').val('');
        table.ajax.reload();
    });
    
    // Auto-load info when semester changes
    $('#process_semester_id').on('change', function () {
        var id = $(this).val();
        if (id) loadSemesterInfo(id);
        else $('#processInfo').removeClass('show');
    });

    // Intercept form submit
    $('#processForm').on('submit', function (e) {
        e.preventDefault();
        
        var semesterId = $('#process_semester_id').val();
        if (!semesterId) {
            Swal.fire('Atenção!', 'Selecione um semestre para processar.', 'warning');
            return;
        }
        
        if (!confirm('Tem certeza que deseja processar este semestre? Esta ação não pode ser desfeita.')) return;

        $('#loadingModal').modal('show');
        $('#processBtn').prop('disabled', true);

        var msgs = [
            'Calculando médias por disciplina...',
            'Processando resultados individuais...',
            'Gerando estatísticas da turma...',
            'Preparando boletins...',
            'Finalizando processamento...'
        ];
        var idx = 0;
        var iv  = setInterval(function () {
            if (idx < msgs.length) $('#loadingMessage').text(msgs[idx++]);
        }, 2000);

        $.ajax({
            url: '<?= site_url('admin/academic/semesters/process') ?>',
            type: 'POST',
            data: {
                semester_id: semesterId,
                generate_report_cards: $('input[name="generate_report_cards"]').is(':checked') ? 1 : 0,
                close_semester: $('input[name="close_semester"]').is(':checked') ? 1 : 0,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function (r) {
                clearInterval(iv);
                $('#loadingModal').modal('hide');
                if (r.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        html: r.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: r.message
                    });
                }
            },
            error: function (xhr) {
                clearInterval(iv);
                $('#loadingModal').modal('hide');
                var msg = 'Erro ao processar semestre';
                try {
                    var r = JSON.parse(xhr.responseText);
                    msg = r.message || msg;
                } catch(e) {}
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: msg
                });
            },
            complete: function () {
                $('#processBtn').prop('disabled', false);
            }
        });
    });
    
    // Função para carregar estatísticas
    function loadStats() {
        $.ajax({
            url: '<?= site_url('admin/academic/semesters/get-stats') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#totalSemesters').text(data.total || 0);
                $('#activeSemesters').text(data.active || 0);
                $('#processedSemesters').text(data.processed || 0);
                $('#concludedSemesters').text(data.concluded || 0);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar estatísticas:', error);
            }
        });
    }
    
    // Função para formatar data
    function formatDate(dateString) {
        if (!dateString) return '';
        var date = new Date(dateString);
        return ('0' + date.getDate()).slice(-2) + '/' +
               ('0' + (date.getMonth() + 1)).slice(-2) + '/' +
               date.getFullYear();
    }
});

function loadSemesterInfo(id) {
    $('#processInfoContent').html(
        '<div style="text-align:center;padding:.75rem;color:var(--text-muted);font-size:.82rem;">' +
        '<div class="ci-spinner" style="width:24px;height:24px;border-width:2px;margin:0 auto .4rem;"></div>' +
        'Carregando...</div>'
    );
    $('#processInfo').addClass('show');

    $.getJSON('<?= site_url("admin/academic/semesters/info") ?>/' + id, function (r) {
        if (!r.success) {
            $('#processInfoContent').html('<div style="color:var(--danger);font-size:.82rem;padding:.5rem;">' + r.message + '</div>');
            return;
        }

        var d = r.data;
        var s = r.stats;
        var html = '<div class="row g-2" style="font-size:.82rem;">' +
            '<div class="col-sm-6"><strong>Período:</strong> ' + d.semester_name + '</div>' +
            '<div class="col-sm-6"><strong>Tipo:</strong> ' + d.semester_type + '</div>' +
            '<div class="col-sm-6"><strong>Ano Letivo:</strong> ' + d.year_name + '</div>' +
            '<div class="col-sm-6"><strong>Início:</strong> ' + d.start_date + ' → <strong>Fim:</strong> ' + d.end_date + '</div>' +
            '</div>' +
            '<div class="process-info-grid">' +
            '<div class="process-stat"><span class="process-stat-val">' + (s.total_exams || 0) + '</span><span class="process-stat-label">Exames</span></div>' +
            '<div class="process-stat"><span class="process-stat-val">' + (s.total_students || 0) + '</span><span class="process-stat-label">Alunos</span></div>' +
            '<div class="process-stat"><span class="process-stat-val">' + (s.total_results || 0) + '</span><span class="process-stat-label">Resultados</span></div>' +
            '</div>';

        if (s.total_results > 0) {
            html += '<div class="warning-alert mt-2" style="font-size:.78rem;"><i class="fas fa-exclamation-triangle"></i> Este semestre já possui resultados. Processar novamente irá sobrescrever os dados existentes.</div>';
        }

        $('#processInfoContent').html(html);
    }).fail(function () {
        $('#processInfoContent').html('<div style="color:var(--danger);font-size:.82rem;padding:.5rem;">Erro ao carregar informações.</div>');
    });
}

function checkExams() {
    var id = $('#process_semester_id').val();
    if (!id) {
        Swal.fire('Atenção!', 'Selecione um semestre primeiro!', 'warning');
        return;
    }
    window.open('<?= site_url("admin/exams?semester_id=") ?>' + id, '_blank');
}
</script>
<?= $this->endSection() ?>