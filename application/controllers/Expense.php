<?php
class Expense extends MY_Controller{
    private $indexPage = "expense/index";
    private $formPage = "expense/form";
    private $approveForm = "expense/approve_form";
    private $rejectForm = "expense/reject_form";
   
    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Expense";
		$this->data['headData']->controller = "expense";
		$this->data['headData']->pageUrl = "expense";
	}

    public function index(){
        $this->data['tableHeader'] = getMasterDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
		$data=$this->input->post();
        $data['status'] = $status;
		$result = $this->expense->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getExpenseData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addExpense(){
        $this->data['exp_prefix'] = 'EXP';
		$this->data['exp_no'] = $this->expense->getNextExpNo();
        $this->data['exp_number'] = $this->data['exp_prefix'].$this->data['exp_no'];
        $this->data['projectList'] = $this->project->getProjectList(['is_active'=>0]);
        $this->data['expList'] = $this->party->getPartyList(['party_category'=>5,'group_code'=>'"ED","EI"']);
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['exp_by_id']))
            $errorMessage['exp_by_id'] = "Emp Name is required.";
        if(empty($data['exp_ledger_id']))
            $errorMessage['exp_ledger_id'] = "Exp Type is required.";
        if(empty($data['demand_amount']))
            $errorMessage['demand_amount'] = "Amount is required.";

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
                    
                    $imagePath = realpath(APPPATH . '../assets/uploads/expense/');
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
            $this->printJson($this->expense->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->expense->getExpense($data);
        $this->data['expList'] = $this->party->getPartyList(['party_category'=>5,'group_code'=>'"ED","EI"']);
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->data['projectList'] = $this->project->getProjectList(['is_active'=>0]);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->expense->delete($id));
        endif;
    }

    public function getApproveExpense(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->expense->getExpense($data);
        $this->load->view($this->approveForm,$this->data);
    }

    public function saveApprovedData(){
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
            $this->printJson($this->expense->saveApproveExpense($data));
        endif;
    }

    public function getRejectExpense(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->expense->getExpense($data);
        $this->load->view($this->rejectForm,$this->data);
    }

}
?>