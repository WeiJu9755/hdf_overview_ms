<?php

//error_reporting(E_ALL); 
//ini_set('display_errors', '1');

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


$xajax->registerFunction("DeleteRow");
function DeleteRow($auto_seq,$case_id,$memberID){

	$objResponse = new xajaxResponse();
	
	$mDB = "";
	$mDB = new MywebDB();

	//刪除主資料
	$Qry="delete from overview_sub where auto_seq = '$auto_seq'";
	$mDB->query($Qry);

	//更新主檔
	$Qry="UPDATE CaseManagement set
		last_modify8 = now()
		,makeby8	= '$memberID'
		,update_count8 = update_count8 + 1
		WHERE case_id = '$case_id'";
	$mDB->query($Qry);

	
	$mDB->remove();
	
    $objResponse->script("oTable = $('#overview_sub_table').dataTable();oTable.fnDraw(false)");
	$objResponse->script("autoclose('提示', '資料已刪除！', 500);");

	return $objResponse;
	
}


$xajax->registerFunction("returnValue");
function returnValue($auto_seq,$builder_id,$scaffold,$rebar,$hydropower,$layout,$concrete,$concrete_plant,$masonry,$painting,$drywall,$responsible,$maincontractor_pricing_staff,$subcontractor_pricing_staff){
	$objResponse = new xajaxResponse();

	$mDB = "";
	$mDB = new MywebDB();
	
	//代工單位
	$Qry="SELECT subcontractor_name FROM subcontractor WHERE subcontractor_id = '$builder_id'";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		$row=$mDB->fetchRow(2);
		$builder_name = $row['subcontractor_name'];
	}
	$show_builder_id = "<div class=\"size12\">".$builder_name."</div><div class=\"size08\">".$builder_id."</div>";
	$objResponse->assign("builder_id".$auto_seq,"innerHTML",$show_builder_id);	

	//鷹架
	$Qry="SELECT subcontractor_name FROM subcontractor WHERE subcontractor_id = '$scaffold'";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		$row=$mDB->fetchRow(2);
		$scaffold_name = $row['subcontractor_name'];
	}
	$show_scaffold_name = "<div class=\"size12\">".$scaffold_name."</div><div class=\"size08\">".$scaffold."</div>";
	$objResponse->assign("scaffold".$auto_seq,"innerHTML",$show_scaffold_name);	
	
	//鋼筋
	$Qry="SELECT subcontractor_name FROM subcontractor WHERE subcontractor_id = '$rebar'";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		$row=$mDB->fetchRow(2);
		$rebar_name = $row['subcontractor_name'];
	}
	$show_rebar_name = "<div class=\"size12\">".$rebar_name."</div><div class=\"size08\">".$rebar."</div>";
	$objResponse->assign("rebar".$auto_seq,"innerHTML",$show_rebar_name);	

	//水電
	$Qry="SELECT subcontractor_name FROM subcontractor WHERE subcontractor_id = '$hydropower'";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		$row=$mDB->fetchRow(2);
		$hydropower_name = $row['subcontractor_name'];
	}
	$show_hydropower_name = "<div class=\"size12\">".$hydropower_name."</div><div class=\"size08\">".$hydropower."</div>";
	$objResponse->assign("hydropower".$auto_seq,"innerHTML",$show_hydropower_name);	

	//放樣
	$Qry="SELECT subcontractor_name FROM subcontractor WHERE subcontractor_id = '$layout'";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		$row=$mDB->fetchRow(2);
		$layout_name = $row['subcontractor_name'];
	}
	$show_layout_name = "<div class=\"size12\">".$layout_name."</div><div class=\"size08\">".$layout."</div>";
	$objResponse->assign("layout".$auto_seq,"innerHTML",$show_layout_name);	

	//壓送
	$Qry="SELECT subcontractor_name FROM subcontractor WHERE subcontractor_id = '$concrete'";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		$row=$mDB->fetchRow(2);
		$concrete_name = $row['subcontractor_name'];
	}
	$show_concrete_name = "<div class=\"size12\">".$concrete_name."</div><div class=\"size08\">".$concrete."</div>";
	$objResponse->assign("concrete".$auto_seq,"innerHTML",$show_concrete_name);	

	//混凝土廠
	$Qry="SELECT subcontractor_name FROM subcontractor WHERE subcontractor_id = '$concrete_plant'";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		$row=$mDB->fetchRow(2);
		$concrete_plant_name = $row['subcontractor_name'];
	}
	$show_concrete_plant_name = "<div class=\"size12\">".$concrete_plant_name."</div><div class=\"size08\">".$concrete_plant."</div>";
	$objResponse->assign("concrete_plant".$auto_seq,"innerHTML",$show_concrete_plant_name);	

	//泥作
	$Qry="SELECT subcontractor_name FROM subcontractor WHERE subcontractor_id = '$masonry'";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		$row=$mDB->fetchRow(2);
		$masonry_name = $row['subcontractor_name'];
	}
	$show_masonry_name = "<div class=\"size12\">".$masonry_name."</div><div class=\"size08\">".$masonry."</div>";
	$objResponse->assign("masonry".$auto_seq,"innerHTML",$show_masonry_name);	

	//油漆
	$Qry="SELECT subcontractor_name FROM subcontractor WHERE subcontractor_id = '$painting'";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		$row=$mDB->fetchRow(2);
		$painting_name = $row['subcontractor_name'];
	}
	$show_painting_name = "<div class=\"size12\">".$painting_name."</div><div class=\"size08\">".$painting."</div>";
	$objResponse->assign("painting".$auto_seq,"innerHTML",$show_painting_name);	

	//輕隔間
	$Qry="SELECT subcontractor_name FROM subcontractor WHERE subcontractor_id = '$drywall'";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		$row=$mDB->fetchRow(2);
		$drywall_name = $row['subcontractor_name'];
	}
	$show_drywall_name = "<div class=\"size12\">".$drywall_name."</div><div class=\"size08\">".$drywall."</div>";
	$objResponse->assign("drywall".$auto_seq,"innerHTML",$show_drywall_name);	


	//負責工務
	$Qry="SELECT employee_name FROM employee WHERE employee_id = '$responsible'";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		$row=$mDB->fetchRow(2);
		$responsible_name = $row['employee_name'];
	}
	$show_responsible_name = "<div class=\"size12\">".$responsible_name."</div><div class=\"size08\">".$responsible."</div>";
	$objResponse->assign("responsible".$auto_seq,"innerHTML",$show_responsible_name);	

	//上包計價人員
	$Qry="SELECT employee_name FROM employee WHERE employee_id = '$maincontractor_pricing_staff'";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		$row=$mDB->fetchRow(2);
		$maincontractor_pricing_staff_name = $row['employee_name'];
	}
	$show_maincontractor_pricing_staff_name = "<div class=\"size12\">".$maincontractor_pricing_staff_name."</div><div class=\"size08\">".$maincontractor_pricing_staff."</div>";
	$objResponse->assign("maincontractor_pricing_staff".$auto_seq,"innerHTML",$show_maincontractor_pricing_staff_name);	

	//下包計價人員
	$Qry="SELECT employee_name FROM employee WHERE employee_id = '$subcontractor_pricing_staff'";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		$row=$mDB->fetchRow(2);
		$subcontractor_pricing_staff_name = $row['employee_name'];
	}
	$show_subcontractor_pricing_staff_name = "<div class=\"size12\">".$subcontractor_pricing_staff_name."</div><div class=\"size08\">".$subcontractor_pricing_staff."</div>";
	$objResponse->assign("subcontractor_pricing_staff".$auto_seq,"innerHTML",$show_subcontractor_pricing_staff_name);	



	$mDB->remove();
	

    return $objResponse;
	
}

$xajax->processRequest();


$auto_seq = $_GET['auto_seq'];
$fm = $_GET['fm'];

$mess_title = $title;

$mDB = "";
$mDB = new MywebDB();
$Qry="SELECT a.* FROM CaseManagement a
WHERE a.auto_seq = '$auto_seq'";
$mDB->query($Qry);
$total = $mDB->rowCount();
if ($total > 0) {
    //已找到符合資料
	$row=$mDB->fetchRow(2);
	$case_id = $row['case_id'];
	$region = $row['region'];
	$construction_id = $row['construction_id'];
	$builder_id = $row['builder_id'];
	$contractor_id = $row['contractor_id'];
	$county = $row['county'];
	$town = $row['town'];
	$zipcode = $row['zipcode'];
	$address = $row['address'];
	$status1 = $row['status1'];
	$status2 = $row['status2'];
	$makeby8 = $row['makeby8'];
	$last_modify8 = $row['last_modify8'];

}

$mDB->remove();



$show_savebtn=<<<EOT
<div class="btn-group vbottom" role="group" style="margin-top:5px;">
	<button id="close" class="btn btn-danger" type="button" onclick="parent.myDraw();parent.$.fancybox.close();" style="padding: 5px 15px;"><i class="bi bi-power"></i>&nbsp;關閉</button>
</div>
EOT;


//取得使用者員工身份
$member_picture = getmemberpict50($makeby8);

$member_row = getkeyvalue2("memberinfo","member","member_no = '$makeby8'","member_name");
$member_name = $member_row['member_name'];

$employee_row = getkeyvalue2($site_db."_info","employee","member_no = '$makeby8'","count(*) as manager_count,employee_name,employee_type");
$manager_count =$employee_row['manager_count'];
if ($manager_count > 0) {
	$employee_name = $employee_row['employee_name'];
	$employee_type = $employee_row['employee_type'];
} else {
	$employee_name = $member_name;
	$employee_type = "未在員工名單";
}

$member_logo=<<<EOT
<div class="float-end text-nowrap mt-3 size14 weight">
	<div class="inline mytable bg-white rounded">
		<div class="myrow">
			<div class="mycell text-center text-nowrap">
				<div class="inline me-1">By：</div>
				<img src="$member_picture" height="32" class="inline rounded">
			</div>
			<div class="mycell text-start ps-1 w-auto">
				<div class="size08 blue02 weight text-nowrap">$employee_name</div>
				<div class="size06 weight text-nowrap">$employee_type</div>
			</div>
		</div>
	</div>
</div>
EOT;


if (!($detect->isMobile() && !$detect->isTablet())) {
	$isMobile = 0;
	

$show_fellow_btn=<<<EOT
<div class="btn-group ms-5" role="group">
	<button type="button" class="btn btn-primary mb-1 px-4" onclick="openfancybox_edit('/index.php?ch=ch_subcontractordata&case_id=$case_id&fm=$fm',800,'96%','');"><i class="bi bi-plus-circle"></i>&nbsp;加入下包資料</button>
	<button type="button" class="btn btn-danger mb-1 px-4" onclick="openfancybox_edit('/index.php?ch=overview_sub_edit&case_id=$case_id&fm=$fm',1200,'96%','');"><i class="bi bi-plus-circle"></i>&nbsp;新增工程概況</button>
	<button type="button" class="btn btn-success text-nowrap mb-1 px-4" onclick="overview_sub_myDraw();"><i class="bi bi-arrow-repeat"></i>&nbsp;重整</button>
</div>
EOT; 

/*
$show_fellow_btn2=<<<EOT
<div class="btn-group" role="group">
	<button type="button" class="btn btn-danger px-4" onclick="openfancybox_edit('/index.php?ch=construction_add&dispatch_id=$dispatch_id&fm=$fm',600,550,'');"><i class="bi bi-plus-circle"></i>&nbsp;新增工地</button>
	<button type="button" class="btn btn-success text-nowrap px-4" onclick="dispatch_construction_myDraw();"><i class="bi bi-arrow-repeat"></i>&nbsp;重整</button>
</div>
EOT; 
*/


$style_css=<<<EOT
<style>

.card_full {
    width: 100%;
	height: 100vh;
}

#full {
    width: 100%;
	height: 100%;
}

#info_container {
	width: 100% !Important;
	max-width: 1800px; !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:150px;font-size:18px;color:#000;text-align:right;font-weight:700;padding:15px 10px 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}
.field_div1a {width:150px;font-size:18px;color:#000;text-align:right;font-weight:700;padding:15px 10px 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}
.field_div2 {width:100%;max-width:240px;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 0 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}
.field_div2a {width:100%;max-width:280px;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 0 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}
.field_div3 {width:100%;max-width:550px;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 0 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}

.code_class {
	width:150px;
	text-align:right;
	padding:0 10px 0 0;
}

.maxwidth {
    width: 100%;
    max-width: 220px;
}

.maxwidth2 {
    width: 100%;
    max-width: 500px;
}

.maxwidth3 {
    width: 100%;
    max-width: 220px;
}

</style>

EOT;

} else {
	$isMobile = 1;


$show_fellow_btn=<<<EOT
<div class="btn-group" role="group">
	<button type="button" class="btn btn-success text-nowrap mb-1 px-4" onclick="dispatch_member_myDraw();"><i class="bi bi-arrow-repeat"></i></button>
</div>
$button_dropdown
EOT; 

/*
$show_fellow_btn2=<<<EOT
<div class="btn-group" role="group">
	<button type="button" class="btn btn-danger px-4" onclick="openfancybox_edit('/index.php?ch=construction_add&dispatch_id=$dispatch_id&fm=$fm','96%','96%','');"><i class="bi bi-plus-circle"></i>&nbsp;新增工地</button>
	<button type="button" class="btn btn-success text-nowrap px-4" onclick="dispatch_construction_myDraw();"><i class="bi bi-arrow-repeat"></i></button>
</div>
EOT; 
*/

$style_css=<<<EOT
<style>

.card_full {
    width: 100%;
	height: 100vh;
}

#full {
    width: 100%;
	height: 100%;
}

#info_container {
	width: 100% !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:15px 10px 0 0;vertical-align: top;}
.field_div1a {width:auto;font-size:18px;color:#000;text-align:left;font-weight:700;padding:15px 10px 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}
.field_div2 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 10px 0 0;vertical-align: top;}
.field_div2a {width:auto;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 0 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}
.field_div3 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 10px 0 0;vertical-align: top;}

.code_class {
	width:auto;
	text-align:left;
	padding:0 10px 0 0;
}

.maxwidth {
    width: 100%;
}

.maxwidth2 {
    width: 100%;
}

.maxwidth3 {
    width: 100%;
    max-width: 220px;
}


</style>
EOT;

}



$m_location		= "/website/smarty/templates/".$site_db."/".$templates;
include $m_location."/sub_modal/project/func06/overview_ms/overview_sub.php";



$now = date('Y-m-d  H:i');

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
	<div id="full" class="card-body p-1" data-overlayscrollbars-initialize>
		<div id="info_container">
			<div class="w-100 mb-5">
				<div class="field_container3">
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-3 col-sm-12 col-md-12">
								<div class="field_div1a">案件編號:</div> 
								<div class="field_div2a">
									<div class="inline weight blue02 pt-2 me-2">$case_id</div>
								</div> 
							</div> 
							<div class="col-lg-9 col-sm-12 col-md-12">
								<div class="field_div1a">工程名稱:</div> 
								<div class="field_div2a blue02 pt-3">
									$construction_id
								</div> 
								$member_logo
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-7 col-sm-12 col-md-12">
								$show_fellow_btn
							</div> 
						</div>
					</div>
					$show_overview_sub
				</div>
			</div>
		</div>
	</div>
</div>
EOT;

?>