<?php
class Items extends MY_ApiController{

	public function __construct(){
		parent::__construct();
        $this->data['headData']->pageTitle = "Items";
        $this->data['headData']->pageUrl = "api/items";
        $this->data['headData']->base_url = base_url();
	}

    public function addItem(){
        $data = $this->input->post();
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['category_type'=>1,'final_category'=>1]);
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data]);
    }

    public function save(){
        $data = $this->input->post();
		$data['item_type'] = 1;
		//$data['item_code'] = $this->getItemCode($data['item_type']);
        $errorMessage = array();
        
        if(empty($data['category_id']))
            $errorMessage['category_id'] = "Category is required.";

        if(empty($data['item_name']))
            $errorMessage['item_name'] = "Item Name is required.";		

        if(empty($data['uom']))
            $errorMessage['uom'] = "Unit is required.";
            
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:

            $data['item_name'] = ucfirst($data['item_name']);

            $this->printJson($this->item->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->delete($id));
        endif;
    }

    public function getItemList(){
        $data = $this->input->post();
        $this->data['itemList'] = $this->item->getItemList($data);
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['itemList']]);
    }

    public function getItemDetails(){
        $data = $this->input->post();
        $this->data['itemDetail'] = $this->item->getItem($data);
        $this->printJson(['status'=>1,'messge'=>'Data Found','data'=>$this->data['itemDetail']]);
    }


}
?>