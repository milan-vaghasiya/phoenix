<?php
class PurchaseIndent extends MY_Controller
{
    private $indexPage = "purchase_indent/index";
    private $form = "purchase_indent/form";

    public function __construct(){
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Purchase Indent";
        $this->data['headData']->controller = "purchaseIndent";
        $this->data['headData']->pageUrl = "purchaseIndent";
        $this->data['entryData'] = "";//$this->transMainModel->getEntryType(['controller'=>'purchaseIndent','tableName'=>'purchase_indent']);
    }

    public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $enqData = $this->purchaseIndent->getEmpWiseSubMenuPermission(['emp_id'=>$this->loginId, 'sub_controller_name'=>'purchaseDesk']);
        $poData = $this->purchaseIndent->getEmpWiseSubMenuPermission(['emp_id'=>$this->loginId, 'sub_controller_name'=>'purchaseOrders']);
		$this->data['enqData'] = (!empty($enqData->is_write) ? 1 : 0);
		$this->data['poData'] = (!empty($poData->is_write) ? 1 : 0);
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($status=1){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->purchaseIndent->getDTRows($data);

        $sendData = array();
        $i=($data['start']+1);		
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            if ($row->trans_status == 1) :
                $row->trans_status_label = '<span class="font-10 font-weight-bold badge bg-danger">Pending</span> <br>'.$row->created_name;
            elseif ($row->trans_status == 2) :
                $row->trans_status_label = '<span class="font-10 font-weight-bold badge bg-success">Completed</span> <br>'.$row->created_name;
            elseif ($row->trans_status == 3) :
                $row->trans_status_label = '<span class="font-10 font-weight-bold badge bg-dark">Closed</span> <br>'.$row->created_name;
            elseif ($row->trans_status == 4) :
                $row->trans_status_label = '<span class="font-10 font-weight-bold badge bg-primary">Approved</span> <br>'.$row->created_name;
            endif;
            $sendData[] = getPurchaseIndentData($row);
        endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function addPurchaseIndent(){
        $this->data['projectList'] = $this->project->getProjectList(['is_active'=>0]); 
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view($this->form, $this->data);
    }
	   
	public function save(){
        $data = $this->input->post();
        $errorMessage = array();

		if (empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required.";

        if (empty($data['item_data']) && empty($data['id']))
            $errorMessage['general_error'] = "Item Detail is required.";


        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            if(empty($data['id'])):
                $data['trans_no'] =  $this->purchaseIndent->getNextIndentNo();
                $data['trans_prefix'] =  "MR/".$this->shortYear;
                $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];
            endif;
          
            $this->printJson($this->purchaseIndent->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->purchaseIndent->getPurchaseIndent($data);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['projectList'] = $this->project->getProjectList(['is_active'=>0]); 
        $this->load->view($this->form, $this->data);
    }

    public function delete(){
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
            $this->printJson($this->purchaseIndent->changeReqStatus($data));
        endif;
    }

    public function approveIndent(){
		$data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$date = ($data['is_approve'] == 1) ? date('Y-m-d') : NULL;
            $isApprove =  ($data['is_approve'] == 1) ? $this->loginId : 0;
			
			$approveData = ['id'=> $data['id'],'trans_status'=>$data['trans_status'], 'approved_by' => $isApprove, 'approve_date'=>$date];
			$this->printJson($this->purchaseIndent->approveIndent($approveData));
		endif;
	}

}
?>