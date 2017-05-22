<?php
/**
 * 
*/
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
// $data		the data of the student



?>
<style>
h1 {
	font-size: 27px;
	font-weight: normal;
	letter-spacing: -1px;
	color: #006699;
	margin-left:5px;
	margin-right:15px;
}

li {
	text-align: left; 
	font: normal 70% arial, sans-serif;
	color: black;  
	margin-left: 40px;
	margin-right:30px;
}

p {
	text-align: left; 
	font: normal arial, sans-serif;
	color: black;  
	margin-left: 30px;
	margin-right:30px;
}

table {
	margin-left: 30px;
}
</style>
<?php

print_r($parentData);
print_r($childrenData);
	


?>
<h1>Confirmation of Discount Request</h1>
This is a test;




Her I can add all the needed information for the certifcate.

But can I show the following image.<br>

<?php
	 echo $this->Html->image('logo_6.png', array ('alt' => 'logo'));
	 echo "<br><br>";
	 echo $this->Html->image("logo_6.png", array('fullBase' => true));
	 echo "<br><br>";	 
	 echo $this->Html->image('Signature_director.png', array('alt' 	=> 'Confirm',
															'width' => '100',
															'height'=> '60',));
	 echo $this->Html->image('Signature_director.png', array('alt' 	=> 'Confirm',
															'width' => '100',
															'height'=> '60',
															'fullBase' => true,
															));

?>













