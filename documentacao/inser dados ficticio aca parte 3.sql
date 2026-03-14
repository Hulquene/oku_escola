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