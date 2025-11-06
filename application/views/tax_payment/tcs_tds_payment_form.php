<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="payment_id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="order_type" id="order_type" value="<?= (!empty($dataRow->order_type)) ? $dataRow->order_type : ""; ?>" />

            <div class="col-md-2 form-group <?=($this->cm_id_count == 1)?"hidden":""?>">
                <label for="cm_id">Select Unit</label>
                <select name="cm_id" id="cm_id" class="form-control" data-selected_cm_id="<?=(!empty($dataRow->cm_id))?$dataRow->cm_id:""?>">
                    <?=getCompanyListOptions($companyList,((!empty($dataRow->cm_id))?$dataRow->cm_id:""))?>
                </select>
            </div>

            <div class="col-md-<?=($this->cm_id_count > 1)?"2":"3"?> form-group">
                <label for="memo_type">Quarter</label>
                <select name="memo_type" id="memo_type" class="form-control basic-select2" data-selected_memo="<?=(!empty($dataRow->memo_type))?$dataRow->memo_type:""?>">
                    <option value="">Select Quarter</option>
                    <?php
                        foreach($quarterList as $key=>$row):
                            $selected = (!empty($dataRow->memo_type) && $dataRow->memo_type == $key)?"selected":"";
                            echo '<option value="'.$key.'" data-start="'.$row['start'].'" data-end="'.$row['end'].'" '.$selected.'>'.$key.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="vou_name_s">Entry Type</label>
                <select name="vou_name_s" id="vou_name_s" class="form-control basic-select2" data-selected_vou="<?=(!empty($dataRow->vou_name_s))?$dataRow->vou_name_s:""?>">
                    <option value="TCSPmt" <?=(!empty($dataRow->vou_name_s) && $dataRow->vou_name_s == "TCSPmt")?"selected":"" ;?> >TCS Payment</option>
                    <option value="TDSPmt" <?=(!empty($dataRow->vou_name_s) && $dataRow->vou_name_s == "TDSPmt")?"selected":"" ;?> >TDS Payment</option>
                </select>
            </div>

            <div class="col-md-<?=($this->cm_id_count > 1)?"2":"3"?> form-group">
                <label for="trans_no">CHL. No.</label>
                <input type="text" name="trans_no" id="trans_no" class="form-control req" value="<?= (!empty($dataRow->trans_no)) ? $dataRow->trans_no : "" ?>" readonly />
            </div>

            <div class="col-md-3 form-group">
                <label for="trans_date">CHL. Date</label>
                <input type="date" class="form-control req fyDates" name="trans_date" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
            </div>

            <div class="col-md-6 form-group">
                <label>Party Name</label> 
                <small class="float-right">Balance : <span  id="opp_acc_balance">0</span></small>
                <select name="opp_acc_id" id="opp_acc_id" class="form-control partyDetails basic-select2 req" data-res_function="resOppAcc">
                    <option value="">Select Party</option>
                    <?=getPartyListOption($partyList,((!empty($dataRow->opp_acc_id))?$dataRow->opp_acc_id:0))?>
                </select>
                <input type="hidden" name="party_name" id="party_name" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""?>">
            </div>

            <div class="col-md-6 form-group">
                <label>Bank/Cash Account</label>
                <small class="float-right">Balance : <span  id="vou_acc_balance">0</span></small>
                <select name="vou_acc_id" id="vou_acc_id" class="form-control partyDetails basic-select2 req" data-res_function="resVouAcc">
                    <option value="">Select Ledger</option>
                    <?=getPartyListOption($ledgerList,((!empty($dataRow->vou_acc_id))?$dataRow->vou_acc_id:0))?>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="trans_number">Bank Vou. No.</label>
                <input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="doc_no">Cheque/DD No.</label>
                <input type="text" name="doc_no" id="doc_no" class="form-control" value="<?=(!empty($dataRow->doc_no))?$dataRow->doc_no:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="ref_by">BRS Code</label>
                <input type="text" name="ref_by" id="ref_by" class="form-control req" value="<?=(!empty($dataRow->ref_by))?$dataRow->ref_by:""?>">
            </div>

            <div class="col-md-3 form-group vouType">
                <label>Amount</label>
                <input type="text" name="net_amount" id="net_amount" class="form-control floatOnly req" value="<?= (!empty($dataRow->net_amount)) ? $dataRow->net_amount : ""; ?>">
            </div>

            <div class="col-md-3 form-group vouType">
                <label>Interest Amount</label>
                <input type="text" name="igst_amount" id="igst_amount" class="form-control floatOnly" value="<?= (!empty($dataRow->igst_amount)) ? $dataRow->igst_amount : ""; ?>">
            </div>

            <div class="col-md-3 form-group vouType">
                <label>Payment Mode</label>
                <select name="payment_mode" id="payment_mode" class="form-control basic-select2" data-selected="<?=(!empty($dataRow->payment_mode)) ? $dataRow->payment_mode:''?>">
                    <option value="">Select Payment Mode</option>
                    <?php
                    foreach($this->paymentMode as $row):
                        if($row != "CASH"):
                                $selected = (!empty($dataRow->payment_mode) && $row == $dataRow->payment_mode) ? "selected":"";
                                echo '<option value="'.$row.'" '.$selected.'>'.$row.'</option>';
                            endif;
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-9 form-group">
                <label for="remark">Note</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : ""; ?>">
            </div>
        </div>
    </div>
</form>
<script>
var old_no = ""; var old_prefix = "";
$(document).ready(function(){
    if($("#payment_id").val() == ""){$("#cm_id").val(($("#company_id :selected").val() || 1));}
	setTimeout(function(){$("#cm_id").trigger('change');},500);
    
    old_no = $('#trans_no').val();

    $(".partyDetails").trigger('change');
	
	$(document).on("change","#memo_type,#vou_name_s,#cm_id",function(){       
        var vou_name_s = $("#vou_name_s").val();
        var cm_id = $("#cm_id").val();
        var memo_type = $("#memo_type").val();
        var start_date = $("#memo_type :selected").data('start');
        var end_date = $("#memo_type :selected").data('end');
        var selected_cm_id = $("#cm_id").data('selected_cm_id') || 0;
        var selected_vou = $("#vou_name_s").data('selected_vou') || 0;
        var selected_memo = $("#memo_type").data('selected_memo') || 0;

        $(".entry_type").html("");
        if(vou_name_s != ''){
            if(selected_vou == vou_name_s && selected_memo == memo_type){
                $('#trans_no').val(old_no);
            }else{
                $.ajax({
                    url : base_url + controller + '/getTransNo',
                    type: 'post',
                    data:{vou_name_s:vou_name_s,cm_id:cm_id,start_date:start_date,end_date:end_date,memo_type:memo_type},
                    dataType:'json',
                    success:function(res){                    
                        $("#trans_no").val(res.trans_no);
                    }
                }); 
            }
        }else{
            $(".vou_name_s").html("Entry Type is required.");
        }

        $.ajax({
            url : base_url + controller + '/getLedgerList',
            type : 'post',
            data : {cm_id:cm_id, vou_acc_id : $("#vou_acc_id :selected").val(), opp_acc_id : $("#opp_acc_id :selected").val(),vou_name_s:vou_name_s},
            dataType : 'json',
        }).done(function(response){
            $("#vou_acc_id").html("");
            $("#vou_acc_id").html(response.vou_acc_list);

            $("#opp_acc_id").html("");
            $("#opp_acc_id").html(response.opp_acc_list);
            initSelect2();
        });
    });
});

function resOppAcc(response=""){
    if(response != ""){
        var partyDetail = response.data.partyDetail;
        $("#party_name").val(partyDetail.party_name);
        $("#opp_acc_balance").html(inrFormat(partyDetail.closing_balance)+' '+partyDetail.closing_type);
    }else{
        $("#party_name").val("");
		$("#opp_acc_balance").html(0);        
    }
    $("#opp_acc_balance").focus();
}

function resVouAcc(response=""){
    if(response != ""){
        var partyDetail = response.data.partyDetail;
        $("#vou_acc_balance").html(inrFormat(partyDetail.closing_balance)+' '+partyDetail.closing_type);
    }else{
		$("#vou_acc_balance").html(0);    
    }
    initSelect2();
}
</script>