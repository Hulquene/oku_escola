<?= $this->extend('layouts/public_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="page-header bg-primary text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center" data-aos="fade-up">
                <h1 class="display-4 fw-bold">Nossos Cursos</h1>
                <p class="lead">Conheça nossa grade curricular completa do Ensino Médio</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="/" class="text-white">Início</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Cursos</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Filtros de Cursos -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row justify-content-center" data-aos="fade-up">
            <div class="col-lg-8">
                <div class="course-filters text-center">
                    <button class="btn btn-outline-primary filter-btn active mx-1 my-1" data-filter="all">Todos</button>
                    <button class="btn btn-outline-primary filter-btn mx-1 my-1" data-filter="Ciências">Ciências</button>
                    <button class="btn btn-outline-primary filter-btn mx-1 my-1" data-filter="Humanidades">Humanidades</button>
                    <button class="btn btn-outline-primary filter-btn mx-1 my-1" data-filter="Económico-Jurídico">Económico-Jurídico</button>
                    <button class="btn btn-outline-primary filter-btn mx-1 my-1" data-filter="Técnico">Técnico</button>
                    <button class="btn btn-outline-primary filter-btn mx-1 my-1" data-filter="Profissional">Profissional</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Lista de Cursos -->
<section class="py-5">
    <div class="container">
        <div class="row" id="coursesContainer">
            <?php if(isset($courses) && !empty($courses)): ?>
                <?php foreach($courses as $index => $course): ?>
                <div class="col-lg-4 col-md-6 mb-4 course-item" data-category="<?= $course['course_type'] ?>" data-aos="fade-up" data-aos-delay="<?= ($index % 3) * 100 ?>">
                    <div class="course-card h-100">
                        <div class="course-image">
                            <?php
                            $imageUrls = [
                                'Ciências' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
                                'Humanidades' => 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
                                'Económico-Jurídico' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
                                'Técnico' => 'https://images.unsplash.com/photo-1581091226033-d5c48150dbaa?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
                                'Profissional' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'
                            ];
                            $imageUrl = $imageUrls[$course['course_type']] ?? 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
                            ?>
                            <img src="<?= $imageUrl ?>" alt="<?= $course['course_name'] ?>">
                            <span class="course-category"><?= $course['course_type'] ?></span>
                        </div>
                        <div class="course-content">
                            <h5 class="course-title">
                                <a href="/cursos/<?= $course['id'] ?>"><?= $course['course_name'] ?></a>
                            </h5>
                            <p class="course-description">
                                <?= isset($course['description']) ? substr($course['description'], 0, 100) . '...' : 'Curso de formação geral com ênfase em ' . $course['course_type'] . '.' ?>
                            </p>
                            <div class="course-meta">
                                <span class="course-meta-item">
                                    <i class="fas fa-clock"></i> <?= $course['duration_years'] ?> anos
                                </span>
                                <span class="course-meta-item">
                                    <i class="fas fa-layer-group"></i> 10ª à <?= $course['duration_years'] == 3 ? '12ª' : '13ª' ?> Classe
                                </span>
                            </div>
                            
                            <div class="course-highlights mt-3 mb-3">
                                <small class="text-muted d-block mb-2"><i class="fas fa-check-circle text-primary me-1"></i> Certificação reconhecida pelo MINED</small>
                                <small class="text-muted d-block"><i class="fas fa-check-circle text-primary me-1"></i> Corpo docente especializado</small>
                            </div>
                            
                            <div class="course-footer">
                                <span class="course-price">
                                    <i class="fas fa-graduation-cap me-1"></i> Ensino Gratuito
                                </span>
                                <button class="btn-course" data-bs-toggle="modal" data-bs-target="#courseModal<?= $course['id'] ?>">
                                    Ver Grade <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Detalhado do Curso -->
                <div class="modal fade" id="courseModal<?= $course['id'] ?>" tabindex="-1" aria-labelledby="courseModalLabel<?= $course['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="courseModalLabel<?= $course['id'] ?>">
                                    <i class="fas fa-book-open me-2"></i><?= $course['course_name'] ?>
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12 mb-4">
                                        <h6 class="fw-bold">Sobre o Curso</h6>
                                        <p><?= $course['description'] ?? 'Curso de formação geral com ênfase em ' . $course['course_type'] . ', preparando os alunos para o ingresso no ensino superior e para o mercado de trabalho.' ?></p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="grade-card p-3 bg-light rounded-3 mb-3">
                                            <h6 class="fw-bold text-primary"><i class="fas fa-graduation-cap me-2"></i>10ª Classe</h6>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success me-2"></i>Língua Portuguesa</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Matemática</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Física</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Química</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Biologia</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Inglês</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Educação Física</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Educação Moral e Cívica</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="grade-card p-3 bg-light rounded-3 mb-3">
                                            <h6 class="fw-bold text-primary"><i class="fas fa-graduation-cap me-2"></i>11ª Classe</h6>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success me-2"></i>Língua Portuguesa</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Matemática</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Física</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Química</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Biologia</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Filosofia</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Geografia</li>
                                                <li><i class="fas fa-check text-success me-2"></i>História</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="grade-card p-3 bg-light rounded-3">
                                            <h6 class="fw-bold text-primary"><i class="fas fa-graduation-cap me-2"></i>12ª Classe</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <ul class="list-unstyled">
                                                        <li><i class="fas fa-check text-success me-2"></i>Língua Portuguesa</li>
                                                        <li><i class="fas fa-check text-success me-2"></i>Matemática</li>
                                                        <li><i class="fas fa-check text-success me-2"></i>Disciplinas de Opção</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <ul class="list-unstyled">
                                                        <li><i class="fas fa-check text-success me-2"></i>Monografia/TCC</li>
                                                        <li><i class="fas fa-check text-success me-2"></i>Preparação para Exames</li>
                                                        <li><i class="fas fa-check text-success me-2"></i>Orientação Vocacional</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="alert alert-primary">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Duração:</strong> <?= $course['duration_years'] ?> anos (10ª à <?= $course['duration_years'] == 3 ? '12ª' : '13ª' ?> Classe)
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                <a href="/inscricao" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i>Inscrever-se neste Curso
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Nenhum curso disponível no momento.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Por que escolher nossos cursos -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Por que escolher nossos cursos?</h2>
            <p>Diferenciais que fazem a diferença na sua formação</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="feature-card text-center p-4 bg-white rounded-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-chalkboard-teacher fa-3x text-primary"></i>
                    </div>
                    <h5>Corpo Docente Especializado</h5>
                    <p class="text-muted">Professores qualificados com experiência no ensino médio e preparatório para vestibulares.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card text-center p-4 bg-white rounded-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-book-open fa-3x text-primary"></i>
                    </div>
                    <h5>Material Didático Atualizado</h5>
                    <p class="text-muted">Conteúdos alinhados com as diretrizes do MINED e as principais universidades do país.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card text-center p-4 bg-white rounded-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-laptop fa-3x text-primary"></i>
                    </div>
                    <h5>Plataforma Digital</h5>
                    <p class="text-muted">Acesso a materiais complementares, exercícios e simulados online.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Chamada para Inscrição -->
<section class="py-5 cta-section">
    <div class="container">
        <div class="cta-content" data-aos="fade-up">
            <h2 class="mb-4">Gostou dos nossos cursos?</h2>
            <p class="lead mb-5">Faça já a sua pré-inscrição e garanta sua vaga para o próximo ano letivo.</p>
            <div class="cta-buttons">
                <a href="/inscricao" class="btn-cta btn-cta-light">
                    <i class="fas fa-pen me-2"></i>Inscrever-se Agora
                </a>
                <a href="/contato" class="btn-cta btn-cta-outline">
                    <i class="fas fa-envelope me-2"></i>Tirar Dúvidas
                </a>
            </div>
        </div>
    </div>
</section>

<script>
    // Filtro de cursos
    $(document).ready(function() {
        $('.filter-btn').click(function() {
            var filter = $(this).data('filter');
            
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            
            if(filter === 'all') {
                $('.course-item').fadeIn(300);
            } else {
                $('.course-item').each(function() {
                    if($(this).data('category') === filter) {
                        $(this).fadeIn(300);
                    } else {
                        $(this).fadeOut(300);
                    }
                });
            }
        });
    });
</script>

<style>
    .grade-card {
        transition: var(--transition);
    }
    .grade-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }
    .filter-btn.active {
        background-color: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    .filter-btn {
        min-width: 120px;
        transition: var(--transition);
    }
    .filter-btn:hover {
        transform: translateY(-2px);
    }
</style>

<?= $this->endSection() ?>