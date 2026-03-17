<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-file-alt me-2" style="color: var(--accent);"></i>Mini Pautas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mini Pautas</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/mini-grade-sheet/trimestral') ?>" class="hdr-btn info me-2">
                <i class="fas fa-calendar-alt me-1"></i> Pautas Trimestrais
            </a>
            <a href="<?= site_url('admin/mini-grade-sheet/disciplina') ?>" class="hdr-btn success">
                <i class="fas fa-book-open me-1"></i> Pautas por Disciplina
            </a>
        </div>
    </div>
    <div class="mt-2">
        <span class="badge-ci info">
            <i class="fas fa-info-circle me-1"></i>
            Visualize e gerencie as mini pautas de exames
        </span>
    </div>
</div>

<!-- Filtros com estilo do sistema -->
<div class="ci-card mb-4">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-filter"></i>
            <span>Filtros</span>
        </div>
    </div>
    <div class="ci-card-body">
        <form action="<?= site_url('admin/mini-grade-sheet') ?>" method="get" id="filterForm">
            <div class="filter-grid">
                <div>
                    <label class="filter-label">Ano Letivo</label>
                    <select name="ano_letivo" class="filter-select" id="anoLetivo">
                        <option value="">Todos</option>
                        <?php foreach ($anosLetivos as $ano): ?>
                            <option value="<?= $ano['id'] ?>" <?= ($filters['ano_letivo'] ?? '') == $ano['id'] ? 'selected' : '' ?>>
                                <?= $ano['year_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="filter-label">Curso</label>
                    <select name="curso" class="filter-select" id="curso">
                        <option value="">Todos</option>
                        <option value="0" <?= ($filters['curso'] ?? '') === '0' ? 'selected' : '' ?>>Ensino Geral</option>
                        <?php foreach ($cursos as $curso): ?>
                            <option value="<?= $curso['id'] ?>" <?= ($filters['curso'] ?? '') == $curso['id'] ? 'selected' : '' ?>>
                                <?= $curso['course_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="filter-label">Nível</label>
                    <select name="level" class="filter-select" id="level">
                        <option value="">Todos</option>
                        <?php foreach ($levels as $level): ?>
                            <option value="<?= $level['id'] ?>" <?= ($filters['level'] ?? '') == $level['id'] ? 'selected' : '' ?>>
                                <?= $level['level_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="filter-label">Turma</label>
                    <select name="turma" class="filter-select" id="turma">
                        <option value="">Todas</option>
                        <?php foreach ($turmas as $turma): ?>
                            <option value="<?= $turma['id'] ?>" <?= ($filters['turma'] ?? '') == $turma['id'] ? 'selected' : '' ?>>
                                <?= $turma['class_name'] ?> (<?= $turma['year_name'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="filter-label">Disciplina</label>
                    <select name="disciplina" class="filter-select" id="disciplina">
                        <option value="">Todas</option>
                        <?php foreach ($disciplinas as $disc): ?>
                            <option value="<?= $disc['id'] ?>" <?= ($filters['disciplina'] ?? '') == $disc['id'] ? 'selected' : '' ?>>
                                <?= $disc['discipline_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="filter-label">Status</label>
                    <select name="status" class="filter-select">
                        <?php foreach ($statusOptions as $key => $value): ?>
                            <option value="<?= $key ?>" <?= ($filters['status'] ?? '') == $key ? 'selected' : '' ?>>
                                <?= $value ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="filter-actions mt-3">
                <button type="submit" class="btn-filter apply">
                    <i class="fas fa-search me-2"></i>Filtrar
                </button>
                <a href="<?= site_url('admin/mini-grade-sheet') ?>" class="btn-filter clear">
                    <i class="fas fa-undo me-2"></i>Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Mini Pautas -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-list"></i>
            <span>Mini Pautas</span>
        </div>
        <span class="badge-ci primary">
            <i class="fas fa-list me-1"></i> <?= count($miniPautas) ?> registros
        </span>
    </div>
    <div class="ci-card-body p0">
        <?php if (!empty($miniPautas)): ?>
            <div class="table-responsive">
                <table id="miniPautasTable" class="ci-table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="align-middle center">ID</th>
                            <th rowspan="2" class="align-middle">Ano</th>
                            <th rowspan="2" class="align-middle">Curso</th>
                            <th rowspan="2" class="align-middle">Turma</th>
                            <th rowspan="2" class="align-middle">Disciplina</th>
                            <th rowspan="2" class="align-middle">Professor</th>
                            <th rowspan="2" class="align-middle">Período</th>
                            <th rowspan="2" class="align-middle">Data</th>
                            <th colspan="2" class="center">Alunos</th>
                            <th rowspan="2" class="align-middle center">Status</th>
                            <th rowspan="2" class="align-middle center">Ações</th>
                        </tr>
                        <tr>
                            <th class="center">Total</th>
                            <th class="center">Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($miniPautas as $pauta): ?>
                            <?php
                            $statusColors = [
                                'Agendado' => 'secondary',
                                'Realizado' => 'success',
                                'Cancelado' => 'danger',
                                'Adiado' => 'warning'
                            ];
                            $statusColor = $statusColors[$pauta['status']] ?? 'secondary';
                            ?>
                            <tr>
                                <td class="center">
                                    <span class="id-chip">#<?= str_pad($pauta['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td><?= $pauta['year_name'] ?></td>
                                <td><?= $pauta['course_name'] ?? 'Ensino Geral' ?></td>
                                <td>
                                    <span class="fw-semibold"><?= $pauta['class_name'] ?></span>
                                </td>
                                <td>
                                    <?= $pauta['discipline_name'] ?>
                                    <br>
                                    <small class="text-muted"><?= $pauta['discipline_code'] ?? '' ?></small>
                                </td>
                                <td>
                                    <?php if ($pauta['teacher_first_name'] ?? ''): ?>
                                        <?= $pauta['teacher_first_name'] . ' ' . ($pauta->teacher_last_name ?? '') ?>
                                    <?php else: ?>
                                        <span class="badge-ci secondary">Não atribuído</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $pauta['period_name'] ?></td>
                                <td>
                                    <span class="code-badge">
                                        <?= date('d/m/Y', strtotime($pauta['exam_date'])) ?>
                                    </span>
                                </td>
                                <td class="center">
                                    <span class="badge-ci info"><?= $pauta['total_students'] ?? 0 ?></span>
                                </td>
                                <td class="center">
                                    <?php if (($pauta->results_count ?? 0) > 0): ?>
                                        <span class="badge-ci success"><?= $pauta->results_count ?></span>
                                    <?php else: ?>
                                        <span class="badge-ci warning">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="center">
                                    <span class="badge-ci <?= $statusColor ?>">
                                        <?= $pauta['status'] ?>
                                    </span>
                                </td>
                                <td class="center">
                                    <div class="action-group">
                                        <a href="<?= site_url('admin/mini-grade-sheet/view/' . $pauta['id']) ?>" 
                                           class="row-btn view" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('admin/mini-grade-sheet/print/' . $pauta['id']) ?>" 
                                           class="row-btn secondary" title="Imprimir" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="<?= site_url('admin/mini-grade-sheet/export/' . $pauta['id']) ?>" 
                                           class="row-btn success" title="Exportar Excel">
                                            <i class="fas fa-file-excel"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($pager): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <h5>Nenhuma mini pauta encontrada</h5>
                <p class="text-muted">Utilize os filtros para encontrar pautas específicas.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Legenda -->
<div class="ci-card mt-4">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-info-circle"></i>
            <span>Legenda de Status</span>
        </div>
    </div>
    <div class="ci-card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <span class="badge-ci secondary">Agendado</span> - Exame agendado
            </div>
            <div class="col-md-3">
                <span class="badge-ci success">Realizado</span> - Exame realizado
            </div>
            <div class="col-md-3">
                <span class="badge-ci warning">Adiado</span> - Exame adiado
            </div>
            <div class="col-md-3">
                <span class="badge-ci danger">Cancelado</span> - Exame cancelado
            </div>
        </div>
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
        initDataTable('#miniPautasTable', {
            order: [[0, 'desc']],
            pageLength: 25,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
            },
            columnDefs: [
                { orderable: false, targets: [11] }
            ]
        });
    } else {
        // Fallback se a função global não existir
        $('#miniPautasTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
            },
            order: [[0, 'desc']],
            pageLength: 25,
            dom: 'rtip',
            columnDefs: [
                { orderable: false, targets: [11] }
            ]
        });
    }
    
    // Auto-submit filters on change
    $('.filter-select').on('change', function() {
        clearTimeout(window.filterTimeout);
        window.filterTimeout = setTimeout(() => {
            $('#filterForm').submit();
        }, 800);
    });
});
</script>

<style>
/* Estilos adicionais específicos para esta página */
.id-chip {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 600;
    background: rgba(27,43,75,0.07);
    color: var(--primary);
    padding: 0.15rem 0.45rem;
    border-radius: 5px;
}

.code-badge {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 600;
    background: rgba(59,127,232,0.1);
    color: var(--accent);
    padding: 0.15rem 0.45rem;
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

/* Animações */
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
    
    .ci-table td {
        min-width: 120px;
    }
    
    .ci-table td:first-child {
        min-width: auto;
    }
    
    .hdr-actions {
        flex-wrap: wrap;
    }
}
</style>
<?= $this->endSection() ?>