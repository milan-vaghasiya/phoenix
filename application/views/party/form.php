<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
			<input type="hidden" name="party_type" value="<?=(!empty($dataRow->party_type))?$dataRow->party_type:$party_type?>" />
			<input type="hidden" name="country" value="<?=(!empty($dataRow->country))?$dataRow->country:"India"?>" />
			<input type="hidden" name="party_code" id="party_code" value="<?= (!empty($dataRow->party_code)) ?$dataRow->party_code : ($party_code??''); ?>"  />
			<input type="hidden" name="party_category" id="party_category" value="<?= (!empty($dataRow->party_category)) ?$dataRow->party_category : ($party_category??''); ?>"  />
			<input type="hidden" name="group_code" id="group_code" value="<?= (!empty($dataRow->group_code)) ?$dataRow->group_code : ($group_code??'SD'); ?>"  />

            <div class="col-md-6 form-group">
                <label for="party_name">Company/Trade Name</label>
                <input type="text" name="party_name" id="party_name" class="form-control text-capitalize req" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""; ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" class="form-control text-capitalize" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="party_phone">Phone No.</label>
                <input type="text" name="party_phone" class="form-control numericOnly" value="<?=(!empty($dataRow->party_phone))?$dataRow->party_phone:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="whatsapp_no">Whatsapp No.</label>
                <input type="text" name="whatsapp_no" class="form-control numericOnly" value="<?=(!empty($dataRow->whatsapp_no))?$dataRow->whatsapp_no:""?>" />
            </div>
			
            <div class="col-md-3 form-group">
                <label for="party_email">Party Email</label>
                <input type="email" name="party_email" class="form-control" value="<?=(!empty($dataRow->party_email))?$dataRow->party_email:""; ?>" />
            </div> 
			
			<div class="col-md-3 form-group">
                <label for="credit_days">Credit Days</label>
                <input type="text" name="credit_days" class="form-control numericOnly" value="<?=(!empty($dataRow->credit_days))?$dataRow->credit_days:""?>" />
            </div>
			
            <div class="col-md-3 form-group">
                <label for="business_type">Business Type</label>
                <select name="business_type" id="business_type" class="form-control select2">
                    <?php
                        foreach($this->businessTypes as $key=>$value):
                            $selected = (!empty($dataRow->business_type) && $dataRow->business_type == $value)?"selected":"";
                            echo '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            
			<div class="col-md-3 form-group">
                <label for="gstin">GSTIN</label>
                <input type="text" name="gstin" id="gstin" class="form-control text-uppercase" value="<?=(!empty($dataRow->gstin))?$dataRow->gstin:""; ?>" />
            </div>	  
			
			<div class="col-md-3 form-group">
                <label for="pan_no">PAN No.</label>
                <input type="text" name="pan_no" id="pan_no" class="form-control req text-uppercase" value="<?=(!empty($dataRow->pan_no))?$dataRow->pan_no:""; ?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="state">Select State</label>
                <select name="state" id="state" class="form-control state_list basic-select2 req" data-city="city" data-selected_city="<?=(!empty($dataRow->city))?$dataRow->city:""?>">
                    <option value="">Select State</option>
					<?php
						foreach($stateData as $row):
							$selected = (!empty($dataRow->state) && $dataRow->state == $row->name)?"selected":((empty($dataRow) && $row->name == "Gujarat")?"selected":"");
							echo '<option value="'.$row->name.'" '.$selected.' data-state_id="'.$row->id.'">'.$row->name.'</option>';
						endforeach;
                    ?>
                </select>
            </div>
            
            <div class="col-md-3 form-group">
                <label for="city">Select District</label>
                <select name="city" id="city" class="form-control basic-select2 req">
                    <option value="">Select District</option>
                </select>
            </div> 
            
            <div class="col-md-12 form-group">
                <label for="party_address">Address</label>
                <textarea name="party_address" id="party_address" class="form-control req" rows="2"><?=(!empty($dataRow->party_address))?$dataRow->party_address:""?></textarea>
            </div>
        </div>        
    </div>
</form>
<script>
$(document).ready(function(){    
    setTimeout(function(){
        $("#state").trigger('change');
    },500);
	
	$(document).on('change',".state_list",function(e){
        e.stopImmediatePropagation();e.preventDefault();
		var state = $(this).val();
		var state_id =$(this).find(":selected").data('state_id');
		var selected_city = $(this).data('selected_city') || "";
		if(id == ""){
			$("#city").html('<option value="">Select District</option>');
			//$("#"+city_id).select2();//.comboSelect();
		}else{
			$.ajax({
				url: base_url + 'parties/getCitiesOptions',
				type:'post',
				data:{state:state,state_id:state_id},
				dataType:'json',
				success:function(data){
					$("#city").html(data.result);
					if(selected_city != ""){
						$("#city").val(selected_city);
						initSelect2();
						
					}else{
					}					
				}
			});
		}
		
	});
/*
    $('#village_name').typeahead({
		source: function(query, result){
			$.ajax({
				url:base_url + 'parties/getVillageList',
				method:"POST",
				global:false,
				data:{villange_name:query,country:$("#country :selected").val(),state:$("#state :selected").val(),city:$("#city :selected").val()},
				dataType:"json",
				success:function(data){
					result($.map(data, function(row){ return {name:row.name,id:row.id,country:row.country,state:row.state,city:row.city}; }));
				}
			});
		},
		updater: function(item) {
            if(item.city != ""){
                $("#city").val(item.city || "");
                initSelect2();
            }            
			return item;
        }
	});*/
});
</script>