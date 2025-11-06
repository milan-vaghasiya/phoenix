<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
  
            <div class="col-md-6 form-group">
                <label for="emp_name">Employee Name</label>
                <input type="text" name="emp_name" class="form-control text-capitalize req" placeholder="Emp Name" value="<?=(!empty($dataRow->emp_name))?$dataRow->emp_name:""; ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="emp_mobile_no">Mobile No.</label>
                <input type="text" name="emp_mobile_no" class="form-control numericOnly req" placeholder="Phone No." value="<?=(!empty($dataRow->emp_mobile_no))?$dataRow->emp_mobile_no:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="emp_alt_contact">Emergency Contact</label>
                <input type="text" name="emp_alt_contact" class="form-control numericOnly" placeholder="Phone No." value="<?=(!empty($dataRow->emp_alt_contact))?$dataRow->emp_alt_contact:""?>" />
            </div>
			
			<div class="col-md-3 form-group">
                <label for="emp_department">Department</label>
                <select name="emp_department" id="emp_department" class="form-control basic-select2 req">
                    <option value="">Select Department</option>
                    <?php
                    if(!empty($departmentList)):
                        foreach($departmentList as $row):
                            $selected = ((!empty($dataRow->emp_department) && $row->id == $dataRow->emp_department) ? "selected" : "");
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="emp_designation">Designation</label>
                <select name="emp_designation" id="emp_designation" class="form-control basic-select2 req">
                    <option value="">Select Designation</option>
                    <?php
                    if(!empty($designationList)):
                        foreach($designationList as $row):
                            $selected = ((!empty($dataRow->emp_designation) && $row->id == $dataRow->emp_designation) ? "selected" : "");
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->title.'</option>';
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="shift_id">Shift</label>
                <select name="shift_id" id="shift_id" class="form-control basic-select2 ">
                    <option value="">Select Shift</option>
                    <?php
                    if(!empty($shiftList)):
                        foreach($shiftList as $row):
                            $selected = ((!empty($dataRow->shift_id) && $row->id == $dataRow->shift_id) ? "selected" : "");
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->shift_name.'</option>';
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="super_auth_id">Leave Authority</label>
                <select name="super_auth_id[]" id="super_auth_id" class="form-control basic-select2" multiple>
                    <option value="">Select Leave Authority</option>
                    <?php
                    if(!empty($empList)):
                        foreach($empList as $row):
                             if(!empty($dataRow) && ($dataRow->id == $row->id)){
                                continue;
                             }
                             $selected = (!empty($dataRow->super_auth_id) && (in_array($row->id,  explode(',', $dataRow->super_auth_id)))) ? "selected" : "";

                                echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
            
            <div class="col-md-3 form-group">
                <label for="emp_joining_date">Joining Date</label>
                <input type="date" name="emp_joining_date" id="emp_joining_date" class="form-control req" value="<?=(!empty($dataRow->emp_joining_date))?$dataRow->emp_joining_date:date("Y-m-d")?>" max="<?=(!empty($dataRow->emp_joining_date))?$dataRow->emp_joining_date:date("Y-m-d")?>" />
            </div>
                        
            <div class="col-md-3 form-group">
                <label for="emp_birthdate">Date of Birth</label>
                <input type="date" name="emp_birthdate" id="emp_birthdate" class="form-control req" value="<?=(!empty($dataRow->emp_birthdate))?$dataRow->emp_birthdate:date("Y-m-d")?>" max="<?=(!empty($dataRow->emp_birthdate))?$dataRow->emp_birthdate:date("Y-m-d")?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="emp_blood_group">Blood Group</label>
                <input type="text" name="emp_blood_group" class="form-control" value="<?=(!empty($dataRow->emp_blood_group))?$dataRow->emp_blood_group:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="emp_height">Height (FT.INCH)</label>
                <input type="text" name="emp_height" class="form-control" value="<?=(!empty($dataRow->emp_height))?$dataRow->emp_height:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="emp_weight">Weight (KG)</label>
                <input type="text" name="emp_weight" class="form-control" value="<?=(!empty($dataRow->emp_weight))?$dataRow->emp_weight:""?>" />
            </div>
			<div class="col-md-3 form-group">
                <label for="emp_gender">Gender</label>
                <select name="emp_gender" id="emp_gender" class="form-control basic-select2">
                    <option value="Male" <?=(!empty($dataRow) && $dataRow->emp_gender == "Male")?"selected":""?>>Male</option>
                    <option value="Female" <?=(!empty($dataRow) && $dataRow->emp_gender == "Female")?"selected":""?>>Female</option>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="nominee_name">Nominee Name</label>
                <input type="text" name="nominee_name" class="form-control text-capitalize" placeholder="Emp Name" value="<?=(!empty($dataRow->nominee_name))?$dataRow->nominee_name:""; ?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="bank_name">Bank Name</label>
                <input type="text" name="bank_name" id="bank_name" class="form-control" value="<?=(!empty($dataRow->bank_name))?$dataRow->bank_name:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="account_no">Account No</label>
                <input type="text" name="account_no" id="account_no" class="form-control" value="<?=(!empty($dataRow->account_no))?$dataRow->account_no:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="ifsc_code">Ifsc Code</label>
                <input type="text" name="ifsc_code" id="ifsc_code" class="form-control" value="<?=(!empty($dataRow->ifsc_code))?$dataRow->ifsc_code:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="attendance_status">Attendance Status</label>
                <select id="attendance_status" name="attendance_status" class="form-control basic-select2">
                    <option value="1" <?=(!empty($dataRow) && $dataRow->attendance_status == 1)?"selected":""?>>YES</option>
                    <option value="0" <?=(!empty($dataRow) && $dataRow->attendance_status == 0)?"selected":""?>>NO</option>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="week_off">Week Off</label>
                <select name="week_off" id="week_off" class="form-control basic-select2 req">
                    <option value="">Select Week Off</option>
                    <?php
						$weekDays = [];
						$weekDays['Sun'] = 'Sunday';$weekDays['Mon'] = 'Monday';$weekDays['Tue'] = 'Tuesday';$weekDays['Wed'] = 'Wednesday';$weekDays['Thu'] = 'Thursday';
						$weekDays['Fri'] = 'Friday';$weekDays['Sat'] = 'Saturday';
                        foreach($weekDays as $key=>$label):
                            $selected = (!empty($dataRow->week_off) && $key == $dataRow->week_off)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected.'>'.$label.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="emp_address">Address</label>
                <textarea name="emp_address" class="form-control req" placeholder="Address" style="resize:none;" rows="2"><?=(!empty($dataRow->emp_address))?$dataRow->emp_address:""?></textarea>
				<div class="error emp_address"></div>
            </div>
                        
        </div>
    </div>
</form>