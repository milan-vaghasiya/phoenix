
<div class="col-md-12"> 
    <form enctype="multipart/form-data" data-res_function="getEmpDocsHtml">
        <div class="row">
        <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="emp_id" id="emp_id" value="<?=$emp_id?>" />

            <div class="col-md-4 form-group">
                <label for="doc_name">Document Name</label>
                <input type="text" name="doc_name" id="doc_name" class="form-control req" value="" />
            </div>

            <div class="col-md-4 form-group">
                <label for="doc_no">Document No.</label>
                <input type="text" name="doc_no" id="doc_no" class="form-control req" value="" />
            </div>

            <div class="col-md-4 form-group">
                <label for="doc_file">Document File</label>
                <div class="input-group">
                    <input type="file" name="doc_file" id="doc_file" class="form-control req" style="width:60%;"/>       
					<?php
						$param = "{'formId':'addDocuments','fnsave':'saveDocuments','res_function':'getEmpDocsHtml'}";
					?>
					<button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-end" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-plus"></i> Add</button>
				</div>
            </div>
        </div>
    </form>
    <hr>
    <div class="row">
        <div class="table-responsive">
            <table id="docTable" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th class="text-center">Document Name</th>
                        <th class="text-center">Document No.</th>                        
                        <th class="text-center">Document File</th>
                        <th class="text-center" style="width:10%;">Action</th>
                        
                    </tr>
                </thead>
                <tbody id="docBody">
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
$(document).ready(function(){
    setPlaceHolder();
    if(!tbodyData){
        var postData = {'postData':{'emp_id':$("#emp_id").val()},'table_id':"docTable",'tbody_id':'docBody','tfoot_id':'','fnget':'getEmpDocsHtml'};
        getTransHtml(postData);
        tbodyData = true;
    }
});

function getEmpDocsHtml(data,formId="addDocuments"){ 
    if(data.status==1){
        $('#'+formId)[0].reset();
        var postData = {'postData':{'emp_id':$("#emp_id").val()},'table_id':"docTable",'tbody_id':'docBody','tfoot_id':'','fnget':'getEmpDocsHtml'};
        getTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            Swal.fire({ icon: 'error', title: data.message });	
        }
    }   
 }
</script>