-- --------------------------------------------------------
-- 1. TABELAS BASE (já existentes)
-- --------------------------------------------------------
-- tbl_academic_years, tbl_semesters, tbl_classes, 
-- tbl_disciplines, tbl_class_disciplines, tbl_enrollments
-- tbl_exam_boards, tbl_exam_results

-- --------------------------------------------------------
-- 2. NOVAS TABELAS PARA GESTÃO DE EXAMES
-- --------------------------------------------------------

-- 2.1. Períodos de Exame (organização por época)
CREATE TABLE IF NOT EXISTS `tbl_exam_periods` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `period_name` VARCHAR(255) NOT NULL COMMENT 'Ex: 1ª Época - 1º Semestre 2024',
    `academic_year_id` INT(11) NOT NULL,
    `semester_id` INT(11) NOT NULL,
    `period_type` ENUM('Normal','Recurso','Especial','Final','Admissão') NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `description` TEXT,
    `status` ENUM('Planejado','Em Andamento','Concluído','Cancelado') DEFAULT 'Planejado',
    `is_published` TINYINT(1) DEFAULT '0',
    `created_by` INT(11),
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `academic_year_id` (`academic_year_id`),
    KEY `semester_id` (`semester_id`),
    KEY `period_type` (`period_type`),
    CONSTRAINT `fk_exam_periods_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_exam_periods_semester` FOREIGN KEY (`semester_id`) REFERENCES `tbl_semesters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.2. Calendário de Exames (agendamento por disciplina)
CREATE TABLE IF NOT EXISTS `tbl_exam_schedules` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `exam_period_id` INT(11) NOT NULL,
    `class_id` INT(11) NOT NULL,
    `discipline_id` INT(11) NOT NULL,
    `exam_board_id` INT(11) NOT NULL,
    `exam_date` DATE NOT NULL,
    `exam_time` TIME,
    `exam_room` VARCHAR(50),
    `duration_minutes` INT(4) DEFAULT '120',
    `max_score` DECIMAL(5,2) DEFAULT '20.00',
    `min_score` DECIMAL(5,2) DEFAULT '0.00',
    `approval_score` DECIMAL(5,2) DEFAULT '10.00',
    `observations` TEXT,
    `status` ENUM('Agendado','Realizado','Cancelado','Adiado') DEFAULT 'Agendado',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_exam_schedule` (`exam_period_id`, `class_id`, `discipline_id`),
    KEY `class_id` (`class_id`),
    KEY `discipline_id` (`discipline_id`),
    KEY `exam_board_id` (`exam_board_id`),
    KEY `exam_date` (`exam_date`),
    CONSTRAINT `fk_exam_schedules_period` FOREIGN KEY (`exam_period_id`) REFERENCES `tbl_exam_periods` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_exam_schedules_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_exam_schedules_discipline` FOREIGN KEY (`discipline_id`) REFERENCES `tbl_disciplines` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_exam_schedules_board` FOREIGN KEY (`exam_board_id`) REFERENCES `tbl_exam_boards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.3. Resultados de Exames (já existe - apenas ajustes)
ALTER TABLE `tbl_exam_results` 
ADD COLUMN `exam_schedule_id` INT(11) NULL AFTER `exam_id`,
ADD COLUMN `is_absent` TINYINT(1) DEFAULT '0' AFTER `score`,
ADD COLUMN `is_cheating` TINYINT(1) DEFAULT '0' AFTER `is_absent`,
ADD COLUMN `verified_by` INT(11) NULL AFTER `recorded_by`,
ADD COLUMN `verified_at` DATETIME NULL AFTER `verified_by`,
ADD INDEX `idx_exam_schedule` (`exam_schedule_id`);

-- 2.4. Histórico de presenças em exames
CREATE TABLE IF NOT EXISTS `tbl_exam_attendance` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `exam_schedule_id` INT(11) NOT NULL,
    `enrollment_id` INT(11) NOT NULL,
    `attended` TINYINT(1) DEFAULT '0',
    `check_in_time` DATETIME,
    `check_in_method` ENUM('Manual','Biometria','QR Code','Cartão') DEFAULT 'Manual',
    `observations` TEXT,
    `recorded_by` INT(11),
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_attendance` (`exam_schedule_id`, `enrollment_id`),
    KEY `enrollment_id` (`enrollment_id`),
    CONSTRAINT `fk_exam_attendance_schedule` FOREIGN KEY (`exam_schedule_id`) REFERENCES `tbl_exam_schedules` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_exam_attendance_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `tbl_enrollments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. TABELAS PARA AVALIAÇÃO CONTÍNUA (DESCONTINUAR)
-- --------------------------------------------------------
-- NOTA: tbl_continuous_assessment será descontinuada
-- As notas de AC serão migradas para tbl_exam_results com exam_board_id apropriado

-- --------------------------------------------------------
-- 4. TABELAS PARA CÁLCULO DE MÉDIAS
-- --------------------------------------------------------

-- 4.1. Configuração de pesos por tipo de avaliação
CREATE TABLE IF NOT EXISTS `tbl_grade_weights` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `academic_year_id` INT(11) NOT NULL,
    `grade_level_id` INT(11) NOT NULL,
    `exam_board_id` INT(11) NOT NULL,
    `weight_percentage` DECIMAL(5,2) NOT NULL COMMENT 'Peso percentual (ex: 30% para ACs, 70% para Exame)',
    `is_mandatory` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_weight` (`academic_year_id`, `grade_level_id`, `exam_board_id`),
    KEY `exam_board_id` (`exam_board_id`),
    CONSTRAINT `fk_grade_weights_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_grade_weights_level` FOREIGN KEY (`grade_level_id`) REFERENCES `tbl_grade_levels` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_grade_weights_board` FOREIGN KEY (`exam_board_id`) REFERENCES `tbl_exam_boards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.2. Médias calculadas por disciplina
CREATE TABLE IF NOT EXISTS `tbl_discipline_averages` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `enrollment_id` INT(11) NOT NULL,
    `discipline_id` INT(11) NOT NULL,
    `semester_id` INT(11) NOT NULL,
    `ac_score` DECIMAL(5,2) COMMENT 'Média das Avaliações Contínuas',
    `exam_score` DECIMAL(5,2) COMMENT 'Nota do Exame (quando aplicável)',
    `final_score` DECIMAL(5,2) COMMENT 'Média Final Ponderada',
    `status` ENUM('Aprovado','Reprovado','Recurso','Dispensado','Em Andamento') DEFAULT 'Em Andamento',
    `observations` TEXT,
    `calculated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `calculated_by` INT(11),
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_discipline_avg` (`enrollment_id`, `discipline_id`, `semester_id`),
    KEY `discipline_id` (`discipline_id`),
    KEY `semester_id` (`semester_id`),
    KEY `status` (`status`),
    CONSTRAINT `fk_discipline_averages_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `tbl_enrollments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_discipline_averages_discipline` FOREIGN KEY (`discipline_id`) REFERENCES `tbl_disciplines` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_discipline_averages_semester` FOREIGN KEY (`semester_id`) REFERENCES `tbl_semesters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.3. Resultados finais (substitui tbl_final_grades)
CREATE TABLE IF NOT EXISTS `tbl_semester_results` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `enrollment_id` INT(11) NOT NULL,
    `semester_id` INT(11) NOT NULL,
    `overall_average` DECIMAL(5,2) COMMENT 'Média geral do semestre',
    `total_disciplines` INT(3) COMMENT 'Total de disciplinas',
    `approved_disciplines` INT(3) COMMENT 'Disciplinas aprovadas',
    `failed_disciplines` INT(3) COMMENT 'Disciplinas reprovadas',
    `appeal_disciplines` INT(3) COMMENT 'Disciplinas em recurso',
    `status` ENUM('Aprovado','Reprovado','Recurso','Em Andamento') DEFAULT 'Em Andamento',
    `observations` TEXT,
    `calculated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `calculated_by` INT(11),
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_semester_result` (`enrollment_id`, `semester_id`),
    KEY `semester_id` (`semester_id`),
    KEY `status` (`status`),
    CONSTRAINT `fk_semester_results_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `tbl_enrollments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_semester_results_semester` FOREIGN KEY (`semester_id`) REFERENCES `tbl_semesters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.4. Resultados anuais (já existe - tbl_academic_history, ajustar)
ALTER TABLE `tbl_academic_history` 
ADD COLUMN `first_semester_average` DECIMAL(5,2) NULL AFTER `final_average`,
ADD COLUMN `second_semester_average` DECIMAL(5,2) NULL AFTER `first_semester_average`,
ADD COLUMN `appeal_exams_done` INT(2) DEFAULT '0' AFTER `total_absences`;

-- --------------------------------------------------------
-- 5. TABELAS DE APOIO
-- --------------------------------------------------------

-- 5.1. Conflitos de horário
CREATE TABLE IF NOT EXISTS `tbl_exam_conflicts` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `exam_schedule_id` INT(11) NOT NULL,
    `student_id` INT(11) NOT NULL,
    `conflict_type` ENUM('Mesmo Horário','3+ Exames Seguidos','Outro') NOT NULL,
    `conflicting_exam_id` INT(11),
    `resolution_status` ENUM('Pendente','Resolvido','Ignorado') DEFAULT 'Pendente',
    `resolution_notes` TEXT,
    `resolved_by` INT(11),
    `resolved_at` DATETIME,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `exam_schedule_id` (`exam_schedule_id`),
    KEY `student_id` (`student_id`),
    KEY `conflicting_exam_id` (`conflicting_exam_id`),
    CONSTRAINT `fk_exam_conflicts_schedule` FOREIGN KEY (`exam_schedule_id`) REFERENCES `tbl_exam_schedules` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_exam_conflicts_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5.2. Vigilância de exames
CREATE TABLE IF NOT EXISTS `tbl_exam_supervisors` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `exam_schedule_id` INT(11) NOT NULL,
    `teacher_id` INT(11) NOT NULL,
    `role` ENUM('Principal','Auxiliar','Suplente') DEFAULT 'Auxiliar',
    `confirmed` TINYINT(1) DEFAULT '0',
    `notes` TEXT,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_supervisor` (`exam_schedule_id`, `teacher_id`),
    KEY `teacher_id` (`teacher_id`),
    CONSTRAINT `fk_exam_supervisors_schedule` FOREIGN KEY (`exam_schedule_id`) REFERENCES `tbl_exam_schedules` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_exam_supervisors_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `tbl_teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;