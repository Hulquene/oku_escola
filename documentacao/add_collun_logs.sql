ALTER TABLE `tbl_user_logs` 
ADD COLUMN `description` TEXT NULL AFTER `action`,
ADD COLUMN `target_id` INT(11) NULL AFTER `description`,
ADD COLUMN `target_type` VARCHAR(50) NULL AFTER `target_id`,
ADD COLUMN `request_method` VARCHAR(10) NULL AFTER `user_agent`,
ADD COLUMN `request_url` VARCHAR(500) NULL AFTER `request_method`,
ADD COLUMN `details` TEXT NULL AFTER `request_url`;