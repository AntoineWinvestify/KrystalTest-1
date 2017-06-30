<?php

/**
 *
 *
 * View for the tab "entreprenuer"
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-05-13
 * @package



2016-05-13		version 0.1





Pending:
changed data is not saved
validation of form data


*/
?>








  <!--   <script src="js/main.js"></script>-->
    <!-- Page-Level Demo Scripts - Tables - Use for reference -->


<?php echo $this->Html->script('ckeditor/ckeditor.js'); ?>


<script>
	var jsonList = {};	
	

	
$(document).ready(function() {
	
/**
*
* Deals with the SAVE button of personal data of the loaninformation
*
*/
$( "form" ).submit(function( event ) {

	event.preventDefault();
// validation of form data
 
	var action= "writeLoanRequestPersonalData";
	var url_string = "/" + receivingController + "/" + action;	
 // if ($(target).is(':empty')) {
	$.ajax({
		type: "POST",
		url: url_string,
		data: {
			 jsonList		: JSON.stringify(jsonList),
			 loanRequestId	: loanRequestId,
			},	  
		error: function(data){
			$('#modalKO').modal('show'); 
		},
		success: function(data){
			if (data == true ) {
				$('#modalOK').modal('show'); 
			}
			else {
				$('#modalKO').modal('show');				
			}
		}
	})  
//}
});




$("input, select").bind("change", function(event) {
	var id = $(this).attr("id");
	var value = $("#"+id).val();
	var name = $("#"+id).attr("name");
	jsonList[name] = value;
});
   
});
		
</script>







<form method="post" action="loanrequestData" id="CakePHPForm" enctype="multipart/form-data">

    <div id="id-wrapper">
        <div class="row">
			<div class="col-lg-12">			
                <div class="panel panel-default">
                    <div id="ctl00_ContentPlaceHolder1_ctrlDetallePrestamo_seccionDocumentos" class="tab-pane">
                        <div class="panel">
                            <div class="col-xs-12">
                                <div>
<?php
	$count = 0;
	foreach ($resultStartupData[0]['Teammember'] as $member) {
		$count = $count + 1;
?>
								<hr>
								<label>Emprendedor <?php echo $count?></label>
									<div class="form-group">
									    <div class="col-sm-4 col-xs-12 nomargin nopadding">
									        <label><?php echo __("Nombre") ?></label>

<?php
	echo $this->Form->input('Startup.investor_name', array('id' 	=> 'ContentPlaceHolder_startupName',
															'label' => false,
															'class' => "form-control startupContactName",
															'value'	=> $member['member_name'],
															));
?>
										</div>
										<div class="col-sm-8 col-xs-12 nomargin">
											<label><?php echo __("Apellidos") ?></label>

<?php
	echo $this->Form->input('Startup.startup_contactSurname', array(
											'id' 	=> 'ContentPlaceHolder_startupSurnames',
											'label' => false,
											'class' => 'form-control startupContactSurnames',
											'value'	=> $member['member_surnames'],
							));
?>
										</div>
										<div class="errorInputMessage ErrorName">
										    <i class="fa fa-exclamation-circle"></i>
										    <span class="errorMessage">
												<?php echo __('Error') ?><
											</span>
										</div>

										<div class="errorInputMessage ErrorSurnames">
										    <i class="fa fa-exclamation-circle"></i>
										    <span class="errorMessage"><?php echo __('Error') ?></span>
										</div>
									</div>			
							

									<div class="form-group">
									    <div class="col-sm-4 col-xs-12 nomargin nopadding">
									        <label><?php echo __("Telefóno") ?></label>

<?php
	echo $this->Form->input('Startup.investor_name', array('id' 	=> 'ContentPlaceHolder_startupName',
															'label' => false,
															'class' => "form-control startupContactName",
															'value'	=> $member['member_telephone'],
															));
?>
										</div>
									    <div class="col-sm-8 col-xs-12 nomargin">
									        <label><?php echo __("Email") ?></label>

<?php
	echo $this->Form->input('Startup.startup_contactSurname', array(
											'id' 	=> 'ContentPlaceHolder_startupSurnames',
											'label' => false,
											'class' => 'form-control startupContactSurnames',
											'value'	=> $member['member_email'],
							));
?>
									    </div>
										<div class="errorInputMessage ErrorName">
										    <i class="fa fa-exclamation-circle"></i>
										    <span class="errorMessage">
												<?php echo __('Error') ?><
											</span>
										</div>

										<div class="errorInputMessage ErrorSurnames">
										    <i class="fa fa-exclamation-circle"></i>
										    <span class="errorMessage"><?php echo __('Error') ?></span>
										</div>
									</div>
								

									<div class="form-group">
									    <div class="col-sm-4 col-xs-12 nomargin nopadding">
									        <label><?php echo __("Role") ?></label>
	
<?php
	echo $this->Form->input('Startup.investor_name', array('id' 	=> 'ContentPlaceHolder_startupName',
															'label' => false,
															'class' => "form-control startupContactName",
															'value'	=> $member['member_role'],
															));
?>
										</div>
										
										
										<div class="col-sm-6 col-xs-12 nomargin">
										    <label><?php echo __("Photo") ?></label>

<?php
	echo $this->Form->input('Startup.startup_contactSurname', array(
											'id' 	=> 'ContentPlaceHolder_startupSurnames',
											'label' => false,
											'class' => 'form-control startupContactSurnames',
											'value'	=> $member['member_photoGUID'],
							));
?>
										</div>
<?php
	echo $this->Html->image($member['member_photoGUID'], array('class' => 'img-responsive img-rounded img_centered'));
?>

										<div class="errorInputMessage ErrorName">
										    <i class="fa fa-exclamation-circle"></i>
										    <span class="errorMessage">
												<?php echo __('Error') ?><
											</span>
										</div>

										<div class="errorInputMessage ErrorSurnames">
										    <i class="fa fa-exclamation-circle"></i>
										    <span class="errorMessage"><?php echo __('Error') ?></span>
										</div>
									</div>									
									
									<div class="form-group">
										<div class="col-xs-12">
										    <label><?php echo __("Descripción C.V.") ?></label>
										    <textarea name="contentCVDescriptionMessage<?php echo $count?>" id="ContentPlaceHolder_question<?php echo $count?>"
											  class="form-control textarea-extend">
											  <?php echo $member['member_CV']?></textarea>
<script>
CKEDITOR.replace( 'contentCVDescriptionMessage<?php echo $count?>',
    {
		toolbar 	: 'Basic',
		bodyId		: 'shortDescriptionMessageId<?php echo $count?> ',
		uiColor 	: '#7ade2e',
    });
</script>
										</div>
										    <div class="errorInputMessage ErrorTextMessage">
										         <i class="fa-exclamation-circle"></i>
										        <span class="errorMessage"></span>
										    </div>									
									</div>
									
									<div class="form-group">	                                    
										<div class="col-xs-12 nomargin nopadding">
	                                        <div class="errorInputMessage">
	                                            <span class="icon icon-notification"></span>
	                                            <span class="errorMessage">Error</span>
	                                        </div>

											<div class="form-group">
											   <input type="submit" name="ctl00$ContentPlaceHolder1$ctrlDetallePrestamo$btGuardarDocumentos" value="Cambiar" id="ctl00_ContentPlaceHolder1_ctrlDetallePrestamo_btGuardarDocumentos" class="btn btn-green nomargin" />
											   <input type="submit" name="ctl00$ContentPlaceHolder1$ctrlDetallePrestamo$btGuardarDocumentos" value="Borrar" id="ctl00_ContentPlaceHolder1_ctrlDetallePrestamo_btGuardarDocumentos1" class="btn btn-green nomargin" />
											</div>
										</div>
									</div>
								</div>	
<?php		
	}
?>									  
		                   </div>
		               </div>
					</div>
				</div>
			</div>
		</div>
	</div>


