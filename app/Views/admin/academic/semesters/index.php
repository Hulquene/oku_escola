<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
/* Apenas ajustes específicos para esta página */
@media (max-width: 767px) {
    .header-row { 
        flex-direction: column; 
        align-items: flex-start !important; 
        gap: 0.75rem !important; 
    }
    .stats-col-filter, .stats-col-card { 
        width: 100% !important; 
        flex: 0 0 100% !important; 
        max-width: 100% !important; 
    }
    .filter-row > div { 
        flex: 0 0 100%; 
        max-width: 100%; 
    }
}

@media print {
    .btn-header-new, .filter-card, .btn-action-sm, .row-btn.del,
    .dataTables_filter, .dataTables_length, .dt-buttons { 
        display: none !important; 
    }
    .ci-page-header { 
        background: #1B2B4B !important; 
        -webkit-print-color-adjust: exact; 
    }
}

/* Estilo específico para badges de semestre */
.semester-badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.2rem 0.55rem;
    border-radius: 50px;
    display: inline-block;
}

.semester-badge.primeiro {
    background: rgba(59,127,232,0.1);
    color: var(--accent);
}

.semester-badge.segundo {
    background: rgba(22,168,125,0.1);
    color: var(--success);
}

.semester-badge.terceiro {
    background: rgba(232,160,32,0.1);
    color: var(--warning);
}

/* Estilo para período */
.period-text {
    font-family: var(--font-mono);
    font-size: 0.73rem;
    color: var(--text-secondary);
    white-space: nowrap;
}

.period-text i {
    font-size: 0.65rem;
    margin: 0 0.2rem;
    color: var(--text-muted);
}

/* Estilo para badges de status */
.status-badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.22rem 0.6rem;
    border-radius: 50px;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    white-space: nowrap;
}

.status-badge.ativo {
    color: var(--success);
    background: rgba(22,168,125,0.1);
}

.status-badge.inativo {
    color: var(--text-muted);
    background: rgba(107,122,153,0.1);
}

.status-badge.processado {
    color: var(--accent);
    background: rgba(59,127,232,0.1);
}

.status-badge.concluido {
    color: #B07800;
    background: rgba(232,160,32,0.1);
}

.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    display: inline-block;
    flex-shrink: 0;
}

/* Estilo para current badge */
.current-badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.22rem 0.6rem;
    border-radius: 50px;
    background: rgba(59,127,232,0.12);
    color: var(--accent);
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    white-space: nowrap;
}

/* Row ID */
.row-id {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    color: var(--text-muted);
    font-weight: 500;
}

/* Count chips */
.count-chip {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.18rem 0.5rem;
    border-radius: 6px;
    display: inline-block;
}

.count-chip.neutral {
    background: rgba(107,122,153,0.1);
    color: var(--text-secondary);
}

.count-chip.has {
    background: rgba(22,168,125,0.1);
    color: var(--success);
}

.count-chip.zero {
    background: rgba(232,70,70,0.1);
    color: var(--danger);
}
</style>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-calendar-week me-2" style="opacity:.7;font-size:1.1rem;"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= route_to('admin.dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= route_to('academic.years') ?>">Anos Letivos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Semestres/Trimestres</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <button type="button" class="hdr-btn success" data-bs-toggle="modal" data-bs-target="#processSemesterModal">
                <i class="fas fa-calculator"></i> Processar Semestre
            </button>
            <a href="<?= route_to('academic.semesters.form') ?>" class="hdr-btn primary">
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

<!-- Filtros Avançados -->
<div class="ci-card mb-4">
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

<!-- Data Table -->
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
                    <th class="text-center">Exames</th>
                    <th class="text-center">Resultados</th>
                    <th>Status</th>
                    <th class="text-center">Atual</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables preenche via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Processamento de Semestre -->
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

<!-- Modal de Loading -->
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
            url: '<?= route_to('academic.semesters.get-table-data') ?>',
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
            { data: 'id_html' },
            { data: 'year_html' },
            { data: 'name_html' },
            { data: 'type_html' },
            { data: 'period_html' },
            { data: 'exams_html', className: 'text-center' },
            { data: 'results_html', className: 'text-center' },
            { data: 'status_html' },
            { data: 'current_html', className: 'text-center' },
            { data: 'actions', className: 'text-center' }
        ],
        language: {
            url: base_url + "assets/datatables/i18n/pt-BR.json",
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
            url: '<?= route_to('admin.semesters.process') ?>',
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
            url: '<?= route_to('academic.semesters.get-stats') ?>',
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