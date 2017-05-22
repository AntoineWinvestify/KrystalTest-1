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
	$("#accountList").empty();
	$('#accountList').html(data).show();
	console.log("Account successfully deleted");	
}


function errorDelete(data){
	console.log("errorDelete function is called");	
	
}


function successAdd(data){
	console.log("successAdd function is called");
	$("#accountList").empty();
	$('#accountList').html(data).show();
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




	
$(document).ready(function() {

$("#linkNewAccount").on("click", function(event) {
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


$("#addNewAccount").bind("click", function(event) {
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

//tooltip
$(document).on('click', '#tooltipLA', function() {
	$('#linkAccountTooltip').toggle();
});


});
</script>



<?php
	echo '<div id="accountList">';
	if (!empty($linkedAccountResult)) {
		foreach ($linkedAccountResult as $account) {		
?>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                <div class="form-group">
                    <label><?php echo __('CrowdLending Company')?></label>
<?php
											echo $this->Form->input('name', array(
												'label' 		=> false,
												'class' 		=> 'form-control blue',
												'disabled'		=> 'disabled',
												'value'			=> $companyResults[$account['Linkedaccount']['company_id']]['company_name'],						
								));
?>
                </div>					
            </div>
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                <div class="form-group">
                    <label><?php echo __('Your User')?></label>
<?php
										echo $this->Form->input('name', array(
											'label' 		=> false,
											'class' 		=> 'form-control blue',
											'disabled'		=> 'disabled',
											'value'			=> $account['Linkedaccount']['linkedaccount_username'],						
							)); 
?>										
                </div> 					
            </div>
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
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

            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                <div class="form-group">
                    <label>&nbsp;</label><br/>
                    <button type="button" href="/investors/deleteLinkedAccount" value="<?php echo $account['Linkedaccount']['id'] ?>"
                    id="company_<?php echo $account['Linkedaccount']['company_id'] ?>" 
                    onclick='ga_deleteAccountClick("<?php echo $account['Linkedaccount']['company_id'] ?>",
                    "<?php echo $companyResults[$account['Linkedaccount']['company_id']]['company_name']?>")'
                    class="btn btn-primary form submitButton deleteLinkedAccount btn-invest"><?php echo __('Delete')?>
                    </button>	
                </div>					
            </div>						
        </div>
        <!-- /.row -->
    </div>
    <!-- /.box-body  -->
<?php
		}
	}
	else {
?>
    <div class="box box-warning fade in alert-win-success">
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
        </button>
        <strong><?php echo __("You currently don't have any account defined. By adding your crowdlending accounts
                            you will be able see all your global investment position in our global
                            dashboard")?>
        </strong>
    </div>
<?php	
	}
	echo '</div>';		//	accountList
?>
    <div class="box-body">
	<div class="row">
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                <div class="form-group">
                    <button type="submit" id="addNewAccount" class="btn btn-primary btn-invest line-btn"
                    onclick='ga_addNewAccountClick()'><?php echo __('Add New Account')?></button>
		</div>
            </div>	
	</div>
    </div>
    <!-- /.box-body -->
						
    <div class="addLinkedAccount hide">
							
	<div class="box box-warning fade in alert-win-success">
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
        </button>
            <strong>
                <?php echo __("Both the 'Username' and 'Password' of a linked account are encrypted before they are stored on our servers")?>
            </strong>
	</div>
<?php    
			echo $this->Form->create('Linkedaccount', array('inputDefaults' => array(
									 # define error defaults for the form
										'error' => false,		// CakePHP Model errors will not be shown
										'label'	=> false,
												),
									));
?>
	<div class="box-body">
            <div class="overlay" style="display:none;">
		<div class="fa fa-refresh fa-spin"></div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label><?php echo __('CrowdLending Company')?></label>
                        <i class="fa fa-exclamation-circle" id="tooltipLA"></i>	
<?php   	
												echo $this->Form->input('linkedaccount_companyId', array(
												'options' => $companyList,
												'empty' => '(choose one)',
												'id'   	=> 'linkedaccount_companyId',
												'class' => 'form-control blue'
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
													'label' 		=> false,
													'placeholder' 	=> __("Username"),
													'class' 		=> 'form-control blue userName',
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
													'class' 		=> 'form-control blue userPassword',
													));								
?>
                    </div>
		</div>						
                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label>&nbsp;</label><br/>
                        <button type="button" id="linkNewAccount" href="/investors/linkAccount" class="btn btn-primary btn-invest">
                            <?php echo __('Link this Account')?>
                        </button>
                        <img src="/img/gif_carga.gif" class="imagenCargando guardarUserData" style="display: none;" />
                        <div class="cssload-squeeze hide"></div>
                    </div>				
                </div>
            </div>
            <!-- /.row -->
            <div class="row" id="linkAccountTooltip" style="display:none">
		<div class="col-xs-12 col-sm-8 col-md-6 col-lg-6">
                    <small><?php echo __('Los datos de acceso se corresponden con la plataforma a enlazar');?></small>
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
        <!-- /.box-body -->
    </div>	
    <!-- /.addLinkedAccount -->
						

