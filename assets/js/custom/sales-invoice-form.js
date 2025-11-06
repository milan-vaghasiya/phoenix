var itemCount = 0;var inv_print = 0;
$(document).ready(function(){
	$(".ledgerColumn").hide();
	$(".summary_desc").attr('style','width: 60%;');
	var party_name = $("#party_name").val();
	setTimeout(function(){
		if($("#memo_type").val() != "CASH"){
			$("#party_id").trigger('change');
		}
	},500);

	setTimeout(function(){
		if($("#memo_type").val() == "DEBIT"){
			$(".cashMemo").hide();$(".debitMemo").show();
		}else{
			var company_state_code = $("#cmId").find(":selected").data('state_code') || 24;
			$("#party_name").val(party_name);
			$("#party_state_code").val(company_state_code);
			$(".cashMemo").show();$(".debitMemo").hide();
		}
	},100);
	
	
	$(document).on('click','#savePrint',function(){
		inv_print = 1;
	});
	
	$(document).on('click','.getPendingOrders',function(){
		var party_id = $('#party_id').val();
		var cm_id = $('#cmId').val();
		var party_name = $('#party_id :selected').text();
		$('.party_id').html("");

		if (party_id != "" || party_id != 0) {
			$.ajax({
				url: base_url + 'salesOrders/getPartyOrders',
				type: 'post',
				data: { party_id: party_id, cm_id : cm_id },
				success: function (response) {
					$("#modal-xl").modal("show");
					$('#modal-xl .modal-body').html('');
					$('#modal-xl .modal-title').html("Carete Invoice [ Party Name : "+party_name+" ]");
					$('#modal-xl .modal-body').html(response);
					$('#modal-xl .modal-body form').attr('id',"createInvoiceForm");
					$('#modal-xl .modal-footer .btn-save').html('<i class="fa fa-check"></i> Create Invoice');
					$("#modal-xl .modal-footer .btn-save").attr('onclick',"createInvoice();");
				}
			});
		} else {
			$('.party_id').html("Party is required.");
		}	
	});

	$(document).on('click','.orderItem',function(){
		var trans_main_id = $(this).data('trans_main_id') || 0;
		if(trans_main_id){
			$(".order"+trans_main_id).prop('checked',true);
		}
	});

    $(document).on('click', '.add-item', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('');
		$('#itemForm #row_index').val("");
		$('#itemForm #stock_eff').val("1");
        $("#itemForm .error").html();

		var party_id = $('#party_id').val();
		$(".party_id").html("");
		$("#itemForm #row_index").val("");
		if(party_id){
			setPlaceHolder();
			$("#itemModel").modal("show");
			$("#itemModel .btn-close").show();
			$("#itemModel .btn-save").show();	

			setTimeout(function(){ $("#itemForm #item_id").focus();setPlaceHolder();initSelect2('itemModel'); },500);
		}else{ 
            $(".party_id").html("Party name is required."); $("#itemModel").modal('hide'); 
        }
	});

	$(document).on('change','#item_id',function(){
		var party_id = $('#party_name').val();
		$(".party_id").html("");

		if(party_id == ""){
            $(".party_id").html("Party name is required.");
			$("#item_id").val("");
			initSelect2();
        }else{
			/* $("#item_id").addClass("itemDetails");
			$(".itemDetails").trigger('change'); */
		}
	});

    $(document).on('click', '.saveItem', function () {
		/* var fd = $('#itemForm').serializeArray();
		var formData = {};
		$.each(fd, function (i, v) {
			formData[v.name] = v.value;
		}); */

		var formData = {};
        $.each($(".itemFormInput"),function(i, v) {
            formData[$(this).attr("id")] = $(this).val();
        });
		
        $("#itemForm .error").html("");

        if (formData.item_id == "") {
			$(".item_id").html("Item Name is required.");
		}
        if (formData.qty == "" || parseFloat(formData.qty) == 0) {
            $(".qty").html("Qty is required.");
        }
        if (formData.price == "" || parseFloat(formData.price) == 0) {
            $(".price").html("Price is required.");
        }

        var errorCount = $('#itemForm .error:not(:empty)').length;

		if (errorCount == 0) {
			formData.id = formData.trans_id;
            var itemData = calculateItem(formData);

            AddRow(itemData);

			var selectedItem = $('#itemForm #item_id option:selected');
            /* $('#itemForm')[0].reset(); */
			$.each($('.itemFormInput'),function(){ $(this).val(""); });

            $("#itemForm input:hidden").val('');
            $('#itemForm #row_index').val("");
            $('#itemForm #stock_eff').val(1);
            initSelect2();
			
			setTimeout(function(){
				selectedItem.next().attr('selected', 'selected');
				initSelect2();
				$('.itemDetails').trigger('change');
				setTimeout(function(){
					//$("#itemForm #total_box").focus().select();
					$("#itemForm #item_id").focus();
				},150);
			},100);			

            /* if ($(this).data('fn') == "save") {
                $("#item_id").focus();
            } else if ($(this).data('fn') == "save_close") {
                $("#itemModel").modal('hide');
            } */
        }
	});

    $(document).on('click', '.btn-item-form-close', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('')
		$('#itemForm #row_index').val("");
		$("#itemForm .error").html("");
        initSelect2('itemModel');
	}); 

	$(document).on('change','#unit_id',function(){
		$("#unit_name").val("");
		if($(this).val()){ $("#unit_name").val($("#unit_id :selected").data('unit')); }
	});

	$(document).on('change','#hsn_code',function(){
		$("#gst_per").val(($("#hsn_code :selected").data('gst_per') || 0));
		initSelect2('itemModel');
	});

	if($("#company_id :selected").val() != ""){
		$("#cmId").val(($("#company_id :selected").val() || 1));
		setTimeout(function(){$("#cmId").trigger('change');},500);
	}
	$(document).on('change','#cmId',function(){
		var cm_id = $(this).val();
		var selected_cm_id = $(this).data('selected_cm_id');
		var trans_prefix = "";		

		if(selected_cm_id != cm_id){
			setTimeout(function(){
				if(cm_id == 1){
					trans_prefix = "GJTD";
				}else if(cm_id == 2){
					trans_prefix = "RJTD";
				}else{
					trans_prefix = "SHNTD";
				}

				var trans_no = $('#trans_no').val();
				$('#trans_prefix').val(trans_prefix);
				$('#trans_number').val(trans_prefix+trans_no);
				$("#memo_type").trigger('change');
			},1000);
		}
	});

	//setTimeout(function(){$("#memo_type").trigger('change');},500);
	$(".cashMemo").hide();
	$(".debitMemo").show();
	$(document).on('change','#memo_type',function(){
		var memo_type = $(this).val();
		var selected_memo_type = $(this).data('selected_memo_type');
		var cm_id = $("#cmId").val();
		var selected_cm_id = $("#cmId").data('selected_cm_id');
		var trans_prefix = "";

		if(memo_type == "CASH"){
			trans_prefix = "TR";

			$("#party_id").val("");initSelect2();
			$("#party_id").trigger("change");

			$(".cashMemo").show();
			$(".debitMemo").hide();
		}else{
			if(cm_id == 1){
				trans_prefix = "GJTD";
			}else if(cm_id == 2){
				trans_prefix = "RJTD";
			}else{
				trans_prefix = "SHNTD";
			}
			$("#party_id").trigger("change");

			$(".cashMemo").hide();
			$(".debitMemo").show();
		}

		if(selected_cm_id == cm_id && selected_memo_type == memo_type){
			$('#trans_no').val(old_no);
			$('#trans_prefix').val(old_prefix);
			$('#trans_number').val(old_prefix+old_no);
		}else{
			$.ajax({
				url : base_url + controller + '/getNextInvNo',
				type : 'post',
				data : {cm_id : cm_id, memo_type : memo_type},
				dataType : 'json'
			}).done(function(response){
				$('#trans_no').val(response.next_no);
				$('#trans_prefix').val(trans_prefix);
				$('#trans_number').val(trans_prefix+response.next_no);
			});
		}
	});

	$(document).on('change',"#apply_penalty",function(){
		var apply_penalty = $(this).val();

		if($("#itemForm #item_id").val() != ""){
			if($("#apply_penalty").val() == 1){
				var mrp = parseFloat(parseFloat($("#itemForm #org_price").val()) + parseFloat($("#itemForm #penalty_price").val())).toFixed(2);
				$("#itemForm #org_price").val(mrp);
				var price = calculatePrice({org_price:mrp,gst_per:$("#itemForm #gst_per").val(),disc_per:$("#itemForm #disc_per").val()},"price");
				$("#itemForm #price").val(price);
				$("#itemForm #penalty_applicable").val(1);
			}else{
				var mrp = parseFloat(parseFloat($("#itemForm #org_price").val()) - parseFloat($("#itemForm #penalty_price").val())).toFixed(2);
				$("#itemForm #org_price").val(mrp);
				var price = calculatePrice({org_price:mrp,gst_per:$("#itemForm #gst_per").val(),disc_per:$("#itemForm #disc_per").val()},"price");
				$("#itemForm #price").val(price);
				$("#itemForm #penalty_applicable").val(0);
			}
		}		

		var formData, itemData = {}; itemCount = 0;
		$.each($("#salesInvoiceItems tbody tr"),function(){
			formData = $(this).data('item_data');			
			formData.trans_id = formData.id;
			formData.row_index = $(this).attr('id');

			if(apply_penalty == 1 && formData.penalty_applicable == 0){
				formData.org_price = parseFloat(parseFloat(formData.org_price) + parseFloat(formData.penalty_price)).toFixed(2);
				formData.price = calculatePrice({org_price:formData.org_price,gst_per:formData.gst_per,disc_per:formData.disc_per},"price");				
			}

			if(apply_penalty == 0 && formData.penalty_applicable == 1){
				formData.org_price = parseFloat(parseFloat(formData.org_price) - parseFloat(formData.penalty_price)).toFixed(2);
				formData.price = calculatePrice({org_price:formData.org_price,gst_per:formData.gst_per,disc_per:formData.disc_per},"price");
			}

			formData.penalty_applicable = apply_penalty;
            itemData = calculateItem(formData);

            AddRow(itemData);
		});
	});
});

function createInvoice(){
	var fromEntryTypes = $("#saveSalesInvoice #from_entry_type").val();
	var refIds = $("#saveSalesInvoice #ref_id").val();
	var ship_to_id = []; var mainRefIds = []; var mainFromEntryType = [];

	if(refIds != ""){ mainRefIds = refIds.split(","); }
	if(fromEntryTypes != ""){ mainFromEntryType = fromEntryTypes.split(","); }
	
	$(".orderItem:checked").map(function() {
		row = $(this).data('row');
		
		mainRefIds.push(row.trans_main_id);
		mainFromEntryType.push(row.main_entry_type);
		ship_to_id.push(row.ship_to_id);

		row.qty = row.pending_qty;
		row.gst_per = parseFloat(row.gst_per);
		row.org_price = (parseFloat(row.org_price) > 0)?row.org_price:row.price;
		row.stock_eff = (row.stock_eff == 0)?1:0;
		AddRow(row);		
	}).get();

	mainRefIds = $.unique(mainRefIds);
	mainFromEntryType = $.unique(mainFromEntryType);
	ship_to_id = $.unique(ship_to_id); 

	mainRefIds = mainRefIds.join(",");
	mainFromEntryType = mainFromEntryType.join(",");

	$("#saveSalesInvoice #ref_id").val("");
	$("#saveSalesInvoice #ref_id").val(mainRefIds);
	$("#saveSalesInvoice #from_entry_type").val("");
	$("#saveSalesInvoice #from_entry_type").val(mainFromEntryType);
	$("#saveSalesInvoice #ship_to_id").val("");
	$("#saveSalesInvoice #ship_to_id").val(ship_to_id[0]);
	initSelect2();

	$("#modal-xl").modal('hide');
	$('#modal-xl .modal-body').html('');
}

function AddRow(data) {
    var tblName = "salesInvoiceItems";

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
	$(row).attr('id',itemCount);

    //Add index cell
	var countRow = (data.row_index == "") ? ($('#' + tblName + ' tbody tr:last').index() + 1) : (parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:5%;");

    var idInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][id]", value: data.id });
    var itemIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_id]", class:"item_id", value: data.item_id });
	var itemNameInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_name]", value: data.item_name });
    var formEnteryTypeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][from_entry_type]", value: data.from_entry_type });
	var refIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][ref_id]", value: data.ref_id });
    var itemCodeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_code]", value: data.item_code });
    var itemtypeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_type]", value: data.item_type });
	var stockEffInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][stock_eff]", value: data.stock_eff });
    var pormInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][p_or_m]", value: -1 });
    cell = $(row.insertCell(-1));
    cell.html(data.item_name);
    cell.append(idInput);
    cell.append(itemIdInput);
    cell.append(itemNameInput);
    cell.append(formEnteryTypeInput);
    cell.append(refIdInput);
    cell.append(itemCodeInput);
    cell.append(itemtypeInput);
	cell.append(stockEffInput);
    cell.append(pormInput);

    var hsnCodeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][hsn_code]", value: data.hsn_code });
	cell = $(row.insertCell(-1));
	cell.html(data.hsn_code);
	cell.append(hsnCodeInput);

    var qtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][qty]", class:"item_qty", value: data.qty });
	var qtyErrorDiv = $("<div></div>", { class: "error qty" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	cell.append(qtyErrorDiv);

    var unitIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][unit_id]", value: data.unit_id });
	var unitNameInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][unit_name]", value: data.unit_name });
	cell = $(row.insertCell(-1));
	cell.html(data.unit_name);
	cell.append(unitIdInput);
	cell.append(unitNameInput);

    var priceInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][price]", value: data.price});
    var orgPriceInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][org_price]", value: data.org_price});
	var priceErrorDiv = $("<div></div>", { class: "error price" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.price);
	cell.append(priceInput);
	cell.append(orgPriceInput);
	cell.append(priceErrorDiv);

    var discPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][disc_per]", value: data.disc_per});
	var discAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][disc_amount]", value: data.disc_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.disc_amount + '(' + data.disc_per + '%)');
	cell.append(discPerInput);
	cell.append(discAmtInput);

    var cgstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][cgst_per]", value: data.cgst_per });
	var cgstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][cgst_amount]", class:'cgst_amount', value: data.cgst_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.cgst_amount + '(' + data.cgst_per + '%)');
	cell.append(cgstPerInput);
	cell.append(cgstAmtInput);
	cell.attr("class", "cgstCol");

	var sgstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][sgst_per]", value: data.sgst_per });
	var sgstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][sgst_amount]", class:"sgst_amount", value: data.sgst_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.sgst_amount + '(' + data.sgst_per + '%)');
	cell.append(sgstPerInput);
	cell.append(sgstAmtInput);
	cell.attr("class", "sgstCol");

	var gstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][gst_per]", class:"gst_per", value: data.gst_per });
	var gstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][gst_amount]", class:"gst_amount", value: data.gst_amount });
	var igstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][igst_per]", value: data.igst_per });
	var igstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][igst_amount]", class:"igst_amount", value: data.igst_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.igst_amount + '(' + data.igst_per + '%)');
	cell.append(gstPerInput);
	cell.append(gstAmtInput);
	cell.append(igstPerInput);
	cell.append(igstAmtInput);
	cell.attr("class", "igstCol");

    var amountInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][amount]", class:"amount", value: data.amount });
    var taxableAmountInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][taxable_amount]", class:"taxable_amount", value: data.taxable_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.taxable_amount);
	cell.append(amountInput);
	cell.append(taxableAmountInput);
	cell.attr("class", "amountCol");

	var netAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][net_amount]", value: data.net_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.net_amount);
	cell.append(netAmtInput);
	cell.attr("class", "netAmtCol");

    var itemRemarkInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_remark]", value: data.item_remark});
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(itemRemarkInput);

    //Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="mdi mdi-trash-can-outline"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
	btnRemove.attr("style", "margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger btn-sm waves-effect waves-light");

	var btnEdit = $('<button><i class="mdi mdi-square-edit-outline"></i></button>');
	btnEdit.attr("type", "button");
	btnEdit.attr("onclick", "Edit(" + JSON.stringify(data) + ",this);");
	btnEdit.attr("class", "btn btn-outline-warning btn-sm waves-effect waves-light");

	cell.append(btnEdit);
	cell.append(btnRemove);
	cell.attr("class", "text-center");
	cell.attr("style", "width:10%;");

	$(row).attr('data-item_data',JSON.stringify(data));

    claculateColumn();calculateItemQty();
	itemCount++;
}

function calculateItemQty(){
	var totalQtyArray = $(".item_qty").map(function () { return $(this).val(); }).get();
	var totalQtySum = 0;
	$.each(totalQtyArray, function () { totalQtySum += parseFloat(this) || 0; });
	$("#totalQty").html(totalQtySum.toFixed(2));
}

function Edit(data, button) {
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal("show");
	$("#itemModel .btn-save").hide();
	$.each(data, function (key, value) {
		$("#itemForm #" + key).val(value);
	});

	initSelect2('itemModel');
	//$("#item_id").addClass("itemDetails");
	$("#itemForm #trans_id").val(data.id);
	$("#itemForm #row_index").val(row_index);
	$("#itemForm #qty").trigger('change');

	if($.inArray(parseInt(data.item_type),[3,4,10]) >= 0){
		$("#itemForm #org_price").prop('readonly',false);
		$("#itemForm #price").prop('readonly',false);
	}else{
		$("#itemForm #org_price").prop('readonly',true);
		$("#itemForm #price").prop('readonly',true);
	}
}

function Remove(button) {
    var tableId = "salesInvoiceItems";
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#"+tableId)[0];
	table.deleteRow(row[0].rowIndex);
	$('#'+tableId+' tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#'+tableId+' tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="15" align="center">No data available in table</td></tr>');
	}

	claculateColumn();calculateItemQty();
}

function resPartyDetail(response = ""){
    var html = '<option value="">Select GST No.</option>';
	var shopToOptions = '<option value="">Select Ship To</option>';
	$(".limit_error").html("");
    if(response != ""){
        var partyDetail = response.data.partyDetail;
        $("#party_name").val(partyDetail.party_name);
		$("#party_state_code").val(partyDetail.state_code);
		$("#closing_balance").html(inrFormat(partyDetail.closing_balance)+' '+partyDetail.closing_type);

		if(parseFloat(partyDetail.credit_limit) > 0 && (parseFloat(partyDetail.cl_balance) * -1) >= parseFloat(partyDetail.credit_limit)){
			$(".limit_error").html("Customer has reached her credit limit.");
		}

        var gstDetails = response.data.gstDetails; var i = 1;
        $.each(gstDetails,function(index,row){  
			if(row.gstin !=""){
				html += '<option value="'+row.gstin+'" '+((i==1)?"selected":"")+'>'+row.gstin+'</option>';
				i++;
			}            
        });

		var shipToList = response.data.shipToDetails;
		var selectedShipTo = $("#ship_to_id").data('selected_value');
		$.each(shipToList,function(index,row){  
			var selected = (selectedShipTo != "" && selectedShipTo == row.id)?"selected":"";
			shopToOptions += '<option value="'+row.id+'" '+selected+'>'+row.ship_to+'</option>';
        });

		
		partyDetail.vou_name_s = ["'Sale'","'GInc'"];
		partyDetail.cm_id = $("#cmId").val();
		partyDetail.trans_date = $("#trans_date").val() || "";
		partyDetail.trans_main_id = $(".trans_main_id").val() || "";
		checkPartyTurnover(partyDetail);		
    }else{
        $("#party_name").val("");
		$("#party_state_code").val("");
		$("#closing_balance").html(0);
		$("#turnover").val(0);
		$("#Turnover").html(0);

		var company_state_code = $("#cmId").find(":selected").data('state_code') || 24;
		$("#party_state_code").val(company_state_code);
    }
    $("#gstin").html(html).focus();
    $("#ship_to_id").html(shopToOptions);
	initSelect2();gstin();
}

function resItemDetail(response = ""){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        $("#itemForm #item_id").val(itemDetail.id);
        $("#itemForm #item_code").val(itemDetail.item_code);
        $("#itemForm #item_name").val(itemDetail.item_name);
        $("#itemForm #item_type").val(itemDetail.item_type);
        $("#itemForm #unit_id").val(itemDetail.unit_id);
        $("#itemForm #unit_name").val(itemDetail.unit_name);
		$("#itemForm #disc_per").val(itemDetail.defualt_disc);
		$("#itemForm #price").val(itemDetail.price);
		$("#itemForm #org_price").val(itemDetail.mrp);
		$("#itemForm #total_box").val(0);
		$("#itemForm #strip_qty").val(0);
		$("#itemForm #qty").val(0);
        $("#itemForm #hsn_code").val(itemDetail.hsn_code);
        $("#itemForm #gst_per").val(parseFloat(itemDetail.gst_per));

		if(itemDetail.item_type == 10){ $("#itemForm #stock_eff").val(0); }else{ $("#itemForm #stock_eff").val(1); }
    }else{
		$("#itemForm #item_id").val("");
        $("#itemForm #item_code").val("");
        $("#itemForm #item_name").val("");
        $("#itemForm #item_type").val("");
        $("#itemForm #unit_id").val("");
        $("#itemForm #unit_name").val("");
		$("#itemForm #disc_per").val("");
		$("#itemForm #price").val("");
		$("#itemForm #org_price").val("");
        $("#itemForm #hsn_code").val("");
        $("#itemForm #gst_per").val(0);
		$("#itemForm #stock_eff").val(1);
    }
	initSelect2();
}

function resSaveInvoice(data,formId){
    if(data.status==1){
        $('#'+formId)[0].reset();
        Swal.fire({ icon: 'success', title: data.message});

		if(inv_print == 1){
			var postData = {id:data.id,original:1,duplicate:1,triplicate:0,extra_copy:0,header_footer:0}; 
			var url = base_url + controller + '/printInvoice/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
			window.open(url);
		}

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