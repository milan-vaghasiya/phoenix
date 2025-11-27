<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
			    <div class="page-title-box">
                    <div class="row">
                        <div class="col-md-4">
                            <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                        </div>      
                        <div class="col-md-3">
                            <select name="project_id" id="project_id" class="form-control basic-select2">
                                <option value="">ALL Project</option>
                                <?php 
                                if (!empty($projectList)) :
                                    foreach ($projectList as $row) :
                                        echo '<option value="'.$row->id.'">'.$row->project_name.'</option>';
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">   
                            <input type="date" name="from_date" id="from_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date('Y-m-01')?>" />
                            <div class="error fromDate"></div>
                        </div>     
                        <div class="col-md-3">  
                            <div class="input-group">
                                <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                <div class="input-group-append ml-2">
                                    <button type="button" class="btn waves-effect waves-light btn-success float-right refreshReportData loadData" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> Load
                                    </button>
                                    <button type="button" class="btn btn-danger loadData" data-type="PDF" datatip="PDF" flow="down" target="_blank"><i class="fa fa-file-pdf"></i> PDF</button>
                                </div>
                            </div>
                            <div class="error toDate"></div>
                        </div>                 
                    </div>      

					<div class="card">
                        <div class="card-body reportDiv" style="min-height:75vh">
                            <div class="table-responsive">
                                <table id='reportTable' class="table table-bordered">
                                    <thead class="thead-info" id="theadData">
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th style="min-width:100px;">Project Name</th>
                                            <th style="min-width:80px;">Created By</th>
                                            <th style="min-width:80px;">Created Date</th>
                                            <th style="min-width:150px;">Message</th>
                                            <th style="min-width:100px;">Media</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData"> </tbody>
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
        reportTable();
        setTimeout(function() { $(".loadData").trigger('click'); }, 500);

        $(document).on('click','.loadData',function(e){
            e.stopImmediatePropagation();e.preventDefault();
            $(".error").html("");
            
            var valid = 1;
            var project_id = $('#project_id').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            var type = $(this).data('type');

            if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
            if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
            if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

            if(valid)
            {
                var sendData = {project_id:project_id, from_date:from_date, to_date:to_date, file_type:type};
                if(type == 'PDF'){
                    var url =  base_url + controller + '/getSiteHistory/' + encodeURIComponent(window.btoa(JSON.stringify(sendData)));
                    window.open(url,'_blank');
                }else{
                    $.ajax({
                        url: base_url + controller + '/getSiteHistory',
                        data: sendData,
                        type: "POST",
                        dataType:'json',
                        success:function(data){                            
                            $("#reportTable").dataTable().fnDestroy();
                            $("#tbodyData").html(data.tbody);
                            reportTable();
                        }
                    });
                }
            }
        });   
    });
</script>