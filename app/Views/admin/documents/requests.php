<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/documents') ?>">Central de Documentos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Solicitações</li>
        </ol>
    </nav>
</div>

<!-- Filtros -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="pending" <?= ($_GET['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Pendente</option>
                    <option value="processing" <?= ($_GET['status'] ?? '') == 'processing' ? 'selected' : '' ?>>Processando</option>
                    <option value="ready" <?= ($_GET['status'] ?? '') == 'ready' ? 'selected' : '' ?>>Pronto</option>
                    <option value="delivered" <?= ($_GET['status'] ?? '') == 'delivered' ? 'selected' : '' ?>>Entregue</option>
                    <option value="cancelled" <?= ($_GET['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                    <option value="rejected" <?= ($_GET['status'] ?? '') == 'rejected' ? 'selected' : '' ?>>Rejeitado</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="user_type" class="form-label">Tipo</label>
                <select class="form-select" id="user_type" name="user_type">
                    <option value="">Todos</option>
                    <option value="student" <?= ($_GET['user_type'] ?? '') == 'student' ? 'selected' : '' ?>>Alunos</option>
                    <option value="teacher" <?= ($_GET['user_type'] ?? '') == 'teacher' ? 'selected' : '' ?>>Professores</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Nº solicitação ou nome" value="<?= $_GET['search'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">Data Inicial</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="<?= $_GET['date_from'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">Data Final</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="<?= $_GET['date_to'] ?? '' ?>">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Solicitações -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-file-signature text-primary me-2"></i>
            Solicitações de Documentos
        </h5>
        <span class="badge bg-primary"><?= count($requests) ?> solicitação(ões)</span>
    </div>
    <div class="card-body">
        <?php if (!empty($requests)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nº Solicitação</th>
                            <th>Data</th>
                            <th>Solicitante</th>
                            <th>Tipo</th>
                            <th>Formato</th>
                            <th>Valor</th>
                            <th>Pagamento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td><code><?= $req->request_number ?></code></td>
                                <td><?= date('d/m/Y', strtotime($req->created_at)) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-<?= $req->user_type == 'student' ? 'primary' : 'success' ?> rounded-circle me-2">
                                            <?= strtoupper(substr($req->user_type, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <span class="fw-semibold d-block"><?= $req->user_fullname ?? 'N/A' ?></span>
                                            <small class="text-muted">
                                                <?= $req->user_type == 'student' ? 'Aluno' : 'Professor' ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $req->document_type ?></td>
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
                                    <?php
                                    $statusLabels = [
                                        'pending' => ['warning', 'Pendente'],
                                        'processing' => ['info', 'Processando'],
                                        'ready' => ['success', 'Pronto'],
                                        'delivered' => ['secondary', 'Entregue'],
                                        'cancelled' => ['danger', 'Cancelado'],
                                        'rejected' => ['danger', 'Rejeitado']
                                    ];
                                    $status = $statusLabels[$req->status] ?? ['secondary', $req->status];
                                    ?>
                                    <span class="badge bg-<?= $status[0] ?>"><?= $status[1] ?></span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('admin/documents/request/view/' . $req->id) ?>" 
                                           class="btn btn-outline-primary" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($req->status == 'pending'): ?>
                                            <a href="<?= site_url('admin/documents/request/process/' . $req->id) ?>" 
                                               class="btn btn-outline-warning" title="Processar"
                                               onclick="return confirm('Iniciar processamento desta solicitação?')">
                                                <i class="fas fa-cog"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center py-4">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h5>Nenhuma solicitação encontrada</h5>
                <p>Não há solicitações de documentos com os filtros selecionados.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

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