<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=$dataRow->id??''?>">
            <input type="hidden" name="entry_type" id="entry_type" value="<?=$dataRow->entry_type??$entry_type?>">
            <input type="hidden" name="trans_prefix" id="trans_prefix" value="<?=$dataRow->trans_prefix??$trans_prefix?>">
            <input type="hidden" name="trans_no" id="trans_no" value="<?=$dataRow->trans_no??$trans_no?>">

            <div class="col-md-4 form-group">
                <label for="trans_number">Vou. No.</label>
                <input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?=$dataRow->trans_number??$trans_number?>" readonly>
            </div>

            <div class="col-md-4 form-group">
                <label for="trans_date">Vou. Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control req" value="<?=$dataRow->trans_date??getFyDate()?>">
            </div>

            <div class="col-md-4 form-group">
                <label for="net_amount">Amount</label>
                <input type="text" name="net_amount" id="net_amount" class="form-control floatOnly req" value="<?=$dataRow->net_amount??''?>">
            </div>

            <div class="col-md-12 form-group">
                <label for="remark">Description</label>
                <textarea name="remark" id="remark" class="form-control req" rows="3" style="height:auto;"><?=$dataRow->remark??''?></textarea>
            </div>
        </div>
    </div>
</form>