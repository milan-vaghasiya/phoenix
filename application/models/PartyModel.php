<?php
class PartyModel extends MasterModel{
    private $partyMaster = "party_master";
    private $countries = "countries";
	private $states = "states";
	private $cities = "cities";
    private $villages = "villages";
    private $transDetails = "trans_details";

    public function getPartyCode($category=1){
        $queryData['tableName'] = $this->partyMaster;
        $queryData['select'] = "ifnull((MAX(CAST(REGEXP_SUBSTR(party_code,'[0-9]+') AS UNSIGNED)) + 1),1) as code";
        $queryData['where']['party_category'] = $category;
        $result = $this->row($queryData)->code;
        return $result;
    }

	public function getDTRows($data){
        $data['tableName'] = $this->partyMaster;

        $data['select'] = "party_master.id, party_master.party_category,party_master.party_name, party_master.gstin, party_master.contact_person, party_master.party_phone, party_master.city, party_master.business_type,party_master.whatsapp_no,party_master.party_email,party_master.group_code,group_master.name as group_name";
        $data['leftJoin']['group_master'] = "group_master.group_code = party_master.group_code";

		$data['where']['party_master.party_category'] = $data['party_category'];
        $data['group_by'][] = "party_master.id";
        
        if(in_array($data['party_category'], [1,2,3])){
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "party_master.city";
            $data['searchCol'][] = "party_master.business_type";
            $data['searchCol'][] = "party_master.contact_person";
            $data['searchCol'][] = "party_master.party_phone";
        }elseif($data['party_category'] == 4){
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "party_master.party_phone";
            $data['searchCol'][] = "party_master.whatsapp_no";
            $data['searchCol'][] = "party_master.party_email";
        }elseif($data['party_category'] == 5){
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "group_master.name";
        }

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function getPartyList($data=array()){
        $queryData = array();
        $queryData['tableName']  = $this->partyMaster;
        $queryData['select'] = "party_master.id, party_master.party_code, party_master.party_name, party_master.group_code, party_master.party_phone, party_master.party_category, party_master.party_type, party_master.state, party_master.city";
		
        $queryData['select'] .= ", (CASE WHEN party_master.party_category = 1 THEN 'Customer' WHEN party_master.party_category = 2 THEN 'Supplier' ELSE 'Vendor' END) as partyCategory";

        
        if(!empty($data['party_category'])):
            $queryData['where_in']['party_category'] = $data['party_category'];
        endif;
        
        if(!empty($data['group_code'])):
            $queryData['where_in']['party_master.group_code'] = $data['group_code'];
        endif;

        $queryData['order_by']['party_name'] = "ASC";

        if(!empty($data['party_type'])):
            $queryData['where_in']['party_type'] = $data['party_type'];
        else:
            $queryData['where']['party_type'] = 1;
        endif;

        return $this->rows($queryData);
    }

    public function getParty($data){
        $queryData = array();
        $queryData['tableName']  = $this->partyMaster;
        $queryData['select'] = "party_master.*";

        if(!empty($data['id'])):
            $queryData['where']['party_master.id'] = $data['id'];
        endif;

        if(!empty($data['party_category'])):
            $queryData['where_in']['party_master.party_category'] = $data['party_category'];
        endif;

        if(!empty($data['party_name'])):
            $queryData['where']['party_master.party_name'] = $data['party_name'];
        endif;

        return $this->row($queryData);
    }

    public function getCurrencyList(){
		$queryData['tableName'] = 'currency';
		return $this->rows($queryData);
	}

    public function getCountries(){
		$queryData['tableName'] = $this->countries;
		$queryData['order_by']['name'] = "ASC";
		return $this->rows($queryData);
	}

    public function getCountry($data){
		$queryData['tableName'] = $this->countries;
		$queryData['where']['id'] = $data['id'];
		return $this->row($queryData);
	}

    public function getStates($data=array()){
        $queryData['tableName'] = $this->states;
		if(!empty($data['country_id'])){$queryData['where']['country_id'] = $data['country_id'];}
		if(!empty($data['name'])){$queryData['where']['name'] = $data['name'];}
		$queryData['order_by']['name'] = "ASC";
        if(!empty($data['single_row'])){
            return $this->row($queryData);
        }
		return $this->rows($queryData);
    }

    public function getState($data){
        $queryData['tableName'] = $this->states;
		$queryData['where']['id'] = $data['id'];
		return $this->row($queryData);
    }

    public function getCities($data=array()){
        $queryData['tableName'] = $this->cities;
		if(!empty($data['state_id'])){$queryData['where']['state_id'] = $data['state_id'];}
		if(!empty($data['name'])){$queryData['where']['name'] = $data['name'];}
		$queryData['order_by']['name'] = "ASC";
		$result = $this->rows($queryData);
		 return $result;
    }

    public function getCity($data){
        $queryData['tableName'] = $this->cities;
        $queryData['select'] = 'cities.*,states.name as state_name,states.gst_statecode as state_code,countries.name as country_name';
        $queryData['leftJoin']['states'] = 'cities.state_id = states.id';
        $queryData['leftJoin']['countries'] = "countries.id = cities.country_id";
		$queryData['where']['cities.id'] = $data['id'];
		return $this->row($queryData);
    }

    public function getVillageList($data){
        $queryData = [];
        $queryData['tableName'] = $this->villages;

        if(!empty($data['country_id'])):
            $queryData['where']['country_id'] = $data['country_id'];
        endif;

        if(!empty($data['state_id'])):
            $queryData['where']['state_id'] = $data['state_id'];
        endif;

        if(!empty($data['city_id'])):
            $queryData['where']['city_id'] = $data['city_id'];
        endif;

        if(!empty($data['village_name'])):
            $queryData['like']['name'] = $data['village_name'];
        endif;

        $result = $this->rows($queryData);
        return $result;
    }

    public function getVillage($data){
        $queryData = [];
        $queryData['tableName'] = $this->villages;

        if(!empty($data['country_id'])):
            $queryData['where']['country_id'] = $data['country_id'];
        endif;

        if(!empty($data['state_id'])):
            $queryData['where']['state_id'] = $data['state_id'];
        endif;

        if(!empty($data['city_id'])):
            $queryData['where']['city_id'] = $data['city_id'];
        endif;

        if(!empty($data['village_name'])):
            $queryData['where']['name'] = $data['village_name'];
        endif;

        $result = $this->row($queryData);
        return $result;
    }

    public function save($data){
		try {
			$this->db->trans_begin();
            $opBalance = array();

			if ($this->checkDuplicate($data) > 0) :
				$errorMessage['party_name'] = "Company name is duplicate.";
				return ['status' => 0, 'message' => $errorMessage];
            endif;
			
			if (in_array($data['party_category'],[2,3]) && !empty($data['gstin']) && $this->checkDuplicateGST($data) > 0):
				$errorMessage['gstin'] = "GSTIN is duplicate.";
				return ['status' => 0, 'message' => $errorMessage];
            endif;
			
			$groupData = $this->groupMaster->getGroup($data['group_code']);
			
			if(!empty($groupData)){$data['group_id'] = $groupData->id;}
			
            $result = $this->store($this->partyMaster, $data, 'Party');
			
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    public function saveVillage($data){
        /* try {
			$this->db->trans_begin(); */

            $village = $this->getVillage($data);

			if (!empty($village)) :
				return "Village name found.";
            else:
                $villageData = [
                    'id' => "",
                    'name' => $data['village_name'],
                    'country_id' => $data['country_id'],
                    'state_id' => $data['state_id'],
                    'city_id' => $data['city_id']
                ];
            endif;
			
            if(!empty($data['village_name'])):
                $this->store($this->villages, $villageData, 'Village');
                return true;
            else:
                return "Village name is empty.";
            endif;
			
			/* if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return true;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		} */
    }

    public function checkDuplicate($data){
        $queryData['tableName'] = $this->partyMaster;
        $queryData['where']['party_name'] = $data['party_name'];
        
		if(!empty($data['party_category'])):
            $queryData['where']['party_category'] = $data['party_category']; 
        endif;
        
        if(!empty($data['id'])):
            $queryData['where']['id !='] = $data['id'];
        endif;
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

	public function checkDuplicateGST($data){
        $queryData['tableName'] = $this->partyMaster;
        
		if(!empty($data['gstin'])):
            $queryData['where']['gstin'] = $data['gstin']; 
        endif;
        
        if(!empty($data['id'])):
            $queryData['where']['id !='] = $data['id'];
        endif;
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
		try {
			$this->db->trans_begin();

            $checkData['columnName'] = ['party_id'];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Party is currently in use. you cannot delete it.'];
            endif;

			$result = $this->trash($this->partyMaster, ['id' => $id], 'Party');
			
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