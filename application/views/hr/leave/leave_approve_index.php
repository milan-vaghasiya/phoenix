<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item"> <button onclick="statusTab('leaveApproveTable',1);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                            <li class="nav-item"> <button onclick="statusTab('leaveApproveTable',2);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Approved</button> </li>
                            <li class="nav-item"> <button onclick="statusTab('leaveApproveTable',3);" class=" btn waves-effect waves-light btn-outline-danger" style="outline:0px" data-toggle="tab" aria-expanded="false">Rejected</button> </li>        
                        </ul>
                    </div>                  
                </div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='leaveApproveTable' class="table table-bordered ssTable bt-switch1" data-url="/getDTRows/<?=$type?>"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
