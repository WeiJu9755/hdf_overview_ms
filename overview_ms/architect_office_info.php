<?php


//error_reporting(E_ALL); 
//ini_set('display_errors', '1');


require_once '/website/os/Mobile-Detect-2.8.34/Mobile_Detect.php';
$detect = new Mobile_Detect;

if( $detect->isMobile() && !$detect->isTablet() ){
	$isMobile = 1;
} else {
	$isMobile = 0;
}


$fm = $_GET['fm'];

$sure_to_delete = getlang("您確定要刪除此筆資料嗎?");

$dataTable_de = getDataTable_de();
$Prompt = getlang("提示訊息");
$Confirm = getlang("確認");
$Cancel = getlang("取消");


$list_view=<<<EOT
<div class="w-100 px-3 py-2">
	<table class="table table-bordered border-dark w-100" id="architect_office_info_table" style="min-width:1720px;">
		<thead class="table-light border-dark">
			<tr style="border-bottom: 1px solid #000;">
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:10%;">聯絡人</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:5%;">連絡電話</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:5%;">備註</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:4%;">處理</th>
			</tr>
		</thead>
		<tbody class="table-group-divider">
			<tr>
				<td colspan="21" class="dataTables_empty">資料載入中...</td>
			</tr>
		</tbody>
	</table>
</div>
EOT;



$scroll = true;
if (!($detect->isMobile() && !$detect->isTablet())) {
	$scroll = false;
}


$show_architect_office_info=<<<EOT
<style>
#architect_office_info_table {
	width: 100% !Important;
	margin: 5px 0 0 0 !Important;
}
</style>

$list_view

<script>
	var oTable;
	$(document).ready(function() {
		$('#architect_office_info_table').dataTable( {
			"processing": false,
			"serverSide": true,
			"responsive":  {
				details: true
			},//RWD響應式
			"scrollX": '$scroll',
			"paging": false,
			"searching": false,  //禁用原生搜索
			"ordering": false,
			"ajaxSource": "/smarty/templates/$site_db/$templates/sub_modal/project/func06/overview_ms/server_case_contacts.php?site_db=$site_db&case_id=$case_id&organization_id=$architect_office&organization_type=architect_office",
			"info": false,
			"language": {
						"sUrl": "$dataTable_de"
					},
			"fixedHeader": true,
			"fixedColumns": {
        		left: 1,
    		},
			"fnRowCallback": function( nRow, aData, iDisplayIndex ) { 

				//聯絡人
				var contact_name = "";
				if (aData[4] != null && aData[4] != "")
					contact_name = aData[4];

				$('td:eq(0)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+contact_name+'</div>' );

				//聯絡電話
				var contact_tel = "";
				if (aData[5] != null && aData[5] != "")
					contact_tel = aData[5];

				$('td:eq(1)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+contact_tel+'</div>' );

				var remark = "";
				if (aData[6] != null && aData[6] != "")
					remark = aData[6];

				$('td:eq(2)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+remark+'</div>' );

				

				//處理
				var url1 = "openfancybox_edit('/index.php?ch=case_contacts_edit&auto_seq="+aData[0]+"&case_id="+aData[1]+"&fm=$fm',600,'50%','');";
				var mdel = "case_contacts_myDel("+aData[0]+",'"+aData[1]+"','$memberID');";

				var show_btn = '';
					show_btn = '<div class="btn-group text-nowrap">'
						+'<button type="button" class="btn btn-light" onclick="'+url1+'" title="編輯聯絡資料"><i class="bi bi-pencil-square"></i></button>'
						+'<button type="button" class="btn btn-light" onclick="'+mdel+'" title="刪除"><i class="bi bi-trash"></i></button>'
						+'</div>';

				$('td:eq(3)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;">'+show_btn+'</div>' );


				return nRow;
			}
			
		});
	
		/* Init the table */
		oTable = $('#architect_office_info_table').dataTable();
		
	} );
	

var case_contacts_myDel = function(auto_seq,case_id,memberID) {

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
			xajax_case_contact_DeleteRow(auto_seq,case_id,memberID);
		}
	});

};


var architect_office_info_myDraw = function(){
	var oTable;
	oTable = $('#architect_office_info_table').dataTable();
	oTable.fnDraw(false);
}

</script>

EOT;

?>