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
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4"> <!-- Crowdlending Company Logo -->
                <div class="form-group">
                    <div class="box box-widget widget-user-2">
                        <div class="widget-user-header">
                            <img style="vertical-align: middle; max-height: 100px;" class="responsiveImg center-block platformLogo" src="/img/logo/<?php echo $companyResults[$account['Linkedaccount']['company_id']]['company_logoGUID'] ?>" alt="<?php echo $companyResults[$account['Linkedaccount']['company_id']]['company_name']?>">
                        </div>
                    </div>
                </div>
                <button type="button" href="/investors/deleteLinkedAccount" value="<?php echo $account['Linkedaccount']['id'] ?>"
                id="company_<?php echo $account['Linkedaccount']['company_id'] ?>" 
                onclick='ga_deleteAccountClick("<?php echo $account['Linkedaccount']['company_id'] ?>",
                "<?php echo $companyResults[$account['Linkedaccount']['company_id']]['company_name']?>")'
        class="btn btn-default btnRounded form submitButton deleteLinkedAccount center-block"><i class="ion ion-trash-a"></i> <small><?php echo __('Delete')?></small>
                </button>
            </div> <!-- /crowdlending company -->
            <div class="col-xs-12 col-md-12 col-md-7 col-lg-7">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-12 col-lg-12"> <!-- Username -->
                        <div class="form-group">
                            <label><small><?php echo __('Your User')?></small></label>
                            <?php
                                                            echo $this->Form->input('name', array(
                                                                    'label' 		=> false,
                                                                    'class' 		=> 'form-control blue_noborder22',
                                                                    'disabled'		=> 'disabled',
                                                                    'value'			=> $account['Linkedaccount']['linkedaccount_username'],						
                                    )); 
                            ?>					
                        </div>					
                    </div>
                    <!-- /Username -->
                    <!-- Password -->
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label><small><?php echo __('Your Password')?></small></label>
                            <?php
                                echo $this->Form->input('password', array(
                                        'label' 		=> false,
                                        'type'			=> 'password',
                                        'class' 		=> 'form-control blue_noborder2',
                                        'disabled'		=> 'disabled',
                                        'value'			=> $account['Linkedaccount']['linkedaccount_password'],						
                                )); 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
							
<?php
	$index++;
	}
?>

</div>							