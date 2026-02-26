<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Pauta Final - <?= $class->class_name ?></h1>
        <div>
            <button class="btn btn-success" onclick="exportPauta()">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </button>
            <button class="btn btn-secondary" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <a href="<?= site_url('admin/academic-records') ?>" class="btn btn-info">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/academic-records') ?>">Pautas</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $class->class_name ?></li>
        </ol>
    </nav>
</div>

<!-- Cabeçalho da Pauta -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>MAPA DE AVALIAÇÃO FINAL</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="150"><strong>Ano Lectivo:</strong></td>
                        <td><?= $class->year_name ?></td>
                    </tr>
                    <tr>
                        <td><strong>Curso:</strong></td>
                        <td><?= $class->course_name ?? 'Ensino Geral' ?></td>
                    </tr>
                    <tr>
                        <td><strong>Classe:</strong></td>
                        <td><?= $class->level_name ?> / <?= $class->class_shift ?></td>
                    </tr>
                    <tr>
                        <td><strong>Turma:</strong></td>
                        <td><?= $class->class_name ?></td>
                    </tr>
                    <tr>
                        <td><strong>Professor:</strong></td>
                        <td><?= ($class->teacher_first_name ?? 'Não') . ' ' . ($class->teacher_last_name ?? 'atribuído') ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4 text-end">
                <p class="mb-1"><strong>Total de Alunos:</strong> <?= count($students) ?></p>
                <p class="mb-0">
                    <span class="badge bg-success me-1">Aprovados: 0</span>
                    <span class="badge bg-warning me-1">Recurso: 0</span>
                    <span class="badge bg-danger">Reprovados: 0</span>
                </p>
            </div>
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
                        
                        <?php foreach ($disciplines as $disc): ?>
                            <th colspan="4" class="text-center"><?= $disc->discipline_name ?></th>
                        <?php endforeach; ?>
                        
                        <th rowspan="2" class="align-middle text-center" width="120">RESULTADO FINAL</th>
                    </tr>
                    <tr>
                        <?php foreach ($disciplines as $disc): ?>
                            <th class="text-center" width="50">M1</th>
                            <th class="text-center" width="50">MT2</th>
                            <th class="text-center" width="50">MT3</th>
                            <th class="text-center" width="50">MFD</th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students)): ?>
                        <?php $counter = 1; ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td class="text-center"><?= $counter++ ?></td>
                                <td>
                                    <strong><?= $student->full_name ?></strong>
                                    <br>
                                    <small class="text-muted">Nº: <?= $student->student_number ?></small>
                                </td>
                                
                                <?php foreach ($disciplines as $disc): ?>
                                    <?php $notas = $student->disciplinas[$disc->id] ?? []; ?>
                                    
                                    <!-- M1 (Média do 1º Trimestre) -->
                                    <td class="text-center <?= isset($notas['trimestre1']['mt']) ? '' : 'text-muted' ?>">
                                        <?= $notas['trimestre1']['mt'] ?? '—' ?>
                                    </td>
                                    
                                    <!-- MT2 (Média do 2º Trimestre) -->
                                    <td class="text-center <?= isset($notas['trimestre2']['mt']) ? '' : 'text-muted' ?>">
                                        <?= $notas['trimestre2']['mt'] ?? '—' ?>
                                    </td>
                                    
                                    <!-- MT3 (Média do 3º Trimestre) -->
                                    <td class="text-center <?= isset($notas['trimestre3']['mt']) ? '' : 'text-muted' ?>">
                                        <?= $notas['trimestre3']['mt'] ?? '—' ?>
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
                                    <?php if ($student->resultado_final == 'Transita'): ?>
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-check me-1"></i> Transita
                                        </span>
                                    <?php elseif ($student->resultado_final == 'Não Transita'): ?>
                                        <span class="badge bg-danger px-3 py-2">
                                            <i class="fas fa-times me-1"></i> Não Transita
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary px-3 py-2">
                                            <?= $student->resultado_final ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= 3 + (count($disciplines) * 4) ?>" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Nenhum aluno matriculado nesta turma.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="<?= 2 + (count($disciplines) * 4) ?>" class="text-end pe-4">
                            <strong>Média Geral da Turma:</strong>
                        </td>
                        <td colspan="2">
                            <?php
                            $somaMedias = 0;
                            $alunosComMedia = 0;
                            foreach ($students as $student) {
                                if ($student->media_final_geral > 0) {
                                    $somaMedias += $student->media_final_geral;
                                    $alunosComMedia++;
                                }
                            }
                            $mediaGeral = $alunosComMedia > 0 ? round($somaMedias / $alunosComMedia, 1) : 0;
                            ?>
                            <strong><?= number_format($mediaGeral, 1) ?></strong>
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
                    <strong>Legenda:</strong> M1 = Média do 1º Trimestre | MT2 = Média do 2º Trimestre | 
                    MT3 = Média do 3º Trimestre | MFD = Média Final da Disciplina
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
<div class="row mt-5 mb-3">
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
    font-size: 0.9rem;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-weight: 500;
    font-size: 0.85rem;
}

.table td {
    vertical-align: middle;
}

@media print {
    .btn, .breadcrumb, .page-header a, .no-print {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
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
function exportPauta() {
    const classId = '<?= $class->id ?>';
    window.location.href = '<?= site_url('admin/academic-records/export-final/') ?>' + classId;
}

function filterTable() {
    const input = document.getElementById('searchInput');
    if (!input) return;
    
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('.student-row');
    
    rows.forEach(row => {
        const nameElement = row.querySelector('.student-name');
        if (nameElement) {
            const text = nameElement.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        }
    });
}
</script>
<?= $this->endSection() ?>