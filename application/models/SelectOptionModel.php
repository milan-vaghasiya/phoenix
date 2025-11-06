<?php
class SelectOptionModel extends MasterModel
{     
    private $select_master = "select_master"; 

    public function getDTRows($data){
        $data['tableName'] = $this->select_master;
        $data['where']['type'] = $data['type'];
		
		if(!empty($data['type']) && $data['type'] == 2){
			$data['order_by']['select_master.sequence'] = 'ASC';
		}
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "detail";
        $data['searchCol'][] = "remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getSelectOption($data){
        $queryData['tableName'] = $this->select_master;
        if(!empty($data['id'])){ $queryData['where']['id'] = $data['id']; }
		if(!empty($data['detail'])){ $queryData['where']['detail'] = $data['detail']; }
        return $this->row($queryData);
    }

    public function getSelectOptionList($data){
		
		$queryData['tableName'] = $this->select_master;
        
		if(!empty($data['select'])):
			$queryData['select'] = $data['select'];
		elseif(!empty($data['selectbox'])):
			$queryData['select'] = "select_master.id, select_master.detail, select_master.type, select_master.remark, select_master.is_active";
		else:
			$queryData['select'] = "select_master.*";
		endif;
		
        if(!empty($data['id'])){ $queryData['where']['id'] = $data['id']; }
        if(!empty($data['ids'])){ $queryData['where_in']['id'] = $data['ids']; }
		
        if(!empty($data['is_active'])){ $queryData['where']['is_active'] = $data['is_active']; }
		else{$queryData['where']['is_active'] = 1;}
        
        if(!empty($data['type'])){
            $queryData['where']['type'] = $data['type'];
        }else{ 
			$queryData['where']['type <='] = 5;
		}

		if(!empty($data['order_by'])){ $queryData['order_by']['select_master.sequence'] = $data['order_by']; }

        if(!empty($data['id'])):
            $result = $this->row($queryData);
        else:
            $result = $this->rows($queryData);
        endif;
		
		return $result;
    }

    public function getLaborCategoriesAPP($param){
		
		$queryData['tableName'] = $this->select_master;
        
		$queryData['select'] = "select_master.id as lab_cat_id, select_master.detail as labor_cat_name, IFNULL(la.present,0) as present";
		$queryData['leftJoin']['(SELECT present, labor_cat_id FROM labor_attendance WHERE project_id = '.$param['project_id'].' AND agency_id = '.$param['agency_id'].' AND work_id = '.$param['work_id'].' AND trans_date = "'.$param['trans_date'].'" AND is_delete=0) as la'] = "la.labor_cat_id = select_master.id";
		
        if(!empty($param['ids'])){ $queryData['where_in']['select_master.id'] = $param['ids']; }
		
        if(!empty($param['is_active'])){ $queryData['where']['select_master.is_active'] = $param['is_active']; }
		else{$queryData['where']['select_master.is_active'] = 1;}
        
        $queryData['where']['select_master.type'] = 2;

        $result = $this->rows($queryData);
		//$this->printQuery();
		return $result;
    }

    public function save($data){
        try{
            $this->db->trans_begin();
			
            $result = $this->store($this->select_master,$data,'Select Option');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function delete($id){
        try {
            $this->db->trans_begin();

            $result = $this->trash($this->select_master, ['id' => $id], 'Select Option');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;

        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

	public function updateCategorySequance($data){
		try{
            $this->db->trans_begin();
    		$ids = explode(',', $data['id']);
            
    		$i=1;
    		foreach($ids as $category_id):
    			$seqData=Array("sequence"=>$i++);
    			$this->edit($this->select_master,['id'=>$category_id],$seqData);
    		endforeach;

    		$result = ['status'=>1,'message'=>'Category updated successfully.'];

    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
	}
	
}
?>