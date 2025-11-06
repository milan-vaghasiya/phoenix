<?php
class Store extends MY_Controller
{
    public function __construct(){
		parent::__construct(); 
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Material Issue";
		$this->data['headData']->controller = "store";
	}

    public function materialIssue(){
        $this->data['headData']->pageTitle = "Material Issue";
        $this->data['tableHeader'] = getStoreDtHeader('materialIssue');
        $this->load->view('store/issue_index', $this->data);
    }

    public function getIssueDTRows() {
        $data = $this->input->post();
        $result = $this->store->getIssueDTRows($data);
		$sendData = array(); $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getMaterialIssueData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function issueMaterial(){
        $data = $this->input->post();
        $issue_no = $this->store->getNextIssueNo();
        $this->data['issue_number'] = 'ISU/'.$this->shortYear.'/'.$issue_no;
        $this->data['issue_no'] = $issue_no;
        $this->data['projectList'] = $this->project->getProjectList(['is_active'=>0]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,10"]);
		$this->data['partyList'] = $this->party->getPartyList(['party_category'=>"3"]);
        $this->load->view('store/issue_form', $this->data);
    }

    public function getItemStock(){
        $data = $this->input->post();
        $stockData = $this->store->getItemStockBatchWise(['item_id'=>$data['item_id'],'location_id'=>$data['location_id'],'stock_required'=>1,'single_row'=>1]);

        $stock_qty = (!empty($stockData->qty)?floatval($stockData->qty).' <small>'.$stockData->uom.'</small>':0);
        $this->printJson(['status'=>1,'stock_qty'=>$stock_qty]);
    }

    public function saveIssuedMaterial() {

        $data = $this->input->post();
        $errorMessage = array(); 

        if(empty($data['item_id'])) {  $errorMessage['item_id'] = "Item is required";  }
		if(empty($data['project_id'])){ $errorMessage['project_id'] = "Project is required."; }
		if(empty($data['issue_qty']) OR $data['issue_qty']<=0){ $errorMessage['issue_qty'] = "Issue Qty is required."; }
        else{
            $stockData = $this->store->getItemStockBatchWise(['item_id'=>$data['item_id'],'location_id'=>$data['project_id'],'stock_required'=>1,'single_row'=>1]);
            if($data['issue_qty'] > $stockData->qty){
                $errorMessage['issue_qty'] = "Stock not available."; 
            }
        }

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$this->printJson($this->store->saveIssuedMaterial($data));
        endif;
    }

    public function deleteIssuedItem(){
        $data = $this->input->post();
        if(empty($data)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->store->deleteIssuedItem($data));
        endif;
    }

    public function stockTransfer(){
        $this->data['headData']->pageTitle = "Stock Transfer";
        $this->data['tableHeader'] = getStoreDtHeader('stockTransferLog');
        $this->load->view('store/transfer_index', $this->data);
    }

    public function getStockTransferDTRows() {
        $data = $this->input->post();
        $result = $this->store->getStockTransferDTRows($data);
		$sendData = array(); $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getStockTransferData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function addTransferLog(){
        $data = $this->input->post();        
        $trans_no = $this->store->getNextTransferNo();
        $this->data['trans_number'] = 'STR/'.$this->shortYear.'/'.$trans_no;
        $this->data['trans_no'] = $trans_no;
        $this->data['projectList'] = $this->project->getProjectList(['is_active'=>0]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,10"]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>[3]]);
        $this->load->view('store/transfer_form', $this->data);
    }

    public function saveTransferItem() {

        $data = $this->input->post();
        $errorMessage = array(); 
        if(empty($data['item_id'])) {  $errorMessage['item_id'] = "Item is required";  }
		if(empty($data['from_project_id'])){ $errorMessage['from_project_id'] = "Project is required."; }
		if(empty($data['to_project_id'])){ $errorMessage['to_project_id'] = "Project is required."; }
        if(!empty($data['to_project_id']) && !empty($data['from_project_id']) && $data['to_project_id'] == $data['from_project_id']){ $errorMessage['to_project_id'] = "From and To both projects are same"; }
		if(empty($data['qty']) OR $data['qty']<=0){ $errorMessage['qty'] = "Transfer Qty is required."; }
        else{
            $stockData = $this->store->getItemStockBatchWise(['item_id'=>$data['item_id'],'location_id'=>$data['from_project_id'],'stock_required'=>1,'single_row'=>1]);
            if($data['qty'] > $stockData->qty){
                $errorMessage['qty'] = "Stock not available."; 
            }
        }

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$this->printJson($this->store->saveTransferItem($data));
        endif;
    }

    public function deleteTransferedLog(){
        $data = $this->input->post();
        if(empty($data)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->store->deleteTransferedLog($data));
        endif;
    }

	public function issueMaterialPrint($id){
		$this->data['dataRow'] = $dataRow = $this->store->getIssueMaterialData(['id'=>$id]);
		$this->data['logo'] = $logo = base_url('assets/images/logo.png');
        $this->data['companyData'] = $this->masterModel->getCompanyInfo();

        $pdfData = $this->load->view('store/issue_print',$this->data,true);	
		        
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = str_replace(["/","-"],"_",$dataRow->issue_number) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo,0.03,array(120,45));
        $mpdf->showWatermarkImage = true;
		$mpdf->AddPage('P', '', '', '', '', 5, 5, 5, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A5-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

	public function stockTransferPrint($id){
		$this->data['dataRow'] = $dataRow = $this->store->getStockTransferLog(['id'=>$id, 'single_row'=>1]);
		$this->data['logo'] = $logo = base_url('assets/images/logo.png');
        $this->data['companyData'] = $this->masterModel->getCompanyInfo();

        $pdfData = $this->load->view('store/transfer_print',$this->data,true);	
		        
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = str_replace(["/","-"],"_",$dataRow->issue_number) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo,0.03,array(120,45));
        $mpdf->showWatermarkImage = true;
		$mpdf->AddPage('P', '', '', '', '', 5, 5, 5, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A5-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
	public function materialReturn(){
        $data = $this->input->post();        
        $this->data['issue_id'] = $data['id'];
        $this->load->view('store/material_return', $this->data);
    }

    public function saveMaterialReturn() {
        $data = $this->input->post();
        $errorMessage = array(); 
      
		if(empty($data['return_qty']) OR $data['return_qty']<=0){ $errorMessage['return_qty'] = "Return Qty is required."; }
        else{
            $issueData = $this->store->getIssueMaterialData(['id'=>$data['issue_id']]);
           
            if($data['return_qty'] > $issueData->issue_qty){
                $errorMessage['return_qty'] = "Issue Qty not available."; 
            }
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$this->printJson($this->store->saveMaterialReturn($data));
        endif;
    }

    public function materialReturnLog(){
        $data = $this->input->post();
        $this->data['returnData'] = $this->store->getStockTrans(['main_ref_id'=>$data['id'],'multi_rows'=>1,'trans_type'=>'RTN']); 
        $this->load->view('store/return_log',$this->data);
    }

    public function deleteMaterialReturn(){
        $data = $this->input->post();
        if(empty($data)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->store->deleteMaterialReturn($data));
        endif;
    }

    /* Opening Stock Start */
    public function openingStock(){
        $this->data['headData']->pageTitle = "Opening Stock";
        $this->data['tableHeader'] = getStoreDtHeader('openingStock');
        $this->load->view('store/opening_stock_index', $this->data);
    }

    public function getOpeningStockDTRows() {
        $data = $this->input->post();
        $result = $this->store->getOpeningStockDTRows($data);
		$sendData = array(); $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getOpeningStockData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function addOpeningStock(){
        $data = $this->input->post(); 
        $this->data['projectList'] = $this->project->getProjectList(['is_active'=>0]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,10"]);
        $this->load->view('store/opening_stock_form', $this->data);
    }

    public function saveOpeningStock() {

        $data = $this->input->post();
        $errorMessage = array(); 
        if(empty($data['item_id'])) {  $errorMessage['item_id'] = "Item is required";  }
		if(empty($data['project_id'])){ $errorMessage['project_id'] = "Project is required."; }
		if(empty($data['qty']) OR $data['qty']<=0){ $errorMessage['qty'] = "Stock Qty is required."; }
      
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$this->printJson($this->store->saveOpeningStock($data));
        endif;
    }

    public function deleteOpeningStock(){
        $data = $this->input->post();
        if(empty($data)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->store->deleteOpeningStock($data));
        endif;
    }

    /* Opening Stock End */
}
?>