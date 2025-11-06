<?php
class TaxPayment extends MY_Controller{
    private $index = "tax_payment/index";
    private $form = "tax_payment/form";
    private $tcsTdsPaymentIndex = "tax_payment/tcs_tds_payment_index";
    private $tcsTdsPaymentForm = "tax_payment/tcs_tds_payment_form";
    private $voucherSettlementForm = "tax_payment/tcs_tds_settlement_form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = ($this->uri->segment(1) != "tcsTdsPayment")?"GST Payment":"TCS/TDS Payment";
        $controller = ($this->uri->segment(1) != "tcsTdsPayment")?"taxPayment":"tcsTdsPayment";
		$this->data['headData']->controller = $controller;        
        $this->data['headData']->pageUrl = $controller;
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>$controller]);
	}

    /* GST Payment Code Start */
    public function index(){
        $this->data['tableHeader'] = getAccountingDtHeader("taxPayment");
		$this->load->view($this->index,$this->data);
    }

    public function getDtRows(){
        $data = $this->input->post();
        $data['entry_type'] = $this->data['entryData']->id;
		$result = $this->taxPayment->getDtRows($data); 
		$sendData = array(); $i=($data['start'] + 1);
		foreach($result['data'] as $row):
			$row->sr_no = $i++;
			$sendData[] = getTaxPaymentData($row);
		endforeach;
		$result['data'] = $sendData;
		$this->printJson($result);
	}

    public function addTaxPaymentVoucher(){		
		$this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>['"BA"','"BOL"','"BOA"','"CS"']]);		
		$this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] =  $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] =  $this->data['entryData']->trans_no;	
        $this->data['trans_number']	= $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['igstLedgerList'] = $this->party->getPartyList(['system_code'=>["'CLIGSTACC'","'CLIGSTINTACC'","'CLIGSTPENAACC'","'CLIGSTFEESACC'","'CLIGSTOTHACC'"]]);
        $this->data['cgstLedgerList'] = $this->party->getPartyList(['system_code'=>["'CLCGSTACC'","'CLCGSTINTACC'","'CLCGSTPENAACC'","'CLCGSTFEESACC'","'CLCGSTOTHACC'"]]);
        $this->data['sgstLedgerList'] = $this->party->getPartyList(['system_code'=>["'CLSGSTACC'","'CLSGSTINTACC'","'CLSGSTPENAACC'","'CLSGSTFEESACC'","'CLSGSTOTHACC'"]]);
        $this->data['cessLedgerList'] = $this->party->getPartyList(['system_code'=>["'CLGSTCESSACC'","'CLGSTCESSINTACC'","'CLGSTCESSPENAACC'","'CLGSTCESSFEESACC'","'CLGSTCESSOTHACC'"]]);
		$this->load->view($this->form,$this->data);
	}

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_number']))
            $errorMessage['trans_number'] = "Vou. No. is required.";
        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Vou. Date is required.";
        if(empty($data['vou_acc_id']))
            $errorMessage['vou_acc_id'] = "Bank/Cash Account is required.";
        if(empty($data['doc_no']))
            $errorMessage['doc_no'] = "CHL No. is required.";
        if(empty($data['doc_date']))
            $errorMessage['doc_date'] = "CHL Date is required.";
        if(empty(floatval($data['net_amount'])))
            $errorMessage['general_error'] = "GST Ledger Amount is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;

            // Filter the array for the first element with 'amount' greater than 0
            $filteredArray = array_filter($data['itemData'], function ($item) {
                return $item['amount'] > 0;
            });

            // Get the first element from the filtered array and return 'acc_id'
            $firstAccId = current(array_column($filteredArray, 'acc_id'));

            $data['party_id'] = $data['opp_acc_id'] = $firstAccId;

            $this->printJson($this->taxPayment->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->taxPayment->getTaxPayment($data);
		$this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>['"BA"','"BOL"','"BOA"','"CS"']]);
        $this->data['igstLedgerList'] = $this->party->getPartyList(['system_code'=>["'CLIGSTACC'","'CLIGSTINTACC'","'CLIGSTPENAACC'","'CLIGSTFEESACC'","'CLIGSTOTHACC'"]]);
        $this->data['cgstLedgerList'] = $this->party->getPartyList(['system_code'=>["'CLCGSTACC'","'CLCGSTINTACC'","'CLCGSTPENAACC'","'CLCGSTFEESACC'","'CLCGSTOTHACC'"]]);
        $this->data['sgstLedgerList'] = $this->party->getPartyList(['system_code'=>["'CLSGSTACC'","'CLSGSTINTACC'","'CLSGSTPENAACC'","'CLSGSTFEESACC'","'CLSGSTOTHACC'"]]);
        $this->data['cessLedgerList'] = $this->party->getPartyList(['system_code'=>["'CLGSTCESSACC'","'CLGSTCESSINTACC'","'CLGSTCESSPENAACC'","'CLGSTCESSFEESACC'","'CLGSTCESSOTHACC'"]]);
		$this->load->view($this->form,$this->data);
	}

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->taxPayment->delete($id));
        endif;
    }
    /* GST Payment Code End */

    /* TCS and TDS Payment Code Start */
    public function tcsTdsPayment(){
        $this->data['tableHeader'] = getAccountingDtHeader("tcsTdsPayment");
		$this->load->view($this->tcsTdsPaymentIndex,$this->data);
    }

    public function getTcsTdsDtRows(){
        $data = $this->input->post();
        $data['entry_type'] = $this->data['entryData']->id;
		$result = $this->taxPayment->getTcsTdsDtRows($data); 
		$sendData = array(); $i=($data['start'] + 1);
		foreach($result['data'] as $row):
			$row->sr_no = $i++;
			$sendData[] = getTcsTdsVoucherData($row);
		endforeach;
		$result['data'] = $sendData;
		$this->printJson($result);
	}

    public function addTcsTdsVoucher(){
        $this->data['quarterList'] = getQuarters($this->startYearDate,$this->endYearDate);
        $this->data['partyList'] = $this->party->getPartyList(['tax_type'=>['"TDS"','"TCS"']]);
        $this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>['"BA"','"BOL"','"BOA"']]);
        $this->load->view($this->tcsTdsPaymentForm,$this->data);
    }

    public function getTransNo(){
		$data = $this->input->post();
        $postData = [
            'tableName' => 'trans_main',
            'no_column' => 'trans_no',
            'condition' => 'vou_name_s = "'.$data['vou_name_s'].'" AND memo_type = "'.$data['memo_type'].'" AND trans_date >= "'.$this->startYearDate.'" AND trans_date <= "'.$this->endYearDate.'"'
        ];
        $trans_no = $this->transMainModel->getNextNo($postData);

		$this->printJson(['status'=>1,'trans_no'=>$trans_no]);
	}

    public function getLedgerList(){
		$data = $this->input->post();

		$postData['cm_ids'] = [$data['cm_id'],0];
		$postData['group_code'] = ['"BA"','"BOL"','"BOA"'];
		$bankCashAccounts = $this->party->getPartyList($postData);

		$options = '<option value="">Select Ledger</option>';
		$options .= getPartyListOption($bankCashAccounts,((!empty($data['vou_acc_id']))?$data['vou_acc_id']:0));

        $tax_type = ($data['vou_name_s'] == "TCSPmt")?'"TCS"':'"TDS"';
        $partyList = $this->party->getPartyList(['tax_type'=>[$tax_type]]);
        $partyListOptions = '<option value="">Select Ledger</option>';
		$partyListOptions .= getPartyListOption($partyList,((!empty($data['opp_acc_id']))?$data['opp_acc_id']:0));

		$this->printJson(['status'=>1,'vou_acc_list'=>$options,'opp_acc_list'=>$partyListOptions]);
	}

    public function saveTcsTdsVoucher(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['memo_type']))
            $errorMessage['memo_type'] = "Quarter is required.";
        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "CHL. Date is required.";
        if(empty($data['opp_acc_id']))
            $errorMessage['opp_acc_id'] = "Tax Ledger is required.";
        if(empty($data['vou_acc_id']))
            $errorMessage['vou_acc_id'] = "Bank Account is required.";
        if(empty($data['trans_number']))
            $errorMessage['trans_number'] = "Bank Vou. No. is required.";
        if(empty($data['ref_by']))
            $errorMessage['ref_by'] = "BRS Code is required.";
        if(empty($data['net_amount']))
            $errorMessage['net_amount'] = "Amount is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['party_id'] = $data['opp_acc_id'];
            $data['entry_type'] = $this->data['entryData']->id;
            $this->printJson($this->taxPayment->saveTcsTdsVoucher($data));
        endif;
    }

    public function editTcsTdsVoucher(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->taxPayment->getTcsTdsVoucher($data);
        $this->data['quarterList'] = getQuarters($this->startYearDate,$this->endYearDate);
        $this->data['partyList'] = $this->party->getPartyList(['tax_type'=>['"TDS"','"TCS"']]);
        $this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>['"BA"','"BOL"','"BOA"']]);
        $this->load->view($this->tcsTdsPaymentForm,$this->data);
    }

    public function deleteTcsTdsVoucher(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->taxPayment->deleteTcsTdsVoucher($id));
        endif;
    }

    public function voucherSettlement(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->taxPayment->getTcsTdsVoucher($data);
        $this->load->view($this->voucherSettlementForm,$this->data);
    }

    public function getUnsettledTransaction(){
        $data = $this->input->post();
        $result = $this->taxPayment->getUnsettledTransaction($data);

        $tbody = '';
        foreach($result as $row):
            $row->pending_amount = round(($row->tax_amount - $row->settled_amount),3);
            $tbody .= '<tr>
                <td>
                    <input type="checkbox" name="itemData['.$row->id.']" id="inv_checkbox'.$row->id.'" class="filled-in chk-col-success invCheck" value="'.$row->pending_amount.'">
                    <label for="inv_checkbox'.$row->id.'" id="inv_label'.$row->id.'"></label>
                </td>
                <td>'.$row->trans_number.'</td>
                <td>'.formatDate($row->trans_date).'</td>
                <td>'.$row->party_name.'</td>
                <td>'.$row->tax_amount.'</td>
                <td>'.$row->settled_amount.'</td>
                <td>'.$row->pending_amount.'</td>
            </tr>';
        endforeach;

        if(empty($tbody)):
            $tbody = '<tr>
                <td class="text-center" colspan="7">No data available in table</td>
            </tr>';
        endif;

        $this->printJson(['status'=>0,'tbodyData'=>$tbody]);
    }

    public function getSettledTransaction(){
        $data = $this->input->post();
        $result = $this->taxPayment->getSettledTransaction($data);

        $tbody = '';
        foreach($result as $row):
            $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Voucher','fndelete':'removeSettlement','res_function':'resRemoveSettlement'}";
            $deleteButton = '<button type="button" onclick="trash('.$deleteParam.');" class="btn btn-outline-danger btn-sm waves-effect waves-light"><i class="mdi mdi-trash-can-outline"></i></button>';

            $tbody .= '<tr>
                <td>'.$row->trans_number.'</td>
                <td>'.formatDate($row->trans_date).'</td>
                <td>'.$row->party_name.'</td>
                <td>'.$row->tax_amount.'</td>
                <td>'.$row->settled_amount.'</td>
                <td class="text-center">'.$deleteButton.'</td>
            </tr>';
        endforeach;

        if(empty($tbody)):
            $tbody = '<tr>
                <td class="text-center" colspan="7">No data available in table</td>
            </tr>';
        endif;

        $this->printJson(['status'=>0,'tbodyData'=>$tbody]);
    }

    public function saveSettledTransaction(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['itemData'])):
            $errorMessage['item_error'] = "Please select voucher.";
        else:
            $voucherDetail = $this->taxPayment->getTcsTdsVoucher(['id'=>$data['id']]);
            if(intval(array_sum($data['itemData'])) > floatval($voucherDetail->net_amount)):
                $errorMessage['item_error'] = "Settlement Amount greater than Voucher Amount.";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->taxPayment->saveSettledTransaction($data));
        endif;
    }

    public function removeSettlement(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->taxPayment->removeSettlement($id));
        endif;
    }
    /* TCS and TDS Payment Code End */
}
?>