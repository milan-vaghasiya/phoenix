<html>
    <head>
        <title>Sales Order</title>
        <!-- Favicon icon -->
        <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url();?>assets/images/favicon.png">
    </head>
    <body>
        <div class="row">
            <div class="col-12">
                <table class="table item-list-bb">
                    <tr>
                        <td style="width:33%;" class="fs-12 text-left text-danger">
                            <b>Dealer's City/Village : <?=$dataRow->ship_to?></b>
                        </td>
                        <th style="width:33%;" class="fs-18 text-center">
                            Sales Order<br><?=$companyData->company_code?>
                        </th>
                        <td style="width:33%;" class="fs-12 text-right text-danger">
                            <b>Ship To : <?=(!empty($dataRow->ship_to))?$dataRow->ship_to:""?></b>
                        </td>
                    </tr>
                </table>
                
                <table class="table item-list-bb fs-22" style="margin-top:5px;">
                    <tr>
                        <td>
                            <b>SO. No. :  <?=$dataRow->trans_number?></b>
                        </td>
                        <td>
                            <b>SO. Date :  <?=formatDate($dataRow->trans_date)?></b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><b>Party Name : <?=$dataRow->party_name?> </b></td>
                    </tr>
                </table>
                
                <table class="table item-list-bb" style="margin-top:10px;">
                    <thead>
                        <tr>
                            <th style="width:40px;">No.</th>
                            <th class="text-left">Item Description</th>
                            <th style="width:90px;">HSN/SAC</th>
                            <th style="width:100px;">Qty</th>
                            <th style="width:50px;">UOM</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i=1;$totalQty = $totalBoxQty = 0;
                            if(!empty($dataRow->itemList)):
                                foreach($dataRow->itemList as $row):
                                    $item_remark=(!empty($row->item_remark))?'<br><small>Remark:.'.$row->item_remark.'</small>':'';
                                    
                                    echo '<tr>';
                                        echo '<td class="text-center">'.$i++.'</td>';
                                        echo '<td>'.$row->item_name." ".$item_remark.'</td>';
                                        echo '<td class="text-center">'.$row->hsn_code.'</td>';
                                        echo '<td class="text-right">'.floatval($row->qty).'</td>';
                                        echo '<td class="text-center">'.$row->unit_name.'</td>';
                                    echo '</tr>';
                                    $totalQty += $row->qty;
                                endforeach;

                                $blankLines = (12 - $i);
                                if($blankLines > 0):
                                    for($j=1;$j<=$blankLines;$j++):
                                        echo '<tr>
                                            <td style="border-top:none;border-bottom:none;">&nbsp;</td>
                                            <td style="border-top:none;border-bottom:none;"></td>
                                            <td style="border-top:none;border-bottom:none;"></td>
                                            <td style="border-top:none;border-bottom:none;"></td>
                                            <td style="border-top:none;border-bottom:none;"></td>
                                        </tr>';
                                    endfor;
                                endif;
                            endif;
                        ?>
                        <tr>
                            <th colspan="3" class="text-right">Total Qty.</th>
                            <th class="text-right"><?=floatval($totalQty)?></th>
                            <th class="text-right"></th>
                        </tr>                                 
                    </tbody>  
                </table>
                
                <htmlpagefooter name="lastpage">
                    <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;">SO No. & Date : <?=$dataRow->trans_number.' ['.formatDate($dataRow->trans_date).']'?></td>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>
                </htmlpagefooter>
				<sethtmlpagefooter name="lastpage" value="on" />                
            </div>
        </div>        
    </body>
</html>
