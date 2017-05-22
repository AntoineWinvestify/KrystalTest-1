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
 * Public form for creating the basic structure of a new school
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2014-06-21
 * @package


2014-06-21	  version 2014_0.1
very basic version without any fancy formatting





*/
?>

<h1 >Create an object structure for a new school, step 1 (2)</h1>

<?php
	echo $this->Html->css('form_template');
	echo $this->Form->create('Test', array(
											'action' => 'createStructure',
											) );
?>

<p>
Todos los campos son obligatorios
</p>


<table>
	<tbody>
<tr>
<?php
	echo '<td  valign="bottom" width="200"  style="text-align:right;">';
	if (array_key_exists('school',$errors)) {
		echo '<div class="formerror">';
	}
	else {
		echo '<div class="form">';
	}
	echo '<label style="width: 300; color:grey;margin-right: 10px;">'  . "Name of school:" . "</label>";;
	echo "</div>";
	echo '</td>
		<td  valign="top" width="250" > ';
	if (array_key_exists('school',$errors)) {
		echo '<div class="formerror">';
	}
	else {
		echo '<div class="form">';
	}

	echo $this->Form->input('school', array(
   	 	'options' => $schoolList,
			'label' =>	false,
    		'empty' => '(choose one)'
			));
	echo "</div>";
	echo "</td>";
?>
</tr>




<tr>
<?php
	echo '<td  width="200" valign="bottom" style="text-align:right;">';
	if (array_key_exists('teacher',$errors)) {
		echo '<div class="formerror">';
	}
	else {
		echo '<div class="form">';
	}

	echo '<label style="width: 300; color:grey;margin-right: 10px;">' . "Teacher Name:" . '</label>';
	echo "</div>";
	echo '</td>
		<td  valign="top" width="250" >';
	if (array_key_exists('teachername',$errors)) {
		echo '<div class="formerror">';
	}
	else {
		echo '<div class="form">';
	}
	echo $this->Form->input('teachername', array(
				'label' =>	false,
				'maxLength' => '40',
				'size' => '30'
    				));
	echo '</div>';
	echo "</td>";
?>
</tr>



<tr>
<?php
	echo '<td  width="200" valign="bottom" style="text-align:right;">';
	if (array_key_exists('teacher',$errors)) {
		echo '<div class="formerror">';
	}
	else {
		echo '<div class="form">';
	}

	echo '<label style="width: 300; color:grey;margin-right: 10px;">' . "Teacher Surname:" . '</label>';
	echo "</div>";
	echo '</td>
		<td  valign="top" width="250" >';
	if (array_key_exists('teachersurname',$errors)) {
		echo '<div class="formerror">';
	}
	else {
		echo '<div class="form">';
	}
	echo $this->Form->input('teachersurname', array(
				'label' =>	false,
				'maxLength' => '40',
				'size' => '30'
    				));
	echo '</div>';
	echo "</td>";
?>
</tr>


<?php
	echo "<tr>";
	echo '<td  width="200" valign="bottom" style="text-align:right;">';
	if (array_key_exists('prefix',$errors)) {
		echo '<div class="formerror">';
	}
	else {
		echo '<div class="form">';
	}
	echo '<label style="width: 300; color:grey;margin-right: 10px;">' . "Group name prefix:" . '</label>';
	echo "</div>";
	echo '</td>
		<td  valign="top" width="250" >';
	if (array_key_exists('prefix',$errors)) {
		echo '<div class="formerror">';
	}
	else {
		echo '<div class="form">';
	}
	echo $this->Form->input('prefix', array(
				'label' =>	false,
				'maxLength' => '50',
				'size' => '30'
    				));
	echo "</div></td>";
?>
</tr>


<tr>
<?php
	echo '<td width="200" valign="bottom" style="text-align:right;">';
	if (array_key_exists('groups',$errors)) {
		echo '<div class="formerror">';
	}
	else {
		echo '<div class="form">';
	}
	echo '<label style="width: 300; color:grey;margin-right: 10px;">' . "Number of groups" . '</label>';
	echo "</div>";
	echo '</td>
		<td  valign="top"  width="250" >';
	if (array_key_exists('groups',$errors)) {
		echo '<div class="formerror">';
	}
	else {
		echo '<div class="form">';
	}
	if (empty($selectedNoGroups)) {
		$selectedNoGroups = 10;
	}
	echo $this->Form->select('groups', array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20),
															array('empty' => false,
																	'value' => $selectedNoGroups)
															);

	echo '</div>';
	echo "</td>";
?>
</tr>

<tr>
<?php
	echo '<td width="200" valign="bottom" style="text-align:right;">';
	if (array_key_exists('classes',$errors)) {
		echo '<div class="formerror">';
	}
	else {
		echo '<div class="form">';
	}
	echo '<label style="width: 300; color:grey;margin-right: 10px;">' . "Number of classes/week" . '</label>';
	echo "</div>";
	echo '</td>
		<td  valign="top"  width="250" >';
	if (array_key_exists('classes',$errors)) {
		echo '<div class="formerror">';
	}
	else {
		echo '<div class="form">';
	}
	if (empty($selectedNoClasses)) {
		$selectedNoClasses = 2;
	}
	echo $this->Form->select('classes', array(0,1,2,3,4,5,6,7,8,9,10),
															array('empty' => false,
																	'value' => $selectedNoClasses)
															);
	echo '</div>';
	echo "</td>";
?>
</tr>


<tr>
<?php
	echo '<td width="200" valign="bottom" style="text-align:right;">';
	if (array_key_exists('duration',$errors)) {
		echo '<div class="formerror">';
	}
	else {
		echo '<div class="form">';
	}
	echo '<label style="width: 300; color:grey;margin-right: 10px;">' . "Duration of class (min.)" . '</label>';
	echo "</div>";
	echo '</td>
		<td  valign="top"  width="250" >';
	if (array_key_exists('duration',$errors)) {
		echo '<div class="formerror">';
	}
	else {
		echo '<div class="form">';
	}
	
	if (empty($selectedDuration)) {
		$selectedDuration = 60;
	} 
	echo $this->Form->select('duration', array(15 => '15', 30 => '30', 45 => '45',
															60 => '60', 75 => '75', 90 => '90', 105 => '105', 120 => '120'),
															array('empty' => false,
																	'value' => $selectedDuration)
															);
	echo '</div>';
	echo "</td>";
?>
</tr>

<?php
	echo "<tr><td></td><td><br><br>";

	echo $this->Form->button(__('Send'), array("name" => "confirm",
											"type" => "submit",
 											));
	echo "</td>
		</tr>
  </tbody>
     </table>";

?>

<br />
<br />
<br />

<?php
	if (!empty($groupDefinitions) ) {
?>
		<div class="privateform">
		<table>
		<th><?php echo __("Select"); ?></th>
		<th><?php echo __("GroupName"); ?></th>
		<th><?php echo __("ClassNumber"); ?></th>
		<th><?php echo __("Day of Course"); ?></th>
		<th><?php echo __("Start of Course<br>(Format HHMM)"); ?></th>


<?php
		$groupIndex = 0;
		foreach ($groupDefinitions as $value) {
			echo "<tr><td>";
			echo $this->Form->checkbox('Definition.'.$groupIndex.'.Selected',  array('value' => 1));

			echo '</td>
					<td valign="middle">';

			echo $this->Form->input('Definition.' .$groupIndex.'.Group.name', array(
																'label' =>	false,
																'value' => $value,
																'maxLength' => '60',
																'size' => '30'
    															));
/*
	echo $this->Form->input('Definition.' .$groupIndex.'.School.name', array(
										'value'	=> $schoolName,
										'type'	=> 'hidden'
										));
*/
			echo "</td>";

			for ($i = 0; $i < $classes; $i++) {
				if ($i<>0) {echo "<tr><td></td><td></td>";}
				echo "<td>";
				$number = $i+1;
				echo "Class No. $number ";
				echo "</td>";
				echo "<td>";

//				echo $this->Form->input('Definition.' .$groupIndex.'.Coursetimetable.dummy'.$number.'.comment', array(
																					//	'value'	=> $value,
//																						'type'	=> 'hidden'
//																						));

				echo $this->Form->select('Definition.' .$groupIndex.'.Coursetimetable.dummy' .$number.'.dayofweek',
																array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
																array('empty' => true)
																);
				echo "</td><td>";

				echo $this->Form->input('Definition.' .$groupIndex.'.Coursetimetable.dummy' .$number.'.starttime', array(
																	'label' =>	false,
																	'maxLength' => '50',
																	'size' => '30'
																		));
				echo "</td>";
			}
			echo "</tr>";
			$groupIndex = $groupIndex + 1;
		}// foreach ($groupDefinition as $value)

		echo "</table>";
		echo "</div>";

		echo $this->Form->button('Confirm Structure', array("name" => "confirm_structure",
												"type" => "submit",
 												));
	}

	echo $this->Form->end();
?>
