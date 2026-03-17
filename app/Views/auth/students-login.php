<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aluno Login - Sistema Escolar</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #1B2B4B;
            --primary-light: #243761;
            --accent: #3B7FE8;
            --accent-hover: #2C6FD4;
            --success: #16A87D;
            --danger: #E84646;
            --warning: #E8A020;
            --surface: #F5F7FC;
            --surface-card: #FFFFFF;
            --border: #E2E8F4;
            --text-primary: #1A2238;
            --text-secondary: #6B7A99;
            --text-muted: #9AA5BE;
            --shadow-lg: 0 8px 32px rgba(27,43,75,0.14);
        }
        
        body {
            font-family: 'Sora', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Imagem de fundo absoluta */
        .background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        
        .background-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(27,43,75,0.85) 0%, rgba(36,55,97,0.85) 60%, rgba(45,74,122,0.85) 100%);
            z-index: 1;
        }
        
        .background-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        
        /* Elementos decorativos sobre a imagem */
        .background-image::after {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            background: url('<?= base_url('assets/images/school-bg.jpg') ?>') no-repeat center;
            background-size: contain;
            opacity: 0.03;
            transform: rotate(15deg);
            pointer-events: none;
            z-index: 2;
        }
        
        .login-container {
            max-width: 420px;
            width: 100%;
            margin: 0 auto;
            position: relative;
            z-index: 10;
        }
        
        .login-card {
            background: var(--surface-card);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
            border: 1px solid var(--border);
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            pointer-events: none;
        }
        
        .login-header::after {
            content: '';
            position: absolute;
            bottom: -40px;
            left: -40px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(59,127,232,0.15);
            pointer-events: none;
        }
        
        .login-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.12);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            position: relative;
            z-index: 1;
            border: 2px solid rgba(255,255,255,0.2);
        }
        
        .login-icon i {
            font-size: 35px;
            color: white;
        }
        
        .login-header h2 {
            color: white;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
            font-size: 1.6rem;
        }
        
        .login-header p {
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }
        
        .login-body {
            padding: 35px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.07em;
        }
        
        .form-label i {
            color: var(--accent);
            margin-right: 5px;
            font-size: 0.7rem;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            z-index: 10;
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-size: 0.9rem;
            font-family: 'Sora', sans-serif;
            color: var(--text-primary);
            background: var(--surface);
            transition: all 0.2s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            background: var(--surface-card);
            box-shadow: 0 0 0 3px rgba(59,127,232,0.1);
        }
        
        .form-control::placeholder {
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--accent);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 5px 15px rgba(59,127,232,0.3);
        }
        
        .btn-login:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59,127,232,0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login i {
            font-size: 1rem;
        }
        
        .login-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }
        
        .login-footer a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .login-footer a:hover {
            color: var(--accent);
            text-decoration: underline;
        }
        
        .login-footer a i {
            margin-right: 5px;
            font-size: 0.7rem;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 15px;
            color: white;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            background: rgba(255,255,255,0.1);
            padding: 8px 15px;
            border-radius: 50px;
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.2s;
            backdrop-filter: blur(5px);
        }
        
        .back-link:hover {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateX(-3px);
        }
        
        .student-info {
            background: rgba(59,127,232,0.05);
            border-left: 3px solid var(--accent);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.8rem;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        
        .student-info i {
            color: var(--accent);
            font-size: 1.2rem;
            margin-top: 2px;
        }
        
        .student-info p {
            margin: 0;
            color: var(--text-secondary);
            line-height: 1.5;
        }
        
        .student-info strong {
            color: var(--text-primary);
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 3px solid;
        }
        
        .alert-danger {
            background: rgba(232,70,70,0.08);
            border-left-color: var(--danger);
            color: #B03030;
        }
        
        .alert-danger i {
            color: var(--danger);
        }
        
        .alert ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .help-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            text-align: center;
            font-size: 0.8rem;
        }
        
        .help-box small {
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        
        .help-box a {
            color: var(--accent);
            font-weight: 600;
            text-decoration: none;
        }
        
        .help-box a:hover {
            text-decoration: underline;
        }
        
        .text-white-50 {
            color: rgba(255,255,255,0.5) !important;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .background-image::after,
            .background-image::before {
                display: none;
            }
            
            body {
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            }
            
            .background-image {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Imagem de fundo absoluta -->
    <div class="background-image">
        <img src="<?= base_url('assets/images/school-bg.jpg') ?>" alt="Fundo Escolar">
    </div>
    
    <div class="login-container">
        <a href="<?= site_url() ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar ao site
        </a>
        
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h2>Área do Aluno</h2>
                <p>Acesso ao portal do estudante</p>
            </div>
            
            <div class="login-body">
                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= session('error') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <ul style="margin:0; padding-left:20px;">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="<?= site_url('auth/students/signin') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i> Usuário / Email / Matrícula
                        </label>
                        <div class="input-group">
                            <span class="input-icon"><i class="fas fa-user"></i></span>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   placeholder="Digite seu usuário, email ou nº matrícula"
                                   value="<?= old('username') ?>"
                                   autofocus
                                   required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> Senha
                        </label>
                        <div class="input-group">
                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Digite sua senha"
                                   required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Acessar Portal do Aluno
                    </button>
                </form>
                
                <div class="help-box">
                    <small>
                        <i class="fas fa-question-circle" style="color: var(--accent);"></i>
                        <strong>Primeiro acesso?</strong> Use sua data de nascimento no formato <strong>DDMMAAAA</strong>.
                        <a href="<?= site_url('auth/forgetpassword') ?>"> Esqueceu a senha?</a>
                    </small>
                </div>
                
                <div class="login-footer">
                    <a href="<?= site_url('auth/forgetpassword') ?>">
                        <i class="fas fa-key"></i> Recuperar senha
                    </a>
                    <a href="<?= site_url('/') ?>">
                        <i class="fas fa-home"></i> Página inicial
                    </a>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <small class="text-white-50">
                <i class="fas fa-shield-alt me-1"></i>
                Sistema de Gestão Escolar © <?= date('Y') ?>
            </small>
        </div>
    </div>
</body>
</html>