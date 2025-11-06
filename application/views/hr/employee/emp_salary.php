<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($empData->id))?$empData->id:$emp_id; ?>" />
  
            <div class="col-md-4 form-group">
                <label for="pf">PF</label>
                <select id="pf" name="pf" class="form-control basic-select2">
                    <option value="Yes" <?=(!empty($empData->pf) && $empData->pf == "Yes")?"selected":""?>>YES</option>
                    <option value="No" <?=(!empty($empData->pf) && $empData->pf == "No")?"selected":""?>>NO</option>
                </select>
            </div>
            
            <div class="col-md-4 from-group">
                <label for="pf_no">PF No.</label>
                <input type="text" name="pf_no" class="form-control" value="<?= (!(empty($empData->pf_no)) ? $empData->pf_no : "");?>" />
            </div>
            
            <div class="col-md-4 form-group">
                <label for="salary_duration">Salary Duration</label>
                <select id="salary_duration" name="salary_duration" class="form-control basic-select2">
                <option value="Monthly" <?= (!empty($empData->salary_duration) && $empData->salary_duration == "Monthly")? "selected":"";?> >Monthly</option>
                <option value="Hourly" <?= (!empty($empData->salary_duration) && $empData->salary_duration == "Hourly")? "selected":"";?> >Hourly</option>
                <option value="Fixed" <?= (!empty($empData->salary_duration) && $empData->salary_duration == "Fixed")? "selected":"";?> >Fixed</option>
                </select>
            </div>
            
            <div class="col-md-6 form-group">
                <label for="sal_amt" class="salaryAmt">Monthly Salary</label>
                <input type="text" id="sal_amt" name="sal_amt" class="form-control req" value="<?=(!empty($empData->sal_amt)) ? $empData->sal_amt : ""?>"/>
            </div>
            <div class="col-md-6 form-group">
                <label for="day_hours">Day Hours</label>
                <input type="text" id="day_hours" name="day_hours" class="form-control req" value="<?=(!empty($empData->day_hours)) ? $empData->day_hours : ""?>"/>
            </div>
        </div>
    </div>
</form>