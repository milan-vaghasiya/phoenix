<?php
class NotificationModel extends MasterModel{

    /* Send Notification in Multipal Device */
    public function sendMultipalNotification($data){
        try{
            $token = $data['pushToken']; // array of push tokens
            
            $this->fcm->setTitle($data['notificationTitle']);
            $this->fcm->setMessage($data['notificationMsg']);

            /**
             * set to true if the notificaton is used to invoke a function
             * in the background
             */
            $this->fcm->setIsBackground(true);

            /**
             * payload is userd to send additional data in the notification
             * This is purticularly useful for invoking functions in background
             * -----------------------------------------------------------------
             * set payload as null if no custom data is passing in the notification
             */
            $payload = (!empty($data['payload']))?$data['payload']:array('notification' => "");
            $this->fcm->setPayload($payload);

            /**
             * set callback url for open web page when click on notification
             */
            $callBack = (!empty($data['callBack']))?$data['callBack']:base_url();
            $this->fcm->setCallbackLink($callBack);

            /**
             * Send images in the notification
             */
            $imagePath = base_url('assets/images/favicon.png');
            $this->fcm->setImage($imagePath);

            /**
             * Get the compiled notification data as an array
             */
            $json = $this->fcm->getPush();
            $result = $this->fcm->sendMultiple($token, $json);
            $result = json_decode($result);
            
            if($result->success == 0):
                $errorMsg = (!empty($result->errorMessage))?$result->errorMessage:"";
                return ['status'=>0,'message'=>'Somthing went wrong. Error : '.$errorMsg,'failed_error'=>0,'failed_error_message'=>null,'data'=>$result];
            else:
                return ['status'=>1,'message'=>'Notification send successfully.','failed_error'=>0,'failed_error_message'=>null,'data'=>$result];
            endif;
        }catch(\Exception $e){
            return ['status'=>0,'message'=>'Somthing is wrong. Error : '.$e->getMessage(),'failed_error'=>0,'failed_error_message'=>null];
        }
    }

    /* Send Notification in One Device */
    public function sendNotification($data){
        try{
            $token = $data['pushToken']; // push token
            
            $this->fcm->setTitle($data['notificationTitle']);
            $this->fcm->setMessage($data['notificationMsg']);

            /**
             * set to true if the notificaton is used to invoke a function
             * in the background
             */
            $this->fcm->setIsBackground(true);

            /**
             * payload is userd to send additional data in the notification
             * This is purticularly useful for invoking functions in background
             * -----------------------------------------------------------------
             * set payload as null if no custom data is passing in the notification
             */
            $payload = (!empty($data['payload']))?$data['payload']:array('notification' => "");
            $this->fcm->setPayload($payload);

            /**
             * set callback url for open web page when click on notification
             */
            $callBack = (!empty($data['callBack']))?$data['callBack']:base_url();
            $this->fcm->setCallbackLink($callBack);

            /**
             * Send images in the notification
             */
            $imagePath = base_url('assets/images/favicon.png');
            $this->fcm->setImage($imagePath);
            
            /**
             * Get the compiled notification data as an array
             */
            $json = $this->fcm->getPush();
            $result = $this->fcm->send($token, $json);
            $result = json_decode($result);

            if($result->success == 0):
                $errorMsg = (!empty($result->errorMessage))?$result->errorMessage:"";
                return ['status'=>0,'message'=>'Somthing went wrong. Error : '.$errorMsg,'failed_error'=>0,'failed_error_message'=>null,'data'=>$result];
            else:
                return ['status'=>1,'message'=>'Notification send successfully.','failed_error'=>0,'failed_error_message'=>null,'data'=>$result];
            endif;
        }catch(\Exception $e){
            return ['status'=>0,'message'=>'Somthing is wrong. Error : '.$e->getMessage(),'failed_error'=>0,'failed_error_message'=>null];
        }
    }
}
?>