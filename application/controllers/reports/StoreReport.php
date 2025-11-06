<?php
class StoreReport extends MY_Controller{
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Store Report";
		$this->data['headData']->controller = "reports/storeReport";
    }

    public function stockRegister(){
		$this->data['headData']->pageTitle = "STOCK REGISTER";
        $this->data['headData']->pageUrl = "reports/storeReport/stockRegister";
        $this->data['pageHeader'] = 'STOCK REGISTER';
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
		$this->data['projectList'] = $this->project->getProjectList();
        $this->load->view("report/store_report/item_stock",$this->data);
    }

    public function getStockRegisterData(){
        $data = $this->input->post();
        $stockData = $this->storeReport->getStockRegisterData($data);

        $tbody=''; $i=1;
        if (!empty($stockData)) {
            foreach ($stockData as $row) {
                $tbody .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td class="text-center">'.$row->item_code.'</td>
                    <td class="text-left">'.$row->item_name.'</td>
                    <td class="text-center">'.$row->uom.'</td>
                    <td class="text-right">'.floatval($row->stock_qty).'</td>
                </tr>';
            }
        }		
        $this->printJson(['status'=>1,'tbody'=>$tbody]);
    }


    public function minimumStock($jsonData=''){
        $this->data['postData'] = [];
        if(!empty($jsonData)):
            $this->data['postData'] = (Array) decodeURL($jsonData);
        endif;
		$this->data['headData']->pageTitle = "Minimum Stock";
        $this->data['headData']->pageUrl = "reports/storeReport/minimumStock";
        $this->data['pageHeader'] = 'Minimum Stock';
		$this->data['projectList'] = $this->project->getProjectList();
        $this->load->view("report/store_report/minimum_stock",$this->data);
    }

    public function getMinimumStockData($jsonData=''){
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else: 
            $data = $this->input->post();
        endif;
        $stockData = $this->storeReport->getMinimumStockData($data);
        $tbody=''; $i=1;
        if (!empty($stockData)) {
            foreach ($stockData as $row) {
                $tbody .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td class="text-center">'.$row->item_code.'</td>
                    <td class="text-left">'.$row->item_name.'</td>
                    <td class="text-left">'.$row->category_name.'</td>
                    <td class="text-center">'.$row->uom.'</td>
                    <td class="text-right">'.floatval($row->min_stock).'</td>
                    <td class="text-right">'.floatval($row->stock_qty).'</td>
                    <td class="text-right">'.floatval($row->min_stock) - floatval($row->stock_qty).'</td>
                </tr>';
            }
        }		
        $this->printJson(['status'=>1,'tbody'=>$tbody]);
    }

}
?>