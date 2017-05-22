<?php

/**
 *
 *
 * AJAX format for displaying a list of students that meet certain search criteria
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2014-02-20
 * @package



2015-05-11		version 0.1
Use of helper Studentlink for displaying student data									[OK]




Pending:
--


*/
?>

<script>
$(document).ready(function() {

$(function() {

$('.editButton').qtip({
    content: {
        text: "Edit the subscriber data",
    },
	style: { classes:'qtip-blue qtip-rounded'},   
})


 
});
});
</script>


<?php 	

echo '<div class="student_listing">';
	 if (count($studentList) == 0) {
		  echo "<br />";
		  echo __("No results found");
	 }
	 else {

echo "serviceId = $serviceId <br>";
//print_r($serviceData);
		foreach ($studentList as $result)  {
			echo $result['Student']['id'] . " " . 	$result['Student']['payment_means'] . " " .
													$result['Student']['name'] . " " .
													$result['Student']['surname'] . " " .
													$result['Student']['amount'];

			echo $serviceData[0]['Service']['service_desc_short'];
			echo ($serviceData[0]['Service']['service_price']/100) .  "€";
			echo $this->Form->checkbox('Student.' . $result['Student']['id'],  array('value' => 1));
			echo "<br/>";
		}
	}

?>
</div>
