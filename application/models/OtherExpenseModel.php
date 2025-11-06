<?php
class OtherExpenseModel extends MasterModel{
    private $transMain = "trans_main";
    
    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = 'trans_main.id,trans_main.trans_number,trans_main.trans_date,trans_main.net_amount,trans_main.remark,trans_main.trans_status';

        $data['where']['trans_main.entry_type'] = $data['entry_type'];
        $data['where']['trans_main.trans_status'] = $data['status'];

        if($data['status'] == 0):
		    $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        else:
            $data['where']['trans_main.trans_date >='] = $this->startYearDate;
		    $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        endif;
        
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.remark";
        $data['searchCol'][] = "trans_main.amount";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if(empty($data['id'])):
                $data['trans_no'] = $this->transMainModel->getNextNo(['tableName' => 'trans_main', 'no_column' => 'trans_no', 'condition' => 'trans_date >= "'.$this->startYearDate.'" AND trans_date <= "'.$this->endYearDate.'" AND entry_type = '.$this->data['entryData']->id]);
            endif;
            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];            
            
            $result = $this->store($this->transMain,$data,"Expense Entry");            

            if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Throwable $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}
    }

    public function getOtherExpense($data){
        $queryData = [];
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $data['id'];
        $result = $this->row($queryData);
        return $result;
    }

    public function delete($data){
        try{
            $this->db->trans_begin();

            $postData["table_name"] = $this->transMain;
            $postData['where'] = [['column_name'=>'from_entry_type','column_value'=>$this->data['entryData']->id]];
            $postData['find'] = [['column_name'=>'ref_id','column_value'=>$data['id']]];
            $checkRef = $this->checkEntryReference($postData);
            if($checkRef['status'] == 0):
                $this->db->trans_rollback();
                return $checkRef;
            endif;

            $result = $this->trash($this->transMain,['id'=>$data['id']],'Expense Entry');

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