<?php
class Attendance extends MY_ApiController{	

	public function __construct(){
		parent::__construct();
        $this->data['headData']->pageTitle = "Attendance";
        $this->data['headData']->pageUrl = "api/attendance";
        $this->data['headData']->base_url = base_url();
	}
	
	public function getEmployeeDetail(){
        $this->data['employeeDetail'] = $this->employee->getEmployeeData();
        $this->printJson(['status'=>1,'message'=>'Data Found.','data'=>$this->data]);
    }

    public function getAttendanceList(){
        $data = $this->input->post();
		$weekOffDays = ['MON'=>1,'TUE'=>2,'WED'=>3,'THU'=>4,'FRI'=>5,'SAT'=>6,'SUN'=>7];
		//$data['attendance_date'] = date('Y-m-d');
        $empData = $this->employee->getEmployeeDetail(['id'=>$this->loginId]);
        $logData = $this->attendance->getAttendanceList($data);
		$wo_day[] = (!empty($logData[0]->week_off) ? $weekOffDays[strtoupper($logData[0]->week_off)] : 4);
        // Columns to remove
        $columnsToRemove = ['start_at','approve_by','approve_at','notes','start_location', 'attendance_status','img_file','created_by','created_at','updated_at','updated_by','is_delete'];
        
		$empPunches = [];$response['warn_msg'] = "";
		if(!empty($logData)){foreach($logData as $lrow){$empPunches[] = $lrow->punch_date;}}
		$empPunches = sortDates($empPunches,'ASC');
		$shiftStart = date('Y-m-d H:i:s',strtotime(date('Y-m-d').' '.$empData->shift_start));
		$lateGraceTime = (!empty($empData->late_in) ? ($empData->late_in * 60) : 0);
		if(!empty($empPunches[0]) AND (strtotime($shiftStart) + $lateGraceTime) < strtotime($empPunches[0]))
		{
			$lateSec = strtotime($empPunches[0]) - strtotime($shiftStart);
			
			$response['warn_msg'] = "Ohh! You are late...😌 (".s2hi($lateSec).")";
		}
        // Remove columns from result
        $logData = array_map(function($row) use ($columnsToRemove) {
            $row->distance=2;
            $row->punch_date = date('d-m-Y h:i:s A',strtotime($row->punch_date));			
            $row->lat_long = $row->start_location;
            //$row->type = $row->in_out_flag;
            $row->img_file_path = base_url('/assets/uploads/attendance_log/'.((!empty($row->img_file))?$row->img_file:"user_default.png"));
            return array_diff_key((array) $row, array_flip($columnsToRemove));
        }, $logData);
		
		
		$response['empPunches'] = $empPunches;
		
		
		$summaryParam = ['from_date'=>date('Y-m-01'),'to_date'=>date('Y-m-t')];
        $response['summary'] = $this->attendance->getDatewiseAttendanceSummary($summaryParam);
        $response['summary']->total_days = intval(date('t',strtotime($data['from_date'])));
		
		if(strtotime($data['to_date']) >= strtotime(date('Y-m-d'))){ $data['to_date'] = date('Y-m-d'); $response['summary']->total_days = intval(date('d')); }
		
		$wo = getWeekOffs($data['from_date'], $data['to_date'], $wo_day);
		
		$response['summary']->present_days = intval($response['summary']->present_days);
		
		$response['summary']->absent_days = $response['summary']->total_days - $response['summary']->present_days - count($wo);
		
		
		$response['summary']->week_off = count($wo);
		$response['summary']->leave = 0;
		
		$response['logData'] = $logData;
		//print_r($wo);exit;
        $this->printJson(['status'=>1,'dataList'=>$response]);
    }

    public function getGeoLocation(){
        $data = $this->input->post();

        $data['start_location'] = ((!empty($data['s_lat']) AND !empty($data['s_lon'])) ? $data['s_lat'].','.$data['s_lon'] : "");
        unset($data['s_lat'],$data['s_lon']);
        $this->data['loc_add']='No Location Found';
		$adr = [];
        if(!empty($data['start_location'])):
		    $add = $this->callcUrl(['callURL'=>'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$data['start_location'].'&key='.GMAK]);
		    $add = (!empty($add) ? json_decode($add) : new StdClass);
		    //$this->data['full_address'] = (isset($add->results[0]) ? $add->results[0] : "");
		    $this->data['loc_add'] = (isset($add->results[0]->formatted_address) ? $add->results[0]->formatted_address : "");
			$adr = $add->results;
		endif;

        $this->printJson(['status'=>1,'message'=>'Data Found.','data'=>$this->data['loc_add']]);
    }

	public function saveAttendance(){
        $data = $this->input->post();

        if(!empty($_FILES['img_file'])):
            if($_FILES['img_file']['name'] != null || !empty($_FILES['img_file']['name'])):
                $this->load->library('upload');
                $_FILES['userfile']['name']     = $_FILES['img_file']['name'];
                $_FILES['userfile']['type']     = $_FILES['img_file']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['img_file']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['img_file']['error'];
                $_FILES['userfile']['size']     = $_FILES['img_file']['size'];
				
                $imagePath = realpath(APPPATH . '../assets/uploads/attendance_log/');
                $config = ['file_name' => $this->loginId."_".str_replace(' ','_',$data['type'])."_".time(),'allowed_types' => 'jpg|jpeg|png','max_size' => 1500,'overwrite' => FALSE, 'upload_path' => $imagePath];

                $this->upload->initialize($config);
                if (!$this->upload->do_upload()):
                    $errorMessage['img_file'] = $this->upload->display_errors();
                    $this->printJson(["status"=>0,"message"=>$errorMessage]);
                else:
                    $uploadData = $this->upload->data();
					
					$image_path = realpath(APPPATH . '../assets/uploads/attendance_log/'.$uploadData['file_name']);
					
					//$caption_text = $data['loc_add'];
					if(!empty($data['loc_add'])){ $this->add_caption_to_image($image_path, $data['loc_add']); }
					
                    $data['img_file'] = $uploadData['file_name'];
                endif;
            endif;
        endif;

        $data['emp_id'] = $this->loginId;
        $data['punch_type'] = 4;
        $data['attendance_date'] = date("Y-m-d");
        $data['punch_date'] = date("Y-m-d H:i:s");

        $data['start_location'] = ((!empty($data['s_lat']) AND !empty($data['s_lon'])) ? $data['s_lat'].','.$data['s_lon'] : "");
        unset($data['s_lat'],$data['s_lon']);
        
		/* 
		
		$data['loc_add']='';

        if(!empty($data['start_location'])):
		    $add = $this->callcUrl(['callURL'=>'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$data['start_location'].'&key='.GMAK]);
		    $add = (!empty($add) ? json_decode($add) : new StdClass);
		    $data['loc_add'] = (isset($add->results[0]->formatted_address) ? $add->results[0]->formatted_address : "");
		endif;
		
		*/
		
        $data['approve_by'] = $this->loginId;
		$data['approve_at'] = date('Y-m-d H:i:s');
		$data['attendance_status'] = 1;
		if(empty($data['emp_id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$data['shift_id'] = 1;
			$empInfo = $this->employee->getEmployee(['id'=>$this->loginId]);
			if(!empty($empInfo->emp_type) AND $empInfo->emp_type == 1)
			{
				$projectInfo = $this->project->getProject(['id'=>$data['project_id']]);
				$data['shift_id'] = ((!empty($projectInfo->shift_id)) ? $projectInfo->shift_id : 1);
			}
			elseif(!empty($empInfo->emp_type) AND $empInfo->emp_type == 2)
			{
				$empInfo = $this->employee->getEmployee(['id'=>$this->loginId]);
				$data['shift_id'] = ((!empty($empInfo->shift_id)) ? $empInfo->shift_id : 1);
			}
			$this->printJson($this->attendance->saveAttendance($data));
        endif;
    }
	
	public function add_caption_to_image($file_path, $caption, $align = 'center') {
        $font_path = realpath(APPPATH . '../assets/css/verdana.ttf'); // Ensure this path is correct
        $font_size = 70;
		$margin = 27;
		$line_spacing = 32;
		//$white = imagecolorallocate($img, 255, 255, 255);
		
		$img = imagecreatefromjpeg($file_path);
		//$img = imagerotate($img, -90, $white);
		$width = imagesx($img);
		$height = imagesy($img);

		$text_color = imagecolorallocate($img, 255, 255, 255);
		$bg_color = imagecolorallocatealpha($img, 0, 0, 0, 50); // semi-transparent

		$wrapped_text = $this->wrap_text($caption, $font_size, $font_path, $width - 2 * $margin);
		$lines = explode("\n", $wrapped_text);
		$text_height = count($lines) * ($font_size + $line_spacing) + $margin;

		// Draw background
		imagefilledrectangle($img, 0, $height - $text_height, $width, $height, $bg_color);

		// Draw text line by line with alignment
		$y = $height - $text_height + $font_size + 27;
		foreach ($lines as $line) {
			$bbox = imagettfbbox($font_size, 0, $font_path, $line);
			$text_width = $bbox[2] - $bbox[0];

			// Calculate x-position based on alignment
			switch ($align) {
				case 'left':
					$x = $margin;
					break;
				case 'right':
					$x = $width - $text_width - $margin;
					break;
				case 'center':
				default:
					$x = ($width - $text_width) / 2;
					break;
			}

			imagettftext($img, $font_size, 0, $x, $y, $text_color, $font_path, $line);
			$y += $font_size + $line_spacing;
		}
		
		//$img = imagerotate($img, 90, $white);
		imagejpeg($img, $file_path);
		imagedestroy($img);
		//imagedestroy($rotatedImg);
    }

    public function wrap_text($text, $font_size, $font_path, $max_width) {
        $words = explode(' ', $text);
        $line = '';
        $wrapped_text = '';

        foreach ($words as $word) {
            $test_line = $line . ' ' . $word;
            $box = imagettfbbox($font_size, 0, $font_path, $test_line);
            $line_width = $box[2] - $box[0];

            if ($line_width > $max_width && $line !== '') {
                $wrapped_text .= trim($line) . "\n";
                $line = $word;
            } else {
                $line = $test_line;
            }
        }

        $wrapped_text .= trim($line);
        return $wrapped_text;
    }
	
	public function getAttendanceReport($jsonData=''){
        if(!empty($jsonData)){$postData = (Array) decodeURL($jsonData);}
        else{$postData = $this->input->post();}
		
		$postData['emp_id'] = (!empty($postData['emp_id']) ? $postData['emp_id'] : $this->loginId);
		$postData['file_type'] = "PDF";
		$postData['attendance_status'] = 1;
		$postData['is_active'] = 1;
		$postData['from_date'] = (!empty($postData['from_date']) ? formatDate($postData['from_date'],'Y-m-d') : date('Y-m-d'));
		$postData['to_date'] = (!empty($postData['to_date']) ? formatDate($postData['to_date'],'Y-m-d') : date('Y-m-d'));
		$postData['appReport'] = "YES";
        $empData = $this->employee->getEmployeeList($postData);
		
        $lastDay = intVal(date('d',strtotime($postData['to_date'])));
        $thead='<tr style="background:#dddddd;">';
					//$thead.='<th style="width:50px;">Code</th>';
					//$thead.='<th>Designation</th>';
					$thead.='<th class="text-center">Date</th>';
					$thead.='<th class="text-center">Status</th>';
					$thead.='<th class="text-center">Penalty</th>';
					$thead.='<th class="text-center">In Time</th>';
					$thead.='<th class="text-center">Break Start</th>';
					$thead.='<th class="text-center">Break End</th>';
					$thead.='<th class="text-center">Out Time</th>';
					$thead.='<th class="text-center" style="width:80px;">WH</th>';
					$thead.='<th style="width:150px;">Project</th>';
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
				//$tbody.='<td class="text-center" style="vertical-align:middle;">'.$emp->emp_code.'</td>';
				//$tbody.='<td style="vertical-align:middle;" >'.$emp->emp_name.'<br><small>'.$emp->emp_designation.'</small></td>';
                
				$status="A"; $class="text-danger";$punchDates = array();$whRow='';
				$empAttendanceLog = $this->attendance->getPunchByDate(['emp_id' => $emp->id,'from_date' => $currentDate,'to_date' => $currentDate]);
				
				$empPunches = array_column($empAttendanceLog, 'punch_date');
				$empPunches = sortDates($empPunches,'ASC');
				
				$late = false;$lateFine = 0;
				$lateGraceTimes = array_column($empAttendanceLog, 'late_in');
				$shiftStarts = array_column($empAttendanceLog, 'shift_start');
				$lateFines = array_column($empAttendanceLog, 'late_fine');
				
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
						$status = "A";
						$late = false;
						$class="text-danger";
					elseif($wh >= $minLimitPerDay AND $wh < $hdLimit):
						$status = (($late) ? "L-HD" : "HD");
						$class = "text-info";
					endif;
				endif;
				
                if($currentDay == "Sun"){
					if($status == "A"){$status = "W";$class = "bg-light text-dark";}
                    if($status == "P"){$status = "WP";$class = "text-success";}
                    if($status == "HD"){$status = "W-HD";$class = "text-success";}
                    if($late){$status = "L-W";}
                }
				
				if($late){ $lateFine = (!empty($lateFines[0]) ? $lateFines[0] : 0); }
				
				if($wh > 0){ $whRow = '<td class="text-center">'.s2hi($wh).'</td>'; }
				else{ $whRow = '<td class="text-center"> - </td>'; }
				
				$tbody .= '<td class="text-center" style="">'.formatDate($currentDate,'d-m-Y').'</td>';
				$tbody .= '<td class="text-center '.$class.'" style="">'.$status.'</td>';
				$tbody .= '<td class="text-center" style="vertical-align:middle;" >'.(!empty($lateFine) ? $lateFine : '').'</td>';
				$tbody .= '<td class="text-center" style="vertical-align:middle;" >'.(!empty($punches[0]) ? $punches[0] : '').'</td>';
				$tbody .= '<td class="text-center" style="vertical-align:middle;" >'.(!empty($punches[1]) ? $punches[1] : '').'</td>';
				$tbody .= '<td class="text-center" style="vertical-align:middle;" >'.(!empty($punches[2]) ? $punches[2] : '').'</td>';
				$tbody .= '<td class="text-center" style="vertical-align:middle;" >'.(!empty($punches[3]) ? $punches[3] : '').'</td>';
				$tbody .= $whRow;
				$projectName = (!empty($empAttendanceLog[0]->project_name) ? $empAttendanceLog[0]->project_name : "");
				$tbody.='<td style="vertical-align:middle;" >'.$projectName.'</td>';
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
            //$mpdf = new \Mpdf\Mpdf();
			$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [220, 420]]);
            $pdfFileName = 'AttendanceReport_'.str_replace(["/","-"],"_",date('d-m-Y')).'.pdf';          
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            //$mpdf->AddPage('P','','','','',5,5,15,10,3,3,'','','','','','','','','','A5-P');
            $mpdf->AddPage('P','','','','',5,5,15,10,3,3);
            $mpdf->WriteHTML($pdfData);	
            ob_clean();	
            //$mpdf->Output($pdfFileName, 'I');
			
			$this->printJson(['status'=>1,'message'=>'Data Found.','data'=>base64_encode($mpdf->OutputBinaryData())]);
        } else { 
            $this->printJson(['status'=>1,'thead'=>$thead, 'tbody'=>$tbody]); 
        }
	}

}
?>