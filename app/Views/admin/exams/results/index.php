<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-chart-line me-2" style="color: var(--success);"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Resultados</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Mostrar o ano atual -->
    <div class="alert-ci info mt-3">
        <i class="fas fa-calendar-alt me-2"></i>
        Mostrando resultados do ano letivo: <strong><?= $currentYearId ?></strong>
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
                <label class="filter-label">Exame</label>
                <select class="filter-select" name="exam" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <?php foreach ($exams as $exam): ?>
                        <option value="<?= $exam['id'] ?>" <?= $selectedExam == $exam['id'] ? 'selected' : '' ?>>
                            <?= date('d/m/Y', strtotime($exam['exam_date'])) ?> - <?= $exam['discipline_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="filter-label">Turma</label>
                <select class="filter-select" name="class" onchange="this.form.submit()">
                    <option value="">Todas</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['id'] ?>" <?= $selectedClass == $class['id'] ? 'selected' : '' ?>>
                            <?= $class['class_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="filter-label">Disciplina</label>
                <select class="filter-select" name="discipline" onchange="this.form.submit()">
                    <option value="">Todas</option>
                    <?php foreach ($disciplines as $disc): ?>
                        <option value="<?= $disc['id'] ?>" <?= $selectedDiscipline == $disc['id'] ? 'selected' : '' ?>>
                            <?= $disc['discipline_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="filter-label">Semestre</label>
                <select class="filter-select" name="semester" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= $sem['id'] ?>" <?= $selectedSemester == $sem['id'] ? 'selected' : '' ?>>
                            <?= $sem['semester_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Tabela de Resultados com DataTable -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-table"></i>
            <span>Lista de Resultados</span>
        </div>
        <span class="badge-ci primary">
            <i class="fas fa-list me-1"></i> <?= count($results) ?> registros
        </span>
    </div>
    <div class="ci-card-body p0">
        <?php if (!empty($results)): ?>
            <div class="table-responsive">
                <table id="resultsTable" class="ci-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Aluno</th>
                            <th>Turma</th>
                            <th>Disciplina</th>
                            <th>Tipo</th>
                            <th class="center">Nota</th>
                            <th class="center">Status</th>
                            <th class="center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $result): ?>
                            <?php
                            $statusClass = $result['score'] >= 10 ? 'success' : ($result['score'] >= 7 ? 'warning' : 'danger');
                            $statusText = $result['score'] >= 10 ? 'Aprovado' : ($result['score'] >= 7 ? 'Recurso' : 'Reprovado');
                            ?>
                            <tr>
                                <td>
                                    <?= date('d/m/Y', strtotime($result['exam_date'])) ?>
                                    <?php if ($result['exam_time']): ?>
                                        <br><small class="text-muted"><?= substr($result['exam_time'], 0, 5) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="teacher-initials me-2" style="width: 35px; height: 35px; font-size: 0.9rem; background: var(--accent);">
                                            <?= strtoupper(substr($result['first_name'], 0, 1) . substr($result['last_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <span class="fw-semibold"><?= $result['first_name'] ?> <?= $result['last_name'] ?></span>
                                            <br>
                                            <small class="text-muted"><?= $result['student_number'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $result['class_name'] ?></td>
                                <td>
                                    <?= $result['discipline_name'] ?>
                                    <br>
                                    <small class="text-muted"><?= $result['board_name'] ?></small>
                                </td>
                                <td>
                                    <span class="badge-ci info"><?= $result['board_type'] ?></span>
                                </td>
                                <td class="center">
                                    <span class="num-chip" style="font-size: 1rem; <?= $statusClass == 'success' ? 'background: rgba(22,168,125,0.15); color: var(--success);' : ($statusClass == 'warning' ? 'background: rgba(232,160,32,0.15); color: var(--warning);' : 'background: rgba(232,70,70,0.15); color: var(--danger);') ?>">
                                        <?= number_format($result['score'], 1) ?>
                                    </span>
                                </td>
                                <td class="center">
                                    <span class="badge-ci <?= $statusClass ?> p-2">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="center">
                                    <div class="action-group">
                                        <a href="<?= site_url('admin/students/view/' . $result['student_id']) ?>" 
                                           class="row-btn view" title="Ver aluno">
                                            <i class="fas fa-user"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-chart-line"></i>
                <h5>Nenhum resultado encontrado</h5>
                <p class="text-muted">
                    Tente ajustar os filtros ou registe notas de exames.
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
            order: [[0, 'desc']], // Ordenar por data decrescente
            pageLength: 25,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
            },
            columnDefs: [
                { orderable: false, targets: [7] } // Desabilitar ordenação na coluna de ações
            ]
        });
    } else {
        // Fallback se a função global não existir
        $('#resultsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
            },
            order: [[0, 'desc']],
            pageLength: 25,
            dom: 'rtip',
            columnDefs: [
                { orderable: false, targets: [7] }
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

.num-chip {
    font-family: var(--font-mono);
    font-size: 1rem;
    font-weight: 700;
    padding: 0.2rem 0.6rem;
    border-radius: 5px;
    display: inline-block;
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

/* Responsividade */
@media (max-width: 768px) {
    .ci-table td {
        min-width: 120px;
    }
    
    .ci-table td:first-child {
        min-width: auto;
    }
    
    .filter-grid {
        grid-template-columns: 1fr;
    }
}

/* Animação */
.ci-card {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
<?= $this->endSection() ?>