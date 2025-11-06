<form data-res_function="resSaveAgencyWork">
    <div class="col-md-12">
		<div class="row">
			<input type="hidden" name="id" id="id" value=""/>
			<input type="hidden" name="project_id" id="project_id" value="<?=$project_id??''?>"/>
			<input type="hidden" name="work_id" id="work_id" value="<?=(!empty($work_id) ? $work_id : "")?>"/>
															
			<div class="col-md-8 form-group">
				<label for="agency_id">Agency</label>
				<select name="agency_id" id="agency_id" class="form-control basic-select2 req">
					<option value="">Select Agency</option>
					<?=getPartyListOption($partyList,($dataRow->agency_id??""))?>
				</select>
			</div>
			<div class="col-md-4 form-group">
				<label for="contract_type">Contract Type</label>
				<select name="contract_type" id="contract_type" class="form-control">
					<option value="1" <?=(!empty($dataRow->contract_type) && $dataRow->contract_type == 1)?"selected":""?> >Labor + Material</option>
					<option value="2" <?=(!empty($dataRow->contract_type) && $dataRow->contract_type == 2)?"selected":""?> >Labor</option>
				</select>
			</div>
			
			<div class="col-md-4 form-group">
				<label for="work_qty">Work Size (Feet)</label>
				<input type="text" name="work_qty" id="work_qty" class="form-control numericOnly" value="<?= (!empty($dataRow->work_qty)) ? $dataRow->work_qty : "" ?>">
			</div>
			
			<div class="col-md-4 form-group">
				<label for="work_rate">Work Rate</label>
				<input type="text" name="work_rate" id="work_rate" class="form-control floatOnly" value="<?= (!empty($dataRow->work_rate)) ? $dataRow->work_rate : "" ?>">
			</div>
			
			<div class="col-md-4 form-group">
				<label for="material_qty">Approx. Material</label>
				<input type="text" name="material_qty" id="material_qty" class="form-control numericOnly" value="<?= (!empty($dataRow->material_qty)) ? $dataRow->material_qty : "" ?>">
			</div>

			<div class="col-md-12 form-group">
				<label for="description">Description</label>
				<div class="input-group">
					<input type="text" name="description" id="description" class="form-control" value="">
					<div class="input-group-append">
						<button type="button" class="btn btn-success btn-save" onclick="customStore({'formId':'agencyWorkForm','fnsave':'saveAgencyWork','controller':'projectMaster'});"><i class="fas fa-plus"></i> Add</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<hr>

<div class="col-md-12">
    <div class="row">
        <div class="col-md-12 form-group">
            <div class="table-responsive">
                <table id="agencyWorkDetail" class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center" style="width:5%;">#</th>
                            <th>Agency</th>
                            <th>Contract Type</th>
							<th>Work Size</th>
							<th>Rate</th>
							<th>Approx. Material</th>
							<th class="text-center" style="width:10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="agencyWork">
                        <tr>
                            <td colspan="7" class="text-center">No data available in table</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    setTimeout(() => {
        var getAgencyWork = {'postData':{'project_id':$("#agencyWorkForm #project_id").val(), 'work_id':$("#agencyWorkForm #work_id").val()},'table_id':"agencyWorkDetail",'tbody_id':'agencyWork','tfoot_id':'','fnget':'getAgencyWork'};
        getTransHtml(getAgencyWork);
    }, 500);
});

function resSaveAgencyWork(data,formId){
    if(data.status==1){
        $("#agencyWorkForm #id,#agencyWorkForm #work_qty,#agencyWorkForm #work_rate,#agencyWorkForm #material_qty,#agencyWorkForm #description").val('');
        Swal.fire({ icon: 'success', title: data.message}).then(function(){
            var getAgencyWork = {'postData':{'project_id':$("#agencyWorkForm #project_id").val(), 'work_id':$("#agencyWorkForm #work_id").val()},'table_id':"agencyWorkDetail",'tbody_id':'agencyWork','tfoot_id':'','fnget':'getAgencyWork'};
            getTransHtml(getAgencyWork);
        });
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function getAgencyWork(data){
    //var data = response.data;
    $("#agencyWorkForm #id").val(data.id);
    $("#agencyWorkForm #agency_id").val(data.agency_id);
    $("#agencyWorkForm #contract_type").val(data.contract_type);
    $("#agencyWorkForm #work_qty").val(data.work_qty);
    $("#agencyWorkForm #work_rate").val(data.work_rate);
    $("#agencyWorkForm #material_qty").val(data.material_qty);
    $("#agencyWorkForm #description").val(data.description);
	initSelect2();
}

function resRemoveAgencyWork(data){
    if(data.status==1){
        Swal.fire({ icon: 'success', title: data.message}).then(function(){
            var getAgencyWork = {'postData':{'project_id':$("#agencyWorkForm #project_id").val(), 'work_id':$("#agencyWorkForm #work_id").val()},'table_id':"agencyWorkDetail",'tbody_id':'agencyWork','tfoot_id':'','fnget':'getAgencyWork'};
            getTransHtml(getAgencyWork);
        });
    }else{
        Swal.fire({ icon: 'error', title: data.message });
    }
}
</script>