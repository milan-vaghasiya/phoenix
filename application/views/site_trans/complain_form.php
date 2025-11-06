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
                <select name="project_id" id="project_id" class="form-control basic-select2 req" data-agency_id="<?=($dataRow->agency_id??$dataRow->agency_id)?>">
                    <option value="">Select Project</option>
                    <?php
                        foreach($projectList as $row):
                            $selected = (!empty($dataRow->project_id) && $dataRow->project_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->project_name.' ('.$row->party_name.')</option>';
                        endforeach;
                    ?>
                </select>
            </div>
			
            <div class="col-md-12 form-group">
                <label for="agency_id">Agency</label>
                <select name="agency_id" id="agency_id" class="form-control basic-select2">
                    <option value="">Select Agency</option>
                </select>
            </div>
			
            <div class="col-md-12 form-group">
                <label for="complain_title">Complain Title</label>
                <input type="text" name="complain_title" id="complain_title" class="form-control" value="<?=($dataRow->complain_title??'')?>">
            </div>
			
            <div class="col-md-12 form-group">
                <label for="complain_note">Complain</label>
                <textarea name="complain_note" id="complain_note" class="form-control" rows="4" cols="50"><?=($dataRow->complain_note??'')?></textarea>
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
        var agency_id = $(this).data('agency_id');
		let sendData = {project_id:project_id,agency_id:agency_id};
        $.ajax({
			url : base_url + controller + '/getProjectAgency',
			type:'post',
			data: sendData,
			dataType : 'json',
		}).done(function(response){
            $("#agency_id").html(response.options);
            initSelect2();
		});
    });
});
</script>