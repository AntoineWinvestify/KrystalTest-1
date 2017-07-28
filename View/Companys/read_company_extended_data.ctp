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
* @date 2016-10-09
* @package
*


2016-10-09		version 0.1
multi-language support added






Pending:
Re-use!!


*/

?>
<?php
	if ($error) {
		echo "0";
	}
	else {
		echo "1";
	}
?>


		
<script>
function successRateButton(data){
	console.log("successRateButton function is called");
	$(".rateCompany").empty();
	$('.rateCompany').html(data).show();
}


function errorRateButton(data){
	console.log("errorRateButton function is called");
	
	
}



$(document).ready(function() {
	
$(".rateCompanyButton").on("click", function(event) {
	console.log("Rate button pressed, request question from server");	
	var link = $(this).attr( "href" );	
	var pollId = $(this).attr( "id" );
	console.log("pollId = " + pollId);	

	event.stopPropagation();
	event.preventDefault();	 
	
	var params = { pollId:pollId };
	var data = jQuery.param( params );

	getServerData(link, data, successRateButton, errorRateButton);
});


});
</script>



<div class="row">
Company Extended Data. Format not clear
Add more data that reletes to this company
<?php 
echo __('Number of investors: ');
?>
			<div> 
				<div>
					<button type="button"  id = "<?php echo $companyExtendedDataResult[$companyId]['poll_id']?>"  href="/polls/readPollQuestions" class="form rateCompanyButton">Rate Company</button>		
				</div>
			</div>
			<div class="rateCompany">
	
			</div>
</div>

						