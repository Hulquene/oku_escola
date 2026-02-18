<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <h1><?= $title ?></h1>
    <div>
        <button type="button" class="btn btn-success" id="generateSelected">
            <i class="fas fa-file-pdf me-2"></i> Gerar Selecionados
        </button>
    </div>
</div>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Filtros -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <label for="user_type" class="form-label">Tipo de Usuário</label>
                <select class="form-select" id="user_type" name="user_type">
                    <option value="">Todos</option>
                    <option value="student" <?= ($_GET['user_type'] ?? '') == 'student' ? 'selected' : '' ?>>Alunos</option>
                    <option value="teacher" <?= ($_GET['user_type'] ?? '') == 'teacher' ? 'selected' : '' ?>>Professores</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="document_type" class="form-label">Tipo de Documento</label>
                <select class="form-select" id="document_type" name="document_type">
                    <option value="">Todos</option>
                    <?php 
                    $docTypes = [
                        'CERT_MATRICULA' => 'Certificado de Matrícula',
                        'DECL_FREQUENCIA' => 'Declaração de Frequência',
                        'HISTORICO_NOTAS' => 'Histórico de Notas',
                        'CERT_CONCLUSAO' => 'Certificado de Conclusão',
                        'DECL_APROVEITAMENTO' => 'Declaração de Aproveitamento',
                        'ATESTADO_MATRICULA' => 'Atestado de Matrícula',
                        'DECL_SERVICO' => 'Declaração de Serviço',
                        'CERT_TRABALHO' => 'Certificado de Trabalho',
                        'DECL_VENCIMENTO' => 'Declaração de Vencimento'
                    ];
                    foreach ($docTypes as $code => $name): 
                    ?>
                        <option value="<?= $code ?>" <?= ($_GET['document_type'] ?? '') == $code ? 'selected' : '' ?>>
                            <?= $name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">Data Inicial</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="<?= $_GET['date_from'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">Data Final</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="<?= $_GET['date_to'] ?? '' ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Solicitações Pendentes -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-clock text-warning me-2"></i>
            Solicitações Pendentes (<?= count($requests) ?>)
        </h5>
        <div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAll">
                <i class="fas fa-check-double me-1"></i> Selecionar Todos
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($requests)): ?>
            <form id="bulkGenerateForm" action="<?= site_url('admin/document-generator/generate/bulk') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="checkAll" class="form-check-input">
                                </th>
                                <th>Nº Solicitação</th>
                                <th>Solicitante</th>
                                <th>Documento</th>
                                <th>Data</th>
                                <th>Formato</th>
                                <th>Qtd</th>
                                <th>Taxa</th>
                                <th>Pagamento</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $req): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="<?= $req->id ?>" class="form-check-input row-checkbox">
                                    </td>
                                    <td><code><?= $req->request_number ?></code></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-<?= $req->user_type == 'student' ? 'primary' : 'success' ?> rounded-circle me-2">
                                                <?= strtoupper(substr($req->user_type, 0, 1)) ?>
                                            </div>
                                            <div>
                                                <span class="fw-semibold d-block"><?= $req->user_name ?? 'N/A' ?></span>
                                                <small class="text-muted">
                                                    <?= $req->user_type == 'student' ? 'Aluno' : 'Professor' ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold"><?= $req->document_type ?></span>
                                        <br>
                                        <small class="text-muted"><?= $req->document_code ?></small>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($req->created_at)) ?></td>
                                    <td>
                                        <?php
                                        $formatLabels = [
                                            'digital' => 'Digital',
                                            'impresso' => 'Impresso',
                                            'ambos' => 'Digital + Impresso'
                                        ];
                                        echo $formatLabels[$req->format] ?? $req->format;
                                        ?>
                                    </td>
                                    <td class="text-center"><?= $req->quantity ?></td>
                                    <td class="fw-bold"><?= number_format($req->fee_amount, 2, ',', '.') ?> Kz</td>
                                    <td>
                                        <?php if ($req->payment_status == 'paid'): ?>
                                            <span class="badge bg-success">Pago</span>
                                        <?php elseif ($req->payment_status == 'pending' && $req->fee_amount > 0): ?>
                                            <span class="badge bg-warning">Pendente</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Isento</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= site_url('admin/document-generator/preview/' . $req->id) ?>" 
                                               class="btn btn-outline-info" title="Pré-visualizar" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= site_url('admin/document-generator/generate/' . $req->id) ?>" 
                                               class="btn btn-outline-success" title="Gerar Documento">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php if ($req->purpose): ?>
                                <tr class="table-light">
                                    <td colspan="10" class="py-1">
                                        <small><i class="fas fa-comment me-1 text-muted"></i> Finalidade: <?= $req->purpose ?></small>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-info text-center py-4">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h5>Nenhuma solicitação pendente</h5>
                <p>Todas as solicitações foram processadas.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('selectAll').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
    });
    
    document.getElementById('checkAll').checked = !allChecked;
});

document.getElementById('checkAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
    });
});

document.getElementById('generateSelected').addEventListener('click', function() {
    const selected = document.querySelectorAll('.row-checkbox:checked');
    
    if (selected.length === 0) {
        alert('Selecione pelo menos uma solicitação');
        return;
    }
    
    if (confirm(`Deseja gerar ${selected.length} documento(s)?`)) {
        document.getElementById('bulkGenerateForm').submit();
    }
});
</script>

<style>
.avatar {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
}
</style>

<?= $this->endSection() ?>