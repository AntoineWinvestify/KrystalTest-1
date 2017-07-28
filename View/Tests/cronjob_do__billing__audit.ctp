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

Avoid double submit:

<input type="submit" value = "Submit" id ="mybutton" />
form action ="xxx.php" method = POST
onsubmit="document.getElementById('myButton')disabled=true;
getElementById('mybutton').value="Submitting, please wait...";"

*/
?>



<h1 >test template</h1>

<?php
	echo $this->Html->css('form_template');
	echo $this->Form->create('Test', array(
											'action' =>'mytest',
											) );

	echo "<br>";
	echo "Select a school:&nbsp;&nbsp;";
	$attributes = array('legend' => false, 'id' => 'list-select-school');
	echo $this->Form->select('status', $listing, $attributes);
	echo $this->Form->button(__('Add'), array('type' => 'button','id' => "addGroups",));
?>

</div>
<br />


<br/><br/>

<?php 	?>

<p>
</p>

<?php
	echo $this->Form->button(__('Confirm'), array("name" => "confirm",
											"type" => "submit",
 											));

	echo $this->Form->end();

?>
