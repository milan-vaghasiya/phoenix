<?php
class PurchaseOrderModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $transExpense = "trans_expense";
    private $transDetails = "trans_details";
    private $purchseReq = "purchase_request";

	public function getDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.id as trans_child_id, trans_child.qty, trans_child.item_remark, trans_child.trans_status, trans_child.item_code, trans_child.item_name, trans_child.dispatch_qty, (trans_child.qty - IFNULL(trans_child.dispatch_qty,0)) as pending_qty, trans_main.id, trans_main.trans_number, DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y') as trans_date, trans_main.party_name, trans_main.net_amount, trans_main.sales_type, project_master.project_name, trans_main.is_approve, (CASE WHEN trans_child.from_entry_type = 130 THEN purchase_enquiry.trans_number ELSE purchase_indent.trans_number END) as enq_number, party_master.party_name";
        
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id"; 
		$data['leftJoin']['project_master'] = "project_master.id = trans_main.project_id"; 
        $data['leftJoin']['purchase_enquiry'] = "purchase_enquiry.id = trans_child.ref_id"; 
        $data['leftJoin']['purchase_indent'] = "purchase_indent.id = trans_child.req_id";

        $data['where']['trans_main.entry_type'] = $data['entry_type'];
        $data['where']['trans_main.trans_date >='] = $this->startYearDate;
        $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        
        $data['where']['trans_child.trans_status'] = $data['status'];

        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "project_master.project_name";
        $data['searchCol'][] = "CONCAT('[ ',trans_child.item_code,' ] ',trans_child.item_name)";        
		$data['searchCol'][] = "trans_child.qty";
		$data['searchCol'][] = "trans_child.dispatch_qty";
		$data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.net_amount";
        $data['searchCol'][] = "IF(trans_child.from_entry_type = 130, purchase_enquiry.trans_number, purchase_indent.trans_number)";
        $data['searchCol'][] = "trans_child.item_remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            /* $generate_agreement = 0;
            if(isset($data['generate_agreement'])):
                $generate_agreement = $data['generate_agreement'];
                unset($data['generate_agreement']);
            endif; */

            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];
            
            if($this->checkDuplicate($data) > 0):
                $errorMessage['trans_number'] = "PO. No. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            if(!empty($data['id'])):
                $itemList = $this->getPurchaseOrderItems(['id'=>$data['id']]);
                foreach($itemList as $row):
                    if(!empty($row->ref_id)):
                        $this->edit('purchase_enquiry', ['id'=>$row->ref_id], ['trans_status'=>2]);
                    endif;

                     if(!empty($row->req_id)):
                        $setData = array();
                        $setData['tableName'] = 'purchase_indent';
                        $setData['where']['id'] = $row->req_id;
                        $setData['set']['po_qty'] = 'po_qty, - '.$row->qty;
                        $setData['update']['trans_status'] = 4;
                        $this->setValue($setData);
                    endif;
                    $this->trash($this->transChild,['id'=>$row->id]);
                endforeach;

                $this->trash($this->transExpense,['trans_main_id'=>$data['id']]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"PO TERMS"]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"PO MASTER DETAILS"]);
            endif;

            $data['gstin'] = (!empty($data['gstin']))?$data['gstin']:"URP";
            $data['disc_amount'] = array_sum(array_column($data['itemData'],'disc_amount'));;
            $data['total_amount'] = $data['taxable_amount'] + $data['disc_amount'];
            $data['igst_amount'] = array_sum(array_column($data['itemData'],'igst_amount'));
            $data['cgst_amount'] = array_sum(array_column($data['itemData'],'cgst_amount'));
            $data['sgst_amount'] = array_sum(array_column($data['itemData'],'sgst_amount'));
            $data['gst_amount'] = $data['igst_amount'];
            $data['net_amount'] = $data['taxable_amount'] + $data['igst_amount'];
            $masterDetails = $data['masterDetails'];
            $itemData = $data['itemData'];

            $transExp = getExpArrayMap(((!empty($data['expenseData']))?$data['expenseData']:array()));
			$expAmount = $transExp['exp_amount'];
            $termsData = (!empty($data['termsData']))?$data['termsData']:array();

            unset($transExp['exp_amount'],$data['itemData'],$data['expenseData'],$data['termsData'],$data['masterDetails']);		

            $result = $this->store($this->transMain,$data,'Purchase Order');

            $masterDetails['id'] = "";
            $masterDetails['main_ref_id'] = $result['id'];
            $masterDetails['table_name'] = $this->transMain;
            $masterDetails['description'] = "PO MASTER DETAILS";
            $this->store($this->transDetails,$masterDetails);

            $expenseData = array();
            if($expAmount <> 0):				
				$expenseData = $transExp;
                $expenseData['id'] = "";
				$expenseData['trans_main_id'] = $result['id'];
                $this->store($this->transExpense,$expenseData);
			endif;

            if(!empty($termsData)):
                foreach($termsData as $row):
                    $row['id'] = "";
                    $row['table_name'] = $this->transMain;
                    $row['description'] = "PO TERMS";
                    $row['main_ref_id'] = $result['id'];
                    $this->store($this->transDetails,$row);
                endforeach;
            endif;

            foreach($itemData as $row):
                $row['entry_type'] = $data['entry_type'];
                $row['trans_main_id'] = $result['id'];
                $row['is_delete'] = 0;
                $this->store($this->transChild,$row);

                if(!empty($row['ref_id'])):
                    $enqData = $this->purchase->getPurchaseEnqList(['id'=>$row['ref_id'], 'single_row'=>1]);

                    $poData = $this->getPurchaseOrderItems(['ref_id'=>$row['ref_id']]);
                    $po_qty = (!empty($poData) ? array_sum(array_column($poData,'qty')) : 0);

                    if($enqData->qty <= $po_qty):
                        $this->edit('purchase_enquiry', ['id'=>$row['ref_id']], ['trans_status'=>5]);
                    endif;
                endif;

                if(!empty($row['req_id'])):
                    $reqData = $this->purchaseIndent->getPurchaseIndent(['id'=>$row['req_id']]);

                    $poData = $this->getPurchaseOrderItems(['req_id'=>$row['req_id']]);
                    $po_qty = (!empty($poData) ? array_sum(array_column($poData,'qty')) : 0);

                    if(!empty($row['req_id'])):
						$setData = array();
						$setData['tableName'] = 'purchase_indent';
						$setData['where']['id'] = $row['req_id']; 
						$setData['set']['po_qty'] = 'po_qty, + '.$row['qty'];
						$setData['update']['trans_status'] = "(CASE WHEN po_qty >= qty THEN 2 ELSE 4 END)";
						$this->setValue($setData);
					endif;
                endif;
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
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['trans_number'] = $data['trans_number'];
        $queryData['where']['entry_type'] = $data['entry_type'];
        $queryData['where']['trans_date >='] = $this->startYearDate;
        $queryData['where']['trans_date <='] = $this->endYearDate;

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getPurchaseOrder($data){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.*, trans_details.i_col_1 as transport_id, transport_master.transport_name, transport_master.transport_id as transport_gstin, trans_details.t_col_3 as delivery_address,employee_master.emp_name as prepared_by,apv.emp_name as approved_by, IFNULL(trans_details.t_col_3,'') as ship_to";
        $queryData['leftJoin']['trans_details'] = "trans_main.id = trans_details.main_ref_id AND trans_details.description = 'PO MASTER DETAILS' AND trans_details.table_name = '".$this->transMain."'";
        $queryData['leftJoin']['transport_master'] = "trans_details.i_col_1 = transport_master.id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = trans_main.created_by";
        $queryData['leftJoin']['employee_master apv'] = "apv.id = trans_main.is_approve";
        $queryData['where']['trans_main.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
            $result->itemList = $this->getPurchaseOrderItems($data);
        endif;

        $queryData = array();
        $queryData['tableName'] = $this->transExpense;
        $queryData['where']['trans_main_id'] = $data['id'];
        $result->expenseData = $this->row($queryData);

		$queryData = array();
        $queryData['tableName'] = $this->transDetails;
        $queryData['select'] = "t_col_2 as condition, i_col_1 as term_id, t_col_1 as term_title";
        $queryData['where']['main_ref_id'] = $data['id'];
        $queryData['where']['table_name'] = $this->transMain;
        $queryData['where']['description'] = "PO TERMS";
        $result->termsConditions = $this->rows($queryData);

        return $result;
    }

	public function getPurchaseOrderItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*, item_master.make_brand, item_master.uom, (CASE WHEN trans_child.from_entry_type = 130 THEN purchase_enquiry.trans_number ELSE purchase_indent.trans_number END) as ref_number";
        $queryData['leftJoin']['purchase_enquiry'] = "purchase_enquiry.id = trans_child.ref_id"; 
        $queryData['leftJoin']['purchase_indent'] = "purchase_indent.id = trans_child.req_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
		
        if (!empty($data['id'])) { $queryData['where']['trans_child.trans_main_id'] = $data['id']; }

        if (!empty($data['ref_id'])) { $queryData['where']['trans_child.ref_id'] = $data['ref_id']; }

        if (!empty($data['req_id'])) { $queryData['where']['trans_child.req_id'] = $data['req_id']; }

        $result = $this->rows($queryData);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $itemList = $this->getPurchaseOrderItems(['id'=>$id]);
            foreach($itemList as $row):
                if(!empty($row->ref_id)):
                    $this->edit('purchase_enquiry', ['id'=>$row->ref_id], ['trans_status'=>2]);
                endif;

                if(!empty($row->req_id)):
                    $setData = array();
                    $setData['tableName'] = 'purchase_indent';
                    $setData['where']['id'] = $row->req_id;
                    $setData['set']['po_qty'] = 'po_qty, - '.$row->qty;
                    $setData['update']['trans_status'] = 4;
                    $this->setValue($setData);
                endif;
                $this->trash($this->transChild,['id'=>$row->id]);
            endforeach;

            $this->trash($this->transExpense,['trans_main_id'=>$id]);
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"PO TERMS"]);
            $result = $this->trash($this->transMain,['id'=>$id],'Purchase Order');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getItemWisePoList($data){
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_main.id as po_id,trans_main.trans_number,trans_child.id as po_trans_id,trans_child.qty,trans_child.dispatch_qty as received_qty,(trans_child.qty - trans_child.dispatch_qty) as pending_qty,trans_child.price,trans_child.disc_per";

        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";

        $queryData['where']['trans_child.entry_type'] = 21;

        $queryData['where']['trans_child.item_id'] = $data['item_id'];
        $queryData['where']['(trans_child.qty - trans_child.dispatch_qty) >'] = 0;

        return $this->rows($queryData);
    }

    public function getPartyWisePoList($data){
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.id as po_id,trans_main.trans_number";

        $queryData['where']['trans_main.entry_type'] = 21;
        $queryData['where']['trans_main.party_id'] = $data['party_id'];
		if(!empty($data['project_id'])){ $queryData['where']['trans_main.project_id'] = $data['project_id']; }
		if(empty($data['po_id'])){ $queryData['where']['trans_main.trans_status'] = 3; }
		$queryData['where']['trans_main.is_approve >'] = 0;
		
        return $this->rows($queryData);
    }

    public function getPendingPoItems($data){
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.id as po_trans_id,trans_child.item_id,item_master.item_code,item_master.item_name,item_master.uom, trans_child.qty,trans_child.dispatch_qty as received_qty,(trans_child.qty - trans_child.dispatch_qty) as pending_qty,trans_child.price,trans_child.disc_per, IFNULL(employee_master.emp_name,'') as request_by";

        $queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $queryData['leftJoin']['purchase_indent'] = "purchase_indent.id = trans_child.req_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = purchase_indent.created_by";

        $queryData['where']['trans_child.entry_type'] = 21;
        $queryData['where']['trans_child.trans_main_id'] = $data['po_id'];
        $queryData['where']['(trans_child.qty - trans_child.dispatch_qty) >'] = 0;
        $result = $this->rows($queryData);
		//$this->printQuery();
		return $result;
    }
	
	public function approvePO($data) {
        try{
            $this->db->trans_begin();

            $date = ($data['is_approve'] == 1) ? date('Y-m-d') : NULL;
            $isApprove =  ($data['is_approve'] == 1) ? $this->loginId : 0;
            
			if($data['trans_status'] == 3){
				$this->store($this->transMain, ['id'=> $data['id'],'trans_status'=>$data['trans_status'], 'is_approve' => $isApprove, 'approve_date'=>$date]);
				$this->edit($this->transChild,['trans_main_id'=>$data['id'],'trans_status'=>0],['trans_status'=>$data['trans_status']]);
            }else{
				$this->store($this->transMain, ['id'=> $data['id'],'trans_status'=>$data['trans_status'], 'is_approve' => $isApprove, 'approve_date'=>$date]);
				$this->edit($this->transChild,['trans_main_id'=>$data['id'],'trans_status'=>3],['trans_status'=>$data['trans_status']]);
			}
            $result = ['status' => 1, 'message' => 'Purchase Order ' . $data['msg'] . ' Successfully.'];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	    
    }

    public function changeOrderStatus($data){ 
        try{
            $this->db->trans_begin();

            $result = $this->edit($this->transChild, ['id'=>$data['id']], ['trans_status'=>$data['trans_status']]);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
	
    public function getPOList($param=[]){
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.id, trans_main.trans_date, trans_main.trans_number, trans_main.net_amount, trans_main.party_id, party_master.party_name, party_master.party_phone, project_master.project_name, project_master.project_name, IFNULL(employee_master.emp_name,'') as purchase_by";
        
        $queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";        
        $queryData['leftJoin']['project_master'] = "project_master.id = trans_main.project_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = trans_main.created_by";
		
		if(isset($param['trans_status'])):
			$queryData['where']['trans_main.trans_status'] = $param['trans_status'];
		endif;
		
		if(isset($param['is_approve'])):
			$queryData['where']['trans_main.is_approve > '] = "0";
		endif;
				
        if(!empty($param['id'])):
            $queryData['where']['trans_main.id'] = $param['id'];
			$param['result_type'] = 'row';
		endif;
		
        if(!empty($param['project_id'])):
            $queryData['where']['trans_main.project_id'] = $param['project_id'];
		endif;
		
        if(!empty($param['item_id'])):
            $queryData['where']['trans_main.item_id'] = $param['item_id'];
		endif;
		
        if(!empty($param['approved_by'])):
            $queryData['where']['trans_main.approved_by'] = $param['approved_by'];
		endif;
		
        if(!empty($param['trans_date'])):
            $queryData['where']['DATE(trans_main.trans_date)'] = $param['trans_date'];
		endif;
		
        if(!empty($param['from_date']) AND !empty($param['to_date'])):
            $queryData['where']['DATE(trans_main.trans_date) >= '] = $param['from_date'];
            $queryData['where']['DATE(trans_main.trans_date) <= '] = $param['to_date'];
		endif;
		
		if(!empty($param['search'])):
            $queryData['like']['project_master.project_name'] = $param['search'];
            //$queryData['like']['item_master.item_name'] = $param['search'];
            $queryData['like']['trans_main.trans_date'] = $param['search'];
            $queryData['like']['DATE_FORMAT(trans_main.delivery_date,"%d-%m-%Y")'] = $param['search'];
        endif;
		
		if(!empty($param['limit'])): 
			$queryData['limit'] = $param['limit']; 
		endif;
		
		if(isset($param['start']) && isset($param['length'])):
			$queryData['start'] = $param['start'];
			$queryData['length'] = $param['length'];
		endif;
		
		if(!empty($param['result_type'])):
			$result = $this->getData($queryData,$param['result_type']);
		else:
			$result = $this->getData($queryData,'rows');
		endif;
		//$this->printQuery();
		return $result;
    }

}
?>