<?php

/**
 *
 *
 * View for the tab "global data"
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
	var financialInformation,
		roi,
		legalInformation,shortDescription1,
		marketInformation;	

	event.preventDefault();

	roi = CKEDITOR.instances.ContentPlaceHolderRoiId.getData();
	marketInformation = CKEDITOR.instances.ContentPlaceHolderMarketDataId.getData();	
	financialInformation = CKEDITOR.instances.ContentPlaceHolderFinancialInformationId.getData();
	legalInformation = CKEDITOR.instances.ContentPlaceHolderLegalId.getData();
	
	var action= "writeStartupGlobalData";	

	jsonList['<?php echo FINANCIAL_INFORMATION ?>'] = financialInformation;
	jsonList['<?php echo LEGAL_DISCLAIMER ?>'] = legalInformation;
	jsonList['<?php echo ROI ?>'] = roi;
	jsonList['<?php echo MARKET_DATA  ?>'] = marketInformation;
	 
	var url_string = "/" + receivingController + "/" + action;

	$.ajax({
		type: "POST",
		url: url_string,
		data: {
			 jsonList	: JSON.stringify(jsonList),
			 startupId	: startupId,
			},	  
		error: function(data){
			app.utils.trace("Error detected");
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
	})  
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
	$textMessage = array();
	foreach ($resultStartupData[0]['Companytext'] as $text) {
		$textMessage[$text['companytext_textCode']] = $text['companytext_text'];
	}
?>

									<?php echo '<br><label>' .__('Finanzas') . '</label> <br>'?>
                                    <textarea name="contentROIMessage" id="ContentPlaceHolderRoiId"
											  class="form-control textarea-extend">
											  <?php echo $textMessage[ROI]?></textarea>
<script>
CKEDITOR.replace( 'contentROIMessage',
    {
		toolbar 	: 'Basic',
		bodyId		: 'ContentPlaceHolderRoiId',
		uiColor 	: '#7ade2e',
    });
</script>

									<?php echo '<br><label>' .__('Retorno') . '</label> <br>'?>
                                    <textarea name="contentFinancialInformationMessage" id="ContentPlaceHolderFinancialInformationId"
											  class="form-control textarea-extend">
											  <?php echo $textMessage[FINANCIAL_INFORMATION]?></textarea>
<script>
CKEDITOR.replace( 'contentFinancialInformationMessage',
    {
		toolbar 	: 'Basic',
		bodyId		: 'ContentPlaceHolderFinancialInformationId',
		uiColor 	: '#7ade2e',
    });
</script>

									<?php echo '<br><label>' .__('Mercado') . '</label> <br>'?>
                                    <textarea name="contentMarketDataMessage" id="ContentPlaceHolderMarketDataId"
											  class="form-control textarea-extend">
											  <?php echo $textMessage[MARKET_DATA]?></textarea>
<script>
CKEDITOR.replace( 'contentMarketDataMessage',
    {
		toolbar 	: 'Basic',
		bodyId		: 'ContentPlaceHolderMarketDataId',
		uiColor 	: '#7ade2e',
    });
</script>

									<?php echo '<br><label>' .__('Legal') . '</label> <br>'?>
                                    <textarea name="contentLegalMessage" id="ContentPlaceHolderLegalId"
											  class="form-control textarea-extend">
											  <?php echo $textMessage[LEGAL_DISCLAIMER]?></textarea>
<script>
 CKEDITOR.replace( 'contentLegalMessage',
    {
		toolbar 	: 'Basic',
		bodyId		: 'ContentPlaceHolderLegalId',
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
<?php
	
?>										
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