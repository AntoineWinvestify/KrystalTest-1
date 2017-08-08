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
* @date 2017-02-02
* @package
*

Panel which shows all linked accounts and for adding a new one or deleting and
existing linked acount

2017-02-02		version 0.1
multi-language support added

Added global AJAX service	(later to be put in global main.js				[OK]
removed <style>
added alert msgs informing user of successfull add or delete account				[OK]





Pending:




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

<?php
        $message = "";
        $class_message = "alert-win-success";
        switch ($action) {
            case "error":
                $message = '<strong>' . __("The combination of user/password is incorrect. Please check your data and try again") . '</strong>';
                $class_message = "alert-win-warning";
                break;
            case "add":
                $message = '<strong>' . __("The account has been successfully added") . '</strong>';
                break;
            case "delete":
                $message = '<strong>' . __("The account has been deleted") . '</strong>';
                break;
        }
?>
        <div class="row row-to-fade"> 
            <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                <div id="messageErrorLinkAccount" role="alert" class="alert bg-success alert-dismissible <?php echo $class_message ?> fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button>
                    <strong><?php echo $message ?></strong>
                </div>
            </div> 
        </div>
<?php	
	if (!empty($linkedAccountResult)) {	
            foreach ($linkedAccountResult as $account) {		
?>	
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 accountLinkingPadding">                
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
                                                    <!-- /.row  -->
<?php
            }
	
	}
	else {
?>
            <div class="alert bg-success alert-dismissible alert-win-success fade in">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px; margin-top: 5px"><span aria-hidden="true">&times;</span></button>
                <strong><?php echo __("You currently don't have any account defined. By adding your crowdlending accounts
                                                                    you will be able see all your global investment position in your global
                                                                    dashboard")?>
                </strong>
            </div>
<?php	
	}
