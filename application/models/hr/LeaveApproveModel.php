<?php
class LeaveApproveModel extends MasterModel{
    private $leaveMaster = "leave_master";
	private $leaveType = "leave_type";
    private $empMaster = "employee_master";
	
	public function getDTRows($data){
		$data['tableName'] = $this->leaveMaster;
		$data['select'] = "leave_master.*,employee_master.emp_name,employee_master.fla_id, employee_master.emp_code, emp_designation.dsg_title,leave_type.leave_type";
        $data['join']['employee_master'] = "employee_master.id = leave_master.emp_id";
        $data['join']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
		$data['leftJoin']['leave_type'] = "leave_type.id = leave_master.leave_type_id";
        if(!in_array($this->userRole,[-1,1])){
			$data['customWhere'][] = 'FIND_IN_SET('.$this->empId.',employee_master.fla_id) > 0';
		}
		$data['where']['leave_master.approve_status'] = $data['status'];
		$data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "employee_master.emp_code";
        $data['searchCol'][] = "leave_type.leave_type";
        $data['searchCol'][] = "leave_master.start_date";
        $data['searchCol'][] = "leave_master.end_date";
        $data['searchCol'][] = "leave_master.total_days";
        $data['searchCol'][] = "leave_master.leave_reason";
        $data['searchCol'][] = "";
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        $result = $this->pagingRows($data);
        return $result;
    }

    public function getLeaveType(){
        $data['tableName'] = $this->leaveType;
        $leaveType = $this->rows($data);
		return $leaveType;
    }
	
    public function getLeave($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->leaveMaster;
        return $this->row($data);
    }

	public function save($data){
		try{
            $this->db->trans_begin();
            $result = Array();
            $result = $this->store($this->leaveMaster,$data,'Leave');
            
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

}
?>