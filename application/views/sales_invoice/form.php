<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form autocomplete="off" id="saveSalesInvoice" data-res_function="resSaveInvoice" enctype="multipart/form-data">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="hiddenInput">
                                            <input type="hidden" name="id" id="id" class="trans_main_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                                            <input type="hidden" name="entry_type" id="entry_type" value="<?=(!empty($dataRow->entry_type))?$dataRow->entry_type:$entry_type?>">
                                            <input type="hidden" name="from_entry_type" id="from_entry_type" value="<?=(!empty($dataRow->from_entry_type))?$dataRow->from_entry_type:((!empty($from_entry_type))?$from_entry_type:"")?>">
                                            <input type="hidden" name="ref_id" id="ref_id" value="<?=(!empty($dataRow->ref_id))?$dataRow->ref_id:((!empty($ref_id))?$ref_id:"")?>">
                                            
                                            <input type="hidden" name="gst_type" id="gst_type" value="<?=(!empty($dataRow->gst_type))?$dataRow->gst_type:""?>">
                                            <input type="hidden" name="party_state_code" id="party_state_code" value="<?=(!empty($dataRow->party_state_code))?$dataRow->party_state_code:""?>">
                                            <input type="hidden" name="tax_class" id="tax_class" value="<?=(!empty($dataRow->tax_class))?$dataRow->tax_class:""?>">
                                            <input type="hidden" name="sp_acc_id" id="sp_acc_id" value="<?=(!empty($dataRow->sp_acc_id))?$dataRow->sp_acc_id:0?>">

                                            <input type="hidden" id="inv_type" value="SALES">
                                            <input type="hidden" id="tcs_applicable" value="">
                                            <input type="hidden" id="tcs_limit" value="">
                                            <input type="hidden" id="defual_tcs_per" value="">
                                            <input type="hidden" id="turnover" value="">
                                            <input type="hidden" id="vou_name_s" value="<?=(!empty($entryData))?$entryData->vou_name_short:""?>">
                                        </div>

                                        <div class="col-md-2 form-group <?=($this->cm_id_count == 1)?"hidden":""?>">
                                            <label for="cm_id">Select Unit</label>
                                            <select name="cm_id" id="cmId" class="form-control" data-selected_cm_id="<?=(!empty($dataRow->cm_id))?$dataRow->cm_id:""?>">
                                                <?=getCompanyListOptions($companyList,((!empty($dataRow->cm_id))?$dataRow->cm_id:""))?>
                                            </select>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="trans_number">Inv. No.</label>

                                            <div class="input-group">
                                                <input type="text" name="trans_prefix" id="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:((!empty($trans_prefix))?$trans_prefix:"")?>" readonly>
                                                <input type="text" name="trans_no" id="trans_no" class="form-control numericOnly" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:((!empty($trans_no))?$trans_no:"")?>" readonly>
                                            </div>

                                            <input type="hidden" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:((!empty($trans_number))?$trans_number:"")?>" readonly>
                                            <div class="error trans_number"></div>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="trans_date">Inv. Date</label>
                                            <input type="date" name="trans_date" id="trans_date" class="form-control fyDates req" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
                                        </div>

                                        <div class="col-md-<?=($this->cm_id_count > 1)?"4":"5"?> form-group debitMemo">
                                            <label for="party_id">Customer Name</label>

                                            <div class="float-right">	
                                                <!-- <span class="dropdown float-right">
                                                    <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                                                    <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY" x-placement="start-left" style="left: -87px;">
                                                        <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>

                                                        <?php
                                                            $custParam = "{'postData':{'party_category' : 1},'modal_id' : 'bs-left-lg-modal', 'controller' : 'parties','call_function':'addParty', 'form_id' : 'addSupplier', 'title' : 'Add Customer ', 'res_function' : 'resPartyMaster', 'js_store_fn' : 'customStore'}";

                                                            $supParam = "{'postData':{'party_category' : 2},'modal_id' : 'bs-left-lg-modal', 'controller' : 'parties','call_function':'addParty', 'form_id' : 'addSupplier', 'title' : 'Add Supplier ', 'res_function' : 'resPartyMaster', 'js_store_fn' : 'customStore'}";

                                                            $venParam = "{'postData':{'party_category' : 3},'modal_id' : 'bs-left-lg-modal', 'controller' : 'parties','call_function':'addParty', 'form_id' : 'addVendor', 'title' : 'Add Vendor ', 'res_function' : 'resPartyMaster', 'js_store_fn' : 'customStore'}";
                                                        ?>
                                                        <button type="button" class="dropdown-item " onclick="modalAction(<?=$custParam?>);" ><i class="fa fa-plus"></i> Customer</button>

                                                        <button type="button" class="dropdown-item hidden" onclick="modalAction(<?=$supParam?>);" ><i class="fa fa-plus"></i> Supplier</button>

                                                        <button type="button" class="dropdown-item hidden" onclick="modalAction(<?=$venParam?>);" ><i class="fa fa-plus"></i> Vendor</button>  
                                                    </div>
                                                </span> -->

                                                <span class="float-right m-r-10">
                                                    <a class="text-primary font-bold waves-effect waves-dark getPendingOrders" href="javascript:void(0)">+ Sales Order</a>
                                                </span>
                                            </div>

                                            <select name="party_id" id="party_id" class="form-control basic-select2 partyDetails partyOptions req" data-res_function="resPartyDetail" data-party_category="1">
                                                <option value="">Select Party</option>
                                                <?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:0))?>
                                            </select>

                                            <small>Cl. Balance : <span id="closing_balance">0</span></small>
                                            <small class="float-right">T.O. : <span id="Turnover">0</span></small>
                                        </div>

                                        <div class="col-md-<?=($this->cm_id_count > 1)?"2":"3"?> form-group debitMemo">
                                            <label for="gstin">GST NO.</label>
                                            <select name="gstin" id="gstin" class="form-control basic-select2">
                                                <option value="">Select GST No.</option>
                                                <?php
                                                    if(!empty($dataRow->party_id)):
                                                        foreach($gstinList as $row):
                                                            $selected = ($dataRow->gstin == $row->gstin)?"selected":"";
                                                            echo '<option value="'.$row->gstin.'" '.$selected.'>'.$row->gstin.'</option>';
                                                        endforeach;
                                                    endif;
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-md-<?=($this->cm_id_count > 1)?"4":"5"?> form-group cashMemo">
                                            <label for="party_name">Customer Name</label>
                                            <input type="text" name="party_name" id="party_name" class="form-control req" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""?>">
                                            <div class="error party_id"></div>
                                        </div>

                                        <div class="col-md-<?=($this->cm_id_count > 1)?"2":"3"?> form-group cashMemo">
                                            <label for="party_mobile">Mobile No.</label>
                                            <input type="text" name="masterDetails[t_col_1]" id="party_mobile" class="form-control" value="<?=(!empty($dataRow->party_mobile))?$dataRow->party_mobile:""?>">
                                        </div>

                                        <div class="col-md-12 form-group cashMemo">
                                            <label for="party_address">Customer Address</label>
                                            <input type="text" name="masterDetails[t_col_2]" id="party_address" class="form-control" value="<?=(!empty($dataRow->party_address))?$dataRow->party_address:""?>">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="memo_type">Memo Type</label>
                                            <select name="memo_type" id="memo_type" class="form-control" data-selected_memo_type="<?=(!empty($dataRow->memo_type))?$dataRow->memo_type:""?>">
                                                <option value="DEBIT" <?=(!empty($dataRow->memo_type) && $dataRow->memo_type == "DEBIT")?"selected":""?> >Debit</option>
                                                <option value="CASH" <?=(!empty($dataRow->memo_type) && $dataRow->memo_type == "CASH")?"selected":""?> >Cash</option>
                                            </select>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="sp_acc_id">GST Type </label>
                                            <select name="tax_class_id" id="tax_class_id" class="form-control basic-select2 req">
                                                <?=getTaxClassListOption($taxClassList,((!empty($dataRow->tax_class_id))?$dataRow->tax_class_id:0))?>
                                            </select>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="ship_to_id">Ship To</label>
                                            <select name="ship_to_id" id="ship_to_id" class="form-control basic-select2" data-selected_value="<?=(!empty($dataRow->ship_to_id)?$dataRow->ship_to_id:"")?>">
                                                <option value="">Select Ship To</option>
                                                <?php
                                                    if(!empty($dataRow->party_id)):
                                                        foreach($shipToList as $row):
                                                            $selected = ($dataRow->ship_to_id == $row->id)?"selected":"";
                                                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->ship_to.'</option>';
                                                        endforeach;
                                                    endif;
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6 form-group">
                                            <label for="remark">Notes</label>
                                            <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
                                        </div>

                                        <div class="col-md-2 form-group hidden">
                                            <label for="challan_no">Challan No.</label>
                                            <input type="text" name="challan_no" class="form-control" placeholder="Enter Challan No." value="<?= (!empty($dataRow->challan_no)) ? $dataRow->challan_no : "" ?>" />
                                        </div>

                                        <div class="col-md-2 form-group hidden">
                                            <label for="doc_no">PO. No.</label>
                                            <input type="text" name="doc_no" id="doc_no" class="form-control" value="<?=(!empty($dataRow->doc_no))?$dataRow->doc_no:""?>">
                                        </div>

                                        <div class="col-md-3 form-group hidden">
                                            <label for="doc_date">PO. Date</label>
                                            <input type="date" name="doc_date" id="doc_date" class="form-control" value="<?=(!empty($dataRow->doc_date))?$dataRow->doc_date:getFyDate()?>">
                                        </div>

                                        <div class="col-md-2 form-group hidden">
                                            <label for="apply_round">Apply Round Off</label>
                                            <select name="apply_round" id="apply_round" class="form-control">
                                                <option value="1" <?= (!empty($dataRow) && $dataRow->apply_round == 1) ? "selected" : "" ?>>Yes</option>
                                                <option value="0" <?= (!empty($dataRow) && $dataRow->apply_round == 0) ? "selected" : "" ?>>No</option>
                                            </select>
                                        </div>

                                        <div class="col-md-2 form-group exportData <?=(empty($dataRow))?"hidden":((!empty($dataRow->tax_class) && !in_array($dataRow->tax_class,["EXPORTGSTACC","EXPORTTFACC"]))?"hidden":"")?>">
                                            <label for="port_code">Port Code</label>
                                            <input type="text" name="port_code" id="port_code" class="form-control" value="<?=(!empty($dataRow->port_code))?$dataRow->port_code:""?>">
                                        </div>

                                        <div class="col-md-2 form-group exportData <?=(empty($dataRow))?"hidden":((!empty($dataRow->tax_class) && !in_array($dataRow->tax_class,["EXPORTGSTACC","EXPORTTFACC"]))?"hidden":"")?>">
                                            <label for="ship_bill_no">Shipping Bill No.</label>
                                            <input type="text" name="ship_bill_no" id="ship_bill_no" class="form-control" value="<?=(!empty($dataRow->ship_bill_no))?$dataRow->ship_bill_no:""?>">
                                        </div>

                                        <div class="col-md-2 form-group exportData <?=(empty($dataRow))?"hidden":((!empty($dataRow->tax_class) && !in_array($dataRow->tax_class,["EXPORTGSTACC","EXPORTTFACC"]))?"hidden":"")?>">
                                            <label for="ship_bill_date">Shipping Bill Date</label>
                                            <input type="date" name="ship_bill_date" id="ship_bill_date" class="form-control" value="<?=(!empty($dataRow->ship_bill_date))?$dataRow->ship_bill_date:""?>">
                                        </div>
                                        
                                    </div>

                                    <hr>
                                    <div class="error limit_error"></div>
                                    <div class="col-md-12" id="itemForm">                                        
                                        <div class="row form-group">
                                            <div id="itemInputs">
                                                <input type="hidden" class="itemFormInput" id="trans_id" value="" />
                                                <input type="hidden" class="itemFormInput" id="from_entry_type" value="" />
                                                <input type="hidden" class="itemFormInput" id="ref_id" value="" />
                                                <input type="hidden" class="itemFormInput" id="row_index" value="">
                                                <input type="hidden" class="itemFormInput" id="item_code" value="" />
                                                <input type="hidden" class="itemFormInput" id="item_name" value="" />
                                                <input type="hidden" class="itemFormInput" id="item_type" value="0" />
                                                <input type="hidden" class="itemFormInput" id="stock_eff" value="1" />
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label for="item_id">Product Name</label>
                                                
                                                <!-- <div class="float-right">	
                                                    <span class="dropdown float-right">
                                                        <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                                                        <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
                                                            <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>

                                                            <?php
                                                                $productParam = "{'postData':{'item_type':1},'modal_id' : 'bs-left-lg-modal','controller':'items', 'call_function':'addItem', 'form_id' : 'addItem', 'title' : 'Add Product','res_function':'resItemMaster','js_store_fn':'customStore'}";

                                                                $rmParam = "{'postData':{'item_type':3},'modal_id' : 'bs-left-lg-modal','controller':'items', 'call_function':'addItem', 'form_id' : 'addItem', 'title' : 'Add Raw Material','res_function':'resItemMaster','js_store_fn':'customStore'}";

                                                                $conParam = "{'postData':{'item_type':2},'modal_id' : 'bs-left-lg-modal','controller':'items', 'call_function':'addItem', 'form_id' : 'addItem', 'title' : 'Add Consumable','res_function':'resItemMaster','js_store_fn':'customStore'}";
                                                            ?>
                                                            <button type="button" class="dropdown-item" onclick="modalAction(<?=$productParam?>);"><i class="fa fa-plus"></i> Product</button>

                                                            <button type="button" class="dropdown-item hidden" onclick="modalAction(<?=$rmParam?>);"><i class="fa fa-plus"></i> Raw Material</button>

                                                            <button type="button" class="dropdown-item hidden" onclick="modalAction(<?=$conParam?>);"><i class="fa fa-plus"></i> Consumable</button>
                                                        </div>
                                                    </span>
                                                </div> -->

                                                <select id="item_id" class="form-control basic-select2 itemDetails itemOptions itemFormInput partyReq" data-res_function="resItemDetail" data-item_type="1">
                                                    <option value="">Select Product Name</option>
                                                    <?=getItemListOption($itemList); ?>
                                                </select>
                                            </div>                                            
                                            <div class="col-md-8 form-group">
                                                <div class="input-group">
                                                    <label for="qty" class="col-md-3">Qty.</label>
                                                    <label for="price" class="col-md-3">Price</label>
                                                    <label for="org_price" class="col-md-3">MRP</label>
                                                    <label for="disc_per" class="col-md-3">Disc. (%)</label>
                                                </div>
                                                <div class="input-group">
                                                    <input type="text" id="qty" class="form-control floatOnly calculateQty req itemFormInput" value="0" >
                                                    <input type="text" id="price" class="form-control floatOnly calculatePrice req itemFormInput" value="0"  />
                                                    <input type="text" id="org_price" class="form-control floatOnly calculatePrice req itemFormInput" tabIndex="-1" value="0" readonly/>
                                                    <input type="text" id="disc_per" class="form-control calculatePrice floatOnly itemFormInput" value="0">
                                                </div>
                                            </div>  
                                            <div class="col-md-4 form-group hidden">
                                                <label for="unit_id">Unit</label>        
                                                <select id="unit_id" class="form-control basic-select2 itemFormInput">
                                                    <option value="">Select Unit</option>
                                                    <?=getItemUnitListOption($unitList)?>
                                                </select> 
                                                <input type="hidden" id="unit_name" class="form-control itemFormInput" value="" />                       
                                            </div>
                                            <div class="col-md-4 form-group hidden">
                                                <label for="hsn_code">HSN Code</label>
                                                <select id="hsn_code" class="form-control basic-select2 itemFormInput">
                                                    <option value="">Select HSN Code</option>
                                                    <?=getHsnCodeListOption($hsnList)?>
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group hidden">
                                                <label for="gst_per">GST Per.(%)</label>
                                                <select id="gst_per" class="form-control basic-select2 itemFormInput">
                                                    <?php
                                                        foreach($this->gstPer as $per=>$text):
                                                            echo '<option value="'.$per.'">'.$text.'</option>';
                                                        endforeach;
                                                    ?>
                                                </select>
                                            </div>
                                            
                                            <!-- <div class="col-md-1 form-group">
                                                <label for="">&nbsp;</label>
                                                <button type="button" class="btn btn-outline-success btn-block saveItem"><i class="fa fa-plus"></i> Add</button>
                                            </div> -->
                                            
                                            <div class="col-md-12 form-group">
                                                <label for="item_remark">Remark</label>
                                                <div class="input-group">
                                                    <input type="text" id="item_remark" class="form-control itemFormInput" value="" />

                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-outline-success saveItem m-l-3"><i class="fa fa-plus"></i> Add</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <div class="error itemData"></div>
                                        <div class="row form-group">
                                            <div class="table-responsive">
                                                <table id="salesInvoiceItems" class="table table-striped table-bordered">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th style="width:5%;">#</th>
                                                            <th>Item Name</th>
                                                            <th>HSN Code</th>
                                                            <th>Qty.</th>
                                                            <th>Unit</th>
                                                            <th>Price</th>
                                                            <th>Disc.</th>
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
                                                            <td colspan="15" class="text-center">No data available in table</td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot class="thead-dark">
                                                        <tr>
                                                            <th colspan="3" class="text-right">Total</th>
                                                            <th id="totalQty">0</th>
                                                            <th colspan="3"></th>
                                                            <th class="igstCol"></th>
                                                            <th class="cgstCol"></th>
                                                            <th class="sgstCol"></th>
                                                            <th class="amountCol"></th>
                                                            <th class="netAmtCol"></th>
                                                            <th colspan="2"></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr>

                                    <div id="taxSummaryHtml"></div>
                                    <?php //$this->load->view('includes/tax_summary',['expenseList'=>$expenseList,'taxList'=>$taxList,'ledgerList'=>$ledgerList,'dataRow'=>((!empty($dataRow))?$dataRow:array())])?>

                                    <?php $this->load->view('includes/terms_form',['termsList'=>$termsList,'termsConditions'=>(!empty($dataRow->termsConditions)) ? $dataRow->termsConditions : array()])?>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer bg-facebook">
                            <div class="col-md-12"> 
                                <button type="button" class="btn btn-success waves-effect show_terms hidden" >Terms & Conditions (<span id="termsCounter">0</span>)</button>
                                <span class="term_error text-danger font-bold"></span>

                                <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'saveSalesInvoice'});" ><i class="fa fa-check"></i> Save </button>

                                <button type="button" class="btn btn-secondary press-close-btn btn-close-modal save-form float-right m-r-10" onclick="window.location.href='<?=base_url($headData->controller)?>'"><i class="fa fa-times"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- <div class="modal modal-right fade" id="itemModel" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header" style="display:block;"><h4 class="modal-title">Add or Update Item</h4></div>
            <div class="modal-body">
                <form id="itemForm">
                    <div class="col-md-12" >
                        <div class="row form-group">
                            <div id="itemInputs">
                                <input type="hidden" id="id" name="id" value="" />
                                <input type="hidden" name="from_entry_type" id="from_entry_type" value="" />
                                <input type="hidden" name="ref_id" id="ref_id" value="" />
                                <input type="hidden" name="row_index" id="row_index" value="">
                                <input type="hidden" name="item_code" id="item_code" value="" />
                                <input type="hidden" name="item_name" id="item_name" value="" />
                                <input type="hidden" name="item_type" id="item_type" value="0" />
                                <input type="hidden" name="stock_eff" id="stock_eff" value="1" />
                                <input type="hidden" name="packing_qty" id="packing_qty" value="" />
                                <input type="hidden" name="packing_unit_qty" id="packing_unit_qty" value="" />
                            </div>

                            <div class="col-md-12 form-group">
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
                                            ?>
                                            <button type="button" class="dropdown-item" onclick="modalAction(<?=$productParam?>);"><i class="fa fa-plus"></i> Product</button>

                                            <button type="button" class="dropdown-item hidden" onclick="modalAction(<?=$rmParam?>);"><i class="fa fa-plus"></i> Raw Material</button>

                                            <button type="button" class="dropdown-item hidden" onclick="modalAction(<?=$conParam?>);"><i class="fa fa-plus"></i> Consumable</button>
                                        </div>
                                    </span>
                                </div>

                                <input type="hidden" name="item_name" id="item_name" class="form-control" value="" />
                                <select name="item_id" id="item_id" class="form-control basic-select2 itemDetails itemOptions" data-res_function="resItemDetail" data-item_type="1">
                                    <option value="">Select Product Name</option>
                                    <?=getItemListOption($itemList); ?>
                                </select>
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="total_box">Cartoon Qty</label>
                                <input type="text" name="total_box" id="total_box" class="form-control floatOnly calculateQty req" value="0">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="strip_qty">Box Qty</label>
                                <input type="text" name="strip_qty" id="strip_qty" class="form-control floatOnly calculateQty req" value="0">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="qty">Total Qty.</label>
                                <input type="text" name="qty" id="qty" class="form-control floatOnly calculateQty req" value="0">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="disc_per">Disc. (%)</label>
                                <input type="text" name="disc_per" id="disc_per" class="form-control calculatePrice floatOnly" value="0">
                            </div>                            
                            <div class="col-md-2 form-group">
                                <label for="org_price">MRP</label>
                                <input type="text" name="org_price" id="org_price" class="form-control floatOnly calculatePrice req" value="0" />
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="price">Price</label>
                                <input type="text" name="price" id="price" class="form-control floatOnly calculatePrice req" value="0" readonly />
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="unit_id">Unit</label>        
                                <select name="unit_id" id="unit_id" class="form-control basic-select2">
                                    <option value="">Select Unit</option>
                                    <?=getItemUnitListOption($unitList)?>
                                </select> 
                                <input type="hidden" name="unit_name" id="unit_name" class="form-control" value="" />                       
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="hsn_code">HSN Code</label>
                                <select name="hsn_code" id="hsn_code" class="form-control basic-select2">
                                    <option value="">Select HSN Code</option>
                                    <?=getHsnCodeListOption($hsnList)?>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="gst_per">GST Per.(%)</label>
                                <select name="gst_per" id="gst_per" class="form-control basic-select2">
                                    <?php
                                        foreach($this->gstPer as $per=>$text):
                                            echo '<option value="'.$per.'">'.$text.'</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="item_remark">Remark</label>
                                <input type="text" name="item_remark" id="item_remark" class="form-control" value="" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-success saveItem btn-save" data-fn="save"><i class="fa fa-check"></i> Save</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-warning saveItem btn-save-close" data-fn="save_close"><i class="fa fa-check"></i> Save & Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-item-form-close" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div> -->

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/sales-invoice-form.js?v=<?= time() ?>"></script>
<!-- <script src="<?php echo base_url(); ?>assets/js/custom/row-attachment.js?v=<?= time() ?>"></script> -->
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
?>