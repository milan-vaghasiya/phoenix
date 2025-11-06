<?php
class BuildTypeModel extends MasterModel{
    private $buildTypeMaster = "build_type_master";

    public function getDTRows($data){
        $data['tableName'] = $this->buildTypeMaster;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "build_type";
        $data['searchCol'][] = "description";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getBuildTypeDetail($data){
        $queryData['tableName'] = $this->buildTypeMaster;

        if(!empty($data['id'])):
            $queryData['where']['id'] = $data['id'];
        endif;

        if(!empty($data['build_type'])):
            $queryData['where']['build_type'] = $data['build_type'];
        endif;

        return $this->row($queryData);
    }

    public function getBuildTypeList($data = []){
        $queryData['tableName'] = $this->buildTypeMaster;
        $queryData['select'] = "id,build_type";
        return $this->rows($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['build_type'] = "Build Type is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            $result = $this->store($this->buildTypeMaster,$data,'Build Type');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function checkDuplicate($data){
        $queryData['tableName'] = $this->buildTypeMaster;
        $queryData['where']['build_type'] = $data['build_type'];

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($data){
        try{
            $this->db->trans_begin();

            $hsnData = $this->getHSNDetail(['id'=>$id]);

            $checkData['columnName'] = ["build_type_id"];
            $checkData['value'] = $hsnData->hsn;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Build Type is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->buildTypeMaster,['id'=>$id],'Build Type');

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