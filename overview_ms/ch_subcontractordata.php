<?php

session_start();

$memberID = $_SESSION['memberID'];
$powerkey = $_SESSION['powerkey'];


require_once '/website/os/Mobile-Detect-2.8.34/Mobile_Detect.php';
$detect = new Mobile_Detect;


//載入公用函數
@include_once '/website/include/pub_function.php';

//連結資料
@include_once("/website/class/".$site_db."_info_class.php");

/* 使用xajax */
@include_once '/website/xajax/xajax_core/xajax.inc.php';
$xajax = new xajax();


// 下包商資料中會帶入的下包欄位（5～8 畫面已不顯示，不帶入）
function overviewSubcontractorSlots() {
	return array(1, 2, 3, 4, 9, 10);
}

// 逗號字串轉棟別陣列（去空白、去重複）
function overviewSplitBuildings($value) {
	$result = array();
	foreach (explode(',', (string)$value) as $item) {
		$item = trim($item);
		if ($item !== '' && !in_array($item, $result, true)) {
			$result[] = $item;
		}
	}
	return $result;
}

function overviewAppendUnique(&$list, $value) {
	$value = trim((string)$value);
	if ($value !== '' && !in_array($value, $list, true)) {
		$list[] = $value;
	}
}

/**
 * 依下包商資料同步案件下包單位（overview_sub）與案件棟別下包（overview_building）。
 * 規則見《棟別整合改版_修改說明》§3.1。
 */
function overviewSyncSubcontractorData($case_id, $memberID) {

	$result = array('ok' => false, 'message' => '', 'added' => 0, 'updated' => 0, 'cancelled' => 0, 'warnings' => array());

	$caseSql = addslashes($case_id);
	$memberSql = addslashes($memberID);

	$mDB = new MywebDB();

	$mDB->query("SELECT * FROM CaseManagement WHERE case_id = '$caseSql' LIMIT 1");
	if ($mDB->rowCount() <= 0) {
		$mDB->remove();
		$result['message'] = '找不到案件資料';
		return $result;
	}
	$case = $mDB->fetchRow(2);

	$estimatedArrivalSql = addslashes((string)$case['estimated_arrival_date']);
	$completionSql = addslashes((string)$case['completion_date']);

	// 1) 整理各下包欄位；同一下包商出現在多個欄位時合併為一筆
	$builders = array();
	foreach (overviewSubcontractorSlots() as $slot) {
		$builder = trim((string)$case["subcontractor_id$slot"]);
		if ($builder === '') {
			continue;
		}
		$floor = trim((string)$case["construction_floor$slot"]);
		$buildings = overviewSplitBuildings($case["subcontractor_buildings$slot"]);

		if (!isset($builders[$builder])) {
			$builders[$builder] = array('slot' => $slot, 'floors' => array(), 'buildings' => array());
		}
		overviewAppendUnique($builders[$builder]['floors'], $floor);

		if (empty($buildings)) {
			$result['warnings'][] = "下包代工{$slot} 尚未勾選棟別";
			continue;
		}
		foreach ($buildings as $building) {
			if (!isset($builders[$builder]['buildings'][$building])) {
				$builders[$builder]['buildings'][$building] = array();
			}
			overviewAppendUnique($builders[$builder]['buildings'][$building], $floor);
		}
	}

	$activeKeys = array();

	foreach ($builders as $builder => $info) {
		$builderSql = addslashes($builder);
		$overview = implode('、', $info['floors']);
		$overviewSql = addslashes($overview);
		$slot = (int)$info['slot'];

		// 2) 案件下包單位：upsert overview_sub (case_id, builder_id)
		$seq = '';
		$mDB->query("SELECT auto_seq, engineering_overview, source_slot FROM overview_sub
			WHERE case_id = '$caseSql' AND builder_id = '$builderSql'
			ORDER BY auto_seq LIMIT 1");
		if ($mDB->rowCount() > 0) {
			$row = $mDB->fetchRow(2);
			$seq = $row['auto_seq'];
			if ((string)$row['engineering_overview'] !== $overview || (string)$row['source_slot'] !== (string)$slot) {
				// 只在實際變更時更新 last_modify，避免 overview_notify 多發通知
				$mDB->query("UPDATE overview_sub SET
						engineering_overview = '$overviewSql'
						,source_slot = '$slot'
						,makeby = '$memberSql'
						,last_modify = now()
					WHERE auto_seq = '$seq'");
			}
		} else {
			$mDB->query("INSERT INTO overview_sub (case_id, builder_id, engineering_overview, source_slot, makeby, last_modify)
				VALUES ('$caseSql', '$builderSql', '$overviewSql', '$slot', '$memberSql', now())");
			$mDB->query("SELECT auto_seq FROM overview_sub
				WHERE case_id = '$caseSql' AND builder_id = '$builderSql'
				ORDER BY auto_seq DESC LIMIT 1");
			if ($mDB->rowCount() > 0) {
				$row = $mDB->fetchRow(2);
				$seq = $row['auto_seq'];
			}
		}

		if ($seq === '') {
			$result['warnings'][] = "下包單位 {$builder} 建立失敗";
			continue;
		}

		// 3) 案件棟別下包：每一棟 upsert overview_building (case_id, building, builder_id)
		foreach ($info['buildings'] as $building => $floors) {
			$activeKeys[$building."\t".$builder] = true;
			$buildingSql = addslashes($building);
			$floorText = implode('、', $floors);
			$floorSql = addslashes($floorText);

			$mDB->query("SELECT auto_seq, seq, construction_floor, sync_status FROM overview_building
				WHERE case_id = '$caseSql' AND building = '$buildingSql' AND builder_id = '$builderSql'
				ORDER BY auto_seq LIMIT 1");
			if ($mDB->rowCount() > 0) {
				$row = $mDB->fetchRow(2);
				if ((string)$row['seq'] !== (string)$seq || (string)$row['construction_floor'] !== $floorText || $row['sync_status'] !== '正常') {
					// 不動進場日、天數等已編輯資料
					$mDB->query("UPDATE overview_building SET
							seq = '$seq'
							,construction_floor = '$floorSql'
							,sync_status = '正常'
							,makeby = '$memberSql'
							,last_modify = now()
						WHERE auto_seq = '{$row['auto_seq']}'");
					$result['updated']++;
				}
			} else {
				$mDB->query("INSERT INTO overview_building (case_id, seq, builder_id, building, construction_floor, sync_status
						,scheduled_entry_date, scheduled_completion_date, construction_days_first_floor, construction_days_per_floor, makeby, last_modify)
					VALUES ('$caseSql', '$seq', '$builderSql', '$buildingSql', '$floorSql', '正常'
						,'$estimatedArrivalSql', '$completionSql', '30', '18', '$memberSql', now())");
				$result['added']++;
			}
		}
	}

	// 4) 取消勾選：只標示，不刪除
	$cancelSeqs = array();
	$mDB->query("SELECT auto_seq, building, builder_id FROM overview_building
		WHERE case_id = '$caseSql' AND builder_id IS NOT NULL AND builder_id <> '' AND sync_status <> '已取消勾選'");
	while ($row = $mDB->fetchRow(2)) {
		if (!isset($activeKeys[$row['building']."\t".$row['builder_id']])) {
			$cancelSeqs[] = (int)$row['auto_seq'];
		}
	}
	if (!empty($cancelSeqs)) {
		$mDB->query("UPDATE overview_building SET
				sync_status = '已取消勾選'
				,makeby = '$memberSql'
				,last_modify = now()
			WHERE auto_seq IN (".implode(',', $cancelSeqs).")");
		$result['cancelled'] = count($cancelSeqs);
	}

	// 5) 更新主檔
	$mDB->query("UPDATE CaseManagement SET
			last_modify8 = now()
			,makeby8 = '$memberSql'
			,update_count8 = update_count8 + 1
		WHERE case_id = '$caseSql'");

	$mDB->remove();

	$result['ok'] = true;
	return $result;
}


$xajax->registerFunction("processform");
function processform($aFormValues){

	$objResponse = new xajaxResponse();

	$memberID = $_SESSION['memberID'];
	$case_id = trim($aFormValues['case_id']);

	if ($case_id == '') {
		$objResponse->script("jAlert('警示', '缺少案件編號', 'red', '', 2000);");
		return $objResponse;
	}

	$result = overviewSyncSubcontractorData($case_id, $memberID);

	if (!$result['ok']) {
		$objResponse->script("jAlert('警示', '".addslashes($result['message'])."', 'red', '', 2000);");
		return $objResponse;
	}

	$html = '<div class="alert alert-success mb-2">已加入！新增 '.$result['added'].' 棟、更新 '.$result['updated'].' 棟、取消勾選 '.$result['cancelled'].' 棟</div>';
	if (!empty($result['warnings'])) {
		$html .= '<div class="alert alert-warning mb-2">';
		foreach ($result['warnings'] as $warning) {
			$html .= '<div>'.htmlspecialchars($warning, ENT_QUOTES, 'UTF-8').'</div>';
		}
		$html .= '</div>';
	}
	if ($result['cancelled'] > 0) {
		$html .= '<div class="alert alert-danger mb-2">取消勾選的棟別不會自動刪除，請至「預計工程人力」確認後手動刪除。</div>';
	}

	$objResponse->assign("sync_result", "innerHTML", $html);
	$objResponse->script("parent.overview_sub_myDraw();");

	return $objResponse;
}

$xajax->processRequest();


$fm = $_GET['fm'];
$case_id = $_GET['case_id'];
$caseSql = addslashes($case_id);

$mess_title = $title;


$mDB = "";
$mDB = new MywebDB();

$slots = overviewSubcontractorSlots();

$Qry = "SELECT a.*";
foreach ($slots as $slot) {
	$Qry .= ", s$slot.subcontractor_name AS subcontractor_name$slot";
}
$Qry .= " FROM CaseManagement a";
foreach ($slots as $slot) {
	$Qry .= " LEFT JOIN subcontractor s$slot ON s$slot.subcontractor_id = a.subcontractor_id$slot";
}
$Qry .= " WHERE a.case_id = '$caseSql'";

$mDB->query($Qry);
$slot_html = '';
if ($mDB->rowCount() > 0) {
	$row = $mDB->fetchRow(2);
	$region = $row['region'];
	$case_id = $row['case_id'];
	$construction_id = $row['construction_id'];

	foreach ($slots as $slot) {
		$e = function($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
		$sub_id = $e($row["subcontractor_id$slot"]);
		$sub_name = $e($row["subcontractor_name$slot"]);
		$floor = $e($row["construction_floor$slot"]);
		$buildings = $e(implode('、', overviewSplitBuildings($row["subcontractor_buildings$slot"])));
		$amt = number_format((float)$row["total_contract_amt$slot"]);

		if (trim((string)$row["subcontractor_id$slot"]) === '') {
			$buildings_html = '<span class="text-secondary">（未填下包商，不帶入）</span>';
		} elseif ($buildings === '') {
			$buildings_html = '<span class="red">尚未勾選棟別，不會建立棟別資料</span>';
		} else {
			$buildings_html = $buildings;
		}

$slot_html .= <<<EOT
					<div>
						<div class="field_div1">下包代工{$slot}:</div>
						<div class="field_div2">
							<div class="inline blue weight my-2 me-2">$sub_id</div>
							<div class="inline blue weight my-2">$sub_name</div>
						</div>
					</div>
					<div>
						<div class="field_div1">棟別{$slot}:</div>
						<div class="field_div2">
							<div class="inline blue weight my-2">$buildings_html</div>
						</div>
					</div>
					<div>
						<div class="field_div1">施作樓層{$slot}:</div>
						<div class="field_div2">
							<div class="inline blue weight my-2">$floor</div>
						</div>
					</div>
					<div>
						<div class="field_div1">合約總價{$slot}(含稅):</div>
						<div class="field_div2">
							<div class="inline blue weight my-2">$amt</div>
						</div>
					</div>
					<hr class="style_b" style="margin: 5px 0 -5px 0;">

EOT;
	}
}

$mDB->remove();


$show_savebtn=<<<EOT
<div class="btn-group vbottom" role="group" style="margin-top:5px;">
	<button id="close" class="btn btn-danger" type="button" onclick="parent.overview_sub_myDraw();parent.$.fancybox.close();" style="padding: 5px 15px;"><i class="bi bi-power"></i>&nbsp;關閉</button>
</div>
EOT;


if (!($detect->isMobile() && !$detect->isTablet())) {
	$isMobile = 0;

$style_css=<<<EOT
<style>

.card_full {
    width: 100%;
	height: 100vh;
}

#full {
    width: 100%;
	height: 100vh;
}

#info_container {
	width: 100% !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:220px;display: none;font-size:18px;color:#000;text-align:right;font-weight:700;padding:15px 10px 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}
.field_div2 {width:100%;max-width:500px;display: none;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 0 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}

.code_class {
	width:220px;
	text-align:right;
	padding:0 10px 0 0;
}

.custom-pointer {
  cursor: pointer;
}

</style>

EOT;

} else {
	$isMobile = 1;

$style_css=<<<EOT
<style>

.card_full {
    width: 100vw;
	height: 100vh;
}

#full {
    width: 100vw;
	height: 100vh;
}

#info_container {
	width: 100% !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:15px 10px 0 0;vertical-align: top;}
.field_div2 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 10px 0 0;vertical-align: top;}

.code_class {
	width:auto;
	text-align:left;
	padding:0 10px 0 0;
}

</style>
EOT;

}


$show_center=<<<EOT

$style_css

<div class="card card_full">
	<div class="card-header text-bg-info">
		<div class="size14 weight float-start" style="margin-top: 5px;">
			$mess_title
		</div>
		<div class="float-end" style="margin-top: -5px;">
			$show_savebtn
		</div>
	</div>
	<div id="full" class="card-body data-overlayscrollbars-initialize">
		<div id="info_container">
			<form method="post" id="modifyForm" name="modifyForm" enctype="multipart/form-data" action="javascript:void(null);">
			<div class="w-100 mb-5">
				<div class="field_container3">
					<div>
						<div class="field_div2">
							<div class="my-1 text-nowrap">
								<div class="inline code_class">工程案件:</div>
								<div class="inline blue weight me-2">$case_id</div>
								<div class="inline blue weight me-2">$region</div>
								<div class="inline blue weight me-2">$construction_id</div>
							</div>
						</div>
					</div>
					<div class="size12 text-secondary my-2">依下包商資料的下包代工 1、2、3、4、9、10 建立「下包單位」與「棟別 + 下包單位」；取消勾選的棟別只標示，不會刪除。</div>
					<hr class="style_b" style="margin: 5px 0 -5px 0;">
$slot_html
					<div id="sync_result" class="mt-3"></div>
					<div class="mt-3 text-center">
						<input type="hidden" name="fm" value="$fm" />
						<input type="hidden" name="site_db" value="$site_db" />
						<input type="hidden" name="case_id" value="$case_id" />
						<button id="confirm_sync" class="btn btn-danger btn-lg" type="button" onclick="CheckValue(this.form);" style="padding: 5px 15px;"><i class="bi bi-building-add"></i>&nbsp;確定加入</button>
					</div>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
<script>

function CheckValue(thisform) {
	xajax_processform(xajax.getFormValues('modifyForm'));
}

</script>

EOT;

?>
