<?php
class Whatsapp extends MY_Controller{
    private $indexPage = "whatsapp/index";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Whatsapp";
		$this->data['headData']->controller = "whatsapp";
        $this->data['headData']->pageUrl = "whatsapp";
	}

	public function index(){
        $this->load->view($this->indexPage,$this->data);
    }

	public function getWpQrCode(){
		$apiKey = WP_API_KEY;
		if(!empty($apiKey)):
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'http://whatsapp.nativebittechnologies.com/api/v1/GetConnectionStatus?key='.$apiKey,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
			));
			$response = curl_exec($curl);
			curl_close($curl);
			$resultData =  json_decode($response);
			
			if($resultData->ErrorCode == "000"):
				if($resultData->Data->Status == "CONNECTED"):
					$this->session->set_userdata('whatsappLogin',1);
					$logData = [
						'action_name' => "GetConnectionStatus",
						'post_json' => "",
						'response_json' => $response,
						'created_by' => $this->loginId,
						'created_at' => date("Y-m-d H:i:s")
					];
					$this->db->insert("whatsapp_log",$logData);
					$this->printJson(['status'=>1,'message'=>"success",'data'=>$resultData,'key'=>$apiKey]);
				else:
					$this->unsetWhatsappInstanceNumber();
					$qrCode = $this->generateWpQrCode();
					$qrCode['key'] = $apiKey;
					$this->printJson($qrCode);
				endif;
			else:
				$logData = [
					'action_name' => "GetConnectionStatus",
					'post_json' => "",
					'response_json' => $response,
					'created_by' => $this->loginId,
					'created_at' => date("Y-m-d H:i:s")
				];
				$this->db->insert("whatsapp_log",$logData);

				$this->printJson(['status'=>0,'message'=>"Somthing went wrong. Error : ".$resultData->ErrorMessage,'data'=>$resultData]);
			endif;
		else:
			$logData = [
				'action_name' => "GetConnectionStatus",
				'post_json' => "",
				'response_json' => "{'Error' : 'API Key not found.'}",
				'created_by' => $this->loginId,
				'created_at' => date("Y-m-d H:i:s")
			];
			$this->db->insert("whatsapp_log",$logData);

			$this->printJson(['status'=>0,'message'=>'API Key not found.']);
		endif;
	}

	public function generateWpQrCode(){
		$masterKey = WP_MASTER_KEY;
		$apiKey = WP_API_KEY;
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'http://whatsapp.nativebittechnologies.com/api/v1/GetQRCode?masterkey='.$masterKey.'&key='.$apiKey,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		$resultData =  json_decode($response);
		if($resultData->ErrorCode == "000"):
			$logData = [
				'action_name' => "GetQRCode",
				'post_json' => "",
				'response_json' => $response,
				'created_by' => $this->loginId,
				'created_at' => date("Y-m-d H:i:s")
			];
			$this->db->insert("whatsapp_log",$logData);

			return ['status'=>2,'message'=>"success",'data'=>$resultData];
		else:
			$logData = [
				'action_name' => "GetQRCode",
				'post_json' => "",
				'response_json' => $response,
				'created_by' => $this->loginId,
				'created_at' => date("Y-m-d H:i:s")
			];
			$this->db->insert("whatsapp_log",$logData);

			return ['status'=>0,'message'=>"Somthing went wrong. Error : ".$resultData->ErrorMessage,'data'=>$resultData];
		endif;
	}

	public function getQrStatus(){
		$key = $this->input->post('key');
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'http://whatsapp.nativebittechnologies.com/api/v1/GetConnectionStatus?key='.$key,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		$resultData =  json_decode($response);
		if($resultData->ErrorCode == "000"):
			if($resultData->Data->Status == "CONNECTED"):
				$this->session->set_userdata('whatsappLogin',1);

				$logData = [
					'action_name' => "GetConnectionStatus",
					'post_json' => "",
					'response_json' => $response,
					'created_by' => $this->loginId,
					'created_at' => date("Y-m-d H:i:s")
				];
				$this->db->insert("whatsapp_log",$logData);

				$this->printJson(['status'=>1,'message'=>"success",'result'=>$resultData,'key'=>$key]);
			else:
				$this->printJson(['status'=>0,'message'=>"false",'result'=>$resultData]);
			endif;
		else:
			$logData = [
				'action_name' => "GetConnectionStatus",
				'post_json' => "",
				'response_json' => $response,
				'created_by' => $this->loginId,
				'created_at' => date("Y-m-d H:i:s")
			];
			$this->db->insert("whatsapp_log",$logData);

			$this->printJson(['status'=>0,'message'=>"Somthing went wrong. Error : ".$resultData->ErrorMessage,'result'=>$resultData]);
		endif;
	}

	public function whatsappLogout(){
		$apiKey = WP_API_KEY;
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'http://whatsapp.nativebittechnologies.com/api/v1/SetInstanceLogout?key='.$apiKey,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
		));
		$response = curl_exec($curl);
		$resultData =  json_decode($response);
		curl_close($curl);
		if($resultData->ErrorCode == "000"):
			if($resultData->Data->Status == "LOGOUT"):
				$logData = [
					'action_name' => "SetInstanceLogout",
					'post_json' => "",
					'response_json' => $response,
					'created_by' => $this->loginId,
					'created_at' => date("Y-m-d H:i:s")
				];
				$this->db->insert("whatsapp_log",$logData);

				$this->unsetWhatsappInstanceNumber();
				$this->session->set_userdata('whatsappLogin',0);
				$this->printJson(['status'=>1,'message'=>"success",'result'=>$resultData]);
			else:
				$logData = [
					'action_name' => "SetInstanceLogout",
					'post_json' => "",
					'response_json' => $response,
					'created_by' => $this->loginId,
					'created_at' => date("Y-m-d H:i:s")
				];
				$this->db->insert("whatsapp_log",$logData);

				$this->printJson(['status'=>0,'message'=>"false",'result'=>$resultData]);
			endif;
		else:
			$logData = [
				'action_name' => "SetInstanceLogout",
				'post_json' => "",
				'response_json' => $response,
				'created_by' => $this->loginId,
				'created_at' => date("Y-m-d H:i:s")
			];
			$this->db->insert("whatsapp_log",$logData);

			$this->printJson(['status'=>0,'message'=>"Somthing went wrong. Error : ".$resultData->ErrorMessage,'result'=>$resultData]);
		endif;
	}

	public function unsetWhatsappInstanceNumber(){
		$apiKey = WP_API_KEY;
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://whatsapp.nativebittechnologies.com/api/v1/ChangeInstanceNumber?key='.$apiKey,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		));
		$response = curl_exec($curl);
		curl_close($curl);
		//echo "<script>console.log(".$response.");</script>";

		$logData = [
			'action_name' => "ChangeInstanceNumber",
			'post_json' => "",
			'response_json' => $response,
			'created_by' => $this->loginId,
			'created_at' => date("Y-m-d H:i:s")
		];
		$this->db->insert("whatsapp_log",$logData);

		return true;
	}

	// message_type : Message/Document/Image
	public function sendMessage(){
		//$message_type,$data
		$data = $this->input->post();
		$message_type = $data['message_type'];
		$subject = (!empty($data['subject']))?$data['subject']:"Send Invoice";
		$postData = array();$url='';$instanceKey = WP_API_KEY;

		if($message_type == "Document"):

			if(in_array($subject,["Send Invoice",'Send C.N.'])):
				$invoiceData = $this->generatePdf($data['inv_id'],$data['docName']);	
				$data['file_path'] = $invoiceData['file_path'];
				$data['file_name'] = $invoiceData['file_name'];
				$invData = $invoiceData['inv_data'];
				$partyData = $this->party->getParty(['id'=>$invData->party_id]);
			endif;

			if($_SERVER['HTTP_HOST'] == 'localhost'):
				$partyData->whatsapp_no = "8160897829";
				//$partyData->whatsapp_no = "9427235336";
			endif;

			if(!empty($partyData->whatsapp_no)):
				$partyData->whatsapp_no = str_replace("+91",'',$partyData->whatsapp_no);
				$partyData->whatsapp_no = preg_replace('/[^0-9]/', '', $partyData->whatsapp_no);
				$partyData->whatsapp_no = substr(trim($partyData->whatsapp_no),-10);
				if(strlen($partyData->whatsapp_no) > 10 || strlen($partyData->whatsapp_no) < 10):
					$this->printJson(['status'=>0,'message'=>"Invalid mobile no.",'data'=>$postData]);
				endif;
				$data['send_to'] = "91".trim($partyData->whatsapp_no);
			else:
				$this->printJson(['status'=>0,'message'=>"Somthing went wrong. Error : Mobile no. not found.",'data'=>$postData]);
			endif;
		elseif($message_type == "Image"):

		else:
			if(in_array($subject,["Send Invoice","Send Payment Reminder"])):
				$invData = $this->salesInvoice->getSalesInvoice(['id'=>$data['inv_id'],'itemList'=>0]);
				$partyData = $this->party->getParty(['id'=>$invData->party_id]);
			endif;

			if($_SERVER['HTTP_HOST'] == 'localhost'):
				$partyData->whatsapp_no = "8160897829";
				//$partyData->whatsapp_no = "9427235336";
			endif;

			if(!empty($partyData->whatsapp_no)):
				$partyData->whatsapp_no = str_replace("+91",'',$partyData->whatsapp_no);
				$partyData->whatsapp_no = preg_replace('/[^0-9]/', '', $partyData->whatsapp_no);
				$partyData->whatsapp_no = substr(trim($partyData->whatsapp_no),-10);
				if(strlen($partyData->whatsapp_no) > 10 || strlen($partyData->whatsapp_no) < 10):
					$this->printJson(['status'=>0,'message'=>"Invalid mobile no.",'data'=>$postData]);
				endif;
				$data['send_to'] = "91".trim($partyData->whatsapp_no);
			else:
				$this->printJson(['status'=>0,'message'=>"Somthing went wrong. Error : Mobile no. not found.",'data'=>$postData]);
			endif;
		endif;
		// Sales Invoice
		$data['message'] = $this->messageTamplate($invData,$subject);
		
		switch($message_type){
			case 'Message';
				$postData['key'] = $instanceKey;
				$postData['to'] = $data['send_to'];
				$postData['message'] = $data['message'];
				$postData['IsUrgent'] = 'True';
				break;
			case 'Document';
				$postData['key'] = $instanceKey;
				$postData['to'] = $data['send_to'];
				$postData['url'] = 'data:application/'.pathinfo($data['file_path'], PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents($data['file_path']));
				$postData['caption'] = $data['message'];
				$postData['filename'] = $data['file_name'];
				$postData['IsUrgent'] = 'True';
				break;
			case 'Image';
				$postData['key'] = $instanceKey;
				$postData['to'] = $data['send_to'];
				$postData['url'] = 'data:image/'.pathinfo($data['file_path'], PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents($data['file_path']));
				$postData['caption'] = $data['caption'];
				$postData['filename'] = $data['file_name'];
				$postData['IsUrgent'] = 'True';
				break;
		}

		if(!empty($postData)):
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'http://whatsapp.nativebittechnologies.com/api/v1/send'.$message_type,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => json_encode($postData),
			  CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
			));
			$response = curl_exec($curl);
			curl_close($curl); 
			$result = json_decode($response);	
			unset($postData['url']);
			if($result->ErrorCode == "000"):
				$logData = [
					'action_name' => "send".$message_type,
					'post_json' => json_encode($postData),
					'response_json' => $response,
					'created_by' => $this->loginId,
					'created_at' => date("Y-m-d H:i:s")
				];
				$this->db->insert("whatsapp_log",$logData);

				$this->printJson(['status'=>1,'message'=>$message_type." send successfully.",'data'=>$result]);
			else:
				$logData = [
					'action_name' => "send".$message_type,
					'post_json' => json_encode($postData),
					'response_json' => $response,
					'created_by' => $this->loginId,
					'created_at' => date("Y-m-d H:i:s")
				];
				$this->db->insert("whatsapp_log",$logData);

				$this->printJson(['status'=>0,'message'=>"Somthing went wrong. Error : ".$result->ErrorMessage,'data'=>$result]);
			endif;
		else:
			$logData = [
				'action_name' => "send".$message_type,
				'post_json' => "",
				'response_json' => "{'Error': 'Post Data not found.'}",
				'created_by' => $this->loginId,
				'created_at' => date("Y-m-d H:i:s")
			];
			$this->db->insert("whatsapp_log",$logData);

			$this->printJson(['status'=>0,'message'=>"Somthing went wrong. Error : Data not found.",'data'=>$postData]);
		endif;
	}

	public function sendBulkMessage(){
		$data = $this->input->post();
		$message_type = $data['message_type'];
		$postData = array();$instanceKey = WP_API_KEY;
		$data['send_to'] = array();
		
		if($message_type == "Document"):
			$invoiceData = $this->generatePdf($data['inv_id'],$data['docName']);	
			$data['file_path'] = $invoiceData['file_path'];
			$data['file_name'] = $invoiceData['file_name'];
			$invData = $invoiceData['inv_data'];
			$partyData = $this->party->getParty(['id'=>$invData->party_id]);
			if(!empty($partyData->whatsapp_no)):
				$partyData->whatsapp_no = str_replace("+91",'',$partyData->whatsapp_no);
				$partyData->whatsapp_no = preg_replace('/[^0-9]/', '', $partyData->whatsapp_no);
				$partyData->whatsapp_no = substr(trim($partyData->whatsapp_no),-10);
				if(strlen($partyData->whatsapp_no) == 10):
					$data['send_to'][0]['to'] = "91".trim($partyData->whatsapp_no);
				endif;		
			endif;
		elseif($message_type == "Image"):

		else:
			$invData = $this->salesInvoice->getSalesInvoice(['id'=>$data['inv_id'],'itemList'=>0]);
			$partyData = $this->party->getParty(['id'=>$invData->party_id]);
			if(!empty($partyData->whatsapp_no)):
				$partyData->whatsapp_no = str_replace("+91",'',$partyData->whatsapp_no);
				$partyData->whatsapp_no = preg_replace('/[^0-9]/', '', $partyData->whatsapp_no);
				$partyData->whatsapp_no = substr(trim($partyData->whatsapp_no),-10);
				if(strlen($partyData->whatsapp_no) == 10):
					$data['send_to'][0]['to'] = "91".trim($partyData->whatsapp_no);
				endif;
			endif;
		endif;
		
		if(empty($data['send_to'])):
			$this->printJson(['status'=>0,'message'=>"Somthing went wrong. Error : Mobile no. not found.",'data'=>$postData]);
		endif;

		// Sales Invoice
		$data['message'] = $this->messageTamplate($invData);
		
		$slug = "";
		switch($message_type){
			case 'Message';
				$slug = "SendBulkMessage";
				$postData['apikeys']['key'] = $instanceKey;
				$postData['toNumbers'] = $data['send_to'];
				$postData['message'] = $data['message'];
				$postData['IsUrgent'] = 'True';
				break;
			case 'Document';
				$slug = "sendBulkForDocument";
				$postData['apikeys']['key'] = $instanceKey;
				$postData['toNumbers'] = $data['send_to'];
				$postData['url'] = 'data:application/'.pathinfo($data['file_path'], PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents($data['file_path']));
				$postData['caption'] = $data['message'];
				$postData['filename'] = $data['file_name'];
				$postData['IsUrgent'] = 'True';
				break;
			case 'Image';
				$slug = "sendBulkForImage";
				$postData['apikeys']['key'] = $instanceKey;
				$postData['toNumbers'] = $data['send_to'];
				$postData['url'] = 'data:image/'.pathinfo($data['file_path'], PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents($data['file_path']));
				$postData['caption'] = $data['caption'];
				$postData['filename'] = $data['file_name'];
				$postData['IsUrgent'] = 'True';
				break;
		}

		if(!empty($postData)):
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'http://whatsapp.nativebittechnologies.com/api/v1/'.$slug,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => json_encode($postData),
			  CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
			));
			$response = curl_exec($curl);
			curl_close($curl); 
			$result = json_decode($response);	
			if($result->ErrorCode == "000"):
				$logData = [
					'action_name' => $slug,
					'post_json' => json_encode($postData),
					'response_json' => $response,
					'created_by' => $this->loginId,
					'created_at' => date("Y-m-d H:i:s")
				];
				$this->db->insert("whatsapp_log",$logData);

				$this->printJson(['status'=>1,'message'=>$message_type." send successfully.",'data'=>$result]);
			else:
				$logData = [
					'action_name' => $slug,
					'post_json' => json_encode($postData),
					'response_json' => $response,
					'created_by' => $this->loginId,
					'created_at' => date("Y-m-d H:i:s")
				];
				$this->db->insert("whatsapp_log",$logData);

				$this->printJson(['status'=>0,'message'=>"Somthing went wrong. Error : ".$result->ErrorMessage,'data'=>$result]);
			endif;
		else:
			$logData = [
				'action_name' => $slug,
				'post_json' => "",
				'response_json' => "{'Error' : 'Post Data not found.'}",
				'created_by' => $this->loginId,
				'created_at' => date("Y-m-d H:i:s")
			];
			$this->db->insert("whatsapp_log",$logData);

			$this->printJson(['status'=>0,'message'=>"Somthing went wrong. Error : Data not found.",'data'=>$postData]);
		endif;
	}

	public function generatePdf($inv_id,$documentType="TaxInv"){
        $this->trashFiles();//delete old pdf files from dir. if file is 24 hours old

		$printTypes[] = "ORIGINAL";
		$postData['header_footer'] = 1;

		$this->data['header_footer'] = $postData['header_footer'];
		$printHtml = "";
		if($documentType == "TaxInv"):
			$this->data['invData'] = $invData = $this->salesInvoice->getSalesInvoice(['id'=>$inv_id,'itemList'=>1]);
			$printHtml = "sales_invoice/print";
		elseif($documentType == "C.N."):
			$this->data['invData'] = $invData = $this->creditNote->getCreditNote(['id'=>$inv_id,'itemList'=>1]);
			$printHtml = "credit_note/print";
		endif;
		
		$this->data['partyData'] = $this->party->getParty(['id'=>$invData->party_id]);

        $taxClass = $this->taxClass->getTaxClass($invData->tax_class_id);
        $this->data['taxList'] = (!empty($taxClass->tax_ids))?$this->taxMaster->getTaxList(['tax_ids'=>$taxClass->tax_ids]):array();
        $this->data['expenseList'] = (!empty($taxClass->expense_ids))?$this->expenseMaster->getExpenseList(['expense_ids'=>$taxClass->expense_ids]):array();

		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo($invData->cm_id);
		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url($companyData->print_header);

        $htmlFooter = '<table style="border-top:1px solid #545454;margin-top:1px;">
            <tr>
                <td class="text-right">Page No. {PAGENO}/{nbpg}</td>
            </tr>
        </table>';
				
        $pdfData = "";
        $countPT = count($printTypes); $i=0;
        foreach($printTypes as $printType):
            ++$i;           
            $this->data['printType'] = $printType;
            $this->data['maxLinePP'] = (!empty($postData['max_lines']))?$postData['max_lines']:15;
		    $pdfData .= $this->load->view($printHtml,$this->data,true); 
            if($i != $countPT): $pdfData .= "<pagebreak resetpagenum='1'>"; endif;
        endforeach;
            
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = str_replace(["/","-"," "],"_",$invData->trans_number).'.pdf';
		$filePath = base_url('assets/uploads/invoice/'.$pdfFileName);
		$fpath = 'assets/uploads/invoice/'.$pdfFileName;

        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->SetTitle($pdfFileName); 
        $mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,45));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
        $mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,5,(($postData['header_footer'] == 1)?5:35),10,5,5,'','','','','','','','','','A4-P');    
		$mpdf->WriteHTML($pdfData);

		$mpdf->Output(FCPATH.$fpath,'F');
		return ['file_path'=>$filePath,'inv_data'=>$invData,'file_name'=>$pdfFileName];
	}	

	public function messageTamplate($invData,$subject){
        $companyData = $this->masterModel->getCompanyInfo($invData->cm_id);
		$message = "";

		if($subject == "Send Invoice"):
			$message = "To, *".strVal(LTRIM(RTRIM($invData->party_name)))."* \r\nRespected Sir,\r\nPlease find attached Sales Invoice No. : *".$invData->trans_number."*,\r\nDated : ".date("d/m/Y",strtotime($invData->trans_date)).", For Amount of Rs. : *".round($invData->net_amount,2)."*\r\nDISPATCHED IN VEHICLE NUMBER : *".$invData->vehicle_no."*\r\nRegards,\r\n*SHREE HARI NAMKEEN*";
		endif;

		if($subject == "Send C.N."):
			$message = "To, *".strVal(LTRIM(RTRIM($invData->party_name)))."* \r\nRespected Sir,\r\nPlease find attached Credit Note No. : *".$invData->trans_number."*,\r\nDated : ".date("d/m/Y",strtotime($invData->trans_date)).", For Amount of Rs. : *".round($invData->net_amount,2)."*\r\nRegards,\r\n*SHREE HARI NAMKEEN*";
		endif;

		if($subject == "Trip Detail"):
			$message = "To, *".strVal(LTRIM(RTRIM($invData->party_name)))."* \r\nRespected Sir,\r\nPlease find attached Sales Invoice No. : *".$invData->trans_number."*,\r\nDated : ".date("d/m/Y",strtotime($invData->trans_date)).", For Amount of Rs. : *".round($invData->net_amount,2)."*\r\nDISPATCHED IN VEHICLE NUMBER : *".$invData->vehicle_no."*\r\nDRIVER NAME : *".LTRIM(RTRIM($invData->driver_name))."*\r\nDRIVER PHONE NUMBER : *".$invData->driver_mobile_no."*\r\nRegards,\r\n*SHREE HARI NAMKEEN*";
		endif;

		if($subject == "Send Payment Reminder"):
			$message = "Hi, *".strVal(LTRIM(RTRIM($invData->party_name)))."* \r\n\r\nHope you're well! Just a friendly reminder regarding the pending payment for Invoice # *".$invData->trans_number."*, dated *".date("d/m/Y",strtotime($invData->trans_date))."* in the due amount of *Rs. ".round($invData->net_amount,2)."*.The due date is approaching, and we would appreciate it if you could settle the invoice at your earliest convenience.\r\n \r\nThank you for your prompt attention to this matter.\r\n\r\nBest Regards,\r\n*".$companyData->company_name."*";
		endif;

		return $message;
	}

	public function sendTripMessage(){
		$data = $this->input->post();
		$message_type = $data['message_type'];
		$subject = (!empty($data['subject']))?$data['subject']:"Send Invoice";
		$postData = array();$url='';$instanceKey = WP_API_KEY;

		$tripData = $this->trip->getTrip(['id'=>$data['ref_id']]);

		$invIds = explode(",",$tripData->inv_ids);
		foreach($invIds as $key=>$invId):			
			$invoiceData = $this->generatePdf($invId,"TaxInv");	
			$data['file_path'] = $invoiceData['file_path'];
			$data['file_name'] = $invoiceData['file_name'];
			$invData = $invoiceData['inv_data'];
			$invData->driver_name = $tripData->driver_name;
			$invData->driver_mobile_no = $tripData->driver_mobile_no;
			$partyData = $this->party->getParty(['id'=>$invData->party_id]);			

			if($_SERVER['HTTP_HOST'] == 'localhost'):
				$partyData->whatsapp_no = "8160897829";
				//$partyData->whatsapp_no = "9427235336";
			endif;

			$mobileNoError = false;
			if(!empty($partyData->whatsapp_no)):
				$partyData->whatsapp_no = str_replace("+91",'',$partyData->whatsapp_no);
				$partyData->whatsapp_no = preg_replace('/[^0-9]/', '', $partyData->whatsapp_no);
				$partyData->whatsapp_no = substr(trim($partyData->whatsapp_no),-10);
				if(strlen($partyData->whatsapp_no) > 10 || strlen($partyData->whatsapp_no) < 10):
					$mobileNoError = true;
					//$this->printJson(['status'=>0,'message'=>"Invalid mobile no.",'data'=>$postData]);
				endif;
				$data['send_to'] = "91".trim($partyData->whatsapp_no);
			else:
				$mobileNoError = true;
				//$this->printJson(['status'=>0,'message'=>"Somthing went wrong. Error : Mobile no. not found.",'data'=>$postData]);
			endif;

			// Sales Invoice
			$data['message'] = $this->messageTamplate($invData,$subject);
			
			switch($message_type){
				case 'Message';
					$postData['key'] = $instanceKey;
					$postData['to'] = $data['send_to'];
					$postData['message'] = $data['message'];
					$postData['IsUrgent'] = 'True';
					break;
				case 'Document';
					$postData['key'] = $instanceKey;
					$postData['to'] = $data['send_to'];
					$postData['url'] = 'data:application/'.pathinfo($data['file_path'], PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents($data['file_path']));
					$postData['caption'] = $data['message'];
					$postData['filename'] = $data['file_name'];
					$postData['IsUrgent'] = 'True';
					break;
				case 'Image';
					$postData['key'] = $instanceKey;
					$postData['to'] = $data['send_to'];
					$postData['url'] = 'data:image/'.pathinfo($data['file_path'], PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents($data['file_path']));
					$postData['caption'] = $data['caption'];
					$postData['filename'] = $data['file_name'];
					$postData['IsUrgent'] = 'True';
					break;
			}

			if(!empty($postData) && $mobileNoError == false):
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => 'http://whatsapp.nativebittechnologies.com/api/v1/send'.$message_type,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS => json_encode($postData),
					CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
				));
				$response = curl_exec($curl);
				curl_close($curl); 
				$result = json_decode($response);
				unset($postData['url']);
				if($result->ErrorCode == "000"):
					$logData = [
						'action_name' => "send".$message_type,
						'post_json' => json_encode($postData),
						'response_json' => $response,
						'created_by' => $this->loginId,
						'created_at' => date("Y-m-d H:i:s")
					];
					$this->db->insert("whatsapp_log",$logData);					
				else:
					$logData = [
						'action_name' => "send".$message_type,
						'post_json' => json_encode($postData),
						'response_json' => $response,
						'created_by' => $this->loginId,
						'created_at' => date("Y-m-d H:i:s")
					];
					$this->db->insert("whatsapp_log",$logData);
				endif;
			else:
				$logData = [
					'action_name' => "send".$message_type,
					'post_json' => "",
					'response_json' => "{'Error': 'Post Data not found.'}",
					'created_by' => $this->loginId,
					'created_at' => date("Y-m-d H:i:s")
				];
				$this->db->insert("whatsapp_log",$logData);
			endif;
		endforeach;

		$this->printJson(['status'=>1,'message'=>"Message send successfully."]);
	}
}
?>