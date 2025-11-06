<form>
    <div class="col-md-12">
        <div class="error item_error"></div>
        <div class="row">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="p_or_m" id="p_or_m" value="1">
            <input type="hidden" name="batch_no" id="batch_no" value="GB">
            <input type="hidden" name="unique_id" id="unique_id" value="0">
            <input type="hidden" id="packing_qty" value="" />
            <input type="hidden" id="packing_unit_qty" value="" />            

            <div class="col-md-<?=($this->cm_id_count > 1)?"6":"4"?> form-group">
                <label for="ref_date">Date</label>
                <input type="date" name="ref_date" id="ref_date" class="form-control fyDates" value="<?=getFyDate()?>">
            </div>

            <div class="col-md-6 form-group <?=($this->cm_id_count == 1)?"hidden":""?>">
                <label for="cm_id">Select Unit</label>
                <select name="cm_id" id="cm_id" class="form-control" data-selected_cm_id="<?=(!empty($dataRow->cm_id))?$dataRow->cm_id:""?>">
                    <?=getCompanyListOptions($companyList,((!empty($dataRow->cm_id))?$dataRow->cm_id:""))?>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="stock_entry_type">Entry Type</label>
                <select name="stock_entry_type" id="stock_entry_type" class="form-control basic-select2 itemDetails req" data-res_function="resItemDetail">
                    <option value="1">Stock Journal</option>
                    <option value="2">Stock Deduction</option>
                    <!--<option value="3">Stock Addition</option>-->
                </select>
				
            </div>

            <div class="col-md-12 form-group">
                <label for="from_item_id">From Item Name</label>
                <small class="float-right">Current Stock : <span id="current_stock">0</span></small>
                <select name="from_item_id" id="from_item_id" class="form-control basic-select2 itemDetails req" data-res_function="resItemDetail">
                    <option value="">Select From Item</option>
                    <?=getItemListOption($itemList)?>
                </select>
				
            </div>

            <div class="col-md-12 form-group">
                <label for="to_item_id">To Item Name</label>
                <select name="to_item_id" id="to_item_id" class="form-control basic-select2 itemDetails req" data-res_function="resItemDetail">
                    <option value="">Select To Item</option>
                    <?=getItemListOption($itemList)?>
                </select>               
            </div>
            
            <div class="col-md-4 form-group">
                <label for="total_box">Cartoon Qty</label>
                <input type="text" name="total_box" id="total_box" class="form-control floatOnly calculateQty req" value="" readOnly>
            </div>

            <div class="col-md-4 form-group">
                <label for="strip_qty">Strip Qty</label>
                <input type="text" name="strip_qty" id="strip_qty" class="form-control floatOnly calculateQty req" value="">
            </div>

            <div class="col-md-4 form-group">
                <label for="qty">Total Qty</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly calculateQty req" value="" readOnly>
            </div>
			
			<div class="col-md-4 form-group">
                <label for="price">Price</label>
                <input type="text" name="price" id="price" class="form-control floatOnly req" value="">
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $("#cm_id").val(($("#company_id :selected").val() || 1));
    
    $(document).on('change','#unique_id',function(){
        if($(this).find(":selected").val() != ""){
            $("#batch_no").val($(this).find(":selected").text());
        }else{
            $("#batch_no").val("");
        }
    });

    $(document).on('change keyup','.calculateQty',function(){
		var packing_qty = $("#packing_qty").val() || 0;
		var packing_unit_qty = $("#packing_unit_qty").val() || 0;
		var total_box = 0,strip_qty = 0,total_qty = 0;

		if($(this).attr('id') == "total_box"){
			total_box = $("#total_box").val() || 0;
			strip_qty = parseFloat(parseFloat(total_box) * parseFloat(packing_qty)).toFixed(2);
			total_qty = parseFloat(parseFloat(strip_qty) * parseFloat(packing_unit_qty)).toFixed(2);

			$("#strip_qty").val(strip_qty);
			$("#qty").val(total_qty);
		}

		if($(this).attr('id') == "strip_qty"){
			strip_qty = $("#strip_qty").val() || 0;
			if(parseFloat(packing_qty) > 0){
				total_box = parseFloat(parseFloat(strip_qty) / parseFloat(packing_qty)).toFixed(2);
			}
			total_qty = parseFloat(parseFloat(strip_qty) * parseFloat(packing_unit_qty)).toFixed(2);

			$("#total_box").val(total_box);
			$("#qty").val(total_qty);
		}

		if($(this).attr('id') == "qty"){
			total_qty = $("#qty").val() || 0;
			if(parseFloat(packing_qty) > 0 && parseFloat(packing_unit_qty) > 0){
				strip_qty = parseFloat(parseFloat(total_qty) / parseFloat(packing_unit_qty)).toFixed(2);
				total_box = parseFloat(parseFloat(strip_qty) / parseFloat(packing_qty)).toFixed(2);
			}

			$("#strip_qty").val(strip_qty);
			$("#total_box").val(total_box);
		}		
	});

	$(document).on('change','#from_item_id',function(){
		var item_id = $(this).val();
		$('#current_stock').html("0");
		$.ajax({
			url : base_url + controller + '/getItemCurrentStock',
			type : 'post',
			data : {item_id : item_id},
			dataType : 'json'
		}).done(function(response){
			$('#current_stock').html(response.qty);
		});
	});
});
function resItemDetail(response=""){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        $("#packing_qty").val(itemDetail.packing_qty);
		$("#packing_unit_qty").val(itemDetail.packing_unit_qty);
    }else{
        $("#packing_qty").val("");
		$("#packing_unit_qty").val("");
    }
}
</script>