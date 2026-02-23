<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aluno Login - Sistema Escolar</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            max-width: 400px;
            width: 90%;
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .login-header i {
            font-size: 60px;
            margin-bottom: 15px;
            background: rgba(255,255,255,0.2);
            width: 100px;
            height: 100px;
            line-height: 100px;
            border-radius: 50%;
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
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 14px;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #f5576c;
            z-index: 1;
        }
        
        .form-control {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #f5576c;
            box-shadow: 0 0 0 3px rgba(245, 87, 108, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(245, 87, 108, 0.4);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .login-footer a {
            color: #f5576c;
            text-decoration: none;
            font-size: 14px;
            margin: 0 10px;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 15px;
            color: white;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .student-info {
            background: #fff0f3;
            border-left: 4px solid #f5576c;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        
        .student-info i {
            color: #f5576c;
            margin-right: 5px;
        }
        
        .student-info p {
            margin: 5px 0;
            color: #a03a48;
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
        
        .help-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
            margin-top: 15px;
            text-align: center;
        }
        
        .help-box small {
            color: #666;
        }
        
        .help-box a {
            color: #f5576c;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <a href="<?= site_url('auth/login') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-user-graduate"></i>
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
                        <ul style="margin:0; padding-left:20px;">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
          <!--       <div class="student-info">
                    <i class="fas fa-lightbulb"></i>
                    <p><strong>Informações de Acesso:</strong></p>
                    <p>Use seu <strong>nome de usuário</strong> ou <strong>email</strong> cadastrado</p>
                    <p>Digite sua <strong>senha</strong> de acesso</p>
                </div>
                 -->
                <form action="<?= site_url('auth/students/signin') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-user"></i> Usuário ou Email ou Número de Matrícula
                        </label>
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   placeholder="Digite seu usuário ou email"
                                   value="<?= old('username') ?>"
                                   autofocus
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
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Acessar Portal do Aluno
                    </button>
                </form>
                
                <!-- <div class="help-box">
                    <small>
                        <i class="fas fa-question-circle"></i>
                        <strong>Primeiro acesso?</strong> Sua senha inicial é sua data de nascimento (DDMMAAAA). 
                        Após o login, você poderá alterá-la.
                    </small>
                </div> -->
                
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
    </div>
</body>
</html>