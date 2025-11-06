<?php
class WorkOrderModel extends MasterModel{
    private $workOrder = "work_order";
    private $workOrderTrans = "work_order_trans";
	private $transDetails = "trans_details";

	public function getDTRows($data){
        $data['tableName'] = $this->workOrder;
        $data['select'] = "work_order.*,party_master.party_name,project_master.project_name";
        
		$data['leftJoin']['party_master'] = "party_master.id = work_order.party_id";
		$data['leftJoin']['project_master'] = "project_master.id = work_order.project_id"; 

        $data['where']['work_order.status'] = $data['status'];

        $data['order_by']['work_order.trans_date'] = "DESC";
        $data['order_by']['work_order.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "work_order.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(work_order.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "project_master.project_name";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

	public function nextTransNo(){
		$data['tableName'] = $this->workOrder;
        $data['select'] = "MAX(trans_no) as trans_no";
		
		$data['where']['trans_date >='] = $this->startYearDate;
		$data['where']['trans_date <='] = $this->endYearDate;

		$trans_no = $this->specificRow($data)->trans_no;
		$trans_no = (empty($last_no))?($trans_no + 1):$trans_no;
		return $trans_no;
    }
	
    public function save($data){
        try{
            $this->db->trans_begin();

            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];
            
            if($this->checkDuplicate($data) > 0):
                $errorMessage['trans_number'] = "PO. No. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            if(!empty($data['id'])):
                $itemList = $this->getWorkOrderItems(['id'=>$data['id']]);
                foreach($itemList as $row):
                    $this->trash($this->workOrderTrans,['id'=>$row->id]);
                endforeach;

                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->workOrder,'description'=>"WO TERMS"]);
            endif;

            $itemData = $data['itemData'];
            $termsData = (!empty($data['termsData']))?$data['termsData']:array();

            unset($data['itemData'],$data['termsData']);
            $result = $this->store($this->workOrder,$data,'Work Order');
            
			if(!empty($termsData)):
                foreach($termsData as $row):
                    $row['id'] = "";
                    $row['table_name'] = $this->workOrder;
                    $row['description'] = "WO TERMS";
                    $row['main_ref_id'] = $result['id'];
                    $this->store($this->transDetails,$row);
                endforeach;
            endif;

            foreach($itemData as $row):
                $row['wo_id'] = $result['id'];
                $row['is_delete'] = 0;
                $this->store($this->workOrderTrans,$row);
            endforeach;            

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
        $queryData['tableName'] = $this->workOrder;
        $queryData['where']['trans_number'] = $data['trans_number'];
        $queryData['where']['trans_date >='] = $this->startYearDate;
        $queryData['where']['trans_date <='] = $this->endYearDate;

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getWorkOrder($data){
        $queryData = array();
        $queryData['tableName'] = $this->workOrder;
        $queryData['select'] = "work_order.*,project_master.project_name";
        $queryData['leftJoin']['project_master'] = "project_master.id = work_order.project_id";
        $queryData['where']['work_order.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
            $result->itemList = $this->getWorkOrderItems($data);
        endif;

		$queryData = array();
        $queryData['tableName'] = $this->transDetails;
        $queryData['select'] = "t_col_2 as condition, i_col_1 as term_id, t_col_1 as term_title";
        $queryData['where']['main_ref_id'] = $data['id'];
        $queryData['where']['table_name'] = $this->workOrder;
        $queryData['where']['description'] = "WO TERMS";
        $result->termsConditions = $this->rows($queryData);

        return $result;
    }

	public function getWorkOrderItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->workOrderTrans;
        $queryData['select'] = "work_order_trans.*,CONCAT('[',unit_master.unit_name,'] ',unit_master.description) as full_unit_name";
        $queryData['leftJoin']['unit_master'] = "unit_master.id = work_order_trans.unit_id";
		
        if (!empty($data['id'])) { $queryData['where']['work_order_trans.wo_id'] = $data['id']; }

        return $this->rows($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $itemList = $this->getWorkOrderItems(['id'=>$id]);
            foreach($itemList as $row):
                $this->trash($this->workOrderTrans,['id'=>$row->id]);
            endforeach;

            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->workOrder,'description'=>"WO TERMS"]);
            $result = $this->trash($this->workOrder,['id'=>$id],'Work Order');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
	
	public function closeWO($data) {
        try{
            $this->db->trans_begin();
            
			$this->edit($this->workOrder,['id'=>$data['id']],['status'=>$data['status']]);
			$result = ['status' => 1, 'message' => 'Work Order ' . $data['msg'] . ' Successfully.'];

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