<html>
    <head>
        <title>Purchase Order</title>
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

				<table class="table">
					<tr class="" style="letter-spacing: 2px;font-weight:bold;padding:2px !important; border-bottom:1px solid #000000;">
						<td style="width:33%;" class="fs-18 text-left"></td>
						<th style="width:33%;" class="fs-18 text-center">PURCHASE ORDER</th>
						<td style="width:33%;" class="fs-18 text-right"></td>
					</tr>
				</table>               
                
                <table class="table item-list-bb fs-22" style="margin-top:3px;">
                    <tr >
                        <td style="width:50%;border-bottom:0px;font-size:14px;"><b>M/S. <?=$dataRow->party_name?></b></td>
                        <td style="width:10%;"><b>PO No.</b></td>
                        <td style="width:15%;"><?=$dataRow->trans_number?></td>
                        <td style="width:10%;"><b>PO Date</b></td>
                        <td style="width:15%;"><?=formatDate($dataRow->trans_date)?></td>
                    </tr>
                    <tr>
						<td style="vertical-align:top;border-top:0px;">
                            <?=(!empty($partyData->party_address) ? $partyData->party_address : '')?><br>
                            <b>Contact No. :</b> <?=$partyData->party_phone?><br>
                            <b>GSTIN :</b> <?=$dataRow->gstin?>
                        </td>
                        <td colspan="4" style="vertical-align:top;"><b>SHIP TO</b><br><?=$dataRow->ship_to?></td>
                    </tr>
                </table>
                
                <table class="table item-list-bb" style="margin-top:10px;">
					<thead>
						<tr>
							<th style="width:40px;">No.</th>
							<th class="text-left">Item Description</th>
							<th style="width:75px;">Make</th>
							<th style="width:80px;">Qty</th>
							<th style="width:75px;">Rate</th>
							<!-- <th style="width:60px;">Disc (%)</th> -->
                            <th style="width:60px;">GST <small>(%)</small></th>
							<th style="width:110px;">Taxable Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$i=1;$totalQty = 0;
							if(!empty($dataRow->itemList)):
								foreach($dataRow->itemList as $row):
									$indent = (!empty($row->ref_id) OR !empty($row->req_id)) ? '<br>Reference No:'.$row->ref_number : '';
									$delivery_date = (!empty($row->delivery_date)) ? '<br>Delivery Date :'.formatDate($row->delivery_date) : '';
									
									$notes = '';$notesRSPN = 1;
									if(!empty($row->item_remark)){$notes = '<tr><td colspan="5"><i>Notes:</i> '.$row->item_remark.'</td></tr>';$notesRSPN = 2;}
									
									echo '<tr>';
										echo '<td class="text-center" rowspan="'.$notesRSPN.'">'.$i++.'</td>';
										echo '<td>'.$row->item_name.$indent.$delivery_date.'</td>';
										echo '<td class="text-center">'.$row->make_brand.'</td>';
										echo '<td class="text-right">'.sprintf('%0.2f',$row->qty).' <small>('.$row->uom.')</small></td>';
										echo '<td class="text-right">'.$row->price.'</td>';
										// echo '<td class="text-center">'.$row->disc_per.'</td>';
										echo '<td class="text-center">'.$row->gst_per.'</td>';
										echo '<td rowspan="'.$notesRSPN.'" class="text-right">'.moneyFormatIndia($row->taxable_amount).'</td>';
									echo '</tr>';
									echo $notes;
									
									$totalQty += $row->qty;
								endforeach;
							endif;
						?>
						<tr>
							<th colspan="3" class="text-right">Total Qty.</th>
							<th class="text-right"><?=sprintf('%.3f',$totalQty)?></th>
							<!-- <th class="text-right"></th> -->
							<th colspan="2" class="text-right">Sub Total</th>
							<th class="text-right"><?=moneyFormatIndia($dataRow->taxable_amount)?></th>
						</tr>
						<?php
						$rwspan = ($dataRow->party_state_code == 24)?2:1;
						?>
						<tr>
							<td class="text-left" style="vertical-align:top;" colspan="4" rowspan="<?=$rwspan+1?>">
								<b>Note: </b> <?= $dataRow->remark?>
							</td>
							<?php
							if($dataRow->party_state_code == 24){
								?>
								<th colspan="2" class="text-right">SGST</th>
								<td class="text-right"><?=moneyFormatIndia(array_sum(array_column($dataRow->itemList,'sgst_amount')))?></td>
								<?php
							}else{
								?>
								<th colspan="2" class="text-right">IGST</th>
								<td class="text-right"><?=moneyFormatIndia(array_sum(array_column($dataRow->itemList,'igst_amount')))?></td>
								<?php
							}
							?>
						</tr>
						<?php
						if($dataRow->party_state_code == 24){
							?>
							<tr>
								<th colspan="2" class="text-right">CGST</th>
								<td class="text-right"><?=moneyFormatIndia(array_sum(array_column($dataRow->itemList,'cgst_amount')))?></td>
							</tr>
							<?php
						}
						$netAmount = array_sum(array_column($dataRow->itemList,'net_amount'));
						$netArr = (!empty($netAmount) ? explode(".",$netAmount) : 0);
						$decimal = (!empty($netArr[1]) ? $netArr[1] : 0);
						$roundOff = 0;
						$rnetAmount = $netAmount;
						if ($decimal !== 0) {
							if ($decimal >= 50) {
								$roundOff = (100 - $decimal) / 100;
								$rnetAmount = $netAmount + $roundOff;
							} else {
								$roundOff = ($decimal - ($decimal * 2)) / 100;
								$rnetAmount = $netAmount + $roundOff;
							}
						}
						?>
						<tr>
							<th colspan="2" class="text-right">Net Amount</th>
							<th class="text-right"><?=moneyFormatIndia($netAmount)?></th>
						</tr>
						<tr>
							<td class="text-left" colspan="4" rowspan = "2">
								<b>Amount In Words:</b> <br><?=numToWordEnglish(sprintf('%.2f',$rnetAmount))?>
							</td>
							<th colspan="2" class="text-right">Round Off</th>
							<td class="text-right"><?=sprintf('%.2f',$roundOff)?></td>
						</tr>
						<tr>
							<th colspan="2" class="text-right">Grand Total</th>
							<th class="text-right"><?=moneyFormatIndia($rnetAmount)?></th>
						</tr>
					</tbody>
                </table>

				<table class="table top-table" style="margin-top:10px;">
					<tr>
						<th class="text-left">Terms & Conditions :-</th>
					</tr>
					<?php
						if(!empty($dataRow->termsConditions)):
							$terms = $dataRow->termsConditions;
							foreach($terms as $row):
								echo '<tr>';
									echo '<th class="text-left fs-11" style="width:140px;">'.$row->term_title.' : </th>';
									echo '<td class=" fs-11">'.nl2br($row->condition).'</td>';
								echo '</tr>';
							endforeach;
						endif;
						/*
						if(!empty($termsData->condition)):
							echo '<tr>';
								echo '<td class=" fs-10">'.$termsData->condition.'</td>';
							echo '</tr>';
						endif;
						*/
					?>
				</table>
                
				<htmlpagefooter name="lastpage">
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:50%;" rowspan="4"></td>
							<th colspan="2">For, <?=$companyData->company_name?></th>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"><?=$dataRow->prepared_by?><br>(<?=formatDate($dataRow->created_at)?>)</td>
							<td style="width:25%;" class="text-center"><?=(!empty($dataRow->approved_by) ? $dataRow->approved_by : '')?><br><?=(!empty($dataRow->approve_date) ? '('.formatDate($dataRow->approve_date).')' : '')?></td>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"><b>Prepared By</b></td>
							<td style="width:25%;" class="text-center"><b>Authorised By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;">PO No. & Date : <?=$dataRow->trans_number.' ['.formatDate($dataRow->trans_date).']'?></td>
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
