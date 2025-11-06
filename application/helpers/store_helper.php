<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getStoreDtHeader($page){
    /* Location Master header */
    $data['storeLocation'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['storeLocation'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['storeLocation'][] = ["name"=>"Store Name"];
    $data['storeLocation'][] = ["name"=>"Location"];
    $data['storeLocation'][] = ["name"=>"Remark"];

    /* Gate Entry */
    $data['gateEntry'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['gateEntry'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['gateEntry'][] = ["name"=> "GE No.", "textAlign" => "center"];
    $data['gateEntry'][] = ["name" => "GE Date", "textAlign" => "center"];
    $data['gateEntry'][] = ["name" => "Transport"];
    $data['gateEntry'][] = ["name" => "LR No."];
    $data['gateEntry'][] = ["name" => "Vehicle Type"];
    $data['gateEntry'][] = ["name" => "Vehicle No."];
    $data['gateEntry'][] = ['name' => "Invoice No."];
    $data['gateEntry'][] = ['name' => "Invoice Date"];
    $data['gateEntry'][] = ['name' => "Challan No."];
    $data['gateEntry'][] = ['name' => "Challan Date"];

    /* Gate Inward Pending GE Tab Header */
    $data['pendingGE'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['pendingGE'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['pendingGE'][] = ["name"=> "GE No.", "textAlign" => "center"];
    $data['pendingGE'][] = ["name" => "GE Date", "textAlign" => "center"];
    $data['pendingGE'][] = ["name" => "Party Name"];
    $data['pendingGE'][] = ["name" => "Inv. No."];
    $data['pendingGE'][] = ["name" => "Inv. Date"];
    $data['pendingGE'][] = ['name' => "CH. NO."];
    $data['pendingGE'][] = ['name' => "CH. Date"];

    /* Gate Inward Pending/Compeleted Tab Header */
    $data['gateInward'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['gateInward'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['gateInward'][] = ["name"=> "GI No.", "textAlign" => "center"];
    $data['gateInward'][] = ["name" => "GI Date", "textAlign" => "center"];
    $data['gateInward'][] = ["name" => "Supplier"];
	$data['gateInward'][] = ["name" => "Project"];
    $data['gateInward'][] = ["name" => "Item Name"];
    $data['gateInward'][] = ["name" => "Qty"];
    $data['gateInward'][] = ["name" => "PO. NO."]; 
    
    /* FG Stock Inward Table Header */
    $data['stockTrans'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['stockTrans'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['stockTrans'][] = ["name" => "Unit"];
    $data['stockTrans'][] = ["name" => "Date"];
    $data['stockTrans'][] = ["name" => "Item Name"];
    $data['stockTrans'][] = ["name" => "Category"];
    $data['stockTrans'][] = ["name" => "Cartoon Qty"];
    $data['stockTrans'][] = ["name" => "Box Qty"];
    $data['stockTrans'][] = ["name" => "Total Qty"];
    $data['stockTrans'][] = ["name" => "Remark"];

    /* RM Stock Inward Table Header */
    $data['rmStockTrans'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['rmStockTrans'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['rmStockTrans'][] = ["name" => "Unit"];
    $data['rmStockTrans'][] = ["name" => "Date"];
    $data['rmStockTrans'][] = ["name" => "Item Name"];
    $data['rmStockTrans'][] = ["name" => "Category"];
    $data['rmStockTrans'][] = ["name" => "Qty"];
    $data['rmStockTrans'][] = ["name" => "Remark"];

    /* Material Issue Table Header */
    $data['materialIssue'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['materialIssue'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['materialIssue'][] = ["name" => "Issue No"];
    $data['materialIssue'][] = ["name" => "Issue Date"];
    $data['materialIssue'][] = ["name" => "Vendor"]; 
    $data['materialIssue'][] = ["name" => "Product"];
    $data['materialIssue'][] = ["name" => "Project"];
    $data['materialIssue'][] = ["name" => "Issue Qty"];
    $data['materialIssue'][] = ["name" => "Return Qty"];
    $data['materialIssue'][] = ["name" => "Remark"];

    /* Stock Transfer Table Header */
    $data['stockTransferLog'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['stockTransferLog'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
	$data['stockTransferLog'][] = ["name" => "Transfer No."];
    $data['stockTransferLog'][] = ["name" => "Date"];
    $data['stockTransferLog'][] = ["name" => "Item"];
    $data['stockTransferLog'][] = ["name" => "From Project"];
    $data['stockTransferLog'][] = ["name" => "To Project"];
    $data['stockTransferLog'][] = ["name" => "Transfer Qty"];
    $data['stockTransferLog'][] = ["name" => "Transfer By"];
    $data['stockTransferLog'][] = ["name" => "Issue To"];
    $data['stockTransferLog'][] = ["name" => "Remark"];
	
	/*Opening Stock Table Header */
    $data['openingStock'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['openingStock'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['openingStock'][] = ["name" => "Project"];
    $data['openingStock'][] = ["name" => "Item"];
    $data['openingStock'][] = ["name" => "Qty"];
    $data['openingStock'][] = ["name" => "Created By"];
    $data['openingStock'][] = ["name" => "Created At"];
  
    return tableHeader($data[$page]);
}

/* Store Location Data */
function getStoreLocationData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Store Location'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editStoreLocation', 'title' : 'Update Store Location','call_function':'edit'}";

    $editButton = ''; $deleteButton = '';
    if(!empty($data->ref_id) && empty($data->store_type)):
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    endif;

    if($data->final_location == 0):
        $locationName = '<a href="' . base_url("storeLocation/list/" . $data->id) . '">' . $data->location . '</a>';
    else:
        $locationName = $data->location;
    endif;
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->store_name,$locationName,$data->remark];
}

/* Gate Entry Data  */
function getGateEntryData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Gate Entry'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editGateEntry', 'title' : 'Update Gate Entry','call_function':'edit'}";

    $editButton = "";
    $deleteButton = "";
    if($data->trans_status == 0):
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    endif;

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->transport_name,$data->lr,$data->vehicle_type_name,$data->vehicle_no,$data->inv_no,((!empty($data->inv_date))?formatDate($data->inv_date):""),$data->doc_no,((!empty($data->doc_date))?formatDate($data->doc_date):"")];
}

/* GateInward Data Data  */
function getGateInwardData($data){
    $deleteButton = $editButton = '';
	
    if ($data->ref_id <= 0) {
        $deleteParam = "{'postData':{'id' : ".$data->id.", 'grn_id' : '".$data->grn_id."', 'project_id' : '".$data->project_id."'},'message' : 'Gate Inward'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

        $editParam = "{'postData':{'id' : ".$data->id.", 'grn_id' : '".$data->grn_id."'}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'editGateInward', 'title' : 'Update Gate Inward', 'call_function' : 'edit', 'fnsave' : 'updateGRN'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    }

    $printBtn = '<a class="btn btn-dribbble" href="'.base_url('gateInward/printGRN/'.$data->grn_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print"></i></a>';

    $action = getActionButton($printBtn.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->project_name,$data->item_name,$data->qty,$data->po_number];
}

/* FG Stock Inward Table Data */
function getStockTransData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Stock'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($deleteButton);

    if(in_array($data->item_type,[1,4])):
        return [$action,$data->sr_no,$data->company_code,formatDate($data->ref_date),$data->item_name,$data->category_name,floatval($data->total_box),floatval($data->strip_qty),floatval($data->qty),$data->remark];
    else:
        return [$action,$data->sr_no,$data->company_code,formatDate($data->ref_date),$data->item_name,$data->category_name,floatval($data->qty),$data->remark];
    endif;
}

/* Material Issue Table Data */
function getMaterialIssueData($data){    
    $deleteButton = $printBtn = $returnButton = $returnLogBtn = '';
    if($data->return_qty <= 0){
        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Material Issue','fndelete':'deleteIssuedItem'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';    
    }
    $printBtn = '<a href="'.base_url('store/issueMaterialPrint/'.$data->id).'" type="button" class="btn btn-primary" datatip="Print" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
        
    if($data->is_return == 1 && empty($data->ref_id)){
        $returnParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'materialReturn', 'title' : 'Material Return ( Issue Qty : ".$data->issue_qty." )','call_function':'materialReturn','fnsave':'saveMaterialReturn'}";
        $returnButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Material Return" flow="down" onclick="modalAction('.$returnParam.');"><i class="mdi mdi-reply"></i></a>';

    }
    
    $returnLogParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'materialReturnLog', 'call_function' : 'materialReturnLog', 'title' : 'Material Return Log', 'button' : 'close'}";
    $returnLogBtn = '<a href="javascript:void(0)" datatip="Material Return Log" flow="down" onclick="modalAction('.$returnLogParam.');">'.floatval($data->return_qty).'</a>';

	$action = getActionButton($returnButton.$printBtn.$deleteButton);
	return [$action,$data->sr_no,$data->issue_number,formatDate($data->issue_date),$data->party_name,$data->item_name,$data->project_name,floatval($data->issue_qty), $returnLogBtn,$data->remark];
}

function getStockTransferData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Stock','fndelete':'deleteTransferedLog'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $printBtn = '<a href="'.base_url('store/stockTransferPrint/'.$data->id).'" type="button" class="btn btn-primary" datatip="Print" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
    
    $action = getActionButton($printBtn.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->item_name,$data->from_project_name,$data->to_project_name,floatval($data->qty),$data->transfer_by,(!empty($data->issued_to) ? $data->issued_to : ''),$data->remark];
} 

function getOpeningStockData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Stock','fndelete':'deleteOpeningStock'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    
    $action = getActionButton($deleteButton);

    return [$action,$data->sr_no,$data->project_name,$data->item_name,floatval($data->qty),$data->created_name,formatDate($data->created_at)];
} 
?>