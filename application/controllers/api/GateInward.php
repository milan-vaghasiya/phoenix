<?php
class GateInward extends MY_ApiController{

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Gate Inward";
        $this->data['headData']->pageUrl = "api/gateInward";
        $this->data['headData']->base_url = base_url();
    }
	
    public function getGIList(){		
		$data = $this->input->post();
        $this->data['giList'] = $this->gateInward->getGIList($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['giList']]);
    }
	
    public function addGateInward(){
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>[1,2,3]]);
        //$this->data['itemList'] = $this->item->getItemList(['item_type'=>[1,2,3]]);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }

    public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();
        
        if(!empty($data['item_data']) && gettype($data['item_data']) == "string"): $data['item_data'] = json_decode($data['item_data'],true); endif;
		
        if(empty(strtotime($data['trans_date'])))
			$errorMessage['trans_date'] = "Date is required";
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        if (empty($data['item_data']))
            $errorMessage['item_data'] = "Item is required.";
		if (empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			if(isset($_FILES['img_file']['name'])):
                if($_FILES['img_file']['name'] != null || !empty($_FILES['img_file']['name'])):
                    $this->load->library('upload');
                    $_FILES['userfile']['name']     = $_FILES['img_file']['name'];
                    $_FILES['userfile']['type']     = $_FILES['img_file']['type'];
                    $_FILES['userfile']['tmp_name'] = $_FILES['img_file']['tmp_name'];
                    $_FILES['userfile']['error']    = $_FILES['img_file']['error'];
                    $_FILES['userfile']['size']     = $_FILES['img_file']['size'];
                    
                    $imagePath = realpath(APPPATH . '../assets/uploads/material_receive/');
                    $config = ['file_name' => 'mr-'.time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path' =>$imagePath];

                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload()):
                        $errorMessage['img_file'] = $this->upload->display_errors();
                        $this->printJson(["status"=>0,"message"=>$errorMessage]);
                    else:
                        $uploadData = $this->upload->data();
                        $data['img_file'] = $uploadData['file_name'];
                    endif;
                endif;
            endif;
			$data['trans_date'] = date('Y-m-d',strtotime($data['trans_date']));
			
            $this->printJson($this->gateInward->save($data));
        endif;
    }

	public function printGRN(){
        $id = $this->input->post('id'); 

		$this->data['dataRow'] = $dataRow = $this->gateInward->getGIList(['id'=>$id, 'item_detail'=>1]);
		$this->data['partyData'] = $this->party->getParty(['id'=>$dataRow->party_id]);
		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo(1);
        if(isset($dataRow) && $dataRow !=null){
		
            $logo = base_url('assets/images/logo.png');
            $this->data['letter_head'] =  base_url('assets/images/letterhead.png');

            $pdfData = $this->load->view('gate_inward/print',$this->data,true);	
            
            $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                    <tr>
                        <td style="width:25%;">GRN No. & Date : '.$dataRow->trans_number.' ['.formatDate($dataRow->trans_date).']</td>
                        <td style="width:25%;"></td>
                        <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                    </tr>
                </table>';

            $mpdf = new \Mpdf\Mpdf();
            $pdfFileName = str_replace(["/","-"],"_",$dataRow->trans_number) . '.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->SetDisplayMode('fullpage');		
            $mpdf->SetWatermarkImage($logo,0.03,array(120,30));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetProtection(array('print'));
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
            $mpdf->WriteHTML($pdfData);
            // $mpdf->Output($pdfFileName,'I');
            $this->printJson(['status'=>1,'message'=>'Data Found.','data'=>base64_encode($mpdf->OutputBinaryData())]);
        }else{
            $this->printJson(['status'=>0,'message'=>'Data Not Found.','data'=>null]);
        }
	}

}
?>