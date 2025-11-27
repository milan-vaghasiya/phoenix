<?php
class AttendanceModel extends MasterModel{
    private $agencyAttendance = "agency_attendance";
    private $attendance_log = "attendance_log";
	
    public function getDTRows($data){
        $data['tableName'] = $this->agencyAttendance;

        $data['select'] = "agency_attendance.*, project_master.project_name, agency_master.party_name as agency_name";

        $data['leftJoin']['project_master'] = "project_master.id = agency_attendance.project_id";
        $data['leftJoin']['party_master as agency_master'] = "agency_master.id = agency_attendance.party_id";

		$data['where']['agency_attendance.attendance_date >='] = $this->startYearDate;
        $data['where']['agency_attendance.attendance_date <='] = $this->endYearDate;
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "agency_attendance.attendance_date";
        $data['searchCol'][] = "project_master.project_name";
        $data['searchCol'][] = "agency_master.party_name";
        $data['searchCol'][] = "agency_attendance.labour_category";
        $data['searchCol'][] = "agency_attendance.shift_name";
        $data['searchCol'][] = "agency_attendance.male";
        $data['searchCol'][] = "agency_attendance.female";
        
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }
	
	/********** Attendance **********/
        
		public function getAttendanceDTRows($data){
            $data['tableName'] = $this->attendance_log;
            $data['select'] = "attendance_log.id, attendance_log.punch_type, attendance_log.type, attendance_log.emp_id, attendance_log.attendance_date, attendance_log.punch_date, attendance_log.start_at, attendance_log.img_file, attendance_log.start_location, attendance_log.loc_add, attendance_log.approve_by, attendance_log.approve_at, attendance_log.notes, attendance_log.project_id, attendance_log.attendance_status, attendance_log.created_by, emp.emp_code,emp.emp_name,employee_master.emp_name as approve_name,project_master.project_name,shift_master.shift_name,project_master.lat_lng";
            $data['leftJoin']['employee_master emp'] = "emp.id = attendance_log.emp_id";
            $data['leftJoin']['employee_master'] = "employee_master.id = attendance_log.approve_by";
            $data['leftJoin']['project_master'] = "project_master.id = attendance_log.project_id";
            $data['leftJoin']['shift_master'] = "shift_master.id = attendance_log.shift_id";

            //$data['leftJoin']['project_master pm'] = "pm.id = emp.quarter_id";
			
			//if(empty($data['status'])) {  $data['where']['attendance_log.approve_by'] = 0; }
			//$data['where']['attendance_log.approve_by'] = 0;
			
			$data['customWhere'][] = "MONTH(attendance_log.punch_date) = ".date('m')." AND YEAR(attendance_log.punch_date) = ".date('Y');
			
			if(!in_array($this->userRole,[1,-1,3])):
				$data['customWhere'][] = '(find_in_set("'.$this->loginId.'", emp.super_auth_id ) > 0)';
            endif;
			
			$data['order_by']['attendance_log.punch_date'] = "DESC";
			$data['order_by']['attendance_log.emp_id'] = "DESC";
              
			$data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "emp.emp_code";
            $data['searchCol'][] = "emp.emp_name";
            $data['searchCol'][] = "(CASE WHEN attendance_log.punch_type = 1 THEN 'Device Punch' WHEN attendance_log.punch_type = 2 THEN 'Manual Punch' WHEN attendance_log.punch_type = 3 THEN 'Extra Hours' WHEN attendance_log.punch_type = 4 THEN 'App Punch' ELSE 'AUTO PUNCH OUT' END)";
            $data['searchCol'][] = "attendance_log.attendance_date";
            $data['searchCol'][] = "attendance_log.punch_date";
            $data['searchCol'][] = "project_master.project_name";
            $data['searchCol'][] = "shift_master.shift_name";
            $data['searchCol'][] = "";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            return $this->pagingRows($data);
        }

        public function getEmployeeData(){
            $data['tableName'] = $this->attendance_log;
            $data['where']['emp_id'] = $this->loginId;
            $data['order_by']['punch_date'] = "DESC";
            $data['limit'] = 1;
            return $this->row($data);
        }

        public function getManualAttendanceData($data){
            $data['tableName'] = $this->attendance_log;
            $data['where']['id'] = $data['id'];
            return $this->row($data);
        }

        public function deleteManualAttendance($id){
            try{
                $this->db->trans_begin();

                $result = $this->trash($this->attendance_log,['id'=>$id],'Manual Attendance');

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Throwable $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }

        public function getEmpLogData($data = []){
            $queryData['tableName'] = $this->attendance_log;

            $queryData['select'] = "attendance_log.*,employee_master.emp_code,employee_master.emp_name,CONCAT(project_master.hq_lat,',',project_master.hq_long) as hq_location,project_master.name as hq_name";
            $queryData['leftJoin']['employee_master'] = "employee_master.id = attendance_log.emp_id";
            $queryData['leftJoin']['project_master'] = "employee_master.quarter_id = project_master.id";

            $queryData['where']['attendance_log.emp_id'] = $this->loginId;
            $queryData['order_by']['attendance_log.punch_date'] = "DESC";

            if(isset($data['attendance_status'])):
                $queryData['where']['attendance_log.attendance_status'] = $data['attendance_status'];
            endif;

            if(!empty($data['search'])):
                $queryData['like']['attendance_log.type'] = $data['search'];
                $queryData['like']['DATE_FORMAT(attendance_log.punch_date,"%d-%m-%Y")'] = $data['search'];
                $queryData['like']['attendance_log.loc_add'] = $data['search'];
                $queryData['like']['attendance_log.start_location'] = $data['search'];
            endif;
			
			if(!in_array($this->userRole,[1,-1,3])):
				if(!empty($data['self_punch']) AND $data['self_punch']==1):	// Self Punch
					$queryData['where']['attendance_log.emp_id'] = $this->loginId;
				elseif(!empty($data['self_punch']) AND $data['self_punch']==2):	// Punch To Be Approved
					$queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) > 0 )';
				else:
					$queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) > 0 OR attendance_log.emp_id = '.$this->loginId.')';
				endif;
			endif;
			
			
            if(isset($data['start']) && isset($data['length'])):
                $queryData['start'] = $data['start'];
                $queryData['length'] = $data['length'];
            endif;

            return $this->rows($queryData);
        }
		
        public function getAttendanceList($data = []){
            $queryData['tableName'] = $this->attendance_log;

            $queryData['select'] = "attendance_log.*,employee_master.emp_code,employee_master.emp_name, employee_master.week_off, project_master.project_name";
			//$queryData['select'] .= ", CASE WHEN MOD(ROW_NUMBER() OVER (PARTITION BY attendance_log.emp_id, attendance_log.attendance_date ORDER BY attendance_log.punch_date), 2) = 1 THEN 'IN' ELSE 'OUT' END AS in_out_flag";
			
            $queryData['leftJoin']['employee_master'] = "employee_master.id = attendance_log.emp_id";
            $queryData['leftJoin']['project_master'] = "project_master.id = attendance_log.project_id";

            if(isset($data['emp_id'])):
				$data['where']['attendance_log.emp_id'] = $data['emp_id'];
			else:
				$queryData['where']['attendance_log.emp_id'] = $this->loginId;
			endif;
			
            if(isset($data['attendance_status'])):
                $queryData['where']['attendance_log.attendance_status'] = $data['attendance_status'];
            endif;
			
            if(isset($data['attendance_date'])):
				$queryData['where']['attendance_log.attendance_date'] = $data['attendance_date'];
			else:
				if(isset($data['from_date'])):
					$queryData['where']['attendance_log.attendance_date >= '] = $data['from_date'];
				endif;
				
				if(isset($data['to_date'])):
					$queryData['where']['attendance_log.attendance_date <= '] = $data['to_date'];
				endif;
			endif;

            if(!empty($data['search'])):
                $queryData['like']['attendance_log.type'] = $data['search'];
                $queryData['like']['DATE_FORMAT(attendance_log.punch_date,"%d-%m-%Y")'] = $data['search'];
                $queryData['like']['attendance_log.loc_add'] = $data['search'];
                $queryData['like']['attendance_log.start_location'] = $data['search'];
            endif;
			
			//$queryData['where']['attendance_log.emp_id'] = 6;
			
			if(!in_array($this->userRole,[1,-1,3])):
				if(!empty($data['self_punch']) AND $data['self_punch']==1):	// Self Punch
					$queryData['where']['attendance_log.emp_id'] = $this->loginId;
				elseif(!empty($data['self_punch']) AND $data['self_punch']==2):	// Punch To Be Approved
					$queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) > 0 )';
				else:
					$queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) > 0 OR attendance_log.emp_id = '.$this->loginId.')';
				endif;
			endif;
			
			if(!empty($param['group_by'])):
                $queryData['group_by'][] = $data['group_by'];
            endif;

            if(!empty($param['having'])):
                $queryData['having'][] = $data['having'];
            endif;
			
            $queryData['order_by']['attendance_log.punch_date'] = "DESC";
			
			
            if(isset($data['start']) && isset($data['length'])):
                $queryData['start'] = $data['start'];
                $queryData['length'] = $data['length'];
            endif;

            return $this->rows($queryData);
        }

        public function countEmpPunches($param = []){
            $queryData['tableName'] = $this->attendance_log;

            $queryData['select'] = "count(*) as total_punch";
			
            $queryData['where']['attendance_log.emp_id'] = (!empty($param['emp_id']) ? $param['emp_id'] : $this->loginId);
			if(!empty($param['from_date'])){$data['where']['DATE(attendance_log.punch_date) >= '] = $param['from_date'];}
            if(!empty($param['to_date'])){$data['where']['DATE(attendance_log.punch_date) <= '] = $param['to_date'];}

            return $this->row($queryData);
        }

        public function saveAttendance($data){ 
            try{
                $this->db->trans_begin();
				
                $result = $this->store($this->attendance_log,$data,'Attendance Log');
                

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }
		
		public function checkDuplicateAttendance($data){  
            $queryData['tableName'] = $this->attendance_log;
            $queryData['where']['type'] = $data['type'];
            $queryData['where']['emp_id'] = $data['emp_id'];
            $queryData['where']['DATE(punch_date)'] = date('Y-m-d',strtotime($data['punch_date']));
            
            if(!empty($data['id'])) { 
                $queryData['where']['id != '] = $data['id'];
            }
            
            $queryData['resultType'] = "numRows";
            return $this->specificRow($queryData);
        }

        public function getEmpPunchesByDate($param = []){
			$dateCondition = '';
			if(!empty($param['from_date'])){$dateCondition .= "AND DATE(al.punch_date) >= '".$param['from_date']."'";}
			if(!empty($param['to_date'])){$dateCondition .= "AND DATE(al.punch_date) <= '".$param['to_date']."'";}
			
            $data['tableName'] = $this->empMaster;
            $data['select'] = "employee_master.emp_code,employee_master.emp_name";
            $data['select'] .= ",( 
									SELECT GROUP_CONCAT(al.punch_date) FROM attendance_log as al WHERE al.emp_id = employee_master.id AND al.is_delete=0 ".$dateCondition." 
								) as punch_date,";
								
            if(!empty($param['emp_id'])){$data['where']['employee_master.id'] = $param['emp_id'];}
			$data['where']['employee_master.is_active'] = 1;
			$data['where']['employee_master.attendance_status'] = 1;
            
			// 20-05-2024
			if(!in_array($this->userRole,[1,-1,3])):
				$data['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id) > 0 OR employee_master.id = '.$this->loginId.')';
			endif;
			
			if(!empty($param['group_by'])):
                $data['group_by'][] = $param['group_by'];
            endif;

            if(!empty($param['having'])):
                $data['having'][] = $param['having'];
            endif;
			
            return $this->rows($data);
        }

        public function getPunchByDate($param = []){
            $data['tableName'] = $this->attendance_log;
            $data['select'] = "attendance_log.*,employee_master.emp_code,employee_master.emp_name, project_master.project_name, shift_master.shift_name, shift_master.late_in, shift_master.late_fine, shift_master.shift_start";
			
            if(!empty($param['count'])){ $data['select'] .= ",count(attendance_log.id) as count_punch"; }
			
			$data['leftJoin']['employee_master'] = "employee_master.id = attendance_log.emp_id AND employee_master.is_active = 1 AND employee_master.attendance_status = 1";
            $data['leftJoin']['project_master'] = "project_master.id = attendance_log.project_id";
			$data['leftJoin']['shift_master'] = "shift_master.id = attendance_log.shift_id";
			
			$data['where']['employee_master.is_active'] = 1;
			$data['where']['employee_master.attendance_status'] = 1;
			
            if(!empty($param['from_date'])){$data['where']['DATE(attendance_log.punch_date) >= '] = $param['from_date'];}
			
            if(!empty($param['to_date'])){$data['where']['DATE(attendance_log.punch_date) <= '] = $param['to_date'];}
			
            if(!empty($param['report_date'])){$data['where']['DATE(attendance_log.punch_date)'] = $param['report_date'];}
			
            if(!empty($param['emp_id'])){$data['where']['attendance_log.emp_id'] = $param['emp_id'];}
            
			
			if(!in_array($this->userRole,[-1,1,2,3])):
				$data['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id) > 0 OR employee_master.id = '.$this->loginId.')';
			endif;
			
			if(!empty($param['group_by'])):
                $data['group_by'][] = $param['group_by'];
            endif;

            if(!empty($param['having'])):
                $data['having'][] = $param['having'];
            endif;
			
			$data['order_by']['attendance_log.punch_date'] = 'ASC';

            $result = $this->rows($data);
			//$this->printQuery();
			return $result;
        }

		public function getMonthlyAttendance($param = []){
            $queryData['tableName'] = $this->attendance_log;
            $queryData['select'] = "attendance_log.id, attendance_log.punch_type, attendance_log.type, attendance_log.emp_id, attendance_log.attendance_date, attendance_log.punch_date, attendance_log.start_at, attendance_log.img_file, attendance_log.start_location, attendance_log.loc_add, attendance_log.approve_by, attendance_log.approve_at, attendance_log.notes, attendance_log.project_id, attendance_log.attendance_status, attendance_log.created_by, emp.emp_code,emp.emp_name,employee_master.emp_name as approve_name, IFNULL(pm.lat_lng,'') as lat_lng, IFNULL(pm.site_add,'') as site_add";
            $data['leftJoin']['employee_master emp'] = "emp.id = attendance_log.emp_id";
            $data['leftJoin']['employee_master'] = "employee_master.id = attendance_log.approve_by";
            $data['leftJoin']['project_master hq'] = "pm.id = emp.quarter_id";
			
			if(empty($data['status'])) {  $data['where']['attendance_log.approve_by'] = 0; }
			elseif($data['status'] == 1){
				$queryData['customWhere'][] = "MONTH(attendance_log.punch_date) = ".date('m')." AND YEAR(attendance_log.punch_date) = ".date('Y');
				$queryData['where']['attendance_log.approve_by > '] = 0;
				$queryData['customWhere'][] = 'attendance_log.approve_by = attendance_log.emp_id';
			}
			elseif($data['status'] == 2){
				$queryData['customWhere'][] = "MONTH(attendance_log.punch_date) = ".date('m')." AND YEAR(attendance_log.punch_date) = ".date('Y');
				$queryData['where']['attendance_log.approve_by > '] = 0;
				$queryData['where']['attendance_log.punch_type != '] = 5;
				$queryData['customWhere'][] = 'attendance_log.approve_by != attendance_log.emp_id';
			}
			elseif($data['status'] == 5){
				$queryData['customWhere'][] = "MONTH(attendance_log.punch_date) = ".date('m')." AND YEAR(attendance_log.punch_date) = ".date('Y');
				$queryData['where']['attendance_log.punch_type'] = 5;
			}
			
			if(!in_array($this->userRole,[1,-1,3])):
				$queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
			endif;

            if(!empty($param['emp_id'])) { $data['where']['employee_master.id'] = $param['emp_id']; }
            return $this->rows($data);
        }
		
		public function getAttendanceStats($param = []){
            $data['tableName'] = $this->empMaster;
            $data['select'] = "employee_master.*,DATE(aLog.punch_date) as punch_date,lm.start_date";

            $data['leftJoin']['(SELECT punch_date,emp_id FROM attendance_log WHERE is_delete = 0 AND DATE(punch_date) >= "'.$param['month'].'" AND MONTH(punch_date) = "'.date('m',strtotime($param['month'])).'" AND YEAR(punch_date) = "'.date('Y',strtotime($param['month'])).'" AND attendance_log.approve_by != 0 GROUP BY DATE(punch_date),emp_id) as aLog'] = "employee_master.id = aLog.emp_id AND employee_master.is_active = 1";

            $data['leftJoin']['(SELECT start_date,emp_id FROM leave_master WHERE is_delete = 0 AND approve_by > 0 AND start_date >= "'.$param['month'].'" AND MONTH(start_date) = "'.date('m',strtotime($param['month'])).'" AND YEAR(start_date) = "'.date('Y',strtotime($param['month'])).'" GROUP BY start_date,emp_id) as lm'] = "employee_master.id = lm.emp_id AND employee_master.is_active = 1";

            $data['where']['employee_master.emp_role !='] = "-1";
			
			if(!in_array($this->userRole,[1,-1,3])):
				$data['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
			endif;
			
            $data['order_by']['employee_master.emp_code'] = "ASC";

            if(!empty($param['emp_id'])) { $data['where']['employee_master.id'] = $param['emp_id']; }
            return $this->rows($data);
        }
    
        public function approveAttendance($data){
            try{
                $this->db->trans_begin();

                $data['approve_by'] = $this->loginId;
                $data['approve_at'] = date("Y-m-d H:i:s");
                if(!isset($data['attendance_status'])): $data['attendance_status'] = 1; endif;
               
                $result = $this->store($this->attendance_log, $data,'Attendance Status');
                
                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }
	
	    public function confirmAttendance(){
            try{
                $this->db->trans_begin();
                $param['count'] = 1;
                $param['to_date'] = date("Y-m-d",strtotime(' -1 day'));
                $param['having'] = '(count_punch % 2) > 0';
                $param['group_by'] = 'attendance_log.emp_id,DATE(attendance_log.punch_date)';
                $attenData = $this->getPunchByDate($param);
                $cmInfo = $this->getCompanyInfo();
                foreach($attenData as $punch){ 
                        $end_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d").' 23:30:00'));
                    if(date("Y-m-d",strtotime($punch->punch_date)) != date("Y-m-d")){
                        $end_time = date("Y-m-d H:i:s",strtotime(formatDate($punch->punch_date,'Y-m-d').' '.$cmInfo->punch_out_time));
                    }

                    if(date("Y-m-d",strtotime($punch->punch_date)) != date("Y-m-d") OR (date("Y-m-d",strtotime($punch->punch_date)) == date("Y-m-d") && date('H:i') >= date("H:i",strtotime($cmInfo->punch_out_time)))){
                        $punchData = [
                            'id'=>'',
                            'type'=> 'OUT',
                            'punch_type' => 5,
                            'emp_id' => $punch->emp_id,
                            'attendance_date'=>formatDate($end_time,'Y-m-d'),
                            'punch_date'=>$end_time,
                            'start_at' =>$end_time,
                            'lat_lng' =>$punch->lat_lng,
                            'site_add' => $punch->site_add,
                            'start_location' =>$punch->lat_lng,
                            'loc_add' => $punch->site_add,
                            'attendance_status' => 1,
                            'approve_by' => 1,
                            'approve_at'=> date("Y-m-d H:i:s")
                        ];
						$this->store($this->attendance_log, $punchData);
						/*
                        $this->store($this->attendance_log, $punchData);
                        $locLog = Array();
                        $locLog['log_type'] = 2;
                        $locLog['emp_id'] = $punch->emp_id;
                        $locLog['log_time'] = $end_time;
                        $locLog['location'] = (!empty($punch->lat_lng) ? $punch->lat_lng :"");
                        $locLog['address'] = $punch->site_add;

                        $llResult = $this->saveLocationLog($locLog);*/
                    }
                }
                $result = ['status'=>1,'message'=>"Attendance Confirmed Successfully."];

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }
		
		public function getAttendanceData($data){
			$data['tableName'] = $this->attendance_log;
			$data['select'] = "attendance_log.*,emp.emp_code,emp.emp_name";
			$data['leftJoin']['employee_master emp'] = "emp.id = attendance_log.emp_id";
			$data['order_by']['attendance_log.id'] = 'DESC';
			
			if(!in_array($this->userRole,[1,-1,3])):
				$data['customWhere'][] = '(find_in_set("'.$this->loginId.'", emp.super_auth_id ) >0 OR emp.id = '.$this->loginId.')';
			endif;
			
			if(!empty($data['approve_by'])):
				$data['where']['attendance_log.approve_by'] = 0;
			endif;
			
			return $this->rows($data);
		}

		public function getTodayPunchData(){
			$data['tableName'] = $this->attendance_log;
			$data['customWhere'][] = "DATE(punch_date) = '".date('Y-m-d')."' && emp_id = ".$this->loginId." && type = 'OUT'";
			return $this->row($data);
		}

		public function getDatewiseAttendanceSummary($param){
			
			if(empty($param['from_date'])){$param['from_date'] = date('Y-m-d');}
            if(empty($param['to_date'])){$param['to_date'] = date('Y-m-d');}			
            if(empty($param['emp_id'])){$param['emp_id'] = $this->loginId;}		
			
			
			$queryData = "SELECT COUNT(DISTINCT DATE(alog.punch_date)) AS present_days FROM (SELECT attendance_date,punch_date,approve_by FROM `attendance_log` WHERE emp_id=".$param['emp_id']." AND attendance_date BETWEEN '".formatDate($param['from_date'],'Y-m-d')."' AND '".formatDate($param['to_date'],'Y-m-d')."' group by attendance_date ORDER by attendance_date) as alog WHERE alog.approve_by > 0";
			
			$result = $this->db->query($queryData)->row();
			//$this->printQuery();
			return $result;
		}
		
		// Migrate HQ ID FROM EMP ID
        public function migrateHQID($data){
            try{
                $this->db->trans_begin();
                
				if(!empty($data['emp_id'])){
                    $result = $this->edit($this->attendance_log, ['emp_id'=>$data['emp_id']], ['quarter_id'=>$data['quarter_id']], '');
                }
                

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }
	
	/********** End Attendance **********/
	
	/* Penalty Start */

    public function getPenaltyData($param = []){
        $data['tableName'] = $this->attendance_log;
        $data['select'] = "attendance_log.*,MIN(attendance_log.punch_date) AS first_punch_time,employee_master.emp_code,employee_master.emp_name, emp_designation.title, shift_master.shift_name, shift_master.late_in, shift_master.late_fine, shift_master.shift_start";
		
		$data['select'] .= ", TIMESTAMPDIFF(SECOND, CONCAT(attendance_log.attendance_date,' ',shift_start), attendance_log.punch_date) as late_seconds";
                
        $data['leftJoin']['employee_master'] = "employee_master.id = attendance_log.emp_id";
        $data['leftJoin']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        $data['leftJoin']['shift_master'] = "shift_master.id = attendance_log.shift_id";
        
        if(!empty($param['from_date'])){$data['where']['DATE(attendance_log.attendance_date) >= '] = $param['from_date'];}
        if(!empty($param['to_date'])){$data['where']['DATE(attendance_log.attendance_date) <= '] = $param['to_date'];}
        if(!empty($param['emp_id'])){$data['where']['attendance_log.emp_id'] = $param['emp_id'];}
        if(!empty($param['emp_designation'])){$data['where']['emp_designation.id'] = $param['emp_designation'];}
        
		$data['where']['employee_master.is_active'] = 1;
		$data['where']['employee_master.attendance_status'] = 1;
		$data['customWhere'][] = 'shift_master.shift_start IS NOT NULL';
		$data['having'][] = "late_seconds > IFNULL((shift_master.late_in*60),0)";
        $data['group_by'][] = 'attendance_log.emp_id, attendance_log.attendance_date';
        $data['order_by']['attendance_log.punch_date'] = 'ASC';

        $result = $this->rows($data);
		//$this->printQuery();
        return $result;
    }

    public function savePenalty($data){  
        try{
            $this->db->trans_begin();

            foreach($data['penalty'] as $row):
                $row['penalty_approve_by'] = $this->loginId;
                $row['penalty_approve_at'] = date("Y-m-d H:i:s");
                $result = $this->store('attendance_log',$row,'Penalty');
            endforeach;
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    /* Penalty End */
}
?>