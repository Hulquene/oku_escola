-- ========================================================
-- SCRIPT PARA INSERIR DADOS FICTÍCIOS DE PERÍODOS E AGENDAMENTOS DE EXAME
-- PARA TESTES E DEMONSTRAÇÕES
-- ========================================================

USE escola_angolana;

-- ========================================================
-- 1. LIMPAR DADOS EXISTENTES (OPCIONAL - COMENTE SE NÃO QUISER LIMPAR)
-- ========================================================
-- DELETE FROM tbl_exam_schedules;
-- DELETE FROM tbl_exam_periods;
-- DELETE FROM tbl_class_disciplines;

-- ========================================================
-- 2. CRIAR PERÍODOS DE EXAME (3 ÉPOCAS POR TRIMESTRE)
-- ========================================================

-- 2.1 Períodos para o 1º Trimestre
INSERT INTO `tbl_exam_periods` (`period_name`, `academic_year_id`, `semester_id`, `period_type`, `start_date`, `end_date`, `status`, `created_at`) VALUES
('AC - 1º Trimestre 2024', 1, 1, 'Avaliação Contínua', '2024-04-01', '2024-04-12', 'Planejado', NOW()),
('NPP - 1º Trimestre 2024', 1, 1, 'Prova do Professor', '2024-04-15', '2024-04-19', 'Planejado', NOW()),
('NPT - 1º Trimestre 2024', 1, 1, 'Prova Trimestral', '2024-04-22', '2024-04-26', 'Planejado', NOW());

-- 2.2 Períodos para o 2º Trimestre
INSERT INTO `tbl_exam_periods` (`period_name`, `academic_year_id`, `semester_id`, `period_type`, `start_date`, `end_date`, `status`, `created_at`) VALUES
('AC - 2º Trimestre 2024', 1, 2, 'Avaliação Contínua', '2024-07-15', '2024-07-26', 'Planejado', NOW()),
('NPP - 2º Trimestre 2024', 1, 2, 'Prova do Professor', '2024-07-29', '2024-08-02', 'Planejado', NOW()),
('NPT - 2º Trimestre 2024', 1, 2, 'Prova Trimestral', '2024-08-05', '2024-08-09', 'Planejado', NOW());

-- 2.3 Períodos para o 3º Trimestre
INSERT INTO `tbl_exam_periods` (`period_name`, `academic_year_id`, `semester_id`, `period_type`, `start_date`, `end_date`, `status`, `created_at`) VALUES
('AC - 3º Trimestre 2024', 1, 3, 'Avaliação Contínua', '2024-11-11', '2024-11-22', 'Planejado', NOW()),
('NPP - 3º Trimestre 2024', 1, 3, 'Prova do Professor', '2024-11-25', '2024-11-29', 'Planejado', NOW()),
('EX-FIN - 3º Trimestre 2024', 1, 3, 'Exame Final', '2024-12-02', '2024-12-13', 'Planejado', NOW());

-- 2.4 Períodos de Recurso (opcionais)
INSERT INTO `tbl_exam_periods` (`period_name`, `academic_year_id`, `semester_id`, `period_type`, `start_date`, `end_date`, `status`, `created_at`) VALUES
-- ('REC - 1º Trimestre 2024', 1, 1, 'Recurso', '2024-04-29', '2024-05-03', 'Planejado', NOW()),
-- ('REC - 2º Trimestre 2024', 1, 2, 'Recurso', '2024-08-12', '2024-08-16', 'Planejado', NOW()),
('REC - 3º Trimestre 2024', 1, 3, 'Recurso', '2024-12-16', '2024-12-20', 'Planejado', NOW());

-- ========================================================
-- FIM DA ATRIBUIÇÃO DE PROFESSORES
-- ========================================================
COMMIT;