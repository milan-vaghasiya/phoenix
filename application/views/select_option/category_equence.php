<form>
    <div class="col-md-12">
		<h6 style="color:#ff0000;font-size:1rem;"><i>Note : Drag &amp; Drop Row to Change Category Sequence</i></h6>
        <div class="row">
            <table class="table table-bordered" id="laborCategory" width="100%">
				<thead>
					<tr class="thead-dark">
						<th>Sequence</th>
						<th>Category Name</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if(!empty($categoryLists)){
							foreach($categoryLists as $row){
								echo '<tr id="'.$row->id.'">
									<td>'.$row->sequence.'</td>
									<td>'.$row->detail.'</td>
								</tr>';
							}
						}
					?>
				</tbody>
			</table>
        </div>
    </div>
</form>
<script>
	$("#laborCategory tbody").sortable({
        items: 'tr',
        cursor: 'pointer',
        axis: 'y',
        dropOnEmpty: false,
        // helper: fixWidthHelper,
        start: function (e, ui) {
            ui.item.addClass("selected");
        },
        stop: function (e, ui) {
            ui.item.removeClass("selected");
            $(this).find("tr").each(function (index) {
                $(this).find("td").eq(0).html(index+1);
            });
        },
        update: function () 
        {
            var ids='';
            $(this).find("tr").each(function (index) {ids += $(this).attr("id")+",";});
            var lastChar = ids.slice(-1);
            if (lastChar == ',') {ids = ids.slice(0, -1);}
            
            $.ajax({
                url: base_url + 'selectOption/updateCategorySequance',
                type:'post',
                data:{id:ids},
                dataType:'json',
                global:false,
                success:function(data){
					initTable();
				}
            });
        }
    });
</script>