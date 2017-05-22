<?php
/**
 *
 *
 * Simple form for inviting another user
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-02-25
 * @package

 
 
2016-02-29		version 0.1
multi-language support added
 
 
  
 
 
 
 
*/
?>




<script type="text/javascript">
	var receivingController;

$(document).ready(function() {
 	receivingController = $('#receivingController').val();
$(document).ajaxComplete(function(event, request, settings) {
	$('#ajax_loader').hide();
});
$(document).ajaxSend(function(event, request, settings) {
	$('#ajax_loader').show();	
});
	

$(function() {


$("#ContentPlaceHolder_btnSendInvitation").bind("click", function(event) {
	var userText = $(".textarea-extend").val();
	var email = $(".receiverEmail").val();
	var name = $(".invitationName").val();
	var surnames = $(".invitationSurnames").val();
	var link = "createInvitation";
	
	event.stopPropagation();
	event.preventDefault();	

	if (($result = app.visual.checkFormCreateInvitation()) == false) {
		return false;
	}
	else {
		$.ajax({
			type: "POST",
			url: link,
			data: {
				invitation_name:	name,
				invitation_surnames:	surnames,
				invitation_message:	userText,
				invitation_email:	email				
				},
		
			error: function(data){
				$('#modalKO').modal('show');
			},
			success: function(data){
				$('#modalOK').modal('show')
			
				return true;
			}
		})
	}
});




/* import  the contacts from an external provider */
$("#ContentPlaceHolder_btnGoogleImport").bind("click", function(event) {
	var url = "importContacts";
	var providerName = "Google";
	app.utils.trace('Showing the providers');
	if ($(".contactProviders").is(":visible") == true) {
		$('.contactProviders').fadeOut('slow').addClass('hide');
	}
	else {
		$('.contactProviders').fadeIn('slow').removeClass('hide');
	}
	
	return;
	$.ajax({
		type: "POST",
		url: url,
		data: {
			contactsProvider:	providerName
			},	
		
		error: function(data){
app.utils.trace("received error");			
			$('#modalKO').modal('show');
		},
		success: function(data){
			$('#modalOK').modal('show')
		
			return true;
		}
	});
});






});
});

</script>


<style>
.opaque {
	opacity: .3;	
}

</style>



		<div id="ContentPlaceHolder1_divCabecera" class="row-fluid row-green row-green-sm">
			<div class="container">
			</div>
		</div>

		<div id="ContentPlaceHolder_upPanel">
			<div class="container" id="InvitationContainer">
                <div class="row row-margen" id="Invitation">
                    <div class="panel">
                        <div class="col-md-7 col-xs-12 col-md-offset-1 col-xs-offset-0">
							<div class="invitationData">
      
	                            <h3><?php echo __("Invite your Contacts")?></h3>
								 <div class="form-group" id ="invitationForm">
									<div class="col-md-7 col-xs-12 col-md-offset-1 col-xs-offset-0">
									    
										
										
										<div class="form-group">
									        <input type="submit" name="contentPlaceHolder_btnGoogleImport" value="<?php echo __('Recomendent Winvestify to my Contacts')?>" id="ContentPlaceHolder_btnGoogleImport" class="btn btn-green nomargin" />
									    </div>								
										<?php echo __("We don't store your password and we encrypt your contacts before storing them") ?>

										<div class="contactProviders hide">
											<div class="col-xs-12 col-sm-3  nomargin">
												<?php echo $this->Html->image('google_logo.png',  array('alt' 	=> 'Google_logo',
																										'class'	=> 'img-responsive',
																										'url' 	=> array('controller' => 'invitations', 'action' => 'importGoogleContacts/Google'))); ?>
											</div>
											<div class="col-xs-12 col-sm-3 nomargin">
												<?php echo $this->Html->image("STARTUP_ASSETS" .DS . 'logo_4.jpg' , array('alt' 	=> 'Yahoo_logo',
																															'class'	=> 'img-responsive opaque',
																															)); ?>
											</div>
											<div  class="col-xs-12 col-sm-3 nomargin">
												<?php echo $this->Html->image("STARTUP_ASSETS" .DS . 'logo_3.jpg' , array('alt' 	=> 'Microsoft_logo',
																														 'class'	=> 'img-responsive opaque',
																														 )); ?>
											</div>
											
											

											
										</div>
										
										
									</div>
								</div>
								<hr class="row selectInvestoption col-md-11 col-xs-12">
                                <h3><?php echo __("Invitar a friend")?></h3>
								
                                <div class="form-group" id ="invitationForm">
									<div class="col-xs-12 col-sm-5  nomargin nopadding-left">
											<?php echo __("Name of Contact) ?>
									
<?php
echo $this->Form->input('Invitation.invitation_name', array('id' 	=> 'ContentPlaceHolder_invitation_name',
															'label' => false,
															'class' => 'form-control invitationName',
															));
?>
                                    </div>

                                    <div class="col-sm-7 col-xs-12 nomarginDD">
                                        <?php echo __("Surname(s) of Contact") ?>
<?php
echo $this->Form->input('Invitation.invitation_surname', array('id'	=> 'ContentPlaceHolder_invitation_surname',
															'label' => false,
															'class' => 'form-control invitationSurnames',
						));
?>
                                    </div>

                                    <div id="ContentPlaceHolder_blockErrorInvitationName" class="errorInputMessage ErrorInvitationName">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <span class="errorMessage">
                                            <?php echo __('Error') ?>
										</span>
                                    </div>
                                    <div id="ContentPlaceHolder_blockErrorInvitationSurnames" class="errorInputMessage ErrorInvitationSurnames">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <span class="errorMessage">
                                            <?php echo __('Error') ?>
										</span>
                                    </div>
                                </div>								
								
                                <div class="form-group">
                                    <p><?php echo __("Email of Invitee")?></p>
                                    <input name="ctl00$ContentPlaceHolder$txbEmail" type="email" id="ContentPlaceHolder_Email" class="form-control receiverEmail" />
                                    <div class="errorInputMessage ErrorEmail">
                                        <i class="fa-exclamation-circle"></i>
                                        <span class="errorMessage"></span>
                                    </div>
                                    <p style="margin-top: 25px;"><?php echo __("Introduzca su texto para el/la invitado/a")?></p>
                                    <textarea name="ctl00$ContentPlaceHolderMessage" id="ContentPlaceHolder_question" class="form-control textarea-extend" placeholder="<?php echo __('I like the services of Winvestify a lot, have a look');?>"></textarea>
                                    <div class="errorInputMessage ErrorTextMessage">
                                        <i class="fa-exclamation-circle"></i>
                                        <span class="errorMessage"></span>
                                    </div>
									<div id='ajax_loader' style="position: fixed; left: 50%; top: 50%; display: none;">
										<?php echo $this->Html->image('gif_carga.gif', array('alt' => 'Wait')); ?>
									</div>					
								</div>

                                <div class="mensajeErrorDuda colorRed ocultar"></div>
                                <p class="mensajeAvisoExito mensajeExitoDuda ocultar"></p>
                                <div class="form-group">
                                    <input type="submit" name="ctl00$ContentPlaceHolder1$btnSendinvitation" value="<?php echo __('Send')?>" id="ContentPlaceHolder_btnSendInvitation" class="btn btn-green nomargin" />
                                </div>
                            </div> <!-- invitationData -->
                        </div>
                    </div>
                </div>
			</div> 
		</div>
		