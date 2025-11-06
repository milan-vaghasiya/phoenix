<form>
	<div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
			<div class="col-md-12 form-group">
				<label for='name' class="control-label">Department Name</label>
				<input type="text" id="name" name="name" class="form-control req" value="<?=(!empty($dataRow->name))?$dataRow->name:""?>">		
			</div>
			<div class="col-md-12 form-group">
				<label for='remark' class="control-label">Remark</label>
				<input type="text" id="remark" name="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">		
			</div>
		</div>
	</div>	
</form>
            
