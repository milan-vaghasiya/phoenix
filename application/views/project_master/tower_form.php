<form > <!-- data-res_function="resSaveProjectTower" -->
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value=""/>
            <input type="hidden" name="project_id" id="project_id" value="<?=$project_id??''?>"/>
			
			<div class="col-md-12 form-group">
                <label for="tower_name">Tower/Block Name</label>
				<input type="text" name="tower_name" id="tower_name" class="form-control" value="">
			</div>
			
			<div class="col-md-6 form-group">
				<label for="total_basement">No. of Basement</label>
				<input type="text" name="total_basement" id="total_basement" class="form-control numericOnly" value="<?= (!empty($dataRow->total_basement)) ? $dataRow->total_basement : "" ?>">
			</div>
			
			<div class="col-md-6 form-group">
				<label for="total_floor">No. of Floor</label>
				<input type="text" name="total_floor" id="total_floor" class="form-control numericOnly" value="<?= (!empty($dataRow->total_floor)) ? $dataRow->total_floor : "" ?>">
			</div>

            <div class="col-md-12 form-group">
                <label for="description">Description</label>
                <div class="input-group">
					<input type="text" name="description" id="description" class="form-control" value="">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-success btn-save" onclick="customStore({'formId':'projectTowerForm','fnsave':'saveProjectTower','controller':'projectMaster'});"><i class="fas fa-plus"></i> Add</button>
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
                <table id="projectTowerDetail" class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center" style="width:5%;">#</th>
                            <th>Tower/Block</th>
                            <th>Basement</th>
                            <th>Floors</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="projectTowerDetailBody">
                        <tr>
                            <td colspan="5" class="text-center">No data available in table</td>
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
        var getProjectTowerList = {'postData':{'project_id':$("#projectTowerForm #project_id").val()},'table_id':"projectTowerDetail",'tbody_id':'projectTowerDetailBody','tfoot_id':'','fnget':'getProjectTowerList'};
        getTransHtml(getProjectTowerList);
    }, 500);
});

function resSaveWorkProgress(data,formId){
    if(data.status==1){
		$('#projectTowerForm')[0].reset();
        Swal.fire({ icon: 'success', title: data.message}).then(function(){
            var getProjectTowerList = {'postData':{'project_id':$("#projectTowerForm #project_id").val()},'table_id':"projectTowerDetail",'tbody_id':'projectTowerDetailBody','tfoot_id':'','fnget':'getProjectTowerList'};
            getTransHtml(getProjectTowerList);
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

function getProjectTower(data){
    ///var data = response.data;
    $("#projectTowerForm #id").val(data.id);
    $("#projectTowerForm #tower_name").val(data.tower_name);
    $("#projectTowerForm #total_basement").val(data.total_basement);
    $("#projectTowerForm #total_floor").val(data.total_floor);
    $("#projectTowerForm #description").val(data.description);
}

function resRemoveProjectTower(data){
    if(data.status==1){
        Swal.fire({ icon: 'success', title: data.message}).then(function(){
            var getProjectTowerList = {'postData':{'project_id':$("#projectTowerForm #project_id").val()},'table_id':"projectTowerDetail",'tbody_id':'projectTowerDetailBody','tfoot_id':'','fnget':'getProjectTowerList'};
            getTransHtml(getProjectTowerList);
        });
    }else{
        Swal.fire({ icon: 'error', title: data.message });
    }
}
</script>