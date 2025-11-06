<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
       
            <div class="col-md-4 form-group">
                <label for="trans_date">Indent Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?= (!empty($dataRow->trans_date)) ? $dataRow->trans_date : getFyDate() ?>" />
            </div>

			<div class="col-md-8 form-group">
                <label for="project_id">Project</label>
                <select name="project_id" id="project_id" class="form-control basic-select2 req">
                    <option value="">Select Project</option>
                    <?php
                    if(!empty($projectList)):
                        foreach($projectList as $row):
                            $selected = (!empty($dataRow->project_id) && $dataRow->project_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->project_name.'</option>';
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

			<div class="col-md-12 form-group">
                <label for="item_id">Item </label>
                <select name="item_id" id="item_id" class="form-control basic-select2 req">
                    <option value="">Select Item</option>
                    <?php 
                    if (!empty($itemList)) :
                        foreach ($itemList as $row) :
                            $selected = ((!empty($dataRow->item_id) && $dataRow->item_id == $row->id) ? "selected" : '');
							echo '<option value="'.$row->id.'" '.$selected.' data-uom="'.$row->uom.'">'.(!empty($row->item_code) ? '[ '.$row->item_code.' ] ' : '').$row->item_name.'</option>';//16-05-25
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
            
            <div class="col-md-6 form-group">
                <label for="qty">Qty</label>
                <div class="float-right"><a class="text-primary font-bold " href="javascript:void(0)" id="uom">Unit</a></div><!-- 16-05-25-->
                <input type="text" name="qty" id="qty" class="form-control req" value="<?= (!empty($dataRow->qty) ? $dataRow->qty : '') ?>">
            </div>

            <div class="col-md-6 form-group">
                <label for="delivery_date">Delivery Date</label>
                <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="<?= (!empty($dataRow->delivery_date)) ? $dataRow->delivery_date : getFyDate() ?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" rows="1" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>">
            </div>

            <?php if(empty($dataRow)){ ?>
                <div class="col-md-12 form-group">
                    <button type="button" class="btn btn-outline-success float-right" onclick="AddRow();"><i class="fa fa-plus"></i> Add</button>
                </div>
            <?php } ?>
        </div>

        <?php if(empty($dataRow)){ ?>
            <hr>
            <div class="row">
                <div class="error general_error"></div>
                <div class="table-responsive">
                    <table id="reqTable" class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width:5%">#</th>
                                <th style="width:30%">Item Name</th>
                                <th style="width:20%">Qty</th>
                                <th style="width:20%">Delivery Date</th>
                                <th style="width:15%">Remark</th>
                                <th style="width:10%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="reqBody">                            
                            <tr id="noData">
                                <td class="text-center" colspan="6">No data available in table</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
    </div>
</form>

<script>
    $(document).ready(function(){
        $("#item_id").trigger('change');
        $(document).on('change', '#item_id', function (e) {
            e.stopImmediatePropagation();e.preventDefault();
            var uom = $("#item_id :selected").data('uom') || "";
            $("#uom").html('Unit : '+uom);			

        });
    });
var itemCount=0;
function AddRow(){
    $(".error").html("");
    var valid = 1;

    if ($("#item_id").val() == "") { $(".item_id").html("Item is required."); valid = 0; }
    if ($("#qty").val() == "") { $(".qty").html("Qty is required."); valid = 0; }

    if (valid) {
        var item_id = $("#item_id").val();
        var item_name = $("#item_id :selected").text();
        var qty = $("#qty").val();
        var delivery_date = $("#delivery_date").val();
        var remark = $("#remark").val();

        //Get the reference of the Table's TBODY element.
        $('table#reqTable tr#noData').remove();

        var tblName = "reqTable";
        var tBody = $("#" + tblName + " > TBODY")[0];

        //Add Row.
        row = tBody.insertRow(-1);

        //Add index cell
        var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
        var cell = $(row.insertCell(-1));
        cell.html(countRow);

        cell = $(row.insertCell(-1));
        cell.html(item_name + '<input type="hidden" name="item_data['+ itemCount +'][item_id]" value="' + item_id + '">');

        cell = $(row.insertCell(-1));
        cell.html(qty + '<input type="hidden" name="item_data['+ itemCount +'][qty]" value="' + qty + '">');
        
        cell = $(row.insertCell(-1));
        cell.html(delivery_date + '<input type="hidden" name="item_data['+ itemCount +'][delivery_date]" value="' + delivery_date + '">');
        
        cell = $(row.insertCell(-1));
        cell.html(remark + '<input type="hidden" name="item_data['+ itemCount +'][remark]" value="' + remark + '">');

        //Add Button cell.
        cell = $(row.insertCell(-1));
        var btnRemove = $('<button><i class="mdi mdi-trash-can-outline"></i></button>');
        btnRemove.attr("type", "button");
        btnRemove.attr("onclick", "Remove(this);");
        btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light btn-sm");
        cell.append(btnRemove);
        cell.attr("class", "text-center");
        
        $("#item_id").val("");
        $("#qty").val("");
        $("#remark").val("");
        initSelect2();

        itemCount++;
    }
}

function Remove(button) {
    //Determine the reference of the Row using the Button.
    var row = $(button).closest("TR");
    var table = $("#reqTable")[0];
    table.deleteRow(row[0].rowIndex);

    $('#reqTable tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
    var countTR = $('#reqTable tbody tr:last').index() + 1;

    if(countTR == 0){
        $("#reqBody").html('<tr id="noData"><td colspan="6" align="center">No data available in table</td></tr>');
    }	
};
</script>