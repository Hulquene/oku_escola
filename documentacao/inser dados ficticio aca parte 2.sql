-- ========================================================
-- SCRIPT PARA INSERIR STAFF RESTANTE (IDs 2-10)
-- E CRIAR PERÍODOS E AGENDAMENTOS DE EXAME
-- ========================================================

USE escola_angolana;

-- ========================================================
-- 1. INSERIR STAFF (Funcionários) - IDs 2 a 10
-- (Admin já existe ID 1)
-- ========================================================

-- Inserir usuários staff com diferentes roles
INSERT INTO `tbl_users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `phone`, `role_id`, `user_type`, `is_active`, `created_at`) VALUES
-- Diretor (role_id 2)
(2, 'diretor.manuel', 'diretor@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Manuel', 'Diretor', '+244 923 456 701', 2, 'staff', 1, NOW()),

-- Secretários (role_id 3)
(3, 'secretario.ana', 'ana.secretaria@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Secretária', '+244 923 456 702', 3, 'staff', 1, NOW()),
(4, 'secretario.pedro', 'pedro.secretaria@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pedro', 'Secretário', '+244 923 456 703', 3, 'staff', 1, NOW()),

-- Tesoureiros (role_id 7)
(5, 'tesoureiro.maria', 'maria.tesouraria@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maria', 'Tesoureira', '+244 923 456 704', 7, 'staff', 1, NOW()),
(6, 'tesoureiro.jose', 'jose.tesouraria@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'José', 'Tesoureiro', '+244 923 456 705', 7, 'staff', 1, NOW()),

-- Bibliotecários (role_id 8)
(7, 'biblioteca.luisa', 'luisa.biblioteca@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Luísa', 'Bibliotecária', '+244 923 456 706', 8, 'staff', 1, NOW()),
(8, 'biblioteca.carlos', 'carlos.biblioteca@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos', 'Bibliotecário', '+244 923 456 707', 8, 'staff', 1, NOW()),

-- Encarregados de Educação (role_id 6)
(9, 'encarregado.fatima', 'fatima.encarregado@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fátima', 'Encarregada', '+244 923 456 708', 6, 'staff', 1, NOW()),
(10, 'encarregado.antonio', 'antonio.encarregado@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'António', 'Encarregado', '+244 923 456 709', 6, 'staff', 1, NOW());

-- ========================================================
-- 2. CRIAR PERÍODOS DE EXAME
-- ========================================================

-- 2.1 Períodos para o 1º Trimestre
INSERT INTO `tbl_exam_periods` (`period_name`, `academic_year_id`, `semester_id`, `period_type`, `start_date`, `end_date`, `status`, `created_at`) VALUES
('1ª Época - 1º Trimestre 2024', 1, 1, 'Normal', '2024-04-15', '2024-04-26', 'Planejado', NOW()),
('2ª Época - 1º Trimestre 2024', 1, 1, 'Recurso', '2024-04-29', '2024-05-03', 'Planejado', NOW());

-- 2.2 Períodos para o 2º Trimestre
INSERT INTO `tbl_exam_periods` (`period_name`, `academic_year_id`, `semester_id`, `period_type`, `start_date`, `end_date`, `status`, `created_at`) VALUES
('1ª Época - 2º Trimestre 2024', 1, 2, 'Normal', '2024-07-29', '2024-08-09', 'Planejado', NOW()),
('2ª Época - 2º Trimestre 2024', 1, 2, 'Recurso', '2024-08-12', '2024-08-16', 'Planejado', NOW());

-- 2.3 Períodos para o 3º Trimestre
INSERT INTO `tbl_exam_periods` (`period_name`, `academic_year_id`, `semester_id`, `period_type`, `start_date`, `end_date`, `status`, `created_at`) VALUES
('1ª Época - 3º Trimestre 2024', 1, 3, 'Normal', '2024-11-25', '2024-12-13', 'Planejado', NOW()),
('2ª Época - 3º Trimestre 2024', 1, 3, 'Recurso', '2024-12-16', '2024-12-20', 'Planejado', NOW());

-- ========================================================
-- 3. CRIAR AGENDAMENTOS DE EXAMES (Provas)
-- ========================================================

-- NOTA: Os IDs dos exam_boards são:
-- AC (Avaliação Contínua) = 1
-- NPP (Prova do Professor) = 2
-- NPT (Prova Trimestral) = 3
-- EX-FIN (Exame Final) = 4
-- REC (Recurso) = 5

-- ========================================================
-- 3.1 Provas do Professor (NPP) - Período ID 1 (1ª Época)
-- ========================================================
INSERT INTO `tbl_exam_schedules` (`exam_period_id`, `class_id`, `discipline_id`, `exam_board_id`, `exam_date`, `exam_time`, `exam_room`, `duration_minutes`, `status`, `created_at`) VALUES
-- Turmas de Iniciação (class_id 1-6)
(1, 1, 1, 2, '2024-04-15', '08:30:00', 'Sala 101', 60, 'Agendado', NOW()),
(1, 1, 2, 2, '2024-04-16', '08:30:00', 'Sala 101', 60, 'Agendado', NOW()),
(1, 1, 3, 2, '2024-04-17', '08:30:00', 'Sala 101', 45, 'Agendado', NOW()),

-- Turmas Primário (class_id 7-18)
(1, 7, 1, 2, '2024-04-15', '10:30:00', 'Sala 201', 90, 'Agendado', NOW()),
(1, 7, 2, 2, '2024-04-16', '10:30:00', 'Sala 201', 90, 'Agendado', NOW()),
(1, 7, 3, 2, '2024-04-17', '10:30:00', 'Sala 201', 60, 'Agendado', NOW()),
(1, 7, 4, 2, '2024-04-18', '10:30:00', 'Sala 201', 60, 'Agendado', NOW()),
(1, 7, 5, 2, '2024-04-19', '10:30:00', 'Sala 201', 60, 'Agendado', NOW()),

-- Turmas I Ciclo (class_id 13-18)
(1, 13, 1, 2, '2024-04-15', '13:30:00', 'Sala 301', 120, 'Agendado', NOW()),
(1, 13, 2, 2, '2024-04-16', '13:30:00', 'Sala 301', 120, 'Agendado', NOW()),
(1, 13, 11, 2, '2024-04-17', '13:30:00', 'Sala 301', 90, 'Agendado', NOW()),
(1, 13, 12, 2, '2024-04-18', '13:30:00', 'Sala 301', 90, 'Agendado', NOW()),
(1, 13, 13, 2, '2024-04-19', '13:30:00', 'Sala 301', 90, 'Agendado', NOW()),

-- Turmas II Ciclo (class_id 17-28)
(1, 17, 1, 2, '2024-04-15', '15:30:00', 'Sala 401', 120, 'Agendado', NOW()),
(1, 17, 2, 2, '2024-04-16', '15:30:00', 'Sala 401', 120, 'Agendado', NOW()),
(1, 17, 11, 2, '2024-04-17', '15:30:00', 'Sala 401', 120, 'Agendado', NOW()),
(1, 17, 12, 2, '2024-04-18', '15:30:00', 'Sala 401', 120, 'Agendado', NOW()),
(1, 17, 13, 2, '2024-04-19', '15:30:00', 'Sala 401', 120, 'Agendado', NOW());

-- ========================================================
-- 3.2 Provas Trimestrais (NPT) - Período ID 3 (outro período)
-- NOTA: Usando exam_period_id = 3 (3º período criado)
-- ========================================================
INSERT INTO `tbl_exam_schedules` (`exam_period_id`, `class_id`, `discipline_id`, `exam_board_id`, `exam_date`, `exam_time`, `exam_room`, `duration_minutes`, `status`, `created_at`) VALUES
-- Turmas Iniciação (NPT)
(3, 1, 1, 3, '2024-04-22', '08:30:00', 'Sala 101', 90, 'Agendado', NOW()),
(3, 1, 2, 3, '2024-04-23', '08:30:00', 'Sala 101', 90, 'Agendado', NOW()),
(3, 1, 3, 3, '2024-04-24', '08:30:00', 'Sala 101', 60, 'Agendado', NOW()),

-- Turmas Primário (NPT)
(3, 7, 1, 3, '2024-04-22', '10:30:00', 'Sala 201', 120, 'Agendado', NOW()),
(3, 7, 2, 3, '2024-04-23', '10:30:00', 'Sala 201', 120, 'Agendado', NOW()),
(3, 7, 3, 3, '2024-04-24', '10:30:00', 'Sala 201', 90, 'Agendado', NOW()),
(3, 7, 4, 3, '2024-04-25', '10:30:00', 'Sala 201', 90, 'Agendado', NOW()),

-- Turmas I Ciclo (NPT)
(3, 13, 1, 3, '2024-04-22', '13:30:00', 'Sala 301', 150, 'Agendado', NOW()),
(3, 13, 2, 3, '2024-04-23', '13:30:00', 'Sala 301', 150, 'Agendado', NOW()),
(3, 13, 11, 3, '2024-04-24', '13:30:00', 'Sala 301', 120, 'Agendado', NOW()),
(3, 13, 12, 3, '2024-04-25', '13:30:00', 'Sala 301', 120, 'Agendado', NOW()),

-- Turmas II Ciclo (NPT)
(3, 17, 1, 3, '2024-04-22', '15:30:00', 'Sala 401', 150, 'Agendado', NOW()),
(3, 17, 2, 3, '2024-04-23', '15:30:00', 'Sala 401', 150, 'Agendado', NOW()),
(3, 17, 11, 3, '2024-04-24', '15:30:00', 'Sala 401', 150, 'Agendado', NOW()),
(3, 17, 12, 3, '2024-04-25', '15:30:00', 'Sala 401', 150, 'Agendado', NOW());

-- ========================================================
-- 3.3 Provas de Recurso (REC) - Período ID 2 (2ª Época)
-- ========================================================
INSERT INTO `tbl_exam_schedules` (`exam_period_id`, `class_id`, `discipline_id`, `exam_board_id`, `exam_date`, `exam_time`, `exam_room`, `duration_minutes`, `status`, `created_at`) VALUES
(2, 7, 1, 5, '2024-04-29', '10:30:00', 'Sala 201', 120, 'Agendado', NOW()),
(2, 7, 2, 5, '2024-04-30', '10:30:00', 'Sala 201', 120, 'Agendado', NOW()),
(2, 13, 1, 5, '2024-04-29', '13:30:00', 'Sala 301', 150, 'Agendado', NOW()),
(2, 13, 2, 5, '2024-04-30', '13:30:00', 'Sala 301', 150, 'Agendado', NOW()),
(2, 17, 1, 5, '2024-04-29', '15:30:00', 'Sala 401', 150, 'Agendado', NOW()),
(2, 17, 2, 5, '2024-04-30', '15:30:00', 'Sala 401', 150, 'Agendado', NOW());

-- ========================================================
-- 4. ATRIBUIR PROFESSORES ÀS DISCIPLINAS DAS TURMAS
-- ========================================================

-- Associar professores às disciplinas das turmas
INSERT INTO `tbl_class_disciplines` (`class_id`, `discipline_id`, `teacher_id`, `period_type`, `workload_hours`, `created_at`) VALUES
-- Turma INI-01A (class_id 1)
(1, 1, 12, 'Anual', 120, NOW()),  -- LP - Prof. Maria
(1, 2, 11, 'Anual', 120, NOW()),  -- MAT - Prof. João
(1, 3, 14, 'Anual', 90, NOW()),   -- CN - Prof. Ana
(1, 6, 19, 'Anual', 60, NOW()),   -- EDF - Prof. António
(1, 7, 20, 'Anual', 45, NOW()),   -- EMC - Prof. Fátima

-- Turma PRI-01A (class_id 7)
(7, 1, 12, 'Anual', 150, NOW()),  -- LP - Prof. Maria
(7, 2, 11, 'Anual', 150, NOW()),  -- MAT - Prof. João
(7, 3, 14, 'Anual', 90, NOW()),   -- CN - Prof. Ana
(7, 4, 15, 'Anual', 60, NOW()),   -- HIS - Prof. Pedro
(7, 5, 16, 'Anual', 60, NOW()),   -- GEO - Prof. Isabel
(7, 6, 19, 'Anual', 60, NOW()),   -- EDF - Prof. António
(7, 10, 13, 'Anual', 60, NOW()),  -- EMP - Prof. Manuel

-- Turma CIC-07A (class_id 13)
(13, 1, 12, 'Anual', 180, NOW()),  -- LP - Prof. Maria
(13, 2, 11, 'Anual', 180, NOW()),  -- MAT - Prof. João
(13, 11, 13, 'Anual', 90, NOW()),  -- FIS - Prof. Manuel
(13, 12, 17, 'Anual', 90, NOW()),  -- QUI - Prof. Carlos
(13, 13, 14, 'Anual', 90, NOW()),  -- BIO - Prof. Ana
(13, 4, 15, 'Anual', 90, NOW()),   -- HIS - Prof. Pedro
(13, 5, 16, 'Anual', 90, NOW()),   -- GEO - Prof. Isabel
(13, 8, 18, 'Anual', 90, NOW()),   -- ING - Prof. Lúcia
(13, 9, 12, 'Anual', 90, NOW()),   -- FRA - Prof. Maria
(13, 6, 19, 'Anual', 60, NOW()),   -- EDF - Prof. António

-- Turma CFB-10A (class_id 17)
(17, 1, 12, 'Anual', 120, NOW()),  -- LP - Prof. Maria
(17, 2, 11, 'Anual', 180, NOW()),  -- MAT - Prof. João
(17, 11, 13, 'Anual', 150, NOW()), -- FIS - Prof. Manuel
(17, 12, 17, 'Anual', 150, NOW()), -- QUI - Prof. Carlos
(17, 13, 14, 'Anual', 150, NOW()), -- BIO - Prof. Ana
(17, 24, 16, 'Anual', 90, NOW()),  -- GEO-CFB - Prof. Isabel
(17, 8, 18, 'Anual', 90, NOW()),   -- ING - Prof. Lúcia
(17, 6, 19, 'Anual', 60, NOW());   -- EDF - Prof. António

-- ========================================================
-- 5. ATUALIZAR PERÍODOS PARA "EM ANDAMENTO" (opcional)
-- ========================================================

-- Marcar o período atual como "Em Andamento"
UPDATE `tbl_exam_periods` SET `status` = 'Em Andamento' WHERE `id` = 1;

-- ========================================================
-- FIM DO SCRIPT
-- ========================================================
COMMIT;