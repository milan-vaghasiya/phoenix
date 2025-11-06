<?php
class LeaveAuthority extends MY_Controller
{
    private $indexPage = "hr/leave/leave_authority";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Leave Authority";
		$this->data['headData']->controller = "hr/leaveAuthority";
	}
	
	public function index(){
		$this->data['deptRows'] = $this->department->getDepartmentList();
        $this->load->view($this->indexPage,$this->data);
    }

	public function getLeaveAuthority(){
        $data = $this->input->post();
        $result = $this->leaveAuthorityModel->getLeaveAuthority($data);
        
		$tbody=''; $i = 1; $empOpt = '';
		
		if(!empty($result)){
			foreach($result as $row){
				$authParam = "'".$row->id."','".$row->pla_id."','".$row->fla_id."','".$row->emp_name."'";
				
				$row->plaList = (!empty($row->plaList) ? str_replace(',','/',$row->plaList) : '');
				$row->flaList = (!empty($row->flaList) ? str_replace(',','/',$row->flaList) : '');
				
				$tbody.='<tr>
					<td>'.$i++.'</td>
					<td class="text-left">'.$row->emp_code.'</td>
					<td class="text-left">'.$row->emp_name.'</td>
					<td class="text-left fs-12">'.$row->plaList.'<span class="pla_id'.$row->id.' error"></span></td>
					<td class="text-left fs-12">'.$row->flaList.'<span class="fla_id'.$row->id.' error"></span></td>
					<td><button type="button" class="btn waves-effect waves-light btn-info" onclick="openLeaveAuthModal('.$authParam.');">Set Authority</button></td>
				</tr>';
			}
		}
        $this->printJson(['status'=>1,"tbodyData"=>$tbody]);
    }
	
	public function getEmpLeaveAuthDetail(){
        $postData = $this->input->post();
        $empData = $this->employee->getEmployeeList(['status'=>1]);
		$flaOptions = '';
		$plaOptions = '';
		if(!empty($empData)){ 
			foreach($empData as $row){ 
				$empName = (!empty($row->emp_code)) ? '['.$row->emp_code.'] '.$row->emp_name : $row->emp_name;
				$selected = (!empty($postData['fla']) AND in_array($row->id, explode(",", $postData['fla']))) ? 'selected' : '';
				$flaOptions.= '<option value="'.$row->id.'" '.$selected.'>'.$empName.'</option>';
				$plaSelected = (!empty($postData['pla_id']) AND in_array($row->id, explode(",", $postData['pla_id']))) ? 'selected' : '';
				$plaOptions.= '<option value="'.$row->id.'" '.$plaSelected.'>'.$empName.'</option>';
			}
		}
		$this->printJson(['status'=>1,"plaOptions"=>$plaOptions,"flaOptions"=>$flaOptions]);
    }

    public function saveAuthority(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['fla_id'])):
            $errorMessage['fla_id'] = "Atleast 1 Authoriser required.";
		else:
			$data['fla_id'] = implode(',',$data['fla_id']);
		endif;
		$data['pla_id'] = !empty($data['pla_id']) ? implode(',',$data['pla_id']) : "";
		if(!empty($errorMessage)):
				$this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$data['updated_by'] = $this->loginId;
        	$data['updated_at'] = date("Y-m-d H:i:s");
        	$this->printJson($this->leaveAuthorityModel->saveAuthority($data));
        endif;
		
    }
}
?>