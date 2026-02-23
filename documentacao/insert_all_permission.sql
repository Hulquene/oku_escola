-- --------------------------------------------------------
-- Inserir todas as Permissões do Sistema
-- Organizado por módulos
-- --------------------------------------------------------

-- 1. DASHBOARD
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Ver Dashboard', 'dashboard.view', 'dashboard', NOW()),
('Ver Estatísticas', 'dashboard.statistics', 'dashboard', NOW());

-- 2. ALUNOS (STUDENTS)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Alunos', 'students.list', 'students', NOW()),
('Ver Aluno', 'students.view', 'students', NOW()),
('Criar Aluno', 'students.create', 'students', NOW()),
('Editar Aluno', 'students.edit', 'students', NOW()),
('Eliminar Aluno', 'students.delete', 'students', NOW()),
('Importar Alunos', 'students.import', 'students', NOW()),
('Exportar Alunos', 'students.export', 'students', NOW()),
('Ativar/Desativar Aluno', 'students.toggle', 'students', NOW());

-- 3. MATRÍCULAS (ENROLLMENTS)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Matrículas', 'enrollments.list', 'enrollments', NOW()),
('Ver Matrícula', 'enrollments.view', 'enrollments', NOW()),
('Criar Matrícula', 'enrollments.create', 'enrollments', NOW()),
('Editar Matrícula', 'enrollments.edit', 'enrollments', NOW()),
('Eliminar Matrícula', 'enrollments.delete', 'enrollments', NOW()),
('Ver Histórico do Aluno', 'enrollments.history', 'enrollments', NOW()),
('Transferir Matrícula', 'enrollments.transfer', 'enrollments', NOW()),
('Concluir Matrícula', 'enrollments.complete', 'enrollments', NOW());

-- 4. PROFESSORES (TEACHERS)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Professores', 'teachers.list', 'teachers', NOW()),
('Ver Professor', 'teachers.view', 'teachers', NOW()),
('Criar Professor', 'teachers.create', 'teachers', NOW()),
('Editar Professor', 'teachers.edit', 'teachers', NOW()),
('Eliminar Professor', 'teachers.delete', 'teachers', NOW()),
('Atribuir Disciplinas', 'teachers.assign_subjects', 'teachers', NOW()),
('Ver Atribuições', 'teachers.assignments', 'teachers', NOW()),
('Importar Professores', 'teachers.import', 'teachers', NOW()),
('Exportar Professores', 'teachers.export', 'teachers', NOW());

-- 5. TURMAS (CLASSES)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Turmas', 'classes.list', 'classes', NOW()),
('Ver Turma', 'classes.view', 'classes', NOW()),
('Criar Turma', 'classes.create', 'classes', NOW()),
('Editar Turma', 'classes.edit', 'classes', NOW()),
('Eliminar Turma', 'classes.delete', 'classes', NOW()),
('Ver Alunos por Turma', 'classes.students', 'classes', NOW()),
('Atribuir Professor Principal', 'classes.assign_teacher', 'classes', NOW());

-- 6. DISCIPLINAS (SUBJECTS)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Disciplinas', 'subjects.list', 'subjects', NOW()),
('Ver Disciplina', 'subjects.view', 'subjects', NOW()),
('Criar Disciplina', 'subjects.create', 'subjects', NOW()),
('Editar Disciplina', 'subjects.edit', 'subjects', NOW()),
('Eliminar Disciplina', 'subjects.delete', 'subjects', NOW()),
('Atribuir a Turmas', 'subjects.assign_to_classes', 'subjects', NOW()),
('Ver Atribuições', 'subjects.assignments', 'subjects', NOW());

-- 7. CURSOS (COURSES)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Cursos', 'courses.list', 'courses', NOW()),
('Ver Curso', 'courses.view', 'courses', NOW()),
('Criar Curso', 'courses.create', 'courses', NOW()),
('Editar Curso', 'courses.edit', 'courses', NOW()),
('Eliminar Curso', 'courses.delete', 'courses', NOW()),
('Gerir Currículo', 'courses.curriculum', 'courses', NOW());

-- 8. EXAMES E AVALIAÇÕES (EXAMS)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Exames', 'exams.list', 'exams', NOW()),
('Ver Exame', 'exams.view', 'exams', NOW()),
('Criar Exame', 'exams.create', 'exams', NOW()),
('Editar Exame', 'exams.edit', 'exams', NOW()),
('Eliminar Exame', 'exams.delete', 'exams', NOW()),
('Lançar Notas', 'exams.enter_grades', 'exams', NOW()),
('Ver Notas', 'exams.view_grades', 'exams', NOW()),
('Editar Notas', 'exams.edit_grades', 'exams', NOW()),
('Publicar Resultados', 'exams.publish_results', 'exams', NOW()),
('Ver Resultados', 'exams.view_results', 'exams', NOW()),
('Registrar Presenças em Exames', 'exams.attendance', 'exams', NOW()),
('Agendar Exame', 'exams.schedule', 'exams', NOW()),
('Ver Calendário', 'exams.calendar', 'exams', NOW());

-- 9. PRESENÇAS DIÁRIAS (ATTENDANCE)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Presenças', 'attendance.list', 'attendance', NOW()),
('Registrar Presenças', 'attendance.mark', 'attendance', NOW()),
('Editar Presenças', 'attendance.edit', 'attendance', NOW()),
('Ver Relatório de Presenças', 'attendance.report', 'attendance', NOW()),
('Ver Estatísticas de Presenças', 'attendance.statistics', 'attendance', NOW()),
('Exportar Relatório', 'attendance.export', 'attendance', NOW());

-- 10. AVALIAÇÕES CONTÍNUAS (GRADES)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Notas', 'grades.list', 'grades', NOW()),
('Lançar Notas AC', 'grades.enter_ac', 'grades', NOW()),
('Lançar NPP', 'grades.enter_npp', 'grades', NOW()),
('Lançar NPT', 'grades.enter_npt', 'grades', NOW()),
('Ver Médias', 'grades.view_averages', 'grades', NOW()),
('Calcular Médias', 'grades.calculate', 'grades', NOW()),
('Ver Relatório de Notas', 'grades.report', 'grades', NOW()),
('Exportar Notas', 'grades.export', 'grades', NOW());

-- 11. FINANCEIRO (FINANCIAL)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Ver Dashboard Financeiro', 'financial.dashboard', 'financial', NOW()),
('Ver Relatórios Financeiros', 'financial.reports', 'financial', NOW());

-- 12. FATURAS (INVOICES)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Faturas', 'invoices.list', 'invoices', NOW()),
('Ver Fatura', 'invoices.view', 'invoices', NOW()),
('Criar Fatura', 'invoices.create', 'invoices', NOW()),
('Editar Fatura', 'invoices.edit', 'invoices', NOW()),
('Eliminar Fatura', 'invoices.delete', 'invoices', NOW()),
('Emitir Fatura', 'invoices.issue', 'invoices', NOW()),
('Cancelar Fatura', 'invoices.cancel', 'invoices', NOW());

-- 13. PAGAMENTOS (PAYMENTS)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Pagamentos', 'payments.list', 'payments', NOW()),
('Ver Pagamento', 'payments.view', 'payments', NOW()),
('Registrar Pagamento', 'payments.create', 'payments', NOW()),
('Editar Pagamento', 'payments.edit', 'payments', NOW()),
('Eliminar Pagamento', 'payments.delete', 'payments', NOW()),
('Gerar Recibo', 'payments.receipt', 'payments', NOW());

-- 14. PROPINAS (FEES)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Propinas', 'fees.list', 'fees', NOW()),
('Ver Propina', 'fees.view', 'fees', NOW()),
('Criar Propina', 'fees.create', 'fees', NOW()),
('Editar Propina', 'fees.edit', 'fees', NOW()),
('Eliminar Propina', 'fees.delete', 'fees', NOW()),
('Gerar Propinas em Massa', 'fees.bulk_generate', 'fees', NOW()),
('Ver Relatório de Propinas', 'fees.report', 'fees', NOW());

-- 15. TIPOS DE PROPINA (FEE_TYPES)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Tipos', 'fee_types.list', 'fee_types', NOW()),
('Criar Tipo', 'fee_types.create', 'fee_types', NOW()),
('Editar Tipo', 'fee_types.edit', 'fee_types', NOW()),
('Eliminar Tipo', 'fee_types.delete', 'fee_types', NOW());

-- 16. DESPESAS (EXPENSES)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Despesas', 'expenses.list', 'expenses', NOW()),
('Ver Despesa', 'expenses.view', 'expenses', NOW()),
('Criar Despesa', 'expenses.create', 'expenses', NOW()),
('Editar Despesa', 'expenses.edit', 'expenses', NOW()),
('Eliminar Despesa', 'expenses.delete', 'expenses', NOW()),
('Ver Relatório de Despesas', 'expenses.report', 'expenses', NOW());

-- 17. CATEGORIAS DE DESPESA (EXPENSE_CATEGORIES)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Categorias', 'expense_categories.list', 'expense_categories', NOW()),
('Criar Categoria', 'expense_categories.create', 'expense_categories', NOW()),
('Editar Categoria', 'expense_categories.edit', 'expense_categories', NOW()),
('Eliminar Categoria', 'expense_categories.delete', 'expense_categories', NOW());

-- 18. DOCUMENTOS (DOCUMENTS)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Documentos', 'documents.list', 'documents', NOW()),
('Ver Documento', 'documents.view', 'documents', NOW()),
('Upload Documento', 'documents.upload', 'documents', NOW()),
('Editar Documento', 'documents.edit', 'documents', NOW()),
('Eliminar Documento', 'documents.delete', 'documents', NOW()),
('Verificar Documento', 'documents.verify', 'documents', NOW());

-- 19. SOLICITAÇÕES DE DOCUMENTOS (DOCUMENT_REQUESTS)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Solicitações', 'document_requests.list', 'document_requests', NOW()),
('Ver Solicitação', 'document_requests.view', 'document_requests', NOW()),
('Criar Solicitação', 'document_requests.create', 'document_requests', NOW()),
('Processar Solicitação', 'document_requests.process', 'document_requests', NOW()),
('Cancelar Solicitação', 'document_requests.cancel', 'document_requests', NOW());

-- 20. RELATÓRIOS (REPORTS)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Ver Relatórios', 'reports.view', 'reports', NOW()),
('Gerar Relatório Académico', 'reports.academic', 'reports', NOW()),
('Gerar Relatório Financeiro', 'reports.financial', 'reports', NOW()),
('Gerar Relatório de Presenças', 'reports.attendance', 'reports', NOW()),
('Gerar Relatório de Notas', 'reports.grades', 'reports', NOW()),
('Gerar Relatório de Propinas', 'reports.fees', 'reports', NOW()),
('Exportar Relatório', 'reports.export', 'reports', NOW());

-- 21. UTILIZADORES (USERS)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Utilizadores', 'users.list', 'users', NOW()),
('Ver Utilizador', 'users.view', 'users', NOW()),
('Criar Utilizador', 'users.create', 'users', NOW()),
('Editar Utilizador', 'users.edit', 'users', NOW()),
('Eliminar Utilizador', 'users.delete', 'users', NOW()),
('Ativar/Desativar Utilizador', 'users.toggle', 'users', NOW());

-- 22. PERFIS E PERMISSÕES (ROLES)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Perfis', 'roles.list', 'roles', NOW()),
('Ver Perfil', 'roles.view', 'roles', NOW()),
('Criar Perfil', 'roles.create', 'roles', NOW()),
('Editar Perfil', 'roles.edit', 'roles', NOW()),
('Eliminar Perfil', 'roles.delete', 'roles', NOW()),
('Gerir Permissões', 'roles.permissions', 'roles', NOW());

-- 23. NOTIFICAÇÕES (NOTIFICATIONS)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Ver Notificações', 'notifications.view', 'notifications', NOW()),
('Marcar como Lida', 'notifications.mark_read', 'notifications', NOW()),
('Marcar Todas como Lidas', 'notifications.mark_all_read', 'notifications', NOW()),
('Criar Notificação', 'notifications.create', 'notifications', NOW()),
('Eliminar Notificação', 'notifications.delete', 'notifications', NOW());

-- 24. CALENDÁRIO ESCOLAR (CALENDAR)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Ver Calendário', 'calendar.view', 'calendar', NOW()),
('Criar Evento', 'calendar.create_event', 'calendar', NOW()),
('Editar Evento', 'calendar.edit_event', 'calendar', NOW()),
('Eliminar Evento', 'calendar.delete_event', 'calendar', NOW());

-- 25. CONFIGURAÇÕES (SETTINGS)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Ver Configurações', 'settings.view', 'settings', NOW()),
('Editar Configurações Gerais', 'settings.general', 'settings', NOW()),
('Editar Configurações Académicas', 'settings.academic', 'settings', NOW()),
('Editar Configurações Financeiras', 'settings.financial', 'settings', NOW()),
('Editar Configurações de Notificações', 'settings.notifications', 'settings', NOW()),
('Gerir Anos Letivos', 'settings.academic_years', 'settings', NOW()),
('Gerir Semestres', 'settings.semesters', 'settings', NOW()),
('Gerir Níveis de Ensino', 'settings.grade_levels', 'settings', NOW());

-- 26. AUDITORIA E LOGS (LOGS)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Ver Logs', 'logs.view', 'logs', NOW()),
('Exportar Logs', 'logs.export', 'logs', NOW());

-- --------------------------------------------------------
-- Atribuir todas as permissões ao Administrador (role_id = 1)
-- --------------------------------------------------------
INSERT INTO `tbl_role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT 1, id, NOW() FROM `tbl_permissions`;

-- --------------------------------------------------------
-- Atribuir permissões específicas ao Diretor (role_id = 2)
-- --------------------------------------------------------
INSERT INTO `tbl_role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT 2, id, NOW() FROM `tbl_permissions` 
WHERE permission_key IN (
    'dashboard.view', 'dashboard.statistics',
    'students.list', 'students.view', 'students.create', 'students.edit',
    'enrollments.list', 'enrollments.view', 'enrollments.create', 'enrollments.edit', 'enrollments.history',
    'teachers.list', 'teachers.view', 'teachers.create', 'teachers.edit', 'teachers.assign_subjects',
    'classes.list', 'classes.view', 'classes.create', 'classes.edit', 'classes.students',
    'subjects.list', 'subjects.view', 'subjects.assign_to_classes',
    'exams.list', 'exams.view', 'exams.enter_grades', 'exams.view_results',
    'attendance.report', 'attendance.statistics',
    'grades.view_averages', 'grades.report',
    'reports.view', 'reports.academic'
);

-- --------------------------------------------------------
-- Atribuir permissões ao Secretário (role_id = 3)
-- --------------------------------------------------------
INSERT INTO `tbl_role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT 3, id, NOW() FROM `tbl_permissions` 
WHERE permission_key IN (
    'dashboard.view',
    'students.list', 'students.view', 'students.create', 'students.edit',
    'enrollments.list', 'enrollments.view', 'enrollments.create', 'enrollments.edit', 'enrollments.history',
    'classes.list', 'classes.view', 'classes.students',
    'documents.list', 'documents.view', 'documents.upload', 'documents.verify',
    'document_requests.list', 'document_requests.view', 'document_requests.create', 'document_requests.process'
);

-- --------------------------------------------------------
-- Atribuir permissões ao Professor (role_id = 4)
-- --------------------------------------------------------
INSERT INTO `tbl_role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT 4, id, NOW() FROM `tbl_permissions` 
WHERE permission_key IN (
    'dashboard.view',
    'classes.list', 'classes.view', 'classes.students',
    'exams.list', 'exams.view', 'exams.enter_grades', 'exams.view_results', 'exams.attendance',
    'attendance.mark', 'attendance.report',
    'grades.enter_ac', 'grades.enter_npp', 'grades.enter_npt', 'grades.view_averages'
);

-- --------------------------------------------------------
-- Atribuir permissões ao Tesoureiro (role_id = 7)
-- --------------------------------------------------------
INSERT INTO `tbl_role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT 7, id, NOW() FROM `tbl_permissions` 
WHERE permission_key IN (
    'dashboard.view',
    'financial.dashboard', 'financial.reports',
    'invoices.list', 'invoices.view', 'invoices.create', 'invoices.issue',
    'payments.list', 'payments.view', 'payments.create', 'payments.receipt',
    'fees.list', 'fees.view', 'fees.create', 'fees.bulk_generate', 'fees.report',
    'expenses.list', 'expenses.view', 'expenses.create', 'expenses.report'
);

-- --------------------------------------------------------
-- Atribuir permissões ao Aluno (role_id = 5)
-- --------------------------------------------------------
INSERT INTO `tbl_role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT 5, id, NOW() FROM `tbl_permissions` 
WHERE permission_key IN (
    'dashboard.view',
    'exams.view_results',
    'attendance.list',
    'grades.view_averages',
    'documents.view',
    'document_requests.create'
);

-- --------------------------------------------------------
-- Atribuir permissões ao Encarregado (role_id = 6)
-- --------------------------------------------------------
INSERT INTO `tbl_role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT 6, id, NOW() FROM `tbl_permissions` 
WHERE permission_key IN (
    'dashboard.view',
    'exams.view_results',
    'attendance.statistics',
    'grades.view_averages',
    'payments.list', 'payments.view',
    'fees.list', 'fees.view'
);


-- --------------------------------------------------------
-- PERMISSÕES FALTANTES - Adicionar após a seção 8 (EXAMES)
-- --------------------------------------------------------

-- 8A. PERÍODOS DE EXAME (EXAM PERIODS)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Períodos de Exame', 'exam_periods.list', 'exams', NOW()),
('Ver Período de Exame', 'exam_periods.view', 'exams', NOW()),
('Criar Período de Exame', 'exam_periods.create', 'exams', NOW()),
('Editar Período de Exame', 'exam_periods.edit', 'exams', NOW()),
('Eliminar Período de Exame', 'exam_periods.delete', 'exams', NOW()),
('Publicar Período de Exame', 'exam_periods.publish', 'exams', NOW());

-- 8B. CALENDÁRIO DE EXAMES (EXAM SCHEDULES)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Listar Calendário de Exames', 'exam_schedules.list', 'exams', NOW()),
('Ver Calendário de Exames', 'exam_schedules.view', 'exams', NOW()),
('Criar Calendário de Exames', 'exam_schedules.create', 'exams', NOW()),
('Editar Calendário de Exames', 'exam_schedules.edit', 'exams', NOW()),
('Eliminar Calendário de Exames', 'exam_schedules.delete', 'exams', NOW());

-- 8C. VIGILÂNCIAS DE EXAMES (EXAM SUPERVISORS)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Gerir Vigilâncias', 'exam_supervisors.manage', 'exams', NOW()),
('Atribuir Vigilantes', 'exam_supervisors.assign', 'exams', NOW());

-- --------------------------------------------------------
-- PERMISSÕES FALTANTES - Adicionar após a seção 6 (DISCIPLINAS)
-- --------------------------------------------------------

-- 6A. MÉDIAS POR DISCIPLINA (DISCIPLINE AVERAGES)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Ver Médias por Disciplina', 'discipline_averages.view', 'subjects', NOW()),
('Calcular Médias', 'discipline_averages.calculate', 'subjects', NOW());

-- --------------------------------------------------------
-- PERMISSÕES FALTANTES - Adicionar após a seção 20 (RELATÓRIOS)
-- --------------------------------------------------------

-- 20A. PROCESSAMENTO DE RESULTADOS (RESULTS PROCESSING)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Processar Resultados', 'results.process', 'reports', NOW()),
('Ver Resultados Processados', 'results.view', 'reports', NOW()),
('Concluir Semestre', 'semester.conclude', 'reports', NOW());

-- 20B. BOLETINS (REPORT CARDS)
INSERT INTO `tbl_permissions` (`permission_name`, `permission_key`, `module`, `created_at`) VALUES
('Gerar Boletins', 'report_cards.generate', 'reports', NOW()),
('Ver Boletins', 'report_cards.view', 'reports', NOW()),
('Exportar Boletins', 'report_cards.export', 'reports', NOW());