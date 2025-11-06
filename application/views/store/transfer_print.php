<html>
    <head>
        <title>OUTGOING MATERIAL GATE PASS</title>
        <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url();?>assets/images/favicon.png">
    </head>
    <body>
        <div class="row">
            <div class="col-12">
                <table class="table top-table" style="border-bottom:1px solid #545454;">
                    <tr>
                        <td style="width:25%;"><img src="<?=$logo?>" style="height:40px;"></td>
                        <td class="org_title text-center" style="font-size:1rem;width:50%;">OUTGOING MATERIAL GATE PASS</td>
                        <td style="width:25%;" class="text-right"><span style="font-size:0.8rem;"></td>
                    </tr>
                </table>

                <table class="table item-list-bb fs-22" style="margin-top:5px;">
                    <tr>
                        <td rowspan="3" style="width:55%;vertical-align:top;">
                            <b>Project : </b><?=(!empty($dataRow->to_project_name) ? $dataRow->to_project_name : '')?><br><br>
                            <b>Agency : </b><?=(!empty($dataRow->agency_name) ? $dataRow->agency_name : '')?><br>
                            <b>Issue To : </b><?=(!empty($dataRow->issued_to) ? $dataRow->issued_to : '')?><br>
                        </td>
				        <th class="text-left" style="width:20%">From Project</th>
                        <td style="width:25%"><?=(!empty($dataRow->from_project_name) ? $dataRow->from_project_name : '')?></td>
                    </tr>
                    <tr>
				        <th class="text-left">Transfer No.</th>
                        <td><?=(!empty($dataRow->trans_number) ? $dataRow->trans_number : '')?></td>
                    </tr>
                    <tr>
                        <th class="text-left">Transfer Date</th>
                        <td><?=(!empty($dataRow->trans_date) ? formatDate($dataRow->trans_date) : '')?></td>
                    </tr>
                </table>
                         
                <table class="table item-list-bb" style="margin-top:10px;">
					<thead>
						<tr class="text-center bg-light">
							<th style="width:40%">ITEM NAME</th>
							<th style="width:20%">QTY.</th>
							<th style="width:40%">REMARK</th>
						</tr>
					</thead>
					<tbody>
						<?php
                        $i = 1;
                        if(!empty($dataRow)):
                            echo '<tr class="text-center">
                               <td>'.(!empty($dataRow->item_code) ? '[ '.$dataRow->item_code.' ] ' : '').$dataRow->item_name.'</td>
                               <td>'.floatval($dataRow->qty).(!empty($dataRow->uom) ? ' <small>('.$dataRow->uom.')</small>' : '').'</td>
                               <td>'.$dataRow->remark.'</td>
                           </tr>';
                        endif;

                        $blankLines = (10 - $i);
                        if($blankLines > 0):
                            for($j=1; $j<=$blankLines; $j++):
                                echo '<tr>
                                    <td style="border-top:none;border-bottom:none;">&nbsp;</td>
                                    <td style="border-top:none;border-bottom:none;"></td>
                                    <td style="border-top:none;border-bottom:none;"></td>
                                </tr>';
                            endfor;
                        endif;

                        echo '<tr>
                            <td style="border-top:none;">&nbsp;</td>
                            <td style="border-top:none;"></td>
                            <td style="border-top:none;"></td>
                        </tr>';
						?>
					</tbody>
                </table>
                
                <htmlpagefooter name="lastpage">
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr class="text-center">
                            <td style="width:33%;"></td>
                            <td></td>
							<th style="width:33%;">For, <?=$companyData->company_name?></th>
						</tr>
						<tr class="text-center">
							<td style="width:33%;"><?=$dataRow->transfer_by?><br>(<?=formatDate($dataRow->created_at)?>)</td>
							<td class="text-center"></td>
							<td style="width:33%;"></td>
						</tr>
						<tr class="text-center">
							<td style="width:33%;"><b>Deliver Sign.</b></td>
                            <td class="text-center"><b>Security Sign.</b></td>
                            <td style="width:33%;"><b>Receiver Sign.</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:60%;">Transfer No. & Date : <?=$dataRow->trans_number.' ['.formatDate($dataRow->trans_date).']'?></td>
							<td style="width:20%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>
                </htmlpagefooter>
				<sethtmlpagefooter name="lastpage" value="on" />
            </div>
        </div>        
    </body>
</html>