<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-file-alt me-2"></i>Pauta Final - <?= $class['class_name'] ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/academic-records') ?>">Pautas</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $class['class_name'] ?></li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/academic-records/export-final/' . $class['id']) ?>" class="hdr-btn success">
                <i class="fas fa-file-excel me-1"></i> Exportar Excel
            </a>
            <button class="hdr-btn secondary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Imprimir
            </button>
            <a href="<?= site_url('admin/academic-records') ?>" class="hdr-btn info">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cabeçalho da Pauta -->
<div class="ci-card mb-4">
    <div class="ci-card-header" style="background: var(--primary);">
        <div class="ci-card-title" style="color: #fff;">
            <i class="fas fa-file-alt me-2"></i>
            <span>MAPA DE AVALIAÇÃO FINAL</span>
        </div>
    </div>
    <div class="ci-card-body">
        <div class="row">
            <div class="col-md-8">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td width="150" class="text-muted">Ano Lectivo:</td>
                        <td class="fw-semibold"><?= $class['year_name'] ?? '' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Curso:</td>
                        <td class="fw-semibold"><?= $class['course_name'] ?? 'Ensino Geral' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Classe:</td>
                        <td class="fw-semibold"><?= $class['level_name'] ?? '' ?> / <?= $class['class_shift'] ?? '' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Turma:</td>
                        <td class="fw-semibold"><?= $class['class_name'] ?? '' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Professor:</td>
                        <td class="fw-semibold"><?= ($class['teacher_first_name'] ?? 'Não') . ' ' . ($class['teacher_last_name'] ?? 'atribuído') ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4 text-end">
                <?php
                // Calcular estatísticas a partir dos dados do controller
                $aprovados = $estatisticas['aprovados'] ?? 0;
                $recurso = $estatisticas['recurso'] ?? 0;
                $reprovados = $estatisticas['reprovados'] ?? 0;
                ?>
                <div class="mb-2">
                    <span class="badge-ci primary p-2">Total de Alunos: <?= count($students ?? []) ?></span>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <span class="badge-ci success p-2">Aprovados: <?= $aprovados ?></span>
                    <span class="badge-ci warning p-2">Recurso: <?= $recurso ?></span>
                    <span class="badge-ci danger p-2">Reprovados: <?= $reprovados ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Barra de Pesquisa -->
<div class="row mb-3">
    <div class="col-md-4">
        <div class="position-relative">
            <input type="text" 
                   class="filter-input form-control" 
                   id="searchInput" 
                   placeholder="Pesquisar aluno..."
                   onkeyup="filterTable()">
            <i class="fas fa-search position-absolute text-muted" 
               style="right: 12px; top: 50%; transform: translateY(-50%);"></i>
        </div>
    </div>
</div>

<!-- Tabela de Resultados - Modelo Pauta Final -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" id="resultsTable">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="align-middle text-center" width="50">Nº</th>
                        <th rowspan="2" class="align-middle">NOME COMPLETO</th>
                        
                        <?php foreach ($disciplines ?? [] as $disc): ?>
                            <th colspan="4" class="text-center"><?= $disc['discipline_name'] ?? '' ?></th>
                        <?php endforeach; ?>
                        
                        <th rowspan="2" class="align-middle text-center" width="150">RESULTADO FINAL</th>
                    </tr>
                    <tr>
                        <?php foreach ($disciplines ?? [] as $disc): ?>
                            <th class="text-center" width="50">M1</th>
                            <th class="text-center" width="50">M2</th>
                            <th class="text-center" width="50">M3</th>
                            <th class="text-center" width="50">MFD</th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students)): ?>
                        <?php $counter = 1; ?>
                        <?php foreach ($students as $student): ?>
                            <tr class="student-row" data-student-id="<?= $student['enrollment_id'] ?? '' ?>">
                                <td class="text-center"><?= $counter++ ?></td>
                                <td class="student-name">
                                    <strong><?= $student['full_name'] ?? '' ?></strong>
                                    <br>
                                    <small class="text-muted">Nº: <?= $student['student_number'] ?? '' ?></small>
                                </td>
                                
                                <?php foreach ($disciplines as $disc): ?>
                                    <?php $notas = $student['disciplinas'][$disc['id']] ?? []; ?>
                                    
                                    <!-- M1 (Média do 1º Período) -->
                                    <td class="text-center">
                                        <?php if (isset($notas['periodo1']['mt']) && $notas['periodo1']['mt'] !== null): ?>
                                            <?= number_format($notas['periodo1']['mt'], 1) ?>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- M2 (Média do 2º Período) -->
                                    <td class="text-center">
                                        <?php if (isset($notas['periodo2']['mt']) && $notas['periodo2']['mt'] !== null): ?>
                                            <?= number_format($notas['periodo2']['mt'], 1) ?>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- M3 (Média do 3º Período) -->
                                    <td class="text-center">
                                        <?php if (isset($notas['periodo3']['mt']) && $notas['periodo3']['mt'] !== null): ?>
                                            <?= number_format($notas['periodo3']['mt'], 1) ?>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- MFD (Média Final da Disciplina) -->
                                    <td class="text-center">
                                        <?php if (isset($notas['mfd']) && $notas['mfd'] !== null): ?>
                                            <strong class="<?= $notas['mfd'] >= 10 ? 'text-success' : 'text-warning' ?>">
                                                <?= number_format($notas['mfd'], 1) ?>
                                            </strong>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                
                                <!-- Resultado Final -->
                                <td class="text-center">
                                    <?php 
                                    $resultadoArray = $student['resultado_final'] ?? [];
                                    $resultadoTexto = 'Pendente';
                                    $badgeClass = 'secondary';
                                    
                                    if (is_array($resultadoArray)) {
                                        if ($resultadoArray['transita'] ?? false) {
                                            $resultadoTexto = 'Transita';
                                            $badgeClass = 'success';
                                        } elseif ($resultadoArray['recurso'] ?? false) {
                                            $resultadoTexto = 'Recurso';
                                            $badgeClass = 'warning';
                                        } elseif ($resultadoArray['reprovado'] ?? false) {
                                            $resultadoTexto = 'Não Transita';
                                            $badgeClass = 'danger';
                                        } elseif ($resultadoArray['pendente'] ?? false) {
                                            $resultadoTexto = 'Pendente';
                                            $badgeClass = 'secondary';
                                        }
                                    } elseif (is_string($resultadoArray)) {
                                        // Fallback para string (compatibilidade)
                                        $resultadoTexto = $resultadoArray;
                                        if ($resultadoTexto == 'Transita') $badgeClass = 'success';
                                        elseif ($resultadoTexto == 'Recurso') $badgeClass = 'warning';
                                        elseif ($resultadoTexto == 'Não Transita') $badgeClass = 'danger';
                                        else $badgeClass = 'secondary';
                                    }
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?> px-3 py-2">
                                        <?php if ($badgeClass == 'success'): ?>
                                            <i class="fas fa-check me-1"></i>
                                        <?php elseif ($badgeClass == 'warning'): ?>
                                            <i class="fas fa-clock me-1"></i>
                                        <?php elseif ($badgeClass == 'danger'): ?>
                                            <i class="fas fa-times me-1"></i>
                                        <?php else: ?>
                                            <i class="fas fa-question-circle me-1"></i>
                                        <?php endif; ?>
                                        <?= $resultadoTexto ?>
                                    </span>
                                    
                                    <?php if (!empty($resultadoArray['disciplinas_recurso'])): ?>
                                        <br>
                                        <small class="text-muted">
                                            <?= count($resultadoArray['disciplinas_recurso']) ?> disciplina(s) em recurso
                                        </small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= 3 + (count($disciplines ?? []) * 4) ?>" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Nenhum aluno matriculado nesta turma.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="<?= 2 + (count($disciplines ?? []) * 4) ?>" class="text-end pe-4">
                            <strong>Média Geral da Turma:</strong>
                        </td>
                        <td colspan="2">
                            <strong><?= number_format($estatisticas['mediaTurma'] ?? 0, 1) ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="<?= 2 + (count($disciplines ?? []) * 4) ?>" class="text-end pe-4">
                            <strong>Taxa de Aprovação:</strong>
                        </td>
                        <td colspan="2">
                            <strong><?= ($estatisticas['taxaAprovacao'] ?? 0) ?>%</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Legenda -->
<div class="card mt-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <p class="mb-0 small text-muted">
                    <i class="fas fa-info-circle text-primary me-1"></i>
                    <strong>Legenda:</strong> M1 = Média do 1º Período | M2 = Média do 2º Período | 
                    M3 = Média do 3º Período | MFD = Média Final da Disciplina
                </p>
            </div>
            <div class="col-md-4 text-end">
                <p class="mb-0">
                    <span class="badge bg-light text-dark">Aprovado: MFD ≥ 10</span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Espaço para assinaturas -->
<div class="row mt-5 mb-3 no-print">
    <div class="col-md-4 text-center">
        <div class="border-top pt-2">O Professor</div>
    </div>
    <div class="col-md-4 text-center">
        <div class="border-top pt-2">O Coordenador</div>
    </div>
    <div class="col-md-4 text-center">
        <div class="border-top pt-2">O Director</div>
    </div>
</div>

<!-- Estilos adicionais -->
<style>
.table thead th {
    background-color: #f2f2f2;
    vertical-align: middle;
    font-size: 0.85rem;
    white-space: nowrap;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-weight: 500;
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
}

.table td {
    vertical-align: middle;
    font-size: 0.9rem;
}

.text-success {
    color: #28a745 !important;
}

.text-warning {
    color: #ffc107 !important;
}

@media print {
    .btn, .breadcrumb, .hdr-actions, .no-print, .filter-input {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    
    .table {
        font-size: 10px !important;
    }
    
    .table td, .table th {
        padding: 4px !important;
    }
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function filterTable() {
    const input = document.getElementById('searchInput');
    if (!input) return;
    
    const filter = input.value.toLowerCase().trim();
    const rows = document.querySelectorAll('.student-row');
    
    rows.forEach(row => {
        const nameElement = row.querySelector('.student-name');
        if (nameElement) {
            const text = nameElement.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        }
    });
}

// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
<?= $this->endSection() ?>