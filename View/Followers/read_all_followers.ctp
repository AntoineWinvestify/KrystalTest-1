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
* @date 2016-10-18
* @package
*


Displays a list (with photographs) of ALL the followers of an investor.
The photographs are hyperlinked to a large picture of the follower




2016-10-18 	version 0.1







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
<div class="container">
	
<?php
// add bootstrap formatting
	foreach ($followers as $follower) {	
		echo $this->Html->image($follower['Follower']['follower_photoChatGUID'], array( "alt" => "Brownies",
																	'url' => array('controller' => 'recipes',
																						'action' => 'view', 6)
								));
//		echo $follower['Follower']['alias'];
		echo "parentId = " . $follower['Follower']['follower_parentId'];
	}
	
?>

</div>							