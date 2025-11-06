<?php
class HeadQuarterModel extends MasterModel{
    private $head_quarter = "head_quarter"; 

    public function getHeadQuarterDTRows($data){
        $data['tableName'] = $this->head_quarter;
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "name";
        $data['searchCol'][] = "hq_lat";
        $data['searchCol'][] = "hq_long";
        $data['searchCol'][] = "remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getHeadQuarter($data){
        $queryData['tableName'] = $this->head_quarter;
        $queryData['where']['id'] = $data['id'];
        return $this->row($queryData);
    }

    public function getHeadQuarterList(){
        $queryData['tableName'] = $this->head_quarter;
        return $this->rows($queryData);
    }

    public function saveHeadQuarter($data){
        try{
            $this->db->trans_begin();
            
            if(!empty($data['emp_id'])){ $emp_id = $data['emp_id']; unset($data['emp_id']); }
            
            $result = $this->store($this->head_quarter,$data,'Head Quarter');

            if(!empty($emp_id) && empty($data['id'])){ $this->edit('employee_master', ['id'=>$emp_id], ['quarter_id'=>$result['insert_id']]); }

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function saveNewHeadQuarter($data){
        try{
            $this->db->trans_begin();

            $result = $this->store('hq_change_req',$data,'New Head Quarter');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getNewHeadQuarterList($data=[]){
        $queryData['tableName'] = 'hq_change_req';
        $queryData['select'] = "hq_change_req.*,employee_master.emp_code,employee_master.emp_name,emp_designation.title as designation_name,department_master.name as department_name,head_quarter.name as hq_name,hq.name as new_hq_name";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = hq_change_req.emp_id";
        $queryData['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        $queryData['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $queryData['leftJoin']['head_quarter'] = "hq_change_req.hq_id = head_quarter.id";
        $queryData['leftJoin']['head_quarter as hq'] = "hq_change_req.new_hq_id = hq.id";

        if(isset($data['status'])):
            $queryData['where']['hq_change_req.status'] = $data['status']; 
        endif;
            
        if(!in_array($this->userRole,[1,-1])):
            $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
        endif;

        if(!empty($data['search'])):
            $queryData['like']['employee_master.emp_name'] = $data['search'];
            $queryData['like']['employee_master.emp_code'] = $data['search'];
            $queryData['like']['department_master.name'] = $data['search'];
            $queryData['like']['emp_designation.title'] = $data['search'];
            $queryData['like']['head_quarter.name'] = $data['search'];
            $queryData['like']['hq.name'] = $data['search'];
        endif;

        if(isset($data['start']) && isset($data['length'])):
            $queryData['start'] = $data['start'];
            $queryData['length'] = $data['length'];
        endif;

        return $this->rows($queryData);
    }

    public function changeHqRequest($data){ 
        try{
            $this->db->trans_begin();
            if($data['status'] == 1){
                $status = ['status'=>$data['status'],'approve_by'=>$this->loginId, 'approve_at'=>date('Y-m-d')];
                $this->edit('employee_master', ['id'=>$data['emp_id']], ['quarter_id'=>$data['new_hq_id']]);
            }else{
                $status = ['status'=>$data['status']];
            }
            $this->edit('hq_change_req', ['id'=>$data['id']], $status);
            
            $result = ['status'=>1,'message'=>"Request status changed Successfully."];
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

}
?>