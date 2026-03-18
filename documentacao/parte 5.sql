-- ========================================================
-- SCRIPT PARA INSERIR PRESENÇAS E NOTAS DOS EXAMES
-- COM BASE NOS IDs REAIS DA TABELA tbl_exam_schedules
-- ========================================================

USE escola_angolana;

-- ========================================================
-- 1. PRESENÇAS EM EXAMES (tbl_exam_attendance)
-- ========================================================

-- Registrar presenças para as Provas do Professor (NPP) - IDs 1 a 18
INSERT INTO `tbl_exam_attendance` (`exam_schedule_id`, `enrollment_id`, `attended`, `check_in_time`, `check_in_method`, `recorded_by`, `created_at`) VALUES
-- Turma INI-01A (class_id 1) - exam_schedule_id 1,2,3
(1, 1, 1, '2024-04-15 08:25:00', 'Manual', 11, NOW()),  -- João Miguel presente (LP)
(1, 2, 1, '2024-04-15 08:20:00', 'Manual', 11, NOW()),  -- Maria Luísa presente
(1, 3, 1, '2024-04-15 08:28:00', 'Manual', 11, NOW()),  -- Pedro António presente
(1, 4, 0, NULL, 'Manual', 11, NOW()),                    -- Ana Clara faltou
(1, 5, 1, '2024-04-15 08:22:00', 'Manual', 11, NOW()),  -- Carlos Eduardo presente

(2, 1, 1, '2024-04-16 08:25:00', 'Manual', 11, NOW()),  -- João Miguel presente (MAT)
(2, 2, 1, '2024-04-16 08:20:00', 'Manual', 11, NOW()),  -- Maria Luísa presente
(2, 3, 1, '2024-04-16 08:28:00', 'Manual', 11, NOW()),  -- Pedro António presente
(2, 4, 0, NULL, 'Manual', 11, NOW()),                    -- Ana Clara faltou
(2, 5, 1, '2024-04-16 08:22:00', 'Manual', 11, NOW()),  -- Carlos Eduardo presente

(3, 1, 1, '2024-04-17 08:25:00', 'Manual', 11, NOW()),  -- João Miguel presente (CN)
(3, 2, 1, '2024-04-17 08:20:00', 'Manual', 11, NOW()),  -- Maria Luísa presente
(3, 3, 1, '2024-04-17 08:28:00', 'Manual', 11, NOW()),  -- Pedro António presente
(3, 4, 0, NULL, 'Manual', 11, NOW()),                    -- Ana Clara faltou
(3, 5, 1, '2024-04-17 08:22:00', 'Manual', 11, NOW()),  -- Carlos Eduardo presente

-- Turma PRI-01A (class_id 7) - exam_schedule_id 4,5,6,7,8
(4, 6, 1, '2024-04-15 10:25:00', 'Manual', 12, NOW()),  -- Aluno 6 presente (LP)
(4, 7, 1, '2024-04-15 10:20:00', 'Manual', 12, NOW()),  -- Aluno 7 presente
(4, 8, 1, '2024-04-15 10:28:00', 'Manual', 12, NOW()),  -- Aluno 8 presente
(4, 9, 0, NULL, 'Manual', 12, NOW()),                    -- Aluno 9 faltou
(4, 10, 1, '2024-04-15 10:22:00', 'Manual', 12, NOW()), -- Aluno 10 presente

(5, 6, 1, '2024-04-16 10:25:00', 'Manual', 12, NOW()),  -- Aluno 6 presente (MAT)
(5, 7, 1, '2024-04-16 10:20:00', 'Manual', 12, NOW()),  -- Aluno 7 presente
(5, 8, 1, '2024-04-16 10:28:00', 'Manual', 12, NOW()),  -- Aluno 8 presente
(5, 9, 0, NULL, 'Manual', 12, NOW()),                    -- Aluno 9 faltou
(5, 10, 1, '2024-04-16 10:22:00', 'Manual', 12, NOW()), -- Aluno 10 presente

(6, 6, 1, '2024-04-17 10:25:00', 'Manual', 12, NOW()),  -- Aluno 6 presente (CN)
(6, 7, 1, '2024-04-17 10:20:00', 'Manual', 12, NOW()),  -- Aluno 7 presente
(6, 8, 1, '2024-04-17 10:28:00', 'Manual', 12, NOW()),  -- Aluno 8 presente
(6, 9, 0, NULL, 'Manual', 12, NOW()),                    -- Aluno 9 faltou
(6, 10, 1, '2024-04-17 10:22:00', 'Manual', 12, NOW()), -- Aluno 10 presente

(7, 6, 1, '2024-04-18 10:25:00', 'Manual', 12, NOW()),  -- Aluno 6 presente (HIS)
(7, 7, 1, '2024-04-18 10:20:00', 'Manual', 12, NOW()),  -- Aluno 7 presente
(7, 8, 1, '2024-04-18 10:28:00', 'Manual', 12, NOW()),  -- Aluno 8 presente
(7, 9, 0, NULL, 'Manual', 12, NOW()),                    -- Aluno 9 faltou
(7, 10, 1, '2024-04-18 10:22:00', 'Manual', 12, NOW()), -- Aluno 10 presente

(8, 6, 1, '2024-04-19 10:25:00', 'Manual', 12, NOW()),  -- Aluno 6 presente (GEO)
(8, 7, 1, '2024-04-19 10:20:00', 'Manual', 12, NOW()),  -- Aluno 7 presente
(8, 8, 1, '2024-04-19 10:28:00', 'Manual', 12, NOW()),  -- Aluno 8 presente
(8, 9, 0, NULL, 'Manual', 12, NOW()),                    -- Aluno 9 faltou
(8, 10, 1, '2024-04-19 10:22:00', 'Manual', 12, NOW()); -- Aluno 10 presente

-- ========================================================
-- 2. NOTAS DOS EXAMES - PROVAS DO PROFESSOR (NPP)
-- exam_schedule_ids 1 a 18, assessment_type = 'NPP'
-- ========================================================

INSERT INTO `tbl_exam_results` (`enrollment_id`, `exam_schedule_id`, `assessment_type`, `score`, `is_absent`, `recorded_by`, `recorded_at`, `created_at`) VALUES
-- Turma INI-01A - LP (exam_schedule_id = 1)
(1, 1, 'NPP', 16.5, 0, 11, NOW(), NOW()),
(2, 1, 'NPP', 15.0, 0, 11, NOW(), NOW()),
(3, 1, 'NPP', 14.5, 0, 11, NOW(), NOW()),
(4, 1, 'NPP', 0, 1, 11, NOW(), NOW()),  -- Faltou
(5, 1, 'NPP', 17.0, 0, 11, NOW(), NOW()),

-- Turma INI-01A - MAT (exam_schedule_id = 2)
(1, 2, 'NPP', 15.5, 0, 11, NOW(), NOW()),
(2, 2, 'NPP', 14.0, 0, 11, NOW(), NOW()),
(3, 2, 'NPP', 16.0, 0, 11, NOW(), NOW()),
(4, 2, 'NPP', 0, 1, 11, NOW(), NOW()),  -- Faltou
(5, 2, 'NPP', 18.0, 0, 11, NOW(), NOW()),

-- Turma INI-01A - CN (exam_schedule_id = 3)
(1, 3, 'NPP', 17.5, 0, 11, NOW(), NOW()),
(2, 3, 'NPP', 16.0, 0, 11, NOW(), NOW()),
(3, 3, 'NPP', 15.5, 0, 11, NOW(), NOW()),
(4, 3, 'NPP', 0, 1, 11, NOW(), NOW()),  -- Faltou
(5, 3, 'NPP', 19.0, 0, 11, NOW(), NOW()),

-- Turma PRI-01A - LP (exam_schedule_id = 4)
(6, 4, 'NPP', 14.5, 0, 12, NOW(), NOW()),
(7, 4, 'NPP', 16.0, 0, 12, NOW(), NOW()),
(8, 4, 'NPP', 13.5, 0, 12, NOW(), NOW()),
(9, 4, 'NPP', 0, 1, 12, NOW(), NOW()),  -- Faltou
(10, 4, 'NPP', 15.5, 0, 12, NOW(), NOW()),

-- Turma PRI-01A - MAT (exam_schedule_id = 5)
(6, 5, 'NPP', 13.5, 0, 12, NOW(), NOW()),
(7, 5, 'NPP', 15.0, 0, 12, NOW(), NOW()),
(8, 5, 'NPP', 14.0, 0, 12, NOW(), NOW()),
(9, 5, 'NPP', 0, 1, 12, NOW(), NOW()),  -- Faltou
(10, 5, 'NPP', 16.5, 0, 12, NOW(), NOW()),

-- Turma PRI-01A - CN (exam_schedule_id = 6)
(6, 6, 'NPP', 15.5, 0, 12, NOW(), NOW()),
(7, 6, 'NPP', 16.5, 0, 12, NOW(), NOW()),
(8, 6, 'NPP', 14.5, 0, 12, NOW(), NOW()),
(9, 6, 'NPP', 0, 1, 12, NOW(), NOW()),  -- Faltou
(10, 6, 'NPP', 17.0, 0, 12, NOW(), NOW()),

-- Turma PRI-01A - HIS (exam_schedule_id = 7)
(6, 7, 'NPP', 16.0, 0, 12, NOW(), NOW()),
(7, 7, 'NPP', 17.5, 0, 12, NOW(), NOW()),
(8, 7, 'NPP', 15.0, 0, 12, NOW(), NOW()),
(9, 7, 'NPP', 0, 1, 12, NOW(), NOW()),  -- Faltou
(10, 7, 'NPP', 16.5, 0, 12, NOW(), NOW()),

-- Turma PRI-01A - GEO (exam_schedule_id = 8)
(6, 8, 'NPP', 15.0, 0, 12, NOW(), NOW()),
(7, 8, 'NPP', 16.0, 0, 12, NOW(), NOW()),
(8, 8, 'NPP', 14.0, 0, 12, NOW(), NOW()),
(9, 8, 'NPP', 0, 1, 12, NOW(), NOW()),  -- Faltou
(10, 8, 'NPP', 15.5, 0, 12, NOW(), NOW()),

-- Turma CIC-07A - LP (exam_schedule_id = 9)
(21, 9, 'NPP', 14.0, 0, 13, NOW(), NOW()),
(22, 9, 'NPP', 15.5, 0, 13, NOW(), NOW()),
(23, 9, 'NPP', 13.5, 0, 13, NOW(), NOW()),
(24, 9, 'NPP', 16.0, 0, 13, NOW(), NOW()),
(25, 9, 'NPP', 14.5, 0, 13, NOW(), NOW()),

-- Turma CIC-07A - MAT (exam_schedule_id = 10)
(21, 10, 'NPP', 13.5, 0, 13, NOW(), NOW()),
(22, 10, 'NPP', 15.0, 0, 13, NOW(), NOW()),
(23, 10, 'NPP', 14.0, 0, 13, NOW(), NOW()),
(24, 10, 'NPP', 16.5, 0, 13, NOW(), NOW()),
(25, 10, 'NPP', 15.0, 0, 13, NOW(), NOW()),

-- Turma CIC-07A - FIS (exam_schedule_id = 11)
(21, 11, 'NPP', 14.5, 0, 13, NOW(), NOW()),
(22, 11, 'NPP', 16.0, 0, 13, NOW(), NOW()),
(23, 11, 'NPP', 13.0, 0, 13, NOW(), NOW()),
(24, 11, 'NPP', 15.5, 0, 13, NOW(), NOW()),
(25, 11, 'NPP', 14.0, 0, 13, NOW(), NOW()),

-- Turma CIC-07A - QUI (exam_schedule_id = 12)
(21, 12, 'NPP', 15.0, 0, 13, NOW(), NOW()),
(22, 12, 'NPP', 16.5, 0, 13, NOW(), NOW()),
(23, 12, 'NPP', 14.5, 0, 13, NOW(), NOW()),
(24, 12, 'NPP', 17.0, 0, 13, NOW(), NOW()),
(25, 12, 'NPP', 15.5, 0, 13, NOW(), NOW()),

-- Turma CIC-07A - BIO (exam_schedule_id = 13)
(21, 13, 'NPP', 16.0, 0, 13, NOW(), NOW()),
(22, 13, 'NPP', 17.5, 0, 13, NOW(), NOW()),
(23, 13, 'NPP', 15.0, 0, 13, NOW(), NOW()),
(24, 13, 'NPP', 16.5, 0, 13, NOW(), NOW()),
(25, 13, 'NPP', 14.5, 0, 13, NOW(), NOW()),

-- Turma CFB-10A - LP (exam_schedule_id = 14)
(31, 14, 'NPP', 15.5, 0, 19, NOW(), NOW()),
(32, 14, 'NPP', 16.0, 0, 19, NOW(), NOW()),
(33, 14, 'NPP', 14.5, 0, 19, NOW(), NOW()),
(34, 14, 'NPP', 17.0, 0, 19, NOW(), NOW()),
(35, 14, 'NPP', 15.0, 0, 19, NOW(), NOW()),

-- Turma CFB-10A - MAT (exam_schedule_id = 15)
(31, 15, 'NPP', 16.5, 0, 19, NOW(), NOW()),
(32, 15, 'NPP', 17.0, 0, 19, NOW(), NOW()),
(33, 15, 'NPP', 15.5, 0, 19, NOW(), NOW()),
(34, 15, 'NPP', 18.0, 0, 19, NOW(), NOW()),
(35, 15, 'NPP', 16.0, 0, 19, NOW(), NOW()),

-- Turma CFB-10A - FIS (exam_schedule_id = 16)
(31, 16, 'NPP', 15.0, 0, 19, NOW(), NOW()),
(32, 16, 'NPP', 16.5, 0, 19, NOW(), NOW()),
(33, 16, 'NPP', 14.0, 0, 19, NOW(), NOW()),
(34, 16, 'NPP', 17.5, 0, 19, NOW(), NOW()),
(35, 16, 'NPP', 15.5, 0, 19, NOW(), NOW()),

-- Turma CFB-10A - QUI (exam_schedule_id = 17)
(31, 17, 'NPP', 14.5, 0, 19, NOW(), NOW()),
(32, 17, 'NPP', 16.0, 0, 19, NOW(), NOW()),
(33, 17, 'NPP', 15.0, 0, 19, NOW(), NOW()),
(34, 17, 'NPP', 17.0, 0, 19, NOW(), NOW()),
(35, 17, 'NPP', 16.5, 0, 19, NOW(), NOW()),

-- Turma CFB-10A - BIO (exam_schedule_id = 18)
(31, 18, 'NPP', 16.0, 0, 19, NOW(), NOW()),
(32, 18, 'NPP', 17.5, 0, 19, NOW(), NOW()),
(33, 18, 'NPP', 15.5, 0, 19, NOW(), NOW()),
(34, 18, 'NPP', 18.0, 0, 19, NOW(), NOW()),
(35, 18, 'NPP', 16.5, 0, 19, NOW(), NOW());

-- ========================================================
-- 3. NOTAS DAS PROVAS TRIMESTRAIS (NPT)
-- exam_schedule_ids 19 a 39, assessment_type = 'NPT'
-- ========================================================

INSERT INTO `tbl_exam_results` (`enrollment_id`, `exam_schedule_id`, `assessment_type`, `score`, `is_absent`, `recorded_by`, `recorded_at`, `created_at`) VALUES
-- Turma INI-01A - NPT LP (exam_schedule_id = 19)
(1, 19, 'NPT', 15.0, 0, 11, NOW(), NOW()),
(2, 19, 'NPT', 14.0, 0, 11, NOW(), NOW()),
(3, 19, 'NPT', 16.0, 0, 11, NOW(), NOW()),
(4, 19, 'NPT', 0, 1, 11, NOW(), NOW()),  -- Faltou
(5, 19, 'NPT', 17.5, 0, 11, NOW(), NOW()),

-- Turma INI-01A - NPT MAT (exam_schedule_id = 20)
(1, 20, 'NPT', 14.5, 0, 11, NOW(), NOW()),
(2, 20, 'NPT', 15.5, 0, 11, NOW(), NOW()),
(3, 20, 'NPT', 13.5, 0, 11, NOW(), NOW()),
(4, 20, 'NPT', 0, 1, 11, NOW(), NOW()),  -- Faltou
(5, 20, 'NPT', 18.0, 0, 11, NOW(), NOW()),

-- Turma INI-01A - NPT CN (exam_schedule_id = 21)
(1, 21, 'NPT', 16.5, 0, 11, NOW(), NOW()),
(2, 21, 'NPT', 15.0, 0, 11, NOW(), NOW()),
(3, 21, 'NPT', 14.0, 0, 11, NOW(), NOW()),
(4, 21, 'NPT', 0, 1, 11, NOW(), NOW()),  -- Faltou
(5, 21, 'NPT', 19.0, 0, 11, NOW(), NOW()),

-- Turma PRI-01A - NPT LP (exam_schedule_id = 22)
(6, 22, 'NPT', 14.0, 0, 12, NOW(), NOW()),
(7, 22, 'NPT', 15.5, 0, 12, NOW(), NOW()),
(8, 22, 'NPT', 13.5, 0, 12, NOW(), NOW()),
(9, 22, 'NPT', 0, 1, 12, NOW(), NOW()),  -- Faltou
(10, 22, 'NPT', 16.5, 0, 12, NOW(), NOW()),

-- Turma PRI-01A - NPT MAT (exam_schedule_id = 23)
(6, 23, 'NPT', 13.5, 0, 12, NOW(), NOW()),
(7, 23, 'NPT', 15.0, 0, 12, NOW(), NOW()),
(8, 23, 'NPT', 14.0, 0, 12, NOW(), NOW()),
(9, 23, 'NPT', 0, 1, 12, NOW(), NOW()),  -- Faltou
(10, 23, 'NPT', 17.0, 0, 12, NOW(), NOW()),

-- Turma PRI-01A - NPT CN (exam_schedule_id = 24)
(6, 24, 'NPT', 15.0, 0, 12, NOW(), NOW()),
(7, 24, 'NPT', 16.0, 0, 12, NOW(), NOW()),
(8, 24, 'NPT', 14.5, 0, 12, NOW(), NOW()),
(9, 24, 'NPT', 0, 1, 12, NOW(), NOW()),  -- Faltou
(10, 24, 'NPT', 17.5, 0, 12, NOW(), NOW()),

-- Turma PRI-01A - NPT HIS (exam_schedule_id = 25)
(6, 25, 'NPT', 16.0, 0, 12, NOW(), NOW()),
(7, 25, 'NPT', 17.5, 0, 12, NOW(), NOW()),
(8, 25, 'NPT', 15.0, 0, 12, NOW(), NOW()),
(9, 25, 'NPT', 0, 1, 12, NOW(), NOW()),  -- Faltou
(10, 25, 'NPT', 16.5, 0, 12, NOW(), NOW());

-- ========================================================
-- 4. AVALIAÇÕES CONTÍNUAS (AC) - Sem exam_schedule_id
-- ========================================================

INSERT INTO `tbl_exam_results` (`enrollment_id`, `assessment_type`, `score`, `recorded_by`, `recorded_at`, `created_at`) VALUES
-- Turma INI-01A - Alunos 1-5
(1, 'AC', 14.5, 11, NOW(), NOW()),
(2, 'AC', 15.0, 11, NOW(), NOW()),
(3, 'AC', 13.5, 11, NOW(), NOW()),
(4, 'AC', 12.0, 11, NOW(), NOW()),
(5, 'AC', 16.0, 11, NOW(), NOW()),

-- Turma PRI-01A - Alunos 6-10
(6, 'AC', 14.0, 12, NOW(), NOW()),
(7, 'AC', 15.5, 12, NOW(), NOW()),
(8, 'AC', 13.0, 12, NOW(), NOW()),
(9, 'AC', 11.5, 12, NOW(), NOW()),
(10, 'AC', 16.5, 12, NOW(), NOW()),

-- Turma CIC-07A - Alunos 21-25
(21, 'AC', 13.5, 13, NOW(), NOW()),
(22, 'AC', 14.5, 13, NOW(), NOW()),
(23, 'AC', 15.0, 13, NOW(), NOW()),
(24, 'AC', 12.5, 13, NOW(), NOW()),
(25, 'AC', 16.0, 13, NOW(), NOW()),

-- Turma CFB-10A - Alunos 31-35
(31, 'AC', 14.0, 19, NOW(), NOW()),
(32, 'AC', 15.0, 19, NOW(), NOW()),
(33, 'AC', 13.5, 19, NOW(), NOW()),
(34, 'AC', 12.0, 19, NOW(), NOW()),
(35, 'AC', 16.5, 19, NOW(), NOW());


-- ========================================================
-- CONTINUAÇÃO: PRESENÇAS E NOTAS DOS EXAMES
-- ========================================================

-- ========================================================
-- 1. CONTINUAR PRESENÇAS EM EXAMES (tbl_exam_attendance)
-- ========================================================

-- Presenças para Provas Trimestrais (NPT) - exam_schedule_ids 19 a 39
INSERT INTO `tbl_exam_attendance` (`exam_schedule_id`, `enrollment_id`, `attended`, `check_in_time`, `check_in_method`, `recorded_by`, `created_at`) VALUES
-- Turma INI-01A - NPT LP (19)
(19, 1, 1, '2024-04-22 08:25:00', 'Manual', 11, NOW()),
(19, 2, 1, '2024-04-22 08:20:00', 'Manual', 11, NOW()),
(19, 3, 1, '2024-04-22 08:28:00', 'Manual', 11, NOW()),
(19, 4, 0, NULL, 'Manual', 11, NOW()),
(19, 5, 1, '2024-04-22 08:22:00', 'Manual', 11, NOW()),

(20, 1, 1, '2024-04-23 08:25:00', 'Manual', 11, NOW()),
(20, 2, 1, '2024-04-23 08:20:00', 'Manual', 11, NOW()),
(20, 3, 1, '2024-04-23 08:28:00', 'Manual', 11, NOW()),
(20, 4, 0, NULL, 'Manual', 11, NOW()),
(20, 5, 1, '2024-04-23 08:22:00', 'Manual', 11, NOW()),

(21, 1, 1, '2024-04-24 08:25:00', 'Manual', 11, NOW()),
(21, 2, 1, '2024-04-24 08:20:00', 'Manual', 11, NOW()),
(21, 3, 1, '2024-04-24 08:28:00', 'Manual', 11, NOW()),
(21, 4, 0, NULL, 'Manual', 11, NOW()),
(21, 5, 1, '2024-04-24 08:22:00', 'Manual', 11, NOW()),

-- Turma PRI-01A - NPT (22-25)
(22, 6, 1, '2024-04-22 10:25:00', 'Manual', 12, NOW()),
(22, 7, 1, '2024-04-22 10:20:00', 'Manual', 12, NOW()),
(22, 8, 1, '2024-04-22 10:28:00', 'Manual', 12, NOW()),
(22, 9, 0, NULL, 'Manual', 12, NOW()),
(22, 10, 1, '2024-04-22 10:22:00', 'Manual', 12, NOW()),

(23, 6, 1, '2024-04-23 10:25:00', 'Manual', 12, NOW()),
(23, 7, 1, '2024-04-23 10:20:00', 'Manual', 12, NOW()),
(23, 8, 1, '2024-04-23 10:28:00', 'Manual', 12, NOW()),
(23, 9, 0, NULL, 'Manual', 12, NOW()),
(23, 10, 1, '2024-04-23 10:22:00', 'Manual', 12, NOW()),

(24, 6, 1, '2024-04-24 10:25:00', 'Manual', 12, NOW()),
(24, 7, 1, '2024-04-24 10:20:00', 'Manual', 12, NOW()),
(24, 8, 1, '2024-04-24 10:28:00', 'Manual', 12, NOW()),
(24, 9, 0, NULL, 'Manual', 12, NOW()),
(24, 10, 1, '2024-04-24 10:22:00', 'Manual', 12, NOW()),

(25, 6, 1, '2024-04-25 10:25:00', 'Manual', 12, NOW()),
(25, 7, 1, '2024-04-25 10:20:00', 'Manual', 12, NOW()),
(25, 8, 1, '2024-04-25 10:28:00', 'Manual', 12, NOW()),
(25, 9, 0, NULL, 'Manual', 12, NOW()),
(25, 10, 1, '2024-04-25 10:22:00', 'Manual', 12, NOW()),

-- Turma CIC-07A - NPT (26-29)
(26, 21, 1, '2024-04-22 13:25:00', 'Manual', 13, NOW()),
(26, 22, 1, '2024-04-22 13:20:00', 'Manual', 13, NOW()),
(26, 23, 1, '2024-04-22 13:28:00', 'Manual', 13, NOW()),
(26, 24, 1, '2024-04-22 13:22:00', 'Manual', 13, NOW()),
(26, 25, 1, '2024-04-22 13:26:00', 'Manual', 13, NOW()),

(27, 21, 1, '2024-04-23 13:25:00', 'Manual', 13, NOW()),
(27, 22, 1, '2024-04-23 13:20:00', 'Manual', 13, NOW()),
(27, 23, 1, '2024-04-23 13:28:00', 'Manual', 13, NOW()),
(27, 24, 1, '2024-04-23 13:22:00', 'Manual', 13, NOW()),
(27, 25, 1, '2024-04-23 13:26:00', 'Manual', 13, NOW()),

(28, 21, 1, '2024-04-24 13:25:00', 'Manual', 13, NOW()),
(28, 22, 1, '2024-04-24 13:20:00', 'Manual', 13, NOW()),
(28, 23, 1, '2024-04-24 13:28:00', 'Manual', 13, NOW()),
(28, 24, 1, '2024-04-24 13:22:00', 'Manual', 13, NOW()),
(28, 25, 1, '2024-04-24 13:26:00', 'Manual', 13, NOW()),

(29, 21, 1, '2024-04-25 13:25:00', 'Manual', 13, NOW()),
(29, 22, 1, '2024-04-25 13:20:00', 'Manual', 13, NOW()),
(29, 23, 1, '2024-04-25 13:28:00', 'Manual', 13, NOW()),
(29, 24, 1, '2024-04-25 13:22:00', 'Manual', 13, NOW()),
(29, 25, 1, '2024-04-25 13:26:00', 'Manual', 13, NOW()),

-- Turma CFB-10A - NPT (30-33)
(30, 31, 1, '2024-04-22 15:25:00', 'Manual', 19, NOW()),
(30, 32, 1, '2024-04-22 15:20:00', 'Manual', 19, NOW()),
(30, 33, 1, '2024-04-22 15:28:00', 'Manual', 19, NOW()),
(30, 34, 1, '2024-04-22 15:22:00', 'Manual', 19, NOW()),
(30, 35, 1, '2024-04-22 15:26:00', 'Manual', 19, NOW()),

(31, 31, 1, '2024-04-23 15:25:00', 'Manual', 19, NOW()),
(31, 32, 1, '2024-04-23 15:20:00', 'Manual', 19, NOW()),
(31, 33, 1, '2024-04-23 15:28:00', 'Manual', 19, NOW()),
(31, 34, 1, '2024-04-23 15:22:00', 'Manual', 19, NOW()),
(31, 35, 1, '2024-04-23 15:26:00', 'Manual', 19, NOW()),

(32, 31, 1, '2024-04-24 15:25:00', 'Manual', 19, NOW()),
(32, 32, 1, '2024-04-24 15:20:00', 'Manual', 19, NOW()),
(32, 33, 1, '2024-04-24 15:28:00', 'Manual', 19, NOW()),
(32, 34, 1, '2024-04-24 15:22:00', 'Manual', 19, NOW()),
(32, 35, 1, '2024-04-24 15:26:00', 'Manual', 19, NOW()),

(33, 31, 1, '2024-04-25 15:25:00', 'Manual', 19, NOW()),
(33, 32, 1, '2024-04-25 15:20:00', 'Manual', 19, NOW()),
(33, 33, 1, '2024-04-25 15:28:00', 'Manual', 19, NOW()),
(33, 34, 1, '2024-04-25 15:22:00', 'Manual', 19, NOW()),
(33, 35, 1, '2024-04-25 15:26:00', 'Manual', 19, NOW()),

-- Provas de Recurso (REC) - exam_schedule_ids 34-39
(34, 6, 1, '2024-04-29 10:25:00', 'Manual', 12, NOW()),
(34, 7, 1, '2024-04-29 10:20:00', 'Manual', 12, NOW()),
(34, 8, 1, '2024-04-29 10:28:00', 'Manual', 12, NOW()),
(34, 9, 0, NULL, 'Manual', 12, NOW()),
(34, 10, 1, '2024-04-29 10:22:00', 'Manual', 12, NOW()),

(35, 6, 1, '2024-04-30 10:25:00', 'Manual', 12, NOW()),
(35, 7, 1, '2024-04-30 10:20:00', 'Manual', 12, NOW()),
(35, 8, 1, '2024-04-30 10:28:00', 'Manual', 12, NOW()),
(35, 9, 0, NULL, 'Manual', 12, NOW()),
(35, 10, 1, '2024-04-30 10:22:00', 'Manual', 12, NOW()),

(36, 21, 1, '2024-04-29 13:25:00', 'Manual', 13, NOW()),
(36, 22, 1, '2024-04-29 13:20:00', 'Manual', 13, NOW()),
(36, 23, 1, '2024-04-29 13:28:00', 'Manual', 13, NOW()),
(36, 24, 1, '2024-04-29 13:22:00', 'Manual', 13, NOW()),
(36, 25, 1, '2024-04-29 13:26:00', 'Manual', 13, NOW()),

(37, 21, 1, '2024-04-30 13:25:00', 'Manual', 13, NOW()),
(37, 22, 1, '2024-04-30 13:20:00', 'Manual', 13, NOW()),
(37, 23, 1, '2024-04-30 13:28:00', 'Manual', 13, NOW()),
(37, 24, 1, '2024-04-30 13:22:00', 'Manual', 13, NOW()),
(37, 25, 1, '2024-04-30 13:26:00', 'Manual', 13, NOW()),

(38, 31, 1, '2024-04-29 15:25:00', 'Manual', 19, NOW()),
(38, 32, 1, '2024-04-29 15:20:00', 'Manual', 19, NOW()),
(38, 33, 1, '2024-04-29 15:28:00', 'Manual', 19, NOW()),
(38, 34, 1, '2024-04-29 15:22:00', 'Manual', 19, NOW()),
(38, 35, 1, '2024-04-29 15:26:00', 'Manual', 19, NOW()),

(39, 31, 1, '2024-04-30 15:25:00', 'Manual', 19, NOW()),
(39, 32, 1, '2024-04-30 15:20:00', 'Manual', 19, NOW()),
(39, 33, 1, '2024-04-30 15:28:00', 'Manual', 19, NOW()),
(39, 34, 1, '2024-04-30 15:22:00', 'Manual', 19, NOW()),
(39, 35, 1, '2024-04-30 15:26:00', 'Manual', 19, NOW());

-- ========================================================
-- 2. CONTINUAR NOTAS DOS EXAMES - PROVAS TRIMESTRAIS (NPT)
-- exam_schedule_ids 26 a 33, assessment_type = 'NPT'
-- ========================================================

INSERT INTO `tbl_exam_results` (`enrollment_id`, `exam_schedule_id`, `assessment_type`, `score`, `is_absent`, `recorded_by`, `recorded_at`, `created_at`) VALUES
-- Turma CIC-07A - NPT LP (26)
(21, 26, 'NPT', 13.5, 0, 13, NOW(), NOW()),
(22, 26, 'NPT', 15.0, 0, 13, NOW(), NOW()),
(23, 26, 'NPT', 14.0, 0, 13, NOW(), NOW()),
(24, 26, 'NPT', 16.5, 0, 13, NOW(), NOW()),
(25, 26, 'NPT', 15.5, 0, 13, NOW(), NOW()),

-- Turma CIC-07A - NPT MAT (27)
(21, 27, 'NPT', 12.5, 0, 13, NOW(), NOW()),
(22, 27, 'NPT', 14.5, 0, 13, NOW(), NOW()),
(23, 27, 'NPT', 13.0, 0, 13, NOW(), NOW()),
(24, 27, 'NPT', 15.5, 0, 13, NOW(), NOW()),
(25, 27, 'NPT', 14.0, 0, 13, NOW(), NOW()),

-- Turma CIC-07A - NPT FIS (28)
(21, 28, 'NPT', 14.0, 0, 13, NOW(), NOW()),
(22, 28, 'NPT', 15.5, 0, 13, NOW(), NOW()),
(23, 28, 'NPT', 13.5, 0, 13, NOW(), NOW()),
(24, 28, 'NPT', 16.0, 0, 13, NOW(), NOW()),
(25, 28, 'NPT', 14.5, 0, 13, NOW(), NOW()),

-- Turma CIC-07A - NPT QUI (29)
(21, 29, 'NPT', 15.0, 0, 13, NOW(), NOW()),
(22, 29, 'NPT', 16.5, 0, 13, NOW(), NOW()),
(23, 29, 'NPT', 14.5, 0, 13, NOW(), NOW()),
(24, 29, 'NPT', 17.0, 0, 13, NOW(), NOW()),
(25, 29, 'NPT', 15.5, 0, 13, NOW(), NOW()),

-- Turma CFB-10A - NPT LP (30)
(31, 30, 'NPT', 14.5, 0, 19, NOW(), NOW()),
(32, 30, 'NPT', 15.5, 0, 19, NOW(), NOW()),
(33, 30, 'NPT', 13.5, 0, 19, NOW(), NOW()),
(34, 30, 'NPT', 16.5, 0, 19, NOW(), NOW()),
(35, 30, 'NPT', 15.0, 0, 19, NOW(), NOW()),

-- Turma CFB-10A - NPT MAT (31)
(31, 31, 'NPT', 16.0, 0, 19, NOW(), NOW()),
(32, 31, 'NPT', 17.0, 0, 19, NOW(), NOW()),
(33, 31, 'NPT', 15.0, 0, 19, NOW(), NOW()),
(34, 31, 'NPT', 17.5, 0, 19, NOW(), NOW()),
(35, 31, 'NPT', 16.5, 0, 19, NOW(), NOW()),

-- Turma CFB-10A - NPT FIS (32)
(31, 32, 'NPT', 15.0, 0, 19, NOW(), NOW()),
(32, 32, 'NPT', 16.0, 0, 19, NOW(), NOW()),
(33, 32, 'NPT', 14.5, 0, 19, NOW(), NOW()),
(34, 32, 'NPT', 17.0, 0, 19, NOW(), NOW()),
(35, 32, 'NPT', 15.5, 0, 19, NOW(), NOW()),

-- Turma CFB-10A - NPT QUI (33)
(31, 33, 'NPT', 14.0, 0, 19, NOW(), NOW()),
(32, 33, 'NPT', 15.5, 0, 19, NOW(), NOW()),
(33, 33, 'NPT', 13.5, 0, 19, NOW(), NOW()),
(34, 33, 'NPT', 16.5, 0, 19, NOW(), NOW()),
(35, 33, 'NPT', 15.0, 0, 19, NOW(), NOW());

-- ========================================================
-- 3. NOTAS DAS PROVAS DE RECURSO (REC)
-- exam_schedule_ids 34 a 39, assessment_type = 'REC'
-- ========================================================

INSERT INTO `tbl_exam_results` (`enrollment_id`, `exam_schedule_id`, `assessment_type`, `score`, `is_absent`, `recorded_by`, `recorded_at`, `created_at`) VALUES
-- Turma PRI-01A - REC LP (34) - Aluno 9 que faltou
(9, 34, 'REC', 10.5, 0, 12, NOW(), NOW()),

-- Turma PRI-01A - REC MAT (35) - Aluno 9
(9, 35, 'REC', 9.5, 0, 12, NOW(), NOW()),

-- Turma CIC-07A - REC LP (36) - Alunos com notas baixas
(23, 36, 'REC', 12.0, 0, 13, NOW(), NOW()),
(25, 36, 'REC', 11.5, 0, 13, NOW(), NOW()),

-- Turma CIC-07A - REC MAT (37)
(21, 37, 'REC', 10.5, 0, 13, NOW(), NOW()),
(23, 37, 'REC', 11.0, 0, 13, NOW(), NOW()),

-- Turma CFB-10A - REC LP (38)
(33, 38, 'REC', 11.5, 0, 19, NOW(), NOW()),

-- Turma CFB-10A - REC MAT (39)
(33, 39, 'REC', 12.0, 0, 19, NOW(), NOW()),
(35, 39, 'REC', 10.5, 0, 19, NOW(), NOW());

-- ========================================================
-- 4. MAIS AVALIAÇÕES CONTÍNUAS (AC) PARA COMPLETAR
-- ========================================================

INSERT INTO `tbl_exam_results` (`enrollment_id`, `assessment_type`, `score`, `recorded_by`, `recorded_at`, `created_at`) VALUES
-- Alunos 11-15 (Turma PRI-03A)
(11, 'AC', 13.5, 12, NOW(), NOW()),
(12, 'AC', 14.0, 12, NOW(), NOW()),
(13, 'AC', 12.5, 12, NOW(), NOW()),
(14, 'AC', 15.0, 12, NOW(), NOW()),
(15, 'AC', 14.5, 12, NOW(), NOW()),

-- Alunos 16-20 (Turma PRI-05A)
(16, 'AC', 14.0, 12, NOW(), NOW()),
(17, 'AC', 15.5, 12, NOW(), NOW()),
(18, 'AC', 13.0, 12, NOW(), NOW()),
(19, 'AC', 16.0, 12, NOW(), NOW()),
(20, 'AC', 14.5, 12, NOW(), NOW()),

-- Alunos 26-30 (Turma CIC-09A)
(26, 'AC', 14.5, 13, NOW(), NOW()),
(27, 'AC', 15.0, 13, NOW(), NOW()),
(28, 'AC', 13.5, 13, NOW(), NOW()),
(29, 'AC', 16.0, 13, NOW(), NOW()),
(30, 'AC', 14.0, 13, NOW(), NOW()),

-- Alunos 36-40 (Turma CEJ-10A)
(36, 'AC', 15.0, 17, NOW(), NOW()),
(37, 'AC', 14.5, 17, NOW(), NOW()),
(38, 'AC', 13.5, 17, NOW(), NOW()),
(39, 'AC', 16.0, 17, NOW(), NOW()),
(40, 'AC', 15.5, 17, NOW(), NOW()),

-- Alunos 41-45 (Turma CH-11A)
(41, 'AC', 14.0, 15, NOW(), NOW()),
(42, 'AC', 15.5, 15, NOW(), NOW()),
(43, 'AC', 13.0, 15, NOW(), NOW()),
(44, 'AC', 16.5, 15, NOW(), NOW()),
(45, 'AC', 15.0, 15, NOW(), NOW()),

-- Alunos 46-50 (Turma CFB-12A)
(46, 'AC', 15.5, 19, NOW(), NOW()),
(47, 'AC', 16.0, 19, NOW(), NOW()),
(48, 'AC', 14.5, 19, NOW(), NOW()),
(49, 'AC', 17.0, 19, NOW(), NOW()),
(50, 'AC', 15.0, 19, NOW(), NOW()),

-- Alunos 51-55
(51, 'AC', 14.5, 14, NOW(), NOW()),
(52, 'AC', 15.0, 14, NOW(), NOW()),
(53, 'AC', 13.5, 14, NOW(), NOW()),
(54, 'AC', 16.0, 14, NOW(), NOW()),
(55, 'AC', 14.0, 14, NOW(), NOW()),

-- Alunos 56-60
(56, 'AC', 15.5, 15, NOW(), NOW()),
(57, 'AC', 16.0, 15, NOW(), NOW()),
(58, 'AC', 14.5, 15, NOW(), NOW()),
(59, 'AC', 17.0, 15, NOW(), NOW()),
(60, 'AC', 15.0, 15, NOW(), NOW()),

-- Alunos 61-65
(61, 'AC', 14.0, 16, NOW(), NOW()),
(62, 'AC', 15.5, 16, NOW(), NOW()),
(63, 'AC', 13.0, 16, NOW(), NOW()),
(64, 'AC', 16.5, 16, NOW(), NOW()),
(65, 'AC', 15.0, 16, NOW(), NOW()),

-- Alunos 66-70
(66, 'AC', 15.5, 17, NOW(), NOW()),
(67, 'AC', 16.0, 17, NOW(), NOW()),
(68, 'AC', 14.5, 17, NOW(), NOW()),
(69, 'AC', 17.0, 17, NOW(), NOW()),
(70, 'AC', 15.0, 17, NOW(), NOW()),

-- Alunos 71-75
(71, 'AC', 14.5, 18, NOW(), NOW()),
(72, 'AC', 15.0, 18, NOW(), NOW()),
(73, 'AC', 13.5, 18, NOW(), NOW()),
(74, 'AC', 16.0, 18, NOW(), NOW()),
(75, 'AC', 14.0, 18, NOW(), NOW()),

-- Alunos 76-80
(76, 'AC', 15.5, 19, NOW(), NOW()),
(77, 'AC', 16.0, 19, NOW(), NOW()),
(78, 'AC', 14.5, 19, NOW(), NOW()),
(79, 'AC', 17.0, 19, NOW(), NOW()),
(80, 'AC', 15.0, 19, NOW(), NOW()),

-- Alunos 81-85
(81, 'AC', 14.0, 20, NOW(), NOW()),
(82, 'AC', 15.5, 20, NOW(), NOW()),
(83, 'AC', 13.0, 20, NOW(), NOW()),
(84, 'AC', 16.5, 20, NOW(), NOW()),
(85, 'AC', 15.0, 20, NOW(), NOW()),

-- Alunos 86-90
(86, 'AC', 15.5, 11, NOW(), NOW()),
(87, 'AC', 16.0, 11, NOW(), NOW()),
(88, 'AC', 14.5, 11, NOW(), NOW()),
(89, 'AC', 17.0, 11, NOW(), NOW()),
(90, 'AC', 15.0, 11, NOW(), NOW()),

-- Alunos 91-95
(91, 'AC', 14.5, 12, NOW(), NOW()),
(92, 'AC', 15.0, 12, NOW(), NOW()),
(93, 'AC', 13.5, 12, NOW(), NOW()),
(94, 'AC', 16.0, 12, NOW(), NOW()),
(95, 'AC', 14.0, 12, NOW(), NOW()),

-- Alunos 96-100
(96, 'AC', 15.5, 13, NOW(), NOW()),
(97, 'AC', 16.0, 13, NOW(), NOW()),
(98, 'AC', 14.5, 13, NOW(), NOW()),
(99, 'AC', 17.0, 13, NOW(), NOW()),
(100, 'AC', 15.0, 13, NOW(), NOW()),

-- Alunos 101-105
(101, 'AC', 14.0, 14, NOW(), NOW()),
(102, 'AC', 15.5, 14, NOW(), NOW()),
(103, 'AC', 13.0, 14, NOW(), NOW()),
(104, 'AC', 16.5, 14, NOW(), NOW()),
(105, 'AC', 15.0, 14, NOW(), NOW()),

-- Alunos 106-110
(106, 'AC', 15.5, 15, NOW(), NOW()),
(107, 'AC', 16.0, 15, NOW(), NOW()),
(108, 'AC', 14.5, 15, NOW(), NOW()),
(109, 'AC', 17.0, 15, NOW(), NOW()),
(110, 'AC', 15.0, 15, NOW(), NOW()),

-- Alunos 111-115
(111, 'AC', 14.5, 16, NOW(), NOW()),
(112, 'AC', 15.0, 16, NOW(), NOW()),
(113, 'AC', 13.5, 16, NOW(), NOW()),
(114, 'AC', 16.0, 16, NOW(), NOW()),
(115, 'AC', 14.0, 16, NOW(), NOW()),

-- Alunos 116-120
(116, 'AC', 15.5, 17, NOW(), NOW()),
(117, 'AC', 16.0, 17, NOW(), NOW()),
(118, 'AC', 14.5, 17, NOW(), NOW()),
(119, 'AC', 17.0, 17, NOW(), NOW()),
(120, 'AC', 15.0, 17, NOW(), NOW());

-- ========================================================
-- 5. RESUMO DOS DADOS INSERIDOS
-- ========================================================

-- SELECT 'PRESENÇAS' as Tipo, COUNT(*) as Total FROM tbl_exam_attendance
-- UNION ALL
-- SELECT 'NOTAS NPP', COUNT(*) FROM tbl_exam_results WHERE assessment_type = 'NPP'
-- UNION ALL
-- SELECT 'NOTAS NPT', COUNT(*) FROM tbl_exam_results WHERE assessment_type = 'NPT'
-- UNION ALL
-- SELECT 'NOTAS REC', COUNT(*) FROM tbl_exam_results WHERE assessment_type = 'REC'
-- UNION ALL
-- SELECT 'NOTAS AC', COUNT(*) FROM tbl_exam_results WHERE assessment_type = 'AC';

-- ========================================================
-- FIM DO SCRIPT
-- ========================================================
COMMIT;
-- ========================================================
-- 5. CONSULTA PARA VERIFICAR OS DADOS INSERIDOS
-- ========================================================

-- Verificar presenças
-- SELECT * FROM tbl_exam_attendance LIMIT 10;

-- Verificar notas
-- SELECT er.*, es.class_id, es.discipline_id 
-- FROM tbl_exam_results er
-- JOIN tbl_exam_schedules es ON er.exam_schedule_id = es.id
-- WHERE er.assessment_type = 'NPP'
-- LIMIT 20;

-- ========================================================
-- FIM DO SCRIPT
-- ========================================================



COMMIT;