<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=$dataRow->id?>">
            <div class="col-md-2 form-group">
                <label for="trans_number">SO. No.</label>
                <input type="text" id="trans_number" class="form-control" value="<?=$dataRow->trans_number?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="trans_date">SO. Date</label>
                <input type="text" id="trans_date" class="form-control" value="<?=formatDate($dataRow->trans_date)?>" readonly>
            </div>

            <div class="col-md-4 form-group">
                <label for="party_name">Customer Name</label>
                <input type="text" id="party_name" class="form-control" value="<?=$dataRow->party_name?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="ship_to">Ship To</label>
                <input type="text" id="ship_to" class="form-control" value="<?=$dataRow->ship_to?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="to_cm_id">To Unit</label>
                <select name="to_cm_id" id="to_cm_id" class="form-control req">
                    <option value="">Select Unit</option>
                    <?php
                        foreach($companyList as $row):
                            if($dataRow->cm_id != $row->id):
                                echo '<option value="'.$row->id.'">'.$row->company_code.'</option>';
                            endif;
                        endforeach;
                    ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="error itemError"></div>
                <div class="table table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Item Name</th>
                                <th>Cartoon Qty</th>
                                <th>Strip Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($dataRow->itemList as $row):
                                    echo '<tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="trans_id[]" id="md_checkbox_'.$row->id.'" class="filled-in chk-col-success categoryChecked category_'.$row->brand_id.'" data-category_id="'.$row->brand_id.'" value="'.$row->id.'">
                                            <label for="md_checkbox_'.$row->id.'" class="mr-3"></label>
                                        </td>
                                        <td>'.$row->item_name.' '.$row->brand_name.'</td>
                                        <td>'.floatval($row->total_box).'</td>
                                        <td>'.floatval($row->strip_qty).'</td>
                                    </tr>';
                                endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('click','.categoryChecked',function(){
        if($(this).prop("checked") == true){
            var category_id = $(this).data('category_id');
            $(".category_"+category_id).prop('checked',true);
        }
    });
});
</script>