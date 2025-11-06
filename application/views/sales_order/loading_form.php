<form data-confirm_message="Are you sure to save this changes?">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=$id?>">

            <div class="col-md-12 form-group">
                <label for="loading_by">Employee Name</label>
                <select name="loading_by" id="loading_by" class="form-control basic-select2 req">
                    <option value="">Select Employee</option>
                    <?=getEmployeeListOption($employeeList)?>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="vehicle_id">Vehicle No.</label>
                <select name="vehicle_id" id="vehicle_id" class="form-control basic-select2">
                    <option value="">Select Vehicle No.</option>
                    <?php
                        foreach($vehicleList as $row):
                            echo '<option value="'.$row->id.'">'.$row->vehicle_no.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
        </div>
    </div>
</form>