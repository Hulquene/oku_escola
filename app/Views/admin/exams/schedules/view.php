<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-calendar-check me-2"></i>Detalhes do Exame</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/schedules') ?>">Agendamentos</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $schedule['discipline_name'] ?></li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <?php if ($schedule['status'] == 'Agendado'): ?>
                <a href="<?= site_url('admin/exams/schedules/attendance/' . $schedule['id']) ?>" 
                   class="hdr-btn success">
                    <i class="fas fa-user-check me-1"></i> Presenças
                </a>
                <a href="<?= site_url('admin/exams/schedules/results/' . $schedule['id']) ?>" 
                   class="hdr-btn warning">
                    <i class="fas fa-graduation-cap me-1"></i> Notas
                </a>
            <?php endif; ?>
             
            <!-- Botão para mudar status -->
            <?php if ($schedule['status'] != 'Realizado' && $schedule['status'] != 'Cancelado'): ?>
                <button type="button" class="hdr-btn info" data-bs-toggle="modal" data-bs-target="#statusModal">
                    <i class="fas fa-sync-alt me-1"></i> Mudar Status
                </button>
            <?php endif; ?>
            
            <a href="<?= site_url('admin/exams/schedules/edit/' . $schedule['id']) ?>" 
               class="hdr-btn primary">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
            <a href="<?= site_url('admin/exams/schedules') ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <div class="mt-2">
        <span class="badge-ci info">
            <i class="fas fa-calendar-check me-1"></i>
            <?= $schedule['discipline_name'] ?> • <?= $schedule['class_name'] ?>
        </span>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações do Exame -->
<div class="stat-grid mb-4">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-calendar"></i>
        </div>
        <div>
            <div class="stat-label">Data/Hora</div>
            <div class="stat-value"><?= date('d/m/Y', strtotime($schedule['exam_date'])) ?></div>
            <div class="stat-sub"><?= $schedule['exam_time'] ? substr($schedule['exam_time'], 0, 5) : 'Horário não definido' ?></div>
        </div>
    </div>
    
    <div class="stat-card">
        <?php
        $statusColors = [
            'Agendado' => 'orange',
            'Realizado' => 'green',
            'Cancelado' => 'navy',
            'Adiado' => 'blue'
        ];
        $statusColor = $statusColors[$schedule['status']] ?? 'secondary';
        $statusIcon = [
            'Agendado' => 'fa-clock',
            'Realizado' => 'fa-check-circle',
            'Cancelado' => 'fa-times-circle',
            'Adiado' => 'fa-hourglass-half'
        ][$schedule['status']] ?? 'fa-circle';
        ?>
        <div class="stat-icon <?= $statusColor ?>">
            <i class="fas <?= $statusIcon ?>"></i>
        </div>
        <div>
            <div class="stat-label">Status</div>
            <div class="stat-value">
                <span class="badge-ci <?= $statusColor ?> p-2">
                    <?= $schedule['status'] ?>
                </span>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <div class="stat-label">Presenças</div>
            <div class="stat-value"><?= $attendanceStats->present ?? 0 ?>/<?= $attendanceStats->total ?? 0 ?></div>
            <div class="stat-sub"><?= $attendanceStats->percentage ?? 0 ?>%</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div>
            <div class="stat-label">Notas Registadas</div>
            <div class="stat-value"><?= count($results) ?>/<?= $attendanceStats->present ?? 0 ?></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <!-- Detalhes do Exame -->
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-info-circle"></i>
                    <span>Informações do Exame</span>
                </div>
            </div>
            <div class="ci-card-body">
                <table class="ci-table table-borderless">
                    <tr>
                        <th width="150">Período:</th>
                        <td><?= $schedule['period_name'] ?></td>
                    </tr>
                    <tr>
                        <th>Ano Letivo:</th>
                        <td><?= $schedule['year_name'] ?></td>
                    </tr>
                    <tr>
                        <th>Semestre:</th>
                        <td><?= $schedule['semester_name'] ?></td>
                    </tr>
                    <tr>
                        <th>Turma:</th>
                        <td><?= $schedule['class_name'] ?> (<?= $schedule['class_code'] ?>)</td>
                    </tr>
                    <tr>
                        <th>Disciplina:</th>
                        <td><?= $schedule['discipline_name'] ?> (<?= $schedule['discipline_code'] ?>)</td>
                    </tr>
                    <tr>
                        <th>Tipo:</th>
                        <td>
                            <span class="badge-ci info"><?= $schedule['board_name'] ?></span>
                            <span class="badge-ci secondary ms-1">Peso: <?= $schedule['weight'] ?>x</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Sala:</th>
                        <td><?= $schedule['exam_room'] ?: 'Não definida' ?></td>
                    </tr>
                    <tr>
                        <th>Duração:</th>
                        <td><?= $schedule['duration_minutes'] ?> minutos</td>
                    </tr>
                    <?php if ($schedule['observations']): ?>
                        <tr>
                            <th>Observações:</th>
                            <td><?= $schedule['observations'] ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- Lista de Resultados com Presenças e Notas na mesma tabela -->
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Resultados e Presenças</span>
                </div>
                <?php if (!empty($combinedData)): ?>
                    <?php
                    $scores = array_filter(array_column($combinedData, 'score'), function($v) { return $v !== null; });
                    if (!empty($scores)):
                        $avgScore = array_sum($scores) / count($scores);
                        $maxScore = max($scores);
                        $minScore = min($scores);
                    ?>
                    <div class="text-end">
                        <span class="badge-ci secondary me-1">Média: <?= number_format($avgScore, 1) ?></span>
                        <span class="badge-ci success me-1">Max: <?= number_format($maxScore, 1) ?></span>
                        <span class="badge-ci danger">Min: <?= number_format($minScore, 1) ?></span>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="ci-card-body p0">
                <?php if (!empty($combinedData)): ?>
                    <div class="table-responsive">
                        <table class="ci-table">
                            <thead>
                                <tr>
                                    <th class="ps-3">Aluno</th>
                                    <th class="center">Processo</th>
                                    <th class="center">Presença</th>
                                    <th class="center">Nota</th>
                                    <th class="center pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($combinedData as $item): ?>
                                    <?php
                                    // Determinar classe da nota
                                    $scoreClass = '';
                                    $scoreText = '—';
                                    
                                    if ($item->score !== null) {
                                        if ($item->score >= 10) {
                                            $scoreClass = 'success';
                                            $scoreText = 'Aprovado';
                                        } elseif ($item->score >= 7) {
                                            $scoreClass = 'warning';
                                            $scoreText = 'Recurso';
                                        } else {
                                            $scoreClass = 'danger';
                                            $scoreText = 'Reprovado';
                                        }
                                    }
                                    
                                    // Classe da presença
                                    $attendanceClass = $item->attended ? 'success' : 'danger';
                                    $attendanceIcon = $item->attended ? 'fa-check' : 'fa-times';
                                    $attendanceText = $item->attended ? 'Presente' : 'Ausente';
                                    ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <div class="teacher-initials me-2" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                                    <?= strtoupper(substr($item->first_name, 0, 1) . substr($item->last_name, 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <span class="fw-medium"><?= $item->first_name ?> <?= $item->last_name ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <span class="code-badge"><?= $item->student_number ?></span>
                                        </td>
                                        <td class="center">
                                            <span class="badge-ci <?= $attendanceClass ?>">
                                                <i class="fas <?= $attendanceIcon ?> me-1"></i>
                                                <?= $attendanceText ?>
                                            </span>
                                            <?php if ($item->check_in_time): ?>
                                                <br>
                                                <small class="text-muted"><?= date('H:i', strtotime($item->check_in_time)) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="center">
                                            <?php if ($item->score !== null): ?>
                                                <span class="num-chip" style="font-size: 1rem;"><?= number_format($item->score, 1) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="center pe-3">
                                            <?php if ($item->score !== null): ?>
                                                <span class="badge-ci <?= $scoreClass ?>">
                                                    <?= $scoreText ?>
                                                </span>
                                            <?php elseif ($item->attended): ?>
                                                <span class="badge-ci secondary">Aguardando</span>
                                            <?php else: ?>
                                                <span class="badge-ci secondary">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <h5>Nenhum aluno encontrado</h5>
                        <p class="text-muted mb-3">Nenhum aluno registado para este exame.</p>
                        <?php if ($schedule['status'] == 'Agendado'): ?>
                            <div class="mt-2">
                                <a href="<?= site_url('admin/exams/schedules/attendance/' . $schedule['id']) ?>" 
                                   class="btn-ci success me-2">
                                    <i class="fas fa-user-check me-1"></i> Registar Presenças
                                </a>
                                <a href="<?= site_url('admin/exams/schedules/results/' . $schedule['id']) ?>" 
                                   class="btn-ci warning">
                                    <i class="fas fa-graduation-cap me-1"></i> Registar Notas
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mudar status com estilo do sistema -->
<div class="modal fade ci-modal" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header info-header">
                <h5 class="modal-title">
                    <i class="fas fa-sync-alt me-2"></i>
                    Alterar Status do Exame
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('admin/exams/schedules/update-status/' . $schedule['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="filter-label">Status Atual</label>
                        <div>
                            <span class="badge-ci <?= $statusColor ?> p-2" style="font-size: 1rem;">
                                <?= $schedule['status'] ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="filter-label">Novo Status</label>
                        <select name="status" class="filter-select" required>
                            <option value="">Selecione...</option>
                            <option value="Agendado">Agendado</option>
                            <option value="Realizado">Realizado</option>
                            <option value="Cancelado">Cancelado</option>
                            <option value="Adiado">Adiado</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="filter-label">Observações</label>
                        <textarea name="observations" class="form-input-ci" rows="2" 
                                  placeholder="Motivo da alteração (opcional)"></textarea>
                    </div>
                    
                    <?php if ($schedule['status'] != 'Realizado'): ?>
                    <div class="alert-ci info small">
                        <i class="fas fa-info-circle me-2"></i>
                        Ao marcar como <strong>Realizado</strong>, o exame será finalizado e não poderá mais ser editado.
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($schedule['status'] == 'Cancelado'): ?>
                    <div class="alert-ci danger small">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Ao cancelar o exame, todas as presenças e notas serão mantidas, mas o exame ficará inativo.
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-filter clear" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn-filter info">
                        <i class="fas fa-save me-1"></i> Salvar Alteração
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<style>
/* Estilos adicionais específicos para esta página */
.table-borderless th,
.table-borderless td {
    padding: 0.75rem;
    vertical-align: top;
    border: none;
}

.table-borderless tr {
    border-bottom: 1px solid var(--border);
}

.table-borderless tr:last-child {
    border-bottom: none;
}

.ci-table tbody tr:hover {
    background: rgba(59,127,232,0.03);
}

.teacher-initials {
    background: var(--accent);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    text-transform: uppercase;
    flex-shrink: 0;
}

/* Cabeçalhos de modal */
.modal-header.info-header {
    background: var(--accent);
    color: #fff;
}

/* Ajustes para badges no header da página */
.badge-ci.info {
    background: rgba(59,127,232,0.15);
    color: var(--accent);
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
}

/* Responsividade */
@media (max-width: 768px) {
    .stat-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .hdr-actions {
        flex-wrap: wrap;
    }
    
    .hdr-btn {
        padding: 0.35rem 0.8rem;
        font-size: 0.75rem;
    }
    
    .ci-table td {
        min-width: 120px;
    }
    
    .ci-table td:first-child {
        min-width: auto;
    }
}
</style>
<?= $this->endSection() ?>