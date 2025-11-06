<?php
class PaymentVoucher extends MY_ApiController{

	public function __construct(){
		parent::__construct();
        $this->data['headData']->pageTitle = "Payment Voucher";
        $this->data['headData']->pageUrl = "api/paymentVoucher";
        $this->data['headData']->base_url = base_url();
	}
	
    public function addPaymentVoucher(){
		$data = $this->input->post();
		
		$party_category = [1,2,3,4,5];
		$this->data['fromPartyList'] = [];
		$this->data['bankCashList'] = [];
		if(!empty($data['entry_type']) AND (in_array($data['entry_type'],[1,2,3])))
		{
			$groupCode = [];
			if($data['entry_type'] == 1){$groupCode = ['"ED"','"EI"'];}
			if($data['entry_type'] == 2){$groupCode = ['"SD"'];}
			if($data['entry_type'] ==3){$groupCode = ['"SC"','"SD"'];}
			$groupCode[] ='"CP"';$groupCode[] = '"EMP"';
			$this->data['fromPartyList'] = $this->party->getPartyList(['group_code'=>$groupCode]);	
			
			$this->data['bankCashList'] = $this->party->getPartyList(['group_code'=>['"BA"','"BOL"','"BOA"','"CS"']]);			
		}
		else{ $this->data['fromPartyList'] = $this->party->getPartyList($party_category); }
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
	}

    public function getVoucher(){		
		$data = $this->input->post();
        $this->data['expenseDetail'] = $this->paymentVoucher->getVoucher($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['expenseDetail']]);
    }

    public function getVoucherList(){		
		$data = $this->input->post();
        $this->data['summary'] = $this->paymentVoucher->getPaymentSummary($data);
        $this->data['transList'] = $this->paymentVoucher->getVoucherList($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }
	
    public function getExpenseSummary(){		
		$data = $this->input->post();
		
		$data['group_by'] = 'payment_trans.vou_acc_id';
        $this->data['summary'] = $this->paymentVoucher->getExpenseSummary($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }


    public function save(){
        $data = $this->input->post();
		$data['trans_no'] = $this->paymentVoucher->getNextTransNo();
        $data['trans_number']	= 'PT/'.$this->shortYear.'/'.$data['trans_no'];
		
        $errorMessage = array();
		
		$data['vou_acc_id'] = $data['from_party_id'];
		
        if(empty($data['entry_type'])):
            $errorMessage['entry_type'] = "Voucher Type is required.";
		else:
			if($data['entry_type']==4):
				$data['opp_acc_id'] = $data['to_party_id'];
			else:
				$data['opp_acc_id'] = $data['bank_cash_id'];
			endif;
		endif;
		unset($data['from_party_id'],$data['to_party_id'],$data['bank_cash_id']);
		
        if(empty(strtotime($data['trans_date'])))
            $errorMessage['trans_date'] = "Date is required.";
        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required.";
        if(empty($data['vou_acc_id']))
            $errorMessage['vou_acc_id'] = "Party is required.";
        if(empty($data['opp_acc_id']))
			$errorMessage['opp_acc_id'] = "Cash/Bank is required.";
		if(empty($data['amount']))
            $errorMessage['amount'] = "Amount is required.";
		if($data['entry_type']!=4):
			if(empty($data['pay_mode']))
				$errorMessage['pay_mode'] = "Pay Mode is required.";
		endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(isset($_FILES['proof_file']['name'])):
                if($_FILES['proof_file']['name'] != null || !empty($_FILES['proof_file']['name'])):
                    $this->load->library('upload');
                    $_FILES['userfile']['name']     = $_FILES['proof_file']['name'];
                    $_FILES['userfile']['type']     = $_FILES['proof_file']['type'];
                    $_FILES['userfile']['tmp_name'] = $_FILES['proof_file']['tmp_name'];
                    $_FILES['userfile']['error']    = $_FILES['proof_file']['error'];
                    $_FILES['userfile']['size']     = $_FILES['proof_file']['size'];
                    
                    $imagePath = realpath(APPPATH . '../assets/uploads/payment/');
                    $config = ['file_name' => 'expense-'.time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload()):
                        $errorMessage['proof_file'] = $this->upload->display_errors();
                        $this->printJson(["status"=>0,"message"=>$errorMessage]);
                    else:
                        $uploadData = $this->upload->data();
                        $data['proof_file'] = $uploadData['file_name'];
                    endif;
                endif;
            endif;
			$data['trans_date'] = date('Y-m-d',strtotime($data['trans_date']));
			
            $this->printJson($this->paymentVoucher->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->paymentVoucher->delete($id));
        endif;
    }

    public function saveApproveExpense(){
        $data = $this->input->post(); 
        $errorMessage = array();

        if($data['status'] == 1){
            if(empty($data['amount']) || $data['amount'] <= 0)
                $errorMessage['amount'] = "Amount is required.";
        }elseif($data['status'] == 2){
            if(empty($data['rej_reason']))
                $errorMessage['rej_reason'] = "Reject Reason is required.";
        }
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->paymentVoucher->saveApproveExpense($data));
        endif;
    }

	public function getTransReport($jsonData=''){
        if(!empty($jsonData)){$postData = (Array) decodeURL($jsonData);}
        else{$postData = $this->input->post();}
		
		$postData['emp_id'] = (!empty($postData['emp_id']) ? $postData['emp_id'] : $this->loginId);
		$postData['file_type'] = "PDF";
		$postData['attendance_status'] = 1;
		$postData['is_active'] = 1;
		$postData['from_date'] = (!empty($postData['from_date']) ? formatDate($postData['from_date'],'Y-m-d') : date('Y-m-d'));
		$postData['to_date'] = (!empty($postData['to_date']) ? formatDate($postData['to_date'],'Y-m-d') : date('Y-m-d'));
		
        $empData = $this->employee->getEmployeeList($postData);
		
        $lastDay = intVal(date('d',strtotime($postData['to_date'])));
        $thead='<tr style="background:#dddddd;">';
					//$thead.='<th style="width:50px;">Code</th>';
					//$thead.='<th>Designation</th>';
					$thead.='<th class="text-center">Date</th>';
					$thead.='<th class="text-center">Status</th>';
					$thead.='<th class="text-center">In Time</th>';
					$thead.='<th class="text-center">Break Start</th>';
					$thead.='<th class="text-center">Break End</th>';
					$thead.='<th class="text-center">Out Time</th>';
					$thead.='<th class="text-center" style="width:80px;">WH</th>';
					$thead.='<th style="width:150px;">Project</th>';
				$thead.='</tr>';  
       
        $empArray = array_reduce($empData, function($emp, $employee) {
            $emp[$employee->emp_name][] = $employee;
            return $emp;
        }, []);
		
		$begin = new DateTime($postData['from_date']);
		$end = new DateTime($postData['to_date']);
		$end = $end->modify( '+1 day' ); 
		
		$interval = new DateInterval('P1D');
		$dateRange = new DatePeriod($begin, $interval ,$end);
		
        $tbody='';$i=0;
        foreach($empData as $emp):
            $i++;
            
			$minLimitPerDay = 3600; // 1 Hour
			$hdLimit = 14400; // 4 Hour
            
            foreach($dateRange as $date):
				
				$currentDate =  date("Y-m-d",strtotime($date->format("Y-m-d")));
				$currentDay =  date("D",strtotime($date->format("Y-m-d")));
			
				$tbody.='<tr>';
				//$tbody.='<td class="text-center" style="vertical-align:middle;">'.$emp->emp_code.'</td>';
				//$tbody.='<td style="vertical-align:middle;" >'.$emp->emp_name.'<br><small>'.$emp->emp_designation.'</small></td>';
                
				$status="A"; $class="text-danger";$punchDates = array();$whRow='';
				$empAttendanceLog = $this->attendance->getPunchByDate(['emp_id' => $emp->id,'from_date' => $currentDate,'to_date' => $currentDate]);
				
				$empPunches = array_column($empAttendanceLog, 'punch_date');
				$empPunches = sortDates($empPunches,'ASC');
				
				$t=1;$wph = Array();$idx=0;$stay_time=0;$twh = 0;$wh=0;$ot=0;$present_status = 'P';$punches = Array();
				foreach($empPunches as $punch)
				{
					$punches[]= date("H:i:s", strtotime($punch));
					$wph[$idx][]=strtotime($punch);
					if($t%2 == 0){$stay_time += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
					$t++;
				}
				$wh = $stay_time;
				if(empty($punches[1]) AND !empty($punches[0]) AND $currentDate == date('Y-m-d')):
					$status = "P";
					$class = "text-success";
				else:
					if($wh >= $hdLimit):
						$status = "P";
						$class = "text-success";
					elseif($wh > 0 AND $wh < $minLimitPerDay):
						$status = "A";
						$class="text-danger";
					elseif($wh >= $minLimitPerDay AND $wh < $hdLimit):
						$status = "HD";
						$class = "text-info";
					endif;
				endif;
				
                if($currentDay == "Sun"){
					if($status == "A"){$status = "W";$class = "bg-light text-dark";}
                    if($status == "P"){$status = "WP";$class = "text-success";}
                    if($status == "HD"){$status = "W-HD";$class = "text-success";}
                    if($status == "L"){$status = "WL";}
                }
				
				if($wh > 0){ $whRow = '<td class="text-center">'.s2hi($wh).'</td>'; }
				else{ $whRow = '<td class="text-center"> - </td>'; }
				
				$tbody .= '<td class="text-center" style="">'.formatDate($currentDate,'d-m-Y').'</td>';
				$tbody .= '<td class="text-center '.$class.'" style="">'.$status.'</td>';
				$tbody .= '<td class="text-center" style="vertical-align:middle;" >'.(!empty($punches[0]) ? $punches[0] : '').'</td>';
				$tbody .= '<td class="text-center" style="vertical-align:middle;" >'.(!empty($punches[1]) ? $punches[1] : '').'</td>';
				$tbody .= '<td class="text-center" style="vertical-align:middle;" >'.(!empty($punches[2]) ? $punches[2] : '').'</td>';
				$tbody .= '<td class="text-center" style="vertical-align:middle;" >'.(!empty($punches[3]) ? $punches[3] : '').'</td>';
				$tbody .= $whRow;
				$projectName = (!empty($empAttendanceLog[0]->project_name) ? $empAttendanceLog[0]->project_name : "");
				$tbody.='<td style="vertical-align:middle;" >'.$projectName.'</td>';
				$tbody .= '</tr>';
            endforeach;
			
        endforeach;
        
        $reportTitle = 'Attendance Report';
        $report_date = $postData['from_date'].' to '.formatDate($postData['to_date'],'d-m-Y');
		$logo = base_url('assets/images/logo.png');

        $pdfData = '<table id="attendanceTable" class="table table-bordered itemList" repeat_header="1">
                        <thead class="thead-info" id="theadData">'.$thead.'</thead>
                        <tbody id="tbodyData">'.$tbody.'</tbody>
                    </table>';

                
        $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="text-uppercase text-left"><img src="'.$logo.'" class="img" style="height:30px;"></td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                            <td class="text-uppercase text-right" style="font-size:0.8rem;width:30%">Date : '.$report_date.'</td>
                        </tr>
                    </table>';
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                    <tr>
                        <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                        <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                    </tr>
                </table>';

        if(!empty($postData['file_type']) && $postData['file_type'] == 'PDF')
        {
            //$mpdf = new \Mpdf\Mpdf();
			$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [220, 420]]);
            $pdfFileName = 'AttendanceReport_'.str_replace(["/","-"],"_",date('d-m-Y')).'.pdf';          
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            //$mpdf->AddPage('P','','','','',5,5,15,10,3,3,'','','','','','','','','','A5-P');
            $mpdf->AddPage('P','','','','',5,5,15,10,3,3);
            $mpdf->WriteHTML($pdfData);	
            ob_clean();	
            //$mpdf->Output($pdfFileName, 'I');
			
			$this->printJson(['status'=>1,'message'=>'Data Found.','data'=>base64_encode($mpdf->OutputBinaryData())]);
        } else { 
            $this->printJson(['status'=>1,'thead'=>$thead, 'tbody'=>$tbody]); 
        }
	}

	
}
?>