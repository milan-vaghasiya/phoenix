<form>
    <div class="col-md-12">
        <div class="row">
            
            <input type="hidden" id="trans_date" value="<?=(!empty($dataRow['trans_date']) ? $dataRow['trans_date'] : '')?>">
            <input type="hidden" id="project_id" value="<?=(!empty($dataRow['project_id']) ? $dataRow['project_id'] : '')?>">

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <th style="width:30%">
                                <input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkPdf" value="" checked><label for="masterSelect"> Select ALL</label>
                            </th>
                            <th style="width:70%">Media List</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i=1;
                        if (!empty($mediaList)) {
                            foreach ($mediaList as $row) {
                                echo '<tr class="text-center">
                                    <td>
                                        <input type="checkbox" name="ref_id[]" id="ref_id_'.$i.'" class="filled-in chk-col-success BulkPdf" value="'.$row->id.'" checked><label for="ref_id_'.$i.'"></label>
                                    </td>
                                    <td>
                                        <img src="'.$row->media_file.'" style="width:125px;height:125px;border:1px solid #000;border-radius:10px;">
                                    </td>
                                </tr>';
                                $i++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function() {
    $(document).on('click', '.BulkPdf', function() {
		if ($(this).attr('id') == "masterSelect") {
			if ($(this).prop('checked') == true) {
				$("input[name='ref_id[]']").prop('checked', true);
			} else {
				$("input[name='ref_id[]']").prop('checked', false);
			}
		} else {
			if ($("input[name='ref_id[]']").not(':checked').length != $("input[name='ref_id[]']").length) {
				$("#masterSelect").prop('checked', false);
			} else {
			}

			if ($("input[name='ref_id[]']:checked").length == $("input[name='ref_id[]']").length) {
				$("#masterSelect").prop('checked', true);
			}
			else{$("#masterSelect").prop('checked', false);}
		}
	});
});

function printDPR(){
    var ref_id = [];
    $("input[name='ref_id[]']:checked").each(function() {
        ref_id.push(this.value);
    });
    var ids = ref_id.join("~");
    var trans_date = $("#trans_date").val();
    var project_id = $('#project_id').val();
    var is_pdf = 1;

    var postData = { trans_date:trans_date, project_id:project_id, is_pdf:is_pdf, ids:ids };
    
    var url = base_url + controller + '/getDprReport/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
    window.open(url);
}
</script>