<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1 class="h3 mb-1"><?= $title ?? 'Encarregados de Educação' ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= route_to('admin.dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= route_to('students.index') ?>">Alunos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Encarregados</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <button type="button" class="hdr-btn primary" data-bs-toggle="modal" data-bs-target="#guardianModal">
                <i class="fas fa-plus-circle"></i> Novo Encarregado
            </button>
            <a href="<?= site_url('admin/students/guardians/export') ?>" class="hdr-btn secondary">
                <i class="fas fa-file-excel"></i> Exportar
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Stats Cards -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <div class="stat-label">Total</div>
            <div class="stat-value"><?= $totalGuardians ?? 0 ?></div>
            <div class="stat-sub">Encarregados</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-user-tie"></i>
        </div>
        <div>
            <div class="stat-label">Pais/Mães</div>
            <?php
            $pais = array_filter($guardians ?? [], function($g) { 
                return in_array($g['guardian_type'], ['Pai', 'Mãe']); 
            });
            ?>
            <div class="stat-value"><?= count($pais) ?></div>
            <div class="stat-sub">Encarregados</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-user-tag"></i>
        </div>
        <div>
            <div class="stat-label">Tutores</div>
            <?php
            $tutores = array_filter($guardians ?? [], function($g) { 
                return in_array($g['guardian_type'], ['Tutor', 'Encarregado']); 
            });
            ?>
            <div class="stat-value"><?= count($tutores) ?></div>
            <div class="stat-sub">Encarregados</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon navy">
            <i class="fas fa-child"></i>
        </div>
        <div>
            <div class="stat-label">Alunos</div>
            <?php
            $totalAlunos = array_sum(array_column($guardians ?? [], 'student_count'));
            ?>
            <div class="stat-value"><?= $totalAlunos ?></div>
            <div class="stat-sub">Vinculados</div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-filter"></i>
            <span>Filtros</span>
        </div>
    </div>
    <div class="ci-card-body">
        <form id="filterForm" class="filter-grid">
            <div>
                <label class="filter-label">Tipo</label>
                <select class="filter-select" id="filterType">
                    <option value="">Todos</option>
                    <option value="Pai">Pai</option>
                    <option value="Mãe">Mãe</option>
                    <option value="Tutor">Tutor</option>
                    <option value="Encarregado">Encarregado</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>
            
            <div>
                <label class="filter-label">Status</label>
                <select class="filter-select" id="filterStatus">
                    <option value="">Todos</option>
                    <option value="1">Ativos</option>
                    <option value="0">Inativos</option>
                </select>
            </div>
            
            <div>
                <label class="filter-label">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="filter-select" id="filterSearch" placeholder="Nome, telefone ou email...">
                </div>
            </div>
            
            <div class="filter-actions" style="grid-column: -1 / 1;">
                <button type="button" class="btn-filter apply" id="applyFilters">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <button type="button" class="btn-filter clear" id="clearFilters">
                    <i class="fas fa-times"></i> Limpar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-list"></i>
            <span>Lista de Encarregados</span>
        </div>
        <span class="badge-ci primary">
            <i class="fas fa-users me-1"></i> <?= $totalGuardians ?? 0 ?> registros
        </span>
    </div>
    
    <div class="ci-card-body p0">
        <div class="table-responsive">
            <table id="guardiansTable" class="ci-table">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Contacto</th>
                        <th>Email</th>
                        <th>Profissão</th>
                        <th class="center">Alunos</th>
                        <th class="center">Status</th>
                        <th class="center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($guardians)): ?>
                        <?php foreach ($guardians as $i => $guardian): ?>
                            <tr>
                                <td><span class="id-chip">#<?= str_pad( $guardian['id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="teacher-initials me-2" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                            <?= strtoupper(substr($guardian['full_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong><?= $guardian['full_name'] ?></strong>
                                            <?php if (!empty($guardian['identity_document'])): ?>
                                                <br><small class="text-muted">Doc: <?= $guardian['identity_document'] ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $typeClass = match($guardian['guardian_type']) {
                                        'Pai' => 'primary',
                                        'Mãe' => 'success',
                                        'Tutor' => 'info',
                                        'Encarregado' => 'warning',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge-ci <?= $typeClass ?>">
                                        <i class="fas fa-user-tie me-1"></i><?= $guardian['guardian_type'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ( $guardian['phone']): ?>
                                        <a href="tel:<?=  $guardian['phone'] ?>" class="text-decoration-none">
                                            <i class="fas fa-phone-alt text-primary me-1"></i><?=  $guardian['phone'] ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($guardian['email']): ?>
                                        <a href="mailto:<?= $guardian['email'] ?>" class="text-decoration-none">
                                            <i class="fas fa-envelope text-info me-1"></i><?= $guardian['email'] ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($guardian['profession']): ?>
                                        <span class="code-badge">
                                            <i class="fas fa-briefcase me-1"></i><?= $guardian['profession'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="center">
                                    <span class="badge-ci primary" style="font-size: 0.75rem;">
                                        <?= $guardian['student_count']?? 0 ?>
                                    </span>
                                </td>
                                <td class="center">
                                    <?php if ($guardian['is_active']): ?>
                                        <span class="status-badge active">
                                            <i class="fas fa-circle me-1" style="font-size: 6px;"></i>Ativo
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge inactive">
                                            <i class="fas fa-circle me-1" style="font-size: 6px;"></i>Inativo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="center">
                                    <div class="action-group">
                                        <button type="button" class="row-btn edit edit-guardian" 
                                                data-id="<?=  $guardian['id'] ?>"
                                                data-name="<?= $guardian['full_name'] ?>"
                                                data-type="<?= $guardian['guardian_type'] ?>"
                                                data-phone="<?=  $guardian['phone'] ?>"
                                                data-phone2="<?=  $guardian['phone2'] ?>"
                                                data-email="<?= $guardian['email'] ?>"
                                                data-profession="<?= $guardian['profession'] ?>"
                                                data-workplace="<?= $guardian['workplace'] ?>"
                                                data-document-type="<?= $guardian['identity_type'] ?>"
                                                data-document="<?=  $guardian['identity_document'] ?>"
                                                data-nif="<?= $guardian['nif'] ?>"
                                                data-birth-date="<?= $guardian['birth_date'] ?>"
                                                data-address="<?= $guardian['address'] ?>"
                                                data-city="<?= $guardian['city'] ?>"
                                                data-municipality="<?= $guardian['municipality'] ?>"
                                                data-province="<?= $guardian['province'] ?>"
                                                data-notes="<?= $guardian['notes'] ?>"
                                                data-active="<?= $guardian['is_active'] ?>"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <button type="button" class="row-btn view" 
                                                onclick="viewGuardian(<?=  $guardian['id'] ?>)"
                                                title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <button type="button" class="row-btn enroll" 
                                                onclick="associateStudents(<?=  $guardian['id'] ?>, '<?= $guardian['full_name'] ?>')"
                                                title="Associar Alunos">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        
                                        <?php if (has_permission('students.delete')): ?>
                                        <button type="button" class="row-btn del" 
                                                onclick="deleteGuardian(<?=  $guardian['id'] ?>)"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                        <!-- Dentro da div action-group, após os botões existentes -->
                                        <?php if (empty($guardian['user_id'])): ?>
                                            <button type="button" class="row-btn success" 
                                                    onclick="createUser(<?= $guardian['id'] ?>, '<?= $guardian['full_name'] ?>', '<?= $guardian['email'] ?>', '<?= $guardian['phone'] ?>')"
                                                    title="Criar usuário">
                                                <i class="fas fa-user-plus"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="row-btn secondary" 
                                                    onclick="viewUser(<?= $guardian['user_id'] ?>)"
                                                    title="Ver usuário (ID: <?= $guardian['user_id'] ?>)">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <h5>Nenhum encarregado encontrado</h5>
                                    <p class="text-muted">Clique no botão "Novo Encarregado" para adicionar.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL PARA CADASTRO DE ENCARREGADO - TAMANHO MÉDIO -->
<div class="modal fade modal-custom" id="guardianModal" tabindex="-1">
    <div class="modal-dialog modal-lg"> <!-- Tamanho médio (modal-lg) -->
        <div class="modal-content">
            <form action="<?= route_to('students.guardians.save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="guardianId">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-user-tie me-2"></i>
                        <span>Novo Encarregado</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <!-- Informações Pessoais -->
                    <div class="form-section-title">
                        <i class="fas fa-user"></i> Informações Pessoais
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label-ci">
                                <i class="fas fa-user"></i> Nome Completo <span class="req">*</span>
                            </label>
                            <input type="text" class="form-input-ci" id="full_name" name="full_name" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label-ci">
                                <i class="fas fa-tag"></i> Tipo <span class="req">*</span>
                            </label>
                            <select class="form-select-ci" id="guardian_type" name="guardian_type" required>
                                <option value="">Selecione...</option>
                                <option value="Pai">Pai</option>
                                <option value="Mãe">Mãe</option>
                                <option value="Tutor">Tutor</option>
                                <option value="Encarregado">Encarregado</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Contactos -->
                    <div class="form-section-title">
                        <i class="fas fa-address-book"></i> Contactos
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label-ci">
                                <i class="fas fa-phone"></i> Telefone <span class="req">*</span>
                            </label>
                            <input type="text" class="form-input-ci" id="phone" name="phone" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label-ci">
                                <i class="fas fa-phone-alt"></i> Telefone Alternativo
                            </label>
                            <input type="text" class="form-input-ci" id="phone2" name="phone2">
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label-ci">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email" class="form-input-ci" id="email" name="email">
                        </div>
                    </div>
                    
                    <!-- Profissão -->
                    <div class="form-section-title">
                        <i class="fas fa-briefcase"></i> Profissão
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label-ci">
                                <i class="fas fa-briefcase"></i> Profissão
                            </label>
                            <input type="text" class="form-input-ci" id="profession" name="profession">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label-ci">
                                <i class="fas fa-building"></i> Local de Trabalho
                            </label>
                            <input type="text" class="form-input-ci" id="workplace" name="workplace">
                        </div>
                    </div>
                    
                    <!-- Documentos -->
                    <div class="form-section-title">
                        <i class="fas fa-id-card"></i> Documentos
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label-ci">
                                <i class="fas fa-id-card"></i> Tipo Documento
                            </label>
                            <select class="form-select-ci" id="identity_type" name="identity_type">
                                <option value="">Selecione...</option>
                                <option value="BI">BI</option>
                                <option value="Passaporte">Passaporte</option>
                                <option value="Cédula">Cédula</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label-ci">
                                <i class="fas fa-hashtag"></i> Nº Documento
                            </label>
                            <input type="text" class="form-input-ci" id="identity_document" name="identity_document">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label-ci">
                                <i class="fas fa-barcode"></i> NIF
                            </label>
                            <input type="text" class="form-input-ci" id="nif" name="nif">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label-ci">
                                <i class="fas fa-calendar"></i> Data Nascimento
                            </label>
                            <input type="date" class="form-input-ci" id="birth_date" name="birth_date">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label-ci">&nbsp;</label>
                            <div class="d-flex align-items-center mt-2">
                                <div class="ci-switch me-2">
                                    <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                                    <label class="ci-switch-track" for="is_active"></label>
                                </div>
                                <label class="form-label-ci mb-0">Ativo</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Endereço -->
                    <div class="form-section-title">
                        <i class="fas fa-map-marker-alt"></i> Endereço
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label-ci">
                                <i class="fas fa-map-marker-alt"></i> Endereço
                            </label>
                            <textarea class="form-input-ci" id="address" name="address" rows="2"></textarea>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label-ci">Cidade</label>
                            <input type="text" class="form-input-ci" id="city" name="city">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label-ci">Município</label>
                            <input type="text" class="form-input-ci" id="municipality" name="municipality">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label-ci">Província</label>
                            <input type="text" class="form-input-ci" id="province" name="province">
                        </div>
                    </div>
                    
                    <!-- Observações -->
                    <div class="form-section-title">
                        <i class="fas fa-sticky-note"></i> Observações
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-12">
                            <textarea class="form-input-ci" id="notes" name="notes" rows="2" placeholder="Observações adicionais..."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-filter clear" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn-filter apply">
                        <i class="fas fa-save me-2"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Associar Alunos -->
<div class="modal fade modal-custom" id="associateStudentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= route_to('students.guardians.associate-students') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="guardian_id" id="assocGuardianId">
                
                <div class="modal-header" style="background: var(--success);">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>
                        <span id="assocModalTitle">Associar Alunos</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="alert-ci info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Selecione os alunos que deseja associar a <strong id="guardianNameDisplay"></strong>
                    </div>
                    
                    <!-- Filtro para alunos -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="filter-label">Filtrar por Turma</label>
                            <select class="filter-select" id="filterClass">
                                <option value="">Todas as Turmas</option>
                                <?php if (!empty($classes)): ?>
                                    <?php foreach ($classes as $class): ?>
                                        <option value="<?= $class['id'] ?>"><?= $class['class_name'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="filter-label">Buscar Aluno</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="filter-select" id="searchStudent" placeholder="Nome do aluno...">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista de alunos -->
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="ci-table">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAllStudents" class="form-check-input">
                                    </th>
                                    <th>Aluno</th>
                                    <th>Turma</th>
                                    <th>Nº Processo</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="studentsList">
                                <?php if (!empty($students)): ?>
                                    <?php foreach ($students as $student): ?>
                                        <tr class="student-row" data-class-id="<?= $student['class_id'] ?>" data-name="<?= strtolower($student['name']?? $student['first_name'] . ' ' . $student['last_name']) ?>">
                                            <td>
                                                <input type="checkbox" name="student_ids[]" value="<?= $student['id'] ?>" 
                                                       class="form-check-input student-checkbox"
                                                       <?= isset($guardianStudents[$assocGuardianId ?? 0]) && in_array($student['id'], $guardianStudents[$assocGuardianId ?? 0]) ? 'checked' : '' ?>>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="teacher-initials me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                        <?= strtoupper(substr($student['name']?? $student['first_name'], 0, 1)) ?>
                                                    </div>
                                                    <?= $student['name']?? $student['first_name'] . ' ' . $student['last_name'] ?>
                                                </div>
                                            </td>
                                            <td><?= $student['class_name'] ?? '-' ?></td>
                                            <td><span class="code-badge"><?= $student['student_number'] ?></span></td>
                                            <td>
                                                <?php if ($student['is_active']): ?>
                                                    <span class="status-badge active">Ativo</span>
                                                <?php else: ?>
                                                    <span class="status-badge inactive">Inativo</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-3">
                                            <div class="empty-state">
                                                <i class="fas fa-users fa-2x mb-2"></i>
                                                <p>Nenhum aluno encontrado</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge-ci primary" id="selectedCount">0</span> alunos selecionados
                        </div>
                        <div>
                            <button type="button" class="btn-filter clear" id="clearSelections">
                                <i class="fas fa-times"></i> Limpar Seleção
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-filter clear" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn-filter apply">
                        <i class="fas fa-save me-2"></i>Salvar Associações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Criar Usuário -->
<div class="modal fade ci-modal" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--success);">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>
                    Criar Usuário para Encarregado
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="createUserForm">
                <?= csrf_field() ?>
                <input type="hidden" name="guardian_id" id="userGuardianId">
                
                <div class="modal-body">
                    <div class="alert-ci info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Será criado um usuário para <strong id="userGuardianName"></strong> com acesso à área de encarregados.
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="filter-label">Nome de Usuário</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-input-ci" id="username" name="username" 
                                   placeholder="nome.sobrenome" required>
                        </div>
                        <small class="form-text text-muted">Nome de usuário para login</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="filter-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-input-ci" id="userEmail" name="email" 
                                   placeholder="email@exemplo.com" required>
                        </div>
                        <small class="form-text text-muted">Email para login e recuperação</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="filter-label">Senha</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="text" class="form-input-ci" id="userPassword" name="password" 
                                   value="<?= bin2hex(random_bytes(4)) ?>" required readonly>
                            <button type="button" class="btn-filter clear" onclick="generatePassword()" 
                                    style="border-radius: 0 10px 10px 0; margin: 0;">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">Senha gerada automaticamente</small>
                    </div>
                    
                    <div class="alert-ci warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção:</strong> Guarde esta senha para fornecer ao encarregado.
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-filter clear" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn-filter success" id="createUserBtn">
                        <i class="fas fa-save me-2"></i>Criar Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#guardiansTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc']],
        pageLength: 25,
        dom: 'rtip',
        buttons: []
    });
    
    // Apply filters
    $('#applyFilters').on('click', function() {
        var type = $('#filterType').val();
        var status = $('#filterStatus').val();
        var search = $('#filterSearch').val();
        
        // Apply column filters
        if (type) {
            table.column(2).search('^' + type + '$', true, false).draw();
        } else {
            table.column(2).search('').draw();
        }
        
        if (status === '1') {
            table.column(7).search('Ativo').draw();
        } else if (status === '0') {
            table.column(7).search('Inativo').draw();
        } else {
            table.column(7).search('').draw();
        }
        
        table.search(search).draw();
    });
    
    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#filterType').val('');
        $('#filterStatus').val('');
        $('#filterSearch').val('');
        table.search('').columns().search('').draw();
    });
    
    // Edit guardian
    $('.edit-guardian').click(function() {
        $('#modalTitle span').text('Editar Encarregado');
        $('#guardianId').val($(this).data('id'));
        $('#full_name').val($(this).data('name'));
        $('#guardian_type').val($(this).data('type'));
        $('#phone').val($(this).data('phone'));
        $('#phone2').val($(this).data('phone2'));
        $('#email').val($(this).data('email'));
        $('#profession').val($(this).data('profession'));
        $('#workplace').val($(this).data('workplace'));
        $('#identity_type').val($(this).data('document-type'));
        $('#identity_document').val($(this).data('document'));
        $('#nif').val($(this).data('nif'));
        $('#birth_date').val($(this).data('birth-date'));
        $('#address').val($(this).data('address'));
        $('#city').val($(this).data('city'));
        $('#municipality').val($(this).data('municipality'));
        $('#province').val($(this).data('province'));
        $('#notes').val($(this).data('notes'));
        
        // Handle switch
        $('#is_active').prop('checked', $(this).data('active') == 1);
        
        $('#guardianModal').modal('show');
    });
    
    // New guardian button
    $('[data-bs-target="#guardianModal"]').click(function() {
        $('#modalTitle span').text('Novo Encarregado');
        $('#guardianId').val('');
        $('#guardianModal form')[0].reset();
        $('#is_active').prop('checked', true);
    });
    
    // Reset form on modal close
    $('#guardianModal').on('hidden.bs.modal', function() {
        if (!$('#guardianId').val()) {
            $(this).find('form')[0].reset();
            $('#is_active').prop('checked', true);
        }
    });
    
    // Toggle switch styling
    $('#is_active').change(function() {
        $('.ci-switch-track').toggleClass('checked', $(this).is(':checked'));
    });


    // No seu arquivo de script, adicione:

$('#associateStudentsModal form').on('submit', function(e) {
    e.preventDefault();
    
    var form = $(this);
    var url = form.attr('action');
    var data = form.serialize();
    
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        beforeSend: function() {
            // Mostrar loading
            $('#associateStudentsModal .btn-filter.apply').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processando...');
        },
        success: function(response) {
            if (response.success) {
                // Fechar modal
                $('#associateStudentsModal').modal('hide');
                
                // Mostrar mensagem de sucesso
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message,
                    timer: 2000
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: response.message
                });
            }
        },
        error: function(xhr) {
            var message = 'Erro ao processar requisição';
            try {
                var response = JSON.parse(xhr.responseText);
                message = response.message || message;
            } catch(e) {}
            
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: message
            });
        },
        complete: function() {
            $('#associateStudentsModal .btn-filter.apply').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Salvar Associações');
        }
    });
});
});

function viewGuardian(id) {
    window.location.href = '<?= site_url('admin/students/guardians/view/') ?>' + id;
}

function associateStudents(guardianId, guardianName) {
    console.log('associateStudents chamado:', guardianId, guardianName);
    
    $('#assocGuardianId').val(guardianId);
    $('#guardianNameDisplay').text(guardianName);
    $('#assocModalTitle').text('Associar Alunos - ' + guardianName);
    
    // Reset filters
    $('#filterClass').val('');
    $('#searchStudent').val('');
    $('.student-row').show();
    
    // Buscar alunos já associados para este guardião - CORRIGIDO
    var url = '<?= site_url('admin/students/guardians/get-by-student/') ?>' + guardianId;
    console.log('URL da requisição:', url);
    
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function(associados) {
            console.log('Associados recebidos:', associados);
            
            // Limpar todas as seleções
            $('.student-checkbox').prop('checked', false);
            
            // Marcar checkboxes dos alunos já associados
            if (associados && associados.length > 0) {
                var associadosIds = associados.map(function(g) { 
                    return g.student_id ? g.student_id : g.id; 
                });
                
                $('.student-checkbox').each(function() {
                    var studentId = parseInt($(this).val());
                    if (associadosIds.includes(studentId)) {
                        $(this).prop('checked', true);
                    }
                });
            }
            
            updateSelectedCount();
            updateSelectAllState();
        },
        error: function(xhr, status, error) {
            console.error('Erro ao buscar associações:', error);
            console.error('Resposta:', xhr.responseText);
        }
    });
    
    $('#associateStudentsModal').modal('show');
}

function deleteGuardian(id) {
    if (confirm('Tem certeza que deseja eliminar este encarregado?')) {
        window.location.href = '<?= site_url('admin/students/guardians/delete/') ?>' + id;
    }
}

function exportToExcel() {
    window.location.href = '<?= site_url('admin/students/guardians/export') ?>';
}

// Filter students in association modal
$(document).ready(function() {
    $('#filterClass').on('change', function() {
        var classId = $(this).val();
        filterStudents();
    });
    
    $('#searchStudent').on('keyup', function() {
        filterStudents();
    });
    
    function filterStudents() {
        var classId = $('#filterClass').val();
        var searchTerm = $('#searchStudent').val().toLowerCase();
        
        $('.student-row').each(function() {
            var show = true;
            
            if (classId && $(this).data('class-id') != classId) {
                show = false;
            }
            
            if (searchTerm && $(this).data('name').indexOf(searchTerm) === -1) {
                show = false;
            }
            
            $(this).toggle(show);
        });
        
        // Update select all checkbox after filtering
        updateSelectAllState();
    }
    
    // Select all students
    $('#selectAllStudents').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.student-checkbox:visible').prop('checked', isChecked);
        updateSelectedCount();
    });
    
    // Individual checkbox change
    $(document).on('change', '.student-checkbox', function() {
        updateSelectedCount();
        updateSelectAllState();
    });
    
    function updateSelectAllState() {
        var totalVisible = $('.student-checkbox:visible').length;
        var checkedVisible = $('.student-checkbox:visible:checked').length;
        
        if (totalVisible === 0) {
            $('#selectAllStudents').prop('checked', false).prop('indeterminate', false);
        } else if (checkedVisible === totalVisible) {
            $('#selectAllStudents').prop('checked', true).prop('indeterminate', false);
        } else if (checkedVisible > 0) {
            $('#selectAllStudents').prop('indeterminate', true);
        } else {
            $('#selectAllStudents').prop('checked', false).prop('indeterminate', false);
        }
    }
    
    // Clear selections
    $('#clearSelections').on('click', function() {
        $('.student-checkbox').prop('checked', false);
        $('#selectAllStudents').prop('checked', false).prop('indeterminate', false);
        updateSelectedCount();
    });
    
    function updateSelectedCount() {
        var count = $('.student-checkbox:checked').length;
        $('#selectedCount').text(count);
    }
    
    // Initialize counts when modal opens
    $('#associateStudentsModal').on('shown.bs.modal', function() {
        updateSelectedCount();
        updateSelectAllState();
    });
});

// Switch styling initialization
$(document).ready(function() {
    $('.ci-switch-track').each(function() {
        var input = $(this).prev('input');
        if (input.is(':checked')) {
            $(this).addClass('checked');
        }
    });
});
</script>
<script>
// Função para criar usuário
function createUser(guardianId, guardianName, email, phone) {
    $('#userGuardianId').val(guardianId);
    $('#userGuardianName').text(guardianName);
    
    // Sugerir username baseado no nome
    var nameParts = guardianName.toLowerCase().split(' ');
    var username = nameParts[0] + '.' + nameParts[nameParts.length - 1];
    username = username.replace(/[^a-z0-9.]/g, '');
    $('#username').val(username);
    
    // Sugerir email baseado no email existente ou gerar
    if (email) {
        $('#userEmail').val(email);
    } else {
        $('#userEmail').val(username + '@escola.ao');
    }
    
    // Gerar senha aleatória
    generatePassword();
    
    $('#createUserModal').modal('show');
}

// Função para gerar senha aleatória
function generatePassword() {
    var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    var password = "";
    for (var i = 0; i < 8; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    $('#userPassword').val(password);
}

// Função para visualizar usuário
function viewUser(userId) {
    window.location.href = '<?= site_url('admin/users/view/') ?>' + userId;
}

// Processar formulário de criação de usuário
$('#createUserForm').on('submit', function(e) {
    e.preventDefault();
    
    var form = $(this);
    var data = form.serialize();
    
    $.ajax({
        url: '<?= site_url('admin/students/guardians/create-user') ?>',
        type: 'POST',
        data: data,
        dataType: 'json',
        beforeSend: function() {
            $('#createUserBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processando...');
        },
        success: function(response) {
            if (response.success) {
                // Fechar modal
                $('#createUserModal').modal('hide');
                
                // Mostrar mensagem de sucesso com as credenciais
                Swal.fire({
                    icon: 'success',
                    title: 'Usuário criado com sucesso!',
                    html: `
                        <strong>Usuário:</strong> ${response.username}<br>
                        <strong>Senha:</strong> <span class="text-success">${response.password}</span><br>
                        <small class="text-muted">Anote estas credenciais para entregar ao encarregado.</small>
                    `,
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: response.message
                });
            }
        },
        error: function(xhr) {
            var message = 'Erro ao processar requisição';
            try {
                var response = JSON.parse(xhr.responseText);
                message = response.message || message;
            } catch(e) {}
            
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: message
            });
        },
        complete: function() {
            $('#createUserBtn').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Criar Usuário');
        }
    });
});
</script>
<?= $this->endSection() ?>