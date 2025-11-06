<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
           <input type="hidden" name="party_category" id="party_category" value="<?=(!empty($dataRow->party_category))?$dataRow->party_category:$party_category?>" />	
           <input type="hidden" name="party_type" value="<?=(!empty($dataRow->party_type))?$dataRow->party_type:$party_type?>" />

            <div class="col-md-12 form-group">
                <label for="party_name">Ladger Name</label>
                <input type="text" name="party_name" class="form-control text-capitalize req" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""; ?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="group_code">Group Name</label>
                <select name="group_code" id="group_code" class="form-control basic-select2">
                    <option value="">Select Group Name</option>
                    <?php
                        foreach($groupList as $row):
                            $selected = (!empty($dataRow->group_code) && $row->group_code == $dataRow->group_code)?"selected":"";
                            echo '<option value="'.$row->group_code.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <!-- <div class="col-md-12 form-group">
                <label for="opening_balance">Op. Blanace</label>
                <div class="input-group">
                    <select name="opening_balance_type" id="opening_balance_type" class="form-control" style="width: 20%;">
                        <option value="1">CR</option>
                        <option value="-1">DR</option>
                    </select>
                    <input type="text" id="opening_balance" name="opening_balance" class="form-control floatOnly" value="" style="width: 40%;" />
                </div>
            </div> -->
        </div>
    </div>
</form>