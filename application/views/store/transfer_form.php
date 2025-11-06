<form>
    <div class="col-md-12">
        <div class="row">            

            <div class="col-md-3 form-group">
                <label for="trans_number">Transfer No.</label>
                <div class="input-group">
                    <input type="text" name="trans_number" class="form-control" value="<?= $trans_number ?>" readOnly />
                    <input type="hidden" name="trans_no" value="<?= $trans_no ?>" readOnly />
                    <input type="hidden" name="id" value="" />
                </div>
            </div>
            
            <div class="col-md-3 form-group">
                <label for="trans_date"> Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date("Y-m-d")?>">
            </div>

            <div class="col-md-6 form-group">
				<label for="agency_id">Agency</label>
				<select name="agency_id" id="agency_id" class="form-control basic-select2">
					<option value="">Select Agency</option>
					<?=getPartyListOption($partyList)?>
				</select>
			</div>
            
            <div class="col-md-6 form-group">
                <label for="from_project_id">From Project</label>
                <select name="from_project_id" id="from_project_id" class="form-control basic-select2 req getStockData">
                    <option value="">Select Project</option>
                    <?php
                        if (!empty($projectList)) {
                            foreach ($projectList as $row) {
                                echo "<option value='".$row->id."'>".$row->project_name."</option>";
                            }
                        }
                    ?>
                </select>
            </div>          
            
            <div class="col-md-6 form-group">
                <label for="to_project_id">To Project</label>
                <select name="to_project_id" id="to_project_id" class="form-control basic-select2 req">
                    <option value="">Select Project</option>
                    <?php
                        if(!empty($projectList)){
                            foreach ($projectList as $row) {
                                echo "<option value='".$row->id."' >".$row->project_name."</option>";
                            }
                        }                            
                    ?>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="item_id">Product</label>
                <div class="float-right"><a class="text-primary font-bold " href="javascript:void(0)" id="stock_qty">Stock</a></div>
                <select name="item_id" id="item_id" class="form-control basic-select2 req getStockData">
                    <option value="">Select Product</option>
                    <?php
                        if(!empty($itemList)){
                            foreach ($itemList as $row) {
                                echo "<option value='".$row->id."'  >".$row->item_name."</option>";
                            }
                        }                            
                    ?>
                </select>
                <div class="error item_err"></div>
            </div>
             
            <div class="col-md-4 form-group">
                <label for="qty">Transfer Qty</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly req" >
            </div>
            
            <div class="col-md-4 form-group">
                <label for="issued_to">Issued To</label>
                <input type="text" name="issued_to" id="issued_to" class="form-control" value="">
            </div>
            
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" class="form-control"></textarea>
            </div>

        </div>
    </div>
</form>
<script>    
$(document).ready(function(){
    $(document).on('change', '.getStockData', function (e) {
        e.stopImmediatePropagation();
        e.preventDefault();

        var item_id = $("#item_id").val();
        var from_project_id = $("#from_project_id :selected").val();  
        $("#qty").val(""); 
        if (item_id != '' && from_project_id != '') {
            $.ajax({
                url: base_url + controller + "/getItemStock",
                type: 'post',
                data: { item_id: item_id, location_id: from_project_id },
                dataType: 'json',
                success: function(data) {
                    $("#stock_qty").html('Stock : ' + data.stock_qty);
                }
            });
        }
    });
});
</script>