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
                <th style="width:33%;" class="fs-18 text-left"></th>
                <th style="width:33%;" class="fs-18 text-center"><?=(($pvData->entry_type == 1)?"Payment Receipt":"Payment Voucher")?></th>
                <th style="width:33%;" class="fs-18 text-right"></th>
            </tr>
        </table>
        
        <table class="table item-list-bb fs-22" style="margin-top:5px;">
            <tr>
                <td>
                    <b>Voucher No. <?=$pvData->trans_number?></b>
                </td>
                <td>
                    <b>Voucher Date. <?=formatDate($pvData->trans_date)?></b>
                </td>
            </tr>
        </table>
        <?php 
        $title = ($pvData->trans_mode == 'CASH')?'Cash':'Bank';
		$refNo = (!empty($pvData->doc_no))?'<b><u>'.$pvData->doc_no.'</u></b>':'________';
		$refDate = (!empty($pvData->doc_date))?'<b><u>'.formatDate($pvData->doc_date).'</u></b>':'________';
		$rno = ($title != 'Cash' && !empty($refNo))?' Ref No.: '.$refNo.' Dt.: '.$refDate:'';
        ?>
        <table class="table item-list-bb fs-22" style="margin-top:5px;">
            <tr>
                <td style="width:100%; vertical-align:top;">
                    Received with thanks from M/s. <b><?=$pvData->party_name?></b><br><br>              
                    The sum of Ruppes : <b><?=numToWordEnglish($pvData->amount)?></b><br><br>
                    By <?= $pvData->trans_mode.$rno ?><br><br>   
                    
                    Ruppes : <b><?= $pvData->amount ?> /-</b><br><br><br>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom:30px;">
                    <b>Note: </b> <?= $pvData->remark ?>
                </td> 
            </tr>
        </table>
    </div>
</div>