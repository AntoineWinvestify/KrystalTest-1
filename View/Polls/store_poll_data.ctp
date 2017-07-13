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
* @date 2016-10-10
* @package
*


Result from storing the rating


2016-10-10		version 0.1







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
var jsonList = {};

function storeRatings(index, value) {
	console.log("index = " + index + " and value = " + value);
	jsonList[index] = value;
	console.log("json =" + JSON.stringify(jsonList));
	
}
function successAddPollData() {
	console.log("successAddPollData");

	
	$(document).html(data).show();
	$('.modalOK').modal('show');	
}
function errorAddPollData() {
	console.log("errorAddPollData");	
}



$(document).ready(function() {
	
	
$(document).on("click", "#pollStoreData",function(event) {
	console.log("Send Polling button");
	var link = $(this).attr( "href" );
	
	event.stopPropagation();
	event.preventDefault();
	
	var params = { pollId:2, answers:jsonList};
	var data = jQuery.param( params );
	getServerData(link, data, successAddPollData, errorAddPollData);
});	
	
	
 
});

</script>


	
<div class="modalOK hide">
<?php echo "<h4>" . __("Your rating has been saved") . "</h4><br>";?>
</div>

