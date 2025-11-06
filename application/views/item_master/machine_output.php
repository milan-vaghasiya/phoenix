<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="machine_id" id="machine_id" value="<?=$machine_id?>">
            <div class="col-md-12">
                <div class="error general_error"></div>
                <div class="table table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Category Name</th>
                                <th>Output Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $itemOutput = [];
                                if(!empty($machineOutput)):
                                    $itemOutput = array_reduce($machineOutput, function($itemData, $row) {
                                        $itemData[$row->item_id] = $row;
                                        return $itemData;
                                    }, []);
                                endif;

                                $i=1;
                                foreach($itemList as $row):
                                    $trans_id = (isset($itemOutput[$row->id]))?$itemOutput[$row->id]->id:"";
                                    $qty = (isset($itemOutput[$row->id]))?$itemOutput[$row->id]->output:"";
                                    echo '<tr>
                                        <td>'.$row->item_code.'</td>
                                        <td>'.$row->item_name.'</td>
                                        <td>'.$row->category_name.'</td>
                                        <td>
                                            <input type="hidden" name="itemData['.$i.'][id]" value="'.$trans_id.'">
                                            <input type="hidden" name="itemData['.$i.'][item_id]" value="'.$row->id.'">
                                            <input type="text" name="itemData['.$i.'][output]" class="form-control floatOnly" value="'.$qty.'">
                                        </td>
                                    </tr>';

                                    $i++;
                                endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>