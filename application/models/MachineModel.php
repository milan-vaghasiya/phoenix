<?php
class MachineModel extends MasterModel{
    private $machineMaster = "machine_master";

    public function getDTRows($data){
        $data['tableName'] = $this->machineMaster;
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "machine_name";
        $data['searchCol'][] = "remark";
		
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		
        return $this->pagingRows($data);
    }

    public function getMachine($data){
        $queryData['tableName'] = $this->machineMaster;
        $queryData['where']['id'] = $data['id'];
        return $this->row($queryData);
    }
	
    public function getMachineList($data=[]){
        $queryData['tableName'] = $this->machineMaster;
        return $this->rows($queryData);
    }

    public function save($data){
		try{
            $this->db->trans_begin();

            $result = $this->store($this->machineMaster, $data, 'Machine');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}

    public function delete($id){
		try{
            $this->db->trans_begin();

            $checkData['columnName'] = ["machine_ids"];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);
            
            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Machine is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->machineMaster, ['id'=>$id], 'Machine');

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