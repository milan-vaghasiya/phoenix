<?php
class Notification extends MY_Controller{   

    public function index(){
        $this->load->view('notification');
    }

    public function send(){
        $data = $this->input->post();

        $postData['title'] = (!empty($data['notificationTitle']))?$data['notificationTitle']:"Test Notification";
        $postData['body'] = (!empty($data['notificationMsg']))?$data['notificationMsg']:"Notification test successfull.";
        $postData['appCallBack'] = 'notification';
        $postData['link'] = base_url('notification');
        $postData['empIds'] = "1";

        $result = sendFirebaseNotification($postData);
        
        $this->printJson($result);
    }

    public function saveFcmPushToken(){
		// Get the raw POST body
        $json = file_get_contents('php://input');

        // Decode JSON to array
        $data = json_decode($json, true);

        if(!empty($data['token'])):
            $result = $this->masterModel->edit('employee_master', ['id'=>$this->loginId], ['web_push_token'=>$data['token']]); 
            if($result):
                $this->printJson(['status'=>1, 'message'=>'FCM Token saved successfully.']);
            else:
                $this->printJson(['status'=>0, 'message'=>'Failed to save FCM Token.']);
            endif;
        else:
            $this->printJson(['status'=>0, 'message'=>'Token is empty.']);
        endif;
	}
}
?>