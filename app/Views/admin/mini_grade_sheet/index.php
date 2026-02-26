<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Mini Pautas - Administração</h1>
        <a href="<?= site_url('admin/mini-grade-sheet/trimestral') ?>" class="btn btn-info me-2">
            <i class="fas fa-calendar-alt"></i> Pautas Trimestrais
        </a>
        <a href="<?= site_url('admin/mini-grade-sheet/disciplina') ?>" class="btn btn-success">
            <i class="fas fa-book-open"></i> Pautas por Disciplina
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mini Pautas</li>
        </ol>
    </nav>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros</h5>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/mini-grade-sheet') ?>" method="get" id="filterForm">
            <div class="row">
                <div class="col-md-2 mb-3">
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
                
                <div class="col-md-2 mb-3">
                    <label class="form-label">Curso</label>
                    <select name="curso" class="form-select" id="curso">
                        <option value="">Todos</option>
                        <option value="0" <?= ($filters['curso'] ?? '') === '0' ? 'selected' : '' ?>>Ensino Geral</option>
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
                                <?= $turma->class_name ?> (<?= $turma->year_name ?>)
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
                
                <div class="col-md-2 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach ($statusOptions as $key => $value): ?>
                            <option value="<?= $key ?>" <?= ($filters['status'] ?? '') == $key ? 'selected' : '' ?>>
                                <?= $value ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Filtrar
                </button>
                <a href="<?= site_url('admin/mini-grade-sheet') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo me-2"></i>Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Mini Pautas -->
<div class="card">
    <div class="card-body">
        <?php if (!empty($miniPautas)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2" class="align-middle text-center">ID</th>
                            <th rowspan="2" class="align-middle">Ano</th>
                            <th rowspan="2" class="align-middle">Curso</th>
                            <th rowspan="2" class="align-middle">Turma</th>
                            <th rowspan="2" class="align-middle">Disciplina</th>
                            <th rowspan="2" class="align-middle">Professor</th>
                            <th rowspan="2" class="align-middle">Período</th>
                            <th rowspan="2" class="align-middle">Data</th>
                            <th colspan="2" class="text-center">Alunos</th>
                            <th rowspan="2" class="align-middle text-center">Status</th>
                            <th rowspan="2" class="align-middle text-center">Ações</th>
                        </tr>
                        <tr>
                            <th class="text-center">Total</th>
                            <th class="text-center">Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($miniPautas as $pauta): ?>
                            <tr>
                                <td class="text-center"><?= $pauta->id ?></td>
                                <td><?= $pauta->year_name ?></td>
                                <td><?= $pauta->course_name ?? 'Ensino Geral' ?></td>
                                <td><?= $pauta->class_name ?></td>
                                <td><?= $pauta->discipline_name ?></td>
                                <td><?= ($pauta->teacher_first_name ?? 'Não') . ' ' . ($pauta->teacher_last_name ?? 'atribuído') ?></td>
                                <td><?= $pauta->period_name ?></td>
                                <td><?= date('d/m/Y', strtotime($pauta->exam_date)) ?></td>
                                <td class="text-center"><?= $pauta->total_students ?? 0 ?></td>
                                <td class="text-center">
                                    <?php if (($pauta->results_count ?? 0) > 0): ?>
                                        <span class="badge bg-success"><?= $pauta->results_count ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $statusClass = [
                                        'Agendado' => 'primary',
                                        'Realizado' => 'success',
                                        'Cancelado' => 'danger',
                                        'Adiado' => 'warning'
                                    ][$pauta->status] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>"><?= $pauta->status ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('admin/mini-grade-sheet/view/' . $pauta->id) ?>" 
                                           class="btn btn-info" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('admin/mini-grade-sheet/print/' . $pauta->id) ?>" 
                                           class="btn btn-secondary" title="Imprimir" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="<?= site_url('admin/mini-grade-sheet/export/' . $pauta->id) ?>" 
                                           class="btn btn-success" title="Exportar Excel">
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
                <div class="mt-4">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                <h5>Nenhuma mini pauta encontrada</h5>
                <p class="text-muted">Utilize os filtros para encontrar pautas específicas.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Legenda -->
<div class="card mt-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <span class="badge bg-primary">Agendado</span> - Exame agendado
            </div>
            <div class="col-md-3">
                <span class="badge bg-success">Realizado</span> - Exame realizado
            </div>
            <div class="col-md-3">
                <span class="badge bg-warning">Adiado</span> - Exame adiado
            </div>
            <div class="col-md-3">
                <span class="badge bg-danger">Cancelado</span> - Exame cancelado
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>