<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-graduation-cap me-2" style="color: var(--warning);"></i>Registar Notas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/schedules') ?>">Agendamentos</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/schedules/view/' . $schedule['id']) ?>">Detalhes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Notas</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/exams/schedules/view/' . $schedule['id']) ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <div class="mt-2">
        <span class="badge-ci info me-2">
            <i class="fas fa-book me-1"></i> <?= $schedule['discipline_name'] ?>
        </span>
        <span class="badge-ci primary me-2">
            <i class="fas fa-users me-1"></i> <?= $schedule['class_name'] ?>
        </span>
        <span class="badge-ci warning">
            <i class="fas fa-graduation-cap me-1"></i> Peso: <?= $schedule['weight'] ?>x
        </span>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row g-4">
    <div class="col-md-8">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Registar Notas (0-20 valores)</span>
                </div>
            </div>
            <div class="ci-card-body">
        <form method="post" action="<?= route_to('exams.schedules.results.post', $schedule['id']) ?>" id="resultsForm">
            <?= csrf_field() ?>
                    
                    <div class="table-responsive">
                        <table class="ci-table">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Nº Aluno</th>
                                    <th>Nome do Aluno</th>
                                    <th class="center">Nota</th>
                                    <th class="center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($students)): ?>
                                    <?php $i = 1; ?>
                                    <?php foreach ($students as $student): ?>
                                        <?php 
                                        $score = $existingScores[$student['enrollment_id']] ?? '';
                                        $scoreClass = '';
                                        $status = '';
                                        
                                        if ($score !== '') {
                                            if ($score >= 10) {
                                                $status = '<span class="badge-ci success">Aprovado</span>';
                                            } elseif ($score >= 7) {
                                                $status = '<span class="badge-ci warning">Recurso</span>';
                                            } elseif ($score > 0) {
                                                $status = '<span class="badge-ci danger">Reprovado</span>';
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td><?= str_pad($i++, 2, '0', STR_PAD_LEFT) ?></td>
                                            <td>
                                                <span class="code-badge"><?= $student['student_number'] ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="teacher-initials me-2" style="width: 35px; height: 35px; font-size: 0.9rem; background: var(--accent);">
                                                        <?= strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)) ?>
                                                    </div>
                                                    <span class="fw-semibold"><?= $student['first_name'] ?> <?= $student['last_name'] ?></span>
                                                </div>
                                            </td>
                                            <td class="center">
                                                <input type="number" 
                                                       class="form-input-ci score-input text-center" 
                                                       name="scores[<?= $student['enrollment_id'] ?>]" 
                                                       value="<?= $score ?>"
                                                       min="0" max="20" step="0.1"
                                                       placeholder="0-20"
                                                       data-enrollment="<?= $student['enrollment_id'] ?>"
                                                       style="width: 100px; margin: 0 auto;">
                                            </td>
                                            <td class="center status-cell" id="status_<?= $student['enrollment_id'] ?>">
                                                <?= $status ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-users"></i>
                                                <p class="text-muted">Nenhum aluno presente para registar notas</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge-ci info">
                                <i class="fas fa-info-circle me-1"></i>
                                Notas de 0 a 20 valores
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="<?= site_url('admin/exams/schedules/view/' . $schedule['id']) ?>" class="btn-filter clear">
                                Cancelar
                            </a>
                            <button type="submit" class="btn-ci warning">
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
        <div class="ci-card mb-3">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-info-circle"></i>
                    <span>Informações</span>
                </div>
            </div>
            <div class="ci-card-body">
                <table class="ci-table table-borderless">
                    <tr>
                        <td class="text-muted">Disciplina:</td>
                        <td class="fw-semibold"><?= $schedule['discipline_name'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tipo:</td>
                        <td class="fw-semibold">
                            <span class="badge-ci info"><?= $schedule['board_name'] ?></span>
                            <span class="badge-ci secondary ms-1">Peso: <?= $schedule['weight'] ?>x</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Turma:</td>
                        <td class="fw-semibold"><?= $schedule['class_name'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Data:</td>
                        <td class="fw-semibold"><?= date('d/m/Y', strtotime($schedule['exam_date'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total Alunos:</td>
                        <td class="fw-semibold"><?= count($students) ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Card de Estatísticas -->
        <div class="ci-card mb-3">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-chart-bar"></i>
                    <span>Estatísticas</span>
                </div>
            </div>
            <div class="ci-card-body" id="statsCard">
                <div class="text-center text-muted py-3">
                    <i class="fas fa-calculator fa-2x mb-2" style="color: var(--text-muted);"></i>
                    <p>As estatísticas serão atualizadas<br>ao inserir as notas</p>
                </div>
            </div>
        </div>
        
        <!-- Card de Ações Rápidas -->
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-bolt"></i>
                    <span>Ações Rápidas</span>
                </div>
            </div>
            <div class="ci-card-body">
                <div class="d-grid gap-2">
                    <button class="btn-ci outline" onclick="fillRandomScores()" style="justify-content: center;">
                        <i class="fas fa-dice me-2"></i>Preencher Aleatório
                    </button>
                    <button class="btn-ci outline" onclick="clearAllScores()" style="justify-content: center;">
                        <i class="fas fa-undo me-2"></i>Limpar Todas
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
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
                    statusCell.innerHTML = '<span class="badge-ci success">Aprovado</span>';
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                } else if (score >= 7) {
                    statusCell.innerHTML = '<span class="badge-ci warning">Recurso</span>';
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                } else {
                    statusCell.innerHTML = '<span class="badge-ci danger">Reprovado</span>';
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                }
            } else {
                statusCell.innerHTML = '<span class="badge-ci secondary">Inválida</span>';
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
            <div class="border-top pt-3" style="border-color: var(--border) !important;">
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
                <i class="fas fa-calculator fa-2x mb-2" style="color: var(--text-muted);"></i>
                <p>As estatísticas serão atualizadas<br>ao inserir as notas</p>
            </div>
        `;
    }
}

// Fill random scores (for testing)
function fillRandomScores() {
    if (confirm('Preencher com notas aleatórias para teste?')) {
        document.querySelectorAll('.score-input').forEach(input => {
            let randomScore = (Math.random() * 20).toFixed(1);
            input.value = randomScore;
            input.dispatchEvent(new Event('input'));
        });
    }
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
        Swal.fire({
            icon: 'warning',
            title: 'Notas inválidas',
            text: 'Existem notas inválidas. As notas devem estar entre 0 e 20.',
            confirmButtonColor: 'var(--warning)'
        });
        return false;
    }
    
    e.preventDefault(); // Prevenir envio imediato para confirmação
    
    Swal.fire({
        title: 'Confirmar registo',
        text: 'Confirmar registo de notas? Esta ação irá recalcular as médias dos alunos.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: 'var(--warning)',
        cancelButtonColor: 'var(--border)',
        confirmButtonText: '<i class="fas fa-check me-2"></i>Sim, registar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit();
        }
    });
});

// Initialize stats on page load
document.addEventListener('DOMContentLoaded', function() {
    updateStatistics();
    
    // Inicializar estilos de validação
    document.querySelectorAll('.score-input').forEach(input => {
        input.dispatchEvent(new Event('input'));
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

.code-badge {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 600;
    background: rgba(59,127,232,0.1);
    color: var(--accent);
    padding: 0.25rem 0.5rem;
    border-radius: 5px;
    display: inline-block;
}

.score-input {
    width: 100px !important;
    margin: 0 auto !important;
    text-align: center !important;
    font-family: var(--font-mono);
    font-weight: 600;
}

.score-input.is-valid {
    border-color: var(--success) !important;
    background: rgba(22,168,125,0.05) !important;
}

.score-input.is-invalid {
    border-color: var(--danger) !important;
    background: rgba(232,70,70,0.05) !important;
}

/* Estilo para a tabela borderless */
.table-borderless tr {
    border-bottom: 1px solid var(--border);
}

.table-borderless tr:last-child {
    border-bottom: none;
}

/* Botões outline */
.btn-ci.outline {
    background: transparent;
    border: 1.5px solid var(--border);
    color: var(--text-secondary);
    width: 100%;
}

.btn-ci.outline:hover {
    background: var(--surface);
    color: var(--primary);
    border-color: var(--primary);
}

/* Cores de texto */
.text-success { color: var(--success) !important; }
.text-danger { color: var(--danger) !important; }
.text-warning { color: var(--warning) !important; }

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
    .ci-table td {
        min-width: 120px;
    }
    
    .ci-table td:first-child {
        min-width: auto;
    }
    
    .score-input {
        width: 80px !important;
    }
}
</style>
<?= $this->endSection() ?>