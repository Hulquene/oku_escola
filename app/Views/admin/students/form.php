<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students') ?>">Alunos</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-<?= $student ? 'edit' : 'user-plus' ?>"></i> <?= $title ?>
        <?php if (!$student): ?>
            <span class="badge bg-info ms-2">Passo 1 de 2 - Cadastro do Aluno</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/students/save') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <?php if ($student): ?>
                <input type="hidden" name="id" value="<?= $student->id ?>">
                <input type="hidden" name="user_id" value="<?= $student->user_id ?>">
            <?php endif; ?>
            
            <!-- Campos Obrigatórios em Destaque -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Campos marcados com <span class="text-danger">*</span> são obrigatórios.
            </div>
            
            <!-- Informações Pessoais -->
            <h5 class="mb-3">Informações Pessoais</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= session('errors.first_name') ? 'is-invalid' : '' ?>" 
                               id="first_name" 
                               name="first_name" 
                               value="<?= old('first_name', $student->first_name ?? '') ?>"
                               required
                               maxlength="50">
                        <?php if (session('errors.first_name')): ?>
                            <div class="invalid-feedback"><?= session('errors.first_name') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Sobrenome <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= session('errors.last_name') ? 'is-invalid' : '' ?>" 
                               id="last_name" 
                               name="last_name" 
                               value="<?= old('last_name', $student->last_name ?? '') ?>"
                               required
                               maxlength="50">
                        <?php if (session('errors.last_name')): ?>
                            <div class="invalid-feedback"><?= session('errors.last_name') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" 
                               class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                               id="email" 
                               name="email" 
                               value="<?= old('email', $student->email ?? '') ?>"
                               required
                               maxlength="100">
                        <?php if (session('errors.email')): ?>
                            <div class="invalid-feedback"><?= session('errors.email') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="birth_date" class="form-label">Data de Nascimento <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control <?= session('errors.birth_date') ? 'is-invalid' : '' ?>" 
                               id="birth_date" 
                               name="birth_date" 
                               value="<?= old('birth_date', $student->birth_date ?? '') ?>"
                               required
                               max="<?= date('Y-m-d', strtotime('-5 years')) ?>">
                        <?php if (session('errors.birth_date')): ?>
                            <div class="invalid-feedback"><?= session('errors.birth_date') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gênero <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.gender') ? 'is-invalid' : '' ?>" 
                                id="gender" 
                                name="gender" 
                                required>
                            <option value="">Selecione...</option>
                            <option value="Masculino" <?= old('gender', $student->gender ?? '') == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                            <option value="Feminino" <?= old('gender', $student->gender ?? '') == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                        </select>
                        <?php if (session('errors.gender')): ?>
                            <div class="invalid-feedback"><?= session('errors.gender') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="nationality" class="form-label">Nacionalidade</label>
                        <input type="text" 
                               class="form-control" 
                               id="nationality" 
                               name="nationality" 
                               value="<?= old('nationality', $student->nationality ?? 'Angolana') ?>"
                               maxlength="100">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefone</label>
                        <input type="text" 
                               class="form-control" 
                               id="phone" 
                               name="phone" 
                               value="<?= old('phone', $student->phone ?? '') ?>"
                               placeholder="+244 000 000 000"
                               maxlength="20">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="identity_type" class="form-label">Tipo de Documento</label>
                        <select class="form-select" id="identity_type" name="identity_type">
                            <option value="BI" <?= old('identity_type', $student->identity_type ?? '') == 'BI' ? 'selected' : '' ?>>BI</option>
                            <option value="Passaporte" <?= old('identity_type', $student->identity_type ?? '') == 'Passaporte' ? 'selected' : '' ?>>Passaporte</option>
                            <option value="Cédula" <?= old('identity_type', $student->identity_type ?? '') == 'Cédula' ? 'selected' : '' ?>>Cédula</option>
                            <option value="Outro" <?= old('identity_type', $student->identity_type ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="identity_document" class="form-label">Nº do Documento</label>
                        <input type="text" 
                               class="form-control" 
                               id="identity_document" 
                               name="identity_document" 
                               value="<?= old('identity_document', $student->identity_document ?? '') ?>"
                               maxlength="50">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="nif" class="form-label">NIF</label>
                        <input type="text" 
                               class="form-control" 
                               id="nif" 
                               name="nif" 
                               value="<?= old('nif', $student->nif ?? '') ?>"
                               maxlength="20">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="blood_type" class="form-label">Tipo Sanguíneo</label>
                        <select class="form-select" id="blood_type" name="blood_type">
                            <option value="">Selecione...</option>
                            <option value="A+" <?= old('blood_type', $student->blood_type ?? '') == 'A+' ? 'selected' : '' ?>>A+</option>
                            <option value="A-" <?= old('blood_type', $student->blood_type ?? '') == 'A-' ? 'selected' : '' ?>>A-</option>
                            <option value="B+" <?= old('blood_type', $student->blood_type ?? '') == 'B+' ? 'selected' : '' ?>>B+</option>
                            <option value="B-" <?= old('blood_type', $student->blood_type ?? '') == 'B-' ? 'selected' : '' ?>>B-</option>
                            <option value="AB+" <?= old('blood_type', $student->blood_type ?? '') == 'AB+' ? 'selected' : '' ?>>AB+</option>
                            <option value="AB-" <?= old('blood_type', $student->blood_type ?? '') == 'AB-' ? 'selected' : '' ?>>AB-</option>
                            <option value="O+" <?= old('blood_type', $student->blood_type ?? '') == 'O+' ? 'selected' : '' ?>>O+</option>
                            <option value="O-" <?= old('blood_type', $student->blood_type ?? '') == 'O-' ? 'selected' : '' ?>>O-</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Endereço -->
            <h5 class="mb-3 mt-4">Endereço</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="province" class="form-label">Província</label>
                        <input type="text" 
                               class="form-control" 
                               id="province" 
                               name="province" 
                               value="<?= old('province', $student->province ?? '') ?>"
                               maxlength="100">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="municipality" class="form-label">Município</label>
                        <input type="text" 
                               class="form-control" 
                               id="municipality" 
                               name="municipality" 
                               value="<?= old('municipality', $student->municipality ?? '') ?>"
                               maxlength="100">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="city" class="form-label">Cidade</label>
                        <input type="text" 
                               class="form-control" 
                               id="city" 
                               name="city" 
                               value="<?= old('city', $student->city ?? '') ?>"
                               maxlength="100">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Endereço Completo</label>
                <textarea class="form-control" id="address" name="address" rows="2" maxlength="500"><?= old('address', $student->address ?? '') ?></textarea>
            </div>
            
            <!-- Dados da Pré-Matrícula -->
            <div class="card mt-4 mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-graduation-cap"></i> Dados da Pré-Matrícula
                    <small class="text-white-50 ms-2">(Estes dados serão usados para criar a matrícula pendente)</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="academic_year_id" class="form-label">Ano Letivo <span class="text-danger">*</span></label>
                                <select class="form-select <?= session('errors.academic_year_id') ? 'is-invalid' : '' ?>" 
                                        id="academic_year_id" 
                                        name="academic_year_id" 
                                        required>
                                    <option value="">Selecione o ano letivo...</option>
                                    <?php if (!empty($academicYears)): ?>
                                        <?php foreach ($academicYears as $year): ?>
                                            <option value="<?= $year->id ?>" 
                                                <?= (old('academic_year_id', $selectedYear ?? $currentYear->id ?? '') == $year->id) ? 'selected' : '' ?>>
                                                <?= $year->year_name ?> <?= $year->is_current ? '(Atual)' : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if (session('errors.academic_year_id')): ?>
                                    <div class="invalid-feedback"><?= session('errors.academic_year_id') ?></div>
                                <?php endif; ?>
                                <small class="text-muted">Ano letivo em que o aluno pretende se matricular</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="grade_level_id" class="form-label">Nível/Classe Pretendida <span class="text-danger">*</span></label>
                                <select class="form-select <?= session('errors.grade_level_id') ? 'is-invalid' : '' ?>" 
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
                                    <div class="invalid-feedback"><?= session('errors.grade_level_id') ?></div>
                                <?php endif; ?>
                                <small class="text-muted">Nível de ensino pretendido pelo aluno</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="enrollment_type" class="form-label">Tipo de Matrícula <span class="text-danger">*</span></label>
                                <select class="form-select <?= session('errors.enrollment_type') ? 'is-invalid' : '' ?>" 
                                        id="enrollment_type" 
                                        name="enrollment_type" 
                                        required>
                                    <option value="">Selecione...</option>
                                    <option value="Nova" <?= old('enrollment_type') == 'Nova' ? 'selected' : '' ?>>Nova Matrícula</option>
                                    <option value="Renovação" <?= old('enrollment_type') == 'Renovação' ? 'selected' : '' ?>>Renovação</option>
                                    <option value="Transferência" <?= old('enrollment_type') == 'Transferência' ? 'selected' : '' ?>>Transferência</option>
                                </select>
                                <?php if (session('errors.enrollment_type')): ?>
                                    <div class="invalid-feedback"><?= session('errors.enrollment_type') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="previous_grade_id" class="form-label">Última Classe Concluída</label>
                                <select class="form-select" id="previous_grade_id" name="previous_grade_id">
                                    <option value="">Selecione...</option>
                                    <?php if (!empty($gradeLevels)): ?>
                                        <?php foreach ($gradeLevels as $level): ?>
                                            <option value="<?= $level->id ?>" 
                                                <?= (old('previous_grade_id', $student->previous_grade_id ?? '') == $level->id) ? 'selected' : '' ?>>
                                                <?= $level->level_name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted">Classe que o aluno concluiu anteriormente</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- NOVO CAMPO CURSO -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="course_id" class="form-label">Curso <span class="text-muted">(opcional)</span></label>
                                <select class="form-select <?= session('errors.course_id') ? 'is-invalid' : '' ?>" 
                                        id="course_id" 
                                        name="course_id">
                                    <option value="">Selecione o curso (apenas para Ensino Médio)...</option>
                                    <?php if (!empty($courses)): ?>
                                        <?php foreach ($courses as $course): ?>
                                            <option value="<?= $course->id ?>" 
                                                <?= (old('course_id', $selectedCourse ?? '') == $course->id) ? 'selected' : '' ?>>
                                                <?= esc($course->course_name) ?> (<?= esc($course->course_code) ?>) - <?= esc($course->course_type) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if (session('errors.course_id')): ?>
                                    <div class="invalid-feedback"><?= session('errors.course_id') ?></div>
                                <?php endif; ?>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Selecione o curso apenas para alunos do Ensino Médio (10ª-12ª classes)
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Nota:</strong> Após salvar o aluno, será criada uma matrícula com status "Pendente". 
                        Você poderá concluir a matrícula posteriormente, atribuindo uma turma específica.
                    </div>
                </div>
            </div>
            
            <!-- Informações Escolares Anteriores -->
            <h5 class="mb-3 mt-4">Histórico Escolar</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="previous_school" class="form-label">Escola Anterior</label>
                        <input type="text" 
                               class="form-control" 
                               id="previous_school" 
                               name="previous_school" 
                               value="<?= old('previous_school', $student->previous_school ?? '') ?>"
                               maxlength="255">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="student_number" class="form-label">Número de Matrícula</label>
                        <input type="text" 
                               class="form-control" 
                               id="student_number" 
                               name="student_number" 
                               value="<?= old('student_number', $student->student_number ?? '(gerado automaticamente)') ?>"
                               <?= $student ? 'readonly' : 'disabled' ?>>
                        <small class="text-muted">Gerado automaticamente pelo sistema</small>
                    </div>
                </div>
            </div>
            
            <!-- Informações de Emergência -->
            <h5 class="mb-3 mt-4">Contacto de Emergência</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="emergency_contact_name" class="form-label">Nome do Contacto</label>
                        <input type="text" 
                               class="form-control" 
                               id="emergency_contact_name" 
                               name="emergency_contact_name" 
                               value="<?= old('emergency_contact_name', $student->emergency_contact_name ?? '') ?>"
                               maxlength="255">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="emergency_contact" class="form-label">Telefone de Emergência</label>
                        <input type="text" 
                               class="form-control" 
                               id="emergency_contact" 
                               name="emergency_contact" 
                               value="<?= old('emergency_contact', $student->emergency_contact ?? '') ?>"
                               placeholder="+244 000 000 000"
                               maxlength="20">
                    </div>
                </div>
            </div>
            
            <!-- Saúde -->
            <h5 class="mb-3 mt-4">Informações de Saúde</h5>
            <div class="mb-3">
                <label for="health_conditions" class="form-label">Condições de Saúde</label>
                <textarea class="form-control" id="health_conditions" name="health_conditions" rows="2" maxlength="500"><?= old('health_conditions', $student->health_conditions ?? '') ?></textarea>
                <small class="text-muted">Alergias, doenças crônicas, etc.</small>
            </div>
            
            <div class="mb-3">
                <label for="special_needs" class="form-label">Necessidades Especiais</label>
                <textarea class="form-control" id="special_needs" name="special_needs" rows="2" maxlength="500"><?= old('special_needs', $student->special_needs ?? '') ?></textarea>
            </div>
            
            <!-- Foto -->
            <h5 class="mb-3 mt-4">Foto do Aluno</h5>
            <div class="row">
                <div class="col-md-4">
                    <?php if ($student && $student->photo): ?>
                        <div class="mb-3">
                            <img src="<?= base_url('uploads/students/' . $student->photo) ?>" 
                                 alt="Foto do Aluno" 
                                 class="img-thumbnail" 
                                 style="max-width: 150px;">
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Foto</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <small class="text-muted">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</small>
                    </div>
                </div>
            </div>
            
            <!-- Status e Data de Registro -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="registration_date" class="form-label">Data de Registro</label>
                        <input type="date" 
                               class="form-control" 
                               id="registration_date" 
                               name="registration_date" 
                               value="<?= old('registration_date', $student->registration_date ?? date('Y-m-d')) ?>"
                               <?= $student ? 'readonly' : '' ?>>
                        <small class="text-muted">Data em que o aluno foi registrado no sistema</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   <?= (old('is_active', $student->is_active ?? true)) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Aluno Ativo no Sistema</label>
                        </div>
                        <small class="text-muted d-block">Alunos inativos não podem ser matriculados</small>
                    </div>
                </div>
            </div>
            
            <!-- Registration Status (hidden) -->
            <?php if (!$student): ?>
                <input type="hidden" name="registration_status" value="Registrado">
            <?php endif; ?>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/students') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-primary">
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
        console.log('CSRF token encontrado:', csrfField.value.substring(0, 10) + '...');
    } else {
        console.error('CSRF token NÃO encontrado no formulário!');
        const newCsrfField = document.createElement('input');
        newCsrfField.type = 'hidden';
        newCsrfField.name = '<?= csrf_token() ?>';
        newCsrfField.value = csrfHash;
        form.appendChild(newCsrfField);
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

// Preview da foto antes de upload
document.getElementById('photo').addEventListener('change', function(e) {
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
            const preview = document.querySelector('.photo-preview');
            if (preview) {
                preview.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    }
});

// Mostrar/esconder campo curso baseado no nível selecionado
document.getElementById('grade_level_id').addEventListener('change', function() {
    const gradeLevelId = this.value;
    const courseField = document.getElementById('course_id').closest('.row');
    
    if (gradeLevelId) {
        // Aqui você pode fazer uma chamada AJAX para verificar se o nível é Ensino Médio
        // Por enquanto, vamos apenas mostrar o campo
        courseField.style.display = 'block';
    }
});

// Adicionar preview de foto
const photoInput = document.getElementById('photo');
if (photoInput) {
    const previewDiv = document.createElement('div');
    previewDiv.className = 'mt-2';
    previewDiv.innerHTML = '<img class="photo-preview img-thumbnail" style="max-width: 150px; display: none;">';
    photoInput.parentNode.appendChild(previewDiv);
}
</script>
<?= $this->endSection() ?>