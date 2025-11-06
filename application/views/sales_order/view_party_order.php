<form>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="error item_error"></div>
                <div class="table table-responsive">                                            
                    <?php
                        $groupedCategory = array_reduce($itemList, function($itemData, $row) {
                            $itemData[$row->category_name][] = $row;
                            return $itemData;
                        }, []);

                        $i = 1;
                        foreach ($groupedCategory as $category => $item):
                            echo '<table class="table table-bordered">
                            <thead class="thead-info">
                                <tr>
                                    <th colspan="6" class="text-center">'.$category.'</th>
                                </tr>
                                <tr>
                                    <th style="width:5%;">#</th>
                                    <th style="width:40%;">Item Name</th>
                                    <th style="width:10%;" class="text-center">Cartoon Qty</th>
                                    <th style="width:10%;" class="text-center">Order Qty</th>
                                    <th style="width:10%;" class="text-center">Received Qty</th>
                                    <th style="width:10%;" class="text-center">Pending Qty</th>
                                </tr>
                            </thead>
                            <tbody>';

                            $j = 1;
                            foreach ($item as $row):
                                    echo '<tr>
                                        <td>'.$j.'</td>
                                        <td>
                                            '.$row->item_name.'
                                        </td>
                                        <td class="text-center">
                                            '.floatval($row->total_box).'
                                        </td>
                                        <td class="text-center">
                                            '.floatval($row->strip_qty).'
                                        </td>
                                        <td class="text-center">
                                            '.floatval($row->dispatch_qty).'
                                        </td>
                                        <td class="text-center">
                                            '.floatval($row->pending_qty).'
                                        </td>
                                    </tr>';
                                $j++;$i++;
                            endforeach;

                            echo '</tbody>
                            <tfoot class="thead-info">
                                <tr>
                                    <th colspan="2" class="text-right">Total</th>
                                    <th class="text-center">'.array_sum(array_column($item,'total_box')).'</th>
                                    <th class="text-center">'.array_sum(array_column($item,'strip_qty')).'</th>
                                    <th class="text-center">'.array_sum(array_column($item,'dispatch_qty')).'</th>
                                    <th class="text-center">'.array_sum(array_column($item,'pending_qty')).'</th>
                                </tr>
                            </tfoot>
                            </table>';                            
                        endforeach;
                    ?>  
                    
                    <table class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th colspan="6" class="text-center">Category Summary</th>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th class="text-left">Category</th>
                                <th style="width:10%;" class="text-center">Cartoon Qty.</th>
                                <th style="width:10%;" class="text-center">Order Qty.</th>
                                <th style="width:10%;" class="text-center">Received Qty</th>
                                <th style="width:10%;" class="text-center">Pending Qty</th>
                            </tr>
                        </thead>
                        <tbody>                        
                            <?php
                                $i = 1;$catTotalBox = $catTotalStripQty = $catTotalDispQty = $catTotalPendingQty = 0;
                                foreach($groupedCategory as $category => $item):
                                    $boxQty = array_sum(array_column($item,'total_box'));
                                    $stripQty = array_sum(array_column($item,'strip_qty'));
                                    $dispatchQty = array_sum(array_column($item,'dispatch_qty'));
                                    $pendingQty = array_sum(array_column($item,'pending_qty'));

                                    echo '<tr>
                                        <td class="text-center">'.$i.'</td>
                                        <td>'.$category.'</td>
                                        <td class="text-center">'.$boxQty.'</td>
                                        <td class="text-center">'.$stripQty.'</td>
                                        <td class="text-center">'.$dispatchQty.'</td>
                                        <td class="text-center">'.$pendingQty.'</td>
                                    </tr>';
                                    $i++;

                                    $catTotalBox += $boxQty; 
                                    $catTotalStripQty += $stripQty;
                                    $catTotalDispQty += $dispatchQty;
                                    $catTotalPendingQty += $pendingQty;
                                endforeach;
                            ?>
                        </tbody>
                        <tfoot class="thead-info">
                            <tr>
                                <th colspan="2" class="text-right">Total Qty.</th>
                                <th class="text-center"><?=$catTotalBox?></th>
                                <th class="text-center"><?=$catTotalStripQty?></th>
                                <th class="text-center"><?=$catTotalDispQty?></th>
                                <th class="text-center"><?=$catTotalPendingQty?></th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>            
        </div>
    </div>
</form>