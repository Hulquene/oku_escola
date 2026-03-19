-- ========================================================
-- SCRIPT GERADOR COMPLETO - PRESENÇAS E NOTAS PARA TODOS OS EXAMES
-- Este script gera automaticamente todos os dados necessários
-- ========================================================

USE escola_angolana;

-- ========================================================
-- 1. PRIMEIRO, VAMOS OBTER OS IDs REAIS
-- ========================================================

-- Criar tabelas temporárias para mapear os IDs
DROP TEMPORARY TABLE IF EXISTS temp_enrollments;
DROP TEMPORARY TABLE IF EXISTS temp_exam_schedules;

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
    ep.period_type
FROM tbl_exam_schedules es
JOIN tbl_exam_periods ep ON es.exam_period_id = ep.id
WHERE ep.academic_year_id = 1;

-- ========================================================
-- 2. GERAR PRESENÇAS (tbl_exam_attendance)
-- ========================================================

-- Limpar dados existentes (opcional - cuidado!)
-- DELETE FROM tbl_exam_attendance;
-- DELETE FROM tbl_exam_results;

INSERT INTO tbl_exam_attendance (exam_schedule_id, enrollment_id, attended, check_in_time, check_in_method, recorded_by, created_at)
SELECT 
    tes.exam_schedule_id,
    te.enrollment_id,
    -- 90% de presença, com algumas variações
    CASE 
        WHEN RAND() < 0.9 THEN 1 
        ELSE 0 
    END AS attended,
    -- Horário de check-in (se presente)
    CASE 
        WHEN RAND() < 0.9 THEN 
            DATE_ADD(
                STR_TO_DATE(
                    CASE 
                        WHEN tes.exam_period_id BETWEEN 1 AND 3 THEN CONCAT('2024-04-', LPAD(15 + FLOOR(RAND() * 5), 2, '0'))
                        WHEN tes.exam_period_id BETWEEN 4 AND 6 THEN CONCAT('2024-07-', LPAD(29 + FLOOR(RAND() * 5), 2, '0'))
                        WHEN tes.exam_period_id BETWEEN 7 AND 9 THEN CONCAT('2024-11-', LPAD(25 + FLOOR(RAND() * 5), 2, '0'))
                        ELSE CONCAT('2024-', LPAD(1 + FLOOR(RAND() * 12), 2, '0'), '-', LPAD(1 + FLOOR(RAND() * 28), 2, '0'))
                    END,
                    '%Y-%m-%d'
                ),
                INTERVAL (30 + FLOOR(RAND() * 20)) MINUTE
            )
        ELSE NULL
    END AS check_in_time,
    'Manual' AS check_in_method,
    -- Professor baseado na turma (simplificado)
    11 + (te.class_id % 10) AS recorded_by,
    NOW() AS created_at
FROM temp_exam_schedules tes
CROSS JOIN temp_enrollments te
WHERE te.class_id = tes.class_id
  AND tes.exam_board_id IN (1, 2, 3, 4, 5)  -- Todos os tipos de exame
LIMIT 3000;  -- Ajuste conforme necessário

-- ========================================================
-- 3. GERAR NOTAS (tbl_exam_results)
-- ========================================================

-- 3.1 NOTAS PARA PROVAS (NPP, NPT, EX-FIN, REC)
INSERT INTO tbl_exam_results (enrollment_id, exam_schedule_id, assessment_type, score, is_absent, recorded_by, recorded_at, created_at)
SELECT 
    te.enrollment_id,
    tes.exam_schedule_id,
    CASE tes.exam_board_id
        WHEN 2 THEN 'NPP'
        WHEN 3 THEN 'NPT'
        WHEN 4 THEN 'EX-FIN'
        WHEN 5 THEN 'REC'
        ELSE 'NPP'
    END AS assessment_type,
    -- Nota baseada no nível de ensino e tipo de prova
    CASE 
        WHEN tes.exam_board_id = 5 THEN  -- REC (recuperação) - notas mais baixas
            ROUND(8 + RAND() * 5, 1)
        WHEN te.grade_level_id <= 3 THEN  -- Iniciação - notas mais altas
            ROUND(12 + RAND() * 7, 1)
        WHEN te.grade_level_id BETWEEN 4 AND 9 THEN  -- Primário
            ROUND(10 + RAND() * 8, 1)
        ELSE  -- I e II Ciclo
            ROUND(9 + RAND() * 9, 1)
    END AS score,
    0 AS is_absent,
    -- Professor que registrou
    11 + (te.class_id % 10) AS recorded_by,
    DATE_ADD(NOW(), INTERVAL -FLOOR(RAND() * 30) DAY) AS recorded_at,
    NOW() AS created_at
FROM temp_exam_schedules tes
JOIN temp_enrollments te ON te.class_id = tes.class_id
WHERE tes.exam_board_id IN (2, 3, 4, 5)  -- NPP, NPT, EX-FIN, REC
  AND RAND() < 0.95  -- 95% dos alunos têm nota (os outros faltaram)
LIMIT 2500;

-- 3.2 NOTAS PARA ALUNOS QUE FALTARAM
INSERT INTO tbl_exam_results (enrollment_id, exam_schedule_id, assessment_type, score, is_absent, recorded_by, recorded_at, created_at)
SELECT 
    te.enrollment_id,
    tes.exam_schedule_id,
    CASE tes.exam_board_id
        WHEN 2 THEN 'NPP'
        WHEN 3 THEN 'NPT'
        WHEN 4 THEN 'EX-FIN'
        WHEN 5 THEN 'REC'
        ELSE 'NPP'
    END AS assessment_type,
    0 AS score,
    1 AS is_absent,
    11 + (te.class_id % 10) AS recorded_by,
    DATE_ADD(NOW(), INTERVAL -FLOOR(RAND() * 30) DAY) AS recorded_at,
    NOW() AS created_at
FROM temp_exam_schedules tes
JOIN temp_enrollments te ON te.class_id = tes.class_id
WHERE tes.exam_board_id IN (2, 3, 4, 5)
  AND RAND() < 0.05  -- 5% dos alunos faltaram
LIMIT 150;

-- ========================================================
-- 4. GERAR AVALIAÇÕES CONTÍNUAS (AC) - 3 POR ALUNO
-- ========================================================

-- 4.1 AC - 1º Trimestre
INSERT INTO tbl_exam_results (enrollment_id, assessment_type, score, recorded_by, recorded_at, created_at)
SELECT 
    te.enrollment_id,
    'AC' AS assessment_type,
    ROUND(10 + RAND() * 8, 1) AS score,
    11 + (te.class_id % 10) AS recorded_by,
    DATE_ADD(NOW(), INTERVAL -90 DAY) AS recorded_at,
    NOW() AS created_at
FROM temp_enrollments te
WHERE te.enrollment_id NOT IN (SELECT enrollment_id FROM tbl_exam_results WHERE assessment_type = 'AC')
LIMIT 165;

-- 4.2 AC - 2º Trimestre
INSERT INTO tbl_exam_results (enrollment_id, assessment_type, score, recorded_by, recorded_at, created_at)
SELECT 
    te.enrollment_id,
    'AC' AS assessment_type,
    ROUND(11 + RAND() * 7, 1) AS score,
    11 + (te.class_id % 10) AS recorded_by,
    DATE_ADD(NOW(), INTERVAL -60 DAY) AS recorded_at,
    NOW() AS created_at
FROM temp_enrollments te
WHERE (te.enrollment_id, 'AC') NOT IN (SELECT enrollment_id, assessment_type FROM tbl_exam_results WHERE assessment_type = 'AC' GROUP BY enrollment_id HAVING COUNT(*) >= 2)
LIMIT 165;

-- 4.3 AC - 3º Trimestre
INSERT INTO tbl_exam_results (enrollment_id, assessment_type, score, recorded_by, recorded_at, created_at)
SELECT 
    te.enrollment_id,
    'AC' AS assessment_type,
    ROUND(12 + RAND() * 6, 1) AS score,
    11 + (te.class_id % 10) AS recorded_by,
    DATE_ADD(NOW(), INTERVAL -30 DAY) AS recorded_at,
    NOW() AS created_at
FROM temp_enrollments te
WHERE (te.enrollment_id, 'AC') NOT IN (SELECT enrollment_id, assessment_type FROM tbl_exam_results WHERE assessment_type = 'AC' GROUP BY enrollment_id HAVING COUNT(*) >= 3)
LIMIT 165;

-- ========================================================
-- 5. GERAR NOTAS DE RECURSO (REC) PARA ALUNOS COM NOTAS BAIXAS
-- ========================================================

INSERT INTO tbl_exam_results (enrollment_id, exam_schedule_id, assessment_type, score, is_absent, recorded_by, recorded_at, created_at)
SELECT 
    er.enrollment_id,
    tes.exam_schedule_id,
    'REC' AS assessment_type,
    ROUND(er.score + 2 + RAND() * 3, 1) AS score,  -- Melhora a nota
    0 AS is_absent,
    er.recorded_by,
    DATE_ADD(NOW(), INTERVAL -15 DAY) AS recorded_at,
    NOW() AS created_at
FROM tbl_exam_results er
JOIN temp_exam_schedules tes ON tes.exam_schedule_id = er.exam_schedule_id
WHERE er.assessment_type = 'NPT' 
  AND er.score < 10  -- Alunos com nota baixa
  AND er.score > 0   -- Que não faltaram
  AND tes.exam_board_id = 5  -- Período de recurso
  AND RAND() < 0.3  -- 30% destes alunos fazem recurso
LIMIT 50;

-- ========================================================
-- 6. ESTATÍSTICAS E VERIFICAÇÃO
-- ========================================================

SELECT 'RESUMO DE DADOS GERADOS' AS '';

SELECT 
    'Presenças' AS Tipo,
    COUNT(*) AS Total
FROM tbl_exam_attendance
UNION ALL
SELECT 
    CONCAT('Notas ', assessment_type),
    COUNT(*)
FROM tbl_exam_results
GROUP BY assessment_type
UNION ALL
SELECT 
    'TOTAL REGISTROS',
    (SELECT COUNT(*) FROM tbl_exam_attendance) + (SELECT COUNT(*) FROM tbl_exam_results);

SELECT 
    'MÉDIAS POR TIPO DE AVALIAÇÃO' AS '';
    
SELECT 
    assessment_type,
    COUNT(*) AS quantidade,
    ROUND(AVG(score), 2) AS media_geral,
    ROUND(AVG(CASE WHEN is_absent = 0 THEN score END), 2) AS media_presentes,
    SUM(is_absent) AS total_faltas
FROM tbl_exam_results
GROUP BY assessment_type
ORDER BY assessment_type;

-- ========================================================
-- 7. LIMPAR TABELAS TEMPORÁRIAS
-- ========================================================

DROP TEMPORARY TABLE IF EXISTS temp_enrollments;
DROP TEMPORARY TABLE IF EXISTS temp_exam_schedules;

-- ========================================================
-- FIM DO SCRIPT GERADOR
-- ========================================================
COMMIT;