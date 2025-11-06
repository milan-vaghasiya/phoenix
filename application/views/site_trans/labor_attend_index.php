<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
                        <?php
                            $addParam = "{'postData':{'type' : 1},'modal_id' : 'bs-right-md-modal', 'call_function':'addLaborAttendance', 'form_id' : 'addLaborAttendance', 'title' : 'Add Attendance', 'fnsave' : 'saveLaborAttendance'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Labor Attendance</button>
					</div>
                    <h4 class="card-title">Labor Attendance</h4>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='workDetailTable' class="table table-bordered ssTable ssTable-cf" data-url='/getLaborAttendanceDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>