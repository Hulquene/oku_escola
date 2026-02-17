-- --------------------------------------------------------
-- Adicionar colunas created_at e updated_at nas tabelas que não possuem
-- --------------------------------------------------------

-- 1. Tabela: tbl_academic_years
ALTER TABLE `tbl_academic_years` 
ADD COLUMN `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 2. Tabela: tbl_semesters (já tem created_at, adicionar updated_at)
ALTER TABLE `tbl_semesters` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 3. Tabela: tbl_grade_levels
ALTER TABLE `tbl_grade_levels` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 4. Tabela: tbl_classes
ALTER TABLE `tbl_classes` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 5. Tabela: tbl_disciplines
ALTER TABLE `tbl_disciplines` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 6. Tabela: tbl_class_disciplines
ALTER TABLE `tbl_class_disciplines` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 7. Tabela: tbl_guardians
ALTER TABLE `tbl_guardians` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 8. Tabela: tbl_student_guardians
ALTER TABLE `tbl_student_guardians` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 9. Tabela: tbl_enrollment_documents
ALTER TABLE `tbl_enrollment_documents` 
ADD COLUMN `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 10. Tabela: tbl_attendance
ALTER TABLE `tbl_attendance` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 11. Tabela: tbl_exam_boards
ALTER TABLE `tbl_exam_boards` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 12. Tabela: tbl_exams
ALTER TABLE `tbl_exams` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 13. Tabela: tbl_continuous_assessment
ALTER TABLE `tbl_continuous_assessment` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 14. Tabela: tbl_final_grades
ALTER TABLE `tbl_final_grades` 
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


-- Adicionar colunas created_at e updated_at na tabela tbl_semesters
ALTER TABLE `tbl_semesters` 
ADD COLUMN `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

