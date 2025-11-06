<?php
class Party extends MY_ApiController{

	public function __construct(){
		parent::__construct();
        $this->data['headData']->pageTitle = "Parties";
        $this->data['headData']->pageUrl = "api/parties";
        $this->data['headData']->base_url = base_url();
	}

    public function addParty(){
        $data = $this->input->post();
		
        $this->data['party_category'] = 1;
        $this->data['party_type'] = (isset($data['party_type']))?$data['party_type']:1;
		$this->data['stateData'] = $this->party->getStates(['country_id'=>101]);
		$this->data['businessTypes'] = $this->businessTypes;
		$this->data['party_code'] = $this->getPartyCode($this->data['party_category']);
        
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
	}	

    public function getPartyList(){
        $postData = $this->input->post();
		
		if($postData['start'] == 0){$postData['length'] = 200;}
		
		$postData['party_category'] = 1;
        $partyList = $this->party->getPartyList($postData);

        $this->printJson(['status'=>1,'data'=>$partyList]);
    }

    public function getPartyDetail(){
        $postData = $this->input->post();
		
		$partyList = $this->party->getParty($postData);

        $this->printJson(['status'=>1,'data'=>$partyList]);
    }

	/* Auto Generate Party Code */
    public function getPartyCode($party_category=""){
        $partyCategory = (!empty($party_category))?$party_category:$this->input->post('party_category');
        $code = $this->party->getPartyCode($partyCategory);
        $prefix = "";
        if($partyCategory == 1):
            $prefix = "C";
        elseif($partyCategory == 2):
            $prefix = "S";
        elseif($partyCategory == 3):
            $prefix = "V";
        endif;

        $party_code = $prefix.sprintf("%03d",$code);

        if(!empty($party_category)):
            return $party_code;
        else:
            $this->printJson(['status'=>1,'party_code'=>$party_code]);
        endif;
    }

    public function save(){
        $data = $this->input->post(); 
		
        $data['party_type'] = 1;
        
		if(isset($data['party_category']) AND $data['party_category'] == 1){ $data['group_code'] = 'SD'; }
		if(isset($data['party_category']) AND (in_array($data['party_category'],[2,3]))){ $data['group_code'] = 'SC'; }
		
		$data['party_code'] = $this->getPartyCode($data['party_category']);
        
		$errorMessage = array();

        if (empty($data['party_name']))
			$errorMessage['party_name'] = "Company name is required.";
		
		if (empty($data['party_phone']))
			$errorMessage['party_phone'] = 'Mobile No. is required.';
		
		if (empty($data['state']))
			$errorMessage['state'] = 'State is required.';

		if (empty($data['city']))
			$errorMessage['city'] = 'City is required.';

		if (empty($data['party_address']))
			$errorMessage['party_address'] = "Address is required.";
		

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$data['state'] = ucwords($data['state']);
			$data['city'] = ucwords($data['city']);
            $this->printJson($this->party->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->party->delete($id));
        endif;
    }

    public function getPartyActivity(){
        $data = $this->input->post();
        $this->data['activityDetails'] = $this->party->getPartyActivity($data);
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }

	/*****************************
			PROJECT
	*****************************/
	
    public function addProject(){
        $this->data['projectTypeList'] = $this->selectOption->getSelectOptionList(['type'=>1]);
        $partyList = $this->party->getPartyList(['party_category'=>[1]]);
		$pData = [];
		$pData["id"] = "0";
		$pData["party_code"] = "";
		$pData["party_name"] = "OWN PROJECT";
		$pData["party_category"] = "";
		$pData["party_type"] = "";
		$pData["state"] = "";
		$pData["city"] = "";
		/*if(!empty($partyList))
		{
			foreach($partyList as $row)
			{
				$pData["party_code"] = "0";
				$pData["party_name"] = "OWN PROJECT";
				$pData["party_category"] = "";
				$pData["party_type"] = "";
				$pData["state"] = "";
				$pData["city"] = "";
			}
		}*/
		$this->data['partyList'] = array_merge($pData,$partyList);
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }

    public function saveProject(){
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

    public function deleteProject(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->project->delete($data));
        endif;
    }

}
?>