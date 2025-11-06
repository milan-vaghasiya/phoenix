<?php
class SalesOrders extends MY_Controller{
    private $indexPage = "sales_order/index";
    private $form = "sales_order/form"; 
    private $partyOrder = "sales_order/party_order";   
    private $orderForm = "sales_order/party_order_form";   
    private $viewOrder = "sales_order/view_party_order";   
    private $loadingForm = "sales_order/loading_form";
    private $lead_order_index = "sales_order/lead_order_index";
    private $splitOrder = "sales_order/split_order";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Sales Order";
		$this->data['headData']->controller = "salesOrders";        
        $this->data['headData']->pageUrl = "salesOrders";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'salesOrders']);
	}

    public function index(){
        $this->data['tableHeader'] = getSalesDtHeader("salesOrders");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->salesOrder->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->userRole = $this->userRole;
            $sendData[] = getSalesOrderData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addOrder(){
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        //$this->data['brandList'] = $this->brandMaster->getBrandList();
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Sales']);
        $this->data['vehicleList'] = $this->vehicle->getVehicleList();
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_no']))
            $errorMessage['trans_number'] = "SO. No. is required.";
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        if(empty($data['ship_to_id']))
            $errorMessage['ship_to_id'] = "Ship to is required.";
        if(empty($data['itemData']))
            $errorMessage['itemData'] = "Item Details is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short;
            //if(empty($data['id'])): $data['is_approve'] = $this->loginId; endif;
            $this->printJson($this->salesOrder->save($data));
        endif;
    }

    public function edit($id,$accept = 0){
        $this->data['is_approve'] = (!empty($accept))?$this->loginId:0;
        $this->data['approve_date'] = (!empty($accept))?date("Y-m-d"):"";
        $this->data['dataRow'] = $dataRow = $this->salesOrder->getSalesOrder(['id'=>$id,'itemList'=>1]);
        $this->data['gstinList'] = $this->party->getPartyGSTDetail(['party_id' => $dataRow->party_id]);
        $this->data['shipToList'] = $this->party->getPartyDeliveryAddressDetails(['party_id' => $dataRow->party_id]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category' => 1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        //$this->data['brandList'] = $this->brandMaster->getBrandList();
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Sales']);
        $this->data['vehicleList'] = $this->vehicle->getVehicleList();
        $this->load->view($this->form,$this->data);
    }

    public function changeOrderStatus(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->salesOrder->changeOrderStatus($data));
        endif;
    }

    public function loadingBy(){
        $data = $this->input->post();
        $this->data['id'] = $data['id'];
        $this->data['employeeList'] = $this->employee->getEmployeeList();
        $this->data['vehicleList'] = $this->vehicle->getVehicleList();
        $this->load->view($this->loadingForm,$this->data);
    }

    public function saveLoadingBy(){
        $data = $this->input->post();
        $this->printJson($this->salesOrder->saveLoadingBy($data));
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->salesOrder->delete($id));
        endif;
    }

    public function printOrder($id,$pdf_type=''){
        $this->data['dataRow'] = $dataRow = $this->salesOrder->getSalesOrder(['id'=>$id,'itemList'=>1]);
        $this->data['partyData'] = $this->party->getParty(['id'=>$dataRow->party_id]);
        $this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo($dataRow->cm_id);
        
        $logo = base_url('assets/images/logo.png');
        $this->data['letter_head'] =  base_url($companyData->print_header);
        
        $pdfData = $this->load->view('sales_order/print', $this->data, true);
        
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
            <tr>
                <td style="width:25%;">SO. No. & Date : '.$dataRow->trans_number . ' [' . formatDate($dataRow->trans_date) . ']</td>
                <td style="width:25%;"></td>
                <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
            </tr>
        </table>';
        
		$mpdf = new \Mpdf\Mpdf();
		$filePath = realpath(APPPATH . '../assets/uploads/sales_quotation/');
        $pdfFileName = $filePath.'/' . str_replace(["/","-"],"_",$dataRow->trans_number) . '.pdf';

        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo, 0.03, array(120, 120));
        $mpdf->showWatermarkImage = true;
        $mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,5,5,15,5,5,'','','','','','','','','','A4-P');
        $mpdf->WriteHTML($pdfData);
		
		ob_clean();
		$mpdf->Output($pdfFileName, 'I');
		
    }

    public function staffPrintOrder($id,$pdf_type=''){
        $this->data['dataRow'] = $dataRow = $this->salesOrder->getSalesOrder(['id'=>$id,'itemList'=>1]);
        $this->data['partyData'] = $this->party->getParty(['id'=>$dataRow->party_id]);
        $this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo($dataRow->cm_id);
        
        $logo = base_url('assets/images/logo.png');
        $this->data['letter_head'] =  base_url($companyData->print_header);
        
        $pdfData = $this->load->view('sales_order/staff_print', $this->data, true);
        
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
            <tr>
                <td style="width:25%;">SO. No. & Date : '.$dataRow->trans_number . ' [' . formatDate($dataRow->trans_date) . ']</td>
                <td style="width:25%;"></td>
                <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
            </tr>
        </table>';
        
		$mpdf = new \Mpdf\Mpdf();
		$filePath = realpath(APPPATH . '../assets/uploads/sales_quotation/');
        $pdfFileName = $filePath.'/' . str_replace(["/","-"],"_",$dataRow->trans_number) . '.pdf';

        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo, 0.03, array(120, 120));
        $mpdf->showWatermarkImage = true;
        $mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,5,5,15,5,5,'','','','','','','','','','A4-P');
        $mpdf->WriteHTML($pdfData);
		
		ob_clean();
		$mpdf->Output($pdfFileName, 'I');
		
    }

    public function getPartyOrders(){
        $data = $this->input->post();
        $this->data['orderItems'] = $this->salesOrder->getPendingOrderItems($data);
        $this->load->view('sales_invoice/create_invoice',$this->data);
    }

    /* Party Order Start */

    public function partyOrders(){
        $this->data['headData']->pageTitle = "Orders";
        $this->data['tableHeader'] = getSalesDtHeader("partyOrders");
        $this->load->view($this->partyOrder,$this->data);
    }

    public function getPartyOrderDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->salesOrder->getPartyOrderDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPartyOrderData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPartyOrder(){
        $data = $this->input->post();
        if(!empty($data['id'])):
            $this->data['dataRow'] = $this->salesOrder->getPartyOrderItems($data);
        endif;
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['shipToList'] = (!empty($this->partyId))?$this->party->getPartyDeliveryAddressDetails(['party_id' => $this->partyId]):array();;
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view($this->orderForm,$this->data);
    }

    public function savePartyOrder(){
        $data = $this->input->post(); 
        $errorMessage = array();

        if(empty($data['cm_id']))
            $errorMessage['cm_id'] = "Unit is required.";
        if(empty($data['ship_to_id']))
            $errorMessage['ship_to_id'] = "Ship to is required.";
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Customer Name is required.";

        if(array_sum(array_column($data['itemData'],'total_box')) <= 0):
            $errorMessage['item_error'] = "Please enter at least one item qty.";
        endif;

        if(!empty($data['id'])):
            $orderData = $this->salesOrder->getSalesOrder(['id'=>$data['id'],'itemList'=>0]);
            if($orderData->is_approve > 0):
                $errorMessage['order_error'] = "Your Order has been accepted. you can not update it.";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $partyData = $this->party->getParty(['id'=>$data['party_id']]);
            $gstType = ($partyData->state_code != 24)?2:1;
            $price_structure_id = $partyData->price_structure_id;

            $total_amount = $taxable_amount = $cgst_amount = $sgst_amount = $igst_amount = $net_amount = 0;

            $itemData = array();
            foreach($data['itemData'] as $row):
                if($row['total_box'] > 0):
                    $itemDetail = $this->item->getItem(['id'=>$row['item_id'],'price_structure_id'=>$price_structure_id,'party_id'=>$partyData->id,'cm_id'=>$data['cm_id']]);
                    $total_box = $row['total_box'];
                    $strip_qty = round(($total_box * $itemDetail->packing_qty),2);
                    $total_qty = round(($strip_qty * $itemDetail->packing_unit_qty),2);

                    $gstPer = $igstPer = $cgstPer = $sgstPer = 0;
                    $amount = $taxableAmt = $netAmt = $discAmt = 0;
                    $gstAmt = $igstAmt = $cgstAmt = $sgstAmt = 0;                

                    $gstPer = $igstPer = $itemDetail->gst_per;
                    $cgstPer = $sgstPer = round(($itemDetail->gst_per/2),2);                

                    $amount = $taxableAmt = $strip_qty * $itemDetail->price;

                    if(!empty($taxableAmt)):
                        $gstAmt = $igstAmt = round((($gstPer * $taxableAmt)/100),2);
                        $cgstAmt = $sgstAmt = round(($gstAmt/2),2);
                    endif;

                    $netAmt = $taxableAmt + $gstAmt;

                    $total_amount += $amount;
                    $taxable_amount += $taxableAmt;
                    if($gstType == 1):
                        $cgst_amount += $cgstAmt;
                        $sgst_amount += $sgstAmt;
                    else:
                        $igst_amount += $igstAmt;
                    endif;
                    $net_amount += $netAmt;

                    $itemData[] = [
                        'id' => ((!empty($row['id']))?$row['id']:""),
                        'item_id' => $itemDetail->id,
                        'item_name' => $itemDetail->item_name,
                        'item_code' => $itemDetail->item_code,
                        'item_type' => $itemDetail->item_type,
                        'hsn_code' => $itemDetail->hsn_code,
                        'total_box' => $total_box,
                        'strip_qty' => $strip_qty,
                        'qty' => $total_qty,
                        'unit_id' => $itemDetail->unit_id,
                        'unit_name' => $itemDetail->unit_name,
                        'brand_id' => $itemDetail->category_id,
                        'brand_name' => $itemDetail->category_name,
                        'price' => $itemDetail->price,
                        'org_price' => $itemDetail->mrp,
                        'disc_per' => $itemDetail->defualt_disc,
                        'disc_amount' => $discAmt,
                        'amount' => $amount,
                        'taxable_amount' => $taxableAmt,
                        'net_amount' => $netAmt,
                        'amount' => $amount,
                        'cgst_per' => $cgstPer,
                        'cgst_amount' => $cgstAmt,
                        'sgst_per' => $sgstPer,
                        'sgst_amount' => $sgstAmt,
                        'igst_per' => $igstPer,
                        'igst_amount' => $igstAmt,
                        'gst_per' => $gstPer,
                        'gst_amount' => $gstAmt,
                        'item_remark' => "",
                        'cm_id' => $data['cm_id']
                    ];
                endif;
            endforeach;

            if(empty($data['id'])):
                $trans_prefix = $this->data['entryData']->trans_prefix;
                $trans_no = $this->transMainModel->nextTransNo($this->data['entryData']->id,0,"",$data['cm_id']);

                $data['trans_prefix'] = $trans_prefix;
                $data['trans_no'] = $trans_no;
                $data['trans_number'] = $trans_prefix.$trans_no;
                $data['trans_date'] = date("Y-m-d");
            endif;

            $masterData = [
                'id' => ((!empty($data['id']))?$data['id']:""),
                'entry_type' => $this->data['entryData']->id,     
                'trans_prefix' => $data['trans_prefix'],
                'trans_no' => $data['trans_no'],
                'trans_number' => $data['trans_number'],
                'trans_date' => $data['trans_date'],
                'sales_executive' => (!empty($this->partyId))?$partyData->id:0,
                'party_id' => $partyData->id,
                'party_name' => $partyData->party_name,
                'gstin' => $partyData->gstin,
                'gst_type' => $gstType,
                'party_state_code' => $partyData->state_code,
                'delivery_date' => NULL,//(!empty($data['delivery_date']))?$data['delivery_date']:NULL,
                'ship_to_id' => $data['ship_to_id'],
                'apply_round' => 1,
                'ledger_eff' => 0,
                'masterDetails' => [
                    't_col_1' => $partyData->contact_person,
                    't_col_2' => $partyData->party_mobile,
                ],
                'itemData' => $itemData,
                'total_amount' => $total_amount,
                'taxable_amount' => $taxable_amount,
                'cgst_amount' => $cgst_amount,
                'sgst_amount' => $sgst_amount,
                'igst_amount' => $igst_amount,
                'net_amount' => $net_amount,
                'remark' => $data['remark'],
                'cm_id' => $data['cm_id']
            ];           

            $masterData['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $masterData['vou_name_s'] = $this->data['entryData']->vou_name_short;
            $masterData['is_approve'] = (empty($data['id']) && empty($this->partyId))?$this->loginId:0;
            $this->printJson($this->salesOrder->save($masterData));
        endif;
    }

    public function viewPartyOrderItems(){
        $data = $this->input->post();
        $this->data['itemList'] = $this->salesOrder->getPartyOrderItems($data);
        $this->load->view($this->viewOrder,$this->data);
    }

    public function editPartyOrder(){
        $data = $this->input->post();
        $this->data['copyOrder'] = 1;
        $this->data['orderData'] = $orderData = $this->salesOrder->getSalesOrder(['id'=>$data['id'],'itemList'=>0]);
        $this->data['dataRow'] = $this->salesOrder->getPartyOrderItems($data);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['shipToList'] = $this->party->getPartyDeliveryAddressDetails(['party_id' => $orderData->party_id]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view($this->orderForm,$this->data);
    }

    public function deletePartyOrder(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->salesOrder->delete($id,1));
        endif;
    }
    /* Party Order End */

    /*** Lead Orders */
    public function leadOrder(){
        $this->data['tableHeader'] = getSalesDtHeader("leadOrder");
        $this->load->view($this->lead_order_index,$this->data);
    }

    public function getLeadOrderDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->crm->getLeadOrderDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->userRole = $this->userRole;
            $sendData[] = getLeadOrderData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    /** Lead Orders End */

    public function splitOrder(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->salesOrder->getSalesOrder(['id'=>$id,'itemList'=>1]);
        $this->load->view($this->splitOrder,$this->data);
    }

    public function saveSplitOrder(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['to_cm_id']))
            $errorMessage['to_cm_id'] = "To Unit is required.";
        if(empty($data['trans_id']))
            $errorMessage['itemError'] = "Select Item to split order.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $orderData = $this->salesOrder->getSalesOrder(['id'=>$data['id'],'itemList'=>0]);
            $orderItemList = $this->salesOrder->getSalesOrderItems(['id'=>$data['id'],'trans_ids'=>$data['trans_id']]);

            $partyData = $this->party->getParty(['id'=>$orderData->party_id]);
            $gstType = ($partyData->state_code != 24)?2:1;
            $price_structure_id = $partyData->price_structure_id;

            $total_amount = $taxable_amount = $cgst_amount = $sgst_amount = $igst_amount = $net_amount = 0;

            $itemData = array();
            foreach($orderItemList as $row):
                if($row->strip_qty > 0):
                    $itemDetail = $this->item->getItem(['id'=>$row->item_id,'price_structure_id'=>$price_structure_id,'party_id'=>$partyData->id,'cm_id'=>$data['to_cm_id']]);
                    $total_box = round(($row->strip_qty / $itemDetail->packing_qty),2);
                    $strip_qty = $row->strip_qty;
                    $total_qty = round(($strip_qty * $itemDetail->packing_unit_qty),2);

                    $gstPer = $igstPer = $cgstPer = $sgstPer = 0;
                    $amount = $taxableAmt = $netAmt = $discAmt = 0;
                    $gstAmt = $igstAmt = $cgstAmt = $sgstAmt = 0;                

                    $gstPer = $igstPer = $itemDetail->gst_per;
                    $cgstPer = $sgstPer = round(($itemDetail->gst_per/2),2);                

                    $amount = $taxableAmt = $strip_qty * $itemDetail->price;

                    if(!empty($taxableAmt)):
                        $gstAmt = $igstAmt = round((($gstPer * $taxableAmt)/100),2);
                        $cgstAmt = $sgstAmt = round(($gstAmt/2),2);
                    endif;

                    $netAmt = $taxableAmt + $gstAmt;

                    $total_amount += $amount;
                    $taxable_amount += $taxableAmt;
                    if($gstType == 1):
                        $cgst_amount += $cgstAmt;
                        $sgst_amount += $sgstAmt;
                    else:
                        $igst_amount += $igstAmt;
                    endif;
                    $net_amount += $netAmt;

                    $itemData[] = [
                        'id' => "",
                        'item_id' => $itemDetail->id,
                        'item_name' => $itemDetail->item_name,
                        'item_code' => $itemDetail->item_code,
                        'item_type' => $itemDetail->item_type,
                        'hsn_code' => $itemDetail->hsn_code,
                        'total_box' => $total_box,
                        'strip_qty' => $strip_qty,
                        'qty' => $total_qty,
                        'unit_id' => $itemDetail->unit_id,
                        'unit_name' => $itemDetail->unit_name,
                        'brand_id' => $itemDetail->category_id,
                        'brand_name' => $itemDetail->category_name,
                        'price' => $itemDetail->price,
                        'org_price' => $itemDetail->mrp,
                        'disc_per' => $itemDetail->defualt_disc,
                        'disc_amount' => $discAmt,
                        'amount' => $amount,
                        'taxable_amount' => $taxableAmt,
                        'net_amount' => $netAmt,
                        'amount' => $amount,
                        'cgst_per' => $cgstPer,
                        'cgst_amount' => $cgstAmt,
                        'sgst_per' => $sgstPer,
                        'sgst_amount' => $sgstAmt,
                        'igst_per' => $igstPer,
                        'igst_amount' => $igstAmt,
                        'gst_per' => $gstPer,
                        'gst_amount' => $gstAmt,
                        'item_remark' => "",
                        'feasible_remark' => $orderData->trans_number,
                        'feasibility_by' => $row->id,
                        'feasibility_at' => date("Y-m-d H:i:s"),
                        'cm_id' => $data['to_cm_id']
                    ];

                    $this->masterModel->edit('trans_child',['id'=>$row->id],['is_delete'=>1,'feasible_remark'=>'To CM Id : '.$data['to_cm_id']]);
                endif;
            endforeach;
            
            $trans_prefix = $this->data['entryData']->trans_prefix;
            $trans_no = $this->transMainModel->nextTransNo($this->data['entryData']->id,0,"",$data['to_cm_id']);
            $data['trans_prefix'] = $trans_prefix;
            $data['trans_no'] = $trans_no;
            $data['trans_number'] = $trans_prefix.$trans_no;
            $data['trans_date'] = date("Y-m-d");
            

            $masterData = [
                'id' => "",
                'entry_type' => $this->data['entryData']->id,     
                'trans_prefix' => $data['trans_prefix'],
                'trans_no' => $data['trans_no'],
                'trans_number' => $data['trans_number'],
                'trans_date' => $data['trans_date'],
                'sales_executive' => $orderData->sales_executive,
                'party_id' => $partyData->id,
                'party_name' => $partyData->party_name,
                'gstin' => $partyData->gstin,
                'gst_type' => $gstType,
                'party_state_code' => $partyData->state_code,
                'delivery_date' => NULL,//(!empty($data['delivery_date']))?$data['delivery_date']:NULL,
                'ship_to_id' => $orderData->ship_to_id,
                'apply_round' => 1,
                'ledger_eff' => 0,
                'masterDetails' => [
                    't_col_1' => $partyData->contact_person,
                    't_col_2' => $partyData->party_mobile,
                ],
                'itemData' => $itemData,
                'total_amount' => $total_amount,
                'taxable_amount' => $taxable_amount,
                'cgst_amount' => $cgst_amount,
                'sgst_amount' => $sgst_amount,
                'igst_amount' => $igst_amount,
                'net_amount' => $net_amount,
                'remark' => $orderData->remark,
                'cm_id' => $data['to_cm_id']
            ];           

            $masterData['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $masterData['vou_name_s'] = $this->data['entryData']->vou_name_short;
            $masterData['is_approve'] = 0;
            $this->salesOrder->save($masterData);

            $orderData = $this->salesOrder->getSalesOrder(['id'=>$data['id'],'itemList'=>1]);

            $orderUpdate['total_amount'] = array_sum(array_column($orderData->itemList,'amount'));
            $orderUpdate['taxable_amount'] = array_sum(array_column($orderData->itemList,'taxable_amount'));

            $orderUpdate['cgst_amount'] = $orderUpdate['sgst_amount'] = $orderUpdate['igst_amount'] = 0;

            if($orderData->gst_type == 1):
                $orderUpdate['cgst_amount'] = array_sum(array_column($orderData->itemList,'cgst_amount'));
                $orderUpdate['sgst_amount'] = array_sum(array_column($orderData->itemList,'sgst_amount'));
            elseif($orderData->gst_type == 2):
                $orderUpdate['igst_amount'] = array_sum(array_column($orderData->itemList,'igst_amount'));
            endif;

            $orderUpdate['net_amount'] = $orderUpdate['taxable_amount'] + $orderUpdate['cgst_amount'] +  $orderUpdate['sgst_amount'] + $orderUpdate['igst_amount'];
            $this->masterModel->edit("trans_main",['id'=>$data['id']],$orderUpdate);

            $this->printJson(['status'=>1,'message'=>'Order Split successfully.']);
        endif;
    }
}
?>