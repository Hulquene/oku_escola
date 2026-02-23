<?= $this->extend('layouts/public_layout') ?>

<?= $this->section('content') ?>

<!-- Hero Section Moderno -->
<section class="hero-section">
    <div class="hero-particles"></div>
    <div class="container">
        <div class="row align-items-center min-vh-80">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="hero-content">
                    <span class="badge bg-light text-primary mb-3 px-3 py-2">
                        <i class="fas fa-star me-2"></i>Excelência em Educação
                    </span>
                    <h1 class="hero-title">Bem-vindo à Escola Angolana Modelo</h1>
                    <p class="hero-subtitle">Formando líderes e cidadãos preparados para os desafios do futuro com qualidade, inovação e compromisso social.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="/inscricao" class="btn btn-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Inscreva-se Já
                        </a>
                        <a href="/cursos" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-book me-2"></i>Conheça Nossos Cursos
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="hero-stats">
                        <div class="hero-stat-item">
                            <span class="hero-stat-number counter"><?= $schoolInfo['students'] ?? '1500' ?></span>
                            <span class="hero-stat-label">Alunos</span>
                        </div>
                        <div class="hero-stat-item">
                            <span class="hero-stat-number counter"><?= $schoolInfo['teachers'] ?? '80' ?></span>
                            <span class="hero-stat-label">Professores</span>
                        </div>
                        <div class="hero-stat-item">
                            <span class="hero-stat-number counter"><?= $schoolInfo['classes'] ?? '45' ?></span>
                            <span class="hero-stat-label">Turmas</span>
                        </div>
                        <div class="hero-stat-item">
                            <span class="hero-stat-number counter"><?= date('Y') - ($schoolInfo['founded'] ?? 2010) ?></span>
                            <span class="hero-stat-label">Anos</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                <div class="hero-image">
                    <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         alt="Estudantes" class="img-fluid rounded-4 shadow-lg">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Cards -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6" data-aos="fade-up">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="stat-number counter"><?= $schoolInfo['students'] ?? '1500' ?></h3>
                    <p class="stat-label">Alunos Matriculados</p>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3 class="stat-number counter"><?= $schoolInfo['teachers'] ?? '80' ?></h3>
                    <p class="stat-label">Professores</p>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <h3 class="stat-number counter"><?= $schoolInfo['classes'] ?? '45' ?></h3>
                    <p class="stat-label">Turmas</p>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 class="stat-number counter">95</h3>
                    <p class="stat-label">% Aprovações</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sobre a Escola -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         alt="Nossa Escola" class="img-fluid rounded-4 shadow-lg">
                    <div class="experience-badge">
                        <span class="years"><?= date('Y') - ($schoolInfo['founded'] ?? 2010) ?></span>
                        <span class="text">Anos de<br>Excelência</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <span class="badge bg-primary mb-3 px-3 py-2">Sobre Nós</span>
                <h2 class="display-5 fw-bold mb-4">Educação de Qualidade para um Futuro Brilhante</h2>
                <p class="lead text-muted mb-4"><?= $schoolInfo['mission'] ?? 'Formar cidadãos críticos, criativos e comprometidos com o desenvolvimento de Angola.' ?></p>
                
                <div class="row g-4 mt-4">
                    <div class="col-sm-6">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-primary fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Ensino de Qualidade</h5>
                                <p class="text-muted">Metodologia moderna e corpo docente qualificado</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-laptop text-primary fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Tecnologia e Inovação</h5>
                                <p class="text-muted">Laboratórios modernos e plataforma digital</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-heart text-primary fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Formação Integral</h5>
                                <p class="text-muted">Atividades extracurriculares e projetos sociais</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-globe text-primary fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Intercâmbio Cultural</h5>
                                <p class="text-muted">Parcerias internacionais e programas de intercâmbio</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <a href="/sobre" class="btn btn-primary mt-4">
                    Saiba Mais <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Cursos em Destaque -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Nossos Cursos</h2>
            <p>Conheça nossa grade curricular completa e encontre o curso ideal para você</p>
        </div>
        
        <div class="row">
            <?php if(isset($featuredCourses) && !empty($featuredCourses)): ?>
                <?php foreach($featuredCourses as $index => $course): ?>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                    <div class="course-card">
                        <div class="course-image">
                            <img src="https://images.unsplash.com/photo-1532012197267-da84d127e765?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                                 alt="<?= $course['course_name'] ?>">
                            <span class="course-category"><?= $course['course_type'] ?></span>
                        </div>
                        <div class="course-content">
                            <h5 class="course-title">
                                <a href="/cursos/<?= $course['id'] ?>"><?= $course['course_name'] ?></a>
                            </h5>
                            <p class="course-description">
                                <?= isset($course['description']) ? substr($course['description'], 0, 100) . '...' : 'Descrição em breve' ?>
                            </p>
                            <div class="course-meta">
                                <span class="course-meta-item">
                                    <i class="fas fa-clock"></i> <?= $course['duration_years'] ?> anos
                                </span>
                                <span class="course-meta-item">
                                    <i class="fas fa-layer-group"></i> Ensino Médio
                                </span>
                            </div>
                            <div class="course-footer">
                                <span class="course-price">Grátis</span>
                                <a href="/cursos/<?= $course['id'] ?>" class="btn-course">
                                    Ver Detalhes <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Nenhum curso disponível no momento.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="/cursos" class="btn btn-primary btn-lg px-5">
                Ver Todos os Cursos <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Por que escolher nossa escola -->
<section class="py-5">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Por que escolher a EAM?</h2>
            <p>Descubra as vantagens de estudar na Escola Angolana Modelo</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h4 class="feature-title">Excelência Acadêmica</h4>
                    <p class="feature-description">Mais de 90% de aprovação nos exames nacionais e vestibulares das principais universidades do país.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <h4 class="feature-title">Tecnologia e Inovação</h4>
                    <p class="feature-description">Laboratórios modernos, sala de informática e plataforma digital de aprendizagem com conteúdo interativo.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h4 class="feature-title">Formação Integral</h4>
                    <p class="feature-description">Atividades extracurriculares, projetos sociais, clubes estudantis e programas de liderança.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h4 class="feature-title">Corpo Docente Qualificado</h4>
                    <p class="feature-description">Professores especializados, com experiência e dedicação exclusiva ao desenvolvimento dos alunos.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h4 class="feature-title">Infraestrutura Moderna</h4>
                    <p class="feature-description">Salas climatizadas, biblioteca, laboratórios, quadra poliesportiva e áreas de convivência.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h4 class="feature-title">Acompanhamento Personalizado</h4>
                    <p class="feature-description">Monitoria, tutoria e acompanhamento psicológico e pedagógico para todos os alunos.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Depoimentos -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>O que dizem nossos alunos</h2>
            <p>Depoimentos de quem já faz parte da nossa história</p>
        </div>
        
        <div class="swiper testimonialSwiper" data-aos="fade-up">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <div class="testimonial-quote">"</div>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"A Escola Angolana Modelo me preparou para o vestibular e para a vida. Os professores são excelentes e a infraestrutura é incrível! Consegui entrar em Medicina na UAN graças ao ensino de qualidade que recebi."</p>
                        <div class="testimonial-author">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Ana Silva">
                            <div class="author-info">
                                <h6>Ana Silva</h6>
                                <small>Ex-aluna, Medicina UAN</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <div class="testimonial-quote">"</div>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"Estudar na EAM foi uma das melhores decisões da minha vida. Além do ensino de qualidade, participei de projetos sociais que mudaram minha visão de mundo. Hoje sou engenheiro e levo os valores que aprendi aqui."</p>
                        <div class="testimonial-author">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="João Santos">
                            <div class="author-info">
                                <h6>João Santos</h6>
                                <small>Ex-aluno, Engenharia</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <div class="testimonial-quote">"</div>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"A EAM me proporcionou uma base sólida para minha carreira. Os professores são dedicados e sempre dispostos a ajudar. A estrutura moderna e os laboratórios bem equipados fazem toda diferença no aprendizado."</p>
                        <div class="testimonial-author">
                            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Maria Fernandes">
                            <div class="author-info">
                                <h6>Maria Fernandes</h6>
                                <small>Ex-aluna, Direito</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<!-- Chamada para Ação -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content" data-aos="fade-up">
            <h2>Pronto para começar sua jornada?</h2>
            <p>Faça sua inscrição e garanta sua vaga para o próximo ano letivo. Vagas limitadas!</p>
            <div class="cta-buttons">
                <a href="/inscricao" class="btn-cta btn-cta-light">
                    <i class="fas fa-pen me-2"></i>Inscreva-se Agora
                </a>
                <a href="/contato" class="btn-cta btn-cta-outline">
                    <i class="fas fa-envelope me-2"></i>Fale Conosco
                </a>
            </div>
        </div>
    </div>
</section>

<script>
    // Inicializar Swiper para depoimentos
    var testimonialSwiper = new Swiper('.testimonialSwiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            992: {
                slidesPerView: 3,
            },
        },
    });
    
    // Contador animado
    $('.counter').each(function() {
        var $this = $(this);
        var countTo = parseInt($this.text().replace(/[^0-9]/g, ''));
        
        if (!isNaN(countTo)) {
            $({ countNum: 0 }).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'swing',
                step: function() {
                    $this.text(Math.floor(this.countNum));
                },
                complete: function() {
                    $this.text(countTo);
                }
            });
        }
    });
</script>

<?= $this->endSection() ?>