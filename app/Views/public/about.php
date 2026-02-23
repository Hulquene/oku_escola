<?= $this->extend('layouts/public_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="page-header bg-primary text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center" data-aos="fade-up">
                <h1 class="display-4 fw-bold">Sobre Nós</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="/" class="text-white">Início</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Sobre Nós</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Nossa História -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                     alt="Nossa Escola" class="img-fluid rounded-4 shadow-lg">
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <span class="badge bg-primary mb-3 px-3 py-2">Nossa História</span>
                <h2 class="display-5 fw-bold mb-4"><?= date('Y') - ($schoolInfo['founded'] ?? 2010) ?> Anos de Excelência em Educação</h2>
                <p class="lead mb-4"><?= $schoolInfo['history'] ?? 'Fundada em 2010, a Escola Angolana Modelo nasceu com o propósito de oferecer educação de qualidade alinhada às necessidades do século XXI.' ?></p>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-primary-light p-3">
                                    <i class="fas fa-flag fa-2x text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Missão</h5>
                                <p class="text-muted"><?= $schoolInfo['mission'] ?? 'Formar cidadãos críticos, criativos e comprometidos com o desenvolvimento de Angola.' ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-primary-light p-3">
                                    <i class="fas fa-eye fa-2x text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Visão</h5>
                                <p class="text-muted"><?= $schoolInfo['vision'] ?? 'Ser referência nacional em educação de qualidade, inovação e inclusão social.' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Nossos Valores -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Nossos Valores</h2>
            <p>Os princípios que norteiam nossa instituição</p>
        </div>
        
        <div class="row g-4">
            <?php 
            $values = explode(',', $schoolInfo['values'] ?? 'Excelência, Ética, Inovação, Inclusão, Compromisso Social');
            $valueDescriptions = [
                'Excelência' => 'Busca contínua pela qualidade em todos os processos educacionais e administrativos.',
                'Ética' => 'Transparência, honestidade e respeito nas relações com toda comunidade escolar.',
                'Inovação' => 'Adoção de metodologias modernas e tecnologias para potencializar o aprendizado.',
                'Inclusão' => 'Respeito à diversidade e garantia de acesso à educação de qualidade para todos.',
                'Compromisso Social' => 'Formação de cidadãos conscientes e atuantes na sociedade.'
            ];
            ?>
            <?php foreach($values as $index => $value): ?>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                <div class="value-card text-center p-4 bg-white rounded-4 shadow-sm h-100">
                    <div class="value-icon mb-3">
                        <?php
                        $icon = 'fa-star';
                        switch(trim($value)) {
                            case 'Excelência': $icon = 'fa-medal'; break;
                            case 'Ética': $icon = 'fa-scale-balanced'; break;
                            case 'Inovação': $icon = 'fa-lightbulb'; break;
                            case 'Inclusão': $icon = 'fa-people-group'; break;
                            case 'Compromisso Social': $icon = 'fa-hand-holding-heart'; break;
                        }
                        ?>
                        <i class="fas <?= $icon ?> fa-4x text-primary"></i>
                    </div>
                    <h4 class="mb-3"><?= trim($value) ?></h4>
                    <p class="text-muted"><?= $valueDescriptions[trim($value)] ?? 'Valor fundamental da nossa instituição.' ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Nossa Equipe -->
<section class="py-5">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Nossa Equipe Diretiva</h2>
            <p>Profissionais dedicados à excelência educacional</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="team-card text-center p-4 rounded-4 shadow-sm h-100">
                    <div class="team-image mb-4">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Diretora" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <h4>Dra. Maria Santos</h4>
                    <p class="text-primary mb-2">Diretora Geral</p>
                    <p class="text-muted small">Mestre em Educação pela UAN, 20 anos de experiência</p>
                    <div class="social-links-team">
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="team-card text-center p-4 rounded-4 shadow-sm h-100">
                    <div class="team-image mb-4">
                        <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Diretor Pedagógico" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <h4>Prof. João Ferreira</h4>
                    <p class="text-primary mb-2">Diretor Pedagógico</p>
                    <p class="text-muted small">Especialista em Currículo, 15 anos de experiência</p>
                    <div class="social-links-team">
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="team-card text-center p-4 rounded-4 shadow-sm h-100">
                    <div class="team-image mb-4">
                        <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Coordenadora" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <h4>Dra. Ana Paula</h4>
                    <p class="text-primary mb-2">Coordenadora Acadêmica</p>
                    <p class="text-muted small">Doutora em Educação, 12 anos de experiência</p>
                    <div class="social-links-team">
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Números -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Nossa Escola em Números</h2>
            <p>Resultados que comprovam nossa qualidade</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3 col-6" data-aos="fade-up">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="stat-number counter"><?= $schoolInfo['students'] ?? '1500' ?></h3>
                    <p class="stat-label">Alunos</p>
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
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 class="stat-number counter">95</h3>
                    <p class="stat-label">% Aprovação</p>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3 class="stat-number counter">5</h3>
                    <p class="stat-label">Prêmios</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .value-card {
        transition: var(--transition);
    }
    .value-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-hover) !important;
    }
    .value-icon {
        width: 100px;
        height: 100px;
        margin: 0 auto;
        background: var(--primary-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .team-card {
        transition: var(--transition);
    }
    .team-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-hover) !important;
    }
    .bg-primary-light {
        background: var(--primary-light);
    }
</style>

<?= $this->endSection() ?>