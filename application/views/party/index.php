<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
                        <?php
                            //$addParam = "{'postData':{'party_category' : ".$party_category."},'modal_id' : '".(($party_category != 4)?"bs-right-lg-modal":"bs-right-md-modal")."', 'call_function':'addParty', 'form_id' : 'add".$this->partyCategory[$party_category]."', 'title' : 'Add ".$this->partyCategory[$party_category]."'}";
							
							$addParam = "{'postData':{'party_category' : ".$party_category."},'modal_id' : '".(in_array($party_category, [1,2,3])?"bs-right-lg-modal":"bs-right-md-modal")."', 'call_function':'addParty', 'form_id' : 'add".$this->partyCategory[$party_category]."', 'title' : 'Add ".$this->partyCategory[$party_category]."'}";
                        ?>
						<button type="button" class="btn btn-outline-dark btn-sm float-right permission-write press-add-btn" onclick="modalAction(<?=$addParam?>);" ><i class="fa fa-plus"></i> Add <?=$this->partyCategory[$party_category]?></button>
					</div>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='partyTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows/<?=$party_category?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function () {
    $(document).on('click','.createUser',function(){
        var id = $(this).data('id');
        var emp_id = $(this).data('emp_id');
        var status = $(this).data('val');
        var msg = "";
        if(status == 1){
            msg = "Complete";
        }

        Swal.fire({
            title: 'Are you sure?',
            text: 'Are you sure want to '+msg+' Create User?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Do it!',
        }).then(function(result) {
            if (result.isConfirmed){
                $.ajax({
                    url: base_url + controller + '/createUser',
                    data: {id:id,emp_id:emp_id,user_status:status},
                    type: "POST",
                    dataType:"json",
                    success:function(data){
                        if(data.status==0){
                            Swal.fire( 'Sorry...!', data.message, 'error' );
                        }else{
                            initTable();
                            Swal.fire( 'Success', data.message, 'success' );
                        }
                    }
                });
            }
        });
    });
});

function resSavePartyGstDetail(data,formId){
    if(data.status==1){
        Swal.fire( 'Success', data.message, 'success' );

        $('#'+formId)[0].reset();

        var gstTrans = {'postData':{'party_id':$("#gstDetail #party_id").val()},'table_id':"gstDetail",'tbody_id':'gstDetailBody','tfoot_id':'','fnget':'getPartyGSTDetailHtml'};
        getTransHtml(gstTrans);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire( 'Sorry...!', data.message, 'error' );
        }			
    }
}

function resTrashPartyGstDetail(data){
    if(data.status==1){
        Swal.fire( 'Success', data.message, 'success' );

        var gstTrans = {'postData':{'party_id':$("#gstDetail #party_id").val()},'table_id':"gstDetail",'tbody_id':'gstDetailBody','tfoot_id':'','fnget':'getPartyGSTDetailHtml'};
        getTransHtml(gstTrans);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire( 'Sorry...!', data.message, 'error' );
        }			
    }
}

function resSavePartyContactDetail(data,formId){
    if(data.status==1){
        Swal.fire( 'Success', data.message, 'success' );

        $('#'+formId)[0].reset();

        var contactTrans = {'postData':{'party_id':$("#contactDetail #party_id").val()},'table_id':"contactDetail",'tbody_id':'contactDetailBody','tfoot_id':'','fnget':'getPartyContactDetailHtml'};
        getTransHtml(contactTrans);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire( 'Sorry...!', data.message, 'error' );
        }			
    }
}

function resTrashPartyContactDetail(data){
    if(data.status==1){
        Swal.fire( 'Success', data.message, 'success' );

        var contactTrans = {'postData':{'party_id':$("#contactDetail #party_id").val()},'table_id':"contactDetail",'tbody_id':'contactDetailBody','tfoot_id':'','fnget':'getPartyContactDetailHtml'};
        getTransHtml(contactTrans);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire( 'Sorry...!', data.message, 'error' );
        }			
    }
}

function resSaveDeliveryAddressDetail(data,formId){
    if(data.status==1){
        Swal.fire( 'Success', data.message, 'success' );

        $('#'+formId)[0].reset();
        initSelect2();
        $("#delivery_country_id").trigger('change');

        var addressTrans = {'postData':{'party_id':$("#deliveryAddressDetail #party_id").val()},'table_id':"addressDetail",'tbody_id':'addressDetailBody','tfoot_id':'','fnget':'getPartyDeliveryAddressDetailHtml'};
        getTransHtml(addressTrans);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire( 'Sorry...!', data.message, 'error' );
        }			
    }
}

function resTrashDeliveryAddressDetail(data){
    if(data.status==1){
        Swal.fire( 'Success', data.message, 'success' );

        var addressTrans = {'postData':{'party_id':$("#deliveryAddressDetail #party_id").val()},'table_id':"addressDetail",'tbody_id':'addressDetailBody','tfoot_id':'','fnget':'getPartyDeliveryAddressDetailHtml'};
        getTransHtml(addressTrans);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            Swal.fire( 'Sorry...!', data.message, 'error' );
        }			
    }
}
</script>