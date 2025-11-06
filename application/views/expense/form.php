<form>
    <div class="col-md-12">
        <div class="row">            

            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id) ? $dataRow->id : "")?>" />
            <input type="hidden" name="exp_prefix" id="exp_prefix" value="<?=(!empty($dataRow->exp_prefix)) ? $dataRow->exp_prefix : $exp_prefix?>" />
            <input type="hidden" name="exp_no" id="exp_no" value="<?=(!empty($dataRow->exp_no)) ? $dataRow->exp_no : $exp_no?>" />

            <div class="col-md-6 form-group">
                <label for="exp_number">Expense Number</label>
                <input type="text" name="exp_number" id="exp_number" class="form-control req" value="<?=(!empty($dataRow->exp_number) ? $dataRow->exp_number : $exp_prefix.sprintf("%03d",$exp_no))?>" readOnly />
            </div>

            <div class="col-md-6 form-group">
                <label for="exp_date">Exp Date</label>
                <input type="date" name="exp_date" id="exp_date" class="form-control req" value="<?=(!empty($dataRow->exp_date) ? $dataRow->exp_date : date('Y-m-d'))?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="project_id">Project</label>
                <select name="project_id" id="project_id" class="form-control basic-select2 req">
                    <option value="">Select Project</option>
                    <?php
                        foreach($projectList as $row){
                            $selected = (!empty($dataRow->project_id) && $dataRow->project_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->project_name.'</option>';
                        }
                    ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="exp_by_id">Employee</label>
                <select name="exp_by_id" id="exp_by_id" class="form-control basic-select2 req">
                    <option value="">Select Employee</option>
                    <?php
                        foreach($empList as $row){
                            $selected = (!empty($dataRow->exp_by_id) && $dataRow->exp_by_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
                        }
                    ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="exp_ledger_id">Exp Type</label>
                <select name="exp_ledger_id" id="exp_ledger_id" class="form-control basic-select2 req">
                    <option value="">Select Exp Type</option>
                    <?php
                        foreach($expList as $row){
                            $selected = (!empty($dataRow->exp_ledger_id) && $dataRow->exp_ledger_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';
                        }
                    ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="demand_amount">Demand Amount</label>
                <input type="text" name="demand_amount" id="demand_amount" class="form-control req floatOnly" value="<?=(!empty($dataRow->demand_amount) ? $dataRow->demand_amount : '')?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="proof_file">File Upload</label>
                <div class="input-group">
                    <input type="file" name="proof_file" class="form-control" />
                </div>
            </div>

            <div class="col-md-12 form-group">
                <label for="notes">Description</label>
                <textarea name="notes" id="notes" class="form-control" rows="2"><?=(!empty($dataRow->notes) ? $dataRow->notes : "")?></textarea>
            </div>

        </div>
    </div>
</form>