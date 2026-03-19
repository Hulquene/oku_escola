-- ========================================================
-- SCRIPT GERADOR COMPLETO - CORRIGIDO (SEM ERROS DE SINTAXE)
-- ========================================================

USE escola_angolana;

-- ========================================================
-- 1. LIMPAR DADOS EXISTENTES (OPCIONAL - DESCOMENTE SE QUISER RECOMEÇAR)
-- ========================================================
-- DELETE FROM tbl_exam_attendance;
-- DELETE FROM tbl_exam_results;
-- ALTER TABLE tbl_exam_attendance AUTO_INCREMENT = 1;
-- ALTER TABLE tbl_exam_results AUTO_INCREMENT = 1;

-- ========================================================
-- 2. CRIAR TABELAS TEMPORÁRIAS
-- ========================================================

DROP TEMPORARY TABLE IF EXISTS temp_enrollments;
DROP TEMPORARY TABLE IF EXISTS temp_exam_schedules;
DROP TEMPORARY TABLE IF EXISTS temp_combinations;

-- Mapear matrículas por turma
CREATE TEMPORARY TABLE temp_enrollments AS
SELECT 
    e.id AS enrollment_id,
    e.student_id,
    e.class_id,
    c.grade_level_id,
    ROW_NUMBER() OVER (PARTITION BY e.class_id ORDER BY e.student_id) AS aluno_num
FROM tbl_enrollments e
JOIN tbl_classes c ON e.class_id = c.id
WHERE e.academic_year_id = 1;

-- Mapear exam_schedules por período e turma
CREATE TEMPORARY TABLE temp_exam_schedules AS
SELECT 
    es.id AS exam_schedule_id,
    es.exam_period_id,
    es.class_id,
    es.discipline_id,
    es.exam_board_id,
    ep.semester_id,
    ep.period_type,
    ep.start_date,
    ep.end_date
FROM tbl_exam_schedules es
JOIN tbl_exam_periods ep ON es.exam_period_id = ep.id
WHERE ep.academic_year_id = 1;

-- Criar tabela de combinações para evitar duplicatas
CREATE TEMPORARY TABLE temp_combinations AS
SELECT DISTINCT
    tes.exam_schedule_id,
    te.enrollment_id,
    te.class_id,
    te.grade_level_id,
    tes.exam_board_id
FROM temp_exam_schedules tes
CROSS JOIN temp_enrollments te
WHERE te.class_id = tes.class_id
  AND tes.exam_board_id IN (1, 2, 3, 4, 5);

-- ========================================================
-- 3. GERAR PRESENÇAS (tbl_exam_attendance) - CORRIGIDO
-- ========================================================

INSERT IGNORE INTO tbl_exam_attendance (exam_schedule_id, enrollment_id, attended, check_in_time, check_in_method, recorded_by, created_at)
SELECT 
    tc.exam_schedule_id,
    tc.enrollment_id,
    -- 90% de presença, com algumas variações
    CASE 
        WHEN RAND() < 0.9 THEN 1 
        ELSE 0 
    END AS attended,
    -- Horário de check-in (se presente)
    CASE 
        WHEN RAND() < 0.9 THEN 
            DATE_ADD(
                DATE_ADD(
                    STR_TO_DATE(
                        CASE 
                            WHEN tes.exam_period_id BETWEEN 1 AND 3 THEN '2024-04-15'
                            WHEN tes.exam_period_id BETWEEN 4 AND 6 THEN '2024-07-29'
                            WHEN tes.exam_period_id BETWEEN 7 AND 9 THEN '2024-11-25'
                            ELSE '2024-04-15'
                        END,
                        '%Y-%m-%d'
                    ),
                    INTERVAL FLOOR(RAND() * 4) DAY
                ),
                INTERVAL (30 + FLOOR(RAND() * 20)) MINUTE
            )
        ELSE NULL
    END AS check_in_time,
    'Manual' AS check_in_method,
    11 + (tc.class_id % 10) AS recorded_by,
    NOW() AS created_at
FROM temp_combinations tc
JOIN temp_exam_schedules tes ON tc.exam_schedule_id = tes.exam_schedule_id
WHERE tes.exam_board_id IN (1, 2, 3, 4, 5);

-- ========================================================
-- 4. GERAR NOTAS (tbl_exam_results) - SEM DUPLICATAS
-- ========================================================

-- 4.1 NOTAS PARA PROVAS (NPP, NPT, EX-FIN)
INSERT IGNORE INTO tbl_exam_results (enrollment_id, exam_schedule_id, assessment_type, score, is_absent, recorded_by, recorded_at, created_at)
SELECT 
    tc.enrollment_id,
    tc.exam_schedule_id,
    CASE tc.exam_board_id
        WHEN 2 THEN 'NPP'
        WHEN 3 THEN 'NPT'
        WHEN 4 THEN 'EX-FIN'
        ELSE 'NPP'
    END AS assessment_type,
    -- Nota baseada no nível de ensino
    CASE 
        WHEN tc.grade_level_id <= 3 THEN ROUND(12 + RAND() * 7, 1)  -- Iniciação
        WHEN tc.grade_level_id BETWEEN 4 AND 9 THEN ROUND(10 + RAND() * 8, 1)  -- Primário
        ELSE ROUND(9 + RAND() * 9, 1)  -- I e II Ciclo
    END AS score,
    0 AS is_absent,
    11 + (tc.class_id % 10) AS recorded_by,
    DATE_ADD(NOW(), INTERVAL -FLOOR(RAND() * 60) DAY) AS recorded_at,
    NOW() AS created_at
FROM temp_combinations tc
WHERE tc.exam_board_id IN (2, 3, 4)
  AND NOT EXISTS (
      SELECT 1 FROM tbl_exam_results er 
      WHERE er.exam_schedule_id = tc.exam_schedule_id 
        AND er.enrollment_id = tc.enrollment_id
  );

-- 4.2 NOTAS PARA ALUNOS QUE FALTARAM
INSERT IGNORE INTO tbl_exam_results (enrollment_id, exam_schedule_id, assessment_type, score, is_absent, recorded_by, recorded_at, created_at)
SELECT 
    tc.enrollment_id,
    tc.exam_schedule_id,
    CASE tc.exam_board_id
        WHEN 2 THEN 'NPP'
        WHEN 3 THEN 'NPT'
        WHEN 4 THEN 'EX-FIN'
        ELSE 'NPP'
    END AS assessment_type,
    0 AS score,
    1 AS is_absent,
    11 + (tc.class_id % 10) AS recorded_by,
    DATE_ADD(NOW(), INTERVAL -FLOOR(RAND() * 60) DAY) AS recorded_at,
    NOW() AS created_at
FROM temp_combinations tc
WHERE tc.exam_board_id IN (2, 3, 4)
  AND RAND() < 0.05  -- 5% dos alunos faltaram
  AND NOT EXISTS (
      SELECT 1 FROM tbl_exam_results er 
      WHERE er.exam_schedule_id = tc.exam_schedule_id 
        AND er.enrollment_id = tc.enrollment_id
  );

-- ========================================================
-- 5. GERAR AVALIAÇÕES CONTÍNUAS (AC) - 1 POR ALUNO
-- ========================================================

INSERT IGNORE INTO tbl_exam_results (enrollment_id, assessment_type, score, recorded_by, recorded_at, created_at)
SELECT 
    te.enrollment_id,
    'AC' AS assessment_type,
    ROUND(10 + RAND() * 8, 1) AS score,
    11 + (te.class_id % 10) AS recorded_by,
    DATE_ADD(NOW(), INTERVAL -90 DAY) AS recorded_at,
    NOW() AS created_at
FROM temp_enrollments te
WHERE NOT EXISTS (
    SELECT 1 FROM tbl_exam_results er 
    WHERE er.enrollment_id = te.enrollment_id 
      AND er.assessment_type = 'AC'
);

-- ========================================================
-- 6. GERAR NOTAS DE RECURSO (REC) PARA QUEM PRECISA
-- ========================================================

INSERT IGNORE INTO tbl_exam_results (enrollment_id, exam_schedule_id, assessment_type, score, is_absent, recorded_by, recorded_at, created_at)
SELECT 
    er.enrollment_id,
    tes.exam_schedule_id,
    'REC' AS assessment_type,
    LEAST(20, ROUND(er.score + 2 + RAND() * 3, 1)) AS score,  -- Melhora a nota, máximo 20
    0 AS is_absent,
    er.recorded_by,
    DATE_ADD(NOW(), INTERVAL -15 DAY) AS recorded_at,
    NOW() AS created_at
FROM tbl_exam_results er
JOIN temp_exam_schedules tes ON tes.exam_schedule_id = er.exam_schedule_id
WHERE er.assessment_type = 'NPT' 
  AND er.score < 10 
  AND er.score > 0
  AND tes.exam_board_id = 5
  AND NOT EXISTS (
      SELECT 1 FROM tbl_exam_results er2 
      WHERE er2.enrollment_id = er.enrollment_id 
        AND er2.assessment_type = 'REC'
        AND er2.exam_schedule_id = tes.exam_schedule_id
  )
LIMIT 50;

-- ========================================================
-- 7. ESTATÍSTICAS E VERIFICAÇÃO
-- ========================================================

SELECT '======================================' AS '';
SELECT 'RESUMO DE DADOS GERADOS' AS 'RESUMO';
SELECT '======================================' AS '';

SELECT 
    'Presenças' AS Tipo,
    COUNT(*) AS Total,
    CONCAT(ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM temp_combinations), 2), '%') AS Cobertura
FROM tbl_exam_attendance

UNION ALL

SELECT 
    CONCAT('Notas ', assessment_type),
    COUNT(*),
    CASE assessment_type
        WHEN 'AC' THEN CONCAT(ROUND(COUNT(*) * 100.0 / 165, 2), '%')
        ELSE CONCAT(ROUND(COUNT(*) * 100.0 / 165, 2), '%')
    END
FROM tbl_exam_results
GROUP BY assessment_type

UNION ALL

SELECT 
    'TOTAL REGISTROS',
    (SELECT COUNT(*) FROM tbl_exam_attendance) + (SELECT COUNT(*) FROM tbl_exam_results),
    '100%';

SELECT '' AS '';
SELECT '======================================' AS '';
SELECT 'MÉDIAS POR TIPO DE AVALIAÇÃO' AS '';
SELECT '======================================' AS '';

SELECT 
    assessment_type,
    COUNT(*) AS quantidade,
    ROUND(AVG(score), 2) AS media_geral,
    ROUND(AVG(CASE WHEN is_absent = 0 THEN score END), 2) AS media_presentes,
    SUM(is_absent) AS total_faltas,
    ROUND(SUM(is_absent) * 100.0 / COUNT(*), 2) AS perc_faltas
FROM tbl_exam_results
WHERE assessment_type != 'AC'
GROUP BY assessment_type
ORDER BY assessment_type;

SELECT '' AS '';
SELECT '======================================' AS '';
SELECT 'DISTRIBUIÇÃO POR NÍVEL DE ENSINO' AS '';
SELECT '======================================' AS '';

SELECT 
    c.grade_level_id,
    COUNT(DISTINCT er.enrollment_id) AS alunos,
    COUNT(er.id) AS total_notas,
    ROUND(AVG(er.score), 2) AS media_notas
FROM tbl_exam_results er
JOIN tbl_enrollments e ON er.enrollment_id = e.id
JOIN tbl_classes c ON e.class_id = c.id
WHERE er.assessment_type IN ('NPP', 'NPT', 'EX-FIN')
GROUP BY c.grade_level_id
ORDER BY c.grade_level_id;

-- ========================================================
-- 8. LIMPAR TABELAS TEMPORÁRIAS
-- ========================================================

DROP TEMPORARY TABLE IF EXISTS temp_enrollments;
DROP TEMPORARY TABLE IF EXISTS temp_exam_schedules;
DROP TEMPORARY TABLE IF EXISTS temp_combinations;

-- ========================================================
-- FIM DO SCRIPT GERADOR CORRIGIDO
-- ========================================================
COMMIT;