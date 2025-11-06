<?php
class StoreReportModel extends MasterModel{
    private $itemMaster = "item_master";

	public function getStockRegisterData($data){       
        $location_id = "";
        if(!empty($data['location_id'])):
            $location_id = ' AND location_id = '.$data['location_id'];
        endif;

        $queryData = array();
        $queryData['tableName'] = $this->itemMaster;
        $queryData['select'] = "item_master.item_code,item_master.item_name,item_master.uom,ifnull(st.stock_qty,0) as stock_qty";
        $queryData['leftJoin']['(SELECT SUM(qty * p_or_m) as stock_qty,item_id,location_id FROM stock_trans WHERE is_delete = 0 '.$location_id.' GROUP BY item_id) as st'] = "item_master.id = st.item_id";
        $queryData['where']['item_master.item_type'] = 1;
        
        if(!empty($data['item_id'])):
            $queryData['where']['item_master.id'] = $data['item_id'];
        endif;

        $result = $this->rows($queryData);		
        return $result;
    }
	
	/*Minimum Stock  Data */
	public function getMinimumStockData($data){
        $queryData = [];
        $queryData['tableName'] = "stock_limit";
        $queryData['select'] = "stock_limit.id, stock_limit.min_stock, stock_limit.project_id,item_master.item_code, item_master.item_name,item_master.uom, item_category.category_name,IFNULL(stock.stock_qty,0) as stock_qty,project_master.project_name";
        $queryData['leftJoin']['item_master'] = 'item_master.id = stock_limit.item_id';
        $queryData['leftJoin']['item_category'] = 'item_category.id = item_master.category_id';
        $queryData['leftJoin']['project_master'] = 'project_master.id = stock_limit.project_id';
        
        $queryData['leftJoin']['(SELECT SUM(qty * p_or_m) as stock_qty,item_id,location_id FROM stock_trans WHERE is_delete = 0  GROUP BY item_id,location_id) as stock'] = "stock_limit.item_id = stock.item_id AND stock_limit.project_id = stock.location_id";

        if(!empty($data['location_id'])):
            $queryData['where']['stock_limit.project_id'] = $data['location_id'];
        endif;

		$queryData['customWhere'][] = "(stock_limit.min_stock > stock.stock_qty)";

        if(!empty($data['dashData'])):
		    $queryData['order_by']['stock_limit.min_stock'] = "DESC";
            $queryData['limit'] = 10;
        endif;
       
        $result = $this->rows($queryData);		
        return $result;
    }	
}
?>