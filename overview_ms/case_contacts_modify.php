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

	if (trim($aFormValues['auto_seq']) == "")	{
		$objResponse->script("jAlert('警示', '確認', 'red', '', 2000);");
		return $objResponse;
		exit;
	}

	SaveValue($aFormValues);
	
	$objResponse->script("setSave();");

	$objResponse->script("parent.builder_info_myDraw();");
	$objResponse->script("parent.contractor_info_myDraw();");
	$objResponse->script("parent.$.fancybox.close();");
		
	
	return $objResponse;
}


$xajax->registerFunction("SaveValue");
function SaveValue($aFormValues){

	$objResponse = new xajaxResponse();
	
		//進行存檔動作
		
		$memberID				= trim($aFormValues['memberID']);
		$case_id				= trim($aFormValues['case_id']);
		$auto_seq				= trim($aFormValues['auto_seq']);
		$contact_name			= trim($aFormValues['contact_name']);
		$contact_tel			= trim($aFormValues['contact_tel']);
		$remark					= trim($aFormValues['remark']);
		

		//存入實體資料庫中
		$mDB = "";
		$mDB = new MywebDB();

		$Qry="UPDATE case_contacts set
				 contact_name 	= '$contact_name'		
				,contact_tel 	= '$contact_tel'
				,remark 		= '$remark'
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
$organization_type = $_GET['organization_type'];
$organization_id = $_GET['organization_id'];



$mDB = "";
$mDB = new MywebDB();


if (empty($_GET['auto_seq'])) {
	//若無$_GET['auto_seq']，則自動新增一筆
	$Qry="INSERT INTO case_contacts (case_id, organization_type, organization_id) VALUES ('$case_id','$organization_type','$organization_id')";
	$mDB->query($Qry);
	//再取出 auto_seq
	$Qry="SELECT auto_seq FROM case_contacts WHERE case_id = '$case_id' ORDER BY auto_seq DESC LIMIT 0,1";
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



$organization_name = $_GET['organization_name'];
$fm = $_GET['fm'];

$mess_title = $title;


$Qry="SELECT a.* FROM case_contacts a
WHERE a.auto_seq = '$auto_seq'";
$mDB->query($Qry);
$total = $mDB->rowCount();
if ($total > 0) {
    //已找到符合資料
	$row=$mDB->fetchRow(2);
	$organization_id = $row['organization_id'];
	$organization_type = $row['organization_type'];
	$contact_name = $row['contact_name'];
	$contact_tel = $row['contact_tel'];
	$remark = $row['remark'];



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

					<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">聯絡人:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="contact_name" name="contact_name" size="50"  maxlength="50" value="$contact_name" onchange="setEdit();"/>
								</div> 
							</div> 
						</div>
					<div>
					<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">聯絡電話:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="contact_tel" name="contact_tel" size="50"  maxlength="50" value="$contact_tel" onchange="setEdit();"/>
								</div> 
							</div> 
						</div>
					<div>
					<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">備注:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="remark" name="remark" size="50"  maxlength="50" value="$remark" onchange="setEdit();"/>
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