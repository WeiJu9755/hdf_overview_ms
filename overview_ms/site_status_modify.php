<?php


session_start();
$memberID = $_SESSION['memberID'];
$powerkey = $_SESSION['powerkey'];


//載入公用函數
@include_once '/website/include/pub_function.php';

@include_once("/website/class/".$site_db."_info_class.php");

// 使用xajax
@include_once '/website/xajax/xajax_core/xajax.inc.php';
$xajax = new xajax();

$xajax->registerFunction("processform");
function processform($aFormValues){

	$objResponse = new xajaxResponse();
	

	
	$auto_seq			= trim($aFormValues['auto_seq']);
	$memberID			= trim($aFormValues['memberID']);
	$site_status			= trim($aFormValues['site_status']);

	//存入實體資料庫中
	$mDB = "";
	$mDB = new MywebDB();
	
	
	$Qry = "UPDATE CaseManagement set
			`site_status` = '$site_status'
			,last_modify8 = now()
			,makeby8	= '$memberID'
			where auto_seq = '$auto_seq'";
	$mDB->query($Qry);
	
	$mDB->remove();
	
	$objResponse->script("myDraw();");
	$objResponse->script("art.dialog.tips('已存檔!',1);");
	$objResponse->script("parent.$.fancybox.close();");
	
	return $objResponse;
}

$xajax->processRequest();


$auto_seq = $_GET['auto_seq'];


$mDB = "";
$mDB = new MywebDB();

$Qry="SELECT * FROM CaseManagement
WHERE auto_seq = '$auto_seq'";
$mDB->query($Qry);
$total = $mDB->rowCount();
if ($total > 0) {
    //已找到符合資料
	$row=$mDB->fetchRow(2);
	$site_status = $row['site_status'];
}

//載入工地狀態
$Qry="select caption from items where pro_id = 'site_status' order by pro_id,orderby";
$mDB->query($Qry);
$select_site_status = "";
$select_site_status .= "<option></option>";

if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_caption = $row['caption'];
		$select_site_status .= "<option value=\"$ch_caption\" ".mySelect($ch_caption,$site_status).">$ch_caption</option>";
	}
}

$mDB->remove();




$show_center=<<<EOT
<link rel="stylesheet" type="text/css" href="/os/clockpicker-gh-pages/dist/jquery-clockpicker.css">
<script type="text/javascript" src="/os/clockpicker-gh-pages/dist/jquery-clockpicker.js"></script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<style type="text/css">

.card_full {
	width:100%;
	height:100vh;
}

#full {
	width: 100%;
	height: 100%;
}

#info_container {
	width: 100% !Important;
	margin: 10px auto !Important;
}

</style>
<div class="card card_full">
	<div id="full" class="card-body data-overlayscrollbars-initialize">
		<div id="info_container">
			<form method="post" id="modifyForm" name="modifyForm" enctype="multipart/form-data" action="javascript:void(null);">
			<div style="width:auto;margin: 0;padding:0;">
				<div class="field_container3 px-5 size14" style="margin-bottom: 50px;">
					<div>
						<div class="pb-1 weight">工地狀態:</div> 
						<div>
							<select id="site_status" name="site_status" placeholder="請選擇工地狀態" class="w-100" style="max-width:300px;">
								$select_site_status
							</select>
						</div> 
					</div>
				</div>
				<div class="form_btn_div">
					<input type="hidden" name="auto_seq" value="$auto_seq" />
					<input type="hidden" name="memberID" value="$memberID" />
					<button class="btn btn-primary" type="button" onclick="CheckValue(this.form);" style="padding: 10px;margin-right: 10px;"><i class="bi bi-check-lg green"></i>&nbsp;&nbsp;存檔</button>
					<button class="btn btn-warning" type="button" onclick="clearall();" style="padding: 10px;margin-right: 10px;"><i class="bi bi-x-lg"></i>&nbsp;&nbsp;清除</button>
					<button class="btn btn-danger" type="button" onclick="parent.$.fancybox.close();" style="padding: 10px;"><i class="bi bi-power"></i>&nbsp;&nbsp;關閉</button>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

function CheckValue(thisform) {
	xajax_processform(xajax.getFormValues('modifyForm'));
	thisform.submit();
}

function clearall() {
	$("#site_status").val("");
}


var myDraw = function(){
	var oTable;
	oTable = parent.$('#db_table').dataTable();
	oTable.fnDraw(false);
}

</script>

EOT;

?>