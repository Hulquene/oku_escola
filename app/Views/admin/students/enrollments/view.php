<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
/* Estilos específicos para a página de detalhes da matrícula */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.info-item {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.75rem 1rem;
}

.info-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--text-muted);
    margin-bottom: 0.2rem;
}

.info-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-primary);
}

.info-value i {
    color: var(--accent);
    width: 20px;
}

.enrollment-number {
    font-family: var(--font-mono);
    font-size: 1.1rem;
    font-weight: 700;
    background: rgba(59,127,232,0.1);
    color: var(--accent);
    padding: 0.3rem 1rem;
    border-radius: 50px;
    display: inline-block;
}

.student-name-large {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--text-primary);
}

.section-divider {
    height: 1px;
    background: var(--border);
    margin: 1.5rem 0;
}

.fee-row {
    transition: background 0.2s;
}

.fee-row:hover {
    background: var(--surface);
}

.fee-total {
    font-family: var(--font-mono);
    font-weight: 700;
    color: var(--accent);
}

.fee-total.paid {
    color: var(--success);
}

.fee-total.pending {
    color: var(--warning);
}

.fee-total.overdue {
    color: var(--danger);
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}

.summary-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1rem;
    text-align: center;
}

.summary-value {
    font-size: 1.8rem;
    font-weight: 700;
    font-family: var(--font-mono);
    color: var(--accent);
    line-height: 1.2;
}

.summary-label {
    font-size: 0.75rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.07em;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-top: 1rem;
}
</style>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-file-signature me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= route_to('admin.dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= route_to('students.index') ?>">Alunos</a></li>
                    <li class="breadcrumb-item"><a href="<?= route_to('students.enrollments') ?>">Matrículas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalhes da Matrícula</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <?php if (isset($enrollment['student_id'])): ?>
            <a href="<?= site_url('admin/students/enrollments/history/' . $enrollment['student_id']) ?>" class="hdr-btn info">
                <i class="fas fa-history me-1"></i> Histórico
            </a>
            <a href="<?= site_url('admin/students/view/' . $enrollment['student_id']) ?>" class="hdr-btn success">
                <i class="fas fa-user-graduate me-1"></i> Ver Aluno
            </a>
            <?php endif; ?>
            <a href="<?= site_url('admin/students/enrollments/form-edit/' . $enrollment['id']) ?>" class="hdr-btn warning">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
            <a href="<?= route_to('students.enrollments') ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Resumo da Matrícula -->
<div class="row g-4">
    <!-- Coluna Esquerda -->
    <div class="col-lg-6">
        <!-- Informações da Matrícula -->
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-file-signature"></i>
                    <span>Informações da Matrícula</span>
                </div>
            </div>
            <div class="ci-card-body">
                <div class="enrollment-number mb-3"><?= $enrollment['enrollment_number'] ?></div>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Data da Matrícula</div>
                        <div class="info-value">
                            <i class="fas fa-calendar me-1"></i>
                            <?= date('d/m/Y', strtotime($enrollment['enrollment_date'])) ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Tipo</div>
                        <div class="info-value">
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
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <?php
                            $statusClass = [
                                'Ativo' => 'ativo',
                                'Pendente' => 'processado',
                                'Concluído' => 'concluido',
                                'Transferido' => 'info',
                                'Anulado' => 'inativo',
                                'Cancelado' => 'inativo'
                            ][$enrollment['status']] ?? 'inativo';
                            
                            $statusIcon = [
                                'Ativo' => 'check-circle',
                                'Pendente' => 'clock',
                                'Concluído' => 'check-double',
                                'Transferido' => 'exchange-alt',
                                'Anulado' => 'ban',
                                'Cancelado' => 'ban'
                            ][$enrollment['status']] ?? 'circle';
                            ?>
                            <span class="status-badge <?= $statusClass ?>">
                                <i class="fas fa-<?= $statusIcon ?> me-1"></i>
                                <?= $enrollment['status'] ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Ano Letivo</div>
                        <div class="info-value">
                            <i class="fas fa-calendar-alt me-1"></i>
                            <?= $enrollment['year_name'] ?? 'N/A' ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Nível/Classe</div>
                        <div class="info-value">
                            <?php if (isset($enrollment->level_name)): ?>
                                <span class="badge bg-info"><?= $enrollment->level_name ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Registrado por</div>
                        <div class="info-value">
                            <i class="fas fa-user me-1"></i>
                            <?= $enrollment->created_by_username ?? 'Sistema' ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Data de Registro</div>
                        <div class="info-value">
                            <i class="fas fa-clock me-1"></i>
                            <?= isset($enrollment['created_at']) ? date('d/m/Y H:i', strtotime($enrollment['created_at'])) : '-' ?>
                        </div>
                    </div>
                    
                    <?php if (isset($enrollment->updated_at) && $enrollment->updated_at != $enrollment['created_at']): ?>
                    <div class="info-item">
                        <div class="info-label">Última Atualização</div>
                        <div class="info-value">
                            <i class="fas fa-sync me-1"></i>
                            <?= date('d/m/Y H:i', strtotime($enrollment->updated_at)) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($enrollment->observations)): ?>
                <div class="mt-3">
                    <div class="info-item">
                        <div class="info-label">Observações</div>
                        <div class="info-value">
                            <i class="fas fa-sticky-note me-1"></i>
                            <?= nl2br($enrollment->observations) ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Informações da Turma -->
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-school"></i>
                    <span>Informações da Turma</span>
                </div>
            </div>
            <div class="ci-card-body">
                <?php if (isset($enrollment->class_id) && $enrollment->class_id): ?>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Turma</div>
                            <div class="info-value">
                                <i class="fas fa-users me-1"></i>
                                <strong><?= $enrollment->class_name ?? 'N/A' ?></strong>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Código</div>
                            <div class="info-value">
                                <span class="code-badge"><?= $enrollment->class_code ?? '-' ?></span>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Turno</div>
                            <div class="info-value">
                                <?php
                                $shiftClass = match($enrollment->class_shift) {
                                    'Manhã' => 'type-primary',
                                    'Tarde' => 'type-success',
                                    'Noite' => 'type-warning',
                                    'Integral' => 'type-info',
                                    default => 'type-secondary'
                                };
                                ?>
                                <span class="type-badge <?= $shiftClass ?>"><?= $enrollment->class_shift ?? '-' ?></span>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Sala</div>
                            <div class="info-value">
                                <i class="fas fa-door-open me-1"></i>
                                <?= $enrollment['class_room'] ?: '-' ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Professor</div>
                            <div class="info-value">
                                <?php if (isset($enrollment->teacher_first_name)): ?>
                                    <div class="d-flex align-items-center">
                                        <div class="teacher-initials me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                            <?= strtoupper(substr($enrollment->teacher_first_name, 0, 1)) ?>
                                        </div>
                                        <?= $enrollment->teacher_first_name ?> <?= $enrollment->teacher_last_name ?? '' ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">Não atribuído</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="<?= site_url('admin/classes/classes/view/' . $enrollment->class_id) ?>" 
                           class="btn-filter apply">
                            <i class="fas fa-eye me-1"></i> Ver Detalhes da Turma
                        </a>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-school"></i>
                        <h5>Nenhuma turma atribuída</h5>
                        <p>Esta matrícula ainda não possui uma turma.</p>
                        <a href="<?= site_url('admin/students/enrollments/form-edit/' . $enrollment['id']) ?>" 
                           class="btn-filter apply">
                            <i class="fas fa-edit me-1"></i> Atribuir Turma
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Coluna Direita -->
    <div class="col-lg-6">
        <!-- Informações do Aluno -->
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-user-graduate"></i>
                    <span>Informações do Aluno</span>
                </div>
                <a href="<?= site_url('admin/students/view/' . $enrollment['student_id']) ?>" class="hdr-btn info">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
            <div class="ci-card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nome</div>
                        <div class="info-value">
                            <div class="d-flex align-items-center">
                                <div class="teacher-initials me-2" style="width: 35px; height: 35px;">
                                    <?= strtoupper(substr($enrollment['first_name'] ?? '', 0, 1)) ?>
                                </div>
                                <strong><?= $enrollment['first_name'] ?? '' ?> <?= $enrollment['last_name'] ?? '' ?></strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Nº Estudante</div>
                        <div class="info-value">
                            <span class="student-number"><?= $enrollment['student_number'] ?? '-' ?></span>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value">
                            <i class="fas fa-envelope me-1"></i>
                            <?php if (!empty($enrollment->email)): ?>
                                <a href="mailto:<?= $enrollment->email ?>" class="text-accent"><?= $enrollment->email ?></a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Telefone</div>
                        <div class="info-value">
                            <i class="fas fa-phone-alt me-1"></i>
                            <?= $enrollment['phone'] ?: '-' ?>
                        </div>
                    </div>
                    
                    <?php if (isset($enrollment->birth_date)): ?>
                    <div class="info-item">
                        <div class="info-label">Data Nascimento</div>
                        <div class="info-value">
                            <i class="fas fa-calendar me-1"></i>
                            <?= date('d/m/Y', strtotime($enrollment->birth_date)) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($enrollment->gender)): ?>
                    <div class="info-item">
                        <div class="info-label">Gênero</div>
                        <div class="info-value">
                            <i class="fas fa-venus-mars me-1"></i>
                            <?= $enrollment->gender ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Informações do Curso -->
        <?php if (isset($enrollment['course_id']) && $enrollment['course_id']): ?>
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Curso (Ensino Médio)</span>
                </div>
                <a href="<?= site_url('admin/courses/view/' . $enrollment['course_id']) ?>" class="hdr-btn info">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
            <div class="ci-card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Curso</div>
                        <div class="info-value">
                            <strong><?= $enrollment['course_name'] ?? 'N/A' ?></strong>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Código</div>
                        <div class="info-value">
                            <span class="code-badge"><?= $enrollment['course_code'] ?? 'N/A' ?></span>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Tipo</div>
                        <div class="info-value">
                            <?php
                            $typeMap = [
                                'Ciências' => 'type-primary',
                                'Humanidades' => 'type-success',
                                'Económico-Jurídico' => 'type-warning',
                                'Técnico' => 'type-info',
                                'Profissional' => 'type-secondary',
                                'Outro' => 'type-dark'
                            ];
                            $typeClass = $typeMap[$enrollment->course_type] ?? 'type-secondary';
                            ?>
                            <span class="type-badge <?= $typeClass ?>"><?= $enrollment->course_type ?? 'N/A' ?></span>
                        </div>
                    </div>
                    
                    <?php if (isset($enrollment->duration_years)): ?>
                    <div class="info-item">
                        <div class="info-label">Duração</div>
                        <div class="info-value">
                            <span class="num-chip"><?= $enrollment->duration_years ?> anos</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Classe Anterior -->
        <?php if (isset($enrollment->previous_grade_id) && $enrollment->previous_grade_id): ?>
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-history"></i>
                    <span>Classe Anterior</span>
                </div>
            </div>
            <div class="ci-card-body">
                <div class="info-item">
                    <div class="info-value">
                        <i class="fas fa-arrow-left me-1"></i>
                        <?= $enrollment->previous_level_name ?? 'N/A' ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Resumo Financeiro -->
<div class="ci-card mt-4">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-money-bill-wave"></i>
            <span>Taxas e Propinas</span>
        </div>
        <a href="<?= site_url('admin/fees/payments?enrollment=' . $enrollment['id']) ?>" class="hdr-btn success">
            <i class="fas fa-external-link-alt me-1"></i> Ver Todas
        </a>
    </div>
    <div class="ci-card-body">
        <?php 
        $totalPendente = 0;
        $totalPago = 0;
        $totalVencido = 0;
        
        if (!empty($fees)) {
            foreach ($fees as $fee) {
                if ($fee->status == 'Pago') {
                    $totalPago += $fee->total_amount;
                } elseif ($fee->status == 'Vencido') {
                    $totalVencido += $fee->total_amount;
                    $totalPendente += $fee->total_amount;
                } else {
                    $totalPendente += $fee->total_amount;
                }
            }
        }
        ?>
        
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-value text-success"><?= number_format($totalPago, 2, ',', '.') ?> Kz</div>
                <div class="summary-label">Total Pago</div>
            </div>
            <div class="summary-card">
                <div class="summary-value text-warning"><?= number_format($totalPendente, 2, ',', '.') ?> Kz</div>
                <div class="summary-label">Total Pendente</div>
            </div>
            <div class="summary-card">
                <div class="summary-value text-danger"><?= number_format($totalVencido, 2, ',', '.') ?> Kz</div>
                <div class="summary-label">Total Vencido</div>
            </div>
        </div>
        
        <?php if (!empty($fees)): ?>
            <div class="table-responsive mt-4">
                <table class="ci-table">
                    <thead>
                        <tr>
                            <th>Referência</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Vencimento</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fees as $fee): ?>
                            <tr class="fee-row">
                                <td><span class="code-badge"><?= $fee->reference_number ?></span></td>
                                <td><?= $fee->type_name ?></td>
                                <td>
                                    <span class="fee-total <?= $fee->status == 'Pago' ? 'paid' : ($fee->status == 'Vencido' ? 'overdue' : 'pending') ?>">
                                        <?= number_format($fee->total_amount, 2, ',', '.') ?> Kz
                                    </span>
                                </td>
                                <td>
                                    <span class="period-text">
                                        <?= date('d/m/Y', strtotime($fee->due_date)) ?>
                                    </span>
                                    <?php if ($fee->status != 'Pago' && strtotime($fee->due_date) < time()): ?>
                                        <span class="status-badge inativo ms-2">
                                            <span class="status-dot"></span> Vencido
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $feeStatusClass = [
                                        'Pago' => 'ativo',
                                        'Pendente' => 'processado',
                                        'Vencido' => 'inativo',
                                        'Parcial' => 'info',
                                        'Cancelado' => 'inativo'
                                    ][$fee->status] ?? 'inativo';
                                    ?>
                                    <span class="status-badge <?= $feeStatusClass ?>">
                                        <?= $fee->status ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-group">
                                        <button type="button" class="row-btn success" 
                                                onclick="registerPayment(<?= $fee->id ?>, <?= $fee->total_amount ?>)"
                                                <?= $fee->status == 'Pago' ? 'disabled' : '' ?>
                                                title="Registrar Pagamento">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state mt-3">
                <i class="fas fa-receipt"></i>
                <h5>Nenhuma taxa associada</h5>
                <p>Esta matrícula não possui taxas ou propinas.</p>
                <a href="<?= site_url('admin/fees/structure?enrollment=' . $enrollment['id']) ?>" 
                   class="btn-filter apply">
                    <i class="fas fa-plus-circle me-1"></i> Adicionar Taxas
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Ações Adicionais -->
<div class="ci-card mt-4">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-cog"></i>
            <span>Ações Adicionais</span>
        </div>
    </div>
    <div class="ci-card-body">
        <div class="action-buttons">
            <?php if ($enrollment['status'] == 'Pendente'): ?>
                <a href="<?= site_url('admin/students/enrollments/approve/' . $enrollment['id']) ?>" 
                   class="btn-filter apply"
                   onclick="return confirm('Confirmar aprovação desta matrícula?')">
                    <i class="fas fa-check-circle me-1"></i> Aprovar Matrícula
                </a>
            <?php endif; ?>
            
            <?php if ($enrollment['status'] == 'Ativo'): ?>
                <a href="<?= site_url('admin/students/enrollments/complete/' . $enrollment['id']) ?>" 
                   class="btn-filter apply"
                   onclick="return confirm('Confirmar conclusão desta matrícula?')">
                    <i class="fas fa-graduation-cap me-1"></i> Concluir Matrícula
                </a>
                
                <a href="<?= site_url('admin/students/enrollments/transfer/' . $enrollment['id']) ?>" 
                   class="btn-filter warning">
                    <i class="fas fa-exchange-alt me-1"></i> Transferir
                </a>
            <?php endif; ?>
            
            <?php if ($enrollment['status'] != 'Ativo' && $enrollment['status'] != 'Concluído'): ?>
                <button type="button" class="btn-filter clear" 
                        onclick="confirmDelete(<?= $enrollment['id'] ?>)">
                    <i class="fas fa-trash me-1"></i> Eliminar Matrícula
                </button>
            <?php endif; ?>
            
            <a href="<?= site_url('admin/students/enrollments/print/' . $enrollment['id']) ?>" 
               class="btn-filter clear" target="_blank">
                <i class="fas fa-print me-1"></i> Imprimir
            </a>
        </div>
    </div>
</div>

<!-- Modal de Pagamento -->
<div class="modal fade ci-modal" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="paymentForm" method="post" action="<?= site_url('admin/fees/payments/process') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="student_fee_id" id="payment_fee_id">
                
                <div class="modal-header success-header">
                    <h5 class="modal-title">
                        <i class="fas fa-money-bill-wave me-2"></i>Registrar Pagamento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-ci" for="payment_date">Data do Pagamento</label>
                        <input type="date" class="form-input-ci" id="payment_date" name="payment_date" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label-ci" for="amount_paid">Valor Pago (Kz)</label>
                        <input type="number" class="form-input-ci" id="amount_paid" name="amount_paid" 
                               step="0.01" min="0" required>
                        <div class="form-hint" id="amount_hint"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label-ci" for="payment_method">Método de Pagamento</label>
                        <select class="form-select-ci" id="payment_method" name="payment_method" required>
                            <option value="">Selecione...</option>
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Transferência">Transferência Bancária</option>
                            <option value="Multicaixa">Multicaixa</option>
                            <option value="Depósito">Depósito</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label-ci" for="payment_reference">Referência</label>
                        <input type="text" class="form-input-ci" id="payment_reference" name="payment_reference">
                        <div class="form-hint">Nº de referência da transação (opcional)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label-ci" for="observations">Observações</label>
                        <textarea class="form-input-ci" id="observations" name="observations" rows="2"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-filter clear" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn-filter apply">
                        <i class="fas fa-save me-1"></i>Registrar Pagamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Eliminação -->
<div class="modal fade ci-modal" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header danger-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar esta matrícula?</p>
                <div class="warning-alert">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita. Todas as taxas associadas também serão removidas.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-filter clear" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn-filter apply" style="background:var(--danger);">
                    <i class="fas fa-trash me-1"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function registerPayment(feeId, totalAmount) {
    document.getElementById('payment_fee_id').value = feeId;
    document.getElementById('amount_paid').value = totalAmount;
    document.getElementById('amount_paid').max = totalAmount;
    document.getElementById('amount_hint').textContent = 'Valor total: ' + totalAmount.toFixed(2) + ' Kz';
    
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}

function confirmDelete(id) {
    document.getElementById('confirmDeleteBtn').href = '<?= site_url('admin/students/enrollments/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: 'Pagamento registrado com sucesso',
                timer: 2000
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: data.message || 'Erro ao registrar pagamento'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Erro ao processar requisição: ' + error
        });
    });
});

// Auto-submit quando filtros mudarem (se houver)
let filterTimeout;
$('#filterForm select').change(function() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => $('#filterForm').submit(), 500);
});

// Inicializar tooltips
$(document).ready(function() {
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
<?= $this->endSection() ?>