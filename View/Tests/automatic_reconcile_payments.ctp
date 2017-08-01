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




</script>

<h1 >Automatic Reconciliation of payments</h1>

<?php
print_r($printData);

	echo $this->Html->css('form_template');
	echo $this->Form->create('Test', array(
											'enctype' 			=> 'multipart/form-data',
											'inputDefaults' 	=> array(
																 # define error defaults for the form
																		'error' => array(
																		),
																	),
											'action' => 'automaticReconcilePayments',
											)
											);
	echo '<div class="form">';
	echo '<label style="width: 300; color:grey;margin-right: 10px;">'  . "Is this a simulation?:&nbsp;" . "</label>";
	echo $this->Form->select('simulated', array (0 => "NO", 1 => "YES"));

	echo '<br clear="all">';
	echo '<br/>';
	echo '<label style="width: 300; color:grey;margin-right: 10px;">'  . "Payment File to Upload:&nbsp;" . "</label>";
	echo $this->Form->input('submittedfile', array('type' => 'file'));

 	echo '</div>';
	echo '<br clear="all">';

	echo $this->Form->button(__('Send'), array("name" => "confirm",
											"type" => "submit",
 											));

	echo $this->Form->end();

echo '<div class="student_listing">';
	 if (count($studentList) == 0) {
		  echo "<br />";
		  echo __("No results found");
	 }
	 else {


	}

?>
</div>
