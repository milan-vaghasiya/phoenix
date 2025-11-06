<form autocomplete="off">
    <div class="col-md-12">
        <input type="hidden" name="id" value="<?=(!empty($id))?$id:""; ?>" />
        <input type="hidden" name="status" value="<?=(!empty($status))?$status:""; ?>" />
        <div class="row">
			<div class="col-md-12 form-group">
                <label for="leave_amt">Leave Amount</label>
                <select name="leave_amt" id="leave_amt" class="form-control single-select req">
                    <option value="UNPAID">UNPAID</option>
                    <option value="PAID">PAID</option>
                </select>
            </div>
        </div>
	</div>
</form>