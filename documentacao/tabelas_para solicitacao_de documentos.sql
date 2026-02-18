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