<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Notas da Turma</h1>
            <p class="text-muted mb-0">Visualize todas as notas dos alunos por disciplina</p>
        </div>
        <div>
            <a href="<?= site_url('admin/grades/report?class=' . ($class->id ?? '')) ?>" class="btn btn-info me-2">
                <i class="fas fa-chart-bar me-1"></i> Relatório
            </a>
            <a href="<?= site_url('admin/grades/export/class/' . ($class->id ?? '')) ?>" class="btn btn-success me-2">
                <i class="fas fa-file-excel me-1"></i> Exportar
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
            <li class="breadcrumb-item active">Notas da Turma</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<?php if (isset($class) && $class): 
    ?>
    
    <!-- Informações da Turma -->
    <div class="alert alert-info d-flex align-items-center mb-4">
        <div class="me-3">
            <i class="fas fa-school fa-3x"></i>
        </div>
        <div>
            <h4 class="mb-1"><?= $class->class_name ?> (<?= $class->class_code ?>)</h4>
            <p class="mb-0">
                <strong>Nível:</strong> <?= $class->class_name ?> | 
                <strong>Turno:</strong> <?= $class->class_shift ?> | 
                <strong>Sala:</strong> <?= $class->class_room ?: 'Não definida' ?> |
                <strong>Professor:</strong> <?= $class->teacher_first_name ?? 'Não atribuído' ?> <?= $class->teacher_last_name ?? '' ?>
            </p>
        </div>
    </div>
    
    <!-- Filtros Rápidos -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filtros</h5>
        </div>
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Disciplina</label>
                    <select class="form-select" name="discipline" id="disciplineFilter">
                        <option value="">Todas as disciplinas</option>
                        <?php if (!empty($disciplines)): ?>
                            <?php foreach ($disciplines as $disc): ?>
                                <option value="<?= $disc->id ?>" <?= ($selectedDiscipline ?? '') == $disc->id ? 'selected' : '' ?>>
                                    <?= $disc->discipline_name ?> (<?= $disc->discipline_code ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Semestre</label>
                    <select class="form-select" name="semester">
                        <option value="">Todos os semestres</option>
                        <?php if (!empty($semesters)): ?>
                            <?php foreach ($semesters as $sem): ?>
                                <option value="<?= $sem->id ?>" <?= ($selectedSemester ?? '') == $sem->id ? 'selected' : '' ?>>
                                    <?= $sem->semester_name ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Situação</label>
                    <select class="form-select" name="status">
                        <option value="">Todos</option>
                        <option value="aprovado" <?= ($selectedStatus ?? '') == 'aprovado' ? 'selected' : '' ?>>Aprovados</option>
                        <option value="reprovado" <?= ($selectedStatus ?? '') == 'reprovado' ? 'selected' : '' ?>>Reprovados</option>
                        <option value="recurso" <?= ($selectedStatus ?? '') == 'recurso' ? 'selected' : '' ?>>Recurso</option>
                    </select>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Cards de Resumo por Disciplina -->
    <?php if (empty($selectedDiscipline)): ?>
        <div class="row mb-4">
            <?php if (!empty($disciplines)): ?>
                <?php foreach ($disciplines as $disc): ?>
                    <?php
                    // Calcular estatísticas da disciplina
                    $totalAlunos = count($students ?? []);
                    $notasDisciplina = [];
                    foreach ($students ?? [] as $student) {
                        if (isset($student->grades[$disc->id])) {
                            $notas = array_filter([
                                $student->grades[$disc->id]['ac1'] ?? null,
                                $student->grades[$disc->id]['ac2'] ?? null,
                                $student->grades[$disc->id]['ac3'] ?? null
                            ]);
                            if (!empty($notas)) {
                                $media = array_sum($notas) / count($notas);
                                $notasDisciplina[] = $media;
                            }
                        }
                    }
                    
                    $mediaGeral = !empty($notasDisciplina) ? array_sum($notasDisciplina) / count($notasDisciplina) : 0;
                    $aprovados = count(array_filter($notasDisciplina, function($n) { return $n >= 10; }));
                    $reprovados = count(array_filter($notasDisciplina, function($n) { return $n < 10 && $n > 0; }));
                    ?>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><?= $disc->discipline_name ?></h6>
                                <small><?= $disc->discipline_code ?></small>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Média:</span>
                                    <span class="fw-bold"><?= number_format($mediaGeral, 1) ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Aprovados:</span>
                                    <span class="text-success fw-bold"><?= $aprovados ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Reprovados:</span>
                                    <span class="text-danger fw-bold"><?= $reprovados ?></span>
                                </div>
                            </div>
                            <div class="card-footer bg-white text-center">
                                <a href="?discipline=<?= $disc->id ?>" class="btn btn-sm btn-outline-primary">
                                    Ver Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <!-- Tabela de Notas Detalhada -->
    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2 text-primary"></i>
                    <?php if ($selectedDiscipline): ?>
                        Notas de <?= $disciplines[$selectedDiscipline]->discipline_name ?? 'Disciplina Selecionada' ?>
                    <?php else: ?>
                        Todas as Disciplinas
                    <?php endif; ?>
                </h5>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">#</th>
                            <th>Aluno</th>
                            <th>Nº Matrícula</th>
                            
                            <?php if ($selectedDiscipline): ?>
                                <!-- Visualização por disciplina selecionada -->
                                <th colspan="3" class="text-center bg-primary">Avaliações</th>
                                <th width="100">Média</th>
                                <th width="100">Situação</th>
                            <?php else: ?>
                                <!-- Visualização de todas as disciplinas -->
                                <?php if (!empty($disciplines)): ?>
                                    <?php foreach ($disciplines as $disc): ?>
                                        <th colspan="3" class="text-center bg-primary">
                                            <?= $disc->discipline_name ?>
                                            <small class="d-block text-white-50"><?= $disc->discipline_code ?></small>
                                        </th>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <th width="100">Média Geral</th>
                            <?php endif; ?>
                        </tr>
                        
                        <?php if ($selectedDiscipline): ?>
                            <tr class="table-secondary">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="text-center">AC1</th>
                                <th class="text-center">AC2</th>
                                <th class="text-center">AC3</th>
                                <th></th>
                                <th></th>
                            </tr>
                        <?php else: ?>
                            <tr class="table-secondary">
                                <th></th>
                                <th></th>
                                <th></th>
                                <?php if (!empty($disciplines)): ?>
                                    <?php foreach ($disciplines as $disc): ?>
                                        <th class="text-center">AC1</th>
                                        <th class="text-center">AC2</th>
                                        <th class="text-center">AC3</th>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <th></th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>
                        <?php if (!empty($students)): ?>
                            <?php $i = 1; ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td class="text-center"><?= $i++ ?></td>
                                    <td>
                                        <strong><?= $student->first_name ?> <?= $student->last_name ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= $student->student_number ?></span>
                                    </td>
                                    
                                    <?php if ($selectedDiscipline): ?>
                                        <!-- Visualização por disciplina selecionada -->
                                        <?php
                                        $ac1 = $student->grades[$selectedDiscipline]['ac1'] ?? null;
                                        $ac2 = $student->grades[$selectedDiscipline]['ac2'] ?? null;
                                        $ac3 = $student->grades[$selectedDiscipline]['ac3'] ?? null;
                                        
                                        $notas = array_filter([$ac1, $ac2, $ac3]);
                                        $media = !empty($notas) ? array_sum($notas) / count($notas) : 0;
                                        
                                        $situacao = $media >= 10 ? 'Aprovado' : ($media >= 7 ? 'Recurso' : 'Reprovado');
                                        $situacaoClass = $media >= 10 ? 'success' : ($media >= 7 ? 'warning' : 'danger');
                                        ?>
                                        
                                        <td class="text-center <?= $ac1 ? 'bg-light-success' : '' ?>">
                                            <?php if ($ac1): ?>
                                                <span class="fw-bold <?= $ac1 >= 10 ? 'text-success' : 'text-danger' ?>">
                                                    <?= number_format($ac1, 1) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center <?= $ac2 ? 'bg-light-success' : '' ?>">
                                            <?php if ($ac2): ?>
                                                <span class="fw-bold <?= $ac2 >= 10 ? 'text-success' : 'text-danger' ?>">
                                                    <?= number_format($ac2, 1) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center <?= $ac3 ? 'bg-light-success' : '' ?>">
                                            <?php if ($ac3): ?>
                                                <span class="fw-bold <?= $ac3 >= 10 ? 'text-success' : 'text-danger' ?>">
                                                    <?= number_format($ac3, 1) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td class="text-center">
                                            <?php if ($media > 0): ?>
                                                <span class="badge bg-<?= $media >= 10 ? 'success' : 'warning' ?> p-2">
                                                    <?= number_format($media, 1) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td class="text-center">
                                            <?php if ($media > 0): ?>
                                                <span class="badge bg-<?= $situacaoClass ?> p-2">
                                                    <?= $situacao ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Sem notas</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                    <?php else: ?>
                                        <!-- Visualização de todas as disciplinas -->
                                        <?php
                                        $mediaGeral = 0;
                                        $totalDisciplinas = 0;
                                        ?>
                                        
                                        <?php if (!empty($disciplines)): ?>
                                            <?php foreach ($disciplines as $disc): ?>
                                                <?php
                                                $ac1 = $student->grades[$disc->id]['ac1'] ?? null;
                                                $ac2 = $student->grades[$disc->id]['ac2'] ?? null;
                                                $ac3 = $student->grades[$disc->id]['ac3'] ?? null;
                                                
                                                $notasDisc = array_filter([$ac1, $ac2, $ac3]);
                                                if (!empty($notasDisc)) {
                                                    $mediaDisc = array_sum($notasDisc) / count($notasDisc);
                                                    $mediaGeral += $mediaDisc;
                                                    $totalDisciplinas++;
                                                }
                                                ?>
                                                
                                                <td class="text-center <?= $ac1 ? 'bg-light-success' : '' ?>">
                                                    <?php if ($ac1): ?>
                                                        <span class="fw-bold <?= $ac1 >= 10 ? 'text-success' : 'text-danger' ?>">
                                                            <?= number_format($ac1, 1) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center <?= $ac2 ? 'bg-light-success' : '' ?>">
                                                    <?php if ($ac2): ?>
                                                        <span class="fw-bold <?= $ac2 >= 10 ? 'text-success' : 'text-danger' ?>">
                                                            <?= number_format($ac2, 1) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center <?= $ac3 ? 'bg-light-success' : '' ?>">
                                                    <?php if ($ac3): ?>
                                                        <span class="fw-bold <?= $ac3 >= 10 ? 'text-success' : 'text-danger' ?>">
                                                            <?= number_format($ac3, 1) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        
                                        <td class="text-center">
                                            <?php if ($totalDisciplinas > 0): ?>
                                                <?php $mediaFinal = $mediaGeral / $totalDisciplinas; ?>
                                                <span class="badge bg-<?= $mediaFinal >= 10 ? 'success' : 'warning' ?> p-2">
                                                    <?= number_format($mediaFinal, 1) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= $selectedDiscipline ? 8 : (4 + (count($disciplines ?? []) * 3)) ?>" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Nenhum aluno encontrado</h5>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    
                    <!-- Rodapé com médias -->
                    <?php if (!empty($students) && !$selectedDiscipline): ?>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Médias da Turma:</th>
                                <?php if (!empty($disciplines)): ?>
                                    <?php foreach ($disciplines as $disc): ?>
                                        <?php
                                        $somas = ['ac1' => 0, 'ac2' => 0, 'ac3' => 0];
                                        $counts = ['ac1' => 0, 'ac2' => 0, 'ac3' => 0];
                                        
                                        foreach ($students as $student) {
                                            foreach (['ac1', 'ac2', 'ac3'] as $ac) {
                                                if (isset($student->grades[$disc->id][$ac])) {
                                                    $somas[$ac] += $student->grades[$disc->id][$ac];
                                                    $counts[$ac]++;
                                                }
                                            }
                                        }
                                        ?>
                                        
                                        <th class="text-center">
                                            <?= $counts['ac1'] > 0 ? number_format($somas['ac1'] / $counts['ac1'], 1) : '-' ?>
                                        </th>
                                        <th class="text-center">
                                            <?= $counts['ac2'] > 0 ? number_format($somas['ac2'] / $counts['ac2'], 1) : '-' ?>
                                        </th>
                                        <th class="text-center">
                                            <?= $counts['ac3'] > 0 ? number_format($somas['ac3'] / $counts['ac3'], 1) : '-' ?>
                                        </th>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <th></th>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Gráfico de Desempenho (opcional) -->
    <?php if ($selectedDiscipline && !empty($students)): ?>
    <div class="card mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Distribuição de Notas</h5>
        </div>
        <div class="card-body">
            <canvas id="gradesChart" style="height: 300px;"></canvas>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('gradesChart').getContext('2d');
        
        // Coletar dados das notas
        const notas = [
            <?php foreach ($students as $student): ?>
                <?php
                $ac1 = $student->grades[$selectedDiscipline]['ac1'] ?? null;
                $ac2 = $student->grades[$selectedDiscipline]['ac2'] ?? null;
                $ac3 = $student->grades[$selectedDiscipline]['ac3'] ?? null;
                $notas = array_filter([$ac1, $ac2, $ac3]);
                $media = !empty($notas) ? array_sum($notas) / count($notas) : 0;
                ?>
                <?= $media ?>,
            <?php endforeach; ?>
        ].filter(n => n > 0);
        
        // Criar histograma
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['0-4', '5-9', '10-14', '15-20'],
                datasets: [{
                    label: 'Quantidade de Alunos',
                    data: [
                        notas.filter(n => n < 5).length,
                        notas.filter(n => n >= 5 && n < 10).length,
                        notas.filter(n => n >= 10 && n < 15).length,
                        notas.filter(n => n >= 15).length
                    ],
                    backgroundColor: [
                        'rgba(220, 53, 69, 0.5)',
                        'rgba(255, 193, 7, 0.5)',
                        'rgba(40, 167, 69, 0.5)',
                        'rgba(0, 123, 255, 0.5)'
                    ],
                    borderColor: [
                        '#dc3545',
                        '#ffc107',
                        '#28a745',
                        '#007bff'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        stepSize: 1,
                        title: {
                            display: true,
                            text: 'Número de Alunos'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Faixa de Notas'
                        }
                    }
                }
            }
        });
    });
    </script>
    <?php endif; ?>
    
<?php else: ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-school fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Turma não encontrada</h4>
            <a href="<?= site_url('admin/grades') ?>" class="btn btn-primary mt-3">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Auto-submit quando mudar o filtro de disciplina
$('#disciplineFilter').change(function() {
    $(this).closest('form').submit();
});

// Tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
</script>

<style>
.bg-light-success {
    background-color: rgba(40, 167, 69, 0.1);
}
.bg-light-warning {
    background-color: rgba(255, 193, 7, 0.1);
}
.bg-light-danger {
    background-color: rgba(220, 53, 69, 0.1);
}
.table th {
    vertical-align: middle;
    white-space: nowrap;
}
.card-header {
    font-weight: 600;
}
</style>
<?= $this->endSection() ?>