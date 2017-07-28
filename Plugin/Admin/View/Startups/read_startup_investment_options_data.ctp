<?php

/**
 *
 *
 * View for the tab "investment options"
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-05-13
 * @package



2016-05-13		version 0.1





Pending:



*/
?>

<?php
//	echo ($result ? '1':'0');
?>
<?php echo $this->Html->script('ckeditor/ckeditor.js'); ?>



<script>
	var jsonList = {};	
	

	
$(document).ready(function() {

$("form").submit(function( event ) {
	var investoptionDescription,
		investoptionLongDescription,
		url_string,
		action;

	event.preventDefault();

	investoptionDescription = CKEDITOR.instances.ContentPlaceHolderRoiId.getData();
	investoptionLongDescription = CKEDITOR.instances.ContentPlaceHolderLongDescriptionExtraId.getData();	
	
	action= "writeStartupInvestOptionData";	
	url_string = "/" + receivingController + "/" + action;

	$.ajax({
		type: "POST",
		url: url_string,
		data: {
			 jsonList	: JSON.stringify(jsonList),
			 startupId	: startupId,
			},	  
		error: function(data){
			app.utils.trace("Error detected");
			data = data.trim();
			$('#modalKO').modal('show'); 
		},
		success: function(data){
			data = data.trim();
		
			if (data.startsWith("1")) {
				data = data.substr(1, data.length - 1).trim();
				$('#modalOK').modal('show');
			}
			else {
				errorData = data.substr(1, data.length - 1).trim();
				$('#modalKO').modal('show');
			}			
		}
	}); 
});




$("input, select").bind("change", function(event) {
	var id = $(this).attr("id");
	var value = $("#"+id).val();
	var name = $("#"+id).attr("name");
	jsonList[name] = value;
});
   
});
		
</script>

<form method="post" action="globalData" id="CakePHPForm" enctype="multipart/form-data">

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
	foreach ($resultStartupData[0]['Investoption'] as $option) {
		$count = $count + 1;
?>
								<hr>
								<label>Investment Option <?php echo $count?></label>

									<?php echo '<br><label>' .__('Descripción') . '</label> <br>'?>
                                    <textarea name="contentLongDescriptionMessage<?php echo $count?>" id="contentLongDescriptionMessage<?php echo $count?>"
											  class="form-control textarea-extend">
											  <?php echo $option['investoption_longDescription']?></textarea>
<script>
CKEDITOR.replace( 'contentLongDescriptionMessage<?php echo $count?>',
    {
		toolbar 	: 'Basic',
		bodyId		: 'ContentPlaceHolderLongDescriptionId<?php echo $count?>',
		uiColor 	: '#7ade2e',
    });
</script>

									<?php echo '<br><label>' .__('Descripción Larga') . '</label> <br>'?>
                                    <textarea name="contentLongDescriptionExtraMessage<?php echo $count?>" id="ContenLongDescriptionExtraId<?php echo $count?>"
											  class="form-control textarea-extend">
											  <?php echo $option['investoption_longDescriptionExtra']?></textarea>
<script>
CKEDITOR.replace( 'contentLongDescriptionExtraMessage<?php echo $count?>',
    {
		toolbar 	: 'Basic',
		bodyId		: 'ContentPlaceHolderLongDescriptionExtraId<?php echo $count?>',
		uiColor 	: '#7ade2e',
    });
</script>

									<div class="form-group">
									    <div class="col-sm-4 col-xs-12 nomargin nopadding">
									        <label><?php echo __("Texto TPV") ?></label>

<?php
	echo $this->Form->input('Startup.investor_name', array('id' 	=> 'ContentPlaceHolder_startupName',
															'label' => false,
															'class' => "form-control startupContactName",
															'value'	=> $option['investoption_textTPV'],
															));
?>
										</div>
										
										<div class="col-sm-8 col-xs-12 nomargin">
											<label><?php echo __("Eslogan") ?></label>
<?php
	echo $this->Form->input('Startup.startup_contactSurname', array(
											'id' 	=> 'ContentPlaceHolder_startupSurnames',
											'label' => false,
											'class' => 'form-control startupContactSurnames',
											'value'	=> $option['investoption_slogan'],
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
									        <label><?php echo __("Rolling Amount  [€]") ?></label>

<?php
	echo $this->Form->input('Startup.investor_name', array('id' 	=> 'ContentPlaceHolder_startupName',
															'label' => false,
															'class' => "form-control startupContactName",
															'value'	=> $option['investoption_rollingAmount']/100,
															));
?>
										</div>
										
										<div class="col-sm-8 col-xs-12 nomargin">
											<label><?php echo __("Objective [€]") ?></label>
<?php
	echo $this->Form->input('Startup.startup_contactSurname', array(
											'id' 	=> 'ContentPlaceHolder_startupSurnames',
											'label' => false,
											'class' => 'form-control startupContactSurnames',
											'value'	=> $option['investoption_objective']/100,
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
									        <label><?php echo __("Fecha Devolución") ?></label>

<?php
	echo $this->Form->input('Startup.investor_name', array('id' 	=> 'ContentPlaceHolder_startupName',
															'label' => false,
															'class' => "form-control startupContactName",
															'value'	=> $option['investoption_returnInvestmentDate'],
															));
?>
										</div>
										
										<div class="col-sm-8 col-xs-12 nomargin">
											<label><?php echo __("Equity %") ?></label>
<?php
	echo $this->Form->input('Startup.startup_contactSurname', array(
											'id' 	=> 'ContentPlaceHolder_startupSurnames',
											'label' => false,
											'class' => 'form-control startupContactSurnames',
											'value'	=> $option['investoption_equity']/100,
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
									        <label><?php echo __("Importe Mínimo Inversión [€]") ?></label>

<?php
	echo $this->Form->input('Startup.investor_name', array('id' 	=> 'ContentPlaceHolder_startupName',
															'label' => false,
															'class' => "form-control startupContactName",
															'value'	=> $option['investoption_minInvestmentAmount']/100,
															));
?>
										</div>
									    <div class="col-sm-8 col-xs-12 nomargin">
									        <label><?php echo __("Importe Máximo Inversión [€]") ?></label>

<?php
	echo $this->Form->input('Startup.startup_contactSurname', array(
											'id' 	=> 'ContentPlaceHolder_startupSurnames',
											'label' => false,
											'class' => 'form-control startupContactSurnames',
											'value'	=> $option['investoption_maxInvestmentAmount']/100,
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
								


<?php
	}
?>

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