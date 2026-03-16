<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
/* Estilos específicos para a página de detalhes */
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

.detail-section {
    margin-bottom: 2rem;
}

.detail-section-title {
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-section-title i {
    color: var(--accent);
    font-size: 0.9rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}
</style>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-user-tie me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= route_to('admin.dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= route_to('students.index') ?>">Alunos</a></li>
                    <li class="breadcrumb-item"><a href="<?= route_to('students.guardians') ?>">Encarregados</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <button type="button" class="hdr-btn warning" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <a href="<?= route_to('students.guardians') ?>" class="hdr-btn primary">
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
        <!-- Informações Pessoais -->
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-user-circle"></i>
                    <span>Informações Pessoais</span>
                </div>
            </div>
            <div class="ci-card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nome Completo</div>
                        <div class="info-value"><?= $guardian['full_name'] ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Tipo</div>
                        <div class="info-value">
                            <?php
                            $typeClass = match($guardian['guardian_type']) {
                                'Pai' => 'type-primary',
                                'Mãe' => 'type-success',
                                'Tutor' => 'type-info',
                                'Encarregado' => 'type-warning',
                                default => 'type-secondary'
                            };
                            ?>
                            <span class="type-badge <?= $typeClass ?>">
                                <i class="fas fa-user-tie me-1"></i><?= $guardian['guardian_type'] ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Telefone</div>
                        <div class="info-value">
                            <i class="fas fa-phone-alt me-1"></i>
                            <?= $guardian['phone'] ?: '-' ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Telefone Alternativo</div>
                        <div class="info-value">
                            <i class="fas fa-phone-alt me-1"></i>
                            <?= $guardian['phone2'] ?? '-' ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value">
                            <i class="fas fa-envelope me-1"></i>
                            <?php if (!empty($guardian['email'])): ?>
                                <a href="mailto:<?= $guardian['email'] ?>" class="text-accent"><?= $guardian['email'] ?></a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Documentos -->
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-id-card"></i>
                    <span>Documentos</span>
                </div>
            </div>
            <div class="ci-card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Tipo Documento</div>
                        <div class="info-value">
                            <i class="fas fa-id-card me-1"></i>
                            <?= $guardian['identity_type'] ?: '-' ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Nº Documento</div>
                        <div class="info-value">
                            <i class="fas fa-hashtag me-1"></i>
                            <?= $guardian['identity_document'] ?: '-' ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">NIF</div>
                        <div class="info-value">
                            <i class="fas fa-barcode me-1"></i>
                            <?= $guardian['nif'] ?: '-' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profissão -->
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-briefcase"></i>
                    <span>Profissão</span>
                </div>
            </div>
            <div class="ci-card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Profissão</div>
                        <div class="info-value">
                            <i class="fas fa-briefcase me-1"></i>
                            <?= $guardian['profession'] ?: '-' ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Local de Trabalho</div>
                        <div class="info-value">
                            <i class="fas fa-building me-1"></i>
                            <?= $guardian['workplace'] ?: '-' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Coluna Direita -->
    <div class="col-lg-8">
        <!-- Endereço -->
        <div class="ci-card mb-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Endereço</span>
                </div>
            </div>
            <div class="ci-card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Endereço</div>
                        <div class="info-value">
                            <i class="fas fa-home me-1"></i>
                            <?= $guardian['address'] ?: '-' ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Cidade</div>
                        <div class="info-value">
                            <i class="fas fa-city me-1"></i>
                            <?= $guardian['city'] ?: '-' ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Município</div>
                        <div class="info-value">
                            <i class="fas fa-map-pin me-1"></i>
                            <?= $guardian['municipality'] ?: '-' ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Província</div>
                        <div class="info-value">
                            <i class="fas fa-map me-1"></i>
                            <?= $guardian['province'] ?: '-' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Alunos Associados -->
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-user-graduate"></i>
                    <span>Alunos Associados</span>
                </div>
                <span class="badge bg-primary"><?= $totalStudents ?? 0 ?> alunos</span>
            </div>
            <div class="ci-card-body p0">
                <?php if (!empty($students)): ?>
                    <div class="table-responsive">
                        <table class="ci-table">
                            <thead>
                                <tr>
                                    <th>Nº Processo</th>
                                    <th>Nome do Aluno</th>
                                    <th>Turma</th>
                                    <th>Parentesco</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td>
                                            <span class="student-number"><?= $student['student_number'] ?? '-' ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="teacher-initials me-2" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                                    <?= strtoupper(substr($student['first_name'] ?? '', 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <strong><?= ($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? '') ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($student['class_name'])): ?>
                                                <span class="badge bg-info"><?= $student['class_name'] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $relationship = $student['relationship'] ?? $guardian['guardian_type'];
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
                                        <td class="text-center">
                                            <?php if ($student['is_active'] ?? false): ?>
                                                <span class="status-badge ativo">
                                                    <span class="status-dot"></span> Ativo
                                                </span>
                                            <?php else: ?>
                                                <span class="status-badge inativo">
                                                    <span class="status-dot"></span> Inativo
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-group">
                                                <a href="<?= site_url('admin/students/view/' . ($student['id'] ?? 0)) ?>" 
                                                   class="row-btn view" 
                                                   title="Ver Aluno"
                                                   data-bs-toggle="tooltip">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-user-graduate"></i>
                        <h5>Nenhum aluno associado</h5>
                        <p>Este encarregado ainda não está associado a nenhum aluno.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Observações -->
        <?php if (!empty($guardian['notes'])): ?>
        <div class="ci-card mt-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-sticky-note"></i>
                    <span>Observações</span>
                </div>
            </div>
            <div class="ci-card-body">
                <p class="mb-0"><?= nl2br($guardian['notes']) ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>