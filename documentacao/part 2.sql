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
-- 5. ATUALIZAR PERÍODOS PARA "EM ANDAMENTO" (opcional)
-- ========================================================

-- Marcar o período atual como "Em Andamento"
UPDATE `tbl_exam_periods` SET `status` = 'Em Andamento' WHERE `id` = 1;

-- ========================================================
-- FIM DO SCRIPT
-- ========================================================
COMMIT;