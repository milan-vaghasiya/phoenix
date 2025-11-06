<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="<?=$dataRow->id ?? ''?>">

            <div class="col-md-12 form-group">
                <label for="project_name">Project Name</label>
                <input type="text" class="form-control req" name="project_name" id="project_name" value="<?=$dataRow->project_name??''?>">
            </div>

            <div class="col-md-12 form-group">
                <label for="party_id">Customer Name</label>
                <select name="party_id" id="party_id" class="form-control basic-select2">
                    <option value="0">Own Project</option>
                    <?=getPartyListOption($partyList,($dataRow->party_id??""))?>
                </select>
            </div>
			
			<div class="col-md-12 form-group">
                <label for="consultant">Consultant Name</label>
                <input type="text" class="form-control" name="consultant" id="consultant" value="<?=$dataRow->consultant??''?>">
            </div>

            <div class="col-md-6 form-group">
                <label for="project_type">Project Type</label>
                <select name="project_type" id="project_type" class="form-control basic-select2 req">
                    <option value="">Select Project Type</option>
                    <?php
                        foreach($projectTypeList as $row):
                            $selected = (!empty($dataRow->project_type) && $dataRow->project_type == $row->detail)?"selected":"";
                            echo '<option value="'.$row->detail.'" '.$selected.'>'.$row->detail.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="shift_id">Shift</label>
                <select name="shift_id" id="shift_id" class="form-control basic-select2 req">
                    <option value="">Select Project Type</option>
                    <?php
                        foreach($shiftList as $row):
                            $selected = (!empty($dataRow->shift_id) && $dataRow->shift_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->shift_name.' ('.$row->shift_start.')</option>';
                        endforeach;
                    ?>
                </select>
            </div>

			<div class="col-md-4 form-group">
                <label for="work_size">Work Size (Feet)</label>
                <input type="text" class="form-control floatOnly" name="work_size" id="work_size" value="<?=$dataRow->work_size??''?>">
            </div>
			
            <div class="col-md-4 form-group">
                <label for="cost_type">Cost Type</label>
                <select name="cost_type" id="cost_type" class="form-control">
                    <option value="1" <?=(!empty($dataRow->cost_type) && $dataRow->cost_type == 1)?"selected":""?>>Fixed</option>
                    <option value="2" <?=(!empty($dataRow->cost_type) && $dataRow->cost_type == 2)?"selected":""?>>Per Feet</option>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="amount">Amount</label>
                <input type="text" class="form-control floatOnly req" name="amount" id="amount" value="<?=$dataRow->amount??''?>">
            </div>

            <div class="col-md-12 form-group">
                <label for="remark">Note</label>
                <textarea name="remark" id="remark" class="form-control" rows="4" cols="50"><?=$dataRow->remark??''?></textarea>
            </div>

            <div class="col-md-6 form-group">
                <label for="lat">Latitude</label>
                <input type="text" class="form-control floatOnly" name="lat" id="lat" value="<?=$dataRow->lat??''?>" >
            </div>

            <div class="col-md-6 form-group">
                <label for="lng">Longitude</label>
                <input type="text" class="form-control floatOnly" name="lng" id="lng" value="<?=$dataRow->lng??''?>" >
            </div>
			
            <div class="col-md-12 form-group">
                <label for="site_add">Address</label>
                <textarea name="site_add" id="site_add" class="form-control" rows="3" cols="50"><?=$dataRow->site_add??''?></textarea>
            </div>
        </div>
    </div>
</form>