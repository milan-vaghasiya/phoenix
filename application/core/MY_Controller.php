<?php 
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class MY_Controller extends CI_Controller{

	public $termsTypeArray = ["Purchase","Sales"];
	public $gstPer = ['0'=>"NILL",'0.10'=>'0.10 %','0.25'=>"0.25 %",'1'=>"1 %",'3'=>"3%",'5'=>"5 %","6"=>"6 %","7.50"=>"7.50 %",'12'=>"12 %",'18'=>"18 %",'28'=>"28 %"];
	public $deptCategory = ["1"=>"Admin","2"=>"HR","3"=>"Purchase","4"=>"Sales","5"=>"Store","6"=>"QC","7"=>"General","8"=>"Machining"];
	public $empRole = ["1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","9" => "Sale Executive","5"=>"Purchase Manager","6"=>"Employee"];
    public $gender = ["M"=>"Male","F"=>"Female","O"=>"Other"];

    public $systemDesignation = [1=>"Machine Operator",2=>"Line Inspector",3=>"Setter Inspector",4=>"Process Setter",5=>"FQC Inspector",6=>"Sale Executive",7=>"Designer",8=>"Production Executive"];
	
	public $maritalStatus = ["Married","UnMarried","Widow"];
	public $empType = [1=>"Permanent (Fix)",2=>"Permanent (Hourly)",3=>"Temporary"];
	public $empGrade = ["Grade A","Grade B","Grade C","Grade D"];
	public $paymentMode = ['CASH','CHEQUE','NEFT/RTGS/IMPS ','CARD','UPI'];

	public $partyCategory = [1=>'Customer',2=>'Supplier',3=>'Vendor',4=>'Director',5=>'Ledger'];
	public $suppliedType = [1=>'Goods',2=>'Services',3=>'Goods & Services'];
	public $gstRegistrationTypes = [4=>'Un-Registerd',1=>'Registerd',2=>'Composition',3=>'Overseas'];
	public $automotiveArray = ["1" => 'Yes', "2" => "No"];
	public $businessTypes = ["Builder","Individuals","Government","Institute","Other"];
	public $vendorTypes = ['Manufacture', 'Service'];

	public $itemTypes = [1 => "Finish Goods", 2 => "Consumable", 3 => "Raw Material", 4 => 'Semi Finish', 5 => "Machineries", 10 => "Service Items"];
	public $stockTypes = [0=>"None",1=>'Batch Wise',2=>"Serial Wise"];
	public $leaveType = ["Casual leave","Sick leave","Marriage leave","Maternity leave","Paternity leave","Study leave"];

	//Types of Invoice
	public $purchaseTypeCodes = ["'PURGSTACC'","'PURIGSTACC'","'PURJOBGSTACC'","'PURJOBIGSTACC'","'PURURDGSTACC'","'PURURDIGSTACC'","'PURTFACC'","'PUREXEMPTEDTFACC'","'IMPORTACC'","'IMPORTSACC'","'SEZRACC'"/* ,"'SEZSGSTACC'","'SEZSTFACC'","'DEEMEDEXP'" */];

	public $salesTypeCodes = ["'SALESGSTACC'","'SALESIGSTACC'","'SALESJOBGSTACC'","'SALESJOBIGSTACC'","'SALESTFACC'","'SALESEXEMPTEDTFACC'","'EXPORTGSTACC'","'EXPORTTFACC'","'SEZSGSTACC'","'SEZSTFACC'","'DEEMEDEXP'"];
	
	public $appMenus = ["Project" => "84", "History" => "133", "Attendance" => "98", "Expense" => "90", "Purchase Order" => "140", "Leave" => "141", "Leave Approve" => "151", "DPR" => "132", "Material" => "121", "Work Progress" => "143", "Profile" => "150", "Profile" => "0", "Home" => "0"];

	public $taxClassCodes = [
		1 => [
			'PURGSTACC' => 'Local',
			'PURIGSTACC' => 'Central',
			'PURJOBGSTACC' => 'Jobwork Local',
			'PURJOBIGSTACC' => 'Jobwork Central',
			'PURURDGSTACC' => 'URD Local',
			'PURURDIGSTACC' => 'URD Central',
			'PURTFACC' => 'Local Tax Free',
			'PURCTFACC' => 'Central Tax Free',
			'PUREXEMPTEDTFACC' => 'Local Exempted (Nill Rated)',
			'PURCEXEMPTEDTFACC' => 'Central Exempted (Nill Rated)',
			'PURNONGST' => 'Local Non GST',
			'PURCNONGST' => 'Central Non GST',
			'IMPORTACC' => 'Import',
			'IMPORTSACC' => 'Import Services',
			'SEZRACC' => 'Received SEZ'
		],
		2 => [
			'SALESGSTACC' => 'Local',
			'SALESIGSTACC' => 'Central',
			'SALESJOBGSTACC' => 'Jobwork Local',
			'SALESJOBIGSTACC' => 'Jobwork Central',
			'SALESTFACC' => 'Local Tax Free',
			'SALESCTFACC' => 'Central Tax Free',
			'SALESEXEMPTEDTFACC' => 'Local Exempted (Nill Rated)',
			'SALESCEXEMPTEDTFACC' => 'Central Exempted (Nill Rated)',
			'SALESNONGST' => 'Local Non GST',
			'SALESCNONGST' => 'Central Non GST',
			'EXPORTGSTACC' => 'Export With Payment',
			'EXPORTTFACC' => "Export Without Payment",
			'SEZSGSTACC' => 'SEZ With Payment',
			'SEZSTFACC' => 'SEZ Without Payment',
			'DEEMEDEXP' => 'Deemed Export'
		]
	];

	public $stockTransTYpe = [
		'OPS' => 'OPENING STOCK',
		'GRN' => 'GRN',
		'SSI' => 'STORE STOCK ISSUE',
		'SSR' => 'STORE STOCK RETURN',
		'SDI' => 'STORE DIE ISSUE',
		'PRD' => 'PRODUCTION',
		'PMR' => 'PRODUCTION MATERIAL RETURN',
		'FIR' => 'FIR',
		'PCK' => 'PACKING',
		'STR' => 'STOCK TRANSFER',
		'CNV' => 'PRODUCT CONVERSION',
		'DLC' => 'DELIVERY CHALLAN',
		'INV' => 'SALES TAX INVOICE',
		'PUR' => 'PURCHASE INVOICE',
		'SVR' => 'STOCK VERIFICATION',
		'CUT' => 'CUTTING PRODUCTION',
		'MRJ' => 'MANUAL REJECTION',
		'EPS' => 'END PIECE RETURNED REVIEWED'
	];
	
	public function __construct(){
		parent::__construct();
		//echo '<br><br><hr><h1 style="text-align:center;color:red;">We are sorry!<br>Your ERP is Updating New Features</h1><hr><h2 style="text-align:center;color:green;">Thanks For Co-operate</h1>';exit;
		$this->isLoggedin();
		$this->data['headData'] = new StdClass;
		$this->load->library('form_validation');
		//$this->load->library('fcm');
		
		$this->load->model('masterModel');
		//$this->load->model('NotificationModel',"notification");
		$this->load->model('DashboardModel','dashboard');
		$this->load->model('PermissionModel','permission');
		//$this->load->model('StockTransModel','itemStock');

		/* Configration Models */
		$this->load->model("TermsModel","terms");
		$this->load->model("TransportModel","transport");
		$this->load->model("HsnMasterModel","hsnModel");
		$this->load->model("SelectOptionModel","selectOption");
		$this->load->model("GroupMasterModel","groupMaster");
		$this->load->model("HeadQuarterModel","headQuarter");

		/* HR Models */
		$this->load->model("hr/EmployeeModel","employee");
		$this->load->model("hr/AttendanceModel","attendance");
		$this->load->model("hr/DesignationModel","designation");
		$this->load->model("hr/LeaveApproveModel","leaveApprove");
		$this->load->model("hr/LeaveAuthorityModel","leaveAuthority");
		$this->load->model("hr/LeaveModel","leave");
		$this->load->model("hr/ShiftModel","shiftModel");
		$this->load->model("hr/DepartmentModel","department");

		/* Master Model */
		$this->load->model('PartyModel','party');
		$this->load->model('ProjectMasterModel','project');
		$this->load->model('ProjectHistoryModel','projectHistory');
		$this->load->model('ItemCategoryModel','itemCategory');
		$this->load->model('ItemModel','item');
		$this->load->model('SiteTransModel','siteTrans');
		$this->load->model('ExpenseModel','expense');
		$this->load->model('MachineModel','machine');
		$this->load->model('StockLimitModel','stockLimit'); 

		/* Accounting Master Models */
		$this->load->model('TransactionMainModel','transMainModel');
		$this->load->model('TaxMasterModel','taxMaster');
		$this->load->model('ExpenseMasterModel','expenseMaster');
		$this->load->model('TaxClassModel','taxClass');
		$this->load->model('PaymentVoucherModel','paymentVoucher');

		/* Sales Model */
		$this->load->model('SalesOrderModel','salesOrder');
		$this->load->model('SalesEnquiryModel','salesEnquiry');
		$this->load->model('SalesQuotationModel','salesQuotation');

		/* Purchase Model */
		$this->load->model('PurchaseOrderModel','purchaseOrder');
		$this->load->model('PurchaseIndentModel','purchaseIndent');
		$this->load->model('PurchaseModel','purchase');
		$this->load->model('WorkOrderModel','workOrder');

		/* Store Model */
		$this->load->model('StoreLocationModel','storeLocation');
		$this->load->model('GateInwardModel','gateInward');
		$this->load->model('StoreModel','store');

		/* Report Model */
		$this->load->model('report/StoreReportModel','storeReport'); 
		$this->load->model('report/PurchaseReportModel','purchaseReport');
		
		$this->setSessionVariables(["masterModel", "dashboard", "permission", "terms", "transport", "hsnModel", "selectOption", "employee", "attendance", "party", "project", "itemCategory", "item", "transMainModel", "taxMaster", "expenseMaster", "taxClass", "salesOrder", "salesEnquiry", "salesQuotation", "purchaseOrder", "storeLocation", "gateInward", "siteTrans","store", "purchaseIndent", "purchase", "designation","groupMaster","paymentVoucher", "projectHistory", "leave", "leaveAuthority", "leaveApprove","shiftModel","headQuarter","storeReport","purchaseReport","machine","stockLimit","workOrder","department"]);
	}

	public function setSessionVariables($modelNames){
		$this->data['dates'] = $this->dates = explode(' AND ',$this->session->userdata('financialYear'));
        $this->data['shortYear'] = $this->shortYear = date('y',strtotime($this->dates[0])).'-'.date('y',strtotime($this->dates[1]));
		$this->data['startYear'] = $this->startYear = date('Y',strtotime($this->dates[0]));
		$this->data['endYear'] = $this->endYear = date('Y',strtotime($this->dates[1]));
		$this->data['startYearDate'] = $this->startYearDate = date('Y-m-d',strtotime($this->dates[0]));
		$this->data['endYearDate'] = $this->endYearDate = date('Y-m-d',strtotime($this->dates[1]));

		$this->loginId = $this->session->userdata('loginId');
		$this->userCode = $this->session->userdata('emp_code');
		$this->userName = $this->session->userdata('emp_name');
		$this->userRole = $this->session->userdata('role');
		$this->userRoleName = $this->session->userdata('roleName');
		$this->partyId = $this->session->userdata('party_id');

		$models = $modelNames;
		foreach($models as $modelName):
			$modelName = trim($modelName);
			$this->{$modelName}->dates = $this->dates;
			$this->{$modelName}->shortYear = $this->shortYear;
			$this->{$modelName}->startYear = $this->startYear;
			$this->{$modelName}->endYear = $this->endYear;
			$this->{$modelName}->startYearDate = $this->startYearDate;
			$this->{$modelName}->endYearDate = $this->endYearDate;

			$this->{$modelName}->loginId = $this->loginId;
			$this->{$modelName}->userCode = $this->userCode;
			$this->{$modelName}->userName = $this->userName;
			$this->{$modelName}->userRole = $this->userRole;
			$this->{$modelName}->userRoleName = $this->userRoleName;
			$this->{$modelName}->partyId = $this->partyId;
		endforeach;
		return true;
	}
	
	public function isLoggedin(){
		if(!$this->session->userdata("loginId")):
			echo '<script>window.location.href="'.base_url().'";</script>';
		endif;
		return true;
	}
	
	public function printJson($data){
		print json_encode($data);exit;
	}
	
	public function checkGrants($url){
		$empPer = $this->session->userdata('emp_permission');
		if(!array_key_exists($url,$empPer)):
			redirect(base_url('error_403'));
		endif;
		return true;
	}
	
	/**** Generate QR Code ****/
	public function getQRCode($qrData,$dir,$file_name){
		if(isset($qrData) AND isset($file_name)):
			$file_name .= '.png';
			/* Load QR Code Library */
			$this->load->library('ciqrcode');
			
			if (!file_exists($dir)) {mkdir($dir, 0775, true);}

			/* QR Configuration  */
			$config['cacheable']    = true;
			$config['imagedir']     = $dir;
			$config['quality']      = true;
			$config['size']         = '1024';
			$config['black']        = array(255,255,255);
			$config['white']        = array(255,255,255);
			$this->ciqrcode->initialize($config);
	  
			/* QR Data  */
			$params['data']     = $qrData;
			$params['level']    = 'L';
			$params['size']     = 10;
			$params['savename'] = FCPATH.$config['imagedir']. $file_name;
			
			$this->ciqrcode->generate($params);

			return $dir. $file_name;
		endif;

		return false;
	}

	public function getTableHeader(){
		$data = $this->input->post();

		$response = call_user_func_array($data['hp_fn_name'],[$data['page']]);
		
		$result['theads'] = (isset($response[0])) ? $response[0] : '';
		$result['textAlign'] = (isset($response[1])) ? $response[1] : '';
		$result['srnoPosition'] = (isset($response[2])) ? $response[2] : 1;
		$result['sortable'] = (isset($response[3])) ? $response[3] : '';

		$this->printJson(['status'=>1,'data'=>$result]);
	}

	public function getPartyDetails(){
        $data = $this->input->post();
        $partyDetail = $this->party->getParty($data);
        $gstDetails = [];//$this->party->getPartyGSTDetail(['party_id'=>$data['id']]);
		$shipToDetails = [];//$this->party->getPartyDeliveryAddressDetails(['party_id'=>$data['id']]);
        $this->printJson(['status'=>1,'data'=>['partyDetail'=>$partyDetail,'gstDetails'=>$gstDetails,'shipToDetails'=>$shipToDetails]]);
    }

	public function getItemDetail(){
		$data = $this->input->post();
		$itemDetail = $this->item->getItem($data);

		if(empty($itemDetail)):
			$this->printJson(['status'=>0,'message'=>'Item Not Found.']);
		else:
			$this->printJson(['status'=>1,'data'=>['itemDetail'=>$itemDetail]]);
		endif;
	}

	public function getPartyInvoiceList(){
        $data = $this->input->post();
        $this->printJson($this->transMainModel->getPartyInvoiceList($data));
    }

	public function getVillageList(){
		$data = $this->input->post();
		$this->printJson($this->party->getVillageList($data));
	}

	public function getProjectAgencyList($postData = []){
		$data = (!empty($postData))?$postData:$this->input->post();
		$result = $this->project->getProjectAgencyList($data);
		if(!empty($postData)):
			return $result;
		else:
			$this->printJson(['status'=>1,'agencyList'=>$result]);
		endif;
	}

	public function getNextTransNo(){
		$data = $this->input->post();
		$nextNo = $this->transMainModel->nextTransNo($data['entry_type'],0,"",$data['cm_id']);
		$this->printJson(['status'=>1,'next_no'=>$nextNo]);
	}

	public function getAccountSummaryHtml(){
        $data = $this->input->post();
		$taxClass = $this->taxClass->getTaxClass($data['tax_class_id']);

        $this->data['taxList'] = (!empty($taxClass->tax_ids))?$this->taxMaster->getTaxList(['tax_ids'=>$taxClass->tax_ids,'is_active'=>((!empty($data['taxSummary']))?0:1)]):array();
        $this->data['expenseList'] = (!empty($taxClass->expense_ids))?$this->expenseMaster->getExpenseList(['expense_ids'=>$taxClass->expense_ids,'is_active'=>((!empty($data['taxSummary']))?0:1)]):array();
        $this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'DT'","'ED'","'EI'","'ID'","'II'"]]);
		
		$dataRow = (!empty($data['taxSummary']))?$data['taxSummary']:array();
        $this->data['dataRow'] = (object) $dataRow;
        $this->load->view('includes/tax_summary',$this->data);
    }

	public function trashFiles(){
        /** define the directory **/
        $dirs = [
            realpath(APPPATH . '../assets/uploads/qr_code/'),
            realpath(APPPATH . '../assets/uploads/import_excel/'),
            realpath(APPPATH . '../assets/uploads/invoice/'),
            realpath(APPPATH . '../assets/uploads/gst_report/'),
            realpath(APPPATH . '../assets/uploads/tcs_report/'),
            /* realpath(APPPATH . '../assets/uploads/eway_bill/'),
            realpath(APPPATH . '../assets/uploads/eway_bill_detail/'),
            realpath(APPPATH . '../assets/uploads/e_inv/') */
        ];

        foreach($dirs as $dir):
            $files = array();
            $files = scandir($dir);
            unset($files[0],$files[1]);

            /*** cycle through all files in the directory ***/
            foreach($files as $file):
                /*** if file is 24 hours (86400 seconds) old then delete it ***/
                if(time() - filectime($dir.'/'.$file) > 86400):
                    unlink($dir.'/'.$file);
                    //print_r(filectime($dir.'/'.$file)); print_r("<hr>");
                endif;
            endforeach;
        endforeach;

        return true;
    }

	public function getMonthListFY(){
		$monthList = array();
		$start    = (new DateTime($this->startYearDate))->modify('first day of this month');
        $end      = (new DateTime($this->endYearDate))->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);
        $i=0;
        foreach ($period as $dt):
            $monthList[$i]['val'] = $dt->format("Y-m-d");
            $monthList[$i++]['label'] = $dt->format("F-Y");
		endforeach;
		return $monthList;
	}
	
	public function callcURL($param = []){
	    $response = new StdClass;
	    if(isset($param['callURL']) AND (!empty($param['callURL'])))
	    {
    	    $curl = curl_init();
    
            curl_setopt_array($curl, array(
              CURLOPT_URL => $param['callURL'],
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
	    }
        return $response;
	}	
}
?>