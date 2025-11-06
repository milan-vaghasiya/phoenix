$(document).ready(function(){
	setPlaceHolder();

    $(document).on('click', '.saveItem', function () {
		
		var formData = {};
        $.each($(".itemFormInput"),function() {
            formData[$(this).attr("id")] = $(this).val();
        });
		
		
        $("#itemForm .error").html("");

        if (formData.item_name == "") {
			$(".item_name").html("Item Name is required.");
		}
        if (formData.unit_id == "" || parseFloat(formData.unit_id) == 0) {
            $(".unit_id").html("Unit is required.");
        }
        if (formData.rate == "" || parseFloat(formData.rate) == 0) {
            $(".rate").html("Rate is required.");
        }

        // var item_ids = $(".item_id").map(function () { return $(this).val(); }).get();
        // if ($.inArray(formData.item_id, item_ids) >= 0 && formData.row_index == "") {
            // $(".item_name").html("Item already added.");
        // }

        var errorCount = $('#itemForm .error:not(:empty)').length;

		if (errorCount == 0) {
			formData.unit_name = $('#unit_id option:selected').text();
            var itemData = calculateItem(formData);
            AddRow(itemData);
            // $('#itemForm')[0].reset();
			var selectedItem = $('#itemForm #item_id option:selected');
			$.each($('.itemFormInput'),function(){ $(this).val(""); });
            $("#itemForm input:hidden").val('')
            $('#itemForm #row_index').val("");
            setTimeout(function(){
				selectedItem.next().attr('selected', 'selected');
				initSelect2();
				$('.itemDetails').trigger('change');
				setTimeout(function(){
					$("#itemForm #item_id").focus();
				},150);
			},100);	

        }
	});

    $(document).on('click', '.btn-item-form-close', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('')
		$('#itemForm #row_index').val("");
		$("#itemForm .error").html("");
		initSelect2('itemModel');
	});   
	
	$(document).on("change","#project_id",function(){
		var site_address = $(this).find(':selected').data('site_address');
		$("#delivery_address").val(site_address);
	});
});

var itemCount = 0;
function AddRow(data) {
    var tblName = "purchaseOrderItems";

    //Remove blank line.
	$('table#'+tblName+' tr#noData').remove();

	//Get the reference of the Table's TBODY element.
	var tBody = $("#" + tblName + " > TBODY")[0];

	//Add Row.
	if (data.row_index != "") {
		var trRow = data.row_index;
		//$("tr").eq(trRow).remove();
		$("#" + tblName + " tbody tr:eq(" + trRow + ")").remove();
	}
	var ind = (data.row_index == "") ? -1 : data.row_index;
	row = tBody.insertRow(ind);

    //Add index cell
	var countRow = (data.row_index == "") ? ($('#' + tblName + ' tbody tr:last').index() + 1) : (parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:5%;");

    var idInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][id]", value: data.id });
	var itemNameInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_name]", value: data.item_name });
    
    cell = $(row.insertCell(-1));
    cell.html(data.item_name);
    cell.append(idInput);
    cell.append(itemNameInput);

    var unitIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][unit_id]", value: data.unit_id });
	// var unitNameInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][unit_name]", value: data.unit_name });
	cell = $(row.insertCell(-1));
	cell.html(data.unit_name);
	cell.append(unitIdInput);
	// cell.append(unitNameInput);

    var rateInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][rate]", value: data.rate});
	var priceErrorDiv = $("<div></div>", { class: "error price" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.rate);
	cell.append(rateInput);

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

	$(row).attr('data-item_data',JSON.stringify(data));

    claculateColumn();
	itemCount++;
}

function Edit(data, button) {
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal('show');
	$(".btn-close").hide();
	$(".btn-save").hide();
	$.each(data, function (key, value) {
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

	claculateColumn();
}

function resPartyDetail(response = ""){
    var html = '<option value="">Select GST No.</option>';
    if(response != ""){
        var partyDetail = response.data.partyDetail;
        $("#party_name").val(partyDetail.party_name);
        $("#party_state_code").val(partyDetail.party_state_code);
		$("#master_t_col_1").val(partyDetail.contact_person);
        $("#master_t_col_2").val(partyDetail.party_mobile);
        $("#gstin").val(partyDetail.gstin);
        //$("#master_t_col_3").val(partyDetail.address);
		
        // var gstDetails = response.data.gstDetails;var i = 1;
        // $.each(gstDetails,function(index,row){  
		// 	if(row.gstin !=""){
		// 		html += '<option value="'+row.gstin+'" '+((i==1)?"selected":"")+'>'+row.gstin+'</option>';
		// 		i++;
		// 	}
        // });         
    }else{
        $("#party_name").val("");
        $("#party_state_code").val("");
		$("#master_t_col_1").val("");
		$("#master_t_col_2").val("");
        //$("#master_t_col_3").val("");
    }
    //html += '<option value="URP">URP</option>';
    // $("#gstin").html(html).focus();
	initSelect2('itemModel');
	gstin();
}

function resItemDetail(response = ""){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        $("#itemForm #item_code").val(itemDetail.item_code);
        $("#itemForm #item_name").val(itemDetail.item_name);
        $("#itemForm #item_type").val(itemDetail.item_type);
        $("#itemForm #unit_id").val(itemDetail.unit_id);
        $("#itemForm #unit_name").val(itemDetail.unit_name);
        $("#itemForm #disc_per").val(itemDetail.defualt_disc);
        $("#itemForm #price").val(itemDetail.price);        
        $("#itemForm #hsn_code").val(itemDetail.hsn_code);
        $("#itemForm #gst_per").val(parseFloat(itemDetail.gst_per).toFixed(0));
    }else{
        $("#itemForm #item_code").val("");
        $("#itemForm #item_name").val("");
        $("#itemForm #item_type").val("");
        $("#itemForm #unit_id").val("");
        $("#itemForm #unit_name").val("");
		$("#itemForm #disc_per").val("");
        $("#itemForm #price").val("");
        $("#itemForm #hsn_code").val("");
        $("#itemForm #gst_per").val(0);
    }
	initSelect2('itemModel');
}

function resSaveOrder(data,formId){
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

function calcfootTotal(){
	var totalQtyArray = $(".item_qty").map(function () { return $(this).val(); }).get();
	var totalQtySum = 0;
	$.each(totalQtyArray, function () { totalQtySum += parseFloat(this) || 0; });
	$("#totalQty").html(totalQtySum.toFixed(2));

	var totaligstArray = $(".igst_amount").map(function () { return $(this).val(); }).get();
	var totaligstSum = 0;
	$.each(totaligstArray, function () { totaligstSum += parseFloat(this) || 0; });
	$("#totaligst").html(totaligstSum.toFixed(2));
	
	var totalcgstArray = $(".cgst_amount").map(function () { return $(this).val(); }).get();
	var totalcgstSum = 0;
	$.each(totalcgstArray, function () { totalcgstSum += parseFloat(this) || 0; });
	$("#totalcgst").html(totalcgstSum.toFixed(2));
	
	var totalsgstArray = $(".sgst_amount").map(function () { return $(this).val(); }).get();
	var totalsgstSum = 0;
	$.each(totalsgstArray, function () { totalsgstSum += parseFloat(this) || 0; });
	$("#totalsgst").html(totalsgstSum.toFixed(2));
	
	var totalAmtArray = $(".amount").map(function () { return $(this).val(); }).get();
	var totalAmtSum = 0;
	$.each(totalAmtArray, function () { totalAmtSum += parseFloat(this) || 0; });
	$("#totalamt").html(totalAmtSum.toFixed(2));
	
	var totalNetAmtArray = $(".net_amount").map(function () { return $(this).val(); }).get();
	var totalNetAmtSum = 0;
	$.each(totalNetAmtArray, function () { totalNetAmtSum += parseFloat(this) || 0; });
	$("#totalnetamt").html(totalNetAmtSum.toFixed(2));
}