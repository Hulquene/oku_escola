ALTER TABLE tbl_enrollments 
ADD COLUMN grade_level_id INT NULL AFTER academic_year_id,
ADD COLUMN previous_grade_id INT NULL AFTER grade_level_id,
ADD FOREIGN KEY (grade_level_id) REFERENCES tbl_grade_levels(id),
ADD FOREIGN KEY (previous_grade_id) REFERENCES tbl_grade_levels(id);