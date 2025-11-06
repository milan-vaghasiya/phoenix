<form>
    <div class="col-md-12">
        <div class="error order_error"></div>
        <div class="row">
            <input type="hidden" name="id" id="id" class="trans_main_id" value="<?=(!empty($orderData->id))?$orderData->id:""?>">
            <input type="hidden" name="trans_prefix" id="trans_prefix" value="<?=(!empty($orderData->trans_prefix))?$orderData->trans_prefix:""?>">
            <input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($orderData->trans_no))?$orderData->trans_no:""?>">
            <input type="hidden" name="trans_number" id="trans_number" value="<?=(!empty($orderData->trans_number))?$orderData->trans_number:""?>">
            <input type="hidden" name="trans_date" id="trans_date" value="<?=(!empty($orderData->trans_date))?$orderData->trans_date:""?>">

            <div class="col-md-12">
                <div class="error item_error"></div>
                <div class="table table-responsive">                                            
                    <?php
                        $groupedCategory = array_reduce($itemList, function($itemData, $row) {
                            $itemData[$row->category_name][] = $row;
                            return $itemData;
                        }, []);

                        $repeatItem = array();
                        if(!empty($dataRow)):
                            foreach($dataRow as $row):
                                $repeatItem[$row->item_id] = $row;
                            endforeach;
                        endif;

                        $i = 1;$categoryHtml = '';$c=1;
                        foreach ($groupedCategory as $category => $item):                           

                            echo '<table class="table table-bordered">
                            <thead class="thead-info">
                                <tr>
                                    <th colspan="4" class="text-center">'.$category.'</th>
                                </tr>
                                <tr>
                                    <th style="width:5%;">#</th>
                                    <th style="width:15%;">Image</th>
                                    <th style="width:40%;">Item Name</th>
                                    <th style="width:10%;">Cartoon Qty</th>
                                </tr>
                            </thead>
                            <tbody>';

                            $j = 1;
                            foreach ($item as $row):
                                if(!empty($row->packing_standard)):
                                    $total_box = (!empty($repeatItem[$row->id]->total_box))?floatval($repeatItem[$row->id]->total_box):"";
                                    $trans_id = (!empty($repeatItem[$row->id]->id) && !empty($orderData))?floatval($repeatItem[$row->id]->id):"";

                                    $productImage = "";
                                    if(!empty($row->item_image)):
                                        $productImage = '<img src="'.base_url("assets/uploads/products/".$row->item_image).'" class="img-zoom" alt="IMG">';
                                    endif;

                                    echo '<tr>
                                        <td>'.$j.'</td>
                                        <td class="text-center">
                                            '.$productImage.'
                                        </td>
                                        <td>
                                            '.$row->item_name.'
                                            <input type="hidden" name="itemData['.$i.'][item_id]" id="item_id_'.$i.'" value="'.$row->id.'">
                                            <input type="hidden" name="itemData['.$i.'][id]" id="id_'.$i.'" value="'.$trans_id.'">
                                        </td>
                                        <td>
                                            <input type="text" name="itemData['.$i.'][total_box]" id="total_box_'.$i.'" class="form-control floatOnly calOrdQty categoryQty'.$row->category_id.'" data-category_id="'.$row->category_id.'" value="'.$total_box.'">
                                        </td>
                                    </tr>';
                                    $j++;$i++;
                                endif;
                            endforeach;

                            echo '</tbody>
                            <tfoot class="thead-info">
                                <tr>
                                    <th colspan="3" class="text-right">Total</th>
                                    <th class="totalCategoryQty'.$row->category_id.'">0</th>
                                </tr>
                            </tfoot></table>';
                            
                            $categoryHtml .= '<tr>
                                <td>'.$c++.'</td>
                                <td>'.$category.'</td>
                                <td class="totalCategoryQty'.$row->category_id.' categoryQty">0</td>
                            </tr>';
                        endforeach;
                    ?>    
                    
                    <table id="categorySummary" class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th colspan="3">Category Summary</th>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Qty.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?=$categoryHtml?>
                        </tbody>
                        <tfoot class="thead-info">
                            <tr>
                                <th colspan="2" class="text-right">Total</th>
                                <th class="totalOrderQty">0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div> 

            <div class="col-md-12 form-group <?=(!empty($this->partyId))?"hidden":""?>">
                <label for="party_id">Customer Name</label>
                <select name="party_id" id="party_id" class="form-control basic-select2 partyDetails partyOptions req" data-res_function="resPartyDetail" data-party_category="1">
                    <option value="">Select Party</option>
                    <?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:$this->partyId))?>
                </select>
            </div>
            
            <div class="col-md-3 form-group">
                <label for="cm_id">Select Unit</label>
                <select name="cm_id" id="cm_id" class="form-control req">
                    <option value="">Select Unit</option>
                    <?=getCompanyListOptions($companyList,((!empty($orderData->cm_id))?$orderData->cm_id:""))?>
                </select>
            </div>

            <div class="col-md-3 form-group hidden">
                <label for="delivery_date">Delivery Date</label>
                <input type="date" name="delivery_date" id="delivery_date" class="form-control" min="<?=date("Y-m-d",strtotime(date("Y-m-d")."+2 Days"))?>" value="<?=(!empty($orderData->delivery_date))?$orderData->delivery_date:date("Y-m-d",strtotime(date("Y-m-d")."+2 Days"))?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="ship_to_id">Ship To</label>
                <select name="ship_to_id" id="ship_to_id" class="form-control basic-select2 req">
                    <option value="">Select Ship To</option>
                    <?php
                        foreach($shipToList as $row):
                            $selected = ($orderData->ship_to_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->ship_to.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($orderData->remark))?$orderData->remark:""?>">
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    setTimeout(function(){ 
        $(".calOrdQty").trigger('change');      
        if($(".trans_main_id").val() == ""){
            <?php if(in_array($this->userRole,[7,11,12])): ?>
                $("#cm_id option[value='3']").remove(); 
            <?php endif; ?>
            $("#party_id").trigger('change');
        }
    },500);

    $(document).on('change keyup','.calOrdQty',function(){
        var category_id = $(this).data('category_id');

        var qtyArray = $(".categoryQty"+category_id).map(function () { return $(this).val(); }).get();
        var qtySum = 0;
        $.each(qtyArray, function () { qtySum += parseFloat(this) || 0; });
        $(".totalCategoryQty"+category_id).html(qtySum.toFixed(2));

        var orderQtyArray = $("#categorySummary .categoryQty").map(function () { return $(this).html(); }).get();
        var orderQtySum = 0;
        $.each(orderQtyArray, function () { orderQtySum += parseFloat(this) || 0; });
        $(".totalOrderQty").html(orderQtySum.toFixed(2));
    });

    setTimeout(function(){$("#trans_date").trigger('change');},100);
	$(document).on('change','#trans_date',function(){
		var inputDate = $("#trans_date").val();
		if (inputDate) {
			var dateObj = new Date(inputDate);
			dateObj.setDate(dateObj.getDate() + 2);

			var resultDate = dateObj.toISOString().split('T')[0];
			$("#delivery_date").attr('min',resultDate);
		}
	});
});

function resPartyDetail(response = ""){
	var shopToOptions = '<option value="">Select Ship To</option>';
    if(response != ""){
        var partyDetail = response.data.partyDetail; 
		
		var shipToList = response.data.shipToDetails;
		$.each(shipToList,function(index,row){  
			shopToOptions += '<option value="'+row.id+'">'+row.ship_to+'</option>';
        });
    }
	$("#ship_to_id").html(shopToOptions);
	initSelect2();
}
</script>