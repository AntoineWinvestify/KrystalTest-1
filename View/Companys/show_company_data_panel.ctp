<?php
/**
* +-----------------------------------------------------------------------+
* | Copyright (C) 2014, http://beyond-language-skills.com                 |
* +-----------------------------------------------------------------------+
* | This file is free software; you can redistribute it and/or modify     |
* | it under the terms of the GNU General Public License as published by  |
* | the Free Software Foundation; either version 2 of the License, or     |
* | (at your option) any later version.                                   |
* | This file is distributed in the hope that it will be useful           |
* | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
* | GNU General Public License for more details.                          |
* +-----------------------------------------------------------------------+
* | Author: Antoine de Poorter                                            |
* +-----------------------------------------------------------------------+
*
*
* @author Antoine de Poorter
* @version 0.1
* @date 2016-10-06
* @package
*

Panel which shows all linked accounts and for adding a new one or deleting and
existing linked acount

2016-10-06						version 0.1





Pending:

AJAX spinner 


*/
?>


<?php // echo $this->Html->script('stars.js'); ?>


		
<script>
//	methodWS: format controller/method
//	data:		serialized data
//	success:	function callback for success
//	error:		function callback for error
//
/*
function getServerData(methodWS, data, success, error) {
        console.log("**AJAX** " + methodWS + " **", data);

        $.ajax({
            type: 'POST',
 //           dataType: "json",
            data:		data,
            url: 		methodWS,
		    complete:	ajaxComplete,
		    beforeSend:	ajaxSend,
	    success: 	function (data) {
				data = data.trim();
				data1 = data.substr(1, data.length - 1);
								
				if (data.startsWith("1")) {
                    if (!!success) {
			console.log("SUCCESS RETURNED FROM AJAX Operation");
                        success(data1);      
                    }					
				}
				else {
                    if (!!error) {
			console.log("ERROR from AJAX operation");
                    }					
//					$('.editDatosPersonales').replaceWith(errorData);
				}			
				
            },
            error: function (e) {
                app.utils.trace("error");
		}
		});
    }

	
	
function ajaxSend(){
	console.log("AJAX call started");
	$(".cssload-squeeze").removeClass('hide');
}




function ajaxComplete(){
	console.log("AJAX call has finished");
	$('.cssload-squeeze').hide();
}
*/


function errorExtendedData(data){
	console.log("errorExtendedData function is called");
	
	
}


function successExtendedData(data){
	console.log("successExtendedData function is called");
	$(".selectedCompanyExtendedData").empty();
	
//$(this).closest(".panel").find('.selectedInvestmentOption').removeClass("selectedInvestmentOption");

//$(this).closest(".container").find('.extendedCompanyData').addClass("selectedCompanyExtendedData");	
	console.log("Class selectedCompanyExtendedData deleted");
	$('.selectedCompanyExtendedData').html(data).show();		
}



$(document).ready(function() {
			
$(document).on("click", ".extendedDataButton",function(event) {
	console.log("extended data Button pressed");
	var link = $(this).attr( "href" );
	var companyId = $(this).attr( "id" );
	console.log("CompanyId = " + companyId);	
	console.log("WSMethod = " + link);

	$(this).closest(".panel").find('.extendedCompanyData').addClass("selectedCompanyExtendedData");

	var params = { companyId:companyId };
	var data = jQuery.param( params );

	event.stopPropagation();
	event.preventDefault();
	
	getServerData(link, data, successExtendedData, errorExtendedData);
});



});
</script>

<?php
echo "<h3>" . __("Company Data NEW FORMAT") . "</h3>";
?>
	<div class="companiesList">
		<div class="container">
			<div class="row">
				<div class="panel">
					<div class="col-xs-3 col-lg-1 col-md-1 text-center nomargin-left  nopadding-left">
						<label>
							<strong> <?php echo __("Company") ?> </strong>
						</label>
					</div>
					<div class="col-xs-3 col-lg-4 col-md-1 text-center ">
						<label>
							<strong> <?php echo __("Logo") ?> </strong>
						</label>
					</div>
					<div class="col-xs-3 col-lg-2 col-md-1 text-center">
						<label>
							<strong> <?php echo __("Rating") ?> </strong>
						</label>
					</div>
					<div class="col-xs-6 col-lg-1 col-md-1 text-center">
						<label>
							<strong> <?php echo __("Action") ?> </strong>
						</label>
					</div>
				</div>
			</div>	
		</div>
		
	
<?php
	$index = 0;
	foreach ($companyResults as $result) {
?>
		<div class="companiesListItem">
			<div class="container">
				<div class="row">
					<div class="panel">
						<div class="form-group">
							<div class="col-xs-3 col-lg-1 col-md-1 text-center">
								<?php echo $result['company_name'];?>
							</div>
							<div class="col-xs-3 col-lg-4 col-md-1 text-center ">
								<?php echo $result['company_logoGUID'];?>
							</div>
							<div class="col-xs-6 col-lg-2 col-md-1">		
								<?php echo '
									<i class="fa fa-star" aria-hidden="true"></i>
									<i class="fa fa-star" aria-hidden="true"></i>
									<i class="fa fa-star" aria-hidden="true"></i>
									<i class="fa fa-star-o" aria-hidden="true"></i>
									<i class="fa fa-star-o" aria-hidden="true"></i>
									';
								?>
							</div>
						</div>
						<div class="col-xs-6 col-lg-1 col-md-1">	
							<button type="button" value="<?php echo $index ?>"  id = "<?php echo $result['id']?>" href="/companys/readCompanyExtendedData" class="form extendedDataButton">
								More
							</button>					
						</div>
							
						<div class="extendedCompanyData">
						</div>
					</div>		
				</div>
			</div>	
		</div>
				
<?php
	$index++;
	}
?>	
	</div>