-- --------------------------------------------------------
-- SCRIPT PARA POPULAR BASE DE DADOS - ESCOLA ANGOLANA
-- Dados Acadêmicos de Teste (VERSÃO CORRIGIDA)
-- --------------------------------------------------------

USE escola_angolana;

-- ========================================================
-- 1. ANOS LETIVOS E SEMESTRES
-- ========================================================

-- Ano Letivo 2024
INSERT INTO `tbl_academic_years` (`year_name`, `start_date`, `end_date`, `is_active`, `created_at`) VALUES
('2024', '2024-02-05', '2024-12-13', 1, NOW());

-- Guardar o ID do ano letivo
SET @academic_year_id = LAST_INSERT_ID();

-- Semestres/Trimestres para 2024 (Sistema Trimestral)
INSERT INTO `tbl_semesters` (`academic_year_id`, `semester_name`, `semester_type`, `start_date`, `end_date`, `is_current`, `status`, `created_at`) VALUES
(@academic_year_id, '1º Trimestre 2024', '1º Trimestre', '2024-02-05', '2024-04-26', 1, 'ativo', NOW()),
(@academic_year_id, '2º Trimestre 2024', '2º Trimestre', '2024-05-06', '2024-08-09', 0, 'ativo', NOW()),
(@academic_year_id, '3º Trimestre 2024', '3º Trimestre', '2024-08-19', '2024-12-13', 0, 'ativo', NOW());

SET @semester1_id = LAST_INSERT_ID();
SET @semester2_id = @semester1_id + 1;
SET @semester3_id = @semester1_id + 2;

-- ========================================================
-- 2. PROFESSORES (10 professores)
-- ========================================================

-- Inserir usuários (professores)
INSERT INTO `tbl_users` (`username`, `email`, `password`, `first_name`, `last_name`, `phone`, `role_id`, `user_type`, `is_active`, `created_at`) VALUES
('prof.joao', 'joao.silva@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'João', 'Silva', '+244 923 456 789', 4, 'teacher', 1, NOW()),
('prof.maria', 'maria.santos@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maria', 'Santos', '+244 923 456 790', 4, 'teacher', 1, NOW()),
('prof.manuel', 'manuel.fernandes@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Manuel', 'Fernandes', '+244 923 456 791', 4, 'teacher', 1, NOW()),
('prof.ana', 'ana.martins@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Martins', '+244 923 456 792', 4, 'teacher', 1, NOW()),
('prof.pedro', 'pedro.costa@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pedro', 'Costa', '+244 923 456 793', 4, 'teacher', 1, NOW()),
('prof.isabel', 'isabel.pereira@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Isabel', 'Pereira', '+244 923 456 794', 4, 'teacher', 1, NOW()),
('prof.carlos', 'carlos.rodrigues@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos', 'Rodrigues', '+244 923 456 795', 4, 'teacher', 1, NOW()),
('prof.lucia', 'lucia.almeida@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lúcia', 'Almeida', '+244 923 456 796', 4, 'teacher', 1, NOW()),
('prof.antonio', 'antonio.mendes@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'António', 'Mendes', '+244 923 456 797', 4, 'teacher', 1, NOW()),
('prof.fatima', 'fatima.nunes@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fátima', 'Nunes', '+244 923 456 798', 4, 'teacher', 1, NOW());

-- Guardar IDs dos professores (do último insert)
SET @teacher1_id = LAST_INSERT_ID() - 9;
SET @teacher2_id = @teacher1_id + 1;
SET @teacher3_id = @teacher1_id + 2;
SET @teacher4_id = @teacher1_id + 3;
SET @teacher5_id = @teacher1_id + 4;
SET @teacher6_id = @teacher1_id + 5;
SET @teacher7_id = @teacher1_id + 6;
SET @teacher8_id = @teacher1_id + 7;
SET @teacher9_id = @teacher1_id + 8;
SET @teacher10_id = @teacher1_id + 9;

-- Inserir dados específicos de professores
INSERT INTO `tbl_teachers` (`user_id`, `birth_date`, `gender`, `nationality`, `identity_document`, `qualifications`, `specialization`, `admission_date`, `is_active`) VALUES
(@teacher1_id, '1985-03-15', 'Masculino', 'Angolana', '006543210LA045', 'Licenciatura em Matemática', 'Matemática Pura', '2020-01-10', 1),
(@teacher2_id, '1990-07-22', 'Feminino', 'Angolana', '007654321LA056', 'Licenciatura em Português', 'Literatura', '2021-02-15', 1),
(@teacher3_id, '1982-11-05', 'Masculino', 'Angolana', '005432109LA034', 'Licenciatura em Física', 'Física Geral', '2019-03-20', 1),
(@teacher4_id, '1988-09-18', 'Feminino', 'Angolana', '008765432LA067', 'Licenciatura em Biologia', 'Biologia Celular', '2020-05-10', 1),
(@teacher5_id, '1979-04-30', 'Masculino', 'Angolana', '004321098LA023', 'Licenciatura em História', 'História de Angola', '2018-08-15', 1),
(@teacher6_id, '1992-12-12', 'Feminino', 'Angolana', '009876543LA078', 'Licenciatura em Geografia', 'Geografia Física', '2021-09-01', 1),
(@teacher7_id, '1984-06-25', 'Masculino', 'Angolana', '003210987LA012', 'Licenciatura em Química', 'Química Orgânica', '2019-11-20', 1),
(@teacher8_id, '1991-08-08', 'Feminino', 'Angolana', '001234567LA089', 'Licenciatura em Inglês', 'Língua Inglesa', '2022-01-15', 1),
(@teacher9_id, '1987-02-14', 'Masculino', 'Angolana', '002345678LA090', 'Licenciatura em Educação Física', 'Desporto Escolar', '2020-10-05', 1),
(@teacher10_id, '1989-10-20', 'Feminino', 'Angolana', '003456789LA101', 'Licenciatura em Filosofia', 'Ética e Filosofia', '2021-07-12', 1);

-- ========================================================
-- 3. OBTER IDs DOS NÍVEIS DE ENSINO E DISCIPLINAS
-- ========================================================

-- Obter IDs dos níveis de ensino
SELECT id INTO @ini1_id FROM tbl_grade_levels WHERE level_code = 'INI-01';
SELECT id INTO @ini2_id FROM tbl_grade_levels WHERE level_code = 'INI-02';
SELECT id INTO @ini3_id FROM tbl_grade_levels WHERE level_code = 'INI-03';
SELECT id INTO @pri1_id FROM tbl_grade_levels WHERE level_code = 'PRI-01';
SELECT id INTO @pri2_id FROM tbl_grade_levels WHERE level_code = 'PRI-02';
SELECT id INTO @pri3_id FROM tbl_grade_levels WHERE level_code = 'PRI-03';
SELECT id INTO @pri4_id FROM tbl_grade_levels WHERE level_code = 'PRI-04';
SELECT id INTO @pri5_id FROM tbl_grade_levels WHERE level_code = 'PRI-05';
SELECT id INTO @pri6_id FROM tbl_grade_levels WHERE level_code = 'PRI-06';
SELECT id INTO @cic71_id FROM tbl_grade_levels WHERE level_code = '1CIC-07';
SELECT id INTO @cic81_id FROM tbl_grade_levels WHERE level_code = '1CIC-08';
SELECT id INTO @cic91_id FROM tbl_grade_levels WHERE level_code = '1CIC-09';
SELECT id INTO @cic101_id FROM tbl_grade_levels WHERE level_code = '2CIC-10';
SELECT id INTO @cic111_id FROM tbl_grade_levels WHERE level_code = '2CIC-11';
SELECT id INTO @cic121_id FROM tbl_grade_levels WHERE level_code = '2CIC-12';

-- Obter IDs das disciplinas
SELECT id INTO @lp_id FROM tbl_disciplines WHERE discipline_code = 'LP';
SELECT id INTO @mat_id FROM tbl_disciplines WHERE discipline_code = 'MAT';
SELECT id INTO @cn_id FROM tbl_disciplines WHERE discipline_code = 'CN';
SELECT id INTO @his_id FROM tbl_disciplines WHERE discipline_code = 'HIS';
SELECT id INTO @geo_id FROM tbl_disciplines WHERE discipline_code = 'GEO';
SELECT id INTO @edf_id FROM tbl_disciplines WHERE discipline_code = 'EDF';
SELECT id INTO @emc_id FROM tbl_disciplines WHERE discipline_code = 'EMC';
SELECT id INTO @ing_id FROM tbl_disciplines WHERE discipline_code = 'ING';
SELECT id INTO @fra_id FROM tbl_disciplines WHERE discipline_code = 'FRA';
SELECT id INTO @emp_id FROM tbl_disciplines WHERE discipline_code = 'EMP';
SELECT id INTO @emu_id FROM tbl_disciplines WHERE discipline_code = 'EMU';
SELECT id INTO @fis_id FROM tbl_disciplines WHERE discipline_code = 'FIS';
SELECT id INTO @qui_id FROM tbl_disciplines WHERE discipline_code = 'QUI';
SELECT id INTO @bio_id FROM tbl_disciplines WHERE discipline_code = 'BIO';
SELECT id INTO @tic_id FROM tbl_disciplines WHERE discipline_code = 'TIC';
SELECT id INTO @ln_id FROM tbl_disciplines WHERE discipline_code = 'LN';
SELECT id INTO @fil_id FROM tbl_disciplines WHERE discipline_code = 'FIL';
SELECT id INTO @soc_id FROM tbl_disciplines WHERE discipline_code = 'SOC';
SELECT id INTO @eco_id FROM tbl_disciplines WHERE discipline_code = 'ECO';
SELECT id INTO @cont_id FROM tbl_disciplines WHERE discipline_code = 'CONT';
SELECT id INTO @dir_id FROM tbl_disciplines WHERE discipline_code = 'NDI';
SELECT id INTO @est_id FROM tbl_disciplines WHERE discipline_code = 'EST';
SELECT id INTO @gfd_id FROM tbl_disciplines WHERE discipline_code = 'GF';
SELECT id INTO @geo_cfb_id FROM tbl_disciplines WHERE discipline_code = 'GEO-CFB';

-- Obter IDs dos cursos
SELECT id INTO @cfb_id FROM tbl_courses WHERE course_code = 'CFB' LIMIT 1;
SELECT id INTO @cej_id FROM tbl_courses WHERE course_code = 'CEJ' LIMIT 1;
SELECT id INTO @ch_id FROM tbl_courses WHERE course_code = 'CH' LIMIT 1;

-- ========================================================
-- 4. CURRÍCULO - INICIAÇÃO À 9ª CLASSE
-- ========================================================

-- Associar disciplinas aos níveis de ensino
INSERT INTO `tbl_grade_disciplines` (`grade_level_id`, `discipline_id`, `workload_hours`, `is_mandatory`, `semester`) VALUES
-- INICIAÇÃO (1º, 2º, 3º Ano)
(@ini1_id, @lp_id, 80, 1, 'Anual'),
(@ini1_id, @mat_id, 80, 1, 'Anual'),
(@ini1_id, @cn_id, 60, 1, 'Anual'),
(@ini1_id, @emp_id, 40, 1, 'Anual'),
(@ini1_id, @edf_id, 40, 1, 'Anual'),
(@ini1_id, @emc_id, 30, 1, 'Anual'),

(@ini2_id, @lp_id, 100, 1, 'Anual'),
(@ini2_id, @mat_id, 100, 1, 'Anual'),
(@ini2_id, @cn_id, 70, 1, 'Anual'),
(@ini2_id, @emp_id, 50, 1, 'Anual'),
(@ini2_id, @edf_id, 50, 1, 'Anual'),
(@ini2_id, @emc_id, 40, 1, 'Anual'),

(@ini3_id, @lp_id, 120, 1, 'Anual'),
(@ini3_id, @mat_id, 120, 1, 'Anual'),
(@ini3_id, @cn_id, 80, 1, 'Anual'),
(@ini3_id, @emp_id, 60, 1, 'Anual'),
(@ini3_id, @edf_id, 60, 1, 'Anual'),
(@ini3_id, @emc_id, 50, 1, 'Anual'),

-- ENSINO PRIMÁRIO (1ª a 6ª Classe)
(@pri1_id, @lp_id, 150, 1, 'Anual'),
(@pri1_id, @mat_id, 150, 1, 'Anual'),
(@pri1_id, @cn_id, 90, 1, 'Anual'),
(@pri1_id, @his_id, 60, 1, 'Anual'),
(@pri1_id, @geo_id, 60, 1, 'Anual'),
(@pri1_id, @edf_id, 60, 1, 'Anual'),
(@pri1_id, @emp_id, 60, 1, 'Anual'),
(@pri1_id, @emc_id, 45, 1, 'Anual'),

(@pri2_id, @lp_id, 150, 1, 'Anual'),
(@pri2_id, @mat_id, 150, 1, 'Anual'),
(@pri2_id, @cn_id, 90, 1, 'Anual'),
(@pri2_id, @his_id, 60, 1, 'Anual'),
(@pri2_id, @geo_id, 60, 1, 'Anual'),
(@pri2_id, @edf_id, 60, 1, 'Anual'),
(@pri2_id, @emp_id, 60, 1, 'Anual'),
(@pri2_id, @emc_id, 45, 1, 'Anual'),

(@pri3_id, @lp_id, 150, 1, 'Anual'),
(@pri3_id, @mat_id, 150, 1, 'Anual'),
(@pri3_id, @cn_id, 90, 1, 'Anual'),
(@pri3_id, @his_id, 60, 1, 'Anual'),
(@pri3_id, @geo_id, 60, 1, 'Anual'),
(@pri3_id, @edf_id, 60, 1, 'Anual'),
(@pri3_id, @ing_id, 60, 1, 'Anual'),
(@pri3_id, @emp_id, 60, 1, 'Anual'),

(@pri4_id, @lp_id, 150, 1, 'Anual'),
(@pri4_id, @mat_id, 150, 1, 'Anual'),
(@pri4_id, @cn_id, 90, 1, 'Anual'),
(@pri4_id, @his_id, 60, 1, 'Anual'),
(@pri4_id, @geo_id, 60, 1, 'Anual'),
(@pri4_id, @edf_id, 60, 1, 'Anual'),
(@pri4_id, @ing_id, 60, 1, 'Anual'),
(@pri4_id, @emp_id, 60, 1, 'Anual'),

(@pri5_id, @lp_id, 150, 1, 'Anual'),
(@pri5_id, @mat_id, 150, 1, 'Anual'),
(@pri5_id, @cn_id, 90, 1, 'Anual'),
(@pri5_id, @his_id, 60, 1, 'Anual'),
(@pri5_id, @geo_id, 60, 1, 'Anual'),
(@pri5_id, @edf_id, 60, 1, 'Anual'),
(@pri5_id, @ing_id, 60, 1, 'Anual'),
(@pri5_id, @emp_id, 60, 1, 'Anual'),

(@pri6_id, @lp_id, 150, 1, 'Anual'),
(@pri6_id, @mat_id, 150, 1, 'Anual'),
(@pri6_id, @cn_id, 90, 1, 'Anual'),
(@pri6_id, @his_id, 60, 1, 'Anual'),
(@pri6_id, @geo_id, 60, 1, 'Anual'),
(@pri6_id, @edf_id, 60, 1, 'Anual'),
(@pri6_id, @ing_id, 60, 1, 'Anual'),
(@pri6_id, @fra_id, 60, 0, 'Anual'),

-- I CICLO (7ª a 9ª Classe)
(@cic71_id, @lp_id, 180, 1, 'Anual'),
(@cic71_id, @mat_id, 180, 1, 'Anual'),
(@cic71_id, @fis_id, 90, 1, 'Anual'),
(@cic71_id, @qui_id, 90, 1, 'Anual'),
(@cic71_id, @bio_id, 90, 1, 'Anual'),
(@cic71_id, @his_id, 90, 1, 'Anual'),
(@cic71_id, @geo_id, 90, 1, 'Anual'),
(@cic71_id, @edf_id, 60, 1, 'Anual'),
(@cic71_id, @ing_id, 90, 1, 'Anual'),
(@cic71_id, @fra_id, 90, 1, 'Anual'),
(@cic71_id, @emc_id, 45, 1, 'Anual'),
(@cic71_id, @tic_id, 60, 1, 'Anual'),
(@cic71_id, @ln_id, 60, 0, 'Anual'),

(@cic81_id, @lp_id, 180, 1, 'Anual'),
(@cic81_id, @mat_id, 180, 1, 'Anual'),
(@cic81_id, @fis_id, 90, 1, 'Anual'),
(@cic81_id, @qui_id, 90, 1, 'Anual'),
(@cic81_id, @bio_id, 90, 1, 'Anual'),
(@cic81_id, @his_id, 90, 1, 'Anual'),
(@cic81_id, @geo_id, 90, 1, 'Anual'),
(@cic81_id, @edf_id, 60, 1, 'Anual'),
(@cic81_id, @ing_id, 90, 1, 'Anual'),
(@cic81_id, @fra_id, 90, 1, 'Anual'),
(@cic81_id, @emc_id, 45, 1, 'Anual'),
(@cic81_id, @tic_id, 60, 1, 'Anual'),
(@cic81_id, @ln_id, 60, 0, 'Anual'),

(@cic91_id, @lp_id, 180, 1, 'Anual'),
(@cic91_id, @mat_id, 180, 1, 'Anual'),
(@cic91_id, @fis_id, 90, 1, 'Anual'),
(@cic91_id, @qui_id, 90, 1, 'Anual'),
(@cic91_id, @bio_id, 90, 1, 'Anual'),
(@cic91_id, @his_id, 90, 1, 'Anual'),
(@cic91_id, @geo_id, 90, 1, 'Anual'),
(@cic91_id, @edf_id, 60, 1, 'Anual'),
(@cic91_id, @ing_id, 90, 1, 'Anual'),
(@cic91_id, @fra_id, 90, 1, 'Anual'),
(@cic91_id, @emc_id, 45, 1, 'Anual'),
(@cic91_id, @tic_id, 60, 1, 'Anual'),
(@cic91_id, @ln_id, 60, 0, 'Anual');

-- 5. CURSOS DO II CICLO (10ª - 12ª) - DISCIPLINAS ESPECÍFICAS POR CURSO E NÍVEL
-- ========================================================

-- 5.1 CURSO DE CIÊNCIAS FÍSICAS E BIOLÓGICAS (CFB)
INSERT INTO `tbl_course_disciplines` (`course_id`, `discipline_id`, `grade_level_id`, `workload_hours`, `is_mandatory`, `semester`) VALUES
-- 10ª Classe - CFB
(@cfb_id, @lp_id, @cic101_id, 120, 1, 'Anual'),
(@cfb_id, @mat_id, @cic101_id, 180, 1, 'Anual'),
(@cfb_id, @fis_id, @cic101_id, 150, 1, 'Anual'),
(@cfb_id, @qui_id, @cic101_id, 150, 1, 'Anual'),
(@cfb_id, @bio_id, @cic101_id, 150, 1, 'Anual'),
(@cfb_id, @geo_cfb_id, @cic101_id, 90, 1, 'Anual'),
(@cfb_id, @ing_id, @cic101_id, 90, 1, 'Anual'),
(@cfb_id, @edf_id, @cic101_id, 60, 1, 'Anual'),
(@cfb_id, @tic_id, @cic101_id, 60, 1, 'Anual'),
(@cfb_id, @emc_id, @cic101_id, 45, 1, 'Anual'),

-- 11ª Classe - CFB
(@cfb_id, @lp_id, @cic111_id, 120, 1, 'Anual'),
(@cfb_id, @mat_id, @cic111_id, 180, 1, 'Anual'),
(@cfb_id, @fis_id, @cic111_id, 150, 1, 'Anual'),
(@cfb_id, @qui_id, @cic111_id, 150, 1, 'Anual'),
(@cfb_id, @bio_id, @cic111_id, 150, 1, 'Anual'),
(@cfb_id, @geo_cfb_id, @cic111_id, 90, 1, 'Anual'),
(@cfb_id, @ing_id, @cic111_id, 90, 1, 'Anual'),
(@cfb_id, @edf_id, @cic111_id, 60, 1, 'Anual'),
(@cfb_id, @fil_id, @cic111_id, 60, 1, 'Anual'),

-- 12ª Classe - CFB
(@cfb_id, @lp_id, @cic121_id, 120, 1, 'Anual'),
(@cfb_id, @mat_id, @cic121_id, 180, 1, 'Anual'),
(@cfb_id, @fis_id, @cic121_id, 150, 1, 'Anual'),
(@cfb_id, @qui_id, @cic121_id, 150, 1, 'Anual'),
(@cfb_id, @bio_id, @cic121_id, 150, 1, 'Anual'),
(@cfb_id, @geo_cfb_id, @cic121_id, 90, 1, 'Anual'),
(@cfb_id, @ing_id, @cic121_id, 90, 1, 'Anual'),
(@cfb_id, @fil_id, @cic121_id, 60, 1, 'Anual');

-- 5.2 CURSO DE CIÊNCIAS ECONÓMICAS E JURÍDICAS (CEJ)
INSERT INTO `tbl_course_disciplines` (`course_id`, `discipline_id`, `grade_level_id`, `workload_hours`, `is_mandatory`, `semester`) VALUES
-- 10ª Classe - CEJ
(@cej_id, @lp_id, @cic101_id, 120, 1, 'Anual'),
(@cej_id, @mat_id, @cic101_id, 180, 1, 'Anual'),
(@cej_id, @his_id, @cic101_id, 90, 1, 'Anual'),
(@cej_id, @geo_id, @cic101_id, 90, 1, 'Anual'),
(@cej_id, @eco_id, @cic101_id, 120, 1, 'Anual'),
(@cej_id, @cont_id, @cic101_id, 120, 1, 'Anual'),
(@cej_id, @ing_id, @cic101_id, 90, 1, 'Anual'),
(@cej_id, @edf_id, @cic101_id, 60, 1, 'Anual'),
(@cej_id, @tic_id, @cic101_id, 60, 1, 'Anual'),
(@cej_id, @emc_id, @cic101_id, 45, 1, 'Anual'),

-- 11ª Classe - CEJ
(@cej_id, @lp_id, @cic111_id, 120, 1, 'Anual'),
(@cej_id, @mat_id, @cic111_id, 150, 1, 'Anual'),
(@cej_id, @his_id, @cic111_id, 90, 1, 'Anual'),
(@cej_id, @geo_id, @cic111_id, 90, 1, 'Anual'),
(@cej_id, @eco_id, @cic111_id, 120, 1, 'Anual'),
(@cej_id, @cont_id, @cic111_id, 120, 1, 'Anual'),
(@cej_id, @dir_id, @cic111_id, 90, 1, 'Anual'),
(@cej_id, @est_id, @cic111_id, 90, 1, 'Anual'),
(@cej_id, @ing_id, @cic111_id, 90, 1, 'Anual'),

-- 12ª Classe - CEJ
(@cej_id, @lp_id, @cic121_id, 120, 1, 'Anual'),
(@cej_id, @mat_id, @cic121_id, 150, 1, 'Anual'),
(@cej_id, @his_id, @cic121_id, 90, 1, 'Anual'),
(@cej_id, @geo_id, @cic121_id, 90, 1, 'Anual'),
(@cej_id, @eco_id, @cic121_id, 120, 1, 'Anual'),
(@cej_id, @gfd_id, @cic121_id, 90, 1, 'Anual'),
(@cej_id, @dir_id, @cic121_id, 90, 1, 'Anual'),
(@cej_id, @fil_id, @cic121_id, 60, 1, 'Anual');

-- 5.3 CURSO DE CIÊNCIAS HUMANAS (CH)
INSERT INTO `tbl_course_disciplines` (`course_id`, `discipline_id`, `grade_level_id`, `workload_hours`, `is_mandatory`, `semester`) VALUES
-- 10ª Classe - CH
(@ch_id, @lp_id, @cic101_id, 180, 1, 'Anual'),
(@ch_id, @mat_id, @cic101_id, 120, 1, 'Anual'),
(@ch_id, @his_id, @cic101_id, 150, 1, 'Anual'),
(@ch_id, @geo_id, @cic101_id, 150, 1, 'Anual'),
(@ch_id, @fil_id, @cic101_id, 90, 1, 'Anual'),
(@ch_id, @soc_id, @cic101_id, 90, 1, 'Anual'),
(@ch_id, @ing_id, @cic101_id, 90, 1, 'Anual'),
(@ch_id, @fra_id, @cic101_id, 90, 1, 'Anual'),
(@ch_id, @edf_id, @cic101_id, 60, 1, 'Anual'),
(@ch_id, @emc_id, @cic101_id, 45, 1, 'Anual'),

-- 11ª Classe - CH
(@ch_id, @lp_id, @cic111_id, 180, 1, 'Anual'),
(@ch_id, @mat_id, @cic111_id, 90, 1, 'Anual'),
(@ch_id, @his_id, @cic111_id, 150, 1, 'Anual'),
(@ch_id, @geo_id, @cic111_id, 150, 1, 'Anual'),
(@ch_id, @fil_id, @cic111_id, 90, 1, 'Anual'),
(@ch_id, @soc_id, @cic111_id, 90, 1, 'Anual'),
(@ch_id, @ing_id, @cic111_id, 90, 1, 'Anual'),
(@ch_id, @fra_id, @cic111_id, 90, 1, 'Anual'),
(@ch_id, @ln_id, @cic111_id, 60, 0, 'Anual'),

-- 12ª Classe - CH
(@ch_id, @lp_id, @cic121_id, 180, 1, 'Anual'),
(@ch_id, @his_id, @cic121_id, 150, 1, 'Anual'),
(@ch_id, @geo_id, @cic121_id, 150, 1, 'Anual'),
(@ch_id, @fil_id, @cic121_id, 90, 1, 'Anual'),
(@ch_id, @soc_id, @cic121_id, 90, 1, 'Anual'),
(@ch_id, @ing_id, @cic121_id, 90, 1, 'Anual'),
(@ch_id, @fra_id, @cic121_id, 90, 1, 'Anual');

-- ========================================================
-- 6. CRIAÇÃO DE TURMAS
-- ========================================================

-- 6.1 Turmas de Iniciação
INSERT INTO `tbl_classes` (`class_name`, `class_code`, `grade_level_id`, `academic_year_id`, `class_shift`, `capacity`, `class_teacher_id`, `is_active`, `created_at`) VALUES
('Iniciação 1A', 'INI-01A', @ini1_id, @academic_year_id, 'Manhã', 25, @teacher1_id, 1, NOW()),
('Iniciação 1B', 'INI-01B', @ini1_id, @academic_year_id, 'Tarde', 25, @teacher2_id, 1, NOW()),
('Iniciação 2A', 'INI-02A', @ini2_id, @academic_year_id, 'Manhã', 25, @teacher3_id, 1, NOW()),
('Iniciação 2B', 'INI-02B', @ini2_id, @academic_year_id, 'Tarde', 25, @teacher4_id, 1, NOW()),
('Iniciação 3A', 'INI-03A', @ini3_id, @academic_year_id, 'Manhã', 25, @teacher5_id, 1, NOW()),
('Iniciação 3B', 'INI-03B', @ini3_id, @academic_year_id, 'Tarde', 25, @teacher6_id, 1, NOW());

-- 6.2 Turmas do Ensino Primário (1ª a 6ª)
INSERT INTO `tbl_classes` (`class_name`, `class_code`, `grade_level_id`, `academic_year_id`, `class_shift`, `capacity`, `class_teacher_id`, `is_active`, `created_at`) VALUES
('1ª Classe A', 'PRI-01A', @pri1_id, @academic_year_id, 'Manhã', 30, @teacher1_id, 1, NOW()),
('1ª Classe B', 'PRI-01B', @pri1_id, @academic_year_id, 'Tarde', 30, @teacher2_id, 1, NOW()),
('2ª Classe A', 'PRI-02A', @pri2_id, @academic_year_id, 'Manhã', 30, @teacher3_id, 1, NOW()),
('2ª Classe B', 'PRI-02B', @pri2_id, @academic_year_id, 'Tarde', 30, @teacher4_id, 1, NOW()),
('3ª Classe A', 'PRI-03A', @pri3_id, @academic_year_id, 'Manhã', 30, @teacher5_id, 1, NOW()),
('3ª Classe B', 'PRI-03B', @pri3_id, @academic_year_id, 'Tarde', 30, @teacher6_id, 1, NOW()),
('4ª Classe A', 'PRI-04A', @pri4_id, @academic_year_id, 'Manhã', 30, @teacher7_id, 1, NOW()),
('4ª Classe B', 'PRI-04B', @pri4_id, @academic_year_id, 'Tarde', 30, @teacher8_id, 1, NOW()),
('5ª Classe A', 'PRI-05A', @pri5_id, @academic_year_id, 'Manhã', 30, @teacher9_id, 1, NOW()),
('5ª Classe B', 'PRI-05B', @pri5_id, @academic_year_id, 'Tarde', 30, @teacher10_id, 1, NOW()),
('6ª Classe A', 'PRI-06A', @pri6_id, @academic_year_id, 'Manhã', 30, @teacher1_id, 1, NOW()),
('6ª Classe B', 'PRI-06B', @pri6_id, @academic_year_id, 'Tarde', 30, @teacher2_id, 1, NOW());

-- 6.3 Turmas do I Ciclo (7ª a 9ª)
INSERT INTO `tbl_classes` (`class_name`, `class_code`, `grade_level_id`, `academic_year_id`, `class_shift`, `capacity`, `class_teacher_id`, `is_active`, `created_at`) VALUES
('7ª Classe A', 'CIC-07A', @cic71_id, @academic_year_id, 'Manhã', 35, @teacher3_id, 1, NOW()),
('7ª Classe B', 'CIC-07B', @cic71_id, @academic_year_id, 'Tarde', 35, @teacher4_id, 1, NOW()),
('8ª Classe A', 'CIC-08A', @cic81_id, @academic_year_id, 'Manhã', 35, @teacher5_id, 1, NOW()),
('8ª Classe B', 'CIC-08B', @cic81_id, @academic_year_id, 'Tarde', 35, @teacher6_id, 1, NOW()),
('9ª Classe A', 'CIC-09A', @cic91_id, @academic_year_id, 'Manhã', 35, @teacher7_id, 1, NOW()),
('9ª Classe B', 'CIC-09B', @cic91_id, @academic_year_id, 'Tarde', 35, @teacher8_id, 1, NOW());

-- 6.4 Turmas do II Ciclo - Cursos (10ª a 12ª)
INSERT INTO `tbl_classes` (`class_name`, `class_code`, `grade_level_id`, `academic_year_id`, `course_id`, `class_shift`, `capacity`, `class_teacher_id`, `is_active`, `created_at`) VALUES
-- 10ª Classe
('10ª CFB A', 'CFB-10A', @cic101_id, @academic_year_id, @cfb_id, 'Manhã', 35, @teacher9_id, 1, NOW()),
('10ª CFB B', 'CFB-10B', @cic101_id, @academic_year_id, @cfb_id, 'Tarde', 35, @teacher10_id, 1, NOW()),
('10ª CEJ A', 'CEJ-10A', @cic101_id, @academic_year_id, @cej_id, 'Manhã', 35, @teacher1_id, 1, NOW()),
('10ª CEJ B', 'CEJ-10B', @cic101_id, @academic_year_id, @cej_id, 'Tarde', 35, @teacher2_id, 1, NOW()),
('10ª CH A', 'CH-10A', @cic101_id, @academic_year_id, @ch_id, 'Manhã', 35, @teacher3_id, 1, NOW()),
('10ª CH B', 'CH-10B', @cic101_id, @academic_year_id, @ch_id, 'Tarde', 35, @teacher4_id, 1, NOW()),

-- 11ª Classe
('11ª CFB A', 'CFB-11A', @cic111_id, @academic_year_id, @cfb_id, 'Manhã', 35, @teacher5_id, 1, NOW()),
('11ª CFB B', 'CFB-11B', @cic111_id, @academic_year_id, @cfb_id, 'Tarde', 35, @teacher6_id, 1, NOW()),
('11ª CEJ A', 'CEJ-11A', @cic111_id, @academic_year_id, @cej_id, 'Manhã', 35, @teacher7_id, 1, NOW()),
('11ª CEJ B', 'CEJ-11B', @cic111_id, @academic_year_id, @cej_id, 'Tarde', 35, @teacher8_id, 1, NOW()),
('11ª CH A', 'CH-11A', @cic111_id, @academic_year_id, @ch_id, 'Manhã', 35, @teacher9_id, 1, NOW()),
('11ª CH B', 'CH-11B', @cic111_id, @academic_year_id, @ch_id, 'Tarde', 35, @teacher10_id, 1, NOW()),

-- 12ª Classe
('12ª CFB A', 'CFB-12A', @cic121_id, @academic_year_id, @cfb_id, 'Manhã', 35, @teacher1_id, 1, NOW()),
('12ª CFB B', 'CFB-12B', @cic121_id, @academic_year_id, @cfb_id, 'Tarde', 35, @teacher2_id, 1, NOW()),
('12ª CEJ A', 'CEJ-12A', @cic121_id, @academic_year_id, @cej_id, 'Manhã', 35, @teacher3_id, 1, NOW()),
('12ª CEJ B', 'CEJ-12B', @cic121_id, @academic_year_id, @cej_id, 'Tarde', 35, @teacher4_id, 1, NOW()),
('12ª CH A', 'CH-12A', @cic121_id, @academic_year_id, @ch_id, 'Manhã', 35, @teacher5_id, 1, NOW()),
('12ª CH B', 'CH-12B', @cic121_id, @academic_year_id, @ch_id, 'Tarde', 35, @teacher6_id, 1, NOW());

-- ========================================================
-- 7. CRIAÇÃO DE ALUNOS (50 alunos)
-- ========================================================

-- Guardar IDs das turmas principais para distribuir alunos
SELECT id INTO @turma1 FROM tbl_classes WHERE class_code = 'INI-01A' LIMIT 1;
SELECT id INTO @turma2 FROM tbl_classes WHERE class_code = 'PRI-01A' LIMIT 1;
SELECT id INTO @turma3 FROM tbl_classes WHERE class_code = 'PRI-03A' LIMIT 1;
SELECT id INTO @turma4 FROM tbl_classes WHERE class_code = 'PRI-05A' LIMIT 1;
SELECT id INTO @turma5 FROM tbl_classes WHERE class_code = 'CIC-07A' LIMIT 1;
SELECT id INTO @turma6 FROM tbl_classes WHERE class_code = 'CIC-09A' LIMIT 1;
SELECT id INTO @turma7 FROM tbl_classes WHERE class_code = 'CFB-10A' LIMIT 1;
SELECT id INTO @turma8 FROM tbl_classes WHERE class_code = 'CEJ-10A' LIMIT 1;
SELECT id INTO @turma9 FROM tbl_classes WHERE class_code = 'CH-11A' LIMIT 1;
SELECT id INTO @turma10 FROM tbl_classes WHERE class_code = 'CFB-12A' LIMIT 1;

-- Inserir usuários (alunos)
INSERT INTO `tbl_users` (`username`, `email`, `password`, `first_name`, `last_name`, `phone`, `role_id`, `user_type`, `is_active`, `created_at`) VALUES
('aluno001', 'joao.miguel@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'João', 'Miguel', '+244 923 456 101', 5, 'student', 1, NOW()),
('aluno002', 'maria.luisa@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maria', 'Luísa', '+244 923 456 102', 5, 'student', 1, NOW()),
('aluno003', 'pedro.antonio@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pedro', 'António', '+244 923 456 103', 5, 'student', 1, NOW()),
('aluno004', 'ana.clara@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Clara', '+244 923 456 104', 5, 'student', 1, NOW()),
('aluno005', 'carlos.eduardo@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos', 'Eduardo', '+244 923 456 105', 5, 'student', 1, NOW()),
('aluno006', 'lucia.helena@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lúcia', 'Helena', '+244 923 456 106', 5, 'student', 1, NOW()),
('aluno007', 'antonio.jose@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'António', 'José', '+244 923 456 107', 5, 'student', 1, NOW()),
('aluno008', 'isabel.cristina@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Isabel', 'Cristina', '+244 923 456 108', 5, 'student', 1, NOW()),
('aluno009', 'manuel.fernando@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Manuel', 'Fernando', '+244 923 456 109', 5, 'student', 1, NOW()),
('aluno010', 'fatima.maria@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fátima', 'Maria', '+244 923 456 110', 5, 'student', 1, NOW()),
-- ... (restante dos alunos, igual ao seu script)
('aluno050', 'veronica.luz@aluno.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Verónica', 'Luz', '+244 923 456 150', 5, 'student', 1, NOW());

-- Guardar ID do primeiro aluno
SET @first_aluno_user_id = LAST_INSERT_ID() - 49;

-- Inserir dados específicos de alunos (igual ao seu script)
-- ... (manter a inserção de alunos que você já tem)

-- Guardar IDs dos alunos (tbl_students)
SELECT id INTO @student1_id FROM tbl_students WHERE student_number = 'AL2024001' LIMIT 1;

-- ========================================================
-- 8. MATRÍCULAS (igual ao seu script)
-- ========================================================
-- ... (manter as matrículas que você já tem)

-- ========================================================
-- 9. ATUALIZAR CONFIGURAÇÕES
-- ========================================================

-- Atualizar ano letivo atual nas configurações
UPDATE `tbl_settings` SET `value` = '2024' WHERE `name` = 'current_academic_year';
UPDATE `tbl_settings` SET `value` = '1º Trimestre 2024' WHERE `name` = 'current_semester';

-- ========================================================
-- FIM DO SCRIPT
-- ========================================================
COMMIT;