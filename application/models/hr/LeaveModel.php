<?php
class LeaveModel extends MasterModel{
    private $leaveMaster = "leave_master";
	private $leaveType = "leave_type";
	private $leaveAuthority = "leave_authority";
    private $empMaster = "employee_master";
	
    public function getNextSrNo(){
        $queryData['tableName'] = $this->leaveMaster;
        $queryData['select'] = "trans_no";
        $queryData['order_by']['leave_master.id'] = "DESC";        
        $queryData['limit'] = 1;
        $transData = $this->specificRow($queryData);
        $trans_no = (empty($transData->trans_no) OR  $transData->trans_no == 9999) ? 1 : ($transData->trans_no + 1);
        return $trans_no;
    }

	public function getDTRows($data){
        $data['tableName'] = $this->leaveMaster;
        $data['select'] = "leave_master.id, leave_master.trans_no, leave_master.leave_type, leave_master.start_date, leave_master.start_section, leave_master.end_date, leave_master.end_section, leave_master.total_days, leave_master.leave_reason, leave_master.status, emp.emp_name, emp.emp_code, emp_designation.title as dsg_title, leave_master.auth_notes, apr.emp_name as auth_by,emp.super_auth_id, IFNULL(pm.project_name,'') as project_name";
		$data['select'] .= ",(if(leave_master.proof_file IS NULL, '', CONCAT('https://phoenix.nativebittechnologies.in/assets/uploads/leave/',leave_master.proof_file))) as proof_file";
        $data['leftJoin']['employee_master emp'] = "emp.id = leave_master.emp_id";
        $data['leftJoin']['emp_designation'] = "emp.emp_designation = emp_designation.id";
		$data['leftJoin']['employee_master apr'] = "apr.id = leave_master.auth_by";
		$data['leftJoin']['project_master pm'] = "pm.id = leave_master.project_id";
		
		if(!empty($data['status'])):
			$data['where']['leave_master.status'] = $data['status'];
		endif;
				
        if(!empty($data['id'])):
            $data['where']['leave_master.id'] = $data['id'];
			$data['result_type'] = 'row';
		endif;
		
        if(!empty($data['emp_id'])):
            $data['where']['leave_master.emp_id'] = $data['emp_id'];
		endif;
		
        if(!empty($data['auth_by'])):
            $data['where']['leave_master.auth_by'] = $data['auth_by'];
		endif;
		
        if(!empty($data['leave_date'])):
            $data['where']['DATE(leave_master.start_date) <= '] = $data['leave_date'];
            $data['where']['DATE(leave_master.end_date) >= '] = $data['leave_date'];
		endif;
		
        if(!empty($data['from_date']) AND !empty($data['to_date'])):
            $data['where']['DATE(leave_master.start_date) >= '] = $data['from_date'];
            $data['where']['DATE(leave_master.end_date) <= '] = $data['to_date'];
		endif;
		
		
		if(!empty($data['status']) AND $data['status'] == 1):
			$data['order_by']['leave_master.start_date'] = 'ASC';
		else:
			$data['order_by']['leave_master.start_date'] = 'DESC';
		endif;

        if(!empty($data['type']) AND $data['type'] == 1){

            $data['where']['leave_master.emp_id'] = $this->loginId;
        }else{
    		$data['customWhere'][] = 'FIND_IN_SET('.$this->loginId.',emp.super_auth_id) > 0';
        }

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "employee_master.emp_code";
        $data['searchCol'][] = "leave_master.leave_type";
        $data['searchCol'][] = "leave_master.start_date";
        $data['searchCol'][] = "leave_master.end_date";
        $data['searchCol'][] = "leave_master.total_days";
        $data['searchCol'][] = "leave_master.leave_reason";
        $data['searchCol'][] = "";
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
		
	public function getLeaveList($param=[]){
        $queryData['tableName'] = $this->leaveMaster;
        $queryData['select'] = "leave_master.id, leave_master.trans_no, leave_master.leave_type, leave_master.start_date, leave_master.start_section, leave_master.end_date, leave_master.end_section, leave_master.total_days, leave_master.leave_reason, leave_master.status, emp.emp_name, emp.emp_code, emp.id as emp_id, emp_designation.title as dsg_title, leave_master.auth_notes, IFNULL(leave_master.auth_at,'') as auth_at, emp.super_auth_id, IFNULL(apr.emp_name,'') as auth_by, IFNULL(pm.project_name,'') as project_name";
		$queryData['select'] .= ",(if(leave_master.proof_file IS NULL, '', CONCAT('https://phoenix.nativebittechnologies.in/assets/uploads/leave/',leave_master.proof_file))) as proof_file";
        $queryData['select'] .= ", (CASE WHEN leave_master.status = 1 THEN 'Pending' WHEN leave_master.status = 2 THEN 'Approved' ELSE 'Rejected' END) as status_label";
        
		$queryData['leftJoin']['employee_master emp'] = "emp.id = leave_master.emp_id";
        $queryData['leftJoin']['emp_designation'] = "emp.emp_designation = emp_designation.id";
		$queryData['leftJoin']['employee_master apr'] = "apr.id = leave_master.auth_by";
		$queryData['leftJoin']['project_master pm'] = "pm.id = leave_master.project_id";
		
		$queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", emp.super_auth_id) > 0 OR leave_master.emp_id = '.$this->loginId.')';
		
		if(!empty($param['status'])):
			$queryData['where']['leave_master.status'] = $param['status'];
		endif;
				
        if(!empty($param['id'])):
            $queryData['where']['leave_master.id'] = $param['id'];
			$param['result_type'] = 'row';
		endif;
		
        if(!empty($param['emp_id'])):
            $queryData['where']['leave_master.emp_id'] = $param['emp_id'];
		endif;
		
        if(!empty($param['auth_by'])):
            $queryData['where']['leave_master.auth_by'] = $param['auth_by'];
		endif;
		
        if(!empty($param['from_date']) AND !empty($param['to_date'])):
            $queryData['where']['DATE(leave_master.start_date) >= '] = $param['from_date'];
            $queryData['where']['DATE(leave_master.end_date) <= '] = $param['to_date'];
		endif;
		
		if(!empty($param['search'])):
            $queryData['like']['emp.emp_name'] = $param['search'];
            $queryData['like']['leave_master.leave_reason'] = $param['search'];
            $queryData['like']['leave_master.start_date'] = $param['search'];
            $queryData['like']['leave_master.end_date'] = $param['search'];
        endif;
		
		if(!empty($param['limit'])): 
			$queryData['limit'] = $param['limit']; 
		endif;
		
		if(isset($param['start']) && isset($param['length'])):
			$queryData['start'] = $param['start'];
			$queryData['length'] = $param['length'];
		endif;
		
		if(!empty($param['status']) AND $param['status'] == 1):
			$queryData['order_by']['leave_master.start_date'] = 'ASC';
		else:
			$queryData['order_by']['leave_master.start_date'] = 'DESC';
		endif;
		
		if(!empty($param['result_type'])):
			$result = $this->getData($queryData,$param['result_type']);
		else:
			$result = $this->getData($queryData,'rows');
		endif;
		
		//$this->printQuery();
		return $result;
    }

	public function getLeaveCount($param=[]){
        $queryData['tableName'] = $this->leaveMaster;
        $queryData['select'] = "IFNULL(SUM(total_days),'0') as leave_count";
		
		if(!empty($param['status'])):
			$queryData['where']['leave_master.status'] = $param['status'];
		endif;
		
        if(!empty($param['emp_id'])):
            $queryData['where']['leave_master.emp_id'] = $param['emp_id'];
		endif;
		
        if(!empty($param['auth_by'])):
            $queryData['where']['leave_master.auth_by'] = $param['auth_by'];
		endif;
		
        if(!empty($param['from_date']) AND !empty($param['to_date'])):
            $queryData['where']['DATE(leave_master.start_date) >= '] = $param['from_date'];
            $queryData['where']['DATE(leave_master.end_date) <= '] = $param['to_date'];
		endif;
		
		$result = $this->getData($queryData,'row');
		
		//$this->printQuery();
		return $result;
    }
		
	public function checkLeaveDate($param=[]){
        $queryData['tableName'] = $this->leaveMaster;
        //$queryData['select'] = "IFNULL(SUM(total_days),'0') as leave_count";
        $queryData['select'] = 'leave_amt, IFNULL(SUM(total_days), "0") AS leave_count, 
                                IFNULL(SUM(CASE WHEN leave_amt = "PAID" THEN 1 ELSE 0 END), "0") AS totalPaidLeave, 
                                IFNULL(SUM(CASE WHEN leave_amt = "UNPAID" THEN 1 ELSE 0 END),  "0") AS totalUnpaidLeave';
		
		if(!empty($param['status'])):
			$queryData['where']['leave_master.status'] = $param['status'];
		endif;
		
        if(!empty($param['emp_id'])):
            $queryData['where']['leave_master.emp_id'] = $param['emp_id'];
		endif;
		
        if(!empty($param['auth_by'])):
            $queryData['where']['leave_master.auth_by'] = $param['auth_by'];
		endif;
		
        if(!empty($param['from_date']) AND !empty($param['to_date'])):
            $queryData['where']['DATE(leave_master.start_date) <= '] = $param['to_date'];
            $queryData['where']['DATE(leave_master.end_date) >= '] = $param['from_date'];
		endif;
		
		$result = $this->getData($queryData,'row');
		
		//$this->printQuery();
		return $result;
    }
	
    public function save($data){
        try{
            $this->db->trans_begin();
			
			if(empty($data['id'])){ $data['trans_no'] = $this->getNextSrNo(); }
            
            $result =$this->store($this->leaveMaster,$data,'Leave');
            
    		if($this->db->trans_status() !== FALSE):
				
				$users = $this->employee->getEmployee(['id'=>$data['emp_id']]);
				
				$body = $data['total_days'].' Days | '.formatDate($data['start_date'],'d.m.Y').'('.$data['start_section'].')'.' - '.formatDate($data['end_date'],'d.m.Y').'('.$data['end_section'].')';
				
				$notifyArr = Array();
				$notifyArr['title'] = $data['leave_type']." Request By ".$users->emp_name;
				$notifyArr['body'] = $body.$data['leave_reason'];
				$notifyArr['appCallBack'] = 'Leave';
				$notifyArr['project_id'] = 0;
				$notifyArr['project_name'] = "";
				$notifyArr['link'] = "";
				$notifyArr['empIds'] = (!empty($users->super_auth_id) ? $users->super_auth_id : "");
				
				$notify = sendFirebaseNotification($notifyArr);
				
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
		return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $result = Array();
			
			$leaveData = $this->getLeaveList(['id'=>$id]);
			
            $result = $this->trash($this->leaveMaster,['id'=>$id],'Leave');            
					
			if(!empty($leaveData->proof_file))
			{
				$filePath = realpath(APPPATH . '../assets/uploads/leave/'.$leaveData->proof_file);
				unlink($filePath);
			}
			
    		if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
		return $result;
    }	

	public function saveApproveLeave($data) {
        try{
            $this->db->trans_begin();
            if($data['status'] == 2){
                $leaveAmt = $data['leave_amt'];
                $data['msg'] ="Approve";
            }else{
                $leaveAmt = NULL;
            }
            $this->store($this->leaveMaster, ['id'=> $data['id'],'status'=>$data['status'], 'auth_by' => $this->loginId, 'auth_at'=> date('Y-m-d') ,'leave_amt'=>$leaveAmt]);

            $result = ['status' => 1, 'message' => 'Leave ' . $data['msg'] . ' Successfully.'];

            if ($this->db->trans_status() !== FALSE):
				/*$users = $this->permission->getMenuUsers(['sub_menu_id'=>"141"]);
				$approvedData = $this->employee->getEmployee(['id'=>$this->loginId);
				
				$notifyArr = Array();
				$notifyArr['title'] = "Leave Approved";
				$notifyArr['body'] = "Approved By ".$approvedData->emp_name;
				$notifyArr['appCallBack'] = 'Leave';
				$notifyArr['project_id'] = 0;
				$notifyArr['project_name'] = "";
				$notifyArr['link'] = "";
				$notifyArr['empIds'] = (!empty($users->empIds) ? $users->empIds : "");

				$notify = sendFirebaseNotification($notifyArr);*/
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
	
}
?>