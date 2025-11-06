<?php
class WorkOrder extends MY_Controller{
    private $indexPage = "work_order/index";
    private $form = "work_order/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Work Order";
		$this->data['headData']->controller = "workOrder";        
        $this->data['headData']->pageUrl = "workOrder";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'workOrder']);
	}

    public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader("workOrder");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->workOrder->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getWorkOrderData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addWorkOrder(){
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->workOrder->nextTransNo();
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>"2,3"]);
        $this->data['projectList'] = $this->project->getProjectList(['is_active'=>0]);
        $this->data['unitList'] = $this->item->itemUnits();
		$this->data['termsList'] = $this->terms->getTermsList(['type'=>'Purchase']);
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_no']))
            $errorMessage['trans_number'] = "PO. No. is required.";
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
		if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required.";
        if(empty($data['itemData']))
            $errorMessage['itemData'] = "Item Details is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->workOrder->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->workOrder->getWorkOrder(['id'=>$id,'itemList'=>1]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>"2,3"]);
        $this->data['projectList'] = $this->project->getProjectList(['is_active'=>0]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Purchase']);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->workOrder->delete($id));
        endif;
    }
	
	public function closeWO(){
		$data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->workOrder->closeWO($data));
		endif;
	}

    public function printWO($id){
		$this->data['dataRow'] = $woData = $this->workOrder->getWorkOrder(['id'=>$id,'itemList'=>1]);
		$this->data['partyData'] = $this->party->getParty(['id'=>$woData->party_id]);
		$this->data['termsData'] = (!empty($woData->termsConditions) ? $woData->termsConditions: "");
		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo(1);
		$logo = base_url('assets/images/logo.png');
        $this->data['letter_head'] =  base_url('assets/images/letterhead.png');

        $pdfData = $this->load->view('work_order/print',$this->data,true);	
		
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:25%;">PO No. & Date : '.$woData->trans_number.' ['.formatDate($woData->trans_date).']</td>
                    <td style="width:25%;"></td>
                    <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';
    
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = str_replace(["/","-"],"_",$woData->trans_number) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		// if(empty($woData->is_approve)){
            // $mpdf->SetWatermarkText('Unapproved',0.03,array(120,45));
            // $mpdf->showWatermarkText = true;
        // }else{
            $mpdf->SetWatermarkImage($logo,0.03,array(120,45));
            $mpdf->showWatermarkImage = true;
        // }
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
}
?>