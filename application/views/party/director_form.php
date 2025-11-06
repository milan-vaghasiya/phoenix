<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
			<input type="hidden" name="party_type" value="<?=(!empty($dataRow->party_type))?$dataRow->party_type:$party_type?>" />
			<input type="hidden" name="party_category" id="party_category" value="<?= (!empty($dataRow->party_category)) ?$dataRow->party_category : ($party_category??''); ?>"  />
			<input type="hidden" name="group_code" id="group_code" value="<?= (!empty($dataRow->group_code) ?$dataRow->group_code : 'CP'); ?>"  />

            <div class="col-md-12 form-group">
                <label for="party_name">Director Name</label>
                <input type="text" name="party_name" id="party_name" class="form-control text-capitalize req" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""; ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="party_phone">Phone No.</label>
                <input type="text" name="party_phone" class="form-control numericOnly req" value="<?=(!empty($dataRow->party_phone))?$dataRow->party_phone:""?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="whatsapp_no">Whatsapp No.</label>
                <input type="text" name="whatsapp_no" class="form-control numericOnly" value="<?=(!empty($dataRow->whatsapp_no))?$dataRow->whatsapp_no:""?>" />
            </div>
			
            <div class="col-md-12 form-group">
                <label for="party_email">Party Email</label>
                <input type="email" name="party_email" class="form-control" value="<?=(!empty($dataRow->party_email))?$dataRow->party_email:""; ?>" />
            </div>
        </div>        
    </div>
</form>