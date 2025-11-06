<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <!-- <div class="float-start">
                        <a href="<?= base_url($headData->controller) ?>" class="btn btn-outline-primary active">FG Stock Inward</a>
                        <a href="<?= base_url($headData->controller."/rmStock") ?>" class="btn btn-outline-primary">RM Stock Inward</a>
                    </div> -->
                    
					<div class="float-end">
                        <?php
                            $addParam = "{'postData':{'item_type':'1'},'modal_id' : 'bs-right-md-modal', 'call_function':'addStock', 'form_id' : 'addStock', 'title' : 'Add Stock'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add Stock</button>

                        <?php
                            $importParam = "{'postData':{'item_type':'1'},'modal_id' : 'modal-md', 'call_function':'importStock', 'form_id' : 'importStock', 'button' : 'close', 'title' : 'Import FG Stock'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-primary permission-write float-right" onclick="modalAction(<?=$importParam?>);"><i class="fa fa-plus"></i> Import</button>
					
						<?php
                            $transferParam = "{'postData':{'item_type':'1'},'modal_id' : 'bs-right-md-modal', 'call_function':'stockTransfer', 'fnsave':'saveStockTransfer', 'form_id' : 'stockTransfer', 'title' : 'Add Stock Transfer'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark permission-write float-right press-add-btn" onclick="modalAction(<?=$transferParam?>);"><i class="fa fa-plus"></i> Stock Transfer</button>

                        <a href="<?=base_url($headData->controller.'/stockTransferLog')?>" target="_blank" type="button" class="btn waves-effect waves-light btn-outline-primary permission-write float-right press-add-btn" ><i class="fas fa-list"></i> Stock Transfer Log</a>

					</div>					
                    <h4 class="card-title text-left">FG Stock Inward</h4>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='itemStockTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows/1'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>