-- --------------------------------------------------------
-- SCRIPT PARA POPULAR BASE DE DADOS - ESCOLA ANGOLANA
-- Dados Acadêmicos de Teste (VERSÃO SIMPLIFICADA)
-- --------------------------------------------------------

USE escola_angolana;

-- ========================================================
-- 1. ANOS LETIVOS E SEMESTRES
-- ========================================================

-- Ano Letivo 2024
INSERT INTO `tbl_academic_years` (`year_name`, `start_date`, `end_date`, `is_active`, `created_at`) VALUES
('2024', '2024-02-05', '2024-12-13', 1, NOW());

-- Semestres/Trimestres para 2024 (Sistema Trimestral)
INSERT INTO `tbl_semesters` (`academic_year_id`, `semester_name`, `semester_type`, `start_date`, `end_date`, `is_current`, `status`, `created_at`) VALUES
(1, '1º Trimestre 2024', '1º Trimestre', '2024-02-05', '2024-04-26', 1, 'ativo', NOW()),
(1, '2º Trimestre 2024', '2º Trimestre', '2024-05-06', '2024-08-09', 0, 'ativo', NOW()),
(1, '3º Trimestre 2024', '3º Trimestre', '2024-08-19', '2024-12-13', 0, 'ativo', NOW());

-- ========================================================
-- 2. PROFESSORES (10 professores) - IDs 11 a 20
-- ========================================================

-- Inserir usuários (professores)
INSERT INTO `tbl_users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `phone`, `role_id`, `user_type`, `is_active`, `created_at`) VALUES
(11, 'prof.joao', 'joao.silva@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'João', 'Silva', '+244 923 456 789', 4, 'teacher', 1, NOW()),
(12, 'prof.maria', 'maria.santos@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maria', 'Santos', '+244 923 456 790', 4, 'teacher', 1, NOW()),
(13, 'prof.manuel', 'manuel.fernandes@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Manuel', 'Fernandes', '+244 923 456 791', 4, 'teacher', 1, NOW()),
(14, 'prof.ana', 'ana.martins@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Martins', '+244 923 456 792', 4, 'teacher', 1, NOW()),
(15, 'prof.pedro', 'pedro.costa@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pedro', 'Costa', '+244 923 456 793', 4, 'teacher', 1, NOW()),
(16, 'prof.isabel', 'isabel.pereira@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Isabel', 'Pereira', '+244 923 456 794', 4, 'teacher', 1, NOW()),
(17, 'prof.carlos', 'carlos.rodrigues@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos', 'Rodrigues', '+244 923 456 795', 4, 'teacher', 1, NOW()),
(18, 'prof.lucia', 'lucia.almeida@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lúcia', 'Almeida', '+244 923 456 796', 4, 'teacher', 1, NOW()),
(19, 'prof.antonio', 'antonio.mendes@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'António', 'Mendes', '+244 923 456 797', 4, 'teacher', 1, NOW()),
(20, 'prof.fatima', 'fatima.nunes@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fátima', 'Nunes', '+244 923 456 798', 4, 'teacher', 1, NOW());

-- Inserir dados específicos de professores
INSERT INTO `tbl_teachers` (`user_id`, `birth_date`, `gender`, `nationality`, `identity_document`, `qualifications`, `specialization`, `admission_date`, `is_active`) VALUES
(11, '1985-03-15', 'Masculino', 'Angolana', '006543210LA045', 'Licenciatura em Matemática', 'Matemática Pura', '2020-01-10', 1),
(12, '1990-07-22', 'Feminino', 'Angolana', '007654321LA056', 'Licenciatura em Português', 'Literatura', '2021-02-15', 1),
(13, '1982-11-05', 'Masculino', 'Angolana', '005432109LA034', 'Licenciatura em Física', 'Física Geral', '2019-03-20', 1),
(14, '1988-09-18', 'Feminino', 'Angolana', '008765432LA067', 'Licenciatura em Biologia', 'Biologia Celular', '2020-05-10', 1),
(15, '1979-04-30', 'Masculino', 'Angolana', '004321098LA023', 'Licenciatura em História', 'História de Angola', '2018-08-15', 1),
(16, '1992-12-12', 'Feminino', 'Angolana', '009876543LA078', 'Licenciatura em Geografia', 'Geografia Física', '2021-09-01', 1),
(17, '1984-06-25', 'Masculino', 'Angolana', '003210987LA012', 'Licenciatura em Química', 'Química Orgânica', '2019-11-20', 1),
(18, '1991-08-08', 'Feminino', 'Angolana', '001234567LA089', 'Licenciatura em Inglês', 'Língua Inglesa', '2022-01-15', 1),
(19, '1987-02-14', 'Masculino', 'Angolana', '002345678LA090', 'Licenciatura em Educação Física', 'Desporto Escolar', '2020-10-05', 1),
(20, '1989-10-20', 'Feminino', 'Angolana', '003456789LA101', 'Licenciatura em Filosofia', 'Ética e Filosofia', '2021-07-12', 1);

-- ========================================================
-- 3. CURRÍCULO - INICIAÇÃO À 9ª CLASSE
-- ========================================================

-- Associar disciplinas aos níveis de ensino (usando IDs fixos baseados no sistema atual)
-- INICIAÇÃO (1º, 2º, 3º Ano) - Níveis 1-3, Disciplinas 1-6
INSERT INTO `tbl_grade_disciplines` (`grade_level_id`, `discipline_id`, `workload_hours`, `is_mandatory`, `semester`) VALUES
(1, 1, 80, 1, 'Anual'), (1, 2, 80, 1, 'Anual'), (1, 3, 60, 1, 'Anual'), (1, 10, 40, 1, 'Anual'), (1, 6, 40, 1, 'Anual'), (1, 7, 30, 1, 'Anual'),
(2, 1, 100, 1, 'Anual'), (2, 2, 100, 1, 'Anual'), (2, 3, 70, 1, 'Anual'), (2, 10, 50, 1, 'Anual'), (2, 6, 50, 1, 'Anual'), (2, 7, 40, 1, 'Anual'),
(3, 1, 120, 1, 'Anual'), (3, 2, 120, 1, 'Anual'), (3, 3, 80, 1, 'Anual'), (3, 10, 60, 1, 'Anual'), (3, 6, 60, 1, 'Anual'), (3, 7, 50, 1, 'Anual'),

-- ENSINO PRIMÁRIO (1ª a 6ª Classe) - Níveis 4-9
(4, 1, 150, 1, 'Anual'), (4, 2, 150, 1, 'Anual'), (4, 3, 90, 1, 'Anual'), (4, 4, 60, 1, 'Anual'), (4, 5, 60, 1, 'Anual'), (4, 6, 60, 1, 'Anual'), (4, 10, 60, 1, 'Anual'), (4, 7, 45, 1, 'Anual'),
(5, 1, 150, 1, 'Anual'), (5, 2, 150, 1, 'Anual'), (5, 3, 90, 1, 'Anual'), (5, 4, 60, 1, 'Anual'), (5, 5, 60, 1, 'Anual'), (5, 6, 60, 1, 'Anual'), (5, 10, 60, 1, 'Anual'), (5, 7, 45, 1, 'Anual'),
(6, 1, 150, 1, 'Anual'), (6, 2, 150, 1, 'Anual'), (6, 3, 90, 1, 'Anual'), (6, 4, 60, 1, 'Anual'), (6, 5, 60, 1, 'Anual'), (6, 6, 60, 1, 'Anual'), (6, 8, 60, 1, 'Anual'), (6, 10, 60, 1, 'Anual'),
(7, 1, 150, 1, 'Anual'), (7, 2, 150, 1, 'Anual'), (7, 3, 90, 1, 'Anual'), (7, 4, 60, 1, 'Anual'), (7, 5, 60, 1, 'Anual'), (7, 6, 60, 1, 'Anual'), (7, 8, 60, 1, 'Anual'), (7, 10, 60, 1, 'Anual'),
(8, 1, 150, 1, 'Anual'), (8, 2, 150, 1, 'Anual'), (8, 3, 90, 1, 'Anual'), (8, 4, 60, 1, 'Anual'), (8, 5, 60, 1, 'Anual'), (8, 6, 60, 1, 'Anual'), (8, 8, 60, 1, 'Anual'), (8, 10, 60, 1, 'Anual'),
(9, 1, 150, 1, 'Anual'), (9, 2, 150, 1, 'Anual'), (9, 3, 90, 1, 'Anual'), (9, 4, 60, 1, 'Anual'), (9, 5, 60, 1, 'Anual'), (9, 6, 60, 1, 'Anual'), (9, 8, 60, 1, 'Anual'), (9, 9, 60, 0, 'Anual'),

-- I CICLO (7ª a 9ª Classe) - Níveis 10-12
(10, 1, 180, 1, 'Anual'), (10, 2, 180, 1, 'Anual'), (10, 11, 90, 1, 'Anual'), (10, 12, 90, 1, 'Anual'), (10, 13, 90, 1, 'Anual'), 
(10, 4, 90, 1, 'Anual'), (10, 5, 90, 1, 'Anual'), (10, 6, 60, 1, 'Anual'), (10, 8, 90, 1, 'Anual'), (10, 9, 90, 1, 'Anual'), 
(10, 7, 45, 1, 'Anual'), (10, 15, 60, 1, 'Anual'), (10, 16, 60, 0, 'Anual'),

(11, 1, 180, 1, 'Anual'), (11, 2, 180, 1, 'Anual'), (11, 11, 90, 1, 'Anual'), (11, 12, 90, 1, 'Anual'), (11, 13, 90, 1, 'Anual'), 
(11, 4, 90, 1, 'Anual'), (11, 5, 90, 1, 'Anual'), (11, 6, 60, 1, 'Anual'), (11, 8, 90, 1, 'Anual'), (11, 9, 90, 1, 'Anual'), 
(11, 7, 45, 1, 'Anual'), (11, 15, 60, 1, 'Anual'), (11, 16, 60, 0, 'Anual'),

(12, 1, 180, 1, 'Anual'), (12, 2, 180, 1, 'Anual'), (12, 11, 90, 1, 'Anual'), (12, 12, 90, 1, 'Anual'), (12, 13, 90, 1, 'Anual'), 
(12, 4, 90, 1, 'Anual'), (12, 5, 90, 1, 'Anual'), (12, 6, 60, 1, 'Anual'), (12, 8, 90, 1, 'Anual'), (12, 9, 90, 1, 'Anual'), 
(12, 7, 45, 1, 'Anual'), (12, 15, 60, 1, 'Anual'), (12, 16, 60, 0, 'Anual');

-- ========================================================
-- 4. CURSOS DO II CICLO (10ª - 12ª) - Níveis 13-15
-- ========================================================

-- 4.1 CURSO DE CIÊNCIAS FÍSICAS E BIOLÓGICAS (CFB) - Curso ID 1
INSERT INTO `tbl_course_disciplines` (`course_id`, `discipline_id`, `grade_level_id`, `workload_hours`, `is_mandatory`, `semester`) VALUES
-- 10ª Classe - CFB (Nível 13)
(1, 1, 13, 120, 1, 'Anual'), (1, 2, 13, 180, 1, 'Anual'), (1, 11, 13, 150, 1, 'Anual'), 
(1, 12, 13, 150, 1, 'Anual'), (1, 13, 13, 150, 1, 'Anual'), (1, 24, 13, 90, 1, 'Anual'), 
(1, 8, 13, 90, 1, 'Anual'), (1, 6, 13, 60, 1, 'Anual'), (1, 15, 13, 60, 1, 'Anual'), (1, 7, 13, 45, 1, 'Anual'),

-- 11ª Classe - CFB (Nível 14)
(1, 1, 14, 120, 1, 'Anual'), (1, 2, 14, 180, 1, 'Anual'), (1, 11, 14, 150, 1, 'Anual'), 
(1, 12, 14, 150, 1, 'Anual'), (1, 13, 14, 150, 1, 'Anual'), (1, 24, 14, 90, 1, 'Anual'), 
(1, 8, 14, 90, 1, 'Anual'), (1, 6, 14, 60, 1, 'Anual'), (1, 17, 14, 60, 1, 'Anual'),

-- 12ª Classe - CFB (Nível 15)
(1, 1, 15, 120, 1, 'Anual'), (1, 2, 15, 180, 1, 'Anual'), (1, 11, 15, 150, 1, 'Anual'), 
(1, 12, 15, 150, 1, 'Anual'), (1, 13, 15, 150, 1, 'Anual'), (1, 24, 15, 90, 1, 'Anual'), 
(1, 8, 15, 90, 1, 'Anual'), (1, 17, 15, 60, 1, 'Anual');

-- 4.2 CURSO DE CIÊNCIAS ECONÓMICAS E JURÍDICAS (CEJ) - Curso ID 2
INSERT INTO `tbl_course_disciplines` (`course_id`, `discipline_id`, `grade_level_id`, `workload_hours`, `is_mandatory`, `semester`) VALUES
-- 10ª Classe - CEJ (Nível 13)
(2, 1, 13, 120, 1, 'Anual'), (2, 2, 13, 180, 1, 'Anual'), (2, 4, 13, 90, 1, 'Anual'), 
(2, 5, 13, 90, 1, 'Anual'), (2, 18, 13, 120, 1, 'Anual'), (2, 19, 13, 120, 1, 'Anual'), 
(2, 8, 13, 90, 1, 'Anual'), (2, 6, 13, 60, 1, 'Anual'), (2, 15, 13, 60, 1, 'Anual'), (2, 7, 13, 45, 1, 'Anual'),

-- 11ª Classe - CEJ (Nível 14)
(2, 1, 14, 120, 1, 'Anual'), (2, 2, 14, 150, 1, 'Anual'), (2, 4, 14, 90, 1, 'Anual'), 
(2, 5, 14, 90, 1, 'Anual'), (2, 18, 14, 120, 1, 'Anual'), (2, 19, 14, 120, 1, 'Anual'), 
(2, 20, 14, 90, 1, 'Anual'), (2, 21, 14, 90, 1, 'Anual'), (2, 8, 14, 90, 1, 'Anual'),

-- 12ª Classe - CEJ (Nível 15)
(2, 1, 15, 120, 1, 'Anual'), (2, 2, 15, 150, 1, 'Anual'), (2, 4, 15, 90, 1, 'Anual'), 
(2, 5, 15, 90, 1, 'Anual'), (2, 18, 15, 120, 1, 'Anual'), (2, 22, 15, 90, 1, 'Anual'), 
(2, 20, 15, 90, 1, 'Anual'), (2, 17, 15, 60, 1, 'Anual');

-- 4.3 CURSO DE CIÊNCIAS HUMANAS (CH) - Curso ID 3
INSERT INTO `tbl_course_disciplines` (`course_id`, `discipline_id`, `grade_level_id`, `workload_hours`, `is_mandatory`, `semester`) VALUES
-- 10ª Classe - CH (Nível 13)
(3, 1, 13, 180, 1, 'Anual'), (3, 2, 13, 120, 1, 'Anual'), (3, 4, 13, 150, 1, 'Anual'), 
(3, 5, 13, 150, 1, 'Anual'), (3, 17, 13, 90, 1, 'Anual'), (3, 23, 13, 90, 1, 'Anual'), 
(3, 8, 13, 90, 1, 'Anual'), (3, 9, 13, 90, 1, 'Anual'), (3, 6, 13, 60, 1, 'Anual'), (3, 7, 13, 45, 1, 'Anual'),

-- 11ª Classe - CH (Nível 14)
(3, 1, 14, 180, 1, 'Anual'), (3, 2, 14, 90, 1, 'Anual'), (3, 4, 14, 150, 1, 'Anual'), 
(3, 5, 14, 150, 1, 'Anual'), (3, 17, 14, 90, 1, 'Anual'), (3, 23, 14, 90, 1, 'Anual'), 
(3, 8, 14, 90, 1, 'Anual'), (3, 9, 14, 90, 1, 'Anual'), (3, 16, 14, 60, 0, 'Anual'),

-- 12ª Classe - CH (Nível 15)
(3, 1, 15, 180, 1, 'Anual'), (3, 4, 15, 150, 1, 'Anual'), (3, 5, 15, 150, 1, 'Anual'), 
(3, 17, 15, 90, 1, 'Anual'), (3, 23, 15, 90, 1, 'Anual'), (3, 8, 15, 90, 1, 'Anual'), 
(3, 9, 15, 90, 1, 'Anual');

-- ========================================================
-- 5. CRIAÇÃO DE TURMAS
-- ========================================================

-- 5.1 Turmas de Iniciação (Níveis 1-3)
INSERT INTO `tbl_classes` (`class_name`, `class_code`, `grade_level_id`, `academic_year_id`, `class_shift`, `capacity`, `class_teacher_id`, `is_active`, `created_at`) VALUES
('Iniciação 1A', 'INI-01A', 1, 1, 'Manhã', 25, 11, 1, NOW()),
('Iniciação 1B', 'INI-01B', 1, 1, 'Tarde', 25, 12, 1, NOW()),
('Iniciação 2A', 'INI-02A', 2, 1, 'Manhã', 25, 13, 1, NOW()),
('Iniciação 2B', 'INI-02B', 2, 1, 'Tarde', 25, 14, 1, NOW()),
('Iniciação 3A', 'INI-03A', 3, 1, 'Manhã', 25, 15, 1, NOW()),
('Iniciação 3B', 'INI-03B', 3, 1, 'Tarde', 25, 16, 1, NOW());

-- 5.2 Turmas do Ensino Primário (Níveis 4-9)
INSERT INTO `tbl_classes` (`class_name`, `class_code`, `grade_level_id`, `academic_year_id`, `class_shift`, `capacity`, `class_teacher_id`, `is_active`, `created_at`) VALUES
('1ª Classe A', 'PRI-01A', 4, 1, 'Manhã', 30, 11, 1, NOW()),
('1ª Classe B', 'PRI-01B', 4, 1, 'Tarde', 30, 12, 1, NOW()),
('2ª Classe A', 'PRI-02A', 5, 1, 'Manhã', 30, 13, 1, NOW()),
('2ª Classe B', 'PRI-02B', 5, 1, 'Tarde', 30, 14, 1, NOW()),
('3ª Classe A', 'PRI-03A', 6, 1, 'Manhã', 30, 15, 1, NOW()),
('3ª Classe B', 'PRI-03B', 6, 1, 'Tarde', 30, 16, 1, NOW()),
('4ª Classe A', 'PRI-04A', 7, 1, 'Manhã', 30, 17, 1, NOW()),
('4ª Classe B', 'PRI-04B', 7, 1, 'Tarde', 30, 18, 1, NOW()),
('5ª Classe A', 'PRI-05A', 8, 1, 'Manhã', 30, 19, 1, NOW()),
('5ª Classe B', 'PRI-05B', 8, 1, 'Tarde', 30, 20, 1, NOW()),
('6ª Classe A', 'PRI-06A', 9, 1, 'Manhã', 30, 11, 1, NOW()),
('6ª Classe B', 'PRI-06B', 9, 1, 'Tarde', 30, 12, 1, NOW());

-- 5.3 Turmas do I Ciclo (Níveis 10-12)
INSERT INTO `tbl_classes` (`class_name`, `class_code`, `grade_level_id`, `academic_year_id`, `class_shift`, `capacity`, `class_teacher_id`, `is_active`, `created_at`) VALUES
('7ª Classe A', 'CIC-07A', 10, 1, 'Manhã', 35, 13, 1, NOW()),
('7ª Classe B', 'CIC-07B', 10, 1, 'Tarde', 35, 14, 1, NOW()),
('8ª Classe A', 'CIC-08A', 11, 1, 'Manhã', 35, 15, 1, NOW()),
('8ª Classe B', 'CIC-08B', 11, 1, 'Tarde', 35, 16, 1, NOW()),
('9ª Classe A', 'CIC-09A', 12, 1, 'Manhã', 35, 17, 1, NOW()),
('9ª Classe B', 'CIC-09B', 12, 1, 'Tarde', 35, 18, 1, NOW());

-- 5.4 Turmas do II Ciclo - Cursos (Níveis 13-15)
INSERT INTO `tbl_classes` (`class_name`, `class_code`, `grade_level_id`, `academic_year_id`, `course_id`, `class_shift`, `capacity`, `class_teacher_id`, `is_active`, `created_at`) VALUES
-- 10ª Classe (Nível 13)
('10ª CFB A', 'CFB-10A', 13, 1, 1, 'Manhã', 35, 19, 1, NOW()),
('10ª CFB B', 'CFB-10B', 13, 1, 1, 'Tarde', 35, 20, 1, NOW()),
('10ª CEJ A', 'CEJ-10A', 13, 1, 2, 'Manhã', 35, 11, 1, NOW()),
('10ª CEJ B', 'CEJ-10B', 13, 1, 2, 'Tarde', 35, 12, 1, NOW()),
('10ª CH A', 'CH-10A', 13, 1, 3, 'Manhã', 35, 13, 1, NOW()),
('10ª CH B', 'CH-10B', 13, 1, 3, 'Tarde', 35, 14, 1, NOW()),

-- 11ª Classe (Nível 14)
('11ª CFB A', 'CFB-11A', 14, 1, 1, 'Manhã', 35, 15, 1, NOW()),
('11ª CFB B', 'CFB-11B', 14, 1, 1, 'Tarde', 35, 16, 1, NOW()),
('11ª CEJ A', 'CEJ-11A', 14, 1, 2, 'Manhã', 35, 17, 1, NOW()),
('11ª CEJ B', 'CEJ-11B', 14, 1, 2, 'Tarde', 35, 18, 1, NOW()),
('11ª CH A', 'CH-11A', 14, 1, 3, 'Manhã', 35, 19, 1, NOW()),
('11ª CH B', 'CH-11B', 14, 1, 3, 'Tarde', 35, 20, 1, NOW()),

-- 12ª Classe (Nível 15)
('12ª CFB A', 'CFB-12A', 15, 1, 1, 'Manhã', 35, 11, 1, NOW()),
('12ª CFB B', 'CFB-12B', 15, 1, 1, 'Tarde', 35, 12, 1, NOW()),
('12ª CEJ A', 'CEJ-12A', 15, 1, 2, 'Manhã', 35, 13, 1, NOW()),
('12ª CEJ B', 'CEJ-12B', 15, 1, 2, 'Tarde', 35, 14, 1, NOW()),
('12ª CH A', 'CH-12A', 15, 1, 3, 'Manhã', 35, 15, 1, NOW()),
('12ª CH B', 'CH-12B', 15, 1, 3, 'Tarde', 35, 16, 1, NOW());

-- ========================================================
-- 6. CRIAÇÃO DE ALUNOS (50 alunos) - IDs a partir de 21
-- ========================================================

-- Inserir usuários (alunos) com IDs de 21 a 70
INSERT INTO `tbl_users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `phone`, `role_id`, `user_type`, `is_active`, `created_at`) VALUES
(21, 'aluno001', 'joao.miguel@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'João', 'Miguel', '+244 923 456 101', 5, 'student', 1, NOW()),
(22, 'aluno002', 'maria.luisa@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maria', 'Luísa', '+244 923 456 102', 5, 'student', 1, NOW()),
(23, 'aluno003', 'pedro.antonio@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pedro', 'António', '+244 923 456 103', 5, 'student', 1, NOW()),
(24, 'aluno004', 'ana.clara@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Clara', '+244 923 456 104', 5, 'student', 1, NOW()),
(25, 'aluno005', 'carlos.eduardo@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos', 'Eduardo', '+244 923 456 105', 5, 'student', 1, NOW()),
(26, 'aluno006', 'lucia.helena@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lúcia', 'Helena', '+244 923 456 106', 5, 'student', 1, NOW()),
(27, 'aluno007', 'antonio.jose@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'António', 'José', '+244 923 456 107', 5, 'student', 1, NOW()),
(28, 'aluno008', 'isabel.cristina@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Isabel', 'Cristina', '+244 923 456 108', 5, 'student', 1, NOW()),
(29, 'aluno009', 'manuel.fernando@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Manuel', 'Fernando', '+244 923 456 109', 5, 'student', 1, NOW()),
(30, 'aluno010', 'fatima.maria@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fátima', 'Maria', '+244 923 456 110', 5, 'student', 1, NOW()),
(31, 'aluno011', 'jose.carlos@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'José', 'Carlos', '+244 923 456 111', 5, 'student', 1, NOW()),
(32, 'aluno012', 'mariana.silva@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mariana', 'Silva', '+244 923 456 112', 5, 'student', 1, NOW()),
(33, 'aluno013', 'ricardo.manuel@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ricardo', 'Manuel', '+244 923 456 113', 5, 'student', 1, NOW()),
(34, 'aluno014', 'sofia.andrade@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sofia', 'Andrade', '+244 923 456 114', 5, 'student', 1, NOW()),
(35, 'aluno015', 'miguel.angelo@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Miguel', 'Ângelo', '+244 923 456 115', 5, 'student', 1, NOW()),
(36, 'aluno016', 'beatriz.lima@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Beatriz', 'Lima', '+244 923 456 116', 5, 'student', 1, NOW()),
(37, 'aluno017', 'fernando.alberto@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fernando', 'Alberto', '+244 923 456 117', 5, 'student', 1, NOW()),
(38, 'aluno018', 'carla.simone@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carla', 'Simone', '+244 923 456 118', 5, 'student', 1, NOW()),
(39, 'aluno019', 'paulo.jorge@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Paulo', 'Jorge', '+244 923 456 119', 5, 'student', 1, NOW()),
(40, 'aluno020', 'rita.mendes@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rita', 'Mendes', '+244 923 456 120', 5, 'student', 1, NOW()),
(41, 'aluno021', 'andre.luis@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'André', 'Luís', '+244 923 456 121', 5, 'student', 1, NOW()),
(42, 'aluno022', 'diana.patricia@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Diana', 'Patrícia', '+244 923 456 122', 5, 'student', 1, NOW()),
(43, 'aluno023', 'hugo.miguel@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hugo', 'Miguel', '+244 923 456 123', 5, 'student', 1, NOW()),
(44, 'aluno024', 'ines.martins@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Inês', 'Martins', '+244 923 456 124', 5, 'student', 1, NOW()),
(45, 'aluno025', 'tiago.oliveira@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tiago', 'Oliveira', '+244 923 456 125', 5, 'student', 1, NOW()),
(46, 'aluno026', 'vanessa.santos@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Vanessa', 'Santos', '+244 923 456 126', 5, 'student', 1, NOW()),
(47, 'aluno027', 'daniel.fernandes@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Daniel', 'Fernandes', '+244 923 456 127', 5, 'student', 1, NOW()),
(48, 'aluno028', 'joana.paula@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Joana', 'Paula', '+244 923 456 128', 5, 'student', 1, NOW()),
(49, 'aluno029', 'rui.pedro@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rui', 'Pedro', '+244 923 456 129', 5, 'student', 1, NOW()),
(50, 'aluno030', 'elisa.rosa@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Elisa', 'Rosa', '+244 923 456 130', 5, 'student', 1, NOW()),
(51, 'aluno031', 'nuno.miguel@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nuno', 'Miguel', '+244 923 456 131', 5, 'student', 1, NOW()),
(52, 'aluno032', 'tereza.jesus@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tereza', 'Jesus', '+244 923 456 132', 5, 'student', 1, NOW()),
(53, 'aluno033', 'simao.pedro@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Simão', 'Pedro', '+244 923 456 133', 5, 'student', 1, NOW()),
(54, 'aluno034', 'lurdes.catarina@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lurdes', 'Catarina', '+244 923 456 134', 5, 'student', 1, NOW()),
(55, 'aluno035', 'goncalo.rafael@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gonçalo', 'Rafael', '+244 923 456 135', 5, 'student', 1, NOW()),
(56, 'aluno036', 'cristina.rita@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cristina', 'Rita', '+244 923 456 136', 5, 'student', 1, NOW()),
(57, 'aluno037', 'filipe.andre@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Filipe', 'André', '+244 923 456 137', 5, 'student', 1, NOW()),
(58, 'aluno038', 'margarida.cruz@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Margarida', 'Cruz', '+244 923 456 138', 5, 'student', 1, NOW()),
(59, 'aluno039', 'bruno.cezar@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bruno', 'Cezar', '+244 923 456 139', 5, 'student', 1, NOW()),
(60, 'aluno040', 'sara.luisa@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sara', 'Luísa', '+244 923 456 140', 5, 'student', 1, NOW()),
(61, 'aluno041', 'eduardo.mario@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Eduardo', 'Mário', '+244 923 456 141', 5, 'student', 1, NOW()),
(62, 'aluno042', 'leonor.alice@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Leonor', 'Alice', '+244 923 456 142', 5, 'student', 1, NOW()),
(63, 'aluno043', 'vasco.miguel@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Vasco', 'Miguel', '+244 923 456 143', 5, 'student', 1, NOW()),
(64, 'aluno044', 'patricia.silva@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Patrícia', 'Silva', '+244 923 456 144', 5, 'student', 1, NOW()),
(65, 'aluno045', 'henrique.jose@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Henrique', 'José', '+244 923 456 145', 5, 'student', 1, NOW()),
(66, 'aluno046', 'marta.sofia@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marta', 'Sofia', '+244 923 456 146', 5, 'student', 1, NOW()),
(67, 'aluno047', 'rafael.paulo@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rafael', 'Paulo', '+244 923 456 147', 5, 'student', 1, NOW()),
(68, 'aluno048', 'claudia.ines@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cláudia', 'Inês', '+244 923 456 148', 5, 'student', 1, NOW()),
(69, 'aluno049', 'diogo.nuno@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Diogo', 'Nuno', '+244 923 456 149', 5, 'student', 1, NOW()),
(70, 'aluno050', 'veronica.luz@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Verónica', 'Luz', '+244 923 456 150', 5, 'student', 1, NOW());

-- Inserir dados específicos de alunos (IDs 21-70)
INSERT INTO `tbl_students` (`user_id`, `student_number`, `birth_date`, `gender`, `nationality`, `identity_document`, `address`, `city`, `province`, `phone`, `is_active`) VALUES
(21, 'AL2024001', '2016-05-10', 'Masculino', 'Angolana', '012345678LA001', 'Rua A, 123', 'Luanda', 'Luanda', '+244 923 456 101', 1),
(22, 'AL2024002', '2016-08-15', 'Feminino', 'Angolana', '012345678LA002', 'Rua B, 456', 'Luanda', 'Luanda', '+244 923 456 102', 1),
(23, 'AL2024003', '2015-03-22', 'Masculino', 'Angolana', '012345678LA003', 'Rua C, 789', 'Luanda', 'Luanda', '+244 923 456 103', 1),
(24, 'AL2024004', '2015-11-30', 'Feminino', 'Angolana', '012345678LA004', 'Rua D, 321', 'Luanda', 'Luanda', '+244 923 456 104', 1),
(25, 'AL2024005', '2014-07-18', 'Masculino', 'Angolana', '012345678LA005', 'Rua E, 654', 'Luanda', 'Luanda', '+244 923 456 105', 1),
(26, 'AL2024006', '2014-09-25', 'Feminino', 'Angolana', '012345678LA006', 'Rua F, 987', 'Luanda', 'Luanda', '+244 923 456 106', 1),
(27, 'AL2024007', '2013-02-14', 'Masculino', 'Angolana', '012345678LA007', 'Rua G, 147', 'Luanda', 'Luanda', '+244 923 456 107', 1),
(28, 'AL2024008', '2013-12-03', 'Feminino', 'Angolana', '012345678LA008', 'Rua H, 258', 'Luanda', 'Luanda', '+244 923 456 108', 1),
(29, 'AL2024009', '2012-06-21', 'Masculino', 'Angolana', '012345678LA009', 'Rua I, 369', 'Luanda', 'Luanda', '+244 923 456 109', 1),
(30, 'AL2024010', '2012-10-08', 'Feminino', 'Angolana', '012345678LA010', 'Rua J, 741', 'Luanda', 'Luanda', '+244 923 456 110', 1),
(31, 'AL2024011', '2011-04-17', 'Masculino', 'Angolana', '012345678LA011', 'Rua K, 852', 'Luanda', 'Luanda', '+244 923 456 111', 1),
(32, 'AL2024012', '2011-08-29', 'Feminino', 'Angolana', '012345678LA012', 'Rua L, 963', 'Luanda', 'Luanda', '+244 923 456 112', 1),
(33, 'AL2024013', '2010-01-12', 'Masculino', 'Angolana', '012345678LA013', 'Rua M, 159', 'Luanda', 'Luanda', '+244 923 456 113', 1),
(34, 'AL2024014', '2010-05-27', 'Feminino', 'Angolana', '012345678LA014', 'Rua N, 753', 'Luanda', 'Luanda', '+244 923 456 114', 1),
(35, 'AL2024015', '2009-09-09', 'Masculino', 'Angolana', '012345678LA015', 'Rua O, 951', 'Luanda', 'Luanda', '+244 923 456 115', 1),
(36, 'AL2024016', '2009-11-19', 'Feminino', 'Angolana', '012345678LA016', 'Rua P, 357', 'Luanda', 'Luanda', '+244 923 456 116', 1),
(37, 'AL2024017', '2008-03-03', 'Masculino', 'Angolana', '012345678LA017', 'Rua Q, 159', 'Luanda', 'Luanda', '+244 923 456 117', 1),
(38, 'AL2024018', '2008-07-22', 'Feminino', 'Angolana', '012345678LA018', 'Rua R, 753', 'Luanda', 'Luanda', '+244 923 456 118', 1),
(39, 'AL2024019', '2007-12-11', 'Masculino', 'Angolana', '012345678LA019', 'Rua S, 951', 'Luanda', 'Luanda', '+244 923 456 119', 1),
(40, 'AL2024020', '2007-04-30', 'Feminino', 'Angolana', '012345678LA020', 'Rua T, 357', 'Luanda', 'Luanda', '+244 923 456 120', 1),
(41, 'AL2024021', '2006-08-14', 'Masculino', 'Angolana', '012345678LA021', 'Rua U, 159', 'Luanda', 'Luanda', '+244 923 456 121', 1),
(42, 'AL2024022', '2006-10-05', 'Feminino', 'Angolana', '012345678LA022', 'Rua V, 753', 'Luanda', 'Luanda', '+244 923 456 122', 1),
(43, 'AL2024023', '2005-02-18', 'Masculino', 'Angolana', '012345678LA023', 'Rua W, 951', 'Luanda', 'Luanda', '+244 923 456 123', 1),
(44, 'AL2024024', '2005-06-29', 'Feminino', 'Angolana', '012345678LA024', 'Rua X, 357', 'Luanda', 'Luanda', '+244 923 456 124', 1),
(45, 'AL2024025', '2004-09-12', 'Masculino', 'Angolana', '012345678LA025', 'Rua Y, 159', 'Luanda', 'Luanda', '+244 923 456 125', 1),
(46, 'AL2024026', '2004-12-20', 'Feminino', 'Angolana', '012345678LA026', 'Rua Z, 753', 'Luanda', 'Luanda', '+244 923 456 126', 1),
(47, 'AL2024027', '2003-03-07', 'Masculino', 'Angolana', '012345678LA027', 'Av. 1, 123', 'Luanda', 'Luanda', '+244 923 456 127', 1),
(48, 'AL2024028', '2003-07-15', 'Feminino', 'Angolana', '012345678LA028', 'Av. 2, 456', 'Luanda', 'Luanda', '+244 923 456 128', 1),
(49, 'AL2024029', '2002-11-23', 'Masculino', 'Angolana', '012345678LA029', 'Av. 3, 789', 'Luanda', 'Luanda', '+244 923 456 129', 1),
(50, 'AL2024030', '2002-05-01', 'Feminino', 'Angolana', '012345678LA030', 'Av. 4, 321', 'Luanda', 'Luanda', '+244 923 456 130', 1),
(51, 'AL2024031', '2001-08-09', 'Masculino', 'Angolana', '012345678LA031', 'Av. 5, 654', 'Luanda', 'Luanda', '+244 923 456 131', 1),
(52, 'AL2024032', '2001-10-17', 'Feminino', 'Angolana', '012345678LA032', 'Av. 6, 987', 'Luanda', 'Luanda', '+244 923 456 132', 1),
(53, 'AL2024033', '2000-12-25', 'Masculino', 'Angolana', '012345678LA033', 'Av. 7, 147', 'Luanda', 'Luanda', '+244 923 456 133', 1),
(54, 'AL2024034', '2000-04-02', 'Feminino', 'Angolana', '012345678LA034', 'Av. 8, 258', 'Luanda', 'Luanda', '+244 923 456 134', 1),
(55, 'AL2024035', '1999-07-11', 'Masculino', 'Angolana', '012345678LA035', 'Av. 9, 369', 'Luanda', 'Luanda', '+244 923 456 135', 1),
(56, 'AL2024036', '1999-09-19', 'Feminino', 'Angolana', '012345678LA036', 'Av. 10, 741', 'Luanda', 'Luanda', '+244 923 456 136', 1),
(57, 'AL2024037', '1998-01-28', 'Masculino', 'Angolana', '012345678LA037', 'Av. 11, 852', 'Luanda', 'Luanda', '+244 923 456 137', 1),
(58, 'AL2024038', '1998-06-06', 'Feminino', 'Angolana', '012345678LA038', 'Av. 12, 963', 'Luanda', 'Luanda', '+244 923 456 138', 1),
(59, 'AL2024039', '1997-10-14', 'Masculino', 'Angolana', '012345678LA039', 'Av. 13, 159', 'Luanda', 'Luanda', '+244 923 456 139', 1),
(60, 'AL2024040', '1997-12-22', 'Feminino', 'Angolana', '012345678LA040', 'Av. 14, 753', 'Luanda', 'Luanda', '+244 923 456 140', 1),
(61, 'AL2024041', '1996-03-30', 'Masculino', 'Angolana', '012345678LA041', 'Av. 15, 951', 'Luanda', 'Luanda', '+244 923 456 141', 1),
(62, 'AL2024042', '1996-07-08', 'Feminino', 'Angolana', '012345678LA042', 'Av. 16, 357', 'Luanda', 'Luanda', '+244 923 456 142', 1),
(63, 'AL2024043', '1995-11-16', 'Masculino', 'Angolana', '012345678LA043', 'Av. 17, 159', 'Luanda', 'Luanda', '+244 923 456 143', 1),
(64, 'AL2024044', '1995-01-24', 'Feminino', 'Angolana', '012345678LA044', 'Av. 18, 753', 'Luanda', 'Luanda', '+244 923 456 144', 1),
(65, 'AL2024045', '1994-05-03', 'Masculino', 'Angolana', '012345678LA045', 'Av. 19, 951', 'Luanda', 'Luanda', '+244 923 456 145', 1),
(66, 'AL2024046', '1994-09-11', 'Feminino', 'Angolana', '012345678LA046', 'Av. 20, 357', 'Luanda', 'Luanda', '+244 923 456 146', 1),
(67, 'AL2024047', '1993-12-19', 'Masculino', 'Angolana', '012345678LA047', 'Rua 21, 123', 'Luanda', 'Luanda', '+244 923 456 147', 1),
(68, 'AL2024048', '1993-02-27', 'Feminino', 'Angolana', '012345678LA048', 'Rua 22, 456', 'Luanda', 'Luanda', '+244 923 456 148', 1),
(69, 'AL2024049', '1992-06-07', 'Masculino', 'Angolana', '012345678LA049', 'Rua 23, 789', 'Luanda', 'Luanda', '+244 923 456 149', 1),
(70, 'AL2024050', '1992-10-15', 'Feminino', 'Angolana', '012345678LA050', 'Rua 24, 321', 'Luanda', 'Luanda', '+244 923 456 150', 1);

-- ========================================================
-- 7. MATRÍCULAS - Usando os IDs da tabela tbl_students
-- ========================================================

-- Distribuir alunos pelas turmas (5 alunos por turma nas turmas selecionadas)
INSERT INTO `tbl_enrollments` (`student_id`, `class_id`, `academic_year_id`, `enrollment_date`, `enrollment_number`, `enrollment_type`, `grade_level_id`, `status`, `created_at`) VALUES
-- Turma INI-01A (class_id 1) - Alunos com student_id 1-5
(1, 1, 1, '2024-02-01', 'MAT20240001', 'Nova', 1, 'Ativo', NOW()),
(2, 1, 1, '2024-02-01', 'MAT20240002', 'Nova', 1, 'Ativo', NOW()),
(3, 1, 1, '2024-02-01', 'MAT20240003', 'Nova', 1, 'Ativo', NOW()),
(4, 1, 1, '2024-02-01', 'MAT20240004', 'Nova', 1, 'Ativo', NOW()),
(5, 1, 1, '2024-02-01', 'MAT20240005', 'Nova', 1, 'Ativo', NOW()),

-- Turma PRI-01A (class_id 7) - Alunos com student_id 6-10
(6, 7, 1, '2024-02-01', 'MAT20240006', 'Nova', 4, 'Ativo', NOW()),
(7, 7, 1, '2024-02-01', 'MAT20240007', 'Nova', 4, 'Ativo', NOW()),
(8, 7, 1, '2024-02-01', 'MAT20240008', 'Nova', 4, 'Ativo', NOW()),
(9, 7, 1, '2024-02-01', 'MAT20240009', 'Nova', 4, 'Ativo', NOW()),
(10, 7, 1, '2024-02-01', 'MAT20240010', 'Nova', 4, 'Ativo', NOW()),

-- Turma PRI-03A (class_id 9) - Alunos com student_id 11-15
(11, 9, 1, '2024-02-01', 'MAT20240011', 'Nova', 6, 'Ativo', NOW()),
(12, 9, 1, '2024-02-01', 'MAT20240012', 'Nova', 6, 'Ativo', NOW()),
(13, 9, 1, '2024-02-01', 'MAT20240013', 'Nova', 6, 'Ativo', NOW()),
(14, 9, 1, '2024-02-01', 'MAT20240014', 'Nova', 6, 'Ativo', NOW()),
(15, 9, 1, '2024-02-01', 'MAT20240015', 'Nova', 6, 'Ativo', NOW()),

-- Turma PRI-05A (class_id 11) - Alunos com student_id 16-20
(16, 11, 1, '2024-02-01', 'MAT20240016', 'Nova', 8, 'Ativo', NOW()),
(17, 11, 1, '2024-02-01', 'MAT20240017', 'Nova', 8, 'Ativo', NOW()),
(18, 11, 1, '2024-02-01', 'MAT20240018', 'Nova', 8, 'Ativo', NOW()),
(19, 11, 1, '2024-02-01', 'MAT20240019', 'Nova', 8, 'Ativo', NOW()),
(20, 11, 1, '2024-02-01', 'MAT20240020', 'Nova', 8, 'Ativo', NOW()),

-- Turma CIC-07A (class_id 13) - Alunos com student_id 21-25
(21, 13, 1, '2024-02-01', 'MAT20240021', 'Nova', 10, 'Ativo', NOW()),
(22, 13, 1, '2024-02-01', 'MAT20240022', 'Nova', 10, 'Ativo', NOW()),
(23, 13, 1, '2024-02-01', 'MAT20240023', 'Nova', 10, 'Ativo', NOW()),
(24, 13, 1, '2024-02-01', 'MAT20240024', 'Nova', 10, 'Ativo', NOW()),
(25, 13, 1, '2024-02-01', 'MAT20240025', 'Nova', 10, 'Ativo', NOW()),

-- Turma CIC-09A (class_id 15) - Alunos com student_id 26-30
(26, 15, 1, '2024-02-01', 'MAT20240026', 'Nova', 12, 'Ativo', NOW()),
(27, 15, 1, '2024-02-01', 'MAT20240027', 'Nova', 12, 'Ativo', NOW()),
(28, 15, 1, '2024-02-01', 'MAT20240028', 'Nova', 12, 'Ativo', NOW()),
(29, 15, 1, '2024-02-01', 'MAT20240029', 'Nova', 12, 'Ativo', NOW()),
(30, 15, 1, '2024-02-01', 'MAT20240030', 'Nova', 12, 'Ativo', NOW()),

-- Turma CFB-10A (class_id 17) - Alunos com student_id 31-35
(31, 17, 1, '2024-02-01', 'MAT20240031', 'Nova', 13, 'Ativo', NOW()),
(32, 17, 1, '2024-02-01', 'MAT20240032', 'Nova', 13, 'Ativo', NOW()),
(33, 17, 1, '2024-02-01', 'MAT20240033', 'Nova', 13, 'Ativo', NOW()),
(34, 17, 1, '2024-02-01', 'MAT20240034', 'Nova', 13, 'Ativo', NOW()),
(35, 17, 1, '2024-02-01', 'MAT20240035', 'Nova', 13, 'Ativo', NOW()),

-- Turma CEJ-10A (class_id 19) - Alunos com student_id 36-40
(36, 19, 1, '2024-02-01', 'MAT20240036', 'Nova', 13, 'Ativo', NOW()),
(37, 19, 1, '2024-02-01', 'MAT20240037', 'Nova', 13, 'Ativo', NOW()),
(38, 19, 1, '2024-02-01', 'MAT20240038', 'Nova', 13, 'Ativo', NOW()),
(39, 19, 1, '2024-02-01', 'MAT20240039', 'Nova', 13, 'Ativo', NOW()),
(40, 19, 1, '2024-02-01', 'MAT20240040', 'Nova', 13, 'Ativo', NOW()),

-- Turma CH-11A (class_id 23) - Alunos com student_id 41-45
(41, 23, 1, '2024-02-01', 'MAT20240041', 'Nova', 14, 'Ativo', NOW()),
(42, 23, 1, '2024-02-01', 'MAT20240042', 'Nova', 14, 'Ativo', NOW()),
(43, 23, 1, '2024-02-01', 'MAT20240043', 'Nova', 14, 'Ativo', NOW()),
(44, 23, 1, '2024-02-01', 'MAT20240044', 'Nova', 14, 'Ativo', NOW()),
(45, 23, 1, '2024-02-01', 'MAT20240045', 'Nova', 14, 'Ativo', NOW()),

-- Turma CFB-12A (class_id 25) - Alunos com student_id 46-50
(46, 25, 1, '2024-02-01', 'MAT20240046', 'Nova', 15, 'Ativo', NOW()),
(47, 25, 1, '2024-02-01', 'MAT20240047', 'Nova', 15, 'Ativo', NOW()),
(48, 25, 1, '2024-02-01', 'MAT20240048', 'Nova', 15, 'Ativo', NOW()),
(49, 25, 1, '2024-02-01', 'MAT20240049', 'Nova', 15, 'Ativo', NOW()),
(50, 25, 1, '2024-02-01', 'MAT20240050', 'Nova', 15, 'Ativo', NOW());

-- ========================================================
-- 8. ATUALIZAR CONFIGURAÇÕES
-- ========================================================

-- Atualizar ano letivo atual nas configurações
UPDATE `tbl_settings` SET `value` = '2024' WHERE `name` = 'current_academic_year';
UPDATE `tbl_settings` SET `value` = '1º Trimestre 2024' WHERE `name` = 'current_semester';

-- ========================================================
-- FIM DO SCRIPT
-- ========================================================
COMMIT;