<html>
    <head>
        <title>Sales Order</title>
        <!-- Favicon icon -->
        <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url();?>assets/images/favicon.png">
    </head>
    <body>
        <div class="row">
            <div class="col-12">
                <table>
                    <tr>
                        <td>
                            <?php if(!empty($letter_head)): ?>
                                <img src="<?=$letter_head?>" class="img">
                            <?php endif;?>
                        </td>
                    </tr>
                </table>

                <table class="table bg-light-grey">
                    <tr class="" style="letter-spacing: 2px;font-weight:bold;padding:2px !important; border-bottom:1px solid #000000;">
                        <td style="width:33%;" class="fs-12 text-left text-danger">
                            <!-- GSTIN: <?=$companyData->company_gst_no?> -->
                            <b>Dealer's City/Village : <?=$dataRow->ship_to?></b><br>
                            <b>Company's City/Village : <?=$companyData->company_village_name?></b>
                        </td>
                        <td style="width:33%;" class="fs-18 text-center">Sales Order</td>
                        <td style="width:33%;" class="fs-12 text-right text-danger">
                            <b>Ship To : <?=(!empty($dataRow->ship_to))?$dataRow->ship_to:""?></b>
                        </td>
                    </tr>
                </table>
                
                <table class="table item-list-bb fs-22" style="margin-top:5px;">
                    <tr >
                        <td rowspan="4" style="width:67%;vertical-align:top;">
                            <b>M/S. <?=$dataRow->party_name?></b><br>
                            <?=(!empty($dataRow->delivery_address) ? $dataRow->delivery_address : '')?><br>
                            <b>Kind. Attn. : <?=$dataRow->contact_person?></b> <br>
                            Contact No. : <?=$dataRow->contact_no?><br>
                            Email : <?=$partyData->contact_email?><br><br>
                            GSTIN : <?=$dataRow->gstin?>
                        </td>
                        <td>
                            <b>SO. No.</b>
                        </td>
                        <td>
                            <?=$dataRow->trans_number?>
                        </td>
                    </tr>
                    <tr>
				        <th class="text-left">SO Date</th>
                        <td><?=formatDate($dataRow->trans_date)?></td>
                    </tr>
                    <tr>
                        <th class="text-left">Cust. PO. No.</th>
                        <td><?=$dataRow->doc_no?></td>
                    </tr>
                    <tr>
                        <th class="text-left">Cust. PO. Date</th>
                        <td><?=(!empty($dataRow->doc_date)) ? formatDate($dataRow->doc_date) : ""?></td>
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
		
                <table class="table top-table" style="margin-top:10px;">
                    <tr>
                        <th class="text-left">Terms & Conditions :-</th>
                    </tr>
                    <?php
                        if(!empty($dataRow->termsConditions)):
                            foreach($dataRow->termsConditions as $row):
                                echo '<tr>';
                                    /* echo '<th class="text-left fs-11" style="width:140px;">'.$row->term_title.'</th>'; */
                                    echo '<td class=" fs-11"><ul><li> '.$row->condition.' </li></ul></td>';
                                echo '</tr>';
                            endforeach;
                        endif;
                    ?>
                </table>
                
                <htmlpagefooter name="lastpage">
                    <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                        <tr>
                            <td style="width:50%;"></td>
                            <td style="width:20%;"></td>
                            <th class="text-center">For, <?=$companyData->company_name?></th>
                        </tr>
                        <tr>
                            <td colspan="3" height="50"></td>
                        </tr>
                        <tr>
                            <td><br>This is a computer-generated order.</td>
                            <td class="text-center"><?=$dataRow->created_name?><br>Prepared By</td>
                            <td class="text-center"><br>Authorised By</td>
                        </tr>
                    </table>
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
