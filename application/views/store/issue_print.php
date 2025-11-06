<html>
    <head>
        <title>MATERIAL ISSUE SLIP</title>
        <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url();?>assets/images/favicon.png">
    </head>
    <body>
        <div class="row">
            <div class="col-12">
                <table class="table top-table" style="border-bottom:1px solid #545454;">
                    <tr>
                        <td style="width:25%;"><img src="<?=$logo?>" style="height:40px;"></td>
                        <td class="org_title text-center" style="font-size:1rem;width:50%;">MATERIAL ISSUE SLIP</td>
                        <td style="width:25%;" class="text-right"><span style="font-size:0.8rem;"></td>
                    </tr>
                </table>
         
                <table class="table top-table" style="margin-top:10px;">
                    <tr>
                        <td style="width:70%"></td>
                        <td style="width:30%"><b>NO. : </b><?=$dataRow->issue_number?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><b>SITE : </b><?=$dataRow->project_name?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><b>Date : </b><?=formatDate($dataRow->issue_date)?></td>
                    </tr>
                </table>

                <table class="table top-table" style="margin-top:10px;">
                    <tr class="text-left">
                        <td><b>ISSUE TO : </b><?=$dataRow->issue_to_name?></td>
                    </tr>
					<tr> 
						<td><b>Vendor : </b><?=$dataRow->party_name?></td>
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
                               <td>'.floatval($dataRow->issue_qty).(!empty($dataRow->uom) ? ' <small>('.$dataRow->uom.')</small>' : '').'</td>
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
						<tr>
							<td style="width:50%;" rowspan="3"></td>
							<th colspan="2">For, <?=$companyData->company_name?></th>
						</tr>
						<tr>
                            <td style="width:25%;"></td>
							<td style="width:25%;" class="text-center"><?=$dataRow->emp_name?><br>(<?=formatDate($dataRow->created_at)?>)</td>
						</tr>
						<tr>
                            <td style="width:25%;"></td>
							<td style="width:25%;" class="text-center"><b>ISSUED BY</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:50%;">Issue No. & Date : <?=$dataRow->issue_number.' ['.formatDate($dataRow->issue_date).']'?></td>
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
