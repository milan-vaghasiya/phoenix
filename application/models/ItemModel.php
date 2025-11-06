<?php
class ItemModel extends MasterModel{
    private $itemMaster = "item_master";
    private $unitMaster = "unit_master";
    private $itemCategory = "item_category";
    private $itemKit = "item_kit";

    public function getItemCode($item_type=1){
        $queryData['tableName'] = $this->itemMaster;
        $queryData['select'] = "ifnull((MAX(CAST(REGEXP_SUBSTR(item_code,'[0-9]+') AS UNSIGNED)) + 1),1) as code";
        $queryData['where']['item_type'] = $item_type;
        $result = $this->row($queryData)->code;
        return $result;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "item_master.*,item_category.category_name";
        $data['leftJoin']['item_category'] = "item_category.id  = item_master.category_id";

        $data['where']['item_master.item_type'] = $data['item_type'];
        $data['where']['item_master.active'] = 1;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "item_master.uom";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getItemList($data=array()){
        $queryData['tableName'] = $this->itemMaster;
        $queryData['select'] = "item_master.*,item_master.id as item_id, item_category.category_name,item_category.batch_stock as stock_type";

        $queryData['leftJoin']['item_category'] = "item_category.id  = item_master.category_id";
        
		if(!empty($data['category_id'])):
            $queryData['where']['item_master.category_id'] = $data['category_id'];
        endif;
		
        if(!empty($data['item_type'])):
            $queryData['where_in']['item_master.item_type'] = $data['item_type'];
        endif;

        if(!empty($data['ids'])):
            $queryData['where_in']['item_master.id'] = $data['ids'];
        endif;

        if(!empty($data['not_category_id'])):
            $queryData['where_not_in']['item_master.category_id'] = $data['not_category_id'];
        endif;

        if(!empty($data['bom_type'])):
            $queryData['where_in']['item_master.bom_type'] = $data['bom_type'];
        endif;

        if(!empty($data['not_ids'])):
            $queryData['where_not_in']['item_master.id'] = $data['not_ids'];
        endif;
        
        if(!empty($data['active_item'])):
            $queryData['where_in']['item_master.active'] = $data['active_item'];
        else:
            $queryData['where']['item_master.active'] = 1;
        endif;
		
		if(!empty($data['search'])):
            $queryData['like']['item_master.item_code'] = $data['search'];
            $queryData['like']['item_master.item_name'] = $data['search'];
            $queryData['like']['item_category.category_name'] = $data['search'];
        endif;

        if(!empty($data['order_cat_itm_name'])){
            $queryData['order_by']['item_category.category_name'] = 'ASC';
            $queryData['order_by']['item_master.item_name'] = 'ASC';
        }else{
            $queryData['order_by']['item_master.item_type'] = "ASC";
            $queryData['order_by']['CAST(item_master.item_code AS UNSIGNED)'] = "ASC";
        }
        
        return $this->rows($queryData);
    }

    public function getItem($data){
        $queryData['tableName'] = $this->itemMaster;
        $queryData['select'] = "item_master.*,item_category.category_name,item_category.batch_stock as stock_type";

        if(!empty($data['price_structure_id'])):
            $mrpColumn = "mrp"; $priceColumn = "price";
            /* if(!empty($data['cm_id']) && $data['cm_id'] == 2):
                $mrpColumn = "mrp_2"; $priceColumn = "price_2";
            elseif(!empty($data['cm_id']) && $data['cm_id'] == 3):
                $mrpColumn = "mrp_3"; $priceColumn = "price_3";
            endif;

            if(!empty($data['party_id'])):
                $partyData = $this->party->getParty(['id'=>$data['party_id']]);
                $mrpColumn = ((!empty($partyData))?$partyData->price_structure_type:"").$mrpColumn;
                $priceColumn = ((!empty($partyData))?$partyData->price_structure_type:"").$priceColumn;
            endif;

            if(!empty($data['lead_id'])):
                $partyData = $this->crm->getLead(['id'=>$data['lead_id']]);
                $mrpColumn = ((!empty($partyData))?$partyData->price_structure_type:"").$mrpColumn;
                $priceColumn = ((!empty($partyData))?$partyData->price_structure_type:"").$priceColumn;
            endif;

            if(!empty($data['price_structure_type'])):
                $mrpColumn = $data['price_structure_type'].$mrpColumn;
                $priceColumn = $data['price_structure_type'].$priceColumn;
            endif; */
            
            $queryData['select'] .= ",ifnull(item_price_structure.".$mrpColumn.", item_master.mrp) as mrp,ifnull(item_price_structure.".$priceColumn.", item_master.price) as price,ifnull(item_price_structure.penalty_price,0) as penalty_price";
            $queryData['leftJoin']['item_price_structure'] = "item_price_structure.item_id = item_master.id AND item_price_structure.structure_id = ".$data['price_structure_id'];
        else:
            $queryData['select'] .= ",item_master.price as price";
        endif;

        $queryData['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        
        if(!empty($data['id'])):
            $queryData['where']['item_master.id'] = $data['id'];
        endif;

        if(!empty($data['item_code'])):
            $queryData['where']['item_master.item_code'] = trim($data['item_code']);
        endif;

        if(!empty($data['item_types'])):
            $queryData['where_in']['item_master.item_type'] = $data['item_types'];
        endif;

        return $this->row($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
			
			if(empty($data['id'])):
				$nextItemCode = $this->getItemCode();
				$data['item_code'] = (($data['item_type'] == 1) ? 'RM' : 'PI').lpad($nextItemCode,3,'0');
            endif;

            if($this->checkDuplicate(['item_name'=>$data['item_name'],'item_type'=>$data['item_type'],'id'=>$data['id']]) > 0):
                $errorMessage['item_name'] = "Item Name is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;
			if($this->checkDuplicate(['item_code'=>$data['item_code'],'item_type'=>$data['item_type'],'id'=>$data['id']]) > 0):
                $errorMessage['item_code'] = "Item Code is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;
			
            $result = $this->store($this->itemMaster,$data,"Item");            

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
        $queryData['tableName'] = $this->itemMaster;

        if(!empty($data['item_name']))
            $queryData['where']['item_name'] = $data['item_name'];
        if(!empty($data['item_type']))
            $queryData['where']['item_type'] = $data['item_type'];
		if(!empty($data['item_code']))
            $queryData['where']['item_code'] = $data['item_code'];
        if(!empty($data['category_id']))
            $queryData['where']['category_id'] = $data['category_id'];
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $checkData['columnName'] = ["item_id","scrap_group","ref_item_id","product_id","machine_id"];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);
            
            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Item is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->itemMaster,['id'=>$id],'Item');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function itemUnits(){
        $queryData['tableName'] = $this->unitMaster;
		return $this->rows($queryData);
	}

    public function itemUnit($id){
        $queryData['tableName'] = $this->unitMaster;
		$queryData['where']['id'] = $id;
		return $this->row($queryData);
	}


}
?>