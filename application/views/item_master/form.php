<form enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <input type="hidden" name="item_type" id="item_type" value="<?=(!empty($dataRow->item_type))?$dataRow->item_type:$item_type?>">  
            <input type="hidden" name="item_code" id="item_code" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:""?>">  
            
            <?php
                $itemType = (!empty($dataRow->item_type))?$dataRow->item_type:$item_type;
            ?>
			<!--
            <div class="col-md-12 form-group">
				<label for="item_code">Item Code</label>
				<input type="text" name="item_code" id="item_code" class="form-control" value="<?= (!empty($dataRow->item_code)) ? $dataRow->item_code : ""; ?>" placeholder="Item Code" />
			</div>
			-->
            <div class="col-md-9 form-group">
                    <label for="item_name">Item Name</label>
                    <input type="text" name="item_name" class="form-control req" value="<?=htmlentities((!empty($dataRow->item_name)) ? $dataRow->item_name : "")?>" placeholder="Item Name"/>
                <div class="error item_name"></div>
            </div>
			
            <div class="col-md-3 form-group">
                <label for="make_brand">Make/Brand Name</label>
                <input type="text" name="make_brand" id="make_brand" class="form-control" value="<?=(!empty($dataRow->make_brand))?$dataRow->make_brand:""?>">
            </div>

            <div class="col-md-4 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control basic-select2 req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>     

            <div class="col-md-4 form-group">
                <label for="uom">UOM</label>
                <select name="uom" id="uom" class="form-control basic-select2 req">
                    <option value="0">--</option>
                    <?=getItemUnitListOption($unitData,((!empty($dataRow->uom))?$dataRow->uom:""))?>
                </select>
            </div>
			
			<div class="col-md-4 form-group">
                <label for="gst_per">GST (%)</label>
                <select name="gst_per" id="gst_per" class="form-control basic-select2">
                    <?php
                        foreach($this->gstPer as $per=>$text):
                            $selected = (!empty($dataRow->gst_per) && floatVal($dataRow->gst_per) == $per)?"selected":"";
                            echo '<option value="'.$per.'" '.$selected.'>'.$text.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>   

            <div class="col-md-3 form-group <?=(in_array($itemType,[1,5,10]))?"hidden":""?>">
                <label for="min_qty">Min. Stock Qty</label>
                <input type="text" name="min_qty" class="form-control floatOnly" value="<?= (!empty($dataRow->min_qty)) ? $dataRow->min_qty : "" ?>" />
            </div>

            <div class="col-md-3 form-group <?=(in_array($itemType,[1,5,10]))?"hidden":""?>">
                <label for="max_qty">Max. Stock Qty</label>
                <input type="text" name="max_qty" class="form-control floatOnly" value="<?= (!empty($dataRow->max_qty)) ? $dataRow->max_qty : "" ?>" />
            </div>

            <div class="<?=(in_array($itemType,[1,10]))?"col-md-12":"col-md-12"?> form-group">
                <label for="description">Product Description</label>
                <textarea name="description" id="description" class="form-control" rows="1"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>

            <?php if($itemType == 1): ?>
                <div class="col-md-6 form-group hidden">
                    <label for="item_image1">Product Image</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="form-control custom-file-input" name="item_image" id="item_image" accept=".jpg, .jpeg, .png" />
                        </div>
                    </div>
                    <div class="error item_image"></div>
                </div>
                <div class="col-md-6"></div>

                <?php if(!empty($dataRow->item_image)): ?>
                    <div class="col-md-2 form-group text-center m-t-20">
                        <img src="<?=base_url("assets/uploads/products/".$dataRow->item_image)?>" class="img-zoom" alt="IMG"><br>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    <?php if($itemType == 5): ?> $("#item_code").prop('readonly',true); <?php endif; ?>
    <?php if(empty($dataRow) && $itemType == 5): ?>
        setTimeout(function(){ 
        getItemCode(<?=$itemType?>); },500);
    <?php endif; ?>
});

function getItemCode(itemType){
    $.ajax({
        url : base_url + '/items/getItemCode',
        type : 'post',
        data : {item_type : itemType},
        dataType : 'json'
    }).done(function(response){
        $("#item_code").val(response.item_code).prop('readonly',true);
    });
}
</script>
