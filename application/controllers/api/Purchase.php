<?php
class Purchase extends MY_ApiController{

	public function __construct(){
		parent::__construct();
        $this->data['headData']->pageTitle = "Purchase";
        $this->data['headData']->pageUrl = "api/purchase";
        $this->data['headData']->base_url = base_url();
	}
	
	public function getPOList(){
        $postData = $this->input->post();
		$param['trans_status'] = 0;
        $poData = $this->purchaseOrder->getPOList($postData);
		$poList = [];
		if(!empty($poData))
		{
			foreach($poData as $row){
				
				$row->itemDetail = $this->purchaseOrder->getPendingPoItems(['po_id'=>$row->id]);
				
				$poList[] = $row;
			}
		}
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$poList]);
    }


}
?>