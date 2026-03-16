<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
/* Estilos específicos para a timeline */
.timeline {
    position: relative;
    padding: 1.5rem 0;
}

.timeline::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 2rem;
    width: 2px;
    background: var(--border);
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
    padding-left: 4rem;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-item.current .timeline-panel {
    border-left: 4px solid var(--success);
}

.timeline-badge {
    position: absolute;
    left: 1.55rem;
    top: 0;
    width: 1.8rem;
    height: 1.8rem;
    border-radius: 50%;
    background: var(--surface-card);
    border: 2px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
    transform: translateX(-50%);
}

.timeline-badge i {
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.timeline-panel {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.5rem;
    position: relative;
    box-shadow: var(--shadow-sm);
    transition: all 0.2s;
}

.timeline-panel:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--accent);
}

.timeline-panel::before {
    content: '';
    position: absolute;
    left: -0.6rem;
    top: 1.2rem;
    width: 0;
    height: 0;
    border-top: 0.6rem solid transparent;
    border-bottom: 0.6rem solid transparent;
    border-right: 0.6rem solid var(--border);
}

.timeline-panel:hover::before {
    border-right-color: var(--accent);
}

.timeline-heading {
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.timeline-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.timeline-date {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    color: var(--text-muted);
    font-size: 0.85rem;
}

.timeline-date i {
    color: var(--accent);
    font-size: 0.75rem;
}

.timeline-body {
    color: var(--text-secondary);
}

.timeline-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.timeline-stat-item {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.timeline-stat-label {
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--text-muted);
}

.timeline-stat-value {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.transfer-info {
    background: var(--surface);
    border-left: 3px solid var(--warning);
    padding: 0.75rem 1rem;
    border-radius: var(--radius-sm);
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin: 1rem 0;
}

.transfer-info i {
    color: var(--warning);
    margin-right: 0.5rem;
}

.observations-box {
    background: var(--surface);
    border: 1px dashed var(--border);
    border-radius: var(--radius-sm);
    padding: 0.75rem 1rem;
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin: 1rem 0;
}

.observations-box i {
    color: var(--accent);
    margin-right: 0.5rem;
}

.student-info-card {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: var(--radius);
    padding: 1.5rem;
    color: #fff;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.student-info-card::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
}

.student-info-card::after {
    content: '';
    position: absolute;
    bottom: -30px;
    right: 50px;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: rgba(59,127,232,0.15);
}

.student-info-content {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.student-info-name {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 0.3rem;
}

.student-info-details {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    color: rgba(255,255,255,0.8);
    font-size: 0.9rem;
}

.student-info-details i {
    margin-right: 0.3rem;
    opacity: 0.7;
}

.student-info-badge {
    background: rgba(255,255,255,0.15);
    padding: 0.5rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.9rem;
    border: 1px solid rgba(255,255,255,0.2);
    backdrop-filter: blur(5px);
}
</style>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-history me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= route_to('admin.dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= route_to('students.index') ?>">Alunos</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/students/view/' . $student['id']) ?>"><?= $student['first_name'] ?> <?= $student['last_name'] ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Histórico de Matrículas</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/students/view/' . $student['id']) ?>" class="hdr-btn info">
                <i class="fas fa-user-graduate me-1"></i> Ver Aluno
            </a>
            <a href="<?= route_to('students.enrollments') ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações do Aluno -->
<div class="student-info-card mb-4">
    <div class="student-info-content">
        <div>
            <div class="student-info-name">
                <i class="fas fa-user-graduate me-2"></i><?= $student['first_name'] ?> <?= $student['last_name'] ?>
            </div>
            <div class="student-info-details">
                <span><i class="fas fa-id-card"></i> <?= $student['student_number'] ?></span>
                <span><i class="fas fa-envelope"></i> <?= $student['email'] ?></span>
                <?php if (!empty($student['phone'])): ?>
                <span><i class="fas fa-phone"></i> <?= $student['phone'] ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="student-info-badge">
            <?php if ($student['is_active']): ?>
                <i class="fas fa-check-circle me-1" style="color: var(--success);"></i> Ativo
            <?php else: ?>
                <i class="fas fa-times-circle me-1" style="color: var(--danger);"></i> Inativo
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Timeline de Matrículas -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-history"></i>
            <span>Histórico de Matrículas</span>
        </div>
        <span class="badge bg-primary"><?= count($history) ?> registros</span>
    </div>
    <div class="ci-card-body">
        <?php if (!empty($history)): ?>
            <div class="timeline">
                <?php foreach ($history as $index => $enrollment): ?>
                    <div class="timeline-item <?= $index == 0 ? 'current' : '' ?>">
                        <div class="timeline-badge">
                            <?php
                            $badgeIcon = [
                                'Ativo' => 'check-circle text-success',
                                'Concluído' => 'flag-checkered text-info',
                                'Transferido' => 'exchange-alt text-primary',
                                'Anulado' => 'times-circle text-danger',
                                'Pendente' => 'clock text-warning'
                            ][$enrollment['status']] ?? 'circle';
                            ?>
                            <i class="fas fa-<?= $badgeIcon ?>"></i>
                        </div>
                        
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h5 class="timeline-title">
                                    <i class="fas fa-calendar-alt me-1" style="color: var(--accent);"></i>
                                    <?= $enrollment['year_name'] ?? 'N/A' ?> - <?= $enrollment['class_name'] ?? 'Turma não atribuída' ?>
                                    <?php if ($enrollment['status'] == 'Ativo'): ?>
                                        <span class="status-badge ativo ms-2">
                                            <span class="status-dot"></span> Atual
                                        </span>
                                    <?php endif; ?>
                                </h5>
                                <div class="timeline-date">
                                    <i class="fas fa-calendar"></i>
                                    <?= date('d/m/Y', strtotime($enrollment['enrollment_date'])) ?>
                                </div>
                            </div>
                            
                            <div class="timeline-body">
                                <div class="timeline-stats">
                                    <div class="timeline-stat-item">
                                        <span class="timeline-stat-label">Nº Matrícula</span>
                                        <span class="timeline-stat-value">
                                            <span class="student-number"><?= $enrollment['enrollment_number'] ?></span>
                                        </span>
                                    </div>
                                    
                                    <div class="timeline-stat-item">
                                        <span class="timeline-stat-label">Tipo</span>
                                        <span class="timeline-stat-value">
                                            <?php
                                            $typeClass = match($enrollment['enrollment_type']) {
                                                'Nova' => 'type-primary',
                                                'Renovação' => 'type-success',
                                                'Transferência' => 'type-warning',
                                                default => 'type-secondary'
                                            };
                                            ?>
                                            <span class="type-badge <?= $typeClass ?>">
                                                <?= $enrollment['enrollment_type'] ?>
                                            </span>
                                        </span>
                                    </div>
                                    
                                    <div class="timeline-stat-item">
                                        <span class="timeline-stat-label">Status</span>
                                        <span class="timeline-stat-value">
                                            <?php
                                            $statusClass = [
                                                'Ativo' => 'ativo',
                                                'Pendente' => 'processado',
                                                'Concluído' => 'concluido',
                                                'Transferido' => 'info',
                                                'Anulado' => 'inativo'
                                            ][$enrollment['status']] ?? 'inativo';
                                            ?>
                                            <span class="status-badge <?= $statusClass ?>">
                                                <?= $enrollment['status'] ?>
                                            </span>
                                        </span>
                                    </div>
                                    
                                    <?php if (!empty($enrollment['course_name'])): ?>
                                    <div class="timeline-stat-item">
                                        <span class="timeline-stat-label">Curso</span>
                                        <span class="timeline-stat-value">
                                            <span class="badge bg-primary"><?= $enrollment['course_name'] ?></span>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty($enrollment['previous_class_id'])): ?>
                                    <?php
                                    $classModel = new \App\Models\ClassModel();
                                    $prevClass = $classModel->find($enrollment['previous_class_id']);
                                    ?>
                                    <div class="transfer-info">
                                        <i class="fas fa-arrow-left"></i>
                                        Transferido de: <strong><?= $prevClass ? $prevClass['class_name'] : 'Classe anterior' ?></strong>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($enrollment['observations'])): ?>
                                    <div class="observations-box">
                                        <i class="fas fa-sticky-note"></i>
                                        <?= nl2br($enrollment['observations']) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mt-3">
                                    <a href="<?= site_url('admin/students/enrollments/view/' . $enrollment['id']) ?>" 
                                       class="btn-filter apply">
                                        <i class="fas fa-eye me-1"></i> Ver Detalhes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-history"></i>
                <h5>Nenhum histórico encontrado</h5>
                <p>Este aluno ainda não possui matrículas registradas.</p>
                <a href="<?= site_url('admin/students/enrollments/form-add?student=' . $student['id']) ?>" 
                   class="btn-filter apply">
                    <i class="fas fa-plus-circle me-1"></i> Nova Matrícula
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>