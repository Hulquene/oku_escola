<?= $this->extend('layouts/public_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="page-header bg-primary text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center" data-aos="fade-up">
                <h1 class="display-4 fw-bold">Contato</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="/" class="text-white">Início</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Contato</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Informações de Contato -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4" data-aos="fade-up">
                <div class="contact-info-card text-center p-4 bg-light rounded-4 h-100">
                    <div class="contact-icon mb-3">
                        <i class="fas fa-map-marker-alt fa-3x text-primary"></i>
                    </div>
                    <h4>Endereço</h4>
                    <p class="text-muted mb-0">Rua da Educação, 123</p>
                    <p class="text-muted">Luanda, Angola</p>
                </div>
            </div>
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="contact-info-card text-center p-4 bg-light rounded-4 h-100">
                    <div class="contact-icon mb-3">
                        <i class="fas fa-phone-alt fa-3x text-primary"></i>
                    </div>
                    <h4>Telefone</h4>
                    <p class="text-muted mb-0">+244 999 999 999</p>
                    <p class="text-muted">+244 999 999 998</p>
                </div>
            </div>
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="contact-info-card text-center p-4 bg-light rounded-4 h-100">
                    <div class="contact-icon mb-3">
                        <i class="fas fa-envelope fa-3x text-primary"></i>
                    </div>
                    <h4>Email</h4>
                    <p class="text-muted mb-0">info@escola.ao</p>
                    <p class="text-muted">secretaria@escola.ao</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Formulário de Contato e Mapa -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                <div class="contact-form p-4 bg-white rounded-4 shadow-sm">
                    <h3 class="mb-4">Envie sua Mensagem</h3>
                    
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
                            <ul class="mb-0">
                                <?php foreach(session()->getFlashdata('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form action="/contato/enviar" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Nome *</label>
                                    <input type="text" class="form-control form-control-lg" id="name" name="name" value="<?= old('name') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" value="<?= old('email') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone" class="form-label">Telefone *</label>
                                    <input type="tel" class="form-control form-control-lg" id="phone" name="phone" value="<?= old('phone') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject" class="form-label">Assunto</label>
                                    <input type="text" class="form-control form-control-lg" id="subject" name="subject" value="<?= old('subject') ?>">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="message" class="form-label">Mensagem *</label>
                                    <textarea class="form-control form-control-lg" id="message" name="message" rows="5" required><?= old('message') ?></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar Mensagem
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-6" data-aos="fade-left">
                <div class="map-container h-100">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126115.271767999!2d13.199959!3d-8.838889!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1a51f3b8b7b7b7b7%3A0x7b7b7b7b7b7b7b7b!2sLuanda%2C%20Angola!5e0!3m2!1spt-PT!2s!4v1620000000000!5m2!1spt-PT!2s" 
                        width="100%" 
                        height="450" 
                        style="border:0; border-radius: 1rem;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Horário de Atendimento -->
<section class="py-5">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Horário de Atendimento</h2>
            <p>Estamos prontos para recebê-lo</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="schedule-card text-center p-4 bg-light rounded-4">
                    <i class="fas fa-calendar-alt fa-3x text-primary mb-3"></i>
                    <h4>Secretaria</h4>
                    <p class="mb-1">Segunda a Sexta: 07:30 - 18:00</p>
                    <p>Sábado: 08:00 - 12:00</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="schedule-card text-center p-4 bg-light rounded-4">
                    <i class="fas fa-clock fa-3x text-primary mb-3"></i>
                    <h4>Aulas - Manhã</h4>
                    <p class="mb-1">Entrada: 07:30</p>
                    <p>Saída: 12:30</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="schedule-card text-center p-4 bg-light rounded-4">
                    <i class="fas fa-clock fa-3x text-primary mb-3"></i>
                    <h4>Aulas - Tarde</h4>
                    <p class="mb-1">Entrada: 13:30</p>
                    <p>Saída: 18:00</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Rápido -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Perguntas Frequentes</h2>
            <p>Tire suas dúvidas rapidamente</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Como faço para matricular meu filho?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Para matricular seu filho, você pode preencher o formulário de inscrição online em nosso site ou comparecer à secretaria da escola com os documentos necessários (BI do aluno e do encarregado, certificado de nascimento, histórico escolar e foto 3x4).
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Quais são as formas de pagamento das propinas?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Aceitamos pagamentos em dinheiro, transferência bancária, depósito em conta e Multicaixa. As propinas podem ser pagas mensalmente ou no início de cada semestre.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                A escola oferece transporte escolar?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Sim, oferecemos serviço de transporte escolar com rotas para os principais bairros de Luanda. Para mais informações sobre rotas e valores, entre em contato com nossa secretaria.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Como posso entrar em contato com os professores?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Os pais podem agendar reuniões com os professores através da secretaria da escola ou utilizar nossa plataforma online para comunicação direta com o corpo docente.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .contact-info-card {
        transition: var(--transition);
    }
    .contact-info-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-hover) !important;
    }
    .schedule-card {
        transition: var(--transition);
    }
    .schedule-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-hover) !important;
    }
    .form-control-lg {
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid #dee2e6;
    }
    .form-control-lg:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(221, 72, 20, 0.25);
    }
    .accordion-button:not(.collapsed) {
        background-color: var(--primary-light);
        color: var(--primary-color);
    }
    .accordion-button:focus {
        box-shadow: none;
        border-color: var(--primary-color);
    }
</style>

<?= $this->endSection() ?>