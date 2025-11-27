<?php
class ProjectMasterModel extends MasterModel{
    private $projectMaster = "project_master";
    private $projectMilestone = "project_milestone";
    private $workProgress = "work_progress_log";
    private $projectTowers = "project_towers";
    private $agencyWork = "agency_work";
	
	/* Project Detail Start */
    public function getDTRows($data){
        $data['tableName'] = $this->projectMaster;
        $data['select'] = "project_master.*, (CASE WHEN project_master.party_id > 0 THEN party_master.party_name ELSE 'Own Project' END) as party_name, (CASE WHEN project_master.cost_type = 1 THEN 'Fixed' ELSE 'Per Feet' END) as cost_type_name";

        $data['leftJoin']['party_master'] = "party_master.id = project_master.party_id";
        $data['where']['project_master.is_active'] = $data['status'];

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "project_master.project_name";
        $data['searchCol'][] = "project_master.project_type";
        $data['searchCol'][] = "(CASE WHEN project_master.party_id > 0 THEN party_master.party_name ELSE 'Own Project' END)";
		$data['searchCol'][] = "project_master.work_size";
        $data['searchCol'][] = "(CASE WHEN project_master.cost_type = 1 THEN 'Fixed' ELSE 'Per Feet' END)";
        $data['searchCol'][] = "project_master.amount";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0) :
				$errorMessage['project_name'] = "Project name is duplicate.";
				return ['status' => 0, 'message' => $errorMessage];
            endif;
			
			$data['lat_lng'] = (!empty($data['lat']) && !empty($data['lng']) ? $data['lat'].",".$data['lng'] : NULL);
			
            $result = $this->store($this->projectMaster, $data, 'Project');

            if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
        }catch(\Exception $e){
            return ['status'=>0,'error'=>$e->getMessage()];
        }
    }

    public function checkDuplicate($data){
        $queryData = [];
        $queryData['tableName'] = $this->projectMaster;
        $queryData['where'] = ['project_name'=>$data['project_name'], 'party_id'=>$data['party_id']];

        if(isset($data['id'])):
            $queryData['where']['id !='] = $data['id'];
        endif;

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getProject($data){
        $queryData = [];
        $queryData['tableName'] = $this->projectMaster;
        $queryData['select'] = "project_master.*, (CASE WHEN project_master.party_id > 0 THEN party_master.party_name ELSE 'Own Project' END) as party_name, IF(project_master.party_id > 0, party_master.contact_person, '') as contact_person, IF(project_master.party_id > 0, party_master.party_phone, '') as party_phone, IF(project_master.party_id > 0, party_master.party_email, '') as party_email, (CASE WHEN project_master.cost_type = 1 THEN 'Fixed' ELSE 'Per Feet' END) as cost_type_name";
        $queryData['leftJoin']['party_master'] = "party_master.id = project_master.party_id";
		
        $queryData['where']['project_master.id'] = $data['id'];
		
        $result = $this->row($queryData);
		//$this->printQuery();
		return $result;
    }

    public function getProjectList($data = []){
        $queryData = [];
        $queryData['tableName'] = $this->projectMaster;
        $queryData['select'] = "project_master.id,project_master.project_name, project_master.project_type, (CASE WHEN project_master.party_id > 0 THEN party_master.party_name ELSE 'Own Project' END) as party_name, (CASE WHEN project_master.cost_type = 1 THEN 'Fixed' ELSE 'Per Feet' END) as cost_type_name, project_master.site_add";
		$queryData['leftJoin']['party_master'] = "party_master.id = project_master.party_id";
		
		if(isset($data['is_active'])):
		    $queryData['where']['project_master.is_active'] = $data['is_active'];
        endif;
		
		if(!in_array($this->userRole,[-1,1])): 
			$queryData['customWhere'][] = 'FIND_IN_SET('.$this->loginId.',project_master.incharge_ids) > 0';
		endif;
		
        if(!empty($data['ignore_project_id'])): 
            $queryData['where']['project_master.id != '] = $data['ignore_project_id'];           
        endif;
		
        return $this->rows($queryData);
    }

    public function getProjectListForChat($data = []){
		
		$queryData = [];
        $queryData['tableName'] = $this->projectMaster;
        $queryData['select'] = "project_master.id AS project_id, project_master.project_name, ph.id AS history_id, ph.message, ph.media_file, ph.created_at";
		$queryData['leftJoin'][' project_history ph'] = "ph.id = ( SELECT ph2.id FROM project_history ph2 WHERE ph2.project_id = project_master.id ORDER BY ph2.created_at DESC LIMIT 1 )";
		
		$queryData['where']['project_master.is_active'] = 0;
		
		if(!in_array($this->userRole,[-1,1])): 
			$queryData['customWhere'][] = 'FIND_IN_SET('.$this->loginId.',project_master.incharge_ids) > 0';
		endif;
		
        if(!empty($data['ignore_project_id'])): 
            $queryData['where']['project_master.id != '] = $data['ignore_project_id'];           
        endif;
		
		$queryData['order_by']['ph.created_at'] = 'DESC';
		
		//$this->printQuery();
		
        return $this->rows($queryData);
    }

    public function delete($data){
        try {
			$this->db->trans_begin();

            $checkData['columnName'] = ['project_id'];
            $checkData['value'] = $data['id'];
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Project is currently in use. you cannot delete it.'];
            endif;

			$result = $this->trash($this->projectMaster, ['id' => $data['id']], 'Project');            

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }
	
	public function saveIncharge($data){
		try{
            $this->db->trans_begin();

            $result = $this->store($this->projectMaster, $data, 'Project');

            if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
        }catch(\Exception $e){
            return ['status'=>0,'error'=>$e->getMessage()];
        }
	}
	/* Project Detail End */


	/* Project Tower Detail Start */
    public function getProjectTower($param = []){
        $queryData = [];
        $queryData['tableName'] = $this->projectTowers;
		
		$queryData['select'] = "project_towers.id, project_towers.project_id, project_towers.tower_name, project_towers.total_basement, project_towers.total_floor,  project_towers.description";
		
        if(!empty($param['id'])):
            $queryData['where']['project_towers.id'] = $param['id'];
			$param['result_type'] = "row";
        endif;
        if(!empty($param['project_id'])):
            $queryData['where']['project_towers.project_id'] = $param['project_id'];
        endif;
		
		
        if(!empty($param['result_type'])):
			$result = $this->getData($queryData,$param['result_type']);
		else:
			$result = $this->getData($queryData,"rows");
        endif;
		
		//$this->printQuery();
        return $result;
    }

    public function saveProjectTower($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicateTower($data) > 0) :
				$errorMessage['tower_name'] = "Tower name is duplicate.";
				return ['status' => 0, 'message' => $errorMessage];
            endif;

            $result = $this->store($this->projectTowers, $data, 'Project');

            if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
        }catch(\Exception $e){
            return ['status'=>0,'error'=>$e->getMessage()];
        }
    }

    public function checkDuplicateTower($data){
        $queryData = [];
        $queryData['tableName'] = $this->projectTowers;
        $queryData['where'] = ['tower_name'=>$data['tower_name'], 'project_id'=>$data['project_id']];

        if(isset($data['id'])):
            $queryData['where']['id !='] = $data['id'];
        endif;

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function deleteProjectTower($data){
        try {
			$this->db->trans_begin();

			$result = $this->trash($this->projectTowers, ['id' => $data['id']], 'Project Tower');            

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }
	
	/* Project Tower Detail End */


    /* Project Milestone Start */
    public function getProjectMilestoneList($data){
        $queryData = [];
        $queryData['tableName'] = $this->projectMilestone;
		$queryData['select'] = "project_milestone.id, project_milestone.project_id, project_milestone.tower_name, IFNULL(select_master.detail,project_milestone.work_type) as work_type, project_milestone.work_qty, project_milestone.work_rate, project_milestone.material_qty, project_milestone.material_used, project_milestone.description, project_milestone.contract_type, (CASE WHEN project_milestone.contract_type = 1 THEN 'Labor + Material' ELSE 'Labor' END) as contract_label";
		
		$queryData['select'] .= ", (CASE WHEN project_master.party_id > 0 THEN party_master.party_name ELSE 'Own Project' END) as party_name";
		$queryData['leftJoin']['project_master'] = "project_master.id = project_milestone.project_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = project_master.party_id";
		$queryData['leftJoin']['select_master'] = "select_master.id = project_milestone.work_type_id";
		        
		if(!empty($data['tower_name'])):
			$queryData['where']['project_milestone.tower_name'] = $data['tower_name'];
		endif;
		
        if(!empty($data['project_id'])):
            $queryData['where']['project_milestone.project_id'] = $data['project_id'];
        endif;
        $result = $this->rows($queryData);
		//$this->printQuery();
        return $result;
    }

    public function getProjectMilestone($data){
        $queryData = [];
        $queryData['tableName'] = $this->projectMilestone;
        $queryData['where']['project_milestone.id'] = $data['id'];
        return $this->row($queryData);
    }
    
    public function saveProjectMileStone($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->projectMilestone, $data, 'Project Milestone');

            if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
        }catch(\Exception $e){
            return ['status'=>0,'error'=>$e->getMessage()];
        }
    }
	
    public function deleteProjectMilestone($data){
        try {
            $this->db->trans_begin();

			$result = $this->trash($this->projectMilestone, ['id' => $data['id']], 'Project Milestone');            

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }
    
	/* Project Milestone End */
	
	
	/* Agency Work Start */
    public function getProjectAgencyList($data = []){
        $queryData = [];
        $queryData['tableName'] = $this->agencyWork;
		if(!empty($data['select'])):
			$queryData['select'] = $data['select'];
		else:
			$queryData['select'] = "agency_work.work_id, agency_work.agency_id, agency_work.id as aw_id, party_master.id, party_master.party_name as agency_name";
		endif;
		
		$queryData['select'] .= ", select_master.detail as work_type, IFNULL(select_master.labor_cat_ids,'') as labor_cat_ids";
		
        $queryData['leftJoin']['party_master'] = "party_master.id = agency_work.agency_id";
		$queryData['leftJoin']['project_milestone'] = "project_milestone.id = agency_work.work_id";
		$queryData['leftJoin']['select_master'] = "select_master.id = project_milestone.work_type_id";

		$queryData['order_by']['agency_work.work_id'] = 'ASC';
		if(!empty($data['tower'])):
			$queryData['select'] .= ",project_milestone.tower_name";
			$queryData['order_by']['project_milestone.tower_name'] = 'ASC';
		endif;

        $queryData['where']['agency_work.project_id'] = $data['project_id'];
		
        $result = $this->rows($queryData);
        return $result;
    }

    public function getAgencyWork($param = []){
        $queryData = [];
        $queryData['tableName'] = $this->agencyWork;
        $queryData['select'] = "agency_work.id, agency_work.agency_id , agency_work.project_id, agency_work.work_id, agency_work.work_qty, agency_work.work_rate, agency_work.material_qty, agency_work.material_used, agency_work.description, agency_work.contract_type, project_milestone.tower_name, project_milestone.work_type, (CASE WHEN agency_work.contract_type = 1 THEN 'Labor' ELSE 'Labor + Material' END) as contract_label, party_master.party_name as agency_name";
        $queryData['leftJoin']['party_master'] = "party_master.id = agency_work.agency_id";
        $queryData['leftJoin']['project_milestone'] = "project_milestone.id = agency_work.work_id";
		
		
        if(!empty($param['id'])):
            $queryData['where']['agency_work.id'] = $param['id'];
			$param['result_type'] = "row";
        endif;	
		
		if(!empty($param['project_id'])):
			$queryData['where']['agency_work.project_id'] = $param['project_id'];
		endif;
		
		if(!empty($param['work_id'])):
			$queryData['where']['agency_work.work_id'] = $param['work_id'];
		endif;	
		
        if(!empty($param['result_type'])):
			$result = $this->getData($queryData,$param['result_type']);
		else:
			$result = $this->getData($queryData,"rows");
        endif;
		
        return $result;
    }
	
    public function saveAgencyWork($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->agencyWork, $data, 'Agency Work');

            if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
        }catch(\Exception $e){
            return ['status'=>0,'error'=>$e->getMessage()];
        }
    }

    public function deleteAgencyWork($data){
        try {
            $this->db->trans_begin();

			$result = $this->trash($this->agencyWork, ['id' => $data['id']], 'Agency Work');            

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }
    
	/* Agency Work End */

	
    /* Agency Work Progress Start */	
	public function getWorkProgressList($data){
        $queryData = [];
        $queryData['tableName'] = $this->workProgress;
        $queryData['select'] = "work_progress_log.*, (CASE WHEN project_master.party_id > 0 THEN party_master.party_name ELSE 'Own Project' END) as party_name, employee_master.emp_name as entry_by,IFNULL(project_milestone.status,0) as status";
		$queryData['leftJoin']['project_master'] = "project_master.id = work_progress_log.project_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = project_master.party_id";
		$queryData['leftJoin']['project_milestone'] = "project_milestone.id = work_progress_log.aw_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = work_progress_log.created_by";
		        
		if(!empty($data['work_ref_id'])):
			$queryData['where']['work_progress_log.work_id'] = $data['work_ref_id'];
		endif;
        if(!empty($data['project_id'])):
            $queryData['where']['work_progress_log.project_id'] = $data['project_id'];
        endif;
		
		$queryData['order_by']['work_progress_log.created_at'] = "DESC";
		$queryData['order_by']['work_progress_log.id'] = "DESC";
		
        $result = $this->rows($queryData);
		//$this->printQuery();
        return $result;
    }	

    public function saveWorkProgress($data){
        try{
            $this->db->trans_begin();
			
			$agProgress = Array();
			$agProgress['id'] = $data['aw_id'];
			if(!empty($data['work_done'])){ $agProgress['work_done'] = $data['work_done']; }
			$agProgress['status'] = (!empty($data['status']) ? $data['status'] : 0);
			if($data['status'] == 1){$data['description'] = "Work Started";}
			if($data['status'] == 2){$data['description'] = "Work put on Hold";}
			if($data['status'] == 3){$data['description'] = "Work Completed";}
			if($data['status'] == 4){$data['description'] = "Work Cancelled";}
			unset($data['status']);
			
            $result = $this->store($this->workProgress, $data, 'Work Progress');	
			
			
            $result1 = $this->store($this->projectMilestone, $agProgress, 'Agency Progress');

            if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
        }catch(\Exception $e){
            return ['status'=>0,'error'=>$e->getMessage()];
        }
    }

    public function deleteWorkProgress($data){
        try {
            $this->db->trans_begin();

			$result = $this->trash($this->workProgress, ['id' => $data['id']], 'Work Progress');            

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }
    
	/* Agency Work Progress End */
	
	//project Change Status
    public function changeProjectStatus($data){
        try{
            $this->db->trans_begin();
			
			$result = $this->edit($this->projectMaster, ['id' => $data['id']], ['is_active'=>$data['is_active']]);            

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }
    
}
?>