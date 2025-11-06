var old_no = ""; var old_prefix = "";
$(document).ready(function(){
    $("#party_id").trigger('change');
    $(document).on('change',"#party_id",function(){
        var party_id = $(this).val();
		var project_id = $("#project_id :selected").val();
        getPoList(party_id,project_id);
    });
	
	$(document).on('change',"#project_id",function(){
        var project_id = $(this).val();
		var party_id = $("#party_id :selected").val();
        getPoList(party_id,project_id);
    });

    $(document).on('change',"#po_id",function(){
        var po_id = $(this).val();
        getItemList(po_id);
    });

    old_no = $('#trans_no').val();
	old_prefix = $('#trans_prefix').val();
	$(document).on('change','#cm_id',function(){
		var cm_id = $(this).val();
		var selected_cm_id = $(this).data('selected_cm_id');
		var append_id = $(this).data('append_id') || "trans_number";		

		if(selected_cm_id == cm_id){
			$('#trans_no').val(old_no);
			$('#trans_prefix').val(old_prefix);
			$('#'+append_id).val(old_prefix+old_no);
		}else{
			$.ajax({
				url : base_url + controller + '/getMirNextNo',
				type : 'post',
				data : {cm_id : cm_id},
				dataType : 'json'
			}).done(function(response){
				$('#trans_no').val(response.next_no);
				$('#trans_prefix').val(old_prefix);
				$('#'+append_id).val(old_prefix+response.next_no);
			});
		}
	});

    $(document).on('click','.addBatch',function(e){
        e.stopImmediatePropagation(); e.preventDefault();
        
        var formData = {};
        $.each($(".itemFormInput"),function() {  
            formData[$(this).attr("id")] = $(this).val();  
        });

        $("#itemForm .error").html("");
        if (formData.item_id == "") { $(".item_id").html("Item Name is required."); 	}
        if (formData.qty == "" || parseFloat(formData.qty) == 0) {  $(".qty").html("Qty is required.");   }
        if (formData.project_id == ""){ $('.project_id').html("Project is required.");  }
		if (formData.po_id == ""){ $('.po_id').html("Purchase Order is required.");  }
        
        // $(".error").html("");
        var errorCount = $('.error:not(:empty)').length;

		if(errorCount == 0){
            formData.po_number = $("#po_id :selected").data('po_no');
            formData.item_name = $("#item_id :selected").text();
            // formData.project_name = $("#project_id :selected").text();
            formData.trans_status = 0; 
            AddBatchRow(formData);
            $.each($('.itemFormInput'),function(){ $(this).val(""); });
            $("#itemForm input:hidden").val('')
            $('#itemForm #row_index').val("");
            // $("#item_id").select2();
            $(".error").html("");
            initSelect2();
        }
    });
});

function resItemDetail(response = ""){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        $("#item_type").val((itemDetail.item_type || 0)); 
        $("#item_stock_type").val((itemDetail.stock_type || 0));
        if($("#po_id").find(":selected").val() == ""){
            $("#disc_per").val((itemDetail.defualt_disc || 0));
            $("#price").val((itemDetail.price || 0));
            $("#po_trans_id").val("");
        }else{
            $("#disc_per").val(($("#item_id").find(":selected").data('disc_per') || 0));
            $("#price").val(($("#item_id").find(":selected").data('price') || 0));
            $("#po_trans_id").val(($("#item_id").find(":selected").data('po_trans_id') || 0));
        }        
    }else{
        $("#item_type").val(""); 
        $("#item_stock_type").val("");
        $("#disc_per").val("");
        $("#price").val("");
        $("#po_trans_id").val("");
    }
}

function getPoList(party_id,project_id){
    var po_id = $('#po_id').val();
    if(party_id){
		$.ajax({
			url : base_url + controller + '/getPoNumberList',
			type : 'post',
			data : { party_id:party_id, project_id:project_id, po_id:po_id },
			dataType : 'json'
		}).done(function(response){
			$("#po_id").html(response.poOptions);
            initSelect2();
		});
    }else{
        $("#po_id").html('<option value="">Select Purchase Order</option>');
    }
}

function getItemList(po_id){
    var cm_id = $("#cm_id").val();
    $.ajax({
        url : base_url + controller + '/getItemList',
        type : 'post',
        data : {po_id : po_id, cm_id : cm_id},
        dataType : 'json'
    }).done(function(response){
        $("#item_id").html(response.itemOptions);
        initSelect2();//09-04-25

    });
}

var itemCount = 0;
function AddBatchRow(data){
    $('table#batchTable tr#noData').remove();
    //Get the reference of the Table's TBODY element.
	var tblName = "batchTable";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	// //Add Row.
	// row = tBody.insertRow(-1);
    // //Add index cell
	// var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
	// var cell = $(row.insertCell(-1));
	// cell.html(countRow);
	// cell.attr("style","width:5%;");	

    //Add Row.
	if (data.row_index != "") {
		var trRow = data.row_index;
		$("#" + tblName + " tbody tr:eq(" + trRow + ")").remove();
	}
	var ind = (data.row_index == "") ? -1 : data.row_index;
	row = tBody.insertRow(ind);

    //Add index cell
	var countRow = (data.row_index == "") ? ($('#' + tblName + ' tbody tr:last').index() + 1) : (parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:5%;");	

    // var cell = $(row.insertCell(-1));
	// cell.html(data.project_name);
	// cell.attr("style","width:5%;");	
    	
	var cell = $(row.insertCell(-1));
	cell.html(data.po_number);
	cell.attr("style","width:5%;");	

    var poIdInput = $("<input/>",{type:"hidden",name:"item_data["+itemCount+"][po_id]",value:data.po_id});
    var poTransIdInput = $("<input/>",{type:"hidden",name:"item_data["+itemCount+"][po_trans_id]",value:data.po_trans_id});
    var itemIdInput = $("<input/>",{type:"hidden",name:"item_data["+itemCount+"][item_id]",value:data.item_id});
    // var projectInput = $("<input/>",{type:"hidden",name:"item_data["+itemCount+"][project_id]",value:data.project_id});
    var mirTransIdInput = $("<input/>",{type:"hidden",name:"item_data["+itemCount+"][id]",value:data.id});
    
	var cell = $(row.insertCell(-1));
	cell.html(data.item_name);
	cell.attr("style","width:5%;");	
	
    cell.append(poIdInput);
	cell.append(poTransIdInput);
	cell.append(itemIdInput);
	// cell.append(projectInput);
    cell.append(mirTransIdInput);

    var batchQtyInput = $("<input/>",{type:"hidden",name:"item_data["+itemCount+"][qty]",value:data.qty});    
    cell = $(row.insertCell(-1));
	cell.html(data.qty);
    cell.append(batchQtyInput);

    var priceInput = $("<input/>",{type:"hidden",name:"item_data["+itemCount+"][price]",value:data.price});   
    cell = $(row.insertCell(-1));
	cell.html(data.price);
    cell.append(priceInput);


    var remarkInput = $("<input/>",{type:"hidden",name:"item_data["+itemCount+"][item_remark]",value:data.item_remark}); 
    cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
    cell.append(remarkInput);

    //Add Button cell.	
    var btnRemove = $('<button><i class="mdi mdi-trash-can-outline"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "batchRemove(this);");
	btnRemove.attr("style", "margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger btn-sm waves-effect waves-light");

    var btnEdit = $('<button><i class="mdi mdi-square-edit-outline"></i></button>');
	btnEdit.attr("type", "button");
	btnEdit.attr("onclick", "Edit(" + JSON.stringify(data) + ",this);");
	btnEdit.attr("class", "btn btn-outline-warning btn-sm waves-effect waves-light");

    
    cell = $(row.insertCell(-1));
    if(data.id == ""){
        cell.append(btnEdit);
        cell.append(btnRemove);
    }

    cell.attr("class","text-center");
    cell.attr("style","width:10%;");

    itemCount++;
}

function Edit(data, button) {
	var row_index = $(button).closest("tr").index();

	$.each(data, function (key, value) {
		$("#" + key).val(value);
	});
    var party_id = $("#party_id").val();
    var project_id = $("#project_id").val();
    if(party_id){
		$.ajax({
			url : base_url + controller + '/getPoNumberList',
			type : 'post',
			data : {party_id : party_id, project_id:project_id,po_id:data.po_id},
			dataType : 'json'
		}).done(function(response){
			$("#po_id").html(response.poOptions);

		});
    }else{
        $("#po_id").html('<option value="">Select Purchase Order</option>');
    }

    // var cm_id = $("#cm_id").val();
    // $.ajax({
    //     url : base_url + controller + '/getItemList',
    //     type : 'post',
    //     data : {po_id : dpo_id, cm_id : cm_id},
    //     dataType : 'json'
    // }).done(function(response){
    //     $("#item_id").html(response.itemOptions);
    //     initSelect2();//09-04-25

    // });
   

    // $("#itemForm #po_id").select2();
	initSelect2();
	$("#row_index").val(row_index);

}

function batchRemove(button){
    var row = $(button).closest("TR");
	var table = $("#batchTable")[0];
	table.deleteRow(row[0].rowIndex);

	$('#batchTable tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#batchTable tbody tr:last').index() + 1;

    if (countTR == 0) {
        $("#batchTable tbody").html('<tr id="noData"><td colspan="9" align="center">No data available in table</td></tr>');
    }
}
