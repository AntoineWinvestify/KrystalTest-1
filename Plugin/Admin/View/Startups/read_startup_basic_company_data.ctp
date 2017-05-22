<?php

/**
 *
 *
 * View for the tab  of the requestloandata 
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-05-13
 * @package



2016-05-13		version 0.1





Pending:
remove references to loans etc
checkform routine

*/
?>

<?php echo $this->Html->script('ckeditor/ckeditor.js'); ?>


    
<script>
	var jsonList = {};	
	
$(document).ready(function() {
	var shortDescription,
		longDescription;

$("form").submit(function( event ) {
	event.preventDefault();

	shortDescription = CKEDITOR.instances.shortDescriptionMessageId.getData();
	longDescription = CKEDITOR.instances.longDescriptionMessageId.getData();
	var action= "writeStartupBasicCompanyData";
	var url_string = "/" + receivingController + "/" + action;	
//checkform
 	jsonList['startup_ideaDescriptionShort'] = CKEDITOR.instances.shortDescriptionMessageId.getData();
 	jsonList['startup_ideaDescriptionLong'] = CKEDITOR.instances.longDescriptionMessageId.getData();

	$.ajax({
		type: "POST",
		url: url_string,
		data: {
			 jsonList	: JSON.stringify(jsonList),
			 startupId	: startupId,
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
});





$("input, select").bind("change", function(event) {
	var id = $(this).attr("id");
	var value = $("#"+id).val();
	var name = $("#"+id).attr("name");
	app.utils.trace("Input line has changed, name = " + name + " id = " + id + " and value = " + value);
	jsonList[name] = value;
});
  
   
});		
</script>



<form method="post" action="basicCompanyData" id="CakePHPForm" enctype="multipart/form-data">

    <div id="id-wrapper">
        <div class="row">
			<div class="col-lg-12">			
                <div class="panel panel-default">
                    <div id="ctl00_ContentPlaceHolder1_ctrlDetallePrestamo_seccionDocumentos" class="tab-pane">
                        <div class="panel">
                                <div class="<?php echo $class?>" id ="companyForm">
									<div class="col-sm-4 col-xs-12 nomargin nopadding">
										<label>
											<?php echo __("CIF del Startup") ?>
										</label>
<?php
echo $this->Form->input('Startup.startup_CIF', array('id' 	=> 'ContentPlaceHolder_startupCIF',
													'label' => false,
													'class' => 'form-control startupCIF',
													'value'	=> $resultStartupData[0]['Startup']['startup_CIF'],
						));
?>
                                    </div>

                                    <div class="col-sm-8 col-xs-12 nomargin">
                                        <label><?php echo __("Nombre completo del Startup") ?></label>
<?php
	echo $this->Form->input('Startup.startup_name', array('id'	=> 'ContentPlaceHolder_startupName',
																'label' => false,
																'class' => 'form-control startupName',
																'value'	=> $resultStartupData[0]['Startup']['startup_name'],
							));
?>
                                    </div>

                                    <div id="ContentPlaceHolder_blockErrorCompanyName" class="errorInputMessage ErrorCompanyName">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <span class="errorMessage">
                                            <?php echo __('Error') ?>
										</span>
                                    </div>
                                    <div id="ContentPlaceHolder_blockErrorCompanyId" class="errorInputMessage ErrorCompanyId">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <span class="errorMessage">
                                            <?php echo __('Error') ?>
										</span>
                                    </div>
                                </div>
							
							
                                <div class="form-group">
                                    <div class="col-sm-4 col-xs-12 nomargin nopadding">
                                        <label><?php echo __("Nombre") ?></label>

<?php
	echo $this->Form->input('Startup.investor_name', array('id' 	=> 'ContentPlaceHolder_startupName',
															'label' => false,
															'class' => "form-control startupContactName",
															'value'	=> $resultStartupData[0]['Startup']['startup_contactName'],
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
											'value'	=> $resultStartupData[0]['Startup']['startup_contactSurnames'],
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
                                    <div class="col-md-4 col-xs-12 nomargin nopadding">
                                        <label><?php echo __("Fecha Alta Startup") ?></label>
<?php

	echo $this->Form->input('Startup.investor_dateOfBirth', array(
											'id' 				=> 'ContentPlaceHolder_investor_dateOfBirth',
											'label' 			=> false,
											'type'				=> 'text',
											'class' 			=> 'form-control datepicker investorDateOfBirth',
											'data-date-format'	=> 'dd/mm/yyyy',
											'placeholder'		=> 'dd/mm/yyyy',
											'value'				=> $resultUserData[0]['Startup']['created'],
							));
?>
                                    </div>

                                    <div class="col-md-4 col-xs-12 nomargin">
                                        <label><?php echo __('DNI') ?></label>
<?php
	echo $this->Form->input('Startup.startup_CIF', array('id' 	=> 'ContentPlaceHolder_investor_DNI',
															'label' => false,
															'class' => "form-control investorDni",
															'value'	=> $resultStartupData[0]['Startup']['startup_CIF'],
	));
?>
                                    </div>
                                    <div class="col-md-4 col-xs-12 nomargin ">
                                        <label><?php echo __("Teléfono") ?></label>
<?php
	echo $this->Form->input('Startup.startup_telephone', array('id' 	=> 'ContentPlaceHolder_investor_telephone',
																'label' => false,
																'class' => 'form-control investorTelephone',
																'maxlength' => "15",
																'value'	=> $resultStartupData[0]['Startup']['startup_telephone'],
																));
?>
									</div>
                                    <div class="errorInputMessage ErrorDateOfBirth">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <span class="errorMessage"></span>
                                    </div>
 									<div class="errorInputMessage ErrorDni">
								        <i class="fa fa-exclamation-circle"></i>
								        <span class="errorMessage">
										</span>
									</div>
 									<div class="errorInputMessage ErrorTelephone">
								        <i class="fa fa-exclamation-circle"></i>
								        <span class="errorMessage">
										</span>
									</div>
                                </div>

                            <div class="col-xs-12">
                                <div>
<?php
									echo ' <br><label>' .__('Vision [Descripción breve]') . '</label> <br>';
?>									
                                    <textarea name="startup_ideaDescriptionShort" id="shortDescriptionMessageId"
											  class="form-control textarea-extend">
											  <?php echo $resultStartupData[0]['Startup']['startup_ideaDescriptionShort']?></textarea>
<script>
	
CKEDITOR.replace( 'startup_ideaDescriptionShort',
    {
		toolbar 	: 'Basic',
		bodyId		: 'shortDescriptionMessageId',
		uiColor 	: '#7ade2e',
    });

</script>
<?php
									echo ' <br><label>' .__('Vision [Descripción larga]') . '</label> <br>';
?>									
                                    <textarea name="startup_ideaDescriptionLong" id="longDescriptionMessageId"
											  class="form-control textarea-extend">
										<?php echo $resultStartupData[0]['Startup']['startup_ideaDescriptionLong']?>
									</textarea>											  
<script>

CKEDITOR.replace( 'startup_ideaDescriptionLong',
    {
		toolbar 	: 'Basic',
		bodyId		: 'longDescriptionMessageId',
		uiColor 	: '#7ade2e',
    });

</script>
                                    <div class="errorInputMessage ErrorTextMessage">
                                         <i class="fa-exclamation-circle"></i>
                                        <span class="errorMessage"></span>
                                    </div>									
									
									<div class="form-group">	                                    
                                        <div class="errorInputMessage">
                                            <span class="icon icon-notification"></span>
                                            <span class="errorMessage">Error</span>
                                        </div>								
                                    </div>
									
                                    <div class="form-group">
				                       <input type="submit" name="ctl00$ContentPlaceHolder1$ctrlDetallePrestamo$btGuardarDocumentos" value="Guardar" id="ctl00_ContentPlaceHolder1_ctrlDetallePrestamo_btGuardarDocumentos" class="btn btn-green nomargin" />
                                    </div>
                                </div>
		                   </div>
		               </div>
					</div>
				</div>
			</div>
		</div>
	</div>