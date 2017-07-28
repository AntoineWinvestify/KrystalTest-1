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
 * Public form for retrieving data from the students database
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2014-09-25
 * @package



2014-09-25	  version 2014_0.1
very basic version without any fancy formatting

2014-09-29	  version 2014_0.2
Added a number of missing fields and improved layout 



Pending:




*/
?>
<h1 >Retrieving data from the 'Student' Database</h1>

<?php
	echo $this->Html->css('form_template');
	echo $this->Form->create('Test', array(
											'action' =>'getDatabaseData',
											) );
											
	echo "<br>";
	echo "<fieldset>";							
	echo $this->Form->input('selected.school', array(
   	 	'options' => $schoolList,
			'label' =>	'School',
    		'empty' => '(choose one)'
			));
	
	echo "<br>";
	echo "Use Student Population:&nbsp;&nbsp;";				

	$options = array('1' => 'Preinscriptions', '2' => 'confirmed Inscriptions');
	$attributes = array('legend' => false);
	echo $this->Form->radio('selected.status', $options, $attributes);
	echo "</fieldset>";




?>

<p>
</p>


Select fields to retrieve:
<table style = "text-align:right">
	<tbody>

<tr>
<td>
<?php echo "<label>ID</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('id', array('hiddenField' => false, 'value' => 'id')); ?>
</td>

<td>
<?php echo "<label>Name</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('name', array('hiddenField' => false, 'value' => 'name')); ?>
</td>
</tr>


<tr>
<td>
<?php echo "<label>Surname</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('surname', array('hiddenField' => false, 'value' => 'surname')); ?>
</td>

<td>
<?php echo "<label>Address</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('address', array('hiddenField' => false, 'value' => 'address')); ?>
</td>				
</tr>

<tr>
<td>
<?php echo "<label>PostCode</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('postcode', array('hiddenField' => false, 'value' => 'postcode')); ?>
</td>

<td>
<?php echo "<label>City</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('city', array('hiddenField' => false, 'value' => 'city')); ?>
</td>				
</tr>

<tr>
<td>
<?php echo "<label>Sex</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('sex', array('hiddenField' => false, 'value' => 'sex')); ?>
</td>

<td>
<?php echo "<label>DNI</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('dni', array('hiddenField' => false, 'value' => 'dni')); ?>
</td>				
</tr>

<tr>
<td>
<?php echo "<label>Date of Birth</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('date_of_birth', array('hiddenField' => false, 'value' => 'date_of_birth')); ?>
</td>

<td>
<?php echo "<label>Email</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('email', array('hiddenField' => false, 'value' => 'email')); ?>
</td>				
</tr>

<tr>
<td>
<?php echo "<label>Enrollment date</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('created', array('hiddenField' => false, 'value' => 'created')); ?>
</td>

<td>
<?php echo "<label>Telephone</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('telephone', array('hiddenField' => false, 'value' => 'telephone')); ?>
</td>				
</tr>

<tr>
<td>
<?php echo "<label>School</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('school_id', array('hiddenField' => false, 'value' => 'school_id')); ?>
</td>

<td>
<?php echo "<label>Comments</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('comments', array('hiddenField' => false, 'value' => 'comments')); ?>
</td>
</tr>


<tr>
<td>
<?php echo "<label>Course</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('course', array('hiddenField' => false, 'value' => 'course')); ?>
</td>

<td>
<?php echo "<label>Client Number</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('clientnumber', array('hiddenField' => false, 'value' => 'clientnumber')); ?>
</td>
</tr>

<td>
<?php echo "<label>Group</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('group_id', array('hiddenField' => false, 'value' => 'group_id')); ?>
</td>				
</tr>

<tr>
<td>
<?php echo "<label>Bank Account</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('bankaccount', array('hiddenField' => false, 'value' => 'bankaccount')); ?>
</td>

<td>
<?php echo "<label>Bank Account Holder</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('bankaccountholder', array('hiddenField' => false, 'value' => 'bankaccountholder')); ?>
</td>				
</tr>

<tr>
<td>
<?php echo "<label>CIF Bank Account Holder</label>"; ?>
</td>
<td>
<?php echo $this->Form->checkbox('cifacctholder', array('hiddenField' => false, 'value' => 'cifacctholder')); ?>
</td>
</tr>





  </tbody>
     </table>


<br />

<?php
	echo $this->Form->button(__('Confirm'), array("name" => "confirm",
											"type" => "submit",
 											));

	echo $this->Form->end();

?>
