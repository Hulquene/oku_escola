<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
/* Estilos específicos para o formulário */

/* Form Card */
.form-card { 
    max-width: 100% !important; 
}
.form-section {
    margin-bottom: 2rem;
}

.form-section-title {
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
    margin-bottom: 1.25rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-section-title i {
    color: var(--accent);
    font-size: 0.9rem;
}

.form-section-title small {
    text-transform: none;
    font-size: 0.7rem;
    color: var(--text-muted);
    font-weight: 400;
    margin-left: 0.5rem;
}

.step-badge {
    background: var(--primary);
    color: #fff;
    border-radius: 50px;
    padding: 0.2rem 0.8rem;
    font-size: 0.7rem;
    font-weight: 600;
    margin-left: 0.5rem;
}

.photo-preview {
    max-width: 150px;
    border-radius: var(--radius-sm);
    border: 2px solid var(--border);
    padding: 0.25rem;
    margin-top: 0.5rem;
}

.photo-preview:hover {
    border-color: var(--accent);
}

.required-field {
    color: var(--danger);
    font-size: 1rem;
    margin-left: 0.2rem;
}

.form-hint {
    font-size: 0.7rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.form-hint i {
    color: var(--accent);
    font-size: 0.65rem;
}

.alert-ci {
    border-radius: var(--radius-sm);
    border: none;
    font-size: 0.85rem;
    padding: 0.8rem 1rem;
}

.alert-ci.info {
    background: rgba(59,127,232,0.08);
    color: #1E4D8C;
    border-left: 3px solid var(--accent);
}

.alert-ci.warning {
    background: rgba(232,160,32,0.1);
    color: #8A5A00;
    border-left: 3px solid var(--warning);
}

.prereg-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.prereg-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--accent), var(--success));
}

.prereg-card-header {
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.prereg-card-header i {
    color: var(--accent);
    font-size: 1rem;
}

.prereg-card-header h5 {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

.prereg-card-body {
    padding: 1.25rem;
}

.switch-wrapper {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
}

.switch-wrapper .form-label-ci {
    margin-bottom: 0;
}

.readonly-field {
    background: var(--surface);
    padding: 0.6rem 0.9rem;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-muted);
    font-size: 0.875rem;
}
</style>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-<?= $student ? 'edit' : 'user-plus' ?> me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= route_to('admin.dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= route_to('students.index') ?>">Alunos</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
                </ol>
            </nav>
        </div>
        <?php if (!$student): ?>
            <span class="step-badge">Passo 1 de 2 - Cadastro do Aluno</span>
        <?php endif; ?>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Form -->
<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-title">
            <i class="fas fa-<?= $student ? 'edit' : 'user-plus' ?>"></i>
            <?= $title ?>
        </div>
    </div>
    <div class="form-card-body">
        <!-- Campos Obrigatórios em Destaque -->
        <div class="alert-ci info mb-4">
            <i class="fas fa-info-circle me-1"></i>
            Campos marcados com <span class="text-danger">*</span> são obrigatórios.
        </div>
        
        <form action="<?= site_url('admin/students/save') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <?php if ($student): ?>
                <input type="hidden" name="id" value="<?= $student['id'] ?>">
                <input type="hidden" name="user_id" value="<?= $student['user_id'] ?>">
            <?php endif; ?>
            
            <!-- Informações Pessoais -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-user"></i>
                    <span>Informações Pessoais</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label-ci" for="first_name">
                                <i class="fas fa-user"></i> Nome <span class="req">*</span>
                            </label>
                            <input type="text" 
                                   class="form-input-ci <?= session('errors.first_name') ? 'is-invalid' : '' ?>" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="<?= old('first_name', $student['first_name'] ?? '') ?>"
                                   required
                                   maxlength="50"
                                   placeholder="Ex: João">
                            <?php if (session('errors.first_name')): ?>
                                <div class="form-error"><?= session('errors.first_name') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label-ci" for="last_name">
                                <i class="fas fa-user"></i> Sobrenome <span class="req">*</span>
                            </label>
                            <input type="text" 
                                   class="form-input-ci <?= session('errors.last_name') ? 'is-invalid' : '' ?>" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="<?= old('last_name', $student['last_name'] ?? '') ?>"
                                   required
                                   maxlength="50"
                                   placeholder="Ex: Silva">
                            <?php if (session('errors.last_name')): ?>
                                <div class="form-error"><?= session('errors.last_name') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label-ci" for="email">
                                <i class="fas fa-envelope"></i> Email <span class="req">*</span>
                            </label>
                            <input type="email" 
                                   class="form-input-ci <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                                   id="email" 
                                   name="email" 
                                   value="<?= old('email', $student['email'] ?? '') ?>"
                                   required
                                   maxlength="100"
                                   placeholder="exemplo@email.com">
                            <?php if (session('errors.email')): ?>
                                <div class="form-error"><?= session('errors.email') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label-ci" for="birth_date">
                                <i class="fas fa-calendar"></i> Data Nascimento <span class="req">*</span>
                            </label>
                            <input type="date" 
                                   class="form-input-ci <?= session('errors.birth_date') ? 'is-invalid' : '' ?>" 
                                   id="birth_date" 
                                   name="birth_date" 
                                   value="<?= old('birth_date', $student['birth_date'] ?? '') ?>"
                                   required
                                   max="<?= date('Y-m-d', strtotime('-5 years')) ?>">
                            <?php if (session('errors.birth_date')): ?>
                                <div class="form-error"><?= session('errors.birth_date') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label-ci" for="gender">
                                <i class="fas fa-venus-mars"></i> Gênero <span class="req">*</span>
                            </label>
                            <select class="form-select-ci <?= session('errors.gender') ? 'is-invalid' : '' ?>" 
                                    id="gender" 
                                    name="gender" 
                                    required>
                                <option value="">Selecione...</option>
                                <option value="Masculino" <?= old('gender', $student['gender'] ?? '') == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                                <option value="Feminino" <?= old('gender', $student['gender'] ?? '') == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                            </select>
                            <?php if (session('errors.gender')): ?>
                                <div class="form-error"><?= session('errors.gender') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label-ci" for="nationality">
                                <i class="fas fa-flag"></i> Nacionalidade
                            </label>
                            <input type="text" 
                                   class="form-input-ci" 
                                   id="nationality" 
                                   name="nationality" 
                                   value="<?= old('nationality', $student['nationality'] ?? 'Angolana') ?>"
                                   maxlength="100"
                                   placeholder="Ex: Angolana">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label-ci" for="phone">
                                <i class="fas fa-phone-alt"></i> Telefone
                            </label>
                            <input type="text" 
                                   class="form-input-ci" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?= old('phone', $student['phone'] ?? '') ?>"
                                   placeholder="+244 000 000 000"
                                   maxlength="20">
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i> Formato: +244 000 000 000
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Documentos -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-id-card"></i>
                    <span>Documentos</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label-ci" for="identity_type">
                                <i class="fas fa-id-card"></i> Tipo Documento
                            </label>
                            <select class="form-select-ci" id="identity_type" name="identity_type">
                                <option value="BI" <?= old('identity_type', $student['identity_type'] ?? '') == 'BI' ? 'selected' : '' ?>>BI</option>
                                <option value="Passaporte" <?= old('identity_type', $student['identity_type'] ?? '') == 'Passaporte' ? 'selected' : '' ?>>Passaporte</option>
                                <option value="Cédula" <?= old('identity_type', $student['identity_type'] ?? '') == 'Cédula' ? 'selected' : '' ?>>Cédula</option>
                                <option value="Outro" <?= old('identity_type', $student['identity_type'] ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label-ci" for="identity_document">
                                <i class="fas fa-hashtag"></i> Nº Documento
                            </label>
                            <input type="text" 
                                   class="form-input-ci" 
                                   id="identity_document" 
                                   name="identity_document" 
                                   value="<?= old('identity_document', $student['identity_document'] ?? '') ?>"
                                   maxlength="50"
                                   placeholder="Número do documento">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label-ci" for="nif">
                                <i class="fas fa-barcode"></i> NIF
                            </label>
                            <input type="text" 
                                   class="form-input-ci" 
                                   id="nif" 
                                   name="nif" 
                                   value="<?= old('nif', $student['nif'] ?? '') ?>"
                                   maxlength="20"
                                   placeholder="Número de identificação fiscal">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label-ci" for="blood_type">
                                <i class="fas fa-tint"></i> Tipo Sanguíneo
                            </label>
                            <select class="form-select-ci" id="blood_type" name="blood_type">
                                <option value="">Selecione...</option>
                                <option value="A+" <?= old('blood_type', $student['blood_type'] ?? '') == 'A+' ? 'selected' : '' ?>>A+</option>
                                <option value="A-" <?= old('blood_type', $student['blood_type'] ?? '') == 'A-' ? 'selected' : '' ?>>A-</option>
                                <option value="B+" <?= old('blood_type', $student['blood_type'] ?? '') == 'B+' ? 'selected' : '' ?>>B+</option>
                                <option value="B-" <?= old('blood_type', $student['blood_type'] ?? '') == 'B-' ? 'selected' : '' ?>>B-</option>
                                <option value="AB+" <?= old('blood_type', $student['blood_type'] ?? '') == 'AB+' ? 'selected' : '' ?>>AB+</option>
                                <option value="AB-" <?= old('blood_type', $student['blood_type'] ?? '') == 'AB-' ? 'selected' : '' ?>>AB-</option>
                                <option value="O+" <?= old('blood_type', $student['blood_type'] ?? '') == 'O+' ? 'selected' : '' ?>>O+</option>
                                <option value="O-" <?= old('blood_type', $student['blood_type'] ?? '') == 'O-' ? 'selected' : '' ?>>O-</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Endereço -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Endereço</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label-ci" for="province">
                                <i class="fas fa-map"></i> Província
                            </label>
                            <input type="text" 
                                   class="form-input-ci" 
                                   id="province" 
                                   name="province" 
                                   value="<?= old('province', $student['province'] ?? '') ?>"
                                   maxlength="100"
                                   placeholder="Ex: Luanda">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label-ci" for="municipality">
                                <i class="fas fa-map-pin"></i> Município
                            </label>
                            <input type="text" 
                                   class="form-input-ci" 
                                   id="municipality" 
                                   name="municipality" 
                                   value="<?= old('municipality', $student['municipality'] ?? '') ?>"
                                   maxlength="100"
                                   placeholder="Ex: Luanda">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label-ci" for="city">
                                <i class="fas fa-city"></i> Cidade
                            </label>
                            <input type="text" 
                                   class="form-input-ci" 
                                   id="city" 
                                   name="city" 
                                   value="<?= old('city', $student['city'] ?? '') ?>"
                                   maxlength="100"
                                   placeholder="Ex: Luanda">
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label-ci" for="address">
                                <i class="fas fa-home"></i> Endereço Completo
                            </label>
                            <textarea class="form-input-ci" id="address" name="address" rows="2" maxlength="500" 
                                      placeholder="Rua, número, bairro, etc."><?= old('address', $student['address'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Dados da Pré-Matrícula -->
            <div class="prereg-card">
                <div class="prereg-card-header">
                    <i class="fas fa-graduation-cap"></i>
                    <h5>Dados da Pré-Matrícula</h5>
                    <div class="form-hint ms-auto">
                        <i class="fas fa-info-circle"></i> Estes dados serão usados para criar a matrícula pendente
                    </div>
                </div>
                <div class="prereg-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-ci" for="academic_year_id">
                                    <i class="fas fa-calendar-alt"></i> Ano Letivo <span class="req">*</span>
                                </label>
                                <select class="form-select-ci <?= session('errors.academic_year_id') ? 'is-invalid' : '' ?>" 
                                        id="academic_year_id" 
                                        name="academic_year_id" 
                                        required>
                                    <option value="">Selecione o ano letivo...</option>
                                    <?php if (!empty($academicYears)): ?>
                                        <?php foreach ($academicYears as $year): ?>
                                            <option value="<?= $year['id'] ?>" 
                                                <?= (old('academic_year_id' , $selectedYear ?? $currentYear['id'] ?? '') == $year['id']) ? 'selected' : '' ?>>
                                                <?= $year['year_name'] ?> <?= $year['id'] == current_academic_year() ? '(Atual)' : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if (session('errors.academic_year_id')): ?>
                                    <div class="form-error"><?= session('errors.academic_year_id') ?></div>
                                <?php endif; ?>
                                <div class="form-hint">Ano letivo em que o aluno pretende se matricular</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-ci" for="grade_level_id">
                                    <i class="fas fa-layer-group"></i> Nível/Classe Pretendida <span class="req">*</span>
                                </label>
                                <select class="form-select-ci <?= session('errors.grade_level_id') ? 'is-invalid' : '' ?>" 
                                        id="grade_level_id" 
                                        name="grade_level_id" 
                                        required>
                                    <option value="">Selecione o nível/classe...</option>
                                    <?php if (!empty($gradeLevels)): ?>
                                        <?php foreach ($gradeLevels as $level): ?>
                                            <option value="<?= $level->id ?>" 
                                                <?= (old('grade_level_id', $selectedLevel ?? '') == $level->id) ? 'selected' : '' ?>>
                                                <?= $level->level_name ?> (<?= $level->education_level ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if (session('errors.grade_level_id')): ?>
                                    <div class="form-error"><?= session('errors.grade_level_id') ?></div>
                                <?php endif; ?>
                                <div class="form-hint">Nível de ensino pretendido pelo aluno</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-ci" for="enrollment_type">
                                    <i class="fas fa-file-signature"></i> Tipo de Matrícula <span class="req">*</span>
                                </label>
                                <select class="form-select-ci <?= session('errors.enrollment_type') ? 'is-invalid' : '' ?>" 
                                        id="enrollment_type" 
                                        name="enrollment_type" 
                                        required>
                                    <option value="">Selecione...</option>
                                    <option value="Nova" <?= old('enrollment_type') == 'Nova' ? 'selected' : '' ?>>Nova Matrícula</option>
                                    <option value="Renovação" <?= old('enrollment_type') == 'Renovação' ? 'selected' : '' ?>>Renovação</option>
                                    <option value="Transferência" <?= old('enrollment_type') == 'Transferência' ? 'selected' : '' ?>>Transferência</option>
                                </select>
                                <?php if (session('errors.enrollment_type')): ?>
                                    <div class="form-error"><?= session('errors.enrollment_type') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-ci" for="previous_grade_id">
                                    <i class="fas fa-history"></i> Última Classe Concluída
                                </label>
                                <select class="form-select-ci" id="previous_grade_id" name="previous_grade_id">
                                    <option value="">Selecione...</option>
                                    <?php if (!empty($gradeLevels)): ?>
                                        <?php foreach ($gradeLevels as $level): ?>
                                            <option value="<?= $level->id ?>" 
                                                <?= (old('previous_grade_id', $student['previous_grade_id'] ?? '') == $level->id) ? 'selected' : '' ?>>
                                                <?= $level->level_name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="form-hint">Classe que o aluno concluiu anteriormente</div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label-ci" for="course_id">
                                    <i class="fas fa-graduation-cap"></i> Curso
                                </label>
                                <select class="form-select-ci <?= session('errors.course_id') ? 'is-invalid' : '' ?>" 
                                        id="course_id" 
                                        name="course_id">
                                    <option value="">Selecione o curso (apenas para Ensino Médio)...</option>
                                    <?php if (!empty($courses)): ?>
                                        <?php foreach ($courses as $course): ?>
                                            <option value="<?= $course->id ?>" 
                                                <?= (old('course_id', $selectedCourse ?? '') == $course->id) ? 'selected' : '' ?>>
                                                <?= esc($course['course_name']) ?> (<?= esc($course->course_code) ?>) - <?= esc($course->course_type) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if (session('errors.course_id')): ?>
                                    <div class="form-error"><?= session('errors.course_id') ?></div>
                                <?php endif; ?>
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> 
                                    Selecione o curso apenas para alunos do Ensino Médio (10ª-12ª classes)
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert-ci info mt-3">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Nota:</strong> Após salvar o aluno, será criada uma matrícula com status "Pendente". 
                        Você poderá concluir a matrícula posteriormente, atribuindo uma turma específica.
                    </div>
                </div>
            </div>
            
            <!-- Histórico Escolar -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-school"></i>
                    <span>Histórico Escolar</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-ci" for="previous_school">
                                <i class="fas fa-school"></i> Escola Anterior
                            </label>
                            <input type="text" 
                                   class="form-input-ci" 
                                   id="previous_school" 
                                   name="previous_school" 
                                   value="<?= old('previous_school', $student['previous_school'] ?? '') ?>"
                                   maxlength="255"
                                   placeholder="Nome da escola anterior">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-ci" for="student_number">
                                <i class="fas fa-hashtag"></i> Número de Matrícula
                            </label>
                            <div class="readonly-field">
                                <?= $student ? $student['student_number'] : '(gerado automaticamente)' ?>
                            </div>
                            <div class="form-hint">Gerado automaticamente pelo sistema</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contacto de Emergência -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-phone-alt"></i>
                    <span>Contacto de Emergência</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-ci" for="emergency_contact_name">
                                <i class="fas fa-user"></i> Nome do Contacto
                            </label>
                            <input type="text" 
                                   class="form-input-ci" 
                                   id="emergency_contact_name" 
                                   name="emergency_contact_name" 
                                   value="<?= old('emergency_contact_name', $student['emergency_contact_name'] ?? '') ?>"
                                   maxlength="255"
                                   placeholder="Nome completo">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-ci" for="emergency_contact">
                                <i class="fas fa-phone"></i> Telefone de Emergência
                            </label>
                            <input type="text" 
                                   class="form-input-ci" 
                                   id="emergency_contact" 
                                   name="emergency_contact" 
                                   value="<?= old('emergency_contact', $student['emergency_contact'] ?? '') ?>"
                                   placeholder="+244 000 000 000"
                                   maxlength="20">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Informações de Saúde -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-heartbeat"></i>
                    <span>Informações de Saúde</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label-ci" for="health_conditions">
                                <i class="fas fa-notes-medical"></i> Condições de Saúde
                            </label>
                            <textarea class="form-input-ci" id="health_conditions" name="health_conditions" rows="2" maxlength="500" 
                                      placeholder="Alergias, doenças crônicas, etc."><?= old('health_conditions', $student['health_conditions'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label-ci" for="special_needs">
                                <i class="fas fa-wheelchair"></i> Necessidades Especiais
                            </label>
                            <textarea class="form-input-ci" id="special_needs" name="special_needs" rows="2" maxlength="500"><?= old('special_needs', $student['special_needs'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Foto do Aluno -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-camera"></i>
                    <span>Foto do Aluno</span>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <?php if ($student && !empty($student['photo'])): ?>
                            <img src="<?= base_url('uploads/students/' . $student['photo']) ?>" 
                                 alt="Foto do Aluno" 
                                 class="photo-preview" 
                                 id="photoPreview">
                        <?php else: ?>
                            <img src="" alt="Preview da Foto" class="photo-preview" id="photoPreview" style="display: none;">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label class="form-label-ci" for="photo">
                                <i class="fas fa-camera"></i> Foto
                            </label>
                            <input type="file" class="form-input-ci" id="photo" name="photo" accept="image/*">
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i> 
                                Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Status e Data de Registro -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-cog"></i>
                    <span>Configurações</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-ci" for="registration_date">
                                <i class="fas fa-calendar"></i> Data de Registro
                            </label>
                            <input type="date" 
                                   class="form-input-ci" 
                                   id="registration_date" 
                                   name="registration_date" 
                                   value="<?= old('registration_date', $student['registration_date'] ?? date('Y-m-d')) ?>"
                                   <?= $student ? 'readonly' : '' ?>>
                            <div class="form-hint">Data em que o aluno foi registrado no sistema</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="switch-wrapper">
                            <div class="ci-switch">
                                <input type="checkbox" id="is_active" name="is_active" value="1"
                                       <?= (old('is_active', $student['is_active'] ?? true)) ? 'checked' : '' ?>>
                                <label class="ci-switch-track" for="is_active"></label>
                            </div>
                            <label class="form-label-ci mb-0" for="is_active">Aluno Ativo no Sistema</label>
                        </div>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i> 
                            Alunos inativos não podem ser matriculados
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Registration Status (hidden) -->
            <?php if (!$student): ?>
                <input type="hidden" name="registration_status" value="Registrado">
            <?php endif; ?>
            
            <hr class="my-4">
            
            <!-- Form Footer -->
            <div class="form-footer">
                <a href="<?= route_to('students.index') ?>" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> <?= $student ? 'Salvar Alterações' : 'Salvar e Criar Pré-Matrícula' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Configuração CSRF para garantir que o token seja enviado
const csrfToken = '<?= csrf_token() ?>';
const csrfHash = '<?= csrf_hash() ?>';

// Garantir que o CSRF seja incluído mesmo com upload de arquivos
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    // Verificar se o token CSRF está presente
    const csrfField = document.querySelector('input[name="<?= csrf_token() ?>"]');
    if (csrfField) {
        console.log('CSRF token encontrado');
    } else {
        console.error('CSRF token NÃO encontrado no formulário!');
        const newCsrfField = document.createElement('input');
        newCsrfField.type = 'hidden';
        newCsrfField.name = '<?= csrf_token() ?>';
        newCsrfField.value = csrfHash;
        form.appendChild(newCsrfField);
    }
    
    // Inicializar preview de foto
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    
    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('A foto não pode ter mais de 2MB');
                    this.value = '';
                    return;
                }
                
                if (!file.type.match('image.*')) {
                    alert('Por favor, selecione uma imagem válida');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                    photoPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

// Validação de idade mínima
document.getElementById('birth_date').addEventListener('change', function() {
    const birthDate = new Date(this.value);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    if (age < 5) {
        alert('O aluno deve ter pelo menos 5 anos de idade.');
        this.value = '';
    }
});

// Validação de telefone (formato angolano)
document.getElementById('phone').addEventListener('blur', function() {
    const phone = this.value;
    if (phone && !phone.match(/^\+?244?\d{9}$/)) {
        console.log('Formato de telefone sugerido: +244 000 000 000');
    }
});

// Mostrar/esconder campo curso baseado no nível selecionado
document.getElementById('grade_level_id').addEventListener('change', function() {
    const gradeLevelId = this.value;
    // Aqui você pode fazer uma chamada AJAX para verificar se o nível é Ensino Médio
});

// Switch styling initialization
$(document).ready(function() {
    $('.ci-switch-track').each(function() {
        const input = $(this).prev('input');
        if (input.is(':checked')) {
            $(this).addClass('checked');
        }
    });
    
    $('.ci-switch input').change(function() {
        $(this).next('.ci-switch-track').toggleClass('checked', $(this).is(':checked'));
    });
});
</script>
<?= $this->endSection() ?>