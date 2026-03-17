<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Escolar</title>
    
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
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 60%, #2D4A7A 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        
        .login-container {
            max-width: 480px;
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
        
        .login-header h2 {
            color: white;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
            font-size: 1.8rem;
        }
        
        .login-header p {
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }
        
        .login-body {
            padding: 35px;
        }
        
        .profile-selector {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 30px;
        }
        
        .profile-option {
            text-align: center;
            padding: 15px 10px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: var(--surface);
        }
        
        .profile-option:hover {
            border-color: var(--accent);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(59,127,232,0.15);
        }
        
        .profile-option.active {
            border-color: var(--accent);
            background: rgba(59,127,232,0.05);
            box-shadow: 0 5px 15px rgba(59,127,232,0.2);
        }
        
        .profile-option i {
            font-size: 28px;
            color: var(--accent);
            margin-bottom: 8px;
        }
        
        .profile-option span {
            display: block;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.8rem;
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
        
        .btn-toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            z-index: 11;
            font-size: 1rem;
        }
        
        .btn-toggle-password:hover {
            color: var(--accent);
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
            text-align: center;
            margin-top: 25px;
        }
        
        .login-footer a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .login-footer a:hover {
            color: var(--accent);
            text-decoration: underline;
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
        
        .alert-success {
            background: rgba(22,168,125,0.08);
            border-left-color: var(--success);
            color: #0E7A5A;
        }
        
        .alert-success i {
            color: var(--success);
        }
        
        .alert ul {
            margin: 0;
            padding-left: 20px;
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
        
        /* Links rápidos para outros logins */
        .quick-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }
        
        .quick-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.75rem;
            transition: all 0.2s;
        }
        
        .quick-link:hover {
            color: var(--accent);
        }
        
        .quick-link i {
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2>Sistema de Gestão Escolar</h2>
                <p>Faça login para acessar sua área</p>
            </div>
            
            <div class="login-body">
                <!-- Mensagens de erro/sucesso -->
                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= session('error') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->has('success')): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= session('success') ?>
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
                
                <!-- Seletor de Perfil -->
                <div class="profile-selector">
                    <div class="profile-option active" data-profile="admin" onclick="selectProfile('admin')">
                        <i class="fas fa-user-shield"></i>
                        <span>Administrador</span>
                    </div>
                    <div class="profile-option" data-profile="teacher" onclick="selectProfile('teacher')">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Professor</span>
                    </div>
                    <div class="profile-option" data-profile="student" onclick="selectProfile('student')">
                        <i class="fas fa-user-graduate"></i>
                        <span>Aluno</span>
                    </div>
                </div>
                
                <!-- Formulário de Login -->
                <form action="<?= site_url('auth/signin') ?>" method="post" id="loginForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="profile_type" id="profile_type" value="admin">
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i> Usuário / Email
                        </label>
                        <div class="input-group">
                            <span class="input-icon"><i class="fas fa-user"></i></span>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   placeholder="Digite seu usuário ou email"
                                   value="<?= old('username') ?>"
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
                            <button type="button" 
                                    class="btn-toggle-password" 
                                    onclick="togglePassword()"
                                    title="Mostrar/ocultar senha">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </button>
                    
                    <div class="login-footer">
                        <a href="<?= site_url('auth/forgetpassword') ?>">
                            <i class="fas fa-key"></i> Esqueceu sua senha?
                        </a>
                    </div>
                    
                    <!-- Links para outros tipos de login -->
                    <div class="quick-links">
                        <a href="<?= route_to('auth.guardians') ?>" class="quick-link">
                            <i class="fas fa-user-tie"></i> Encarregado
                        </a>
                        <a href="<?= route_to('auth.staff') ?>" class="quick-link">
                            <i class="fas fa-user-cog"></i> Staff
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <small class="text-white-50">
                <i class="fas fa-shield-alt me-1"></i>
                Sistema de Gestão Escolar © <?= date('Y') ?>
            </small>
        </div>
    </div>
    
    <script>
        // Selecionar perfil
        function selectProfile(profile) {
            // Remove active class from all
            document.querySelectorAll('.profile-option').forEach(opt => {
                opt.classList.remove('active');
            });
            
            // Add active class to selected
            document.querySelector(`[data-profile="${profile}"]`).classList.add('active');
            
            // Update hidden input
            document.getElementById('profile_type').value = profile;
            
            // Update form action based on profile
            const form = document.getElementById('loginForm');
            if (profile === 'teacher') {
                form.action = '<?= site_url('auth/teachers/signin') ?>';
            } else if (profile === 'student') {
                form.action = '<?= site_url('auth/students/signin') ?>';
            } else {
                form.action = '<?= site_url('auth/signin') ?>';
            }
        }
        
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Remember last selected profile (optional)
        const savedProfile = localStorage.getItem('selectedProfile');
        if (savedProfile) {
            selectProfile(savedProfile);
        }
        
        // Save selected profile
        document.querySelectorAll('.profile-option').forEach(opt => {
            opt.addEventListener('click', function() {
                localStorage.setItem('selectedProfile', this.dataset.profile);
            });
        });
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>