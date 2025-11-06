<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form autocomplete="off" id="savePurchaseOrder" data-res_function="resSaveOrder" enctype="multipart/form-data">
                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="hiddenInput">
                                            <input type="hidden" name="id" id="id" class="trans_main_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="trans_number">WO. No.</label>

                                            <div class="input-group">
                                                <input type="text" name="trans_prefix" id="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:((!empty($trans_prefix))?$trans_prefix:"")?>" readonly>
                                                <input type="text" name="trans_no" id="trans_no" class="form-control numericOnly" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:((!empty($trans_no))?$trans_no:"")?>">
                                            </div>

                                            <input type="hidden" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:((!empty($trans_number))?$trans_number:"")?>" readonly>

                                            <div class="error trans_number"></div>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="trans_date">WO. Date</label>
                                            <input type="date" name="trans_date" id="trans_date" class="form-control fyDates req" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
                                        </div>

                                        <div class="col-md-5 form-group">
                                            <label for="party_id">Party Name</label>
                                            <select name="party_id" id="party_id" class="form-control basic-select2 req" data-res_function="resPartyDetail" data-party_category="2,3">
                                                <option value="">Select Party</option>
												<?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:(!empty($enqItemList[0]->party_id)?$enqItemList[0]->party_id:'')))?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="project_id">Project</label>
                                            <select name="project_id" id="project_id" class="form-control basic-select2 req">
                                                <option value="">Select Project</option>
                                                <?php
                                                    if(!empty($projectList)):
                                                        foreach($projectList as $row):
                                                            $projectId = (!empty($enqItemList[0]->project_id)) ? $enqItemList[0]->project_id : (!empty($reqItemList[0]->project_id) ? $reqItemList[0]->project_id : '');

                                                            $selected = (!empty($dataRow->project_id) && $dataRow->project_id == $row->id)?"selected":((!empty($projectId) && $projectId == $row->id) ? "selected" : "");
                                                            echo '<option value="'.$row->id.'" data-site_address="'.$row->site_add.'" '.$selected.'>'.$row->project_name.'</option>';
                                                        endforeach;
                                                    endif;
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-md-12 form-group">
                                            <label for="delivery_address">Delivery Address</label>
                                            <input type="text" name="delivery_address" id="delivery_address" class="form-control" value="<?=(!empty($dataRow->delivery_address))?$dataRow->delivery_address:""?>">
                                        </div>

                                    </div>

                                    <hr>
                                    <div class="col-md-12" id="itemForm">
                                        <div class="row">
                                            <div id="itemInputs">
                                                <input type="hidden" id="id" value="" class="itemFormInput"/>
                                                <input type="hidden" id="row_index" value="" class="itemFormInput" />
                                            </div>
                                            <div class="col-md-7 form-group">
                                                <label for="item_name">Product Name</label>
												<input type="text" id="item_name" class="form-control itemFormInput req" value="">
                                            </div>
                                            <div class="col-md-2 form-group">
                                                <label for="unit_id">Unit</label>
                                                <select id="unit_id" class="form-control basic-select2 itemFormInput req">
													<option value="">Select Unit</option>
                                                    <?php
                                                        foreach($unitList as $row):
                                                            echo '<option value="'.$row->id.'">'.(!empty($row->unit_name) ? '['.$row->unit_name.'] '.$row->description : $row->description).'</option>';
                                                        endforeach;
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2 form-group">
                                                <label for="rate">Rate</label>
                                                <input type="text" id="rate" class="form-control floatOnly req itemFormInput" value="0" />
                                            </div>
                                            <div class="col-md-1 form-group float-right">
                                                <label for="">&nbsp;</label>
                                                <button type="button" class="btn btn-outline-success saveItem float-right mt-25" style="line-height: 1.8;"><i class="fa fa-plus"></i> Add</button>
                                            </div>                       
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="col-md-12 row">
                                        <div class="col-md-6"><h4>Item Details : </h4></div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <div class="error itemData"></div>
                                        <div class="row form-group">
                                            <div class="table-responsive">
                                                <table id="purchaseOrderItems" class="table table-striped table-borderless">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th style="width:5%;">#</th>
                                                            <th>Item Name</th>
                                                            <th>Unit</th>
                                                            <th>Rate</th>
                                                            <th class="text-center" style="width:10%;">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tempItem" class="temp_item">
                                                        <tr id="noData">
                                                            <td colspan="5" class="text-center">No data available in table</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $this->load->view('includes/terms_form',['termsList'=>$termsList,'termsConditions'=>(!empty($dataRow->termsConditions)) ? $dataRow->termsConditions : array()])?>  
                                </div>
                            </form>
                        </div>
                        <div class="card-footer bg-facebook">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-info waves-effect show_terms" >Terms & Conditions (<span id="termsCounter">0</span>)</button>
                                <span class="term_error text-danger font-bold"></span>
                                
                                
                                <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'savePurchaseOrder'});" ><i class="fa fa-check"></i> Save</button>

                                <button type="button" class="btn btn-secondary press-close-btn btn-close-modal save-form float-right m-r-10" onclick="window.location.href='<?=base_url($headData->controller)?>'"><i class="fa fa-times"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/work-order-form.js?v=<?= time() ?>"></script>
<script src="<?php echo base_url(); ?>assets/js/custom/calculate.js?v=<?= time() ?>"></script>
<?php
if(!empty($dataRow->itemList)):
    foreach($dataRow->itemList as $row):
        $row->row_index = "";
        $row->unit_name = $row->full_unit_name;
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;
?>