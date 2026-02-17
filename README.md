# CodeIgniter 4 Framework


Analisando o script SQL, o usuário admin padrão criado é:

sql
-- Criar usuário admin padrão (senha: admin123)
INSERT INTO `tbl_users` (`username`, `email`, `password`, `first_name`, `last_name`, `role_id`, `user_type`, `is_active`) VALUES
('admin', 'admin@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', 1, 'admin', 1);
Credenciais do Admin:
Campo	Valor
Username	admin
Email	admin@escola.ao
Senha	password
Role ID	1 (Administrador)
User Type	admin
Nome	Administrador Sistema
Outros perfis criados (roles):
sql
INSERT INTO `tbl_roles` (`role_name`, `role_description`) VALUES
(1, 'Administrador', 'Acesso total ao sistema'),
(2, 'Diretor', 'Gestão administrativa e pedagógica'),
(3, 'Secretário', 'Gestão de matrículas e documentação'),
(4, 'Professor', 'Gestão de turmas, notas e presenças'),
(5, 'Aluno', 'Acesso ao portal do aluno'),
(6, 'Encarregado', 'Acompanhamento do aluno'),
(7, 'Tesoureiro', 'Gestão financeira'),
(8, 'Bibliotecário', 'Gestão da biblioteca');
Como testar o login:
Acesse: http://localhost:8080/auth/admin

Digite:

Usuário: admin ou admin@escola.ao

Senha: admin123

Para outros perfis (você precisará criar usuários manualmente):

Professor: user_type = 'teacher'

Aluno: user_type = 'student' (com registro em tbl_students)

etc.

Observações importantes:
A senha admin123 está hasheada com password_hash()

O role_id = 1 corresponde a "Administrador"

O user_type = 'admin' identifica o tipo de usuário

O usuário está ativo (is_active = 1)

Se precisar criar mais admins:
sql
-- Criar outro admin (senha: 123456)
INSERT INTO `tbl_users` (`username`, `email`, `password`, `first_name`, `last_name`, `role_id`, `user_type`, `is_active`) VALUES
('admin2', 'admin2@escola.ao', '$2y$10$YourHashedPasswordHere', 'Outro', 'Admin', 1, 'admin', 1);
Para gerar o hash da senha, você pode usar:

php
<?php
echo password_hash('123456', PASSWORD_DEFAULT);
?>




autenticacao
  geral
  admin


Admin
  dashboard
  