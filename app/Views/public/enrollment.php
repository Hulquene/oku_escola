<?= $this->extend('layouts/public_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="page-header bg-primary text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center" data-aos="fade-up">
                <h1 class="display-4 fw-bold">Inscrição de Estudante</h1>
                <p class="lead">Preencha o formulário abaixo para iniciar seu processo de matrícula</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="/" class="text-white">Início</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Inscrição</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Formulário de Inscrição -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                
                <!-- Alertas -->
                <?php if(session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if(session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Por favor, corrija os seguintes erros:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach(session()->getFlashdata('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Info Card -->
                <div class="alert alert-info mb-4" data-aos="fade-up">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="alert-heading">Informações Importantes</h5>
                            <p class="mb-0">Campos marcados com <span class="text-danger">*</span> são obrigatórios. 
                            Após o envio, nossa equipe entrará em contato para dar continuidade ao processo de matrícula.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Form Card -->
                <div class="card shadow-lg border-0" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-pen me-2"></i>Formulário de Inscrição</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="/inscricao/submit" method="post" id="enrollmentForm">
                            <?= csrf_field() ?>
                            
                            <!-- Dados Pessoais -->
                            <h5 class="mb-3 text-primary">Dados Pessoais</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="full_name" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control form-control-lg <?= session('errors.full_name') ? 'is-invalid' : '' ?>" 
                                           id="full_name" 
                                           name="full_name" 
                                           value="<?= old('full_name') ?>"
                                           placeholder="Digite seu nome completo"
                                           required>
                                    <div class="invalid-feedback"><?= session('errors.full_name') ?? '' ?></div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="birth_date" class="form-label">Data de Nascimento <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control form-control-lg <?= session('errors.birth_date') ? 'is-invalid' : '' ?>" 
                                           id="birth_date" 
                                           name="birth_date" 
                                           value="<?= old('birth_date') ?>"
                                           max="<?= date('Y-m-d', strtotime('-5 years')) ?>"
                                           required>
                                    <div class="invalid-feedback"><?= session('errors.birth_date') ?? '' ?></div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="gender" class="form-label">Gênero <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg <?= session('errors.gender') ? 'is-invalid' : '' ?>" 
                                            id="gender" 
                                            name="gender" 
                                            required>
                                        <option value="">Selecione...</option>
                                        <option value="Masculino" <?= old('gender') == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                                        <option value="Feminino" <?= old('gender') == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                                    </select>
                                    <div class="invalid-feedback"><?= session('errors.gender') ?? '' ?></div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="identity_document" class="form-label">Nº do BI/Passaporte <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control form-control-lg <?= session('errors.identity_document') ? 'is-invalid' : '' ?>" 
                                           id="identity_document" 
                                           name="identity_document" 
                                           value="<?= old('identity_document') ?>"
                                           placeholder="Ex: 000000000LA000"
                                           required>
                                    <div class="invalid-feedback"><?= session('errors.identity_document') ?? '' ?></div>
                                </div>
                                
                                <div class="col-12">
                                    <label for="address" class="form-label">Endereço Completo <span class="text-danger">*</span></label>
                                    <textarea class="form-control form-control-lg <?= session('errors.address') ? 'is-invalid' : '' ?>" 
                                              id="address" 
                                              name="address" 
                                              rows="2"
                                              placeholder="Rua, bairro, município, província"
                                              required><?= old('address') ?></textarea>
                                    <div class="invalid-feedback"><?= session('errors.address') ?? '' ?></div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Telefone <span class="text-danger">*</span></label>
                                    <input type="tel" 
                                           class="form-control form-control-lg <?= session('errors.phone') ? 'is-invalid' : '' ?>" 
                                           id="phone" 
                                           name="phone" 
                                           value="<?= old('phone') ?>"
                                           placeholder="+244 000 000 000"
                                           required>
                                    <div class="invalid-feedback"><?= session('errors.phone') ?? '' ?></div>
                                    <small class="text-muted">Formato: +244 000 000 000</small>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control form-control-lg <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                                           id="email" 
                                           name="email" 
                                           value="<?= old('email') ?>"
                                           placeholder="seu@email.com"
                                           required>
                                    <div class="invalid-feedback"><?= session('errors.email') ?? '' ?></div>
                                </div>
                            </div>
                            
                            <!-- Informações Acadêmicas -->
                            <h5 class="mb-3 text-primary">Informações Acadêmicas</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="grade_level" class="form-label">Classe Pretendida <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg <?= session('errors.grade_level') ? 'is-invalid' : '' ?>" 
                                            id="grade_level" 
                                            name="grade_level" 
                                            required>
                                        <option value="">Selecione a classe...</option>
                                        <?php if(isset($gradeLevels) && !empty($gradeLevels)): ?>
                                            <?php foreach($gradeLevels as $level): ?>
                                                <option value="<?= $level['id'] ?>" <?= old('grade_level') == $level['id'] ? 'selected' : '' ?>>
                                                    <?= $level['level_name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="invalid-feedback"><?= session('errors.grade_level') ?? '' ?></div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="course" class="form-label">Curso (apenas para Ensino Médio)</label>
                                    <select class="form-select form-select-lg <?= session('errors.course') ? 'is-invalid' : '' ?>" 
                                            id="course" 
                                            name="course" 
                                            disabled>
                                        <option value="">Selecione o curso...</option>
                                        <?php if(isset($courses) && !empty($courses)): ?>
                                            <?php foreach($courses as $course): ?>
                                                <option value="<?= $course['id'] ?>" <?= old('course') == $course['id'] ? 'selected' : '' ?>>
                                                    <?= $course['course_name'] ?> (<?= $course['course_type'] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="invalid-feedback"><?= session('errors.course') ?? '' ?></div>
                                    <small class="text-muted">Selecione apenas se for Ensino Médio (10ª-12ª classes)</small>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="previous_school" class="form-label">Escola Anterior</label>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="previous_school" 
                                           name="previous_school" 
                                           value="<?= old('previous_school') ?>"
                                           placeholder="Nome da escola onde estudou anteriormente">
                                </div>
                            </div>
                            
                            <!-- Contato de Emergência -->
                            <h5 class="mb-3 text-primary">Contato de Emergência</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="emergency_name" class="form-label">Nome do Contato <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control form-control-lg <?= session('errors.emergency_name') ? 'is-invalid' : '' ?>" 
                                           id="emergency_name" 
                                           name="emergency_name" 
                                           value="<?= old('emergency_name') ?>"
                                           placeholder="Nome completo"
                                           required>
                                    <div class="invalid-feedback"><?= session('errors.emergency_name') ?? '' ?></div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="emergency_contact" class="form-label">Telefone de Emergência <span class="text-danger">*</span></label>
                                    <input type="tel" 
                                           class="form-control form-control-lg <?= session('errors.emergency_contact') ? 'is-invalid' : '' ?>" 
                                           id="emergency_contact" 
                                           name="emergency_contact" 
                                           value="<?= old('emergency_contact') ?>"
                                           placeholder="+244 000 000 000"
                                           required>
                                    <div class="invalid-feedback"><?= session('errors.emergency_contact') ?? '' ?></div>
                                </div>
                            </div>
                            
                            <!-- Termos e Condições -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input <?= session('errors.terms') ? 'is-invalid' : '' ?>" 
                                           type="checkbox" 
                                           id="terms" 
                                           name="terms" 
                                           value="1"
                                           <?= old('terms') ? 'checked' : '' ?>
                                           required>
                                    <label class="form-check-label" for="terms">
                                        Declaro que as informações fornecidas são verdadeiras e estou ciente das políticas da escola. <span class="text-danger">*</span>
                                    </label>
                                    <div class="invalid-feedback">Você deve aceitar os termos para continuar.</div>
                                </div>
                            </div>
                            
                            <!-- Botões -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar Inscrição
                                </button>
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-2"></i>Limpar Formulário
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Informações Adicionais -->
                <div class="row mt-5 g-4">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="text-center">
                            <div class="rounded-circle bg-primary-light p-3 d-inline-block mb-3">
                                <i class="fas fa-clock fa-2x text-primary"></i>
                            </div>
                            <h6>Processo Rápido</h6>
                            <p class="small text-muted">Resposta em até 48h úteis</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="text-center">
                            <div class="rounded-circle bg-primary-light p-3 d-inline-block mb-3">
                                <i class="fas fa-shield-alt fa-2x text-primary"></i>
                            </div>
                            <h6>Dados Seguros</h6>
                            <p class="small text-muted">Suas informações estão protegidas</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="text-center">
                            <div class="rounded-circle bg-primary-light p-3 d-inline-block mb-3">
                                <i class="fas fa-headset fa-2x text-primary"></i>
                            </div>
                            <h6>Suporte</h6>
                            <p class="small text-muted">Dúvidas? Ligue +244 999 999 999</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .bg-primary-light {
        background: rgba(221, 72, 20, 0.1);
    }
    .form-control-lg, .form-select-lg {
        border-radius: 0.5rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(221, 72, 20, 0.25);
    }
</style>

<script>
$(document).ready(function() {
    // Atualizar cursos baseado na classe selecionada
    $('#grade_level').change(function() {
        var gradeId = parseInt($(this).val());
        var $courseSelect = $('#course');
        
        // IDs das classes do Ensino Médio (10ª, 11ª, 12ª, 13ª)
        var highSchoolGrades = [13, 14, 15, 16]; // Ajuste conforme seus IDs
        
        if(highSchoolGrades.includes(gradeId)) {
            $courseSelect.prop('disabled', false);
            $courseSelect.prop('required', false); // Não obrigatório, mas disponível
        } else {
            $courseSelect.prop('disabled', true);
            $courseSelect.val('');
        }
    });
    
    // Validação do telefone angolano
    function formatPhoneNumber(input) {
        var phone = input.val().replace(/\D/g, '');
        
        // Formato: +244 XXX XXX XXX
        if (phone.length > 0) {
            if (phone.startsWith('244')) {
                phone = phone.substring(3);
            }
            if (phone.length > 9) {
                phone = phone.substring(0, 9);
            }
            
            if (phone.length > 0) {
                var formatted = '+244 ';
                if (phone.length > 3) {
                    formatted += phone.substring(0, 3) + ' ';
                    if (phone.length > 6) {
                        formatted += phone.substring(3, 6) + ' ';
                        formatted += phone.substring(6, 9);
                    } else {
                        formatted += phone.substring(3);
                    }
                } else {
                    formatted += phone;
                }
                input.val(formatted);
            }
        }
    }
    
    $('#phone, #emergency_contact').on('input', function() {
        formatPhoneNumber($(this));
    });
    
    // Validação de idade mínima
    $('#birth_date').change(function() {
        var birthDate = new Date($(this).val());
        var today = new Date();
        var age = today.getFullYear() - birthDate.getFullYear();
        var monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        if (age < 5) {
            alert('O aluno deve ter pelo menos 5 anos de idade.');
            $(this).val('');
        } else if (age > 25) {
            if (!confirm('A idade informada é superior a 25 anos. Deseja continuar?')) {
                $(this).val('');
            }
        }
    });
    
    // Validação de email em tempo real
    $('#email').on('blur', function() {
        var email = $(this).val();
        if (email && !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            $(this).addClass('is-invalid');
            if ($(this).next('.invalid-feedback').length === 0) {
                $(this).after('<div class="invalid-feedback">Email inválido</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
    
    // Confirmação antes de resetar
    $('button[type="reset"]').click(function(e) {
        e.preventDefault();
        if (confirm('Tem certeza que deseja limpar todos os campos?')) {
            $('#enrollmentForm')[0].reset();
            $('#course').prop('disabled', true);
        }
    });
    
    // Prevenir duplo envio
    $('#enrollmentForm').submit(function() {
        var $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true);
        $btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Enviando...');
        return true;
    });
});
</script>

<?= $this->endSection() ?>