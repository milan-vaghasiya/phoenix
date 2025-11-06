<form>
    <div class="col-md-12">
		<div class="row">
			<input type="hidden" name="id" id="id" value="<?=$project_id??''?>"/>

			<div class="col-md-12 form-group">
				<label for="machine_ids">Machine</label> <span class="text-danger">*</span>
				<select id="machineSelect" data-input_id="machine_ids" class="form-control jp_multiselect req"  multiple="multiple" >
					<?php 
						foreach($machineList as $row):
							$selected = '';
                            if (!empty($dataRow->machine_ids)):
                                if(in_array($row->id, explode(',', $dataRow->machine_ids))):
                                    $selected = "selected";
                                endif;
                            endif;
							echo '<option value="'.$row->id.'" ' . $selected . '>'.$row->machine_name.'</option>';
						endforeach;
					?>
				</select>
                <input type="hidden" name="machine_ids" id="machine_ids" value="<?= (!empty($dataRow->machine_ids)) ? $dataRow->machine_ids : "" ?>" />
                <div class="error machine_ids"></div>
			</div>
		</div>
	</div>
</form>