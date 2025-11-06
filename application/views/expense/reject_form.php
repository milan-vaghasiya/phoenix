<form>
    <div class="col-md-12">
        <div class="row">            

            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id) ? $dataRow->id : "")?>" />
            <input type="hidden" name="status" id="status" value="2" />

            <div class="col-md-12 form-group">
                <label for="rej_reason">Reject Reason</label>
                <textarea name="rej_reason" id="rej_reason" class="form-control req" rows="2"><?=(!empty($dataRow->rej_reason) ? $dataRow->rej_reason : "")?></textarea>
            </div>

        </div>
    </div>
</form>