<div style="padding:0px 10px;text-align:justify;font-family:Times New Roman;font-size:15px;">
	<table>
		<tr>
			<td>
				<?php if(!empty($letter_head)): ?>
					<img src="<?=$letter_head?>" class="img">
				<?php endif;?>
			</td>
		</tr>
	</table>

	<table class="table top-table-border">
		<tr class="bg-light-grey">
			<th class="text-center fs-20" colspan="4"><h2>Leave Requested Detail</h2></th>
		</tr>
		<tr>
			<th class="text-left" style="width:20%;">Name</th>
			<td style="width:42%;"><?= !empty($leaveData->emp_code) ? '['.$leaveData->emp_code.'] '.$leaveData->emp_name : $leaveData->emp_name;?></td>
			<th class="text-left" style="width:20%;">Date</th>
			<td style="width:18%;"> <?= date("d-m-Y");?></td>
		</tr>
	
		<!-- <tr>
			<th class="text-left" style="width:20%;">Name</th>
			<td style="width:42%;" colspan="3"><?= !empty($leaveData->emp_code) ? '['.$leaveData->emp_code.'] '.$leaveData->emp_name : $leaveData->emp_name;?></td>
		
		</tr> -->
		
		<tr>
			<th class="text-left">Designation</th>
			<td colspan="3"><?= !empty($leaveData->dsg_title) ? $leaveData->dsg_title : "";?></td>
		</tr>
		
		<tr>
			<th class="text-left">Leave Type</th>
			<td style="width:15%;"> <?= !empty($leaveData->leave_type) ? $leaveData->leave_type : "";?></td>
			<th class="text-left">No. Of Days</th>
			<td> <?= !empty($leaveData->total_days) ? $leaveData->total_days : 0;?></td>
		</tr>
		
		<tr>
			<th class="text-left">Duration</th>
			<td colspan="3"> 
				<?= !empty($leaveData->start_date) ? formatDate($leaveData->start_date) : "";?>
				<?= !empty($leaveData->start_section == "F") ? "<small> (Full Day)</small>" : "<small> (Half Day)</small>";?> TO 
				<?= !empty($leaveData->end_date) ? formatDate($leaveData->end_date) : "";?>
				<?= !empty($leaveData->end_section == "F") ? "<small> (Full Day)</small>" : "<small> (Half Day)</small>";?>
			</td>
		</tr>
		
		<tr>
			<td colspan="4" class="text-left" style="width:2%;">
				<b>Reason:</b><br>
				<?= !empty($leaveData->leave_reason) ? $leaveData->leave_reason : "";?>
			</td>
		</tr>
		
		<tr>
			<td colspan="4" class="text-left" style="width:2%;">
				<b>Comments: </b><br>
				<?= !empty($leaveData->auth_notes) ? $leaveData->auth_notes : "";?>
			</td>
		</tr>
	</table>
	<htmlpagefooter name="lastpage">
		<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
			<tr>
				<td style="width:25%;" class="text-center"><?= $leaveData->emp_name;?></td>
				<td style="width:25%;" class="text-center"><?= !empty($leaveData->hod_name) ? $leaveData->hod_name : "";?></td>
				<td style="width:25%;" class="text-center"><?= !empty($leaveData->hr_name) ? $leaveData->hr_name : "";?></td>
			</tr>
			<tr>
				<td style="width:25%;" class="text-center"><b>Applicant</b></td>
				<td style="width:25%;" class="text-center"><b>Authorized By</b></td>
				<td style="width:25%;" class="text-center"><b>HR Department</b></td>
			</tr>
		</table>
		<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
			<tr>
				<td style="width:25%;"></td>
				<td style="width:25%;"></td>
				<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
			</tr>
		</table>
	</htmlpagefooter>
	<sethtmlpagefooter name="lastpage" value="on" />
</div>