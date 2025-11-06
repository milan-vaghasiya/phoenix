<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="<?=$dataRow->id ?? ''?>">
			
            <div class="col-md-12 form-group">
                <label for="trans_date">Date</label>
                <input type="date" class="form-control req" name="trans_date" id="trans_date" value="<?=($dataRow->trans_date??date('Y-m-d'))?>">
            </div>
			
            <div class="col-md-12 form-group">
                <label for="project_id">Project</label>
                <select name="project_id" id="project_id" class="form-control basic-select2 req" data-tower_name="<?=($dataRow->tower_name??$dataRow->tower_name)?>">
                    <option value="">Select Project</option>
                    <?php
                        foreach($projectList as $row):
                            $selected = (!empty($dataRow->project_id) && $dataRow->project_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->project_name.' ('.$row->party_name.')</option>';
                        endforeach;
                    ?>
                </select>
            </div>
			
            <div class="col-md-6 form-group">
                <label for="tower_name">Tower/Block</label>
                <select name="tower_name" id="tower_name" class="form-control basic-select2 req">
                    <option value="">Select Tower/Block</option>
                </select>
            </div>
			
			<div class="col-md-6 form-group">
                <label for="type">Work Type</label>
                <select name="type" id="work_type" class="form-control basic-select2 req">
                    <option value="1" <?=((!empty($dataRow->type) && $dataRow->type == 1)?"selected":"")?>>Work Done</option>
                    <option value="2" <?=((!empty($dataRow->type) && $dataRow->type == 2)?"selected":"")?>>Work Plan</option>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="work_detail">Work Detail</label>
                <textarea name="work_detail" id="work_detail" class="form-control" rows="2"><?=($dataRow->work_detail??'')?></textarea>
            </div>

            <div class="col-md-6 form-group work_type">
                <label for="execution">Work Executed</label>
                <input type="text" name="execution" id="execution" class="form-control" value="<?=($dataRow->execution??'')?>">
            </div>

            <div class="col-md-6 form-group work_type">
                <label for="uom">UOM</label>
                <input type="text" name="uom" id="uom" class="form-control" value="<?=($dataRow->uom??'')?>">
            </div>
            <div class="col-md-12 form-group work_type">
                <label for="remark">Note</label>
				<input type="text" name="remark" id="remark" class="form-control" value="<?=($dataRow->remark??'')?>">
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

		setTimeout(() => {
			$("#work_type").trigger("change");
		}, 50);		
    
	<?php } ?>
	
    $(document).on('change','#project_id',function(){
        var project_id = $(this).val();
		var tower_name = $(this).data('tower_name');
		let sendData = {project_id:project_id,tower_name:tower_name};
        $.ajax({
			url : base_url + controller + '/getProjectTower',
			type:'post',
			data: sendData,
			dataType : 'json',
		}).done(function(response){
			$("#tower_name").html(response.options);initSelect2();
		});
    });
	
	$(document).on('change','#work_type',function(){
        var work_type = $(this).val();
		if(work_type==1){
			$(".work_type").show();
		}else{
			$(".work_type").hide();
		}
    });
});
</script>