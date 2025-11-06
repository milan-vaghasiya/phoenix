<form>
    <div class="col-md-12">
        <div class="row">            

            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id) ? $dataRow->id : "")?>" />
            <input type="hidden" name="status" id="status" value="1" />

            <div class="col-md-12 form-group">
                <label for="amount">Amount</label>
                <input type="text" name="amount" id="amount" class="form-control req floatOnly" value="<?=(!empty($dataRow->amount) ? $dataRow->amount : '')?>" />
            </div>

        </div>
    </div>
</form>