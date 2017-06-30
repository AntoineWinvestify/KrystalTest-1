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
	echo '<div id="accountList">';
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
        <div class="box box-warning fade in <?php echo $class_message ?>">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="fa fa-times"></i>
            </button>
            <?php echo $message ?>
        </div>
<?php	
	if (!empty($linkedAccountResult)) {	
            foreach ($linkedAccountResult as $account) {		
?>	
                <div class="row">
                    <div class="box-body">
                        <div class="col-xs-12 col-sm-6 col-md-3 text-center">
                            <div class="form-group">
                                <label><?php echo __('CrowdLending Company')?></label>
    <?php
                echo $this->Form->input('name', array(
                    'label' 		=> false,
                    'class' 		=> 'form-control blue',
                    'disabled'		=> 'disabled',
                    'value'             => $companyResults[$account['Linkedaccount']['company_id']]['company_name'],						
                ));
?>
                            </div>					
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-3 text-center">
                            <div class="form-group">
                                <label><?php echo __('Your User')?></label>
<?php
                                echo $this->Form->input('name', array(
                                    'label' 		=> false,
                                    'class' 		=> 'form-control blue',
                                    'disabled'		=> 'disabled',
                                    'value'		=> $account['Linkedaccount']['linkedaccount_username'],						
                                )); 
?>										
                            </div> 					
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-3 text-center">
                            <div class="form-group">
                                <label><?php echo __('Your Password')?></label>
<?php
                                echo $this->Form->input('password', array(
                                        'label' 		=> false,
                                        'type'			=> 'password',
                                        'class' 		=> 'form-control blue',
                                        'disabled'		=> 'disabled',
                                        'value'			=> $account['Linkedaccount']['linkedaccount_password'],						
                                )); 
?>									
                            </div>					
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-3 text-center">
                            <div class="form-group">
                                <label class= "invisible"></label>
                                <button type="button" href="/investors/deleteLinkedAccount" value="<?php echo $account['Linkedaccount']['id'] ?>"
                                        class="btn btn-primary form submitButton deleteLinkedAccount line-btn btn-invest btnRounded"><?php echo __('Delete')?>
                                </button>	
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
            <div class="box box-warning fade in alert-win-success">
                <button type="button" class="btn btn-box-tool" data-widget="remove">
                    <i class="fa fa-times"></i>
                </button>
                <strong><?php echo __("You currently don't have any account defined. By adding your crowdlending accounts
                                                                    you will be able see all your global investment position in your global
                                                                    dashboard")?>
                </strong>
            </div>
<?php	
	}
	echo '</div>';		//	accountList
?>	