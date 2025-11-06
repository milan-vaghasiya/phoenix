<?php
class ProjectHistory extends MY_ApiController{

	public function __construct(){
		parent::__construct();
        $this->data['headData']->pageTitle = "Project History";
        $this->data['headData']->pageUrl = "api/projectHistory";
        $this->data['headData']->base_url = base_url();
	}
	
	public function getProjectHistory(){
        $data = $this->input->post();
        $this->data['phList'] = $this->projectHistory->getProjectHistory($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['phList']]);
    }
	
    public function sendMessage(){
        $data = $this->input->post();
        $errorMessage = [];
		
        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required.";
		
		
		if(!empty($_FILES['media_file'])):
            if($_FILES['media_file']['name'] != null || !empty($_FILES['media_file']['name'])):
                $this->load->library('upload');
                $_FILES['userfile']['name']     = $_FILES['media_file']['name'];
                $_FILES['userfile']['type']     = $_FILES['media_file']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['media_file']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['media_file']['error'];
                $_FILES['userfile']['size']     = $_FILES['media_file']['size'];
                
                $imagePath = realpath(APPPATH . '../assets/uploads/project_history/');
                $config = ['file_name' => time()."_".$data['project_id'],'allowed_types' => '*','max_size' => 25600,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

                $this->upload->initialize($config);
                if (!$this->upload->do_upload()):
                    $errorMessage['media_file'] = $this->upload->display_errors();
                else:
                    $uploadData = $this->upload->data();
                    $data['media_file'] = $uploadData['file_name'];
                endif;
            endif;
        endif;
		
		/*
		if(!empty($_FILES['media_file']['name'][0])):
            foreach ($_FILES['media_file']['name'] as $key => $value):
                if($value != null || !empty($value)):
                    $this->load->library('upload');
                    $_FILES['userfile']['name']     = $value;
                    $_FILES['userfile']['type']     = $_FILES['media_file']['type'][$key];
                    $_FILES['userfile']['tmp_name'] = $_FILES['media_file']['tmp_name'][$key];
                    $_FILES['userfile']['error']    = $_FILES['media_file']['error'][$key];
                    $_FILES['userfile']['size']     = $_FILES['media_file']['size'][$key];
                    
					$imagePath = realpath(APPPATH . '../assets/uploads/project_history/');
					$config = ['file_name' => time()."_".$data['project_id'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];
    
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload()):
                        $errorMessage['visiting_card'] = $this->upload->display_errors();
                    else:
                        $uploadData = $this->upload->data();
                        $data['media_file'][] = $uploadData['file_name'];
                    endif;
                endif;
            endforeach;
            $data['media_file'] = implode(',', $data['media_file']);
        endif;
		*/
        
        if(empty($data['message']) AND empty($data['media_file']))
            $errorMessage['message'] = "Message is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'error'=>$errorMessage]);
        else:
			if(empty($data['media_file'])){unset($data['media_file']);}
			$this->printJson($this->projectHistory->sendMessage($data));
        endif;
    }

    public function changeStatus(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
			$this->printJson($this->projectHistory->changeStatus($data));
        endif;
    }

    public function deleteMessage(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            if(empty($data['status'])):
				$this->printJson($this->projectHistory->deleteProjectHistory($data));
			else:
				$this->printJson($this->projectHistory->changeStatus($data));
			endif;
        endif;
    }

}
?>