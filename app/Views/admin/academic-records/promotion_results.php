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
.stats-icon.danger { background: rgba(232,70,70,.1); color: var(--danger); }
.stats-icon.info { background: rgba(59,127,232,.1); color: var(--accent); }

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
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table-header.success { background: rgba(22,168,125,.05); color: var(--success); }
.table-header.warning { background: rgba(232,160,32,.05); color: var(--warning); }
.table-header.danger { background: rgba(232,70,70,.05); color: var(--danger); }
.table-header.info { background: rgba(59,127,232,.05); color: var(--accent); }

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

.badge-promoted {
    background: rgba(22,168,125,.1);
    color: var(--success);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-graduated {
    background: rgba(59,127,232,.1);
    color: var(--accent);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-retained {
    background: rgba(232,70,70,.1);
    color: var(--danger);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-pending {
    background: rgba(232,160,32,.1);
    color: var(--warning);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.class-tag {
    background: var(--surface);
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.875rem;
    color: var(--primary);
}

.student-name {
    font-weight: 600;
    color: var(--primary);
}

.student-number {
    color: #6B7A99;
    font-size: 0.875rem;
}
</style>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-arrow-up me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/academic-records') ?>">Pautas</a></li>
                    <li class="breadcrumb-item active">Progressão</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- Filtro por Ano Letivo -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Ano Letivo</label>
                <?php
                $academicYearModel = new \App\Models\AcademicYearModel();
                $academicYears = $academicYearModel->where('is_active', 1)->orderBy('year_name', 'DESC')->findAll();
                ?>
                <select name="academic_year" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($academicYears as $year): ?>
                    <option value="<?= $year['id'] ?>" <?= $academicYearId == $year['id'] ? 'selected' : '' ?>>
                        <?= $year['year_name'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon success">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stats-number"><?= count($promoted) ?></div>
            <div class="stats-label">Promovidos</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon info">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stats-number"><?= count($graduated) ?></div>
            <div class="stats-label">Graduados</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon danger">
                <i class="fas fa-ban"></i>
            </div>
            <div class="stats-number"><?= count($retained) ?></div>
            <div class="stats-label">Retidos</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-number"><?= count($pending) ?></div>
            <div class="stats-label">Pendentes</div>
        </div>
    </div>
</div>

<!-- Tabela de Promovidos -->
<?php if (!empty($promoted)): ?>
<div class="table-container">
    <div class="table-header success">
        <span><i class="fas fa-arrow-up me-2"></i> Alunos Promovidos (<?= count($promoted) ?>)</span>
        <span class="badge-promoted">Próximo Ano</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Aluno</th>
                    <th>Turma Anterior</th>
                    <th>Nova Turma</th>
                    <th>Nova Matrícula</th>
                    <th>Média Final</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($promoted as $index => $student): ?>
                <tr>
                    <td><?= $student->student_number ?></td>
                    <td>
                        <div class="student-name"><?= $student['first_name'] ?> <?= $student['last_name'] ?></div>
                        <div class="student-number"><?= $student->student_number ?></div>
                    </td>
                    <td>
                        <span class="class-tag">
                            <i class="fas fa-school me-1"></i> <?= $student->old_class ?? 'N/A' ?>
                        </span>
                    </td>
                    <td>
                        <span class="class-tag">
                            <i class="fas fa-arrow-right text-success me-1"></i> <?= $student->new_class ?? 'Aguardando' ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($student->new_enrollment_number): ?>
                        <span class="badge-promoted"><?= $student->new_enrollment_number ?></span>
                        <?php else: ?>
                        <span class="badge-pending">Pendente</span>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= number_format($student->final_average ?? 0, 1) ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Tabela de Graduados -->
<?php if (!empty($graduated)): ?>
<div class="table-container">
    <div class="table-header info">
        <span><i class="fas fa-graduation-cap me-2"></i> Alunos Graduados (<?= count($graduated) ?>)</span>
        <span class="badge-graduated">Concluído</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Aluno</th>
                    <th>Última Turma</th>
                    <th>Média Final</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($graduated as $student): ?>
                <tr>
                    <td><?= $student->student_number ?></td>
                    <td>
                        <div class="student-name"><?= $student['first_name'] ?> <?= $student['last_name'] ?></div>
                        <div class="student-number"><?= $student->student_number ?></div>
                    </td>
                    <td>
                        <span class="class-tag">
                            <i class="fas fa-school me-1"></i> <?= $student->class_name ?>
                        </span>
                    </td>
                    <td><strong><?= number_format($student->final_average ?? 0, 1) ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Tabela de Retidos -->
<?php if (!empty($retained)): ?>
<div class="table-container">
    <div class="table-header danger">
        <span><i class="fas fa-ban me-2"></i> Alunos Retidos (<?= count($retained) ?>)</span>
        <span class="badge-retained">Repetir Ano</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Aluno</th>
                    <th>Turma</th>
                    <th>Média Final</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($retained as $student): ?>
                <tr>
                    <td><?= $student->student_number ?></td>
                    <td>
                        <div class="student-name"><?= $student['first_name'] ?> <?= $student['last_name'] ?></div>
                        <div class="student-number"><?= $student->student_number ?></div>
                    </td>
                    <td>
                        <span class="class-tag">
                            <i class="fas fa-school me-1"></i> <?= $student->class_name ?>
                        </span>
                    </td>
                    <td><strong><?= number_format($student->final_average ?? 0, 1) ?></strong></td>
                    <td><?= $student->observations ?? 'Não atingiu média mínima' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Tabela de Pendentes -->
<?php if (!empty($pending)): ?>
<div class="table-container">
    <div class="table-header warning">
        <span><i class="fas fa-clock me-2"></i> Alunos Pendentes (<?= count($pending) ?>)</span>
        <span class="badge-pending">Aguardando Processamento</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Aluno</th>
                    <th>Turma</th>
                    <th>Média Final</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending as $student): ?>
                <tr>
                    <td><?= $student->student_number ?></td>
                    <td>
                        <div class="student-name"><?= $student['first_name'] ?> <?= $student['last_name'] ?></div>
                        <div class="student-number"><?= $student->student_number ?></div>
                    </td>
                    <td>
                        <span class="class-tag">
                            <i class="fas fa-school me-1"></i> <?= $student->class_name ?>
                        </span>
                    </td>
                    <td><strong><?= number_format($student->final_average ?? 0, 1) ?></strong></td>
                    <td>
                        <?php if ($student->promotion_status == 'pending'): ?>
                        <span class="badge-pending">Aguardando turma</span>
                        <?php else: ?>
                        <span class="badge-warning">Em análise</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Mensagem se não houver resultados -->
<?php if (empty($promoted) && empty($graduated) && empty($retained) && empty($pending)): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    Nenhum resultado de progressão encontrado para o ano letivo selecionado.
</div>
<?php endif; ?>

<?= $this->endSection() ?>