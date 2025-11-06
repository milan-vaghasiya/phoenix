<?php
class SiteTrans extends MY_ApiController{

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Site Management";
        $this->data['headData']->pageUrl = "api/siteTrans";
        $this->data['headData']->base_url = base_url();
    }

    public function getWorkDetail(){		
		$data = $this->input->post();
        $this->data['workDetail'] = $this->siteTrans->getWorkDetailList($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['workDetail']]);
    }

    public function getWorkDetailList(){
		$data = $this->input->post();
        $this->data['workDetailList'] = $this->siteTrans->getWorkDetailList($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['workDetailList']]);
    }

    public function addWorkDetail(){
        $data = $this->input->post();
		$this->data['lastRecord'] = [];
		$lastData = $this->siteTrans->getLastWorkDetail($data);
		if(!empty($lastData))
		{
			$data['trans_date'] = $lastData->trans_date;
			$data['result_type'] = "row";
			$this->data['lastRecord'] = $this->siteTrans->getWorkDetailList($data);
			unset($data['result_type']);
		}
        $data['select'] = "project_milestone.id, project_milestone.tower_name";
        $this->data['towerList'] = $this->project->getProjectTower($data);
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }

    public function saveBulkWorkDetail(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['type']))
            $errorMessage['type'] = "Work Type is required";
        if(empty($data['trans_date'])):
            $errorMessage['trans_date'] = "Date is required";
        elseif($data['trans_date'] > date('Y-m-d')):
            $errorMessage['trans_date'] = "Invalid Date";
        endif;
        
        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required";
        if(empty($data['work_data']))
            $errorMessage['work_data'] = "Work Detail is required";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			//$data['work_data'] = json_encode($data['work_data']);
            $this->printJson($this->siteTrans->saveBulkWorkDetail($data));
        endif;
    }

    public function deleteWorkDetail(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->siteTrans->deleteWorkDetail($data));
        endif;
    }
    
    public function getProjectMileStoneList(){
        $data = $this->input->post();
        $result = $this->project->getProjectDetails($data);
		
        $option = "<option value=''>Select Work Milestone</option>"; $i = 1;
        foreach($result as $row):
            $selected = (!empty($data['work_ref_id']) && $data['work_ref_id'] == $row->id)?"selected":"";
			$option .= '<option value="'.$row->id.'" '.$selected.'>'.$row->work_type.'</option>';
        endforeach;
        $this->printJson(['status'=>1,'options'=>$option]);
    }

    public function getProjectAgency(){
        $data = $this->input->post();
		
        $result = $this->siteTrans->getProjectAgencies($data);
		
        $option = "<option value=''>Select Agency</option>"; $i = 1;
        foreach($result as $row):
            $selected = (!empty($data['agency_id']) && $data['agency_id'] == $row->id)?"selected":"";
			$option .= '<option value="'.$row->id.'" '.$selected.'>'.$row->agency_name.'</option>';
        endforeach;
        $this->printJson(['status'=>1,'options'=>$option]);
    }

	/***** Labor Attendance ******/
	
    public function getDPRDetail(){		
		$data = $this->input->post();
        $this->data['laborAttendanceList'] = $this->siteTrans->getLaborAttendance($data);
        $this->data['machineryStatuseList'] = $this->siteTrans->getMachineryStatusList($data);
        $this->data['workDetailList'] = $this->siteTrans->getWorkDetailList($data);
        $this->data['complaintList'] = $this->siteTrans->getComplainList($data);
        $this->data['extraActivityList'] = $this->siteTrans->getExtraActivity($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }
	
	public function addLaborAttendance(){
        $data = $this->input->post();
        $data['tower'] = 1;$data['trans_date'] = date('Y-m-d');$this->data['lastRecord'] = [];
		$lastData = $this->siteTrans->getLastLaborAttendance($data);
		if(!empty($lastData))
		{
			$data['trans_date'] = $lastData->trans_date;
			$this->data['lastRecord'] = $this->siteTrans->getLaborAttendance($data);
		}
		$this->data['agencyList'] = $this->project->getProjectAgencyList($data);
		$this->data['laborCatList'] = $this->selectOption->getSelectOptionList(['type'=>2]);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }

    public function saveLaborAttendance(){
        $data = $this->input->post();
		
        if(!empty($data['agency']) && gettype($data['agency']) == "string"): $data['agency'] = json_decode($data['agency'],true); endif;
        if(!empty($data['staff']) && gettype($data['staff']) == "string"): $data['staff'] = json_decode($data['staff'],true); endif;
		
		//print_r($data['agency']);exit;
		
        $errorMessage = [];

        if(empty($data['trans_date'])):
            $errorMessage['trans_date'] = "Date is required";
        elseif($data['trans_date'] > date('Y-m-d')):
            $errorMessage['trans_date'] = "Invalid Date";
        endif;
        
        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->siteTrans->saveLaborAttendance($data));
        endif;
    }

    public function deleteLaborAttendance(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->siteTrans->deleteLaborAttendance($data));
        endif;
    }
    
    public function getLaborAttendanceList(){		
		$data = $this->input->post();
        $this->data['laborAttendanceList'] = $this->siteTrans->getLaborAttendance($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['laborAttendanceList']]);
    }
	
	/*** Machine Status */
	
	public function addMachineStatus(){
        $data = $this->input->post();
        $data['last_record'] = 1;
		$lastData = $this->siteTrans->getLastRecords($data);
		if(!empty($lastData))
		{
			$data['trans_date'] = $lastData->trans_date;
			$this->data['lastRecord'] = $this->siteTrans->getMachineryStatusList($data);
		}
		$this->data['machineList'] = $this->siteTrans->getProjectMachineList($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }

    public function saveMachineStatus(){
        $data = $this->input->post();
		
        if(!empty($data['id']) && gettype($data['id']) == "string"): $data['id'] = json_decode($data['id'],true); endif;
        if(!empty($data['machine_id']) && gettype($data['machine_id']) == "string"): $data['machine_id'] = json_decode($data['machine_id'],true); endif;
        if(!empty($data['qty']) && gettype($data['qty']) == "string"): $data['qty'] = json_decode($data['qty'],true); endif;
		
        $errorMessage = [];

        if(empty($data['trans_date'])):
            $errorMessage['trans_date'] = "Date is required";
        elseif($data['trans_date'] > date('Y-m-d')):
            $errorMessage['trans_date'] = "Invalid Date";
        endif;
        
        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required";
        if(empty($data['machine_id'])){
            $errorMessage['gen_error'] = "Machine required";
        }
		/*
		else{
            foreach($data['machine_id'] AS $key=>$machine_id){
                if(empty($data['qty'][$key])){
                    $errorMessage['qty'.$machine_id] = "Qty required";
                }
            }
        }
*/
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            // print_r($data);exit;
            $this->printJson($this->siteTrans->saveMachineStatus($data));
        endif;
    }

    public function deleteMachineStatus(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->siteTrans->deleteMachineStatus($data));
        endif;
    }
    
    public function getMachineStatusList(){		
		$data = $this->input->post();
        $this->data['machineryStatuseList'] = $this->siteTrans->getMachineryStatusList($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['machineryStatuseList']]);
    }
	
	/*** Complaint */
	
	public function addComplaint(){
        $data = $this->input->post();
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }

    public function saveComplaint(){
        $data = $this->input->post();
		
        $errorMessage = [];

        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required";
        if(empty($data['trans_date'])):
            $errorMessage['trans_date'] = "Date is required";
        elseif($data['trans_date'] > date('Y-m-d')):
            $errorMessage['trans_date'] = "Invalid Date";
        endif;
        
        if(empty($data['complain_note']))
            $errorMessage['complain_note'] = "Complaint is required";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$result = $this->siteTrans->saveComplain($data);
			$this->printJson($result);
        endif;
    }

    public function deleteComplaint(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->siteTrans->deleteComplain($data));
        endif;
    }
    
    public function getComplaint(){		
		$data = $this->input->post();
        $this->data['complaintList'] = $this->siteTrans->getComplainList($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['complaintList']]);
    }
	
	
	/*** Extra Activity */
	
	public function addExtraActivity(){
        $data = $this->input->post();
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }

    public function saveExtraActivity(){
        $data = $this->input->post();
		
        $errorMessage = [];

        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required";
        if(empty($data['trans_date'])):
            $errorMessage['trans_date'] = "Date is required";
        elseif($data['trans_date'] > date('Y-m-d')):
            $errorMessage['trans_date'] = "Invalid Date";
        endif;
        
        if(empty($data['activity']))
            $errorMessage['activity'] = "Activity is required";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$result = $this->siteTrans->saveExtraActivity($data);
			$this->printJson($result);
        endif;
    }

    public function deleteExtraActivity(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->siteTrans->deleteExtraActivity($data));
        endif;
    }
    
    public function getExtraActivity(){		
		$data = $this->input->post();
        $this->data['extraActivityList'] = $this->siteTrans->getExtraActivity($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['extraActivityList']]);
    }
	

	/***** Labor Attendance V2 By: JP@06082025 ******/
	
    public function getDPRDetail_v2(){		
		$data = $this->input->post();
        $this->data['laborAttendanceList'] = $this->siteTrans->getLaborAttendance($data);
        $this->data['machineryStatuseList'] = $this->siteTrans->getMachineryStatusList($data);
        $this->data['workDetailList'] = $this->siteTrans->getWorkDetailList($data);
        $this->data['complaintList'] = $this->siteTrans->getComplainList($data);
        $this->data['extraActivityList'] = $this->siteTrans->getExtraActivity($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }
	
	public function addLaborAttendance_v2(){
        $data = $this->input->post();
        $data['tower'] = 1;$data['trans_date'] = date('Y-m-d');$this->data['lastRecord'] = [];
		/*
		$lastData = $this->siteTrans->getLastLaborAttendance_v2($data);
		if(!empty($lastData))
		{
			$data['trans_date'] = $lastData->trans_date;
			$this->data['lastRecord'] = $this->siteTrans->getLaborAttendance_v2($data);
		}
		*/
		$this->data['agencyList'] = [];
		$data['select'] = "agency_work.agency_id, party_master.party_name as agency_name, agency_work.work_id";
		$data['workDetails'] = "YES";
		$agencyData = $this->project->getProjectAgencyList($data);
		
		if(!empty($agencyData)){
			foreach($agencyData as $row){
				if(!empty($row->labor_cat_ids)){
					$laborCatList = $this->selectOption->getLaborCategoriesAPP(['ids'=>$row->labor_cat_ids,'trans_date'=>$data['trans_date'],'project_id'=>$data['project_id'],'agency_id'=>$row->agency_id,'work_id'=>$row->work_id]);
					if(!empty($laborCatList)){
						$row->labor_category = $laborCatList;
					}
					else{$row->labor_category = [];}
				}
				else{$row->labor_category = [];}
				
				$row->agency_name = $row->agency_name. ' ('.$row->tower_name.' - '.$row->work_type.')';
				unset($row->labor_cat_ids);
				$this->data['agencyList'][] = $row;
			}
		}
		
		//print_r($agencyData);exit;
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }

    public function saveLaborAttendance_v2(){
        $data = $this->input->post();
		
        if(!empty($data['agency']) && gettype($data['agency']) == "string"): $data['agency'] = json_decode($data['agency'],true); endif;
        if(!empty($data['staff']) && gettype($data['staff']) == "string"): $data['staff'] = json_decode($data['staff'],true); endif;
		
        $errorMessage = [];

        if(empty($data['trans_date'])):
            $errorMessage['trans_date'] = "Date is required";
        elseif($data['trans_date'] > date('Y-m-d')):
            $errorMessage['trans_date'] = "Invalid Date";
        endif;
        
        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->siteTrans->saveLaborAttendance_v2($data));
        endif;
    }

    public function deleteLaborAttendance_v2(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->siteTrans->deleteLaborAttendance($data));
        endif;
    }
    
    public function getLaborAttendanceList_v2(){		
		$data = $this->input->post();
        $laData = $this->siteTrans->getLaborAttendance_v2($data);
		
		foreach ($laData as $item) {
			$item = (array) $item;
			$agencyId = $item['agency_id'];

			if (!isset($result[$agencyId])) {
				// Initialize agency group
				$result[$agencyId] = [
					"id" => $item["id"],
					"project_id" => $item["project_id"],
					"type" => $item["type"],
					"trans_date" => $item["trans_date"],
					"agency_id" => $item["agency_id"],
					"agency_name" => $item["agency_name"],
					"work_id" => $item["work_id"],
					"project_name" => $item["project_name"],
					"tower_name" => $item["tower_name"],
					"labor_category" => []
				];
			}

			// Append labor category
			$result[$agencyId]["labor_category"][] = [
				"lab_cat_id" => $item["labor_cat_id"],
				"labor_cat_name" => $item["labor_cat_name"],
				"present" => $item["present"]
			];
		}

		// Re-index result
		$this->data['laborAttendanceList'] = array_values($result);
		//print_r($this->data['laborAttendanceList']);exit;
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['laborAttendanceList']]);
    }
	
	public function getDprReport_v2(){
        $data = $this->input->post();		
        $errorMessage = [];

        if(empty($data['project_id'])){
            $errorMessage['project_id'] = "Project is required";
        }
        if(empty($data['trans_date'])){
            $errorMessage['trans_date'] = "Date is required";
        }
        if(!empty($errorMessage)){
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        }
        else{
			
			$this->data['data'] = $data;
			
            $data['trans_date'] = date('Y-m-d',strtotime($data['trans_date']));

            $this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
            $this->data['projectData'] = $projectData = $this->project->getProject(['id'=>$data['project_id']]);
            $this->data['trans_date'] = $data['trans_date'];

			$pdfData = '';
            if(!empty($projectData)){
                $this->data['machineList'] = $machineList = $this->siteTrans->getMachineList(['ids'=>$projectData->machine_ids]);
                $this->data['machineryStatuseList'] = $this->siteTrans->getMachineryStatusList($data);
                $this->data['workDetailList'] = $this->siteTrans->getWorkDetailList(['project_id'=>$data['project_id'],'trans_date'=>$data['trans_date'],'type'=>1]);
                $this->data['workPlanList'] = $this->siteTrans->getWorkDetailList(['project_id'=>$data['project_id'],'trans_date'=>$data['trans_date'],'type'=>2]);
                $this->data['complaintList'] = $this->siteTrans->getComplainList(['project_id'=>$data['project_id'],'trans_date'=>$data['trans_date'],'result_type'=>'row']);
                $this->data['extraActivityList'] = $this->siteTrans->getExtraActivity(['project_id'=>$data['project_id'],'trans_date'=>$data['trans_date'],'result_type'=>'row']);
                
                $this->data['weather_icon'] = base_url('assets/images/weather/cloudy.png');
                $logo = $this->data['logo'] = base_url('assets/images/logo.png');
                $this->data['letter_head'] = "";

                $this->data['stockData'] = $this->store->getItemWiseStockData(['location_id'=>$data['project_id'],'group_by'=>'item_id','trans_date'=>$data['trans_date']]);

                if($data['trans_date'] < "2025-08-13"):
                    $this->data['laborAttendanceList'] = $laborAttendanceList = $this->siteTrans->getLaborAttendance(['project_id'=>$data['project_id'],'trans_date'=>$data['trans_date']]);
                    $pdfData = $this->load->view('site_trans/dpr_print', $this->data, true);
                else:
                    $this->data['laborAttendanceList'] = $laborAttendanceList = $this->siteTrans->getLaborAttendanceDprData_v2(['project_id'=>$data['project_id'],'trans_date'=>$data['trans_date']]);
                    $pdfData = $this->load->view('site_trans/dpr_print_new', $this->data, true);
                endif;

                $htmlFooter = '<table class="table top-table" style="margin-top:0px;border-top:1px solid #545454;">
                                <tr>
                                    <th style="width:50%;" class="text-center">'.$companyData->company_name.'</th>
                                    <th style="width:50%;" class="text-center">'.$companyData->company_name.'</th>
                                </tr>
                                <tr>
                                    <td colspan="2" height="10"></td>
                                </tr>
                                <tr>
                                    <td class="text-center"><br>Prepared By</td>
                                    <td class="text-center"><br>Authorised By</td>
                                </tr>
                            </table>';
                $htmlFooter .= '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                                    <tr>
                                        <td style="width:25%;">Date : '.date("Y-m-d") . '</td>
                                        <td style="width:25%;"></td>
                                        <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                                    </tr>
                                </table>';

                $mpdf = new \Mpdf\Mpdf();
                $filePath = realpath(APPPATH . '../assets/uploads/dpr/');
                $pdfFileName = $filePath.'/xyz' . '.pdf';

                $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v'.time()));
                $mpdf->WriteHTML($stylesheet, 1);
                $mpdf->SetDisplayMode('fullpage');
                $mpdf->SetWatermarkImage($logo, 0.03, array(125, 30));
                $mpdf->showWatermarkImage = true;
                $mpdf->SetHTMLFooter($htmlFooter);
                $mpdf->AddPage('P','','','','',5,5,5,30,5,5,'','','','','','','','','','A4-P');
                $mpdf->WriteHTML($pdfData);
                // $mpdf->Output($pdfFileName, 'I');
                
                $this->printJson(['status'=>1,'message'=>'Data Found.','data'=>base64_encode($mpdf->OutputBinaryData())]);
            } else{
                $this->printJson(['status'=>0,'message'=>'Data Not Found.','data'=>null]);
            }
        }
    }
}
?>