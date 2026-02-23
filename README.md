# 🏫 OKU ESCOLA - Sistema de Gestão Escolar Angolana

![Versão](https://img.shields.io/badge/versão-1.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-8.3.6-purple)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.7.0-red)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)
![Licença](https://img.shields.io/badge/licença-MIT-green)

## 📋 Sobre o Projeto

O **OKU ESCOLA** é um sistema completo de gestão escolar desenvolvido especificamente para o sistema educacional angolano. O sistema oferece uma solução integrada para administração de escolas, desde a gestão académica até o controle financeiro e documental.

### 🎯 Principais Funcionalidades

- **Gestão Académica**
  - Anos Letivos e Semestres
  - Níveis de Ensino (Iniciação ao Ensino Médio)
  - Turmas e Classes
  - Disciplinas e Cursos
  - Matrículas e Renovações

- **Gestão de Alunos e Professores**
  - Cadastro completo de alunos
  - Gestão de encarregados
  - Perfil de professores com atribuições
  - Histórico académico

- **Avaliações e Exames**
  - Períodos de exame
  - Calendário de exames
  - Lançamento de notas (AC, NPP, NPT)
  - Cálculo de médias
  - Pautas e resultados
  - Boletins automáticos

- **Gestão Financeira**
  - Propinas e taxas
  - Estrutura de pagamentos
  - Faturas e recibos
  - Controle de despesas
  - Múltiplas moedas (AOA, USD, EUR)

- **Central de Documentos**
  - Upload e verificação de documentos
  - Tipos de documentos configuráveis
  - Solicitações de documentos
  - Gerador de documentos (PDF)
  - Certificados e declarações

- **Relatórios e Estatísticas**
  - Relatórios académicos
  - Relatórios financeiros
  - Mapas de presenças
  - Desempenho por turma
  - Exportação de dados

- **Administração do Sistema**
  - Gestão de utilizadores
  - Perfis e permissões (RBAC)
  - Logs de atividades
  - Configurações gerais
  - Backup do sistema

## 🚀 Tecnologias Utilizadas

| Tecnologia | Versão | Descrição |
|------------|--------|-----------|
| PHP | 8.3.6 | Linguagem backend |
| CodeIgniter | 4.7.0 | Framework PHP |
| MySQL | 5.7+ | Banco de dados |
| Bootstrap | 5.3 | Framework frontend |
| jQuery | 3.7 | Biblioteca JavaScript |
| DataTables | 1.13 | Tabelas dinâmicas |
| Chart.js | 4.4 | Gráficos e estatísticas |
| FontAwesome | 6.5 | Ícones |
| DOMPDF | 2.0 | Geração de PDFs |
| SweetAlert2 | 11 | Alertas personalizados |

## 📦 Estrutura do Banco de Dados

O sistema possui mais de 40 tabelas organizadas em módulos:



📁 Base
├── tbl_settings
├── tbl_currencies
├── tbl_users
├── tbl_roles
├── tbl_permissions
└── tbl_user_logs

📁 Académico
├── tbl_academic_years
├── tbl_semesters
├── tbl_grade_levels
├── tbl_classes
├── tbl_disciplines
├── tbl_class_disciplines
├── tbl_courses
└── tbl_course_disciplines

📁 Alunos e Matrículas
├── tbl_students
├── tbl_guardians
├── tbl_student_guardians
├── tbl_enrollments
└── tbl_enrollment_documents

📁 Avaliações
├── tbl_exam_boards
├── tbl_exam_periods
├── tbl_exam_schedules
├── tbl_exam_results
├── tbl_exam_attendance
├── tbl_exam_supervisors
├── tbl_grade_weights
├── tbl_discipline_averages
├── tbl_semester_results
└── tbl_academic_history

📁 Financeiro
├── tbl_fee_types
├── tbl_fee_structure
├── tbl_student_fees
├── tbl_fee_payments
├── tbl_invoices
├── tbl_payments
├── tbl_expense_categories
├── tbl_expenses
├── tbl_taxes
└── tbl_payment_modes

📁 Documentos
├── tbl_documents
├── tbl_document_types
├── tbl_document_requests
├── tbl_generated_documents
└── tbl_requestable_documents

📁 Outros
├── tbl_attendance
├── tbl_report_cards
├── tbl_school_events
├── tbl_products
├── tbl_product_categories
└── tbl_notifications






## 🔧 Instalação

### Pré-requisitos

- PHP 8.3.6 ou superior
- MySQL 5.7 ou superior
- Composer
- Apache/Nginx
- Extensões PHP: mysqli, mbstring, json, curl, gd

### Passos para Instalação

1. **Clone o repositório**
```bash
git clone https://github.com/hu-tao/oku_escola.git
cd oku_escola







Instale as dependências via Composer

composer install

Configure o ambiente
cp env .env
# Edite o arquivo .env com suas configurações

Crie o banco de dados

# Acesse o MySQL
mysql -u root -p

# Execute o script SQL
SOURCE database/oku_escola.sql


Configure as permissões
chmod -R 755 writable/
chmod -R 755 public/uploads/



Configure o servidor web
<VirtualHost *:80>
    ServerName oku-escola.local
    DocumentRoot "/var/www/oku_escola/public"
    
    <Directory "/var/www/oku_escola/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

Para Nginx:
server {
    listen 80;
    server_name oku-escola.local;
    root /var/www/oku_escola/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$args;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
    }
}

Acesse o sistema

URL: http://oku-escola.local
Usuário: admin/admin@escola.ao
Senha: password



📁 Estrutura do Projeto
oku_escola/
├── app/
│   ├── Config/           # Configurações do sistema
│   ├── Controllers/      # Controladores
│   │   ├── admin/        # Controllers administrativos
│   │   ├── auth/         # Autenticação
│   │   ├── students/     # Portal do aluno
│   │   ├── teachers/     # Portal do professor
│   │   └── guardians/    # Portal do encarregado
│   ├── Database/         # Migrações e seeds
│   ├── Filters/          # Filtros (autenticação, permissões)
│   ├── Helpers/          # Helpers personalizados
│   ├── Language/         # Arquivos de idioma
│   ├── Libraries/        # Bibliotecas personalizadas
│   ├── Models/           # Modelos de dados
│   ├── Views/            # Visualizações
│   │   ├── admin/        # Views administrativas
│   │   ├── auth/         # Páginas de login
│   │   ├── errors/       # Páginas de erro
│   │   └── layouts/      # Layouts base
│   └── ThirdParty/       # Bibliotecas de terceiros
├── public/
│   ├── assets/           # CSS, JS, imagens
│   ├── uploads/          # Uploads de usuários
│   └── index.php         # Ponto de entrada
├── writable/
│   ├── cache/            # Cache do sistema
│   ├── logs/             # Logs de atividades
│   ├── session/          # Sessões
│   └── uploads/          # Uploads temporários
├── .env                  # Configurações de ambiente
├── composer.json         # Dependências PHP
└── spark                 # CLI do CodeIgniter



👥 Perfis de Acesso



🔐 Permissões
O sistema utiliza um sistema RBAC (Role-Based Access Control) com mais de 150 permissões distribuídas em módulos:

Dashboard (2 permissões)

Alunos (8 permissões)

Matrículas (8 permissões)

Professores (9 permissões)

Turmas (11 permissões)

Disciplinas (10 permissões)

Cursos (9 permissões)

Exames (18 permissões)

Presenças (6 permissões)

Avaliações (8 permissões)

Financeiro (14 permissões)

Documentos (14 permissões)

Relatórios (13 permissões)

Utilizadores (6 permissões)

Perfis (6 permissões)

Configurações (18 permissões)

Logs (2 permissões)





📊 Funcionalidades por Módulo
🎓 Académico
Gestão de anos letivos

Semestres e trimestres

Níveis de ensino (Iniciação ao Médio)

Turmas e classes

Disciplinas

Cursos (Ensino Médio)

Matriz curricular

👨‍🎓 Alunos
Cadastro completo

Histórico académico

Matrículas

Transferências

Encarregados

Documentos

👨‍🏫 Professores
Cadastro

Atribuição de turmas

Atribuição de disciplinas

Portal do professor

📝 Avaliações
Tipos de exame (AC, NPP, NPT, Exame Final)

Períodos de exame

Calendário de exames

Lançamento de notas

Cálculo de médias

Pautas

Boletins

💰 Financeiro
Propinas

Taxas

Faturas

Pagamentos

Despesas

Relatórios financeiros

📄 Documentos
Upload de documentos

Verificação

Tipos configuráveis

Solicitações

Geração automática (PDF)

📈 Relatórios
Académicos

Financeiros

Presenças

Desempenho

Exportação (CSV, PDF)

⚙️ Administração
Utilizadores

Perfis

Permissões

Logs do sistema

Configurações









🧪 Testes
# Executar testes unitários
vendor/bin/phpunit

# Executar testes com cobertura
vendor/bin/phpunit --coverage-html coverage/




















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
  