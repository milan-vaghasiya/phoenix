<form data-res_function="resSaveSettlement">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=$dataRow->id?>">
            <input type="hidden" name="vou_name_s" id="vou_name_s" value="<?=$dataRow->vou_name_s?>">
            <input type="hidden" id="cm_id" value="<?=$dataRow->cm_id?>">
            <input type="hidden" id="acc_id" value="<?=$dataRow->opp_acc_id?>">

            <div class="col-md-2 form-group">
                <label for="memo_type">Quarter</label>
                <input type="text" id="memo_type" class="form-control" value="<?=$dataRow->memo_type?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="trans_no">CHL. No.</label>
                <input type="text" id="trans_no" class="form-control" value="<?=$dataRow->trans_no?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="trans_date">CHL. Date</label>
                <input type="text" id="trans_date" class="form-control" value="<?=formatDate($dataRow->trans_date)?>" readonly>
            </div>

            <div class="col-md-6 form-group">
                <label for="opp_acc_name">Ledger Name</label>
                <input type="text" id="opp_acc_name" class="form-control" value="<?=$dataRow->opp_acc_name?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="trans_no"><?=($dataRow->vou_name_s == "TCSPmt")?"Collection":"Section"?> Code</label>
                <input type="text" id="order_type" class="form-control" value="<?=$dataRow->order_type?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="net_amount">CHL. Amount</label>
                <input type="text" id="net_amount" class="form-control" value="<?=$dataRow->net_amount?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="from_date">From Date</label>
                <input type="date" id="from_date" class="form-control fyDates" value="<?=getFyDate()?>">
            </div>

            <div class="col-md-4 form-group">
                <label for="to_date">To Date</label>
                <div class="input-group">
                    <input type="date" id="to_date" class="form-control fyDates" value="<?=getFyDate()?>">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-success loadTransaction">Load</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 form-group">
                <b>Unsettled Transaction : </b>
            </div>
            <div class="col-md-12 form-group">
                <div class="error item_error"></div>
                <div class="table table-responsive">
                    <table id="unsettledTrans" class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center">
                                    <input type="checkbox" id="master_checkbox" class="filled-in chk-col-success masterCheck" value="">
                                    <label for="master_checkbox">#</label>
                                </th>
                                <th>Vou. No.</th>
                                <th>Vou. Date</th>
                                <th>Party Name</th>
                                <th><?=($dataRow->vou_name_s == "TCSPmt")?"TCS Amount":"TDS Amount"?></th>
                                <th>Settled Amount</th>
                                <th>Pending Amount</th>
                            </tr>
                        </thead>
                        <tbody id="unsettledTransData">
                            <tr>
                                <td class="text-center" colspan="7">No data available in table</td>
                            </tr>
                        </tbody>
                        <tfoot class="thead-dark">
                            <tr>
                                <th colspan="6" class="text-right">Total</th>
                                <th id="settlementTotal">0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>

<hr>

<div class="col-md-12">
    <div class="row">
        <div class="col-md-12 form-group">
            <b>Settled Transaction : </b>
        </div>

        <div class="col-md-12 form-group">
            <div class="table table-responsive">
                <table id="settledTrans" class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Vou. No.</th>
                            <th>Vou. Date</th>
                            <th>Party Name</th>
                            <th><?=($dataRow->vou_name_s == "TCSPmt")?"TCS Amount":"TDS Amount"?></th>
                            <th>Settled Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="settledTransData">
                        <tr>
                            <td class="text-center" colspan="6">No data available in table</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    setTimeout(function(){
        var postData = {id:$("#id").val(), vou_name_s:$("#vou_name_s").val()};
        getTransHtml({'postData':postData,'controller':'taxPayment','fnget':'getSettledTransaction','table_id':'settledTrans','tbody_id':'settledTransData'});
    },500);

    $(document).on('click','.loadTransaction',function(){
        var valid = 1;

        var vou_name_s = $("#vou_name_s").val();
        var cm_id = $("#cm_id").val();
        var acc_id = $("#acc_id").val();
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();

        if($("#from_date").val() == ""){$(".from_date").html("From Date is required.");valid=0;}
        if($("#to_date").val() == ""){$(".to_date").html("To Date is required.");valid=0;}
        if($("#to_date").val() < $("#from_date").val()){$(".to_date").html("Invalid Date.");valid=0;}

        if(valid){
            var postData = {acc_id:acc_id, vou_name_s:vou_name_s, cm_id:cm_id, from_date:from_date, to_date:to_date};
            getTransHtml({'postData':postData,'controller':'taxPayment','fnget':'getUnsettledTransaction','table_id':'unsettledTrans','tbody_id':'unsettledTransData'});
        }
    });

    $(document).on('click','.masterCheck,.invCheck',function(){
        if($(this).hasClass("masterCheck") == true){
            if($(this).prop('checked') == true){
                $(".invCheck").prop('checked',true);
            }else{
                $(".invCheck").prop('checked',false);
            }
        }else{
            if($(".invCheck").length == $(".invCheck:checked").length){
                $(".masterCheck").prop('checked',true);
            }else{
                $(".masterCheck").prop('checked',false);
            }
        }   
        
        var amountArray = $(".invCheck:checked").map(function () { return $(this).val(); }).get();
        var amountSum = 0;
        $.each(amountArray, function () { amountSum += parseFloat(this) || 0; });
        $("#settlementTotal").html(amountSum.toFixed(3));
    });

    /* $(document).on('click','.invCheck',function(){
        var amountArray = $(".invCheck:checked").map(function () { return $(this).val(); }).get();
        var amountSum = 0;
        $.each(amountArray, function () { amountSum += parseFloat(this) || 0; });
        $("#settlementTotal").html(amountSum.toFixed(3));
    }); */
});

function resSaveSettlement(data,formId){
    if(data.status==1){
        initTable(); 
        Swal.fire({ icon: 'success', title: data.message});
        
        $(".loadTransaction").trigger('click');

        var postData = {id:$("#id").val(), vou_name_s:$("#vou_name_s").val()};
        getTransHtml({'postData':postData,'controller':'taxPayment','fnget':'getSettledTransaction','table_id':'settledTrans','tbody_id':'settledTransData'});        
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }	
}

function resRemoveSettlement(response){
    if(response.status==1){
        initTable(); 
        Swal.fire( 'Deleted!', response.message, 'success' );
        
        $(".loadTransaction").trigger('click');

        var postData = {id:$("#id").val(), vou_name_s:$("#vou_name_s").val()};
        getTransHtml({'postData':postData,'controller':'taxPayment','fnget':'getSettledTransaction','table_id':'settledTrans','tbody_id':'settledTransData'});
    }else{
        Swal.fire( 'Sorry...!', response.message, 'error' );
    }	
}
</script>