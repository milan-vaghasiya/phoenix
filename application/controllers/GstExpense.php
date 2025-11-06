<?php
class GstExpense extends MY_Controller{
    private $indexPage = "gst_expense/index";
    private $form = "gst_expense/form";    

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Gst Expense";
		$this->data['headData']->controller = "gstExpense";        
        $this->data['headData']->pageUrl = "gstExpense";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'gstExpense']);
	}

    public function index(){
        $this->data['tableHeader'] = getAccountingDtHeader("gstExpense");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->gstExpense->getDTRows($data);
        $sendData = array();$i=($data['start'] + 1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;         
            $sendData[] = getGstExpenseData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addExpense(){
		$this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2,3"]);
		$this->data['itemLedgerList'] = $this->party->getPartyList(['party_category'=>4]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
		$this->data['termsList'] = $this->terms->getTermsList(['type'=>'Purchase']);

		$this->data['taxClassList'] = $this->taxClass->getActiveTaxClass(1);
		$this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'DT'","'ED'","'EI'","'ID'","'II'"]]);
		$this->load->view($this->form,$this->data);
	}

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_number']))
            $errorMessage['trans_number'] = "Inv No. is required.";
        if(empty($data['tax_class_id']))
            $errorMessage['tax_class_id'] = "GST Type is required.";
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        if(empty($data['itemData']))
            $errorMessage['itemData'] = "Item Details is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['doc_date'] = date("Y-m-d");
            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short;
            $this->printJson($this->gstExpense->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->gstExpense->getGstExpense(['id'=>$id,'itemList'=>1]);
        $this->data['gstinList'] = $this->party->getPartyGSTDetail(['party_id' => $dataRow->party_id]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2,3"]);
		$this->data['itemLedgerList'] = $this->party->getPartyList(['party_category'=>4]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
		$this->data['termsList'] = $this->terms->getTermsList(['type'=>'Purchase']);

		$this->data['taxClassList'] = $this->taxClass->getActiveTaxClass(1);
		$this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'DT'","'ED'","'EI'","'ID'","'II'"]]);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->gstExpense->delete($id));
        endif;
    }

    public function getPartyInvoiceItems(){
        $data = $this->input->post();
        $this->data['orderItems'] = $this->gstExpense->getPendingInvoiceItems($data);
        $this->load->view('debit_note/create_debitnote',$this->data);
    }

    public function getPendingInvoiceItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*,(trans_child.qty - trans_child.dispatch_qty) as pending_qty,trans_main.entry_type as main_entry_type,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no,1 as packing_qty,1 as packing_unit_qty";

        $queryData['leftJoin']['trans_main'] = "trans_child.trans_main_id = trans_main.id";

        $queryData['where']['trans_main.id'] = $data['id'];
        $queryData['where']['trans_child.entry_type'] = $this->data['entryData']->id;
        $queryData['where']['(trans_child.qty - trans_child.dispatch_qty) >'] = 0;

        if(!empty($data['cm_id'])):
            $queryData['where']['trans_child.cm_id'] = $data['cm_id'];
        endif;

        return $this->rows($queryData);
    }
}
?>