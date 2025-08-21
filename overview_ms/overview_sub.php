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
	<table class="table table-bordered border-dark w-100" id="overview_sub_table" style="min-width:1720px;">
		<thead class="table-light border-dark">
			<tr style="border-bottom: 1px solid #000;">
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:10%;">工程概況</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:5%;">代工單位</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:5%;">配置<br>工班人數</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:5%;">鋁模主辦</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:5%;">鋼筋主辦</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:5%;">水電主辦</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:5%;">工地<br>負責人</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:5%;">工地經理<br>(所長/襄理)</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:4%;">鷹架</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:4%;">鋼筋</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:4%;">水電</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:4%;">放樣</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:4%;">壓送</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:4%;">混凝土廠</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:4%;">泥作</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:4%;">油漆</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:4%;">輕隔間</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:5%;">負責工務</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:5%;">上包<br>計價人員</th>
				<th scope="col" class="text-center text-nowrap vmiddle" style="width:5%;">下包<br>計價人員</th>
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


$show_overview_sub=<<<EOT
<style>
#overview_sub_table {
	width: 100% !Important;
	margin: 5px 0 0 0 !Important;
}
</style>

$list_view

<script>
	var oTable;
	$(document).ready(function() {
		$('#overview_sub_table').dataTable( {
			"processing": false,
			"serverSide": true,
			"responsive":  {
				details: true
			},//RWD響應式
			"scrollX": '$scroll',
			"paging": false,
			"searching": false,  //禁用原生搜索
			"ordering": false,
			"ajaxSource": "/smarty/templates/$site_db/$templates/sub_modal/project/func06/overview_ms/server_overview_sub.php?site_db=$site_db&case_id=$case_id",
			"info": false,
			"language": {
						"sUrl": "$dataTable_de"
					},
			"fixedHeader": true,
			"fixedColumns": {
        		left: 1,
    		},
			"fnRowCallback": function( nRow, aData, iDisplayIndex ) { 

				//工程概況
				var engineering_overview = "";
				if (aData[2] != null && aData[2] != "")
					engineering_overview = aData[2];

				$('td:eq(0)', nRow).html( '<div class="d-flex justify-content-center align-items-center size12 text-center" style="height:auto;min-height:32px;">'+engineering_overview+'</div>' );

				//代工單位
				var builder_id = '<div id="builder_id'+aData[0]+'"></div>';
				//鷹架
				var scaffold = '<div id="scaffold'+aData[0]+'"></div>';
				//鋼筋
				var rebar = '<div id="rebar'+aData[0]+'"></div>';
				//水電
				var hydropower = '<div id="hydropower'+aData[0]+'"></div>';
				//放樣
				var layout = '<div id="layout'+aData[0]+'"></div>';
				//壓送
				var concrete = '<div id="concrete'+aData[0]+'"></div>';
				//混凝土廠
				var concrete_plant = '<div id="concrete_plant'+aData[0]+'"></div>';
				//泥作
				var masonry = '<div id="masonry'+aData[0]+'"></div>';
				//油漆
				var painting = '<div id="painting'+aData[0]+'"></div>';
				//輕隔間
				var drywall = '<div id="drywall'+aData[0]+'"></div>';

				//負責工務
				var responsible = '<div id="responsible'+aData[0]+'"></div>';
				//上包計價人員
				var maincontractor_pricing_staff = '<div id="maincontractor_pricing_staff'+aData[0]+'"></div>';
				//下包計價人員
				var subcontractor_pricing_staff = '<div id="subcontractor_pricing_staff'+aData[0]+'"></div>';


				xajax_returnValue(aData[0],aData[3],aData[10],aData[11],aData[12],aData[13],aData[14],aData[15],aData[16],aData[17],aData[18],aData[19],aData[20],aData[21]);

				$('td:eq(1)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+builder_id+'</div>' );

				//配置工班人數	
				var workers = "";
				if (aData[4] != null && aData[4] != "")
					workers = aData[4];

				$('td:eq(2)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center size12 text-nowrap" style="height:auto;min-height:32px;">'+workers+'</div>' );

				//鋁模主辦
				var aluminum_formwork_host = "";
				if (aData[5] != null && aData[5] != "")
					aluminum_formwork_host = aData[5];

				$('td:eq(3)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center size12 text-nowrap" style="height:auto;min-height:32px;">'+aluminum_formwork_host+'</div>' );

				//鋼筋主辦
				var rebar_host = "";
				if (aData[6] != null && aData[6] != "")
					rebar_host = aData[6];

				$('td:eq(4)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center size12 text-nowrap" style="height:auto;min-height:32px;">'+rebar_host+'</div>' );

				//水電主辦
				var hydropower_host = "";
				if (aData[7] != null && aData[7] != "")
					hydropower_host = aData[7];

				$('td:eq(5)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center size12 text-nowrap" style="height:auto;min-height:32px;">'+hydropower_host+'</div>' );

				//工地負責人
				var site_manager = "";
				if (aData[8] != null && aData[8] != "")
					site_manager = aData[8];

				$('td:eq(6)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center size12 text-nowrap" style="height:auto;min-height:32px;">'+site_manager+'</div>' );

				//工地經理(所長/襄理)	
				var superintendent = "";
				if (aData[9] != null && aData[9] != "")
					superintendent = aData[9];

				$('td:eq(7)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center size12 text-nowrap" style="height:auto;min-height:32px;">'+superintendent+'</div>' );

				//鷹架
				$('td:eq(8)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+scaffold+'</div>' );
				//鋼筋
				$('td:eq(9)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+rebar+'</div>' );
				//水電
				$('td:eq(10)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+hydropower+'</div>' );
				//放樣
				$('td:eq(11)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+layout+'</div>' );
				//壓送
				$('td:eq(12)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+concrete+'</div>' );
				//混凝土廠
				$('td:eq(13)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+concrete_plant+'</div>' );
				//泥作
				$('td:eq(14)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+masonry+'</div>' );
				//油漆
				$('td:eq(15)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+painting+'</div>' );
				//輕隔間
				$('td:eq(16)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+drywall+'</div>' );

				//負責工務
				$('td:eq(17)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+responsible+'</div>' );
				//	//上包計價人員
				$('td:eq(18)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+maincontractor_pricing_staff+'</div>' );
				//下包計價人員
				$('td:eq(19)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;min-height:32px;">'+subcontractor_pricing_staff+'</div>' );

				//處理
				var url1 = "openfancybox_edit('/index.php?ch=overview_sub_edit&auto_seq="+aData[0]+"&case_id="+aData[1]+"&fm=$fm',1200,'96%','');";
				var mdel = "overview_sub_myDel("+aData[0]+",'"+aData[1]+"','$memberID');";

				var show_btn = '';
					show_btn = '<div class="btn-group text-nowrap">'
						+'<button type="button" class="btn btn-light" onclick="'+url1+'" title="編輯工程概況"><i class="bi bi-pencil-square"></i></button>'
						+'<button type="button" class="btn btn-light" onclick="'+mdel+'" title="刪除"><i class="bi bi-trash"></i></button>'
						+'</div>';

				$('td:eq(20)', nRow).html( '<div class="d-flex justify-content-center align-items-center text-center" style="height:auto;">'+show_btn+'</div>' );


				return nRow;
			}
			
		});
	
		/* Init the table */
		oTable = $('#overview_sub_table').dataTable();
		
	} );
	

var overview_sub_myDel = function(auto_seq,case_id,memberID) {

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
			xajax_DeleteRow(auto_seq,case_id,memberID);
		}
	});

};


var overview_sub_myDraw = function(){
	var oTable;
	oTable = $('#overview_sub_table').dataTable();
	oTable.fnDraw(false);
}

</script>

EOT;

?>