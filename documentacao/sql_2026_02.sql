-- --------------------------------------------------------
-- Sistema de Gestão Escolar Angolana
-- Compatível com MySQL 5.7+
-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS escola_angolana;
USE escola_angolana;

-- --------------------------------------------------------
-- Tabelas de Configuração Base
-- --------------------------------------------------------

CREATE TABLE `tbl_settings` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `value` TEXT,
    `status` TINYINT(1) DEFAULT '1',
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_currencies` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `currency_name` VARCHAR(100) NOT NULL,
    `currency_code` VARCHAR(3) NOT NULL,
    `currency_symbol` VARCHAR(10) NOT NULL,
    `is_default` TINYINT(1) DEFAULT '0',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `currency_code` (`currency_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabelas de Usuários e Permissões
-- --------------------------------------------------------

CREATE TABLE `tbl_roles` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `role_name` VARCHAR(100) NOT NULL,
    `role_description` TEXT,
    `role_type` ENUM('admin','teacher','student','staff') NOT NULL DEFAULT 'staff',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_permissions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `permission_name` VARCHAR(255) NOT NULL,
    `permission_key` VARCHAR(255) NOT NULL,
    `module` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `permission_key` (`permission_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_role_permissions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `role_id` INT(11) NOT NULL,
    `permission_id` INT(11) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `role_id` (`role_id`),
    KEY `permission_id` (`permission_id`),
    CONSTRAINT `fk_role_permissions_role` FOREIGN KEY (`role_id`) REFERENCES `tbl_roles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_role_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `tbl_permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20),
    `address` TEXT,
    `photo` VARCHAR(255),
    `role_id` INT(11) NOT NULL,
    `user_type` ENUM('admin','teacher','student','guardian','staff') NOT NULL DEFAULT 'staff',
    `is_active` TINYINT(1) DEFAULT '1',
    `last_login` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `email` (`email`),
    KEY `role_id` (`role_id`),
    KEY `user_type` (`user_type`),
    CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `tbl_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_user_logs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `action` VARCHAR(255) NOT NULL,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `description` TEXT NULL,
    `target_id` INT(11) NULL,
    `target_type` VARCHAR(50) NULL,
    `request_method` VARCHAR(10) NULL,
    `request_url` VARCHAR(500) NULL,
    `details` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `created_at` (`created_at`),
    CONSTRAINT `fk_user_logs_user` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------
-- Tabelas de Configuração Académica
-- --------------------------------------------------------

CREATE TABLE `tbl_academic_years` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `year_name` VARCHAR(50) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `is_current` TINYINT(1) DEFAULT '0',
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `year_name` (`year_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `tbl_semesters` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `academic_year_id` INT(11) NOT NULL,
    `semester_name` VARCHAR(50) NOT NULL,
    `semester_type` ENUM('1º Trimestre','2º Trimestre','3º Trimestre','1º Semestre','2º Semestre') NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `is_current` TINYINT(1) DEFAULT '0',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `academic_year_id` (`academic_year_id`),
    CONSTRAINT `fk_semesters_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `tbl_semesters` 
ADD COLUMN `status` ENUM('ativo', 'inativo', 'processado',  'concluido') NOT NULL DEFAULT 'ativo' AFTER `is_current`,
ADD INDEX `idx_semester_status` (`status`);

CREATE TABLE `tbl_grade_levels` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `level_name` VARCHAR(100) NOT NULL,
    `level_code` VARCHAR(20) NOT NULL,
    `education_level` ENUM('Iniciação','Primário','1º Ciclo','2º Ciclo','Ensino Médio') NOT NULL,
    `grade_number` INT(2) NOT NULL,
    `sort_order` INT(3) DEFAULT '0',
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `level_code` (`level_code`),
    KEY `education_level` (`education_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_classes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `class_name` VARCHAR(100) NOT NULL,
    `class_code` VARCHAR(50) NOT NULL,
    `grade_level_id` INT(11) NOT NULL,
    `academic_year_id` INT(11) NOT NULL,
    `class_shift` ENUM('Manhã','Tarde','Noite','Integral') NOT NULL,
    `class_room` VARCHAR(50),
    `capacity` INT(4) DEFAULT '30',
    `class_teacher_id` INT(11),
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `class_code` (`class_code`),
    KEY `grade_level_id` (`grade_level_id`),
    KEY `academic_year_id` (`academic_year_id`),
    KEY `class_teacher_id` (`class_teacher_id`),
    CONSTRAINT `fk_classes_grade_level` FOREIGN KEY (`grade_level_id`) REFERENCES `tbl_grade_levels` (`id`),
    CONSTRAINT `fk_classes_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`id`),
    CONSTRAINT `fk_classes_teacher` FOREIGN KEY (`class_teacher_id`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_disciplines` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `discipline_name` VARCHAR(255) NOT NULL,
    `discipline_code` VARCHAR(50) NOT NULL,
    `discipline_type` ENUM('Obrigatória','Opcional','Complementar') NOT NULL DEFAULT 'Obrigatória',
    `workload_hours` INT(4),
    `min_grade` DECIMAL(5,2) DEFAULT '0.00',
    `max_grade` DECIMAL(5,2) DEFAULT '20.00',
    `approval_grade` DECIMAL(5,2) DEFAULT '10.00',
    `description` TEXT,
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `discipline_code` (`discipline_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `tbl_class_disciplines` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `class_id` INT(11) NOT NULL,
    `discipline_id` INT(11) NOT NULL,
    `teacher_id` INT(11),
    `period_type` ENUM('Anual','1º Semestre','2º Semestre') DEFAULT 'Anual',
    `workload_hours` INT(4),
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `class_discipline` (`class_id`, `discipline_id`),
    KEY `discipline_id` (`discipline_id`),
    KEY `teacher_id` (`teacher_id`),
    CONSTRAINT `fk_class_disciplines_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_class_disciplines_discipline` FOREIGN KEY (`discipline_id`) REFERENCES `tbl_disciplines` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_class_disciplines_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------
-- Tabelas de Alunos e Encarregados
-- --------------------------------------------------------

CREATE TABLE `tbl_students` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `student_number` VARCHAR(50) NOT NULL,
    `birth_date` DATE,
    `birth_place` VARCHAR(255),
    `gender` ENUM('Masculino','Feminino') NOT NULL,
    `nationality` VARCHAR(100) DEFAULT 'Angolana',
    `identity_document` VARCHAR(50),
    `identity_type` ENUM('BI','Passaporte','Cédula','Outro') DEFAULT 'BI',
    `nif` VARCHAR(20),
    `address` TEXT,
    `city` VARCHAR(100),
    `municipality` VARCHAR(100),
    `province` VARCHAR(100),
    `phone` VARCHAR(20),
    `emergency_contact` VARCHAR(20),
    `emergency_contact_name` VARCHAR(255),
    `previous_school` VARCHAR(255),
    `previous_grade` VARCHAR(50),
    `special_needs` TEXT,
    `health_conditions` TEXT,
    `blood_type` VARCHAR(5),
    `photo` VARCHAR(255),
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `student_number` (`student_number`),
    UNIQUE KEY `user_id` (`user_id`),
    KEY `nif` (`nif`),
    KEY `identity_document` (`identity_document`),
    CONSTRAINT `fk_students_user` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_guardians` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11),
    `guardian_type` ENUM('Pai','Mãe','Tutor','Encarregado','Outro') NOT NULL,
    `full_name` VARCHAR(255) NOT NULL,
    `identity_document` VARCHAR(50),
    `identity_type` ENUM('BI','Passaporte','Cédula','Outro') DEFAULT 'BI',
    `nif` VARCHAR(20),
    `profession` VARCHAR(255),
    `workplace` VARCHAR(255),
    `work_phone` VARCHAR(20),
    `phone` VARCHAR(20) NOT NULL,
    `email` VARCHAR(255),
    `address` TEXT,
    `city` VARCHAR(100),
    `municipality` VARCHAR(100),
    `province` VARCHAR(100),
    `is_primary` TINYINT(1) DEFAULT '1',
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `identity_document` (`identity_document`),
    CONSTRAINT `fk_guardians_user` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_student_guardians` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `student_id` INT(11) NOT NULL,
    `guardian_id` INT(11) NOT NULL,
    `relationship` VARCHAR(100),
    `is_authorized` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `student_guardian` (`student_id`, `guardian_id`),
    KEY `guardian_id` (`guardian_id`),
    CONSTRAINT `fk_student_guardians_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_student_guardians_guardian` FOREIGN KEY (`guardian_id`) REFERENCES `tbl_guardians` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





-- --------------------------------------------------------
-- Tabela de Cursos (para o Ensino Médio)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_courses` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `course_name` VARCHAR(100) NOT NULL,
    `course_code` VARCHAR(20) NOT NULL,
    `course_type` ENUM('Ciências', 'Humanidades', 'Económico-Jurídico', 'Técnico', 'Profissional', 'Outro') NOT NULL,
    `description` TEXT,
    `duration_years` INT(2) DEFAULT '3' COMMENT 'Duração em anos (normalmente 3)',
    `start_grade_id` INT(11) NOT NULL COMMENT 'Nível inicial (ex: 10ª classe)',
    `end_grade_id` INT(11) NOT NULL COMMENT 'Nível final (ex: 12ª ou 13ª classe)',
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `course_code` (`course_code`),
    KEY `start_grade_id` (`start_grade_id`),
    KEY `end_grade_id` (`end_grade_id`),
    CONSTRAINT `fk_courses_start_grade` FOREIGN KEY (`start_grade_id`) REFERENCES `tbl_grade_levels` (`id`),
    CONSTRAINT `fk_courses_end_grade` FOREIGN KEY (`end_grade_id`) REFERENCES `tbl_grade_levels` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de Associação Curso x Disciplinas
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_course_disciplines` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `course_id` INT(11) NOT NULL,
    `discipline_id` INT(11) NOT NULL,
    `grade_level_id` INT(11) NOT NULL COMMENT 'Classe específica onde a disciplina é lecionada',
    `workload_hours` INT(4),
    `is_mandatory` TINYINT(1) DEFAULT '1',
    `semester` ENUM('1', '2', '3', 'Anual') DEFAULT 'Anual',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `course_discipline_grade` (`course_id`, `discipline_id`, `grade_level_id`),
    KEY `discipline_id` (`discipline_id`),
    KEY `grade_level_id` (`grade_level_id`),
    CONSTRAINT `fk_course_disciplines_course` FOREIGN KEY (`course_id`) REFERENCES `tbl_courses` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_course_disciplines_discipline` FOREIGN KEY (`discipline_id`) REFERENCES `tbl_disciplines` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_course_disciplines_grade` FOREIGN KEY (`grade_level_id`) REFERENCES `tbl_grade_levels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabelas de Matrículas
-- --------------------------------------------------------

CREATE TABLE `tbl_enrollments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `student_id` INT(11) NOT NULL,
    `class_id` INT(11),
    `academic_year_id` INT(11) NOT NULL,
    `enrollment_date` DATE NOT NULL,
    `enrollment_number` VARCHAR(50) NOT NULL,
    `enrollment_type` ENUM('Nova','Renovação','Transferência') NOT NULL,
    `previous_class_id` INT(11),
    `final_average` DECIMAL(5,2) NULL,
    `completion_date` DATE NULL,
    `certificate_number` VARCHAR(50) NULL,
    `grade_level_id` INT NOT NULL,
    `previous_grade_id` INT NULL,
    `course_id` INT(11) NULL,
    `status` ENUM('Pendente','Ativo','Concluído','Transferido','Anulado') NOT NULL DEFAULT 'Pendente',
    `final_result` ENUM('Aprovado', 'Reprovado', 'Recurso', 'Em Andamento', 'Dispensado') NULL DEFAULT 'Em Andamento',
    `observations` TEXT,
    `created_by` INT(11),
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `enrollment_number` (`enrollment_number`),
    UNIQUE KEY `student_class_year` (`student_id`, `class_id`, `academic_year_id`),
    KEY `class_id` (`class_id`),
    KEY `academic_year_id` (`academic_year_id`),
    KEY `status` (`status`),
    KEY `course_id` (`course_id`),
    KEY `created_by` (`created_by`),
    FOREIGN KEY (`grade_level_id`) REFERENCES `tbl_grade_levels`(`id`),
    FOREIGN KEY (`previous_grade_id`) REFERENCES `tbl_grade_levels`(`id`),
    FOREIGN KEY (`course_id`) REFERENCES `tbl_courses` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_enrollments_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_enrollments_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_enrollments_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_enrollments_previous_class` FOREIGN KEY (`previous_class_id`) REFERENCES `tbl_classes` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_enrollments_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adicionar campos para controle de resultado final


CREATE TABLE `tbl_enrollment_documents` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `enrollment_id` INT(11) NOT NULL,
    `document_type` VARCHAR(100) NOT NULL,
    `document_path` VARCHAR(255) NOT NULL,
    `uploaded_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `enrollment_id` (`enrollment_id`),
    CONSTRAINT `fk_enrollment_documents_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `tbl_enrollments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabelas de Presenças
-- --------------------------------------------------------

CREATE TABLE `tbl_attendance` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `enrollment_id` INT(11) NOT NULL,
    `class_id` INT(11) NOT NULL,
    `discipline_id` INT(11),
    `semester_id` INT(11),
    `attendance_date` DATE NOT NULL,
    `status` ENUM('Presente','Ausente','Atrasado','Dispensado','Falta Justificada') NOT NULL,
    `justification` TEXT,
    `marked_by` INT(11),
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `attendance_unique` (`enrollment_id`, `class_id`, `attendance_date`, `discipline_id`),
    KEY `class_id` (`class_id`),
    KEY `discipline_id` (`discipline_id`),
    KEY `attendance_date` (`attendance_date`),
    KEY `marked_by` (`marked_by`),
    CONSTRAINT `fk_attendance_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `tbl_enrollments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_attendance_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_attendance_discipline` FOREIGN KEY (`discipline_id`) REFERENCES `tbl_disciplines` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_attendance_marked_by` FOREIGN KEY (`marked_by`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------
-- Tabelas de Avaliações e Notas
-- --------------------------------------------------------

CREATE TABLE `tbl_exam_boards` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `board_name` VARCHAR(255) NOT NULL,
    `board_code` VARCHAR(50) NOT NULL,
    `board_type` ENUM(
    'Avaliação Contínua',  -- MAC
    'Prova Professor',     -- NPP
    'Prova Trimestral',    -- NPT
    'Exame Final',         -- E
    'Recurso',             -- Exame Recurso
    'Especial',            -- Exame Especial
    'Combinado'            -- MEC (Escrito+Oral+Prático)
) NOT NULL,
    `weight` DECIMAL(5,2) DEFAULT '1.00',
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `board_code` (`board_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `tbl_exam_results` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `enrollment_id` INT(11) NOT NULL,
    `exam_schedule_id` INT(11) NULL,
    `assessment_type` ENUM(
    'AC',      -- Avaliação Contínua
    'NPP',     -- Prova Professor
    'NPT',     -- Prova Trimestral
    'E',       -- Exame Final
    'NEE',     -- Exame Combinado Escrito
    'NEO',     -- Exame Combinado Oral
    'NEP',     -- Exame Combinado Prático
    'PAP',     -- Prova Aptidão Profissional
    'NEC'      -- Nota Estágio Curricular
) NOT NULL,
    `is_absent` TINYINT(1) DEFAULT '0',
    `is_cheating` TINYINT(1) DEFAULT '0',
    `verified_by` INT(11) NULL,
    `verified_at` DATETIME NULL,
    `score` DECIMAL(5,2) NOT NULL,
    `score_percentage` DECIMAL(5,2),
    `grade` VARCHAR(5),
    `observations` TEXT,
    `recorded_by` INT(11),
    `recorded_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `enrollment_id` (`enrollment_id`),
    KEY `recorded_by` (`recorded_by`),
    UNIQUE KEY `exam_schedule_student` (`exam_schedule_id`, `enrollment_id`),
    CONSTRAINT `fk_exam_results_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `tbl_enrollments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_exam_results_recorded_by` FOREIGN KEY (`recorded_by`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabelas de Propinas e Taxas
-- --------------------------------------------------------

CREATE TABLE `tbl_fee_types` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `type_name` VARCHAR(255) NOT NULL,
    `type_code` VARCHAR(50) NOT NULL,
    `type_category` ENUM('Propina','Matrícula','Inscrição','Taxa de Exame','Taxa Administrativa','Multa','Outro') NOT NULL,
    `is_recurring` TINYINT(1) DEFAULT '0',
    `recurrence_period` ENUM('Mensal','Trimestral','Semestral','Anual') NULL,
    `description` TEXT,
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `type_code` (`type_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_fee_structure` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `academic_year_id` INT(11) NOT NULL,
    `grade_level_id` INT(11) NOT NULL,
    `fee_type_id` INT(11) NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `currency_id` INT(11) DEFAULT '1',
    `due_day` INT(2),
    `description` TEXT,
    `is_mandatory` TINYINT(1) DEFAULT '1',
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `fee_structure_unique` (`academic_year_id`, `grade_level_id`, `fee_type_id`),
    KEY `grade_level_id` (`grade_level_id`),
    KEY `fee_type_id` (`fee_type_id`),
    KEY `currency_id` (`currency_id`),
    CONSTRAINT `fk_fee_structure_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_fee_structure_grade_level` FOREIGN KEY (`grade_level_id`) REFERENCES `tbl_grade_levels` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_fee_structure_fee_type` FOREIGN KEY (`fee_type_id`) REFERENCES `tbl_fee_types` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_fee_structure_currency` FOREIGN KEY (`currency_id`) REFERENCES `tbl_currencies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_student_fees` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `enrollment_id` INT(11) NOT NULL,
    `fee_structure_id` INT(11) NOT NULL,
    `reference_number` VARCHAR(50) NOT NULL,
    `due_date` DATE NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `fine_amount` DECIMAL(10,2) DEFAULT '0.00',
    `discount_amount` DECIMAL(10,2) DEFAULT '0.00',
    `total_amount` DECIMAL(10,2) NOT NULL,
    `status` ENUM('Pendente','Pago','Parcial','Vencido','Cancelado') NOT NULL DEFAULT 'Pendente',
    `payment_method` ENUM('Dinheiro','Transferência','Depósito','Multicaixa','Cheque','Outro') NULL,
    `payment_date` DATE,
    `payment_reference` VARCHAR(100),
    `observations` TEXT,
    `created_by` INT(11),
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `reference_number` (`reference_number`),
    UNIQUE KEY `student_fee_unique` (`enrollment_id`, `fee_structure_id`),
    KEY `fee_structure_id` (`fee_structure_id`),
    KEY `status` (`status`),
    KEY `due_date` (`due_date`),
    KEY `created_by` (`created_by`),
    CONSTRAINT `fk_student_fees_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `tbl_enrollments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_student_fees_structure` FOREIGN KEY (`fee_structure_id`) REFERENCES `tbl_fee_structure` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_student_fees_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_fee_payments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `student_fee_id` INT(11) NOT NULL,
    `payment_number` VARCHAR(50) NOT NULL,
    `payment_date` DATE NOT NULL,
    `amount_paid` DECIMAL(10,2) NOT NULL,
    `payment_method` ENUM('Dinheiro','Transferência','Depósito','Multicaixa','Cheque','Outro') NOT NULL,
    `payment_reference` VARCHAR(100),
    `bank_name` VARCHAR(255),
    `bank_account` VARCHAR(50),
    `check_number` VARCHAR(50),
    `receipt_number` VARCHAR(50),
    `observations` TEXT,
    `received_by` INT(11),
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `payment_number` (`payment_number`),
    KEY `student_fee_id` (`student_fee_id`),
    KEY `received_by` (`received_by`),
    KEY `payment_date` (`payment_date`),
    CONSTRAINT `fk_fee_payments_student_fee` FOREIGN KEY (`student_fee_id`) REFERENCES `tbl_student_fees` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_fee_payments_received_by` FOREIGN KEY (`received_by`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabelas Financeiras (Mantendo estrutura existente)
-- --------------------------------------------------------

CREATE TABLE `tbl_invoices` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `invoice_number` VARCHAR(50) NOT NULL,
    `student_id` INT(11),
    `guardian_id` INT(11),
    `invoice_date` DATE NOT NULL,
    `due_date` DATE NOT NULL,
    `subtotal` DECIMAL(10,2) NOT NULL,
    `tax_amount` DECIMAL(10,2) DEFAULT '0.00',
    `discount_amount` DECIMAL(10,2) DEFAULT '0.00',
    `total_amount` DECIMAL(10,2) NOT NULL,
    `status` ENUM('Rascunho','Emitida','Paga','Parcial','Vencida','Cancelada') NOT NULL DEFAULT 'Rascunho',
    `notes` TEXT,
    `created_by` INT(11),
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `invoice_number` (`invoice_number`),
    KEY `student_id` (`student_id`),
    KEY `guardian_id` (`guardian_id`),
    KEY `status` (`status`),
    CONSTRAINT `fk_invoices_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_invoices_guardian` FOREIGN KEY (`guardian_id`) REFERENCES `tbl_guardians` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_invoice_items` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `invoice_id` INT(11) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `quantity` INT(11) DEFAULT '1',
    `unit_price` DECIMAL(10,2) NOT NULL,
    `tax_rate` DECIMAL(5,2) DEFAULT '0.00',
    `tax_amount` DECIMAL(10,2) DEFAULT '0.00',
    `discount_rate` DECIMAL(5,2) DEFAULT '0.00',
    `discount_amount` DECIMAL(10,2) DEFAULT '0.00',
    `total` DECIMAL(10,2) NOT NULL,
    `fee_type_id` INT(11),
    PRIMARY KEY (`id`),
    KEY `invoice_id` (`invoice_id`),
    KEY `fee_type_id` (`fee_type_id`),
    CONSTRAINT `fk_invoice_items_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `tbl_invoices` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_invoice_items_fee_type` FOREIGN KEY (`fee_type_id`) REFERENCES `tbl_fee_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_payments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `payment_number` VARCHAR(50) NOT NULL,
    `invoice_id` INT(11) NOT NULL,
    `payment_date` DATE NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `payment_method` VARCHAR(50) NOT NULL,
    `payment_reference` VARCHAR(100),
    `notes` TEXT,
    `received_by` INT(11),
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `payment_number` (`payment_number`),
    KEY `invoice_id` (`invoice_id`),
    KEY `received_by` (`received_by`),
    CONSTRAINT `fk_payments_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `tbl_invoices` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_payments_received_by` FOREIGN KEY (`received_by`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabelas de Despesas
-- --------------------------------------------------------

CREATE TABLE `tbl_expense_categories` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `category_name` VARCHAR(255) NOT NULL,
    `category_code` VARCHAR(50) NOT NULL,
    `description` TEXT,
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `category_code` (`category_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_expenses` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `expense_number` VARCHAR(50) NOT NULL,
    `expense_category_id` INT(11) NOT NULL,
    `description` TEXT NOT NULL,
    `expense_date` DATE NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `supplier_name` VARCHAR(255),
    `supplier_nif` VARCHAR(20),
    `invoice_reference` VARCHAR(100),
    `payment_method` VARCHAR(50),
    `payment_status` ENUM('Pendente','Pago','Parcial') DEFAULT 'Pendente',
    `notes` TEXT,
    `approved_by` INT(11),
    `created_by` INT(11),
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `expense_number` (`expense_number`),
    KEY `expense_category_id` (`expense_category_id`),
    KEY `approved_by` (`approved_by`),
    KEY `created_by` (`created_by`),
    CONSTRAINT `fk_expenses_category` FOREIGN KEY (`expense_category_id`) REFERENCES `tbl_expense_categories` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_expenses_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_expenses_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabelas de Inventário
-- --------------------------------------------------------

CREATE TABLE `tbl_product_categories` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `category_name` VARCHAR(255) NOT NULL,
    `category_code` VARCHAR(50) NOT NULL,
    `description` TEXT,
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `category_code` (`category_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_products` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `product_code` VARCHAR(50) NOT NULL,
    `product_name` VARCHAR(255) NOT NULL,
    `product_category_id` INT(11),
    `description` TEXT,
    `unit` VARCHAR(50),
    `purchase_price` DECIMAL(10,2),
    `sale_price` DECIMAL(10,2),
    `current_stock` INT(11) DEFAULT '0',
    `minimum_stock` INT(11) DEFAULT '0',
    `location` VARCHAR(255),
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `product_code` (`product_code`),
    KEY `product_category_id` (`product_category_id`),
    CONSTRAINT `fk_products_category` FOREIGN KEY (`product_category_id`) REFERENCES `tbl_product_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabelas de Calendário Escolar
-- --------------------------------------------------------

CREATE TABLE `tbl_school_events` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `academic_year_id` INT(11) NOT NULL,
    `event_title` VARCHAR(255) NOT NULL,
    `event_type` ENUM('Feriado','Reunião','Prova','Entrega de Notas','Matrícula','Inscrição','Outro') NOT NULL,
    `event_description` TEXT,
    `start_date` DATE NOT NULL,
    `end_date` DATE,
    `start_time` TIME,
    `end_time` TIME,
    `location` VARCHAR(255),
    `all_day` TINYINT(1) DEFAULT '0',
    `color` VARCHAR(20),
    `created_by` INT(11),
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `academic_year_id` (`academic_year_id`),
    KEY `event_type` (`event_type`),
    KEY `start_date` (`start_date`),
    KEY `created_by` (`created_by`),
    CONSTRAINT `fk_school_events_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_school_events_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabelas de Relatórios e Histórico
-- --------------------------------------------------------

CREATE TABLE `tbl_report_cards` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `enrollment_id` INT(11) NOT NULL,
    `semester_id` INT(11) NOT NULL,
    `report_number` VARCHAR(50) NOT NULL,
    `generated_date` DATE NOT NULL,
    `average_score` DECIMAL(5,2),
    `total_absences` INT(4) DEFAULT '0',
    `justified_absences` INT(4) DEFAULT '0',
    `status` ENUM('Emitido','Pendente','Final') DEFAULT 'Emitido',
    `observations` TEXT,
    `generated_by` INT(11),
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `report_number` (`report_number`),
    UNIQUE KEY `enrollment_semester` (`enrollment_id`, `semester_id`),
    KEY `semester_id` (`semester_id`),
    KEY `generated_by` (`generated_by`),
    CONSTRAINT `fk_report_cards_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `tbl_enrollments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_report_cards_semester` FOREIGN KEY (`semester_id`) REFERENCES `tbl_semesters` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_report_cards_generated_by` FOREIGN KEY (`generated_by`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_academic_history` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `student_id` INT(11) NOT NULL,
    `academic_year_id` INT(11) NOT NULL,
    `class_id` INT(11) NOT NULL,
    `final_status` ENUM('Aprovado','Reprovado','Transferido','Desistente') NOT NULL,
    `final_average` DECIMAL(5,2),
    `total_days` INT(4),
    `total_absences` INT(4),
    `observations` TEXT,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `student_id` (`student_id`),
    KEY `academic_year_id` (`academic_year_id`),
    KEY `class_id` (`class_id`),
    CONSTRAINT `fk_academic_history_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_academic_history_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_academic_history_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------
-- Tabela de Professores
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_teachers` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `birth_date` DATE,
    `gender` ENUM('Masculino','Feminino') NULL,
    `nationality` VARCHAR(100) DEFAULT 'Angolana',
    `identity_document` VARCHAR(50),
    `identity_type` ENUM('BI','Passaporte','Cédula','Outro') DEFAULT 'BI',
    `nif` VARCHAR(20),
    `address` TEXT,
    `city` VARCHAR(100),
    `municipality` VARCHAR(100),
    `province` VARCHAR(100),
    `emergency_contact` VARCHAR(20),
    `emergency_contact_name` VARCHAR(255),
    `qualifications` TEXT COMMENT 'Habilitações literárias',
    `specialization` VARCHAR(255) COMMENT 'Especialização/Área de formação',
    `admission_date` DATE COMMENT 'Data de admissão na escola',
    `bank_name` VARCHAR(100),
    `bank_account` VARCHAR(50),
    `bank_iban` VARCHAR(50),
    `bank_swift` VARCHAR(20),
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_id` (`user_id`),
    UNIQUE KEY `identity_document` (`identity_document`),
    UNIQUE KEY `nif` (`nif`),
    KEY `is_active` (`is_active`),
    KEY `admission_date` (`admission_date`),
    CONSTRAINT `fk_teachers_user` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------
-- Comentários
-- --------------------------------------------------------

-- Esta tabela armazena informações adicionais específicas de professores
-- que não estão incluídas na tabela genérica tbl_users.
-- A relação é 1:1 com tbl_users através do campo user_id.

-- --------------------------------------------------------
-- Tabela de Documentos (para alunos e professores)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_documents` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL COMMENT 'ID do usuário (aluno ou professor)',
    `user_type` ENUM('student','teacher') NOT NULL COMMENT 'Tipo de usuário',
    `document_type` VARCHAR(50) NOT NULL COMMENT 'BI, Passaporte, Certificado, Foto, etc',
    `document_name` VARCHAR(255) NOT NULL COMMENT 'Nome original do arquivo',
    `document_path` VARCHAR(255) NOT NULL COMMENT 'Caminho do arquivo no servidor',
    `document_size` INT(11) COMMENT 'Tamanho em bytes',
    `document_mime` VARCHAR(100) COMMENT 'Tipo MIME do arquivo',
    `description` TEXT COMMENT 'Descrição adicional',
    `is_verified` TINYINT(1) DEFAULT '0' COMMENT '0=Pendente, 1=Verificado, 2=Rejeitado',
    `verified_by` INT(11) COMMENT 'ID do admin que verificou',
    `verified_at` DATETIME NULL COMMENT 'Data da verificação',
    `verification_notes` TEXT COMMENT 'Observações da verificação',
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `user_type` (`user_type`),
    KEY `document_type` (`document_type`),
    KEY `is_verified` (`is_verified`),
    KEY `created_at` (`created_at`),
    CONSTRAINT `fk_documents_user` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de Tipos de Documentos (configurável)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_document_types` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `type_code` VARCHAR(50) NOT NULL COMMENT 'Código único para o tipo',
    `type_name` VARCHAR(100) NOT NULL COMMENT 'Nome do tipo de documento',
    `type_category` ENUM('identificacao','academico','pessoal','outro') NOT NULL DEFAULT 'outro',
    `allowed_extensions` VARCHAR(255) COMMENT 'jpg,png,pdf',
    `max_size` INT(11) DEFAULT '5120' COMMENT 'Tamanho máximo em KB (5MB padrão)',
    `is_required` TINYINT(1) DEFAULT '0' COMMENT 'Obrigatório para alunos/professores',
    `for_student` TINYINT(1) DEFAULT '1' COMMENT 'Disponível para alunos',
    `for_teacher` TINYINT(1) DEFAULT '1' COMMENT 'Disponível para professores',
    `description` TEXT,
    `sort_order` INT(3) DEFAULT '0',
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `type_code` (`type_code`),
    KEY `type_category` (`type_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Inserir tipos de documentos padrão
-- --------------------------------------------------------

INSERT INTO `tbl_document_types` (`type_code`, `type_name`, `type_category`, `allowed_extensions`, `max_size`, `is_required`, `for_student`, `for_teacher`, `sort_order`) VALUES
('BI', 'Bilhete de Identidade', 'identificacao', 'jpg,jpeg,png,pdf', 5120, 1, 1, 1, 1),
('PASSAPORTE', 'Passaporte', 'identificacao', 'jpg,jpeg,png,pdf', 5120, 0, 1, 1, 2),
('CERTIDAO_NASCIMENTO', 'Certidão de Nascimento', 'identificacao', 'jpg,jpeg,png,pdf', 5120, 1, 1, 0, 3),
('FOTO', 'Fotografia', 'pessoal', 'jpg,jpeg,png', 2048, 1, 1, 1, 4),
('CERTIFICADO_CLASSES', 'Certificado de Classes Anteriores', 'academico', 'jpg,jpeg,png,pdf', 5120, 1, 1, 0, 5),
('DIPLOMA', 'Diploma/Certificado', 'academico', 'jpg,jpeg,png,pdf', 5120, 0, 0, 1, 6),
('COMPROVANTE_RESIDENCIA', 'Comprovante de Residência', 'outro', 'jpg,jpeg,png,pdf', 5120, 0, 1, 1, 7),
('CURRICULO', 'Currículo Vitae', 'pessoal', 'pdf', 5120, 0, 0, 1, 8),
('CERTIFICADO_HABILITACOES', 'Certificado de Habilitações', 'academico', 'jpg,jpeg,png,pdf', 5120, 1, 0, 1, 9),
('DECLARACAO_TRABALHO', 'Declaração de Trabalho', 'outro', 'jpg,jpeg,png,pdf', 5120, 0, 0, 1, 10),
('OUTRO', 'Outro Documento', 'outro', 'jpg,jpeg,png,pdf', 5120, 0, 1, 1, 99);

-- --------------------------------------------------------
-- Comentários
-- --------------------------------------------------------

-- A tabela tbl_documents permite que alunos e professores
-- façam upload de múltiplos documentos para validação pela secretaria.
-- O campo is_verified permite controlar o status de verificação:
-- 0 = Pendente, 1 = Verificado, 2 = Rejeitado


-- --------------------------------------------------------
-- Tabela de Solicitações de Documentos
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_document_requests` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `user_type` ENUM('student','teacher','guardian','staff') NOT NULL,
    `request_number` VARCHAR(50) NOT NULL,
    `document_type` VARCHAR(100) NOT NULL COMMENT 'Tipo de documento solicitado (certificado, declaração, etc)',
    `purpose` TEXT COMMENT 'Finalidade da solicitação',
    `quantity` INT(2) DEFAULT '1',
    `format` ENUM('digital','impresso','ambos') DEFAULT 'digital',
    `delivery_method` ENUM('retirar','email','correio') DEFAULT 'retirar',
    `delivery_address` TEXT,
    `fee_amount` DECIMAL(10,2) DEFAULT '0.00',
    `payment_status` ENUM('pending','paid','waived') DEFAULT 'pending',
    `payment_reference` VARCHAR(100),
    `payment_date` DATETIME,
    `status` ENUM('pending','processing','ready','delivered','cancelled','rejected') DEFAULT 'pending',
    `processed_by` INT(11),
    `processed_at` DATETIME,
    `ready_at` DATETIME,
    `delivered_at` DATETIME,
    `rejection_reason` TEXT,
    `notes` TEXT,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `request_number` (`request_number`),
    KEY `user_id` (`user_id`),
    KEY `user_type` (`user_type`),
    KEY `status` (`status`),
    KEY `payment_status` (`payment_status`),
    KEY `created_at` (`created_at`),
    CONSTRAINT `fk_document_requests_user` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de Documentos Gerados (para solicitações)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_generated_documents` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `request_id` INT(11) NOT NULL,
    `document_path` VARCHAR(255) NOT NULL,
    `document_name` VARCHAR(255) NOT NULL,
    `document_size` INT(11),
    `document_mime` VARCHAR(100),
    `generated_by` INT(11),
    `generated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `download_count` INT(11) DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `request_id` (`request_id`),
    KEY `generated_by` (`generated_by`),
    CONSTRAINT `fk_generated_documents_request` FOREIGN KEY (`request_id`) REFERENCES `tbl_document_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de Tipos de Documentos Solicitáveis
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_requestable_documents` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `document_code` VARCHAR(50) NOT NULL,
    `document_name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `category` ENUM('certificado','declaracao','atestado','historico','outro') DEFAULT 'outro',
    `fee_amount` DECIMAL(10,2) DEFAULT '0.00',
    `processing_days` INT(2) DEFAULT '3',
    `available_for` ENUM('all','students','teachers','guardians','staff') DEFAULT 'all',
    `requires_approval` TINYINT(1) DEFAULT '0',
    `template_path` VARCHAR(255),
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `document_code` (`document_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Inserir tipos de documentos solicitáveis
-- --------------------------------------------------------

INSERT INTO `tbl_requestable_documents` (`document_code`, `document_name`, `category`, `fee_amount`, `processing_days`, `available_for`) VALUES
('CERT_MATRICULA', 'Certificado de Matrícula', 'certificado', 500.00, 2, 'students'),
('DECL_FREQUENCIA', 'Declaração de Frequência', 'declaracao', 0.00, 1, 'students'),
('HISTORICO_NOTAS', 'Histórico de Notas', 'historico', 1000.00, 3, 'students'),
('CERT_CONCLUSAO', 'Certificado de Conclusão', 'certificado', 1500.00, 5, 'students'),
('DECL_APROVEITAMENTO', 'Declaração de Aproveitamento', 'declaracao', 0.00, 2, 'students'),
('ATESTADO_MATRICULA', 'Atestado de Matrícula', 'atestado', 0.00, 1, 'students'),
('DECL_SERVICO', 'Declaração de Serviço', 'declaracao', 0.00, 2, 'teachers'),
('CERT_TRABALHO', 'Certificado de Trabalho', 'certificado', 500.00, 3, 'teachers'),
('DECL_VENCIMENTO', 'Declaração de Vencimento', 'declaracao', 0.00, 2, 'teachers');


-- Adicionar coluna document_code à tabela tbl_document_requests
ALTER TABLE `tbl_document_requests` 
ADD COLUMN `document_code` VARCHAR(50) NOT NULL AFTER `document_type`,
ADD INDEX `idx_document_code` (`document_code`);


-- --------------------------------------------------------
-- Tabelas para Gestão Financeira
-- --------------------------------------------------------

-- 1. Tabela de Métodos de Pagamento
CREATE TABLE IF NOT EXISTS `tbl_payment_modes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `mode_name` VARCHAR(100) NOT NULL,
    `mode_code` VARCHAR(50) NOT NULL,
    `description` TEXT,
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `mode_code` (`mode_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir métodos de pagamento padrão
INSERT INTO `tbl_payment_modes` (`mode_name`, `mode_code`, `description`) VALUES
('Dinheiro', 'CASH', 'Pagamento em espécie'),
('Transferência Bancária', 'TRANSFER', 'Transferência bancária'),
('Depósito', 'DEPOSIT', 'Depósito em conta'),
('Multicaixa', 'ATM', 'Pagamento com cartão Multicaixa'),
('Cheque', 'CHECK', 'Pagamento com cheque');

-- 2. Tabela de Taxas/Impostos
CREATE TABLE IF NOT EXISTS `tbl_taxes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `tax_name` VARCHAR(100) NOT NULL,
    `tax_code` VARCHAR(50) NOT NULL,
    `tax_rate` DECIMAL(5,2) NOT NULL,
    `description` TEXT,
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `tax_code` (`tax_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir taxas padrão (IVA Angola)
INSERT INTO `tbl_taxes` (`tax_name`, `tax_code`, `tax_rate`, `description`) VALUES
('IVA - Taxa Normal', 'IVA14', 14.00, 'Imposto sobre Valor Acrescentado - Taxa Normal (14%)'),
('IVA - Taxa Reduzida', 'IVA5', 5.00, 'Imposto sobre Valor Acrescentado - Taxa Reduzida (5%)'),
('IVA - Isento', 'IVA0', 0.00, 'Isento de IVA');

-- 3. Verificar se a tabela tbl_payments já tem o campo receipt_number
-- Se não tiver, adicionar:
ALTER TABLE `tbl_payments` 
ADD COLUMN `receipt_number` VARCHAR(50) NULL AFTER `payment_reference`,
ADD UNIQUE KEY `receipt_number` (`receipt_number`);

-- 4. Verificar se a tabela tbl_invoice_items já tem todos os campos
-- Se necessário, ajustar:
ALTER TABLE `tbl_invoice_items`
MODIFY COLUMN `tax_rate` DECIMAL(5,2) DEFAULT '0.00',
MODIFY COLUMN `discount_rate` DECIMAL(5,2) DEFAULT '0.00';

-- 5. Criar índices para melhor performance
CREATE INDEX idx_invoice_date ON tbl_invoices(invoice_date);
CREATE INDEX idx_invoice_status ON tbl_invoices(status);
CREATE INDEX idx_payment_date ON tbl_payments(payment_date);
CREATE INDEX idx_payment_method ON tbl_payments(payment_method);
CREATE INDEX idx_expense_date ON tbl_expenses(expense_date);
CREATE INDEX idx_expense_category ON tbl_expenses(expense_category_id);
CREATE INDEX idx_expense_status ON tbl_expenses(payment_status);




-- --------------------------------------------------------
-- Adicionar campo course_id à tabela tbl_classes
-- --------------------------------------------------------
ALTER TABLE `tbl_classes` 
ADD COLUMN `course_id` INT(11) NULL AFTER `grade_level_id`,
ADD KEY `course_id` (`course_id`),
ADD CONSTRAINT `fk_classes_course` FOREIGN KEY (`course_id`) REFERENCES `tbl_courses` (`id`) ON DELETE SET NULL;


-- --------------------------------------------------------
-- Adicionar colunas created_at e updated_at nas tabelas que não possuem
-- --------------------------------------------------------

-- 3. Tabela: tbl_grade_levels
ALTER TABLE `tbl_grade_levels` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 4. Tabela: tbl_classes
ALTER TABLE `tbl_classes` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;




-- 7. Tabela: tbl_guardians
ALTER TABLE `tbl_guardians` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 8. Tabela: tbl_student_guardians
ALTER TABLE `tbl_student_guardians` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;


-- 15. Tabela: tbl_fee_types
ALTER TABLE `tbl_fee_types` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 16. Tabela: tbl_fee_structure
ALTER TABLE `tbl_fee_structure` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 17. Tabela: tbl_expense_categories
ALTER TABLE `tbl_expense_categories` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 18. Tabela: tbl_expenses
ALTER TABLE `tbl_expenses` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 19. Tabela: tbl_product_categories
ALTER TABLE `tbl_product_categories` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 20. Tabela: tbl_products
ALTER TABLE `tbl_products` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 21. Tabela: tbl_school_events
ALTER TABLE `tbl_school_events` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 22. Tabela: tbl_academic_history
ALTER TABLE `tbl_academic_history` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;


ALTER TABLE `tbl_documents` 
ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL AFTER `updated_at`;


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
    `completed_at` DATETIME NULL DEFAULT NULL,
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
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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


-- --------------------------------------------------------
-- Estrutura da tabela `tbl_notifications`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_notifications` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL COMMENT 'ID do usuário destinatário',
    `user_type` ENUM('admin','teacher','student','guardian','staff') NOT NULL COMMENT 'Tipo de usuário',
    `title` VARCHAR(255) NOT NULL COMMENT 'Título da notificação',
    `message` TEXT NOT NULL COMMENT 'Mensagem da notificação',
    `icon` VARCHAR(50) DEFAULT 'fa-info-circle' COMMENT 'Ícone FontAwesome',
    `color` VARCHAR(20) DEFAULT 'primary' COMMENT 'Cor do ícone (primary, success, warning, danger, info)',
    `link` VARCHAR(500) DEFAULT NULL COMMENT 'Link para acessar a notificação',
    `link_text` VARCHAR(100) DEFAULT 'Ver detalhes' COMMENT 'Texto do link',
    `entity_type` VARCHAR(50) DEFAULT NULL COMMENT 'Tipo de entidade relacionada (enrollment, exam, fee, etc)',
    `entity_id` INT(11) DEFAULT NULL COMMENT 'ID da entidade relacionada',
    `is_read` TINYINT(1) DEFAULT '0' COMMENT '0=não lida, 1=lida',
    `read_at` DATETIME NULL COMMENT 'Data/hora da leitura',
    `is_global` TINYINT(1) DEFAULT '0' COMMENT '1=para todos os usuários de um tipo',
    `expires_at` DATETIME NULL COMMENT 'Data de expiração da notificação',
    `created_by` INT(11) DEFAULT NULL COMMENT 'ID do usuário que gerou a notificação',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `user_type` (`user_type`),
    KEY `is_read` (`is_read`),
    KEY `created_at` (`created_at`),
    KEY `entity_type_entity_id` (`entity_type`, `entity_id`),
    KEY `expires_at` (`expires_at`),
    CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- --------------------------------------------------------
-- Inserção de Dados Iniciais
-- --------------------------------------------------------

-- Inserir moeda padrão (Kwanza)
INSERT INTO `tbl_currencies` (`currency_name`, `currency_code`, `currency_symbol`, `is_default`) VALUES
('Kwanza Angolano', 'AOA', 'Kz', 1),
('Dólar Americano', 'USD', '$', 0),
('Euro', 'EUR', '€', 0);

-- Inserir com IDs específicos
INSERT INTO `tbl_roles` (`id`, `role_name`, `role_description`, `role_type`) VALUES
(1, 'Administrador', 'Acesso total ao sistema', 'admin'),
(2, 'Diretor', 'Gestão administrativa e pedagógica', 'staff'),
(3, 'Secretário', 'Gestão de matrículas e documentação', 'staff'),
(4, 'Professor', 'Gestão de turmas, notas e presenças', 'teacher'),
(5, 'Aluno', 'Acesso ao portal do aluno', 'student'),
(6, 'Encarregado', 'Acompanhamento do aluno', 'staff'),
(7, 'Tesoureiro', 'Gestão financeira', 'staff'),
(8, 'Bibliotecário', 'Gestão da biblioteca', 'staff');

-- Inserir níveis de ensino (sistema angolano)
INSERT INTO `tbl_grade_levels` (`level_name`, `level_code`, `education_level`, `grade_number`, `sort_order`) VALUES
('Iniciação - 1º Ano', 'INI-01', 'Iniciação', 1, 1),
('Iniciação - 2º Ano', 'INI-02', 'Iniciação', 2, 2),
('Iniciação - 3º Ano', 'INI-03', 'Iniciação', 3, 3),
('1ª Classe', 'PRI-01', 'Primário', 1, 4),
('2ª Classe', 'PRI-02', 'Primário', 2, 5),
('3ª Classe', 'PRI-03', 'Primário', 3, 6),
('4ª Classe', 'PRI-04', 'Primário', 4, 7),
('5ª Classe', 'PRI-05', 'Primário', 5, 8),
('6ª Classe', 'PRI-06', 'Primário', 6, 9),
('7ª Classe', '1CIC-07', '1º Ciclo', 7, 10),
('8ª Classe', '1CIC-08', '1º Ciclo', 8, 11),
('9ª Classe', '1CIC-09', '1º Ciclo', 9, 12),
('10ª Classe', '2CIC-10', '2º Ciclo', 10, 13),
('11ª Classe', '2CIC-11', '2º Ciclo', 11, 14),
('12ª Classe', '2CIC-12', '2º Ciclo', 12, 15),
('13ª Classe', 'MED-13', 'Ensino Médio', 13, 16);

-- Inserir cursos padrão do sistema angolano
-- INSERT INTO `tbl_courses` (`course_name`, `course_code`, `course_type`, `start_grade_id`, `end_grade_id`, `duration_years`) VALUES
-- ('Ciências Físicas e Biológicas', 'CFB', 'Ciências', 13, 15, 3),
-- ('Ciências Económicas e Jurídicas', 'CEJ', 'Económico-Jurídico', 13, 15, 3),
-- ('Ciências Humanas', 'CH', 'Humanidades', 13, 15, 3),
-- ('Ensino Técnico-Profissional', 'ETP', 'Técnico', 13, 15, 3),
-- ('Formação de Professores', 'FP', 'Profissional', 13, 16, 4); -- Alguns cursos vão até 13ª
-- Inserir cursos do ensino médio angolano (II ciclo)
INSERT INTO `tbl_courses` (`course_name`, `course_code`, `course_type`, `start_grade_id`, `end_grade_id`, `duration_years`) VALUES
-- Ciências Físicas e Biológicas
('Ciências Físicas e Biológicas', 'CFB', 'Ciências', 13, 15, 3),
('Ciências Físicas e Biológicas - Variante Física', 'CFB-FIS', 'Ciências', 13, 15, 3),
('Ciências Físicas e Biológicas - Variante Química', 'CFB-QUI', 'Ciências', 13, 15, 3),
('Ciências Físicas e Biológicas - Variante Biologia', 'CFB-BIO', 'Ciências', 13, 15, 3),

-- Ciências Económicas e Jurídicas
('Ciências Económicas e Jurídicas', 'CEJ', 'Económico-Jurídico', 13, 15, 3),
('Ciências Económicas - Variante Economia', 'CEJ-ECO', 'Económico-Jurídico', 13, 15, 3),
('Ciências Jurídicas - Variante Direito', 'CEJ-DIR', 'Económico-Jurídico', 13, 15, 3),

-- Ciências Humanas
('Ciências Humanas', 'CH', 'Humanidades', 13, 15, 3),
('Ciências Humanas - Variante História', 'CH-HIS', 'Humanidades', 13, 15, 3),
('Ciências Humanas - Variante Geografia', 'CH-GEO', 'Humanidades', 13, 15, 3),
('Ciências Humanas - Variante Filosofia', 'CH-FIL', 'Humanidades', 13, 15, 3),

-- Ensino Técnico-Profissional
('Ensino Técnico-Profissional - Eletricidade', 'ETP-ELE', 'Técnico', 13, 15, 3),
('Ensino Técnico-Profissional - Eletrónica', 'ETP-ELT', 'Técnico', 13, 15, 3),
('Ensino Técnico-Profissional - Informática', 'ETP-INF', 'Técnico', 13, 15, 3),
('Ensino Técnico-Profissional - Mecânica', 'ETP-MEC', 'Técnico', 13, 15, 3),
('Ensino Técnico-Profissional - Construção Civil', 'ETP-CC', 'Técnico', 13, 15, 3),
('Ensino Técnico-Profissional - Eletrónica e Telecomunicações', 'ETP-ET', 'Técnico', 13, 15, 3),
('Ensino Técnico-Profissional - Análises Clínicas', 'ETP-AC', 'Técnico', 13, 15, 3),
('Ensino Técnico-Profissional - Enfermagem', 'ETP-ENF', 'Técnico', 13, 15, 3),
('Ensino Técnico-Profissional - Farmácia', 'ETP-FAR', 'Técnico', 13, 15, 3),
('Ensino Técnico-Profissional - Contabilidade', 'ETP-CONT', 'Técnico', 13, 15, 3),
('Ensino Técnico-Profissional - Administração', 'ETP-ADM', 'Técnico', 13, 15, 3),
('Ensino Técnico-Profissional - Secretariado', 'ETP-SEC', 'Técnico', 13, 15, 3),
('Ensino Técnico-Profissional - Turismo', 'ETP-TUR', 'Técnico', 13, 15, 3),
('Ensino Técnico-Profissional - Hotelaria', 'ETP-HOT', 'Técnico', 13, 15, 3),

-- Formação de Professores (Magistério)
('Formação de Professores - Ensino Primário', 'FP-EP', 'Profissional', 13, 16, 4),
('Formação de Professores - Educação de Infância', 'FP-EI', 'Profissional', 13, 16, 4),
('Formação de Professores - Português', 'FP-PT', 'Profissional', 13, 16, 4),
('Formação de Professores - Matemática', 'FP-MAT', 'Profissional', 13, 16, 4),
('Formação de Professores - História', 'FP-HIS', 'Profissional', 13, 16, 4),
('Formação de Professores - Geografia', 'FP-GEO', 'Profissional', 13, 16, 4),
('Formação de Professores - Biologia', 'FP-BIO', 'Profissional', 13, 16, 4),
('Formação de Professores - Física', 'FP-FIS', 'Profissional', 13, 16, 4),
('Formação de Professores - Química', 'FP-QUI', 'Profissional', 13, 16, 4),
('Formação de Professores - Língua Portuguesa', 'FP-LP', 'Profissional', 13, 16, 4),
('Formação de Professores - Educação Física', 'FP-EF', 'Profissional', 13, 16, 4),

-- Cursos Técnicos Profissionais (Curta Duração)
('Técnico de Informática', 'TI', 'Técnico', 13, 13, 1),
('Técnico de Contabilidade', 'TC', 'Técnico', 13, 13, 1),
('Técnico de Eletricidade', 'TE', 'Técnico', 13, 13, 1),
('Técnico de Refrigeração', 'TR', 'Técnico', 13, 13, 1),
('Técnico de Mecânica', 'TM', 'Técnico', 13, 13, 1);

-- Inserir tipos de taxas/propinas
INSERT INTO `tbl_fee_types` (`type_name`, `type_code`, `type_category`, `is_recurring`, `recurrence_period`) VALUES
('Propina Mensal', 'FEE-MONTHLY', 'Propina', 1, 'Mensal'),
('Taxa de Matrícula', 'FEE-ENROLL', 'Matrícula', 0, NULL),
('Taxa de Inscrição', 'FEE-REG', 'Inscrição', 0, NULL),
('Taxa de Exame', 'FEE-EXAM', 'Taxa de Exame', 0, NULL),
('Multa por Atraso', 'FEE-FINE', 'Multa', 0, NULL),
('Taxa de Requerimento', 'FEE-REQ', 'Taxa Administrativa', 0, NULL),
('Taxa de Certificado', 'FEE-CERT', 'Taxa Administrativa', 0, NULL),
('Material Escolar', 'FEE-MAT', 'Outro', 0, NULL);

INSERT INTO `tbl_exam_boards` 
(`board_name`, `board_code`, `board_type`, `weight`, `is_active`) VALUES
-- AVALIAÇÕES CONTÍNUAS (MAC)
('Avaliação Contínua', 'AC', 'Avaliação Contínua', 1.00, 1),
-- PROVA DO PROFESSOR (NPP)
('Prova do Professor', 'NPP', 'Prova Professor', 1.00, 1),
-- PROVAS TRIMESTRAIS (NPT)
('Prova Trimestral', 'NPT', 'Prova Trimestral', 2.00, 1),
-- EXAMES FINAIS (E)
('Exame Final', 'EX-FIN', 'Exame Final', 3.00, 1),
-- EXAMES DE RECURSO
('Exame de Recurso', 'REC', 'Recurso', 1.50, 1),
-- EXAMES ESPECIAIS
('Exame Especial', 'ESP', 'Especial', 1.00, 1),
-- EXAMES COMBINADOS (MEC) - 3 registros
('Exame Combinado - Escrito', 'EC-ESC', 'Combinado', 1.00, 1),
('Exame Combinado - Oral', 'EC-ORAL', 'Combinado', 1.00, 1),
('Exame Combinado - Prático', 'EC-PRAT', 'Combinado', 1.00, 1),
-- CURSOS TÉCNICOS
('Prova Aptidão Profissional', 'PAP', 'Especial', 2.00, 1),
('Nota Estágio Curricular', 'NEC', 'Especial', 2.00, 1);


-- Inserir categorias de despesas
INSERT INTO `tbl_expense_categories` (`category_name`, `category_code`) VALUES
('Salários', 'EXP-SAL'),
('Água', 'EXP-WATER'),
('Electricidade', 'EXP-ELEC'),
('Telecomunicações', 'EXP-TEL'),
('Material de Limpeza', 'EXP-CLEAN'),
('Material de Escritório', 'EXP-OFFICE'),
('Manutenção', 'EXP-MAINT'),
('Alimentação', 'EXP-FOOD'),
('Transporte', 'EXP-TRANS'),
('Impostos', 'EXP-TAX'),
('Outros', 'EXP-OTHER');

-- Criar usuário admin padrão (senha: admin123)
INSERT INTO `tbl_users` (`username`, `email`, `password`, `first_name`, `last_name`, `role_id`, `user_type`, `is_active`) VALUES
('admin', 'admin@escola.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', 1, 'admin', 1);

-- Inserir algumas disciplinas básicas
-- INSERT INTO `tbl_disciplines` (`discipline_name`, `discipline_code`, `workload_hours`, `min_grade`, `max_grade`, `approval_grade`) VALUES
-- ('Língua Portuguesa', 'LP', 120, 0, 20, 10),
-- ('Matemática', 'MAT', 120, 0, 20, 10),
-- ('Ciências da Natureza', 'CN', 90, 0, 20, 10),
-- ('História', 'HIS', 90, 0, 20, 10),
-- ('Geografia', 'GEO', 90, 0, 20, 10),
-- ('Educação Física', 'EDF', 60, 0, 20, 10),
-- ('Educação Moral e Cívica', 'EMC', 60, 0, 20, 10),
-- ('Inglês', 'ING', 90, 0, 20, 10),
-- ('Francês', 'FRA', 90, 0, 20, 10),
-- ('Física', 'FIS', 90, 0, 20, 10),
-- ('Química', 'QUI', 90, 0, 20, 10),
-- ('Biologia', 'BIO', 90, 0, 20, 10);

-- Inserir todas as disciplinas do sistema de ensino angolano com descrição
-- Ensino Primário (1ª a 6ª Classe), I Ciclo (7ª a 9ª Classe) e II Ciclo (10ª a 13ª Classe)

INSERT INTO `tbl_disciplines` (`discipline_name`, `discipline_code`, `workload_hours`, `min_grade`, `max_grade`, `approval_grade`, `description`) VALUES
-- ============ LÍNGUAS ============
('Língua Portuguesa', 'LP', 180, 0, 20, 10, 'Desenvolvimento da comunicação oral e escrita, leitura, escrita, gramática, literatura e produção textual'),
('Línguas Nacionais (Umbundo/Kimbundo/Kikongo)', 'LN', 120, 0, 20, 10, 'Estudo das línguas nacionais angolanas, valorização cultural e comunicação em línguas locais'),
('Inglês', 'ING', 120, 0, 20, 10, 'Desenvolvimento das quatro competências: compreensão oral e escrita, expressão oral e escrita em inglês'),
('Francês', 'FRA', 120, 0, 20, 10, 'Aprendizagem da língua francesa, comunicação e elementos culturais da francofonia'),
('Espanhol', 'ESP', 90, 0, 20, 10, 'Língua espanhola, cultura hispânica e comunicação em contextos diversos'),

-- ============ MATEMÁTICA E CIÊNCIAS ============
('Matemática', 'MAT', 180, 0, 20, 10, 'Operações fundamentais, álgebra, geometria, funções, equações e raciocínio lógico-matemático'),
('Física', 'FIS', 120, 0, 20, 10, 'Mecânica, termodinâmica, eletromagnetismo, óptica, física moderna e fenômenos naturais'),
('Química', 'QUI', 120, 0, 20, 10, 'Química inorgânica, orgânica, físico-química, análises químicas e processos químicos'),
('Biologia', 'BIO', 120, 0, 20, 10, 'Biologia celular, genética, evolução, ecologia, zoologia, botânica e seres vivos'),
('Ciências da Natureza', 'CN', 90, 0, 20, 10, 'Noções sobre seres vivos, corpo humano, saúde, elementos da natureza e meio ambiente'),
('Ciências da Vida', 'CV', 140, 0, 20, 10, 'Estudo integrado dos seres vivos e processos químicos fundamentais para a vida'),
('Ciências da Terra e da Vida', 'CTV', 90, 0, 20, 10, 'Estudo integrado dos sistemas terrestres e da biodiversidade'),
('Geologia', 'GEO-CFB', 90, 0, 20, 10, 'Geologia geral, mineralogia, petrologia, recursos minerais de Angola e geologia histórica'),
('Astronomia', 'AST', 60, 0, 20, 10, 'Noções de astronomia, sistema solar, cosmos e observação astronômica'),

-- ============ CIÊNCIAS HUMANAS E SOCIAIS ============
('História', 'HIS', 120, 0, 20, 10, 'História de Angola, África e mundo, períodos históricos e análise historiográfica'),
('Geografia', 'GEO', 120, 0, 20, 10, 'Geografia física, humana, econômica de Angola e geopolítica mundial'),
('Ciências Sociais', 'CS', 140, 0, 20, 10, 'Estudo integrado da história de Angola e geografia nacional e mundial'),
('Filosofia', 'FIL', 90, 0, 20, 10, 'Introdução ao pensamento filosófico, ética, epistemologia e filosofia africana'),
('Sociologia', 'SOC', 90, 0, 20, 10, 'Teorias sociológicas, instituições sociais, cultura, estratificação e mudança social'),
('Antropologia Cultural', 'ANT', 90, 0, 20, 10, 'Antropologia, diversidade cultural, etnias angolanas e patrimônio cultural'),
('Psicologia Geral', 'PSI', 90, 0, 20, 10, 'Processos psicológicos, desenvolvimento humano, personalidade e psicologia social'),
('Estudo do Meio', 'EM', 120, 0, 20, 10, 'Conhecimento do ambiente natural e social, identidade local, comunidade e meio ambiente'),

-- ============ EDUCAÇÃO E FORMAÇÃO ============
('Educação Física', 'EDF', 90, 0, 20, 10, 'Desenvolvimento motor, desporto, jogos, condicionamento físico, saúde e hábitos saudáveis'),
('Educação Moral e Cívica', 'EMC', 60, 0, 20, 10, 'Formação de valores éticos, cívicos, patriotismo, direitos humanos e convivência social'),
('Educação Manual e Plástica', 'EMP', 60, 0, 20, 10, 'Expressão artística, trabalhos manuais, desenvolvimento da criatividade e coordenação motora fina'),
('Educação Musical', 'EMU', 60, 0, 20, 10, 'Teoria musical, ritmos angolanos, prática instrumental, canto e cultura musical'),
('Desenho', 'DES', 90, 0, 20, 10, 'Técnicas de desenho, perspectiva, composição e expressão gráfica'),
('Desenho Técnico', 'DT', 90, 0, 20, 10, 'Desenho técnico, projeções, cotagem e representações gráficas'),
('Geometria Descritiva', 'GD', 90, 0, 20, 10, 'Geometria descritiva, sistemas de projeção e representação espacial'),
('Formação Pessoal e Social', 'FPS', 45, 0, 20, 10, 'Desenvolvimento da identidade, autonomia, relações interpessoais, orientação vocacional e cidadania'),

-- ============ TECNOLOGIAS ============
('Tecnologias de Informação e Comunicação', 'TIC', 60, 0, 20, 10, 'Introdução à informática, ferramentas digitais, internet, segurança online e programação básica'),
('Programação', 'PROG', 120, 0, 20, 10, 'Lógica de programação, algoritmos, estruturas de dados e linguagens de programação'),
('Redes de Computadores', 'REDES', 90, 0, 20, 10, 'Arquitetura de redes, protocolos, topologias, segurança e administração de redes'),
('Sistemas Operativos', 'SO', 90, 0, 20, 10, 'Funcionamento de sistemas operativos, gestão de processos, memória e arquivos'),
('Base de Dados', 'BD', 90, 0, 20, 10, 'Modelagem de dados, SQL, administração de bancos de dados e sistemas de gestão'),
('Arquitetura de Computadores', 'ARC', 60, 0, 20, 10, 'Organização de computadores, componentes hardware, processadores e memória'),
('Desenvolvimento Web', 'WEB', 90, 0, 20, 10, 'Criação de sites, HTML, CSS, JavaScript, frameworks e desenvolvimento front-end/back-end'),
('Manutenção de Computadores', 'MAN', 60, 0, 20, 10, 'Diagnóstico, reparação, montagem e manutenção preventiva de computadores'),

-- ============ CIÊNCIAS ECONÓMICAS E JURÍDICAS ============
('Introdução à Economia', 'ECO', 120, 0, 20, 10, 'Conceitos econômicos fundamentais, microeconomia, macroeconomia e economia angolana'),
('Noções de Direito', 'NDI', 90, 0, 20, 10, 'Introdução ao direito, direito constitucional, civil e legislação angolana'),
('Contabilidade Geral', 'CONT', 120, 0, 20, 10, 'Princípios contábeis, escrituração, demonstrações financeiras e normas contábeis'),
('Contabilidade Analítica', 'CA', 90, 0, 20, 10, 'Custos, centros de responsabilidade, análise de resultados e tomada de decisão'),
('Gestão Financeira', 'GF', 90, 0, 20, 10, 'Administração financeira, fluxo de caixa, orçamentos e investimentos'),
('Gestão de Empresas', 'GE', 90, 0, 20, 10, 'Princípios de gestão, administração, organização empresarial e liderança'),
('Gestão de Recursos Humanos', 'GRH', 90, 0, 20, 10, 'Recrutamento, seleção, treinamento, avaliação de desempenho e legislação trabalhista'),
('Marketing', 'MARK', 90, 0, 20, 10, 'Estratégias de marketing, pesquisa de mercado, comportamento do consumidor e branding'),
('Estatística', 'EST', 90, 0, 20, 10, 'Estatística descritiva, probabilidades, amostragem e análise de dados'),
('Matemática Financeira', 'MF', 90, 0, 20, 10, 'Juros, descontos, séries de pagamentos, análise de investimentos e sistemas de amortização'),
('Direito Fiscal', 'DF', 60, 0, 20, 10, 'Legislação tributária, impostos, taxas e obrigações fiscais em Angola'),
('Secretariado', 'SEC', 90, 0, 20, 10, 'Técnicas de secretariado, organização administrativa, comunicação e arquivo'),
('Empreendedorismo', 'EMP', 60, 0, 20, 10, 'Criação de negócios, plano de negócios, inovação e gestão empreendedora'),

-- ============ ÁREA TÉCNICO-PROFISSIONAL - ELETRICIDADE/ELETRÓNICA ============
('Eletricidade Geral', 'ELETR', 120, 0, 20, 10, 'Circuitos elétricos, leis fundamentais, corrente contínua e alternada'),
('Eletrónica Analógica', 'ELAN', 120, 0, 20, 10, 'Componentes eletrônicos, amplificadores, fontes de alimentação e circuitos analógicos'),
('Eletrónica Digital', 'ELDI', 120, 0, 20, 10, 'Sistemas digitais, portas lógicas, circuitos combinacionais e sequenciais'),
('Instalações Elétricas', 'IE', 90, 0, 20, 10, 'Projeto e execução de instalações elétricas prediais e industriais, normas técnicas'),
('Máquinas Elétricas', 'ME', 90, 0, 20, 10, 'Motores, geradores, transformadores e equipamentos eletromecânicos'),
('Telecomunicações', 'TEL', 90, 0, 20, 10, 'Sistemas de comunicação, transmissão de dados, redes de telecomunicações'),
('Instrumentação', 'INST', 60, 0, 20, 10, 'Instrumentos de medição, sensores, controladores e automação'),

-- ============ ÁREA TÉCNICO-PROFISSIONAL - MECÂNICA ============
('Mecânica Geral', 'MEC', 120, 0, 20, 10, 'Princípios da mecânica, estática, dinâmica, cinemática e aplicações'),
('Desenho Técnico Mecânico', 'DTM', 90, 0, 20, 10, 'Desenho de peças mecânicas, cotagem, tolerâncias e representações técnicas'),
('Termodinâmica', 'TERM', 90, 0, 20, 10, 'Leis da termodinâmica, ciclos, transferência de calor e aplicações industriais'),
('Mecânica dos Fluidos', 'MFD', 90, 0, 20, 10, 'Propriedades dos fluidos, hidrostática, hidrodinâmica e aplicações'),
('Resistência dos Materiais', 'RM', 90, 0, 20, 10, 'Esforços, tensões, deformações e dimensionamento de elementos estruturais'),
('Processos de Fabrico', 'PF', 90, 0, 20, 10, 'Processos de usinagem, soldagem, fundição e conformação mecânica'),
('Soldadura', 'SOLD', 60, 0, 20, 10, 'Técnicas de soldadura, tipos de solda, segurança e controle de qualidade'),

-- ============ ÁREA TÉCNICO-PROFISSIONAL - CONSTRUÇÃO CIVIL ============
('Desenho de Construção Civil', 'DCC', 90, 0, 20, 10, 'Desenho arquitetônico, plantas, cortes, fachadas e detalhamento'),
('Topografia', 'TOP', 90, 0, 20, 10, 'Levantamentos topográficos, curvas de nível, orientação e instrumentos'),
('Materiais de Construção', 'MC', 90, 0, 20, 10, 'Propriedades e aplicações dos materiais de construção, ensaios tecnológicos'),
('Estruturas', 'ESTR', 120, 0, 20, 10, 'Cálculo estrutural, concreto armado, estruturas metálicas e fundações'),
('Hidráulica', 'HIDR', 90, 0, 20, 10, 'Instalações hidráulicas prediais, sistemas de abastecimento e drenagem'),
('Orçamento e Planeamento', 'OP', 60, 0, 20, 10, 'Orçamentação de obras, cronogramas, gestão de projetos e custos'),

-- ============ ÁREA TÉCNICO-PROFISSIONAL - SAÚDE ============
('Anatomia Humana', 'ANAT', 120, 0, 20, 10, 'Estudo da estrutura do corpo humano, sistemas orgânicos e suas relações'),
('Fisiologia Humana', 'FISIO', 120, 0, 20, 10, 'Funcionamento dos sistemas orgânicos, homeostase e mecanismos fisiológicos'),
('Bioquímica', 'BIOQ', 90, 0, 20, 10, 'Processos bioquímicos, metabolismo, enzimas e biomoléculas'),
('Microbiologia', 'MICRO', 90, 0, 20, 10, 'Estudo dos microrganismos, patogenicidade, controle e técnicas laboratoriais'),
('Enfermagem Geral', 'ENF', 120, 0, 20, 10, 'Fundamentos de enfermagem, cuidados básicos, procedimentos e ética profissional'),
('Farmácia', 'FARM', 120, 0, 20, 10, 'Farmacologia, medicamentos, manipulação farmacêutica e legislação'),
('Primeiros Socorros', 'PS', 60, 0, 20, 10, 'Técnicas de urgência, emergência, suporte básico de vida e prevenção'),
('Higiene e Segurança no Trabalho', 'HST', 60, 0, 20, 10, 'Normas de segurança, prevenção de acidentes, EPIs e saúde ocupacional'),

-- ============ ÁREA TÉCNICO-PROFISSIONAL - TURISMO E HOTELARIA ============
('Turismo Geral', 'TUR', 90, 0, 20, 10, 'Fundamentos do turismo, tipologias, impacto econômico e cultural'),
('Hotelaria', 'HOT', 90, 0, 20, 10, 'Gestão hoteleira, operações de hospedagem, recepção e governança'),
('Restauração', 'REST', 90, 0, 20, 10, 'Serviços de alimentação, gastronomia, gestão de restaurantes e eventos'),
('Gestão Hoteleira', 'GH', 90, 0, 20, 10, 'Administração de hotéis, reservas, marketing hoteleiro e qualidade'),
('Ecoturismo', 'ECO', 60, 0, 20, 10, 'Turismo sustentável, áreas naturais, conservação e desenvolvimento local'),
('Animação Turística', 'AT', 60, 0, 20, 10, 'Atividades de lazer, recreação, animação cultural e entretenimento'),

-- ============ FORMAÇÃO DE PROFESSORES (MAGISTÉRIO) ============
('Didática Geral', 'DID', 90, 0, 20, 10, 'Princípios e métodos de ensino, planejamento didático e estratégias pedagógicas'),
('Psicologia da Educação', 'PSIED', 90, 0, 20, 10, 'Processos de aprendizagem, desenvolvimento cognitivo e afetivo do aluno'),
('Sociologia da Educação', 'SOCED', 60, 0, 20, 10, 'Educação e sociedade, desigualdades educacionais e papel social da escola'),
('Filosofia da Educação', 'FILED', 60, 0, 20, 10, 'Fundamentos filosóficos da educação, correntes pedagógicas e pensamento educacional'),
('História da Educação', 'HED', 60, 0, 20, 10, 'Evolução histórica da educação em Angola e no mundo, reformas educacionais'),
('Metodologias de Ensino', 'MET', 120, 0, 20, 10, 'Métodos e técnicas de ensino por área do conhecimento, recursos didáticos'),
('Prática Pedagógica', 'PP', 180, 0, 20, 10, 'Estágio supervisionado, regência de classe e reflexão sobre a prática docente'),
('Legislação Educacional', 'LEG', 45, 0, 20, 10, 'Lei de Bases do Sistema de Educação, normas e diretrizes educacionais'),
('Gestão Escolar', 'GE', 60, 0, 20, 10, 'Administração escolar, liderança pedagógica, organização e gestão de escolas'),
('Educação Inclusiva', 'EI', 60, 0, 20, 10, 'Atendimento a alunos com necessidades especiais, inclusão e diversidade'),
('Avaliação Educacional', 'AE', 45, 0, 20, 10, 'Tipos e instrumentos de avaliação, elaboração de provas e análise de resultados'),

-- ============ METODOLOGIA E INVESTIGAÇÃO ============
('Metodologia do Trabalho Científico', 'MTC', 45, 0, 20, 10, 'Técnicas de pesquisa, normas acadêmicas, elaboração de relatórios e trabalhos científicos'),
('Lógica Matemática', 'LM', 60, 0, 20, 10, 'Raciocínio lógico, proposições, argumentação e demonstrações matemáticas'),
('Iniciação à Língua Estrangeira', 'ILE', 45, 0, 20, 10, 'Primeiro contato com línguas estrangeiras, vocabulário básico e expressões simples'),

-- ============ PATRIMÓNIO E CULTURA ============
('História das Artes', 'HA', 60, 0, 20, 10, 'Arte angolana, africana e ocidental, movimentos artísticos e crítica de arte'),
('Património Cultural Angolano', 'PCA', 60, 0, 20, 10, 'Preservação do patrimônio, sítios históricos, tradições e identidade cultural');



-- --------------------------------------------------------
-- Dados iniciais para tabela tbl_settings
-- Configurações padrão do sistema escolar angolano
-- --------------------------------------------------------

INSERT INTO `tbl_settings` (`name`, `value`, `status`) VALUES
-- =====================================================
-- CONFIGURAÇÕES GERAIS DA APLICAÇÃO
-- =====================================================
('app_name', 'Sistema Escolar Angolano', 1),
('app_version', '1.0.0', 1),
('app_timezone', 'Africa/Luanda', 1),
('app_date_format', 'd/m/Y', 1),
('app_time_format', 'H:i', 1),
('app_locale', 'pt', 1),
('app_theme', 'light', 1),
('items_per_page', '15', 1),
('enable_debug', '0', 1),
('maintenance_mode', '0', 1),

-- =====================================================
-- CONFIGURAÇÕES DA ESCOLA
-- =====================================================
('school_name', 'Escola Modelo', 1),
('school_acronym', 'EM', 1),
('school_address', 'Rua Principal, 123', 1),
('school_city', 'Luanda', 1),
('school_province', 'Luanda', 1),
('school_phone', '+244 999 999 999', 1),
('school_alt_phone', '+244 999 999 998', 1),
('school_email', 'info@escola.ao', 1),
('school_website', 'www.escola.ao', 1),
('school_nif', '5000000000', 1),
('school_founding_year', '2000', 1),
('school_logo', '', 1),

-- =====================================================
-- CONFIGURAÇÕES ACADÉMICAS
-- =====================================================
('current_academic_year', '', 1),  -- Será preenchido após criar anos letivos
('current_semester', '', 1),       -- Será preenchido após criar semestres
('grading_system', '0-20', 1),
('min_grade', '0', 1),
('max_grade', '20', 1),
('approval_grade', '10', 1),
('attendance_threshold', '75', 1),
('enrollment_deadline', '', 1),
('academic_calendar', 'semester', 1),  -- semester ou trimester

-- =====================================================
-- CONFIGURAÇÕES DE PROPINAS E PAGAMENTOS
-- =====================================================
('default_currency', '1', 1),  -- 1 = Kwanza (AOA)
('payment_tax_rate', '14', 1),  -- IVA Angola 14%
('payment_invoice_prefix', 'INV', 1),
('payment_receipt_prefix', 'REC', 1),
('payment_due_days', '30', 1),
('payment_late_fee', '5', 1),  -- 5% de multa por atraso
('payment_discount_early', '0', 1),
('payment_enable_multiple', '1', 1),
('payment_enable_partial', '1', 1),
('payment_enable_fines', '1', 1),
('late_fee_percentage', '5', 1),
('discount_early_payment', '0', 1),
('payment_deadline_days', '30', 1),

-- =====================================================
-- CONFIGURAÇÕES DE EMAIL
-- =====================================================
('email_protocol', 'smtp', 1),
('email_from', 'noreply@escola.ao', 1),
('email_from_name', 'Sistema Escolar', 1),
('email_smtp_host', 'smtp.gmail.com', 1),
('email_smtp_port', '587', 1),
('email_smtp_user', '', 1),  -- Deve ser preenchido pelo usuário
('email_smtp_pass', '', 1),  -- Deve ser preenchido pelo usuário
('email_smtp_crypto', 'tls', 1),
('email_sendmail_path', '/usr/sbin/sendmail', 1),

-- =====================================================
-- CONFIGURAÇÕES DE SEGURANÇA
-- =====================================================
('session_timeout', '3600', 1),  -- 1 hora em segundos
('max_login_attempts', '5', 1),
('password_min_length', '6', 1),
('require_strong_password', '0', 1),
('two_factor_auth', '0', 1),

-- =====================================================
-- CONFIGURAÇÕES DE NOTIFICAÇÕES
-- =====================================================
('notify_enrollment', '1', 1),
('notify_payment', '1', 1),
('notify_exam_results', '1', 1),
('notify_attendance', '0', 1),
('notify_deadlines', '1', 1),

-- =====================================================
-- CONFIGURAÇÕES DE BACKUP
-- =====================================================
('auto_backup', '0', 1),
('backup_frequency', 'weekly', 1),  -- daily, weekly, monthly
('backup_retention_days', '30', 1),

-- =====================================================
-- CONFIGURAÇÕES DE MATRÍCULA
-- =====================================================
('enrollment_open', '0', 1),
('enrollment_start_date', '', 1),
('enrollment_end_date', '', 1),
('max_students_per_class', '30', 1),
('allow_late_enrollment', '0', 1),

-- =====================================================
-- CONFIGURAÇÕES DE AVALIAÇÃO
-- =====================================================
('allow_makeup_exams', '1', 1),
('makeup_exam_grade', '10', 1),
('weight_ac', '30', 1),  -- Peso das Avaliações Contínuas (%)
('weight_npp', '30', 1), -- Peso da Prova do Professor (%)
('weight_npt', '40', 1), -- Peso da Prova Trimestral (%)
('rounding_method', 'half_up', 1),  -- half_up, half_down, ceil, floor

-- =====================================================
-- CONFIGURAÇÕES DE RELATÓRIOS
-- =====================================================
('report_header', 'RELATÓRIO ESCOLAR', 1),
('report_footer', 'Sistema de Gestão Escolar', 1),
('report_show_logo', '1', 1),
('report_show_signatures', '1', 1),
('report_watermark', '0', 1);

-- =====================================================
-- CONFIGURAÇÕES ADICIONAIS PARA O SISTEMA ANGOLANO
-- =====================================================
INSERT INTO `tbl_settings` (`name`, `value`, `status`) VALUES
('angola_provinces', 'Bengo,Benguela,Bié,Cabinda,Cunene,Huambo,Huíla,Luanda,Lunda Norte,Lunda Sul,Malanje,Moxico,Namibe,Uíge,Zaire', 1),
('grading_scale_10', '0-9:Reprovado;10-13:Suficiente;14-17:Bom;18-20:Excelente', 1),
('academic_years_format', 'YYYY/YYYY', 1),
('school_levels', 'Iniciação,Primário,1º Ciclo,2º Ciclo,Ensino Médio', 1),
('exam_types', 'AC,NPP,NPT,E,NEE,NEO,NEP,PAP,NEC', 1),
('student_status', 'Ativo,Pendente,Transferido,Concluído,Anulado', 1),
('payment_methods', 'Dinheiro,Transferência,Depósito,Multicaixa,Cheque', 1);

-- --------------------------------------------------------
-- Comentários
-- --------------------------------------------------------
-- Os valores entre '' devem ser substituídos pelos dados reais da escola
-- Alguns campos como current_academic_year e current_semester serão
-- preenchidos automaticamente pelo sistema após a criação dos registros
-- --------------------------------------------------------
-- Comentários Finais
-- --------------------------------------------------------

-- Nota: Este banco de dados foi estruturado para o sistema escolar angolano,
-- incluindo particularidades como:
-- - Níveis de ensino (Iniciação, Primário, 1º Ciclo, 2º Ciclo, Ensino Médio)
-- - Sistema de avaliação de 0-20 valores
-- - Gestão de propinas em Kwanzas (AOA)
-- - Encargados de educação
-- - Histórico académico completo

-- Para restaurar o banco de dados:
-- mysql -u root -p escola_angolana < escola_angolana.sql

COMMIT;