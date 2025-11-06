<div class="row">
	<div class="col-12">
		<table class="table">
			<tr>
				<td rowspan="2" style="width:30%;">
					<?php if(!empty($logo)){ echo '<img src="'.$logo.'" class="img" style="width:220px;">'; }?>
				</td>
				<th rowspan="2" style="width:40%;" class="fs-22 text-center text-primary">Daily Progress Report</th>
				<th style="width:30%;" class="fs-20 text-right text-dark"><?= $projectData->project_name?></th>
			</tr>
			<tr><th class="fs-15 text-right text-muted"><?=formatDate($trans_date)?></th></tr>
		</table>
		
		<table class="table item-list-bb" style="margin-top:5px;">
			<tr>
				<th class="text-left" style="width:16%;">CLIENT</th>
				<td><?= $projectData->party_name?></td>
				<th rowspan="3" style="width:20%;">
					<img src="<?=$weather_icon?>" class="img" style="width:35px;margin-bottom:5px;"><br>Cloudy
				</th>
			</tr>
			<tr>
				<th class="text-left">CONSULTANT</th>
				<td><?= $projectData->consultant?></td>
			</tr>
			<tr>
				<th class="text-left">CONTRACTOR</th>
				<td><?= $companyData->company_name?></td>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td valign="top" width="50%" style="padding-left:0px;">
					<table class="table item-list-bb"  width="100%">
						<tr>
							<th colspan="3">Manpower</th>
						</tr>
						<tr class="bg-light text-left">
							<th>S.No.</th>
							<th>Category</th>
							<th>Total</th>
						</tr>
						<tbody>
							<?php 
								$i=1;$total=0; $laborArr=[];$count=0;

								if(!empty($laborAttendanceList)): 
									foreach($laborAttendanceList as $row):
										if($row->totalPresent > 0):
											echo '<tr>
												<td>'.$i++.'</td>
												<td>'.ucfirst($row->labor_cat_name).'</td>
												<td>'.floatval($row->totalPresent).'</td>
											</tr>';
											$total += floatval($row->totalPresent);
										endif;
									endforeach;
									$count = $i;
									
									$blankLines = ((count($machineList) + 1) - $i);
									if($blankLines > 0):
										for($j=1;$j<=$blankLines;$j++):
											echo '<tr>
												<td >&nbsp;</td>
												<td ></td>
												<td ></td>
											</tr>';
										endfor;
									endif;
								else:
									$blankLines = ((count($machineList) + 1) > 0) ? (count($machineList) + 1) : 1;
									for ($j = 1; $j <= $blankLines; $j++):
										echo '<tr>
											<td>&nbsp;</td>
											<td></td>
											<td></td>
										</tr>';
									endfor;
								endif;
							?>
							<tr class="bg-light text-left">
								<th colspan="2">Total</th>
								<th><?=$total?></th>
							</tr>
						</tbody>
					</table>
				</td>
				<!-- Machine Table -->
				<?php 
					//Machine Table
					$machinetable = '<td valign="top" width="50%" style="padding-left:0px;">
						<table class="table item-list-bb">
							<tr>
								<th colspan="3">Machinery</th>
							</tr>
							<tr class="bg-light">
								<th class="text-left">S.No.</th>
								<th class="text-left">Name</th>
								<th class="text-left">Total</th>
							</tr>';
						
					$i=1;$totalMachine=0;
					if(!empty($machineList)):
						foreach($machineList as $row) {
							$machineryStatuseList = $this->siteTrans->getMachineryStatusList(['project_id' => $data['project_id'],'trans_date' => $data['trans_date'],'machine_id' => $row->id,'single_row'=>1]);

							$totalQty = (!empty($machineryStatuseList) && !empty($machineryStatuseList->qty)) ? $machineryStatuseList->qty : '-';

							$machinetable .= '<tr>
								<td>' . $i++ . '</td>
								<td>' . $row->machine_name . '</td>
								<td>' . $totalQty . '</td>
							</tr>';
							$totalMachine += (!empty($machineryStatuseList) && !empty($machineryStatuseList->qty)) ? $machineryStatuseList->qty : 0;
						}
						$blankLines = ($count - $i);
						if($blankLines > 0):
							for($j=1;$j<=$blankLines;$j++):
								$machinetable .= '<tr>
									<td >&nbsp;</td>
									<td ></td>
									<td ></td>
								</tr>';
							endfor;
						endif;
					else:
						$blankLines = ($count > 0) ? $count : 1;
						for ($j = 1; $j <= $blankLines; $j++):
							$machinetable .= '<tr>
								<td>&nbsp;</td>
								<td></td>
								<td></td>
							</tr>';
						endfor;
					endif;
					$machinetable .= '<tr class="bg-light text-left">
							<th colspan="2">Total</th>
							<th>'.$totalMachine.'</th>
						</tr>';
					echo $machinetable.'</table></td>';
				?>
			</tr>
		</table>
		<table class="table item-list-bb"  width="100%">
			<tr>
				<th colspan="5">Material</th>
			</tr>
			<tr class="bg-light">
				<th>Item</th>
				<th>Prev. Stock</th>
				<th>Today Received</th>
				<th>Utilized</th>
				<th>Remain Stock</th>
			</tr>
			<tbody>
				<?php $i=1;
					if(!empty($stockData)):
						foreach($stockData as $stock):
							echo '<tr>
								<td>'.$stock->item_name.'</td>
								<td>'.$stock->preStock.'</td>
								<td>'.$stock->inward.'</td>
								<td>'.$stock->uti.'</td>
								<td>'.$stock->stock.'</td>
							</tr>';
							$i++;
						endforeach;
					else:
						echo '<tr><td colspan="5" class ="text-center">No Data Available</td></tr>';
					endif;
				?>
			</tbody>
		</table>
		
		<h4>PROGRESS STATUS</h4>
		<table class="table item-list-bb fs-22" style="margin-top:3px;">
			<thead>
				<tr class="bg-light">
					<th class="text-center" style="width:7%;">Sr.No.</th>
					<th>Work Description</th>
					<th style="width:15%;">Executed</th>
					<th style="width:15%;">UOM</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$workTbody = "";
					if(!empty($workDetailList)){
						
						$workDetail = array_reduce($workDetailList, function($workDetail, $work) {
							foreach (json_decode($work->work_data) as $work_data) {
								$workDetail[$work_data->tower_name][] = $work_data;
							}
							return $workDetail;
						}, []);
						
						$i=1;
						foreach ($workDetail as $tower_name => $work){
							echo '<tr>
								<th colspan="4" class="text-center bgl_green">'.$tower_name.'</th>
							</tr>';
									
							foreach ($work as $row){
								echo '<tr>
									<td>'.$i++.'</td>
									<td>'.nl2br($row->work_detail).'</td>
									<td>'.(!empty($row->execution) ? $row->execution : "").'</td>
									<td>'.(!empty($row->uom) ? $row->uom : "").'</td>
								</tr>';
							}
						}
					}else{
						echo '<tr><td colspan="4" class ="text-center">No Data Available</td></tr>';
					}
				?>
			</tbody>
		</table>
		
		<h4>PROGRAMME FOR <?=date("l, d F Y",strtotime($trans_date.' +1 Days'))?></h4>
		<table class="table item-list-bb fs-22" style="margin-top:5px;">
			<thead>
				<tr class="bg-light">
					<th class="text-center" style="width:7%;">Sr.No.</th>
					<th>Work Description</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$workTbody = "";
					if(!empty($workPlanList)){
						$workDetail = array_reduce($workPlanList, function($workDetail, $work) {
							foreach (json_decode($work->work_data) as $work_data) {
								$workDetail[$work_data->tower_name][] = $work_data;
							}
							return $workDetail;
						}, []);
						foreach ($workDetail as $tower_name => $work){
										echo '<tr>
												<th colspan="2" class="text-center bgl_green">'.$tower_name.'</th>
											</tr>';
											$i=1;
							foreach ($work as $row){
								echo '<tr>
									<td>'.$i++.'</td>
									<td>'.$row->work_detail.'</td>
								</tr>';
							}
						}
					}else{
						echo '<tr><td colspan="2" class ="text-center">No Data Available</td></tr>';
					}
				?>
		
			</tbody>
		</table>
		<h4>ISSUES AT SITE TO BE ADDRESSED</h4>
		<?php 
			if(!empty($complaintList)):
				$complaindata = json_decode($complaintList->complain_note);
				if (!empty($complaindata)): 
					echo '<ul>';
					foreach ($complaindata as $complaint) {
						echo '<li>' . $complaint->tower_name. ' : ' . $complaint->notes . '</li>';
					}
					echo '</ul>';
				endif;
			else:
				echo '<ul>No Data Available</ul>';
			endif;
		?>
		<h4>OTHER SITE ACTIVITIES</h4>
		<?php 
			if(!empty($extraActivityList)):
				$activitydata = json_decode($extraActivityList->activity);
				if (!empty($activitydata)): 
					echo '<ul>';
					foreach ($activitydata as $activity) {
						echo '<li>' . $activity->tower_name. ' : ' . $activity->notes . '</li>';
					}
					echo '</ul>';
				endif;
				else:
				echo '<ul>No Data Available</ul>';
			endif;
		?>
		
		<htmlpagefooter name="lastpage">
			<table class="table top-table" style="margin-top:0px;border-top:1px solid #545454;">
				<tr>
					<td style="width:50%;" class="text-center"></td>
					<th style="width:50%;" class="text-center"><?= (!empty($laborAttendanceList[0]->emp_name) ? $laborAttendanceList[0]->emp_name : "")?></th>
				</tr>
				<tr>
					<td colspan="2" height="10"></td>
				</tr>
				<tr>
					<td class="text-center"><br></td>
					<td class="text-center"><br>Created By</td>
				</tr>
			</table>
			<table class="table top-table" style="margin-top:0px;border-top:1px solid #545454;">
				<tr>
					<td style="width:25%;"></td>
					<td style="width:25%;"></td>
					<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
				</tr>
			</table>
		</htmlpagefooter>
		<sethtmlpagefooter name="lastpage" value="on" /> 
				
	</div>
</div>