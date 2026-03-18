# 📚 Sistema de Gestão Escolar Angolano

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-red.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

## 📋 Visão Geral

Sistema completo de gestão escolar desenvolvido especificamente para o **modelo de ensino angolano**, contemplando todas as particularidades do sistema educacional de Angola, incluindo:

- **Sistema de avaliação de 0-20 valores**
- **Ensino estruturado em:** Iniciação, Primário, 1º Ciclo, 2º Ciclo e Ensino Médio
- **Gestão de cursos:** Ciências, Humanidades, Económico-Jurídico, Técnico-Profissional
- **Avaliações:** AC (Avaliação Contínua), NPP (Prova Professor), NPT (Prova Trimestral)
- **Cálculos:** MT (Média do Trimestre), MFD (Média Final da Disciplina)
- **Resultados:** Transita, Não Transita, Recurso

---

## 🎯 Funcionalidades Principais

### 👨‍🎓 Gestão Académica
- [x] Cadastro completo de alunos com documentação (BI, Certidão, etc.)
- [x] Matrículas e renovações
- [x] Histórico académico por ano letivo
- [x] Transferências entre escolas
- [x] Certificados e declarações

### 👨‍🏫 Gestão de Professores
- [x] Cadastro de professores com habilitações
- [x] Atribuição de disciplinas por turma
- [x] Registo de notas e presenças
- [x] Visualização de pautas

### 📝 Pautas e Avaliações
- [x] Mini pautas por avaliação (AC, NPP, NPT)
- [x] Pautas trimestrais/semestrais
- [x] Pauta final anual
- [x] Cálculo automático de médias
- [x] Processamento de resultados (Transita/Não Transita/Recurso)
- [x] Exportação para Excel
- [x] Impressão de pautas

### 💰 Gestão Financeira
- [x] Propinas mensais
- [x] Taxas de matrícula e exames
- [x] Multas por atraso
- [x] Recibos e faturas
- [x] Gestão de despesas
- [x] Múltiplas moedas (AOA, USD, EUR)

### 👨‍👩‍👧‍👦 Encarregados de Educação
- [x] Cadastro de encarregados
- [x] Vinculação múltipla a alunos
- [x] Acesso ao portal do encarregado
- [x] Acompanhamento de notas e propinas

### 📅 Calendário Escolar
- [x] Anos letivos
- [x] Semestres/Trimestres
- [x] Períodos de exames
- [x] Feriados e eventos
- [x] Calendário de avaliações

### 📊 Relatórios
- [x] Pautas por turma/período
- [x] Histórico do aluno
- [x] Estatísticas de aprovação
- [x] Relatórios financeiros
- [x] Mapas de notas

---

## 🏗️ Estrutura do Sistema

### Modelo de Avaliação Angolano
┌─────────────────────────────────────────────────────┐
│ AVALIAÇÕES │
├─────────────────────────────────────────────────────┤
│ AC = Avaliação Contínua (múltiplas notas) │
│ NPP = Nota da Prova do Professor (1 nota) │
│ NPT = Nota da Prova Trimestral (1 nota) │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ CÁLCULOS │
├─────────────────────────────────────────────────────┤
│ MT = Média do Trimestre │
│ = (Média AC + NPP + NPT) / 3 │
│ │
│ MFD = Média Final da Disciplina │
│ = (MT1 + MT2 + MT3) / 3 (ou MT1 + MT2 / 2) │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ RESULTADOS │
├─────────────────────────────────────────────────────┤
│ Aprovado = MFD ≥ 10 │
│ Recurso = MFD entre 7 e 9.9 │
│ Reprovado = MFD < 7 │
│ │
│ TRANSITA = Aprovado em todas disciplinas │
│ RECURSO = Até 2 disciplinas em recurso │
│ NÃO TRANSITA = 3+ disciplinas recurso ou reprovada │
└─────────────────────────────────────────────────────┘


### Níveis de Ensino

| Nível | Classes | Duração |
|-------|---------|---------|
| **Iniciação** | 1º - 3º Ano | 3 anos |
| **Ensino Primário** | 1ª - 6ª Classe | 6 anos |
| **1º Ciclo** | 7ª - 9ª Classe | 3 anos |
| **2º Ciclo** | 10ª - 12ª/13ª Classe | 3-4 anos |
| **Ensino Médio** | 10ª - 13ª Classe | 4 anos |

---

## 💻 Requisitos Técnicos

### Servidor
- **PHP:** 7.4 ou superior
- **MySQL:** 5.7 ou superior
- **Web Server:** Apache / Nginx
- **Memória:** Mínimo 512MB (recomendado 1GB)

### Dependências PHP
- CodeIgniter 4.x
- PhpSpreadsheet (exportação Excel)
- MPDF (geração de PDFs)
- PHPMailer (envio de emails)

### Navegadores Suportados
- Chrome (últimas 2 versões)
- Firefox (últimas 2 versões)
- Edge (últimas 2 versões)
- Safari (últimas 2 versões)

---

## 📦 Instalação

### 1. Clone o repositório
```bash
git clone https://github.com/seu-usuario/sistema-escolar-angolano.git
cd sistema-escolar-angolano
# Acesse o MySQL
mysql -u root -p

# Crie o banco de dados
CREATE DATABASE escola_angolana CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Importe a estrutura
mysql -u root -p escola_angolana < database/escola_angolana.sql

# Copie o arquivo de configuração
cp .env.example .env

# Edite com suas configurações
nano .env

# Configurações principais:
# database.default.hostname = localhost
# database.default.database = escola_angolana
# database.default.username = root
# database.default.password = sua_senha
# database.default.DBDriver = MySQLi

# Exemplo para Apache (httpd.conf ou .htaccess)
<VirtualHost *:80>
    ServerName escola.local
    DocumentRoot /var/www/sistema-escolar-angolano/public
    
    <Directory /var/www/sistema-escolar-angolano/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

# Ajuste permissões das pastas
chmod -R 755 writable/
chmod -R 755 public/uploads/

URL: http://escola.local
Usuário: admin
Senha: admin123


sistema-escolar-angolano/
├── app/
│   ├── Config/           # Configurações do sistema
│   ├── Controllers/      
│   │   ├── admin/        # Controllers administrativos
│   │   └── ...           
│   ├── Models/           # Models do banco de dados
│   ├── Views/            
│   │   ├── admin/        # Views administrativas
│   │   └── ...
│   ├── Database/         
│   │   ├── Migrations/   # Migrações do banco
│   │   └── Seeds/        # Dados iniciais
│   ├── Libraries/        # Bibliotecas personalizadas
│   └── Helpers/          # Funções auxiliares
├── public/
│   ├── assets/           
│   │   ├── css/
│   │   ├── js/
│   │   ├── images/
│   │   └── plugins/
│   └── uploads/          # Arquivos enviados
├── writable/             # Arquivos temporários
├── database/
│   └── escola_angolana.sql  # Script completo do BD
└── .env                  # Configurações locais




 Fluxos Principais


  Pauta Trimestral

  1. Professor acessa Mini Pautas
2. Seleciona: Turma + Disciplina + Período
3. Regista notas AC, NPP, NPT por aluno
4. Sistema calcula MT automaticamente
5. Coordenador finaliza período
6. Resultados salvos em semester_results

Pauta Final Anual

1. Sistema calcula MFD por disciplina
2. Determina resultado por aluno
3. Diretor revisa e aprova
4. Atualiza enrollment com resultado
5. Gera histórico acadêmico
6. Processa progressão/retenção

Gestão de Propinas
1. Estrutura de taxas definida por ano/nível
2. Sistema gera faturas automaticamente
3. Encarregado efetua pagamento
4. Tesoureiro regista pagamento
5. Recibo emitido automaticamente
6. Notificações enviadas


Níveis de Acesso

Perfil	Acesso

Administrador	Acesso total ao sistema
Diretor	Gestão académica e relatórios
Secretário	Matrículas e documentação
Tesoureiro	Gestão financeira
Professor	Notas, presenças, pautas
Aluno	Portal do aluno
Encarregado	Acompanhamento do aluno


 Principais Relatórios
Académicos
Pauta trimestral por turma

Pauta final anual

Histórico do aluno

Mapa de notas por disciplina

Estatísticas de aprovação

Lista de alunos por turma

Financeiros
Extrato de propinas por aluno

Receitas por período

Despesas por categoria

Inadimplência

Fluxo de caixa

Administrativos
Matrículas por período

Transferências

Certificados emitidos

Frequência de alunos




2º TRIMESTRE (períodos 4, 5, 6)
❌ AC - 2º Trimestre (período_id = 4)

❌ NPP - 2º Trimestre (período_id = 5)

❌ NPT - 2º Trimestre (período_id = 6)

3º TRIMESTRE (períodos 7, 8, 9)
❌ AC - 3º Trimestre (período_id = 7)

❌ NPP - 3º Trimestre (período_id = 8)

❌ EX-FIN - 3º Trimestre (período_id = 9)

RECURSO (períodos 10, 11, 12) - Opcional
❌ REC - 1º Trimestre (período_id = 10)

❌ REC - 2º Trimestre (período_id = 11)

❌ REC - 3º Trimestre (período_id = 12)