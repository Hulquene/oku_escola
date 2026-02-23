<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Escola Angolana Modelo' ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts - Poppins e Open Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Swiper Slider -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    
    <style>
        :root {
            --primary-color: #dd4814;
            --primary-dark: #b33a0f;
            --primary-light: #fef1ea;
            --secondary-color: #1e2b3c;
            --secondary-dark: #141f2c;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-bg: #f8fafc;
            --gray-bg: #f1f5f9;
            --text-dark: #334155;
            --text-light: #64748b;
            --white: #ffffff;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --shadow-hover: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            --border-radius-sm: 0.375rem;
            --border-radius: 0.5rem;
            --border-radius-lg: 1rem;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--text-dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--secondary-color);
        }

        /* Navbar Styles */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-sm);
            padding: 0.75rem 0;
            transition: var(--transition);
        }

        .navbar.scrolled {
            padding: 0.5rem 0;
            box-shadow: var(--shadow-md);
        }

        .navbar-brand {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand i {
            font-size: 2rem;
        }

        .nav-link {
            font-weight: 500;
            color: var(--secondary-color) !important;
            padding: 0.5rem 1rem !important;
            border-radius: var(--border-radius-sm);
            transition: var(--transition);
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
            background: var(--primary-light);
        }

        .nav-link.active {
            color: var(--primary-color) !important;
            background: var(--primary-light);
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1rem;
            right: 1rem;
            height: 2px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .btn-nav {
            padding: 0.5rem 1.25rem;
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            transition: var(--transition);
            margin-left: 0.5rem;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-primary {
            background: var(--primary-color);
            border: 2px solid var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            min-height: 80vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-dark) 100%);
            overflow: hidden;
        }

        .hero-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            padding: 4rem 0;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .hero-subtitle {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            max-width: 600px;
        }

        .hero-stats {
            display: flex;
            gap: 2rem;
            margin-top: 3rem;
        }

        .hero-stat-item {
            text-align: center;
        }

        .hero-stat-number {
            font-size: 2rem;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            display: block;
            line-height: 1;
        }

        .hero-stat-label {
            font-size: 0.875rem;
            opacity: 0.8;
        }

        .hero-image {
            position: relative;
            z-index: 2;
            animation: float 6s ease-in-out infinite;
        }

        .hero-image img {
            max-width: 100%;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* Cards e Seções */
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 3px;
        }

        .section-title p {
            color: var(--text-light);
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }

        .stat-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 2rem;
            transition: var(--transition);
        }

        .stat-card:hover .stat-icon {
            background: var(--primary-color);
            color: white;
            transform: rotateY(180deg);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-light);
            font-weight: 500;
        }

        .course-card {
            background: white;
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            height: 100%;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .course-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }

        .course-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .course-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .course-card:hover .course-image img {
            transform: scale(1.1);
        }

        .course-category {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
            z-index: 2;
        }

        .course-content {
            padding: 1.5rem;
        }

        .course-title {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }

        .course-title a {
            color: var(--secondary-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .course-title a:hover {
            color: var(--primary-color);
        }

        .course-description {
            color: var(--text-light);
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        .course-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .course-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .course-meta-item i {
            color: var(--primary-color);
        }

        .course-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .course-price {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.25rem;
        }

        .btn-course {
            padding: 0.5rem 1.5rem;
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            border-radius: var(--border-radius-sm);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-course:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Features Section */
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            text-align: center;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            height: 100%;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }

        .feature-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 1.5rem;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 2.5rem;
            transition: var(--transition);
        }

        .feature-card:hover .feature-icon {
            background: var(--primary-color);
            color: white;
            transform: rotate(360deg);
        }

        .feature-title {
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .feature-description {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        /* Testimonials */
        .testimonial-card {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            margin: 1rem;
            position: relative;
        }

        .testimonial-quote {
            position: absolute;
            top: -10px;
            left: 20px;
            font-size: 4rem;
            color: var(--primary-color);
            opacity: 0.1;
        }

        .testimonial-rating {
            color: #ffc107;
            margin-bottom: 1rem;
        }

        .testimonial-text {
            color: var(--text-dark);
            font-style: italic;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 2;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .testimonial-author img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
        }

        .author-info h6 {
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }

        .author-info small {
            color: var(--text-light);
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 5rem 0;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2"/></svg>');
            opacity: 0.3;
            animation: rotate 60s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .cta-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
        }

        .cta-content h2 {
            color: white;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .cta-content p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn-cta {
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            border: 2px solid transparent;
        }

        .btn-cta-light {
            background: white;
            color: var(--primary-color);
        }

        .btn-cta-light:hover {
            background: transparent;
            border-color: white;
            color: white;
            transform: translateY(-2px);
        }

        .btn-cta-outline {
            background: transparent;
            border: 2px solid white;
            color: white;
        }

        .btn-cta-outline:hover {
            background: white;
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        /* Footer */
        .footer {
            background: var(--secondary-color);
            color: white;
            padding: 4rem 0 2rem;
            position: relative;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), transparent);
        }

        .footer h5 {
            color: white;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.75rem;
        }

        .footer h5::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background: var(--primary-color);
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: #cbd5e1;
            text-decoration: none;
            transition: var(--transition);
            display: inline-block;
        }

        .footer-links a:hover {
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .footer-contact {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-contact li {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            color: #cbd5e1;
        }

        .footer-contact i {
            color: var(--primary-color);
            width: 20px;
            margin-top: 4px;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: var(--transition);
        }

        .social-links a:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
        }

        .footer-bottom {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .footer-bottom a {
            color: white;
            text-decoration: none;
        }

        .footer-bottom a:hover {
            color: var(--primary-color);
        }

        /* Modal */
        .modal-content {
            border: none;
            border-radius: var(--border-radius-lg);
            overflow: hidden;
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1.5rem;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
            opacity: 1;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            border-top: 1px solid #eee;
            padding: 1.5rem;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-section {
                min-height: auto;
                padding: 4rem 0;
            }
            
            .hero-stats {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        @media (max-width: 767.98px) {
            .section-title h2 {
                font-size: 2rem;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .cta-content h2 {
                font-size: 1.75rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .footer {
                text-align: center;
            }
            
            .footer h5::after {
                left: 50%;
                transform: translateX(-50%);
            }
            
            .footer-contact li {
                justify-content: center;
            }
            
            .social-links {
                justify-content: center;
            }
        }

        /* Loading Spinner */
        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--primary-light);
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            z-index: 99;
            box-shadow: var(--shadow-lg);
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background: var(--primary-dark);
            color: white;
            transform: translateY(-5px);
        }
        /* Page Header */
.page-header {
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
    opacity: 0.3;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255,255,255,0.5);
}

/* Team Cards */
.team-card {
    background: white;
    border: none;
    transition: var(--transition);
}

.social-links-team .btn {
    width: 40px;
    height: 40px;
    padding: 0;
    line-height: 40px;
    text-align: center;
    margin: 0 5px;
}

/* Contact Cards */
.contact-info-card {
    background: white;
    border: none;
}

/* Schedule Cards */
.schedule-card {
    background: white;
    border: none;
}

/* Accordion */
.accordion-item {
    border: none;
    margin-bottom: 10px;
    border-radius: var(--border-radius) !important;
    overflow: hidden;
}

.accordion-button {
    border-radius: var(--border-radius) !important;
    background-color: white;
    box-shadow: var(--shadow-sm);
}

.accordion-button:not(.collapsed) {
    background-color: var(--primary-light);
    color: var(--primary-color);
}

.accordion-body {
    background-color: white;
    padding: 1.5rem;
}

/* Grade Cards */
.grade-card {
    background: white;
    border: 1px solid #e9ecef;
}

.grade-card ul li {
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

/* Filter Buttons */
.filter-btn {
    border-radius: 50px;
    padding: 0.5rem 1.5rem;
    font-weight: 500;
    transition: var(--transition);
}

/* Course Item Animation */
.course-item {
    transition: var(--transition);
}
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-graduation-cap"></i>
                <span>EAM</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= current_url() == base_url('/') ? 'active' : '' ?>" href="/">
                            <i class="fas fa-home"></i> Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), '/cursos') !== false ? 'active' : '' ?>" href="/cursos">
                            <i class="fas fa-book"></i> Cursos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), '/sobre') !== false ? 'active' : '' ?>" href="/sobre">
                            <i class="fas fa-info-circle"></i> Sobre
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), '/contato') !== false ? 'active' : '' ?>" href="/contato">
                            <i class="fas fa-envelope"></i> Contato
                        </a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="/inscricao" class="btn btn-outline-primary btn-nav">
                        <i class="fas fa-user-plus"></i> Inscrever-se
                    </a>
                    <a href="/auth/students" class="btn btn-primary btn-nav">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main style="margin-top: 76px;">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5><i class="fas fa-graduation-cap me-2"></i> Escola Angolana Modelo</h5>
                    <p class="text-light-emphasis">Excelência em educação desde 2010, formando cidadãos preparados para os desafios do futuro com qualidade e inovação.</p>
                    <div class="social-links">
                        <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-youtube"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-lg-2">
                    <h5>Links Rápidos</h5>
                    <ul class="footer-links">
                        <li><a href="/"><i class="fas fa-chevron-right me-2"></i>Início</a></li>
                        <li><a href="/cursos"><i class="fas fa-chevron-right me-2"></i>Cursos</a></li>
                        <li><a href="/sobre"><i class="fas fa-chevron-right me-2"></i>Sobre Nós</a></li>
                        <li><a href="/contato"><i class="fas fa-chevron-right me-2"></i>Contato</a></li>
                        <li><a href="/inscricao"><i class="fas fa-chevron-right me-2"></i>Inscrição</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5>Contato</h5>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Rua da Educação, 123<br>Luanda, Angola</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span>+244 999 999 999<br>+244 999 999 998</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>info@escola.ao<br>secretaria@escola.ao</span>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5>Horário de Funcionamento</h5>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Secretaria:</strong><br>
                                Seg - Sex: 07:30 - 18:00<br>
                                Sáb: 08:00 - 12:00
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Aulas:</strong><br>
                                Manhã: 07:30 - 12:30<br>
                                Tarde: 13:30 - 18:00
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="row">
                    <div class="col-md-6 text-md-start">
                        <p>&copy; <?= date('Y') ?> Escola Angolana Modelo. Todos os direitos reservados.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p>Desenvolvido com <i class="fas fa-heart text-danger"></i> por <a href="#" target="_blank">EAM Tech</a></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    
    <script>
        // Inicializar AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Navbar scroll effect
        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                $('#mainNav').addClass('scrolled');
                $('#backToTop').addClass('show');
            } else {
                $('#mainNav').removeClass('scrolled');
                $('#backToTop').removeClass('show');
            }
        });

        // Smooth scroll para links
        $('a[href*="#"]').on('click', function(e) {
            if ($(this).attr('href').startsWith('#')) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $($(this).attr('href')).offset().top - 80
                }, 500);
            }
        });

        // Back to top
        $('#backToTop').click(function(e) {
            e.preventDefault();
            $('html, body').animate({scrollTop: 0}, 500);
        });

        // Loading spinner para botões
        $('.btn').click(function() {
            var btn = $(this);
            if (btn.hasClass('btn-loading')) return;
            
            if (btn.attr('type') === 'submit') {
                btn.addClass('btn-loading').prepend('<span class="spinner-border spinner-border-sm me-2"></span>');
            }
        });

        <?= $this->renderSection('scripts') ?>
    </script>
</body>
</html>