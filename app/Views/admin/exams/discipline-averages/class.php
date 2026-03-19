<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
/* Estilos adicionais específicos da página */
.table tbody td {
    vertical-align: middle;
    padding: 0.85rem 1rem;
    border-bottom: 1px solid var(--border);
    font-size: 0.9rem;
}

.table thead th {
    background: var(--surface);
    color: var(--text-secondary);
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 1rem;
    white-space: nowrap;
    border-bottom: 1.5px solid var(--border);
}

.table tbody tr:hover {
    background: #F5F8FF;
}

/* Badges de notas */
.grade-badge {
    font-family: var(--font-mono);
    font-weight: 600;
    font-size: 0.9rem;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    display: inline-block;
    text-align: center;
    min-width: 60px;
}

.grade-badge.success {
    background: rgba(22,168,125,0.15);
    color: var(--success);
    border: 1px solid rgba(22,168,125,0.3);
}

.grade-badge.warning {
    background: rgba(232,160,32,0.15);
    color: var(--warning);
    border: 1px solid rgba(232,160,32,0.3);
}

.grade-badge.danger {
    background: rgba(232,70,70,0.15);
    color: var(--danger);
    border: 1px solid rgba(232,70,70,0.3);
}

.grade-badge.secondary {
    background: rgba(107,122,153,0.15);
    color: var(--text-secondary);
    border: 1px solid rgba(107,122,153,0.3);
}

/* Progress bar container */
.progress-container {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.progress-bar-custom {
    flex: 1;
    height: 8px;
    background: var(--border);
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s ease;
}

.progress-fill.success { background: var(--success); }
.progress-fill.warning { background: var(--warning); }
.progress-fill.danger { background: var(--danger); }
.progress-fill.info { background: var(--accent); }

/* Student row hover effect */
.student-row {
    transition: all 0.2s ease;
    cursor: pointer;
}

.student-row:hover {
    background: #F0F4FF !important;
    transform: translateX(4px);
    box-shadow: -2px 0 0 var(--accent);
}

/* Average cell */
.average-cell {
    font-family: var(--font-mono);
    font-weight: 700;
    font-size: 1.1rem;
}

/* Discipline code chip */
.discipline-chip {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: rgba(59,127,232,0.1);
    color: var(--accent);
    border-radius: 16px;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    border: 1px solid rgba(59,127,232,0.2);
}

/* Empty state */
.empty-state {
    padding: 3rem;
    text-align: center;
}

.empty-state i {
    font-size: 3rem;
    color: var(--text-muted);
    opacity: 0.3;
    margin-bottom: 1rem;
}

.empty-state h5 {
    color: var(--text-secondary);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--text-muted);
    font-size: 0.9rem;
}
</style>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1>
                <i class="fas fa-chart-line me-2" style="opacity:.85"></i>
                Médias da Turma
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/classes') ?>">Turmas</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/classes/view/' . $class['id']) ?>"><?= $class['class_name'] ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Médias - <?= $semester['semester_name'] ?></li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <div class="d-flex gap-2">
                <a href="<?= site_url('admin/exports/class-averages/' . $class['id'] . '/' . $semester['id'] . '/excel') ?>" class="hdr-btn success">
                    <i class="fas fa-file-excel me-1"></i> Excel
                </a>
                <a href="<?= site_url('admin/exports/class-averages/' . $class['id'] . '/' . $semester['id'] . '/pdf') ?>" class="hdr-btn danger">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações da Turma e Semestre -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card h-100">
            <div class="stat-icon blue">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <div class="stat-label">Turma</div>
                <div class="stat-value"><?= $class['class_name'] ?></div>
                <div class="stat-sub">
                    <?= $class['level_name'] ?? '' ?> • <?= $class['year_name'] ?? '' ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="stat-card h-100">
            <div class="stat-icon green">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div>
                <div class="stat-label">Semestre</div>
                <div class="stat-value"><?= $semester['semester_name'] ?></div>
                <div class="stat-sub">
                    <?= date('d/m/Y', strtotime($semester['start_date'])) ?> — <?= date('d/m/Y', strtotime($semester['end_date'])) ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="stat-card h-100">
            <div class="stat-icon purple">
                <i class="fas fa-chart-simple"></i>
            </div>
            <div>
                <div class="stat-label">Média Geral</div>
                <div class="stat-value <?= $classStats['overall_average'] >= 10 ? 'text-success' : 'text-warning' ?>">
                    <?= number_format($classStats['overall_average'], 2) ?>
                </div>
                <div class="stat-sub">
                    <span class="text-success"><?= $classStats['approved_students'] ?> aprovados</span> • 
                    <span class="text-danger"><?= $classStats['failed_students'] ?> reprovados</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cards de Estatísticas Detalhadas -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon green me-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="stat-label">Aprovados</div>
                    <div class="stat-value"><?= $classStats['approved_students'] ?></div>
                    <div class="stat-sub">
                        <?= $classStats['total_students'] > 0 ? round(($classStats['approved_students'] / $classStats['total_students']) * 100) : 0 ?>% da turma
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon amber me-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <div class="stat-label">Em Recurso</div>
                    <div class="stat-value"><?= $classStats['appeal_students'] ?></div>
                    <div class="stat-sub">
                        <?= $classStats['total_students'] > 0 ? round(($classStats['appeal_students'] / $classStats['total_students']) * 100) : 0 ?>% da turma
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon red me-3">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <div class="stat-label">Reprovados</div>
                    <div class="stat-value"><?= $classStats['failed_students'] ?></div>
                    <div class="stat-sub">
                        <?= $classStats['total_students'] > 0 ? round(($classStats['failed_students'] / $classStats['total_students']) * 100) : 0 ?>% da turma
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon navy me-3">
                    <i class="fas fa-percent"></i>
                </div>
                <div>
                    <div class="stat-label">Taxa Aprovação</div>
                    <div class="stat-value text-success">
                        <?= $classStats['total_students'] > 0 ? round(($classStats['approved_students'] / $classStats['total_students']) * 100, 1) : 0 ?>%
                    </div>
                    <div class="stat-sub">
                        <div class="progress-bar-custom" style="width: 100px;">
                            <div class="progress-fill success" style="width: <?= $classStats['total_students'] > 0 ? round(($classStats['approved_students'] / $classStats['total_students']) * 100) : 0 ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas por Disciplina -->
<div class="ci-card mb-4">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-chart-pie me-2 text-accent"></i>
            Desempenho por Disciplina
        </div>
    </div>
    <div class="table-responsive">
        <table class="ci-table">
            <thead>
                <tr>
                    <th>Disciplina</th>
                    <th class="text-center">Código</th>
                    <th class="text-center">Média</th>
                    <th class="text-center">Máxima</th>
                    <th class="text-center">Mínima</th>
                    <th class="text-center">Aprovados</th>
                    <th class="text-center">Recurso</th>
                    <th class="text-center">Reprovados</th>
                    <th class="text-center">Taxa Aprovação</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($disciplineStats)): ?>
                    <?php foreach ($disciplineStats as $stat): ?>
                        <?php
                        $approvalRate = $stat['total_students'] > 0 
                            ? round(($stat['approved'] / $stat['total_students']) * 100, 1) 
                            : 0;
                        $avgClass = $stat['avg_score'] >= 10 ? 'success' : ($stat['avg_score'] >= 7 ? 'warning' : 'danger');
                        ?>
                        <tr>
                            <td class="fw-semibold"><?= $stat['name'] ?></td>
                            <td class="text-center">
                                <span class="discipline-chip"><?= $stat['code'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="grade-badge <?= $avgClass ?>">
                                    <?= number_format($stat['avg_score'], 1) ?>
                                </span>
                            </td>
                            <td class="text-center text-success fw-bold"><?= number_format($stat['max_score'], 1) ?></td>
                            <td class="text-center text-danger fw-bold"><?= number_format($stat['min_score'], 1) ?></td>
                            <td class="text-center">
                                <span class="badge-ci success"><?= $stat['approved'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge-ci warning"><?= $stat['appeal'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge-ci danger"><?= $stat['failed'] ?></span>
                            </td>
                            <td class="text-center">
                                <div class="progress-container">
                                    <div class="progress-bar-custom">
                                        <div class="progress-fill success" style="width: <?= $approvalRate ?>%;"></div>
                                    </div>
                                    <span class="text-muted" style="font-size:0.8rem; min-width:45px;"><?= $approvalRate ?>%</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="empty-state">
                                <i class="fas fa-chart-simple"></i>
                                <p class="text-muted">Nenhuma disciplina encontrada</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Tabela de Médias por Aluno -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-users me-2 text-accent"></i>
            Médias Individuais por Aluno
        </div>
        <div class="d-flex gap-2 align-items-center">
            <div class="position-relative">
                <input type="text" 
                       class="filter-input" 
                       id="searchStudent" 
                       placeholder="Pesquisar aluno..."
                       style="padding-left: 35px; min-width: 250px;">
                <i class="fas fa-search position-absolute text-muted" 
                   style="left: 12px; top: 50%; transform: translateY(-50%);"></i>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="ci-table" id="studentsTable">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Nº Aluno</th>
                    <th>Nome do Aluno</th>
                    <?php foreach ($disciplines as $discipline): ?>
                        <th class="text-center" title="<?= $discipline['discipline_name'] ?>">
                            <?= $discipline['discipline_code'] ?>
                        </th>
                    <?php endforeach; ?>
                    <th class="text-center">Média</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)): ?>
                    <?php $i = 1; ?>
                    <?php foreach ($students as $student): ?>
                        <?php
                        $statusClass = 'secondary';
                        $statusText = 'Em Andamento';
                        
                        if ($student['overall_average'] >= 10) {
                            $statusClass = 'success';
                            $statusText = 'Aprovado';
                        } elseif ($student['overall_average'] >= 7) {
                            $statusClass = 'warning';
                            $statusText = 'Recurso';
                        } elseif ($student['overall_average'] > 0) {
                            $statusClass = 'danger';
                            $statusText = 'Reprovado';
                        }
                        ?>
                        <tr class="student-row" data-student-id="<?= $student['enrollment_id'] ?>">
                            <td class="fw-semibold text-muted"><?= $i++ ?></td>
                            <td>
                                <span class="badge-ci secondary">
                                    <?= $student['student_number'] ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= site_url('admin/discipline-averages/student/' . $student['enrollment_id'] . '/' . $semester['id']) ?>" 
                                   class="text-decoration-none fw-semibold text-primary">
                                    <i class="fas fa-user-graduate me-2"></i>
                                    <?= $student['student_name'] ?>
                                </a>
                            </td>
                            <?php foreach ($disciplines as $discipline): ?>
                                <td class="text-center">
                                    <?php if (isset($student['averages'][$discipline['id']])): ?>
                                        <?php 
                                        $score = $student['averages'][$discipline['id']]['score'];
                                        $scoreClass = $score >= 10 ? 'success' : ($score >= 7 ? 'warning' : 'danger');
                                        ?>
                                        <span class="grade-badge <?= $scoreClass ?>">
                                            <?= number_format($score, 1) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="text-center">
                                <span class="average-cell <?= $student['overall_average'] >= 10 ? 'text-success' : ($student['overall_average'] >= 7 ? 'text-warning' : 'text-danger') ?>">
                                    <?= number_format($student['overall_average'], 2) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge-ci <?= $statusClass ?>">
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= site_url('admin/discipline-averages/student/' . $student['enrollment_id'] . '/' . $semester['id']) ?>" 
                                   class="btn-ci info btn-sm"
                                   data-bs-toggle="tooltip" 
                                   title="Ver detalhes do aluno">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= 5 + count($disciplines) ?>" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-users"></i>
                                <h5 class="text-muted">Nenhum aluno encontrado</h5>
                                <p class="text-muted">Esta turma não possui alunos ativos.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Script de pesquisa -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Pesquisa de alunos
    const searchInput = document.getElementById('searchStudent');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('.student-row');
            
            rows.forEach(row => {
                const studentName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const studentNumber = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                
                if (studentName.includes(searchTerm) || studentNumber.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>

<?= $this->endSection() ?>