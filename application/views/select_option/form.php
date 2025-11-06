<form>
    <div class="col-md-12">
        <div class="row">            
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id) ? $dataRow->id : "")?>" />
            <input type="hidden" name="type" id="type" value="<?=(!empty($dataRow->type) ? $dataRow->type : $type)?>" />
			
            <div class="col-md-12 form-group">
                <label for="detail">Option</label>
                <input type="text" name="detail" id="detail" class="form-control req" value="<?=(!empty($dataRow->detail) ? $dataRow->detail : "")?>" />
            </div>
			
			<?php
				$op_type = (!empty($dataRow->type) ? $dataRow->type : $type);
				if($op_type==4):
			?>
				<div class="col-md-12 from-group">
					<label for="labor_cat_ids">Labor Category</label>
					<select name="labor_cat_ids[]" id="labor_cat_ids" class="form-control basic-select2" multiple>
						<?php
						if(!empty($laborCateList)):
							foreach($laborCateList as $row):
								$selected = (!empty($dataRow->labor_cat_ids) && (in_array($row->id,  explode(',', $dataRow->labor_cat_ids)))) ? "selected" : "";
								echo '<option value="'.$row->id.'" '.$selected.'>'.$row->detail.'</option>';
							endforeach;
						endif;
						?>
					</select>
				</div>
			<?php endif; ?>
			
			<div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" class="form-control"><?=(!empty($dataRow->remark) ? $dataRow->remark : "")?></textarea>
            </div>
        </div>
    </div>
</form>