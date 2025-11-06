<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" value="<?=$dataRow->id ?? ''?>">

            <div class="col-md-12 form-group">
                <label for="build_type">Build Type</label>
                <input type="text" class="form-control req" id="build_type" name="build_type" value="<?=$dataRow->build_type ?? ''?>">
            </div>

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" class="form-control" id="remark" name="remark" value="<?=$dataRow->remark ?? ''?>">
            </div>
        </div>
    </div>
</form>