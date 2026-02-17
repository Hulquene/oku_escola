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

<!-- Form -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-<?= $student ? 'edit' : 'user-plus' ?>"></i> <?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/students/save') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <?php if ($student): ?>
                <input type="hidden" name="id" value="<?= $student->id ?>">
                <input type="hidden" name="user_id" value="<?= $student->user_id ?>">
            <?php endif; ?>
            
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
                               required>
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
                               required>
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
                               required>
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
                               required>
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
                               value="<?= old('nationality', $student->nationality ?? 'Angolana') ?>">
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
                               placeholder="+244 000 000 000">
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
                               value="<?= old('identity_document', $student->identity_document ?? '') ?>">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="nif" class="form-label">NIF</label>
                        <input type="text" 
                               class="form-control" 
                               id="nif" 
                               name="nif" 
                               value="<?= old('nif', $student->nif ?? '') ?>">
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
                               value="<?= old('province', $student->province ?? '') ?>">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="municipality" class="form-label">Município</label>
                        <input type="text" 
                               class="form-control" 
                               id="municipality" 
                               name="municipality" 
                               value="<?= old('municipality', $student->municipality ?? '') ?>">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="city" class="form-label">Cidade</label>
                        <input type="text" 
                               class="form-control" 
                               id="city" 
                               name="city" 
                               value="<?= old('city', $student->city ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Endereço Completo</label>
                <textarea class="form-control" id="address" name="address" rows="2"><?= old('address', $student->address ?? '') ?></textarea>
            </div>
            
            <!-- Informações Escolares -->
            <h5 class="mb-3 mt-4">Informações Escolares</h5>
            <div class="row">
                <div class="col-md-4">
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
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="class_id" class="form-label">Turma (Matrícula Atual)</label>
                        <select class="form-select" id="class_id" name="class_id">
                            <option value="">Selecione...</option>
                            <?php if (!empty($classes)): ?>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class->id ?>">
                                        <?= $class->class_name ?> (<?= $class->class_code ?>) - <?= $class->class_shift ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Selecione para matricular o aluno na turma</small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="previous_school" class="form-label">Escola Anterior</label>
                        <input type="text" 
                               class="form-control" 
                               id="previous_school" 
                               name="previous_school" 
                               value="<?= old('previous_school', $student->previous_school ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="previous_grade" class="form-label">Última Classe Concluída</label>
                        <input type="text" 
                               class="form-control" 
                               id="previous_grade" 
                               name="previous_grade" 
                               value="<?= old('previous_grade', $student->previous_grade ?? '') ?>">
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
                               value="<?= old('emergency_contact_name', $student->emergency_contact_name ?? '') ?>">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="emergency_contact" class="form-label">Telefone de Emergência</label>
                        <input type="text" 
                               class="form-control" 
                               id="emergency_contact" 
                               name="emergency_contact" 
                               value="<?= old('emergency_contact', $student->emergency_contact ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <!-- Saúde -->
            <h5 class="mb-3 mt-4">Informações de Saúde</h5>
            <div class="mb-3">
                <label for="health_conditions" class="form-label">Condições de Saúde</label>
                <textarea class="form-control" id="health_conditions" name="health_conditions" rows="2"><?= old('health_conditions', $student->health_conditions ?? '') ?></textarea>
                <small class="text-muted">Alergias, doenças crônicas, etc.</small>
            </div>
            
            <div class="mb-3">
                <label for="special_needs" class="form-label">Necessidades Especiais</label>
                <textarea class="form-control" id="special_needs" name="special_needs" rows="2"><?= old('special_needs', $student->special_needs ?? '') ?></textarea>
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
                    </div>
                </div>
            </div>
            
            <!-- Status -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   <?= (old('is_active', $student->is_active ?? true)) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Aluno Ativo</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/students') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>