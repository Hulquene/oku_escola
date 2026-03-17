<?php
// app/Views/auth/guardians-login.php
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Encarregados de Educação</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
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
            max-width: 450px;
            width: 100%;
            margin: 0 auto;
            position: relative;
            z-index: 10;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(27,43,75,0.15);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            padding: 30px 30px 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
        }
        
        .login-header::after {
            content: '';
            position: absolute;
            bottom: -30px;
            left: -30px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(59,127,232,0.2);
        }
        
        .login-header h2 {
            color: white;
            font-weight: 700;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }
        
        .login-header p {
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }
        
        .login-icon {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            position: relative;
            z-index: 1;
            border: 2px solid rgba(255,255,255,0.3);
        }
        
        .login-icon i {
            font-size: 30px;
            color: white;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .input-group {
            margin-bottom: 1.5rem;
        }
        
        .input-group-text {
            background: #f8f9fa;
            border: 1.5px solid #e9ecef;
            border-right: none;
            color: #6c757d;
        }
        
        .form-control {
            border: 1.5px solid #e9ecef;
            border-left: none;
            padding: 0.6rem 0.75rem;
            font-size: 0.95rem;
            transition: all 0.2s;
            background: rgba(255,255,255,0.9);
        }
        
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(59,127,232,0.1);
            background: white;
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-hover) 100%);
            color: white;
            font-weight: 600;
            padding: 0.8rem;
            border: none;
            border-radius: 10px;
            width: 100%;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(59,127,232,0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59,127,232,0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .login-footer a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .login-footer a:hover {
            color: var(--accent);
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 10px;
            border-left: 4px solid;
            padding: 1rem;
            margin-bottom: 1.5rem;
            animation: fadeIn 0.3s ease-out;
        }
        
        .alert-danger {
            background: rgba(232,70,70,0.1);
            border-left-color: var(--danger);
            color: #b02a2a;
        }
        
        .alert-success {
            background: rgba(22,168,125,0.1);
            border-left-color: var(--success);
            color: #0e7a5a;
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
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .other-logins {
            margin-top: 20px;
            text-align: center;
        }
        
        .other-logins-title {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 10px;
            position: relative;
        }
        
        .other-logins-title::before,
        .other-logins-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e9ecef;
            margin: 0 10px;
        }
        
        .other-logins-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .other-login-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 0.4rem 1rem;
            background: rgba(248,249,250,0.9);
            border: 1px solid #e9ecef;
            border-radius: 50px;
            color: var(--primary);
            text-decoration: none;
            font-size: 0.8rem;
            transition: all 0.2s;
        }
        
        .other-login-link:hover {
            background: white;
            border-color: var(--accent);
            color: var(--accent);
        }
        
        .other-login-link i {
            font-size: 0.9rem;
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
        <img src="<?= base_url('assets/images/family-bg.jpg') ?>" alt="Fundo Família">
    </div>
    
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h2>Área do Encarregado</h2>
                <p>Acompanhe o desempenho dos seus educandos</p>
            </div>
            
            <div class="login-body">
                <!-- Alertas de erro/sucesso -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <div><?= $error ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?= route_to('guardians.auth.signin') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Email ou NIF</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   name="username" 
                                   placeholder="seu@email.com" 
                                   value="<?= old('username') ?>"
                                   required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Senha</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" 
                                   class="form-control" 
                                   name="password" 
                                   placeholder="••••••••" 
                                   required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Entrar
                    </button>
                </form>
                
                <div class="login-footer">
                    <a href="<?= route_to('auth.forgetpassword') ?>">
                        <i class="fas fa-question-circle me-1"></i>
                        Esqueceu a senha?
                    </a>
                </div>
                
              
            </div>
        </div>
        
        <div class="text-center mt-3">
            <small class="text-white-50" style="color: rgba(255,255,255,0.7) !important;">
                <i class="fas fa-clock me-1"></i>
                Acesso exclusivo para encarregados de educação
            </small>
        </div>
    </div>
    
    <!-- Bootstrap JS (opcional, para componentes interativos) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>