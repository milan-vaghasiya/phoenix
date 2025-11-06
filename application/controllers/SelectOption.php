<?php
class SelectOption extends MY_Controller{ 
    private $index = "select_option/index";
    private $form = "select_option/form";
	private $categorySequence = "select_option/category_equence";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Select Option";
		$this->data['headData']->controller = "selectOption";
        $this->data['headData']->pageUrl = "selectOption";
	} 
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->index,$this->data);
    }
	
    public function getDTRows($type=1){
        $data = $this->input->post(); $data['type'] = $type;
        $result = $this->selectOption->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getSelectOptionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addSelectOption(){
		$data = $this->input->post();
		$this->data['type'] = $data['type'];
		if(!empty($data['type']) && $data['type'] == 4){
			$this->data['laborCateList'] = $this->selectOption->getSelectOptionList(['type'=>2]);
		}
        $this->load->view($this->form, $this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();
        if(empty($data['detail'])){ $errorMessage['detail'] = "Option is required."; }
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if($data['type']==4){ $data['labor_cat_ids']=implode(',',$data['labor_cat_ids']);}else{ unset($data['labor_cat_ids']); }
            $this->printJson($this->selectOption->save($data));
        endif;
    }

    public function edit(){     
        $data = $this->input->post();
        $this->data['dataRow'] =$dataRow= $this->selectOption->getSelectOption($data);
		
		if(!empty($dataRow->type) && $dataRow->type == 4){
			$this->data['laborCateList'] = $this->selectOption->getSelectOptionList(['type'=>2]);
		}
		
        $this->load->view($this->form, $this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->selectOption->delete($id));
        endif;
    }

	public function categorySequence(){
		$data = $this->input->post();
		$data['order_by'] = "ASC";
        $this->data['categoryLists'] = $this->selectOption->getSelectOptionList($data);
        $this->load->view($this->categorySequence, $this->data);
	}
	
	public function updateCategorySequance(){
        $data = $this->input->post();
		
		$errorMessage = array();		
		if(empty($data['id']))
			$errorMessage['id'] = "SYS ID is required.";
		
		if(empty($errorMessage)):
			$this->printJson($this->selectOption->updateCategorySequance($data));			
		endif;
    }
	
}
?>