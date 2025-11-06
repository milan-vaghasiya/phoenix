<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getHrDtHeader($page){
	
    /* Employee Header */
	$data['employees'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['employees'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['employees'][] = ["name"=>"Emp Code"];
    $data['employees'][] = ["name"=>"Employee Name"];
    $data['employees'][] = ["name"=>"Mobile No."];
	$data['employees'][] = ["name"=>"Designation"];
	$data['employees'][] = ["name"=>"Shift"];
	$data['employees'][] = ["name"=>"Joining Date"];
	$data['employees'][] = ["name"=>"Date Of Birth"];
	
    /* Pending/Self Approved Attendance Header */
	$data['attendance'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['attendance'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['attendance'][] = ["name"=>"Employee Code"];
    $data['attendance'][] = ["name"=>"Employee Name"];
    $data['attendance'][] = ["name"=>"Punch Type"];
    $data['attendance'][] = ["name"=>"Attendance Date"];
    $data['attendance'][] = ["name"=>"Punch Time"];
    $data['attendance'][] = ["name"=>"Project"];
    $data['attendance'][] = ["name"=>"Shift"];
    $data['attendance'][] = ["name"=>"Location"];

	/* Department Header */
    $data['department'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['department'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['department'][] = ["name"=>"Department Name"];
    $data['department'][] = ["name"=>"Remark"];
	
	/* Designation Header */
    $data['designation'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['designation'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['designation'][] = ["name"=>"Designation Name"];
    $data['designation'][] = ["name"=>"Remark"];

    /* Leave Header */
    $data['leave'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['leave'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    // $data['leave'][] = ["name"=>"Sr. No"]; //03-05-25
    $data['leave'][] = ["name"=>"Employee"];
    $data['leave'][] = ["name"=>"Emp Code"];
    $data['leave'][] = ["name"=>"Leave Type"];
    $data['leave'][] = ["name"=>"From"];
    $data['leave'][] = ["name"=>"To"];
    $data['leave'][] = ["name"=>"Leave Days"];
    $data['leave'][] = ["name"=>"Reason"];
    $data['leave'][] = ["name"=>"Status"];

	/* Leave Approve Header */
	$data['leaveApprove'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['leaveApprove'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
	$data['leaveApprove'][] = ["name"=>"Employee"];
	$data['leaveApprove'][] = ["name"=>"Emp Code"];
	$data['leaveApprove'][] = ["name"=>"Leave Type"];
	$data['leaveApprove'][] = ["name"=>"From"];
	$data['leaveApprove'][] = ["name"=>"To"];
	$data['leaveApprove'][] = ["name"=>"Leave Days"];
	$data['leaveApprove'][] = ["name"=>"Reason"];
	$data['leaveApprove'][] = ["name"=>"Status"];
	
	/* Shift Header */
	$data['shift'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['shift'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['shift'][] = ["name"=>"Shift Name"];
	$data['shift'][] = ["name"=>"Start Time"];
	$data['shift'][] = ["name"=>"End Time"];
	$data['shift'][] = ["name"=>"Production Time"];
	$data['shift'][] = ["name"=>"Lunch Start Time"];
	$data['shift'][] = ["name"=>"Lunch End Time"];
	$data['shift'][] = ["name"=>"Late In"];
	$data['shift'][] = ["name"=>"Early Out"];
	$data['shift'][] = ["name"=>"Lunch Grace"];
	$data['shift'][] = ["name"=>"Late/Early Fine"];

    return tableHeader($data[$page]);
}

/* Employee Table Data */
function getEmployeeData($data){
    
    $activeButton = ''; $editButton = ''; $deleteButton = ''; $resetPsw = ''; $docButton = ''; $ledgerBtn = ''; $salaryButton ="";
    
    if($data->is_active == 1):
        $activeParam = "{'postData':{'id' : ".$data->id.", 'is_active' : 0},'fnsave':'activeInactive','message':'Are you sure want to De-Active this Employee?'}";
        $activeButton = '<a class="btn btn-youtube permission-modify" href="javascript:void(0)" datatip="De-Active" flow="down" onclick="confirmStore('.$activeParam.');"><i class="fa fa-ban"></i></a>';    

		$editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editEmployee', 'title' : 'Update Employee','call_function':'edit'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
        
		$deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Employee'}";
		$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
        
		$docParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addDocuments', 'call_function' : 'addDocuments', 'title' : 'Add Documents', 'fnsave' : 'saveDocuments', 'button' : 'close'}";
        $docButton = '<a class="btn btn-primary permission-modify" href="javascript:void(0)" datatip="Documents" flow="down" onclick="modalAction('.$docParam.');"><i class="mdi mdi-file-document"></i></a>';
            
        $ledgerParam = "{'postData':{'id' : ".$data->id."},'fnsave':'createLedger','message':'Are you sure want to Create this Ledger?'}";
        $ledgerBtn = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Create Ledger" flow="down" onclick="confirmStore('.$ledgerParam.');"><i class="mdi mdi-finance""></i></a>';
		
		$salaryParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'addEmpSalary', 'call_function' : 'addEmpSalary', 'title' : 'Add Employee Salary', 'fnsave' : 'saveEmpSalary'}";
        $salaryButton = '<a class="btn btn-success permission-modify" href="javascript:void(0)" datatip="Add Salary" flow="down" onclick="modalAction('.$salaryParam.');"><i class="mdi mdi-cash"></i></a>';

	else:
        $activeParam = "{'postData':{'id' : ".$data->id.", 'is_active' : 1},'fnsave':'activeInactive','message':'Are you sure want to Active this Employee?'}";
        $activeButton = '<a class="btn btn-success permission-remove" href="javascript:void(0)" datatip="Active" flow="down" onclick="confirmStore('.$activeParam.');"><i class="fa fa-check"></i></a>';
    endif;
    
    $CI = & get_instance();
    $userRole = $CI->session->userdata('role');

    if(in_array($userRole,[-1,1])):
        $resetParam = "{'postData':{'id' : ".$data->id."},'fnsave':'resetPassword','message':'Are you sure want to Change ".$data->emp_name." Password?'}";
        $resetPsw='<a class="btn btn-danger" href="javascript:void(0)" onclick="confirmStore('.$resetParam.');" datatip="Reset Password" flow="down"><i class="fa fa-key"></i></a>';
    endif;
    
    $action = getActionButton($ledgerBtn.$resetPsw.$activeButton.$docButton.$salaryButton.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->emp_code,$data->emp_name,$data->emp_mobile_no,$data->emp_designation,$data->shift_name,formatDate($data->emp_joining_date),formatDate($data->emp_birthdate)];
}

/* Pending/Self Approved Attendance Table Data */
function getAttendanceData($data){
    // $approveButton = '';$approved_by = '';
    // if(empty($data->approve_by)):
    //     $approveParam = "{'postData':{'id':".$data->id."},'message':'Are you sure want to Approve this Attendance?','fnsave':'approveAttendance'}";
    //     $approveButton = '<a href="javascript:void(0)" class="btn btn-success permission-approve" onclick="confirmStore('.$approveParam.');" datatip="Approve" flow="down"><i class="fa fa-check"></i></a>';
	// else:
	// 	if($data->emp_id != $data->approve_by):
	// 		$approved_by = (!empty($data->approve_name) ? $data->approve_name : ' - ');
	// 	endif;
    // endif;
    $editButton = $deleteButton = "";
    if($data->punch_type == 2){
        $deleteParam = "{'postData':{'id' : ".$data->id."},'fndelete':'deleteManualAttendence','message' : 'Manual Attendance'}";
        $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editAttendance', 'title' : 'Update Manual Attendance','call_function':'editManualAttendence'}";

        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    }
    $punchType = "";
    if($data->punch_type == 1){
        $punchType = "Device Punch";
    }elseif($data->punch_type == 2){
        $punchType = "Manual Punch";
    }elseif($data->punch_type == 3){
        $punchType = "Extra Hours";
    }elseif($data->punch_type == 4){
        $punchType = "App Punch";
    }else{
        $punchType = "AUTO PUNCH OUT";
    }
	$action = getActionButton($editButton.$deleteButton);
    
    $add = '<p style="max-width: 300px; white-space: break-spaces; font-size: 0.8rem; line-height: inherit; margin-bottom: 0px;"' . 
    (($data->distance > 1) ? ' class="text-danger"' : '') . '>' . $data->loc_add . '</p>';
	$add .= '<small>[Distance : '.$data->distance.' Km.]</small>';

    return [$action,$data->sr_no,$data->emp_code,$data->emp_name,$punchType,formatDate($data->attendance_date),date('H:i:s',strtotime($data->punch_date)),$data->project_name,$data->shift_name,$add]; 
}
/* Designation Table Data */
function getDesignationData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Designation'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editDesignation', 'title' : 'Update Designation','call_function':'edit'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->title,$data->description];
}

/* Leave Table Data */
function getLeaveData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Leave'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editLeave', 'title' : 'Update Leave'}";
	
    $editButton = '';$deleteButton = '';$printBtn='';$approveButton = '';$rejectButton = '';
    if($data->type == 1){
        if($data->status == 0 AND strtotime($data->end_date) >= strtotime(date('Y-m-d'))){
            $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
    
            $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
        }
        $printBtn = '<a class="btn btn-primary btn-edit permission-approve" href="'.base_url('hr/leave/printLeave/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="mdi mdi-file-pdf" ></i></a>';
        
    }else{
        if(($data->status == 1))
        {
            $approveParam = "{'postData':{'id' : ".$data->id.",'status':'2','msg':'Approve'},'modal_id' : 'bs-right-md-modal', 'form_id' : 'addLeaveApprove', 'title' : 'Approve Leave','call_function':'addLeaveApprove','fnsave':'saveApproveLeave','savebtn_text':'Approve'}";
            $approveButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Approve" flow="down" onclick="modalAction('.$approveParam.');"><i class="fa fa-check"></i></a>';

            $rejectParam = "{'postData':{'id':".$data->id.",'status':'3','msg':'Reject'},'message':'Are you sure want to Reject this Leave?','fnsave':'saveApproveLeave'}";
            $rejectButton = '<a href="javascript:void(0)" class="btn btn-dark permission-approve" onclick="confirmStore('.$rejectParam.');" datatip="Reject" flow="down"><i class="fa fa-close"></i></a>';
        }
    }
   
	$action = getActionButton($approveButton.$rejectButton.$editButton.$deleteButton.$printBtn);
    return [$action,$data->sr_no,$data->emp_name,$data->emp_code,$data->leave_type,date('d-m-Y',strtotime($data->start_date)),date('d-m-Y',strtotime($data->end_date)),$data->total_days,$data->leave_reason,$data->status_label]; //03-05-25
}

/* Leave Approve Table Data */
function getLeaveApproveData($data){
	$approveButton='';
    if($data->approval_type == 1)
    {
        if($data->role == -1 OR ($data->status == 0 AND (in_array($data->loginId,explode(',',$data->fla_id)))))
        {
            $approveButton = '<a class="btn btn-success btn-leaveAction permission-modify" href="javascript:void(0)" data-id="'.$data->id.'" data-emp_id="'.$data->emp_id.'" data-type_leave="'.$data->type_leave.'" data-min_date="'.date("Y-m-d",strtotime($data->start_date)).'" data-created_at="'.date("Y-m-d",strtotime($data->created_at)).'" data-status="'.$data->status.'" datatip="Leave Action" flow="down"><i class="mdi mdi-check"></i></a>';
        }
    }
    
	$action = getActionButton($approveButton);
    return [$action,$data->sr_no,$data->emp_name,$data->emp_code,$data->leave_type,formatDate($data->start_date),formatDate($data->end_date),$data->total_days,$data->leave_reason,$data->status_label];
}

/* get Shift Data */
function getShiftData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Shift'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editShift', 'title' : 'Update Shift'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

	$action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->shift_name,$data->shift_start,$data->shift_end,$data->production_hour,$data->lunch_start,$data->lunch_end,$data->late_in,$data->early_out,$data->lunch_grace,$data->late_fine];
}

/* Department Table Data */
function getDepartmentData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Department'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editDepartment', 'title' : 'Update Department','call_function':'edit'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->name,$data->remark];
}
?>