<?php
class ProjectHistoryModel extends MasterModel{
    private $projectHistory = "project_history";
    
    public function getProjectHistory($param=[]){
        $queryData['tableName'] = $this->projectHistory;
        $queryData['select'] = "project_history.*, project_master.project_name, employee_master.emp_name as user_name";
		$queryData['select'] .= ",(if(project_history.media_file IS NULL, '', CONCAT('https://jnrinfra.nbterp.com/assets/uploads/project_history/',project_history.media_file))) as media_file";
        
		$queryData['leftJoin']['project_master'] = "project_master.id = project_history.project_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = project_history.created_by";
		
		if(isset($param['trans_status'])):
			$queryData['where']['project_history.status'] = $param['trans_status'];
		endif;
		
        if(!empty($param['id'])):
            $queryData['where']['project_history.id'] = $param['id'];
			$param['result_type'] = 'row';
		endif;
		
        if(!empty($param['project_id'])):
            $queryData['where']['project_history.project_id'] = $param['project_id'];
		endif;
		
        if(!empty($param['trans_date'])):
            $queryData['where']['DATE(project_history.created_at)'] = $param['trans_date'];
		endif;
		
        if(!empty($param['from_date']) AND !empty($param['to_date'])):
            $queryData['where']['DATE(project_history.created_at) >= '] = $param['from_date'];
            $queryData['where']['DATE(project_history.created_at) <= '] = $param['to_date'];
		endif;
		
		if(!empty($param['search'])):
            $queryData['like']['project_master.project_name'] = $param['search'];
            $queryData['like']['project_history.message'] = $param['search'];
            $queryData['like']['project_history.trans_date'] = $param['search'];
        endif;
		
		$queryData['order_by']['project_history.created_at'] = 'DESC'; 
		
		if(!empty($param['limit'])): 
			$queryData['limit'] = $param['limit']; 
		endif;
		
		if(isset($param['start']) && isset($param['length'])):
			$queryData['start'] = $param['start'];
			$queryData['length'] = $param['length'];
		endif;
		
		if(!empty($param['result_type'])):
			$result = $this->getData($queryData,$param['result_type']);
		else:
			$result = $this->getData($queryData,'rows');
		endif;
		//$this->printQuery();
		return $result;
    }

    public function sendMessage($data){
        try{
            $this->db->trans_begin();
			
            $result = $this->store($this->projectHistory, $data, 'Project History');

            if($this->db->trans_status() !== FALSE) :
				
				$users = $this->permission->getMenuUsers(['sub_menu_id'=>"90",'project_id'=>$data['project_id']]);
				$projectData = $this->project->getProject(['id'=>$data['project_id']]);
				
				$notifyArr = Array();
				$notifyArr['title'] = "New Message".(!empty($projectData->project_name) ? " | ".$projectData->project_name : "");
				$notifyArr['body'] = $data['message']." From:".$this->userName." @".date('d.m.Y H:i A');
				$notifyArr['appCallBack'] = 'History';
				$notifyArr['project_id'] = $data['project_id'];
				$notifyArr['project_name'] = (!empty($projectData->project_name) ? $projectData->project_name : "");
				$notifyArr['link'] = "";
				$notifyArr['empIds'] = (!empty($users->empIds) ? $users->empIds : "");

				$notify = sendFirebaseNotification($notifyArr);
				
				$this->db->trans_commit();
				return $result;
			endif;
        }catch(\Exception $e){
            return ['status'=>0,'error'=>$e->getMessage()];
        }
    }
    
	public function changeStatus($data){
        try {
			$this->db->trans_begin();
			
			$result = $this->db->query("UPDATE project_history SET status = (3 - status) WHERE id = ".$data['id']);
			
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'messge'=>'Status Updated'];
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }
	
	public function deleteProjectHistory($data){
        try {
			$this->db->trans_begin();
			
			$phData = $this->getProjectHistory($data);

			$result = $this->trash($this->projectHistory, ['id' => $data['id']], 'Project History');
			
			if(!empty($phData->media_file))
			{
				$filePath = realpath(APPPATH . '../assets/uploads/project_history/'.$phData->media_file);
				unlink($filePath); 
			}

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }
	
    public function getProjectMedia($param=[]){
        $queryData['tableName'] = $this->projectHistory;
        $queryData['select'] = "project_history.id, project_history.message";
		$queryData['select'] .= ",(if(project_history.media_file IS NULL, '', CONCAT('https://jnrinfra.nbterp.com/assets/uploads/project_history/',project_history.media_file))) as media_file";
        
		if(!empty($param['upload_by'])):
			$queryData['select'] .= ", employee_master.emp_name as created_by, employee_master.emp_name as created_at";
			$queryData['leftJoin']['employee_master'] = "employee_master.id = project_history.created_by";
		endif;
		
        if(!empty($param['project_id'])):
            $queryData['where']['project_history.project_id'] = $param['project_id'];
		endif;
		
		$queryData['customWhere'][] = 'project_history.media_file IS NOT NULL';
		
        if(!empty($param['trans_date'])):
            $queryData['where']['DATE(project_history.created_at)'] = $param['trans_date'];
		endif;
		
        if(!empty($param['from_date']) AND !empty($param['to_date'])):
            $queryData['where']['DATE(project_history.created_at) >= '] = $param['from_date'];
            $queryData['where']['DATE(project_history.created_at) <= '] = $param['to_date'];
		endif;
		
		if(!empty($param['ids'])):
			$queryData['where_in']['project_history.id'] = str_replace("~", ",", $param['ids']);
		endif;
		
		if(!empty($param['result_type'])):
			$result = $this->getData($queryData,$param['result_type']);
		else:
			$result = $this->getData($queryData,'rows');
		endif;
		//$this->printQuery();
		return $result;
    }

}
?>