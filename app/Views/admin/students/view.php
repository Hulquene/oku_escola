<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/students/form-edit/' . $student->id) ?>" class="btn btn-info">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="<?= site_url('admin/students/enrollments/history/' . $student->id) ?>" class="btn btn-primary">
                <i class="fas fa-history"></i> Histórico
            </a>
            <a href="<?= site_url('admin/students') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students') ?>">Alunos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes do Aluno</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row">
    <!-- Foto e Informações Básicas -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-user-graduate"></i> Foto do Aluno
            </div>
            <div class="card-body text-center">
                <?php if ($student->photo): ?>
                    <img src="<?= base_url('uploads/students/' . $student->photo) ?>" 
                         alt="Foto do Aluno" 
                         class="img-fluid rounded-circle mb-3"
                         style="width: 150px; height: 150px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 150px; height: 150px;">
                        <i class="fas fa-user-graduate fa-5x text-secondary"></i>
                    </div>
                <?php endif; ?>
                
                <h4><?= $student->first_name ?> <?= $student->last_name ?></h4>
                <p class="text-muted">
                    <span class="badge bg-info"><?= $student->student_number ?></span>
                </p>
                
                <div class="mt-3">
                    <?php if ($student->is_active): ?>
                        <span class="badge bg-success p-2">Ativo</span>
                    <?php else: ?>
                        <span class="badge bg-danger p-2">Inativo</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Contactos -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-address-book"></i> Contactos
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th><i class="fas fa-envelope"></i> Email:</th>
                        <td><?= $student->email ?></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-phone"></i> Telefone:</th>
                        <td><?= $student->phone ?: '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Contacto de Emergência -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-phone-alt"></i> Contacto de Emergência
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>Nome:</th>
                        <td><?= $student->emergency_contact_name ?: '-' ?></td>
                    </tr>
                    <tr>
                        <th>Telefone:</th>
                        <td><?= $student->emergency_contact ?: '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Informações Detalhadas -->
    <div class="col-md-8">
        <!-- Matrícula Atual -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-graduation-cap"></i> Matrícula Atual
            </div>
            <div class="card-body">
                <?php if ($currentEnrollment): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Turma:</strong> <?= $currentEnrollment->class_name ?></p>
                            <p><strong>Ano Letivo:</strong> <?= $currentEnrollment->year_name ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Data Matrícula:</strong> <?= date('d/m/Y', strtotime($currentEnrollment->enrollment_date)) ?></p>
                            <p><strong>Nº Matrícula:</strong> <?= $currentEnrollment->enrollment_number ?></p>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="<?= site_url('admin/students/enrollments/view/' . $currentEnrollment->id) ?>" 
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Ver Detalhes da Matrícula
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Aluno não possui matrícula ativa no momento.</p>
                    <a href="<?= site_url('admin/students/enrollments/form-add?student=' . $student->id) ?>" 
                       class="btn btn-sm btn-success">
                        <i class="fas fa-plus-circle"></i> Matricular Aluno
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Informações Pessoais -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-id-card"></i> Informações Pessoais
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th>Nome Completo:</th>
                                <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                            </tr>
                            <tr>
                                <th>Data Nascimento:</th>
                                <td><?= date('d/m/Y', strtotime($student->birth_date)) ?></td>
                            </tr>
                            <tr>
                                <th>Gênero:</th>
                                <td><?= $student->gender ?></td>
                            </tr>
                            <tr>
                                <th>Nacionalidade:</th>
                                <td><?= $student->nationality ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th>Tipo Documento:</th>
                                <td><?= $student->identity_type ?></td>
                            </tr>
                            <tr>
                                <th>Nº Documento:</th>
                                <td><?= $student->identity_document ?: '-' ?></td>
                            </tr>
                            <tr>
                                <th>NIF:</th>
                                <td><?= $student->nif ?: '-' ?></td>
                            </tr>
                            <tr>
                                <th>Tipo Sanguíneo:</th>
                                <td><?= $student->blood_type ?: '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Endereço -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-map-marker-alt"></i> Endereço
            </div>
            <div class="card-body">
                <p>
                    <?= $student->address ?: '-' ?><br>
                    <?= $student->city ? $student->city . ', ' : '' ?>
                    <?= $student->municipality ? $student->municipality . ', ' : '' ?>
                    <?= $student->province ?: '' ?>
                </p>
            </div>
        </div>
        
        <!-- Informações de Saúde -->
        <?php if ($student->health_conditions || $student->special_needs): ?>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-heartbeat"></i> Informações de Saúde
            </div>
            <div class="card-body">
                <?php if ($student->health_conditions): ?>
                    <p><strong>Condições de Saúde:</strong><br><?= nl2br($student->health_conditions) ?></p>
                <?php endif; ?>
                
                <?php if ($student->special_needs): ?>
                    <p><strong>Necessidades Especiais:</strong><br><?= nl2br($student->special_needs) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Encarregados de Educação -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-users"></i> Encarregados de Educação</span>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#guardianModal">
                        <i class="fas fa-plus-circle"></i> Adicionar
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($guardians)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Parentesco</th>
                                    <th>Contacto</th>
                                    <th>Principal</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($guardians as $guardian): ?>
                                    <tr>
                                        <td><?= $guardian->full_name ?></td>
                                        <td><?= $guardian->relationship ?: $guardian->guardian_type ?></td>
                                        <td><?= $guardian->phone ?></td>
                                        <td>
                                            <?php if ($guardian->is_primary): ?>
                                                <span class="badge bg-success">Principal</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="removeGuardian(<?= $student->id ?>, <?= $guardian->id ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Nenhum encarregado associado.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Resumo Financeiro -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-pie"></i> Resumo Financeiro
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h3 class="text-primary"><?= number_format($feesSummary->total_fees ?? 0) ?></h3>
                            <small class="text-muted">Total de Taxas</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h3 class="text-success"><?= number_format($feesSummary->paid ?? 0, 2, ',', '.') ?> Kz</h3>
                            <small class="text-muted">Valor Pago</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h3 class="text-warning"><?= number_format($feesSummary->pending ?? 0, 2, ',', '.') ?> Kz</h3>
                            <small class="text-muted">Valor Pendente</small>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($recentPayments)): ?>
                    <hr>
                    <h6>Últimos Pagamentos</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Referência</th>
                                    <th>Valor</th>
                                    <th>Método</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentPayments as $payment): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($payment->payment_date)) ?></td>
                                        <td><?= $payment->payment_number ?></td>
                                        <td><?= number_format($payment->amount_paid, 2, ',', '.') ?> Kz</td>
                                        <td><?= $payment->payment_method ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Adicionar Encarregado -->
<div class="modal fade" id="guardianModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/students/guardians/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="student_id" value="<?= $student->id ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Encarregado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="guardian_id" class="form-label">Selecionar Encarregado</label>
                        <select class="form-select" id="guardian_id" name="guardian_id" required>
                            <option value="">Selecione...</option>
                            <!-- Options serão carregadas via AJAX -->
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="relationship" class="form-label">Parentesco</label>
                        <input type="text" class="form-control" id="relationship" name="relationship">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary" value="1">
                            <label class="form-check-label" for="is_primary">Encarregado Principal</label>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function removeGuardian(studentId, guardianId) {
    if (confirm('Tem certeza que deseja remover este encarregado?')) {
        fetch('<?= site_url('admin/students/guardians/remove') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                student_id: studentId,
                guardian_id: guardianId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao remover encarregado');
            }
        });
    }
}

// Carregar encarregados disponíveis via AJAX
document.getElementById('guardianModal').addEventListener('show.bs.modal', function() {
    fetch('<?= site_url('admin/students/guardians/get-available/' . $student->id) ?>')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('guardian_id');
            select.innerHTML = '<option value="">Selecione...</option>';
            data.forEach(guardian => {
                select.innerHTML += `<option value="${guardian.id}">${guardian.full_name} (${guardian.phone})</option>`;
            });
        });
});
</script>
<?= $this->endSection() ?>