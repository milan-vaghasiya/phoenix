<?php
class SiteTrans extends MY_Controller{
    private $index = "site_trans/index";
    private $form = "site_trans/form";
    private $projectDetail = "site_trans/project_detail";
    private $agencyProgressForm = "site_trans/agency_progress_form";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Site Management";
        $this->data['headData']->controller = "siteTrans";
        $this->data['headData']->pageUrl = "siteTrans";
    }

    public function workUpdate(){
        $this->data['headData']->pageUrl = "siteTrans/workUpdate";
        $this->data['tableHeader'] = getMasterDtHeader("workUpdate");
        $this->load->view("site_trans/index",$this->data);
    }

    public function getDTRows($work_type=1){
        $data = $this->input->post();
		$data['work_type'] = $work_type;
        $result = $this->siteTrans->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getProjectWorkData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
    public function addWorkDetail(){
        $data = $this->input->post();
        $this->data['type'] = $data['type'];
		$this->data['projectList'] = $this->project->getProjectList($data);
        $this->load->view($this->form,$this->data);
    }

    public function saveWorkDetail(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Date is required";
        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required";
        if(empty($data['tower_name']))
            $errorMessage['tower_name'] = "Tower/Block is required";
        if(empty($data['work_detail']))
            $errorMessage['work_detail'] = "Work Detail is required";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->siteTrans->saveWorkDetail($data));
        endif;
    }

    public function editWorkDetail(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->siteTrans->getWorkDetailList($data);
		$this->data['projectList'] = $this->project->getProjectList($data);
        $this->load->view($this->form,$this->data);
    }

    public function deleteWorkDetail(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->siteTrans->deleteWorkDetail($data));
        endif;
    }
    
	public function getProjectTower(){
        $data = $this->input->post();
        $result = $this->project->getProjectTower($data);
		
        $option = "<option value=''>Select Tower/Block</option>"; $i = 1;
        foreach($result as $row):
            $selected = (!empty($data['tower_name']) && $data['tower_name'] == $row->tower_name)?"selected":"";
			$option .= '<option value="'.$row->tower_name.'" '.$selected.'>'.$row->tower_name.'</option>';
        endforeach;

        $this->printJson(['status'=>1,'options'=>$option]);
    }

    public function getProjectAgency(){
        $data = $this->input->post();
		
		if(!empty($data['tbody'])){ $data['tower'] = 1; }
		$result = $this->project->getProjectAgencyList($data);
		
		if(empty($data['tbody'])){
			
			$option = "<option value=''>Select Agency</option>"; $i = 1;
			foreach($result as $row):
				$selected = (!empty($data['agency_id']) && $data['agency_id'] == $row->id)?"selected":"";
				$option .= '<option value="'.$row->id.'" '.$selected.'>'.$row->agency_name.'</option>';
			endforeach;

			$this->printJson(['status'=>1,'options'=>$option]);
			
		}else{
			
			$tbody = ""; $i = 0;
			foreach($result as $row):
				$tbody .= '<tr>
					<td>
						'.$row->agency_name.'<br><small>'.$row->tower_name.'</small>
						<input type="hidden" name="agency['.$i.'][agency_id]" class="form-control" value="'.$row->id.'">
						<input type="hidden" name="agency['.$i.'][tower_name]" class="form-control" value="'.$row->tower_name.'">
					</td>
					<td><input type="text" name="agency['.$i.'][male]" class="form-control numericOnly" value=""></td>
					<td><input type="text" name="agency['.$i.'][female]" class="form-control numericOnly" value=""></td>
				</tr>'; $i++;
			endforeach;

			$this->printJson(['status'=>1,'tbody'=>$tbody]);

		}
    }

    /******* Labor Attendance */
    public function laborAttendance(){
        $this->data['headData']->pageUrl = "siteTrans/laborAttendance";
        $this->data['tableHeader'] = getMasterDtHeader("laborAttendance");	
        $this->load->view("site_trans/labor_attend_index",$this->data);
    }
    
	public function getLaborAttendanceDTRows(){
        $data = $this->input->post();
        $result = $this->siteTrans->getLaborAttendanceDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getLaborAttendanceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function addLaborAttendance(){
        $data = $this->input->post();
        $this->data['type'] = $data['type'];
		$this->data['projectList'] = $this->project->getProjectList($data);
		$this->data['laborCatList'] = $this->selectOption->getSelectOptionList(['type'=>2]);
        $this->load->view('site_trans/labor_attend_form',$this->data);
    }

    public function editAttendance(){
        $data = $this->input->post();$data['single_row'] = 1;
        $this->data['dataRow'] = $this->siteTrans->getLaborAttendance($data);
        $this->data['projectList'] = $this->project->getProjectList($data);
		$this->data['laborCatList'] = $this->selectOption->getSelectOptionList(['type'=>2]);
        $this->load->view('site_trans/labor_attend_form',$this->data);
    }

    public function saveLaborAttendance(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Date is required";
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

	
    /*** Machine Status */
    public function machineryStatus(){
        $this->data['headData']->pageUrl = "siteTrans/machineryStatus";
        $this->data['tableHeader'] = getMasterDtHeader("machineryStatus");
        $this->load->view("site_trans/mc_status_index",$this->data);
    }
    
	public function getMachineryStatusDTRows(){
        $data = $this->input->post();
        $result = $this->siteTrans->getMachineryStatusDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getMachineryStatusData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMachineStatus(){
        $data = $this->input->post();
        $this->data['type'] = $data['type'];
		$this->data['projectList'] = $this->project->getProjectList($data);
		$this->data['machineList'] = $this->siteTrans->getMachineList();
        $this->load->view('site_trans/mc_status_form',$this->data);
    }

    public function saveMachineStatus(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Date is required";
        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            // print_r($data);exit;
            $this->printJson($this->siteTrans->saveMachineStatus($data));
        endif;
    }
	
    public function editMachineStatus(){
        $data = $this->input->post(); $data['single_row'] = 1;
        $this->data['dataRow'] = $this->siteTrans->getMachineryStatusList($data);
        $this->data['projectList'] = $this->project->getProjectList($data);
        $this->data['machineList'] = $this->siteTrans->getMachineList();
        $this->load->view('site_trans/mc_status_form',$this->data);
    }

    public function deleteMachineStatus(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->siteTrans->deleteMachineStatus($data));
        endif;
    }

    /*** Complain */
    public function complain(){
        $this->data['headData']->pageUrl = "siteTrans/complain";
        $this->data['tableHeader'] = getMasterDtHeader("complain");
     
        $this->load->view("site_trans/complain_index",$this->data);
    }

    public function getComplainDTRows(){
        $data = $this->input->post();
        $result = $this->siteTrans->getComplainDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getComplainData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function addComplain(){
        $data = $this->input->post();
        $this->data['type'] = $data['type'];
		$this->data['projectList'] = $this->project->getProjectList($data);
        $this->load->view('site_trans/complain_form',$this->data);
    }

    public function saveComplain(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Date is required";
        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required";
        if(empty($data['complain_title']))
            $errorMessage['complain_title'] = "Title is required";
        if(empty($data['complain_note']))
            $errorMessage['complain_note'] = "Complain is required";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->siteTrans->saveComplain($data));
        endif;
    }

    public function editComplain(){
        $data = $this->input->post();$data['single_row'] = 1;
        $this->data['dataRow'] = $this->siteTrans->getComplainList($data);
		$this->data['projectList'] = $this->project->getProjectList($data);
        $this->load->view('site_trans/complain_form',$this->data);
    }

    public function deleteComplain(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->siteTrans->deleteComplain($data));
        endif;
    }

    /*** Extra Activity */
    public function extraActivity(){
        $this->data['headData']->pageUrl = "siteTrans/extraActivity";
        $this->data['tableHeader'] = getMasterDtHeader("extraActivity");
     
        $this->load->view("site_trans/extra_activity_index",$this->data);
    }

    public function getExtraActivityDTRows(){
        $data = $this->input->post();
        $result = $this->siteTrans->getExtraActivityDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getExtraActivityData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function addExtraActivity(){
        $data = $this->input->post();
		$this->data['projectList'] = $this->project->getProjectList($data);
        $this->load->view('site_trans/extra_activity_form',$this->data);
    }

    public function saveExtraActivity(){
        $data = $this->input->post();
		
        $errorMessage = [];

        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Date is required";
        if(empty($data['activity']))
            $errorMessage['activity'] = "Activity is required";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$this->printJson($this->siteTrans->saveExtraActivity($data));
        endif;
    }

    public function editExtraActivity(){
        $data = $this->input->post();$data['single_row'] = 1;
        $this->data['dataRow'] = $this->siteTrans->getExtraActivity($data);
		$this->data['projectList'] = $this->project->getProjectList($data);
        $this->load->view('site_trans/extra_activity_form',$this->data);
    }

    public function deleteExtraActivity(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->siteTrans->deleteExtraActivity($data));
        endif;
    }


    /***** DPR *****/
    public function dpr(){
        $data = $this->input->post();
		$this->data['projectList'] = $this->project->getProjectList($data);
        $this->load->view('site_trans/dpr_detail',$this->data);
    }

    public function getDprReport($jsonData=""){
        if(!empty($jsonData)){
            $data = (array) decodeURL($jsonData);
        }else{
            $data = $this->input->post();
        }
		$this->data['data'] = $data;
		
        $this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
        $this->data['projectData'] = $projectData = $this->project->getProject(['id'=>$data['project_id']]);
        $this->data['trans_date'] = $data['trans_date'];
        /*
		$this->data['laborAttendanceList'] = $laborAttendanceList =  $this->siteTrans->getLaborAttendance(['project_id'=>$data['project_id'],'trans_date'=>$data['trans_date']]);
		*/
		
        $this->data['machineList'] = $machineList = $this->siteTrans->getMachineList(['ids'=>$projectData->machine_ids]);
        $this->data['machineryStatuseList'] = $this->siteTrans->getMachineryStatusList($data);
        $this->data['workDetailList'] = $this->siteTrans->getWorkDetailList(['project_id'=>$data['project_id'],'trans_date'=>$data['trans_date'],'type'=>1]);
        $this->data['workPlanList'] = $this->siteTrans->getWorkDetailList(['project_id'=>$data['project_id'],'trans_date'=>$data['trans_date'],'type'=>2]);
        $this->data['complaintList'] = $this->siteTrans->getComplainList(['project_id'=>$data['project_id'],'trans_date'=>$data['trans_date'],'result_type'=>'row']);
        $this->data['extraActivityList'] = $this->siteTrans->getExtraActivity(['project_id'=>$data['project_id'],'trans_date'=>$data['trans_date'],'result_type'=>'row']);
		$this->data['mediaList'] = $mediaList = $this->projectHistory->getProjectMedia(['project_id'=>$data['project_id'],'trans_date'=>$data['trans_date'],'ids'=>(!empty($data['ids']) ? $data['ids'] : ''),'result_type'=>'rows']);
		
        $this->data['weather_icon'] = base_url('assets/images/weather/cloudy.png');
        $logo = $this->data['logo'] = base_url('assets/images/logo.png');
        $this->data['letter_head'] = "";
		
		$this->data['stockData']= $this->store->getItemWiseStockData(['location_id'=>$data['project_id'],'group_by'=>'item_id','trans_date'=>$data['trans_date']]);
				
		if($data['trans_date'] < "2025-08-13"):
            $this->data['laborAttendanceList'] = $laborAttendanceList =  $this->siteTrans->getLaborAttendance(['project_id'=>$data['project_id'],'trans_date'=>$data['trans_date']]);
            $pdfData = $this->load->view('site_trans/dpr_print', $this->data, true);
        else:
            $this->data['laborAttendanceList'] = $laborAttendanceList =  $this->siteTrans->getLaborAttendanceDprData_v2(['project_id'=>$data['project_id'],'trans_date'=>$data['trans_date']]);
            $pdfData = $this->load->view('site_trans/dpr_print_new', $this->data, true);
        endif;

        //$pdfData = $this->load->view('site_trans/dpr_print', $this->data, true);

        if(!empty($data['is_pdf'])):
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
			
			$mediaHtml = '';$m=0;
			if(!empty($mediaList) && !empty($data['ids']))
			{
				$mediaHtml = '<table class="table" style="width:100%;border-collapse:collapse;"><tr>';
				foreach($mediaList as $m_row)
				{
					if($m%4==0 AND $m > 0){$mediaHtml .= '</tr><tr>';}
					$mediaHtml .= '<td style="width:25%;text-align:center;border:1px solid #000000;font-size:12px;" align="center"><img src="'.$m_row->media_file.'" style="width:125px;height:125px;border:1px solid #000000;border-radius:10px;" ><br><p>'.(!empty($m_row->message) ? $m_row->message : '&nbsp;').'</p></td>';
					$m++;
				}
				$mediaHtml .= '</tr></table>';
                $mpdf->AddPage('P','','','','',5,5,5,30,5,5,'','','','','','','','','','A4-P');
                $mpdf->WriteHTML($mediaHtml);
			}
            
            
            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        else:
            $response = [
                'status' => 1,
                'tbody' => $pdfData,
            ];
            $this->printJson($response);

        endif;
    }
	
	/* Select Images For DPR PDF */
    public function dprPrintForm(){
        $this->data['dataRow'] = $data = $this->input->post();
        $this->data['mediaList'] = $this->projectHistory->getProjectMedia(['project_id'=>$data['project_id'],'trans_date'=>$data['trans_date'],'result_type'=>'rows']);
        $this->load->view('site_trans/dpr_print_form',$this->data);
    }

	/*
    public function getDprData(){
        $data = $this->input->post();
        $projectData = $this->project->getProject(['id'=>$data['project_id']]);
        $projectTbl = '<tr>
                            <th style="width:20%">Date</th>
                            <td>'.formatDate($data['trans_date']).'</td>
                        </tr>
                        <tr>
                            <th>Client Name</th>
                            <td>'.$projectData->party_name.'</td>
                        </tr>
                        <tr>
                            <th>Project Consultant</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>Project  Contractor</th>
                            <td>PHOENIX INFRA</td>
                        </tr>
                        <tr>
                            <th>Weather</th>
                            <td>Sunny</td>
                        </tr>';
        $attendaceTbody = "";$attendaceTfoot = ""; $i=1;$totalDay =0;$totalNight = 0;$totalBbr = 0;
        $attendanceData = $this->siteTrans->getLaborAttendance($data);
        if(!empty($attendanceData)){
           
            foreach($attendanceData AS $row){
                // print_r($row);
                $attendaceTbody .= '<tr>
                                        <td>'.$i++.'</td>
                                        <td>'.$row->labor_category.'</td>
                                        <td>'.(($row->shift == "Day")?$row->total_labor:"").'</td>
                                        <td>'.(($row->shift == "Night")?$row->total_labor:"").'</td>
                                        <td>'.$row->total_labor.'</td>
                                    </tr>';
                $totalDay +=(($row->shift == "Day")?$row->total_labor:0);
                $totalNight += (($row->shift == "Night")?$row->total_labor:0);
                $totalBbr += $row->total_labor;
            }
        }
        $attendaceTfoot = '<tr>
                                <th colspan="2" class="text-right"> Total</th>
                                <th>'.$totalDay.'</th>
                                <th>'.$totalNight.'</th>
                                <th>'.$totalBbr.'</th>
                            </tr>';
        $machineTbody = "";$machineTfoot = ""; $i=1;$totalMc = 0;
        $mcData = $this->siteTrans->getMachineryStatusList($data);
        if(!empty($mcData)){
            foreach($mcData AS $row){
                $machineTbody .= '<tr>
                                    <td>'.$i++.'</td>
                                    <td>'.$row->machine_name.'</td>
                                    <td>'.$row->qty.'</td>
                                </tr>';
                $totalMc += $row->qty;
            }
        }
        $machineTfoot = '<tr>
                <th colspan="2" class="text-right"> Total</th>
                <th>'.$totalMc.'</th>
            </tr>';

        $workData = $this->siteTrans->getWorkDetailList($data);

        $workTbody = "";
        if(!empty($workData)){
            $workDetail = array_reduce($workData, function($workDetail, $work) { $workDetail[$work->work_title][] = $work; return $workDetail; }, []);
                foreach ($workDetail as $work_title => $work){
                    $workTbody .= '<tr>
                                        <th colspan="5" class="text-center bg-light">'.$work_title.'</th>
                                    </tr>';
                                    $i=1;
                    foreach ($work as $row){
                        $workTbody .= '<tr>
                                        <td>'.$i++.'</td>
                                        <td>'.$row->work_detail.'</td>
                                        <td>'.$row->uom.'</td>
                                        <td>'.$row->execution.'</td>
                                        <td>'.$row->remark.'</td>
                                    </tr>';
                    }
                }
        }
        $itemData = $this->item->getItemList(['item_type'=>1]);
        $totalStock = $this->store->getItemStockBatchWise(['location_id'=>$data['project_id'],'group_by'=>'item_id']);
        $todayRecv = $this->store->getItemStockBatchWise(['location_id'=>$data['project_id'],'trans_date'=>$data['trans_date'],'trans_type'=>'GRN','group_by'=>'item_id']);
        $receiveData = array_reduce($todayRecv, function($receiveData, $item) { $receiveData[$item->item_id] = $item; return $receiveData; }, []);
        $stockData = array_reduce($totalStock, function($stockData, $item) { $stockData[$item->item_id] = $item; return $stockData; }, []);
        $materialTbody="";$i=1;
        if(!empty($itemData)){
            foreach($itemData AS $row){
                $toDayReceive = (!empty($todayRecv[$row->id]->qty)?$todayRecv[$row->id]->qty:0);
                $totalStock = (!empty($stockData[$row->id]->qty)?$stockData[$row->id]->qty:0);
                $prevStock = $totalStock  - $toDayReceive;
                $materialTbody .= '<tr> 
                                        <td>'.$i++.'</td>
                                        <td>'.$row->item_name.'</td>
                                        <td>'.$toDayReceive.'</td>
                                        <td>'.$prevStock.'</td>
                                        <td>'.$totalStock.'</td>
                                   </tr>';
            }
        }
        $this->printJson(['projectTbl'=>$projectTbl,'attendaceTbody'=>$attendaceTbody,'attendaceTfoot'=>$attendaceTfoot,'machineTbody'=>$machineTbody,'machineTfoot'=>$machineTfoot,'workTbody'=>$workTbody,'materialTbody'=>$materialTbody]);
    }

    public function getDprReport(){
        $data = $this->input->post();
		
        //$this->data['dataRow'] = $dataRow = $this->salesQuotation->getSalesQuotation(['id'=>$id,'itemList'=>1]);
		//$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo($dataRow->cm_id);
        
        $this->data['weather_icon'] = base_url('assets/images/weather/cloudy.png');
        $logo = $this->data['logo'] = base_url('assets/images/logo.png');
        $this->data['letter_head'] = "";// base_url($companyData->print_header);
        
        $pdfData = $this->load->view('site_trans/dpr_print', $this->data, true);
        
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
            <tr>
                <td style="width:25%;">Qtn. No. & Date : '.$dataRow->trans_number . ' [' . formatDate($dataRow->trans_date) . ']</td>
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
		$mpdf->AddPage('P','','','','',5,5,5,10,5,5,'','','','','','','','','','A4-P');
        $mpdf->WriteHTML($pdfData);
		
		ob_clean();
		$mpdf->Output($pdfFileName, 'I');
    }
	*/
}
?>