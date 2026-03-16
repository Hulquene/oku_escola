<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
/* Estilos específicos para a página de detalhes do aluno */
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

.photo-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.5rem;
    background: var(--surface);
    border-radius: var(--radius);
}

.student-photo {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--border);
    box-shadow: var(--shadow-md);
    margin-bottom: 1rem;
}

.photo-placeholder {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: var(--surface);
    border: 3px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.photo-placeholder i {
    font-size: 4rem;
    color: var(--text-muted);
}

.student-name-large {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.student-number-badge {
    font-family: var(--font-mono);
    font-size: 0.85rem;
    background: var(--accent);
    color: #fff;
    padding: 0.25rem 1rem;
    border-radius: 50px;
    display: inline-block;
}

.financial-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.financial-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 1rem;
    text-align: center;
}

.financial-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--accent);
    font-family: var(--font-mono);
    line-height: 1.2;
    margin-bottom: 0.25rem;
}

.financial-label {
    font-size: 0.7rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.07em;
}
</style>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-user-graduate me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= route_to('admin.dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= route_to('students.index') ?>">Alunos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalhes do Aluno</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/students/form-edit/' . $student['id']) ?>" class="hdr-btn primary">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="<?= site_url('admin/students/enrollments/history/' . $student['id']) ?>" class="hdr-btn warning">
                <i class="fas fa-history"></i> Histórico
            </a>
            <a href="<?= route_to('students.index') ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row g-4">
    <!-- Coluna Esquerda -->
    <div class="col-lg-4">
        <!-- Foto do Aluno -->
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-camera"></i>
                    <span>Foto do Aluno</span>
                </div>
            </div>
            <div class="ci-card-body text-center">
                <?php if (!empty($student['photo'])): ?>
                    <img src="<?= base_url('uploads/students/' . $student['photo']) ?>" 
                         alt="Foto do Aluno" 
                         class="student-photo">
                <?php else: ?>
                    <div class="photo-placeholder">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                <?php endif; ?>
                
                <div class="student-name-large"><?= $student['first_name'] ?> <?= $student['last_name'] ?></div>
                <div class="student-number-badge mb-3"><?= $student['student_number'] ?></div>
                
                <div class="mt-2">
                    <?php if ($student['is_active']): ?>
                        <span class="status-badge ativo">
                            <span class="status-dot"></span> Ativo
                        </span>
                    <?php else: ?>
                        <span class="status-badge inativo">
                            <span class="status-dot"></span> Inativo
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Contactos -->
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-address-book"></i>
                    <span>Contactos</span>
                </div>
            </div>
            <div class="ci-card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value">
                            <i class="fas fa-envelope me-1"></i>
                            <?= $student['email'] ?? '-' ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Telefone</div>
                        <div class="info-value">
                            <i class="fas fa-phone-alt me-1"></i>
                            <?= $student['phone'] ?: '-' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contacto de Emergência -->
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-phone-alt"></i>
                    <span>Contacto de Emergência</span>
                </div>
            </div>
            <div class="ci-card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nome</div>
                        <div class="info-value">
                            <i class="fas fa-user me-1"></i>
                            <?= $student['emergency_contact_name'] ?: '-' ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Telefone</div>
                        <div class="info-value">
                            <i class="fas fa-phone me-1"></i>
                            <?= $student['emergency_contact'] ?: '-' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Coluna Direita -->
    <div class="col-lg-8">
        <!-- Matrícula Atual -->
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Matrícula Atual</span>
                </div>
            </div>
            <div class="ci-card-body">
                <?php if (!empty($currentEnrollment)): ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Turma</div>
                                <div class="info-value">
                                    <i class="fas fa-users me-1"></i>
                                    <?= $currentEnrollment['class_name'] ?? '-' ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Ano Letivo</div>
                                <div class="info-value">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?= $currentEnrollment['year_name'] ?? '-' ?>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($currentEnrollment['course_name'])): ?>
                        <div class="col-12">
                            <div class="info-item">
                                <div class="info-label">Curso</div>
                                <div class="info-value">
                                    <span class="badge bg-primary"><?= esc($currentEnrollment['course_name']) ?></span>
                                    <?php if (!empty($currentEnrollment['course_code'])): ?>
                                        <small class="text-muted ms-2"><?= esc($currentEnrollment['course_code']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Data Matrícula</div>
                                <div class="info-value">
                                    <i class="fas fa-calendar-check me-1"></i>
                                    <?= date('d/m/Y', strtotime($currentEnrollment['enrollment_date'])) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Nº Matrícula</div>
                                <div class="info-value">
                                    <span class="student-number"><?= $currentEnrollment['enrollment_number'] ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 mt-2">
                            <a href="<?= site_url('admin/students/enrollments/view/' . $currentEnrollment['id']) ?>" 
                               class="btn-filter apply">
                                <i class="fas fa-eye me-1"></i> Ver Detalhes da Matrícula
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-graduation-cap"></i>
                        <p class="mb-2">Aluno não possui matrícula ativa no momento.</p>
                        <a href="<?= site_url('admin/students/enrollments/form-add?student=' . $student['id']) ?>" 
                           class="btn-filter apply">
                            <i class="fas fa-plus-circle me-1"></i> Matricular Aluno
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Informações Pessoais -->
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-id-card"></i>
                    <span>Informações Pessoais</span>
                </div>
            </div>
            <div class="ci-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">Nome Completo</div>
                            <div class="info-value">
                                <i class="fas fa-user me-1"></i>
                                <?= $student['first_name'] ?> <?= $student['last_name'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">Data Nascimento</div>
                            <div class="info-value">
                                <i class="fas fa-calendar me-1"></i>
                                <?= date('d/m/Y', strtotime($student['birth_date'])) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">Gênero</div>
                            <div class="info-value">
                                <i class="fas fa-venus-mars me-1"></i>
                                <?= $student['gender'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">Nacionalidade</div>
                            <div class="info-value">
                                <i class="fas fa-flag me-1"></i>
                                <?= $student['nationality'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">Tipo Documento</div>
                            <div class="info-value">
                                <i class="fas fa-id-card me-1"></i>
                                <?= $student['identity_type'] ?: '-' ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">Nº Documento</div>
                            <div class="info-value">
                                <i class="fas fa-hashtag me-1"></i>
                                <?= $student['identity_document'] ?: '-' ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">NIF</div>
                            <div class="info-value">
                                <i class="fas fa-barcode me-1"></i>
                                <?= $student['nif'] ?: '-' ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">Tipo Sanguíneo</div>
                            <div class="info-value">
                                <i class="fas fa-tint me-1"></i>
                                <?= $student['blood_type'] ?: '-' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Endereço -->
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Endereço</span>
                </div>
            </div>
            <div class="ci-card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="info-item">
                            <div class="info-label">Endereço</div>
                            <div class="info-value">
                                <i class="fas fa-home me-1"></i>
                                <?= $student['address'] ?: '-' ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <div class="info-label">Cidade</div>
                            <div class="info-value">
                                <i class="fas fa-city me-1"></i>
                                <?= $student['city'] ?: '-' ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <div class="info-label">Município</div>
                            <div class="info-value">
                                <i class="fas fa-map-pin me-1"></i>
                                <?= $student['municipality'] ?: '-' ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <div class="info-label">Província</div>
                            <div class="info-value">
                                <i class="fas fa-map me-1"></i>
                                <?= $student['province'] ?: '-' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informações de Saúde -->
        <?php if (!empty($student['health_conditions']) || !empty($student['special_needs'])): ?>
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-heartbeat"></i>
                    <span>Informações de Saúde</span>
                </div>
            </div>
            <div class="ci-card-body">
                <?php if (!empty($student['health_conditions'])): ?>
                    <div class="info-item mb-3">
                        <div class="info-label">Condições de Saúde</div>
                        <div class="info-value">
                            <?= nl2br($student['health_conditions']) ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($student['special_needs'])): ?>
                    <div class="info-item">
                        <div class="info-label">Necessidades Especiais</div>
                        <div class="info-value">
                            <?= nl2br($student['special_needs']) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Encarregados de Educação -->
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-users"></i>
                    <span>Encarregados de Educação</span>
                </div>
                <button type="button" class="hdr-btn primary" data-bs-toggle="modal" data-bs-target="#guardianModal">
                    <i class="fas fa-plus-circle me-1"></i> Adicionar
                </button>
            </div>
            <div class="ci-card-body p0">
                <?php if (!empty($guardians)): ?>
                    <div class="table-responsive">
                        <table class="ci-table">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Parentesco</th>
                                    <th>Contacto</th>
                                    <th class="text-center">Principal</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($guardians as $guardian): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="teacher-initials me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                    <?= strtoupper(substr($guardian['full_name'], 0, 1)) ?>
                                                </div>
                                                <strong><?= $guardian['full_name'] ?></strong>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $relationship = $guardian['relationship'] ?? $guardian['guardian_type'];
                                            $relClass = match($relationship) {
                                                'Pai' => 'primary',
                                                'Mãe' => 'success',
                                                'Tutor' => 'info',
                                                'Encarregado' => 'warning',
                                                default => 'secondary'
                                            };
                                            ?>
                                            <span class="badge bg-<?= $relClass ?>"><?= $relationship ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($guardian['phone'])): ?>
                                                <a href="tel:<?= $guardian['phone'] ?>" class="text-decoration-none">
                                                    <i class="fas fa-phone-alt text-primary me-1"></i><?= $guardian['phone'] ?>
                                                </a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($guardian['is_primary'])): ?>
                                                <span class="current-badge"><i class="fas fa-star"></i> Principal</span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-group">
                                                <button type="button" class="row-btn del" 
                                                        onclick="removeGuardian(<?= $student['id'] ?>, <?= $guardian['id'] ?>)"
                                                        title="Remover">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <h5>Nenhum encarregado associado</h5>
                        <p>Clique em "Adicionar" para associar um encarregado a este aluno.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Resumo Financeiro -->
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-chart-pie"></i>
                    <span>Resumo Financeiro</span>
                </div>
            </div>
            <div class="ci-card-body">
                <div class="financial-grid">
                    <div class="financial-card">
                        <div class="financial-value"><?= number_format($feesSummary->total_fees ?? 0) ?></div>
                        <div class="financial-label">Total de Taxas</div>
                    </div>
                    <div class="financial-card">
                        <div class="financial-value text-success"><?= number_format($feesSummary->paid ?? 0, 2, ',', '.') ?> Kz</div>
                        <div class="financial-label">Valor Pago</div>
                    </div>
                    <div class="financial-card">
                        <div class="financial-value text-warning"><?= number_format($feesSummary->pending ?? 0, 2, ',', '.') ?> Kz</div>
                        <div class="financial-label">Valor Pendente</div>
                    </div>
                </div>
                
                <?php if (!empty($recentPayments)): ?>
                    <hr class="my-4">
                    <h6 class="mb-3">Últimos Pagamentos</h6>
                    <div class="table-responsive">
                        <table class="ci-table">
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
                                        <td><span class="period-text"><?= date('d/m/Y', strtotime($payment->payment_date)) ?></span></td>
                                        <td><span class="code-badge"><?= $payment->payment_number ?></span></td>
                                        <td><span class="num-chip"><?= number_format($payment->amount_paid, 2, ',', '.') ?> Kz</span></td>
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
<div class="modal fade ci-modal" id="guardianModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/students/guardians/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>
                        Adicionar Encarregado
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-ci" for="guardian_id">Selecionar Encarregado</label>
                        <select class="form-select-ci" id="guardian_id" name="guardian_id" required>
                            <option value="">Selecione...</option>
                            <!-- Options serão carregadas via AJAX -->
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label-ci" for="relationship">Parentesco</label>
                        <input type="text" class="form-input-ci" id="relationship" name="relationship" placeholder="Ex: Pai, Mãe, Tutor...">
                    </div>
                    
                    <div class="mb-3">
                        <label class="opt-row">
                            <input type="checkbox" name="is_primary" value="1">
                            <div class="opt-row-text">
                                <div class="ot-title"><i class="fas fa-star" style="color:var(--warning);"></i> Encarregado Principal</div>
                                <div class="ot-sub">Marcar como responsável principal</div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-filter clear" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn-filter apply">
                        <i class="fas fa-save me-1"></i> Adicionar
                    </button>
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
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: data.message || 'Erro ao remover encarregado'
                });
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Erro ao processar requisição'
            });
        });
    }
}

// Carregar encarregados disponíveis via AJAX
document.getElementById('guardianModal').addEventListener('show.bs.modal', function() {
    const select = document.getElementById('guardian_id');
    select.innerHTML = '<option value="">Carregando...</option>';
    
    fetch('<?= site_url('admin/students/guardians/get-available/' . $student['id']) ?>')
        .then(response => response.json())
        .then(data => {
            select.innerHTML = '<option value="">Selecione...</option>';
            data.forEach(guardian => {
                select.innerHTML += `<option value="${guardian.id}">${guardian.full_name} (${guardian.phone})</option>`;
            });
        })
        .catch(error => {
            console.error('Erro:', error);
            select.innerHTML = '<option value="">Erro ao carregar</option>';
        });
});
</script>
<?= $this->endSection() ?>