<?php

session_start();

$memberID = $_SESSION['memberID'];
$powerkey = $_SESSION['powerkey'];


//載入公用函數
@include_once '/website/include/pub_function.php';

@include_once("/website/class/".$site_db."_info_class.php");


$m_location		= "/website/smarty/templates/".$site_db."/".$templates;
$m_pub_modal	= "/website/smarty/templates/".$site_db."/pub_modal";

$sid = "";
if (isset($_GET['sid']))
	$sid = $_GET['sid'];


//程式分類
$ch = empty($_GET['ch']) ? 'default' : $_GET['ch'];
switch($ch) {
	case 'add':
		$title = "新增資料";
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/overview_add.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'edit':
		$title = "工程概況";
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/overview_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'mview':
	case 'view':
		$title = "資料瀏覽";
		if (empty($sid))
			$sid = "mbpjitem";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/overview_view.php";
		include $modal;
		//$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
		/*
	case 'overview_day_summary':
		if (empty($sid))
			$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/overview_day_summary.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		break;
	case 'excel':
		$title = "匯出Excel".$mt;
		if (empty($sid))
			$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/overview_report_excel.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		break;
	case 'ch_team_member':
		$title = "團員選單";
		if (empty($sid))
			$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/ch_team_member.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'construction_add':
		$title = "新增工地";
		if (empty($sid))
			$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/overview_construction_add.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'ch_construction':
		$title = "工地選單";
		if (empty($sid))
			$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/ch_construction.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'ch_building':
		$title = "棟別選單";
		if (empty($sid))
			$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/ch_building.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'ch_household':
		$title = "戶別選單";
		if (empty($sid))
			$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/ch_household.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'ch_floor':
		$title = "樓別選單";
		if (empty($sid))
			$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/ch_floor.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'attendance':
		$title = "出勤狀況";
		if (empty($sid))
			$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/attendance_status_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'attendance_end':
		$title = "收工狀況";
		if (empty($sid))
			$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/attendance_end_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'leave':
		$title = "請休假";
		if (empty($sid))
			$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/leave_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'work_overtime':
		$title = "加班";
		if (empty($sid))
			$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/work_overtime_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'knock_off':
		$title = "收工作業";
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/overview_knock_off.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'knock_off_edit':
		$title = "收工修改";
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/overview_knock_off_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'attendance_manpower':
		$title = "修改人力";
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/attendance_manpower_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'attendance_day':
		$title = "修改工時";
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/attendance_day_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'construction_status_edit':
		$title = "出工內容";
		if (empty($sid))
			$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/construction_status_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'manpower':
		$title = "修改總人力";
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/manpower_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
		*/
	case 'case_type':
		$title = "案件種類";
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/case_type_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'google_location':
		$title = "工地google定位網址";
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/google_location_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'site_status':
		$title = "工地狀態";
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/site_status_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'overview_sub_edit':
		$title = "編輯工程概況";
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/overview_sub_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'case_contacts_edit':
		$title = "編輯聯絡資料";
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/case_contacts_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'architect_office_edit':
		$title = "編輯建築師事務所";
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/architect_office_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'ch_employee':
	case 'ch_employee2':
		$title = "員工名單";
		if (empty($sid))
			$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/ch_employee.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'ch_subcontractordata':
		$title = "加入下包資料";
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/ch_subcontractordata.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	default:
		if (empty($sid))
			$sid = "mbpjitem";
		$modal = $m_location."/sub_modal/project/func06/overview_ms/overview.php";
		include $modal;
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
};

?>