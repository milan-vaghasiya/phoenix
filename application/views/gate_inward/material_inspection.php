<form>
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Item Name</th>
                            <th>Project</th>
                            <th>Received Qty</th>
                            <th style="width:10%;">Ok Qty</th>
                            <th style="width:10%;">Reject Qty</th>
                            <th style="width:10%;">Short Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i=0;
                            foreach($dataRow as $row):
                                if($row->item_type != 10):
                        ?>

                        <tr>
                            <td><?=++$i?></td>
                            <td><?=(!empty($row->item_code)?"[ ".$row->item_code." ] ":"").$row->item_name?></td>
                            <td><?=$row->project_name?></td>
                            <td><?=floatVal($row->qty)?></td>
                            <td>
                                <input type="hidden" name="itemData[<?=$i?>][id]" value="<?=$row->id?>">
                                <input type="hidden" name="itemData[<?=$i?>][mir_id]" value="<?=$row->mir_id?>">
                                <input type="hidden" name="itemData[<?=$i?>][inspection_date]" value="<?=(!empty($row->inspection_date))?$row->inspection_date:getFyDate()?>">

                                <input type="text" name="itemData[<?=$i?>][ok_qty]" id="ok_qty_<?=$i?>" class="form-control floatOnly" value="<?=(!empty($row->ok_qty))?floatVal($row->ok_qty):""?>">
                            </td>
                            <td>
                                <input type="text" name="itemData[<?=$i?>][reject_qty]" id="rej_qty_<?=$i?>" class="form-control floatOnly" value="<?=(!empty($row->reject_qty))?floatVal($row->reject_qty):""?>">
                            </td>
                            <td>
                                <input type="text" name="itemData[<?=$i?>][short_qty]" id="short_qty_<?=$i?>" class="form-control floatOnly" value="<?=(!empty($row->short_qty))?floatVal($row->short_qty):""?>">
                            </td>
                        </tr>

                        <?php
                                endif;
                            endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>