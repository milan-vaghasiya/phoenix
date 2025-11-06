<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StockLimit extends MY_Controller{
    private $indexPage = "stock_limit/index";
    private $form = "stock_limit/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "StockLimit";
		$this->data['headData']->controller = "stockLimit";        
	}

    public function index(){
        $this->data['headData']->pageUrl = "stockLimit";
        $this->data['tableHeader'] = getMasterDtHeader('stockLimit');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->stockLimit->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getStockLimitData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addStockLimit(){
        $data = $this->input->post();
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['category_type'=>1,'final_category'=>1]);
        $this->data['projectList'] = $this->project->getProjectList(['is_active'=>0]);
        $this->load->view($this->form,$this->data);
    }
	
      public function save(){
        $data = $this->input->post();
		$errorMessage = array();

        if(empty($data['project_id']))
			$errorMessage['project_id'] = "Project is required.";
         if(empty($data['category_id']))
			$errorMessage['category_id'] = "Category is required.";

        if(empty($data['min_stock'][0])){
            $errorMessage['general_error'] = "Product Data Is Required.";
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:

            $this->printJson($this->stockLimit->save($data));
        endif;
    }

    public function delete(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->stockLimit->delete($data));
        endif;
    }

     public function getProductList(){
		$data = $this->input->post();

        $productData = $this->item->getItemList(['category_id'=>$data['category_id'],'stock_limit'=>1]);
        $tbodyData='';$i=0;
        if (!empty($productData)) {
            foreach ($productData as $row) { 
                $stockData = $this->stockLimit->getStockLimitData(['project_id'=>$data['project_id'],'category_id'=>$data['category_id'],'item_id'=>$row->id,'single_row'=>1]);
                $tbodyData .= '<tr>
                        <td style="width:100px;font-size:11px;" class="text-wrap text-left">'.$row->item_code.'</td>
                        <td style="width:130px;font-size:11px;" class="text-wrap text-left">'.$row->item_name.'</td>
                        <td style="width:100px;font-size:11px;" class="text-wrap text-left">'.$row->category_name.'</td>
                        <td style="width:40px;font-size:11px;" class="text-wrap text-left">'.$row->uom.'</td>
                        <td class="text-center">
                            <input type="text" name="min_stock[]" id="min_stock_'.$i.'" class="form-control floatOnly" value="'.(!empty($stockData->min_stock) ? $stockData->min_stock :"").'">
                            <input type="hidden" name="item_id[]" id="item_id_'.$i.'" class="form-control" value="'.$row->id.'">
                            <input type="hidden" name="id[]" id="id_'.$i.'" class="form-control" value="'.(!empty($stockData->id) ? $stockData->id :"").'">
                        </td>
                        
                    </tr>';
                $i++;
            }
        } else {
            $tbodyData .= "<td colspan='5' class='text-center'>No Data</td>";
        }
        $this->printJson(['status'=>1, 'tbodyData'=>$tbodyData]); 

    }
}
?>