<html>
    <head>
        <title>Work Order</title>
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
						<th style="width:33%;" class="fs-18 text-center">Work ORDER</th>
						<td style="width:33%;" class="fs-18 text-right"></td>
					</tr>
				</table>               
                
                <table class="table item-list-bb fs-22" style="margin-top:3px;">
					<tr>
						<td rowspan="4">
							<b>M/S. <?=$partyData->party_name?></b><br>
                            <?=(!empty($partyData->party_address) ? $partyData->party_address : '')?><br>
                            <b>Contact No. :</b> <?=$partyData->party_phone?><br>
                            <b>PAN :</b> <?=$partyData->pan_no?><br>
                            <b>GSTIN :</b> <?=$partyData->gstin?>
                        </td>
						<td style="width:20%;"><b>WO No.</b></td>
						<td style="width:30%;"><?=$dataRow->trans_number?></td>
                    </tr>
                    <tr>
                        <td><b>WO Date</b></td>
                        <td><?=formatDate($dataRow->trans_date)?></td>
					</tr>
					<tr>
                        <td><b>Project</b></td>
                        <td><?=$dataRow->project_name;?></td>
					</tr>
                    <tr>
                        <td><b>Address</b></td>
                        <td><?=$dataRow->delivery_address;?></td>
					</tr>
                </table>
                
                <table class="table item-list-bb" style="margin-top:10px;">
					<thead>
						<tr>
							<th style="width:5%;">No.</th>
							<th class="text-left">Item Description</th>
							<th style="width:20%;">Unit</th>
							<th style="width:15%;">Rate</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$i=1;
							if(!empty($dataRow->itemList)){
								foreach($dataRow->itemList as $row){
									echo '<tr>
										<td>'.$i++.'</td>
										<td>'.$row->item_name.'</td>
										<td>'.$row->full_unit_name.'</td>
										<td>'.$row->rate.'</td>
									</tr>';
								}
							}
						?>
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
					?>
				</table>
                
				<htmlpagefooter name="lastpage">
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:50%;" rowspan="4"></td>
							<th colspan="2">For, <?=$companyData->company_name?></th>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center">&nbsp;</td>
							<td style="width:25%;" class="text-center">&nbsp;</td>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center">&nbsp;</td>
							<td style="width:25%;" class="text-center">&nbsp;</td>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"><b>Sign. of JNR Phoenix Infra</b></td>
							<td style="width:25%;" class="text-center"><b>Sign. of SubContractor</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;">WO No. & Date : <?=$dataRow->trans_number.' ['.formatDate($dataRow->trans_date).']'?></td>
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
