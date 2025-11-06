<?php $this->load->view("includes/header"); ?>
<style>.input-group .select2-container{width:70%!important;}</style>
<div class="page-content-tab">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-end">
                        <?php
							$addParam = "{'postData':{'project_id' : ".$project_id."}, 'modal_id' : 'bs-right-md-modal', 'call_function':'addProjectTower', 'form_id' : 'projectTowerForm', 'title' : 'Add Towers'}";
							
							$addInchargeParam = "{'postData':{'project_id' : ".$project_id."}, 'modal_id' : 'modal-md', 'call_function':'addIncharge', 'fnsave':'saveIncharge', 'form_id' : 'addIncharge', 'title' : 'Add In-Charge'}";
						
							$addMachineParam = "{'postData':{'project_id' : ".$project_id."}, 'modal_id' : 'modal-md', 'call_function':'addMachine', 'fnsave':'saveMachine', 'form_id' : 'addMachine', 'title' : 'Add Machine'}";
						?>
						<button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Towers</button>
						<button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write press-add-btn" onclick="modalAction(<?=$addInchargeParam?>);"><i class="fa fa-plus"></i> Add In-Charge</button>
						<button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write press-add-btn" onclick="modalAction(<?=$addMachineParam?>);"><i class="fa fa-plus"></i> Add Machine</button>
						<a href="<?=base_url("projectMaster")?>" class="btn btn-outline-dark"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                    <h4 class="page-title">Project Detail</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="met-profile">
                            <div class="row">

                                <div class="col-lg-4 align-self-center mb-3 mb-lg-0">
                                    <div class="met-profile-main">
                                        <div class="d-flex align-items-center">
                                            <span class="thumb-xl justify-content-center d-flex align-items-center bg-soft-success rounded-circle me-2"><?=(!empty($dataRow->project_name))?substr($dataRow->project_name,0,1):''?></span>
                                        </div>

                                        <div class="met-profile_user-detail">
                                            <h5 class="met-user-name"><?=$dataRow->project_name??''?></h5>                                                        
                                            <p class="mb-0 met-user-name-post"><?=$dataRow->build_type??''?></p>                                                        
                                        </div>
                                    </div>                                                
                                </div>
                                
                                <div class="col-lg-4 ms-auto align-self-center">
                                    <ul class="list-unstyled personal-detail mb-0">
										<li class="">
                                            <i class="las la-briefcase mr-2 text-secondary font-22 align-middle"></i> <b> Customer </b> : <?=$dataRow->party_name??''?>
                                        </li>
                                        <li class="">
                                            <i class="las la-user mr-2 text-secondary font-22 align-middle"></i> <b> Contect Person </b> : <?=$dataRow->contact_person??''?>
                                        </li>
                                        <li class="">
                                            <i class="las la-phone mr-2 text-secondary font-22 align-middle"></i> <b> phone </b> : <?=$dataRow->party_mobile??''?>
                                        </li>
                                        <li class="mt-2">
                                            <i class="las la-envelope text-secondary font-22 align-middle mr-2"></i> <b> Email </b> : <?=$dataRow->party_email??''?>
                                        </li>                                                  
                                    </ul>
                                </div>

                                <div class="col-lg-4 align-self-center">
                                    <div class="row">
                                        <div class="col-auto text-end border-end">
                                            <p class="mb-0 fw-semibold">Cost Type</p>
                                            <h4 class="m-0 fw-bold"><?=$dataRow->cost_type_name??''?></h4>
                                        </div>
                                        <div class="col-auto">
                                            <p class="mb-0 fw-semibold">Amount</p>
                                            <h4 class="m-0 fw-bold"><?=(!empty($dataRow->amount))?moneyFormatIndia($dataRow->amount):0?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <ul class="nav nav-tabs" role="tablist">
							
							<?php
								$i = 1;$initTowerName = '';
								if(!empty($projectTowerList))
								{
									$initTowerName = $projectTowerList[0]->tower_name;
									foreach($projectTowerList as $row)
									{
										$tabID = str_replace(' ','_',strtolower($row->tower_name));
										echo '<li class="nav-item">
												<a class="nav-link '.(($i==1) ? 'active' : '').'" data-tower_name="'.$row->tower_name.'" data-tabid="'.$tabID.'" data-project_id="'.$project_id.'" data-bs-toggle="tab" href="#'.$tabID.'" role="tab" aria-selected="true"><b>'.$row->tower_name.'</b></a>
											</li>';
										$i++;
									}
								}
							?>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane p-3 active" id="milestone" role="tabpanel">
                                <div class="card-body">
                                    <form id="projectMilestone" data-res_function="resSaveProjectMilestone">
                                        <div class="col-md-12">
											<div class="row">
												<input type="hidden" name="id" id="id" value=""/>
												<input type="hidden" name="project_id" id="project_id" value="<?=$project_id??''?>"/>
												<input type="hidden" name="tower_name" id="tower_name" value="<?=(!empty($tower_name) ? $tower_name : $initTowerName)?>"/>
																								
												<div class="col-md-4 form-group">
													<label for="work_type_id">Work Type</label>
													<select name="work_type_id" id="work_type_id" class="form-control basic-select2 req">
														<option value="">Select Work Type</option>
														<?php
															foreach($workTypeList as $row):
																$selected = (!empty($dataRow->work_type_id) && $dataRow->work_type_id == $row->detail)?"selected":"";
																echo '<option value="'.$row->id.'" '.$selected.'>'.$row->detail.'</option>';
															endforeach;
														?>
													</select>
												</div>
												<div class="col-md-2 form-group">
													<label for="contract_type">Contract Type</label>
													<select name="contract_type" id="contract_type" class="form-control basic-select2">
														<option value="1" <?=(!empty($dataRow->contract_type) && $dataRow->contract_type == 1)?"selected":""?>>Labor + Material</option>
														<option value="2" <?=(!empty($dataRow->contract_type) && $dataRow->contract_type == 2)?"selected":""?>>Labor</option>
													</select>
												</div>
												
												<div class="col-md-2 form-group">
													<label for="work_qty">Work Size (Feet)</label>
													<input type="text" name="work_qty" id="work_qty" class="form-control numericOnly" value="<?= (!empty($dataRow->work_qty)) ? $dataRow->work_qty : "" ?>">
												</div>
												
												<div class="col-md-2 form-group">
													<label for="work_rate">Work Rate</label>
													<input type="text" name="work_rate" id="work_rate" class="form-control floatOnly" value="<?= (!empty($dataRow->work_rate)) ? $dataRow->work_rate : "" ?>">
												</div>
												
												<div class="col-md-2 form-group">
													<label for="material_qty">Approx. Material</label>
													<input type="text" name="material_qty" id="material_qty" class="form-control numericOnly" value="<?= (!empty($dataRow->material_qty)) ? $dataRow->material_qty : "" ?>">
												</div>

												<div class="col-md-12 form-group">
													<label for="description">Description</label>
													<div class="input-group">
														<input type="text" name="description" id="description" class="form-control" value="">
														<div class="input-group-append">
															<button type="button" class="btn btn-success btn-save" onclick="customStore({'formId':'projectMilestone','fnsave':'saveProjectMilestone','controller':'projectMaster'});"><i class="fas fa-plus"></i> Add</button>
														</div>
													</div>
												</div>
											</div>
										</div>
									</form>
                                    <hr>

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 form-group">
                                                <div class="table-responsive">
                                                    <table id="projectMileStoneDetail" class="table table-bordered">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th class="text-center" style="width:5%;">Sr. No.</th>
                                                                <th>Work Type</th>
                                                                <th>Contract Type</th>
                                                                <th>Work Size</th>
                                                                <th>Rate</th>
                                                                <th>Approx. Material</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="projectMileStoneDetailBody" class="projectMileStoneDetailBody">
                                                            <tr>
                                                                <td colspan="7" class="text-center">No data available in table</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
						</div>        
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
        

<?php $this->load->view("includes/footer"); ?>

<script>
$(document).ready(function(){
    setTimeout(() => {
        var projectDetailTrans = {'postData':{'tower_name':$("#projectMilestone #tower_name").val(), 'project_id':$("#projectMilestone #project_id").val()},'table_id':"projectMileStoneDetail",'tbody_id':'projectMileStoneDetailBody','tfoot_id':'','fnget':'getProjectMilestoneList'};
        getTransHtml(projectDetailTrans);
    }, 200);  
    
    $(document).on('click','.nav-link',function(){
        var tabName = $(this).attr('href');
        var tabid = $(this).data('tabid');
		var tower_name = $(this).data('tower_name');
		var project_id = $(this).data('project_id') || "";
        
		if(tabName == "#"+tabid){
			$("#tower_name").val(tower_name);
			var getProjectMilestoneList = {'postData':{'tower_name':$("#projectMilestone #tower_name").val(), 'project_id':$("#projectMilestone #project_id").val()},'table_id':"projectMileStoneDetail",'tbody_id':'projectMileStoneDetailBody','tfoot_id':'','fnget':'getProjectMilestoneList'};
            getTransHtml(getProjectMilestoneList);
			
        }
    }); 
});

function resSaveProjectMilestone(data,formId){
    if(data.status==1){
        $("#projectMilestone #id,#projectMilestone #work_qty,#projectMilestone #work_rate,#projectMilestone #material_qty,#projectMilestone #description").val('');
        Swal.fire({ icon: 'success', title: data.message}).then(function(){
            var getProjectMilestoneList = {'postData':{'tower_name':$("#projectMilestone #tower_name").val(), 'project_id':$("#projectMilestone #project_id").val()},'table_id':"projectMileStoneDetail",'tbody_id':'projectMileStoneDetailBody','tfoot_id':'','fnget':'getProjectMilestoneList'};
            getTransHtml(getProjectMilestoneList);
        });
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}

function getProjectMilestone(response){
    var data = response.data;
    $("#projectMilestone #id").val(data.id);
    $("#projectMilestone #project_id").val(data.project_id);
    $("#projectMilestone #tower_name").val(data.tower_name);
    $("#projectMilestone #work_type").val(data.work_type);
    $("#projectMilestone #contract_type").val(data.contract_type);
    $("#projectMilestone #work_qty").val(data.work_qty);
    $("#projectMilestone #work_rate").val(data.work_rate);
    $("#projectMilestone #material_qty").val(data.material_qty);
    $("#projectMilestone #work_qty").val(data.work_qty);
    $("#projectMilestone #description").val(data.description);
	
	initSelect2();
} 

function resRemoveProjectMilestone(data){
    if(data.status==1){
        Swal.fire({ icon: 'success', title: data.message}).then(function(){
            var getProjectMilestoneList = {'postData':{'detail_type':$("#projectMilestone #detail_type").val(), 'project_id':$("#projectMilestone #project_id").val(), 'tower_name':$("#projectMilestone #tower_name").val()},'table_id':"projectMileStoneDetail",'tbody_id':'projectMileStoneDetailBody','tfoot_id':'','fnget':'getProjectMilestoneList'};
            getTransHtml(getProjectMilestoneList);
        });
    }else{
        Swal.fire({ icon: 'error', title: data.message });
    }
}
</script>