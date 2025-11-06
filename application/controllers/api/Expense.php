<?php
class Expense extends MY_ApiController{

	public function __construct(){
		parent::__construct();
        $this->data['headData']->pageTitle = "Expense";
        $this->data['headData']->pageUrl = "api/expense";
        $this->data['headData']->base_url = base_url();
	}
	
	
    public function addExpense(){
        $this->data['expList'] = $this->party->getPartyList(['party_category'=>5,'group_code'=>'"ED","EI"']);
        $this->data['empList'] = $this->employee->getEmployeeList();        
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }

    public function getExpense(){		
		$data = $this->input->post();
        $this->data['expenseDetail'] = $this->expense->getExpense($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['expenseDetail']]);
    }

    public function getExpenseList(){		
		$data = $this->input->post();
        $this->data['expenseList'] = $this->expense->getExpenseList($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['expenseList']]);
    }

    public function save(){
        $data = $this->input->post();
        $data['exp_prefix'] = 'EXP';
		$data['exp_no'] = $this->expense->getNextExpNo();
        $data['exp_number'] = $data['exp_prefix'].$data['exp_no'];
		
        $errorMessage = array();

        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required.";
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

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->expense->delete($id));
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
            $this->printJson($this->expense->saveApproveExpense($data));
        endif;
    }

	
}
?>