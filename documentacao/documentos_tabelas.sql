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