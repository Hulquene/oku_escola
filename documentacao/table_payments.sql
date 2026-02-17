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