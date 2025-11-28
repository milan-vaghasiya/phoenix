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
                        <div class="col-md-2">
                            <select name="project_id" id="project_id" class="form-control basic-select2">
                                <?php 
                                    if (!empty($projectList)){
                                        foreach ($projectList as $row){
                                            echo '<option value="'.$row->id.'">'.$row->project_name.'</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">   
                            <input type="date" name="from_date" id="from_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date('Y-m-01')?>" />
                            <div class="error fromDate"></div>
                        </div>     
                        <div class="col-md-4">  
                            <div class="input-group">
                                <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                <div class="input-group-append ml-2">
                                    <button type="button" class="btn waves-effect waves-light btn-success  refreshReportData loadData" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> Load
                                    </button>
                                    <button type="button" class="btn btn-primary btnLoadData" data-type="excel" datatip="EXCEL" flow="down" target="_blank"><i class="fa fa-file-excel"></i> Excel</button>

                                    <button type="button" class="btn btn-danger btnLoadData" data-type="PDF" datatip="PDF" flow="down" target="_blank"><i class="fa fa-file-pdf"></i> PDF</button>
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
                        
            loadData();
        }); 

        $(document).on('click','.btnLoadData',function(e){
            var type = $(this).data('type');                   
            loadData(type);
        });
    });

    function loadData(type = ''){
        var valid = 1;
        var project_id = $('#project_id').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();

        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
        if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
        if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

        if(valid)
        {
            var sendData = {project_id:project_id, from_date:from_date, to_date:to_date, file_type:type};
            var ajaxOptions = {
                url: base_url + controller + '/getSiteHistory',
                data: sendData,
                method: "POST"
            };

            if (type == 'excel' || type == 'PDF') {
                ajaxOptions.xhrFields = { responseType: 'blob' };
            } else {
                ajaxOptions.dataType = 'json';
            }

            var today = new Date();
            var date = ("0" + today.getDate()).slice(0, 2) + "-" + ("0" + (today.getMonth() + 1)).slice(0, 2) + "-" + today.getFullYear();
            var cleanDate = date.replace(/\//g, "_").replace(/-/g, "_");
            var file_name = "SiteHistoryReport_" + cleanDate;

            $.ajax(ajaxOptions).done(function (data) {
                if (type == 'excel' || type == 'PDF') {
                    var today = new Date();
                    var date = ("0" + today.getDate()).slice(0, 2) + "-" + ("0" + (today.getMonth() + 1)).slice(0, 2) + "-" + today.getFullYear();
                    var cleanDate = date.replace(/\//g, "_").replace(/-/g, "_");
                    var file_name = "SiteHistoryReport_" + cleanDate;
                    
                    var blob = new Blob([data]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
    
                    link.download = (type == "excel" ? file_name+".xlsx" : file_name+".pdf");
                    link.click();
                } else {
                    $("#reportTable").dataTable().fnDestroy();
                    $("#tbodyData").html(data.tbody);
                    reportTable();
                }
            });
        }
    }
</script>