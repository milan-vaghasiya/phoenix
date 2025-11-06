<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
			<input type="hidden" id="trans_prefix" value="<?=(!empty($trans_prefix)) ? $trans_prefix : ""; ?>" />
            <input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($dataRow->trans_no)) ? $dataRow->trans_no : $trans_no ?>" />

            <div class="col-md-4 form-group">
                <label for="entry_type">Entry Type</label>
                <select name="entry_type" id="entry_type" class="form-control basic-select2" data-selected_vou="<?=(!empty($dataRow->entry_type))?$dataRow->entry_type:""?>">
                    <option value="1" <?=(!empty($dataRow->entry_type) && $dataRow->entry_type == 1)?"selected":""?>>Receive</option>
                    <option value="2" <?=(!empty($dataRow->entry_type) && $dataRow->entry_type == 2)?"selected":"" ?>>Paid</option>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="trans_number">Voucher No.</label>
                <input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?= (!empty($dataRow->trans_number)) ? $dataRow->trans_number : $trans_number ?>" readonly />
            </div>

            <div class="col-md-4 form-group">
                <label for="trans_date">Voucher Date</label>
                <input type="date" class="form-control req fyDates" name="trans_date" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
            </div>

            <div class="col-md-6 form-group">
                <label for="opp_acc_id">Party Name</label> 
                <select name="opp_acc_id" id="opp_acc_id" class="form-control basic-select2">
                    <option value="">Select Party</option>
                    <?=getPartyListOption($partyList,((!empty($dataRow->opp_acc_id))?$dataRow->opp_acc_id:0))?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="vou_acc_id">Bank/Cash Account</label>
                <select name="vou_acc_id" id="vou_acc_id" class="form-control basic-select2">
                <option value="">Select Ledger</option>
                    <?=getPartyListOption($ledgerList,((!empty($dataRow->vou_acc_id))?$dataRow->vou_acc_id:0))?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="amount">Amount</label>
                <input type="text" name="amount" id="amount" class="form-control floatOnly req" value="<?= (!empty($dataRow->amount)) ? $dataRow->amount : ""; ?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="trans_mode">Payment Mode</label>
                <select name="trans_mode" id="trans_mode" class="form-control basic-select2" data-selected="<?=(!empty($dataRow->trans_mode)) ? $dataRow->trans_mode:''?>">
                    <option value="">Select Payment Mode</option>
                    <?php
                        foreach($this->paymentMode as $row):
                            $selected = (!empty($dataRow->trans_mode) && $row == $dataRow->trans_mode) ? "selected":"";
                            echo '<option value="'.$row.'" '.$selected.'>'.$row.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="doc_no">Ref. No.</label>
                <input type="text" class="form-control" id="doc_no" name="doc_no" value="<?= (!empty($dataRow->doc_no)) ? $dataRow->doc_no : ""; ?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="doc_date">Ref. Date</label>
                <input type="date" class="form-control" id="doc_date" name="doc_date" max="<?=getFyDate()?>" value="<?= (!empty($dataRow->doc_date)) ? $dataRow->doc_date : getFyDate(); ?>">
            </div>

			<div class="col-md-12 form-group">
                <label for="notes">Note</label>
                <input type="text" name="notes" id="notes" class="form-control" value="<?= (!empty($dataRow->notes)) ? $dataRow->notes : ""; ?>">
            </div>
         <div>
    </div>

</form>
<script>
var old_no = ""; var old_prefix = "";
$(document).ready(function(){    
    old_no = $('#trans_no').val();
	old_prefix = $('#trans_prefix').val(); 
    
	$(document).on("change","#entry_type",function(){       
        var entry_type = $("#entry_type").val();
        var selected_vou = $("#entry_type").data('selected_vou') || 0; console.log(selected_vou);

        $(".entry_type").html("");
        if(entry_type != ''){
            if(selected_vou == entry_type){
                $('#trans_no').val(old_no);
                $('#trans_prefix').val(old_prefix);
                $('#trans_number').val(old_prefix+old_no);
            }else{
                $.ajax({
                    url : base_url + controller + '/getTransNo',
                    type: 'post',
                    data:{entry_type:entry_type},
                    dataType:'json',
                    success:function(res){                    
                        $("#trans_prefix").val(res.data.trans_prefix);
                        $("#trans_no").val(res.data.trans_no);
                        $("#trans_number").val(res.data.trans_number);
                    }
                }); 
            }
        }else{
            $(".entry_type").html("Entry Type is required.");
        }
    });    
});
</script>
