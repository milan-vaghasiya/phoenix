<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
					    <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <button onclick="statusTab('salesOrderTable',0);" id="pending_so" class="nav-tab btn waves-effect waves-light btn-outline-danger active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> 
                            </li>
                            <li class="nav-item"> 
                                <button onclick="statusTab('salesOrderTable',1);" id="accepted_so" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Accepted</button> 
                            </li>
                            <!-- <li class="nav-item"> 
                                <button onclick="statusTab('salesOrderTable',2);" id="loading_so" class="nav-tab btn waves-effect waves-light btn-outline-warning" style="outline:0px" data-toggle="tab" aria-expanded="false">Loading</button> 
                            </li> -->
                            <li class="nav-item"> 
                                <button onclick="statusTab('salesOrderTable',3);" id="complete_so" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> 
                            </li>
                        </ul>
					</div>
					<div class="float-end">
                        <a href="javascript:void(0)" class="btn waves-effect waves-light btn-outline-dark float-right permission-write press-add-btn" onclick="window.location.href='<?=base_url($headData->controller.'/addOrder')?>'"><i class="fa fa-plus"></i> Add Order</a>

                        <?php
                            /* $addParam = "{'modal_id' : 'bs-right-lg-modal', 'call_function':'addPartyOrder', 'fnsave' : 'savePartyOrder', 'form_id' : 'addPartyOrder', 'title' : 'Add Order'}"; */
                        ?>
						<!-- <button type="button" class="btn btn-outline-dark btn-sm float-right permission-write press-add-btn" onclick="modalAction(<?=$addParam?>);" ><i class="fa fa-plus"></i> Add Order</button> -->
					</div>
                    <h4 class="card-title text-center">Sales Orders</h4>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='salesOrderTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>