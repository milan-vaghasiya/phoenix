<?php
class EmployeeModel extends MasterModel{
    private $empMaster = "employee_master";

	
    public function getNextEmpNo(){
        $queryData['tableName'] = $this->empMaster;
        $queryData['select'] = "ifnull((MAX(CAST(SUBSTRING_INDEX(emp_code, 'JNR', -1) AS UNSIGNED)) + 1),1) AS max_code";
		$queryData['customWhere'][]= "employee_master.emp_code LIKE 'JNR%'";
        $empData = $this->row($queryData);
		if(!empty($empData->max_code)){return $empData->max_code;}
		else{return 1;}
    }

	public function getDTRows($data){
        $data['tableName'] = $this->empMaster;
        $data['select'] = "employee_master.*,emp_designation.title as emp_designation,shift_master.shift_name";
        $data['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        $data['leftJoin']['shift_master'] = "employee_master.shift_id = shift_master.id";
		
        if(!empty($data['emp_role'])):
            $data['where']['employee_master.emp_role'] = $data['emp_role'];
        else:
            $data['where_not_in']['employee_master.emp_role'] = [-1];
        endif;

		if($data['status'] == 0):
            $data['where']['employee_master.is_active']=1;
        else:
            $data['where']['employee_master.is_active']=0;
        endif;
		
		$data['order_by']['employee_master.emp_code']='ASC';
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "employee_master.emp_code";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "employee_master.emp_mobile_no";
        $data['searchCol'][] = "emp_designation.title";
        $data['searchCol'][] = "shift_master.shift_name"; 
        $data['searchCol'][] = "employee_master.emp_joining_date";
        $data['searchCol'][] = "employee_master.emp_birthdate";
        
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }
    
	public function getEmployeeList($data=array()){
        $queryData['tableName'] = $this->empMaster;
        $queryData['select'] = "employee_master.*,emp_designation.title as emp_designation, shift_master.shift_name, shift_master.late_in, shift_master.late_fine, shift_master.shift_start";
        $queryData['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        $queryData['leftJoin']['shift_master'] = "employee_master.shift_id = shift_master.id";

        if(empty($data['appReport'])):
            $empRole = [-1];
        endif;

        if(!empty($data['not_role'])):
            $empRole = $data['not_role'];
        endif;

        if(!empty($data['emp_id'])):
            $queryData['where_in']['employee_master.id'] = $data['emp_id'];
        endif;

        if(!empty($data['emp_role'])):
            $queryData['where_in']['employee_master.emp_role'] = $data['emp_role'];
        endif;

        if(!empty($data['is_active'])):
            $queryData['where_in']['employee_master.is_active'] = $data['is_active'];
        endif;
		
		if(!empty($data['attendance_status'])):
            $queryData['where']['employee_master.attendance_status'] = $data['attendance_status'];
        endif;

        if(empty($data['all']) && empty($data['employee_master.emp_role'])):
            $queryData['where_not_in']['employee_master.emp_role'] = $empRole;
        endif;

        $result = $this->rows($queryData);
		return $result;
    }

	public function getEmpListForSelect($data=array()){
        $queryData['tableName'] = $this->empMaster;
        $queryData['select'] = "employee_master.id, employee_master.emp_code, employee_master.emp_name, employee_master.emp_mobile_no, emp_designation.title as emp_designation, employee_master.party_id";
        $queryData['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";

        if(empty($data['appReport'])):
            $empRole = [-1];
        endif;

        if(!empty($data['not_role'])):
            $empRole = $data['not_role'];
        endif;

        if(!empty($data['emp_id'])):
            $queryData['where_in']['employee_master.id'] = $data['emp_id'];
        endif;

        if(!empty($data['emp_role'])):
            $queryData['where_in']['employee_master.emp_role'] = $data['emp_role'];
        endif;

        $queryData['where']['employee_master.is_active'] = 1;

        if(empty($data['all']) && empty($data['employee_master.emp_role'])):
            $queryData['where_not_in']['employee_master.emp_role'] = $empRole;
        endif;

        $result = $this->rows($queryData);
		return $result;
    }

    public function getEmployee($data){
        $queryData['tableName'] = $this->empMaster;
        $queryData['select'] = "employee_master.*";
        $queryData['where']['employee_master.id'] = $data['id'];
        return $this->row($queryData);
    }
	
    public function getEmployeeDetail($data){
        $queryData['tableName'] = $this->empMaster;
        $queryData['select'] = "employee_master.id, employee_master.emp_role, employee_master.emp_code, employee_master.emp_name, employee_master.nominee_name, employee_master.emp_mobile_no, employee_master.emp_designation, employee_master.emp_birthdate, employee_master.emp_joining_date, employee_master.emp_height, employee_master.emp_weight, employee_master.emp_blood_group, employee_master.emp_gender,emp_designation.title as emp_designation, shift_master.shift_name, shift_master.late_in, shift_master.late_fine, shift_master.shift_start";
		$queryData['select'] .= ",(if(employee_master.emp_profile IS NULL, 'https://jnrinfra.nbterp.com/assets/uploads/emp_profile/user_default.png', CONCAT('https://jnrinfra.nbterp.com/assets/uploads/emp_profile/',employee_master.emp_profile))) as emp_profile";
		
        $queryData['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        $queryData['leftJoin']['shift_master'] = "employee_master.shift_id = shift_master.id";
		
        $queryData['where']['employee_master.id'] = $data['id'];
        $result = $this->row($queryData);
		return $result;
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['emp_contact'] = "Contact no. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            if(empty($data['id'])):
                $data['emp_psc'] = $data['emp_password'];
                $data['emp_password'] = md5($data['emp_password']);
				$nextEmpNo = $this->getNextEmpNo();
				$data['emp_code'] = 'JNR'.lpad($nextEmpNo,3,'0');
            endif;

            $result =  $this->store($this->empMaster,$data,'Employee');
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }        
    }

    public function checkDuplicate($data){
        $queryData['tableName'] = $this->empMaster;
        $queryData['where']['emp_mobile_no'] = $data['emp_mobile_no'];
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $checkData['columnName'] = ['created_by','updated_by','emp_id'];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The employee is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->empMaster,['id'=>$id],'Employee');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function activeInactive($postData){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->empMaster,$postData,'');
            $result['message'] = "Employee ".(($postData['is_active'] == 1)?"Activated":"De-activated")." successfully.";
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function changePassword($data){
        try{
            $this->db->trans_begin();

            if(empty($data['id'])):
                return ['status'=>2,'message'=>'Somthing went wrong...Please try again.'];
            endif;

            $empData = $this->getEmployee(['id'=>$data['id']]);
            if(md5($data['old_password']) != $empData->emp_password):
                return ['status'=>0,'message'=>['old_password'=>"Old password not match."]];
            endif;

            if(md5($data['new_password']) == $empData->emp_password):
                return ['status'=>0,'message'=>['new_password'=>"The new password cannot be the same as the old password. Please choose a different password."]];
            endif;

            $postData = ['id'=>$data['id'],'emp_password'=>md5($data['new_password']),'emp_psc'=>$data['new_password']];
            $result = $this->store($this->empMaster,$postData);
            $result['message'] = "Password changed successfully.";

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function resetPassword($id){
        try{
            $this->db->trans_begin();

            $data['id'] = $id;
            $data['emp_psc'] = '123456';
            $data['emp_password'] = md5($data['emp_psc']); 
            
            $result = $this->store($this->empMaster,$data);
            $result['message'] = 'Password Reset successfully.';

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}

	public function saveDocuments($data){
        try{
            $this->db->trans_begin();

            $result =  $this->store('emp_docs',$data,'Employee Documents');
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }        
    }

    public function deleteDocuments($id){
        try{
            $this->db->trans_begin();

            $result = $this->trash('emp_docs',['id'=>$id],'Employee Documents');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getEmpDocuments($param=[]){
        $queryData['tableName'] = "emp_docs";
        $queryData['select'] = "emp_docs.id, emp_docs.emp_id, emp_docs.doc_name, emp_docs.doc_no, emp_docs.doc_type";
		$queryData['select'] .= ",(if(emp_docs.doc_file IS NULL, '', CONCAT('https://jnrinfra.nbterp.com/assets/uploads/employee/documents/',emp_docs.doc_file))) as doc_file";

        if (!empty($param['emp_id'])) { $queryData['where']['emp_docs.emp_id'] = $param['emp_id']; }

        return $this->rows($queryData);
    }

    public function createLedger($data) { 
        try{
            $this->db->trans_begin();

            $empData = $this->getEmployee(['id'=>$data['id']]);
            $partyData = [
                'id'=> '',
                'emp_id' => $empData->id,
                'party_category' => 5,  
                'party_code' => $empData->emp_code,
                'party_name' => $empData->emp_name,
                'party_phone' => $empData->emp_mobile_no,
                'group_code' =>' EMP'
            ];
            $result = $this->store('party_master',$partyData);

            if(!empty($result['id'])){
                $this->edit($this->empMaster, ['id'=>$data['id']], ['party_id'=>$result['id']]);
            }

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	    
    }

	public function saveEmpSalary($data){
        try{
            $this->db->trans_begin();

            $result =  $this->store($this->empMaster,$data,'Employee Salary');
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }        
    }

	public function getEmployeeBirthdayList(){
        $queryData['tableName'] = $this->empMaster;
        $queryData['select'] = "employee_master.id,employee_master.emp_name,employee_master.emp_mobile_no";

        $queryData['where']['employee_master.emp_birthdate !='] = NULL;
        $queryData['customWhere'][] = '(DATE_FORMAT(emp_birthdate, "%m-%d") = DATE_FORMAT(CURDATE(), "%m-%d"))';
        
		/*
        if(!in_array($this->userRole,[1,-1,3])):
            $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id) > 0 OR employee_master.id = '.$this->loginId.')';
        endif;
		*/

        $result = $this->rows($queryData);
		return $result;
    }
	
}
?>