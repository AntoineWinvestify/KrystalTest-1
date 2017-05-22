<?php
/*
// +-----------------------------------------------------------------------+
// | Copyright (C) 2014, http://beyond-language-skills.com                 |
// +-----------------------------------------------------------------------+
// | This file is free software; you can redistribute it and/or modify     |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation; either version 2 of the License, or     |
// | (at your option) any later version.                                   |
// | This file is distributed in the hope that it will be useful           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
// | GNU General Public License for more details.                          |
// +-----------------------------------------------------------------------+
// | Author: Antoine de Poorter                                            |
// +-----------------------------------------------------------------------+
//

/*
/**
 *
 *
 * Public form for producing a list of students which need to pay manually the regular fee
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2015-05-11
 * @package



2015-05-11	  version 0.1
Basic, no formatting





Pending:







*/

?>

<script type="text/javascript">
	var name = "";
	var population = "1";


$(function() {
$(".toggle").click(function() {
	if ($(this).next().is(":hidden")) {
	    $(this).next().slideDown("fast");
	    }else{
	    $(this).next().hide("fast");
    }
});


$("#input_name").blur(function(){
	name = $.trim(this.value);
	if(!name){
		name = "";
	}
});




$("#select-list-students").click(function(){
	var output1;
	var serviceId= $(this).val();
	var url_string = "/tests/getListStudents";
		$.ajax({
			type: "POST",
			url: url_string,
			data: {
				 serviceId:		serviceId,
				},
			success:function(output){
				output1 = $.trim(output);
				$( ".student_listing" ).remove();  // CHECK
				$(output1).insertAfter('#end_of_parameters');
				}
		}
);
});


});

</script>

<h1 >Prepare list of student who pay manually NOT GOOD TITLE</h1>

<?php
	echo $this->Html->css('form_template');
	echo $this->Form->create('Test', array(
											'inputDefaults' => array(
																 # define error defaults for the form
																	'error' => array(
																		'wrap' => 'span',
																		'class' => 'error-message'
																			),
																		),
															'action' => 'prepareManualPayment',
															'class'	  => 'form',
																	) );

	echo '<label style="width: 300; color:grey;margin-right: 10px;">'  . "Is this a simulation?:&nbsp;" . "</label>";	
	echo $this->Form->select('simulated', array (0 => "NO", 1 => "YES"));
	echo '<br clear="all">';

	echo '<label style="width: 300; color:grey;margin-right: 10px;">'  . "Select Type of Charging Period:&nbsp;" . "</label>";
	echo $this->Form->select('billing_period', $paymentPeriodList);
	echo '<br clear="all">';

	echo '<br/>';
	echo '<label style="width: 300; color:grey;margin-right: 10px;">'  . "Enter Period:&nbsp;" . "</label>";
	echo $this->Form->input('billing_period_data', array(	'label' 	=>	false,
															'required' 	=>	false,
															'maxLength' => '40',
															'size' 		=> '30'
    													));
	echo '<br clear="all">';

	echo "<br/><br/>";
	echo '<label style="width: 300; color:grey;margin-right: 10px;">' . "Select Service Type" . "</label>";
	$attributes = array('legend' => false, 'id' => 'select-list-students');
	echo $this->Form->select("temp", $services, $attributes);
	echo '<br clear="all">';
	echo "<br/>";

?>
<div id="end_of_parameters"><p></p></div>

<br/>
<br/>
<?php

	echo $this->Form->button(__('Confirm'), array(	'name' => 'confirm',
								//					'type'	=> 'button',
													"type" => "submit",
												//	'id' 	=> 'process-list',
 											));

?>
</div>
