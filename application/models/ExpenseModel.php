<?php
class ExpenseModel extends MasterModel{
    private $expenseManager = "expense_manager";

    public function getNextExpNo(){
		$queryData['tableName'] = $this->expenseManager;
        $queryData['select'] = "MAX(exp_no) as exp_no";
		$trans_no = $this->specificRow($queryData)->exp_no;
		$trans_no = (!empty($trans_no))?($trans_no + 1):1;
		return $trans_no;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->expenseManager;

        $data['select'] = "expense_manager.*,employee_master.emp_name,party_master.party_name";

        $data['leftJoin']['employee_master'] = "employee_master.id = expense_manager.exp_by_id";
        $data['leftJoin']['party_master'] = "party_master.id = expense_manager.exp_ledger_id";
        $data['where']['expense_manager.status'] = $data['status'];
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "expense_manager.exp_number ";
        $data['searchCol'][] = "expense_manager.exp_date ";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "expense_manager.demand_amount";
        $data['searchCol'][] = "expense_manager.amount";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "expense_manager.rej_reason";
		
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		
        return $this->pagingRows($data);
    }
	
    public function getExpenseList($param=[]){
        $queryData['tableName'] = $this->expenseManager;

        $queryData['select'] = "expense_manager.*, project_master.project_name, employee_master.emp_name, party_master.party_name";
		
        $queryData['leftJoin']['employee_master'] = "employee_master.id = expense_manager.exp_by_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = expense_manager.exp_ledger_id";
        $queryData['leftJoin']['project_master'] = "project_master.id = expense_manager.project_id";
		
		if(isset($param['status'])):
			$queryData['where']['expense_manager.status'] = $param['status'];
		endif;
				
        if(!empty($param['id'])):
            $queryData['where']['expense_manager.id'] = $param['id'];
			$param['result_type'] = 'row';
		endif;
		
        if(!empty($param['project_id'])):
            $queryData['where']['expense_manager.project_id'] = $param['project_id'];
		endif;
		
        if(!empty($param['exp_ledger_id'])):
            $queryData['where']['expense_manager.exp_ledger_id'] = $param['exp_ledger_id'];
		endif;
		
        if(!empty($param['exp_by_id'])):
            $queryData['where']['expense_manager.exp_by_id'] = $param['exp_by_id'];
		endif;
		
        if(!empty($param['exp_date'])):
            $queryData['where']['DATE(expense_manager.exp_date)'] = $param['exp_date'];
		endif;
		
        if(!empty($param['from_date']) AND !empty($param['to_date'])):
            $queryData['where']['DATE(expense_manager.exp_date) >= '] = $param['from_date'];
            $queryData['where']['DATE(expense_manager.exp_date) <= '] = $param['to_date'];
		endif;
		
		if(!empty($param['search'])):
            $queryData['like']['employee_master.emp_name'] = $param['search'];
            $queryData['like']['party_master.party_name'] = $param['search'];
            $queryData['like']['project_master.project_name'] = $param['search'];
            $queryData['like']['expense_manager.exp_date'] = $param['search'];
            $queryData['like']['(CASE WHEN expense_manager.status = 0 THEN "PENDING" WHEN expense_manager.status = 1 THEN "Approved" ELSE "Rejected" END)'] = $data['search'];
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
		return $result;
    }

    public function getExpense($data){
        $queryData = array();
        $queryData['tableName'] = $this->expenseManager;

        if(!empty($data['id']))
            $queryData['where']['id'] = $data['id'];
        return $this->row($queryData);
    }

    public function checkDuplicate($data){
        $queryData['tableName'] = $this->expenseManager;
        $queryData['where']['exp_ledger_id'] = $data['exp_ledger_id'];
        $queryData['where']['exp_by_id'] = $data['exp_by_id'];
        $queryData['where']['exp_date'] = $data['exp_date'];
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['name'] = "Exp Type is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            if(empty($data['id'])):
                $data['exp_prefix'] = $exp_prefix = "EXP";  
                $data['exp_no'] = $exp_no = $this->expense->getNextExpNo();
                $data['exp_number'] = $exp_prefix.sprintf("%03d",$exp_no);
            endif;
            $result = $this->store($this->expenseManager,$data,'Expense');

            if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function delete($id){
		try{
            $this->db->trans_begin();

            $result = $this->trash($this->expenseManager,['id'=>$id],'Expense');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function saveApproveExpense($data) { 
        try{
            $this->db->trans_begin();
			
			$data['approved_by'] = $this->loginId;
			$data['approved_at'] = date('Y-m-d H:i:s');
			
			$result = $this->store($this->expenseManager,$data);

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