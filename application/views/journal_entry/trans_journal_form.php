<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="col-12">
                    <div class="card">
                        <div class="card-body">
							<form autocomplete="off" data-res_function="resHavalaEntry" id="saveHavalaEntry">
								<div class="col-md-12">

									<div class="hiddenInput">
										<input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id)?$dataRow->id:"")?>">
										<input type="hidden" name="entry_type" id="entry_type" value="<?=$entry_type?>">
										<input type="hidden" name="order_type" id="order_type" value="HAVALA">
									</div>

									<div class="row form-group">

										<div class="col-md-2 form-group <?=($this->cm_id_count == 1)?"hidden":""?>">
                                            <label for="cm_id">Select Unit</label>
                                            <select name="cm_id" id="cm_id" class="form-control" data-selected_cm_id="<?=(!empty($dataRow->cm_id))?$dataRow->cm_id:""?>">
                                                <?=getCompanyListOptions($companyList,((!empty($dataRow->cm_id))?$dataRow->cm_id:""))?>
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <label for="trans_number">Journal No.</label>

                                            <div class="input-group">
                                                <input type="text" name="trans_prefix" id="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:((!empty($trans_prefix))?$trans_prefix:"")?>">
                                                <input type="text" name="trans_no" id="trans_no" class="form-control numericOnly" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:((!empty($trans_no))?$trans_no:"")?>">
                                            </div>

                                            <input type="hidden" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:((!empty($trans_number))?$trans_number:"")?>" readonly>
                                        </div>

										<div class="col-md-3">
											<label for="trans_date">Journal Date</label>
                                            <input type="date" id="trans_date" name="trans_date" class="form-control fyDates req" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>" />
										</div>

                                        <div class="col-md-5 form-group">
                                            <label for="opp_acc_id">Ledger Name</label>
                                            <select name="opp_acc_id" id="opp_acc_id" class="form-control basic-select2">
                                                <option value="">Select Ledger</option>
                                                <?=getPartyListOption($ledgerList,((!empty($dataRow->party_id))?$dataRow->party_id:""))?>
                                            </select>
                                        </div>
									</div>
								</div>

								<hr>

								<div class="col-md-12 row">
									<div class="col-md-6">
										<h4>Havala Details : </h4>
									</div>
								</div>
								<div class="col-md-12 mt-3">
									<div class="error item_name_error"></div>
									<div class="row form-group">
										<div class="table-responsive ">
											<table id="havalaEntryData" class="table table-striped table-borderless" >
												<thead class="thead-dark">
													<tr>
														<th style="width:5%;">#</th>
														<th>Ledger</th>
														<th>CR</th>
														<th>DR</th>
														<th>Remark</th>
														<th>Action</th>
													</tr>
												</thead>
												<tbody id="tempItem" class="temp_item">
													<tr id="noData">
														<td colspan="6" class="text-center">No data available in table</td>
													</tr>
												</tbody>
                                                <tfoot class="thead-dark">
                                                    <tr>
                                                        <th colspan="2" class="font-bold">Total</th>
                                                        <th id="total_cr_amount" class="font-bold">0.00</th>
                                                        <th id="total_dr_amount" class="font-bold">0.00</th>
                                                        <th colspan="2"></th>
                                                    </tr>
                                                    <tr id="itemForm">
                                                        <td colspan="2" style="width:30%;">
                                                            <div id="itemInputs">
                                                                <input type="hidden" id="id" class="itemFormInput" value="" />		
                                                                <input type="hidden" id="row_index" class="itemFormInput" value="">
                                                                <input type="hidden" id="ledger_name" class="itemFormInput" value="" />
                                                            </div>

                                                            <select id="acc_id" class="form-control basic-select2 partyDetails itemFormInput req" data-res_function="resPartyDetail">
                                                                <option value="">Select Ledger</option>
                                                                <?=getPartyListOption($ledgerList)?>
                                                            </select>
                                                        </td>

                                                        <td style="width:20%;">
                                                            <div class="input-group">
                                                                <select id="cr_dr" class="form-control itemFormInput" style="width:40%;">
                                                                    <option value="CR">Credit</option>
                                                                    <option value="DR">Debit</option>
                                                                </select>
                                                                <input type="text" id="price" class="form-control floatOnly itemFormInput" value="0" style="width:60%;">
                                                            </div>
                                                        </td>

                                                        <td colspan="2">
                                                            <input type="text" id="item_remark" class="form-control itemFormInput" value="">
                                                        </td>

                                                        <td class="text-center">
                                                            <button type="button" class="btn waves-effect waves-light btn-outline-success saveItem btn-save" data-fn="save"><i class="fa fa-check"></i></button>
                                                        </td>
                                                    </tr>
                                                </tfoot>
											</table>
										</div>
									</div>
								</div>
							
							</form>
						</div>
                        <div class="card-footer bg-facebook">
                            <div class="col-md-12"> 
                                <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'saveHavalaEntry','fnsave':'saveHavalaEntry'});" ><i class="fa fa-check"></i> Save </button>

                                <button type="button" class="btn btn-secondary press-close-btn btn-close-modal save-form float-right m-r-10" onclick="window.location.href='<?=base_url($headData->controller)?>'"><i class="fa fa-times"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>

<script>
$(document).ready(function(){
    calculateCRDR();

    $(document).on('click', '.saveItem', function () {
        var formData = {};
        $.each($(".itemFormInput"),function(i, v) {
            formData[$(this).attr("id")] = $(this).val();
        });

        $("#itemForm .error").html("");

        if (formData.acc_id == "") {
            $(".acc_id").html("Ledger is required.");
        }
        if (formData.cr_dr == "") {
            $(".cr_dr").html("CR DR is required.");
        }
        if (formData.price == "" || formData.price == "0") {
            $(".price").html("Amount is required.");
        }

        var accIds = $(".accIds").map(function () { return $(this).val(); }).get();
        if ($.inArray(formData.acc_id, accIds) >= 0 && formData.row_index == "") {
            $(".acc_id").html("Ledger already added.");
        }

        var errorCount = $('#itemForm .error:not(:empty)').length;
        if (errorCount == 0) {
            var amount = formData.price;
            formData.credit_amount = (formData.cr_dr == 'CR') ? amount : 0;
            formData.debit_amount = (formData.cr_dr == 'DR') ? amount : 0;
            formData.p_or_m = (formData.cr_dr == 'DR') ? -1 : 1;
            AddRow(formData);

            $("#itemForm .error").html('');
            $.each($('.itemFormInput'),function(){ $(this).val(""); });
            $("#itemForm #cr_dr").val(formData.cr_dr);

            initSelect2();
            $("#itemForm #acc_id").focus();
        }
    });

    $("#cm_id").val(($("#company_id :selected").val() || 1));
	setTimeout(function(){$("#cm_id").trigger('change');},500);
	
	old_no = $('#trans_no').val();
	old_prefix = $('#trans_prefix').val();
	$(document).on('change','#cm_id',function(){
		var entry_type = $("#entry_type").val();
		var cm_id = $(this).val();		
		var selected_cm_id = $(this).data('selected_cm_id');
		var append_id = $(this).data('append_id') || "trans_number";		

		if(selected_cm_id == cm_id){
			$('#trans_no').val(old_no);
			$('#trans_prefix').val(old_prefix);
			$('#'+append_id).val(old_prefix+old_no);
		}else{
			$.ajax({
				url : base_url + controller + '/getNextTransNo',
				type : 'post',
				data : {cm_id : cm_id, entry_type : entry_type},
				dataType : 'json'
			}).done(function(response){
				$('#trans_no').val(response.next_no);
				$('#trans_prefix').val(old_prefix);
				$('#'+append_id).val(old_prefix+response.next_no);
			});
		}
	});
});

var itemCount = 0;
function AddRow(data) {
	var tblName = "havalaEntryData";

	//Remove blank line.
	$('table#'+tblName+' tr#noData').remove();

	//Get the reference of the Table's TBODY element.
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

	var accIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][acc_id]", value: data.acc_id, class:'accIds' });
	var ledgerNameInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][ledger_name]", value: data.ledger_name });
	var priceInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][price]", value: data.price });
	var transIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][id]", value: data.id });
	cell = $(row.insertCell(-1));
	cell.html(data.ledger_name);
	cell.append(accIdInput);
	cell.append(ledgerNameInput);
	cell.append(priceInput);
	cell.append(transIdInput);

	var crDrInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][cr_dr]", value: data.cr_dr });
	var creditInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][credit_amount]", value: data.credit_amount, class:'credit_amount' });
	var priceErrorDiv = $("<div></div>", { class: "error price" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.credit_amount);
	cell.append(creditInput);
	cell.append(crDrInput);
	cell.append(priceErrorDiv);

	var debitInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][debit_amount]", value: data.debit_amount, class:'debit_amount' });
	cell = $(row.insertCell(-1));
	cell.html(data.debit_amount);
	cell.append(debitInput);

	var itemRemarkInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_remark]", value: data.item_remark });
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

	calculateCRDR();
	itemCount++;
}

function Edit(data, button) {
	var row_index = $(button).closest("tr").index();
	$.each(data, function (key, value) { $("#itemForm #" + key).val(value); });
	$("#itemForm #row_index").val(row_index);
	initSelect2();
    $("#itemForm #acc_id").focus();
}

function Remove(button) {
    var tableId = "havalaEntryData";
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#"+tableId)[0];
	table.deleteRow(row[0].rowIndex);
	$('#'+tableId+' tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#'+tableId+' tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="6" align="center">No data available in table</td></tr>');
	}

	calculateCRDR();
};

function calculateCRDR() {
	var creditAmountArray = $(".credit_amount").map(function () { return $(this).val(); }).get();
	var total_cr_amount = 0;
	$.each(creditAmountArray, function () { total_cr_amount += parseFloat(this) || 0; });

	var debitAmountArray = $(".debit_amount").map(function () { return $(this).val(); }).get();
	var total_dr_amount = 0;
	$.each(debitAmountArray, function () { total_dr_amount += parseFloat(this) || 0; });


	$("#total_cr_amount").html(total_cr_amount.toFixed(2));
	$("#total_dr_amount").html(total_dr_amount.toFixed(2));

	var difference = 0;
	difference = parseFloat(parseFloat(total_cr_amount) - parseFloat(total_dr_amount)).toFixed(2);
	difference = Math.abs(difference);
	$("#difference").html(difference);
}

function resPartyDetail(response = ""){
    if(response != ""){
        var partyDetail = response.data.partyDetail;
        $("#ledger_name").val(partyDetail.party_name);        
    }else{
        $("#ledger_name").val("");
    }
}

function resHavalaEntry(data,formId){
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
</script>

<?php
if(!empty($dataRow->ledgerData)):
    foreach($dataRow->ledgerData as $row):
        if($row->vou_acc_id != $dataRow->party_id):
            $row->row_index = "";
            $row->price = $row->amount;
            $row->cr_dr = $row->c_or_d;
            $row->credit_amount = ($row->c_or_d=='CR') ? $row->amount : 0;
            $row->debit_amount = ($row->c_or_d=='DR') ? $row->amount : 0;
            $row->item_remark = $row->remark;
            echo '<script>AddRow('.json_encode($row).');</script>';
        endif;
    endforeach;
endif;
?>