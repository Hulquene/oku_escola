<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Registar Notas</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-graduation-cap me-1"></i>
                <?= $schedule->discipline_name ?> • <?= $schedule->class_name ?>
            </p>
        </div>
        <div>
            <a href="<?= site_url('admin/exams/schedules/view/' . $schedule->id) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/schedules') ?>">Agendamentos</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/schedules/view/' . $schedule->id) ?>">Detalhes</a></li>
            <li class="breadcrumb-item active">Notas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-graduation-cap me-2 text-warning"></i>
                    Registar Notas (0-20 valores)
                </h5>
            </div>
            <div class="card-body">
                <form method="post" id="resultsForm">
                    <?= csrf_field() ?>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Nº Aluno</th>
                                    <th>Nome do Aluno</th>
                                    <th width="150" class="text-center">Nota</th>
                                    <th width="100" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($students)): ?>
                                    <?php $i = 1; ?>
                                    <?php foreach ($students as $student): ?>
                                        <?php 
                                        $score = $existingScores[$student->enrollment_id] ?? '';
                                        $scoreClass = '';
                                        $status = '';
                                        
                                        if ($score !== '') {
                                            if ($score >= 10) {
                                                $status = '<span class="badge bg-success">Aprovado</span>';
                                            } elseif ($score >= 7) {
                                                $status = '<span class="badge bg-warning text-dark">Recurso</span>';
                                            } elseif ($score > 0) {
                                                $status = '<span class="badge bg-danger">Reprovado</span>';
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td>
                                                <span class="badge bg-info bg-opacity-10 text-info p-2">
                                                    <?= $student->student_number ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-2">
                                                        <?= strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) ?>
                                                    </div>
                                                    <span class="fw-semibold"><?= $student->first_name ?> <?= $student->last_name ?></span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <input type="number" 
                                                       class="form-control form-control-sm text-center score-input" 
                                                       name="scores[<?= $student->enrollment_id ?>]" 
                                                       value="<?= $score ?>"
                                                       min="0" max="20" step="0.1"
                                                       placeholder="0-20"
                                                       data-enrollment="<?= $student->enrollment_id ?>">
                                            </td>
                                            <td class="text-center status-cell" id="status_<?= $student->enrollment_id ?>">
                                                <?= $status ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Nenhum aluno presente para registar notas</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Notas de 0 a 20 valores
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="<?= site_url('admin/exams/schedules/view/' . $schedule->id) ?>" class="btn btn-light">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i> Registar Notas
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Card de Informações -->
        <div class="card mb-3">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-info-circle me-2 text-info"></i>
                    Informações
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Disciplina:</h6>
                    <p class="fw-semibold"><?= $schedule->discipline_name ?></p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Tipo:</h6>
                    <p class="fw-semibold">
                        <span class="badge bg-info"><?= $schedule->board_name ?></span>
                        <span class="badge bg-secondary ms-1">Peso: <?= $schedule->weight ?>x</span>
                    </p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Turma:</h6>
                    <p class="fw-semibold"><?= $schedule->class_name ?></p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Data:</h6>
                    <p class="fw-semibold"><?= date('d/m/Y', strtotime($schedule->exam_date)) ?></p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Total Alunos:</h6>
                    <p class="fw-semibold"><?= count($students) ?></p>
                </div>
            </div>
        </div>
        
        <!-- Card de Estatísticas -->
        <div class="card mb-3">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-chart-bar me-2 text-success"></i>
                    Estatísticas
                </h5>
            </div>
            <div class="card-body" id="statsCard">
                <div class="text-center text-muted py-3">
                    <i class="fas fa-calculator fa-2x mb-2"></i>
                    <p>As estatísticas serão atualizadas<br>ao inserir as notas</p>
                </div>
            </div>
        </div>
        
        <!-- Card de Ações Rápidas -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-bolt me-2 text-warning"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-success" onclick="fillRandomScores()">
                        <i class="fas fa-dice me-2"></i>Preencher Aleatório
                    </button>
                    <button class="btn btn-outline-danger" onclick="clearAllScores()">
                        <i class="fas fa-undo me-2"></i>Limpar Todas
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
}

.score-input {
    width: 100px;
    margin: 0 auto;
}

.score-input.is-valid {
    border-color: #198754;
    background-color: #d1e7dd;
}

.score-input.is-invalid {
    border-color: #dc3545;
    background-color: #f8d7da;
}
</style>

<script>
// Update status and stats on score change
document.querySelectorAll('.score-input').forEach(input => {
    input.addEventListener('input', function() {
        let score = parseFloat(this.value);
        let enrollmentId = this.dataset.enrollment;
        let statusCell = document.getElementById('status_' + enrollmentId);
        
        if (isNaN(score) || score === '') {
            statusCell.innerHTML = '';
            this.classList.remove('is-valid', 'is-invalid');
        } else {
            if (score >= 0 && score <= 20) {
                if (score >= 10) {
                    statusCell.innerHTML = '<span class="badge bg-success">Aprovado</span>';
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                } else if (score >= 7) {
                    statusCell.innerHTML = '<span class="badge bg-warning text-dark">Recurso</span>';
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                } else {
                    statusCell.innerHTML = '<span class="badge bg-danger">Reprovado</span>';
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                }
            } else {
                statusCell.innerHTML = '<span class="badge bg-secondary">Inválida</span>';
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            }
        }
        
        updateStatistics();
    });
});

// Update statistics
function updateStatistics() {
    let scores = [];
    let validScores = [];
    
    document.querySelectorAll('.score-input').forEach(input => {
        let score = parseFloat(input.value);
        if (!isNaN(score) && score !== '' && score >= 0 && score <= 20) {
            scores.push(score);
            validScores.push(score);
        }
    });
    
    if (validScores.length > 0) {
        let sum = validScores.reduce((a, b) => a + b, 0);
        let avg = sum / validScores.length;
        let max = Math.max(...validScores);
        let min = Math.min(...validScores);
        let approved = validScores.filter(s => s >= 10).length;
        let appeal = validScores.filter(s => s >= 7 && s < 10).length;
        let failed = validScores.filter(s => s < 7).length;
        
        document.getElementById('statsCard').innerHTML = `
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Média:</span>
                    <span class="fw-semibold">${avg.toFixed(2)}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Maior nota:</span>
                    <span class="fw-semibold text-success">${max.toFixed(1)}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Menor nota:</span>
                    <span class="fw-semibold text-danger">${min.toFixed(1)}</span>
                </div>
            </div>
            <div class="border-top pt-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Aprovados:</span>
                    <span class="fw-semibold text-success">${approved}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Recurso:</span>
                    <span class="fw-semibold text-warning">${appeal}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Reprovados:</span>
                    <span class="fw-semibold text-danger">${failed}</span>
                </div>
            </div>
        `;
    } else {
        document.getElementById('statsCard').innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="fas fa-calculator fa-2x mb-2"></i>
                <p>As estatísticas serão atualizadas<br>ao inserir as notas</p>
            </div>
        `;
    }
}

// Fill random scores (for testing)
function fillRandomScores() {
    document.querySelectorAll('.score-input').forEach(input => {
        let randomScore = (Math.random() * 20).toFixed(1);
        input.value = randomScore;
        input.dispatchEvent(new Event('input'));
    });
}

// Clear all scores
function clearAllScores() {
    if (confirm('Limpar todas as notas?')) {
        document.querySelectorAll('.score-input').forEach(input => {
            input.value = '';
            input.dispatchEvent(new Event('input'));
        });
    }
}

// Confirm before submit
document.getElementById('resultsForm').addEventListener('submit', function(e) {
    let hasInvalid = false;
    
    document.querySelectorAll('.score-input').forEach(input => {
        let score = parseFloat(input.value);
        if (!isNaN(score) && (score < 0 || score > 20)) {
            hasInvalid = true;
        }
    });
    
    if (hasInvalid) {
        e.preventDefault();
        alert('Existem notas inválidas. As notas devem estar entre 0 e 20.');
        return false;
    }
    
    if (!confirm('Confirmar registo de notas? Esta ação irá recalcular as médias dos alunos.')) {
        e.preventDefault();
    }
});

// Initialize stats on page load
document.addEventListener('DOMContentLoaded', function() {
    updateStatistics();
});
</script>

<?= $this->endSection() ?>