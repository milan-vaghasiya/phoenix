<?php
class Employees extends MY_Controller{
    private $indexPage = "hr/employee/index";
    private $employeeForm = "hr/employee/form";
    private $profile = "hr/employee/emp_profile";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Employees";
		$this->data['headData']->controller = "hr/employees";   
        $this->data['headData']->pageUrl = "hr/employees";
	}

    public function index(){        
        $this->data['tableHeader'] = getHrDtHeader('employees');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); $data['status']=$status;
        $result = $this->employee->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
		foreach($result['data'] as $row):
			$row->sr_no = $i++;
			$sendData[] = getEmployeeData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addEmployee(){
        //$this->data['emp_no'] = $this->employee->getNextEmpNo();  
        $this->data['designationList'] = $this->designation->getDesignations();
		$this->data['departmentList'] = $this->department->getDepartmentData();
        $this->data['shiftList'] = $this->shiftModel->getShiftList(); 
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->load->view($this->employeeForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['emp_name']))
            $errorMessage['emp_name'] = "Employee name is required.";
		if(empty($data['emp_department']))
            $errorMessage['emp_department'] = "Designation is required.";
        if(empty($data['emp_designation']))
            $errorMessage['emp_designation'] = "Designation is required.";
        if(empty($data['emp_mobile_no']))
            $errorMessage['emp_mobile_no'] = "Contact No. is required.";
		if(empty($data['emp_joining_date']))
            $errorMessage['emp_joining_date'] = "Joining Date is required.";
		if(empty($data['emp_birthdate']))
            $errorMessage['emp_birthdate'] = "Date Of Birth is required.";

        if(empty($data['id'])):
            $data['emp_password'] = "123456";
			$data['emp_role'] = "2";
        endif;
				
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['super_auth_id'] = (!empty($data['super_auth_id'])?implode(",",$data['super_auth_id']):'');
            $this->printJson($this->employee->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->employee->getEmployee($data);
        $this->data['designationList'] = $this->designation->getDesignations();
		$this->data['departmentList'] = $this->department->getDepartmentData();
        $this->data['shiftList'] = $this->shiftModel->getShiftList(); 
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->load->view($this->employeeForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->delete($id));
        endif;
    }

    public function activeInactive(){
        $postData = $this->input->post();
        if(empty($postData['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->activeInactive($postData));
        endif;
    }
    
    public function changePassword(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['old_password']))
            $errorMessage['old_password'] = "Old Password is required.";
        if(empty($data['new_password']))
            $errorMessage['new_password'] = "New Password is required.";
        if(empty($data['cpassword']))
            $errorMessage['cpassword'] = "Confirm Password is required.";
        if(!empty($data['new_password']) && !empty($data['cpassword'])):
            if($data['new_password'] != $data['cpassword'])
                $errorMessage['cpassword'] = "Confirm Password and New Password is Not match!.";
        endif;

        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['id'] = $this->loginId;
			$result =  $this->employee->changePassword($data);
			$this->printJson($result);
		endif;
    }

    public function resetPassword(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->resetPassword($data['id']));
        endif;
    }

    public function addDocuments(){
        $this->data['emp_id'] = $this->input->post('id');
        $this->load->view('hr/employee/emp_documents',$this->data);
    }

    public function saveDocuments(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['doc_name']))
            $errorMessage['doc_name'] = "Document Name is required.";
        if(empty($data['doc_no']))
            $errorMessage['doc_no'] = "Document No. is required."; 
        if(empty($_FILES['doc_file']['name']) || $_FILES['doc_file']['name'] == null)
            $errorMessage['doc_file'] = "Document File is required."; 
		
		if(!empty($_FILES['doc_file']['name'])):
            $attachment = "";
            $this->load->library('upload');
            
            $_FILES['userfile']['name']     = $_FILES['doc_file']['name'];
            $_FILES['userfile']['type']     = $_FILES['doc_file']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['doc_file']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['doc_file']['error'];
            $_FILES['userfile']['size']     = $_FILES['doc_file']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/employee/documents/');

            $fileName = 'doc_file_'.time();
			$config = ['file_name' => $fileName, 'allowed_types' => '*', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

            $this->upload->initialize($config);

            if(!$this->upload->do_upload()):
                $errorMessage['doc_file'] = $fileName . " => " . $this->upload->display_errors();
            else:
                $uploadData = $this->upload->data();
                $data['doc_file'] = $uploadData['file_name'];
            endif;

            if(!empty($errorMessage['doc_file'])):
				if (file_exists($imagePath . '/' . $fileName)) : unlink($imagePath . '/' . $fileName); endif;
            endif;            
        endif;
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->employee->saveDocuments($data));
        endif;
    }

    public function getEmpDocsHtml(){
        $data = $this->input->post();
        $docData = $this->employee->getEmpDocuments(['emp_id'=>$data['emp_id']]);

		$i=1; $tbody='';
        if(!empty($docData)):
            foreach($docData as $row):
                $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Documents','res_function':'getEmpDocsHtml','fndelete':'deleteDocuments'}";

                $tbody .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td class="text-center">'.$row->doc_name.'</td>
                    <td class="text-center">'.$row->doc_no.'</td>
                    <td class="text-center">'.((!empty($row->doc_file))?'<a href="'.base_url('assets/uploads/documents/'.$row->doc_file).'" target="_blank"><i class="fa fa-download"></i></a>':"") .'</td>
                    <td class="text-center">
                        <button type="button" onclick="trash('.$deleteParam.');" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
                    </td>
                </tr>';
            endforeach;
        else:
            $tbody = '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
        endif;

        $this->printJson(['status'=>1,'tbodyData'=>$tbody]);
	}

    public function deleteDocuments(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->deleteDocuments($id));
        endif;
    }

    public function createLedger(){
        $data = $this->input->post();
        if(empty($data)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->createLedger($data));
        endif;
    }

	public function addEmpSalary(){
        $this->data['emp_id'] = $this->input->post('id');
        $this->data['empData'] = $this->employee->getEmployee(['id'=>$this->data['emp_id']]);
        $this->load->view('hr/employee/emp_salary',$this->data);
    }

    public function saveEmpSalary(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['sal_amt']) OR $data['sal_amt'] <= 0):
            $errorMessage['sal_amt'] = "Salary Amount is required.";
        endif;

        if(empty($data['day_hours']) OR $data['day_hours'] <= 0):
            $errorMessage['day_hours'] = "Days Hours is required.";
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->employee->saveEmpSalary($data));
        endif;
    }
}
?>