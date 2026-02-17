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
    `setting_name` VARCHAR(255) NOT NULL,
    `setting_value` TEXT,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `setting_name` (`setting_name`)
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
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
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
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
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
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `academic_year_id` (`academic_year_id`),
    CONSTRAINT `fk_semesters_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
    PRIMARY KEY (`id`),
    UNIQUE KEY `discipline_code` (`discipline_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_class_disciplines` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `class_id` INT(11) NOT NULL,
    `discipline_id` INT(11) NOT NULL,
    `teacher_id` INT(11),
    `workload_hours` INT(4),
    `semester_id` INT(11),
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `class_discipline` (`class_id`, `discipline_id`, `semester_id`),
    KEY `discipline_id` (`discipline_id`),
    KEY `teacher_id` (`teacher_id`),
    KEY `semester_id` (`semester_id`),
    CONSTRAINT `fk_class_disciplines_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_class_disciplines_discipline` FOREIGN KEY (`discipline_id`) REFERENCES `tbl_disciplines` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_class_disciplines_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_class_disciplines_semester` FOREIGN KEY (`semester_id`) REFERENCES `tbl_semesters` (`id`) ON DELETE SET NULL
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
    `email` VARCHAR(255),
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
-- Tabelas de Matrículas
-- --------------------------------------------------------

CREATE TABLE `tbl_enrollments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `student_id` INT(11) NOT NULL,
    `class_id` INT(11) NOT NULL,
    `academic_year_id` INT(11) NOT NULL,
    `enrollment_date` DATE NOT NULL,
    `enrollment_number` VARCHAR(50) NOT NULL,
    `enrollment_type` ENUM('Nova','Renovação','Transferência') NOT NULL,
    `previous_class_id` INT(11),
    `status` ENUM('Pendente','Ativo','Concluído','Transferido','Anulado') NOT NULL DEFAULT 'Pendente',
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
    KEY `created_by` (`created_by`),
    CONSTRAINT `fk_enrollments_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_enrollments_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_enrollments_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_enrollments_previous_class` FOREIGN KEY (`previous_class_id`) REFERENCES `tbl_classes` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_enrollments_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_enrollment_documents` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `enrollment_id` INT(11) NOT NULL,
    `document_type` VARCHAR(100) NOT NULL,
    `document_path` VARCHAR(255) NOT NULL,
    `uploaded_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
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
    `attendance_date` DATE NOT NULL,
    `status` ENUM('Presente','Ausente','Atrasado','Dispensado','Falta Justificada') NOT NULL,
    `justification` TEXT,
    `marked_by` INT(11),
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
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
    `board_type` ENUM('Normal','Recurso','Especial','Final','Admissão') NOT NULL,
    `weight` DECIMAL(5,2) DEFAULT '1.00',
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `board_code` (`board_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_exams` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `exam_name` VARCHAR(255) NOT NULL,
    `class_id` INT(11) NOT NULL,
    `discipline_id` INT(11) NOT NULL,
    `semester_id` INT(11) NOT NULL,
    `exam_board_id` INT(11) NOT NULL,
    `exam_date` DATE NOT NULL,
    `exam_time` TIME,
    `exam_room` VARCHAR(50),
    `max_score` DECIMAL(5,2) DEFAULT '20.00',
    `min_score` DECIMAL(5,2) DEFAULT '0.00',
    `approval_score` DECIMAL(5,2) DEFAULT '10.00',
    `description` TEXT,
    `is_published` TINYINT(1) DEFAULT '0',
    `created_by` INT(11),
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `class_id` (`class_id`),
    KEY `discipline_id` (`discipline_id`),
    KEY `semester_id` (`semester_id`),
    KEY `exam_board_id` (`exam_board_id`),
    KEY `created_by` (`created_by`),
    CONSTRAINT `fk_exams_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_exams_discipline` FOREIGN KEY (`discipline_id`) REFERENCES `tbl_disciplines` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_exams_semester` FOREIGN KEY (`semester_id`) REFERENCES `tbl_semesters` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_exams_board` FOREIGN KEY (`exam_board_id`) REFERENCES `tbl_exam_boards` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_exams_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_exam_results` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `exam_id` INT(11) NOT NULL,
    `enrollment_id` INT(11) NOT NULL,
    `score` DECIMAL(5,2) NOT NULL,
    `score_percentage` DECIMAL(5,2),
    `grade` VARCHAR(5),
    `observations` TEXT,
    `recorded_by` INT(11),
    `recorded_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `exam_student` (`exam_id`, `enrollment_id`),
    KEY `enrollment_id` (`enrollment_id`),
    KEY `recorded_by` (`recorded_by`),
    CONSTRAINT `fk_exam_results_exam` FOREIGN KEY (`exam_id`) REFERENCES `tbl_exams` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_exam_results_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `tbl_enrollments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_exam_results_recorded_by` FOREIGN KEY (`recorded_by`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_continuous_assessment` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `enrollment_id` INT(11) NOT NULL,
    `discipline_id` INT(11) NOT NULL,
    `semester_id` INT(11) NOT NULL,
    `assessment_type` VARCHAR(100) NOT NULL,
    `score` DECIMAL(5,2) NOT NULL,
    `assessment_date` DATE,
    `recorded_by` INT(11),
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `enrollment_id` (`enrollment_id`),
    KEY `discipline_id` (`discipline_id`),
    KEY `semester_id` (`semester_id`),
    KEY `recorded_by` (`recorded_by`),
    CONSTRAINT `fk_continuous_assessment_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `tbl_enrollments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_continuous_assessment_discipline` FOREIGN KEY (`discipline_id`) REFERENCES `tbl_disciplines` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_continuous_assessment_semester` FOREIGN KEY (`semester_id`) REFERENCES `tbl_semesters` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_continuous_assessment_recorded_by` FOREIGN KEY (`recorded_by`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbl_final_grades` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `enrollment_id` INT(11) NOT NULL,
    `discipline_id` INT(11) NOT NULL,
    `semester_id` INT(11) NOT NULL,
    `average_score` DECIMAL(5,2) NOT NULL,
    `final_score` DECIMAL(5,2),
    `exam_score` DECIMAL(5,2),
    `status` ENUM('Aprovado','Reprovado','Recurso','Dispensado') NOT NULL,
    `observations` TEXT,
    `calculated_by` INT(11),
    `calculated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `enrollment_discipline_semester` (`enrollment_id`, `discipline_id`, `semester_id`),
    KEY `discipline_id` (`discipline_id`),
    KEY `semester_id` (`semester_id`),
    KEY `calculated_by` (`calculated_by`),
    CONSTRAINT `fk_final_grades_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `tbl_enrollments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_final_grades_discipline` FOREIGN KEY (`discipline_id`) REFERENCES `tbl_disciplines` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_final_grades_semester` FOREIGN KEY (`semester_id`) REFERENCES `tbl_semesters` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_final_grades_calculated_by` FOREIGN KEY (`calculated_by`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL
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
-- Inserção de Dados Iniciais
-- --------------------------------------------------------

-- Inserir moeda padrão (Kwanza)
INSERT INTO `tbl_currencies` (`currency_name`, `currency_code`, `currency_symbol`, `is_default`) VALUES
('Kwanza Angolano', 'AOA', 'Kz', 1),
('Dólar Americano', 'USD', '$', 0),
('Euro', 'EUR', '€', 0);

-- Inserir roles/perfis
INSERT INTO `tbl_roles` (`role_name`, `role_description`) VALUES
('Administrador', 'Acesso total ao sistema'),
('Diretor', 'Gestão administrativa e pedagógica'),
('Secretário', 'Gestão de matrículas e documentação'),
('Professor', 'Gestão de turmas, notas e presenças'),
('Aluno', 'Acesso ao portal do aluno'),
('Encarregado', 'Acompanhamento do aluno'),
('Tesoureiro', 'Gestão financeira'),
('Bibliotecário', 'Gestão da biblioteca');

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

-- Inserir tipos de exames/avaliações
INSERT INTO `tbl_exam_boards` (`board_name`, `board_code`, `board_type`, `weight`) VALUES
('Avaliação Contínua 1', 'AC1', 'Normal', 1.0),
('Avaliação Contínua 2', 'AC2', 'Normal', 1.0),
('Avaliação Contínua 3', 'AC3', 'Normal', 1.0),
('Exame Trimestral', 'EX-TRI', 'Normal', 2.0),
('Exame Semestral', 'EX-SEM', 'Normal', 2.0),
('Exame Final', 'EX-FIN', 'Final', 3.0),
('Exame de Recurso', 'EX-REC', 'Recurso', 1.5),
('Exame Especial', 'EX-ESP', 'Especial', 1.0);

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
INSERT INTO `tbl_disciplines` (`discipline_name`, `discipline_code`, `workload_hours`, `min_grade`, `max_grade`, `approval_grade`) VALUES
('Língua Portuguesa', 'LP', 120, 0, 20, 10),
('Matemática', 'MAT', 120, 0, 20, 10),
('Ciências da Natureza', 'CN', 90, 0, 20, 10),
('História', 'HIS', 90, 0, 20, 10),
('Geografia', 'GEO', 90, 0, 20, 10),
('Educação Física', 'EDF', 60, 0, 20, 10),
('Educação Moral e Cívica', 'EMC', 60, 0, 20, 10),
('Inglês', 'ING', 90, 0, 20, 10),
('Francês', 'FRA', 90, 0, 20, 10),
('Física', 'FIS', 90, 0, 20, 10),
('Química', 'QUI', 90, 0, 20, 10),
('Biologia', 'BIO', 90, 0, 20, 10);

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