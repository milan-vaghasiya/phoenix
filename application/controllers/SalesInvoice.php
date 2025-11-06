<?php
class SalesInvoice extends MY_Controller{
    private $indexPage = "sales_invoice/index";
    private $form = "sales_invoice/form";    
    private $packingPrintForm = "sales_invoice/packing_print_form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Sales Invoice";
		$this->data['headData']->controller = "salesInvoice";        
        $this->data['headData']->pageUrl = "salesInvoice";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'salesInvoice']);
	}

    public function index(){
        $this->data['tableHeader'] = getAccountingDtHeader("salesInvoice");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();
        $data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->salesInvoice->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getSalesInvoiceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addInvoice(){
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = "GJTD";//$this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>[1,3,4,10]]);
        //$this->data['brandList'] = $this->brandMaster->getBrandList();
        //$this->data['sizeList'] = $this->sizeMaster->getSizeList();
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();        
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Sales']);
        $this->data['vehicleList'] = $this->vehicle->getVehicleList();

        $this->data['taxClassList'] = $this->taxClass->getActiveTaxClass(2);
		$this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'DT'","'ED'","'EI'","'ID'","'II'"]]);
        $this->load->view($this->form,$this->data);
    }
    
    public function getNextInvNo(){
        $data = $this->input->post();
        $trans_no = $this->transMainModel->getNextNo(['tableName'=>'trans_main','no_column'=>'trans_no','condition'=>'trans_date >= "'.$this->startYearDate.'" AND trans_date <= "'.$this->endYearDate.'" AND cm_id = '.$data['cm_id'].' AND vou_name_s = "'.$this->data['entryData']->vou_name_short.'" AND memo_type = "'.$data['memo_type'].'"']);
        $this->printJson(['status'=>1,'next_no'=>$trans_no]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_no']))
            $errorMessage['trans_number'] = "Inv. No. is required.";
        if(empty($data['party_name']))
            $errorMessage['party_id'] = "Party Name is required.";
        if(empty($data['tax_class_id']))
            $errorMessage['tax_class_id'] = "GST Type is required.";
        if(empty($data['ship_to_id']) && $data['memo_type'] == "DEBIT")
            $errorMessage['ship_to_id'] = "Ship to is required.";
        if(empty($data['itemData'])):
            $errorMessage['itemData'] = "Item Details is required.";
        else:
            $bQty = array();
            //CHECK STOCK AVAILABLE OR NOT ?
            foreach($data['itemData'] as $key => $row):

                if($row['stock_eff'] == 1):
                    $postData = ['item_id' => $row['item_id'], 'batch_no' => 'GB', 'location_id' => $this->RTD_STORE->id, 'stock_required'=>1, 'single_row'=>1];
                    
                    $stockData = $this->itemStock->getItemStockBatchWise($postData);  
                    $batchKey = "";
                    $batchKey = $this->RTD_STORE->id.$row['item_id'];
                    
                    $stockQty = (!empty($stockData->qty))?floatVal($stockData->qty):0;
                    if(!empty($row['id'])):
                        $oldItem = $this->salesInvoice->getSalesInvoiceItem(['id'=>$row['id']]);
                        $stockQty = ($data['cm_id'] == $oldItem->cm_id)?($stockQty + $oldItem->qty):$stockQty;
                    endif;
                    
                    if(!isset($bQty[$batchKey])):
                        $bQty[$batchKey] = $row['qty'] ;
                    else:
                        $bQty[$batchKey] += $row['qty'];
                    endif;

                    if(empty($stockQty)):
                        $errorMessage['qty'.$key] = "Stock not available.";
                    else:
                        if($bQty[$batchKey] > $stockQty):
                            $errorMessage['qty'.$key] = "Stock not available.";
                        endif;
                    endif;
                endif;
            endforeach;
           
        endif;
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short;
            $this->printJson($this->salesInvoice->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->salesInvoice->getSalesInvoice(['id'=>$id,'itemList'=>1]);
        $this->data['gstinList'] = $this->party->getPartyGSTDetail(['party_id' => $dataRow->party_id]);
        $this->data['shipToList'] = $this->party->getPartyDeliveryAddressDetails(['party_id' => $dataRow->party_id]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category' => 1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>[1,3,4,10]]);
        //$this->data['brandList'] = $this->brandMaster->getBrandList();
        //$this->data['sizeList'] = $this->sizeMaster->getSizeList();
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Sales']);
        $this->data['vehicleList'] = $this->vehicle->getVehicleList();
        
        $this->data['taxClassList'] = $this->taxClass->getActiveTaxClass(2);
        $this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'DT'","'ED'","'EI'","'ID'","'II'"]]);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->salesInvoice->delete($id));
        endif;
    }

    public function printInvoice($jsonData=""){
        if(!empty($jsonData)):
            $postData = (Array) decodeURL($jsonData);
        else: 
            $postData = $this->input->post();
        endif;
        
        $printTypes = array();
        if(!empty($postData['original'])):
            $printTypes[] = "ORIGINAL";
        endif;

        if(!empty($postData['duplicate'])):
            $printTypes[] = "DUPLICATE";
        endif;

        if(!empty($postData['triplicate'])):
            $printTypes[] = "TRIPLICATE";
        endif;

        if(!empty($postData['extra_copy'])):
            for($i=1;$i<=$postData['extra_copy'];$i++):
                $printTypes[] = "EXTRA COPY";
            endfor;
        endif;

        $postData['header_footer'] = (!empty($postData['header_footer']))?1:0;
        $this->data['header_footer'] = $postData['header_footer'];

        $inv_id = (!empty($id))?$id:$postData['id'];

		$this->data['invData'] = $invData = $this->salesInvoice->getSalesInvoice(['id'=>$inv_id,'itemList'=>1]);
		$this->data['partyData'] = (!empty($invData->party_id))?$this->party->getParty(['id'=>$invData->party_id]):[];

        $taxClass = $this->taxClass->getTaxClass($invData->tax_class_id);
        $this->data['taxList'] = (!empty($taxClass->tax_ids))?$this->taxMaster->getTaxList(['tax_ids'=>$taxClass->tax_ids]):array();
        $this->data['expenseList'] = (!empty($taxClass->expense_ids))?$this->expenseMaster->getExpenseList(['expense_ids'=>$taxClass->expense_ids]):array();
        
        /* $this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2); */
		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo($invData->cm_id);
		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url($companyData->print_header);

        $htmlFooter = '<table style="border-top:1px solid #545454;margin-top:1px;">
            <tr>
                <td class="text-right">Page No. {PAGENO}/{nbpg}</td>
            </tr>
        </table>';
				
        $pdfData = "";
        $countPT = count($printTypes); $i=0;
        foreach($printTypes as $printType):
            ++$i;           
            $this->data['printType'] = $printType;
            $this->data['maxLinePP'] = (!empty($postData['max_lines']))?$postData['max_lines']:8;
		    $pdfData .= $this->load->view('sales_invoice/print',$this->data,true); 
            if($i != $countPT): $pdfData .= "<pagebreak resetpagenum='1'>"; endif;
        endforeach;
            
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = str_replace(["/","-"," "],"_",$invData->trans_number).'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->SetTitle($pdfFileName); 
        $mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,45));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
        $mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,5,(($postData['header_footer'] == 1)?5:35),12,5,5,'','','','','','','','','','A4-P');
    
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

    public function bulkPrint($jsonData = ""){
        $postData = (Array) decodeURL($jsonData);
        
        $postData['header_footer'] = 1;
        $this->data['header_footer'] = $postData['header_footer'];

        $logo=base_url('assets/images/logo.png');

        $htmlFooter = '<table style="border-top:1px solid #545454;margin-top:1px;">
            <tr>
                <td class="text-right">Page No. {PAGENO}/{nbpg}</td>
            </tr>
        </table>';

        $mpdf = new \Mpdf\Mpdf();
        $pdfFileName = 'Bulk-Invoice.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
        $mpdf->SetTitle($pdfFileName); 
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo,0.03,array(120,45));
        $mpdf->showWatermarkImage = true;
        $mpdf->SetProtection(array('print'));
        $mpdf->SetHTMLFooter($htmlFooter);        
        
        foreach($postData['ids'] as $inv_id):
            $this->data['invData'] = $invData = $this->salesInvoice->getSalesInvoice(['id'=>$inv_id,'itemList'=>1]);            
            $this->data['partyData'] = (!empty($invData->party_id))?$this->party->getParty(['id'=>$invData->party_id]):[];

            $taxClass = $this->taxClass->getTaxClass($invData->tax_class_id);
            $this->data['taxList'] = (!empty($taxClass->tax_ids))?$this->taxMaster->getTaxList(['tax_ids'=>$taxClass->tax_ids]):array();
            $this->data['expenseList'] = (!empty($taxClass->expense_ids))?$this->expenseMaster->getExpenseList(['expense_ids'=>$taxClass->expense_ids]):array();
            
            $this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo($invData->cm_id);                    
            
            $pdfData = "";
            $this->data['printType'] = "ORIGINAL";
            $this->data['maxLinePP'] = (!empty($postData['max_lines']))?$postData['max_lines']:8;
            $pdfData = $this->load->view('sales_invoice/print',$this->data,true);   
            
            $mpdf->AddPage('P','','1','','',10,5,(($postData['header_footer'] == 1)?5:35),12,5,5,'','','','','','','','','','A4-P');
            $mpdf->WriteHTML($pdfData);            
        endforeach;

        $mpdf->Output($pdfFileName,'I');
    }

    public function getPartyInvoiceItems(){
        $data = $this->input->post();
        $this->data['orderItems'] = $this->salesInvoice->getPendingInvoiceItems($data);
        $this->load->view('credit_note/create_creditnote',$this->data);
    }
}
?>