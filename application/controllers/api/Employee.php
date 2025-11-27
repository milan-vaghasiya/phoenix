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

	public function updateProfile(){
        $data = $this->input->post();
        $errorMessage = array();

        if($_FILES['emp_profile']['name'] != null || !empty($_FILES['emp_profile']['name'])):
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['emp_profile']['name'];
            $_FILES['userfile']['type']     = $_FILES['emp_profile']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['emp_profile']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['emp_profile']['error'];
            $_FILES['userfile']['size']     = $_FILES['emp_profile']['size'];
            
            $imagePath = realpath(APPPATH . '../assets/uploads/emp_profile/');
            $ext = pathinfo($_FILES['emp_profile']['name'], PATHINFO_EXTENSION);

            $config = ['file_name' => $data['id'].'.'.$ext,'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path' => $imagePath];

            if(file_exists($config['upload_path'].'/'.$config['file_name'])) unlink($config['upload_path'].'/'.$config['file_name']);

            $this->upload->initialize($config);
            if (!$this->upload->do_upload()):
                $errorMessage['emp_profile'] = $this->upload->display_errors();
            else:
                $uploadData = $this->upload->data();
                $data['emp_profile'] = $uploadData['file_name'];
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else: 
            $this->printJson($this->employee->store("employee_master",$data,'Employee'));
        endif;
    }

}
?>