<?php
class OtherExpense extends MY_Controller{
    private $index = "other_expense/index";
    private $form = "other_expense/form";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Other Expense";
		$this->data['headData']->controller = "otherExpense";        
        $this->data['headData']->pageUrl = "otherExpense";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'otherExpense']);
    }

    public function index(){
        $this->data['tableHeader'] = getAccountingDtHeader("otherExpense");
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->otherExpense->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getOtherExpenseData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addExpense(){
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Exp. Date is required.";
        if(empty($data['net_amount']))
            $errorMessage['net_amount'] = "Amount is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->otherExpense->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->otherExpense->getOtherExpense($data);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->otherExpense->delete($data));
        endif;
    }
}
?>