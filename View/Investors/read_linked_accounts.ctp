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
* @date 2016-11-24
* @package
*

Panel which shows all linked accounts and for adding a new one or deleting and
existing linked acount

2016-11-24		version 0.1
multi-language support added

    Added global AJAX service	(later to be put in global main.js                                  [OK]
removed <style


2017-01-22	version 0.2
Added Google Analytics


2017-04-17      version 0.3                                                                         [Ok]
only execute Google Analytics if ga class exists.

2017-07-28 version 0.4
 * Updated complete View to new looking field.

Pending:
-
 * 




*/
?>

	
<script>
// functions that update Google Analytics 
function ga_addNewAccountClick(){
	console.log("ga LinkedAccount  addNewAccountClick");
        if (typeof ga === 'function') { 
            ga('send', 'event', 'LinkedAccount', 'addNewAccountClick','addNewLinkedAccountDropDown');
        }
	return true;
}

function ga_linkAccountClick(){
	console.log("ga LinkedAccount  linkAccountClick");
	var e = document.getElementById("linkedaccount_companyId");
	companyId = e.options[e.selectedIndex].value;
	companyName = e.options[e.selectedIndex].text;	
	console.log("ga 'send' 'event', 'LinkedAccount' 'linkAccountClick '" +  companyName + " " + companyId);	
        if (typeof ga === 'function') { 
            ga('send', 'event', 'LinkedAccount', 'linkAccountClick', companyName, companyId);
        }
	return true;
}

function ga_deleteAccountClick(companyId, companyName){
	console.log("ga 'send' 'event' 'LinkedAccount' 'DeleteAccountClick ' " + companyName + " " + companyId);
        if (typeof ga === 'function') {        
            ga('send', 'event', 'LinkedAccount', 'deleteAccountClick', companyName, companyId);
        }
	return true;
}




function successDelete(data){
	console.log("successDelete function is called");
	$(".allAccounts").empty();
	$('.allAccounts').html(data).show();
	console.log("Account successfully deleted");	
}


function errorDelete(data){
	console.log("errorDelete function is called");	
}


function successAdd(data){
	console.log("successAdd function is called");
	$(".allAccounts").empty();
	$('.allAccounts').html(data).show();
	$('.addLinkedAccount').addClass("hide");
	console.log("Assigning spaces to username");
	$("#ContentPlaceHolder_userName").val("");
	$("#ContentPlaceHolder_password").val(""); 
	console.log("New account was successfully saved");	
}


function errorAdd(data){
	console.log("errorAdd function is called");
	// add a msg with text to announce that an error occurred

	$(".addLinkedAccount").html(data).prepend();
	console.log("error encountered, to be appended to end");	
	$('.addLinkedAccount').removeClass('hide');	
}

function successChange(data){
    $("#feedbackContainer").html('<div id="messageErrorLinkAccount" role="alert" class="alert bg-success alert-dismissible fade in"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px; margin-top:5px;"><span aria-hidden="true">&times;</span></button><strong><?php echo __("Password changed correctly.") ?></strong></div>');
}

function errorChange(data){
    $("#feedbackContainer").html('<div id="messageErrorLinkAccount" role="alert" class="alert bg-success alert-dismissible alert-win-warning fade in"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px; margin-top:5px;"><span aria-hidden="true">&times;</span></button><strong><?php echo __("Incorrect password.") ?></strong></div>');
}
	
$(document).ready(function() {

$(document).on("click", "#linkNewAccount", function(event) {
	console.log("try to link a new account, link this account btn pressed");
        
	var link = $(this).attr( "href" );	
	var username = $("#ContentPlaceHolder_userName").val(); 	
	var password = $("#ContentPlaceHolder_password").val(); 
	var companyId = $("#linkedaccount_companyId").val();
        
	event.stopPropagation();
	event.preventDefault();	 
	console.log("check for input errors");
	if ((result = app.visual.checkFormAddLinkedAccount(username, password, companyId)) === true) {
		console.log("No input errors found");
		ga_linkAccountClick();	
		var params = { companyId:companyId, userName:username, password: password };
		var data = jQuery.param( params );
		getServerData(link, data, successAdd, errorAdd);
	}
});



$(document).on("click", "#addNewAccount", function(event) {
	console.log("addNewAccount, show new panel");

	$('.addLinkedAccount').removeClass('hide');
	console.log("addNewAccount,new panel");	
	event.stopPropagation();
	event.preventDefault();	
});


$(document).on("click", ".close", function(event) {
	ga_removeInformationBannerClick("No Linked Accounts Defined");
});



$(document).on("click", ".deleteLinkedAccount",function(event) {
	console.log("Delete existing account");
	var link = $(this).attr( "href" );
	var index =  $(this).val();  

	var params = { index:index};
	var data = jQuery.param( params );

	event.stopPropagation();
	event.preventDefault();
	
	getServerData(link, data, successDelete, errorDelete);
});

$(document).on("click", ".changePassLinkedAccount",function(event) {
    
    var index =  $(this).val();
    $(this).removeClass('changePassLinkedAccount');
    $(this).addClass('confirmChangePassLinkedAccount');
    $(this).html('<i class="ion ion-compose"></i> <small><?php echo __('Confirm Password') ?></small>');
    $("#password" + index).prop('disabled', false);
    
    
});

$(document).on("click", ".confirmChangePassLinkedAccount",function(event) {

        var link = $(this).attr( "href");
	var index =  $(this).val();
        var password = $("#password" + index).val();
        var username = $("#name" + index).val();

        $(this).addClass('changePassLinkedAccount');
        $(this).removeClass('confirmChangePassLinkedAccount');
        $(this).html('<i class="ion ion-compose"></i> <small><?php echo __('Edit Password') ?></small>');
        $("#password" + index).prop('disabled', true);   
        
	var params = { id:index,
            password: password,
            username: username,   
        };
	var data = jQuery.param( params );

	event.stopPropagation();
	event.preventDefault();
	
	getServerData(link, data, successChange, errorChange);
});


    //tooltip
    $(document).on('click', '#tooltipLA', function() {
            $('#linkAccountTooltip').toggle();
    });

    //Hide alert element after 5 seconds
    $(document).bind('DOMSubtreeModified',function(){
        fadeOutElement(".row-to-fade", 5000);
    });
        

});
</script>
<style>
    .togetoverlay .overlay  {
        z-index: 50;
        background: rgba(255, 255, 255, 0);
        border-radius: 3px;
    }
    .togetoverlay .overlay > .fa {
        font-size: 50px;
    }
</style>
<div id="accountList">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    <h4 class="title"><strong><?php echo __('Account Linking')?></strong></h4>
                </div>
                <div class="card-content table-responsive togetoverlay">
                    <div class="overlay" style="display:none;">
                        <div class="fa fa-spinner fa-spin" style="color:green;"></div>
                    </div>
                    <div class="row firstParagraph">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <p><?php
                                echo __("Both the 'Username' and 'Password' of a linked account are encrypted before they are stored on our servers")
                                ?></p>
                        </div>
                    </div>
                                            <div id="feedbackContainer">
                            
                        </div>
                    <div class="row allAccounts">
                        <?php
                            if (!empty($linkedAccountResult)) {
                                foreach ($linkedAccountResult as $account) {	
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
                                    <button type="button" data-toggle="modal" data-target="#Modal<?php echo $account['Linkedaccount']['id'] ?>" 
                                    id="company_<?php echo $account['Linkedaccount']['company_id'] ?>" 
                                    onclick='ga_deleteAccountClick("<?php echo $account['Linkedaccount']['company_id'] ?>",
                                    "<?php echo $companyResults[$account['Linkedaccount']['company_id']]['company_name']?>")'
                        class="btn btn-default btnRounded form submitButton"><i class="ion ion-trash-a"></i> <small><?php echo __('Delete')?></small>
                                    </button>
                                    
                                    <button type="button" href="/investors/changePasswordLinkedAccount" value="<?php echo $account['Linkedaccount']['id'] ?>"
                                                    id="PassCompany_<?php echo $account['Linkedaccount']['company_id'] ?>"                            
                                                    class="btn btn-default btnRounded form submitButton changePassLinkedAccount "><i class="ion ion-compose"></i> <small><?php echo __('Edit Password') ?></small>
                                    </button>
                                    
                                </div> <!-- /crowdlending company -->
                                <div class="col-xs-12 col-md-12 col-md-7 col-lg-7">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6 col-md-12 col-lg-12"> <!-- Username -->
                                            <div class="form-group">
                                                <label><small><?php echo __('Your User')?></small></label>
                                                <?php
										echo $this->Form->input('name' . $account['Linkedaccount']['id'], array(
											'label' 		=> false,
											'class' 		=> 'form-control blue_noborder2',
                                                                                        'type'                  => 'text',
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
                                                                                    echo $this->Form->input('password' . $account['Linkedaccount']['id'], array(
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
                        
                            <!-- Modal -->
<div id="Modal<?php echo $account['Linkedaccount']['id'] ?>" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo __("Are you sure?") ?></h4>
      </div>
      <div class="modal-body">
          <p><?php echo __("Do you want delete this linked account?"); ?> </p>
          <button type="button" class="btn btn-default deleteLinkedAccount" data-dismiss="modal" href="/investors/deleteLinkedAccount" value="<?php echo $account['Linkedaccount']['id'] ?>"><?php echo __("Delete"); ?></button>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __("Close"); ?></button>
      </div>
    </div>

  </div>
</div>
                        
                        
                        <?php
                            }
                        }
                        else {
                    ?>
                        <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                            <div class="alert bg-success alert-dismissible alert-win-success fade in">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px; margin-top: 2px;"><span aria-hidden="true">&times;</span></button>
                                <strong><?php echo __("You currently don't have any account defined. By adding your crowdlending accounts you will be able see all your global investment position in our global dashboard.")?>
                                </strong>
                            </div>
                        </div>	
                    <?php 
                        }
                    ?>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <button type="submit" id="addNewAccount" class="btn btn-primary btn1CR line-btn btnRounded" onclick='ga_addNewAccountClick()'><?php echo __('Add New Account')?></button>
                            </div>
                        </div>
                        <div class="addLinkedAccount hide col-xs-12 col-sm-12 col-md-12 col-lg-12">			
                    <?php    
                                            echo $this->Form->create('Linkedaccount', array('inputDefaults' => array(
                                                                                             # define error defaults for the form
                                                                                                    'error' => false,		// CakePHP Model errors will not be shown
                                                                                                    'label'	=> false,
                                                                                                                    ),
                                                                                            ));
                    ?>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo __('CrowdLending Company')?></label>
                                        <i class="fa fa-exclamation-circle" id="tooltipLA"></i>	
                <?php   	
                                                                                                                echo $this->Form->input('linkedaccount_companyId', array(
                                                                                                                'options' => $companyList,
                                                                                                                'empty' => '(choose one)',
                                                                                                                'id'   	=> 'linkedaccount_companyId',
                                                                                                                'class' => 'form-control blue_noborder2'
                                        ));
                                ?>
                                    </div>
                                </div>	
                                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_userName"><?php echo __('Your User')?></label>
                                <?php			
                                        echo $this->Form->input('Linkedaccount.linkedaccount_username', array(
                                                                                                                        'id' 			=> 'ContentPlaceHolder_userName',
                                                                                                                        'type'                  => 'text',
                                                                                                                        'label' 		=> false,
                                                                                                                        'placeholder' 	=> __("Username"),
                                                                                                                        'class' 		=> 'form-control blue_noborder2 userName',
                                                                                                                        ));
                ?>
                                    </div>
                                </div>	
                                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_password"><?php echo __('Your Password')?></label>
                <?php	
                                        echo $this->Form->input('Linkedaccount.linkedaccount_password', array(
                                                                                                                        'id' 			=> 'ContentPlaceHolder_password',
                                                                                                                        'type'			=> 'password',
                                                                                                                        'label' 		=> false,
                                                                                                                        'placeholder' 	=> __("Password"),
                                                                                                                        'class' 		=> 'form-control blue_noborder2 userPassword',
                                                                                                                        ));								
                ?>
                                    </div>
                                </div>						
                                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label><br/>
                                        <button type="button" id="linkNewAccount" href="/investors/linkAccount" class="btn btn-primary btn1CR btnRounded pull-right">
                                            <?php echo __('Link this Account')?>
                                        </button>
                                    </div>				
                                </div>
                            </div>
                            <!-- /.row -->
                            <div class="row" id="linkAccountTooltip" style="display:none">
                                <div class="col-xs-12 col-sm-8 col-md-6 col-lg-6">
                                    <small><?php echo __('The credentials of the platform you like to link to your Dashboard');?></small>
                                </div>
                            </div>
                            <div class="errorInputMessage ErrorUserName col-xs-offset-1">
                                <i class="fa fa-exclamation-circle"></i>
                                <span id="ContentPlaceHolder_ErrorUserName" class="errorMessage">Error</span>
                            </div>

                            <div class="errorInputMessage ErrorPassword col-xs-offset-1">
                                <i class="fa fa-exclamation-circle"></i>
                                <span id="ContentPlaceHolder_ErrorPassword" class="errorMessage">Error</span>
                            </div>

                            <div class="errorInputMessage ErrorPlatform col-xs-offset-1">
                                <i class="fa fa-exclamation-circle"></i>
                                <span id="ContentPlaceHolder_ErrorPlatform" class="errorMessage">Error</span>
                            </div>						
                        </div>
                        <!-- /.addLinkedAccount -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>