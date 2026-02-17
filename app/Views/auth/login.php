<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Escolar</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            max-width: 500px;
            width: 90%;
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .login-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .login-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .login-body {
            padding: 40px;
        }
        
        .profile-selector {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .profile-option {
            text-align: center;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .profile-option:hover {
            border-color: #667eea;
            transform: translateY(-5px);
        }
        
        .profile-option.active {
            border-color: #667eea;
            background: #f0f2ff;
        }
        
        .profile-option i {
            font-size: 32px;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .profile-option span {
            display: block;
            font-weight: 500;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            z-index: 1;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-footer a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
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
                        <i class="fas fa-user-tie"></i>
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
                        <label for="username">
                            <i class="fas fa-user"></i> Usuário / Email
                        </label>
                        <div class="input-group">
                            <i class="fas fa-user"></i>
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
                        <label for="password">
                            <i class="fas fa-lock"></i> Senha
                        </label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Digite sua senha"
                                   required>
                            <button type="button" 
                                    class="btn-toggle-password" 
                                    onclick="togglePassword()"
                                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
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
                </form>
            </div>
        </div>
        
        <div class="text-center mt-3" style="color: white;">
            <small>&copy; 2024 Sistema Escolar Angolano. Todos os direitos reservados.</small>
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