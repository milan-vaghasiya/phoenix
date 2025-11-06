<form>
    <div class="col-md-12">
        <div class="row">

            <div class="error general_error"></div>

            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
                
            <div class="col-md-12 form-group">
                <label for="shift_name">Shift Name</label>
                <input type="text" name="shift_name" class="form-control req" value="<?=(!empty($dataRow->shift_name))?$dataRow->shift_name:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="shift_start">Shift Start Time</label>
                <input type="time" name="shift_start" class="form-control req" value="<?=(!empty($dataRow->shift_start))?$dataRow->shift_start:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="shift_end">Shift End Time</label>
                <input type="time" name="shift_end" class="form-control req" value="<?=(!empty($dataRow->shift_end))?$dataRow->shift_end:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="lunch_start">Lunch Start Time</label>
                <input type="time" name="lunch_start" class="form-control req" value="<?=(!empty($dataRow->lunch_start))?$dataRow->lunch_start:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="lunch_end">Lunch End Time</label>
                <input type="time" name="lunch_end" class="form-control req" value="<?=(!empty($dataRow->lunch_end))?$dataRow->lunch_end:""; ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="late_in">Late In (min)</label>
                <input type="text" name="late_in" class="form-control numericOnly" value="<?=(!empty($dataRow->late_in))?$dataRow->late_in:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="early_out">Early Out (min)</label>
                <input type="text" name="early_out" class="form-control numericOnly" value="<?=(!empty($dataRow->early_out))?$dataRow->early_out:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="lunch_grace">Lunch Grace (min)</label>
                <input type="text" name="lunch_grace" class="form-control numericOnly" value="<?=(!empty($dataRow->lunch_grace))?$dataRow->lunch_grace:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="late_fine">Late/Early Fine</label>
                <input type="text" name="late_fine" class="form-control floatOnly" value="<?=(!empty($dataRow->late_fine))?$dataRow->late_fine:""; ?>" />
            </div>
        </div>
    </div>
</form>