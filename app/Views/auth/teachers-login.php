<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Login - Sistema Escolar</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
            color: #11998e;
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
            border-color: #11998e;
            box-shadow: 0 0 0 3px rgba(17, 153, 142, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
            box-shadow: 0 5px 20px rgba(17, 153, 142, 0.4);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .login-footer a {
            color: #11998e;
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
        
        .teacher-tip {
            background: #f0f9f4;
            border-left: 4px solid #11998e;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #0a5c4e;
        }
        
        .teacher-tip i {
            color: #11998e;
            margin-right: 5px;
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
    </style>
</head>
<body>
    <div class="login-container">
        <a href="<?= site_url('auth/login') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-chalkboard-teacher"></i>
                <h2>Área do Professor</h2>
                <p>Acesso ao painel docente</p>
            </div>
            
            <div class="login-body">
                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= session('error') ?>
                    </div>
                <?php endif; ?>
                
                <div class="teacher-tip">
                    <i class="fas fa-info-circle"></i>
                    Utilize suas credenciais fornecidas pela escola. Em caso de problemas, contacte a secretaria.
                </div>
                
                <form action="<?= site_url('auth/teachers/signin') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-envelope"></i> Email Institucional
                        </label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   placeholder="professor@escola.ao"
                                   value="<?= old('username') ?>"
                                   autofocus
                                   required>
                        </div>
                        <small class="text-muted">Use seu email institucional</small>
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
                        <i class="fas fa-sign-in-alt"></i> Acessar Painel do Professor
                    </button>
                </form>
                
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