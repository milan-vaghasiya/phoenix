<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-4">
                                    <h4 class="card-title pageHeader">DPR</h4>
                                </div>      
                                <div class="col-md-3">
                                    <input type="date" class="form-control" id="trans_date" value="<?=date("Y-m-d")?>">
                                </div>
                                <div class="col-md-3">  
                                    <select name="project_id" id="project_id" class="form-control basic-select2 req float-right">
                                        <option value="">Select Project</option>
                                        <?php
                                            foreach($projectList as $row):
                                                $selected = (!empty($dataRow->project_id) && $dataRow->project_id == $row->id)?"selected":"";
                                                echo '<option value="'.$row->id.'" '.$selected.'>'.$row->project_name.' ('.$row->party_name.')</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn waves-effect waves-light btn-success float-left loadData" data-pdf="0" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> View
                                    </button>
                                    <button type="button" class="btn waves-effect waves-light btn-warning loadData" data-pdf="1" title="PDF">
                                        <i class="fas fa-print"></i> PDF
                                    </button>
                                </div>                 
                            </div>  
                        </div>

                        <div class="card-body reportDiv" style="min-height:75vh" id='dprTable'>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
        var trans_date = $("#trans_date").val();
		var project_id = $('#project_id').val();
        var is_pdf = $(this).data('pdf');

        if($("#project_id").val() == ""){$(".project_id").html("Project is required.");valid=0;}
        var postData = { trans_date:trans_date, project_id:project_id, is_pdf:is_pdf };
		if(valid){
            if(is_pdf == 0){
                $.ajax({
                    url: base_url + controller + '/getDprReport',
                    data: postData,
                    type: "POST",
                    dataType:'json',
                    success:function(data){
                        console.log(data);
                        if (data.status === 1) {
                            $("#dprTable").html(data.tbody);  
                            reportTable(); 
                        } else {
                            alert("Error: Could not load data");
                        }
                    }
                });
            }else{
                var addParam = {
                    postData: postData,
                    modal_id: 'bs-right-md-modal',
                    call_function: 'dprPrintForm',
                    form_id: 'dprPrintForm',
                    title: 'DPR Print',
                    js_store_fn: 'printDPR',
                    savebtn_text: 'PDF'
                };

                modalAction(addParam);
            } 
        }
    });   
});
</script>