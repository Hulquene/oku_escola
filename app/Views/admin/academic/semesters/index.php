<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <!-- NOVO BOTÃO DE PROCESSAMENTO -->
            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#processSemesterModal">
                <i class="fas fa-calculator"></i> Processar Semestre
            </button>
            <a href="<?= site_url('admin/academic/semesters/form-add') ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Novo Semestre/Trimestre
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/academic/years') ?>">Anos Letivos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Semestres/Trimestres</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="academic_year" class="form-label">Ano Letivo</label>
                <select class="form-select" id="academic_year" name="academic_year">
                    <option value="">Todos</option>
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= $year->id ?>" <?= $selectedYear == $year->id ? 'selected' : '' ?>>
                                <?= $year->year_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="active" <?= $selectedStatus == 'active' ? 'selected' : '' ?>>Ativos</option>
                    <option value="inactive" <?= $selectedStatus == 'inactive' ? 'selected' : '' ?>>Inativos</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/academic/semesters') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i> Lista de Semestres/Trimestres
    </div>
    <div class="card-body">
        <table id="semestersTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ano Letivo</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Período</th>
                    <th>Exames</th>
                    <th>Resultados</th>
                    <th>Status</th>
                    <th>Atual</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($semesters as $semester): ?>
                    <tr>
                        <td><?= $semester->id ?></td>
                        <td><?= $semester->year_name ?></td>
                        <td><?= $semester->semester_name ?></td>
                        <td><span class="badge bg-info"><?= $semester->semester_type ?></span></td>
                        <td><?= date('d/m/Y', strtotime($semester->start_date)) ?> - <?= date('d/m/Y', strtotime($semester->end_date)) ?></td>
                        <td class="text-center">
                            <span class="badge bg-secondary"><?= $semester->total_exams ?></span>
                        </td>
                        <td class="text-center">
                            <?php if ($semester->has_results > 0): ?>
                                <span class="badge bg-success"><?= $semester->has_results ?></span>
                            <?php else: ?>
                                <span class="badge bg-warning">0</span>
                            <?php endif; ?>
                        </td>
                       <td>
                            <?php
                            // Mapeamento de status para badges
                            $statusLabels = [
                                'ativo' => ['badge' => 'success', 'text' => 'Ativo', 'icon' => 'fa-play'],
                                'inativo' => ['badge' => 'secondary', 'text' => 'Inativo', 'icon' => 'fa-stop'],
                                'processado' => ['badge' => 'warning', 'text' => 'Processado', 'icon' => 'fa-calculator'],
                                'concluido' => ['badge' => 'dark', 'text' => 'Concluído', 'icon' => 'fa-check-double']
                            ];
                            
                            $status = $semester->status ?? 'ativo';
                            $label = $statusLabels[$status] ?? ['badge' => 'secondary', 'text' => $status, 'icon' => 'fa-circle'];
                            ?>
                            <span class="badge bg-<?= $label['badge'] ?>">
                                <i class="fas <?= $label['icon'] ?> me-1"></i>
                                <?= $label['text'] ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($semester->is_current): ?>
                                <span class="badge bg-primary">Atual</span>
                            <?php else: ?>
                                <a href="<?= site_url('admin/academic/semesters/set-current/' . $semester->id) ?>" 
                                class="btn btn-sm btn-outline-primary"
                                onclick="return confirm('Definir este período como atual?')">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Botão Editar -->
                            <?php if ($semester->status == 'ativo' || $semester->status == 'inativo'): ?>
                                <a href="<?= site_url('admin/academic/semesters/form-edit/' . $semester->id) ?>" 
                                class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            <?php else: ?>
                                <span class="btn btn-sm btn-secondary disabled" 
                                    title="Não pode editar (<?= $statusLabels[$semester->status]['text'] ?? $semester->status ?>)">
                                    <i class="fas fa-edit"></i>
                                </span>
                            <?php endif; ?>
                            
                            <!-- Botão Ver Detalhes -->
                            <a href="<?= site_url('admin/academic/semesters/view/' . $semester->id) ?>" 
                            class="btn btn-sm btn-success" title="Ver Detalhes">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <!-- Botão Concluir (aparece apenas para processado) -->
                            <?php if ($semester->status == 'processado'): ?>
                                <a href="<?= site_url('admin/academic/semesters/conclude/' . $semester->id) ?>" 
                                class="btn btn-sm btn-warning"
                                onclick="return confirm('Concluir este período? Esta ação irá fechar o semestre permanentemente.')"
                                title="Concluir Período">
                                    <i class="fas fa-check-double"></i>
                                </a>
                            <?php endif; ?>
                            
                            <!-- Botão Eliminar -->
                            <?php if (!$semester->is_current && in_array($semester->status, ['ativo', 'inativo'])): ?>
                                <a href="<?= site_url('admin/academic/semesters/delete/' . $semester->id) ?>" 
                                class="btn btn-sm btn-danger" 
                                onclick="return confirm('Tem certeza que deseja eliminar este período?')"
                                title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php elseif (!$semester->is_current): ?>
                                <span class="btn btn-sm btn-secondary disabled" 
                                    title="Não pode eliminar (<?= $statusLabels[$semester->status]['text'] ?? $semester->status ?>)">
                                    <i class="fas fa-trash"></i>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Processar Semestre -->
<div class="modal fade" id="processSemesterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-calculator me-2"></i>Processar Resultados do Semestre
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('admin/academic/semesters/process') ?>" method="post" id="processForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Esta operação irá calcular as médias finais e gerar os resultados para todos os alunos do semestre selecionado.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="process_semester_id" class="form-label fw-bold">Selecionar Semestre/Trimestre</label>
                                <select class="form-select" name="semester_id" id="process_semester_id" required>
                                    <option value="">-- Selecione um período --</option>
                                    <?php if (!empty($semesters)): ?>
                                        <?php foreach ($semesters as $sem): ?>
                                            <option value="<?= $sem->id ?>" 
                                                <?= ($sem->is_current ?? false) ? 'selected' : '' ?>
                                                data-year="<?= $sem->year_name ?? '' ?>">
                                                <?= $sem->semester_name ?> (<?= $sem->year_name ?? '' ?>)
                                                <?= ($sem->is_current ?? false) ? ' - Atual' : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted">Selecione o período que deseja processar</small>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-primary mb-3" onclick="checkExams()">
                                <i class="fas fa-search"></i> Verificar Exames
                            </button>
                        </div>
                    </div>
                    
                    <!-- Informações do Processamento -->
                    <div class="card mt-3 d-none" id="processInfo">
                        <div class="card-header bg-light">
                            <i class="fas fa-info-circle"></i> Informações do Período
                        </div>
                        <div class="card-body" id="processInfoContent">
                            <!-- Preenchido via AJAX -->
                        </div>
                    </div>
                    
                    <!-- Opções de Processamento -->
                    <div class="mt-4">
                        <h6 class="fw-bold">Opções de processamento:</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="generate_report_cards" id="generate_report_cards" value="1" checked>
                            <label class="form-check-label" for="generate_report_cards">
                                <i class="fas fa-file-pdf text-danger me-1"></i>
                                Gerar boletins automaticamente após processamento
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="close_semester" id="close_semester" value="1">
                            <label class="form-check-label" for="close_semester">
                                <i class="fas fa-lock text-warning me-1"></i>
                                Fechar semestre (impedir novos lançamentos)
                            </label>
                        </div>
                    </div>
                    
                    <!-- Aviso Importante -->
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção!</strong> Esta operação:
                        <ul class="mb-0 mt-2">
                            <li>Calcula as médias por disciplina para todos os alunos</li>
                            <li>Gera os resultados consolidados do semestre</li>
                            <li>Cria os boletins individuais</li>
                            <li>Não pode ser desfeita automaticamente</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="processBtn">
                        <i class="fas fa-play me-2"></i>Iniciar Processamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Modal (opcional) -->
<div class="modal fade" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <h5>Processando semestre...</h5>
                <p class="text-muted" id="loadingMessage">Calculando médias por disciplina</p>
                <div class="progress mt-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- No arquivo admin/layouts/index.php, dentro da seção de scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script>
$(document).ready(function() {
    $('#semestersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc'], [4, 'asc']]
    });
});
</script>
<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#semestersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc'], [4, 'asc']]
    });
    
    // Quando selecionar um semestre, buscar informações
    $('#process_semester_id').change(function() {
        let semesterId = $(this).val();
        if (semesterId) {
            loadSemesterInfo(semesterId);
        } else {
            $('#processInfo').addClass('d-none');
        }
    });
});

// Carregar informações do semestre via AJAX
function loadSemesterInfo(semesterId) {
    $.ajax({
        url: '<?= site_url("admin/academic/semesters/info") ?>/' + semesterId,
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#processInfoContent').html('<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Carregando...</div>');
            $('#processInfo').removeClass('d-none');
        },
        success: function(response) {
            if (response.success) {
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Período:</strong> ${response.data.semester_name}</p>
                            <p><strong>Tipo:</strong> ${response.data.semester_type}</p>
                            <p><strong>Ano Letivo:</strong> ${response.data.year_name}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Início:</strong> ${response.data.start_date}</p>
                            <p><strong>Fim:</strong> ${response.data.end_date}</p>
                            <p><strong>Status:</strong> ${response.data.is_current ? '<span class="badge bg-primary">Atual</span>' : '<span class="badge bg-secondary">Não atual</span>'}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center p-2 bg-light rounded">
                                <h5>${response.stats.total_exams}</h5>
                                <small>Exames realizados</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-2 bg-light rounded">
                                <h5>${response.stats.total_students}</h5>
                                <small>Alunos matriculados</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-2 bg-light rounded">
                                <h5>${response.stats.total_results}</h5>
                                <small>Resultados já processados</small>
                            </div>
                        </div>
                    </div>
                `;
                
                if (response.stats.total_results > 0) {
                    html += `
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            Este semestre já possui resultados processados. Processar novamente irá sobrescrever os resultados existentes.
                        </div>
                    `;
                }
                
                $('#processInfoContent').html(html);
            } else {
                $('#processInfoContent').html('<div class="alert alert-danger">' + response.message + '</div>');
            }
        },
        error: function() {
            $('#processInfoContent').html('<div class="alert alert-danger">Erro ao carregar informações</div>');
        }
    });
}

// Verificar exames do semestre
function checkExams() {
    let semesterId = $('#process_semester_id').val();
    if (!semesterId) {
        alert('Selecione um semestre primeiro!');
        return;
    }
    
    window.open('<?= site_url("admin/exams?semester_id=") ?>' + semesterId, '_blank');
}

// Interceptar submit do formulário
$('#processForm').submit(function(e) {
    e.preventDefault();
    
    if (!confirm('Tem certeza que deseja processar este semestre? Esta ação não pode ser desfeita.')) {
        return;
    }
    
    // Mostrar loading
    $('#loadingModal').modal('show');
    $('#processBtn').prop('disabled', true);
    
    let messages = [
        'Calculando médias por disciplina...',
        'Processando resultados individuais...',
        'Gerando estatísticas da turma...',
        'Preparando boletins...',
        'Finalizando processamento...'
    ];
    
    let messageIndex = 0;
    let messageInterval = setInterval(function() {
        if (messageIndex < messages.length) {
            $('#loadingMessage').text(messages[messageIndex]);
            messageIndex++;
        }
    }, 2000);
    
    // Enviar formulário
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            console.log('Resposta recebida:', response); // Debug
            
            // LIMPAR INTERVALO E FECHAR MODAL
            clearInterval(messageInterval);
            $('#loadingModal').modal('hide');
            
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    html: response.message,
                    showConfirmButton: true
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: response.message,
                    showConfirmButton: true
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro AJAX:', status, error);
            console.error('Resposta:', xhr.responseText);
            
            // LIMPAR INTERVALO E FECHAR MODAL
            clearInterval(messageInterval);
            $('#loadingModal').modal('hide');
            
            let errorMsg = 'Erro ao processar semestre';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch(e) {}
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: errorMsg,
                showConfirmButton: true
            });
        },
        complete: function() {
            console.log('Requisição completada'); // Debug
            $('#processBtn').prop('disabled', false);
        }
    });
});
</script>
<?= $this->endSection() ?>