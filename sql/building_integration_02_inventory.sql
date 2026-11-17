-- 棟別整合改版 步驟 2：既有資料盤點（修改說明 §6.1，只讀）
-- 每段查詢的結果匯出給客戶確認。

-- A) 同案同下包有多筆 overview_sub（03_cleanup 會合併成一筆，保留 auto_seq 最小者）
SELECT s.case_id, s.builder_id, sc.subcontractor_name,
       COUNT(*) AS cnt,
       GROUP_CONCAT(s.auto_seq ORDER BY s.auto_seq) AS auto_seqs,
       GROUP_CONCAT(s.engineering_overview ORDER BY s.auto_seq SEPARATOR ' | ') AS engineering_overviews
FROM overview_sub s
LEFT JOIN subcontractor sc ON sc.subcontractor_id = s.builder_id
WHERE s.builder_id IS NOT NULL AND s.builder_id <> ''
GROUP BY s.case_id, s.builder_id, sc.subcontractor_name
HAVING COUNT(*) > 1
ORDER BY s.case_id, s.builder_id;

-- B) 同案同棟同下包有多筆 overview_building（需客戶決定保留哪一筆；manpower_cnt > 0 不可直接刪）
--    以 overview_sub.builder_id 補齊空白 builder_id 後判斷
SELECT b.case_id, b.building,
       COALESCE(NULLIF(b.builder_id, ''), s.builder_id) AS builder_id,
       b.auto_seq, b.seq, b.scheduled_entry_date, b.actual_entry_date,
       b.construction_days_first_floor, b.construction_days_per_floor, b.standard_manpower,
       (SELECT COUNT(*) FROM overview_manpower_sub m WHERE m.seq2 = b.auto_seq) AS manpower_cnt
FROM overview_building b
LEFT JOIN overview_sub s ON s.auto_seq = b.seq
WHERE (b.case_id, b.building, COALESCE(NULLIF(b.builder_id, ''), s.builder_id)) IN (
    SELECT b2.case_id, b2.building, COALESCE(NULLIF(b2.builder_id, ''), s2.builder_id)
    FROM overview_building b2
    LEFT JOIN overview_sub s2 ON s2.auto_seq = b2.seq
    GROUP BY b2.case_id, b2.building, COALESCE(NULLIF(b2.builder_id, ''), s2.builder_id)
    HAVING COUNT(*) > 1
)
ORDER BY b.case_id, b.building, builder_id, b.auto_seq;

-- C) overview_sub.builder_id 不在該案下包代工 1/2/3/4/9/10 的資料（手動新增造成）
SELECT s.auto_seq, s.case_id, s.builder_id, sc.subcontractor_name, s.engineering_overview,
       (SELECT COUNT(*) FROM overview_building b WHERE b.seq = s.auto_seq) AS building_cnt
FROM overview_sub s
INNER JOIN CaseManagement c ON c.case_id = s.case_id
LEFT JOIN subcontractor sc ON sc.subcontractor_id = s.builder_id
WHERE s.builder_id IS NULL OR s.builder_id = ''
   OR s.builder_id NOT IN (
        COALESCE(c.subcontractor_id1, ''), COALESCE(c.subcontractor_id2, ''),
        COALESCE(c.subcontractor_id3, ''), COALESCE(c.subcontractor_id4, ''),
        COALESCE(c.subcontractor_id9, ''), COALESCE(c.subcontractor_id10, ''))
ORDER BY s.case_id, s.auto_seq;

-- D) overview_building.builder_id 為空的資料（fill_from_sub 有值的會由 03_cleanup 補上）
SELECT b.auto_seq, b.case_id, b.building, b.seq, s.builder_id AS fill_from_sub
FROM overview_building b
LEFT JOIN overview_sub s ON s.auto_seq = b.seq
WHERE b.builder_id IS NULL OR b.builder_id = ''
ORDER BY b.case_id, b.building;

-- E) 下包商資料尚未勾選棟別、但 overview_building 已有該下包棟別（03_cleanup 會回填）
SELECT c.case_id, x.builder_id, x.buildings
FROM CaseManagement c
INNER JOIN (
    SELECT case_id, builder_id, GROUP_CONCAT(DISTINCT building ORDER BY building SEPARATOR ',') AS buildings
    FROM overview_building
    WHERE building <> '' AND builder_id <> ''
    GROUP BY case_id, builder_id
) x ON x.case_id = c.case_id
WHERE (x.builder_id = c.subcontractor_id1  AND COALESCE(c.subcontractor_buildings1, '')  = '')
   OR (x.builder_id = c.subcontractor_id2  AND COALESCE(c.subcontractor_buildings2, '')  = '')
   OR (x.builder_id = c.subcontractor_id3  AND COALESCE(c.subcontractor_buildings3, '')  = '')
   OR (x.builder_id = c.subcontractor_id4  AND COALESCE(c.subcontractor_buildings4, '')  = '')
   OR (x.builder_id = c.subcontractor_id9  AND COALESCE(c.subcontractor_buildings9, '')  = '')
   OR (x.builder_id = c.subcontractor_id10 AND COALESCE(c.subcontractor_buildings10, '') = '')
ORDER BY c.case_id;
