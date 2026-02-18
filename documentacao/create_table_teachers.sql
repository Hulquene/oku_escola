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
-- Inserir dados iniciais para professores existentes
-- --------------------------------------------------------

-- Inserir registros para usuários que são professores (user_type = 'teacher')
INSERT INTO `tbl_teachers` (`user_id`, `is_active`, `created_at`, `updated_at`)
SELECT 
    `id` as `user_id`,
    1 as `is_active`,
    NOW() as `created_at`,
    NOW() as `updated_at`
FROM `tbl_users` 
WHERE `user_type` = 'teacher' 
AND `id` NOT IN (SELECT `user_id` FROM `tbl_teachers` WHERE `user_id` IS NOT NULL);

-- --------------------------------------------------------
-- Comentários
-- --------------------------------------------------------

-- Esta tabela armazena informações adicionais específicas de professores
-- que não estão incluídas na tabela genérica tbl_users.
-- A relação é 1:1 com tbl_users através do campo user_id.