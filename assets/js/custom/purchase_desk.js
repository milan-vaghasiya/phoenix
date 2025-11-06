$(document).ready(function(){

	$('.newItem').hide();
	$(document).on('change','#item_id',function(){
		var item_id = $(this).val();

		if(item_id == '-1'){
			$('.newItem').show();
		}else{
			$('.newItem').hide();
			$('#item_name').val($('#item_id :selected').text());
		}
	}); 
	
	$(document).on('click', '.add-item', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('');
		$('#itemForm #row_index').val("");
        $("#itemForm .error").html();
		$("#itemForm #row_index").val("");
		
		$("#itemModel").modal('show');
		$(".btn-close").show();
		$(".btn-save").show();
		
		setTimeout(function(){ 
			$("#itemForm #item_id").focus();
			setPlaceHolder();initSelect2();
		},500);
		
	});

	$('.newItem').hide();
	var item_id = $('#item_id').val();
	if(item_id == '-1'){
		$('.newItem').show();
	}else{
		$('.newItem').hide();
	}
	
	$(document).on('click', '.saveItem', function () {
		var fd = $('#itemForm').serializeArray();
		var formData = {};
		$.each(fd, function (i, v) {
			formData[v.name] = v.value;
		});
        $("#itemForm .error").html("");

        if (formData.item_id == "") {
			$(".item_id").html("Item Name is required.");
		}
		
		if (formData.item_id == "-1" && formData.item_name == "") {
			$(".item_name").html("New Item Name is required.");
		}
		
        if (formData.qty == "" || parseFloat(formData.qty) == 0) {
            $(".qty").html("Qty is required.");
        }
        if (formData.uom == "") {
            $(".uom").html("Unit is required.");
        }

        var item_ids = $(".item_id").map(function () { return $(this).val(); }).get();
        if ($.inArray(formData.item_id, item_ids) >= 0 && formData.row_index == "") {
            $(".item_id").html("Item already added.");
        }
		
        var errorCount = $('#itemForm .error:not(:empty)').length;

		if (errorCount == 0) {
			formData.item_id_name = $('#item_id :selected').text();

            AddRow(formData);
			 
            $('#itemForm')[0].reset();
            $("#itemForm input:hidden").val('')
            $('#itemForm #row_index').val("");
            initSelect2('itemModel');
            if ($(this).data('fn') == "save") {
                $("#itemForm #item_id").focus();
            } else if ($(this).data('fn') == "save_close") {
                $("#itemModel").modal('hide');
            }

        }
	});
	
	$(document).on('change','#enq_number',function(e){
		e.stopImmediatePropagation();e.preventDefault();

		var enq_number = $(this).val();
		if (enq_number) {
			$.ajax({
				url: base_url + controller + '/getCompareList',
				data: { enq_number : enq_number },
				method: "POST",
				dataType: "json",
				success:function(data){
					$("#compareItemList").html('');
					$("#compareItemList").html(data.itemList);
				}
			});
		}
	}); 

	$(document).on('click','.compareBtn',function(e){
		e.stopImmediatePropagation();e.preventDefault();

		var partyIdArray = $(".partyCheck").map(function () { 
			if(this.checked){
				return $(this).val(); 
			}
		}).get();
		var enq_number = $('#enq_number').val();
		
		$(".party_error").html("");
		if (partyIdArray.length > 1) {
			$.ajax({
				url: base_url + controller + '/getPartyComparison',
				data: { party_id : partyIdArray, enq_number : enq_number },
				method: "POST",
				dataType: "json",
				success:function(data){
					$("#partyData").html('');
					$("#partyData").html(data.partyData);
				}
			});
		} else {
			$(".party_error").html("Select More Than One Quotation For Comparison.");
		}
	});
});

function quoteConfirm(data){
	var call_function = data.call_function;
	if(call_function == "" || call_function == null){call_function="edit";}

	var fnsave = data.fnsave;
	if(fnsave == "" || fnsave == null){fnsave="save";}

	var controllerName = data.controller;
	if(controllerName == "" || controllerName == null){controllerName=controller;}
	
	var modal_id = data.modal_id || "";
	var init_action = data.init_action || "";

	var enq_id = data.postData.id;
	var partyName = data.postData.party_name;
	var enquiry_no = data.postData.trans_number;
	var enquiry_date = data.postData.trans_date;
	
	var ajaxParam = {
		type: "POST",   
		url: base_url + controllerName +'/' + call_function,   
		data: data.postData
	}; 

	if(modal_id == ""){ 
		ajaxParam = {
			url: base_url + controllerName +'/' + call_function,   
			type: "POST",   
			data: data.postData,
			dataType : "JSON"
		}; 
	}
	
	$.ajax(ajaxParam).done(function(response){
		if(modal_id != ""){
			initModal(data,response);
		}else{
			window[init_action](response);
		}
		
		$("#party_name").html(partyName);
		$("#enquiry_no").html(enquiry_no);
		$("#enquiry_date").html(enquiry_date);
		$("#enq_id").val(enq_id);

		$('.floatOnly').keypress(function(event) {
			if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
				event.preventDefault();
			}
		});
	});
}

function AddRow(data){
	var tblName = "purchaseOrderItems";
	
	$('table#'+tblName+' tr#noData').remove();

	var tBody = $("#" + tblName + " > TBODY")[0];

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
	
    var idInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][id]", value: data.id });
	var reqIdInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][req_id]", value: data.req_id });
    var fromEntryTypeInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][from_entry_type]", value: data.from_entry_type });
    var itemIdInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][item_id]", class:"item_id", value: data.item_id });
    var itemNameInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][item_name]", class:"item_name", value: data.item_name });
	cell = $(row.insertCell(-1));
	if(data.item_id == "-1"){
		cell.html(data.item_name);
	}else{
		cell.html(data.item_id_name);		
	}
    cell.append(idInput);
    cell.append(reqIdInput);
    cell.append(fromEntryTypeInput);
    cell.append(itemIdInput);
    cell.append(itemNameInput);

    var unitIdInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][uom]", value: data.uom });
	cell = $(row.insertCell(-1));
	cell.html(data.uom);
	cell.append(unitIdInput);
	
    var qtyInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][qty]", class:"qty", value: data.qty });
	var qtyErrorDiv = $("<div></div>", { class: "error qty" + countRow });
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	cell.append(qtyErrorDiv);

    var remarkInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][item_remark]", value: data.item_remark});
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(remarkInput);

    //Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="mdi mdi-trash-can-outline"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
	btnRemove.attr("style", "margin-left:4px;");
	btnRemove.attr("class", "btn btn-sm btn-outline-danger waves-effect waves-light");

	var btnEdit = $('<button><i class="mdi mdi-square-edit-outline"></i></button>');
	btnEdit.attr("type", "button");
	btnEdit.attr("onclick", "Edit(" + JSON.stringify(data) + ",this);");
	btnEdit.attr("class", "btn btn-sm btn-outline-warning waves-effect waves-light");

	cell.append(btnEdit);
	cell.append(btnRemove);
	cell.attr("class", "text-center");
	cell.attr("style", "width:10%;");
}

function Edit(data, button) {
	
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal('show');
	$(".btn-close").hide();
	$(".btn-save").hide();
	var itemId = null; 
	$.each(data, function (key, value) {
		if (key === "item_id") {
            itemId = value;
		}
		if(key === "item_id" && value === "-1"){
			$('.newItem').show();
		}else{
			$('.newItem').hide();
		}
		$("#itemForm #" + key).val(value);
	});
	initSelect2('itemModel');
	$("#itemForm #row_index").val(row_index);
}

function Remove(button) {
    var tableId = "purchaseOrderItems";
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#"+tableId)[0];
	table.deleteRow(row[0].rowIndex);
	$('#'+tableId+' tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#'+tableId+' tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="14" align="center">No data available in table</td></tr>');
	}
}

function resSavePoDesk(data,formId){
    if(data.status==1){
        $('#'+formId)[0].reset();
		Swal.fire({ icon: 'success', title: data.message});
        window.location = base_url + controller;
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
			Swal.fire({ icon: 'error', title: data.message });
        }			
    }	
}