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
* @date 2016-08-23
* @package
*


Panel for which shows all linked account and for adding a new one or deleting and
existing linked acount
added Id to company name and username




2016-01-15		version 0.1
multi-language support added






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
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-3 text-center">
						<label>
							<strong> <?php echo __("Company") ?> </strong>
						</label>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-3 text-center">
						<label>
							<strong> <?php echo __("Username") ?> </strong>
						</label>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-3 text-center">
						<label>
							<strong> <?php echo __("Action") ?> </strong>
						</label>
					</div>
				</div>	
 <?php					
	$index = 0;
	foreach ($linkedaccountsResult as $account) {
?>  
					<div class="row">
						<div class="col-xs-12 col-sm-6 col-md-3 text-center">
						
							<?php echo $this->Form->input('', array('name'	=> 'userId',
										'value'	=> $companyResults[$account['Linkedaccount']['company_id']]['company_name'] ,
										'id'		=> 'linkedaccountCompanyName-' . $index,
										'type'	=> 'hidden'
										));
							?>
							<?php echo $companyResults[$account['Linkedaccount']['company_id']]['company_name'] ?>
						
						</div>
						
						<div class="col-xs-12 col-sm-6 col-md-3 text-center">
						
							<?php echo $this->Form->input('', array(	'name'	=> 'userId',
										'value'	=> $account['Linkedaccount']['linkedaccount_username'],
										'id'		=> 'linkedaccountUsername-' . $index,
										'type'	=> 'hidden'
										));
							?>					
							<?php echo $account['Linkedaccount']['linkedaccount_username'] ?>
						
						
						</div>
						<div class="col-xs-12 col-sm-6 col-md-3 text-center">
						
							<button type="button" value="<?php echo $index ?>" class="deleteLinkedAccount btn-invest btnRounded" id ="linkedaccountDeleteBtn-<?php echo $index ?>" href="/investors/deleteLinkedAccount" class="form submitButton">Delete</button>
 						
						</div>
					</div>
							
<?php
	$index++;
	}
	echo "</table>";
?>

</div>							