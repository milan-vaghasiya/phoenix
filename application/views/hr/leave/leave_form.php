<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            
            <div class="col-md-12 form-group"><div class="error generalError"></div></div>
           
            <div class="col-md-12 form-group">
                <label for="emp_id">Employee</label>
                <select name="emp_id" id="emp_id" class="form-control basic-select2 req">
                    <option value="">Select Employee</option>
                    <?php
                        foreach($empList as $row):
							$selected = (!empty($dataRow->emp_id) && $row->id == $dataRow->emp_id)?"selected":"";
                            if(in_array($this->userRole,[-1,1])){
                                $emp_name = !empty($row->emp_code) ? '['.$row->emp_code.'] '.$row->emp_name : $row->emp_name; 
							    echo '<option value="'.$row->id.'" '.$selected.'> '.$emp_name.'</option>';
                            }elseif($this->empId == $row->id){
							    $emp_name ="My Self";
							    echo '<option value="'.$row->id.'" '.$selected.'> '.$emp_name.'</option>';
                            }
                        endforeach;
                    ?>
                </select>
            </div>


            <div class="col-md-6 form-group">
                <label for="start_date">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control countTotalDays req" value="<?=(!empty($dataRow->start_date))?formatDate($dataRow->start_date,'Y-m-d'):date("Y-m-d")?>"  />
				
				<!-- <input type="date" name="start_date" id="start_date" class="form-control countTotalDays req" value="<?=(!empty($dataRow->start_date))?formatDate($dataRow->start_date,'Y-m-d'):date("Y-m-d")?>" min="<?=(!empty($dataRow->start_date))?formatDate($dataRow->start_date,'Y-m-d'):date("Y-m-d")?>" /> -->
            </div>
			
			<div class="col-md-6 form-group">
                <label for="start_section">Start Section</label>
                <select name="start_section" id="start_section" class="form-control countTotalDays req basic-select2">
                    <option value="F" <?=(!empty($dataRow->start_section) && $dataRow->start_section == 'F')?"selected":""?>>Full day</option>
                    <option value="H" <?=(!empty($dataRow->start_section) && $dataRow->start_section == 'H')?"selected":""?>>Half Day</option>
                </select>
            </div>
            
            <div class="col-md-6 form-group">
                <label for="end_date">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control countTotalDays req" value="<?=(!empty($dataRow->end_date))?formatDate($dataRow->end_date,'Y-m-d'):date("Y-m-d")?>"  />
            </div>
			
			<div class="col-md-6 form-group">
                <label for="end_section">End Section</label>
                <select name="end_section" id="end_section" class="form-control countTotalDays req basic-select2">
                    <option value="F" <?=(!empty($dataRow->end_section) && $dataRow->end_section == 'F')?"selected":""?>>Full day</option>
                    <option value="H" <?=(!empty($dataRow->end_section) && $dataRow->end_section == 'H')?"selected":""?>>Half Day</option>
                </select>
            </div>
            
            <!-- <div class="col-md-6 form-group">
                <label class="totaldays" for="total_days">Total Days</label>
                <input type="text" name="total_days" id="total_days" class="form-control floatOnly req" value="<?=(!empty($dataRow->total_days))?floatval($dataRow->total_days):1; ?>" <?=(!empty($dataRow->leave_type_id) && $dataRow->leave_type_id == -1)? "":"readOnly"; ?> />
            </div> -->
			<div class="col-md-12 form-group">
                <label for="leave_type">Leave Type</label>
                <select name="leave_type" id="leave_type" class="form-control basic-select2 req">
                    <option value="">Select Leave Type</option>
                    <?php
                        foreach($leaveType as $row):
                            $selected = (!empty($dataRow->leave_type) && $row->id == $dataRow->leave_type)?"selected":"";
                            echo '<option value="'.$row.'" '.$selected.'>'.$row.'</option>';
                        endforeach;
                      
                    ?>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="leave_reason">Reason</label>
                <textarea rows="2" name="leave_reason" class="form-control req" placeholder="Reason" ><?=(!empty($dataRow->leave_reason))?$dataRow->leave_reason:""?></textarea>
            </div>
        </div>
    </div>
</form>
