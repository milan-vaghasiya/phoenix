<?php
class PaymentVoucher extends MY_Controller{
    private $index = "payment_voucher/index";
    private $form = "payment_voucher/form";	
	
	public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Payment Voucher";
		$this->data['headData']->controller = "paymentVoucher";
        $this->data['headData']->pageUrl = "paymentVoucher";
	}

	public function index(){
		$this->data['tableHeader'] = getAccountingDtHeader("paymentVoucher");
		$this->load->view($this->index,$this->data);
	}

    public function getDtRows($status=1){
        $data = $this->input->post();$data['entry_type'] = $status;
		$result = $this->paymentVoucher->getDtRows($data); 
		$sendData = array(); $i=($data['start'] + 1);
		foreach($result['data'] as $row):
			$row->sr_no = $i++;
			$sendData[] = getPaymentVoucher($row);
		endforeach;
		$result['data'] = $sendData;
		$this->printJson($result);
	}

    public function addPaymentVoucher(){
		$this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2,3,4"]);
		$this->data['ledgerList'] = $this->party->getPartyList(['party_category'=>5]);		
        $this->data['trans_prefix'] = 'RV/'.$this->shortYear.'/';
		$this->data['trans_no'] = $this->paymentVoucher->getNextTransNo();
        $this->data['trans_number']	= $this->data['trans_prefix'].$this->data['trans_no'];
		$this->load->view($this->form,$this->data);
	}

    public function getTransNo(){
		$data = $this->input->post();
        if($data['entry_type'] == 1):
            $this->data['trans_prefix'] = 'RV/'.$this->shortYear.'/';
			$this->data['trans_no'] = $this->paymentVoucher->getNextTransNo(1);
        else:
            $this->data['trans_prefix'] = 'PV/'.$this->shortYear.'/';
			$this->data['trans_no'] = $this->paymentVoucher->getNextTransNo(2);
        endif;
		$this->data['trans_number']	= $this->data['trans_prefix'].$this->data['trans_no'];
		$this->printJson(['status'=>1,'data'=>$this->data]);
	}

    public function save(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['trans_date']))
			$errorMessage['trans_date'] = "Voucher Date is required.";
		if(empty($data['entry_type']))
			$errorMessage['entry_type'] = "Entry Type is required.";
		if(empty($data['opp_acc_id']))
			$errorMessage['opp_acc_id'] = "Party Name is required.";
		if(empty($data['vou_acc_id']))
			$errorMessage['vou_acc_id'] = "Ledger Name is required.";
		if(empty($data['trans_mode']))
			$errorMessage['trans_mode'] = "Payment Mode is required.";
		if(empty($data['amount']))
			$errorMessage['amount'] = "Amount is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->paymentVoucher->save($data));
		endif;
	}

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->paymentVoucher->getVoucher(['id'=>$data['id']]);
		$this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2,3,4"]);
		$this->data['ledgerList'] = $this->party->getPartyList(['party_category'=>5]);
		if($dataRow->entry_type == 1):
            $this->data['trans_prefix'] = 'RV/'.$this->shortYear.'/';
			$this->data['trans_no'] = $this->paymentVoucher->getNextTransNo(1);
        else:
            $this->data['trans_prefix'] = 'PV/'.$this->shortYear.'/';
			$this->data['trans_no'] = $this->paymentVoucher->getNextTransNo(2);
        endif;
		$this->data['trans_number']	= $this->data['trans_prefix'].$this->data['trans_no'];
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->paymentVoucher->delete($id));
        endif;
    }

	public function printPaymentVoucher($id){
		$this->data['pvData'] = $pvData = $this->paymentVoucher->getVoucher(['id'=>$id]);
		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo(1);

		$logo = base_url('assets/images/logo.png');
        $this->data['letter_head'] =  base_url($companyData->print_header);

        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:50%;"></td>
							<th>For, '.$companyData->company_name.'</th>
						</tr>
						<tr>
							<td style="width:80%;"></td>
							<td style="width:20%;"><br/><br/><br/>'.$pvData->emp_name.'</td>
						</tr>
						<tr>
							<td style="width:60%;"></td>
							<td style="width:20%;"><b>Prepared By</b></td>
						</tr>
					</table>
					<table style="border-top:1px solid #545454;margin-top:1px;">
						<tr>
							<td style="width:25%;">PV No. & Date : '.$pvData->trans_number.' ['.formatDate($pvData->trans_date).']'.'</td>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
				
        $pdfData = "";
		$pdfData .= $this->load->view('payment_voucher/print',$this->data,true);
            
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = str_replace(["/","-"],"_",$pvData->trans_number) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
        $mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,45));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
        $mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
    
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
}
?>