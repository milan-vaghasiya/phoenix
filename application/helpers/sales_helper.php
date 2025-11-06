<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function getSalesDtHeader($page){
    /* Lead Header  */
    $data['lead'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['lead'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['lead'][] = ["name"=>"Approach Date"];
	$data['lead'][] = ["name"=>"Approach No"];
	$data['lead'][] = ["name"=>"Lead From"];
	$data['lead'][] = ["name"=>"Party Name"];
    $data['lead'][] = ["name"=>"Contact No."];
    $data['lead'][] = ["name"=>"Sales Executive"];
    $data['lead'][] = ["name"=>"Appointmens","textAlign"=>"center","sortable"=>"FALSE"];
    $data['lead'][] = ["name"=>"Followup Date","sortable"=>"FALSE"];
    $data['lead'][] = ["name"=>"Followup Remark","sortable"=>"FALSE"];
    $data['lead'][] = ["name"=>"Next Followup Date","sortable"=>"FALSE"];

    $data['lead_won'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['lead_won'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['lead_won'][] = ["name"=>"Approach Date"];
	$data['lead_won'][] = ["name"=>"Approach No"];
	$data['lead_won'][] = ["name"=>"Lead From"];
	$data['lead_won'][] = ["name"=>"Party Name"];
    $data['lead_won'][] = ["name"=>"Contact No."];
    $data['lead_won'][] = ["name"=>"Sales Executive"];
    $data['lead_won'][] = ["name"=>"Followup Date","sortable"=>"FALSE"];
    $data['lead_won'][] = ["name"=>"Followup Remark","sortable"=>"FALSE"];

    $data['lead_lost'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['lead_lost'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];  
	$data['lead_lost'][] = ["name"=>"Approach Date"];
	$data['lead_lost'][] = ["name"=>"Approach No"];
	$data['lead_lost'][] = ["name"=>"Lead From"];
	$data['lead_lost'][] = ["name"=>"Party Name"];
    $data['lead_lost'][] = ["name"=>"Contact No."];
    $data['lead_lost'][] = ["name"=>"Sales Executive"];
    $data['lead_lost'][] = ["name"=>"Lost Remark"];

    /* Sales Enquiry Header */
    $data['salesEnquiry'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['salesEnquiry'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['salesEnquiry'][] = ["name"=>"Unit"];
	$data['salesEnquiry'][] = ["name"=>"SE. No."];
	$data['salesEnquiry'][] = ["name"=>"SE. Date"];
	$data['salesEnquiry'][] = ["name"=>"Customer Name"];
	$data['salesEnquiry'][] = ["name"=>"Item Name"];
    $data['salesEnquiry'][] = ["name"=>"Qty"];

    /* Sales Quotation Header */
    $data['salesQuotation'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['salesQuotation'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['salesQuotation'][] = ["name"=>"Unit"];
	$data['salesQuotation'][] = ["name"=>"Rev. No.","textAlign"=>"center"];
	$data['salesQuotation'][] = ["name"=>"SQ. No."];
	$data['salesQuotation'][] = ["name"=>"SQ. Date"];
	$data['salesQuotation'][] = ["name"=>"Customer Name"];
	$data['salesQuotation'][] = ["name"=>"Item Name"];
    $data['salesQuotation'][] = ["name"=>"Qty"];
    $data['salesQuotation'][] = ["name"=>"Price"];
    $data['salesQuotation'][] = ["name"=>"Confirmed BY"];
    $data['salesQuotation'][] = ["name"=>"Confirmed Date"];
    $data['salesQuotation'][] = ["name"=>"Confirmed Note"];

    /* Sales Order Header */
    $data['salesOrders'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['salesOrders'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['salesOrders'][] = ["name"=>"Ship To"];
    $data['salesOrders'][] = ["name"=>"Unit"];
	$data['salesOrders'][] = ["name"=>"SO. No."];
	$data['salesOrders'][] = ["name"=>"SO. Date"];
	$data['salesOrders'][] = ["name"=>"Customer Name"];
	$data['salesOrders'][] = ["name"=>"Net Amount"];
	/*$data['salesOrders'][] = ["name"=>"Remark"];
    $data['salesOrders'][] = ["name"=>"Order Qty"];
    $data['salesOrders'][] = ["name"=>"Dispatch Qty"];
    $data['salesOrders'][] = ["name"=>"Pending Qty"]; */

    /* Party Order Header */
    $data['partyOrders'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['partyOrders'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['partyOrders'][] = ["name"=>"Order Status",'textAlign'=>'center'];
	$data['partyOrders'][] = ["name"=>"Order No."];
	$data['partyOrders'][] = ["name"=>"Order Date"];
	$data['partyOrders'][] = ["name"=>"Order Amount"];
	$data['partyOrders'][] = ["name"=>"Remark"];
    /* $data['partyOrders'][] = ["name"=>"Order Qty"];
    $data['partyOrders'][] = ["name"=>"Received Qty"];
    $data['partyOrders'][] = ["name"=>"Pending Qty"]; */

    /* Estimate [Cash] Header */
    $data['estimate'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['estimate'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['estimate'][] = ["name"=>"Inv No."];
	$data['estimate'][] = ["name"=>"Inv Date"];
	$data['estimate'][] = ["name"=>"Customer Name"];
	$data['estimate'][] = ["name"=>"Taxable Amount"];
    $data['estimate'][] = ["name"=>"Net Amount"];

    /* Route Plan Header */
    $data['routePlan'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['routePlan'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['routePlan'][] = ["name"=>"Plan No."];
	$data['routePlan'][] = ["name"=>"Plan Date"];
	$data['routePlan'][] = ["name"=>"Sales Executive"];
    $data['routePlan'][] = ["name"=>"Village"];

    //25-04-2024
    /* Sales Order Header */
    $data['leadOrder'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['leadOrder'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['leadOrder'][] = ["name"=>"Unit"];
    $data['leadOrder'][] = ["name"=>"SO. No."];
    $data['leadOrder'][] = ["name"=>"SO. Date"];
    $data['leadOrder'][] = ["name"=>"Lead Name"];
    $data['leadOrder'][] = ["name"=>"Net Amount"];

    /* Dealer Order Header [Delivery Boy] */
    $data['dealerOrder'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['dealerOrder'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['dealerOrder'][] = ["name"=>"SO. No."];
	$data['dealerOrder'][] = ["name"=>"SO. Date"];
	$data['dealerOrder'][] = ["name"=>"Customer Name"];
	$data['dealerOrder'][] = ["name"=>"Executive"];
	$data['dealerOrder'][] = ["name"=>"Net Amount"];

    /* Godown Order Header [Sub Dealer] */
    $data['godownOrder'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['godownOrder'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['godownOrder'][] = ["name"=>"SO. No."];
	$data['godownOrder'][] = ["name"=>"SO. Date"];
	$data['godownOrder'][] = ["name"=>"Customer Name"];
	$data['godownOrder'][] = ["name"=>"Net Amount"];

    return tableHeader($data[$page]);
}

/* Lead Table Data */
function getLeadData($data){

    $followupBtn = '';$appointmentBtn ='';$enqBtn='';$editButton="";$deleteButton="";$leadStatusButton = "";
       
    if(in_array($data->lead_status,[0,4])):
        $followupParam = "{'postData': {'id' : ".$data->id.",'party_id':".$data->party_id.",'sales_executive':".$data->sales_executive.",'entry_type':1}, 'modal_id' : 'modal-lg', 'form_id' : 'followUp', 'title' : 'Follow up', 'call_function' : 'addFollowup', 'fnsave' : 'saveFollowup','res_function' : 'resFollowup', 'button' : 'close'}";
        $followupBtn = '<a class="btn btn-primary" href="javascript:void(0)" datatip="Followup" flow="down" onclick="modalAction('.$followupParam.');" ><i class="fas fa-clipboard-check"></i></a>';

        $appointmentParam = "{'postData': {'id' : ".$data->id.",'party_id':".$data->party_id.",'entry_type':2}, 'modal_id' : 'modal-lg', 'form_id' : 'appointment', 'title' : 'Appointments', 'call_function' : 'addAppointment', 'fnsave' : 'saveAppointment','res_function' : 'resAppointments', 'button' : 'close'}";
        $appointmentBtn = '<a class="btn btn-info leadAction" href="javascript:void(0)" datatip="Appointment" flow="down" onclick="modalAction('.$appointmentParam.');"><i class="far fa-calendar-check"></i></a>';
    endif;

    if($data->lead_status == 0 && empty($data->enq_id)):      
        $editParam = "{'postData' : {'id' : ".$data->id."}, 'modal_id' : 'modal-xl', 'form_id' : 'editLead', 'title' : 'Update Approach'}";
    
        $editButton = '<a class="btn btn-warning btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

        $leadParam = "{'postData' : {'id' : ".$data->id."}, 'modal_id' : 'modal-md', 'form_id' : 'approachStatus', 'title' : 'Update Approach Status','call_function':'approachStatus','fnsave':'saveApproachStatus'}";
        $leadStatusButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Approach Status" flow="down" onclick="modalAction('.$leadParam.');"><i class="fa fa-check"></i></a>';
    endif;

    if(in_array($data->lead_status,[0,4])):
        $postData = ['party_id'=>$data->party_id,'lead_id'=>$data->id];
        $encodedData = urlencode(base64_encode(json_encode($postData)));
        $enqBtn = '<a class="btn btn-info" href="'.base_url('salesEnquiry/create/'.$encodedData).'" datatip="Carete Enquiry" flow="down" ><i class="fa fa-file-alt"></i></a>';
    endif;

    $action = getActionButton($enqBtn.$appointmentBtn.$followupBtn.$leadStatusButton.$editButton.$deleteButton);

    if($data->lead_status == 3):
        $responseData = [$action,$data->sr_no,formatDate($data->lead_date),sprintf("%04d",$data->lead_no),$data->lead_from,$data->party_name,$data->party_phone,$data->emp_name,$data->followupDate,$data->followupNote];
    elseif($data->lead_status == 4):
        $responseData = [$action,$data->sr_no,formatDate($data->lead_date),sprintf("%04d",$data->lead_no),$data->lead_from,$data->party_name,$data->party_phone,$data->emp_name,$data->reason];
    else:
        $responseData = [$action,$data->sr_no,formatDate($data->lead_date),sprintf("%04d",$data->lead_no),$data->lead_from,$data->party_name,$data->party_phone,$data->emp_name,$data->appointments,$data->followupDate,$data->followupNote,$data->next_fup_date];
    endif;

    return $responseData;
}

/* Sales Enquiry Table data */
function getSalesEnquiryData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('salesEnquiry/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Sales Enquiry'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $postData = ['enq_id'=>$data->id];
    $encodedData = urlencode(base64_encode(json_encode($postData)));
    $quotationBtn = '<a class="btn btn-info" href="'.base_url('salesQuotation/create/'.$encodedData).'" datatip="Carete Quotation" flow="down" ><i class="fa fa-file-alt"></i></a>';    

    if($data->trans_status > 0):
        $quotationBtn = $editButton = $deleteButton = "";
    endif;

    $action = getActionButton($quotationBtn.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->company_code,$data->trans_number,$data->trans_date,$data->party_name,$data->item_name,floatVal($data->qty)];
}

/* Sales Quotation Table data */
function getSalesQuotationData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('salesQuotation/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Sales Quotation'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $revision = '<a href="'.base_url('salesQuotation/reviseQuotation/'.$data->id).'" class="btn btn-primary btn-edit permission-modify" datatip="Revision" flow="down"><i class="fa fa-retweet"></i></a>';

    $followupParam = "{'postData': {'id' : ".$data->id.",'party_id':".$data->party_id.",'sales_executive':".$data->sales_executive.",'entry_type':4}, 'modal_id' : 'modal-lg', 'form_id' : 'followUp', 'title' : 'Follow up', 'call_function' : 'addFollowup', 'fnsave' : 'saveFollowup','res_function' : 'resFollowup', 'button' : 'close','controller':'lead'}";
    $followupBtn = '<a class="btn btn-info" href="javascript:void(0)" datatip="Followup" flow="down" onclick="modalAction('.$followupParam.');" ><i class="fas fa-clipboard-check"></i></a>';

    $quoteParam = "{'postData' : {'id' : ".$data->id.",'entry_type':4}, 'modal_id' : 'modal-md', 'form_id' : 'approachStatus', 'title' : 'Update Quotation Status','call_function':'approachStatus','fnsave':'saveApproachStatus','controller':'lead'}";
    $quoteStatusButton = '<a class="btn btn-success btn-edit permission-approve" href="javascript:void(0)" datatip="Quotation Status" flow="down" onclick="modalAction('.$quoteParam.');"><i class="fa fa-check"></i></a>';

    $printBtn = '<a class="btn btn-success btn-edit permission-approve" href="'.base_url('salesQuotation/printQuotation/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    if($data->trans_status > 0):
        $revision = $editButton = $deleteButton = "";
    endif;

    if(!empty($data->is_approve)):
        $followupBtn = $revision = $editButton = $deleteButton = $quoteStatusButton = "";
    endif;

    $action = getActionButton($printBtn.$followupBtn.$quoteStatusButton.$revision.$editButton.$deleteButton);

    $rev_no = sprintf("%02d",$data->quote_rev_no);
    if($data->quote_rev_no > 0):
        $revParam = "{'postData' : {'trans_number' : '".$data->trans_number."'}, 'modal_id' : 'modal-md', 'form_id' : 'revisionList', 'title' : 'Quotation Revision History','call_function':'revisionHistory','button':'close'}";
        $rev_no = '<a href="javascript:void(0)" datatip="Revision History" flow="down" onclick="modalAction('.$revParam.');">'.sprintf("%02d",$data->quote_rev_no).'</a>';
    endif;

    return [$action,$data->sr_no,$data->company_code,$rev_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->item_name,$data->qty,$data->price,$data->approve_by_name,((!empty($data->approve_date))?formatDate($data->approve_date):""),$data->close_reason];
}

/* Sales Order Table data */
function getSalesOrderData($data){
    $editButton = '<a class="btn btn-warning btn-edit permission-modify" href="'.base_url('salesOrders/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Sales Order'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $orderCompleteBtn = '';$printBtn = '';$staffPrintBtn = '';    
    if($data->is_approve > 0):
        $printBtn = '<a class="btn btn-info btn-edit permission-approve1" href="'.base_url('salesOrders/printOrder/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
        $staffPrintBtn = '<a class="btn btn-primary btn-edit permission-approve1" href="'.base_url('salesOrders/staffPrintOrder/'.$data->id).'" target="_blank" datatip="Staff Print" flow="down"><i class="fas fa-print" ></i></a>';

        if(in_array($data->userRole,[-1,1]) && $data->trans_status == 0):
            $orderCompleteParam =  "{'postData':{'id':".$data->id.",'trans_status':1},'fnsave':'changeOrderStatus','message':'Are you sure want to Complete this Order ?'}";
            $orderCompleteBtn = '<a class="btn btn-success permission-modify" href="javascript:void(0)" onclick="confirmStore('.$orderCompleteParam.');" datatip="Complete Order" flow="down"><i class="fas fa-check"></i></a>';
        endif;
    endif;

    $splitOrderBtn = $acceptButton = '';
    if($data->is_approve == 0):
        $acceptButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('salesOrders/edit/'.$data->id.'/1').'" datatip="Accept Order" flow="down" ><i class="fas fa-check"></i></a>';
    endif;

    if($data->trans_status > 0):
        $acceptButton = $editButton = $deleteButton = "";
    endif;

    $action = getActionButton($printBtn.$staffPrintBtn.$splitOrderBtn.$acceptButton.$orderCompleteBtn.$editButton.$deleteButton);

    //return [$action,$data->sr_no,$data->ordered_by,$data->company_code,$data->trans_number,$data->trans_date,$data->party_name,$data->item_name,floatval($data->strip_qty),floatval($data->dispatch_qty),floatval($data->pending_qty)];

    return [$action,$data->sr_no,$data->ship_to,$data->company_code,$data->trans_number,$data->trans_date,$data->party_name,moneyFormatIndia($data->net_amount)];
}

/* Party Order Table Data */
function getPartyOrderData($data){
    $data->order_status = ($data->is_approve == 0)?'<span class="badge bg-danger fs-12">'.$data->order_status.'</span>':'<span class="badge bg-success fs-12">'.$data->order_status.'</span>';

    if($data->trans_status > 0):
        $data->order_status = '<span class="badge bg-success fs-12">Completed</span>';
    endif;

    $viewParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'viewOrderItem', 'title' : 'Order Items ','call_function':'viewPartyOrderItems','button':'close'}";
    $viewButton = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="View Items" flow="down" onclick="modalAction('.$viewParam.');"><i class="mdi mdi-eye-outline"></i></a>';

    $copyParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'call_function':'addPartyOrder', 'fnsave' : 'savePartyOrder', 'form_id' : 'addPartyOrder', 'title' : 'Add Order'}";
    $copyButton = '<a class="btn btn-success btn-edit permission-write" href="javascript:void(0)" datatip="Repeat Order" flow="down" onclick="modalAction('.$copyParam.');"><i class="fas fa-clone"></i></a>';

    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editOrderItem', 'title' : 'Update Order ','call_function':'editPartyOrder','fnsave' : 'savePartyOrder'}";
    $editButton = '<a class="btn btn-warning btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Order','call_function':'deletePartyOrder'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    if($data->is_approve > 0):
        $editButton = $deleteButton = "";
    endif;

    $action = getActionButton($viewButton.$copyButton.$editButton.$deleteButton);

    //return [$action,$data->sr_no,$data->order_status,$data->trans_number,$data->trans_date,$data->item_name,floatval($data->strip_qty),floatval($data->dispatch_qty),floatval($data->pending_qty)];
    return [$action,$data->sr_no,$data->order_status,$data->trans_number,$data->trans_date,moneyFormatIndia($data->net_amount),$data->remark];
}

/* Estimate [Cash] Table Data */
function getEstimateData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('estimate/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Estimate'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $printBtn = '<a class="btn btn-info btn-edit" href="'.base_url('estimate/printEstimate/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $paymentParam = "{'postData':{'id' : ".$data->id."},'modal_id':'modal-lg','form_id':'estimatePayment','title':'Payment','call_function':'estimatePayment','button':'close','res_function':'resSaveEstimatePayment'}";
    $paymentBtn = '<a class="btn btn-warning btn-edit permission-modify" href="javascript:void(0)" datatip="Payment" flow="down" onclick="modalAction('.$paymentParam.');"><i class="fas fa-rupee-sign"></i></a>';

    if($data->trans_no == 0):
        $editButton = $deleteButton = "";
    endif;

    $action = getActionButton($printBtn.$paymentBtn.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,$data->trans_date,$data->party_name,$data->taxable_amount,$data->net_amount];
}

/* Route Plan Table Data */
function getRoutePlanData($data){
    $deleteButton = $editButton = '';
    if(empty($data->plan_status)):
        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Route Plan'}";    
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

        $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-md-modal', 'form_id' : 'editRoutePlan', 'title' : 'Update Route Plan','call_function':'edit'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    endif;

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->plan_number,formatDate($data->plan_date),$data->emp_name,$data->village_name];
}


/* Sales Order Table data */
function getLeadOrderData($data){
    $action = getActionButton("");
    return [$action,$data->sr_no,$data->company_code,$data->trans_number,$data->trans_date,$data->party_name,moneyFormatIndia($data->net_amount)];
}

/* Dealer Order Table data */
function getDealerOrderData($data){
    $deleteButton = $editBtn = $approveBtn = $billButton = '';

    if($data->order_type == "GODOWN ORDER"):
        if($data->is_approve == 0):
            if($data->userRole != 11):
                $approveParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'dealerOrder', 'title' : 'Approve Order','call_function':'dealerOrderItem','fnsave' : 'saveDealerOrder'}";
                $approveBtn = '<a class="btn btn-success btn-edit permission-modify1" href="javascript:void(0)" datatip="Approve" flow="down" onclick="modalAction('.$approveParam.');"><i class="fas fa-check"></i></a>';

                $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Order'}";    
                $deleteButton = '<a class="btn btn-danger btn-delete permission-remove1" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
            else:
                if(empty($data->ref_id)):
                    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'dealerOrder', 'title' : 'Update Order','call_function':'dealerOrderItem','fnsave' : 'saveDealerOrder'}";
                    $editBtn = '<a class="btn btn-warning btn-edit permission-modify1" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
        
                    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Order'}";    
                    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove1" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
                endif;
            endif;
        endif;
    else:
        if(empty($data->bill_no)):
            $billParam =  "{'postData':{'id':".$data->id."},'fnsave':'generateinvoice','message':'Are you sure want to Dispatch ?'}";
            $billButton = '<a class="btn btn-success" href="javascript:void(0)" onclick="confirmStore('.$billParam.');" datatip="Dispatch" flow="down"><i class="fas fa-check"></i></a>';

            if($data->userRole == 12):
                $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'dealerOrder', 'title' : 'Update Order','call_function':'dealerOrderItem','fnsave' : 'saveDealerOrder'}";
                $editBtn = '<a class="btn btn-warning btn-edit permission-modify1" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
            endif;

            $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Order'}";    
            $deleteButton = '<a class="btn btn-danger btn-delete permission-remove1" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
        endif;
    endif;

    $viewParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'viewOrderItem', 'title' : 'Order Items ','call_function':'viewPartyOrderItems','button':'close'}";
    $viewButton = '<a class="btn btn-info btn-edit" href="javascript:void(0)" datatip="View Items" flow="down" onclick="modalAction('.$viewParam.');"><i class="mdi mdi-eye-outline"></i></a>';

    $action = getActionButton($approveBtn.$billButton.$viewButton.$editBtn.$deleteButton);

    if($data->order_type == "GODOWN ORDER"):
        return [$action,$data->sr_no,$data->trans_number,$data->trans_date,$data->party_name,moneyFormatIndia($data->net_amount)];
    else:
        return [$action,$data->sr_no,$data->trans_number,$data->trans_date,$data->party_name,$data->emp_name,moneyFormatIndia($data->net_amount)];
    endif;
}
?>