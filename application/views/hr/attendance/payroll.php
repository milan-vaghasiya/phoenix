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
                            <div class="col-md-6">
                                <h4 class="card-title">Payroll</h4>
                            </div>
							
							<div class="col-md-4">
                                <select name="month" id="month" class="form-control basic-select2">
                                    <?php
                                        foreach($monthArr as $key=>$value):
                                            $selected = (date('m') == $value)?"selected":"";
                                            echo '<option value="'.$value.'" '.$selected.'>'.$key.'</option>';
                                        endforeach;
                                    ?>
                                </select>
							</div>
                            <div class="col-md-2">
								<button type="button" class="btn btn-success loadData" data-type="0" datatip="View Report" flow="down"><i class="fa fa-eye"></i> View</button>
								<!-- <button type="button" class="btn btn-success loadData" data-type="excel" datatip="EXCEL" flow="down" target="_blank"><i class="fa fa-file-excel"></i> Excel</button> -->
                            </div>                     
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered jpDataTable colSearch">
								<thead class="thead-info" id="theadData">
									<tr>
										<th>Emp Name</th>
										<th>Total Days</th>
										<th>Week Off</th>
										<th>Working Days</th>
										<th>Present</th>
										<th>Late</th>
										<th>Absent</th>
										<th>Paid Leave</th>
										<th>UnPaid Leave</th>
										<th>Daily Wage</th>
										<th>Gross Amount</th>
										<th>Penalty</th>
										<th>Net Salary</th>
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
        var month = $("#month").val();
		if(month){
			var sendData = {month:month};
			$.ajax({
				url: base_url + controller + '/getPayrollReport',
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
    });   
});
function reportTable()
{
	var reportTable = $('#reportTable').DataTable( 
	{
		responsive: true,
		scrollY: '55vh',
        scrollCollapse: true,
		"scrollX": true,
		"scrollCollapse":true,
		//'stateSave':true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {$(".loaddata").trigger('click');}}]
	});
	// var printBtn = '<button class="btn btn-outline-primary loaddata" data-file_type="PDF" type="button"><span>PDF</span></button>';
    // reportTable.buttons().container().append(printBtn);
	reportTable.buttons().container().appendTo( '#reportTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return reportTable;
}
</script>