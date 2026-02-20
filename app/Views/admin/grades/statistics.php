<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Estatísticas de Notas</h1>
            <p class="text-muted mb-0">Visão geral do desempenho acadêmico</p>
        </div>
        <div>
            <a href="<?= site_url('admin/grades/report') ?>" class="btn btn-info me-2">
                <i class="fas fa-chart-bar me-1"></i> Relatórios
            </a>
            <a href="<?= site_url('admin/grades') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/grades') ?>">Notas</a></li>
            <li class="breadcrumb-item active">Estatísticas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Estatísticas Gerais -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="text-white-50">Total Alunos</h6>
                <h2 class="mb-0"><?= isset($totalAlunos) ? $totalAlunos : 0 ?></h2>
                <small>Cadastrados no sistema</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="text-white-50">Turmas Ativas</h6>
                <h2 class="mb-0"><?= isset($totalTurmas) ? $totalTurmas : 0 ?></h2>
                <small>Em funcionamento</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="text-white-50">Disciplinas</h6>
                <h2 class="mb-0"><?= isset($totalDisciplinas) ? $totalDisciplinas : 0 ?></h2>
                <small>Ofertadas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="text-white-50">Média Geral</h6>
                <h2 class="mb-0">
                    <?php
                    // Calcular média geral se houver turmas com notas
                    $mediaGeralCalculada = 0;
                    $totalTurmasComMedia = 0;
                    
                    if (isset($turmas) && !empty($turmas)) {
                        foreach ($turmas as $turma) {
                            if (isset($turma->media) && $turma->media > 0) {
                                $mediaGeralCalculada += $turma->media;
                                $totalTurmasComMedia++;
                            }
                        }
                    }
                    
                    $mediaGeralFinal = $totalTurmasComMedia > 0 
                        ? number_format($mediaGeralCalculada / $totalTurmasComMedia, 1) 
                        : '-';
                    
                    echo $mediaGeralFinal;
                    ?>
                </h2>
                <small>Média das turmas</small>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos e Estatísticas Detalhadas -->
<div class="row">
    <!-- Gráfico de Desempenho por Turma -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Média por Turma</h5>
            </div>
            <div class="card-body">
                <?php if (isset($turmas) && !empty($turmas) && isset($turmas[0]->media)): ?>
                    <canvas id="classChart" style="height: 300px;"></canvas>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Sem dados para exibir</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Distribuição de Notas -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Distribuição de Notas</h5>
            </div>
            <div class="card-body">
                <?php 
                $temDistribuicao = (isset($aprovados) && $aprovados > 0) || 
                                   (isset($recurso) && $recurso > 0) || 
                                   (isset($reprovados) && $reprovados > 0);
                ?>
                <?php if ($temDistribuicao): ?>
                    <canvas id="gradesDistributionChart" style="height: 300px;"></canvas>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Sem dados para exibir</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Desempenho por Turma -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-table me-2 text-primary"></i>Desempenho por Turma</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Turma</th>
                        <th>Ano Letivo</th>
                        <th class="text-center">Alunos</th>
                        <th class="text-center">Média</th>
                        <th class="text-center">Aprovados</th>
                        <th class="text-center">Recurso</th>
                        <th class="text-center">Reprovados</th>
                        <th class="text-center">Taxa Aprov.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($turmas) && !empty($turmas)): ?>
                        <?php foreach ($turmas as $turma): ?>
                            <tr>
                                <td>
                                    <strong><?= $turma->class_name ?? 'N/A' ?></strong>
                                    <br>
                                    <small class="text-muted"><?= $turma->class_code ?? '' ?></small>
                                </td>
                                <td><?= $turma->year_name ?? '-' ?></td>
                                <td class="text-center"><?= $turma->total_alunos ?? 0 ?></td>
                                <td class="text-center">
                                    <?php if (isset($turma->media) && $turma->media > 0): ?>
                                        <span class="badge bg-<?= $turma->media >= 10 ? 'success' : 'warning' ?> p-2">
                                            <?= number_format($turma->media, 1) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center text-success"><?= $turma->aprovados ?? 0 ?></td>
                                <td class="text-center text-warning"><?= $turma->recurso ?? 0 ?></td>
                                <td class="text-center text-danger"><?= $turma->reprovados ?? 0 ?></td>
                                <td class="text-center">
                                    <?php 
                                    $taxa = 0;
                                    if (isset($turma->aprovados) && isset($turma->total_alunos) && $turma->total_alunos > 0) {
                                        $taxa = round(($turma->aprovados / $turma->total_alunos) * 100, 1);
                                    }
                                    ?>
                                    <?php if ($taxa > 0): ?>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" 
                                                 role="progressbar" 
                                                 style="width: <?= $taxa ?>%;" 
                                                 aria-valuenow="<?= $taxa ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                <?= $taxa ?>%
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum dado disponível</h5>
                                <p class="text-muted">As estatísticas aparecerão quando houver notas lançadas</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Filtros por Período -->
<div class="card mt-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filtrar Estatísticas</h5>
    </div>
    <div class="card-body">
        <form method="get" action="<?= site_url('admin/grades/statistics') ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Ano Letivo</label>
                <select class="form-select" name="academic_year" id="academicYearFilter">
                    <option value="">Todos os anos</option>
                    <?php if (isset($academicYears) && !empty($academicYears)): ?>
                        <?php 
                        // Encontrar o ID do ano atual
                        $currentYearId = null;
                        foreach ($academicYears as $year) {
                            if (!empty($year->is_current) && $year->is_current == 1) {
                                $currentYearId = $year->id;
                                break;
                            }
                        }
                        ?>
                        <?php foreach ($academicYears as $year): ?>
                            <?php 
                            // Determinar se esta opção deve ser selecionada
                            $isSelected = false;
                            if (isset($selectedYear) && $selectedYear == $year->id) {
                                $isSelected = true;
                            } elseif (!isset($selectedYear) && $year->id == $currentYearId) {
                                $isSelected = true;
                            }
                            ?>
                            <option value="<?= $year->id ?>" <?= $isSelected ? 'selected' : '' ?>>
                                <?= $year->year_name ?> <?= !empty($year->is_current) && $year->is_current ? '(Atual)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Curso</label>
                <select class="form-select" name="course">
                    <option value="">Todos</option>
                    <?php if (isset($cursos) && !empty($cursos)): ?>
                        <?php foreach ($cursos as $curso): ?>
                            <option value="<?= $curso->id ?>" <?= (isset($selectedCourse) && $selectedCourse == $curso->id) ? 'selected' : '' ?>>
                                <?= $curso->course_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sync-alt me-1"></i> Atualizar
                </button>
            </div>
        </form>
        
        <!-- Badge informativo -->
        <div class="mt-3">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Por padrão, as estatísticas mostram o ano letivo atual. Use o filtro para visualizar outros períodos.
            </small>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (isset($turmas) && !empty($turmas) && isset($turmas[0]->media)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preparar dados para o gráfico de médias por turma
    const classLabels = [];
    const classData = [];
    
    <?php foreach ($turmas as $turma): ?>
        <?php if (isset($turma->media) && $turma->media > 0): ?>
            classLabels.push('<?= addslashes($turma->class_name ?? 'Turma') ?>');
            classData.push(<?= $turma->media ?? 0 ?>);
        <?php endif; ?>
    <?php endforeach; ?>
    
    // Gráfico de Média por Turma
    if (classLabels.length > 0) {
        const ctx1 = document.getElementById('classChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: classLabels,
                datasets: [{
                    label: 'Média por Turma',
                    data: classData,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 20,
                        title: {
                            display: true,
                            text: 'Nota Média'
                        }
                    }
                }
            }
        });
    }
    
    // Gráfico de Distribuição de Notas
    <?php if (isset($aprovados) || isset($recurso) || isset($reprovados)): ?>
    const ctx2 = document.getElementById('gradesDistributionChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Aprovados (>=10)', 'Recurso (7-9.9)', 'Reprovados (<7)'],
            datasets: [{
                data: [
                    <?= $aprovados ?? 0 ?>,
                    <?= $recurso ?? 0 ?>,
                    <?= $reprovados ?? 0 ?>
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(220, 53, 69, 0.7)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    <?php endif; ?>
    
    // Auto-submit quando mudar o filtro (opcional)
    document.getElementById('academicYearFilter')?.addEventListener('change', function() {
        this.closest('form').submit();
    });
});
</script>
<?php endif; ?>

<style>
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.1);
    font-weight: 600;
}
.progress {
    background-color: #e9ecef;
}
.progress-bar {
    transition: width 0.6s ease;
}
.table td {
    vertical-align: middle;
}
</style>
<?= $this->endSection() ?>