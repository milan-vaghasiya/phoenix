<form data-res_function="resSaveStock">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="error general_error"></div>
                <div class="table table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <th>Location</th>
                            <th>Invoice No.</th>
                            <th>Stock Qty</th>
                            <?php
                                if($action != "view"):
                                    echo '<th style="width:20%;">Received Qty</th>';
                                endif;
                            ?>
                        </thead>
                        <tbody>
                            <?php
                                if(!empty($stockList)):
                                    $i=1;
                                    foreach($stockList as $row):
                                        echo '<tr>';
                                            echo '<td>'.$row->location.'</td>';
                                            echo '<td>'.$row->ref_no.'</td>';
                                            echo '<td>'.floatval($row->total_box_qty).'</td>';
                                            if($action != "view"):
                                                echo '<td>
                                                    <input type="hidden" name="itemData['.$i.'][id]" value="">
                                                    <input type="hidden" name="itemData['.$i.'][unique_id]" value="'.$row->unique_id.'">
                                                    <input type="hidden" name="itemData['.$i.'][location_id]" value="'.$row->location_id.'">
                                                    <input type="hidden" name="itemData['.$i.'][batch_no]" value="'.$row->batch_no.'">
                                                    <input type="hidden" name="itemData['.$i.'][ref_no]" value="'.$row->ref_no.'">
                                                    <input type="hidden" name="itemData['.$i.'][item_id]" value="'.$row->item_id.'">
                                                    <input type="hidden" name="itemData['.$i.'][party_id]" value="'.$row->party_id.'">
                                                    <input type="hidden" name="itemData['.$i.'][cm_id]" value="'.$row->cm_id.'">

                                                    <input type="text" name="itemData['.$i.'][total_box]" class="form-control floatOnly" value="">
                                                    <div class="error total_box_'.$i.'"></div>
                                                </td>';
                                            endif;
                                        echo '</tr>';

                                        $i++;
                                    endforeach;
                                else:
                                    echo '<tr>
                                        <td colspan="4" class="text-center">No data available in table</td>
                                    </tr>';
                                endif;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>