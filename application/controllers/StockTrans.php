<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StockTrans extends MY_Controller{
    private $indexPage = "stock_trans/index";
    private $sfIndexPage = "stock_trans/sf_index";
    private $form = "stock_trans/form";    
    private $rmIndexPage = "stock_trans/rm_index";
    private $rmform = "stock_trans/rm_form";    
    private $importForm = "stock_trans/import_form";
    private $coldStorageIndex = "stock_trans/cold_storage_index";
    private $coldStorageForm = "stock_trans/cold_storage_form";
    private $stock_transfer = "stock_trans/stock_transfer";
	private $rm_stock_transfer = "stock_trans/rm_stock_transfer";
	private $stock_transfer_log = "stock_trans/stock_transfer_log";

    public function __construct(){
		parent::__construct();
        /* if($this->userRole != -1):
            echo '<br><br><hr><h1 style="text-align:center;color:red;">We are sorry!<br>Your ERP is Updating New Features</h1><hr><h2 style="text-align:center;color:green;">Thanks For Co-operate</h1>';exit;
        endif; */
		$this->data['headData']->pageTitle = "FG Stock Inward";
		$this->data['headData']->controller = "stockTrans";        
        $this->data['headData']->pageUrl = "stockTrans";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'stockTrans']);
	}

    public function index(){
        $this->data['tableHeader'] = getStoreDtHeader("stockTrans");
        $this->load->view($this->indexPage,$this->data);
    }

    public function semiFinishStock(){
        $this->data['headData']->pageTitle = "Semi Finish Stock Inward";
        $this->data['headData']->pageUrl = "stockTrans/semiFinishStock";
        $this->data['tableHeader'] = getStoreDtHeader("stockTrans");
        $this->load->view($this->sfIndexPage,$this->data);
    }

    public function rmStock(){
        $this->data['headData']->pageTitle = "RM Stock Inward";
        $this->data['headData']->pageUrl = "stockTrans/rmStock";
        $this->data['tableHeader'] = getStoreDtHeader("rmStockTrans");
        $this->load->view($this->rmIndexPage,$this->data);
    }

    public function getDTRows($item_type = 1){
        $data = $this->input->post();
        $data['entry_type'] = $this->data['entryData']->id;
        $data['item_type'] = $item_type;
        $result = $this->itemStock->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getStockTransData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addStock(){
        $item_type = $this->input->post('item_type');
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>$item_type]);
        if($item_type != 3):
            $this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
            $this->load->view($this->form, $this->data);
        else:
            $this->load->view($this->rmform,$this->data);
        endif;
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		

        if(empty($data['item_id']))
			$errorMessage['item_id'] = "Item Name is required.";
        if(empty(floatVal($data['qty'])))
			$errorMessage['qty'] = "Qty is required.";

        if(!empty($data['item_id'])):
            $itemData = $this->item->getItem(['id'=>$data['item_id']]);
            if($itemData->bom_type == 2):
                if(empty($data['machine_id'])):
                    $errorMessage['machine_id'] = "Machine is required.";
                else:
                    $machineData = $this->item->getItem(['id'=>$data['machine_id']]);
                    $data['machine_code'] = $machineData->item_code;
                    $itemBom = $this->productBom->getItemBomList(['item_id'=>$data['item_id'],'machine_code'=>$machineData->item_code]);
                    if(empty($itemBom)):
                        $errorMessage['item_id'] = "Item BOM not found.";
                    endif;
                endif;                
            elseif($itemData->bom_type == 1):
                $data['machine_id'] = $data['machine_code'] = "";
                $itemBom = $this->productBom->getItemBomList(['item_id'=>$data['item_id']]);
                if(empty($itemBom)):
                    $errorMessage['item_id'] = "Item BOM not found.";
                endif;
            else:
                $data['machine_id'] = $data['machine_code'] = "";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['unique_id'] = $data['cm_id'];
            $data['entry_type'] = $this->data['entryData']->id;
            $data['location_id'] = $this->RTD_STORE->id;
            
            $this->printJson($this->itemStock->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->itemStock->delete($id));
        endif;
    }

    public function importStock(){
        $this->data['item_type'] = $this->input->post('item_type');
        $this->load->view($this->importForm,$this->data);
    }

    public function createExcel($jsonData=''){
        $postData = (Array) decodeURL($jsonData);

        $sheetName = "NA";
        if($postData['item_type'] == 1):
            $sheetName = "FG STOCK";
        elseif($postData['item_type'] == 4):
            $sheetName = "SF STOCK";
        elseif($postData['item_type'] == 3):
            $sheetName = "RM STOCK";
        endif;

        $paramData = $this->item->getItemList(['item_type'=>$postData['item_type']]);
        $table_column = array('id', 'item_code', 'item_name', 'category_name');
        $spreadsheet = new Spreadsheet();
        $inspSheet = $spreadsheet->getActiveSheet();
        $inspSheet = $inspSheet->setTitle($sheetName);
        $xlCol = 'A';
        $rows = 1;
        foreach ($table_column as $tCols):
            $inspSheet->setCellValue($xlCol . $rows, $tCols);
            $xlCol++;
        endforeach;

        $inspSheet->setCellValue($xlCol . $rows, "cartoon_qty");
        /* $xlCol++;
        $inspSheet->setCellValue($xlCol . $rows, "strip_qty");
        $xlCol++;
        $inspSheet->setCellValue($xlCol . $rows, "qty"); */
        $xlCol++;
        $inspSheet->setCellValue($xlCol . $rows, "machine_code");

        $rows = 2;
        foreach ($paramData as $row):
            $xlCol = "A";
            foreach ($table_column as $tCols):
                $inspSheet->setCellValue($xlCol . $rows, $row->{$tCols});
                $xlCol++;
            endforeach;
            $rows++;
        endforeach;
        
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        $fileDirectory = realpath(APPPATH . '../assets/uploads/import_excel');

        $fileName = "stock_ex_".time() . '.xlsx';
        if($postData['item_type'] == 1):
            $fileName = "/fg_stock_ex_".time() . '.xlsx';
        elseif($postData['item_type'] == 4):
            $fileName = "/sf_stock_ex_".time() . '.xlsx';
        elseif($postData['item_type'] == 3):
            $fileName = "/rm_stock_ex_".time() . '.xlsx';
        endif;

        $writer = new Xlsx($spreadsheet);

        $writer->save($fileDirectory . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        redirect(base_url('assets/uploads/import_excel') . $fileName);
    }

    public function importExcel(){
        $item_type = $this->input->post('item_type');
        $cm_id = $this->input->post('cm_id');
        $ref_date = $this->input->post('ref_date');
        
        if(empty($_FILES['excel_file'])):
            $this->printJson(['status'=>2,'message'=>'Please Select File!']);
        endif;

        $sheetName = "";
        if($item_type == 1):
            $sheetName = "FG STOCK";
        elseif($item_type == 4):
            $sheetName = "SF STOCK";
        elseif($item_type == 3):
            $sheetName = "RM STOCK";
        endif;

        $fileData = $this->importExcelFile($_FILES['excel_file'], 'import_excel', $sheetName);
        $row = 0;$errorMessage = array();$itemData = array();
        if(isset($fileData['status'])):
            $this->printJson($fileData);
        else:
            $fieldArray = $fileData[0][1];

            $importCount = 0;
            for ($i = 2; $i <= count($fileData[0]); $i++):
                $rowData = array();$c = 'A';
                foreach ($fileData[0][$i] as $key => $colData) :
                    $rowData[strtolower($fieldArray[$c])] = (!empty($colData))?$colData:"";
                    $c++;
                endforeach;

                $machine_id = $total_box = $strip_qty = $qty = $error = 0;
                
                $total_box = $rowData['cartoon_qty'];
                if($total_box > 0):
                    $itemData = $this->item->getItem(['id'=>$rowData['id'],'cm_id'=>$cm_id]);

                    if(!empty($itemData)):                            
                        if(empty($itemData->packing_qty)):
                            $errorMessage['packing_std'.$i] = "Packing Std. not found. Item Name : ".$rowData['item_name']."<br>Row No. : ".$i;
                            $error = 1;
                        else:
                            $strip_qty = round($total_box * $itemData->packing_qty,2);
                            $qty = round($strip_qty * $itemData->packing_unit_qty,3);
                        endif;

                        if(!empty($itemData->bom_type)):
                            if($itemData->bom_type == 2):
                                if(empty($rowData['machine_code'])):
                                    $errorMessage['item_error'.$i] = "Machine Code is required. Item Name : ".$rowData['item_name']."<br>Row No. : ".$i;
                                    $error = 1;
                                else:
                                    $machineData = $this->item->getItem(['item_code'=>$rowData['machine_code']]);
                                    $machine_id = $machineData->id;
                                    $itemBom = $this->productBom->getItemBomList(['item_id'=>$rowData['id'],'machine_code'=>$rowData['machine_code']]);
                                    if(empty($itemBom)):
                                        $errorMessage['item_error'.$i] = "Item BOM not found. Item Name : ".$rowData['item_name']."<br>Row No. : ".$i;
                                        $error = 1;
                                    endif;
                                endif;
                            endif;

                            if($itemData->bom_type == 1):
                                $itemBom = $this->productBom->getItemBomList(['item_id'=>$rowData['id']]);
                                if(empty($itemBom)):
                                    $errorMessage['item_error'.$i] = "Item BOM not found. Item Name : ".$rowData['item_name']."<br>Row No. : ".$i;
                                    $error = 1;
                                endif;
                            endif;
                        endif;
                    else:
                        $errorMessage['item_error'.$i] = "Item not found in software. Item Name : ".$rowData['item_name']."<br>Row No. : ".$i;
                        $error = 1;
                    endif;
                endif;  
                
                if(empty($error) && $qty > 0):
                    $transData = [
                        'id' => "",
                        'p_or_m' => 1,
                        //'ref_no' => 'Op. Stock',
                        'batch_no' => "GB",
                        'unique_id' => $cm_id,
                        'ref_date' => (!empty($ref_date))?$ref_date:date("Y-m-d"),
                        //'ref_date' => "2024-04-01",
                        'cm_id' => $cm_id,
                        'item_id' => $rowData['id'],
                        'total_box' => $total_box,
                        /* 'strip_qty' => (isset($rowData['strip_qty']) && !empty($rowData['strip_qty']))?$rowData['strip_qty']:$strip_qty,
                        'qty' => (isset($rowData['qty']) && !empty($rowData['qty']))?$rowData['qty']:$qty, */
                        'strip_qty' => $strip_qty,
                        'qty' => $qty,
                        'remark' => 'IMPORT',
                        'entry_type' => $this->data['entryData']->id,
                        'location_id' => $this->RTD_STORE->id,
                        'machine_id' => $machine_id,
                        'machine_code' => $rowData['machine_code']
                    ];

                    $this->itemStock->save($transData);$importCount++;
                endif;
                        
                $row++;
            endfor;

            if(!empty($errorMessage)):
                $this->printJson(['status'=>0,'message'=>$errorMessage,'success_message'=>'Data Imported successfully. No. of items : '.$importCount]);
            else:
                //$filePath = realpath(APPPATH . '../assets/uploads/import_excel/');
                //unlink($filePath."/".$_FILES['excel_file']['name']);
                $this->printJson(['status'=>1,'message'=>'Data Imported successfully. No. of items : '.$importCount]);
            endif;
        endif;        
    }

    /* public function addDummyStock(){
        $itemList = $this->item->getItemList(['item_type'=>[1]]);

        $i=1;
        foreach($itemList as $row):
            $itemDetail = $this->item->getItem(['id'=>$row->id]);
            $packing_qty = $itemDetail->packing_qty;
            $packing_unit_qty = $itemDetail->packing_unit_qty;

            if($packing_qty > 0):
                $cartoon_qty = 900;
                $strip_qty = round($cartoon_qty * $packing_qty,2);
                $total_qty = round($strip_qty * $packing_unit_qty,2);

                $stockTrans = [
                    'id' => "",
                    'p_or_m' => 1,
                    'entry_type' => $this->data['entryData']->id,
                    'batch_no' => "GB",
                    'location_id' => $this->RTD_STORE->id,
                    'unique_id' => 1,
                    'ref_date' => date("Y-m-d"),
                    'cm_id' => 1,
                    'item_id' => $row->id,
                    'total_box' => $cartoon_qty,
                    'strip_qty' => $strip_qty,
                    'qty' => $total_qty,
                    'remark' => ""
                ];

                $this->itemStock->save($stockTrans); $i++;
            endif;
        endforeach;

        echo "Dummy Stock added. No of Items : ".$i;exit;
    } */

    /* Cold Storage Stock */
    public function coldStorage(){
        $this->data['headData']->pageTitle = "COLD STORAGE STOCK REGISTER";
        $this->data['pageHeader'] = 'COLD STORAGE STOCK REGISTER';
        $this->data['headData']->pageUrl = "stockTrans/coldStorage";
        $this->load->view($this->coldStorageIndex,$this->data);
    }

    public function getColdStorageStockRegisterData(){
        $data = $this->input->post();
        $locationIds = $this->storeLocation->getStoreLocationList(['ref_id'=>6,'final_location'=>1]);
        $locationIds = array_column($locationIds,'id');

        $result = $this->itemStock->getItemStock(['cm_id'=>$data['cm_id'],'location_ids'=>$locationIds]);

        $tbody = '';$i=1;
        foreach($result as $row):

            $viewParam = "{'postData':{'item_id' : ".$row->item_id.", 'cm_id' : '".$data['cm_id']."', 'action':'view'},'modal_id' : 'bs-right-md-modal', 'form_id' : 'batchWiseColsStorageStock', 'title' : 'Invoice Wise Cold Storage Stock','call_function':'batchWiseColsStorageStock', 'button' : 'close'}";
            $viewButton = '<a class="btn btn-info btn-edit permission-read" href="javascript:void(0)" datatip="View" flow="down" onclick="modalAction('.$viewParam.');"><i class="fas fa-eye"></i></a>';

            $receiveParam = "{'postData':{'item_id' : ".$row->item_id.", 'cm_id' : '".$data['cm_id']."', 'action':'receive'},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'batchWiseColsStorageStock', 'title' : 'Receive Cold Storage Stock','call_function':'batchWiseColsStorageStock', 'fnsave' : 'saveColdStorageStock', 'js_store_fn' : 'customStore'}";
            $receiveButton = '<a class="btn btn-warning btn-edit permission-write" href="javascript:void(0)" datatip="Receive Stock" flow="down" onclick="modalAction('.$receiveParam.');"><i class="fa fa-reply"></i></a>';

            $action = getActionButton($viewButton.$receiveButton);

            $tbody .= '<tr>
                <td class="text-center">'.$action.'</td>
                <td class="text-left">'.$row->item_code.'</td>
                <td class="text-left">
					'.$row->item_name.'
				</td>
                <td class="text-left">
					'.$row->category_name.'
				</td>
                <td  class="text-right">
                    '.floatVal($row->stock_qty).'
				</td>
                <td  class="text-right">
                    '.floatVal($row->total_strip_qty).'
				</td>
				<td  class="text-right">
                    '.floatVal($row->total_box_qty).'
				</td>
            </tr>';
        endforeach;

        $this->printJson(['status'=>1,'tbody'=>$tbody]);
    }

    public function batchWiseColsStorageStock(){
        $data = $this->input->post();
        $locationIds = $this->storeLocation->getStoreLocationList(['ref_id'=>6,'final_location'=>1]);
        $locationIds = array_column($locationIds,'id');

        $result = $this->itemStock->getItemStockBatchWise(['cm_id'=>$data['cm_id'],'location_ids'=>$locationIds,'item_id'=>$data['item_id'],'stock_required'=>1]);

        $this->data['action'] = $data['action'];
        $this->data['stockList'] = $result;
        $this->load->view($this->coldStorageForm,$this->data);
    }

    public function saveColdStorageStock(){
        $data = $this->input->post();
        $errorMessage = [];

        if(!empty($data['itemData']) && empty(array_sum(array_column($data['itemData'],'total_box')))):
            $errorMessage['general_error'] = "Received Qty is required.";
        endif;
        if(empty($data['itemData'])):
            $errorMessage['general_error'] = "No Item found for stock received.";
        endif;

        if(!empty($data['itemData'])):
            $bQty = array();
            foreach($data['itemData'] as $key=>$row):
                $postData = ['unique_id' => $row['unique_id'], 'item_id' => $row['item_id'], 'location_id' => $row['location_id'], 'stock_required'=>1, 'single_row'=>1];
                    
                $stockData = $this->itemStock->getItemStockBatchWise($postData);  
                $batchKey = "";
                $batchKey = $row['unique_id'];
                    
                $stockQty = (!empty($stockData->total_box_qty))?floatVal($stockData->total_box_qty):0;
                
                if(!isset($bQty[$batchKey])):
                    $bQty[$batchKey] = $row['total_box'] ;
                else:
                    $bQty[$batchKey] += $row['total_box'];
                endif;

                if(empty($stockQty)):
                    $errorMessage['total_box_'.$key] = "Stock not available.";
                else:
                    if($bQty[$batchKey] > $stockQty):
                        $errorMessage['total_box_'.$key] = "Stock not available.";
                    endif;
                endif;
            endforeach;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['entry_type'] = $this->transMainModel->getEntryType(['controller'=>'stockTrans/coldStorage'])->id;
            $this->printJson($this->itemStock->saveColdStorageStock($data));
        endif;
    }

	/** STOCK TRANSFER **/
	public function stockTransfer(){
        $item_type = $this->input->post('item_type');
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>$item_type]);
        $this->load->view($this->stock_transfer, $this->data);
    }
	
	public function getItemCurrentStock(){
		$data = $this->input->post();
	
		if(empty($data['item_id'])):
            $this->printJson(['status'=>0,'message'=>'Something Went Wrong....']);
        else:
			$result = $this->itemStock->getItemCurrentStock(['item_id'=>$data['item_id']]);
            $this->printJson($result);
        endif;
		
	}
	
	public function saveStockTransfer(){
        $data = $this->input->post();
		$errorMessage = array();		
        
        if(empty($data['from_item_id']))
			$errorMessage['from_item_id'] = "From Item Name is required.";
		
		if($data['stock_entry_type'] == 1)
		{
            if(empty($data['to_item_id'])){ $errorMessage['to_item_id'] = "To Item Name is required."; }
            if(empty(floatVal($data['price']))){ $errorMessage['price'] = "Price is required."; }
            $data['remark'] = 'STOCK TRANSFER';
		}
		else{$data['remark'] = 'STOCK DEDUCTION';}
		
		
        if(empty(floatVal($data['qty'])))
			$errorMessage['qty'] = "Qty is required.";
		

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['unique_id'] = $data['cm_id'];
            $data['entry_type'] = $this->data['entryData']->id;
            $data['location_id'] = $this->RTD_STORE->id;
			//$data['remark'] = 'STOCK TRANSFER';
            
			$this->printJson($this->itemStock->saveStockTransfer($data));
        endif;
    }

    public function stockTransferLog(){
        $this->data['headData']->pageTitle = "Stock Transfer Log";
        $this->data['tableHeader'] = getStoreDtHeader("stockTransferLog");
        $this->load->view($this->stock_transfer_log,$this->data);
    }

    public function getTransferLogDTRows(){
        $data = $this->input->post();
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->itemStock->getTransferLogDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getStockTransferData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function deleteTransferedLog(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->itemStock->deleteTransferedLog($id));
        endif;
    }
}
?>