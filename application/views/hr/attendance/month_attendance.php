<?php 
	$this->load->view('includes/header'); 	
	$today = new DateTime();
	$today->modify('first day of this month');$first_day = date('Y-m-d');
	$today->modify('last day of this month');$last_day = date("t",strtotime($today->format('Y-m-d')));
	$monthArr = ['Apr-'.$startYear=>'01-04-'.$startYear,'May-'.$startYear=>'01-05-'.$startYear,
	'Jun-'.$startYear=>'01-06-'.$startYear,'Jul-'.$startYear=>'01-07-'.$startYear,'Aug-'.$startYear=>'01-08-'.$startYear,'Sep-'.$startYear=>'01-09-'.$startYear,'Oct-'.$startYear=>'01-10-'.$startYear,'Nov-'.$startYear=>'01-11-'.$startYear,'Dec-'.$startYear=>'01-12-'.$startYear,'Jan-'.$endYear=>'01-01-'.$endYear,'Feb-'.$endYear=>'01-02-'.$endYear,'Mar-'.$endYear=>'01-03-'.$endYear];	
		
?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">
                                <h4 class="card-title">Monthly Attendance</h4>
                            </div>
							
							<div class="col-md-2">
                                <select name="report_type" id="report_type" class="form-control select2">
									<option value="1">Form 28</option>
									<option value="2">Hourly</option>
                                </select>
							</div>
							
							<div class="col-md-2">
                                <select name="month" id="month" class="form-control select2">
                                    <?php
                                        foreach($monthArr as $key=>$value):
                                            $selected = (date('m') == $value)?"selected":"";
                                            echo '<option value="'.$value.'" '.$selected.'>'.$key.'</option>';
                                        endforeach;
                                    ?>
                                </select>
							</div>
                            <div class="col-md-3">
								<div class="input-group">
								<button type="button" class="btn btn-info loadData" data-type="0" datatip="View Report" flow="down"><i class="fa fa-eye"></i> View</button>
								<button type="button" class="btn btn-primary loadData" data-type="excel" datatip="EXCEL" flow="down" target="_blank"><i class="fa fa-file-excel"></i> Excel</button>
								<button type="button" class="btn btn-danger loadData" data-type="PDF" datatip="PDF" flow="down" target="_blank"><i class="fa fa-file-pdf"></i> PDF</button>
								</div>
                            </div>                     
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='attendanceTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th>Emp Code</th>
										<th>Employee</th>
										<th>Designation</th>
										<?php for($d=1;$d<=$last_day;$d++){echo '<th>'.$d.'</th>';} ?>
										<th>Total</th>
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
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    reportTable();
    $(document).on('click','.loadData',function(e){
        var report_type = $("#report_type").val();
        var month = $("#month").val();
		var type = $(this).data('type');
		if(month){
			var sendData = {month:month,report_type:report_type,file_type:type};
			if(type == 'excel' || type == 'PDF'){
				var url =  base_url + controller + '/getMonthlyReport/' + encodeURIComponent(window.btoa(JSON.stringify(sendData)));
				window.open(url,'_blank');
			}else{
				$.ajax({
					url: base_url + controller + '/getMonthlyReport',
					data: sendData,
					type: "POST",
					dataType:'json',
					success:function(data){
						$("#reportTable").DataTable().clear().destroy();
						$("#theadData").html(data.thead);
						$("#tbodyData").html(data.tbody);
						reportTable();
					}
				});
			}
        }
    });   
});
</script>