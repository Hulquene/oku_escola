<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
:root {
    --primary: #1B2B4B;
    --accent: #3B7FE8;
    --success: #16A87D;
    --warning: #E8A020;
    --surface: #F5F7FC;
    --border: #E2E8F4;
}

.page-header {
    background: linear-gradient(135deg, var(--primary) 0%, #243761 100%);
    border-radius: 12px;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    color: white;
}

.page-header h1 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.table-container {
    background: white;
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
}

.table-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border);
    font-weight: 600;
    background: var(--surface);
}

.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    background: var(--surface);
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--primary);
    border-bottom: 1px solid var(--border);
}

.table td {
    padding: 1rem;
    border-bottom: 1px solid var(--border);
    color: #1A2238;
}

.table tr:last-child td {
    border-bottom: none;
}

.table tr:hover {
    background: #F5F8FF;
}

.badge-success {
    background: rgba(22,168,125,.1);
    color: var(--success);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
}

.badge-info {
    background: rgba(59,127,232,.1);
    color: var(--accent);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
}

.student-name {
    font-weight: 600;
    color: var(--primary);
}

.student-number {
    color: #6B7A99;
    font-size: 0.875rem;
}

.btn-download {
    background: var(--accent);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-download:hover {
    background: #2C6FD4;
    transform: translateY(-1px);
    color: white;
    text-decoration: none;
}

.filter-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
</style>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-history me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/academic-documents') ?>">Documentos</a></li>
                    <li class="breadcrumb-item active">Histórico</li>
                </ol>
            </nav>
        </div>
        <a href="<?= site_url('admin/academic-documents') ?>" class="btn btn-outline-light">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- Filtros -->
<div class="filter-card">
    <form method="get" class="row g-3">
        <div class="col-md-3">
            <label class="form-label fw-bold">Tipo</label>
            <select name="type" class="form-select">
                <option value="">Todos</option>
                <option value="certificate" <?= isset($_GET['type']) && $_GET['type'] == 'certificate' ? 'selected' : '' ?>>Certificados</option>
                <option value="declaration" <?= isset($_GET['type']) && $_GET['type'] == 'declaration' ? 'selected' : '' ?>>Declarações</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">Data Inicial</label>
            <input type="date" name="start_date" class="form-control" value="<?= $_GET['start_date'] ?? '' ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">Data Final</label>
            <input type="date" name="end_date" class="form-control" value="<?= $_GET['end_date'] ?? '' ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search me-1"></i> Filtrar
            </button>
        </div>
    </form>
</div>

<!-- Lista de Documentos -->
<?php if (empty($documents)): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    Nenhum documento encontrado.
</div>
<?php else: ?>
<div class="table-container">
    <div class="table-header">
        <i class="fas fa-list me-2"></i> Histórico de Documentos Emitidos
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nº Documento</th>
                    <th>Tipo</th>
                    <th>Aluno</th>
                    <th>Ano Letivo</th>
                    <th>Data de Emissão</th>
                    <th>Emitido Por</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
                <tr>
                    <td>
                        <span class="badge-info"><?= $doc['document_number'] ?></span>
                    </td>
                    <td>
                        <?php if ($doc['document_type'] == 'certificate'): ?>
                            <span class="badge-success">
                                <i class="fas fa-certificate me-1"></i> Certificado
                            </span>
                        <?php else: ?>
                            <span class="badge-info">
                                <i class="fas fa-file-alt me-1"></i> Declaração
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="student-name"><?= $doc['student_name'] ?></div>
                        <div class="student-number"><?= $doc['student_number'] ?></div>
                    </td>
                    <td><?= $doc['year_name'] ?></td>
                    <td><?= date('d/m/Y', strtotime($doc['issue_date'])) ?></td>
                    <td><?= $doc['issuer_name'] ?></td>
                    <td class="text-center">
                        <?php if (!empty($doc['file_path']) && file_exists(FCPATH . $doc['file_path'])): ?>
                            <a href="<?= base_url($doc['file_path']) ?>" target="_blank" class="btn-download">
                                <i class="fas fa-download"></i> Download
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Arquivo não disponível</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>