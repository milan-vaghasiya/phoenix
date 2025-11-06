<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getMasterDtHeader($page){
	
    /* Customer Header */
    $data['customer'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
	$data['customer'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""]; 
	$data['customer'][] = ["name"=>"Company Name"];
    $data['customer'][] = ["name"=>"District"];
    $data['customer'][] = ["name"=>"Business Type"];
    $data['customer'][] = ["name"=>"Contact Person"];
    $data['customer'][] = ["name"=>"Contact No."];

    /* Supplier Header */
    $data['supplier'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
	$data['supplier'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""]; 
	$data['supplier'][] = ["name"=>"Company Name"];
    $data['supplier'][] = ["name"=>"District"];
    $data['supplier'][] = ["name"=>"Business Type"];
    $data['supplier'][] = ["name"=>"Contact Person"];
    $data['supplier'][] = ["name"=>"Contact No."];

    /* Vendor Header */
    $data['vendor'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
	$data['vendor'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""]; 
    $data['vendor'][] = ["name"=>"Company Name"];
    $data['vendor'][] = ["name"=>"District"];
    $data['vendor'][] = ["name"=>"Business Type"];
    $data['vendor'][] = ["name"=>"Contact Person"];
    $data['vendor'][] = ["name"=>"Contact No."];
	
   /* Director Header */
    $data['director'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
    $data['director'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""]; 
    $data['director'][] = ["name"=>"Director Name"];
    $data['director'][] = ["name"=>"Contact No."];
    $data['director'][] = ["name"=>"Whatsapp No."];
    $data['director'][] = ["name"=>"Email"];

    /* Ledger Header */
    $data['ledger'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
	$data['ledger'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""];
    $data['ledger'][] = ["name"=>"Ledger Name"];
    $data['ledger'][] = ["name"=>"Group Name"];
    // $data['ledger'][] = ["name"=>"Op. Balance"];
    // $data['ledger'][] = ["name"=>"Cl. Balance"];

    /* Project Master Header */
    $data['projectMaster'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
    $data['projectMaster'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""];
    $data['projectMaster'][] = ["name"=>"Project Name"];
    $data['projectMaster'][] = ["name"=>"Project Type"];
    $data['projectMaster'][] = ["name"=>"Customer Name"];
    $data['projectMaster'][] = ["name"=>"Work Size"];
    $data['projectMaster'][] = ["name"=>"Cost Type"];
    $data['projectMaster'][] = ["name"=>"Amount"];
	
	/* Item Category Header */
    $data['itemCategory'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
	$data['itemCategory'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""];
    $data['itemCategory'][] = ["name"=>"Category Name"];
    $data['itemCategory'][] = ["name"=>"Parent Category"];
    $data['itemCategory'][] = ["name"=>"Is Final ?"];
    $data['itemCategory'][] = ["name"=>"Remark"];

    /* Finish Goods Header */
    $data['finish_goods'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
	$data['finish_goods'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""];
    $data['finish_goods'][] = ["name"=>"Item Code"];
    $data['finish_goods'][] = ["name"=>"Item Name"];
    $data['finish_goods'][] = ["name"=>"Category Name"];
    $data['finish_goods'][] = ["name"=>"Unit"];

    /* Service Items Header */
    $data['service_items'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
	$data['service_items'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""];
    $data['service_items'][] = ["name"=>"Item Code"];
    $data['service_items'][] = ["name"=>"Item Name"];
    $data['service_items'][] = ["name"=>"Category Name"];
    $data['service_items'][] = ["name"=>"Unit"];

    /* Site Trans Header */
    $data['workUpdate'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
	$data['workUpdate'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""];
    $data['workUpdate'][] = ["name"=>"Date"];
    $data['workUpdate'][] = ["name"=>"Project"];
    $data['workUpdate'][] = ["name"=>"Tower/Block"];
    $data['workUpdate'][] = ["name"=>"Work Detail"];
    $data['workUpdate'][] = ["name"=>"Work Executed"];
    $data['workUpdate'][] = ["name"=>"Note"];

     /* Site Trans Header */
     $data['laborAttendance'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
     $data['laborAttendance'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""];
     $data['laborAttendance'][] = ["name"=>"Date"];
     $data['laborAttendance'][] = ["name"=>"Project"];
     $data['laborAttendance'][] = ["name"=>"Agency/Labor Category"];
     $data['laborAttendance'][] = ["name"=>"Total Labor"];
     $data['laborAttendance'][] = ["name"=>"Shift"];

    /* Machinery Status Header */
    $data['machineryStatus'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
    $data['machineryStatus'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""];
    $data['machineryStatus'][] = ["name"=>"Date"];
    $data['machineryStatus'][] = ["name"=>"Project"];
    $data['machineryStatus'][] = ["name"=>"Machine"];
    $data['machineryStatus'][] = ["name"=>"Qty"];

    /* complains Header */
    $data['complain'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
    $data['complain'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""];
    $data['complain'][] = ["name"=>"Date"];
    $data['complain'][] = ["name"=>"Project"];
    $data['complain'][] = ["name"=>"Agency"];
    $data['complain'][] = ["name"=>"Complain Title"];
    $data['complain'][] = ["name"=>"Complain"];

    /* Extra Activity Header */
    $data['extraActivity'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
    $data['extraActivity'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""];
    $data['extraActivity'][] = ["name"=>"Date"];
    $data['extraActivity'][] = ["name"=>"Project"];
    $data['extraActivity'][] = ["name"=>"Activity"];

    /* Expense Header */
    $data['expense'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
	$data['expense'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""]; 
    $data['expense'][] = ["name"=>"Exp No."];
    $data['expense'][] = ["name"=>"Exp Date"];
    $data['expense'][] = ["name"=>"Emp Name"];
    $data['expense'][] = ["name"=>"Exp Type"];
    $data['expense'][] = ["name"=>"Demand Amount"];
    $data['expense'][] = ["name"=>"Approve Amount"];
    $data['expense'][] = ["name"=>"Exp File","textAlign"=>"center"];
    $data['expense'][] = ["name"=>"Rej Reason"];

	/* Machine Header*/
    $data['machine'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['machine'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['machine'][] = ["name"=>"Machine Name"];
    $data['machine'][] = ["name"=>"Remark"];
	
	/* Stock Limit Header */
    $data['stockLimit'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
	$data['stockLimit'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""];
    $data['stockLimit'][] = ["name"=>"Project Name"];
    $data['stockLimit'][] = ["name"=>"Item Code"];
    $data['stockLimit'][] = ["name"=>"Item Name"];
    $data['stockLimit'][] = ["name"=>"Category Name"];
    $data['stockLimit'][] = ["name"=>"Unit"];
    $data['stockLimit'][] = ["name"=>"Min. Stock"];
	
    return tableHeader($data[$page]);
}


function getPartyData($data){
    $CI = & get_instance();
	$userRole = $CI->session->userdata('role');

    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : '".(in_array($data->party_category, [1,2,3])?"bs-right-lg-modal":"bs-right-md-modal")."', 'form_id' : 'edit".$data->party_category_name."', 'title' : 'Update ".$data->party_category_name."','call_function':'edit'}";
    $editButton = '<a class="btn btn-warning btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : '".$data->party_category_name."'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);
    if(in_array($data->party_category, [1,2,3])){
        $responseData = [$action,$data->sr_no,$data->party_name,$data->city,$data->business_type,$data->contact_person,$data->party_phone];
    }elseif($data->party_category == 4){
        $responseData = [$action,$data->sr_no,$data->party_name,$data->party_phone,$data->whatsapp_no,$data->party_email];
    }elseif($data->party_category == 5){
        $responseData = [$action,$data->sr_no,$data->party_name,$data->group_name];
    }

    return $responseData;
}

function getProjectData($data){
	$editButton = $deleteButton = $completeBtn = $reOpenBtn = "";
	if(empty($data->is_active)){
		$editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'project', 'title' : 'Update Project','call_function':'edit'}";
		$editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

		$deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Project'}";
		$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
		
		$completeParam = "{'postData':{'id' : ".$data->id.", 'is_active' : 1, 'msg':'Complete'},'fnsave':'changeProjectStatus','message':'Are you sure want to Complete this Project?'}";
		$completeBtn = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Complete Project" flow="down" onclick="confirmStore('.$completeParam.');"><i class="mdi mdi-check"></i></a>';
	}else{
		$reOpenParam = "{'postData':{'id' : ".$data->id.", 'is_active' : 0 , 'msg':'Re-Open'},'fnsave':'changeProjectStatus','message':'Are you sure want to Re-Open this Project?'}";
		$reOpenBtn = '<a class="btn btn-instagram permission-modify" href="javascript:void(0)" datatip="Re-Open" flow="down" onclick="confirmStore('.$reOpenParam.');"><i class="mdi mdi-replay"></i></a>';
	}

    $projectDetail = '<a href="' . base_url('projectMaster/detail/'.encodeURL($data->id)) . '" datatip="Project Detail" flow="down"><b>'.$data->project_name.'</b></a>';

    $action = getActionButton($completeBtn.$reOpenBtn.$editButton.$deleteButton);

    return [$action,$data->sr_no,$projectDetail,$data->project_type,$data->party_name,$data->cost_type_name,moneyFormatIndia($data->amount),$data->work_size];
}

function getItemCategoryData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Item Category'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editItemCategory', 'title' : 'Update Item Category','call_function':'edit'}";

    $editButton=''; $deleteButton='';
	if(!empty($data->ref_id)):
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    endif;

    $cat_code ='';
	if($data->ref_id ==6 || $data->ref_id == 7):
        $cat_code = (!empty($data->tool_type))?'['.str_pad($data->tool_type,3,'0',STR_PAD_LEFT).'] ':'';
    endif;

    if($data->final_category == 0):
        $data->category_name = $cat_code.'<a href="' . base_url("itemCategory/list/" . $data->id) . '">' . $data->category_name . '</a>';
    else:
        $data->category_name = $cat_code.$data->category_name;
    endif;

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->category_name,$data->parent_category_name,$data->is_final_text,/* $data->stock_type_text, */$data->is_returnable_text,$data->remark];
}

function getProductData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : '".$data->item_type_text."'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editItem', 'title' : 'Update ".$data->item_type_text."','call_function':'edit'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->category_name,$data->uom];
}

function getSalesZoneData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Sales Zone'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editSalesZone', 'title' : 'Update Sales Zone','call_function':'edit'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->zone_name,$data->remark];   
}

function getPackingMasterData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Packing Master'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editPackingMaster', 'title' : 'Update Packing Master','call_function':'edit'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->packing_in,$data->packing_qty,$data->packing_unit,$data->total_qty,$data->remark];   
}

function getItemPriceStructureData($data){
    $deleteParam = "{'postData':{'id' : ".$data->structure_id."},'message' : 'Price Structure'}";
    $editParam = "{'postData':{'structure_id' : ".$data->structure_id."},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'editPriceStructure', 'title' : 'Update Price Structure','call_function':'edit'}";
    $copyParam = "{'postData':{'structure_id' : ".$data->structure_id."},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'copyPriceStructure', 'title' : 'Copy Price Structure','call_function':'copyStructure'}";

    $copyButton = '<a class="btn btn-warning btn-edit permission-write" href="javascript:void(0)" datatip="Copy" flow="down" onclick="modalAction('.$copyParam.');"><i class="fas fa-clone"></i></a>';
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($copyButton.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->structure_name,$data->item_name,$data->category_name,floatval($data->gst_per),floatval($data->price),floatval($data->mrp)]; 
}

function getProductBomData($data){
    $itemBomParam = "{'postData':{'item_id' : ".$data->item_id.",'machine_id':".$data->machine_id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'itemBom', 'title' : 'Product Bom','call_function':'addProductBom','fnsave':'save','button':'close','js_store_fn':'customStore'}";
    $itemBom = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Product Bom" flow="down" onclick="modalAction('.$itemBomParam.');"><i class="fa fa-plus"></i></a>';

    $deleteParam = "{'postData':{'item_id' : ".$data->item_id.",'machine_id':".$data->machine_id."},'message' : 'Product Bom'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($itemBom.$deleteButton);
    return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->category_name,$data->machine_code];
}

/* Expense Table Data */
function getExpenseData($data){
    $editButton = $deleteButton = $approveButton = $rejectButton = "";
    if(empty($data->status)):
        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Expense'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

        $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editExpense', 'title' : 'Update Expense'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

        $approveParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'modal-md', 'form_id' : 'editExpense', 'title' : 'Approve Expense ','call_function' : 'getApproveExpense' , 'fnsave' : 'saveApprovedData','controller':'expense'}";
        $approveButton = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Approve" flow="down" onclick="modalAction('.$approveParam.');"><i class="mdi mdi-check"></i></a>'; 

        $rejectParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'modal-md', 'form_id' : 'editExpense', 'title' : 'Reject Expense ','call_function' : 'getRejectExpense' , 'fnsave' : 'saveApprovedData','controller':'expense'}";
        $rejectButton = '<a class="btn btn-warning  permission-modify" href="javascript:void(0)" datatip="Reject" flow="down" onclick="modalAction('.$rejectParam.');"><i class="mdi mdi-close"></i></a>';

    endif;
   
    $action = getActionButton($approveButton.$rejectButton.$editButton.$deleteButton);
    $download = '';
    if(!empty($data->proof_file)){
		$download ='<a href="'.base_url('assets/uploads/expense/'.$data->proof_file).'" target="_blank"><i class="fa fa-download"></i></a>';
    }
    return [$action,$data->sr_no,$data->exp_number,formatDate($data->exp_date),$data->emp_name,$data->party_name,$data->demand_amount,$data->amount,$download,$data->rej_reason];   
}

function getProjectWorkData($data){    
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editWorkDetail', 'title' : 'Update Project Work','call_function':'editWorkDetail', 'fnsave' : 'saveWorkDetail'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Project Work','fndelete':'deleteWorkDetail'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);
	
	$ex = $data->execution.' ('.$data->uom.')';
    
	return [$action,$data->sr_no,formatDate($data->trans_date,'d-m-Y'),$data->project_name,$data->tower_name,$data->work_detail,$ex,$data->remark];
}

function getLaborAttendanceData($data){    

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Attendace','fndelete':'deleteLaborAttendance'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($deleteButton);
	return [$action,$data->sr_no,formatDate($data->trans_date,'d-m-Y'),$data->project_name,$data->agency_name,$data->total_labor,$data->shift];
}

function getMachineryStatusData($data){    
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editMachineStatus', 'title' : 'Update Machinery Status','call_function':'editMachineStatus', 'fnsave' : 'saveMachineStatus'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Machine Status','fndelete':'deleteMachineStatus'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    
	return [$action,$data->sr_no,formatDate($data->trans_date,'d-m-Y'),$data->project_name,$data->machine_name,$data->qty];
}

function getComplainData($data){    
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editComplain', 'title' : 'Update Complain','call_function':'editComplain', 'fnsave' : 'saveComplain'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Complain','fndelete':'deleteComplain'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    
	return [$action,$data->sr_no,formatDate($data->trans_date,'d-m-Y'),$data->project_name,$data->agency_name,$data->complain_title,$data->complain_note];
}

function getExtraActivityData($data){    
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editExtraActivity', 'title' : 'Update Activity','call_function':'editExtraActivity', 'fnsave' : 'saveExtraActivity'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Complain','fndelete':'deleteExtraActivity'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    
	return [$action,$data->sr_no,formatDate($data->trans_date,'d-m-Y'),$data->project_name,$data->activity];
}

/* Machine Data */
function getMachineData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Machine'}";
    $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-md-modal', 'form_id' : 'editMachine', 'title' : 'Update Machine', 'call_function':'edit'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->machine_name,$data->remark];
}

// Stock Limit Data
function getStockLimitData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Stock Limit'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($deleteButton);

    return [$action,$data->sr_no,$data->project_name,$data->item_code,$data->item_name,$data->category_name,$data->uom,$data->min_stock];
}

?>