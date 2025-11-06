<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-4">
                <div class="crm-desk-left">
					<form id="getCompareData">
						<div class="mb-1">
							<div id="qs">
								<select name="enq_number" id="enq_number" class="form-control basic-select2">
									<option value="">Select Item</option>
									<?php
									if (!empty($enqList)) {
										foreach ($enqList as $row) {
											echo '<option value="'.$row->enq_number.'">'.$row->enq_number.'</option>';
										}
									}
									?>
								</select>
							</div>
						</div>
                        <div class="error party_error"></div>
						<div class="cd-body-left" data-simplebar>
							<div class="cd-list" id="compareItemList">
							</div>
						</div>
						<div class="modal-footer">
							<div class="col-md-12">
								<button type="button" class="btn waves-effect waves-light btn-outline-success btn-block compareBtn"><i class="fa fa-check"></i> Compare</button>
							</div>
						</div>
					</form>
                </div>
			</div>
			<div class="col-lg-8">
                <div class="crm-desk-right">
					<div class="table-responsive" id="partyData">

					</div>
                </div>
			</div>
        </div>
    </div>
</div>