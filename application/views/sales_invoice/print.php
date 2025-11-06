<div class="row">
    <div class="col-12">
        <?php if(!empty($header_footer)): ?>
        <table>
            <tr>
                <td>
                    <?php if(!empty($companyData->print_header)): ?>
                        <img src="<?=base_url($companyData->print_header)?>" class="img">
                    <?php endif;?>
                </td>
            </tr>
        </table>
        <?php endif; ?>
        
        <table class="table bg-light-grey">
            <tr class="" style="letter-spacing: 2px;font-weight:bold;padding:2px !important; border-bottom:1px solid #000000;">
                <th style="width:33%;" class="fs-18 text-left">
                    <?=$invData->memo_type?> MEMO
                </th>
                <th style="width:33%;" class="fs-18 text-center">TAX INVOICE</th>
                <th style="width:33%;" class="fs-18 text-right"><?=$printType?></th>
            </tr>
        </table>
        
        <table class="table item-list-bb fs-22" style="margin-top:5px;">
            <tr>
                <td style="width:60%; vertical-align:top;" rowspan="4">
                    <b>Name & Address of the Receipiant (BILL TO)</b><br>
                    <b><?=$invData->party_name?></b><br>
                    <?php if(!empty($invData->party_id)): ?>
                        <?=(!empty($partyData->party_address) ? $partyData->party_address : '')?><br>                    
                        <b>Mobile No.</b> : <?=$partyData->party_mobile?><br>
                        <b>GSTIN</b> : <?=($invData->gstin != "URP")?$invData->gstin:""?> | <b>STATE</b> : <?=$partyData->state_code ." - ".$partyData->state_name?> | <b>District</b> : <?=$partyData->city_name?> | <b>City/Village</b> : <?=$partyData->village_name." - ".$partyData->party_pincode?>
                    <?php else: ?>
                        <?=(!empty($invData->party_address) ? $invData->party_address : '')?><br>                    
                        <b>Mobile No.</b> : <?=$invData->party_mobile?><br>
                    <?php endif; ?>
                </td>
                <td>
                    <b>Invoice No. : <?=$invData->trans_number?></b>
                </td>
                <td>
                    <b>Date : <?=date('d/m/Y', strtotime($invData->trans_date))?></b>
                </td>
            </tr>
            
            <tr>
                <td style="width:40%;" colspan="2">
                    <b>Vehicle No. </b> : <?=(!empty($invData->vehicle_no))?$invData->vehicle_no:""?>
                </td>
            </tr>

            <tr>
                <td style="width:40%;" colspan="2">
                    <b>SHIP TO</b><br>
                    <?php if(!empty($invData->ship_to_id)): ?>
                        <?=(!empty($invData->delivery_address) ? $invData->delivery_address : '')?><br>
                        <b>Place of Supply</b> : <?=$invData->delivery_state_code ." - ".$invData->delivery_state_name?> | <b>District</b> : <?=$invData->delivery_city_name?> | <b>City/Village</b> : <?=$invData->ship_to." - ".$invData->delivery_pincode?>
                    <?php else: ?>
                        <?=(!empty($invData->party_address) ? $invData->party_address : '')?><br>                    
                        <b>Mobile No.</b> : <?=$invData->party_mobile?><br>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
		
		<?php if($invData->cm_id == 2){ ?>
			<table class="table item-list-bb fs-22">
				<tr>
					<td><b>TERMS OF DELEVERY :-</b> MTERIAL INSURED IN TATA AIG GENERAL INSURANCE COMPANY LIMITED POLICY NO.6520020552 DATED 02/01/2025</td>
				</tr>
			</table>
		<?php } ?>
        
        <table class="table item-list-bb" style="margin-top:10px;">
            <?php $thead = '<thead>
                    <tr>
                        <th style="width:20px;">No.</th>
                        <th class="text-left">Description of Goods</th>
                        <th style="width:10%;">HSN/SAC</th>
                        <th style="width:50px;">Qty</th>
                        <th style="width:50px;">Unit</th>
                        <th style="width:50px;">Rate Without GST</th>
                        <th style="width:50px;">GST<br><small>(%)</small></th>
                        <th style="width:50px;">Rate With GST</th>
                        <th style="width:90px;">Amount</th>
                    </tr>
                </thead>';
                echo $thead;
            ?>
            <tbody>
                <?php
                    $i=1;$totalBoxQty = $totalQty = 0;$migst=0;$mcgst=0;$msgst=0;
                    $rowCount = 1;$pageCount = 1;
                    $hsnSummary = [];
                    if(!empty($invData->itemList)):
                        foreach($invData->itemList as $row):						
                            echo '<tr>';
                                echo '<td class="text-center">'.$i++.'</td>';
                                echo '<td>'.$row->item_name.'</td>';
                                echo '<td class="text-center">'.$row->hsn_code.'</td>';
                                echo '<td class="text-right">'.floatVal($row->qty).'</td>';
                                echo '<td class="text-center">'.$row->unit_name.'</td>';
                                echo '<td class="text-right">'.floatVal($row->price).'</td>';
                                echo '<td class="text-center">'.floatval($row->gst_per).'</td>';
                                echo '<td class="text-right">'.floatVal($row->org_price).'</td>';
                                echo '<td class="text-right">'.$row->taxable_amount.'</td>';
                            echo '</tr>';                            

                            $hsnKey = $row->hsn_code.intVal($row->gst_per);
                            $hsnSummary[$hsnKey]['hsn_code'] = $row->hsn_code;
                            $hsnSummary[$hsnKey]['cgst_amount'] = (!empty($hsnSummary[$hsnKey]['cgst_amount']))?$hsnSummary[$hsnKey]['cgst_amount']:0;
                            $hsnSummary[$hsnKey]['sgst_amount'] = (!empty($hsnSummary[$hsnKey]['sgst_amount']))?$hsnSummary[$hsnKey]['sgst_amount']:0;
                            $hsnSummary[$hsnKey]['igst_amount'] = (!empty($hsnSummary[$hsnKey]['igst_amount']))?$hsnSummary[$hsnKey]['igst_amount']:0;
                            $hsnSummary[$hsnKey]['taxable_amount'] = (!empty($hsnSummary[$hsnKey]['taxable_amount']))?$hsnSummary[$hsnKey]['taxable_amount']:0;
                            $hsnSummary[$hsnKey]['gst_amount'] = (!empty($hsnSummary[$hsnKey]['gst_amount']))?$hsnSummary[$hsnKey]['gst_amount']:0;
                            if($invData->gst_type == 1):
                                $hsnSummary[$hsnKey]['cgst_per'] = floatval($row->cgst_per);
                                $hsnSummary[$hsnKey]['cgst_amount'] += (floatval($row->cgst_amount) + floatval(round(($row->exp_gst_amount / 2),3)));
                                $hsnSummary[$hsnKey]['sgst_per'] = floatval($row->sgst_per);
                                $hsnSummary[$hsnKey]['sgst_amount'] += (floatval($row->sgst_amount) + floatval(round(($row->exp_gst_amount / 2),3)));
                            elseif($invData->gst_type == 2):
                                $hsnSummary[$hsnKey]['igst_per'] = floatval($row->igst_per);
                                $hsnSummary[$hsnKey]['igst_amount'] += (floatval($row->igst_amount) + floatval($row->exp_gst_amount));
                            endif;
                            $hsnSummary[$hsnKey]['taxable_amount'] += (floatval($row->taxable_amount) + floatval($row->exp_taxable_amount));
                            $hsnSummary[$hsnKey]['gst_amount'] += (floatval($row->gst_amount) + floatval($row->exp_gst_amount));
                            
                            $totalQty += $row->qty;
                            if($row->gst_per > $migst){$migst=$row->gst_per;$mcgst=$row->cgst_per;$msgst=$row->sgst_per;}
                        endforeach;
                    endif;

                    $blankLines = ($maxLinePP - $i);
                    if($blankLines > 0):
                        for($j=0;$j<=$blankLines;$j++):
                            echo '<tr>
                                <td style="border-top:none;border-bottom:none;">&nbsp;</td>
                                <td style="border-top:none;border-bottom:none;"></td>
                                <td style="border-top:none;border-bottom:none;"></td>
                                <td style="border-top:none;border-bottom:none;"></td>
                                <td style="border-top:none;border-bottom:none;"></td>
                                <td style="border-top:none;border-bottom:none;"></td>
                                <td style="border-top:none;border-bottom:none;"></td>
                                <td style="border-top:none;border-bottom:none;"></td>
                                <td style="border-top:none;border-bottom:none;"></td>
                            </tr>';
                        endfor;
                    endif;
                    
                    $rwspan= 0; $srwspan = '';
                    $beforExp = "";
                    $afterExp = "";
                    $invExpenseData = (!empty($invData->expenseData)) ? $invData->expenseData : array();
                    foreach ($expenseList as $row) :
                        $expAmt = 0;
                        $amtFiledName = $row->map_code . "_amount";
                        if (!empty($invExpenseData) && $row->map_code != "roff") :
                            $expAmt = floatVal($invExpenseData->{$amtFiledName});
                        endif;

                        $perText = '';
                        if(!empty($invExpenseData->{$row->map_code.'_per'}) && floatval($invExpenseData->{$row->map_code.'_per'}) > 0):
                            $perText = ' @'.(floatval($invExpenseData->{$row->map_code.'_per'})).'%';
                        endif;

                        if(!empty($expAmt)):
                            if ($row->position == 1) :
                                if($rwspan == 0):
                                    $beforExp .= '<th colspan="2" class="text-right">'.$row->exp_name.$perText.'</th>
                                    <td class="text-right">'.sprintf('%.2f',$expAmt).'</td>';
                                else:
                                    $beforExp .= '<tr>
                                        <th colspan="2" class="text-right">'.$row->exp_name.$perText.'</th>
                                        <td class="text-right">'.sprintf('%.2f',$expAmt).'</td>
                                    </tr>';
                                endif;
                                $rwspan++;
                            endif;
                        endif;
                    endforeach;

                    $taxHtml = '';$totalTaxAmount = 0;
                    foreach ($taxList as $taxRow) :
                        $taxAmt = 0;
                        $taxAmt = floatVal($invData->{$taxRow->map_code.'_amount'});
                        
                        $perText = '';
                        if(!empty($invData->{$taxRow->map_code.'_per'}) && floatval($invData->{$taxRow->map_code.'_per'}) > 0):
                            $perText = ' @'.(floatval($invData->{$taxRow->map_code.'_per'})).'%';
                        endif;

                        if(!empty($taxAmt)):
                            if($rwspan == 0):
                                $taxHtml .= '<th colspan="2" class="text-right">'.$taxRow->name.$perText.'</th>
                                <td class="text-right">'.sprintf('%.2f',$taxAmt).'</td>';
                            else:
                                $taxHtml .= '<tr>
                                    <th colspan="2" class="text-right">'.$taxRow->name.$perText.'</th>
                                    <td class="text-right">'.sprintf('%.2f',$taxAmt).'</td>
                                </tr>';
                            endif;
                            $totalTaxAmount += $taxAmt;
                            $rwspan++;
                        endif;
                    endforeach;

                    foreach ($expenseList as $row) :
                        $expAmt = 0;
                        $amtFiledName = $row->map_code . "_amount";
                        if (!empty($invExpenseData) && $row->map_code != "roff") :
                            $expAmt = floatVal($invExpenseData->{$amtFiledName});
                        endif;

                        $perText = '';
                        if(!empty($invExpenseData->{$row->map_code.'_per'}) && floatval($invExpenseData->{$row->map_code.'_per'}) > 0):
                            $perText = ' @'.(floatval($invExpenseData->{$row->map_code.'_per'})).'%';
                        endif;

                        if(!empty($expAmt)):
                            if ($row->position == 2) :
                                if($rwspan == 0):
                                    $afterExp .= '<th colspan="2" class="text-right">'.$row->exp_name.$perText.'</th>
                                    <td class="text-right">'.sprintf('%.2f',$expAmt).'</td>';
                                else:
                                    $afterExp .= '<tr>
                                        <th colspan="2" class="text-right">'.$row->exp_name.$perText.'</th>
                                        <td class="text-right">'.sprintf('%.2f',$expAmt).'</td>
                                    </tr>';
                                endif;
                                $rwspan++;
                            endif;
                        endif;
                    endforeach;
                    $fixRwSpan = (!empty($rwspan))?3:0;
                ?>
                <tr>
                    <th colspan="3" class="text-right">Total Qty.</th>
                    <th class="text-right"><?=floatval($totalQty)?></th>
                    <th></th>
                    <th></th>
                    <th colspan="2" class="text-right">Sub Total</th>
                    <th class="text-right"><?=sprintf('%.2f',$invData->taxable_amount)?></th>
                </tr>
                <tr>
                    <td class="text-left" colspan="6" rowspan="<?=$rwspan?>">
                        <table class="table item-list-bb text-center">
                            <tr>
                                <th <?=($invData->gst_type != 3)?'rowspan="2"':'';?>>HSN/SAC</th>
                                <th <?=($invData->gst_type != 3)?'rowspan="2"':'';?>>Taxable Value</th>
                                <?php if($invData->gst_type == 1): ?>
                                    <th colspan="2">Central Tax</th>
                                    <th colspan="2">State Tax</th>
                                <?php elseif($invData->gst_type == 2): ?>
                                    <th colspan="2">IGST</th>
                                <?php endif; ?>
                                <th <?=($invData->gst_type != 3)?'rowspan="2"':'';?>>Total Tax Amount</th>
                            </tr>
                            <?php
                                if(($invData->gst_type == 1)):
                                    echo '<tr>
                                        <th>Rate</th><th>Amount</th>
                                        <th>Rate</th><th>Amount</th>
                                    </tr>';
                                elseif($invData->gst_type == 2):
                                    echo '<tr>
                                        <th>Rate</th><th>Amount</th>
                                    </tr>';
                                endif;

                                $totalTaxAmt = $totalCgstAmt = $totalSgstAmt = $totalIgstAmt = $totalGstAmt = 0;
                                foreach($hsnSummary as $row):
                                    $cgstAmt = $sgstAmt = $igstAmt = 0;
                                    echo '<tr>';
                                    echo '<td>'.$row['hsn_code'].'</td>';
                                    echo '<td>'.$row['taxable_amount'].'</td>';
                                    if(($invData->gst_type == 1)):
                                        echo '<td>'.$row['cgst_per'].'%</td>';
                                        echo '<td>'.$row['cgst_amount'].'</td>';
                                        echo '<td>'.$row['sgst_per'].'%</td>';
                                        echo '<td>'.$row['sgst_amount'].'</td>';
                                        $cgstAmt = $row['cgst_amount']; $sgstAmt = $row['sgst_amount'];
                                    elseif($invData->gst_type == 2):
                                        echo '<td>'.$row['igst_per'].'%</td>';
                                        echo '<td>'.$row['igst_amount'].'</td>';
                                        $igstAmt = $row['igst_amount'];
                                    endif;
                                    echo '<td>'.$row['gst_amount'].'</td>';
                                    echo '<tr>';

                                    $totalTaxAmt +=  $row['taxable_amount'];
                                    $totalCgstAmt +=  $cgstAmt;
                                    $totalSgstAmt +=  $sgstAmt;
                                    $totalIgstAmt +=  $igstAmt;
                                    $totalGstAmt +=  $row['gst_amount'];
                                endforeach;
                                
                                echo '<tr>';
                                echo '<th>Total</th>';
                                echo '<th>'.$totalTaxAmt.'</th>';
                                if(($invData->gst_type == 1)):
                                    echo '<th></th>';
                                    echo '<th>'.$totalCgstAmt.'</th>';
                                    echo '<th></th>';
                                    echo '<th>'.$totalSgstAmt.'</th>';
                                elseif($invData->gst_type == 2):
                                    echo '<th></th>';
                                    echo '<th>'.$totalIgstAmt.'</th>';
                                endif;
                                echo '<th>'.$totalGstAmt.'</th>';
                                echo '<tr>';
                            ?>
                        </table>
                    </td>

                    <?php if(empty($rwspan)): ?>
                        <th colspan="2" class="text-right">Round Off</th>
                        <td class="text-right"><?=sprintf('%.2f',$invData->round_off_amount)?></td>
                    <?php endif; ?>
                </tr>
                <?=$beforExp.$taxHtml.$afterExp?>
                <tr>
                    <td class="text-left" colspan="6" rowspan="<?=$fixRwSpan?>">
                        <b>Bill Amount (In Words)</b> : <?=numToWordEnglish(sprintf('%.2f',$invData->net_amount))?>
                    </td>	
                    
                    <?php if(empty($rwspan)): ?>
                        <th colspan="2" class="text-right">Grand Total</th>
                        <th class="text-right"><?=sprintf('%.2f',$invData->net_amount)?></th>
                    <?php else: ?>
                        <th colspan="2" class="text-right">Round Off</th>
                        <td class="text-right"><?=sprintf('%.2f',$invData->round_off_amount)?></td>
                    <?php endif; ?>
                </tr>
                
                <?php if(!empty($rwspan)): ?>
                <tr>
                    <th colspan="2" class="text-right">Grand Total</th>
                    <th class="text-right"><?=sprintf('%.2f',$invData->net_amount)?></th>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if(!empty($invData->e_inv_no)): ?>
        <table class="table item-list-bb" style="margin-top:10px; page-break-inside: avoid;">
            <tr>
                <th colspan="7" class="text-center">E-Invoice Details</th>
            </tr>
            <tr>
                <th>ACK NO.</th>
                <td><?=$invData->e_inv_no?></td>

                <th>ACK Date</th>
                <td><?=date("d-m-Y H:i:s",strtotime($invData->e_inv_date))?></td>

                <th>EWB No.</th>
                <td><?=(!empty($invData->eway_bill_no))?$invData->eway_bill_no:'-'?></td>
                
                <td rowspan="2" class="text-center" style="padding:0px;width:20%;">
                    <img width="100" src="data:image/png;base64,'<?=$invData->e_inv_qr_code?>'">
                </td>
            </tr>
            <tr>
                <th>IRN</th>
                <td colspan="5">
                    <?=$invData->e_inv_irn?>
                </td>
            </tr>
        </table>
        <?php endif; ?>
        
        <!-- <table class="table top-table" style="margin-top:10px;width:92%;">
            <tr>
                <th class="text-left">
                    <h4>Terms & Conditions :-</h4>
                </th>
            </tr>
            <?php
                /* if(!empty($invData->termsConditions)):
                    foreach($invData->termsConditions as $row):
                        echo '<tr>';
                            //echo '<th class="text-left fs-11" style="width:140px;">'.$row->term_title.'</th>';
                            echo '<td class=" fs-11"><ul><li> '.$row->condition.' </li></ul></td>';
                        echo '</tr>';
                    endforeach;
                endif; */
            ?>
        </table> -->

        <!-- <htmlpagefooter name="lastpage"> -->
            <table style="border-top:1px solid #545454;margin-top:10px; page-break-inside: avoid;">
                <tr>
                    <th class="text-left" style="width:40%;">
                        <h4>Terms & Conditions :-</h4>
                    </th>
                    <th colspan="2" style="vertical-align:bottom;text-align:right;font-size:1rem;padding:5px 2px;">
                        For, <?=$companyData->company_name?><br>
                    </th>
                </tr>
                <tr>
                    <td class="fs-14">
                        Subject To <?=strtoupper($companyData->company_city)?> Jurisdiction<br><br>
                        <img src="<?=base_url("assets/images/fssai.png")?>" width="60"><br><br>
                        LIC No. : <?=$companyData->lic_no?>
                    </td>
                    <td height="35"></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-center"><?=$invData->created_name?><br><b>Prepared By</b></td>
                    <td style="vertical-align:bottom;text-align:right;font-size:1rem;padding:5px 2px;"><b>Authorised Signature</b></td>
                </tr>
            </table>
            <!-- <table style="border-top:1px solid #545454;margin-top:1px;">
                <tr>
                    <td class="text-right">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table> -->
        <!-- </htmlpagefooter>
		<sethtmlpagefooter name="lastpage" value="on" show-this-page="2"/> -->
    </div>
</div>