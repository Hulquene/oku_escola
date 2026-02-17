-- --------------------------------------------------------
-- Inserir Permissões do Sistema
-- --------------------------------------------------------

-- Permissões para Dashboard
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`) VALUES
('Ver Dashboard', 'view_dashboard', 'dashboard'),
('Ver Estatísticas', 'view_statistics', 'dashboard');

-- Permissões para Alunos
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`) VALUES
('Ver Alunos', 'view_students', 'students'),
('Criar Alunos', 'create_students', 'students'),
('Editar Alunos', 'edit_students', 'students'),
('Eliminar Alunos', 'delete_students', 'students'),
('Importar Alunos', 'import_students', 'students'),
('Exportar Alunos', 'export_students', 'students');

-- Permissões para Matrículas
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`) VALUES
('Ver Matrículas', 'view_enrollments', 'enrollments'),
('Criar Matrículas', 'create_enrollments', 'enrollments'),
('Editar Matrículas', 'edit_enrollments', 'enrollments'),
('Eliminar Matrículas', 'delete_enrollments', 'enrollments'),
('Ver Histórico', 'view_history', 'enrollments');

-- Permissões para Professores
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`) VALUES
('Ver Professores', 'view_teachers', 'teachers'),
('Criar Professores', 'create_teachers', 'teachers'),
('Editar Professores', 'edit_teachers', 'teachers'),
('Eliminar Professores', 'delete_teachers', 'teachers'),
('Atribuir Turmas', 'assign_classes', 'teachers');

-- Permissões para Turmas
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`) VALUES
('Ver Turmas', 'view_classes', 'classes'),
('Criar Turmas', 'create_classes', 'classes'),
('Editar Turmas', 'edit_classes', 'classes'),
('Eliminar Turmas', 'delete_classes', 'classes'),
('Ver Alunos por Turma', 'view_class_students', 'classes');

-- Permissões para Disciplinas
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`) VALUES
('Ver Disciplinas', 'view_subjects', 'subjects'),
('Criar Disciplinas', 'create_subjects', 'subjects'),
('Editar Disciplinas', 'edit_subjects', 'subjects'),
('Eliminar Disciplinas', 'delete_subjects', 'subjects'),
('Atribuir Disciplinas', 'assign_subjects', 'subjects');

-- Permissões para Exames
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`) VALUES
('Ver Exames', 'view_exams', 'exams'),
('Criar Exames', 'create_exams', 'exams'),
('Editar Exames', 'edit_exams', 'exams'),
('Eliminar Exames', 'delete_exams', 'exams'),
('Lançar Notas', 'enter_grades', 'exams'),
('Publicar Resultados', 'publish_results', 'exams'),
('Ver Resultados', 'view_results', 'exams');

-- Permissões para Presenças
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`) VALUES
('Ver Presenças', 'view_attendance', 'attendance'),
('Registrar Presenças', 'mark_attendance', 'attendance'),
('Editar Presenças', 'edit_attendance', 'attendance'),
('Ver Relatórios', 'view_attendance_reports', 'attendance');

-- Permissões para Financeiro
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`) VALUES
('Ver Financeiro', 'view_financial', 'financial'),
('Criar Faturas', 'create_invoices', 'financial'),
('Ver Faturas', 'view_invoices', 'financial'),
('Editar Faturas', 'edit_invoices', 'financial'),
('Eliminar Faturas', 'delete_invoices', 'financial'),
('Registrar Pagamentos', 'record_payments', 'financial'),
('Ver Pagamentos', 'view_payments', 'financial'),
('Gerar Recibos', 'generate_receipts', 'financial'),
('Ver Despesas', 'view_expenses', 'financial'),
('Criar Despesas', 'create_expenses', 'financial'),
('Editar Despesas', 'edit_expenses', 'financial'),
('Eliminar Despesas', 'delete_expenses', 'financial');

-- Permissões para Propinas
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`) VALUES
('Ver Propinas', 'view_fees', 'fees'),
('Criar Tipos', 'create_fee_types', 'fees'),
('Editar Tipos', 'edit_fee_types', 'fees'),
('Eliminar Tipos', 'delete_fee_types', 'fees'),
('Gerar Propinas', 'generate_fees', 'fees'),
('Ver Pagamentos', 'view_fee_payments', 'fees');

-- Permissões para Relatórios
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`) VALUES
('Ver Relatórios', 'view_reports', 'reports'),
('Gerar Relatórios Académicos', 'generate_academic_reports', 'reports'),
('Gerar Relatórios Financeiros', 'generate_financial_reports', 'reports'),
('Exportar Relatórios', 'export_reports', 'reports');

-- Permissões para Utilizadores
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`) VALUES
('Ver Utilizadores', 'view_users', 'users'),
('Criar Utilizadores', 'create_users', 'users'),
('Editar Utilizadores', 'edit_users', 'users'),
('Eliminar Utilizadores', 'delete_users', 'users'),
('Ver Perfis', 'view_roles', 'users'),
('Gerir Permissões', 'manage_permissions', 'users');

-- Permissões para Configurações
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`) VALUES
('Ver Configurações', 'view_settings', 'settings'),
('Editar Configurações Gerais', 'edit_general_settings', 'settings'),
('Editar Configurações Académicas', 'edit_academic_settings', 'settings'),
('Editar Configurações Financeiras', 'edit_financial_settings', 'settings');