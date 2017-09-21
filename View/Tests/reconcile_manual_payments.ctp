<?php

/**
 *
 *
 * Optimized form for reconciling payments.
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2014-02-20
 * @package



2015-05-14		version 0.1




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




//
// Sends the relevant information of a received payment.
// Returns:	1	Request executed in server: Payment executed
//				0	Error occurred in server during processing
//
$(".payment-received").click(function(){
	var output1;
	var paymentreminder_id = $(this).attr( 'value' );
	var url_string = "/paymentreminders/paymentReceived";

		$.ajax({
			type: "POST",
			url: url_string,
			data: {
				 paymentreminder_id:	paymentreminder_id,
				},
			success:function(output){
				output1 = $.trim(output);
				if (output1 == 1){
					alert("Payment Added");
				}
				else {
					alert("Error while adding payment, try again");
				}
				$( ".directdebit_listing" ).remove();
				$(output1).insertAfter('#start_of_directdebitlisting');
				}
		}
);
});









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
		foreach ($resultReconcilePayment as $resultReconcilePayment)  {
			// echo "concept"; student name....
			echo '<div class ="' . $xxx . '">';		// id is changed if succesfull payment using javascript in ajax call
			echo $result['Billdata']['id'] . " " . 	$result['Student']['payment_means'] . " " .
													$result['Student']['name'] . " " .
													$result['Student']['surname'] . " " .
													$result['Student']['amount'];

			echo $serviceData[0]['Service']['service_desc_short'];
			echo ($serviceData[0]['Service']['service_price']/100) .  "€";
			echo $this->Form->checkbox('Student.' . $result['Student']['id'],  array('value' => 1));
			echo "<br/>";
		  	echo $this->form->button(__('Payment<br>Received??'), array("name"	=> "confirm",
																		"class" => "payment-received",
																		"type"	=> "button",
																		"value"	=> $value['id'],
																		));

			// add date, amount, payment means....
		//	if successfull then change color of this id to green					
			echo $this->form->button(__('Cancel Payment'), array("name"	=> "confirm", 
					  											"class" => "payment-cancelled",
					  											"type"	=> "button",
																"value"	=> $item['Billdata']['id'],
																));
			echo "</div";
			}
		}
	

?>
</div>
