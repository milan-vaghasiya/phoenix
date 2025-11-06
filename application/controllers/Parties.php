<?php
class Parties extends MY_Controller{
    private $index = "party/index";
    private $form = "party/form";
    private $ledgerForm = "party/ledger_form";
    private $directorForm = "party/director_form";

    public function __construct(){
        parent::__construct();
		$this->data['headData']->pageTitle = "Party Master";
		$this->data['headData']->controller = "parties";        
    }

    public function list($type="customer"){
        $this->data['headData']->pageUrl = "parties/list/".$type;
        $this->data['type'] = $type;
        $this->data['party_category'] = $party_category = array_search(ucwords($type),$this->partyCategory);
		$this->data['headData']->pageTitle = $this->partyCategory[$party_category];
        $this->data['tableHeader'] = getMasterDtHeader($type);
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($party_category){
        $data=$this->input->post(); $data['party_category'] = $party_category;
        $result = $this->party->getDTRows($data);
        $sendData = array();
        $i = ($data['start']+1);
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->table_status = $party_category;
            $row->party_category_name = $this->partyCategory[$row->party_category];
            $sendData[] = getPartyData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function addParty(){
        $data = $this->input->post();
        $this->data['party_category'] = $data['party_category'];
        $this->data['party_type'] = (isset($data['party_type']))?$data['party_type']:1;
        $this->data['0'] = '';
		
		if(isset($data['party_type']) AND $data['party_type'] == 1){$this->data['group_code'] = 'SD';}
		if(isset($data['party_type']) AND (in_array($data['party_type'],[2,3]))){$this->data['group_code'] = 'SC';}

        if(in_array($data['party_category'], [1,2,3])) {         
            //$this->data['countryData'] = $this->party->getCountries();
            //$this->data['country'] = "India";
            $this->data['stateData'] = $this->party->getStates(['country_id'=>101]);
			$this->data['businessTypes'] = $this->businessTypes;
            $this->data['party_code'] = $this->getPartyCode($data['party_category']);     
            $this->load->view($this->form, $this->data);
        }elseif($data['party_category'] == 4){
            $this->load->view($this->directorForm,$this->data);
        }elseif($data['party_category'] == 5){
            $this->data['groupList'] = $this->groupMaster->getGroupList();
            $this->load->view($this->ledgerForm,$this->data);
        }
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

    public function getStatesOptions($postData=array()){
        $country = (!empty($postData['country']))?$postData['country']:$this->input->post('country');

        $result = $this->party->getStates(['country'=>$country]);

        $html = '<option value="">Select State</option>';
        foreach ($result as $row) :
            $selected = (!empty($postData['state']) && $row->id == $postData['state']) ? "selected" : "";
       
            $state_name = (!empty($row->gst_statecode) && $row->gst_statecode > 0)?$row->gst_statecode.' - '.$row->name: $row->name;       
            $html .= '<option value="'.$row->id.'" '.$selected.'>'.$state_name.'</option>';
        endforeach;

        if(!empty($postData)):
            return $html;
        else:
            $this->printJson(['status'=>1,'result'=>$html]);
        endif;
    }

    public function getCitiesOptions($postData=array()){
        $state = (!empty($postData['state']))?$postData['state']:$this->input->post('state');
        $state_id = (!empty($postData['state_id']))?$postData['state_id']:$this->input->post('state_id');

        // $result = $this->party->getCities(['state_id'=>4030]);
        $result = $this->party->getCities(['state_id'=>$state_id]);
        $html = '<option value="">Select District</option>';
        foreach ($result as $row) :
            $selected = (!empty($postData['city']) && $row->name == $postData['city']) ? "selected" : "";
            $html .= '<option value="' . $row->name . '" ' . $selected . '>' . $row->name . '</option>';
        endforeach;

        if(!empty($postData)):
            return $html;
        else:
            $this->printJson(['status'=>1,'result'=>$html]);
        endif;
    }
    
    public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();
        if (empty($data['party_name']))
            $errorMessage['party_name'] = "Name is required.";

        if (empty($data['party_category']))
            $errorMessage['party_category'] = "Party Category is required.";

        if(in_array($data['party_category'],[1,2,3])){
            if (empty($data['country']))
                $errorMessage['country'] = 'Country is required.';

            if (empty($data['state'])):
                $errorMessage['state'] = 'State is required.';
            else:
                $stateData = $this->party->getStates(['name'=>$data['state'],'single_row'=>1]);
                $data['party_state_code'] = $stateData->gst_statecode;
            endif;

            if (empty($data['city']))
                $errorMessage['city'] = 'District is required.';

            if (empty($data['party_address']))
                $errorMessage['party_address'] = "Address is required.";
			
			if(isset($data['party_category']) AND $data['party_category'] == 1){$data['group_code'] = 'SD';}
			if(isset($data['party_category']) AND (in_array($data['party_category'],[2,3]))){$data['group_code'] = 'SC';}
			
			if(in_array($data['party_category'],[2,3])){
				if(empty($data['pan_no'])){$errorMessage['pan_no'] = 'PAN No. is required.';}
			}
        }
		
		if (empty($data['group_code']))
			$errorMessage['group_code'] = "Group is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else : 
            $data['party_name'] = strtoupper($data['party_name']);
            $this->printJson($this->party->save($data));
        endif;
    }

	public function edit(){
        $data = $this->input->post();
        $result = $this->party->getParty($data);
        $this->data['dataRow'] = $result;

        if(in_array($result->party_category, [1,2,3])) {   
            $this->data['stateData'] = $this->party->getStates(['country_id'=>101]);
            $this->load->view($this->form, $this->data);
        }elseif($result->party_category == 4){
            $this->load->view($this->directorForm,$this->data);
        }elseif($result->party_category == 5){
            $this->data['groupList'] = $this->groupMaster->getGroupList();
            $this->load->view($this->ledgerForm,$this->data);
        }
    }

    public function delete(){
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->party->delete($id));
        endif;
    }

    public function getPartyList(){
        $data = $this->input->post();
        $partyList = $this->party->getPartyList($data);
        $this->printJson(['status'=>1,'data'=>['partyList'=>$partyList]]);
    }

}
?>