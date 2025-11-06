<?php
class GateInwardModel extends masterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $stockTrans = "stock_transaction";
    private $grnMaster = "grn_master";
    private $grnTrans = "grn_trans";

    public function getNextGrnNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'grn_master';
        $queryData['select'] = "MAX(trans_no ) as trans_no ";
		$queryData['where']['grn_master.trans_date >='] = $this->startYearDate;
		$queryData['where']['grn_master.trans_date <='] = $this->endYearDate;
		$trans_no = $this->specificRow($queryData)->trans_no;
		$trans_no = (empty($this->last_trans_no))?($trans_no + 1):$trans_no;
		return $trans_no;
    }

    public function getDTRows($data){
        $data['tableName'] = 'grn_trans';

        $data['select'] = "grn_trans.id,grn_master.trans_number,DATE_FORMAT(grn_master.trans_date,'%d-%m-%Y') as trans_date,grn_trans.qty,party_master.party_name,item_master.item_name,grn_master.doc_no,ifnull(DATE_FORMAT(grn_master.doc_date,'%d-%m-%Y'),'') as doc_date,trans_main.trans_number as po_number,grn_trans.trans_status,project_master.project_name,grn_master.id as grn_id,grn_master.project_id,grn_master.ref_id";

        $data['leftJoin']['grn_master'] = "grn_master.id = grn_trans.grn_id";
        $data['leftJoin']['item_master'] = "item_master.id = grn_trans.item_id";
        $data['leftJoin']['trans_main'] = "trans_main.id = grn_trans.po_id";
        $data['leftJoin']['project_master'] = "project_master.id = grn_master.project_id";
        $data['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";

        if ($data['trans_status'] == 1) {
            $data['where']['grn_master.ref_id >'] = 0;
        } else {
            $data['where']['grn_master.ref_id'] = 0;
        }
		
        $data['order_by']['grn_trans.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "grn_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(grn_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "project_master.project_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "grn_trans.qty";
        $data['searchCol'][] = "trans_main.trans_number";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if (isset($data['order'])) {
			$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
		}

		return $this->pagingRows($data);
    }

    public function getGateInwardList($param){
        $queryData['tableName'] = 'grn_master';

        $queryData['select'] = "grn_master.id, grn_master.trans_number, DATE(grn_master.trans_date) as trans_date, party_master.party_name, grn_master.doc_no, grn_master.doc_date, trans_main.trans_number as po_number, project_master.project_name, grn_master.trans_prefix, grn_master.trans_no, grn_master.party_id, grn_master.project_id, grn_master.vehicle_no";
		
        $queryData['leftJoin']['trans_main'] = "trans_main.id = grn_master.po_id";
        $queryData['leftJoin']['project_master'] = "project_master.id = grn_master.project_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
		
		if(isset($param['trans_status'])):
			$queryData['where']['grn_master.trans_status'] = $param['trans_status'];
		endif;
				
        if(!empty($param['id'])):
            $queryData['where']['grn_master.id'] = $param['id'];
			$param['result_type'] = 'row';
		endif;
		
        if(!empty($param['project_id'])):
            $queryData['where']['grn_master.project_id'] = $param['project_id'];
		endif;
		
        if(!empty($param['party_id'])):
            $queryData['where']['grn_master.party_id'] = $param['party_id'];
		endif;
		
        if(!empty($param['trans_date'])):
            $queryData['where']['DATE(grn_master.trans_date)'] = $param['trans_date'];
		endif;
		
        if(!empty($param['from_date']) AND !empty($param['to_date'])):
            $queryData['where']['DATE(grn_master.trans_date) >= '] = $param['from_date'];
            $queryData['where']['DATE(grn_master.trans_date) <= '] = $param['to_date'];
		endif;
		
		if(!empty($param['search'])):
            $queryData['like']['grn_master.trans_number'] = $param['search'];
            $queryData['like']['party_master.party_name'] = $param['search'];
            $queryData['like']['project_master.project_name'] = $param['search'];
            $queryData['like']['grn_master.doc_no'] = $param['search'];
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
		
		if(isset($param['item_detail']) AND !empty($param['id'])): 
			$result->itemDetail = $this->getInwardItem(['grn_id'=>$param['id']]);
		endif;
		
		return $result;
    }

    public function getGIList($param){
        $queryData['tableName'] = 'grn_trans';

        $queryData['select'] = "grn_master.id, grn_master.party_id, grn_master.trans_number, DATE(grn_master.trans_date) as trans_date, party_master.party_name, grn_master.doc_no, trans_main.trans_number as po_number, project_master.project_name, item_master.item_name, item_master.uom, grn_trans.qty, grn_master.created_at as received_at, employee_master.emp_name as received_by, grn_master.doc_date";
		
        $queryData['leftJoin']['grn_master'] = "grn_trans.grn_id = grn_master.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = grn_trans.item_id";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = grn_master.po_id";
        $queryData['leftJoin']['project_master'] = "project_master.id = grn_master.project_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = grn_master.created_by";
		
		if(isset($param['trans_status'])):
			$queryData['where']['grn_master.trans_status'] = $param['trans_status'];
		endif;
				
        if(!empty($param['id'])):
            $queryData['where']['grn_master.id'] = $param['id'];
			$param['result_type'] = 'row';
		endif;
		
        if(!empty($param['project_id'])):
            $queryData['where']['grn_master.project_id'] = $param['project_id'];
		endif;
		
        if(!empty($param['party_id'])):
            $queryData['where']['grn_master.party_id'] = $param['party_id'];
		endif;
		
        if(!empty($param['trans_date'])):
            $queryData['where']['DATE(grn_master.trans_date)'] = $param['trans_date'];
		endif;
		
        if(!empty($param['from_date']) AND !empty($param['to_date'])):
            $queryData['where']['DATE(grn_master.trans_date) >= '] = $param['from_date'];
            $queryData['where']['DATE(grn_master.trans_date) <= '] = $param['to_date'];
		endif;
		
		if(!empty($param['search'])):
            $queryData['like']['grn_master.trans_number'] = $param['search'];
            $queryData['like']['party_master.party_name'] = $param['search'];
            $queryData['like']['project_master.project_name'] = $param['search'];
            $queryData['like']['grn_master.doc_no'] = $param['search'];
        endif;
		
		if(!empty($param['limit'])): 
			$queryData['limit'] = $param['limit']; 
		endif;
		
		if(isset($param['start']) && isset($param['length'])):
			$queryData['start'] = $param['start'];
			$queryData['length'] = $param['length'];
		endif;
		
		$queryData['order_by']['grn_master.trans_date'] = 'DESC';
		$queryData['order_by']['grn_master.id'] = 'DESC';
		
		if(!empty($param['result_type'])):
			$result = $this->getData($queryData,$param['result_type']);
		else:
			$result = $this->getData($queryData,'rows');
		endif;
		
		if(isset($param['item_detail']) AND !empty($param['id'])): 
			$result->itemDetail = $this->getInwardItem(['grn_id'=>$param['id']]);
		endif;
		return $result;
    }

    public function save($data,$trans_prefix="GI"){
        try{
            $this->db->trans_begin();
			
			$itemData = $data['item_data'];unset($data['item_data']);
			if(!empty($itemData) && gettype($itemData) == "string"): $itemData = json_decode($itemData,true); endif;
			
            if(!empty($data['id'])):
			
                $gateInwardData = $this->getInwardItem(['grn_id'=>$data['id']]);
				
                foreach($gateInwardData as $row):
                    if(!empty($row->po_trans_id)):
                        $setData = array();
                        $setData['tableName'] = $this->transChild;
                        $setData['where']['id'] = $row->po_trans_id;
                        $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$row->qty;
                        $setData['update']['trans_status'] = 3;
                        $this->setValue($setData);

                        $setData = array();
                        $setData['tableName'] = $this->transMain;
                        $setData['where']['id'] = $row->po_id;
                        $setData['update']['trans_status'] = 3;
                        $this->setValue($setData);
                    endif;
                    $this->remove('stock_trans', ['trans_type'=>'GRN', 'main_ref_id'=>$row->id]);
                endforeach;
				
            else:
                $data['trans_no'] = $this->getNextGrnNo();
				$data['trans_prefix'] = $trans_prefix.'/'.$this->shortYear.'/';
                $data['trans_number'] =  $data['trans_prefix'].$data['trans_no'];
            endif;
			
			$result = Array();
			if(!empty($itemData))
			{
				$result = $this->store("grn_master",$data);
				foreach($itemData as $row)
				{
					$row= (object) $row;
					$grnTransData = Array();$stockPlusQuery = Array();
					if(!empty($row->item_id) AND !empty($row->qty))
					{
						$grnTransData['id'] = (!empty($row->id) ? $row->id : "");
						$grnTransData['item_id'] = $row->item_id;
						$grnTransData['qty'] = $row->qty;
						$grnTransData['grn_id'] = (!empty($data['id']) ? $data['id'] : $result['insert_id']);
						$grnTransData['po_id'] = (!empty($row->po_id) ? $row->po_id : 0);
						$grnTransData['po_trans_id'] = (!empty($row->po_trans_id) ? $row->po_trans_id : 0);
						$grnTransData['price'] = (!empty($row->price) ? $row->price : 0);
						$grnTransData['item_remark'] = (!empty($row->item_remark) ? $row->item_remark : "");
						
						$resultTrans = $this->store("grn_trans",$grnTransData);
						
						//STOCK TRANS EFFECT
						$stockPlusQuery = [
							'id' => "",
							'trans_type' =>'GRN',
							'trans_date' => $data['trans_date'],
							'location_id'=> $data['project_id'],
							'item_id' => $row->item_id,
							'qty' => $row->qty,
							'p_or_m' => 1,
							'main_ref_id' =>(!empty($row->id) ? $row->id : $resultTrans['insert_id']),
							'ref_no'=>$data['trans_number']
						];
						$this->store('stock_trans', $stockPlusQuery);
						
						// PURCHASE ORDER EFFECT
						if(!empty($row->po_trans_id)):
							$setData = array();
							$setData['tableName'] = $this->transChild;
							$setData['where']['id'] = $row->po_trans_id;
							$setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$row->qty;
							$setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 3 END)";
							$this->setValue($setData);

							$setData = array();
							$setData['tableName'] = $this->transMain;
							$setData['where']['id'] = $row->po_id; //$data['po_id']
							$setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status != 3, 1, 0)) ,1 , 3 ) as trans_status FROM trans_child WHERE trans_main_id = ".$row->po_id." AND is_delete = 0)";
							$this->setValue($setData);
						endif;
					}
				}
			}
			
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
	
    public function getInwardItem($param){
        $queryData['tableName'] = 'grn_trans';
        $queryData['select'] = "grn_trans.id, grn_trans.item_id, grn_trans.po_trans_id, grn_trans.qty, grn_trans.price, item_master.item_code,item_master.item_name,grn_trans.grn_id, grn_trans.po_id, trans_main.trans_number as po_number, grn_trans.item_remark, item_master.uom";
        $queryData['leftJoin']['item_master'] = "item_master.id = grn_trans.item_id";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = grn_trans.po_id";

		if(isset($param['trans_status'])):
			$queryData['where']['grn_trans.trans_status'] = $param['trans_status'];
		endif;
				
        if(!empty($param['id'])):
            $queryData['where']['grn_trans.id'] = $param['id'];
			$param['result_type'] = 'row';
		endif;
		
        if(!empty($param['grn_id'])):
			$queryData['where']['grn_trans.grn_id'] = $param['grn_id'];
		endif;

        if(!empty($param['result_type'])):
			$result = $this->getData($queryData,$param['result_type']);
		else:
			$result = $this->getData($queryData,'rows');
		endif;
		return $result;
    }

	public function delete($id){
        try{
            $this->db->trans_begin();

            $gateInwardData = $this->getGateInwardList(['id'=>$id,'item_detail'=>1]);

            if(!empty($gateInwardData->ref_id)):
                $this->store($this->grnMaster,['id'=>$gateInwardData->ref_id,'trans_status'=>0]);
            endif;

            foreach($gateInwardData->itemDetail as $row):
                if(!empty($row->po_trans_id)):
                    $setData = array();
                    $setData['tableName'] = $this->transChild;
                    $setData['where']['id'] = $row->po_trans_id;
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$row->qty;
                    $setData['update']['trans_status'] = 0;
                    $this->setValue($setData);

                    $setData = array();
                    $setData['tableName'] = $this->transMain;
                    $setData['where']['id'] = $row->po_id;
                    $setData['update']['trans_status'] = 0;
                    $this->setValue($setData);
                endif;

                $this->trash($this->grnTrans,['id'=>$row->id]);
            endforeach;

            $result = $this->trash($this->grnMaster,['id'=>$id],'Gate Inward');        

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
	
    public function saveInspectedMaterial($data){
        try{
            $this->db->trans_begin();

            $bQty = array();$errorMessage = [];
            foreach($data['itemData'] as $key => $row):
                $mirData = $this->getGateInward($row['mir_id']);
                $mirItem = $this->getInwardItem(['id'=>$row['id']]);

                if(floatval($mirItem->ok_qty) > 0):
                    $postData = ['batch_no' => "GB",'item_id' => $mirItem->item_id, 'location_id' => $data['project_id'],'stock_required'=>1,'single_row'=>1];
                    $stockData = $this->itemStock->getItemStockBatchWise($postData);
                    $batchKey = $mirItem->location_id.$mirItem->item_id;
                    $stockQty = (!empty($stockData->qty))?floatVal($stockData->qty):0;

                    if(!isset($bQty[$batchKey])):
                        $bQty[$batchKey] = $mirItem->ok_qty;
                    else:
                        $bQty[$batchKey] += $mirItem->ok_qty;
                    endif;

                    /* if(empty($stockQty)):
                        $errorMessage['ok_qty_'.$key] = "Stock Used.";
                        $this->db->trans_rollback(); break;
                    else:
                        if($bQty[$batchKey] > $stockQty):
                            $errorMessage['ok_qty_'.$key] = "Stock Used.";
                            $this->db->trans_rollback(); break;
                        endif;
                    endif; */
                endif;

                if(floatval($mirItem->reject_qty) > 0):
                    $postData = ['batch_no' => "GB",'item_id' => $mirItem->item_id, 'location_id' => $this->REJ_STORE->id,'stock_required'=>1,'single_row'=>1];
                    $stockData = $this->itemStock->getItemStockBatchWise($postData);
                    $batchKey = $this->REJ_STORE->id.$mirItem->item_id;
                    $stockQty = (!empty($stockData->qty))?floatVal($stockData->qty):0;

                    if(!isset($bQty[$batchKey])):
                        $bQty[$batchKey] = $mirItem->reject_qty;
                    else:
                        $bQty[$batchKey] += $mirItem->reject_qty;
                    endif;

                    /* if(empty($stockQty)):
                        $errorMessage['rej_qty_'.$key] = "Stock Used.";
                        $this->db->trans_rollback(); break;
                    else:
                        if($bQty[$batchKey] > $stockQty):
                            $errorMessage['rej_qty_'.$key] = "Stock Used.";
                            $this->db->trans_rollback(); break;
                        endif;
                    endif; */
                endif;

                $this->remove($this->stockTrans,['entry_type'=>26,'main_ref_id' => $mirData->id,'child_ref_id' => $mirItem->id]);

                $row['ok_qty'] = (!empty($row['ok_qty']))?$row['ok_qty']:0;
                $row['reject_qty'] = (!empty($row['reject_qty']))?$row['reject_qty']:0;
                $row['short_qty'] = (!empty($row['short_qty']))?$row['short_qty']:0;
                
                $totalQty = 0;
                $totalQty = ($row['ok_qty'] + $row['reject_qty'] + $row['short_qty']);
                if($mirItem->qty < $totalQty):
                    $errorMessage['ok_qty_'.$key] = "Invalid Qty.";
                    $this->db->trans_rollback(); break;
                endif;

                $row['trans_status'] = ($totalQty >= $mirItem->qty)?1:0;

                $this->store($this->mirTrans,$row);

                if(!empty($row['ok_qty'])):
                    $stockData = [
                        'id' => "",
                        'entry_type' => $this->data['entryData']->id,
                        'ref_date' => $mirData->trans_date,
                        'ref_no' => $mirData->trans_number,
                        'main_ref_id' => $mirData->id,
                        'child_ref_id' => $mirItem->id,
                        'location_id' => $mirItem->location_id,
                        'batch_no' => $mirItem->batch_no,
                        'party_id' => $mirData->party_id,
                        'item_id' => $mirItem->item_id,
                        'p_or_m' => 1,
                        'qty' => $row['ok_qty'],
                        'price' => $mirItem->price
                    ];

                    $this->store($this->stockTrans,$stockData);
                endif;

                if(!empty($row['reject_qty'])):
                    $stockData = [
                        'id' => "",
                        'entry_type' => $this->data['entryData']->id,
                        'ref_date' => $mirData->trans_date,
                        'ref_no' => $mirData->trans_number,
                        'main_ref_id' => $mirData->id,
                        'child_ref_id' => $mirItem->id,
                        'location_id' => $this->REJ_STORE->id,
                        'batch_no' => $mirItem->batch_no,
                        'party_id' => $mirData->party_id,
                        'item_id' => $mirItem->item_id,
                        'p_or_m' => 1,
                        'qty' => $row['reject_qty'],
                        'price' => $mirItem->price
                    ];

                    $this->store($this->stockTrans,$stockData);
                endif;
            endforeach;

            if(!empty($errorMessage)):
                return ['status'=>0,'message'=>$errorMessage];
            else:
                $result = ['status'=>1,'message'=>"Material Inspected successfully.",'error'=>$errorMessage];
            endif;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getPendingInwardItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->mirTrans;
        $queryData['select'] = "mir_transaction.*,(mir_transaction.qty - mir_transaction.inv_qty) as pending_qty,mir.entry_type as main_entry_type,mir.trans_number,mir.trans_date,mir.inv_no,mir.inv_date,mir.doc_no,mir.doc_date,item_master.item_code,item_master.item_name,item_master.item_type,item_master.hsn_code,item_master.gst_per,unit_master.id as unit_id,unit_master.unit_name,'0' as stock_eff";
        $queryData['leftJoin']['mir'] = "mir_transaction.mir_id = mir.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = mir_transaction.item_id";
        $queryData['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $queryData['where']['mir.party_id'] = $data['party_id'];
        $queryData['where']['mir_transaction.entry_type'] = $this->data['entryData']->id;
        $queryData['where']['(mir_transaction.qty - mir_transaction.inv_qty) >'] = 0;
        return $this->rows($queryData);
    }

	public function deleteGrn($data){
        try{
            $this->db->trans_begin();

            $grnItemsForCount = $this->getInwardItem(['grn_id'=>$data['grn_id']]);            
            $grnCount = (!empty($grnItemsForCount) ? count($grnItemsForCount) : 0);
            $grnTransData = $this->getInwardItem(['id'=>$data['id'], 'result_type'=>1]);

            if (!empty($grnTransData)) {
                $stockData = $this->store->getItemStockBatchWise(['item_id'=>$grnTransData->item_id, 'location_id'=>$data['project_id'], 'stock_required'=>1, 'single_row'=>1]);
                $stock_qty = (!empty($stockData->qty) ? $stockData->qty : 0);

                if($grnTransData->qty > $stock_qty) {
                    return ['status'=>0,'message'=>'You can not delete this GRN.'];
                }

                $this->remove('stock_trans', ['trans_type'=>'GRN', 'main_ref_id'=>$data['id']]);
                $result = $this->trash($this->grnTrans,['id'=>$data['id']]);

                if(!empty($grnTransData->po_trans_id)):
                    $setData = array();
                    $setData['tableName'] = $this->transChild;
                    $setData['where']['id'] = $grnTransData->po_trans_id;
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$grnTransData->qty;
                    $setData['update']['trans_status'] = 3;
                    $this->setValue($setData);

                    $setData = array();
                    $setData['tableName'] = $this->transMain;
                    $setData['where']['id'] = $grnTransData->po_id;
                    $setData['update']['trans_status'] = 3;
                    $this->setValue($setData);
                endif;

                if($grnCount <= 1):
                    $this->trash($this->grnMaster,['id'=>$data['grn_id']],'Gate Inward');  
                endif;

            }else{
                $result = ['status'=>0,'message'=>'GRN already deleted'];
            }
      
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function updateGRN($data){
        try{
            $this->db->trans_begin();
				
            $itemData = $data['itemData']; unset($data['itemData']); 

			/*
            $stockData = $this->store->getItemStockBatchWise(['item_id'=>$itemData['item_id'], 'location_id'=>$data['project_id'], 'single_row'=>1, 'p_or_m'=>'-1']);
            $stock_qty = (!empty($stockData->qty) ? abs($stockData->qty) : 0);

            if($itemData['qty'] < $stock_qty) {
                $errorMessage['qty'] = 'Invalid qty.';
                return ['status'=>0,'message'=>$errorMessage];
            }
			*/

            if(!empty($itemData['po_trans_id'])):
                $setData = array();
                $setData['tableName'] = $this->transChild;
                $setData['where']['id'] = $itemData['po_trans_id'];
                $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$itemData['qty'];
                $setData['update']['trans_status'] = 3;
                $this->setValue($setData);

                $setData = array();
                $setData['tableName'] = $this->transMain;
                $setData['where']['id'] = $itemData['po_id'];
                $setData['update']['trans_status'] = 3;
                $this->setValue($setData);
            endif;
            $this->remove('stock_trans', ['trans_type'=>'GRN', 'main_ref_id'=>$itemData['trans_id']]);
			           
            $result = $this->store($this->grnMaster, $data, 'GRN');

            if (!empty($itemData['trans_id'])) {
                $itemData['grn_id'] = $data['id'];
                $itemData['id'] = $itemData['trans_id'];
                unset($itemData['trans_id']);                
                $resultTrans = $this->store($this->grnTrans, $itemData);
                
                //STOCK TRANS EFFECT
                $stockPlusQuery = [
                    'id' => "",
                    'trans_type' =>'GRN',
                    'trans_date' => $data['trans_date'],
                    'location_id'=> $data['project_id'],
                    'item_id' => $itemData['item_id'],
                    'qty' => $itemData['qty'],
                    'p_or_m' => 1,
                    'main_ref_id' => $itemData['id'],
                    'ref_no' => $data['trans_number']
                ];
                $this->store('stock_trans', $stockPlusQuery);
                
                // PURCHASE ORDER EFFECT
                if(!empty($itemData['po_trans_id'])):
                    $setData = array();
                    $setData['tableName'] = $this->transChild;
                    $setData['where']['id'] = $itemData['po_trans_id'];
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$itemData['qty'];
                    $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 3 END)";
                    $this->setValue($setData);

                    $setData = array();
                    $setData['tableName'] = $this->transMain;
                    $setData['where']['id'] = $itemData['po_id'];
                    $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status != 3, 1, 0)) ,1 , 3 ) as trans_status FROM trans_child WHERE trans_main_id = ".$itemData['po_id']." AND is_delete = 0)";
                    $this->setValue($setData);
                endif;
            }
			
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