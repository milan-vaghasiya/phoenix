<form>
    <div class="col-md-12">
        <div class="row">
            <div class="row">
                <div class="col-md-3 form-group">
                    <label for="challan_no">Issue No.</label>
                    <div class="input-group">
                        <input type="text" name="issue_number" class="form-control" value="<?= $issue_number ?>" readOnly />
                        <input type="hidden" name="issue_no" value="<?= $issue_no ?>" readOnly />
                        <input type="hidden" name="id" value="" />
                    </div>
                </div>
                <div class="col-md-3 form-group">
                    <label for="issue_date">Issue Date</label>
                    <input type="date" name="issue_date" id="issue_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date("Y-m-d")?>">
                </div>
                <div class="col-md-6 form-group">
                    <label for="vendor_id">Vendor/Agency</label>
                    <select name="vendor_id" id="vendor_id" class="form-control basic-select2">
                        <option value="">Select Vendor/Agency</option>
                        <?php
                            if(!empty($partyList)){
                                foreach ($partyList as $row) {
                                    echo "<option value='".$row->id."'>".$row->party_name."</option>";
                                }
                            }
                        ?>
                    </select>
                    <div class="error item_err"></div>
                </div>
                <div class="col-md-4 form-group">
                    <label for="project_id">Project</label>
                    <select name="project_id" id="project_id" class="form-control basic-select2 req getStock">
                        <option value="">Select Project</option>
                        <?php
                            if(!empty($projectList)){
                                foreach ($projectList as $row) {
									echo "<option value='".$row->id."' >".$row->project_name."</option>";
                                }
							}                            
                        ?>
                    </select>
                    <div class="error item_err"></div>
                </div>

                <div class="col-md-4 form-group">
                    <label for="item_id">Product</label>
                    <div class="float-right"><a class="text-primary font-bold " href="javascript:void(0)" id="stock_qty">Stock</a></div>
                    <select name="item_id" id="item_id" class="form-control basic-select2 req getStock">
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
                    <label for="issue_qty">Issue Qty</label>
                    <input type="text" name="issue_qty" id="issue_qty" class="form-control floatOnly req" >
                </div>
                <div class="col-md-12 form-group">
                    <label for="remark">Remark</label>
                    <textarea name="remark" id="remark" class="form-control"></textarea>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){

	$(document).on('change', '.getStock', function (e) {
		e.stopImmediatePropagation();
		e.preventDefault();

		var item_id = $("#item_id").val();
		var project_id = $("#project_id :selected").val();  

		$("#issue_qty").val(""); 

		if (item_id != '' && project_id != '') {
			$.ajax({
				url: base_url + controller + "/getItemStock",
				type: 'post',
				data: { item_id: item_id, location_id: project_id },
				dataType: 'json',
				success: function(data) {
					$("#stock_qty").html('Stock : ' + data.stock_qty);
				}
			});
		}
	});

});
</script>