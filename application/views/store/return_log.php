<form data-res_function="resTrashReturnLog" id="materialReturnLog">
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr class="thead-info">
                        <th>#</th>
                        <th>Issue No.</th>
                        <th>Issue Date</th>
						<th>Project</th>
						<th>Item Name</th>
                        <th>Return Qty</th>
                        <th>Remark</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                     
                        if(!empty($returnData)):
                            $i = 1;
                            foreach($returnData as $row):
                                $deleteReturnParam = "{'postData':{'id' : ".$row->id.",'issue_id' : ".$row->main_ref_id.",'qty' : ".$row->qty."},'message' : 'Material Return','fndelete':'deleteMaterialReturn','res_function':'resTrashReturnLog'}";
                                $deleteReturnBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteReturnParam.');" datatip="Delete Return" flow="left"><i class="mdi mdi-trash-can-outline"></i></a>';    
                                echo '<tr>
                                    <td>'.$i.'</td>
                                    <td>'.formatDate($row->trans_date).'</td>
                                    <td>'.$row->ref_no.'</td>
                                    <td>'.$row->project_name.'</td>
                                    <td>'.$row->item_name.'</td>
                                    <td>'.floatval($row->qty).'</td>
                                    <td>'.floatval($row->remark).'</td>
                                    <td>'. $deleteReturnBtn.'</td>
                                </tr>';
                                $i++;
                            endforeach;
                        else:
                            echo  '<tr><td colspan="8" class="text-center">Data not available.</td></tr>';
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</form>
<script>
function resTrashReturnLog(response){
    if(response.status==0){
        Swal.fire( 'Sorry...!', response.message, 'error' );
    }else{
        
    Swal.fire( 'Remove!', response.message, 'success' );
        initTable();       
        window.location.reload();
    }	
}
</script>
