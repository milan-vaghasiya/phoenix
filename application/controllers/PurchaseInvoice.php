<?php
class PurchaseInvoice extends MY_Controller{
    private $indexPage = "purchase_invoice/index";
    private $form = "purchase_invoice/form";    

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Purchase Invoice";
		$this->data['headData']->controller = "purchaseInvoice";        
        $this->data['headData']->pageUrl = "purchaseInvoice";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'purchaseInvoice']);
	}

    public function index(){
        $this->data['tableHeader'] = getAccountingDtHeader("purchaseInvoice");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->purchaseInvoice->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPurchaseInvoiceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addInvoice(){
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>[2,3]]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,4,10"]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Purchase']);
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList(['ref_id'=>6,'final_location'=>1]);

        $this->data['taxClassList'] = $this->taxClass->getActiveTaxClass(1);
		$this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'DT'","'ED'","'EI'","'ID'","'II'"]]);
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_number']))
            $errorMessage['trans_number'] = "Inv No. is required.";
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        if(empty($data['tax_class_id']))
            $errorMessage['tax_class_id'] = "GST Type is required.";
        if(empty($data['itemData']))
            $errorMessage['itemData'] = "Item Details is required.";

        if(!empty($_FILES['attachment']['name']) || $_FILES['attachment']['name'] != NULL):
            $attachment = "";
            $this->load->library('upload');
            
            $_FILES['userfile']['name']     = $_FILES['attachment']['name'];
            $_FILES['userfile']['type']     = $_FILES['attachment']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['attachment']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['attachment']['error'];
            $_FILES['userfile']['size']     = $_FILES['attachment']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/purchase_invoice/');
            $file = pathinfo($_FILES['attachment']['name'], PATHINFO_FILENAME);
            $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);

            $fileName = preg_replace('/[^A-Za-z0-9]+/', '_', strtolower($file)).".".$ext;
            $config = ['file_name' => $fileName, 'allowed_types' => 'jpg|jpeg|png|gif|JPG|JPEG|PNG', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

            $this->upload->initialize($config);

            if(!$this->upload->do_upload()):
                $errorMessage['attachment'] = $fileName . " => " . $this->upload->display_errors();
            else:
                $uploadData = $this->upload->data();
                $attachment = $uploadData['file_name'];
            endif;

            if(!empty($errorMessage['attachment'])):
                if (file_exists($imagePath . '/' . $attachment)) : unlink($imagePath . '/' . $attachment); endif;
            endif;

            $data['attachment'] = $attachment;
        endif;
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['doc_date'] = date("Y-m-d");
            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short;
            $this->printJson($this->purchaseInvoice->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->purchaseInvoice->getPurchaseInvoice(['id'=>$id,'itemList'=>1]);
        $this->data['gstinList'] = $this->party->getPartyGSTDetail(['party_id' => $dataRow->party_id]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>[2,3]]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,4,10"]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Purchase']);
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList(['ref_id'=>6,'final_location'=>1]);
        
        $this->data['taxClassList'] = $this->taxClass->getActiveTaxClass(1);
        $this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'DT'","'ED'","'EI'","'ID'","'II'"]]);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->purchaseInvoice->delete($id));
        endif;
    }

    public function getPartyInvoiceItems(){
        $data = $this->input->post();
        $this->data['orderItems'] = $this->purchaseInvoice->getPendingInvoiceItems($data);
        $this->load->view('debit_note/create_debitnote',$this->data);
    }

    public function printInvoice($id){
		$this->data['invData'] = $invData = $this->purchaseInvoice->getPurchaseInvoice(['id'=>$id,'itemList'=>1]);        
		$this->data['partyData'] = $this->party->getParty(['id'=>$invData->party_id]);

        $taxClass = $this->taxClass->getTaxClass($invData->tax_class_id);
        $this->data['taxList'] = (!empty($taxClass->tax_ids))?$this->taxMaster->getTaxList(['tax_ids'=>$taxClass->tax_ids]):array();
        $this->data['expenseList'] = (!empty($taxClass->expense_ids))?$this->expenseMaster->getExpenseList(['expense_ids'=>$taxClass->expense_ids]):array();

		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo($invData->cm_id);
		
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url($companyData->print_header);

        $htmlFooter = '<table style="border-top:1px solid #545454;margin-top:1px;">
            <tr>
                <td class="text-right">Page No. {PAGENO}/{nbpg}</td>
            </tr>
        </table>';

        $this->data['maxLinePP'] = (!empty($data['max_lines']))?$data['max_lines']:15;
        $pdfData = $this->load->view('purchase_invoice/print',$this->data,true); 

		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = str_replace(["/","-"," "],"_",$invData->trans_number).'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->SetTitle($pdfFileName); 
        $mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,45));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
        $mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,5,5,15,5,5,'','','','','','','','','','A4-P');
    
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
}
?>