<?php
class Store extends MY_ApiController{

	public function __construct(){
		parent::__construct();
        $this->data['headData']->pageTitle = "Store";
        $this->data['headData']->pageUrl = "api/store";
        $this->data['headData']->base_url = base_url();
	}
	
	/*** PURCHASE REQUEST ***/
    public function getMRList(){
        $postData = $this->input->post();
        $this->data['mrData'] = $this->purchaseIndent->getMRList($postData);
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['mrData']]);
    }

    public function addMaterialRequest(){
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
		$this->data['projectList'] = $this->project->getProjectList(); 
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }
	
	public function saveRequest(){
        $data = $this->input->post();
		
        $data['trans_prefix'] = "MR/".$this->shortYear;
        $data['trans_no'] = "1";
        $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];
        
		if(!empty($data['item_data']) && gettype($data['item_data']) == "string"): $data['item_data'] = json_decode($data['item_data'],true); endif;
		
        $errorMessage = array();

        if (empty($data['item_data']))
            $errorMessage['item_data'] = "Item is required.";
		if (empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required.";
		if (empty($data['trans_date']))
            $errorMessage['trans_date'] = "Date is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $this->printJson($this->purchaseIndent->save($data));
        endif;
    }

    public function deleteRequest(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->purchaseIndent->delete($id));
        endif;
    }  

    public function changeReqStatus(){
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
            $this->printJson($this->purchaseIndent->save($data));
        endif;
    }

    public function approveIndent(){
		$data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchaseIndent->approveIndent($data));
		endif;
	}
	
	
	/*** ISSUE MATERIAL ***/
    public function getItemStock(){
        $postData = $this->input->post();
        //$postData['stock_required'] = 1;
		if(empty($postData['stock_required'])):
			$this->data['stockData'] = $this->item->getItemList($postData);
		else:
			$postData['location_id'] = $postData['project_id'];
			$postData['group_by'] = 'stock_trans.item_id';
			$this->data['stockData'] = $this->store->getItemStockBatchWise($postData);
		endif;
			
        
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','records_count'=>count($this->data['stockData']), 'data'=>$this->data['stockData']]);
    }

    public function getMaterialIssueList(){
        $postData = $this->input->post();
        $this->data['miList'] = $this->store->getMaterialIssueList($postData);
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['miList']]);
    }

    public function addIssueMaterial(){
        $data = $this->input->post();
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }

    public function saveIssuedMaterial() {

        $data = $this->input->post();
		
		$issue_no = $this->store->getNextIssueNo();
        $data['issue_number'] = 'ISU/'.str_pad($issue_no, 4, '0', STR_PAD_LEFT);
        $data['issue_no'] = $issue_no;
        
        $errorMessage = array(); 

        if(empty(strtotime($data['issue_date']))) {  $errorMessage['issue_date'] = "Date is required";  }
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
			$data['issue_date'] = date('Y-m-d',strtotime($data['issue_date']));
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
	
	/******* STOCK TRANSFER *******/

    public function addTransferLog(){
        $data = $this->input->post();
        $this->data['projectList'] = $this->project->getProjectList(['ignore_project_id' => $data['project_id']]);
        $this->data['empData'] = $this->employee->getEmployeeList();
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }

    public function saveTransferItem() {
        $data = $this->input->post();
		
        $errorMessage = array(); 
		
        if(empty(strtotime($data['trans_date'])))
			$errorMessage['trans_date'] = "Date is required";
		
        if(empty($data['item_id']))
			$errorMessage['item_id'] = "Item is required";
		
		if(empty($data['from_project_id']))
			$errorMessage['from_project_id'] = "Project is required.";
		
		if(empty($data['to_project_id']))
			$errorMessage['to_project_id'] = "Project is required.";
		
        if(!empty($data['to_project_id']) && !empty($data['from_project_id']) && $data['to_project_id'] == $data['from_project_id'])
			$errorMessage['to_project_id'] = "From and To both projects are same";
		
		if(empty($data['qty']) OR $data['qty']<=0){ $errorMessage['qty'] = "Issue Qty is required."; }
        else{
            $stockData = $this->store->getItemStockBatchWise(['item_id'=>$data['item_id'],'location_id'=>$data['from_project_id'],'stock_required'=>1,'single_row'=>1]);
            if($data['qty'] > $stockData->qty){
                $errorMessage['qty'] = "Stock not available."; 
            }
        }

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$data['trans_date'] = date('Y-m-d',strtotime($data['trans_date']));
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

    public function approveRequest(){
		$data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$data['approved_by'] = $this->loginId;
			$data['approve_date'] = date('Y-m-d');
			$this->printJson($this->purchaseIndent->approveIndent($data));
		endif;
	}
	
}
?>