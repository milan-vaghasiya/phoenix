<?php
class Leave extends MY_Controller{
    private $indexPage = "hr/leave/index";
    private $leaveForm = "hr/leave/leave_form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Leave";
		$this->data['headData']->controller = "hr/leave";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('leave');
		$this->data['type'] = 1; //leave Request
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows($type = 1,$status=1){
		$postData = $this->input->post();
		$postData['type'] = $type;
		$postData['status'] = $status;
        $result = $this->leave->getDTRows($postData);
        $sendData = array();$i=1;$count=0;
		
		foreach($result['data'] as $row):
			$row->sr_no = $i++;
			$row->type = $type;
			$row->loginId = $this->loginId;
			if($row->status == 1):
				$row->status_label = '<span class="font-13 font-weight-bold badge bg-info">Pending</span><br>'.$row->auth_by;
            elseif($row->status == 2):
                $row->status_label = '<span class="font-13 font-weight-bold badge bg-success">Approved</span><br>'.$row->auth_by;
			elseif($row->status == 3):
				$row->status_label = '<span class="font-13 font-weight-bold badge bg-danger">Rejected</span><br>'.$row->auth_by;
			endif;
			$sendData[] = getLeaveData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addLeave(){
        $this->data['leaveType'] = $this->leaveType;
        $this->data['empList'] = $this->employee->getEmployeeList(['status'=>1]);
        $this->load->view($this->leaveForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();		
		
		$data['emp_id'] = (!empty($data['emp_id']) ? $data['emp_id'] : $this->loginId);
		$data['total_days'] = 0;
		
        $errorMessage = array();

        // if(empty($data['trans_no']))
        //     $errorMessage['trans_no'] = "Sr. No is required.";
        if(empty($data['leave_type']))
            $errorMessage['leave_type'] = "Leave Type is required.";

		if(empty(strtotime($data['start_date']))):
            $errorMessage['start_date'] = "Start Date is required.";
		else:
			$data['start_date'] = date("Y-m-d",strtotime($data['start_date']));
		endif;
		
		if(strtotime($data['start_date']) < strtotime(date('Y-m-d')))
            //$errorMessage['start_date'] = "Invalid Start Date";
		if(empty($data['start_section']))
            $errorMessage['start_section'] = "Start Section is required.";
		
		if(empty(strtotime($data['end_date']))):
            $errorMessage['end_date'] = "End Date is required.";
		else:
			$data['end_date'] = date("Y-m-d",strtotime($data['end_date']));
		endif;
		
		if(empty($data['end_section']))
            $errorMessage['end_section'] = "End Section is required.";
		if(empty($data['leave_reason']))
            $errorMessage['leave_reason'] = "Reason is required.";
		
		if(strtotime($data['start_date']) > strtotime($data['end_date']) || date("m-Y",strtotime($data['start_date'])) != date("m-Y",strtotime($data['end_date'])))
			$errorMessage['end_date'] = "End Date must be greater than or equal to Start Date and within the same month as the Start Date.";
			
		if(!empty(strtotime($data['start_date'])) AND !empty(strtotime($data['end_date'])))
		{
			if(strtotime($data['start_date']) == strtotime($data['end_date']))
			{
				if($data['start_section'] == "F"){$data['total_days'] = 1;}
				if($data['start_section'] == "H"){$data['total_days'] = 0.5;}
				$data['end_section'] = $data['start_section'];
			}
			else
			{
				if($data['start_section'] == "F"){$data['total_days'] += 1;}else{$data['total_days'] += 0.5;}
				if($data['end_section'] == "F"){$data['total_days'] += 1;}else{$data['total_days'] += 0.5;}
				
				$sd = new DateTime($data['start_date']);
				$ed = new DateTime($data['end_date']);
				$diff = $sd->diff($ed);
				
				$data['total_days'] += $diff->days - 1;				
			}
		}
		
		if(empty($data['total_days']))
            $errorMessage['end_date'] = "You have to apply atleast 1 Day Leave";

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
                    
                    $imagePath = realpath(APPPATH . '../assets/uploads/leave/');
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
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['created_by'] = $this->loginId;
            $this->printJson($this->leave->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['leaveType'] = $this->leaveType;
        $this->data['empList'] = $this->employee->getEmployeeList(['status'=>1]);
        $this->data['dataRow'] = $this->leave->getLeaveList(['id'=>$id]);
        $this->load->view($this->leaveForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->leave->delete($id));
        endif;
    }
	
	function printLeave($id){
		$this->data['leaveData'] = $leaveData = $this->leave->getLeaveList(['id'=>$id]);
		$this->data['companyData'] = $companyData = $this->leave->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png');
        $this->data['letter_head'] = $letter_head=  base_url('assets/images/'.$companyData->print_header);
		
 
		$pdfData = $this->load->view('hr/leave/printLeave',$this->data,true);
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';

		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;" rowspan="3"></td>
							<th colspan="2">For, '.$this->data['companyData']->company_name.'</th>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"></td>
							<td style="width:25%;" class="text-center">Authorised By</td>
						</tr>
					</table>';
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='Leave Request.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,40));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('P','','','','',5,5,5,5,3,3,'','','','','','','','','','A5');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

	public function leaveApprove(){
        $this->data['headData']->pageTitle = "Leave Approve";
        $this->data['tableHeader'] = getHrDtHeader('leaveApprove');
		$this->data['type'] = 2; //leave Approve
        $this->load->view('hr/leave/leave_approve_index',$this->data);
    }

	public function addLeaveApprove(){
        $data = $this->input->post();
		$this->data['id'] = $data['id'];
		$this->data['status'] = $data['status'];
        $this->load->view('hr/leave/leave_approve_form',$this->data);
    }

	public function saveApproveLeave(){
        $postData = $this->input->post();
        if(empty($postData['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->leave->saveApproveLeave($postData));
        endif;
    }
}
?>