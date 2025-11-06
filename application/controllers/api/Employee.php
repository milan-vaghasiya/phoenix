<?php
class Employee extends MY_ApiController{
    public function __construct(){
        parent::__construct();        
        $this->data['headData']->pageTitle = "Employee";
        $this->data['headData']->pageUrl = "api/employee";
        $this->data['headData']->base_url = base_url();
    }

    public function getEmployeeDetail(){
        $this->data['empData'] = $this->employee->getEmployeeDetail(['id'=>$this->loginId]);
        $this->data['empData']->emp_docs = $this->employee->getEmpDocuments(['emp_id'=>$this->loginId]);
        $this->printJson(['status'=>1,'message'=>'Data Found.','data'=>$this->data['empData']]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['emp_name']))
            $errorMessage['emp_name'] = "Employee name is required.";
             
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['emp_name'] = ucwords($data['emp_name']);      
            $this->printJson($this->employee->save($data));
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

}
?>