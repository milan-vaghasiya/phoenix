<?php
class StoreModel extends MasterModel{

	/* STOCK ISSUE START */
    public function getNextIssueNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'issue_register';
        $queryData['select'] = "ifnull(MAX(issue_no + 1),1) as issue_no";
		$queryData['where']['issue_register.issue_date >='] = $this->startYearDate;
		$queryData['where']['issue_register.issue_date <='] = $this->endYearDate;

		$issue_no = $this->specificRow($queryData)->issue_no;
		return $issue_no;
    }

	public function getIssueDTRows($data){
        $data['tableName'] = 'issue_register';
        $data['select'] = "issue_register.*,item_master.item_name,item_master.item_code,project_master.project_name,party_master.party_name,item_category.is_return";
        
        $data['leftJoin']['item_master'] = "item_master.id  = issue_register.item_id";
        $data['leftJoin']['project_master'] = "project_master.id  = issue_register.project_id";
        $data['leftJoin']['party_master'] = "party_master.id  = issue_register.vendor_id";
        $data['leftJoin']['item_category'] = "item_category.id  = item_master.category_id";
        $data['order_by']['issue_register.issue_number'] = 'ASC';

		$data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "issue_register.issue_number";
        $data['searchCol'][] = "DATE_FORMAT(issue_register.issue_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name"; 
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "issue_register.issue_qty";
        $data['searchCol'][] = "issue_register.return_qty";
        $data['searchCol'][] = "issue_register.remark";
		
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getItemStockBatchWise($data){
        $queryData['tableName'] = 'stock_trans';
        $queryData['select'] = "stock_trans.item_id, item_master.item_code, item_master.item_name,item_master.uom, (SUM(TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM stock_trans.qty)) * stock_trans.p_or_m) + 0) as qty, stock_trans.location_id, item_category.category_name";
        
        $queryData['leftJoin']['item_master'] = "stock_trans.item_id = item_master.id";
        $queryData['leftJoin']['item_category'] = "item_master.category_id = item_category.id";

        if(!empty($data['item_id'])): 
            $queryData['where']['stock_trans.item_id'] = $data['item_id'];           
        endif;

        if(!empty($data['location_id'])):
            $queryData['where']['stock_trans.location_id'] = $data['location_id'];
        endif;

        if(!empty($data['location_ids'])):
            $queryData['where_in']['stock_trans.location_id'] = $data['location_ids'];
        endif;
		
        if(!empty($data['category_id'])): 
            $queryData['where']['item_master.category_id'] = $data['category_id'];           
        endif;
		
        if(isset($data['is_active'])): 
            $queryData['where']['item_master.is_active'] = $data['is_active'];           
        endif;
		
        if(!empty($data['p_or_m'])):
            $queryData['where']['stock_trans.p_or_m'] = $data['p_or_m'];
        endif;

        if(!empty($data['trans_type'])):
            $queryData['where']['stock_trans.trans_type'] = $data['trans_type'];
        endif;

        if(!empty($data['main_ref_id'])):
            $queryData['where']['stock_trans.main_ref_id'] = $data['main_ref_id'];
        endif;

        if(!empty($data['child_ref_id'])):
            $queryData['where']['stock_trans.child_ref_id'] = $data['child_ref_id'];
        endif;

        if(!empty($data['ref_no'])):
            $queryData['where']['stock_trans.ref_no'] = $data['ref_no'];
        endif;

        if(!empty($data['trans_date'])):
            $queryData['where']['stock_trans.trans_date'] = $data['trans_date'];
        endif;

        if(!empty($data['customWhere'])):
            $queryData['customWhere'][] = $data['customWhere'];
        endif;
        
        if(!empty($data['stock_required'])):
            $queryData['having'][] = 'SUM(stock_trans.qty * stock_trans.p_or_m) > 0';
        endif;

        if(!empty($data['group_by'])):
            $queryData['group_by'][] = $data['group_by'];
        endif;
		
		if(!empty($data['search'])):
            $queryData['like']['item_master.item_code'] = $data['search'];
            $queryData['like']['item_master.item_name'] = $data['search'];
            $queryData['like']['item_category.category_name'] = $data['search'];
        endif;
		
		if(!empty($data['limit'])): 
			$queryData['limit'] = $data['limit']; 
		endif;
		
		if(isset($data['start']) && isset($data['length'])):
			$queryData['start'] = $data['start'];
			$queryData['length'] = $data['length'];
		endif;
		
		if(!empty($data['result_type'])):
			return $this->getData($queryData,$data['result_type']);
		endif;
       
        if(isset($data['single_row']) && $data['single_row'] == 1):
            $stockData = $this->row($queryData);
        else:
            $stockData = $this->rows($queryData);
        endif;
		
		//$this->printQuery();
		
        return $stockData;
    }

    public function getMaterialIssueList($param=[]){
		
		$queryData['tableName'] = 'issue_register';
        $queryData['select'] = "issue_register.id, issue_register.issue_number, issue_register.issue_date, issue_register.item_id, issue_register.issue_qty, issue_register.issued_to, issue_register.remark, item_master.item_name, item_master.item_code, item_master.uom, project_master.project_name, employee_master.emp_name";
		
        $queryData['leftJoin']['item_master'] = "item_master.id = issue_register.item_id";
        $queryData['leftJoin']['project_master'] = "project_master.id = issue_register.project_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = issue_register.created_by";
						
        if(!empty($param['id'])):
            $queryData['where']['issue_register.id'] = $param['id'];
			$param['result_type'] = 'row';
		endif;
		
        if(!empty($param['project_id'])):
            $queryData['where']['issue_register.project_id'] = $param['project_id'];
		endif;
		
        if(!empty($param['item_id'])):
            $queryData['where']['issue_register.item_id'] = $param['item_id'];
		endif;
		
        if(!empty($param['issue_date'])):
            $queryData['where']['DATE(issue_register.issue_date)'] = $param['issue_date'];
		endif;
		
        if(!empty($param['from_date']) AND !empty($param['to_date'])):
            $queryData['where']['DATE(issue_register.issue_date) >= '] = $param['from_date'];
            $queryData['where']['DATE(issue_register.issue_date) <= '] = $param['to_date'];
		endif;
		
		if(!in_array($this->userRole,[-1,1])): 
            $queryData['where']['issue_register.created_by'] = $this->loginId;
		endif;
		
		if(!empty($param['search'])):
            $queryData['like']['project_master.project_name'] = $param['search'];
            $queryData['like']['item_master.item_name'] = $param['search'];
            $queryData['like']['issue_register.issue_date'] = $param['search'];
            $queryData['like']['DATE_FORMAT(issue_register.issue_date,"%d-%m-%Y")'] = $param['search'];
        endif;
		
		if(!empty($param['limit'])): 
			$queryData['limit'] = $param['limit']; 
		endif;
		
		if(isset($param['start']) && isset($param['length'])):
			$queryData['start'] = $param['start'];
			$queryData['length'] = $param['length'];
		endif;
		
		$queryData['order_by']['issue_register.issue_date'] = 'DESC';
		$queryData['order_by']['issue_register.id'] = 'DESC';
		
		if(!empty($param['result_type'])):
			$result = $this->getData($queryData,$param['result_type']);
		else:
			$result = $this->getData($queryData,'rows');
		endif;
		//$this->printQuery();
		return $result;
    }

    public function saveIssuedMaterial($data,$trans_prefix="ISU"){           
        try {
            $this->db->trans_begin();
			
            $data['issue_no'] = $this->getNextIssueNo();
            $data['issue_number'] =  $trans_prefix.'/'.$this->shortYear.'/'.$data['issue_no'];
			
            $result = $this->store('issue_register', $data, 'Issue Requisition');
            $stockMinusQuery = [
                'id' => "",
                'trans_type' =>'SSI',
                'trans_date' => $data['issue_date'],
                'location_id'=> $data['project_id'],
                'item_id' => $data['item_id'],
                'qty' => $data['issue_qty'],
                'p_or_m' => -1,
                'main_ref_id' => $result['insert_id'],
                'ref_no' => $data['issue_number'],
            ];
            $issueTrans = $this->store('stock_trans', $stockMinusQuery);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material issue Successfully.','issue_number' => $data['issue_number']];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteIssuedItem($data) {
        try {
            $this->db->trans_begin();
            $stockData = $this->remove('stock_trans', ['trans_type'=>'SSI', 'main_ref_id'=>$data['id']]);
            $this->trash('issue_register',['id'=>$data['id']], 'Delete Issued Item');

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Deleted suucessfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

	/* STOCK ISSUE END */

	/* STOCK TRASFER START */
	public function getNextTransferNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'stock_transfer_log';
        $queryData['select'] = "ifnull(MAX(trans_no + 1),1) as trans_no";
		$queryData['where']['stock_transfer_log.trans_date >='] = $this->startYearDate;
		$queryData['where']['stock_transfer_log.trans_date <='] = $this->endYearDate;

		$trans_no = $this->specificRow($queryData)->trans_no;
		return $trans_no;
    }
	
	public function getStockTransferDTRows($data){
        $data['tableName'] = 'stock_transfer_log';
        $data['select'] = "stock_transfer_log.*,item_master.item_name,item_master.item_code,project_master.project_name AS from_project_name,toProject.project_name AS to_project_name,employee_master.emp_name AS transfer_by";
        
        $data['leftJoin']['item_master'] = "item_master.id  = stock_transfer_log.item_id";
        $data['leftJoin']['project_master'] = "project_master.id  = stock_transfer_log.from_project_id ";
        $data['leftJoin']['project_master toProject'] = "toProject.id  = stock_transfer_log.to_project_id ";
        $data['leftJoin']['employee_master'] = "employee_master.id  = stock_transfer_log.created_by";

        $data['order_by']['stock_transfer_log.trans_date'] = 'DESC';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
		$data['searchCol'][] = "stock_transfer_log.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(stock_transfer_log.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "project_master.project_name";
        $data['searchCol'][] = "toProject.project_name";
        $data['searchCol'][] = "stock_transfer_log.qty";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "stock_transfer_log.issued_to";
        $data['searchCol'][] = "stock_transfer_log.remark";
		
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function saveTransferItem($data){
        try {
            $this->db->trans_begin();
          
			$data['trans_no'] = $this->getNextTransferNo();
            $data['trans_number'] =  'STR/'.$this->shortYear.'/'.$data['trans_no'];
			
            $result = $this->store('stock_transfer_log', $data);
			
			if(!empty($result['insert_id']))
			{
				$ref_id = $result['insert_id'];
				
				$stockIssueData = [
					'id' => $data['id'],
					'ref_id' => $ref_id,
					'issue_date' => $data['trans_date'],
					'project_id' => $data['from_project_id'],
					'item_id' => $data['item_id'],
					'issue_qty' => $data['qty'],
					'remark' => $data['remark']
				];
				
				$issueResult = $this->saveIssuedMaterial($stockIssueData,'STR');
				//$transResult = $this->store('stock_trans', $stockMinusQuery);
				
				$itemObj = New StdClass;
				$itemObj->id = "";
				$itemObj->item_id = $data['item_id'];
				$itemObj->qty = $data['qty'];
				$itemObj->po_trans_id = 0;
				
				$stockReceiveData = [
					"id" => $data['id'],
					'ref_id' => $ref_id,
					"trans_date" => $data['trans_date'],
					"party_id" => "0",
					"project_id" => $data['to_project_id'],
					"doc_no" => $issueResult['issue_number'],
					"item_data" => Array($itemObj),
					"remark" => $data['remark'],
					"po_id" => "0",
					"vehicle_no" => ""
				];
				
				$receiveResult = $this->gateInward->save($stockReceiveData,'STR');
				//$this->store('stock_trans', $stockPlusQuery);
			}
			else{
				return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
			}
			

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material transfered Successfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

	public function getStockTransferLog($param = []){
        $data['tableName'] = 'stock_transfer_log';
        $data['select'] = "stock_transfer_log.*,item_master.item_name,item_master.item_code,project_master.project_name AS from_project_name, toProject.project_name AS to_project_name, employee_master.emp_name AS transfer_by, item_master.uom,party_master.party_name as agency_name";
        
        $data['leftJoin']['item_master'] = "item_master.id  = stock_transfer_log.item_id";
        $data['leftJoin']['project_master'] = "project_master.id  = stock_transfer_log.from_project_id ";
        $data['leftJoin']['project_master toProject'] = "toProject.id  = stock_transfer_log.to_project_id ";
        $data['leftJoin']['employee_master'] = "employee_master.id  = stock_transfer_log.created_by ";
        $data['leftJoin']['party_master'] = "party_master.id  = stock_transfer_log.agency_id";

        if(!empty(!empty($param['id']))){ $data['where']['stock_transfer_log.id'] = $param['id']; }
        if(!empty($param['single_row'])){
            return $this->row($data);
        }else{
            return $this->rows($data);
        }  
    }

    public function deleteTransferedLog($data) {
        try {
            $this->db->trans_begin();
            $transData =  $this->getStockTransferLog(['id'=>$data['id'],'single_row'=>1]);
            $stockData = $this->store->getItemStockBatchWise(['item_id'=>$transData->item_id,'location_id'=>$transData->to_project_id,'stock_required'=>1,'single_row'=>1]);
            if($transData->qty > $stockData->qty){
                return ['status' => 0, 'message' => 'You can not delete this log, Stock is used'];
            }
            $this->remove('stock_trans', ['trans_type'=>'STR', 'main_ref_id'=>$data['id']]);
            $result = $this->trash('stock_transfer_log',['id'=>$data['id']], 'Delete Issued Item');

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Deleted suucessfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
	
	/* STOCK TRASFER END */
	
	public function getIssueMaterialData($param = []){
        $data['tableName'] = 'issue_register';
        $data['select'] = "issue_register.*,item_master.item_name,item_master.item_code,project_master.project_name,employee_master.emp_name,issue.emp_name as issue_to_name,item_master.uom,party_master.party_name";
        $data['leftJoin']['item_master'] = "item_master.id = issue_register.item_id";
        $data['leftJoin']['project_master'] = "project_master.id = issue_register.project_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = issue_register.created_by";
        $data['leftJoin']['employee_master issue'] = "issue.id = issue_register.issued_to";
        $data['leftJoin']['party_master'] = "party_master.id = issue_register.vendor_id";

        if(!empty($param['id'])) { $data['where']['issue_register.id'] = $param['id']; }

        return $this->row($data);
    }

	public function getItemWiseStockData($data){
        $queryData['tableName'] = 'stock_trans';
        $queryData['select'] = "stock_trans.item_id,stock_trans.location_id, item_master.item_code, item_master.item_name,
        SUM(CASE WHEN stock_trans.p_or_m = 1 AND stock_trans.trans_date = '".$data['trans_date']."' THEN stock_trans.qty ELSE 0 END) as inward, 
        SUM(CASE WHEN stock_trans.p_or_m = -1 AND stock_trans.trans_date = '".$data['trans_date']."' THEN stock_trans.qty ELSE 0 END) as uti, 
        SUM(CASE WHEN DATE(stock_trans.trans_date) < '".$data['trans_date']."' THEN (COALESCE(stock_trans.qty,0) * COALESCE(stock_trans.p_or_m,0)) ELSE 0 END) as preStock, 
        SUM(stock_trans.qty*stock_trans.p_or_m) as stock";


        $queryData['leftJoin']['item_master'] = "stock_trans.item_id = item_master.id";

        if(!empty($data['location_id'])):
            $queryData['where']['stock_trans.location_id'] = $data['location_id'];
        endif;

        if(!empty($data['group_by'])):
            $queryData['group_by'][] = $data['group_by'];
        endif;
        $queryData['having'][] = 'inward <> 0 OR uti <> 0';
        $stockData = $this->rows($queryData);
        
        return $stockData;
    }
	
	// Material Return STart
    public function saveMaterialReturn($data){           
        try {
            $this->db->trans_begin();
			
            $issueData = $this->store->getIssueMaterialData(['id'=>$data['issue_id']]);
           
            $stockPlusQuery = [
                'id' => "",
                'trans_type' =>'RTN',
                'trans_date' =>$issueData->issue_date,
                'location_id'=> $issueData->project_id,
                'item_id' => $issueData->item_id,
                'qty' => $data['return_qty'],
                'p_or_m' => 1,
                'main_ref_id' => $data['issue_id'],
                'ref_no' => $issueData->issue_number,
                'remark' => $data['remark'],
            ];
            $issueTrans = $this->store('stock_trans', $stockPlusQuery);

            $setData = array();
            $setData['tableName'] = 'issue_register';
            $setData['where']['id'] = $data['issue_id'];
            $setData['set']['return_qty'] = 'return_qty, + '.$data['return_qty'];
            $this->setValue($setData);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material Return Successfully.','issue_number' => $issueData->issue_number];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteMaterialReturn($data){
        try{
            $this->db->trans_begin();

            
            $result = $this->remove('stock_trans', ['trans_type'=>'RTN', 'id'=>$data['id']]);

            $setData = array();
            $setData['tableName'] = 'issue_register';
            $setData['where']['id'] = $data['issue_id'];
            $setData['set']['return_qty'] = 'return_qty, - '.$data['qty'];
            $this->setValue($setData);

           
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    // Material Return END

    // Opening Stock START
    public function getOpeningStockDTRows($data){

        $data['tableName'] = 'stock_trans';
        $data['select'] = "stock_trans.*,item_master.item_name,item_master.item_code,project_master.project_name,employee_master.emp_name as created_name";
        
        $data['leftJoin']['item_master'] = "item_master.id  = stock_trans.item_id";
        $data['leftJoin']['project_master'] = "project_master.id  = stock_trans.location_id";
        $data['leftJoin']['employee_master'] = "employee_master.id  = stock_trans.created_by";

        $data['where']['stock_trans.trans_type'] = 'OPS';
        $data['where']['stock_trans.p_or_m'] = 1;
        $data['order_by']['stock_trans.trans_date'] = 'DESC';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "project_master.project_name";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "stock_trans.qty";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "stock_trans.created_at";
		
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
  
    public function getStockTrans($data){
        $queryData['tableName'] = 'stock_trans';
        $queryData['select'] = "stock_trans.*,item_master.item_name,item_master.item_code,project_master.project_name";

        $queryData['leftJoin']['item_master'] = "item_master.id  = stock_trans.item_id";
        $queryData['leftJoin']['project_master'] = "project_master.id  = stock_trans.location_id";
        if(!empty($data['id'])) { $queryData['where']['id'] = $data['id']; }
        if(!empty($data['main_ref_id'])) { $queryData['where']['main_ref_id'] = $data['main_ref_id']; }
        
        if(!empty($data['trans_type'])) { $queryData['where']['trans_type'] = $data['trans_type']; }

        if(!empty($data['multi_rows'])){
            return $this->rows($queryData);
        }else{
            return $this->row($queryData);
        }
        
    }

    public function saveOpeningStock($data){           
        try {
            $this->db->trans_begin();

            $stockPlusQuery = [
                'id' => "",
                'trans_type' =>'OPS',
                'trans_date' =>date('Y-m-d'),
                'location_id'=> $data['project_id'],
                'item_id'=> $data['item_id'],
                'qty' => $data['qty'],
                'p_or_m' => 1,
            ];
            $result = $this->store('stock_trans', $stockPlusQuery);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result ;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    public function deleteOpeningStock($data){
        try{
            $this->db->trans_begin();

            $transData = $this->getStockTrans(['id'=>$data['id']]);
            $itemStock = $this->getItemStockBatchWise(['item_id'=>$transData->item_id,'location_id'=>$transData->location_id,'single_row'=>1]);

            if($transData->qty > $itemStock->qty):
                return ['status'=>0,'message'=>'Item Stock Used. You cant delete this record.'];
            endif;

            $result = $this->trash('stock_trans',['id'=>$data['id'],'trans_type'=>'OPS'],'Stock');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
    // Opening Stock End
}
?>