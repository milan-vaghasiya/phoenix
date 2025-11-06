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
                <select name="project_id" id="project_id" class="form-control basic-select2 req" data-work_ref_id="<?=($dataRow->work_ref_id??'')?>" >
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
                <div class="error grn_error"></div>
                <table class="table jpExceltable">
                    <thead class="thead-info">
                        <tr>
                            <th>Machine</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($machineList)){
                                foreach($machineList AS $row){ 
									$qty = ((!empty($dataRow->machine_id) && $row->id == $dataRow->machine_id) ? $dataRow->qty : 0); ?>
                                    <tr>
                                        <th><?=$row->machine_name?></th>
                                        <th>
                                            <input type="text" name="qty[]" class="form-control numericOnly" value="<?=$qty?>">
                                        </th>
                                    </tr>
						<?php	}
                            } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>