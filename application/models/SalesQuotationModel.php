<?php
class SalesQuotationModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $transExpense = "trans_expense";
    private $transDetails = "trans_details";

    public function getDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.id as trans_child_id,trans_child.item_name,trans_child.qty,trans_child.price,trans_main.id,trans_main.trans_number,DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y') as trans_date,trans_main.party_id,trans_main.sales_executive,trans_main.party_name,trans_main.sales_type,trans_child.trans_status,trans_main.is_approve,employee_master.emp_name as approve_by_name,trans_main.approve_date,trans_main.quote_rev_no,trans_main.close_reason,company_info.company_code";

        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = trans_main.is_approve";
        $data['leftJoin']['company_info'] = "trans_main.cm_id = company_info.id";

        $data['where']['trans_child.entry_type'] = $data['entry_type'];

        if($data['status'] == 0):
            $data['where']['trans_child.trans_status'] = 0;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        elseif($data['status'] == 1):
            $data['where']['trans_child.trans_status'] = 1;
            $data['where']['trans_main.trans_date >='] = $this->startYearDate;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        elseif($data['status'] == 2):
            $data['where']['trans_child.trans_status'] = 2;
            $data['where']['trans_main.trans_date >='] = $this->startYearDate;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        endif;

        $data['where_in']['trans_main.cm_id'] = (!empty($data['cm_id']))?$data['cm_id']:$this->cm_ids;
        
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['group_by'][] = "trans_child.id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "company_info.company_code";
        $data['searchCol'][] = "trans_main.quote_rev_no";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_child.item_name";
        $data['searchCol'][] = "trans_child.qty";
        $data['searchCol'][] = "trans_child.price";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.approve_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.close_reason";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];
            if($this->checkDuplicate($data) > 0):
                $errorMessage['trans_number'] = "SQ. No. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            if(!empty($data['is_rev'])):
                $this->quotationRevision($data['id']);
                $data['quote_rev_no'] = $data['quote_rev_no'] + 1;
            endif;

            if(!empty($data['id'])):
                $dataRow = $this->getSalesQuotation(['id'=>$data['id'],'itemList'=>1]);
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
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"SQ TERMS"]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"SQ MASTER DETAILS"]);
            endif;
            
            $masterDetails = (!empty($data['masterDetails']))?$data['masterDetails']:array();
            $itemData = $data['itemData'];

            $transExp = getExpArrayMap(((!empty($data['expenseData']))?$data['expenseData']:array()));
			$expAmount = $transExp['exp_amount'];
            $termsData = (!empty($data['termsData']))?$data['termsData']:array();

            unset($transExp['exp_amount'],$data['itemData'],$data['expenseData'],$data['termsData'],$data['masterDetails'],$data['is_rev']);		

            $result = $this->store($this->transMain,$data,'Sales Quotation');

            if(!empty($masterDetails)):
                $masterDetails['id'] = "";
                $masterDetails['main_ref_id'] = $result['id'];
                $masterDetails['table_name'] = $this->transMain;
                $masterDetails['description'] = "SQ MASTER DETAILS";
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
                    $row['description'] = "SQ TERMS";
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

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function quotationRevision($id){
        try{
            $this->db->trans_begin();

            $quotationData = $this->getSalesQuotation(['id'=>$id,'itemList'=>1]);
            
            $masterDetails = [
                't_col_1' => $quotationData->contact_person,                
                't_col_2' => $quotationData->contact_no,
                't_col_3' => $quotationData->contact_email,
                't_col_4' => $quotationData->ship_address,
                't_col_5' => $quotationData->ship_pincode
            ];

            $itemData = $quotationData->itemList;

            $termsData = array();
            if(!empty($quotationData->termsConditions)):
                foreach($quotationData->termsConditions as $row):
                    $termsData[] = [                        
                        'i_col_1' => $row->term_id,
                        't_col_1' => $row->term_title,
                        't_col_2' => $row->condition
                    ];
                endforeach;
            endif;

            $transExp = (!empty($quotationData->expenseData))?$quotationData->expenseData:array();

            unset($quotationData->contact_person,$quotationData->contact_no,$quotationData->contact_email,$quotationData->ship_address,$quotationData->ship_pincode,$quotationData->itemList,$quotationData->termsConditions,$quotationData->expenseData,$quotationData->created_name);

            $quotationData = (array) $quotationData;
            $quotationData["ref_id"] = $quotationData["id"];
            $quotationData["id"] = "";
            $quotationData["from_entry_type"] = $quotationData['entry_type'];
            $quotationData["entry_type"] = "";
            
            $result = $this->store($this->transMain,$quotationData,'Sales Quotation');

            if(!empty($masterDetails)):
                $masterDetails['id'] = "";
                $masterDetails['main_ref_id'] = $result['id'];
                $masterDetails['table_name'] = $this->transMain;
                $masterDetails['description'] = "SQ MASTER DETAILS";
                $this->store($this->transDetails,$masterDetails);
            endif;

            $expenseData = array();
            if(!empty($transExp)):
				$expenseData = (array) $transExp;
                $expenseData['id'] = "";
				$expenseData['trans_main_id'] = $result['id'];
                $this->store($this->transExpense,$expenseData);
			endif;

            if(!empty($termsData)):
                foreach($termsData as $row):
                    $row['id'] = "";
                    $row['table_name'] = $this->transMain;
                    $row['description'] = "SQ TERMS";
                    $row['main_ref_id'] = $result['id'];
                    $this->store($this->transDetails,$row);
                endforeach;
            endif;

            $i=1;
            foreach($itemData as $row):
                $row = (array) $row;
                $row['from_entry_type'] = $row['entry_type'];
                $row['entry_type'] = "";
                $row['ref_id'] = $row['id'];
                $row['id'] = "";
                $row['trans_main_id'] = $result['id'];
                $row['is_delete'] = 0;
                $this->store($this->transChild,$row);
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
        $queryData['where']['cm_id'] = $data['cm_id'];
        $queryData['where']['trans_date >='] = $this->startYearDate;
        $queryData['where']['trans_date <='] = $this->endYearDate;

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getQuotationRevisionList($data){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "id,trans_number,quote_rev_no,doc_date";
        $queryData['where']['trans_main.trans_number'] = $data['trans_number'];
        $result = $this->rows($queryData);
        return $result;
    }

    public function getSalesQuotation($data){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.*,trans_details.t_col_1 as contact_person,trans_details.t_col_2 as contact_no,trans_details.t_col_3 as contact_email,trans_details.t_col_4 as ship_address,trans_details.t_col_5 as ship_pincode,employee_master.emp_name as created_name";

        $queryData['leftJoin']['trans_details'] = "trans_main.id = trans_details.main_ref_id AND trans_details.description = 'SQ MASTER DETAILS' AND trans_details.table_name = '".$this->transMain."'";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = trans_main.created_by";
        $queryData['where']['trans_main.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
            $result->itemList = $this->getSalesQuotationItems($data);
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
        $queryData['where']['description'] = "SQ TERMS";
        $result->termsConditions = $this->rows($queryData);

        return $result;
    }

    public function getSalesQuotationItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*";
        $queryData['where']['trans_child.trans_main_id'] = $data['id'];
        $result = $this->rows($queryData);
        return $result;
    }

    public function getSalesQuotationItem($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*";
        $queryData['where']['trans_child.id'] = $data['id'];
        $result = $this->row($queryData);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $dataRow = $this->getSalesQuotation(['id'=>$id,'itemList'=>1]);
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
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"SQ TERMS"]);
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"SQ MASTER DETAILS"]);
            $result = $this->trash($this->transMain,['id'=>$id],'Sales Order');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getPendingQuotationItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*,trans_main.entry_type as main_entry_type,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no";
        $queryData['leftJoin']['trans_main'] = "trans_child.trans_main_id = trans_main.id";
        $queryData['where']['trans_main.party_id'] = $data['party_id'];
        $queryData['where']['trans_child.entry_type'] = $this->data['entryData']->id;
        $queryData['where']['trans_child.confirm_status'] = 2;
        $queryData['where']['trans_child.trans_status'] = 0;

        if(!empty($data['cm_id'])):
            $queryData['where']['trans_child.cm_id'] = $data['cm_id'];
        endif;

        return $this->rows($queryData);
    }
}
?>