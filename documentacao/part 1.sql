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
-- 8. ATUALIZAR CONFIGURAÇÕES
-- ========================================================

-- Atualizar ano letivo atual nas configurações
UPDATE `tbl_settings` SET `value` = '1' WHERE `name` = 'current_academic_year';
UPDATE `tbl_settings` SET `value` = '1' WHERE `name` = 'current_semester';

-- ========================================================
-- FIM DO SCRIPT
-- ========================================================
COMMIT;