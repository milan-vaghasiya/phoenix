<?php 
class PaymentVoucherModel extends MasterModel{
	private $paymentTrans = "payment_trans";

    public function getNextTransNo($entry_type=1){
		$queryData['tableName'] = $this->paymentTrans;
        $queryData['select'] = "MAX(trans_no) as trans_no";
        if(!empty($entry_type)):
			$queryData['where']['entry_type'] = $entry_type;
		endif;	
		$queryData['where']['payment_trans.trans_date >='] = $this->startYearDate;
		$queryData['where']['payment_trans.trans_date <='] = $this->endYearDate;
		$trans_no = $this->specificRow($queryData)->trans_no;
		$trans_no = (!empty($trans_no))?($trans_no + 1):1;
		return $trans_no;
    }

	public function getDtRows($data){
		$data['tableName'] = $this->paymentTrans;
		$data['select'] = "payment_trans.id, payment_trans.trans_number, payment_trans.trans_date, payment_trans.amount, payment_trans.doc_no, payment_trans.doc_date, payment_trans.notes, opp_acc.party_name as opp_acc_name,vou_acc.party_name as vou_acc_name";

        $data['leftJoin']['party_master as opp_acc'] = "opp_acc.id = payment_trans.opp_acc_id";
        $data['leftJoin']['party_master as vou_acc'] = "vou_acc.id = payment_trans.vou_acc_id";

        if(!empty($data['entry_type'])):
            $data['where']['entry_type'] = $data['entry_type'];
        endif;
		
        $data['where']['payment_trans.trans_date >='] = $this->startYearDate;
        $data['where']['payment_trans.trans_date <='] = $this->endYearDate;

        $data['order_by']['payment_trans.trans_date'] = "DESC";
        $data['order_by']['payment_trans.trans_number'] = "DESC";
		
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "payment_trans.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(payment_trans.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "opp_acc.party_name";
        $data['searchCol'][] = "vou_acc.party_name";
        $data['searchCol'][] = "payment_trans.amount";
        $data['searchCol'][] = "payment_trans.doc_no";
        $data['searchCol'][] = "payment_trans.doc_date";
        $data['searchCol'][] = "payment_trans.notes";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
	}

	public function save($data){
		try{
			$this->db->trans_begin();
			
			$data['doc_date'] = (!empty($data['doc_date']))?$data['doc_date']:null;
			
			$result = ['status'=>2,'message'=>"somthing is wrong."];
			
			if($data['entry_type']==4){
				// Debit Entry
				$data['p_or_m'] = -1;
				
				$result = $this->store($this->paymentTrans,$data,'Voucher');
				
				// Credit Entry
				$creditTrans = $data;
				$creditTrans['p_or_m'] = 1;
				$creditTrans['vou_acc_id'] = $data['opp_acc_id'];
				$creditTrans['opp_acc_id'] = $data['vou_acc_id'];
				
				$result = $this->store($this->paymentTrans,$creditTrans,'Voucher');				
			}
			else{
				if(in_array($data['entry_type'],[1,3])){$data['p_or_m'] = -1;}
				elseif($data['entry_type']==2){$data['p_or_m'] = 1;}
				
				$result = $this->store($this->paymentTrans,$data,'Voucher');
			}            

			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Throwable $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}

	public function getVoucher($data){
        $queryData = array();
        $queryData['tableName'] = $this->paymentTrans;
		$queryData['select'] = "payment_trans.*,party_master.party_name,employee_master.emp_name";
        
		$queryData['leftJoin']['party_master'] = "party_master.id = payment_trans.opp_acc_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = payment_trans.created_by";

        if(!empty($data['id'])){
            $queryData['where']['payment_trans.id'] = $data['id'];
        }
        return $this->row($queryData);
    }

    public function getPaymentSummary($param=[]){
        $queryData['tableName'] = $this->paymentTrans;

        $queryData['select'] = "SUM(CASE WHEN payment_trans.p_or_m = 1 THEN payment_trans.amount ELSE 0 END) as total_in";
        $queryData['select'] .= ", SUM(CASE WHEN payment_trans.p_or_m = -1 THEN payment_trans.amount ELSE 0 END) as total_out";
        $queryData['select'] .= ", SUM(payment_trans.p_or_m * payment_trans.amount) as balance";
		
		if(!empty($param['entry_type'])):
			$queryData['where']['payment_trans.entry_type'] = $param['entry_type'];
		endif;
				
        if(!empty($param['id'])):
            $queryData['where']['payment_trans.id'] = $param['id'];
			$param['result_type'] = 'row';
		endif;
		
        if(!empty($param['project_id'])):
            $queryData['where']['payment_trans.project_id'] = $param['project_id'];
		endif;
		
        if(!empty($param['vou_acc_id'])):
            $queryData['where']['payment_trans.vou_acc_id'] = $param['vou_acc_id'];
		endif;
		
        if(!empty($param['opp_acc_id'])):
            $queryData['where']['payment_trans.opp_acc_id'] = $param['opp_acc_id'];
		endif;
		
        if(!empty($param['trans_date'])):
            $queryData['where']['DATE(payment_trans.trans_date)'] = $param['trans_date'];
		endif;
		
        if(!empty($param['from_date']) AND !empty($param['to_date'])):
            $queryData['where']['DATE(payment_trans.trans_date) >= '] = $param['from_date'];
            $queryData['where']['DATE(payment_trans.trans_date) <= '] = $param['to_date'];
		endif;
		
		if(!in_array($this->userRole,[-1,1])): 
			$queryData['where']['payment_trans.created_by'] = $this->loginId;
		endif;
		
		$result = $this->getData($queryData,'row');
		
		return $result;
    }

    public function getVoucherList($param=[]){
        $queryData['tableName'] = $this->paymentTrans;

        $queryData['select'] = "payment_trans.id, payment_trans.entry_type, payment_trans.trans_date, payment_trans.trans_number, payment_trans.amount, payment_trans.pay_mode, payment_trans.p_or_m, payment_trans.notes";
        $queryData['select'] .= ", project_master.project_name, vParty.party_name as from_party_name, oParty.party_name as to_party_name";
		
        $queryData['leftJoin']['party_master vParty'] = "vParty.id = payment_trans.vou_acc_id";
        $queryData['leftJoin']['party_master oParty'] = "oParty.id = payment_trans.opp_acc_id";
        $queryData['leftJoin']['project_master'] = "project_master.id = payment_trans.project_id";
		
		if(!empty($param['entry_type'])):
			$queryData['where']['payment_trans.entry_type'] = $param['entry_type'];
		endif;
				
        if(!empty($param['id'])):
            $queryData['where']['payment_trans.id'] = $param['id'];
			$param['result_type'] = 'row';
		endif;
		
        if(!empty($param['project_id'])):
            $queryData['where']['payment_trans.project_id'] = $param['project_id'];
		endif;
		
        if(!empty($param['vou_acc_id'])):
            $queryData['where']['payment_trans.vou_acc_id'] = $param['vou_acc_id'];
		endif;
		
        if(!empty($param['opp_acc_id'])):
            $queryData['where']['payment_trans.opp_acc_id'] = $param['opp_acc_id'];
		endif;
		
        if(!empty($param['trans_date'])):
            $queryData['where']['DATE(payment_trans.trans_date)'] = $param['trans_date'];
		endif;
		
        if(!empty($param['from_date']) AND !empty($param['to_date'])):
            $queryData['where']['DATE(payment_trans.trans_date) >= '] = $param['from_date'];
            $queryData['where']['DATE(payment_trans.trans_date) <= '] = $param['to_date'];
		endif;
		
		if(!in_array($this->userRole,[-1,1])): 
			//$queryData['customWhere'][] = 'FIND_IN_SET('.$this->loginId.',project_master.incharge_ids) > 0';
			$queryData['where']['payment_trans.created_by'] = $this->loginId;
		endif;
		
        if(!empty($param['group_by'])):
            $queryData['group_by'][] = $param['group_by'];
		endif;
		
		if(!empty($param['search'])):
            $queryData['like']['vParty.party_name'] = $param['search'];
            $queryData['like']['oParty.party_name'] = $param['search'];
            $queryData['like']['project_master.project_name'] = $param['search'];
            $queryData['like']['payment_trans.trans_date'] = $param['search'];
        endif;
		
		if(!empty($param['limit'])): 
			$queryData['limit'] = $param['limit']; 
		endif;
		
		if(isset($param['start']) && isset($param['length'])):
			$queryData['start'] = $param['start'];
			$queryData['length'] = $param['length'];
		endif;
		
		$queryData['order_by']['payment_trans.trans_date'] = "DESC";
		$queryData['order_by']['payment_trans.id'] = "DESC";
		
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
			
			$result= $this->trash($this->paymentTrans,['id'=>$id],'PaymentVoucher');

			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Throwable $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}

    public function getExpenseSummary($param=[]){
        $queryData['tableName'] = $this->paymentTrans;
		
        $queryData['select'] = "payment_trans.vou_acc_id, vParty.party_name as party_name, SUM(payment_trans.amount) as total_amount";
		
        $queryData['leftJoin']['party_master vParty'] = "vParty.id = payment_trans.vou_acc_id";
		
		if(!empty($param['entry_type'])):
			$queryData['where']['payment_trans.entry_type'] = $param['entry_type'];
		endif;
		
        if(!empty($param['project_id'])):
            $queryData['where']['payment_trans.project_id'] = $param['project_id'];
		endif;
		
        if(!empty($param['vou_acc_id'])):
            $queryData['where']['payment_trans.vou_acc_id'] = $param['vou_acc_id'];
		endif;
		
        if(!empty($param['opp_acc_id'])):
            $queryData['where']['payment_trans.opp_acc_id'] = $param['opp_acc_id'];
		endif;
		
        if(!empty($param['trans_date'])):
            $queryData['where']['DATE(payment_trans.trans_date)'] = $param['trans_date'];
		endif;
		
        if(!empty($param['group_by'])):
            $queryData['group_by'][] = $param['group_by'];
		endif;
		
        if(!empty($param['from_date']) AND !empty($param['to_date'])):
            $queryData['where']['DATE(payment_trans.trans_date) >= '] = $param['from_date'];
            $queryData['where']['DATE(payment_trans.trans_date) <= '] = $param['to_date'];
		endif;
		
		if(!in_array($this->userRole,[-1,1])): 
			$queryData['where']['payment_trans.created_by'] = $this->loginId;
		endif;
		
		$result = $this->getData($queryData,'rows');
		
		return $result;
    }

}
?>