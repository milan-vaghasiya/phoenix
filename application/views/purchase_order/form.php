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
                                            <input type="hidden" name="entry_type" id="entry_type" value="<?=(!empty($dataRow->entry_type))?$dataRow->entry_type:$entry_type?>">
                                            <input type="hidden" name="from_entry_type" id="from_entry_type" value="<?=(!empty($dataRow->from_entry_type))?$dataRow->from_entry_type:((!empty($from_entry_type))?$from_entry_type:"")?>">
                                            <input type="hidden" name="ref_id" id="ref_id" value="<?=(!empty($dataRow->ref_id))?$dataRow->ref_id:((!empty($ref_id))?$ref_id:"")?>">

                                            <input type="hidden" name="party_name" id="party_name" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""?>">
                                            <input type="hidden" name="gst_type" id="gst_type" value="<?=(!empty($dataRow->gst_type))?$dataRow->gst_type:""?>">
                                            <input type="hidden" name="party_state_code" id="party_state_code" value="<?=(!empty($dataRow->party_state_code))?$dataRow->party_state_code:""?>">
                                            <input type="hidden" name="tax_class" id="tax_class" value="<?=(!empty($dataRow->tax_class))?$dataRow->tax_class:""?>">
                                            <input type="hidden" name="sp_acc_id" id="sp_acc_id" value="<?=(!empty($dataRow->sp_acc_id))?$dataRow->sp_acc_id:0?>">
                                            <input type="hidden" name="apply_round" id="apply_round" value="<?=(!empty($dataRow->apply_round))?$dataRow->apply_round:"1"?>">
                                            <input type="hidden" name="gstin" id="gstin" value="<?=(!empty($dataRow->gstin))?$dataRow->gstin:""?>">

                                            <input type="hidden" name="ledger_eff" id="ledger_eff" value="0">
                                            <input type="hidden" id="inv_type" value="PURCHASE">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="trans_number">PO. No.</label>

                                            <div class="input-group">
                                                <input type="text" name="trans_prefix" id="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:((!empty($trans_prefix))?$trans_prefix:"")?>" readonly>
                                                <input type="text" name="trans_no" id="trans_no" class="form-control numericOnly" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:((!empty($trans_no))?$trans_no:"")?>">
                                            </div>

                                            <input type="hidden" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:((!empty($trans_number))?$trans_number:"")?>" readonly>

                                            <div class="error trans_number"></div>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="trans_date">PO. Date</label>
                                            <input type="date" name="trans_date" id="trans_date" class="form-control fyDates req" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
                                        </div>

                                        <div class="col-md-5 form-group">
                                            <label for="party_id">Party Name</label>
                                            <div class="float-right">	
                                                <span class="dropdown float-right">
                                                    <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                                                    <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY" x-placement="start-left" style="left: -87px;">
                                                        <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>

                                                        <?php
                                                            $custParam = "{'postData':{'party_category' : 1},'modal_id' : 'bs-left-lg-modal', 'controller' : 'parties','call_function':'addParty', 'form_id' : 'addSupplier', 'title' : 'Add Customer ', 'res_function' : 'resPartyMaster', 'js_store_fn' : 'customStore'}";

                                                            $supParam = "{'postData':{'party_category' : 2},'modal_id' : 'bs-left-lg-modal', 'controller' : 'parties','call_function':'addParty', 'form_id' : 'addSupplier', 'title' : 'Add Supplier ', 'res_function' : 'resPartyMaster', 'js_store_fn' : 'customStore'}";

                                                            $venParam = "{'postData':{'party_category' : 3},'modal_id' : 'bs-left-lg-modal', 'controller' : 'parties','call_function':'addParty', 'form_id' : 'addVendor', 'title' : 'Add Vendor ', 'res_function' : 'resPartyMaster', 'js_store_fn' : 'customStore'}";
                                                        ?>
                                                        <button type="button" class="dropdown-item hidden" onclick="modalAction(<?=$custParam?>);" ><i class="fa fa-plus"></i> Customer</button>

                                                        <button type="button" class="dropdown-item" onclick="modalAction(<?=$supParam?>);" ><i class="fa fa-plus"></i> Supplier</button>

                                                        <button type="button" class="dropdown-item" onclick="modalAction(<?=$venParam?>);" ><i class="fa fa-plus"></i> Vendor</button>  
                                                    </div>
                                                </span>
                                            </div>
                                            <select name="party_id" id="party_id" class="form-control basic-select2 partyDetails partyOptions req" data-res_function="resPartyDetail" data-party_category="2,3">
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

                                        <div class="col-md-12?> form-group">
                                            <label for="master_t_col_3">Delivery Address</label>
                                            <input type="text" name="masterDetails[t_col_3]" id="master_t_col_3" class="form-control" value="<?=(!empty($dataRow->delivery_address))?$dataRow->delivery_address:""?>">
                                        </div>

                                    </div>

                                    <hr>
                                    <div class="col-md-12" id="itemForm">
                                        <div class="row">
                                            <div id="itemInputs">
                                                <input type="hidden"  id="id" value="" class="itemFormInput"/>
                                                <input type="hidden" id="from_entry_type" id="from_entry_type" value="" class="itemFormInput"/>
                                                <input type="hidden" id="ref_id" value="" class="itemFormInput" />
                                                <input type="hidden" id="req_id" value="" class="itemFormInput" />
                                                <input type="hidden"  id="row_index" value="" class="itemFormInput" />
                                                <input type="hidden" id="item_code" value="" class="itemFormInput"/>
                                                <input type="hidden"  id="item_name" value="" class="itemFormInput"/>
                                                <input type="hidden"  id="item_type" value="" class="itemFormInput"/>
                                                <!-- <input type="hidden" id="gst_per" value="" class="itemFormInput"/> -->
                                            </div>
                                            <div class="col-md-5 form-group">
                                                <label for="item_id">Product Name</label>
                                                <div class="float-right">	
                                                    <span class="dropdown float-right">
                                                        <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                                                        <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
                                                            <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>

                                                            <?php
                                                                $productParam = "{'postData':{'item_type':1},'modal_id' : 'bs-left-lg-modal','controller':'items', 'call_function':'addItem', 'form_id' : 'addItem', 'title' : 'Add Product','res_function':'resItemMaster','js_store_fn':'customStore'}";

                                                                $rmParam = "{'postData':{'item_type':3},'modal_id' : 'bs-left-lg-modal','controller':'items', 'call_function':'addItem', 'form_id' : 'addItem', 'title' : 'Add Raw Material','res_function':'resItemMaster','js_store_fn':'customStore'}";

                                                                $conParam = "{'postData':{'item_type':2},'modal_id' : 'bs-left-lg-modal','controller':'items', 'call_function':'addItem', 'form_id' : 'addItem', 'title' : 'Add Consumable','res_function':'resItemMaster','js_store_fn':'customStore'}";

                                                                $serviceParam = "{'postData':{'item_type':10},'modal_id' : 'bs-left-lg-modal','controller':'items', 'call_function':'addItem', 'form_id' : 'addItem', 'title' : 'Add Service Item','res_function':'resItemMaster','js_store_fn':'customStore'}";
                                                            ?>
                                                            <button type="button" class="dropdown-item hidden" onclick="modalAction(<?=$productParam?>);"><i class="fa fa-plus"></i> Product</button>

                                                            <button type="button" class="dropdown-item" onclick="modalAction(<?=$rmParam?>);"><i class="fa fa-plus"></i> Raw Material</button>

                                                            <button type="button" class="dropdown-item" onclick="modalAction(<?=$conParam?>);"><i class="fa fa-plus"></i> Consumable</button>

                                                            <button type="button" class="dropdown-item" onclick="modalAction(<?=$serviceParam?>);"><i class="fa fa-plus"></i> Service Item</button>
                                                        </div>
                                                    </span>
                                                </div>
                                                <select  id="item_id" class="form-control basic-select2 itemDetails itemOptions itemFormInput" data-res_function="resItemDetail" data-item_type="2,3">
                                                    <option value="">Select Product Name</option>
                                                    <?=getItemListOption($itemList)?>
                                                </select>
                                            </div>
                                            <div class="col-md-2 form-group">
                                                <label for="qty">Qty.</label>
                                                <input type="text" id="qty" class="form-control floatOnly calculateQty req itemFormInput" value="0">
                                            </div>
                                            <div class="col-md-2 form-group">
                                                <label for="price">Price</label>
                                                <input type="text" id="price" class="form-control floatOnly req itemFormInput" value="0" />
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label for="gst_per">GST (%)</label>
                                                <select id="gst_per" class="form-control basic-select2 itemFormInput" >
                                                    <?php
                                                        foreach($this->gstPer as $per=>$text):
                                                            $selected = (!empty($dataRow->gst_per) && floatVal($dataRow->gst_per) == $per)?"selected":"";
                                                            echo '<option value="'.$per.'" '.$selected.'>'.$text.'</option>';
                                                        endforeach;
                                                    ?>
                                                </select>
                                            </div>  
                                            <div class="col-md-11 form-group">
                                                <label for="item_remark">Remark</label>
                                                <input type="text"  id="item_remark" class="form-control itemFormInput" value="" />
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
                                        <!-- <div class="col-md-6">
                                            <button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button>
                                        </div> -->
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
                                                            <th>Qty.</th>
                                                            <th>Price</th>
                                                            <th class="igstCol">IGST</th>
                                                            <th class="cgstCol">CGST</th>
                                                            <th class="sgstCol">SGST</th>
                                                            <th class="amountCol">Amount</th>
                                                            <th class="netAmtCol">Amount</th>
                                                            <th>Remark</th>
                                                            <th class="text-center" style="width:10%;">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tempItem" class="temp_item">
                                                        <tr id="noData">
                                                            <td colspan="12" class="text-center">No data available in table</td>
                                                        </tr>
                                                    </tbody>
													<tfoot class="thead-dark">
                                                        <tr>
                                                            <th colspan="2" class="text-right">Total</th>
                                                            <th id="totalQty">0</th>
                                                            <th ></th>
                                                            <th id="totaligst" class="igstCol">0</th>
                                                            <th id="totalcgst" class="cgstCol">0</th>
                                                            <th id="totalsgst" class="sgstCol">0</th>
                                                            <th id="totalamt" class="amountCol">0</th>
                                                            <th id="totalnetamt" class="netAmtCol">0</th>
                                                            <th colspan="2"></th>
                                                        </tr>
                                                        <input type="hidden" name="taxable_amount" id="taxable_amount">
                                                        <input type="hidden" name="total_amount" id="total_amount">
                                                        <input type="hidden" name="net_amount" id="net_amount">
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr>

                                    <!--<div id="taxSummaryHtml"></div>

                                    <hr>-->

                                    <div class="row">                                    
                                        <div class="col-md-12 form-group">
                                            <label for="remark">Remark</label>
                                            <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
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
<script src="<?php echo base_url(); ?>assets/js/custom/purchase-order-form.js?v=<?= time() ?>"></script>
<script src="<?php echo base_url(); ?>assets/js/custom/calculate.js?v=<?= time() ?>"></script>
<script>
var taxSummary = <?=json_encode(((!empty($dataRow))?$dataRow:array()))?>;
</script>
<?php
if(!empty($dataRow->itemList)):
    foreach($dataRow->itemList as $row):
        $row->row_index = "";
        $row->gst_per = floatVal($row->gst_per);
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;

if(!empty($orderItemList)):
    foreach($orderItemList as $row):
        $row->row_index = "";
        $row->ref_id = $row->id;
        $row->id = "";
        $row->qty = $row->req_qty;
        $row->amount = round(($row->qty * $row->price),2);
        if(!empty($row->disc_per)):
            $row->disc_amount = round((($row->amount * $row->disc_per) / 100),2);
            $row->taxable_amount = round(($row->amount - $row->disc_amount),2);
        else:
            $row->taxable_amount = $row->amount;
        endif;

        $row->gst_per = $row->igst_per = floatVal($row->gst_per);
        $row->gst_amount = $row->igst_amount = round((($row->taxable_amount * $row->gst_per) / 100),2);

        $row->cgst_per = $row->sgst_per = round(($row->gst_per / 2),2);
        $row->cgst_amount = $row->sgst_amount = round(($row->gst_amount / 2),2);

        $row->net_amount = round(($row->taxable_amount + $row->gst_amount),2);
        $row->item_remark = "";

        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;

if(!empty($enqItemList)): 
    foreach($enqItemList as $row):
        $row->from_entry_type = $row->entry_type;
        $row->ref_id = $row->id;
        $row->qty = $row->pending_qty;
        $row->com_qty = $row->qty;
        $row->req_id = $row->req_id;
        $row->id = "";          
        $row->row_index = "";

        $row->amount = round(($row->qty * $row->price),2);
        if(!empty($row->disc_per)):
            $row->disc_amount = round((($row->amount * $row->disc_per) / 100),2);
            $row->taxable_amount = round(($row->amount - $row->disc_amount),2);
        else:
            $row->taxable_amount = $row->amount;
        endif;

        $row->gst_per = $row->igst_per = floatVal($row->gst_per);
        $row->gst_amount = $row->igst_amount = round((($row->taxable_amount * $row->gst_per) / 100),2);

        $row->cgst_per = $row->sgst_per = round(($row->gst_per / 2),2);
        $row->cgst_amount = $row->sgst_amount = round(($row->gst_amount / 2),2);

        $row->net_amount = round(($row->taxable_amount + $row->gst_amount),2);
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;

if(!empty($reqItemList)):
    foreach($reqItemList as $row):
		$pending_qty = $row->qty - $row->po_qty;
	
		if($pending_qty > 0):
			$row->req_id = $row->id;
			$row->com_qty = $pending_qty;
			$row->qty = $pending_qty;
			$row->unit_name = $row->uom;
			$row->item_remark = $row->remark;
			$row->entry_type = "";
			$row->row_index = "";
			$row->id = "";

			$row->amount = round(($row->com_qty * $row->price),2);
			if(!empty($row->disc_per)):
				$row->disc_amount = round((($row->amount * $row->disc_per) / 100),2);
				$row->taxable_amount = round(($row->amount - $row->disc_amount),2);
			else:
				$row->taxable_amount = $row->amount;
			endif;

			$row->gst_per = $row->igst_per = floatVal($row->gst_per);
			$row->gst_amount = $row->igst_amount = round((($row->taxable_amount * $row->gst_per) / 100),2);

			$row->cgst_per = $row->sgst_per = round(($row->gst_per / 2),2);
			$row->cgst_amount = $row->sgst_amount = round(($row->gst_amount / 2),2);

			$row->net_amount = round(($row->taxable_amount + $row->gst_amount),2);
			$row = json_encode($row);
			echo '<script>AddRow('.$row.');</script>';
		endif;
    endforeach;
endif;
?>