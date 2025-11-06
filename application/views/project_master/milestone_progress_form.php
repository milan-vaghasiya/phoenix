<form data-res_function="resSaveWorkProgress">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value=""/>
            <input type="hidden" name="project_id" id="project_id" value="<?=$projectId??''?>"/>
            <input type="hidden" name="work_ref_id" id="work_ref_id" value="<?=$work_ref_id??''?>"/>

            <div class="col-md-2 form-group">
                <label for="work_done">Progress (%)</label>
                <input type="text" name="work_done" id="work_done" class="form-control floatOnly req" value="">
            </div>
            <div class="col-md-10 form-group">
                <label for="description">Description</label>
                <div class="input-group">
					<input type="text" name="description" id="description" class="form-control req" value="">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-success btn-save" onclick="customStore({'formId':'workProgressForm','fnsave':'saveWorkProgress','controller':'projectMaster'});"><i class="fas fa-plus"></i> Add</button>
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
                <table id="workProgressDetail" class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center" style="width:5%;">#</th>
                            <th>Progress (%)</th>
                            <th>Description</th>
                            <th>Entered By</th>
                            <th>Entered At</th>
                        </tr>
                    </thead>
                    <tbody id="workProgressDetailBody">
                        <tr>
                            <td colspan="6" class="text-center">No data available in table</td>
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
        var workProgressTrans = {'postData':{'detail_type':$("#workProgressForm #detail_type").val(), 'project_id':$("#workProgressForm #project_id").val(), 'work_ref_id':$("#workProgressForm #work_ref_id").val()},'table_id':"workProgressDetail",'tbody_id':'workProgressDetailBody','tfoot_id':'','fnget':'getWorkProgressDetails'};
        getTransHtml(workProgressTrans);
    }, 500);
});

function resSaveWorkProgress(data,formId){
    if(data.status==1){
        $("#workProgressForm #id,#workProgressForm #description,#workProgressForm #progress_per,#workProgressForm #amount").val('');
        Swal.fire({ icon: 'success', title: data.message}).then(function(){
            var workProgressTrans = {'postData':{'detail_type':$("#workProgressForm #detail_type").val(), 'project_id':$("#workProgressForm #project_id").val(), 'work_ref_id':$("#workProgressForm #work_ref_id").val()},'table_id':"workProgressDetail",'tbody_id':'workProgressDetailBody','tfoot_id':'','fnget':'getWorkProgressDetails'};
            getTransHtml(workProgressTrans);
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

function getWorkProgress(response){
    var data = response.data;
    $("#workProgressForm #id").val(data.id);
    $("#workProgressForm #description").val(data.description);
    $("#workProgressForm #progress_per").val(data.progress_per);
    $("#workProgressForm #amount").val(data.amount);
}

function resRemoveWorkProgress(data){
    if(data.status==1){
        Swal.fire({ icon: 'success', title: data.message}).then(function(){
            var workProgressTrans = {'postData':{'detail_type':$("#workProgressForm #detail_type").val(), 'project_id':$("#workProgressForm #project_id").val(), 'work_ref_id':$("#workProgressForm #work_ref_id").val()},'table_id':"workProgressDetail",'tbody_id':'workProgressDetailBody','tfoot_id':'','fnget':'getWorkProgressDetails'};
            getTransHtml(workProgressTrans);
        });
    }else{
        Swal.fire({ icon: 'error', title: data.message });
    }
}
</script>