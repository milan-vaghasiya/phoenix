<form>
    <div class="col-md-12">
        <div class="row">

            <div class="col-md-4 form-group">
				<label for="project_id">Project List</label>
				<select name="project_id" id="project_id" class="form-control basic-select2 req stockData">
					<option value="" >Select Project</option>
					<?php
						foreach ($projectList as $row) :
							$selected = (!empty($dataRow[0]->project_id) && $dataRow[0]->project_id == $row->id) ? "selected" : "";
							echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->project_name . '</option>';
						endforeach;
                    ?>
                </select>
			</div>

            <div class="col-md-4 form-group">
				<label for="category_id">Item Category</label>
				<select name="category_id" id="category_id" class="form-control basic-select2 req stockData">
					<option value="" >Select Category</option>
					<?php
						foreach ($categoryList as $row) :
							$selected = (!empty($dataRow[0]->category_id) && $dataRow[0]->category_id == $row->id) ? "selected" : "";
							$disabled = (!empty($dataRow[0]->category_id) && $dataRow[0]->category_id != $row->id) ? "disabled" : "";

							echo '<option value="' . $row->id . '" ' . $selected . ' '.$disabled .'>' . $row->category_name . '</option>';
						endforeach;
                    ?>
                </select>
			</div>

        </div>
        <hr>
        <div class="row">
            <div class="error general_error"></div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped jpExcelTable" id="stockLimitTable">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <th style="width:10%;">Item Code</th>
                            <th style="width:30%;">Item Name</th>
                            <th style="width:30%;">Category Name</th>
                            <th style="width:10%;">Unit</th>
                            <th style="width:20%;">Min. Stock</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyData">
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {

    setTimeout(function(){
        stockLimitList('stockLimitTable');
	},5);
    
	 $(document).on('change','.stockData',function(e){
		e.stopImmediatePropagation();e.preventDefault();
        $(".error").html("");
		var valid = 1;
        var category_id = $('#category_id').val();
        var project_id = $('#project_id').val();
		if($("#category_id").val() == ""){$(".category_id").html("category is required.");valid=0;}
		if($("#project_id").val() == ""){$(".project_id").html("Project is required.");valid=0;}

        if(valid){
            $.ajax({
                url : base_url + controller + '/getProductList',
                type : 'post',
                data : { category_id : category_id,project_id:project_id},
                dataType : 'json'
            }).done(function(data){
                if(data.status == 1){
                    $("#stockLimitTable").DataTable().clear().destroy();
                    $('#tbodyData').html('');
                    $('#tbodyData').html(data.tbodyData);
                    stockLimitList('stockLimitTable');
                }
            });
        }else{
            $('#tbodyData').html('');
        }
    });
    
});

function stockLimitList(tableId = "stockLimitTable"){
    var tableOptions = {
        responsive: true,
        "autoWidth" : false,
        'ordering':false,
        "columnDefs": [
            { type: 'natural', targets: 0 },
            { orderable: false, targets: "_all" }, 
            { className: "text-left", targets: [0] }, 
            { className: "text-center", "targets": "_all" } 
        ],
        "paging": false,
        "bInfo": false,
        buttons: {
            dom: { button: { className: "btn btn-outline-dark" } },
            buttons:[  ]
        },
        language: { search: "",searchPlaceholder: "Search...","emptyTable": "No Data available..."}
    };
    var reportTable = $('#'+tableId).DataTable(tableOptions);
    $('.dataTables_filter .form-control-sm').css("width","100%");
    $('.dataTables_filter .form-control-sm').addClass("csearch");
    $('.dataTables_filter .form-control-sm').css("margin-bottom","5px");
    $('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
    $('.dataTables_filter').css("text-align","right");
    return reportTable;
}

