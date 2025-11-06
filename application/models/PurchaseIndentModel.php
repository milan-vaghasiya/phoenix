<?php
class PurchaseIndentModel extends MasterModel
{
    private $purchase_indent = "purchase_indent";
	
	public function getNextIndentNo(){
        $queryData['tableName'] = $this->purchase_indent;
        $queryData['select'] = "ifnull(MAX(trans_no + 1),1) as next_no";
        $queryData['where']['trans_date >='] = $this->startYearDate;
        $queryData['where']['trans_date <='] = $this->endYearDate;
        return $this->row($queryData)->next_no;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->purchase_indent;
		$data['select'] = "purchase_indent.*,item_master.item_code,item_master.item_name,item_master.uom,created.emp_name as created_name,project_master.project_name";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_indent.item_id";
		$data['leftJoin']['employee_master created'] = "created.id = purchase_indent.created_by";
		$data['leftJoin']['project_master'] = "project_master.id = purchase_indent.project_id";

        $data['where']['purchase_indent.trans_status'] = $data['status'];

        $data['where']['purchase_indent.trans_date >='] = $this->startYearDate;
        $data['where']['purchase_indent.trans_date <='] = $this->endYearDate;

        $data['order_by']['purchase_indent.trans_date'] = "DESC";
        $data['order_by']['purchase_indent.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "purchase_indent.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(purchase_indent.trans_date,'%d-%m-%Y')";
		$data['searchCol'][] = "project_master.project_name";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "purchase_indent.qty";
        $data['searchCol'][] = "DATE_FORMAT(purchase_indent.delivery_date,'%d-%m-%Y')";
        $data['searchCol'][] = "purchase_indent.remark";
		$data['searchCol'][] = "created.emp_name";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            
			if(!empty($data['item_data']))
			{
				$itemData = ($data['item_data']);unset($data['item_data']);
				if(!empty($itemData) && gettype($itemData) == "string"): $itemData = json_decode($itemData,true); endif;
				
				foreach($itemData as $row)
				{
					$data['item_id'] = $row['item_id'];
					$data['qty'] = $row['qty'];
					$data['delivery_date'] = (!empty($row['delivery_date']) ? $row['delivery_date'] : NULL);
					$data['remark'] = (!empty($row['remark']) ? $row['remark'] : NULL);
					$result = $this->store($this->purchase_indent, $data, 'purchase Indent');
				}
			}else{
                $result = $this->store($this->purchase_indent, $data, 'purchase Indent');
            }
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

	public function getPurchaseIndent($data){
        $data['tableName'] = $this->purchase_indent;
        $data['select'] = "purchase_indent.*,item_master.item_type,item_master.item_name,item_master.uom";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_indent.item_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.item_type";
        if(!empty($data['id'])):
            $data['where']['purchase_indent.id'] = $data['id'];
        endif;
        return $this->row($data);
    }
	
    public function getMRList($param=[]){
        $queryData['tableName'] = $this->purchase_indent;
        $queryData['select'] = "purchase_indent.*, project_master.project_name, item_master.item_type,item_master.item_name,item_master.uom, employee_master.emp_name as request_by, apr.emp_name as approved_name";//08-10-2024
        $queryData['leftJoin']['project_master'] = "project_master.id = purchase_indent.project_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = purchase_indent.item_id";
        $queryData['leftJoin']['item_category'] = "item_category.id = item_master.item_type";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = purchase_indent.created_by";
        $queryData['leftJoin']['employee_master apr'] = "apr.id = purchase_indent.approved_by";
		
		if(isset($param['trans_status'])):
			$queryData['where']['purchase_indent.trans_status'] = $param['trans_status'];
		endif;
				
        if(!empty($param['id'])):
            $queryData['where']['purchase_indent.id'] = $param['id'];
			$param['result_type'] = 'row';
		endif;
		
        if(!empty($param['project_id'])):
            $queryData['where']['purchase_indent.project_id'] = $param['project_id'];
		endif;
		
        if(!empty($param['item_id'])):
            $queryData['where']['purchase_indent.item_id'] = $param['item_id'];
		endif;
		
        if(!empty($param['approved_by'])):
            $queryData['where']['purchase_indent.approved_by'] = $param['approved_by'];
		endif;
		
        if(!empty($param['trans_date'])):
            $queryData['where']['DATE(purchase_indent.trans_date)'] = $param['trans_date'];
		endif;
		
        if(!empty($param['from_date']) AND !empty($param['to_date'])):
            $queryData['where']['DATE(purchase_indent.trans_date) >= '] = $param['from_date'];
            $queryData['where']['DATE(purchase_indent.trans_date) <= '] = $param['to_date'];
		endif;
		
		if(!empty($param['search'])):
            $queryData['like']['project_master.project_name'] = $param['search'];
            $queryData['like']['item_master.item_name'] = $param['search'];
            $queryData['like']['purchase_indent.trans_date'] = $param['search'];
            $queryData['like']['DATE_FORMAT(purchase_indent.delivery_date,"%d-%m-%Y")'] = $param['search'];
        endif;
		
		if(!empty($param['limit'])): 
			$queryData['limit'] = $param['limit']; 
		endif;
		
		if(isset($param['start']) && isset($param['length'])):
			$queryData['start'] = $param['start'];
			$queryData['length'] = $param['length'];
		endif;
		
		$queryData['order_by']['purchase_indent.trans_date'] = 'DESC';
		$queryData['order_by']['purchase_indent.id'] = 'DESC';
		
		if(!empty($param['result_type'])):
			$result = $this->getData($queryData,$param['result_type']);
		else:
			$result = $this->getData($queryData,'rows');
		endif;
		//$this->printQuery();
		return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $result = $this->trash($this->purchase_indent, ['id'=>$id], 'Purchase Indent');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

	public function changeReqStatus($postData){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->purchase_indent,$postData,'Purchase Request');
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function approveIndent($data) {
        try{
            $this->db->trans_begin();

            $this->store($this->purchase_indent, $data);
			
			$msg = (($data['trans_status'] == 4) ? "Approved" : "Closed" );

            $result = ['status' => 1, 'message' => 'Purchase Indent ' . $msg . ' Successfully.'];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	    
    }

    public function getPurchaseIndentForOrder($id){
        $data['tableName'] = $this->purchase_indent;
        $data['select'] = "purchase_indent.*,item_master.item_name,item_master.item_code,item_master.gst_per,item_master.price,item_master.uom,item_master.hsn_code,item_master.item_type,item_category.category_name";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_indent.item_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.item_type";
        $data['where_in']['purchase_indent.id'] = str_replace("~", ",", $id);
        $result = $this->rows($data);
        return $result;
    }
	
	public function getEmpWiseSubMenuPermission($data=[]){
        $queryData['tableName'] = 'sub_menu_permission';
		$queryData['select'] = "sub_menu_permission.is_write";
		$queryData['leftJoin']['sub_menu_master'] = "sub_menu_master.id = sub_menu_permission.sub_menu_id";

        if (!empty($data['emp_id'])) { $queryData['where']['sub_menu_permission.emp_id'] = $data['emp_id']; }

        if (!empty($data['sub_controller_name'])) { $queryData['where']['sub_menu_master.sub_controller_name'] = $data['sub_controller_name']; }

        return $this->row($queryData);
    }
}
?>