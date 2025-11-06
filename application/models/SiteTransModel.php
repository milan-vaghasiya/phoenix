<?php
class SiteTransModel extends MasterModel{
    private $projectWork = "project_work";
    private $projectDetails = "project_milestone";
    private $projectMilestone = "project_milestone";
    private $extraActivity = "extra_activity";

    public function getDTRows($data){
        $data['tableName'] = $this->projectWork;
        $data['select'] = "project_work.*, project_master.project_name";
        $data['leftJoin']['project_master'] = "project_master.id = project_work.project_id";
		
		if(!empty($data['work_type'])):
            $data['where']['project_work.type'] = $data['work_type'];
        endif;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(project_work.`,'%d-%m-%Y')";
        $data['searchCol'][] = "project_master.project_name";
        $data['searchCol'][] = "project_work.tower_name";
        $data['searchCol'][] = "project_work.work_detail";
        $data['searchCol'][] = "CONCAT(project_work.execution,' (',project_work.uom,')')";
        $data['searchCol'][] = "project_work.remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
		
    public function getLastWorkDetail($data){
        $queryData['tableName'] = $this->projectWork;		
		
		if(!empty($data['project_id'])):
			$queryData['where']['project_work.project_id'] = $data['project_id'];
		endif;
		
		$queryData['where']['DATE(project_work.trans_date)'] = date('Y-m-d');
		$queryData['where']['project_work.type'] = $data['type'];
		
		$queryData['order_by']['project_work.trans_date'] = "DESC";
		$queryData['order_by']['project_work.id'] = "DESC";
		
		$result = $this->getData($queryData,"row");
		
		//$this->printQuery();
		return $result;
    }
		
    public function getWorkDetailList($data){
        $queryData['tableName'] = $this->projectWork;
        $queryData['select'] = "project_work.*, project_master.project_name";

        $queryData['leftJoin']['project_master'] = "project_master.id = project_work.project_id";
		
        if(!empty($data['id'])):
            $queryData['where']['project_work.id'] = $data['id'];
			$data['result_type'] = "row";
        endif;
		
        if(!empty($data['type'])):
            $queryData['where']['project_work.type'] = $data['type'];
        endif;
		
		if(!empty($data['project_id'])):
			$queryData['where']['project_work.project_id'] = $data['project_id'];
		endif;
		
		if(!empty($data['trans_date'])):
			$queryData['where']['project_work.trans_date'] = $data['trans_date'];
		endif;
		
		if(!empty($data['from_date']) AND !empty($data['to_date'])):
			$queryData['where']['project_work.trans_date >= '] = $data['from_date'];
			$queryData['where']['project_work.trans_date <= '] = $data['to_date'];
		endif;
		
		$queryData['order_by']['project_work.trans_date'] = "DESC";
		$queryData['order_by']['project_work.id'] = "DESC";
		
        if(!empty($data['result_type'])):
			$result = $this->getData($queryData,$data['result_type']);
		else:
			$result = $this->getData($queryData,"rows");
        endif;
		//$this->printQuery();
		return $result;
    }

    public function saveBulkWorkDetail($data){
        try{
            $this->db->trans_begin();
			
			$type = ((isset($data['type']) && !empty($data['type'])) ? $data['type'] : 0);
			$oldData = $this->getWorkDetailList(['trans_date'=>$data['trans_date'],'project_id'=>$data['project_id'],'type'=>$type,'result_type'=>'row']);
			
			if(!empty($oldData->id)){$data['id'] = $oldData->id;}
			$result = $this->store($this->projectWork, $data, 'Work Updates');
			
			
            if($this->db->trans_status() !== FALSE) :				
				$users = $this->permission->getMenuUsers(['sub_menu_id'=>"132",'project_id'=>$data['project_id']]);
				$projectData = $this->project->getProject(['id'=>$data['project_id']]);
				
				$notifyArr = Array();
				$notifyArr['title'] = "DPR Work | ".(!empty($projectData->project_name) ? " | ".$projectData->project_name : "");
				$notifyArr['body'] = $data['tower_name']." | ".$data['work_detail'];
				$notifyArr['appCallBack'] = 'DPR';
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

    public function deleteWorkDetail($data){
        try {
            $this->db->trans_begin();

			$result = $this->trash($this->projectWork, ['id' => $data['id']], 'Work Updates');            

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }
    

    /**** Attendance */
    public function getLaborAttendanceDTRows($data){
        $data['tableName'] = 'labor_attendance';
        $data['select'] = "labor_attendance.*,(labor_attendance.present + labor_attendance.fepresent) as total_labor, project_master.project_name";

        $data['leftJoin']['project_master'] = "project_master.id = labor_attendance.project_id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(labor_attendance.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "project_master.project_name";
        $data['searchCol'][] = "labor_attendance.agency_name";
        $data['searchCol'][] = "(labor_attendance.present + labor_attendance.fepresent)";
        $data['searchCol'][] = "labor_attendance.shift";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
	
    public function getLastLaborAttendance($data){
        $queryData['tableName'] ='labor_attendance';
		
		if(!empty($data['project_id'])):
			$queryData['where']['labor_attendance.project_id'] = $data['project_id'];
		endif;
		
		if(!empty($data['trans_date'])):
			$queryData['where']['labor_attendance.trans_date'] = $data['trans_date'];
		endif;
		
		$queryData['order_by']['labor_attendance.trans_date'] = "DESC";
		$queryData['order_by']['labor_attendance.id'] = "DESC";
		
		$queryData['limit'] = 1;
		
        $result = $this->row($queryData);
        
		//$this->printQuery();
		return $result;
    }
	
    public function getLaborAttendance($data){
        $queryData['tableName'] ='labor_attendance';
        $queryData['select'] = "labor_attendance.*, project_master.project_name,labor_attendance.agency_name,employee_master.emp_name";

        $queryData['leftJoin']['project_master'] = "project_master.id = labor_attendance.project_id";

        $queryData['leftJoin']['employee_master'] = "employee_master.id = labor_attendance.created_by";
		
        if(!empty($data['id'])):
			$queryData['where']['labor_attendance.id'] = $data['id'];
		endif;
		
		if(!empty($data['project_id'])):
			$queryData['where']['labor_attendance.project_id'] = $data['project_id'];
		endif;
		
		if(!empty($data['agency_name'])):
			$queryData['where']['labor_attendance.agency_name'] = $data['agency_name'];
		endif;
		
		if(!empty($data['trans_date'])):
			$queryData['where']['labor_attendance.trans_date'] = $data['trans_date'];
		endif;
		
		if(!empty($data['from_date']) AND !empty($data['to_date'])):
			$queryData['where']['labor_attendance.trans_date >= '] = $data['from_date'];
			$queryData['where']['labor_attendance.trans_date <= '] = $data['to_date'];
		endif;
		
		$queryData['order_by']['labor_attendance.trans_date'] = "DESC";
		$queryData['order_by']['labor_attendance.id'] = "DESC";
		
        if(!empty($data['single_row'])){
            $result = $this->row($queryData);
        }else{
            $result = $this->rows($queryData);
        }
        
		//$this->printQuery();
		return $result;
    }
    
	public function saveLaborAttendance($data){
        try{
            $this->db->trans_begin();
			$result = [];
			if(!empty($data['agency']))
			{
				foreach($data['agency'] As $row){
					$total_present = array_sum($row['log_data']);
					$agencyData = [
						'id'=>(!empty($row['id']) ? $row['id'] : ""),
						'trans_date'=>$data['trans_date'],
						'project_id'=>$data['project_id'],
						'shift'=>$data['shift'],
						'type'=>1,
						'agency_name'=>$row['agency_name'],
						'tower_name'=>$row['tower_name'],
						'log_data'=>json_encode($row['log_data']),
						'other_data'=>json_encode($row['other_data']),
						'total_present'=>$total_present
					];
					if(intval($total_present) > 0)
					{
						$result = $this->store('labor_attendance', $agencyData, 'Attendance');
					}
				}
			}
			if(!empty($data['staff']))
			{
				$total_present = array_sum(array_column($data['staff'], "present"));
				$staffData = [
					'id'=>(!empty($data['staff'][0]['id']) ? $data['staff'][0]['id'] : ""),
					'trans_date'=>$data['trans_date'],
					'project_id'=>$data['project_id'],
					'shift'=>$data['shift'],
					'type'=>2,
					'log_data'=>json_encode($data['staff']),
					'total_present'=>$total_present
				];
				if(intval($staffData['total_present']) > 0){ $result = $this->store('labor_attendance', $staffData, 'Attendance'); }
			}
			
            if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
        }catch(\Exception $e){
            return ['status'=>0,'error'=>$e->getMessage()];
        }
    }

    public function deleteLaborAttendance($data){
        try {
            $this->db->trans_begin();

			$result = $this->trash('labor_attendance', ['id' => $data['id']], 'Attendance');            

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }
    

    /**** Labor Attendance V2 By: JP@06082025*/
    public function getLaborAttendanceDTRows_v2($data){
        $data['tableName'] = 'labor_attendance';
        $data['select'] = "labor_attendance.*,(labor_attendance.present + labor_attendance.fepresent) as total_labor, project_master.project_name";

        $data['leftJoin']['project_master'] = "project_master.id = labor_attendance.project_id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(labor_attendance.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "project_master.project_name";
        $data['searchCol'][] = "labor_attendance.agency_name";
        $data['searchCol'][] = "(labor_attendance.present + labor_attendance.fepresent)";
        $data['searchCol'][] = "labor_attendance.shift";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
	
    public function getLastLaborAttendance_v2($data){
        $queryData['tableName'] ='labor_attendance';
		
		if(!empty($data['project_id'])):
			$queryData['where']['labor_attendance.project_id'] = $data['project_id'];
		endif;
		
		if(!empty($data['trans_date'])):
			$queryData['where']['labor_attendance.trans_date'] = $data['trans_date'];
		endif;
		
		if(!empty($data['work_id'])):
			$queryData['where']['labor_attendance.work_id'] = $data['work_id'];
		endif;
		
		if(!empty($data['agency_id'])):
			$queryData['where']['labor_attendance.agency_id'] = $data['agency_id'];
		endif;
		
		if(!empty($data['labor_cat_id'])):
			$queryData['where']['labor_attendance.labor_cat_id'] = $data['labor_cat_id'];
		endif;
		
		$queryData['order_by']['labor_attendance.trans_date'] = "DESC";
		$queryData['order_by']['labor_attendance.id'] = "DESC";
		
		$queryData['limit'] = 1;
		
        $result = $this->row($queryData);
        
		//$this->printQuery();
		return $result;
    }
	
    public function getLaborAttendance_v2($data){
        $queryData['tableName'] ='labor_attendance';
        $queryData['select'] = "labor_attendance.id, labor_attendance.project_id, labor_attendance.type, DATE_FORMAT(labor_attendance.trans_date,'%d-%m-%Y') as trans_date, labor_attendance.agency_id, labor_attendance.labor_cat_id, labor_attendance.work_id, labor_attendance.present, project_master.project_name, agency.party_name as agency_name, IFNULL(select_master.detail,'') as labor_cat_name, IFNULL(project_milestone.tower_name,'') as tower_name";

        $queryData['leftJoin']['project_master'] = "project_master.id = labor_attendance.project_id";
        $queryData['leftJoin']['party_master as agency'] = "agency.id = labor_attendance.agency_id";
        $queryData['leftJoin']['select_master'] = "select_master.id = labor_attendance.labor_cat_id";
        $queryData['leftJoin']['project_milestone'] = "project_milestone.id = labor_attendance.work_id";
		
        if(!empty($data['id'])):
			$queryData['where']['labor_attendance.id'] = $data['id'];
		endif;
		
		if(!empty($data['project_id'])):
			$queryData['where']['labor_attendance.project_id'] = $data['project_id'];
		endif;
		
		if(!empty($data['agency_id'])):
			$queryData['where']['labor_attendance.agency_id'] = $data['agency_id'];
		endif;
		
		if(!empty($data['trans_date'])):
			$queryData['where']['labor_attendance.trans_date'] = $data['trans_date'];
		endif;
		
		if(!empty($data['from_date']) AND !empty($data['to_date'])):
			$queryData['where']['labor_attendance.trans_date >= '] = $data['from_date'];
			$queryData['where']['labor_attendance.trans_date <= '] = $data['to_date'];
		endif;
		
		$queryData['order_by']['labor_attendance.trans_date'] = "DESC";
		$queryData['order_by']['labor_attendance.id'] = "DESC";
		
        if(!empty($data['single_row'])){
            $result = $this->row($queryData);
        }else{
            $result = $this->rows($queryData);
        }
        
		//$this->printQuery();
		return $result;
    }
    
	public function saveLaborAttendance_v2($data){
        try{
            $this->db->trans_begin();
			$result = [];
			if(!empty($data['agency']))
			{
				foreach($data['agency'] As $row){
					$ag_arr = Array();
					$ag_arr['project_id'] = $data['project_id'];
					$ag_arr['trans_date'] = $data['trans_date'];
					$ag_arr['agency_id'] = $row['agency_id'];
					$ag_arr['work_id'] = $row['work_id'];
					$ag_arr['labor_cat_id'] = $row['labor_cat_id'];
					
					$lastRecord = $this->getLastLaborAttendance_v2($ag_arr);
					
					if($row['present'] > 0)
					{
						$agencyData = [
							'id'=>(!empty($lastRecord->id) ? $lastRecord->id : ""),
							'trans_date'=>$data['trans_date'],
							'project_id'=>$data['project_id'],
							'shift'=>$data['shift'],
							'type'=>1,
							'agency_id'=>$row['agency_id'],
							'work_id'=>$row['work_id'],
							'labor_cat_id'=>$row['labor_cat_id'],
							'present'=>$row['present']
						];
						
						$result = $this->store('labor_attendance', $agencyData, 'Attendance');
					}
				}
			}
			/*
			if(!empty($data['staff']))
			{
				$total_present = array_sum(array_column($data['staff'], "present"));
				$staffData = [
					'id'=>(!empty($data['staff'][0]['id']) ? $data['staff'][0]['id'] : ""),
					'trans_date'=>$data['trans_date'],
					'project_id'=>$data['project_id'],
					'shift'=>$data['shift'],
					'type'=>2,
					'log_data'=>json_encode($data['staff']),
					'total_present'=>$total_present
				];
				if(intval($staffData['total_present']) > 0){ $result = $this->store('labor_attendance', $staffData, 'Attendance'); }
			}
			*/
            if($this->db->trans_status() !== FALSE) :				
				$users = $this->permission->getMenuUsers(['sub_menu_id'=>"132",'project_id'=>$data['project_id']]);
				$projectData = $this->project->getProject(['id'=>$data['project_id']]);
				
				$notifyArr = Array();
				$notifyArr['title'] = "DPR Attendance | ".(!empty($projectData->project_name) ? " | ".$projectData->project_name : "");
				$notifyArr['body'] = "Labor Attendance Submitted for ".formatDate($data['trans_date']);
				$notifyArr['appCallBack'] = 'DPR';
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

    public function deleteLaborAttendance_v2($data){
        try {
            $this->db->trans_begin();

			$result = $this->trash('labor_attendance', ['id' => $data['id']], 'Attendance');            

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }
    
	public function getLaborAttendanceDprData_v2($data){
        $queryData['tableName'] ='labor_attendance';
        $queryData['select'] = "labor_attendance.type,labor_attendance.log_data,IFNULL(SUM(labor_attendance.present), '0') AS totalPresent, IFNULL(select_master.detail,'') as labor_cat_name,employee_master.emp_name";

        $queryData['leftJoin']['select_master'] = "select_master.id = labor_attendance.labor_cat_id";	
        $queryData['leftJoin']['employee_master'] = "employee_master.id = labor_attendance.created_by";
		
		if(!empty($data['project_id'])):
			$queryData['where']['labor_attendance.project_id'] = $data['project_id'];
		endif;
		
		if(!empty($data['trans_date'])):
			$queryData['where']['labor_attendance.trans_date'] = $data['trans_date'];
		endif;
		
		$queryData['group_by'][] = "labor_attendance.labor_cat_id";
		
		$queryData['order_by']['select_master.sequence'] = "ASC";
		
		//$queryData['order_by']['labor_attendance.trans_date'] = "DESC";
		//$queryData['order_by']['labor_attendance.id'] = "DESC";
		
        if(!empty($data['single_row'])){
            $result = $this->row($queryData);
        }else{
            $result = $this->rows($queryData);
        }
        
		return $result;
    }
	
    /*** Machine Status */

    public function getMachineryStatusDTRows($data){
        $data['tableName'] = 'machine_status';
        $data['select'] = "machine_status.*, project_master.project_name, machine_master.machine_name";

        $data['leftJoin']['project_master'] = "project_master.id = machine_status.project_id";
        $data['leftJoin']['machine_master'] = "machine_master.id = machine_status.machine_id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(machine_status.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "project_master.project_name";
        $data['searchCol'][] = "machine_master.machine_name";
        $data['searchCol'][] = "machine_status.qty";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getLastRecords($data){
        $queryData['tableName'] = 'machine_status';
		
		if(!empty($data['project_id'])):
			$queryData['where']['machine_status.project_id'] = $data['project_id'];
		endif;
		
		$queryData['order_by']['machine_status.trans_date'] = "DESC";
		$queryData['order_by']['machine_status.id'] = "DESC";
		
        $queryData['limit'] = 1;
		
		$result = $this->row($queryData);
        
		//$this->printQuery();
		return $result;
    }

    public function getMachineryStatusList($data){
        $queryData['tableName'] = 'machine_status';
        $queryData['select'] = "machine_status.id, machine_status.project_id, machine_status.trans_date, machine_status.agency_id, machine_status.machine_id, machine_status.qty, project_master.project_name,machine_master.machine_name";
		
        $queryData['leftJoin']['project_master'] = "project_master.id = machine_status.project_id";
        $queryData['leftJoin']['machine_master'] = "machine_master.id = machine_status.machine_id";		
        
        if(!empty($data['id'])):
			$queryData['where']['machine_status.id'] = $data['id'];
		endif;
		
		if(!empty($data['project_id'])):
			$queryData['where']['machine_status.project_id'] = $data['project_id'];
		endif;
		
		if(!empty($data['trans_date'])):
			$queryData['where']['machine_status.trans_date'] = $data['trans_date'];
		endif;
		if(!empty($data['machine_id'])):
			$queryData['where']['machine_master.id'] = $data['machine_id'];
		endif;
		if(!empty($data['from_date']) AND !empty($data['to_date'])):
			$queryData['where']['machine_status.trans_date >= '] = $data['from_date'];
			$queryData['where']['machine_status.trans_date <= '] = $data['to_date'];
		endif;
		
		$queryData['order_by']['machine_status.trans_date'] = "DESC";
		$queryData['order_by']['machine_status.id'] = "DESC";
		
		
        if(!empty($data['single_row'])){
            $result = $this->row($queryData);
        }else{
            $result = $this->rows($queryData);
        }
        
		//$this->printQuery();
		return $result;
    }

    public function getMachineList($postData=array()){
        $data['tableName'] = "machine_master";
		if(!empty($postData['ids'])){ $data['where_in']['id'] = $postData['ids']; }
        return $this->rows($data);
    }

    public function getProjectMachineList($postData=array()){
        $data['tableName'] = "project_master";
		$data['select'] = "machine_master.*";
		$data['leftJoin']['machine_master'] = "FIND_IN_SET(machine_master.id,project_master.machine_ids) > 0";
		$data['where']['project_master.id'] = $postData['project_id'];
		$data['where']['machine_master.is_delete'] = 0; 
        return $this->rows($data);
    }

    public function saveMachineStatus($data){
        try{
            $this->db->trans_begin();
			$result = ['status'=>1];
			if(!empty($data['machine_id'])){
				foreach($data['machine_id'] As $key=>$machine_id){
					if(floatval($data['qty'][$key]) > 0):
						$mcData = [
							'id'=> (!empty($data['id'][$key]) ? $data['id'][$key] : ""),
							'trans_date'=>$data['trans_date'],
							'project_id'=>$data['project_id'],
							'machine_id'=>$machine_id,
							'qty'=>$data['qty'][$key],
						];
						$result = $this->store('machine_status', $mcData, 'Status');
					else:
						if(!empty($data['id'][$key])):
							$result = $this->store('machine_status', ['id'=>$data['id'][$key],"is_delete"=>1], 'Status');
						endif;
					endif;
				}
			}
           

            if($this->db->trans_status() !== FALSE) :
				$users = $this->permission->getMenuUsers(['sub_menu_id'=>"132",'project_id'=>$data['project_id']]);
				$projectData = $this->project->getProject(['id'=>$data['project_id']]);
				
				$notifyArr = Array();
				$notifyArr['title'] = "DPR Machine | ".(!empty($projectData->project_name) ? " | ".$projectData->project_name : "");
				$notifyArr['body'] = "Machinery Status Updated for ".formatDate($data['trans_date']);
				$notifyArr['appCallBack'] = 'DPR';
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

    public function deleteMachineStatus($data){
        try {
            $this->db->trans_begin();

			$result = $this->trash('machine_status', ['id' => $data['id']], 'Machinery Status');            

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }
    
    /***** Complain */
    public function getComplainDTRows($data){
        $data['tableName'] = 'complain_master';
        $data['select'] = "complain_master.*, project_master.project_name, (CASE WHEN complain_master.agency_id > 0 THEN agency.party_name ELSE 'Phoenix' END) as agency_name";

        $data['leftJoin']['project_master'] = "project_master.id = complain_master.project_id";
        $data['leftJoin']['party_master agency'] = "agency.id = complain_master.agency_id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(complain_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "project_master.project_name";
        $data['searchCol'][] = "(CASE WHEN complain_master.agency_id > 0 THEN agency.party_name ELSE 'Phoenix' END)";
        $data['searchCol'][] = "complain_master.complain_title";
        $data['searchCol'][] = "complain_master.complain_note";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function saveComplain($data){
        try{
            $this->db->trans_begin();
			
			$oldData = $this->getComplainList(['trans_date'=>$data['trans_date'],'project_id'=>$data['project_id'],'result_type'=>'row']);
			
			if(!empty($oldData->id)){$data['id']=$oldData->id;}
			$result = $this->store('complain_master', $data, 'Complain');
			
            if($this->db->trans_status() !== FALSE) :
				$users = $this->permission->getMenuUsers(['sub_menu_id'=>"132",'project_id'=>$data['project_id']]);
				$projectData = $this->project->getProject(['id'=>$data['project_id']]);
				
				$notifyArr = Array();
				$notifyArr['title'] = "DPR Complain | ".(!empty($projectData->project_name) ? " | ".$projectData->project_name : "");
				$notifyArr['body'] = "Complain received ".formatDate($data['trans_date']);
				$notifyArr['appCallBack'] = 'DPR';
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

	public function getComplainList($data){
        $queryData['tableName'] = 'complain_master';
        
        if(!empty($data['id'])):
			$queryData['where']['complain_master.id'] = $data['id'];
			$data['result_type'] = "row";
		endif;
		
		if(!empty($data['project_id'])):
			$queryData['where']['complain_master.project_id'] = $data['project_id'];
		endif;
		
		if(!empty($data['trans_date'])):
			$queryData['where']['complain_master.trans_date'] = $data['trans_date'];
		endif;
		
		if(!empty($data['from_date']) AND !empty($data['to_date'])):
			$queryData['where']['complain_master.trans_date >= '] = $data['from_date'];
			$queryData['where']['complain_master.trans_date <= '] = $data['to_date'];
		endif;
		
		$queryData['order_by']['complain_master.trans_date'] = "DESC";
		$queryData['order_by']['complain_master.id'] = "DESC";
		
        if(!empty($data['result_type'])):
			$result = $this->getData($queryData,$data['result_type']);
		else:
			$result = $this->getData($queryData,"rows");
        endif;
        
		//$this->printQuery();
		return $result;
    }

	public function deleteComplain($data){
        try {
            $this->db->trans_begin();

			$result = $this->trash('complain_master', ['id' => $data['id']], 'Complain');            

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }
  
    /***** Extra Activity */
    public function getExtraActivityDTRows($data){
        $data['tableName'] = 'extra_activity';
        $data['select'] = "extra_activity.*, project_master.project_name";
        $data['leftJoin']['project_master'] = "project_master.id = extra_activity.project_id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(extra_activity.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "project_master.project_name";
        $data['searchCol'][] = "extra_activity.activity";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

	public function getExtraActivity($data){
        $queryData['tableName'] = 'extra_activity';
        
        if(!empty($data['id'])):
			$queryData['where']['extra_activity.id'] = $data['id'];
			$data['result_type'] = "row";
		endif;
		
		if(!empty($data['project_id'])):
			$queryData['where']['extra_activity.project_id'] = $data['project_id'];
		endif;
		
		if(!empty($data['trans_date'])):
			$queryData['where']['extra_activity.trans_date'] = $data['trans_date'];
		endif;
		
		if(!empty($data['from_date']) AND !empty($data['to_date'])):
			$queryData['where']['extra_activity.trans_date >= '] = $data['from_date'];
			$queryData['where']['extra_activity.trans_date <= '] = $data['to_date'];
		endif;
		
		$queryData['order_by']['extra_activity.trans_date'] = "DESC";
		$queryData['order_by']['extra_activity.id'] = "DESC";
		
        if(!empty($data['result_type'])):
			$result = $this->getData($queryData,$data['result_type']);
		else:
			$result = $this->getData($queryData,"rows");
        endif;
        
		//$this->printQuery();
		return $result;
    }

    public function saveExtraActivity($data){
        try{
            $this->db->trans_begin();
			
			$oldData = $this->getExtraActivity(['trans_date'=>$data['trans_date'],'project_id'=>$data['project_id'],'result_type'=>'row']);
			
			if(!empty($oldData->id)){$data['id']=$oldData->id;}
			$result = $this->store('extra_activity', $data, 'Extra Activity');
			
            
			if($this->db->trans_status() !== FALSE) :
				$users = $this->permission->getMenuUsers(['sub_menu_id'=>"132",'project_id'=>$data['project_id']]);
				$projectData = $this->project->getProject(['id'=>$data['project_id']]);
				$project_name = (!empty($projectData->project_name) ? $projectData->project_name : "");
								
				$notifyArr = Array();
				$notifyArr['title'] = "DPR Activity | ".(!empty($projectData->project_name) ? " | ".$projectData->project_name : "");
				$notifyArr['body'] = "Extra Activity on ".formatDate($data['trans_date']);
				$notifyArr['appCallBack'] = 'DPR';
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
	
	public function deleteExtraActivity($data){
        try {
            $this->db->trans_begin();

			$result = $this->trash('extra_activity', ['id' => $data['id']], 'Extra Activity');            

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }
  
}
?>