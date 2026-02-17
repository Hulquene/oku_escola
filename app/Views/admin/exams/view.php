<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <?php if (!$exam->is_published): ?>
                <a href="<?= site_url('admin/exams/publish/' . $exam->id) ?>" 
                   class="btn btn-success"
                   onclick="return confirm('Publicar resultados? Os alunos poderão ver as notas.')">
                    <i class="fas fa-globe"></i> Publicar Resultados
                </a>
            <?php endif; ?>
            <a href="<?= site_url('admin/exams/form-edit/' . $exam->id) ?>" class="btn btn-info">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="<?= site_url('admin/exams') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes do Exame</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Exam Info -->
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Informações do Exame
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th>Nome do Exame:</th>
                                <td><?= $exam->exam_name ?></td>
                            </tr>
                            <tr>
                                <th>Turma:</th>
                                <td><?= $exam->class_name ?></td>
                            </tr>
                            <tr>
                                <th>Disciplina:</th>
                                <td><?= $exam->discipline_name ?></td>
                            </tr>
                            <tr>
                                <th>Tipo:</th>
                                <td><span class="badge bg-info"><?= $exam->board_name ?></span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th>Data:</th>
                                <td><?= date('d/m/Y', strtotime($exam->exam_date)) ?></td>
                            </tr>
                            <tr>
                                <th>Hora:</th>
                                <td><?= $exam->exam_time ? date('H:i', strtotime($exam->exam_time)) : '-' ?></td>
                            </tr>
                            <tr>
                                <th>Sala:</th>
                                <td><?= $exam->exam_room ?: '-' ?></td>
                            </tr>
                            <tr>
                                <th>Valor Máximo:</th>
                                <td><?= $exam->max_score ?> valores</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php if ($exam->description): ?>
                    <div class="mt-3">
                        <h6>Descrição:</h6>
                        <p><?= nl2br($exam->description) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Results Entry -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-star"></i> Lançamento de Notas
                <?php if ($exam->is_published): ?>
                    <span class="badge bg-success ms-2">Resultados Publicados</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!empty($students)): ?>
                    <form action="<?= site_url('admin/exams/save-results') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="exam_id" value="<?= $exam->id ?>">
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nº Matrícula</th>
                                        <th>Aluno</th>
                                        <th>Nota (0-<?= $exam->max_score ?>)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                        <?php 
                                        $result = $results[$student->enrollment_id] ?? null;
                                        $score = $result ? $result->score : '';
                                        ?>
                                        <tr>
                                            <td><?= $student->student_number ?></td>
                                            <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                                            <td style="width: 150px;">
                                                <input type="number" 
                                                       class="form-control score-input" 
                                                       name="score[<?= $student->enrollment_id ?>]" 
                                                       value="<?= $score ?>"
                                                       step="0.1"
                                                       min="0"
                                                       max="<?= $exam->max_score ?>"
                                                       <?= $exam->is_published ? 'readonly' : '' ?>>
                                            </td>
                                            <td>
                                                <?php if ($score !== ''): ?>
                                                    <?php if ($score >= $exam->approval_score): ?>
                                                        <span class="badge bg-success">Aprovado</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Reprovado</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Pendente</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (!$exam->is_published): ?>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salvar Notas
                                </button>
                                <button type="button" class="btn btn-success" onclick="fillAllScores()">
                                    <i class="fas fa-magic"></i> Preencher Todos
                                </button>
                                <button type="button" class="btn btn-warning" onclick="clearAllScores()">
                                    <i class="fas fa-eraser"></i> Limpar Todos
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                <?php else: ?>
                    <p class="text-muted">Nenhum aluno matriculado nesta turma.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-bar"></i> Estatísticas
            </div>
            <div class="card-body">
                <?php if ($statistics): ?>
                    <div class="text-center mb-3">
                        <h2><?= number_format($statistics->average, 1) ?></h2>
                        <p class="text-muted">Média da Turma</p>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border rounded p-2 mb-2">
                                <h5><?= $statistics->total ?></h5>
                                <small>Total Alunos</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 mb-2">
                                <h5 class="text-success"><?= $statistics->approved ?? 0 ?></h5>
                                <small>Aprovados</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <h5 class="text-danger"><?= $statistics->failed ?? 0 ?></h5>
                                <small>Reprovados</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <h5><?= $statistics->maximum ?></h5>
                                <small>Nota Máx.</small>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mt-3">
                        <a href="<?= site_url('admin/exams/results?exam=' . $exam->id) ?>" class="btn btn-outline-primary w-100">
                            <i class="fas fa-list"></i> Ver Resultados Detalhados
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Nenhuma nota lançada ainda.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Export Options -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-download"></i> Exportar
            </div>
            <div class="card-body">
                <a href="<?= site_url('admin/exams/results/export/' . $exam->id) ?>" class="btn btn-outline-success w-100 mb-2">
                    <i class="fas fa-file-csv"></i> Exportar para CSV
                </a>
                <a href="<?= site_url('printpdf/exam/' . $exam->id) ?>" class="btn btn-outline-danger w-100">
                    <i class="fas fa-file-pdf"></i> Gerar PDF
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function fillAllScores() {
    const inputs = document.querySelectorAll('.score-input');
    inputs.forEach(input => {
        if (!input.value) {
            input.value = '<?= $exam->max_score / 2 ?>';
        }
    });
}

function clearAllScores() {
    if (confirm('Limpar todas as notas?')) {
        document.querySelectorAll('.score-input').forEach(input => {
            input.value = '';
        });
    }
}
</script>
<?= $this->endSection() ?>