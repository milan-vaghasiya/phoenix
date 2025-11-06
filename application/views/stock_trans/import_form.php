<style>
.error hr{
    margin:0px !important;
    background-color: #000000 !important;
    border : 1px;
}
</style>
<form enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12 form-group">
                <label for="ref_date">Date</label>
                <input type="date" name="ref_date" id="ref_date" class="form-control fyDate" value="<?=getFyDate()?>">
            </div>

            <div class="col-md-<?=($this->cm_id_count == 1)?"12":"8"?> form-group">
                <label>Select File</label>
                <input type="hidden" name="item_type" id="item_type" value="<?=$item_type?>">
                <input type="file" name="excel_file" id="excel_file" class="form-control" />                
            </div>    
            
            <div class="col-md-4 form-group <?=($this->cm_id_count == 1)?"hidden":""?>">
                <label for="cm_id">Select Unit</label>
                <select name="cm_id" id="cm_id" class="form-control">
                    <?=getCompanyListOptions($companyList)?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <a href="javascript:void(0);" class="btn btn-labeled btn-info bg-info-dark mr-1 downloadExcel">
                    <i class="fa fa-download"></i> 
                    <span class="btn-label">Download <i class="fa fa-file-excel"></i></span>
                </a>
            </div>

            <div class="col-md-6 form-group text-right">
                <a href="javascript:void(0);" class="btn btn-labeled btn-success bg-success-dark ml-1 importExcel" type="button">
                    <i class="fa fa-upload"></i>
                    <span class="btn-label">Upload <i class="fa fa-file-excel"></i></span>
                </a>                    
            </div>

            <div class="error importError"></div>
            <div class="text-success importSuccess"></div>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
    setTimeout(function(){
        $("#cm_id").val(($("#company_id :selected").val() || 1));
    },200);

    $(document).on('click',".downloadExcel",function(e){
        e.preventDefault();
        e.stopImmediatePropagation();

        var postData = {item_type : '<?=$item_type?>'};
        var url = base_url + controller + '/createExcel/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
		window.open(url);
    });

    $(document).on('click','.importExcel', function(e) {
        e.stopImmediatePropagation();

        $(".importError").html("");
        $(".importSuccess").html("");
        //$(this).prop("disabled",true);
        var form = $('#importStock')[0];
	    var fd = new FormData(form);

        $.ajax({
            url: base_url + controller + '/importExcel',
            data: fd,
            type: "POST",
            dataType: "json",
            contentType: false,
            processData: false
        }).done(function(data) {
            if (data.status === 0) {
                $(".importError").html("");
                $(".importSuccess").html("");
                var error='';
                $.each(data.message, function(key, value) {
                    error+=value+' <hr> ';
                });
                $(".importError").html(error);
                $(".importSuccess").html(data.success_message);
                initTable();
            } else if (data.status == 1) {
                initTable();
                Swal.fire({ icon: 'success', title: data.message});
            }else{
                Swal.fire({ icon: 'error', title: data.message });
            }
         
            //$(this).prop("disabled",false);
            $("#excel_file").val(null);
        });
    });
});
</script>