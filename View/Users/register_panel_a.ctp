<?php

/**
 *
 *
 * Screen which is part of the registration phase
 *
 * @author Antoine de Poorter
 * @version 0.2 
 * @date 2017-03-06
 * @package

 
 
2016-10-25		version 0.1
initial test version
 
2017-03-06		version 0.2
Javascript from other "register" screens was imported
modal has been updated
 
2017-03-08      version 0.3
modal updated with new css & classes
ajax spinner
 
 
2017-07-24      version 0.4
ga_registrationStep4 now sends the number of platforms in which the user has investments,
insted of the types of platforms.




*/
?>
<?php  
	if (!$error) {			// this is the "first" time that this screen is show, will not be send when server has detected an error
// For updating the url in browser if user decides to close a registration window
		echo $this->Form->input('', array('name'    => 'ownDomain',
						'value'     => $ownDomain,
						'id'        => 'ownDomain',
						'type'      => 'hidden'
					));
	

?>

<script src="/plugins/intlTelInput/js/intlTelInput.js"></script>
<link rel="stylesheet" type="text/css" href="/plugins/intlTelInput/css/intlTelInput.css">
<script type="text/javascript">

// Google Analytics events for Registration process
    function ga_registrationStep1(email) {
        console.log("ga 'send' 'event' 'Registration'  'registrationClickphase1' " + email);
        if (typeof ga === 'function') { 
            ga('send', 'event', 'Registration', 'registrationClickphase1', email);
        }
    }

    function ga_registrationStep2(phoneNumber) {
        console.log("ga 'send' 'event' 'Registration'  'registrationClickphase2' " + phoneNumber);
        if (typeof ga === 'function') { 
            ga('send', 'event', 'Registration', 'registrationClickphase2', phoneNumber);
        }
    }

    function ga_registrationStep2NewCode(phoneNumber) {
        console.log("ga 'send' 'event' 'Registration'  'registrationClickphase2NewCode' " + phoneNumber);
        if (typeof ga === 'function') {        
            ga('send', 'event', 'Registration', 'registrationClickphase2NewCode', phoneNumber);
        }
    }

//social network functionality, not yet implemented
    function ga_registrationStep3() {
        console.log("ga 'send' 'event' 'Registration'  'registrationClickphase3' ");
        if (typeof ga === 'function') {   
            ga('send', 'event', 'Registration', 'registrationClickphase3');
        }
    }

    function ga_registrationStep4(accredit, numberOfPlatforms) {
        console.log("ga 'send' 'event' 'Registration'  'registrationClickphase4' " + accredit + " " + numberOfPlatforms);
        if (typeof ga === 'function') { 
            ga('send', 'event', 'Registration', 'registrationClickphase4', accredit, numberOfPlatforms);
        }
    }

    function ga_registrationStep5() {
        console.log("ga 'send' 'event' 'Registration'  'registrationClickphase5'");
        if (typeof ga === 'function') { 
            ga('send', 'event', 'Registration', 'registrationClickphase5');
        }
    }


// The server has determined that one or more input data is not correct. 
    function successRegisterUserDataButton(data) {
        console.log("successRegisterUserDataButton function is called");
        $('#registerModal').replaceWith(data);
    }

    function errorRegisterUserDataButton(data) {
        console.log("errorRegisterUserDataButton function is called");
        $('#registerModal').replaceWith(data);
    }


    function successSendFollowersButton(data) {
        console.log("successSendFollowersButton function is called");
        $('#registerModal').replaceWith(data);
    }


    function errorSendFollowersButton(data) {
        console.log("errorSendFollowersButton function is called");
        $('#registerModal').replaceWith(data);
    }

    function successSendInvestedCompaniesButton(data) {
        console.log("successSendInvestedCompaniesButton function is called");
        $('#registerModal').replaceWith(data);
    }


    function errorSendInvestedCompaniesButton(data) {
        console.log("errorSendInvestedCompaniesButton function is called");
        $('#registerModal').replaceWith(data);
    }

    function successSendRegistrationDButton(data) {
        console.log("successSendRegistrationDButton function is called");
        $('#registerModal').replaceWith(data);

    }
    function errorSendRegistrationDButton(data) {
        console.log("errorSendRegistrationDButton function is called");
        $('#registerModal').replaceWith(data);
    }




    $(function () {

        $('#telephone').intlTelInput();

        $(document).on('click', '.close', function () {
            $('#registerModal').hide();
            document.location.href = "https://" + $('#ownDomain').val();
        });


        $(document).on("click", '.closeBtn', function () {
            $("#registerModal").removeClass("show");
        });

        $(document).on("click", 'ul > li > a', function () {
            return false;
        });

        $(document).on("click", '#tooltip1', function () {
            $('#passwordTooltip').toggle();
        });

        $(document).on("click", "#btnRegisterUserStep1", function (event) {
            var link = $(this).attr("href");
            var username = $(".userName").val();
            var password = $(".password1").val();
            var telephone = $("#telephone").intlTelInput("getNumber");

            event.stopPropagation();
            event.preventDefault();

            if ((result = app.visual.checkFormRegistrationA()) === true) {
                ga_registrationStep1(username);

                var params = {username: username, password: password, telephone: telephone};
                var data = jQuery.param(params);
                getServerData(link, data, successRegisterUserDataButton, errorRegisterUserDataButton);
            } else {
                console.log("error detected in input parameters");
            }
        });


        $(document).on("click", "#btnReturnSMSCode", function (event) {
            var link = $(this).attr("href");
            var username = $("#username").val();
            var code = $(".confirmationCode").val();
            var params = {code: code, username: username};
            var data = jQuery.param(params);
            var telephone = $("#telephone").val();

            event.stopPropagation();
            event.preventDefault();

            if ((result = app.visual.checkFormRegistrationB()) === true) {
                ga_registrationStep2(telephone);
                getServerData(link, data, successSendFollowersButton, errorSendFollowersButton);
            }
        });



        $(document).on("click", "#btnRequestNewCode", function (event) {
            var link = $(this).attr("href");
            var username = $("#username").val();
            var params = {requestNewCode: true, username: username};
            var data = jQuery.param(params);
            var telephone = $("#telephone").val();

            event.stopPropagation();
            event.preventDefault();

            ga_registrationStep2NewCode(telephone);
            getServerData(link, data, successSendFollowersButton, errorSendFollowersButton);
        });


// Not yet used, as this is social network functionality
        $(document).on("click", "#btnSendFollowers", function (event) {
            var link = $(this).attr("href");
            var data = 1;

            event.stopPropagation();
            event.preventDefault();

            console.log("error");
            if ((app.visual.checkFormSendFollowers()) === true) {
                getServerData(link, data, successSendInvestedCompaniesButton, errorSendInvestedCompaniesButton);
                return false;
            }
        });



        $(document).on("click", ".btnSendInvestedCompanies", function (event) {
            var link = $(this).attr("href");
            var username = $("#username").val();
            var investor = $('input[name="accreditedInvestor"]:checked').val();
            var p2p = $('#ContentPlaceHolder_P2PInvestment').is(':checked') ? <?php echo P2P ?> : 0;
            var p2b = $('#ContentPlaceHolder_P2BInvestment').is(':checked') ? <?php echo P2B ?> : 0;
            var invoiceTrading = $('#ContentPlaceHolder_InvoiceTrading').is(':checked') ? <?php echo INVOICE_TRADING ?> : 0;
            var crowdRealEstate = $('#ContentPlaceHolder_CrowdRealEstate').is(':checked') ? <?php echo CROWD_REAL_ESTATE ?> : 0;
            var platformcount = $('#investor_investmentPlatforms option:selected').val();
            var platformtypes = p2b + p2p + invoiceTrading + crowdRealEstate;

            event.stopPropagation();
            event.preventDefault();

            if ((app.visual.checkFormRegistrationD()) === true) {
                ga_registrationStep4(investor, platformcount);
                var params = {username: username,
                    platformcount: platformcount,
                    platformtypes: platformtypes,
                    accreditedInvestor: investor
                };
                var data = jQuery.param(params);
                getServerData(link, data, successSendRegistrationDButton, errorSendRegistrationDButton);
                return false;
            }
        });



        $(document).on("click", "#btnRegisterGoToAccount", function (event) {
            var link = $(this).attr("href");
            ga_registrationStep5();

            event.stopPropagation();
            event.preventDefault();

            window.location = link;

        });

    });
</script>






<style>
    .modal-dialog{
        overflow-y: initial !important
    }

    .modal-body{
        height: 450px;
        overflow-y: auto;
    }

    ul > li > a {
        cursor:default;
    }

    .modal { overflow-y:scroll; }


</style>


<?php
}
	if ($error) {
		echo "0";
	}
?>

<div id="registerModal" class="modal show" role="dialog">
    <!--   Big container   -->
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="wizard-container">
                    <div class="card wizard-card" data-color="green" id="wizardProfile">
                        <div class="overlay">
                            <div class="fa fa-spin fa-spinner" style="color:green">	
                            </div>
                        </div>

<?php echo $this->element("progresswizard", array("progressIndicatorStep" => REGISTRATION_PROGRESS_1)); ?>

                        <div class="tab-content" style="padding-top: 15px;">
							<?php 	echo $this->Form->create('User', array('url' => "registerPanelA",));	?>	
                            <form class="form">	
                                <div class="row">
                                    <div class="col-sm-10 col-sm-offset-1">
                                        <div class="pull-right">
                                            <small ><?php echo __("All fields are required");?></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">
                                        <div class="form-group">
                                            <label><?php echo __("Email");?></label>
											<?php
												$errorClass = "";
												if (array_key_exists('username', $validationResult)) {
													$errorClass = "redBorder";
												}
												$class = "form-control blue center-block userName". ' ' . $errorClass;
												echo $this->Form->input('username',array("label"	=> false,
																					"placeholder"	=> "Email",
																					"class" 		=> $class,
																					"value"			=> $userData['username'],
																					"error"			=> false,
																		));
												$errorClassesForTexts = "errorInputMessage ErrorUsername col-xs-offset-1";
												if (array_key_exists('username',$validationResult)) {
													$errorClassesForTexts .= " ". "actived";
												}
											?>
                                            <div class="<?php echo $errorClassesForTexts?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span id="ContentPlaceHolder_ErrorUsername" class="errorMessage"><?php echo $validationResult['username'][0] ?></span>
                                            </div>	
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">
                                        <div class="form-group">
                                            <label><?php echo __("Password");?></label>
                                            <i class="fa fa-exclamation-circle" id="tooltip1"></i>
                                            <div id="passwordTooltip" style="display:none">
                                                <small><?php echo __('Your password should be at least 8 characters long and contain uppercase and lowercase characters and a number.');?>
                                                </small>
                                            </div>
											<?php
												$errorClass = "";
												if (array_key_exists('password', $validationResult)) {
													$errorClass = "redBorder";
												}
												$class = "form-control blue center-block password1". ' ' . $errorClass;
								
												echo $this->Form->input('password1',array("label"		=> false,
																						"placeholder"	=> __("Password"),
																						'type'			=> 'password',
																						"class"			=> $class,
																						 'value'		=> $userData['password'],
																						 "error"		=> false										
																						));
												$errorClassesForTexts = "errorInputMessage ErrorPassword col-xs-offset-1";
												if (array_key_exists('password',$validationResult)) {
													$errorClassesForTexts .= " ". "actived";
												}
											?>				
                                            <div class="<?php echo $errorClassesForTexts?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span id="ContentPlaceHolder_ErrorPassword" class="errorMessage"><?php echo $validationResult['password'][0]?></span>
                                            </div>	
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-10 col-sm-offset-1">
                                        <div class="form-group">
                                            <label><?php echo __("Repeat Password");?></label>
											<?php
												$errorClass = "";
												if (array_key_exists('password', $validationResult)) {
													$errorClass = "redBorder";
												}	
												$class = "form-control blue center-block password2". ' ' . $errorClass;
								
												echo $this->Form->input('password2',array("label"		=> false,
																						"placeholder"	=> __("Repeat Password"),
																						'type'			=> 'password',
																						"class"			=> $class,
																						 'value'		=> $userData['password'],
																						 "error"		=> false	
																						));
												$errorClassesForTexts = "errorInputMessage ErrorPasswordConfirm col-xs-offset-1";
													if (array_key_exists('password',$validationResult)) {
														$errorClassesForTexts .= " ". "actived";
													}
											?>
                                            <div class="<?php echo $errorClassesForTexts?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span id="ContentPlaceHolder_ErrorPassword" class="errorMessage"><?php echo $validationResult['password'][0]?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-10 col-sm-offset-1">
                                        <div class="form-group">
                                            <label><?php echo __("Mobile Phone");?></label>
                                            <div id="telephoneTooltip">
                                                <small><?php echo __('For security reasons it is mandatory to include your mobile phone number so we can send you an account activation code');
                                                //echo __('Por motivos de seguridad debido a la naturaleza de la actividad es obligatorio incluir su teléfono para activar su cuenta.¡);
                                                ?>
                                                </small>
                                            </div>
                                            <div class="form-control blue">
												<?php
													$errorClass = "";
													if (array_key_exists('investor_telephone', $validationResult)) {
														$errorClass = "redBorder";
													}
													$class = "center-block telephoneNumber". ' ' . $errorClass;
								
													echo $this->Form->input('investor_telephone',array("label"		=> false,
																									"placeholder"	=> __("123456789"),
																									"type"			=> "tel",
																									"class"			=> $class,
																									'value'			=> $userData['telephone'],
																									"error"			=> false,
																									"id"			=> "telephone"
																			));
													$errorClassesForTexts = "errorInputMessage ErrorPhoneNumber col-xs-offset-1";
													if (array_key_exists('investor_telephone',$validationResult)) {
														$errorClassesForTexts .= " ". "actived";
													}
												?>		
                                            </div>
                                            <div class="errorInputMessage ErrorPhoneNumber col-xs-offset-1">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span id="ContentPlaceHolder_ErrorPhoneNumber" class="errorMessage"><?php echo $validationResult['investor_telephone'][0]?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-10 col-sm-offset-1">
                                        <div class="col-xs-offset-1">
                                            <label>
                                                <input name="privacyPolicy" id="ContentPlaceHolder_registerPrivacyPolicy" class="registerPrivacyPolicy" type="checkbox"/>
													<?php echo __('I accept') ?>&nbsp;<a href="/pages/termsOfService" target="_blank"><?php echo __(' the terms of service') ?>
                                                </a><?php echo __(' and the ') ?><a href="/pages/privacyPolicy" target="_blank"><?php echo __('privacy policy') ?></a>   
                                            </label>
                                        </div>
                                        <div class="errorInputMessage ErrorPrivacyPolicy col-xs-offset-1">
                                            <i class="fa fa-exclamation-circle"></i>
                                            <span id="ContentPlaceHolder_ErrorPrivacy" class="errorMessage"><?php echo __('Error') ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="wizard-footer col-sm-10 col-sm-offset-1">
                                        <div class="pull-right">
											<?php
												echo $this->Form->button(__('NEXT'), $options = array('name' 	=> 'btnRegisterUser',
																									  'id' 		=> 'btnRegisterUserStep1',
																									   'href'	=> '/users/registerPanelA', 
																									   'class' 	=> 'btn btn-default center-block btnRegisterUserStep1'));
												echo $this->Form->end();
											?>
                                        </div>
                                    </div> 
                                </div> <!-- /row -->
                            </form>
                        </div> <!-- /tab-content -->
                    </div>  <!-- /wizard-card -->
                </div> <!-- /wizard-container -->
            </div> <!-- /modal -->
        </div>
    </div>
</div>
