<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                        </div>       
                        <div class="col-md-4 float-right">  
                            <div class="input-group">
                                <div class="input-group-append" style="width:60%;">
                                    <select id="location_id" class="form-control basic-select2">
                                        <option value="">Select Project</option>
                                        <?php
                                        if (!empty($projectList)) :
                                            foreach ($projectList as $row) :
                                                $selected = (!empty($postData) && $postData['project_id'] == $row->id)?"selected":"";
                                                echo '<option value="'.$row->id.'" '. $selected.'>'.$row->project_name.'</option>';
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                    <div class="error locationId"></div>
                                </div>
                                <div class="input-group-append">
                                    <button type="button" class="btn waves-effect waves-light btn-success refreshReportData loadData" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> Load
                                    </button>
                                </div>
                            </div>
                        </div>                  
                    </div>                                         
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card"> 
                            <div class="card-body reportDiv" style="min-height:75vh">
                                <div class="table-responsive">
                                    <table id='reportTable' class="table table-bordered">
                                        <thead class="thead-dark" id="theadData">
                                            <tr class="text-center">
                                                <th colspan="8">Minimum Stock</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th class="text-center">Item Code</th>
                                                <th class="text-left">Item Name</th>
                                                <th class="text-left">Category</th>
                                                <th class="text-center">Unit</th>
                                                <th class="text-right">Min. Stock</th>
						                        <th class="text-right">Stock Qty</th>
						                        <th class="text-right">Shortage</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyData"></tbody>
                                    </table>
                                </div>
                            </div>
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
	reportTable();
    setTimeout(function() { $(".loadData").trigger('click'); }, 500);
    
    $(document).on('click','.loadData',function(e){
		e.stopImmediatePropagation();e.preventDefault();
        $(".error").html("");
		var valid = 1;
		var location_id = $('#location_id').val();
		if($("#location_id").val() == ""){$(".locationId").html("Project is required.");valid=0;}

        if(valid){
            $.ajax({
                url: base_url + controller + '/getMinimumStockData',
                data: { location_id:location_id },
                type: "POST",
                dataType:'json',
                success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
                    $("#tbodyData").html(data.tbody);
                    reportTable();
                }
            });
        }
    });  
});
</script>