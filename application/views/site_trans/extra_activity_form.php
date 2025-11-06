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
			
            <div class="col-md-12 form-group">
                <label for="activity">Activity</label>
                <textarea name="activity" id="activity" class="form-control" rows="4"><?=($dataRow->activity??'')?></textarea>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
	
});
</script>