<?= $this->extend('teachers/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Mini Pautas</h1>
      
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('teachers/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mini Pautas</li>
        </ol>
    </nav>
</div>

<!-- Filtros (igual ao anterior) -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros</h5>
    </div>
    <div class="card-body">
        <form action="<?= site_url('teachers/mini-grade-sheet') ?>" method="get" id="filterForm">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Ano Letivo</label>
                    <select name="ano_letivo" class="form-select" id="anoLetivo">
                        <option value="">Todos</option>
                        <?php foreach ($anosLetivos as $ano): ?>
                            <option value="<?= $ano->id ?>" <?= ($filters['ano_letivo'] ?? '') == $ano->id ? 'selected' : '' ?>>
                                <?= $ano->year_name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Curso</label>
                    <select name="curso" class="form-select" id="curso">
                        <option value="">Todos</option>
                        <?php foreach ($cursos as $curso): ?>
                            <option value="<?= $curso->id ?>" <?= ($filters['curso'] ?? '') == $curso->id ? 'selected' : '' ?>>
                                <?= $curso->course_name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label">Nível</label>
                    <select name="level" class="form-select" id="level">
                        <option value="">Todos</option>
                        <?php foreach ($levels as $level): ?>
                            <option value="<?= $level->id ?>" <?= ($filters['level'] ?? '') == $level->id ? 'selected' : '' ?>>
                                <?= $level->level_name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label">Turma</label>
                    <select name="turma" class="form-select" id="turma">
                        <option value="">Todas</option>
                        <?php foreach ($turmas as $turma): ?>
                            <option value="<?= $turma->id ?>" <?= ($filters['turma'] ?? '') == $turma->id ? 'selected' : '' ?>>
                                <?= $turma->class_name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label">Disciplina</label>
                    <select name="disciplina" class="form-select" id="disciplina">
                        <option value="">Todas</option>
                        <?php foreach ($disciplinas as $disc): ?>
                            <option value="<?= $disc->id ?>" <?= ($filters['disciplina'] ?? '') == $disc->id ? 'selected' : '' ?>>
                                <?= $disc->discipline_name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Filtrar
                </button>
                <a href="<?= site_url('teachers/mini-grade-sheet') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo me-2"></i>Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Mini Pautas por Turma/Disciplina -->
<?php if (!empty($miniPautas)): ?>
    <?php foreach ($miniPautas as $pauta): ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>
                        <?= $pauta->course_name ?? 'N/A' ?> | 
                        Turma: <?= $pauta->class_name ?> | 
                        Disciplina: <?= $pauta->discipline_name ?>
                    </h5>
                    <div>
                        <span class="badge bg-light text-dark me-2">
                            <i class="fas fa-users"></i> <?= count($pauta->alunos) ?> alunos
                        </span>
                        <a href="<?= site_url('teachers/exams/grade/' . $pauta->id) ?>" class="btn btn-sm btn-light me-2">
                            <i class="fas fa-star"></i> Lançar Notas
                        </a>
                        <a href="<?= site_url('teachers/mini-grade-sheet/print/' . $pauta->id) ?>" class="btn btn-sm btn-light" target="_blank">
                            <i class="fas fa-print"></i> Imprimir
                        </a>
                         <a href="<?= site_url('teachers/mini-grade-sheet/export/' . $pauta->id) ?>" class="btn btn-sm btn-success me-2">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                    </div>
                </div>
                <div class="small mt-2">
                    <span class="me-3"><strong>Ano Letivo:</strong> <?= $pauta->year_name ?></span>
                    <span class="me-3"><strong>Período:</strong> <?= $pauta->period_name ?></span>
                    <span class="me-3"><strong>Data Exame:</strong> <?= date('d/m/Y', strtotime($pauta->exam_date)) ?></span>
                    <span class="me-3"><strong>Status:</strong> 
                        <?php
                        $statusClass = [
                            'Agendado' => 'warning',
                            'Realizado' => 'success',
                            'Cancelado' => 'danger',
                            'Adiado' => 'info'
                        ][$pauta->status] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $statusClass ?>"><?= $pauta->status ?></span>
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2" class="align-middle text-center" width="50">Nº</th>
                                <th rowspan="2" class="align-middle">Nome do Aluno</th>
                                <th colspan="4" class="text-center">1º TRIMESTRE</th>
                                <th colspan="4" class="text-center">2º TRIMESTRE</th>
                                <th colspan="4" class="text-center">3º TRIMESTRE</th>
                                <th rowspan="2" class="align-middle text-center" width="60">MDF</th>
                                <th rowspan="2" class="align-middle text-center" width="100">Situação</th>
                            </tr>
                            <tr>
                                <th class="text-center" width="50" title="Média Avaliações Contínuas">MAC</th>
                                <th class="text-center" width="50" title="Nota Prova Professor">NPP</th>
                                <th class="text-center" width="50" title="Nota Prova Trimestral">NPT</th>
                                <th class="text-center" width="50" title="Média Trimestral">MT</th>
                                <th class="text-center" width="50" title="Média Avaliações Contínuas">MAC</th>
                                <th class="text-center" width="50" title="Nota Prova Professor">NPP</th>
                                <th class="text-center" width="50" title="Nota Prova Trimestral">NPT</th>
                                <th class="text-center" width="50" title="Média Trimestral">MT</th>
                                <th class="text-center" width="50" title="Média Avaliações Contínuas">MAC</th>
                                <th class="text-center" width="50" title="Nota Prova Professor">NPP</th>
                                <th class="text-center" width="50" title="Nota Prova Trimestral">NPT</th>
                                <th class="text-center" width="50" title="Média Trimestral">MT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pauta->alunos)): ?>
                                <?php $counter = 1; ?>
                                <?php foreach ($pauta->alunos as $aluno): ?>
                                    <?php
                                    $medias = $pauta->medias[$aluno->enrollment_id] ?? [];
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $counter++ ?></td>
                                        <td>
                                            <strong><?= $aluno->full_name ?? $aluno->first_name . ' ' . $aluno->last_name ?></strong>
                                            <br>
                                            <small class="text-muted">Nº: <?= $aluno->student_number ?? '—' ?></small>
                                        </td>
                                        
                                        <!-- 1º Trimestre -->
                                        <td class="text-center"><?= $medias['trimestres'][1]['AC'] ?? '—' ?></td>
                                        <td class="text-center"><?= $medias['trimestres'][1]['NPP'] ?? '—' ?></td>
                                        <td class="text-center"><?= $medias['trimestres'][1]['NPT'] ?? '—' ?></td>
                                        <td class="text-center <?= ($medias['trimestres'][1]['MT'] ?? '—') !== '—' ? 'bg-light' : '' ?>">
                                            <strong><?= $medias['trimestres'][1]['MT'] ?? '—' ?></strong>
                                        </td>
                                        
                                        <!-- 2º Trimestre -->
                                        <td class="text-center"><?= $medias['trimestres'][2]['AC'] ?? '—' ?></td>
                                        <td class="text-center"><?= $medias['trimestres'][2]['NPP'] ?? '—' ?></td>
                                        <td class="text-center"><?= $medias['trimestres'][2]['NPT'] ?? '—' ?></td>
                                        <td class="text-center <?= ($medias['trimestres'][2]['MT'] ?? '—') !== '—' ? 'bg-light' : '' ?>">
                                            <strong><?= $medias['trimestres'][2]['MT'] ?? '—' ?></strong>
                                        </td>
                                        
                                        <!-- 3º Trimestre -->
                                        <td class="text-center"><?= $medias['trimestres'][3]['AC'] ?? '—' ?></td>
                                        <td class="text-center"><?= $medias['trimestres'][3]['NPP'] ?? '—' ?></td>
                                        <td class="text-center"><?= $medias['trimestres'][3]['NPT'] ?? '—' ?></td>
                                        <td class="text-center <?= ($medias['trimestres'][3]['MT'] ?? '—') !== '—' ? 'bg-light' : '' ?>">
                                            <strong><?= $medias['trimestres'][3]['MT'] ?? '—' ?></strong>
                                        </td>
                                        
                                        <!-- MDF -->
                                        <td class="text-center">
                                            <?php if (($medias['MDF'] ?? '—') !== '—'): ?>
                                                <strong class="
                                                    <?= $medias['MDF'] >= 10 ? 'text-success' : ($medias['MDF'] >= 7 ? 'text-warning' : 'text-danger') ?>
                                                "><?= $medias['MDF'] ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Situação -->
                                        <td class="text-center">
                                            <span class="badge bg-<?= $medias['situacaoClass'] ?? 'secondary' ?>">
                                                <?= $medias['situacao'] ?? 'Pendente' ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="18" class="text-center py-4">
                                        <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">Nenhum aluno matriculado nesta turma.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="text-end"><strong>Totais da Turma:</strong></td>
                                <td colspan="16">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <small><strong>Aprovados:</strong> <?= $pauta->estatisticas['aprovados'] ?? 0 ?></small>
                                        </div>
                                        <div class="col-md-3">
                                            <small><strong>Recurso:</strong> <?= $pauta->estatisticas['recurso'] ?? 0 ?></small>
                                        </div>
                                        <div class="col-md-3">
                                            <small><strong>Reprovados:</strong> <?= $pauta->estatisticas['reprovados'] ?? 0 ?></small>
                                        </div>
                                        <div class="col-md-3">
                                            <small><strong>Pendentes:</strong> <?= $pauta->estatisticas['pendentes'] ?? 0 ?></small>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-md-6">
                                            <small><strong>Média da Turma:</strong> <?= $pauta->estatisticas['mediaTurma'] ?? '—' ?></small>
                                        </div>
                                        <div class="col-md-6">
                                            <small><strong>Taxa de Aprovação:</strong> <?= $pauta->estatisticas['taxaAprovacao'] ?? 0 ?>%</small>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light">
                <div class="row">
                    <div class="col-md-12 text-end">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            MAC = Média das Avaliações Contínuas | NPP = Nota Prova Professor | NPT = Nota Prova Trimestral | 
                            MT = Média Trimestral (MAC+NPP+NPT)/3 | MDF = Média Final (MT1+MT2+MT3)/3
                        </small>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
    <?php if ($pager): ?>
        <div class="mt-4">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
    
<?php else: ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
            <h5>Nenhuma mini pauta encontrada</h5>
            <p class="text-muted">Clique em "Nova Mini Pauta" para criar uma.</p>
        </div>
    </div>
<?php endif; ?>

<!-- Legenda -->
<div class="card mt-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <span class="badge bg-success">Aprovado</span> - MDF ≥ 10 valores
            </div>
            <div class="col-md-3">
                <span class="badge bg-warning">Recurso</span> - MDF entre 7 e 9 valores
            </div>
            <div class="col-md-3">
                <span class="badge bg-danger">Reprovado</span> - MDF < 7 valores
            </div>
            <div class="col-md-3">
                <span class="badge bg-secondary">Pendente</span> - Notas incompletas
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <p class="mb-0 text-muted small">
                    <strong>Legenda Completa:</strong> 
                    MAC = Média das Avaliações Contínuas | 
                    NPP = Nota da Prova do Professor | 
                    NPT = Nota da Prova Trimestral | 
                    MT = Média Trimestral (MAC + NPP + NPT) / 3 | 
                    MDF = Média Final da Disciplina (MT1 + MT2 + MT3) / 3
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Script para carregar disciplinas baseado na turma selecionada
document.getElementById('turma').addEventListener('change', function() {
    const turmaId = this.value;
    const disciplinaSelect = document.getElementById('disciplina');
    
    if (turmaId) {
        fetch('<?= site_url('teachers/mini-grade-sheet/getDisciplinas') ?>/' + turmaId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    disciplinaSelect.innerHTML = '<option value="">Todas</option>';
                    data.data.forEach(disciplina => {
                        disciplinaSelect.innerHTML += `<option value="${disciplina.id}">${disciplina.discipline_name}</option>`;
                    });
                }
            })
            .catch(error => console.error('Erro ao carregar disciplinas:', error));
    } else {
        disciplinaSelect.innerHTML = '<option value="">Todas</option>';
    }
});
</script>

<?= $this->endSection() ?>