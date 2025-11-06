<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="<?=$dataRow->id ?? ''?>">
			
            <div class="col-md-6 form-group">
                <label for="trans_date">Date</label>
                <input type="date" class="form-control req" name="trans_date" id="trans_date" value="<?=($dataRow->trans_date??date('Y-m-d'))?>">
            </div>
			<div class="col-md-6 form-group">
                <label for="shift">Shift</label>
                <select name="shift" id="shift" class="form-control">
                    <option value="Day" <?=((!empty($dataRow->shift) && $dataRow->shift == 'Day')?'selected':'')?>>Day</option>
                    <option value="Night" <?=((!empty($dataRow->shift) && $dataRow->shift == 'Night')?'selected':'')?>>Night</option>
                </select>
            </div>
			
            <div class="col-md-12 form-group">
                <label for="project_id">Project</label>
                <select name="project_id" id="project_id" class="form-control basic-select2 req">
                    <option value="">Select Project</option>
                    <?php
                        foreach($projectList as $row):
                            $selected = (!empty($dataRow->project_id) && $dataRow->project_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->project_name.' ('.$row->party_name.')</option>';
                        endforeach;
                    ?>
                </select>
            </div>
			
			<hr>
			<h6>Agency Attendace:</h6>
			<div class="col-md-12 form-group">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:60%;">Agency</th>
                            <th style="width:20%;">Male</th>
                            <th style="width:20%;">Female</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyAgency">
					
					</tbody>
                </table>
            </div>
			
			<hr>
			<h6>Labor Attendace:</h6>
			<div class="col-md-12 form-group">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th>Labor Category</th>
                            <th>NO Of Labor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($laborCatList)){
								$i=0;
                                foreach($laborCatList AS $row){ 
						?>
                                    <tr>
                                        <th><?=$row->detail?></th>
                                        <th>
											<input type="hidden" name="staff[<?=$i?>][labor_category]" class="form-control" value="<?=$row->detail?>">
                                            <input type="text" name="staff[<?=$i?>][present]" class="form-control numericOnly" value="">
                                        </th>
                                    </tr>
						<?php $i++;	
								}
                            } 
						?>
                    </tbody>
                </table>
            </div>
        
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
	
	<?php if(!empty($dataRow->id)) { ?>
		setTimeout(() => {
			$("#project_id").trigger("change");
		}, 50);  
    
	<?php } ?>
	
    $(document).on('change','#project_id',function(){
        var project_id = $(this).val();
		let sendData = {project_id:project_id,tbody:1};
        $.ajax({
			url : base_url + controller + '/getProjectAgency',
			type:'post',
			data: sendData,
			dataType : 'json',
		}).done(function(response){
			$("#tbodyAgency").html("");
			$("#tbodyAgency").html(response.tbody);
		});
    });
});
</script>