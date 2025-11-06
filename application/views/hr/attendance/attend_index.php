<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="page-title-box">	
				<!-- <div class="float-end">
					<h5 class="text-danger">Shows Only Current Month Data</h5>
				</div>	 -->
				<div class="float-end">
					<?php
						$addParam = "{'modal_id' : 'bs-right-md-modal', 'call_function':'addManualAttendence', 'form_id' : 'addManualAttendence', 'title' : 'Add Manual Attendence'}";
					?>
					<button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Manual Attendence</button>
				</div>
                <!-- <h4 class="card-title text-center">Employees</h4> -->
				 <ul class="nav nav-pills">
					<li class="nav-item">   <!-- Pending -->
						<!-- <button onclick="statusTab('attendTable',0);" class="nav-tab btn waves-effect waves-light btn-outline-danger active" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Pending</button>  -->
					</li>
					<li class="nav-item">  <!-- Self Approved -->
						<!-- <button onclick="statusTab('attendTable',1);" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Regular</button>  -->
					</li>
					<li class="nav-item"> <!-- Approved By HOD -->
						<!-- <button onclick="statusTab('attendTable',2);" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Approved (HOD)</button> -->
					</li>
					<li class="nav-item"> <!-- Approved By HOD -->
						<!-- <button onclick="statusTab('attendTable',5);" class="nav-tab btn waves-effect waves-light btn-outline-warning" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Auto Punch</button> -->
					</li>
					<li class="nav-item"> <!-- Add Manual Attendence -->
						<!-- <button type="button" class="btn btn-info float-right addNew press-add-btn permission-write" data-button="both" data-modal_id="right_modal" data-function="addManualAttendence" data-form_title="Add Manual Attendence"><i class="fa fa-plus"></i> Add Manual Attendence</button> -->
					</li>
				</ul>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='attendTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>