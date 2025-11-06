<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

use Google\Client;
use GuzzleHttp\Client as GuzzleClient;

function sendFirebaseNotification($postData) {
    try {
        $CI =& get_instance();

        // Path to your service account JSON file
        $serviceAccount = FCPATH . 'nativebit-175ba-335258e8f014.json';

        // Load Google Client
        require_once APPPATH . '../vendor/autoload.php';
        $client = new Client();
        $client->setAuthConfig($serviceAccount);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        // Get Access Token
        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];
        //print_r($accessToken);exit;

        // FCM URL (change PROJECT_ID to yours)
        $projectId = 'nativebit-175ba'; // From Firebase Project Settings
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $tokens = getNotificationTokens($postData);

        $messages = [];
        if(!empty($tokens)):
            // Prepare message payload
            foreach($tokens as $token):
                $messages[] = [
                    "message" => [
                        "token" => $token,
                        "notification" => [
                            "title" => $postData['title'],
                            "body" => $postData['body']
                        ],
                        "data" => [
                            "icon"  => base_url('assets/images/favicon.png'),
                            "click_action" => $postData['appCallBack'] ?? '',
                            "project_id" => (!empty($postData['project_id']) ? $postData['project_id'] : ""),
                            "project_name" => (!empty($postData['project_name']) ? $postData['project_name'] : ""),
                            "link" => $postData['link']
                        ]
                    ]
                ];
            endforeach;

            // Send each notification
            $http = new GuzzleClient();
            $results = [];
            foreach ($messages as $message) :
                $response = $http->post($url, [
                    'headers' => [
                        'Authorization' => "Bearer {$accessToken}",
                        'Content-Type' => 'application/json'
                    ],
                    'json' => $message
                ]);
				
                $results[] = json_decode($response->getBody(), true);
            endforeach;
			
        else:
            $results = ['status' => 0,'error' => 'No valid tokens found'];
        endif;

        // Log the request & response
        $logData = [
            'log_date' => date("Y-m-d H:i:s"),
            'notification_title' => $postData['title'],
            'notification_data' => json_encode($messages),
            'notification_response' => json_encode($results),
            'created_by' => $CI->loginId ?? 0,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_by' => $CI->loginId ?? 0,
            'updated_at' => date("Y-m-d H:i:s")
        ];
        $CI->db->insert('notification_log', $logData);
		
        return ['status' => 1, 'fcm_response' => $results];
    } catch (Exception $e) {
        return ['status' => 0, 'error' => 'Error occurred: '.$e->getMessage()];
    }
}

function getNotificationTokens($postData = []) {
    $CI =& get_instance();

    $tokenList = [];
    if(!empty($postData['empIds'])):
        // Fetch app and web push tokens from the database
        $CI->db->select('app_push_token, web_push_token');
        $CI->db->from('employee_master');
        $CI->db->where('is_delete', 0);
        $CI->db->where('is_active', 1);
        $CI->db->where_in('id', $postData['empIds'], false);
        $tokens = $CI->db->get()->result();

        $tokenList = [];
        foreach($tokens as $row):
            if(!empty($row->app_push_token) && !empty($postData['appCallBack'])):
                $tokenList[] = $row->app_push_token;
            endif;
            if(!empty($row->web_push_token) && !empty($postData['link'])):
                $tokenList[] = $row->web_push_token;
            endif;
        endforeach;

        $tokenList = array_unique($tokenList);
    endif;

    return $tokenList;
}
?>
