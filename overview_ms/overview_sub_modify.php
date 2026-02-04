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

$xajax->registerFunction("processform");
function processform($aFormValues){

	$objResponse = new xajaxResponse();
	
	$memberID				= trim($aFormValues['memberID']);
	$auto_seq				= trim($aFormValues['auto_seq']);
	$engineering_overview	= trim($aFormValues['engineering_overview']);
	$builder_id				= trim($aFormValues['builder_id']);
	$workers				= trim($aFormValues['workers']);
	
	
	if (trim($aFormValues['engineering_overview']) == "")	{
		$objResponse->script("jAlert('警示', '請輸入工程概況', 'red', '', 2000);");
		return $objResponse;
		exit;
	}

	/*
	if (trim($aFormValues['member_no']) != "")	{
		//檢查會員帳號是否正確
		$member_row = getkeyvalue2("memberinfo","member","member_no = '$member_no'","count(member_no) as m_count");
		$m_count = $member_row['m_count'];
		if ($m_count <= 0)	{
			$objResponse->script("jAlert('警示', '您輸入的會員帳號不存在', 'red', '', 2000);");
			return $objResponse;
			exit;
		}
	}
	*/
	
	SaveValue($aFormValues);
	
	$objResponse->script("setSave();");
	$objResponse->script("parent.overview_sub_myDraw();");
	$objResponse->script("parent.builder_info_myDraw();");
	$objResponse->script("parent.$.fancybox.close();");
		
	
	return $objResponse;
}


$xajax->registerFunction("SaveValue");
function SaveValue($aFormValues){

	$objResponse = new xajaxResponse();
	
		//進行存檔動作
		$site_db				= trim($aFormValues['site_db']);
		$memberID				= trim($aFormValues['memberID']);
		$case_id				= trim($aFormValues['case_id']);
		$auto_seq				= trim($aFormValues['auto_seq']);
		$engineering_overview 	= htmlspecialchars(trim($aFormValues['engineering_overview']), ENT_QUOTES, 'UTF-8');
		$builder_id 			= trim($aFormValues['builder_id']);
		$workers 				= trim($aFormValues['workers']);
		$aluminum_formwork_host = htmlspecialchars(trim($aFormValues['aluminum_formwork_host']), ENT_QUOTES, 'UTF-8');
		$rebar_host 			= htmlspecialchars(trim($aFormValues['rebar_host']), ENT_QUOTES, 'UTF-8');
		$hydropower_host 		= htmlspecialchars(trim($aFormValues['hydropower_host']), ENT_QUOTES, 'UTF-8');
		$site_manager 			= htmlspecialchars(trim($aFormValues['site_manager']), ENT_QUOTES, 'UTF-8');
		$superintendent 		= htmlspecialchars(trim($aFormValues['superintendent']), ENT_QUOTES, 'UTF-8');
		$scaffold 				= trim($aFormValues['scaffold']);
		$rebar 					= trim($aFormValues['rebar']);
		$hydropower 			= trim($aFormValues['hydropower']);
		$layout 				= trim($aFormValues['layout']);
		$concrete 				= trim($aFormValues['concrete']);
		$concrete_plant 		= trim($aFormValues['concrete_plant']);
		$masonry 				= trim($aFormValues['masonry']);
		$painting 				= trim($aFormValues['painting']);
		$drywall 				= trim($aFormValues['drywall']);
		$responsible 			= trim($aFormValues['responsible']);
		$maincontractor_pricing_staff = trim($aFormValues['maincontractor_pricing_staff']);
		$subcontractor_pricing_staff = trim($aFormValues['subcontractor_pricing_staff']);

		//存入實體資料庫中
		$mDB = "";
		$mDB = new MywebDB();

		$Qry="UPDATE overview_sub set
				 engineering_overview = '$engineering_overview'
				,builder_id			= '$builder_id'
				,workers			= '$workers'
				,aluminum_formwork_host = '$aluminum_formwork_host'
				,rebar_host			= '$rebar_host'
				,hydropower_host	= '$hydropower_host'
				,site_manager		= '$site_manager'
				,superintendent		= '$superintendent'
				,scaffold			= '$scaffold'
				,rebar				= '$rebar'
				,hydropower			= '$hydropower'
				,layout				= '$layout'
				,concrete			= '$concrete'
				,concrete_plant		= '$concrete_plant'
				,masonry			= '$masonry'
				,painting			= '$painting'
				,drywall			= '$drywall'
				,responsible		= '$responsible'
				,maincontractor_pricing_staff = '$maincontractor_pricing_staff'
				,subcontractor_pricing_staff = '$subcontractor_pricing_staff'
				,makeby				= '$memberID'
				,last_modify		= now()
				where auto_seq = '$auto_seq'";
				
		$mDB->query($Qry);

		$Qry="UPDATE CaseManagement set
			last_modify8 = now()
			,makeby8	= '$memberID'
			,update_count8 = update_count8 + 1
			WHERE case_id = '$case_id'";
		$mDB->query($Qry);


        $mDB->remove();

		
	return $objResponse;
}

$xajax->processRequest();


$case_id = $_GET['case_id'];



$mDB = "";
$mDB = new MywebDB();


if (!isset($_GET['auto_seq'])) {
	//若無$_GET['auto_seq']，則自動新增一筆
	$Qry="INSERT INTO overview_sub (case_id,makeby,last_modify) VALUES ('$case_id','$memberID',now())";
	$mDB->query($Qry);
	//再取出 auto_seq
	$Qry="SELECT auto_seq FROM overview_sub WHERE case_id = '$case_id' ORDER BY auto_seq DESC LIMIT 0,1";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		//已找到符合資料
		$row=$mDB->fetchRow(2);
		$auto_seq = $row['auto_seq'];
	}
	
	//更新主檔
	$Qry="UPDATE CaseManagement set
		last_modify8 = now()
		,makeby8	= '$memberID'
		,update_count8 = update_count8 + 1
		WHERE case_id = '$case_id'";
	$mDB->query($Qry);


} else {
	$auto_seq = $_GET['auto_seq'];
}




$fm = $_GET['fm'];

$mess_title = $title;


$Qry="SELECT a.*,b.employee_name as responsible_name,c.employee_name as maincontractor_name,d.employee_name as subcontractor_name FROM overview_sub a
LEFT JOIN employee b ON b.employee_id = a.responsible
LEFT JOIN employee c ON c.employee_id = a.maincontractor_pricing_staff
LEFT JOIN employee d ON d.employee_id = a.subcontractor_pricing_staff
WHERE a.auto_seq = '$auto_seq'";
$mDB->query($Qry);
$total = $mDB->rowCount();
if ($total > 0) {
    //已找到符合資料
	$row=$mDB->fetchRow(2);
	$engineering_overview = $row['engineering_overview'];
	$builder_id = $row['builder_id'];
	$workers = $row['workers'];
	$aluminum_formwork_host = $row['aluminum_formwork_host'];
	$rebar_host = $row['rebar_host'];
	$hydropower_host = $row['hydropower_host'];
	$site_manager = $row['site_manager'];
	$superintendent = $row['superintendent'];
	$scaffold = $row['scaffold'];
	$rebar = $row['rebar'];
	$hydropower = $row['hydropower'];
	$layout = $row['layout'];
	$concrete = $row['concrete'];
	$concrete_plant = $row['concrete_plant'];
	$masonry = $row['masonry'];
	$painting = $row['painting'];
	$drywall = $row['drywall'];
	$responsible = $row['responsible'];
	$responsible_name = $row['responsible_name'];
	$maincontractor_pricing_staff = $row['maincontractor_pricing_staff'];
	$maincontractor_name = $row['maincontractor_name'];
	$subcontractor_pricing_staff = $row['subcontractor_pricing_staff'];
	$subcontractor_name = $row['subcontractor_name'];

}

/*
$Qry="SELECT company_id,company_name FROM company ORDER BY company_id";
$mDB->query($Qry);
$select_company = "";
$select_company .= "<option></option>";
if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_company_id = $row['company_id'];
		$ch_company_name = $row['company_name'];
		$select_company .= "<option value=\"$ch_company_id\" ".mySelect($ch_company_id,$company_id).">$ch_company_name $ch_company_id</option>";
	}
}
*/

//載入下包商-代工單位
$Qry="select subcontractor_id,subcontractor_name from subcontractor order by auto_seq";
$mDB->query($Qry);
$select_builder = "";
$select_builder .= "<option></option>";
if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_builder_id = $row['subcontractor_id'];
		$ch_builder_name = $row['subcontractor_name'];
		$select_builder .= "<option value=\"$ch_builder_id\" ".mySelect($ch_builder_id,$builder_id).">$ch_builder_id $ch_builder_name</option>";
	}
}

//載入下包商-鷹架
$Qry="select subcontractor_id,subcontractor_name from subcontractor order by auto_seq";
$mDB->query($Qry);
$select_scaffold = "";
$select_scaffold .= "<option></option>";
if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_scaffold = $row['subcontractor_id'];
		$ch_scaffold_name = $row['subcontractor_name'];
		$select_scaffold .= "<option value=\"$ch_scaffold\" ".mySelect($ch_scaffold,$scaffold).">$ch_scaffold $ch_scaffold_name</option>";
	}
}

//載入下包商-鋼筋
$Qry="select subcontractor_id,subcontractor_name from subcontractor order by auto_seq";
$mDB->query($Qry);
$select_rebar = "";
$select_rebar .= "<option></option>";
if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_rebar = $row['subcontractor_id'];
		$ch_rebar_name = $row['subcontractor_name'];
		$select_rebar .= "<option value=\"$ch_rebar\" ".mySelect($ch_rebar,$rebar).">$ch_rebar $ch_rebar_name</option>";
	}
}

//載入下包商-水電
$Qry="select subcontractor_id,subcontractor_name from subcontractor order by auto_seq";
$mDB->query($Qry);
$select_hydropower = "";
$select_hydropower .= "<option></option>";
if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_hydropower = $row['subcontractor_id'];
		$ch_hydropower_name = $row['subcontractor_name'];
		$select_hydropower .= "<option value=\"$ch_hydropower\" ".mySelect($ch_hydropower,$hydropower).">$ch_hydropower $ch_hydropower_name</option>";
	}
}

//載入下包商-放樣
$Qry="select subcontractor_id,subcontractor_name from subcontractor order by auto_seq";
$mDB->query($Qry);
$select_layout = "";
$select_layout .= "<option></option>";
if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_layout = $row['subcontractor_id'];
		$ch_layout_name = $row['subcontractor_name'];
		$select_layout .= "<option value=\"$ch_layout\" ".mySelect($ch_layout,$layout).">$ch_layout $ch_layout_name</option>";
	}
}

//載入下包商-壓送
$Qry="select subcontractor_id,subcontractor_name from subcontractor order by auto_seq";
$mDB->query($Qry);
$select_concrete = "";
$select_concrete .= "<option></option>";
if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_concrete = $row['subcontractor_id'];
		$ch_concrete_name = $row['subcontractor_name'];
		$select_concrete .= "<option value=\"$ch_concrete\" ".mySelect($ch_concrete,$concrete).">$ch_concrete $ch_concrete_name</option>";
	}
}

//載入下包商-混凝土廠
$Qry="select subcontractor_id,subcontractor_name from subcontractor order by auto_seq";
$mDB->query($Qry);
$select_concrete_plant = "";
$select_concrete_plant .= "<option></option>";
if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_concrete_plant = $row['subcontractor_id'];
		$ch_concrete_plant_name = $row['subcontractor_name'];
		$select_concrete_plant .= "<option value=\"$ch_concrete_plant\" ".mySelect($ch_concrete_plant,$concrete_plant).">$ch_concrete_plant $ch_concrete_plant_name</option>";
	}
}

//載入下包商-泥作
$Qry="select subcontractor_id,subcontractor_name from subcontractor order by auto_seq";
$mDB->query($Qry);
$select_masonry = "";
$select_masonry .= "<option></option>";
if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_masonry = $row['subcontractor_id'];
		$ch_masonry_name = $row['subcontractor_name'];
		$select_masonry .= "<option value=\"$ch_masonry\" ".mySelect($ch_masonry,$masonry).">$ch_masonry $ch_masonry_name</option>";
	}
}

//載入下包商-油漆
$Qry="select subcontractor_id,subcontractor_name from subcontractor order by auto_seq";
$mDB->query($Qry);
$select_painting = "";
$select_painting .= "<option></option>";
if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_painting = $row['subcontractor_id'];
		$ch_painting_name = $row['subcontractor_name'];
		$select_painting .= "<option value=\"$ch_painting\" ".mySelect($ch_painting,$painting).">$ch_painting $ch_painting_name</option>";
	}
}

//載入下包商-輕隔間
$Qry="select subcontractor_id,subcontractor_name from subcontractor order by auto_seq";
$mDB->query($Qry);
$select_drywall = "";
$select_drywall .= "<option></option>";
if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_drywall = $row['subcontractor_id'];
		$ch_drywall_name = $row['subcontractor_name'];
		$select_drywall .= "<option value=\"$ch_drywall\" ".mySelect($ch_drywall,$drywall).">$ch_drywall $ch_drywall_name</option>";
	}
}


$mDB->remove();


$show_savebtn=<<<EOT
<div class="btn-group vbottom" role="group" style="margin-top:5px;">
	<button id="save" class="btn btn-primary" type="button" onclick="CheckValue(this.form);" style="padding: 5px 15px;"><i class="bi bi-check-circle"></i>&nbsp;存檔</button>
	<button id="cancel" class="btn btn-secondary display_none" type="button" onclick="setCancel();" style="padding: 5px 15px;"><i class="bi bi-x-circle"></i>&nbsp;取消</button>
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
	height: 100%;
}

#info_container {
	width: 100% !Important;
	max-width: 1240px; !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:150px;display: none;font-size:18px;color:#000;text-align:right;font-weight:700;padding:15px 10px 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}
.field_div2 {width:100%;max-width:400px;display: none;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 0 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}

.code_class {
	width:150px;
	text-align:right;
	padding:0 10px 0 0;
}

.maxwidth {
    width: 100%;
    max-width: 300px;
}

</style>

EOT;

} else {
	$isMobile = 1;

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
.field_div2 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 10px 0 0;vertical-align: top;}

.code_class {
	width:auto;
	text-align:left;
	padding:0 10px 0 0;
}

.maxwidth {
    width: 100%;
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
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div2">
									<div class="inline code_class">案件編號:</div>
									<div class="inline" style="padding:8px 0;font-size:18px;color:blue;text-align:left;font-weight:700;">$case_id</div>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div1">工程概況:</div> 
								<div class="inline mt-2" style="width:100%;max-width:900px;">
									<input type="text" class="inputtext" id="engineering_overview" name="engineering_overview" size="50" maxlength="120" style="width:100%;max-width:872px;" value="$engineering_overview" onchange="setEdit();"/>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">代工單位:</div> 
								<div class="field_div2">
									<select id="builder_id" name="builder_id" placeholder="請選擇代工單位" style="width:100%;max-width:350px;">
										$select_builder
									</select>
								</div> 
							</div> 
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">配置工班人數:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext" id="workers" name="workers" size="20" style="width:100%;max-width:80px;" value="$workers" onchange="setEdit();"/>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">鋁模主辦:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="aluminum_formwork_host" name="aluminum_formwork_host" size="50"  maxlength="50" value="$aluminum_formwork_host" onchange="setEdit();"/>
								</div> 
							</div> 
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">鋼筋主辦:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="rebar_host" name="rebar_host" size="50"  maxlength="50" value="$rebar_host" onchange="setEdit();"/>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">水電主辦:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="hydropower_host" name="hydropower_host" size="50"  maxlength="50" value="$hydropower_host" onchange="setEdit();"/>
								</div> 
							</div> 
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">工地負責人:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="site_manager" name="site_manager" size="50"  maxlength="50" value="$site_manager" onchange="setEdit();"/>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">工地經理:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="superintendent" name="superintendent" size="50"  maxlength="50" value="$superintendent" onchange="setEdit();"/>
								</div> 
							</div> 
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1"></div> 
								<div class="field_div2">
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-4 col-sm-12 col-md-12">
								<div class="field_div1">鷹架:</div> 
								<div class="inline mt-2">
									<select id="scaffold" name="scaffold" placeholder="請選擇" style="width:100%;max-width:220px;">
										$select_scaffold
									</select>
								</div> 
							</div> 
							<div class="col-lg-4 col-sm-12 col-md-12">
								<div class="field_div1" style="width:120px;">鋼筋:</div> 
								<div class="inline mt-2">
									<select id="rebar" name="rebar" placeholder="請選擇" style="width:100%;max-width:220px;">
										$select_rebar
									</select>
								</div> 
							</div> 
							<div class="col-lg-4 col-sm-12 col-md-12">
								<div class="field_div1" style="width:90px;">水電:</div> 
								<div class="inline mt-2">
									<select id="hydropower" name="hydropower" placeholder="請選擇" style="width:100%;max-width:220px;">
										$select_hydropower
									</select>
								</div> 
							</div> 
						</div>
						<div class="row">
							<div class="col-lg-4 col-sm-12 col-md-12">
								<div class="field_div1">放樣:</div> 
								<div class="inline mt-2">
									<select id="layout" name="layout" placeholder="請選擇" style="width:100%;max-width:220px;">
										$select_layout
									</select>
								</div> 
							</div> 
							<div class="col-lg-4 col-sm-12 col-md-12">
								<div class="field_div1" style="width:120px;">壓送:</div> 
								<div class="inline mt-2">
									<select id="concrete" name="concrete" placeholder="請選擇" style="width:100%;max-width:220px;">
										$select_concrete
									</select>
								</div> 
							</div> 
							<div class="col-lg-4 col-sm-12 col-md-12">
								<div class="field_div1" style="width:90px;">混凝土廠:</div> 
								<div class="inline mt-2">
									<select id="concrete_plant" name="concrete_plant" placeholder="請選擇" style="width:100%;max-width:220px;">
										$select_concrete_plant
									</select>
								</div> 
							</div> 
						</div>
						<div class="row">
							<div class="col-lg-4 col-sm-12 col-md-12">
								<div class="field_div1">泥作:</div> 
								<div class="inline mt-2">
									<select id="masonry" name="masonry" placeholder="請選擇" style="width:100%;max-width:220px;">
										$select_masonry
									</select>
								</div> 
							</div> 
							<div class="col-lg-4 col-sm-12 col-md-12">
								<div class="field_div1" style="width:120px;">油漆:</div> 
								<div class="inline mt-2">
									<select id="painting" name="painting" placeholder="請選擇" style="width:100%;max-width:220px;">
										$select_painting
									</select>
								</div> 
							</div> 
							<div class="col-lg-4 col-sm-12 col-md-12">
								<div class="field_div1" style="width:90px;">輕隔間:</div> 
								<div class="inline mt-2">
									<select id="drywall" name="drywall" placeholder="請選擇" style="width:100%;max-width:220px;">
										$select_drywall
									</select>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div1">負責工務:</div> 
								<div class="field_div2">
									<div class="input-group text-nowrap" style="width:100%;max-width:350px;">
										<input readonly type="text" class="form-control w-25" id="responsible" name="responsible" aria-describedby="responsible_addon" value="$responsible"/>
										<input readonly type="text" class="form-control w-50" id="responsible_name" name="responsible_name"  value="$responsible_name"/>
										<button class="btn btn-outline-secondary w-25" type="button" id="responsible_addon" onclick="openfancybox_edit('/index.php?ch=ch_employee&id=responsible&name=responsible_name&fm=$fm',800,'96%','');">選擇員工</button>
									</div>
								</div>
							</div> 
						</div> 
						<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">上包計價人員:</div> 
								<div class="field_div2">
									<div class="input-group text-nowrap" style="width:100%;max-width:350px;">
										<input readonly type="text" class="form-control w-25" id="maincontractor_pricing_staff" name="maincontractor_pricing_staff" aria-describedby="maincontractor_addon" value="$maincontractor_pricing_staff"/>
										<input readonly type="text" class="form-control w-50" id="maincontractor_name" name="maincontractor_name"  value="$maincontractor_name"/>
										<button class="btn btn-outline-secondary w-25" type="button" id="maincontractor_addon" onclick="openfancybox_edit('/index.php?ch=ch_employee2&id=maincontractor_pricing_staff&name=maincontractor_name&fm=$fm',800,'96%','');">選擇員工</button>
									</div>
								</div>
							</div> 
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">下包計價人員:</div> 
								<div class="field_div2">
									<div class="input-group text-nowrap" style="width:100%;max-width:350px;">
										<input readonly type="text" class="form-control w-25" id="subcontractor_pricing_staff" name="subcontractor_pricing_staff" aria-describedby="subcontractor_addon" value="$subcontractor_pricing_staff"/>
										<input readonly type="text" class="form-control w-50" id="subcontractor_name" name="subcontractor_name"  value="$subcontractor_name"/>
										<button class="btn btn-outline-secondary w-25" type="button" id="subcontractor_addon" onclick="openfancybox_edit('/index.php?ch=ch_employee2&id=subcontractor_pricing_staff&name=subcontractor_name&fm=$fm',800,'96%','');">選擇員工</button>
									</div>
								</div>
							</div> 
						</div>
					</div>
					<div>
						<input type="hidden" name="fm" value="$fm" />
						<input type="hidden" name="site_db" value="$site_db" />
						<input type="hidden" name="memberID" value="$memberID" />
						<input type="hidden" name="case_id" value="$case_id" />
						<input type="hidden" name="auto_seq" value="$auto_seq" />
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
	thisform.submit();
}

function SaveValue(thisform) {
	xajax_SaveValue(xajax.getFormValues('modifyForm'));
	thisform.submit();
}

function setEdit() {
	$('#close', window.document).addClass("display_none");
	$('#cancel', window.document).removeClass("display_none");
}

function setCancel() {
	$('#close', window.document).removeClass("display_none");
	$('#cancel', window.document).addClass("display_none");
	document.forms[0].reset();
}

function setSave() {
	$('#close', window.document).removeClass("display_none");
	$('#cancel', window.document).addClass("display_none");
}


$(document).ready(async function() {
	//等待其他資源載入完成，此方式適用大部份瀏覽器
	await new Promise(resolve => setTimeout(resolve, 100));
	$('#engineering_overview').focus();
});

</script>

EOT;

?>