<?php

//error_reporting(E_ALL); 
//ini_set('display_errors', '1');

session_start();

$memberID = $_SESSION['memberID'];
$powerkey = $_SESSION['powerkey'];


require_once '/website/os/Mobile-Detect-2.8.34/Mobile_Detect.php';
$detect = new Mobile_Detect;

if (!($detect->isMobile() && !$detect->isTablet())) {
	$isMobile = "0";
} else {
	$isMobile = "1";
}

@include_once("/website/class/".$site_db."_info_class.php");

/* 使用xajax */
@include_once '/website/xajax/xajax_core/xajax.inc.php';
$xajax = new xajax();


$xajax->registerFunction("owner_builder");
function owner_builder($auto_seq,$check,$memberID){

	$objResponse = new xajaxResponse();

	$mDB = "";
	$mDB = new MywebDB();
	$Qry = "update CaseManagement set 
			owner_builder = '$check' 
			,makeby8	= '$memberID'
			,last_modify8 = now()
			,update_count8 = update_count8 + 1
			where auto_seq = '$auto_seq'";
	$mDB->query($Qry);
	$mDB->remove();
	
    $objResponse->script("oTable = $('#db_table').dataTable();oTable.fnDraw(false)");

	return $objResponse;
	
}

$xajax->registerFunction("owner_contractor");
function owner_contractor($auto_seq,$check,$memberID){

	$objResponse = new xajaxResponse();

	$mDB = "";
	$mDB = new MywebDB();
	$Qry = "update CaseManagement set 
			owner_contractor = '$check' 
			,makeby8	= '$memberID'
			,last_modify8 = now()
			,update_count8 = update_count8 + 1
			where auto_seq = '$auto_seq'";
	$mDB->query($Qry);
	$mDB->remove();
	
    $objResponse->script("oTable = $('#db_table').dataTable();oTable.fnDraw(false)");

	return $objResponse;
	
}

$xajax->registerFunction("returnValue");
function returnValue($auto_seq){
	$objResponse = new xajaxResponse();
	$earliest_entry_date = "";
	$latest_completion_date = "";

	$mDB = "";
	$mDB = new MywebDB();
	
	$Qry="SELECT 
			a.auto_seq,
			MIN(b.actual_entry_date) AS earliest_entry_date,
			MAX(b.actual_completion_date) AS latest_completion_date
		FROM CaseManagement a
		LEFT JOIN overview_building b 
			ON b.case_id = a.case_id
		WHERE a.auto_seq = '$auto_seq'
		GROUP BY a.auto_seq;";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		while ($row=$mDB->fetchRow(2)) {
			$earliest_entry_date = (!empty($row['earliest_entry_date']) && $row['earliest_entry_date'] != "0000-00-00") ? $row['earliest_entry_date'] : " - ";
			$latest_completion_date = (!empty($row['latest_completion_date']) && $row['latest_completion_date'] != "0000-00-00") ? $row['latest_completion_date'] : " - ";

			

		}
	}
	
	$mDB->remove();
	
	$objResponse->assign("earliest_entry_date".$auto_seq,"innerHTML",$earliest_entry_date);
	$objResponse->assign("latest_completion_date".$auto_seq,"innerHTML",$latest_completion_date);	
	
	
    return $objResponse;
	
}

$xajax->processRequest();


$fm = $_GET['fm'];
//$pjt = $_GET['pjt'];
//$project_id = $_GET['project_id'];
//$auth_id = $_GET['auth_id'];

$project_id = "202502030001";
$auth_id = "OV01";
if (isset($_GET['pjt']))
	$pjt = $_GET['pjt'];
else
	$pjt = "各工程詳細資料";


$tb = "CaseManagement";
$pro_id = "overview";

$m_t = urlencode($_GET['pjt']);

$mess_title = $pjt;


$today = date("Y-m-d");

$dataTable_de = getDataTable_de();
$Prompt = getlang("提示訊息");
$Confirm = getlang("確認");
$Cancel = getlang("取消");

$pubweburl = "//".$domainname;



//網頁標題
$page_title = $pjt;
$page_description = trim(strip_tags($pjt));
$page_description = utf8_substr($page_description,0,1024);
$page_keywords = $pjt;

//載入上方索引列模組
@include $m_location."/sub_modal/base/project_index.php";


$m_pjt = urlencode($_GET['pjt']);

$mk = $_GET['mk'];
$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];


$today = date("Y-m-d");


$pubweburl = "//".$domainname;


//載入功能選單模組
@include $m_location."/sub_modal/base/project_menu.php";


$fellow_count = 0;
//取得指定管理人數
$pjmyfellow_row = getkeyvalue2($site_db."_info","pjmyfellow","web_id = '$web_id' and project_id = '$project_id' and auth_id = '$auth_id' and pro_id = '$pro_id'","count(*) as fellow_count");
$fellow_count =$pjmyfellow_row['fellow_count'];
if ($fellow_count == 0)
	$fellow_count = "";

/*
$warning_count = 0;
//取得指定管理人數(警訊通知對象)
$pjmyfellow_row = getkeyvalue2($site_db."_info","pjmyfellow","web_id = '$web_id' and project_id = '$project_id' and auth_id = '$auth_id' and pro_id = 'alertlist'","count(*) as warning_count");
$warning_count =$pjmyfellow_row['warning_count'];
if ($warning_count == 0)
	$warning_count = "";
*/

$pjItemManager = false;
//檢查是否為指定管理人
$pjmyfellow_row = getkeyvalue2($site_db."_info","pjmyfellow","web_id = '$web_id' and project_id = '$project_id' and auth_id = '$auth_id' and pro_id = '$pro_id' and member_no = '$memberID'","count(*) as enable_count");
$enable_count =$pjmyfellow_row['enable_count'];
if ($enable_count > 0)
	$pjItemManager = true;


//設定權限
$cando = "N";
if (($powerkey=="A") || ($super_admin=="Y") || ($pjItemManager == true)) {
	$cando = "Y";
}


//取得使用者員工身份
$member_picture = getmemberpict160($memberID);

$member_row = getkeyvalue2("memberinfo","member","member_no = '$memberID'","member_name");
$member_name = $member_row['member_name'];

$employee_row = getkeyvalue2($site_db."_info","employee","member_no = '$memberID'","count(*) as manager_count,employee_name,employee_type,team_id");
$manager_count =$employee_row['manager_count'];
$team_id = $employee_row['team_id'];
if ($manager_count > 0) {
	$employee_name = $employee_row['employee_name'];
	$employee_type = $employee_row['employee_type'];

	$team_row = getkeyvalue2($site_db."_info","team","team_id = '$team_id'","team_name");
	$team_name = $team_row['team_name'];
} else {
	$employee_name = $member_name;
	$team_name = "未在員工名單";
}


$member_logo=<<<EOT
<div class="mytable bg-white m-auto rounded">
	<div class="myrow">
		<div class="mycell" style="text-align:center;width:73px;padding: 5px 0;">
			<img src="$member_picture" height="75" class="rounded">
		</div>
		<div class="mycell text-start p-2 vmiddle" style="width:107px;">
			<div class="size14 blue02 weight mb-1 text-nowrap">$employee_name</div>
			<div class="size12 weight text-nowrap">$team_name</div>
			<div class="size12 weight text-nowrap">$employee_type</div>
		</div>
	</div>
</div>
EOT;


$show_disabled = "";
$show_disabled_warning = "";
/*
//if ((empty($team_id)) || ((($super_admin=="Y") && ($admin_readonly == "Y")) || (($super_advanced=="Y") && ($advanced_readonly == "Y")))) {
if (((($super_admin=="Y") && ($admin_readonly == "Y")) || (($super_advanced=="Y") && ($advanced_readonly == "Y")))) {
	if ($pjItemManager <> "Y") {
		$show_disabled = "disabled";
		$show_disabled_warning = "<div class=\"size12 red weight text-center p-2\">此區為管理人專區，非經授權請勿進行任何處理</div>";
	}
}
*/

//if ($cando == "Y") {
	if (($super_admin == "Y") && ($admin_readonly == "Y")) {
		$show_disabled = "disabled";
		$show_disabled_warning = "<div class=\"size12 red weight text-center p-2\">此區為管理人專區，非經授權請勿進行任何處理</div>";
	}
//}


$show_admin_list = "";


if ($cando == "Y") {

	$show_modify_btn = "";

		if (($powerkey == "A") || (($super_admin=="Y") && ($admin_readonly <> "Y"))) {
$show_admin_list=<<<EOT
<div class="text-center">
	<div class="btn-group me-2 mb-2" role="group">
		<a role="button" class="btn btn-light" href="javascript:void(0);" onclick="openfancybox_edit('/index.php?ch=fellowlist&project_id=$project_id&auth_id=$auth_id&pro_id=$pro_id&t=指定管理人&fm=base',850,'96%',true);" title="指定管理人"><i class="bi bi-shield-fill-check size14 red inline me-2 vmiddle"></i><div class="inline size12 me-2">指定管理人</div><div class="inline red weight vmiddle">$fellow_count</div></a>
		<!--
		<a role="button" class="btn btn-light" href="javascript:void(0);" onclick="openfancybox_edit('/index.php?ch=fellowlist&project_id=$project_id&auth_id=$auth_id&pro_id=alertlist&t=警訊通知對象&fm=base',850,'96%',true);" title="警訊通知對象"><i class="bi bi-bell-fill size14 red inline me-2 vmiddle"></i><div class="inline size12 me-2">警訊通知對象</div><div class="inline red weight vmiddle">$warning_count</div></a>
		-->
	</div>
</div>
EOT;
		}

$show_modify_btn=<<<EOT
<div class="text-center my-2">
	<div class="btn-group me-2 mb-2" role="group">
		<!--
		<button $show_disabled type="button" class="btn btn-danger text-nowrap" onclick="openfancybox_edit('/index.php?ch=add&project_id=$project_id&auth_id=$auth_id&t=$t&fm=$fm',1800,'96%','');"><i class="bi bi-plus-circle"></i>&nbsp;新增資料</button>
		-->
		<button type="button" class="btn btn-success text-nowrap" onclick="myDraw();"><i class="bi bi-arrow-repeat"></i>&nbsp;重整</button>
		<button type="button" class="btn btn-warning text-nowrap" onclick="add_shortcuts('$site_db','$web_id','$templates','$project_id','$auth_id','$pjcaption','$i_caption','$fm','$memberID');"><i class="bi bi-lightning-fill red"></i>&nbsp;加入至快捷列</button>
	</div>
	<div class="btn-group mb-2" role="group">
		<!--
		<a $show_disabled role="button" class="btn btn-success text-nowrap" href="/index.php?ch=case_day_summary&project_id=$project_id&auth_id=$auth_id&fm=$fm" target="_blank"><i class="bi bi-printer"></i>&nbsp;出工日報表</a>
		-->
	</div>
</div>
$show_admin_list
EOT;





$list_view=<<<EOT
<div class="w-100 m-auto p-1 mb-5 bg-white">
	<div style="width:auto;padding: 5px;">
		<div class="inline float-start me-1 mb-2">$left_menu</div>
		<a role="button" class="btn btn-light px-2 py-1 float-start inline me-3 mb-2" href="javascript:void(0);" onClick="parent.history.back();"><i class="bi bi-chevron-left"></i>&nbsp;回上頁</a>
		<a role="button" class="btn btn-light p-1" href="/">回首頁</a>$mess_title
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-2 col-sm-12 col-md-12 p-1 d-flex flex-column justify-content-center align-items-center">
				$member_logo
			</div> 
			<div class="col-lg-8 col-sm-12 col-md-12 p-1">
				<div class="size20 pt-1 text-center">$pjt</div>
				$show_modify_btn
				$show_disabled_warning
			</div> 
			<div class="col-lg-2 col-sm-12 col-md-12">
			</div> 
		</div>
	</div>
	$show_ConfirmSending_btn
	<table class="table table-bordered border-dark w-100" id="db_table" style="min-width:1200px;">
		<thead class="table-light border-dark">
			<tr style="border-bottom: 1px solid #000;">
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">案件編號</th>
				<th class="text-center text-nowrap vmiddle" style="width:3%;padding: 10px;background-color: #CBF3FC;">區域</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">案件種類</th>
				<th class="text-center text-nowrap vmiddle th-stack" style="width:8%;background-color: #CBF3FC;"><div>建商單位</div><div>營造商單位</div></th>
				<th class="text-center text-nowrap vmiddle th-stack" style="width:2%;background-color: #CBF3FC;"><div>業主</div><div>業主</div></th>
				<th class="text-center text-nowrap vmiddle" style="width:8%;padding: 10px;background-color: #CBF3FC;">工程名稱</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">承攬模式</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">所屬公司</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">經辦人員</th>
				<th class="text-center text-nowrap vmiddle" style="width:7%;padding: 10px;background-color: #CBF3FC;">合約承攬建物棟數</th>
				<th class="text-center text-nowrap vmiddle th-stack" style="width:5%;background-color: #CBF3FC;"><div>志特編號</div><div>合約號碼</div></th>
				<th class="text-center text-nowrap vmiddle" style="width:8%;padding: 10px;background-color: #CBF3FC;">工地地址</th>
				<th class="text-center text-nowrap vmiddle" style="width:8%;padding: 10px;background-color: #CBF3FC;">工地google定位網址</th>
				<th class="text-center text-nowrap vmiddle th-stack" style="width:5%;background-color: #CBF3FC;"><div>預計進場日</div><div>預計完工日</div></th>
				<th class="text-center text-nowrap vmiddle th-stack" style="width:5%;background-color: #CBF3FC;"><div>實際進場日</div><div>實際完工日</div></th>
				<th class="text-center text-nowrap vmiddle" style="width:4%;padding: 10px;background-color: #CBF3FC;">工地狀態</th>
				<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">工程概況</th>
				<th class="text-center text-nowrap vmiddle" style="width:7%;padding: 10px;background-color: #CBF3FC;">最後修改</th>
			</tr>
		</thead>
		<tbody class="table-group-divider">
			<tr>
				<td colspan="18" class="dataTables_empty">資料載入中...</td>
			</tr>
		</tbody>
	</table>
</div>
EOT;



$scroll = true;
if (!($detect->isMobile() && !$detect->isTablet())) {
	$scroll = false;
}
	
	
$show_view=<<<EOT

<style type="text/css">
#db_table {
	width: 100% !Important;
	margin: 5px 0 0 0 !Important;
}

/* 表頭上下兩段 */
#db_table th.th-stack {
	padding: 0 !important;
}
#db_table th.th-stack > div {
	padding: 6px 10px;
}
#db_table th.th-stack > div + div {
	border-top: 1px solid #000;
}

/* 內容上下兩段 */
#db_table td.td-stack {
	padding: 0 !important;
	vertical-align: middle;
}
#db_table .cell-stack > div {
	min-height: 32px;
	padding: 4px 8px;
}
#db_table .cell-stack > div + div {
	border-top: 1px solid #dee2e6;
}

</style>

$list_view

<script type="text/javascript" charset="utf-8">
	var oTable;
	$(document).ready(function() {
		$('#db_table').dataTable( {
			"processing": true,
			"serverSide": true,
			"responsive":  {
				details: true
			},//RWD響應式
			"scrollX": '$scroll',
			/*"scrollY": 600,*/
			"paging": true,
			"pageLength": 50,
			"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
			"pagingType": "full_numbers",  //分页样式： simple,simple_numbers,full,full_numbers
			"searching": true,  //禁用原生搜索
			"ordering": false,
			"ajaxSource": "/smarty/templates/$site_db/$templates/sub_modal/project/func06/overview_ms/server_overview.php?site_db=$site_db&fm=$fm",
			"language": {
						"sUrl": "$dataTable_de"
						/*"sUrl": '//cdn.datatables.net/plug-ins/1.12.1/i18n/zh-HANT.json'*/
					},
			"fixedHeader": true,
			"fixedColumns": {
        		left: 1,
    		},
			"fnRowCallback": function( nRow, aData, iDisplayIndex ) { 

				//上下兩段顯示
				var stack2 = function(top, bottom) {
					return '<div class="cell-stack">'
						+ '<div class="d-flex justify-content-center align-items-center text-center">'+top+'</div>'
						+ '<div class="d-flex justify-content-center align-items-center text-center">'+bottom+'</div>'
						+ '</div>';
				};

				var isEmpty = function(v) {
					return (v == null || v == "" || v == "0000-00-00");
				};

				//案件編號
				var case_id = "";
				if (aData[3] != null && aData[3] != "")
					case_id = aData[3];

				$('td:eq(0)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center size12 weight text-nowrap" style="height:auto;min-height:32px;">'+case_id+'</div>' );

				//區域
				var region = "";
				if (aData[2] != null && aData[2] != "")
					region = aData[2];

				$('td:eq(1)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center size12 text-nowrap" style="height:auto;min-height:32px;">'+region+'</div>' );

				//案件種類
				var case_type_url = "openfancybox_edit('/index.php?ch=case_type&auto_seq="+aData[14]+"&fm=$fm',400,250,'');";
				var show_case_type_btn = '<button type="button" class="btn btn-light btn-sm me-2" onclick="'+case_type_url+'" title="案件種類"><i class="bi bi-chevron-down"></i></button>';

				var case_type = "";
				if (aData[18] != null && aData[18] != "")
					case_type = '<span class="size12 text-nowrap">'+aData[18]+'</span>';

				$('td:eq(2)', nRow).html( '<div class="text-start text-nowrap" style="height:auto;min-height:32px;">'+show_case_type_btn+case_type+'</div>' );

				//建商單位 / 營造商單位
				var builder_name = "";
				if (aData[15] != null && aData[15] != "")
					builder_name = '<span class="size12 me-1 text-nowrap">'+aData[15]+'</span>';

				var builder_id = "";
				if (aData[5] != null && aData[5] != "")
					builder_id = '<span class="size09">'+aData[5]+'</span>';

				var contractor_name = "";
				if (aData[16] != null && aData[16] != "")
					contractor_name = '<span class="size12 me-1 text-nowrap">'+aData[16]+'</span>';

				var contractor_id = "";
				if (aData[6] != null && aData[6] != "")
					contractor_id = '<span class="size09">'+aData[6]+'</span>';

				$('td:eq(3)', nRow).addClass('td-stack').html( stack2(builder_name+builder_id, contractor_name+contractor_id) );

				//業主(建商) / 業主(營造商)
				if ( aData[19] == "Y" ) {
					var mcheck1 = "xajax_owner_builder("+aData[14]+",'N','$memberID');";
					var img_check1 = '<a href="javascript:void(0);" onclick="'+mcheck1+'"><i class="bi bi-check-circle size16 green weight"></i></a>';
				} else {
					var mcheck1 = "xajax_owner_builder("+aData[14]+",'Y','$memberID');";
					var img_check1 = '<a href="javascript:void(0);" onclick="'+mcheck1+'"><i class="bi bi-circle size16 gray"></i></a>';
				}

				if ( aData[20] == "Y" ) {
					var mcheck2 = "xajax_owner_contractor("+aData[14]+",'N','$memberID');";
					var img_check2 = '<a href="javascript:void(0);" onclick="'+mcheck2+'"><i class="bi bi-check-circle size16 green weight"></i></a>';
				} else {
					var mcheck2 = "xajax_owner_contractor("+aData[14]+",'Y','$memberID');";
					var img_check2 = '<a href="javascript:void(0);" onclick="'+mcheck2+'"><i class="bi bi-circle size16 gray"></i></a>';
				}

				$('td:eq(4)', nRow).addClass('td-stack').html( stack2(img_check1, img_check2) );

				//工程名稱
				var construction_id = "";
				if (aData[4] != null && aData[4] != "")
					construction_id = aData[4];

				$('td:eq(5)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+construction_id+'</div>' );

				//承攬模式
				var ContractingModel = "";
				if (aData[24] != null && aData[24] != "")
					ContractingModel = aData[24];

				$('td:eq(6)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center size12 text-nowrap" style="height:auto;min-height:32px;">'+ContractingModel+'</div>' );

				//所屬公司
				var company_name = "";
				if (aData[27] != null && aData[27] != "")
					company_name = aData[27];
				else if (aData[26] != null && aData[26] != "")
					company_name = aData[26];
				if (company_name != "")
					company_name = '<span class="size12 weight me-1 text-nowrap">'+company_name+'</span>';

				var company_id = "";
				if (aData[25] != null && aData[25] != "")
					company_id = '<span class="size09">'+aData[25]+'</span>';

				$('td:eq(7)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+company_name+company_id+'</div>' );

				//經辦人員
				var Handler_name = "";
				if (aData[29] != null && aData[29] != "")
					Handler_name = '<span class="size12 weight me-1 text-nowrap">'+aData[29]+'</span>';

				var Handler = "";
				if (aData[28] != null && aData[28] != "")
					Handler = '<span class="size09">'+aData[28]+'</span>';

				$('td:eq(8)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+Handler_name+Handler+'</div>' );

				//合約承攬建物棟數
				var buildings_contract = isEmpty(aData[30]) ? "" : aData[30];
				var buildings_contract2 = isEmpty(aData[31]) ? "" : aData[31];
				var std_layer_floor = isEmpty(aData[32]) ? "" : aData[32];
				var roof_protrusion_floor = isEmpty(aData[33]) ? "" : aData[33];

				var show_buildings = "";
				if (buildings_contract != "" || buildings_contract2 != "" || std_layer_floor != "" || roof_protrusion_floor != "")
					show_buildings = buildings_contract2 + ' / ' + std_layer_floor + '；' + roof_protrusion_floor + '、' + buildings_contract;

				$('td:eq(9)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center size12 text-nowrap" style="height:auto;min-height:32px;">'+show_buildings+'</div>' );

				//志特編號 / 合約號碼
				var geto_no = isEmpty(aData[34]) ? "" : '<span class="size12 text-nowrap">'+aData[34]+'</span>';
				var ERP_no = isEmpty(aData[35]) ? "" : '<span class="size12 text-nowrap">'+aData[35]+'</span>';

				$('td:eq(10)', nRow).addClass('td-stack').html( stack2(geto_no, ERP_no) );

				//工地地址
				var zipcode = '';
				if (aData[10] != null && aData[10] != '')
					zipcode = aData[10];
					
				var county = '';
				if (aData[11] != null && aData[11] != '')
					county = aData[11];

				var town = '';
				if (aData[12] != null && aData[12] != '')
					town = aData[12];

				var address = '';
				if (aData[13] != null && aData[13] != '')
					address = aData[13];

				$('td:eq(11)', nRow).html( '<div class="d-flex justify-content-start align-items-center" style="height:auto;min-height:32px;"><div class="text-nowrap"><span class="size12 weight text-nowrap vbottom">'+county+town+'</span></div><div>'+address+'</div></div>' );

				//工地google定位網址
				var google_location_url = "openfancybox_edit('/index.php?ch=google_location&auto_seq="+aData[14]+"&fm=$fm',800,250,'');";
				var show_google_location_btn = '<button type="button" class="btn btn-light btn-sm me-2" onclick="'+google_location_url+'" title="工地google定位網址"><i class="bi bi-geo-alt"></i></button>';

				var google_location = "";
				if (aData[21] != null && aData[21] != "")
					google_location = '<span class="size08"><a href="'+aData[21]+'" target="_blank"><span class="blue02">'+aData[21]+'</span></a></span>';

				$('td:eq(12)', nRow).html( '<div class="text-start text-nowrap" style="height:auto;min-height:32px;">'+show_google_location_btn+google_location+'</div>' );

				//預計進場日 / 預計完工日
				var estimated_arrival_date = isEmpty(aData[36]) ? "" : '<span class="size12 text-nowrap">'+aData[36]+'</span>';
				var completion_date = isEmpty(aData[37]) ? "" : '<span class="size12 text-nowrap">'+aData[37]+'</span>';

				$('td:eq(13)', nRow).addClass('td-stack').html( stack2(estimated_arrival_date, completion_date) );

				//實際進場日 / 實際完工日 (由 overview_building 取最早進場、最晚完工)
				var earliest_entry_date = "";
				var latest_completion_date = "";
				if (aData[14] != null && aData[14] != "") {
					earliest_entry_date = '<span class="size12 text-nowrap" id="earliest_entry_date'+aData[14]+'"></span>';
					latest_completion_date = '<span class="size12 text-nowrap" id="latest_completion_date'+aData[14]+'"></span>';
				}

				$('td:eq(14)', nRow).addClass('td-stack').html( stack2(earliest_entry_date, latest_completion_date) );
				xajax_returnValue(aData[14]);

				//工地狀態
				var site_status_url = "openfancybox_edit('/index.php?ch=site_status&auto_seq="+aData[14]+"&fm=$fm',400,250,'');";
				var show_site_status_btn = '<button type="button" class="btn btn-light btn-sm me-2" onclick="'+site_status_url+'" title="工地狀態"><i class="bi bi-chevron-down"></i></button>';

				var site_status = "";
				if (aData[22] != null && aData[22] != "")
					site_status = '<span class="size12 text-nowrap">'+aData[22]+'</span>';

				$('td:eq(15)', nRow).html( '<div class="text-start text-nowrap" style="height:auto;min-height:32px;">'+show_site_status_btn+site_status+'</div>' );

				//工程概況
				var url1 = "openfancybox_edit('/index.php?ch=edit&auto_seq="+aData[14]+"&fm=$fm',1800,'96%','');";

				var show_btn = '<button type="button" class="btn btn-light btn-sm text-nowrap" onclick="'+url1+'" title="工程概況"><i class="bi bi-building"></i>&nbsp;工程概況</button>';

				$('td:eq(16)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+show_btn+'</div>' );

				//最後修改 
				var last_modify = (aData[9] != null && aData[9] != "") 
				? '<div class="text-nowrap">' + moment(aData[9]).format('YYYY-MM-DD HH:mm') + '</div>' 
				: "";

				//編輯人員
				var member_name = (aData[17] != null && aData[17] != "") 
					? '<span class="text-nowrap">' + aData[17] + '</span>' 
					: "";

				//更新次數
				var update_count_val = (aData[23] != null && aData[23] != "") ? aData[23] : "0";

				if ((last_modify == null || last_modify == "") && (member_name == null || member_name == "")) {
				
					$('td:eq(17)', nRow).html('<div style="height:auto;min-height:32px;"></div>');

				} else {

					//累計修改次數
					var update_count_html = ' <span class="badge rounded-1 bg-secondary opacity-75" ' +
											'style="cursor:help; font-size:10px; padding: 2px 4px; margin-left: 4px;" ' +
											'data-bs-toggle="tooltip" ' + 
											'data-bs-html="true" ' +
											'title="累計修改次數：' + update_count_val + '次">' + 
											update_count_val + '</span>';

					$('td:eq(17)', nRow).html( 
						'<div class="text-center" style="height:auto;min-height:32px;">' + 
							last_modify + 
							'<div>' + member_name + update_count_html + '</div>' + 
						'</div>' 
					);

				}
				return nRow;
			
			}
			
		});
	
		/* Init the table */
		oTable = $('#db_table').dataTable();
		
	} );

var myDel = function(auto_seq) {

	Swal.fire({
	title: "您確定要刪除此筆資料嗎?",
	text: "此項作業會刪除所有與此筆案件記錄有關的資料",
	icon: "question",
	showCancelButton: true,
	confirmButtonColor: "#3085d6",
	cancelButtonColor: "#d33",
	cancelButtonText: "取消",
	confirmButtonText: "刪除"
	}).then((result) => {
		if (result.isConfirmed) {
			xajax_DeleteRow(auto_seq);
		}
	});

};

var myDraw = function(){
	var oTable;
	oTable = $('#db_table').dataTable();
	oTable.fnDraw(false);
}

	
</script>

EOT;

} else {

	$sid = "mbwarning";
	$show_view = mywarning("很抱歉! 目前此功能只開放給本站特定會員，或是您目前的權限無法存取此頁面。");

}

?>