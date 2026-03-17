<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-chart-pie me-2" style="color: var(--accent);"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Resultados Semestrais</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/exams') ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <div class="mt-2">
        <span class="badge-ci info">
            <i class="fas fa-chart-pie me-1"></i>
            Visualize os resultados semestrais dos alunos
        </span>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros com estilo do sistema -->
<div class="ci-card mb-4">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-filter"></i>
            <span>Filtrar Resultados</span>
        </div>
    </div>
    <div class="ci-card-body">
        <form method="get" class="filter-grid">
            <div>
                <label class="filter-label">Semestre</label>
                <select class="filter-select" name="semester" onchange="this.form.submit()">
                    <option value="">Todos os semestres</option>
                    <?php foreach ($semesters as $semester): ?>
                        <option value="<?= $semester['id'] ?>" <?= $selectedSemester == $semester['id'] ? 'selected' : '' ?>>
                            <?= $semester['semester_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="filter-label">Turma</label>
                <select class="filter-select" name="class_id" onchange="this.form.submit()">
                    <option value="">Todas as turmas</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['id'] ?>" <?= $selectedClass == $class['id'] ? 'selected' : '' ?>>
                            <?= $class['class_name'] ?> (<?= $class['class_code'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-actions" style="grid-column: -1 / 1;">
                <a href="<?= site_url('admin/semester-results') ?>" class="btn-filter clear w-100">
                    <i class="fas fa-undo me-1"></i> Limpar Filtros
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Estatísticas com estilo do sistema -->
<div class="stat-grid mb-4">
    <div class="stat-card" style="background: var(--primary); color: #fff;">
        <div class="stat-icon" style="background: rgba(255,255,255,0.15); color: #fff;">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <div>
            <div class="stat-label" style="color: rgba(255,255,255,0.7);">Total de Resultados</div>
            <div class="stat-value" style="color: #fff;"><?= $totalResults ?></div>
        </div>
    </div>
    
    <div class="stat-card" style="background: var(--success); color: #fff;">
        <div class="stat-icon" style="background: rgba(255,255,255,0.15); color: #fff;">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div>
            <div class="stat-label" style="color: rgba(255,255,255,0.7);">Resultados deste Semestre</div>
            <div class="stat-value" style="color: #fff;"><?= $resultsThisSemester ?></div>
        </div>
    </div>
</div>

<!-- Resultados Recentes com DataTable -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-history"></i>
            <span>Resultados Recentes</span>
        </div>
        <span class="badge-ci primary">
            <i class="fas fa-list me-1"></i> <?= count($recentResults) ?> registros
        </span>
    </div>
    <div class="ci-card-body p0">
        <?php if (!empty($recentResults)): ?>
            <div class="table-responsive">
                <table id="resultsTable" class="ci-table">
                    <thead>
                        <tr>
                            <th>Aluno</th>
                            <th>Nº Matrícula</th>
                            <th>Turma</th>
                            <th>Semestre</th>
                            <th class="center">Média Final</th>
                            <th class="center">Status</th>
                            <th class="center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentResults as $result): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="teacher-initials me-2" style="width: 35px; height: 35px; font-size: 0.9rem; background: var(--accent);">
                                            <?= strtoupper(substr($result['first_name'], 0, 1) . substr($result['last_name'], 0, 1)) ?>
                                        </div>
                                        <span class="fw-semibold"><?= $result['first_name'] . ' ' . $result['last_name'] ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="code-badge"><?= $result['student_number'] ?></span>
                                </td>
                                <td><?= $result['class_name'] ?></td>
                                <td><?= $result->semester_name ?></td>
                                <td class="center">
                                    <span class="num-chip <?= $result->overall_average >= 10 ? 'success' : 'danger' ?>" style="font-size: 1rem;">
                                        <?= number_format($result->overall_average, 1) ?>
                                    </span>
                                </td>
                                <td class="center">
                                    <?php
                                    $statusClass = [
                                        'Aprovado' => 'success',
                                        'Recurso' => 'warning',
                                        'Reprovado' => 'danger'
                                    ][$result->status] ?? 'secondary';
                                    ?>
                                    <span class="badge-ci <?= $statusClass ?>">
                                        <?= $result->status ?>
                                    </span>
                                </td>
                                <td class="center">
                                    <div class="action-group">
                                        <a href="<?= site_url('admin/semester-results/student/' . $result['enrollment_id'] . '/' . $result->semester_id) ?>" 
                                           class="row-btn view" title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($result->overall_average > 0): ?>
                                            <a href="<?= site_url('admin/semester-results/export/' . $result['enrollment_id'] . '/' . $result->semester_id . '?type=pdf') ?>" 
                                               class="row-btn success" title="Exportar PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Ações em massa -->
            <div class="ci-card-footer">
                <div class="d-flex justify-content-end gap-2 flex-wrap">
                    <?php if ($selectedClass && $selectedSemester): ?>
                        <a href="<?= site_url('admin/semester-results/class/' . $selectedClass . '/' . $selectedSemester) ?>" 
                           class="btn-ci primary">
                            <i class="fas fa-users me-2"></i>Ver Resultados da Turma
                        </a>
                        <a href="<?= site_url('admin/semester-results/export/' . $selectedClass . '/' . $selectedSemester . '?type=excel') ?>" 
                           class="btn-ci success">
                            <i class="fas fa-file-excel me-2"></i>Exportar Excel
                        </a>
                        <a href="<?= site_url('admin/semester-results/export/' . $selectedClass . '/' . $selectedSemester . '?type=pdf') ?>" 
                           class="btn-ci danger">
                            <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                        </a>
                        <a href="<?= site_url('admin/semester-results/generate-report-cards/' . $selectedClass . '/' . $selectedSemester) ?>" 
                           class="btn-ci warning">
                            <i class="fas fa-print me-2"></i>Gerar Pautas
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-chart-pie"></i>
                <h5>Nenhum resultado encontrado</h5>
                <p class="text-muted mb-0">
                    Os resultados semestrais aparecerão aqui após o cálculo das médias.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar DataTable com configuração padrão
    if (typeof initDataTable !== 'undefined') {
        initDataTable('#resultsTable', {
            order: [[0, 'asc']],
            pageLength: 25,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
            },
            columnDefs: [
                { orderable: false, targets: [6] }
            ]
        });
    } else {
        // Fallback se a função global não existir
        $('#resultsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
            },
            order: [[0, 'asc']],
            pageLength: 25,
            dom: 'rtip',
            columnDefs: [
                { orderable: false, targets: [6] }
            ]
        });
    }
    
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Auto-submit filters on change
document.querySelectorAll('.filter-select').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});
</script>

<style>
/* Estilos adicionais específicos para esta página */
.teacher-initials {
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    text-transform: uppercase;
    color: white;
    flex-shrink: 0;
}

.code-badge {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 600;
    background: rgba(59,127,232,0.1);
    color: var(--accent);
    padding: 0.25rem 0.5rem;
    border-radius: 5px;
    display: inline-block;
}

.num-chip {
    font-family: var(--font-mono);
    font-size: 1rem;
    font-weight: 700;
    padding: 0.2rem 0.6rem;
    border-radius: 5px;
    display: inline-block;
}

.num-chip.success {
    background: rgba(22,168,125,0.15);
    color: var(--success);
}

.num-chip.danger {
    background: rgba(232,70,70,0.15);
    color: var(--danger);
}

/* Cards coloridos */
.stat-card[style*="background: var(--primary)"] {
    border: none;
}

.stat-card[style*="background: var(--success)"] {
    border: none;
}

/* Footer do card */
.ci-card-footer {
    padding: 0.85rem 1.25rem;
    background: var(--surface);
    border-top: 1px solid var(--border);
}

/* Ajustes para DataTables */
.dataTables_wrapper {
    padding: 0.85rem 1.25rem;
}

.dataTables_filter input,
.dataTables_length select {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.3rem 0.6rem;
    font-size: 0.8rem;
    font-family: var(--font);
    color: var(--text-primary);
    background: var(--surface);
}

.dataTables_filter input:focus,
.dataTables_length select:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,0.12);
}

.dataTables_info,
.dataTables_filter label,
.dataTables_length label {
    font-size: 0.78rem;
    color: var(--text-secondary);
}

.dataTables_paginate .paginate_button {
    border-radius: 6px !important;
    font-size: 0.78rem !important;
    font-family: var(--font) !important;
}

.dataTables_paginate .paginate_button.current {
    background: var(--accent) !important;
    color: #fff !important;
    border-color: var(--accent) !important;
}

.dataTables_paginate .paginate_button:hover {
    background: rgba(59,127,232,0.1) !important;
    color: var(--accent) !important;
    border-color: var(--border) !important;
}

/* Animação */
.ci-card {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsividade */
@media (max-width: 768px) {
    .filter-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-grid {
        grid-template-columns: 1fr;
    }
    
    .ci-table td {
        min-width: 120px;
    }
    
    .ci-table td:first-child {
        min-width: auto;
    }
    
    .d-flex.gap-2 {
        flex-wrap: wrap;
    }
    
    .btn-ci {
        width: 100%;
    }
}
</style>
<?= $this->endSection() ?>