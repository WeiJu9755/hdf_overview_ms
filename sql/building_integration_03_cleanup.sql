-- 棟別整合改版 步驟 3：既有資料整理（修改說明 §6.2～§6.6）
-- 前提：已執行 01_schema（含備份），並把 02_inventory 結果交客戶確認。
-- 依段落逐段執行，每段執行後可用 02_inventory 再檢查。

-- ------------------------------------------------------------------
-- §6.2 合併 overview_sub：同案同下包保留 auto_seq 最小者
-- ------------------------------------------------------------------
DROP TEMPORARY TABLE IF EXISTS tmp_sub_keep;
CREATE TEMPORARY TABLE tmp_sub_keep AS
SELECT case_id, builder_id, MIN(auto_seq) AS keep_seq,
       GROUP_CONCAT(DISTINCT NULLIF(TRIM(engineering_overview), '')
                    ORDER BY NULLIF(TRIM(engineering_overview), '') SEPARATOR '、') AS merged_overview
FROM overview_sub
WHERE builder_id IS NOT NULL AND builder_id <> ''
GROUP BY case_id, builder_id
HAVING COUNT(*) > 1;

DROP TEMPORARY TABLE IF EXISTS tmp_sub_map;
CREATE TEMPORARY TABLE tmp_sub_map AS
SELECT s.auto_seq AS old_seq, k.keep_seq
FROM overview_sub s
INNER JOIN tmp_sub_keep k ON k.case_id = s.case_id AND k.builder_id = s.builder_id
WHERE s.auto_seq <> k.keep_seq;

UPDATE overview_sub s
INNER JOIN tmp_sub_keep k ON k.keep_seq = s.auto_seq
SET s.engineering_overview = COALESCE(k.merged_overview, '');

UPDATE overview_building b
INNER JOIN tmp_sub_map m ON m.old_seq = b.seq
SET b.seq = m.keep_seq;

UPDATE overview_manpower_sub ms
INNER JOIN tmp_sub_map m ON m.old_seq = ms.seq
SET ms.seq = m.keep_seq;

-- 材料模組（hdf_material_ms）也以 seq 關聯 overview_sub
UPDATE overview_material_building mb
INNER JOIN tmp_sub_map m ON m.old_seq = mb.seq
SET mb.seq = m.keep_seq;

DELETE s FROM overview_sub s
INNER JOIN tmp_sub_map m ON m.old_seq = s.auto_seq;

-- ------------------------------------------------------------------
-- 回填 overview_sub.source_slot（依 1,2,3,4,9,10 順序，取第一個符合的欄位）
-- ------------------------------------------------------------------
UPDATE overview_sub s INNER JOIN CaseManagement c ON c.case_id = s.case_id
SET s.source_slot = 1  WHERE s.source_slot IS NULL AND s.builder_id <> '' AND s.builder_id = c.subcontractor_id1;
UPDATE overview_sub s INNER JOIN CaseManagement c ON c.case_id = s.case_id
SET s.source_slot = 2  WHERE s.source_slot IS NULL AND s.builder_id <> '' AND s.builder_id = c.subcontractor_id2;
UPDATE overview_sub s INNER JOIN CaseManagement c ON c.case_id = s.case_id
SET s.source_slot = 3  WHERE s.source_slot IS NULL AND s.builder_id <> '' AND s.builder_id = c.subcontractor_id3;
UPDATE overview_sub s INNER JOIN CaseManagement c ON c.case_id = s.case_id
SET s.source_slot = 4  WHERE s.source_slot IS NULL AND s.builder_id <> '' AND s.builder_id = c.subcontractor_id4;
UPDATE overview_sub s INNER JOIN CaseManagement c ON c.case_id = s.case_id
SET s.source_slot = 9  WHERE s.source_slot IS NULL AND s.builder_id <> '' AND s.builder_id = c.subcontractor_id9;
UPDATE overview_sub s INNER JOIN CaseManagement c ON c.case_id = s.case_id
SET s.source_slot = 10 WHERE s.source_slot IS NULL AND s.builder_id <> '' AND s.builder_id = c.subcontractor_id10;

-- ------------------------------------------------------------------
-- §6.3 補 overview_building.builder_id，並帶入施作樓層備註
-- ------------------------------------------------------------------
UPDATE overview_building b
INNER JOIN overview_sub s ON s.auto_seq = b.seq
SET b.builder_id = s.builder_id
WHERE (b.builder_id IS NULL OR b.builder_id = '')
  AND s.builder_id IS NOT NULL AND s.builder_id <> '';

UPDATE overview_building b
INNER JOIN overview_sub s ON s.auto_seq = b.seq
SET b.construction_floor = COALESCE(s.engineering_overview, '')
WHERE b.construction_floor = '';

-- ------------------------------------------------------------------
-- §6.4 重複 overview_building：不在此自動處理。
-- 依 02_inventory B 的清單，由客戶決定後手動刪除多餘列
-- （manpower_cnt > 0 的列需先處理 overview_manpower_sub.seq2）。
-- ------------------------------------------------------------------

-- ------------------------------------------------------------------
-- §6.5 回填下包商資料的棟別（subcontractor_buildings{N} 為空才回填）
-- ------------------------------------------------------------------
DROP TEMPORARY TABLE IF EXISTS tmp_builder_buildings;
CREATE TEMPORARY TABLE tmp_builder_buildings AS
SELECT case_id, builder_id,
       GROUP_CONCAT(DISTINCT building ORDER BY building SEPARATOR ',') AS buildings
FROM overview_building
WHERE building <> '' AND builder_id IS NOT NULL AND builder_id <> ''
GROUP BY case_id, builder_id;

UPDATE CaseManagement c INNER JOIN tmp_builder_buildings x ON x.case_id = c.case_id AND x.builder_id = c.subcontractor_id1
SET c.subcontractor_buildings1 = x.buildings WHERE COALESCE(c.subcontractor_buildings1, '') = '';
UPDATE CaseManagement c INNER JOIN tmp_builder_buildings x ON x.case_id = c.case_id AND x.builder_id = c.subcontractor_id2
SET c.subcontractor_buildings2 = x.buildings WHERE COALESCE(c.subcontractor_buildings2, '') = '';
UPDATE CaseManagement c INNER JOIN tmp_builder_buildings x ON x.case_id = c.case_id AND x.builder_id = c.subcontractor_id3
SET c.subcontractor_buildings3 = x.buildings WHERE COALESCE(c.subcontractor_buildings3, '') = '';
UPDATE CaseManagement c INNER JOIN tmp_builder_buildings x ON x.case_id = c.case_id AND x.builder_id = c.subcontractor_id4
SET c.subcontractor_buildings4 = x.buildings WHERE COALESCE(c.subcontractor_buildings4, '') = '';
UPDATE CaseManagement c INNER JOIN tmp_builder_buildings x ON x.case_id = c.case_id AND x.builder_id = c.subcontractor_id9
SET c.subcontractor_buildings9 = x.buildings WHERE COALESCE(c.subcontractor_buildings9, '') = '';
UPDATE CaseManagement c INNER JOIN tmp_builder_buildings x ON x.case_id = c.case_id AND x.builder_id = c.subcontractor_id10
SET c.subcontractor_buildings10 = x.buildings WHERE COALESCE(c.subcontractor_buildings10, '') = '';

-- ------------------------------------------------------------------
-- §6.6 唯一鍵：02_inventory A、B 都為 0 筆後才執行
-- ------------------------------------------------------------------
-- ALTER TABLE overview_sub      ADD UNIQUE KEY uk_case_builder          (case_id, builder_id);
-- ALTER TABLE overview_building ADD UNIQUE KEY uk_case_building_builder (case_id, building, builder_id);
