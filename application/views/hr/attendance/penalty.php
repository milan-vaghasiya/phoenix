<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
            <div class="col-12">
				<form id="penaltyForm">
					<div class="card">
						<div class="card-header">
							   <div class="row">
                                <div class="col-md-2">
                                    <h4 class="page-title">Penalty</h4>
                                </div>
                                <div class="col-md-3">   
                                    <select name="emp_id" id="emp_id" class="form-control basic-select2">
                                        <option value="0">All Employee</option>
                                        <?php   
                                            foreach($empList as $row): 
                                                $row->emp_name = (!empty($row->emp_code)?'['.$row->emp_code.'] '.$row->emp_name:$row->emp_name);
                                                echo '<option value="'.$row->id.'">'.$row->emp_name.'</option>';
                                            endforeach; 
                                        ?>
                                    </select>
                                </div> 
                                <div class="col-md-3 from-group">
                                    <select name="emp_designation" id="emp_designation" class="form-control basic-select2 req">
                                        <option value="">Select Designation</option>
                                        <?php
                                        if(!empty($designationList)):
                                            foreach($designationList as $row):
                                                $selected = ((!empty($dataRow->emp_designation) && $row->id == $dataRow->emp_designation) ? "selected" : "");
                                                echo '<option value="'.$row->id.'" '.$selected.'>'.$row->title.'</option>';
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="date" id="from_date" name="from_date" class="form-control" value="<?=date("Y-m-d")?>" max=<?=date('Y-m-d')?> >
                                        <input type="date" id="to_date" name="to_date" class="form-control" value="<?=date("Y-m-d")?>" max=<?=date('Y-m-d')?> >
                                        <div class="input-group-append">
                                            <button class="btn btn-info loadData" type="button">Load</button>
                                        </div>
                                    </div>
                                    <div class="error reportDate"></div>
                                </div>                       
                            </div>                                         
						</div>
						<div class="card-body reportDiv" style="min-height:75vh">
							<div class="table-responsive">
								<table id='reportTable' class="table table-bordered jpDataTable">
									<thead class="thead-info">
										<tr>
											<th>#</th>
                                            <th>Code</th>
                                            <th>Emp Name</th>
                                            <th>Designation</th>
                                            <th>Shift</th>
                                            <th>Attendance Date</th>
                                            <th>Punch Time</th>
                                            <th>Shift Start</th>
                                            <th>Late Time</th>
                                            <th>Penalty</th>
										</tr>
									</thead>
									<tbody id="penaltyData"></tbody>
								</table>
							</div>
							<div class="col-md-12"> 
								<?php $postData = "{'formId':'penaltyForm','fnsave':'savePenalty','table_id':'reportTable'}"; ?>
								<button type="button" class=" btn waves-effect waves-light btn-success float-right save-form permission-write" style="letter-spacing:1px;" onclick="customStore(<?=$postData?>);">Save Penalty</button>
							</div>
						</div>
					</div>
				</form>
            </div>
        </div>            
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	reportTable();
	$(document).on("click",".loadData",function(){
         var from_date = $("#penaltyForm #from_date").val();
        var to_date = $("#penaltyForm #to_date").val();
        var emp_id = $("#penaltyForm #emp_id").val();
        var emp_designation = $("#penaltyForm #emp_designation").val();

        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
        if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
        if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

		$.ajax({
			url:base_url + controller + '/getPenaltyData',
			type:'post',
			data:{from_date:from_date,to_date:to_date,emp_id:emp_id,emp_designation:emp_designation},
			dataType:'json',
			success:function(data)
			{
				$("#reportTable").DataTable().clear().destroy();
				$("#penaltyData").html(data.tbodyData);
				reportTable();
			}
		});
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
		"paging": false,
		order:[],
		"columnDefs": 	[
			{ type: 'natural', targets: 0 },
			{ orderable: false, targets: "_all" }, 
			{ className: "text-left", targets: [0,1] }, 
			{ className: "text-center", "targets": "_all" } 
		],
		pageLength:25,
		language: { search: "" },
		// lengthMenu: [
        //     [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        // ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {$(".loadData").trigger('click');}}]
	});
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