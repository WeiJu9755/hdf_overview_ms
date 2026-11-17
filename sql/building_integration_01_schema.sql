-- 棟別整合改版 步驟 1：備份與資料表異動（修改說明 §2.3）
-- 在網站使用的 *_info 資料庫執行，部署 PHP 之前先執行。
-- 唯一鍵放在 building_integration_03_cleanup.sql 最後，資料整理完成後才加。

-- 1) 備份
CREATE TABLE overview_sub_bak_20261111          AS SELECT * FROM overview_sub;
CREATE TABLE overview_building_bak_20261111     AS SELECT * FROM overview_building;
CREATE TABLE overview_manpower_sub_bak_20261111 AS SELECT * FROM overview_manpower_sub;

-- 2) 案件下包單位：記錄來源下包欄位序號
ALTER TABLE overview_sub
  ADD COLUMN source_slot TINYINT NULL COMMENT '來源下包欄位序號 1/2/3/4/9/10' AFTER builder_id;

-- 3) 案件棟別下包：施作樓層與同步狀態
ALTER TABLE overview_building
  ADD COLUMN construction_floor VARCHAR(255) NOT NULL DEFAULT '' COMMENT '施作樓層（下包商資料帶入）' AFTER building,
  ADD COLUMN sync_status VARCHAR(20) NOT NULL DEFAULT '正常' COMMENT '正常 / 已取消勾選' AFTER construction_floor;
