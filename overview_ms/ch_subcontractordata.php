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
	
	$web_id					= trim($aFormValues['web_id']);
	$auto_seq				= trim($aFormValues['auto_seq']);
	$memberID				= trim($aFormValues['memberID']);
	$case_id				= trim($aFormValues['case_id']);
	$subcontractor_id1		= trim($aFormValues['subcontractor_id1']);
	$construction_floor1	= trim($aFormValues['construction_floor1']);
	$total_contract_amt1 	= trim($aFormValues['total_contract_amt1']);
	$subcontractor_id2		= trim($aFormValues['subcontractor_id2']);
	$construction_floor2	= trim($aFormValues['construction_floor2']);
	$total_contract_amt2 	= trim($aFormValues['total_contract_amt2']);
	$subcontractor_id3		= trim($aFormValues['subcontractor_id3']);
	$construction_floor3	= trim($aFormValues['construction_floor3']);
	$total_contract_amt3 	= trim($aFormValues['total_contract_amt3']);
	$subcontractor_id4		= trim($aFormValues['subcontractor_id4']);
	$construction_floor4	= trim($aFormValues['construction_floor4']);
	$total_contract_amt4 	= trim($aFormValues['total_contract_amt4']);

	$confirm1		= trim($aFormValues['confirm1']);
	$confirm2		= trim($aFormValues['confirm2']);
	$confirm3		= trim($aFormValues['confirm3']);
	$confirm4		= trim($aFormValues['confirm4']);


	$mDB = "";
	$mDB = new MywebDB();

	if ($confirm1 == "Y") {
		//檢查是否存在
		$Qry="SELECT auto_seq FROM overview_sub
		WHERE case_id = '$case_id' AND builder_id = '$subcontractor_id1' AND engineering_overview = '$construction_floor1'";
		$mDB->query($Qry);
		$total = $mDB->rowCount();
		if ($total > 0) {
			//已找到符合資料，不予處理
		} else {
			//新增
			$Qry="INSERT INTO overview_sub (case_id,engineering_overview,builder_id) VALUES
				('$case_id','$construction_floor1','$subcontractor_id1')";
			$mDB->query($Qry);
		}
	}

	if ($confirm2 == "Y") {
		//檢查是否存在
		$Qry="SELECT auto_seq FROM overview_sub
		WHERE case_id = '$case_id' AND builder_id = '$subcontractor_id2' AND engineering_overview = '$construction_floor2'";
		$mDB->query($Qry);
		$total = $mDB->rowCount();
		if ($total > 0) {
			//已找到符合資料，不予處理
		} else {
			//新增
			$Qry="INSERT INTO overview_sub (case_id,engineering_overview,builder_id) VALUES
				('$case_id','$construction_floor2','$subcontractor_id2')";
			$mDB->query($Qry);
		}
	}

	if ($confirm3 == "Y") {
		//檢查是否存在
		$Qry="SELECT auto_seq FROM overview_sub
		WHERE case_id = '$case_id' AND builder_id = '$subcontractor_id3' AND engineering_overview = '$construction_floor3'";
		$mDB->query($Qry);
		$total = $mDB->rowCount();
		if ($total > 0) {
			//已找到符合資料，不予處理
		} else {
			//新增
			$Qry="INSERT INTO overview_sub (case_id,engineering_overview,builder_id) VALUES
				('$case_id','$construction_floor3','$subcontractor_id3')";
			$mDB->query($Qry);
		}
	}

	if ($confirm4 == "Y") {
		//檢查是否存在
		$Qry="SELECT auto_seq FROM overview_sub
		WHERE case_id = '$case_id' AND builder_id = '$subcontractor_id4' AND engineering_overview = '$construction_floor4'";
		$mDB->query($Qry);
		$total = $mDB->rowCount();
		if ($total > 0) {
			//已找到符合資料，不予處理
		} else {
			//新增
			$Qry="INSERT INTO overview_sub (case_id,engineering_overview,builder_id) VALUES
				('$case_id','$construction_floor4','$subcontractor_id4')";
			$mDB->query($Qry);
		}
	}


	$Qry="UPDATE CaseManagement set
		last_modify8 = now()
		,makeby8	= '$memberID'
		,update_count8 = update_count8 + 1
		WHERE case_id = '$case_id'";
	$mDB->query($Qry);



	$mDB->remove();


	$objResponse->script("parent.overview_sub_myDraw();");

	$message01 = getlang("已加入!");
	$objResponse->script("parent.jAlert('Success', '$message01', 'green', '', 1000);");
	$objResponse->script("parent.$.fancybox.close();");
		
	return $objResponse;
}

$xajax->processRequest();


$fm = $_GET['fm'];
$case_id = $_GET['case_id'];

$mess_title = $title;

//$pro_id = "com";


$mDB = "";
$mDB = new MywebDB();
/*
$Qry="SELECT a.*,b.employee_name,c.engineering_name,d.builder_name,e.contractor_name FROM CaseManagement a
LEFT JOIN employee b ON b.employee_id = a.Handler
LEFT JOIN construction c ON c.construction_id = a.construction_id
LEFT JOIN builder d ON d.builder_id = a.builder_id
LEFT JOIN contractor e ON e.contractor_id = a.contractor_id
WHERE a.case_id = '$case_id'";
*/
$Qry="SELECT a.*
,b.subcontractor_name as subcontractor_name1
,c.subcontractor_name as subcontractor_name2 
,d.subcontractor_name as subcontractor_name3 
,e.subcontractor_name as subcontractor_name4 
FROM CaseManagement a
LEFT JOIN subcontractor b ON b.subcontractor_id = a.subcontractor_id1
LEFT JOIN subcontractor c ON c.subcontractor_id = a.subcontractor_id2
LEFT JOIN subcontractor d ON d.subcontractor_id = a.subcontractor_id3
LEFT JOIN subcontractor e ON e.subcontractor_id = a.subcontractor_id4
WHERE a.case_id = '$case_id'";
$mDB->query($Qry);
$total = $mDB->rowCount();
if ($total > 0) {
    //已找到符合資料
	$row=$mDB->fetchRow(2);
	$status1 = $row['status1'];
	$status2 = $row['status2'];
	$region = $row['region'];
	$case_id = $row['case_id'];
	$construction_id = $row['construction_id'];
	/*
	$engineering_name = $row['engineering_name'];
	$builder_id = $row['builder_id'];
	$builder_name = $row['builder_name'];
	$contractor_id = $row['contractor_id'];
	$contractor_name = $row['contractor_name'];
	$contact = $row['contact'];
	$site_location = $row['site_location'];
	$county = $row['county'];
	$town = $row['town'];
	$zipcode = $row['zipcode'];
	$address = $row['address'];
	$ContractingModel = $row['ContractingModel'];
	$Handler = $row['Handler'];
	$Handler_name = $row['employee_name'];
	$buildings = $row['buildings'];
	$first_review_date = $row['first_review_date'];
	$estimated_return_date = $row['estimated_return_date'];
	$preliminary_status = $row['preliminary_status'];
	$remark = $row['remark'];

	$engineering_qty = $row['engineering_qty'];
	$std_layer_template_qty = $row['std_layer_template_qty'];
	$roof_protrusion_template_qty = $row['roof_protrusion_template_qty'];
	$material_amt = $row['material_amt'];
	$OEM_cost = $row['OEM_cost'];
	$quotation_amt = $row['quotation_amt'];
	$quotation_sended = $row['quotation_sended'];
	$quotation_date = $row['quotation_date'];
	$estimated_arrival_date = $row['estimated_arrival_date'];
	$actual_entry_date = $row['actual_entry_date'];
	$completion_date = $row['completion_date'];

	$contract_date = $row['contract_date'];
	$advance_payment1 = $row['advance_payment1'];
	$estimated_payment_date1 = $row['estimated_payment_date1'];
	$request_date1 = $row['request_date1'];
	$advance_payment2 = $row['advance_payment2'];
	$estimated_payment_date2 = $row['estimated_payment_date2'];
	$request_date2 = $row['request_date2'];
	$advance_payment3 = $row['advance_payment3'];
	$estimated_payment_date3 = $row['estimated_payment_date3'];
	$request_date3 = $row['request_date3'];

	$geto_no = $row['geto_no'];
	$geto_quotation = $row['geto_quotation'];
	$geto_order_date = $row['geto_order_date'];
	$geto_contract_date = $row['geto_contract_date'];
	$geto_formwork = $row['geto_formwork'];
	$material_import_date = $row['material_import_date'];


	$ERP_no = $row['ERP_no'];
	$buildings_contract = $row['buildings_contract'];
	$total_contract_amt = $row['total_contract_amt'];
	*/

	//下包代工1
	$subcontractor_id1 = $row['subcontractor_id1'];
	$subcontractor_name1 = $row['subcontractor_name1'];
	$construction_floor1 = $row['construction_floor1'];
	$total_contract_amt1 = $row['total_contract_amt1'];
	$fmt_total_contract_amt1 = number_format($total_contract_amt1);

	$subcontractor_id2 = $row['subcontractor_id2'];
	$subcontractor_name2 = $row['subcontractor_name2'];
	$construction_floor2 = $row['construction_floor2'];
	$total_contract_amt2 = $row['total_contract_amt2'];
	$fmt_total_contract_amt2 = number_format($total_contract_amt2);

	$subcontractor_id3 = $row['subcontractor_id3'];
	$subcontractor_name3 = $row['subcontractor_name3'];
	$construction_floor3 = $row['construction_floor3'];
	$total_contract_amt3 = $row['total_contract_amt3'];
	$fmt_total_contract_amt3 = number_format($total_contract_amt3);

	$subcontractor_id4 = $row['subcontractor_id4'];
	$subcontractor_name4 = $row['subcontractor_name4'];
	$construction_floor4 = $row['construction_floor4'];
	$total_contract_amt4 = $row['total_contract_amt4'];
	$fmt_total_contract_amt4 = number_format($total_contract_amt4);

	if (!empty($subcontractor_id1))
		$m_confirm1 = "checked=\"checked\"";

	if (!empty($subcontractor_id2))
		$m_confirm2 = "checked=\"checked\"";

	if (!empty($subcontractor_id3))
		$m_confirm3 = "checked=\"checked\"";

	if (!empty($subcontractor_id4))
		$m_confirm4 = "checked=\"checked\"";


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
					<hr class="style_b" style="margin: 5px 0 -5px 0;">
					<div>
						<div class="field_div1">下包代工1:</div> 
						<div class="field_div2">
							<div class="inline blue weight my-2 me-2">$subcontractor_id1</div>
							<div class="inline blue weight my-2">$subcontractor_name1</div>
						</div> 
					</div>
					<div>
						<div class="field_div1">施作樓層1:</div> 
						<div class="field_div2">
							<div class="inline blue weight my-2">$construction_floor1</div>
						</div> 
					</div>
					<div>
						<div class="field_div1">合約總價1(含稅):</div> 
						<div class="field_div2">
							<div class="inline blue weight my-2">$fmt_total_contract_amt1</div>
							<div class="inline my-2 float-end">
								<input type="checkbox" class="inputtext" name="confirm1" id="confirm1" value="Y" $m_confirm1/>
								<label for="confirm1" class="red">加入到工程概況</label>
							</div>
						</div> 
					</div>
					<hr class="style_b" style="margin: 5px 0 -5px 0;">
					<div>
						<div class="field_div1">下包代工2:</div> 
						<div class="field_div2">
							<div class="inline blue weight my-2 me-2">$subcontractor_id2</div>
							<div class="inline blue weight my-2">$subcontractor_name2</div>
						</div> 
					</div>
					<div>
						<div class="field_div1">施作樓層2:</div> 
						<div class="field_div2">
							<div class="inline blue weight my-2">$construction_floor2</div>
						</div> 
					</div>
					<div>
						<div class="field_div1">合約總價2(含稅):</div> 
						<div class="field_div2">
							<div class="inline blue weight my-2">$fmt_total_contract_amt2</div>
							<div class="inline my-2 float-end">
								<input type="checkbox" class="inputtext" name="confirm2" id="confirm2" value="Y" $m_confirm2/>
								<label for="confirm2" class="red">加入到工程概況</label>
							</div>
						</div> 
					</div>
					<hr class="style_b" style="margin: 5px 0 -5px 0;">
					<div>
						<div class="field_div1">下包代工3:</div> 
						<div class="field_div2">
							<div class="inline blue weight my-2 me-2">$subcontractor_id3</div>
							<div class="inline blue weight my-2">$subcontractor_name3</div>
						</div> 
					</div>
					<div>
						<div class="field_div1">施作樓層3:</div> 
						<div class="field_div2">
							<div class="inline blue weight my-2">$construction_floor3</div>
						</div> 
					</div>
					<div>
						<div class="field_div1">合約總價3(含稅):</div> 
						<div class="field_div2">
							<div class="inline blue weight my-2">$fmt_total_contract_amt3</div>
							<div class="inline my-2 float-end">
								<input type="checkbox" class="inputtext" name="confirm3" id="confirm3" value="Y" $m_confirm3/>
								<label for="confirm3" class="red">加入到工程概況</label>
							</div>
						</div> 
					</div>
					<hr class="style_b" style="margin: 5px 0 -5px 0;">
					<div>
						<div class="field_div1">下包代工4:</div> 
						<div class="field_div2">
							<div class="inline blue weight my-2 me-2">$subcontractor_id4</div>
							<div class="inline blue weight my-2">$subcontractor_name4</div>
						</div> 
					</div>
					<div>
						<div class="field_div1">施作樓層4:</div> 
						<div class="field_div2">
							<div class="inline blue weight my-2">$construction_floor4</div>
						</div> 
					</div>
					<div>
						<div class="field_div1">合約總價4(含稅):</div> 
						<div class="field_div2">
							<div class="inline blue weight my-2">$fmt_total_contract_amt4</div>
							<div class="inline my-2 float-end">
								<input type="checkbox" class="inputtext" name="confirm4" id="confirm4" value="Y" $m_confirm4/>
								<label for="confirm4" class="red">加入到工程概況</label>
							</div>
						</div> 
					</div>
					<hr class="style_b" style="margin: 5px 0 -5px 0;">
					<!--
					<div>
						<div class="field_div1">設定:</div> 
						<div class="field_div2 pt-3">
							<input type="checkbox" class="inputtext" name="confirm7" id="confirm7" value="Y" $m_confirm7 />
							<label for="confirm7" class="red">確認</label>
						</div>
					</div>
					-->
					<div class="mt-3 text-center">
						<input type="hidden" name="fm" value="$fm" />
						<input type="hidden" name="site_db" value="$site_db" />
						<input type="hidden" name="auto_seq" value="$auto_seq" />
						<input type="hidden" name="case_id" value="$case_id" />
						<input type="hidden" name="memberID" value="$memberID" />
						<input type="hidden" name="subcontractor_id1" value="$subcontractor_id1" />
						<input type="hidden" name="subcontractor_id2" value="$subcontractor_id2" />
						<input type="hidden" name="subcontractor_id3" value="$subcontractor_id3" />
						<input type="hidden" name="subcontractor_id4" value="$subcontractor_id4" />
						<input type="hidden" name="construction_floor1" value="$construction_floor1" />
						<input type="hidden" name="construction_floor2" value="$construction_floor2" />
						<input type="hidden" name="construction_floor3" value="$construction_floor3" />
						<input type="hidden" name="construction_floor4" value="$construction_floor4" />
						<button id="close" class="btn btn-danger btn-lg" type="button" onclick="CheckValue(this.form);" style="padding: 5px 15px;"><i class="bi bi-building-add"></i>&nbsp;確定加入</button>
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

</script>

EOT;

?>