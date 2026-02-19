<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <?php if (isset($enrollment->student_id)): ?>
            <a href="<?= site_url('admin/students/enrollments/history/' . $enrollment->student_id) ?>" class="btn btn-info">
                <i class="fas fa-history me-1"></i> Histórico do Aluno
            </a>
            <a href="<?= site_url('admin/students/view/' . $enrollment->student_id) ?>" class="btn btn-primary me-2">
                <i class="fas fa-user-graduate me-1"></i> Ver Aluno
            </a>
            <?php endif; ?>
            <a href="<?= site_url('admin/students/enrollments/form-edit/' . $enrollment->id) ?>" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i> Editar Matrícula
            </a>
            <a href="<?= site_url('admin/students/enrollments') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">
                <i class="fas fa-home me-1"></i>Dashboard
            </a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students') ?>">Alunos</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students/enrollments') ?>">Matrículas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes da Matrícula</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row">
    <div class="col-md-6">
        <!-- Informações da Matrícula -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-file-signature me-2"></i> Informações da Matrícula
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">Nº Matrícula</th>
                        <td><span class="badge bg-info fs-6 p-2"><?= $enrollment->enrollment_number ?></span></td>
                    </tr>
                    <tr>
                        <th>Data da Matrícula</th>
                        <td><?= date('d/m/Y', strtotime($enrollment->enrollment_date)) ?></td>
                    </tr>
                    <tr>
                        <th>Tipo</th>
                        <td><span class="badge bg-secondary"><?= $enrollment->enrollment_type ?></span></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php
                            $statusClass = [
                                'Ativo' => 'success',
                                'Pendente' => 'warning',
                                'Concluído' => 'info',
                                'Transferido' => 'primary',
                                'Anulado' => 'danger',
                                'Cancelado' => 'danger'
                            ][$enrollment->status] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $statusClass ?> p-2">
                                <i class="fas fa-<?= $enrollment->status == 'Ativo' ? 'check-circle' : 
                                    ($enrollment->status == 'Pendente' ? 'clock' : 
                                    ($enrollment->status == 'Concluído' ? 'check-double' : 
                                    ($enrollment->status == 'Transferido' ? 'exchange-alt' : 'ban'))) ?> me-1"></i>
                                <?= $enrollment->status ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Ano Letivo</th>
                        <td>
                            <?= $enrollment->year_name ?? 'N/A' ?>
                            <?php if (isset($enrollment->start_date)): ?>
                                <small class="text-muted">(<?= date('Y', strtotime($enrollment->start_date)) ?>)</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Nível/Classe</th>
                        <td>
                            <?php if (isset($enrollment->level_name)): ?>
                                <span class="badge bg-info"><?= $enrollment->level_name ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Registrado por</th>
                        <td><?= $enrollment->created_by_username ?? 'Sistema' ?></td>
                    </tr>
                    <tr>
                        <th>Data de Registro</th>
                        <td><?= isset($enrollment->created_at) ? date('d/m/Y H:i', strtotime($enrollment->created_at)) : '-' ?></td>
                    </tr>
                    <?php if (isset($enrollment->updated_at) && $enrollment->updated_at != $enrollment->created_at): ?>
                    <tr>
                        <th>Última Atualização</th>
                        <td><?= date('d/m/Y H:i', strtotime($enrollment->updated_at)) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        
        <!-- Observações -->
        <?php if (!empty($enrollment->observations)): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <i class="fas fa-sticky-note me-2"></i> Observações
            </div>
            <div class="card-body">
                <?= nl2br($enrollment->observations) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-6">
        <!-- Informações do Aluno -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <i class="fas fa-user-graduate me-2"></i> Informações do Aluno
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 150px;">Nome</th>
                        <td><strong><?= $enrollment->first_name ?? '' ?> <?= $enrollment->last_name ?? '' ?></strong></td>
                    </tr>
                    <tr>
                        <th>Nº Estudante</th>
                        <td><span class="badge bg-secondary"><?= $enrollment->student_number ?? '-' ?></span></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $enrollment->email ?? '-' ?></td>
                    </tr>
                    <tr>
                        <th>Telefone</th>
                        <td><?= $enrollment->phone ?: '-' ?></td>
                    </tr>
                    <?php if (isset($enrollment->birth_date)): ?>
                    <tr>
                        <th>Data Nascimento</th>
                        <td><?= date('d/m/Y', strtotime($enrollment->birth_date)) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (isset($enrollment->gender)): ?>
                    <tr>
                        <th>Gênero</th>
                        <td><?= $enrollment->gender ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
                
                <div class="mt-3">
                    <a href="<?= site_url('admin/students/view/' . $enrollment->student_id) ?>" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i> Ver Perfil Completo
                    </a>
                    <a href="<?= site_url('admin/students/enrollments/history/' . $enrollment->student_id) ?>" 
                       class="btn btn-sm btn-outline-info ms-2">
                        <i class="fas fa-history me-1"></i> Histórico do Aluno
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Informações da Turma -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <i class="fas fa-school me-2"></i> Informações da Turma
            </div>
            <div class="card-body">
                <?php if (isset($enrollment->class_id) && $enrollment->class_id): ?>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 150px;">Turma</th>
                            <td><strong><?= $enrollment->class_name ?? 'N/A' ?></strong></td>
                        </tr>
                        <tr>
                            <th>Código</th>
                            <td><span class="badge bg-info"><?= $enrollment->class_code ?? '-' ?></span></td>
                        </tr>
                        <tr>
                            <th>Turno</th>
                            <td><?= $enrollment->class_shift ?? '-' ?></td>
                        </tr>
                        <tr>
                            <th>Sala</th>
                            <td><?= $enrollment->class_room ?: '-' ?></td>
                        </tr>
                        <tr>
                            <th>Professor</th>
                            <td>
                                <?php if (isset($enrollment->teacher_first_name)): ?>
                                    <?= $enrollment->teacher_first_name ?> <?= $enrollment->teacher_last_name ?? '' ?>
                                <?php else: ?>
                                    <span class="text-muted">Não atribuído</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                    
                    <div class="mt-3">
                        <a href="<?= site_url('admin/classes/classes/view/' . $enrollment->class_id) ?>" 
                           class="btn btn-sm btn-outline-success">
                            <i class="fas fa-external-link-alt me-1"></i> Ver Detalhes da Turma
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="fas fa-school fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhuma turma atribuída a esta matrícula.</p>
                        <a href="<?= site_url('admin/students/enrollments/form-edit/' . $enrollment->id) ?>" 
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-edit me-1"></i> Atribuir Turma
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Informações do Curso -->
        <?php if (isset($enrollment->course_id) && $enrollment->course_id): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-graduation-cap me-2"></i> Curso (Ensino Médio)
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 150px;">Curso</th>
                        <td><strong><?= $enrollment->course_name ?? 'N/A' ?></strong></td>
                    </tr>
                    <tr>
                        <th>Código</th>
                        <td><span class="badge bg-info"><?= $enrollment->course_code ?? 'N/A' ?></span></td>
                    </tr>
                    <tr>
                        <th>Tipo</th>
                        <td><?= $enrollment->course_type ?? 'N/A' ?></td>
                    </tr>
                    <?php if (isset($enrollment->duration_years)): ?>
                    <tr>
                        <th>Duração</th>
                        <td><?= $enrollment->duration_years ?> anos</td>
                    </tr>
                    <?php endif; ?>
                </table>
                
                <div class="mt-3">
                    <a href="<?= site_url('admin/courses/view/' . $enrollment->course_id) ?>" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i> Ver Detalhes do Curso
                    </a>
                    <a href="<?= site_url('admin/courses/curriculum/' . $enrollment->course_id) ?>" 
                       class="btn btn-sm btn-outline-info ms-2">
                        <i class="fas fa-book-open me-1"></i> Ver Currículo
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Informações da Classe Anterior -->
        <?php if (isset($enrollment->previous_grade_id) && $enrollment->previous_grade_id): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-secondary text-white">
                <i class="fas fa-history me-2"></i> Classe Anterior
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 150px;">Classe</th>
                        <td><?= $enrollment->previous_level_name ?? 'N/A' ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Taxas da Matrícula -->
<div class="card mt-3 shadow-sm">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-money-bill-wave me-2"></i> Taxas e Propinas
        </div>
        <a href="<?= site_url('admin/fees/payments?enrollment=' . $enrollment->id) ?>" class="btn btn-sm btn-light">
            <i class="fas fa-external-link-alt me-1"></i> Ver Todos
        </a>
    </div>
    <div class="card-body">
        <?php if (!empty($fees)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Referência</th>
                            <th>Tipo</th>
                            <th>Valor (Kz)</th>
                            <th>Data Vencimento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalPendente = 0;
                        $totalPago = 0;
                        foreach ($fees as $fee): 
                            if ($fee->status == 'Pago') {
                                $totalPago += $fee->total_amount;
                            } else {
                                $totalPendente += $fee->total_amount;
                            }
                        ?>
                            <tr>
                                <td><small class="text-muted"><?= $fee->reference_number ?></small></td>
                                <td><?= $fee->type_name ?></td>
                                <td class="fw-bold"><?= number_format($fee->total_amount, 2, ',', '.') ?></td>
                                <td>
                                    <?= date('d/m/Y', strtotime($fee->due_date)) ?>
                                    <?php if ($fee->status != 'Pago' && strtotime($fee->due_date) < time()): ?>
                                        <span class="badge bg-danger ms-2">Vencido</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $feeStatusClass = [
                                        'Pago' => 'success',
                                        'Pendente' => 'warning',
                                        'Vencido' => 'danger',
                                        'Parcial' => 'info',
                                        'Cancelado' => 'secondary'
                                    ][$fee->status] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $feeStatusClass ?> p-2">
                                        <?= $fee->status ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-success" 
                                            onclick="registerPayment(<?= $fee->id ?>, <?= $fee->total_amount ?>)"
                                            <?= $fee->status == 'Pago' ? 'disabled' : '' ?>>
                                        <i class="fas fa-money-bill-wave me-1"></i> Pagar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="2" class="text-end">Totais:</th>
                            <th class="text-success">Pago: <?= number_format($totalPago, 2, ',', '.') ?> Kz</th>
                            <th colspan="2" class="text-warning">Pendente: <?= number_format($totalPendente, 2, ',', '.') ?> Kz</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhuma taxa associada a esta matrícula.</p>
                <a href="<?= site_url('admin/fees/structure?enrollment=' . $enrollment->id) ?>" 
                   class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Adicionar Taxas
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Ações Adicionais -->
<div class="card mt-3 shadow-sm">
    <div class="card-header bg-light">
        <i class="fas fa-cog me-2"></i> Ações Adicionais
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            <?php if ($enrollment->status == 'Pendente'): ?>
                <a href="<?= site_url('admin/students/enrollments/approve/' . $enrollment->id) ?>" 
                   class="btn btn-success"
                   onclick="return confirm('Confirmar aprovação desta matrícula?')">
                    <i class="fas fa-check-circle me-1"></i> Aprovar Matrícula
                </a>
            <?php endif; ?>
            
            <?php if ($enrollment->status == 'Ativo'): ?>
                <a href="<?= site_url('admin/students/enrollments/complete/' . $enrollment->id) ?>" 
                   class="btn btn-info"
                   onclick="return confirm('Confirmar conclusão desta matrícula?')">
                    <i class="fas fa-graduation-cap me-1"></i> Concluir Matrícula
                </a>
                
                <a href="<?= site_url('admin/students/enrollments/transfer/' . $enrollment->id) ?>" 
                   class="btn btn-warning">
                    <i class="fas fa-exchange-alt me-1"></i> Transferir
                </a>
            <?php endif; ?>
            
            <?php if ($enrollment->status != 'Ativo' && $enrollment->status != 'Concluído'): ?>
                <button type="button" class="btn btn-danger" 
                        onclick="confirmDelete(<?= $enrollment->id ?>)">
                    <i class="fas fa-trash me-1"></i> Eliminar Matrícula
                </button>
            <?php endif; ?>
            
            <a href="<?= site_url('admin/students/enrollments/print/' . $enrollment->id) ?>" 
               class="btn btn-secondary" target="_blank">
                <i class="fas fa-print me-1"></i> Imprimir
            </a>
        </div>
    </div>
</div>

<!-- Modal de Pagamento -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="paymentForm" method="post" action="<?= site_url('admin/fees/payments/process') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="student_fee_id" id="payment_fee_id">
                
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-money-bill-wave me-2"></i>Registrar Pagamento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_date" class="form-label fw-semibold">Data do Pagamento</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount_paid" class="form-label fw-semibold">Valor Pago (Kz)</label>
                        <input type="number" class="form-control" id="amount_paid" name="amount_paid" 
                               step="0.01" min="0" required>
                        <small class="text-muted" id="amount_hint"></small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label fw-semibold">Método de Pagamento</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Selecione...</option>
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Transferência">Transferência Bancária</option>
                            <option value="Multicaixa">Multicaixa</option>
                            <option value="Depósito">Depósito</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_reference" class="form-label fw-semibold">Referência</label>
                        <input type="text" class="form-control" id="payment_reference" name="payment_reference">
                        <small class="text-muted">Nº de referência da transação (opcional)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observations" class="form-label fw-semibold">Observações</label>
                        <textarea class="form-control" id="observations" name="observations" rows="2"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Registrar Pagamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Eliminação -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar esta matrícula?</p>
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita. Todas as taxas associadas também serão removidas.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
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
            location.reload();
        } else {
            alert('Erro ao registrar pagamento: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        alert('Erro ao processar requisição: ' + error);
    });
});

// Auto-submit quando filtros mudarem (se houver)
let filterTimeout;
$('#filterForm select').change(function() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => $('#filterForm').submit(), 500);
});
</script>

<style>
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-header {
    font-weight: 600;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.badge {
    font-size: 0.85em;
    padding: 0.5em 0.75em;
}

.btn-group .btn {
    margin-right: 2px;
}

.table td {
    vertical-align: middle;
}

/* Animações */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.3s ease-out;
}
</style>
<?= $this->endSection() ?>