<?php
class ShiftModel extends MasterModel{
    private $shiftMaster = "shift_master";

    public function getDTRows($data){		
        $data['tableName'] = $this->shiftMaster;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "shift_name";
        $data['searchCol'][] = "shift_start";
        $data['searchCol'][] = "shift_end";
        $data['searchCol'][] = "production_hour";
        $data['searchCol'][] = "lunch_start";
        $data['searchCol'][] = "lunch_end";
        $data['serachCol'][] = "late_in";
        $data['serachCol'][] = "early_out";
        $data['serachCol'][] = "lunch_grace";
        $data['serachCol'][] = "late_fine";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }


    public function getShiftList($data=[]){
        $queryData['tableName'] = $this->shiftMaster;

		if(!empty($data['id'])):
            $queryData['where']['shift_master.id'] = $data['id'];
			$data['result_type'] = 'row';
		endif;

		if(!empty($data['result_type'])):
			$result = $this->getData($queryData,$data['result_type']);
		else:
			$result = $this->getData($queryData,'rows');
		endif;
		return $result;

    }

    public function save($data){
		try {
            $this->db->trans_begin();
            
			if($this->checkDuplicate($data) > 0):
				$errorMessage['shift_name'] = "Shift Name is duplicate.";
				return ['status'=>0,'message'=>$errorMessage];
			endif;

            $result = $this->store($this->shiftMaster,$data,'Shift');
              
			
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        }catch (\Throwable $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }        
    }

    public function checkDuplicate($data){
        $queryData['tableName'] = $this->shiftMaster;
        $queryData['where']['shift_name'] = $data['shift_name'];
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
		try{
            $this->db->trans_begin();

            $checkData['columnName'] = ['shift_id'];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Shift is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->shiftMaster,['id'=>$id],'Shift');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

}
?>