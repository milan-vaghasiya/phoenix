<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end" style="width:20%;">
                        <div class="input-group">
                            <div class="input-group-append <?=(($this->cm_id_count == 1))?"hidden":""?>" style="width:45%;">
                                <select id="cm_id" class="form-control basic-select2">
                                    <?php
                                        if($this->cm_id_count > 1):
                                            echo '<option value="'.$this->cm_ids.'">All Unit</option>';
                                        endif;
                                    ?>                                        
                                    <?=getCompanyListOptions($companyList)?>
                                </select>
                            </div>
                            <div class="input-group-append">
                                <button type="button" class="btn waves-effect waves-light btn-success refreshReportData loadData" title="Load Data">
                                    <i class="fas fa-sync-alt"></i> Load
                                </button>
                            </div>
                            <div class="error stock_type"></div>
                        </div>                                         
                    </div>
                    <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                </div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body reportDiv" style="min-height:75vh">
                            <div class="table-responsive">
                                <table id='reportTable' class="table table-bordered">
                                    <thead class="thead-dark" id="theadData">
                                        <tr class="text-center">
                                            <th colspan="7">Stock Register</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-left">Item Code</th>
                                            <th class="text-left">Item Description</th>
                                            <th class="text-left">Category</th>
                                            <th class="text-right">Balance Qty.</th>
                                            <th class="text-right">Strip Qty.</th>
                                            <th class="text-right">Box Qty.</th>
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


<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $("#cm_id").val(($("#company_id :selected").val() || '<?=$this->cm_ids?>'));initSelect2();

	reportTable();
    setTimeout(function(){$(".loadData").trigger('click');},500);
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
		var cm_id = $('#cm_id').val();

		if(valid){
            $.ajax({
                url: base_url + controller + '/getColdStorageStockRegisterData',
                data: {cm_id:cm_id},
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

function resSaveStock(data,formId){
    if(data.status==1){        
        $('#'+formId)[0].reset(); closeModal(formId);
        Swal.fire({ icon: 'success', title: data.message});

        setTimeout(function(){ $(".loadData").trigger('click'); },500);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }	
}
</script>