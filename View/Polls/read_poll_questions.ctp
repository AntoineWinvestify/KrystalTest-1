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
* @date 2016-10-07
* @package
*


View for the poll questions



2016-10-07		version 0.1







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
<?php  echo $this->Html->script('stars.js'); ?>
<script>
var jsonList = {};



function storeRatings(index, value) {
	console.log("index = " + index + " and value = " + value);
	jsonList[index] = value;
	console.log("json =" + JSON.stringify(jsonList));
	
}
function successAddPollData() {
	console.log("successAddPollData");
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



<?php
echo "<h4>Please answer the following questions </h4><br>";
	$index = -1;
	foreach ($ratingDataResult[0]['Pollquestion'] as $question)  {
		$index++;
		$class = "click-callback-" . $index;
		echo '<row>';
			echo '<div class="col-xs-3 col-lg-5 col-md-4">';
				echo "<h4>" . $question['pollquestion_text'] . "</h4>";
			echo "</div>";
		echo "</row>";
		
		echo "<row>";
			echo '<div class="col-xs-3 col-lg-5 col-md-4">';		
				echo '<div class="' . $class . '">';
				echo "</div>";
			echo "</div>";
//		echo "</div>";
		
//		echo '<div class="col-xs-3 col-lg-2 col-md-2 text-center ">';		
		$extraInfo =  $question['pollquestion_sequenceNumber'];
//		echo "</div>";
		echo "</row>";
?>

<script>
		console.log("TESTING");

			$(".<?php echo $class?>").stars({
				stars      : 8,                // How many stars are displayed. Default is 5
				emptyIcon  : 'fa-star-o',      // Font icon class for empty stars
				filledIcon : 'fa-star',        // Font icon class when hovering or selected
				color      : '#E4AD22',        // Font color
				value      : 0,                // Default value to initialize filled stars
				text       : [],               // Array of strings, tooltips for each star
				click: function(index) {
				storeRatings(<?php echo $extraInfo?>, index);
			}
		});
</script>

<?php			
	}
?>

						<div class="col-xs-6 col-lg-1 col-md-1">	
							<button type="button" value="<?php echo $index ?>" class="xyz" id = "pollStoreData" href="/polls/storePollData" class="pollSubmitButton">Send</button>					
						</div