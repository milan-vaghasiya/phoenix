<?php
class Attendance extends MY_Controller{
    private $indexPage = "hr/attendance/index";
    private $aReport = "hr/attendance/attendance_report";
    private $attend_index = "hr/attendance/attend_index"; 
    private $monthlyAttendance = "hr/attendance/month_attendance";  
    private $manualAttendance = "hr/attendance/manual_attendance";
    private $payroll = "hr/attendance/payroll";
	private $penalty = "hr/attendance/penalty";
	
	public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Attendance";
		$this->data['headData']->controller = "hr/attendance";
		//$this->data['headData']->pageUrl = "hr/attendance";		
	}
	
	public function index(){
		$this->data['empList'] = $this->employee->getEmployeeList();
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function attendanceReport(){
		$this->data['empList'] = $this->employee->getEmployeeList();
		$this->data['zoneList'] = [];//$this->configuration->getSalesZoneList();
        $this->load->view($this->aReport,$this->data);
    }
	
    public function getAttendanceReport1(){
        $data = $this->input->post();
		$report_date = '';
		if(!empty($data))
		{
			$empAttendanceLog = $this->attendance->getEmpPunchesByDate($data);
			if(!empty($empAttendanceLog))
			{
				$dateWisePunches = array_column($empAttendanceLog, 'punch_date');
				if(!empty($dateWisePunches[0]))
				{
					$empPunches = explode(',',$dateWisePunches[0]);
					//print_r('<pre>');
					print_r($empPunches);
				}
			}
			exit;
			$empTable = "";
			if(!empty($mpData))
			{
				foreach($mpData as $row):
					$empPunches = $row->punch_date; $allPunches = "";			
					if(!empty($empPunches))
					{
						$empPunches = explode(',',$empPunches);						
						$ap = Array();
						foreach($empPunches as $p){$ap[] = date("d-m-Y H:i:s",strtotime($p));}
						$allPunches = implode(', ',$ap);
					}
					 $imgFile = '';
            	    if(!empty($row->img_file)):
            	        $imgPath = base_url('assets/uploads/attendance_log/'.$row->img_file);
						$imgFile='<div class="picture-item" >
                            <a href="'.$imgPath.'" class="lightbox" >
                                <img src="'.$imgPath.'" alt="" class="img-fluid"  width="20" height="20"   style="border-radius:0%;border: 0px solid #ccc;padding:3px;"/>
                            </a> 
                            </div> ';
            		endif;
					$empTable .= '<tr>
                                    <td>'.$row->emp_code.'</td>
                                    <td>'.$row->emp_name.'</td>
                                    <td>'.$row->type.'</td>
                                    <td>'.$allPunches.'</td>
                                    <td class="text-wrap text-left">'.$row->loc_add.'</td>
                                    <td>'.$imgFile.'</td>
                                </tr>';
				endforeach;
				$this->printJson(['status'=>1,'tbody'=>$empTable]);
			}else{
				$this->printJson(['status'=>1,'tbody'=>""]);
			}
		}
	}
    
	public function getAttendanceReport($jsonData=''){
        if(!empty($jsonData)){$postData = (Array) decodeURL($jsonData);}
        else{$postData = $this->input->post();}
		
		$postData['attendance_status'] = 1;
		$postData['is_active'] = 1;
		$postData['from_date'] = (!empty($postData['from_date']) ? formatDate($postData['from_date'],'Y-m-d') : date('Y-m-d'));
		$postData['to_date'] = (!empty($postData['to_date']) ? formatDate($postData['to_date'],'Y-m-d') : date('Y-m-d'));
		
        $empData = $this->employee->getEmployeeList($postData);
		
        $lastDay = intVal(date('d',strtotime($postData['to_date'])));
        $thead='<tr style="background:#dddddd;">';
			$thead.='<th style="width:50px;">Code</th>';
			$thead.='<th>Emp Name</th>';
			$thead.='<th style="width:120px;">Designation</th>';
			$thead.='<th>Project</th>';
			$thead.='<th>Shift</th>';
			$thead.='<th class="text-center">Date</th>';
			$thead.='<th class="text-center">Status</th>';
			$thead.='<th class="text-center">Penalty</th>';
			$thead.='<th class="text-center">In Time</th>';
			$thead.='<th class="text-center">Break Start</th>';
			$thead.='<th class="text-center">Break End</th>';
			$thead.='<th class="text-center">Out Time</th>';
			$thead.='<th class="text-center">WH</th>';
        $thead.='</tr>';  
       
        $empArray = array_reduce($empData, function($emp, $employee) {
            $emp[$employee->emp_name][] = $employee;
            return $emp;
        }, []);
		
		$begin = new DateTime($postData['from_date']);
		$end = new DateTime($postData['to_date']);
		$end = $end->modify( '+1 day' ); 
		
		$interval = new DateInterval('P1D');
		$dateRange = new DatePeriod($begin, $interval ,$end);
		
        $tbody='';$i=0;$late = 0;$lateFine = 0;
        foreach($empData as $emp):
            $i++;
            
			$minLimitPerDay = 3600; // 1 Hour
			$hdLimit = 14400; // 4 Hour
            
            foreach($dateRange as $date):
				
				$currentDate =  date("Y-m-d",strtotime($date->format("Y-m-d")));
				$currentDay =  date("D",strtotime($date->format("Y-m-d")));
			
				$tbody.='<tr>';
				$tbody.='<td class="text-center" style="vertical-align:middle;">'.$emp->emp_code.'</td>';
				$tbody.='<td style="vertical-align:middle;" >'.$emp->emp_name.'</td>';
				$tbody.='<td style="vertical-align:middle;" >'.$emp->emp_designation.'</td>';
                
				$status=""; $class="text-danger";$punchDates = array();$whRow='';$shift_name=[];
				$empAttendanceLog = $this->attendance->getPunchByDate(['emp_id' => $emp->id,'from_date' => $currentDate,'to_date' => $currentDate]);
				
				$empPunches = array_column($empAttendanceLog, 'punch_date');
				$empPunches = sortDates($empPunches,'ASC');				
				
				$late = false;$lateFine = 0;
				$lateGraceTimes = array_column($empAttendanceLog, 'late_in');
				$shiftStarts = array_column($empAttendanceLog, 'shift_start');
				$lateFines = array_column($empAttendanceLog, 'penalty_amt');
				$shift_name = array_column($empAttendanceLog, 'shift_name');
				$project_name = array_column($empAttendanceLog, 'project_name');
				
				
				$shiftStart = (!empty($shiftStarts[0]) ? date('Y-m-d H:i:s',strtotime($currentDate.' '.$shiftStarts[0])) : "00-00-00 00:00:00");
				$lateGraceTime = (!empty($lateGraceTimes[0]) ? ($lateGraceTimes[0] * 60) : 0);
				if(!empty($empPunches[0]) AND (strtotime($shiftStart) + $lateGraceTime) < strtotime($empPunches[0])){ $late = true; }
				
				$t=1;$wph = Array();$idx=0;$stay_time=0;$twh = 0;$wh=0;$ot=0;$present_status = 'P';$punches = Array();
				foreach($empPunches as $punch)
				{
					$punches[]= date("H:i:s", strtotime($punch));
					$wph[$idx][]=strtotime($punch);
					if($t%2 == 0){$stay_time += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
					$t++;
				}
				$wh = $stay_time;
				if(!empty($punches[0]) AND $currentDate == date('Y-m-d')):
					$status = (($late) ? "L-P" : "P");
					$class = "text-success";
				else:
					if($wh >= $hdLimit):
						$status = (($late) ? "L-P" : "P");
						$class = "text-success";
					elseif(($wh <= 0) OR ($wh > 0 AND $wh < $minLimitPerDay)):
						$status = "A";$late = false;
						$class="text-danger";
						
						 // Check Leave if Absent
                        if($wh <= 0):
                            $leaveData = $this->leave->checkLeaveDate(['emp_id' => $emp->id,'status' => 2,'from_date' => $currentDate,'to_date' => $currentDate]);
                            if($leaveData->leave_count > 0):
                                $status = "ON-L";
                            endif;
                        endif;
						
					elseif($wh >= $minLimitPerDay AND $wh < $hdLimit):
						$status = (($late) ? "L-HD" : "HD");
						$class = "text-info";
					endif;
				endif;
				
                if(!empty($emp->week_off) AND $currentDay == $emp->week_off){
					if($status == "A"){$status = "W";$class = "bg-light text-dark";}
                    if($status == "P"){$status = "WP";$class = "text-success";}
                    if($status == "HD"){$status = "W-HD";$class = "text-success";}
                    if($late){$status = "L-W";}
                }
				
				if($late){ $lateFine = (!empty($lateFines[0]) ? $lateFines[0] : 0);$class="text-danger font-bold"; }
				
				if($wh > 0){ $whRow = '<td class="text-center">'.s2hi($wh).'</td>'; }
				else{ $whRow = '<td class="text-center"> - </td>'; }
				
				$tbody .= '<td class="text-center" style="">'.(!empty($project_name[0]) ? $project_name[0] : "").'</td>';
				$tbody .= '<td class="text-center" style="">'.(!empty($shift_name[0]) ? $shift_name[0] : '').' ('.(!empty($shiftStarts[0]) ? $shiftStarts[0] : '').')</td>';
				$tbody .= '<td class="text-center" style="">'.formatDate($currentDate,'d-m-Y').'</td>';
				$tbody .= '<td class="text-center '.$class.'" style="">'.$status.'</td>';
				$tbody .= '<td class="text-center" style="vertical-align:middle;" >'.(!empty($lateFine) ? $lateFine : '').'</td>';
				$tbody .= '<td class="text-center" style="vertical-align:middle;" >'.(!empty($punches[0]) ? $punches[0] : '').'</td>';
				$tbody .= '<td class="text-center" style="vertical-align:middle;" >'.(!empty($punches[1]) ? $punches[1] : '').'</td>';
				$tbody .= '<td class="text-center" style="vertical-align:middle;" >'.(!empty($punches[2]) ? $punches[2] : '').'</td>';
				$tbody .= '<td class="text-center" style="vertical-align:middle;" >'.(!empty($punches[3]) ? $punches[3] : '').'</td>';
				$tbody .= $whRow;
				$tbody .= '</tr>';
            endforeach;
			
        endforeach;
        
        $reportTitle = 'Attendance Report';
        $report_date = $postData['from_date'].' to '.formatDate($postData['to_date'],'d-m-Y');
		$logo = base_url('assets/images/logo.png');

        $pdfData = '<table id="attendanceTable" class="table table-bordered itemList" repeat_header="1">
                        <thead class="thead-info" id="theadData">'.$thead.'</thead>
                        <tbody id="tbodyData">'.$tbody.'</tbody>
                    </table>';

                
        $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="text-uppercase text-left"><img src="'.$logo.'" class="img" style="height:30px;"></td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                            <td class="text-uppercase text-right" style="font-size:0.8rem;width:30%">Date : '.$report_date.'</td>
                        </tr>
                    </table>';
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                    <tr>
                        <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                        <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                    </tr>
                </table>';

        if(!empty($postData['file_type']) && $postData['file_type'] == 'PDF')
        {
            $mpdf = new \Mpdf\Mpdf();
            $pdfFileName = 'AttendanceReport_'.str_replace(["/","-"],"_",date('d-m-Y')).'.pdf';          
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('P','','','','',5,5,15,10,3,3,'','','','','','','','','','A4-P');
            $mpdf->WriteHTML($pdfData);	
            ob_clean();	
            $mpdf->Output($pdfFileName, 'I');
        } else { 
            $this->printJson(['status'=>1,'thead'=>$thead, 'tbody'=>$tbody]); 
        }
	}

    public function getDailyAttendance(){
        $data = $this->input->post();
		$report_date = '';
		if(!empty($data))
		{
			$mpData = $this->attendance->getPunchByDate($data);
			$empTable = "";
			if(!empty($mpData))
			{
				foreach($mpData as $row):
					$empPunches = $row->punch_date; $allPunches = "";			
					if(!empty($empPunches))
					{
						$empPunches = explode(',',$empPunches);						
						$ap = Array();
						foreach($empPunches as $p){$ap[] = date("d-m-Y H:i:s",strtotime($p));}
						$allPunches = implode(', ',$ap);
					}
					 $imgFile = '';
            	    if(!empty($row->img_file)):
            	        $imgPath = base_url('assets/uploads/attendance_log/'.$row->img_file);
						$imgFile='<div class="picture-item" >
                            <a href="'.$imgPath.'" class="lightbox" >
                                <img src="'.$imgPath.'" alt="" class="img-fluid"  width="20" height="20"   style="border-radius:0%;border: 0px solid #ccc;padding:3px;"/>
                            </a> 
                            </div> ';
            		endif;
					$empTable .= '<tr>
                                    <td>'.$row->emp_code.'</td>
                                    <td>'.$row->emp_name.'</td>
                                    <td>'.$row->type.'</td>
                                    <td>'.$allPunches.'</td>
                                    <td class="text-wrap text-left">'.$row->loc_add.'</td>
                                    <td>'.$imgFile.'</td>
                                </tr>';
				endforeach;
				$this->printJson(['status'=>1,'tbody'=>$empTable]);
			}else{
				$this->printJson(['status'=>1,'tbody'=>""]);
			}
		}
	}

	public function attendanceIndex(){
        $this->data['tableHeader'] = getHrDtHeader('attendance');
        $this->load->view($this->attend_index,$this->data);
    }
	
	public function getDTRows(){
        $data = $this->input->post(); 
        $result = $this->attendance->getAttendanceDTRows($data);
		
        $sendData = array();$i=($data['start']+1);
		foreach($result['data'] as $row):
			$row->sr_no = $i++;
			$row->distance = (!empty($row->start_location) AND !empty($row->lat_lng)) ? getDistanceOpt($row->start_location,$row->lat_lng) : 'ERROR';
			$sendData[] = getAttendanceData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addManualAttendence(){
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->data['projectList'] = $this->project->getProjectList(['is_active'=>0]);
        $this->load->view($this->manualAttendance,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();
        if(empty($data['type'])){
			$errorMessage['type'] = "Type is required.";
        }
        if(empty($data['emp_id'])){
			$errorMessage['emp_id'] = "Employee is required.";
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:

            $data['punch_date']  = ($data['attendance_date'] . " " .$data['punch_date']);
            $data['approve_by'] = $this->loginId;
            $data['approve_at'] = date('Y-m-d H:i:s');
			$data['attendance_status'] = 1;

            $data['shift_id'] = 1;
			$empInfo = $this->employee->getEmployee(['id'=>$data['emp_id']]);
			if(!empty($empInfo->emp_type) AND $empInfo->emp_type == 1)
			{
				$projectInfo = $this->project->getProject(['id'=>$data['project_id']]);
				$data['shift_id'] = ((!empty($projectInfo->shift_id)) ? $projectInfo->shift_id : 1);
			}
			elseif(!empty($empInfo->emp_type) AND $empInfo->emp_type == 2)
			{
				$empInfo = $this->employee->getEmployee(['id'=>$data['emp_id']]);
				$data['shift_id'] = ((!empty($empInfo->shift_id)) ? $empInfo->shift_id : 1);
			}

			if($this->attendance->checkDuplicateAttendance($data) > 0):
                $this->printJson(['status'=>0,'message'=>"Attendance already added."]);
            else:
                $this->printJson($this->attendance->saveAttendance($data));
            endif;
        endif;
    }

    public function editManualAttendence(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->attendance->getManualAttendanceData($data);
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->data['projectList'] = $this->project->getProjectList(['is_active'=>0]);
        $this->load->view($this->manualAttendance,$this->data);
    }

    public function deleteManualAttendence(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->attendance->deleteManualAttendance($id));
        endif;
    }

    public function approveAttendance(){
		$data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->attendance->approveAttendance($data));
		endif;
	}

	public function confirmAttendance(){
		$data = $this->input->post();
		$this->printJson($this->attendance->confirmAttendance($data));
	}

	public function confirmVisit(){
		$data = $this->input->post();
		$this->printJson($this->visit->confirmVisit($data));
	}

 	/* Monthly Attendance Report */
    public function monthlyAttendance(){
		$this->load->view($this->monthlyAttendance, $this->data);
	}
    
    public function getMonthlyReport1($jsonData=''){
        if(!empty($jsonData)){$data = (Array) decodeURL($jsonData);}
        else{$data = $this->input->post();}
        $empData = $this->attendance->getMonthlyAttendance($data);

        $lastDay = intVal(date('t',strtotime($data['month'])));
        $thead='<tr style="background:#dddddd;"><th style="width:50px;">Code</th><th style="width:220px;">Emp Name</th>';
        for($d=1;$d<=$lastDay;$d++):	
            $thead.='<th class="text-center">'.$d.'</th>'; 
        endfor;
        
        $thead.='<th class="text-center">WP/WO</th>';
        $thead.='<th class="text-center">Present <br> Days</th>';
        $thead.='<th class="text-center">Leave</th>';
        $thead.='<th class="text-center">Absent <br> Days</th>';    
        $thead.='<th class="text-center">Total <br> Days</th>';
        $thead.='</tr>';  
       
        $empArray = array_reduce($empData, function($emp, $employee) {
            $emp[$employee->emp_name][] = $employee;
            return $emp;
        }, []);

        $tbody='';$i=0;
        foreach($empArray as $emp=>$employee):
            $i++;
            $tbody.='<tr>';
            $tbody.='<td class="text-center">'.$employee[0]->emp_code.'</td>';
            $tbody.='<td>'.$emp.'</td>';
            
            $totalDays = date("t",strtotime($data['month'])); 
            $holiday = countDayInMonth("Sunday",$data['month']);
            $totalDays -= $holiday; 
            $presentDays = 0;$absentDays = 0;$weekOff = 0;$wp = 0;$l = 0;
            
            for($d=1;$d<=$lastDay;$d++):
                
                $day=0; $text="A"; $class="bg-danger text-white";
                
                if(date("D",strtotime(date($d."-m-Y",strtotime($data['month'])))) == "Sun"){
                    if($text == "A"){$text = "W";}
                    if($text == "P"){$text = "WP";$wp++;$class = "text-success";}
                    if($text == "L"){$text = "WL";$wp++;}
                    $class = "bg-light text-dark";
                    $weekOff ++;
                    $day = 0;
                }else{
                    $text = "";
                    $punch_array = array_column($employee,'punch_date');
                    $leave_array = array_column($employee,'leave_date');

                    $date = date("Y-m-".str_pad($d,2,0,STR_PAD_LEFT),strtotime($data['month']));

                    if(in_array($date,$punch_array)){
                        $text = "P"; $class="bg-success text-white"; $day = 1;
                    }else{
                        if(in_array($date,$leave_array)){
                            $text = "L"; $class="bg-info text-white";
                            $l++;
                        }else{
                            $text = "A"; $class="bg-danger text-white";
                        }
                    }
                }                
                $tbody .= '<td class="text-center '.$class.'">'.$text.'</td>';
                $presentDays += $day;
            endfor;     

            $absentDays = (($totalDays - $presentDays) > 0)?($totalDays - $presentDays):0;
            $tbody .= '<td class="text-center" style="width:45px;">'.$wp.'/'.$weekOff.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;">'.$presentDays.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;">'.$l.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;">'.$absentDays.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;">'.$totalDays.'</td>'; 
            $tbody .= '</tr>';
        endforeach;
        
        $reportTitle = 'Attendance Report';
        $report_date = $data['month'].' to '.date('t-m-Y',strtotime($data['month']));
		$logo = base_url('assets/images/logo.png');

        $pdfData = '<table id="attendanceTable" class="table table-bordered itemList" repeat_header="1">
                        <thead class="thead-info" id="theadData">'.$thead.'</thead>
                        <tbody id="tbodyData">'.$tbody.'</tbody>
                    </table>';

                
        $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="text-uppercase text-left"><img src="'.$logo.'" class="img" style="height:30px;"></td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                            <td class="text-uppercase text-right" style="font-size:0.8rem;width:30%">Date : '.$report_date.'</td>
                        </tr>
                    </table>';
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                    <tr>
                        <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                        <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                    </tr>
                </table>';

        if(!empty($data['file_type']) && $data['file_type'] == 'PDF')
        {
            $mpdf = new \Mpdf\Mpdf();
            $pdfFileName = 'AttendanceReport_'.str_replace(["/","-"],"_",date('d-m-Y')).'.pdf';          
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,15,10,3,3,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData);	
            ob_clean();	
            $mpdf->Output($pdfFileName, 'D');
        } else { 
            $this->printJson(['status'=>1,'thead'=>$thead, 'tbody'=>$tbody]); 
        }
	}
 
    public function getMonthlyReport($jsonData=''){
        if(!empty($jsonData)){$data = (Array) decodeURL($jsonData);}
        else{$data = $this->input->post();}
		
		$data['attendance_status'] = 1;
		$data['is_active'] = 1;
		$data['is_zone'] = 1;
        $empData = $this->employee->getEmployeeList($data);
		
        $lastDay = intVal(date('t',strtotime($data['month'])));
        
		$thead='<tr style="background:#dddddd;"><th style="width:50px;">Code</th><th style="">Emp Name</th><th>Designation</th>';
        
		for($d=1;$d<=$lastDay;$d++):	
            $thead.='<th class="text-center">'.$d.'</th>'; 
        endfor;
        
        //$thead.='<th class="text-center">WP/WO</th>';
        $thead.='<th class="text-center">Penalty</th>';
        $thead.='<th class="text-center">Present <br> Days</th>';
        $thead.='<th class="text-center">Leave</th>';
        $thead.='<th class="text-center">Absent <br> Days</th>';    
        $thead.='<th class="text-center">Total <br> Days</th>';
        $thead.='</tr>';  
       
        $empArray = array_reduce($empData, function($emp, $employee) {
            $emp[$employee->emp_name][] = $employee;
            return $emp;
        }, []);

        $tbody='';$i=0;$lCount = 0;
        foreach($empData as $emp):
            $i++;
            $tbody.='<tr>';
            $tbody.='<td class="text-center" style="vertical-align:middle;font-size:12px;" >'.$emp->emp_code.'</td>';
            $tbody.='<td style="vertical-align:middle;font-size:12px;" >'.$emp->emp_name.'</td>';
            $tbody.='<td style="vertical-align:middle;font-size:12px;" >'.$emp->emp_designation.'</td>';
            
            $totalDays = date("t",strtotime($data['month'])); 
            $holiday = countDayInMonth("Sunday",$data['month']);
            $totalDays -= $holiday; 
            $presentDays = 0;$absentDays = 0;$weekOff = 0;$hd = 0;$wp = 0;$leave = 0;$punchRow='';$whRow='';
			$minLimitPerDay = 3600; // 1 Hour
			$hdLimit = 14400; // 4 Hour
            $totalWH = 0;$lateFine = 0;
            for($d=1;$d<=$lastDay;$d++):
                
                $day=0; $text="A"; $class="bg-danger text-white";$punchDates = array();$statusText = '';
				$dt = str_pad($d, 2, '0', STR_PAD_LEFT);
				$currentDate = date('Y-m-'.$dt,strtotime($data['month']));				
				$dayName = date("D", strtotime($currentDate));
				$empAttendanceLog = $this->attendance->getPunchByDate(['emp_id' => $emp->id,'from_date' => $currentDate,'to_date' => $currentDate]);
				$empPunches = array_column($empAttendanceLog, 'punch_date');
				$empPunches = sortDates($empPunches,'ASC');
				
				$late = false;
				$lateGraceTimes = array_column($empAttendanceLog, 'late_in');
				$shiftStarts = array_column($empAttendanceLog, 'shift_start');
				$lateFines = array_column($empAttendanceLog, 'penalty_amt');
				
				$shiftStart = (!empty($shiftStarts[0]) ? date('Y-m-d H:i:s',strtotime($currentDate.' '.$shiftStarts[0])) : "00-00-00 00:00:00");
				$lateGraceTime = (!empty($lateGraceTimes[0]) ? ($lateGraceTimes[0] * 60) : 0);
				if(!empty($empPunches[0]) AND (strtotime($shiftStart) + $lateGraceTime) < strtotime($empPunches[0])){ $late = true; }
				
				$t=1;$wph = Array();$idx=0;$stay_time=0;$twh = 0;$wh=0;$ot=0;$present_status = 'P';$punches = Array();
				foreach($empPunches as $punch)
				{
					$punches[]= date("H:i:s", strtotime($punch));
					$wph[$idx][]=strtotime($punch);
					if($t%2 == 0){$stay_time += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
					$t++;
				}
				$wh = $stay_time;
				
				if($wh >= $hdLimit):
					$day = 1;
					$text = (($late) ? "L-P" : "P");
					$class = "text-success";
				elseif(($wh <= 0) OR ($wh > 0 AND $wh < $minLimitPerDay)):
					$day = 0;
					$text = "A";if($currentDate != date('Y-m-d')){$late = false;}
					$class="bg-danger text-white";
					// Check Leave if Absent
					if($wh <= 0):
						$leaveData = $this->leave->checkLeaveDate(['emp_id' => $emp->id,'status' => 2,'from_date' => $currentDate,'to_date' => $currentDate]);
						if($leaveData->leave_count > 0):
							$text = $leaveData->leave_amt == 'UNPAID' ? 'ON-LWP' : "ON-L";
							$leave++;
						endif;
					endif;
				elseif($wh >= $minLimitPerDay AND $wh < $hdLimit):
					$day = 0.5;
					$text = (($late) ? "L-HD" : "HD");$hd++;
					$class = "bg-info text-white";
				endif;			
				
				
                if(date("D",strtotime(date($d."-m-Y",strtotime($data['month'])))) == "Sun"){
					if($text == "A"){$text = "W";$class = "bg-light text-dark";}
                    if($text == "P"){$text = "WP";$wp++;$class = "bg-light-green text-dark";}
                    if($text == "HD"){$text = "W-HD";$wp++;$class = "bg-success text-white";}
                    if($late){$text = "L-W";$wp++;}
                    //$class = "bg-light text-dark";
                    $weekOff ++;
                    $day = 0;
                }
				
				if($late){ $lateFine += (!empty($lateFines[0]) ? $lateFines[0] : 0); $lCount++;}
				
				if($wh > 0)
				{
					$punchRow .= '<td colspan="2" class="text-center "><small>'.implode(' - ',$punches).'</small></td>';
					$whRow = '<td class="text-center '.$class.'" style="font-size:12px;"><small>'.s2hi($wh).'</small></td>';
					
					if($data['report_type'] == 1){
						$tbody .= '<td class="text-center '.$class.'" style="font-size:12px;">'.$text.'</td>';
					}else{
						$tbody .= $whRow;
					}
				}
				else
				{
					$punchRow .= '<td colspan="2" class="text-center '.$class.'"> - </td>';
					//$whRow .= '<td class="text-center '.$class.'"> - </td>';
					
					$tbody .= '<td class="text-center '.$class.'" style="font-size:12px;">'.$text.'</td>';
				}
				
                //$tbody .= '<td class="text-center '.$class.'" style="min-width:115px;">'.$text.'</td>';
				//$tbody .= $whRow;
                $presentDays += $day;
				$totalWH += $wh;
            endfor;
			
            $absentDays = (($totalDays - $presentDays) > 0)?($totalDays - $presentDays - $leave):0;
			if($data['report_type'] == 1){
				//$tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.$wp.'/'.$weekOff.'</td>';
				$tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.$lateFine.'</td>';
			}else{
				$tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.s2hi($totalWH).'</td>';
			}
            
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.$presentDays.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.$leave.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.$absentDays.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.$totalDays.'</td>'; 
            $tbody .= '</tr>';
			
			//$tbody .= '<tr>'.$punchRow.'</tr>';
			//$tbody .= '<tr>'.$whRow.'</tr>';
        endforeach;
        
        $reportTitle = 'Attendance Report';
        $report_date = $data['month'].' to '.date('t-m-Y',strtotime($data['month']));
		$logo = base_url('assets/images/logo.png');

        $pdfData = '<table id="attendanceTable" class="table table-bordered itemList" repeat_header="1">
                        <thead class="thead-info" id="theadData">'.$thead.'</thead>
                        <tbody id="tbodyData">'.$tbody.'</tbody>
                    </table>';

                
        $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="text-uppercase text-left"><img src="'.$logo.'" class="img" style="height:30px;"></td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                            <td class="text-uppercase text-right" style="font-size:0.8rem;width:30%">Date : '.$report_date.'</td>
                        </tr>
                    </table>';
					
					
					//<i class="fas fa-circle"></i>
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                    <tr>
                        <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                        <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                    </tr>
                </table>';

        if(!empty($data['file_type']) && $data['file_type'] == 'PDF')
        {
            $mpdf = new \Mpdf\Mpdf();
            $pdfFileName = 'AttendanceReport_'.str_replace(["/","-"],"_",date('d-m-Y')).'.pdf';          
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,15,10,3,3,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData);	
            ob_clean();	
            $mpdf->Output($pdfFileName, 'I');
        }elseif(!empty($data['file_type']) && $data['file_type'] == 'excel'){
            $pdfData = '<table id="attendanceTable" class="table table-bordered itemList" repeat_header="1" border="1">
                            <thead class="thead-info" id="theadData">'.$thead.'</thead>
                            <tbody id="tbodyData">'.$tbody.'</tbody>
                        </table>';
            $xls_filename='AttendanceReport_'.str_replace(["/","-"],"_",date('d-m-Y')).'.xls';        
										
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename='.$xls_filename);
			header('Pragma: no-cache');
			header('Expires: 0');
	
			echo $pdfData; exit;
        } else { 
            $this->printJson(['status'=>1,'thead'=>$thead, 'tbody'=>$tbody]); 
        }
	}

	/* Payroll Report */
    public function payroll(){
		$this->data['headData']->pageTitle = "Payroll";
        $this->load->view($this->payroll, $this->data);
    }

    public function getPayrollReport(){
        $data = $this->input->post();
		
		$data['attendance_status'] = 1;
		$data['is_active'] = 1;
        $empData = $this->employee->getEmployeeList($data);
        $lastDay = intVal(date('t',strtotime($data['month'])));
       
        $empArray = array_reduce($empData, function($emp, $employee) {
            $emp[$employee->emp_name][] = $employee;
            return $emp;
        }, []);

        $tbody=''; $i=0;
        foreach($empData as $emp):
            $i++;
            $tbody.='<tr>';
            $tbody.='<td style="vertical-align:middle;" >'.$emp->emp_name.'</td>';
            
            $totalDays = date("t",strtotime($data['month'])); 
            $holiday = countDayInMonth("Sunday",$data['month']);

            $totalDays -= $holiday; 

            $presentDays = 0;$absentDays = 0;$weekOff = 0;$hd = 0;$wp = 0;$punchRow='';$whRow='';$late=0;
			$dailyWage = $gross = $netSalary = $penalty = $late_fines = 0;

			$minLimitPerDay = 3600; // 1 Hour
			$hdLimit = 14400; // 4 Hour
            
            for($d=1;$d<=$lastDay;$d++):
                $day=0; $text="A"; $class="bg-danger text-white";$punchDates = array();$statusText = '';$paidLeave = 0;$unPaidleave = 0;
				$dt = str_pad($d, 2, '0', STR_PAD_LEFT);
				$currentDate = date('Y-m-'.$dt,strtotime($data['month']));				
				$dayName = date("D", strtotime($currentDate));
				$empAttendanceLog = $this->attendance->getPunchByDate(['emp_id' => $emp->id,'from_date' => $currentDate,'to_date' => $currentDate]);
				$empPunches = array_column($empAttendanceLog, 'punch_date');
				$empPunches = sortDates($empPunches,'ASC');
				$late_fines += array_sum(array_column($empAttendanceLog, 'penalty_amt'));
				
				$t=1;$wph = Array();$idx=0;$stay_time=0;$twh = 0;$wh=0;$punches = Array();$isLateForDay = false;
				foreach($empPunches as $key=>$punch)
				{
                    if(!empty($emp->shift_start)){
                        $inTime= date("H:i:s", strtotime($punch));
                        
                        $shift_start= strtotime($emp->shift_start);
                        $lateDay = date("H:i:s", strtotime($emp->shift_start . ' +'.$emp->late_in.' minutes'));
						
                        if($key == 0){
                            if ($inTime > $lateDay) {
                                $late++;
                            }
                        }
                    }
					$wph[$idx][]=strtotime($punch);
					if($t%2 == 0){$stay_time += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
					$t++;
				}
                

				$wh = $stay_time;
				if($wh >= $hdLimit):
				    $day = 1;
                elseif(($wh <= 0) OR ($wh > 0 AND $wh < $minLimitPerDay)):
					$day = 0;
					
					// Check Leave if Absent
					if($wh <= 0):
						$leaveData = $this->leave->checkLeaveDate(['emp_id' => $emp->id,'status' => 2,'from_date' => $currentDate,'to_date' => $currentDate]);
						if($leaveData->totalPaidLeave > 0):
                            $paidLeave++;
						endif;
                        if($leaveData->totalUnpaidLeave > 0):
                            $unPaidleave++;
						endif;
					endif;
				elseif($wh >= $minLimitPerDay AND $wh < $hdLimit):
					$day = 0.5; $hd++;
					$class = "bg-info text-white";
				endif;
                   
                if(date("D",strtotime(date($d."-m-Y",strtotime($data['month'])))) == "Sun"){
                    $weekOff ++;
                    $day = 0;
                }
                $presentDays += $day;
            endfor;

            //$absentDays = (($totalDays - $presentDays - $late) > 0)?($totalDays - $presentDays - $late):0;
			$absentDays = (($totalDays - $presentDays -$late - $paidLeave - $unPaidleave) > 0)?($totalDays - $presentDays - $late -$paidLeave - $unPaidleave):0;
            if($emp->salary_duration == "Monthly"){
                $dailyWage = $emp->sal_amt / $totalDays;
            }elseif($emp->salary_duration == "Hourly"){
                $dailyWage = $emp->sal_amt;
            }
            //$gross = ($presentDays + $late) * $dailyWage;
			$gross = ($presentDays + $late + $paidLeave) * $dailyWage;
            //$penalty = $late * $emp->late_fine;
			$penalty = $late_fines;
            $netSalary = $gross - $penalty;
            $netSalary = max(0, $netSalary);

            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;">'.$lastDay.'</td>'; 
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;">'.$weekOff.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;">'.$totalDays.'</td>'; 
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;">'.$presentDays.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;">'.$late.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;">'.$absentDays.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;">'.$paidLeave.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;">'.$unPaidleave.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;">'.round($dailyWage,2).'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;">'.round($gross,2).'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;">'.round($penalty,2) .'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;">'.round($netSalary,2).'</td>';
            $tbody .= '</tr>';
        endforeach;
     
        $this->printJson(['status'=>1, 'tbody'=>$tbody]); 
	}
	
	/* Penalty Start */
    public function penalty(){
        $this->data['DT_TABLE'] = true;
		$this->data['headData']->pageTitle = "Penalty";
		$this->data['empList'] = $this->employee->getEmployeeList();
        $this->data['designationList'] = $this->designation->getDesignations();
        $this->load->view($this->penalty,$this->data);
    }

    public function getPenaltyData(){
        $data = $this->input->post();
        $result = $this->attendance->getPenaltyData($data);
		//print_r('<pre>');print_r($result);exit();
        $tbody = '';$i=1; $lateDiff=0;
        foreach($result as $row):
            if(empty($row->penalty_approve_by)){
                if (!empty($row->shift_start)) {
                    //$inTime = date("H:i:s", strtotime($row->first_punch_time));

					//$shiftStart = date('H:i:s',strtotime($row->shift_start));
					
                    //$lateDay = strtotime($row->shift_start . ' +' . $row->late_in . ' minutes');

                    //if ($inTime > date("H:i:s", $lateDay)) {
						
                        //$lateDiff = strtotime($inTime) - strtotime($shiftStart);
						
						$tbody .= '<tr>
							<td>'.$i.'</td>
							<td>'.$row->emp_code.'</td>
							<td>'.$row->emp_name.'</td>
							<td>'.$row->title.'</td>
							<td>'.$row->shift_name.'</td>
							<td>'.formatDate($row->attendance_date).'</td>
							<td>'.date("H:i:s", strtotime($row->first_punch_time)).'</td>
							<td>'.($row->shift_start).'</td>
							<td>'.s2hi($row->late_seconds).'</td>
							<td>  
								<input type="text" name="penalty['.$i.'][penalty_amt]" class="floatOnly form-control" value="'.$row->late_fine.'">
								<input type="hidden" name="penalty['.$i.'][id]" value="'.$row->id.'">
							</td>
						</tr>';
                    //} 
                } 
                $i++;
            }
        endforeach;

        $this->printJson(['status'=>1,'tbodyData'=>$tbody]);
    }

    public function savePenalty(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['penalty']))
            $errorMessage['general_error'] = "Penalty Data is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->attendance->savePenalty($data));
        endif;
    }

    /* Penalty END */
}
?>