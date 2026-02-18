<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <h1><?= $title ?></h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#templateModal">
        <i class="fas fa-plus me-2"></i> Novo Modelo
    </button>
</div>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Lista de Modelos -->
<div class="row g-4">
    <?php 
    $categories = [
        'certificado' => 'Certificados',
        'declaracao' => 'Declarações',
        'atestado' => 'Atestados',
        'historico' => 'Históricos',
        'outro' => 'Outros'
    ];
    
    foreach ($categories as $catKey => $catName):
        $catTemplates = array_filter($templates, function($t) use ($catKey) {
            return $t->category == $catKey;
        });
        
        if (empty($catTemplates)) continue;
    ?>
        <div class="col-12">
            <h5 class="mb-3">
                <i class="fas fa-folder-open text-primary me-2"></i>
                <?= $catName ?>
            </h5>
        </div>
        
        <?php foreach ($catTemplates as $template): ?>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle p-3 bg-primary bg-opacity-10">
                                    <i class="fas fa-file-alt fa-2x text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1"><?= $template->document_name ?></h5>
                                <code><?= $template->document_code ?></code>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Taxa</small>
                                    <strong><?= number_format($template->fee_amount, 2, ',', '.') ?> Kz</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Prazo</small>
                                    <strong><?= $template->processing_days ?> dias</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted d-block">Disponível para</small>
                            <div>
                                <?php
                                $availableLabels = [
                                    'all' => '<span class="badge bg-info">Todos</span>',
                                    'students' => '<span class="badge bg-primary">Alunos</span>',
                                    'teachers' => '<span class="badge bg-success">Professores</span>',
                                    'guardians' => '<span class="badge bg-warning">Encarregados</span>',
                                    'staff' => '<span class="badge bg-secondary">Funcionários</span>'
                                ];
                                echo $availableLabels[$template->available_for] ?? $template->available_for;
                                ?>
                            </div>
                        </div>
                        
                        <?php if ($template->description): ?>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                <?= $template->description ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                    onclick="editTemplate(<?= htmlspecialchars(json_encode($template)) ?>)">
                                <i class="fas fa-edit me-1"></i> Editar
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" 
                                    onclick="previewTemplate('<?= $template->document_code ?>')">
                                <i class="fas fa-eye me-1"></i> Prévia
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="col-12"><hr class="my-4"></div>
    <?php endforeach; ?>
</div>

<!-- Modal de Template -->
<div class="modal fade" id="templateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= site_url('admin/document-generator/template/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="template_id" id="template_id">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Novo Modelo de Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="document_code" class="form-label fw-semibold">Código</label>
                            <input type="text" class="form-control" id="document_code" name="document_code" required>
                        </div>
                        <div class="col-md-6">
                            <label for="document_name" class="form-label fw-semibold">Nome do Documento</label>
                            <input type="text" class="form-control" id="document_name" name="document_name" required>
                        </div>
                        <div class="col-md-4">
                            <label for="category" class="form-label fw-semibold">Categoria</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($categories as $key => $cat): ?>
                                    <option value="<?= $key ?>"><?= $cat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="fee_amount" class="form-label fw-semibold">Taxa (Kz)</label>
                            <input type="number" class="form-control" id="fee_amount" name="fee_amount" value="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label for="processing_days" class="form-label fw-semibold">Prazo (dias)</label>
                            <input type="number" class="form-control" id="processing_days" name="processing_days" value="3" min="1">
                        </div>
                        <div class="col-md-6">
                            <label for="available_for" class="form-label fw-semibold">Disponível para</label>
                            <select class="form-select" id="available_for" name="available_for" required>
                                <option value="">Selecione...</option>
                                <?php 
                                $availableFor = [
                                    'all' => 'Todos',
                                    'students' => 'Alunos',
                                    'teachers' => 'Professores',
                                    'guardians' => 'Encarregados',
                                    'staff' => 'Funcionários'
                                ];
                                foreach ($availableFor as $key => $label): 
                                ?>
                                    <option value="<?= $key ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">&nbsp;</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="requires_approval" name="requires_approval" value="1">
                                <label class="form-check-label" for="requires_approval">
                                    Requer aprovação prévia
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label for="template_content" class="form-label fw-semibold">Conteúdo do Modelo</label>
                            <textarea class="form-control" id="template_content" name="template_content" rows="8"></textarea>
                            <small class="text-muted">Use variáveis como {{aluno}}, {{data}}, {{numero}} etc.</small>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    Ativo
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Modelo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editTemplate(template) {
    document.getElementById('modalTitle').textContent = 'Editar Modelo de Documento';
    document.getElementById('template_id').value = template.id;
    document.getElementById('document_code').value = template.document_code;
    document.getElementById('document_name').value = template.document_name;
    document.getElementById('category').value = template.category;
    document.getElementById('fee_amount').value = template.fee_amount;
    document.getElementById('processing_days').value = template.processing_days;
    document.getElementById('available_for').value = template.available_for;
    document.getElementById('requires_approval').checked = template.requires_approval == 1;
    document.getElementById('description').value = template.description || '';
    document.getElementById('is_active').checked = template.is_active == 1;
    
    // Carregar conteúdo do template (simulado)
    document.getElementById('template_content').value = getTemplateContent(template.document_code);
    
    new bootstrap.Modal(document.getElementById('templateModal')).show();
}

function getTemplateContent(code) {
    const templates = {
        'CERT_MATRICULA': '<div class="certificado">\n    <h1>REPÚBLICA DE ANGOLA</h1>\n    <h2>MINISTÉRIO DA EDUCAÇÃO</h2>\n    <h3>{{escola}}</h3>\n    <hr>\n    <h4>CERTIFICADO DE MATRÍCULA Nº {{numero}}</h4>\n    <p>Certificamos que o(a) aluno(a) <strong>{{aluno}}</strong>, portador do BI nº {{bi}}, encontra-se devidamente matriculado(a) na <strong>{{classe}}</strong>, no ano letivo {{ano_letivo}}.</p>\n    <p>Luanda, {{data}}</p>\n    <div class="assinaturas">\n        <div class="secretario">O Secretário</div>\n        <div class="diretor">O Diretor</div>\n    </div>\n</div>',
        'DECL_FREQUENCIA': '<div class="declaracao">\n    <h1>REPÚBLICA DE ANGOLA</h1>\n    <h2>MINISTÉRIO DA EDUCAÇÃO</h2>\n    <h3>{{escola}}</h3>\n    <hr>\n    <h4>DECLARAÇÃO DE FREQUÊNCIA Nº {{numero}}</h4>\n    <p>Declaramos para os devidos fins que o(a) aluno(a) <strong>{{aluno}}</strong>, portador do BI nº {{bi}}, frequenta regularmente a <strong>{{classe}}</strong>, no período {{turno}}, no ano letivo {{ano_letivo}}.</p>\n    <p>A presente declaração é passada a pedido da interessada para fins de {{finalidade}}.</p>\n    <p>Luanda, {{data}}</p>\n    <div class="assinaturas">\n        <div class="secretario">O Secretário</div>\n        <div class="diretor">O Diretor</div>\n    </div>\n</div>',
        'HISTORICO_NOTAS': '<div class="historico">\n    <h1>REPÚBLICA DE ANGOLA</h1>\n    <h2>MINISTÉRIO DA EDUCAÇÃO</h2>\n    <h3>{{escola}}</h3>\n    <hr>\n    <h4>HISTÓRICO DE NOTAS</h4>\n    <p><strong>Aluno:</strong> {{aluno}}</p>\n    <p><strong>BI nº:</strong> {{bi}}</p>\n    <table border="1">\n        <thead>\n            <tr>\n                <th>Ano Letivo</th>\n                <th>Classe</th>\n                <th>Disciplina</th>\n                <th>Nota</th>\n            </tr>\n        </thead>\n        <tbody>\n            {% for item in historico %}\n            <tr>\n                <td>{{item.ano_letivo}}</td>\n                <td>{{item.classe}}</td>\n                <td>{{item.disciplina}}</td>\n                <td>{{item.nota}}</td>\n            </tr>\n            {% endfor %}\n        </tbody>\n    </table>\n    <p>Luanda, {{data}}</p>\n    <div class="assinaturas">\n        <div class="secretario">O Secretário</div>\n        <div class="diretor">O Diretor</div>\n    </div>\n</div>'
    };
    
    return templates[code] || 'Conteúdo do modelo...';
}

function previewTemplate(code) {
    window.open('/admin/document-generator/preview-template/' + code, '_blank');
}
</script>

<?= $this->endSection() ?>