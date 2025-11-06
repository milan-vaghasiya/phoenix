<?php
class Dashboard extends MY_ApiController{

	public function __construct(){
		parent::__construct();
        $this->data['headData']->pageTitle = "Dashboard";
        $this->data['headData']->pageUrl = "api/dashboard";
        $this->data['headData']->base_url = base_url();
	}

    public function index(){
		$postData = $this->input->post();
		
		if(empty($postData['version_code'])){
			$this->load->model('LoginModel','loginModel');
			$headData = json_decode(base64_decode($this->input->get_request_header('sign')));
			$this->loginModel->appLogout($headData->loginId);
			$this->printJson(['status'=>1,'message'=>"Logout successfull."]);
			echo 'Sorry...!Your App is Deprecated';
		}else{
			
			
			$this->data['projectList'] = $this->project->getProjectList(['is_active'=>0]);
			$this->printJson(['status'=>1,'message'=>'Data Found.','data'=>$this->data]);
		}
    }
	
    public function getUserPermission(){
        $this->data['userPermission'] = $this->permission->getEmployeeAppMenuList();
		
        $this->printJson(['status'=>1,'message'=>"Data Found",'data'=>$this->data]);
    }

    public function setFCMToken(){
        $data = $this->input->post();
		
        $errorMessage = array();
        
        if(empty($data['app_push_token']))
            $errorMessage['app_push_token'] = "Token is required.";
            
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$data['id'] = $this->loginId;
            $this->printJson($this->dashboard->setFCMToken($data));
        endif;
    }


}
?>