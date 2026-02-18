ALTER TABLE `tbl_user_logs` 
ADD COLUMN `description` TEXT NULL AFTER `action`,
ADD COLUMN `target_id` INT(11) NULL AFTER `description`,
ADD COLUMN `target_type` VARCHAR(50) NULL AFTER `target_id`,
ADD COLUMN `request_method` VARCHAR(10) NULL AFTER `user_agent`,
ADD COLUMN `request_url` VARCHAR(500) NULL AFTER `request_method`,
ADD COLUMN `details` TEXT NULL AFTER `request_url`;


ALTER TABLE tbl_enrollments 
ADD COLUMN grade_level_id INT NOT NULL AFTER academic_year_id,
ADD COLUMN previous_grade_id INT NULL AFTER grade_level_id,
ADD FOREIGN KEY (grade_level_id) REFERENCES tbl_grade_levels(id),
ADD FOREIGN KEY (previous_grade_id) REFERENCES tbl_grade_levels(id);


ALTER TABLE `tbl_documents` 
ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL AFTER `updated_at`;