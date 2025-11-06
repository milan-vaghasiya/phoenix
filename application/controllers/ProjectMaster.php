<?php
class ProjectMaster extends MY_Controller{
    private $index = "project_master/index";
    private $form = "project_master/form";
    private $projectDetail = "project_master/project_detail";
    private $agencyForm = "project_master/agency_form";
    private $towerForm = "project_master/tower_form";
    private $inchargeForm = "project_master/incharge_form";
    private $towerDetailForm = "project_master/tower_detail_form";
    private $workProgressForm = "project_master/milestone_progress_form";
	private $machineForm = "project_master/machine_form";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Project Master";
        $this->data['headData']->controller = "projectMaster";
        $this->data['headData']->pageUrl = "projectMaster";
    }

    public function index(){
        $this->data['tableHeader'] = getMasterDtHeader("projectMaster");
        $this->load->view($this->index,$this->data);
    }

	public function getDTRows($status = 0){
        $data = $this->input->post();
		$data['status'] = $status;
        $result = $this->project->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getProjectData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
    public function addProject(){
        $this->data['projectTypeList'] = $this->selectOption->getSelectOptionList(['type'=>1]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>[1,2,3]]);
        $this->data['shiftList'] = $this->shiftModel->getShiftList(); 
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['project_name']))
            $errorMessage['project_name'] = "Project Name is required.";
        if(empty($data['project_type']))
            $errorMessage['project_type'] = "Project Type is required.";
        if(empty($data['amount']))
            $errorMessage['amount'] = "Amount is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'error'=>$errorMessage]);
        else:
            $this->printJson($this->project->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->project->getProject($data);
        $this->data['projectTypeList'] = $this->selectOption->getSelectOptionList(['type'=>1]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>[1,2,3]]);
        $this->data['shiftList'] = $this->shiftModel->getShiftList();
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->project->delete($data));
        endif;
    }

    public function detail($id){
        $id = decodeURL($id);
        $this->data['headData']->pageTitle = "Project Detail";
        $this->data['dataRow'] = $this->project->getProject(['id'=>$id]);
        $this->data['project_id'] = $id;
        $this->data['partyList'] = $this->party->getPartyList(); // ['party_category' => 3]
        $this->data['workTypeList'] = $this->selectOption->getSelectOptionList(['type'=>4]);
        $this->data['projectTowerList'] = $this->project->getProjectTower(['project_id'=>$id]);
        $this->load->view($this->projectDetail,$this->data);
    }
	
    public function addProjectTower(){
        $data = $this->input->post();
        $this->data['project_id'] = $data['project_id'];
        $this->load->view($this->towerForm,$this->data);
    }
	
	public function saveProjectTower(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required.";
        if(empty($data['tower_name']))
            $errorMessage['tower_name'] = "Tower Name is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'error'=>$errorMessage]);
        else:
            $this->printJson($this->project->saveProjectTower($data));
        endif;
    }

    public function editProjectTower(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->project->getProjectTower($data);
        $this->load->view($this->towerForm,$this->data);
    }

    public function deleteProjectTower(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->project->deleteProjectTower($data));
        endif;
    }

    public function getProjectTower(){
        $data = $this->input->post();
        $this->data['id'] = $data['id'];
        $this->data['dataRow'] = $this->project->getProjectTower($data);
        $this->data['project_id'] = $data['project_id'];
        $this->load->view($this->towerForm,$this->data);
    }
	
    public function getProjectTowerList(){
        $data = $this->input->post();
        $result = $this->project->getProjectTower($data);

        $tbody = ""; $i = 1;
        foreach($result as $row):
            $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Project Tower', 'fndelete' : 'deleteProjectTower', 'res_function' : 'resRemoveProjectTower'}";
            $deleteBtn = '<button type="button" class="btn btn-danger btn-sm" onclick="trash('.$deleteParam.')"><i class="fas fa-trash"></i></button>';

            //$editParam = "{'postData':{'id' : ".$row->id.",'project_id' : ".$row->project_id."},'controller' : 'projectMaster', 'call_function' : 'getProjectTower', 'init_action' : 'getProjectTower'}";
			
            $editParam = "{'id' : ".$row->id.",'tower_name' : '".$row->tower_name."','total_basement' : '".$row->total_basement."','total_floor' : '".$row->total_floor."','description' : '".$row->description."'}";
            $editBtn = '<button type="button" class="btn btn-warning btn-sm" onclick="getProjectTower('.$editParam.')"><i class="fas fa-edit"></i></button>';

            $tbody .= '<tr>
						<td>'.$i++.'</td>
						<td>'.$row->tower_name.'</td>
						<td>'.$row->total_basement.'</td>
						<td>'.$row->total_floor.'</td>
						<td>'.$editBtn.' '.$deleteBtn.'</td>
					</tr>';
        endforeach;

        if(empty($tbody)):
            $tbody = "<tr><td colspan='5' class='text-center'>No data available in table</td></tr>";
        endif;

        $this->printJson(['status'=>1,'tbodyData'=>$tbody]);
    }
	
    public function getProjectMilestoneList(){
        $data = $this->input->post();
        $towerDetail = $this->project->getProjectMilestoneList($data);
        $this->data['project_id'] = $data['project_id'];
        $this->data['tower_name'] = $data['tower_name'];
        $tbody = ""; $i = 1;
        foreach($towerDetail as $row):
            $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Project Milestone', 'fndelete' : 'deleteProjectMilestone', 'res_function' : 'resRemoveProjectMilestone'}";
            $deleteBtn = '<button type="button" class="btn btn-danger btn-sm" onclick="trash('.$deleteParam.')"><i class="fas fa-trash"></i></button>';

            $editParam = "{'postData':{'id' : ".$row->id."},'controller' : 'projectMaster', 'call_function' : 'getProjectMilestone', 'init_action' : 'getProjectMilestone'}";
            $editBtn = '<button type="button" class="btn btn-warning btn-sm" onclick="modalAction('.$editParam.')"><i class="fas fa-edit"></i></button>';
			
            $addAgencyWorkParam = "{'postData':{'project_id' : ".$row->project_id.", 'work_id' : ".$row->id."}, 'controller' : 'projectMaster', 'call_function' : 'addAgencyWork', 'form_id' : 'agencyWorkForm', 'title' : 'Agency Work [ ".$row->work_type." ]', 'modal_id' : 'bs-right-lg-modal', 'button':'close'}";
            $addAgencyWorkBtn = '<button type="button" class="btn btn-info btn-sm" onclick="modalAction('.$addAgencyWorkParam.')"><i class="fas fa-users"></i></button>';
			
            $addProgressParam = "{'postData':{'project_id' : ".$row->project_id.", 'work_ref_id' : ".$row->id."}, 'controller' : 'projectMaster', 'call_function' : 'addWorkProgress', 'form_id' : 'workProgressForm', 'title' : 'Milestone Progress [".$row->tower_name."]', 'modal_id' : 'bs-right-lg-modal', 'button':'close'}";
            $addProgressBtn = '<button type="button" class="btn btn-success btn-sm" onclick="modalAction('.$addProgressParam.')"><i class="fas fa-plus"></i></button>';

            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$row->work_type.'</td>
                <td>'.$row->contract_label.'</td>
                <td>'.$row->work_qty.'</td>
                <td>'.$row->work_rate.'</td>
                <td>'.$row->material_qty.'</td>
                <td class="text-center">
                    '.$addAgencyWorkBtn.'
                    '.$addProgressBtn.'
                    '.$editBtn.'
                    '.$deleteBtn.'
                </td>
            </tr>';
        endforeach;

        if(empty($tbody)):
            $tbody = "<tr><td colspan='5' class='text-center'>No data available in table</td></tr>";
        endif;
		$this->data['tbody'] = $tbody;
		
        //$this->printJson(['status'=>1,'htmlContent'=>$tbody]);
        $this->printJson(['status'=>1,'tbodyData'=>$tbody]);
    }

	public function addIncharge(){
		$data = $this->input->post();
        $this->data['project_id'] = $data['project_id'];
		$this->data['employeeList'] = $this->employee->getEmployeeList();
        $this->data['dataRow'] = $this->project->getProject(['id'=>$data['project_id']]);
		$this->load->view($this->inchargeForm,$this->data);
	}
	
	public function saveIncharge(){
        $data = $this->input->post();
        $errorMessage = []; 

        if(empty($data['incharge_ids']))
            $errorMessage['incharge_ids'] = "In-Charge is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else: 
			//$data['incharge_ids'] = implode(',',$data['incharge_ids']);
            $this->printJson($this->project->saveIncharge($data));
        endif;
    }

    /* Milestone Detail Start */

    public function getProjectMilestone(){
        $data = $this->input->post();
        $result = $this->project->getProjectMilestone($data);
        $this->printJson(['status'=>1,'data'=>$result]);
    }

    public function saveProjectMilestone(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required.";
        if(empty($data['tower_name']))
            $errorMessage['tower_name'] = "Tower is required.";
        if(empty($data['work_type_id']))
            $errorMessage['work_type_id'] = "Work Type is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->project->saveProjectMilestone($data));
        endif;
    }

    public function deleteProjectMilestone(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->project->deleteProjectMilestone($data));
        endif;
    }
    /* Milestone Detail End */

    /* Agency Detail Start */
    public function addAgencyWork(){
        $data = $this->input->post();
        $this->data['project_id'] = $data['project_id'];
        $this->data['work_id'] = $data['work_id'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>[3]]);
        $this->load->view($this->agencyForm,$this->data);
    }	
	
    public function getAgencyWork(){
        $data = $this->input->post();
        $towerDetail = $this->project->getAgencyWork($data);
        $this->data['project_id'] = $data['project_id'];
        $this->data['work_id'] = $data['work_id'];
        $tbody = ""; $i = 1;
        foreach($towerDetail as $row):
            $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Project Milestone', 'fndelete' : 'deleteAgencyWork', 'res_function' : 'resRemoveAgencyWork'}";
            $deleteBtn = '<button type="button" class="btn btn-danger btn-sm" onclick="trash('.$deleteParam.')"><i class="fas fa-trash"></i></button>';
			
			//$editParam = "{'id' : ".$row->id.",'agency_id' : '".$row->agency_id."','contract_type' : '".$row->contract_type."','work_qty' : '".$row->work_qty."','work_rate' : '".$row->work_rate."','work_done' : '".$row->work_done."','material_qty' : '".$row->material_qty."','description' : '".$row->description."'}";
			$editParam = json_encode($row);
            $editBtn = "<button type='button' class='btn btn-warning btn-sm' onclick='getAgencyWork(".$editParam.")'><i class='fas fa-edit'></i></button>";
						
            $addProgressParam = "{'postData':{'project_id' : ".$row->project_id.", 'work_ref_id' : ".$row->id."}, 'controller' : 'projectMaster', 'call_function' : 'addWorkProgress', 'form_id' : 'workProgressForm', 'title' : 'Milestone Progress [".$row->tower_name."]', 'modal_id' : 'bs-right-lg-modal'}";
            $addProgressBtn = '<button type="button" class="btn btn-success btn-sm" onclick="modalAction('.$addProgressParam.')"><i class="fas fa-plus"></i></button>';
						
            $tbody .= '<tr>
						<td>'.$i++.'</td>
						<td>'.$row->agency_name.'</td>
						<td>'.$row->contract_label.'</td>
						<td>'.$row->work_qty.'</td>
						<td>'.$row->work_rate.'</td>
						<td>'.$row->material_qty.'</td>
						<td class="text-center">
							'.$editBtn.'
							'.$deleteBtn.'
						</td>
					</tr>';
        endforeach;

        if(empty($tbody)):
            $tbody = "<tr><td colspan='5' class='text-center'>No data available in table</td></tr>";
        endif;
		$this->data['tbody'] = $tbody;
		
        //$this->printJson(['status'=>1,'htmlContent'=>$tbody]);
        $this->printJson(['status'=>1,'tbodyData'=>$tbody]);
    }

    public function saveAgencyWork(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project is required.";
        if(empty($data['work_id']))
            $errorMessage['work_id'] = "Work Detail is required.";
        if(empty($data['agency_id']))
            $errorMessage['agency_id'] = "Agency is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->project->saveAgencyWork($data));
        endif;
    }

    public function deleteAgencyWork(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->project->deleteAgencyWork($data));
        endif;
    }
    
	/* Agency Detail End */

    /* Agency Progress Detail Start */
    public function addWorkProgress(){
        $data = $this->input->post();
        $this->data['projectId'] = $data['project_id'];
        $this->data['work_ref_id'] = $data['work_ref_id'];
        $this->load->view($this->workProgressForm,$this->data);
    }

    public function getWorkProgressDetails(){
        $data = $this->input->post(); //print_r($data); exit;
        $result = $this->project->getWorkProgressList($data);

        $tbody = ""; $i = 1;
        foreach($result as $row):
            $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Work Progress', 'fndelete' : 'deleteWorkProgress', 'res_function' : 'resRemoveWorkProgress'}";
            $deleteBtn = '<button type="button" class="btn btn-danger btn-sm" onclick="trash('.$deleteParam.')"><i class="fas fa-trash"></i></button>';

            $editParam = "{'postData':{'id' : ".$row->id."},'controller' : 'projectMaster', 'call_function' : 'getWorkProgress', 'init_action' : 'getWorkProgress'}";
            $editBtn = '<button type="button" class="btn btn-warning btn-sm" onclick="modalAction('.$editParam.')"><i class="fas fa-edit"></i></button>';

            $tbody .= '<tr>
						<td>'.$i++.'</td>
						<td>'.$row->progress_per.'</td>
						<td>'.$row->description.'</td>
						<td>'.$row->entry_by.'</td>
						<td>'.formatDate($row->created_at,'d-m-Y h:i A').'</td>
					</tr>';
        endforeach;

        if(empty($tbody)):
            $tbody = "<tr><td colspan='5' class='text-center'>No data available in table</td></tr>";
        endif;

        $this->printJson(['status'=>1,'tbodyData'=>$tbody]);
    }

    public function saveWorkProgress(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['description']))
            $errorMessage['description'] = "Description is required.";
        if(empty($data['work_done']))
            $errorMessage['work_done'] = "Progress (%) is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->project->saveWorkProgress($data));
        endif;
    }

    public function deleteWorkProgress(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->project->deleteAgencyProgress($data));
        endif;
    }
    /* Agency Progress Detail End */
	
	/* Stock Details Start */
	public function getStockLegerList(){
		$data = $this->input->post();
        $result = $this->store->getItemStockBatchWise($data);

        $tbody = ""; $i = 1;
        foreach($result as $row):
            $tbody .= '<tr>
				<td>'.$i++.'</td>
				<td>'.$row->item_name.'</td>
				<td>'.floatval($row->qty).' ('.$row->uom.')</td>
			</tr>';
        endforeach;

        if(empty($tbody)):
            $tbody = "<tr><td colspan='3' class='text-center'>No data available in table</td></tr>";
        endif;

        $this->printJson(['status'=>1,'tbodyData'=>$tbody]);
	}
	/* Stock Details End */
	
	/* Machine Master Start */
	public function addMachine(){
		$data = $this->input->post();
        $this->data['project_id'] = $data['project_id'];
		$this->data['machineList'] = $this->machine->getMachineList();
        $this->data['dataRow'] = $this->project->getProject(['id'=>$data['project_id']]);
		$this->load->view($this->machineForm,$this->data);
	}
	
	public function saveMachine(){
        $data = $this->input->post();
        $errorMessage = []; 

        if(empty($data['machine_ids']))
            $errorMessage['machine_ids'] = "Machine is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else: 
            $this->printJson($this->project->saveIncharge($data));
        endif;
    }
	/* Machine Master End */

	public function changeProjectStatus(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->project->changeProjectStatus($data));
        endif;
    }
	
}
?>