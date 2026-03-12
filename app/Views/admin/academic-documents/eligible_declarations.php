<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
:root {
    --primary: #1B2B4B;
    --accent: #3B7FE8;
    --success: #16A87D;
    --warning: #E8A020;
    --danger: #E84646;
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

.stats-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.5rem;
    height: 100%;
}

.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.stats-icon.success { background: rgba(22,168,125,.1); color: var(--success); }
.stats-icon.warning { background: rgba(232,160,32,.1); color: var(--warning); }

.stats-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 0.25rem;
}

.stats-label {
    color: #6B7A99;
    font-size: 0.875rem;
}

.table-container {
    background: white;
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    margin-top: 1rem;
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
}

.badge-warning {
    background: rgba(232,160,32,.1);
    color: var(--warning);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.btn-generate {
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

.btn-generate:hover {
    background: #2C6FD4;
    transform: translateY(-1px);
    color: white;
    text-decoration: none;
}

.btn-generate:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
}

.student-name {
    font-weight: 600;
    color: var(--primary);
}

.student-number {
    color: #6B7A99;
    font-size: 0.875rem;
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
            <h1><i class="fas fa-file-alt me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/academic-documents') ?>">Documentos</a></li>
                    <li class="breadcrumb-item active">Declarações</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- Filtro por Ano Letivo -->
<div class="filter-card">
    <form method="get" class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-bold">Ano Letivo</label>
            <select name="academic_year" class="form-select" onchange="this.form.submit()">
                <?php foreach ($academicYears as $year): ?>
                <option value="<?= $year['id'] ?>" <?= $academicYearId == $year['id'] ? 'selected' : '' ?>>
                    <?= $year['year_name'] ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-8 text-end">
            <a href="<?= site_url('admin/academic-documents/eligible-certificates?academic_year=' . $academicYearId) ?>" class="btn btn-outline-primary">
                <i class="fas fa-certificate me-1"></i> Ver Certificados
            </a>
        </div>
    </form>
</div>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="stats-card">
            <div class="stats-icon success">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stats-number"><?= count(array_filter($students, function($s) { return !$s['declaration_issued']; })) ?></div>
            <div class="stats-label">Pendentes</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stats-card">
            <div class="stats-icon warning">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-number"><?= count(array_filter($students, function($s) { return $s['declaration_issued']; })) ?></div>
            <div class="stats-label">Emitidos</div>
        </div>
    </div>
</div>

<!-- Lista de Alunos -->
<?php if (empty($students)): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    Nenhum aluno apto para declaração encontrado no ano letivo selecionado.
</div>
<?php else: ?>
<div class="table-container">
    <div class="table-header">
        <i class="fas fa-list me-2"></i> Alunos Aptos para Declaração
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Aluno</th>
                    <th>Classe</th>
                    <th>Curso</th>
                    <th>Ano Letivo</th>
                    <th>Média</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= $student['student_number'] ?></td>
                    <td>
                        <div class="student-name"><?= $student['first_name'] ?> <?= $student['last_name'] ?></div>
                        <div class="student-number"><?= $student['student_number'] ?></div>
                    </td>
                    <td><?= $student['level_name'] ?></td>
                    <td><?= $student['course_name'] ?? 'Ensino Geral' ?></td>
                    <td><?= $student['year_name'] ?></td>
                    <td><strong><?= number_format($student['final_average'], 1) ?></strong></td>
                    <td>
                        <?php if ($student['declaration_issued']): ?>
                            <span class="badge-success">
                                <i class="fas fa-check-circle me-1"></i> Emitido
                            </span>
                        <?php else: ?>
                            <span class="badge-warning">
                                <i class="fas fa-clock me-1"></i> Pendente
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if (!$student['declaration_issued']): ?>
                            <a href="<?= site_url('admin/academic-documents/generate-declaration/' . $student['enrollment_id']) ?>" 
                               class="btn-generate"
                               onclick="return confirm('Gerar declaração para <?= $student['first_name'] ?> <?= $student['last_name'] ?>?')">
                                <i class="fas fa-file-alt"></i> Gerar
                            </a>
                        <?php else: ?>
                            <span class="badge-success">
                                <i class="fas fa-check"></i> Nº: <?= $student['declaration_number'] ?>
                            </span>
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