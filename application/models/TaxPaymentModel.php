<?php
class TaxPaymentModel extends MasterModel{
    private $transMain = "trans_main";
    private $transLedger = "trans_ledger";
    private $transDetail = "trans_details";

    /* GST Payment Code Start */
    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = 'trans_main.id,trans_main.trans_number,trans_main.trans_date,opp_acc.party_name as opp_acc_name,vou_acc.party_name as vou_acc_name,trans_main.net_amount,trans_main.doc_no,trans_main.doc_date,trans_main.remark,company_info.company_code';

        $data['leftJoin']['party_master as opp_acc'] = "opp_acc.id = trans_main.opp_acc_id";
        $data['leftJoin']['party_master as vou_acc'] = "vou_acc.id = trans_main.vou_acc_id";
        $data['leftJoin']['company_info'] = "trans_main.cm_id = company_info.id";        

        $data['where']['trans_main.entry_type'] = $data['entry_type'];
        $data['where_in']['trans_main.cm_id'] = (!empty($data['cm_id']))?$data['cm_id']:$this->cm_ids;
        
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "company_info.company_code";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "opp_acc.party_name";
        $data['searchCol'][] = "vou_acc.party_name";
        $data['searchCol'][] = "trans_main.doc_no";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.doc_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.net_amount";
        $data['searchCol'][] = "trans_main.remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
			$this->db->trans_begin();

            if(empty($data['id'])):
                $data['trans_no'] = $this->transMainModel->nextTransNo($this->data['entryData']->id,0,$data['vou_name_s'],$data['cm_id']);
            endif;

            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];

            $data['p_or_m'] = ($data['vou_name_s'] == "BCRct")?1:-1;
			$data['doc_date'] = (!empty($data['doc_date']))?$data['doc_date']:null;

            $itemData = $data['itemData']; unset($data['itemData']);
            $result = $this->store($this->transMain,$data,'Voucher');
			$data['id'] = $result['id'];

            $data['itemData'] = $itemData;
            $this->transMainModel->ledgerEffects($data);

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Throwable $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getTaxPayment($data){
        $queryData = [];
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $data['id'];
        $result = $this->row($queryData);

        $queryData = [];
        $queryData['tableName'] = $this->transLedger;
        $queryData['where']['trans_main_id'] = $data['id'];
        $result->ledgerData = $this->rows($queryData);

        return $result;
    }

    public function delete($id){
		try{
			$this->db->trans_begin();
			
			$result= $this->trash($this->transMain,['id'=>$id],'Voucher');
			$this->transMainModel->deleteLedgerTrans($id);

			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Throwable $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}
    /* GST Payment Code End */

    /* TCS & TDS Payment Code Start */
    public function getTcsTdsDtRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = 'trans_main.id, trans_main.trans_number, trans_main.trans_no, trans_main.trans_date, opp_acc.party_name as opp_acc_name, vou_acc.party_name as vou_acc_name, trans_main.ref_by, trans_main.order_type, trans_main.memo_type, trans_main.net_amount, trans_main.doc_no, trans_main.doc_date, trans_main.remark,company_info.company_code, IFNULL(settled_vou.settled_amount,0) as settled_amount';

        $data['leftJoin']['party_master as opp_acc'] = "opp_acc.id = trans_main.opp_acc_id";
        $data['leftJoin']['party_master as vou_acc'] = "vou_acc.id = trans_main.vou_acc_id";
        $data['leftJoin']['company_info'] = "trans_main.cm_id = company_info.id";
        $data['leftJoin']["(SELECT main_ref_id, SUM(d_col_1) as settled_amount FROM trans_details WHERE is_delete = 0 AND table_name = 'trans_main' AND description IN ('TCSPmt','TDSPmt') GROUP BY main_ref_id) as settled_vou"] = "settled_vou.main_ref_id = trans_main.id";

        $data['where']['trans_main.entry_type'] = $data['entry_type'];
        $data['where_in']['trans_main.cm_id'] = (!empty($data['cm_id']))?$data['cm_id']:$this->cm_ids;
        
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "company_info.company_code";
        $data['searchCol'][] = "trans_main.memo_type";
        $data['searchCol'][] = "trans_main.trans_no";
        $data['searchCol'][] = "trans_main.order_type";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "opp_acc.party_name";
        $data['searchCol'][] = "vou_acc.party_name";
        $data['searchCol'][] = "trans_main.doc_no";
        $data['searchCol'][] = "trans_main.ref_by";
        $data['searchCol'][] = "trans_main.net_amount";
        $data['searchCol'][] = "trans_main.remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data); //$this->printQuery();
    }

    public function saveTcsTdsVoucher($data){
        try{
            $this->db->trans_begin();

            if(empty($data['id'])):
                $postData = [
                    'tableName' => 'trans_main',
                    'no_column' => 'trans_no',
                    'condition' => 'vou_name_s = "'.$data['vou_name_s'].'" AND memo_type = "'.$data['memo_type'].'" AND trans_date >= "'.$this->startYearDate.'" AND trans_date <= "'.$this->endYearDate.'"'
                ];
                $data['trans_no'] = $this->transMainModel->getNextNo($postData);
            else:
                $voucherDetail = $this->getTcsTdsVoucher(['id'=>$data['id'],'ref_detail'=>1]);
                if(!empty($voucherDetail->ref_detail)):
                    return ['status'=>2,'message'=>'Voucher Reference already adjusted. if you want to update this entry first unset all reference.'];
                endif;

                if($voucherDetail->vou_name_s != $data['vou_name_s']):
                    $postData = [
                        'tableName' => 'trans_main',
                        'no_column' => 'trans_no',
                        'condition' => 'vou_name_s = "'.$data['vou_name_s'].'" AND memo_type = "'.$data['memo_type'].'" AND trans_date >= "'.$this->startYearDate.'" AND trans_date <= "'.$this->endYearDate.'"'
                    ];
                    $data['trans_no'] = $this->transMainModel->getNextNo($postData);
                endif;
            endif;

            if($data['vou_name_s'] == "TCSPmt"):
                $data['order_type'] = "R";
            else:
                $oppAccDetail = $this->party->getParty(['id'=>$data['opp_acc_id']]);
                $tdsClass = $this->party->getTDSClass(['id'=>$oppAccDetail->tds_class_id]);
                $data['order_type'] = (!empty($tdsClass->section_code))?$tdsClass->section_code:"";
            endif;

            $data['igst_amount'] = floatval($data['igst_amount']);
            if(!empty($data['igst_amount'])):
                $interestAccount = $this->party->getParty(['system_code'=>"TCSTDSINTACC"]);
				$data['igst_acc_id'] = $interestAccount->id;
            endif;

            $data['vou_name_l'] = ($data['vou_name_s'] == "TCSPmt")?"TCS Payment":"TDS Payment";
            $data['doc_date'] = NULL;            

            $result = $this->store($this->transMain,$data,'Voucher');
			$data['id'] = $result['id'];

            $this->transMainModel->ledgerEffects($data);

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Throwable $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getTcsTdsVoucher($data){
        $queryData = [];
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.*,opp_acc.party_name as opp_acc_name";
        $queryData['leftJoin']['party_master as opp_acc'] = "opp_acc.id = trans_main.opp_acc_id";
        $queryData['where']['trans_main.id'] = $data['id'];
        $result = $this->row($queryData);

        if(!empty($data['ref_detail'])):
            $result->ref_detail = $this->getSettledTransaction(['vou_name_s'=>$result->vou_name_s,'id'=>$result->id]);
        endif;

        return $result;
    }

    public function getUnsettledTransaction($data){
        $amountColumn = ($data['vou_name_s'] == "TCSPmt")?"trans_main.tcs_amount":"trans_main.tds_amount";
        $accountColumn = ($data['vou_name_s'] == "TCSPmt")?"trans_main.tcs_acc_id":"trans_main.tds_acc_id";
        $queryData = [];
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.id, trans_main.trans_number, trans_main.trans_date, trans_main.party_name, trans_main.taxable_amount, abs($amountColumn) as tax_amount, trans_main.net_amount, IFNULL(settled_vou.settled_amount,0) as settled_amount";

        $queryData['leftJoin']["(SELECT i_col_1, SUM(d_col_1) as settled_amount FROM trans_details WHERE is_delete = 0 AND table_name = 'trans_main' AND description IN ('".$data['vou_name_s']."') GROUP BY i_col_1) as settled_vou"] = "settled_vou.i_col_1 = trans_main.id";

        $queryData['where_in']['trans_main.vou_name_s'] = ['"Purc"','"D.N."','"Sale"','"C.N."','"GExp"','"GInc"'];
        $queryData['where']['trans_main.trans_status !='] = 3;
        $queryData['where'][$accountColumn] = $data['acc_id'];
        $queryData['where'][$amountColumn." <> "] = 0;
        $queryData['where']['(abs('.$amountColumn.') - IFNULL(settled_vou.settled_amount,0) ) >'] = 0;
        $queryData['where']['trans_main.cm_id'] = $data['cm_id'];

        if(!empty($data['from_date']) && !empty($data['to_date'])):
            $queryData['where']['trans_main.trans_date >='] = $data['from_date'];
            $queryData['where']['trans_main.trans_date <='] = $data['to_date'];
        endif;

        $result = $this->rows($queryData);
        return $result;
    }

    public function getSettledTransaction($data){
        $columnName = ($data['vou_name_s'] == "TCSPmt")?"trans_main.tcs_amount":"trans_main.tds_amount";
        $queryData = [];
        $queryData['tableName'] = $this->transDetail;
        $queryData['select'] = "trans_details.id, trans_details.i_col_1 as trans_main_id, trans_details.d_col_1 as settled_amount, trans_main.trans_number, trans_main.trans_date, trans_main.party_name, trans_main.taxable_amount, abs($columnName) as tax_amount, trans_main.net_amount";

        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_details.i_col_1";

        $queryData['where']['table_name'] = $this->transMain;
        $queryData['where']['description'] = $data['vou_name_s'];
        $queryData['where']['main_ref_id'] = $data['id'];
        $result = $this->rows($queryData);
        return $result;
    }

    public function deleteTcsTdsVoucher($id){
        try{
			$this->db->trans_begin();

            $voucherDetail = $this->getTcsTdsVoucher(['id'=>$id,'ref_detail'=>1]);
            if(!empty($voucherDetail->ref_detail)):
                return ['status'=>0,'message'=>'Voucher Reference already adjusted. if you want to delete this entry first unset all reference.'];
            endif;
			
			$result= $this->trash($this->transMain,['id'=>$id],'Voucher');
			$this->transMainModel->deleteLedgerTrans($id);

			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Throwable $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function saveSettledTransaction($data){
        try{
            $this->db->trans_begin();           

            foreach($data['itemData'] as $trans_main_id => $amount):
                $postData = [
                    'id' => '',
                    'main_ref_id' => $data['id'],
                    'table_name' => $this->transMain,
                    'description' => $data['vou_name_s'],
                    'i_col_1' => $trans_main_id,
                    'd_col_1' => $amount
                ];
                $result = $this->store($this->transDetail,$postData);
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Throwable $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function removeSettlement($id){
        try{
			$this->db->trans_begin();
			
			$result= $this->remove($this->transDetail,['id'=>$id]);

			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Throwable $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
    /* TCS & TDS Payment Code End */
}
?>