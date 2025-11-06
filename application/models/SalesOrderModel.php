<?php
class SalesOrderModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $transExpense = "trans_expense";
    private $transDetails = "trans_details";
    private $orderBom = "order_bom";
    private $purchseReq = "purchase_request";

    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.id,trans_main.trans_number,CONCAT(DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y'),' ',DATE_FORMAT(trans_main.created_at,'%h:%i:%s %p')) as trans_date,trans_main.party_name,trans_main.sales_type,trans_main.trans_status,trans_main.sales_executive,trans_main.party_id,(CASE WHEN trans_main.sales_executive = trans_main.party_id THEN 'Dealer' ELSE 'Office' END) as ordered_by,trans_main.is_approve,trans_main.net_amount,trans_main.remark,company_info.company_code,spd.t_col_1 as ship_to, trans_main.cm_id";

        $data['leftJoin']['company_info'] = "trans_main.cm_id = company_info.id";
        $data['leftJoin']['trans_details as spd'] = "trans_main.ship_to_id = spd.id";

        $data['where']['trans_main.entry_type'] = $data['entry_type'];

        if($data['status'] == 0):
            $data['where']['trans_main.is_approve']  = 0;
            $data['where']['trans_main.trans_status'] = 0;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        elseif($data['status'] == 1):
            $data['where']['trans_main.is_approve >']  = 0;
            $data['where']['trans_main.trans_status'] = 0;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        elseif($data['status'] == 3):
            $data['where']['trans_main.trans_status'] = 1;
            $data['where']['trans_main.trans_date >='] = $this->startYearDate;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        endif;

        $data['where_in']['trans_main.cm_id'] = (!empty($data['cm_id']))?$data['cm_id']:$this->cm_ids;

        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "spd.t_col_1";
        $data['searchCol'][] = "company_info.company_code";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "CONCAT(DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y'),' ',DATE_FORMAT(trans_main.created_at,'%h:%i:%s %p'))";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_main.net_amount";
        //$data['searchCol'][] = "trans_main.remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    /* public function getDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.id as trans_child_id,trans_child.item_name,trans_child.total_box,trans_child.strip_qty,trans_child.qty,IF(trans_child.dispatch_qty > 0,(trans_child.dispatch_qty / (trans_child.strip_qty / trans_child.qty)),0) as dispatch_qty,(trans_child.strip_qty - IF(trans_child.dispatch_qty > 0,(trans_child.dispatch_qty / (trans_child.strip_qty / trans_child.qty)),0)) as pending_qty,trans_main.id,trans_main.trans_number,DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y') as trans_date,trans_main.party_name,trans_main.sales_type,trans_child.trans_status,trans_child.brand_name,trans_main.sales_executive,trans_main.party_id,(CASE WHEN trans_main.sales_executive = trans_main.party_id THEN 'Dealer' ELSE 'Office' END) as ordered_by,trans_main.is_approve,company_info.company_code";

        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['company_info'] = "trans_main.cm_id = company_info.id";

        $data['where']['trans_child.entry_type'] = $data['entry_type'];

        if($data['status'] == 0):
            $data['where']['trans_child.trans_status'] = 0;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        elseif($data['status'] == 1):
            $data['where']['trans_child.trans_status'] = 1;
            $data['where']['trans_main.trans_date >='] = $this->startYearDate;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        endif;

        $data['where_in']['trans_main.cm_id'] = $this->cm_ids;

        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['group_by'][] = "trans_child.id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "(CASE WHEN trans_main.sales_executive = trans_main.party_id THEN 'Dealer' ELSE 'Office' END)";
        $data['searchCol'][] = "company_info.company_code";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_child.item_name";
        $data['searchCol'][] = "trans_child.qty";
        $data['searchCol'][] = "IF(trans_child.dispatch_qty > 0,(trans_child.dispatch_qty / (trans_child.strip_qty / trans_child.qty)),0)";
        $data['searchCol'][] = "(trans_child.strip_qty - IF(trans_child.dispatch_qty > 0,(trans_child.dispatch_qty / (trans_child.strip_qty / trans_child.qty)),0))";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    } */

    public function save($data){
        try{
            $this->db->trans_begin();

            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];
            if($this->checkDuplicate($data) > 0):
                $errorMessage['trans_number'] = "SO. No. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            if(!empty($data['id'])):
                $dataRow = $this->getSalesOrder(['id'=>$data['id'],'itemList'=>1]);
                foreach($dataRow->itemList as $row):
                    if(!empty($row->ref_id)):
                        $setData = array();
                        $setData['tableName'] = $this->transChild;
                        $setData['where']['id'] = $row->ref_id;
                        $setData['update']['trans_status'] = 0;
                        $this->setValue($setData);
                    endif;

                    $this->trash($this->transChild,['id'=>$row->id]);
                endforeach;

                //$this->trash($this->transChild,['trans_main_id'=>$data['id']]);
                $this->trash($this->transExpense,['trans_main_id'=>$data['id']]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"SO TERMS"]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"SO MASTER DETAILS"]);
            endif;
            
            $masterDetails = (!empty($data['masterDetails']))?$data['masterDetails']:array();
            $itemData = $data['itemData'];

            $transExp = getExpArrayMap(((!empty($data['expenseData']))?$data['expenseData']:array()));
			$expAmount = $transExp['exp_amount'];
            $termsData = (!empty($data['termsData']))?$data['termsData']:array();

            unset($transExp['exp_amount'],$data['itemData'],$data['expenseData'],$data['termsData'],$data['masterDetails']);		

            $result = $this->store($this->transMain,$data,'Sales Order');

            if(!empty($masterDetails)):
                $masterDetails['id'] = "";
                $masterDetails['main_ref_id'] = $result['id'];
                $masterDetails['table_name'] = $this->transMain;
                $masterDetails['description'] = "SO MASTER DETAILS";
                $this->store($this->transDetails,$masterDetails);
            endif;

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
                    $row['description'] = "SO TERMS";
                    $row['main_ref_id'] = $result['id'];
                    $this->store($this->transDetails,$row);
                endforeach;
            endif;

            $i=1;
            foreach($itemData as $row):
                $row['entry_type'] = $data['entry_type'];
                $row['cm_id'] = $data['cm_id'];
                $row['trans_main_id'] = $result['id'];
                $row['is_delete'] = 0;
                $this->store($this->transChild,$row);

                if(!empty($row['ref_id'])):
                    $setData = array();
                    $setData['tableName'] = $this->transChild;
                    $setData['where']['id'] = $row['ref_id'];
                    $setData['update']['trans_status'] = "1";
                    $this->setValue($setData);
                endif;
            endforeach;
            
            if(!empty($data['ref_id'])):
                $refIds = explode(",",$data['ref_id']);
                foreach($refIds as $main_id):
                    $setData = array();
                    $setData['tableName'] = $this->transMain;
                    $setData['where']['id'] = $main_id;
                    $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM trans_child WHERE trans_main_id = ".$main_id." AND is_delete = 0)";
                    $this->setValue($setData);
                endforeach;
            endif;

            /* Send Notification */
            /* if(!empty($data['id'])):
                if($dataRow->is_approve == 0 && $data['is_approve'] > 0):
                    $notifyData = array();
                    $notifyData['notificationTitle'] = "Your order has been approved.";
                    $notifyData['notificationMsg'] = "Order No. : ".$data['trans_number']."\nAccepted By : ".$this->userName;
                    $notifyData['callBack'] = base_url("salesOrders/partyOrders");
                    $notifyData['emp_ids'] = [$dataRow->created_by];
                    $this->notify($notifyData);

                    $notifyData = array();
                    $notifyData['notificationTitle'] = "Order Accepted.";
                    $notifyData['notificationMsg'] = "Order No. : ".$data['trans_number']."\nDealer Name : ".$data['party_name']."\nAccepted By : ".$this->userName;
                    $notifyData['callBack'] = base_url("salesOrders");
                    $notifyData['controller'] = ["salesOrders"];
                    $this->notify($notifyData);
                else:
                    $notifyData = array();
                    $notifyData['notificationTitle'] = "Update Order";
                    $notifyData['notificationMsg'] = "Order No. : ".$data['trans_number']."\nDealer Name : ".$data['party_name'];
                    $notifyData['callBack'] = base_url("salesOrders");
                    $notifyData['controller'] = ["salesOrders"];
                    $this->notify($notifyData);
                endif;                
            else:
                $notifyData = array();
                $notifyData['notificationTitle'] = "New Order.";
                $notifyData['notificationMsg'] = "Order No. : ".$data['trans_number']."\nDealer Name : ".$data['party_name'];
                $notifyData['callBack'] = base_url("salesOrders");
                $notifyData['controller'] = ["salesOrders"];
                $this->notify($notifyData);
            endif; */

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
        $queryData['where']['cm_id'] = $data['cm_id'];
        $queryData['where']['trans_date >='] = $this->startYearDate;
        $queryData['where']['trans_date <='] = $this->endYearDate;

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function changeOrderStatus($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->transMain,$data,'Order Status');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getSalesOrder($data){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.*,trans_details.t_col_1 as contact_person,trans_details.t_col_2 as contact_no,employee_master.emp_name as created_name";

        $queryData['select'] .= ",spd.i_col_1 as delivery_country_id, spd.i_col_2 as delivery_state_id, spd.i_col_3 as delivery_city_id, spd.t_col_1 as ship_to, spd.t_col_2 as delivery_address, spd.t_col_3 as delivery_pincode,d_countries.name as delivery_country_name,d_states.name as delivery_state_name,d_states.gst_statecode as delivery_state_code,d_cities.name as delivery_city_name,(CASE WHEN trans_main.cm_id = 1 THEN spd.i_col_4 WHEN trans_main.cm_id = 2 THEN spd.i_col_5 WHEN trans_main.cm_id = 3 THEN spd.i_col_6 ELSE '' END) as distance";

        $queryData['leftJoin']['trans_details'] = "trans_main.id = trans_details.main_ref_id AND trans_details.description = 'SO MASTER DETAILS' AND trans_details.table_name = '".$this->transMain."'";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = trans_main.created_by";

        $queryData['leftJoin']['trans_details as spd'] = "trans_main.ship_to_id = spd.id";
        $queryData['leftJoin']['countries as d_countries'] = "spd.i_col_1 = d_countries.id";
        $queryData['leftJoin']['states as d_states'] = "spd.i_col_2 = d_states.id";
        $queryData['leftJoin']['cities as d_cities'] = "spd.i_col_3 = d_cities.id";

        $queryData['where']['trans_main.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
            $result->itemList = $this->getSalesOrderItems($data);
        endif;

        $queryData = array();
        $queryData['tableName'] = $this->transExpense;
        $queryData['where']['trans_main_id'] = $data['id'];
        $result->expenseData = $this->row($queryData);

        $queryData = array();
        $queryData['tableName'] = $this->transDetails;
        $queryData['select'] = "i_col_1 as term_id,t_col_1 as term_title,t_col_2 as condition";
        $queryData['where']['main_ref_id'] = $data['id'];
        $queryData['where']['table_name'] = $this->transMain;
        $queryData['where']['description'] = "SO TERMS";
        $result->termsConditions = $this->rows($queryData);

        return $result;
    }

    public function getSalesOrderItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*,tmref.trans_number as ref_number";
        $queryData['leftJoin']['trans_child as tcref'] = "tcref.id = trans_child.ref_id";
        $queryData['leftJoin']['trans_main as tmref'] = "tcref.trans_main_id = tmref.id";

        $queryData['where']['trans_child.trans_main_id'] = $data['id'];

        if(!empty($data['trans_ids'])):
            $queryData['where_in']['trans_child.id'] = $data['trans_ids'];
        endif;

        $result = $this->rows($queryData);
        return $result;
    }

    public function getSalesOrderItem($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*";

        $queryData['where']['trans_child.id'] = $data['id'];
        $result = $this->row($queryData);
        return $result;
    }

    public function saveLoadingBy($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->transMain,$data,'Loading By');

            $orderData = $this->getSalesOrder(['id'=>$data['id'],'itemList'=>0]);

            $notifyData = array();
            $notifyData['notificationTitle'] = "Loading Responsibility has been assigned.";
            $notifyData['notificationMsg'] = "Order No. : ".$orderData->trans_number."\nEmp Name : ".$orderData->loading_by_name."\nVehicle No. : ".$orderData->vehicle_no;
            $notifyData['callBack'] = base_url("salesOrders");
            $notifyData['controller'] = ["salesOrders"];
            $this->notify($notifyData);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function delete($id,$partyOrder = 0){
        try{
            $this->db->trans_begin();

            $postData["table_name"] = $this->transMain;
            $postData['where'] = [['column_name'=>'from_entry_type','column_value'=>$this->data['entryData']->id]];
            $postData['find'] = [['column_name'=>'ref_id','column_value'=>$id]];
            $checkRef = $this->checkEntryReference($postData);
            if($checkRef['status'] == 0):
                $this->db->trans_rollback();
                return $checkRef;
            endif;

            $dataRow = $this->getSalesOrder(['id'=>$id,'itemList'=>1]);

            if(!empty($partyOrder)):
                if(!empty($dataRow->is_approve)):
                    $this->db->trans_rollback();
                    return ['status'=>0,'message'=>'Your Order has been accepted. you can not delete it.'];
                endif;
            endif;

            foreach($dataRow->itemList as $row):
                if(!empty($row->ref_id)):
                    $setData = array();
                    $setData['tableName'] = $this->transChild;
                    $setData['where']['id'] = $row->ref_id;
                    $setData['update']['trans_status'] = 0;
                    $this->setValue($setData);
                endif;

                $this->trash($this->transChild,['id'=>$row->id]);
            endforeach;

            if(!empty($dataRow->ref_id)):
                $oldRefIds = explode(",",$dataRow->ref_id);
                foreach($oldRefIds as $main_id):
                    $setData = array();
                    $setData['tableName'] = $this->transMain;
                    $setData['where']['id'] = $main_id;
                    $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM trans_child WHERE trans_main_id = ".$main_id." AND is_delete = 0)";
                    $this->setValue($setData);
                endforeach;
            endif;

            //$this->trash($this->transChild,['trans_main_id'=>$id]);
            $this->trash($this->transExpense,['trans_main_id'=>$id]);
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"SO TERMS"]);
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"SO MASTER DETAILS"]);
            $result = $this->trash($this->transMain,['id'=>$id],'Sales Order');

            /* Send Notification */            
            /* $notifyData['notificationTitle'] = "Order Deleted.";
            $notifyData['notificationMsg'] = "Order No. : ".$dataRow->trans_number."\nDealer Name : ".$dataRow->party_name;
            $notifyData['callBack'] = base_url("salesOrders");
            $notifyData['controller'] = ["salesOrders"];
            $this->notify($notifyData); */

            /* Send Notification to Dealer */ 
            /* if($dataRow->party_id == $dataRow->sales_executive && $dataRow->created_by != $this->loginId):
                $notifyData['notificationTitle'] = "Your order has been canceled by admin.";
                $notifyData['notificationMsg'] = "Order No. : ".$dataRow->trans_number;
                $notifyData['callBack'] = base_url("salesOrders/partyOrders");
                $notifyData['emp_ids'] = [$dataRow->created_by];
                $this->notify($notifyData);
            endif; */

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getPendingOrderItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*,(trans_child.qty - trans_child.dispatch_qty) as pending_qty,(trans_child.strip_qty - IF(trans_child.dispatch_qty > 0,(trans_child.dispatch_qty / (trans_child.qty / trans_child.strip_qty)),0)) as pq,trans_main.entry_type as main_entry_type,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no,trans_main.ship_to_id,item_master.item_name";

        $queryData['leftJoin']['trans_main'] = "trans_child.trans_main_id = trans_main.id";
        $queryData['leftJoin']['item_master'] = "trans_child.item_id = item_master.id";
        $queryData['leftJoin']['party_master'] = "trans_main.party_id = party_master.id";
        $queryData['leftJoin']['item_price_structure'] = "item_price_structure.structure_id = party_master.price_structure_id AND trans_child.item_id = item_price_structure.item_id AND item_price_structure.is_delete = 0";

        $queryData['where']['trans_main.party_id'] = $data['party_id'];
        $queryData['where']['trans_child.entry_type'] = $this->data['entryData']->id;
        $queryData['where']['trans_main.trans_status'] = 0;

        if(!empty($data['completed_order'])):
            $queryData['where']['(trans_child.qty - trans_child.dispatch_qty) <='] = 0;
        else:
            $queryData['where']['(trans_child.qty - trans_child.dispatch_qty) >'] = 0;
        endif;

        if(!empty($data['cm_id'])):
            $queryData['where']['trans_child.cm_id'] = $data['cm_id'];
        endif;

        $queryData['order_by']['trans_main.trans_no'] = "ASC";
        
        return $this->rows($queryData);
    }

    /* Party Order Start */
    public function getPartyOrderDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.*,DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y') as trans_date,if(trans_main.is_approve > 0,'Accepted','Pending') as order_status";

        $data['where']['trans_main.entry_type'] = $data['entry_type'];
        $data['where']['trans_main.party_id'] = $this->partyId;
        $data['customWhere'][] = "trans_main.party_id = trans_main.sales_executive";

        if($data['status'] == 0):
            $data['where']['trans_main.trans_status'] = 0;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        elseif($data['status'] == 1):
            $data['where']['trans_main.trans_status'] = 1;
            $data['where']['trans_main.trans_date >='] = $this->startYearDate;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        endif;

        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "if(trans_main.is_approve > 0,'Accepted','Pending')";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.net_amount";
        $data['searchCol'][] = "trans_main.remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function getPartyOrderItems($data){
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.id as trans_child_id,trans_child.item_id,trans_child.item_name,trans_child.total_box,trans_child.strip_qty,trans_child.qty,IF(trans_child.dispatch_qty > 0,(trans_child.dispatch_qty / (trans_child.qty / trans_child.strip_qty)),0) as dispatch_qty,(trans_child.strip_qty - IF(trans_child.dispatch_qty > 0,(trans_child.dispatch_qty / (trans_child.qty / trans_child.strip_qty)),0)) as pending_qty,trans_child.brand_name as category_name";

        $queryData['where']['trans_child.trans_main_id'] = $data['id'];
        $result = $this->rows($queryData);

        return $result;
    }

    /* public function getPartyOrderDTRowsOld($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.id as trans_child_id,trans_child.item_name,trans_child.total_box,trans_child.strip_qty,trans_child.qty,IF(trans_child.dispatch_qty > 0,(trans_child.dispatch_qty / (trans_child.strip_qty / trans_child.qty)),0) as dispatch_qty,(trans_child.strip_qty - IF(trans_child.dispatch_qty > 0,(trans_child.dispatch_qty / (trans_child.strip_qty / trans_child.qty)),0)) as pending_qty,trans_main.id,trans_main.trans_number,DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y') as trans_date,trans_main.party_name,trans_main.sales_type,trans_child.trans_status,trans_child.brand_name,trans_main.sales_executive,trans_main.party_id,trans_main.is_approve,if(trans_main.is_approve > 0,'Accepted','Pending') as order_status";

        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";

        $data['where']['trans_child.entry_type'] = $data['entry_type'];
        $data['where']['trans_child.created_by'] = $this->loginId;
        $data['customWhere'][] = "trans_main.party_id = trans_main.sales_executive";

        if($data['status'] == 0):
            $data['where']['trans_child.trans_status'] = 0;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        elseif($data['status'] == 1):
            $data['where']['trans_child.trans_status'] = 1;
            $data['where']['trans_main.trans_date >='] = $this->startYearDate;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        endif;

        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['group_by'][] = "trans_child.id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "if(trans_main.is_approve > 0,'Accepted','Pending')";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_child.item_name";
        $data['searchCol'][] = "trans_child.strip_qty";
        $data['searchCol'][] = "IF(trans_child.dispatch_qty > 0,(trans_child.dispatch_qty / (trans_child.strip_qty / trans_child.qty)),0)";
        $data['searchCol'][] = "(trans_child.strip_qty - IF(trans_child.dispatch_qty > 0,(trans_child.dispatch_qty / (trans_child.strip_qty / trans_child.qty)),0))";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    } */
    /* Party Order End */


    /***
    * Created By Mansee [25-04-2024]
    */
    public function getOrderItemList($data){
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.id,trans_main.trans_number,CONCAT(DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y'),' ',DATE_FORMAT(trans_main.created_at,'%h:%i:%s %p')) as trans_date,trans_main.party_name,trans_main.sales_type,trans_main.trans_status,trans_main.sales_executive,trans_main.party_id,(CASE WHEN trans_main.sales_executive = trans_main.party_id THEN 'Dealer' ELSE 'Office' END) as ordered_by,trans_main.is_approve,trans_main.net_amount,trans_main.remark,company_info.company_code";

        $queryData['leftJoin']['company_info'] = "trans_main.cm_id = company_info.id";

        $queryData['where']['trans_main.entry_type'] = $data['entry_type'];

        $queryData['where']['trans_main.trans_date >='] = $this->startYearDate;
        $queryData['where']['trans_main.trans_date <='] = $this->endYearDate;

        $queryData['where_in']['trans_main.cm_id'] = $this->cm_ids;

        if(!in_array($this->userRole,[1,-1]) && empty($this->partyId)){
            $queryData['where']['trans_main.sales_executive'] = $this->loginId;
        }elseif(!empty($this->partyId)){
            $queryData['where']['trans_main.party_id'] = $this->partyId;
        }
        $queryData['order_by']['trans_main.trans_date'] = "DESC";
        $queryData['order_by']['trans_main.id'] = "DESC";

        
         // Search
         if(!empty($data['skey'])):
            $queryData['like']['trans_main.trans_number'] = str_replace(" ", "%", $data['skey']);
            $queryData['like']['trans_main.trans_date'] = str_replace(" ", "%", $data['skey']);
            $queryData['like']['trans_main.party_name'] = str_replace(" ", "%", $data['skey']);
        endif;
        
        if(!empty($data['limit'])):
            $queryData['limit'] = $data['limit']; 
        endif;
        if(isset($data['start'])):
            $queryData['start'] = $data['start'];
        endif;
        if(!empty($data['length'])):
            $queryData['length'] = $data['length'];
        endif;

    
        return $this->rows($queryData);
    }

    public function getSalesOrderCountData($data){
        $queryData['tableName'] = "trans_main";
        $queryData['select'] = "SUM(CASE WHEN MONTH(trans_main.trans_date) = '".date('m')."' THEN 1 ELSE 0 END ) as totalSo,SUM(CASE WHEN (trans_main.trans_status = 0) THEN 1 ELSE 0 END) as pendingSo,SUM(CASE WHEN MONTH(trans_main.trans_date) = '".date('m')."' AND trans_main.trans_status = 3 THEN 1 ELSE 0 END) as cancelSo";
        $queryData['where']['trans_main.entry_type'] = $data['entry_type'];
        if(!empty($this->partyId)){$queryData['where']['trans_main.party_id'] = $this->partyId;} 
        return $this->row($queryData);
    }
    
}
?>