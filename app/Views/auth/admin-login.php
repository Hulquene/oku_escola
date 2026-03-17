<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Sistema Escolar</title>
    
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
            /* background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 60%, #2D4A7A 100%); */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        
        .login-container {
            max-width: 420px;
            width: 100%;
            margin: 0 auto;
        }
        
        .login-card {
            background: var(--surface-card);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
            border: 1px solid var(--border);
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
        
        .login-header h2 {
            color: white;
            font-weight: 700;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
            font-size: 1.4rem;
        }
        
        .login-header p {
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--primary);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .form-label i {
            color: var(--accent);
            margin-right: 5px;
            font-size: 0.7rem;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 0;
        }
        
        .input-group-text {
            background: #f8f9fa;
            border: 1.5px solid var(--border);
            border-right: none;
            color: var(--text-muted);
            border-radius: 10px 0 0 10px;
        }
        
        .form-control {
            width: 100%;
            padding: 0.7rem 0.75rem;
            border: 1.5px solid var(--border);
            border-left: none;
            border-radius: 0 10px 10px 0;
            font-size: 0.9rem;
            font-family: 'Sora', sans-serif;
            transition: all 0.2s;
            background: var(--surface);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(59,127,232,0.1);
            background: var(--surface-card);
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
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59,127,232,0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login i {
            margin-right: 8px;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }
        
        .login-footer a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .login-footer a:hover {
            color: var(--accent);
            text-decoration: underline;
        }
        
        .login-footer a i {
            margin-right: 5px;
            font-size: 0.8rem;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
            color: white;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            background: rgba(255,255,255,0.1);
            padding: 8px 16px;
            border-radius: 50px;
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.2s;
        }
        
        .back-link:hover {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateX(-3px);
        }
        
        .back-link i {
            font-size: 0.8rem;
        }
        
        .alert {
            border-radius: 10px;
            border-left: 4px solid;
            padding: 1rem;
            margin-bottom: 1.5rem;
            animation: fadeIn 0.3s ease-out;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-danger {
            background: rgba(232,70,70,0.08);
            border-left-color: var(--danger);
            color: #b02a2a;
        }
        
        .alert-danger i {
            color: var(--danger);
        }
        
        .alert ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .other-logins {
            margin-top: 20px;
            text-align: center;
        }
        
        .other-logins-title {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .other-logins-title::before,
        .other-logins-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        
        .other-logins-links {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .other-login-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 0.4rem 1rem;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 50px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .other-login-link:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: white;
        }
        
        .other-login-link i {
            font-size: 0.8rem;
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
    </style>
</head>
<body>
    <div class="login-container">
        
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h2>Área Administrativa</h2>
                <p>Acesso restrito à direção e administração</p>
            </div>
            
            <div class="login-body">
                <!-- Alertas de erro/sucesso -->
                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= session('error') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul style="margin:0; padding-left:20px;">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="<?= site_url('auth/admin/signin') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i> Usuário Administrativo
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   name="username" 
                                   placeholder="Digite seu usuário ou email"
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
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" 
                                   class="form-control" 
                                   name="password" 
                                   placeholder="Digite sua senha"
                                   required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Acessar Painel Admin
                    </button>
                </form>
                
                <div class="login-footer">
                    <a href="<?= site_url('auth/forgetpassword') ?>">
                        <i class="fas fa-question-circle me-1"></i>
                        Esqueceu a senha?
                    </a>
                </div>
                
                <div class="other-logins">
                    <div class="other-logins-title">
                        <span class="px-2">Outros acessos</span>
                    </div>
                    <div class="other-logins-links">
                        <a href="<?= route_to('auth.teachers') ?>" class="other-login-link">
                            <i class="fas fa-chalkboard-teacher"></i> Professor
                        </a>
                        <a href="<?= route_to('auth.students') ?>" class="other-login-link">
                            <i class="fas fa-user-graduate"></i> Aluno
                        </a>
                        <a href="<?= route_to('auth.guardians') ?>" class="other-login-link">
                            <i class="fas fa-user-tie"></i> Encarregado
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <small class="text-white-50" style="color: rgba(255,255,255,0.5) !important;">
                <i class="fas fa-clock me-1"></i>
                Acesso exclusivo para administradores do sistema
            </small>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>