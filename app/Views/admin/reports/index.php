<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Relatórios</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Alunos</h6>
                        <h2 class="mb-0"><?= number_format($totalStudents) ?></h2>
                    </div>
                    <i class="fas fa-user-graduate fa-3x opacity-50"></i>
                </div>
                <small>Matrículas ativas: <?= number_format($activeEnrollments) ?></small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Receitas</h6>
                        <h2 class="mb-0"><?= number_format($totalPaid, 0, ',', '.') ?> Kz</h2>
                    </div>
                    <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                </div>
                <small>Pendente: <?= number_format($totalPending, 0, ',', '.') ?> Kz</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Despesas</h6>
                        <h2 class="mb-0"><?= number_format($totalExpenses, 0, ',', '.') ?> Kz</h2>
                    </div>
                    <i class="fas fa-chart-line fa-3x opacity-50"></i>
                </div>
                <small>Total do ano</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Presenças Hoje</h6>
                        <h2 class="mb-0"><?= number_format($attendanceToday) ?></h2>
                    </div>
                    <i class="fas fa-calendar-check fa-3x opacity-50"></i>
                </div>
                <small>Total de registros</small>
            </div>
        </div>
    </div>
</div>

<!-- Report Categories -->
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-graduation-cap fa-4x text-primary mb-3"></i>
                <h5 class="card-title">Relatórios Académicos</h5>
                <p class="card-text text-muted">Desempenho por turma, médias, aprovações e estatísticas académicas.</p>
                <a href="<?= site_url('admin/reports/academic') ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-right"></i> Acessar
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-4x text-success mb-3"></i>
                <h5 class="card-title">Relatórios Financeiros</h5>
                <p class="card-text text-muted">Receitas, despesas, fluxo de caixa e análises financeiras.</p>
                <a href="<?= site_url('admin/reports/financial') ?>" class="btn btn-outline-success">
                    <i class="fas fa-arrow-right"></i> Acessar
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-users fa-4x text-info mb-3"></i>
                <h5 class="card-title">Relatórios de Alunos</h5>
                <p class="card-text text-muted">Listagens, distribuição por turma, idade, género e mais.</p>
                <a href="<?= site_url('admin/reports/students') ?>" class="btn btn-outline-info">
                    <i class="fas fa-arrow-right"></i> Acessar
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-calendar-check fa-4x text-warning mb-3"></i>
                <h5 class="card-title">Relatórios de Presenças</h5>
                <p class="card-text text-muted">Taxas de presença por turma, disciplina e período.</p>
                <a href="<?= site_url('admin/reports/attendance') ?>" class="btn btn-outline-warning">
                    <i class="fas fa-arrow-right"></i> Acessar
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-pencil-alt fa-4x text-danger mb-3"></i>
                <h5 class="card-title">Relatórios de Exames</h5>
                <p class="card-text text-muted">Resultados por exame, médias, aprovações e reprovações.</p>
                <a href="<?= site_url('admin/reports/exams') ?>" class="btn btn-outline-danger">
                    <i class="fas fa-arrow-right"></i> Acessar
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-chalkboard-teacher fa-4x text-secondary mb-3"></i>
                <h5 class="card-title">Relatórios de Professores</h5>
                <p class="card-text text-muted">Carga horária, disciplinas lecionadas e distribuição.</p>
                <a href="<?= site_url('admin/reports/teachers') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right"></i> Acessar
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-coins fa-4x text-primary mb-3"></i>
                <h5 class="card-title">Relatórios de Propinas</h5>
                <p class="card-text text-muted">Pagamentos, pendências, recibos e análises por turma.</p>
                <a href="<?= site_url('admin/reports/fees') ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-right"></i> Acessar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mt-4">
    <div class="card-header">
        <i class="fas fa-download"></i> Exportar Relatórios
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-2">
                <a href="<?= site_url('admin/reports/export/students') ?>" class="btn btn-outline-success w-100">
                    <i class="fas fa-file-excel"></i> Alunos (Excel)
                </a>
            </div>
            <div class="col-md-3 mb-2">
                <a href="<?= site_url('admin/reports/export/financial') ?>" class="btn btn-outline-success w-100">
                    <i class="fas fa-file-excel"></i> Financeiro (Excel)
                </a>
            </div>
            <div class="col-md-3 mb-2">
                <a href="<?= site_url('admin/reports/export/attendance') ?>" class="btn btn-outline-success w-100">
                    <i class="fas fa-file-excel"></i> Presenças (Excel)
                </a>
            </div>
            <div class="col-md-3 mb-2">
                <a href="<?= site_url('admin/reports/export/exams') ?>" class="btn btn-outline-success w-100">
                    <i class="fas fa-file-excel"></i> Exames (Excel)
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>