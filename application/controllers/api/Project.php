<?php
class Project extends MY_ApiController{

	public function __construct(){
		parent::__construct();
        $this->data['headData']->pageTitle = "Project";
        $this->data['headData']->pageUrl = "api/project";
        $this->data['headData']->base_url = base_url();
	}

    public function addProject(){
		$this->data['projectTypeList'] = $this->selectOption->getSelectOptionList(['type'=>1]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>[1]]);
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
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

    public function delete(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->project->delete($data));
        endif;
    }
	
	public function getProjectDetail(){
        $data = $this->input->post();
        $this->data['projectDetail'] = $this->project->getProject($data);
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }
	
	public function getProjectList(){
        $data = $this->input->post();
        $this->data['projectList'] = $projectList = $this->project->getProjectList($data);
		
		if(!empty($data['work_milestone'])):
			$pArr = [];
			if(!empty($projectList)):
				foreach($projectList as $row)
				{
					$row->milstones = [];$marr = [];
					$milstones = $this->project->getProjectMilestoneList(['project_id'=>$row->id]);
					if(!empty($milstones)):
						foreach($milstones as $mrow)
						{
							$row->milstones[] = ['id'=>$mrow->id, 'work_type'=>$mrow->work_type];
						}
					endif;
					$pArr[] = $row;
				}
			endif;
			$this->data['projectList'] = $pArr;
		endif;
		
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }
	
	public function getProjectListForChat(){
        $data = $this->input->post();
        $this->data['projectList'] = $projectList = $this->project->getProjectListForChat($data);
		
		/* usort($this->data['projectList'], function($a, $b) {
			$timeA = strtotime($a->created_at ?? '') ?: 0;
			$timeB = strtotime($b->created_at ?? '') ?: 0;
			return $timeB <=> $timeA; // descending order
		}); */
		//print_r($this->data['projectList']);exit;
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }

    public function addProjectMileStone(){
		$data = $this->input->post();
		
        $this->data['agencyList'] = $this->party->getPartyList(['party_category' => 3]); // 
        $this->data['workTypeList'] = $this->selectOption->getSelectOptionList(['type'=>4]);
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }
	
	public function getProjectMileStoneDetails(){
        $data = $this->input->post();
        $this->data['projectDetails'] = $this->project->getProjectMilestoneList($data);

        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['projectDetails']]);
    }
    
    public function getProjectMilestone(){
        $data = $this->input->post();
        $this->data['projectDetail'] = $this->project->getProjectMilestoneList($data);
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['projectDetail']]);
    }

    public function saveProjectMileStone(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Project Name is required.";
        if(empty($data['work_type']))
            $errorMessage['work_type'] = "Work Type is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'error'=>$errorMessage]);
        else:
            $this->printJson($this->project->saveProjectMileStone($data));
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
	
	/**************************
		Work Progress
	**************************/
	
    public function getWorkProgressList(){
        $data = $this->input->post();
        $this->data['workProgressDetails'] = $this->project->getWorkProgressList($data);
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['workProgressDetails']]);
    }

    public function addWorkTask(){
        $data = $this->input->post();
		$data['tower'] = 1;
        $this->data['project_id'] = $data['project_id'];
		$this->data['towerList'] = $this->project->getProjectTower($data);
		$this->data['agencyList'] = $this->project->getProjectAgencyList($data);
        $this->data['lastRecord'] = $this->siteTrans->getWorkDetailList(['project_id'=>$data['project_id'],'result_type'=>'row']);
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }
	
    public function saveWorkProgress(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['project_id']))
            $errorMessage['project_id'] = "Description is required.";
        if(empty($data['work_id']))
            $errorMessage['work_id'] = "Work is required.";
        if(empty($data['agency_id']))
            $errorMessage['agency_id'] = "Agency is required.";
        if(empty($data['aw_id']))
            $errorMessage['aw_id'] = "Agency Work is required.";
        if(empty($data['description']))
            $errorMessage['description'] = "Description is required.";
        if(!isset($data['status']))
            $errorMessage['status'] = "Status is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			if(empty($data['start_date'])){unset($data['start_date']);}
			if(empty($data['end_date'])){unset($data['end_date']);}
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
    
}
?>