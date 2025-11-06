<?php
class StockLimitModel extends MasterModel{
    private $stock_limit = "stock_limit";

   

    public function getDTRows($data){
        $data['tableName'] = $this->stock_limit;
        $data['select'] = "stock_limit.*,item_category.category_name,item_master.item_code,item_master.item_name,item_master.uom,project_master.project_name";
        $data['leftJoin']['item_master'] = "item_master.id  = stock_limit.item_id";
        $data['leftJoin']['item_category'] = "item_category.id  = item_master.category_id";
        $data['leftJoin']['project_master'] = "project_master.id  = stock_limit.project_id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "project_master.project_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "item_master.uom";
        $data['searchCol'][] = "stock_limit.min_stock";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

  
    public function getStockLimitData($data=[]){
        $queryData['tableName'] = $this->stock_limit;
        $queryData['select'] = "stock_limit.*,item_master.category_id";
        $queryData['leftJoin']['item_master'] = "item_master.id  = stock_limit.item_id";


        if(!empty($data['project_id'])):
            $queryData['where']['stock_limit.project_id'] = $data['project_id'];
        endif;
        if(!empty($data['item_id'])):
            $queryData['where']['stock_limit.item_id'] = $data['item_id'];
        endif;

        if(!empty($data['category_id'])):
            $queryData['where']['item_master.category_id'] = $data['category_id'];
        endif;

        if(!empty($data['single_row'])):
            $result = $this->row($queryData);
        else:
            $result =$this->rows($queryData);
        endif;
        return $result;
    }

      public function save($data){ 
        try{
            $this->db->trans_begin();
			
            foreach($data['item_id'] as $key=>$value):
                if($data['min_stock'][$key] > 0):
                    $stockData =[
                        'id'=>(!empty($data['id'][$key]) ? $data['id'][$key] : ''),
                        'project_id'=>$data['project_id'], 
                        'item_id'=>$value,
                        'min_stock'=>$data['min_stock'][$key]
                    ];
                    $result = $this->store($this->stock_limit,$stockData,'Stock Limit');
                endif;
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function delete($data){
        try{
            $this->db->trans_begin();
            $result = $this->trash($this->stock_limit,['id'=>$data['id']],'Stock Limit');

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