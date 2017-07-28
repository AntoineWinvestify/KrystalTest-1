<?php
/**
 *
 *
 * Simple form for feedback
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-02-25
 * @package

 
 
 Panel where the user can change his/her password after having requested a password reset
 
 
 
 
 
2016-02-25		version 0.1
multi-language support added
 
 
  
 
 
 
 
*/
?>


<script type="text/javascript">
	var receivingController;
	var receivingAction;
	var linkToken = "<?php echo $linkToken ?>";

	
$(document).ready(function() {

// 	receivingController = $('#receivingController').val();

$(document).ajaxComplete(function(event, request, settings) {
	$('#ajax_loader').hide();
});
$(document).ajaxSend(function(event, request, settings) {
	$('#ajax_loader').show();	
});
	

$(function() {

$("#btnChangePassword").bind("click", function(event) {	
	var password = $(".newPassword").val();
	var password1 = $(".repeatNewPassword").val();
	var link = "/users/changePasswordOneTime";
	
	event.stopPropagation();
	event.preventDefault();	

	if (($result = app.visual.revisarNewPasswords()) == false) {
		return false;
	}
	else {
		$.ajax({
			type: "POST",
			url: link,
			data: {
				token:		linkToken,
				password:	password,
				password1:	password1
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
});


});
</script>



		<div id="ContentPlaceHolder1_upPanel">
            <div class="container">
                <div class="row row-margen">
                    <div class="panel">
                        <div class="col-md-6 col-md-offset-1 col-xs-12 col-xs-offset-0">
                            <div id="ContentPlaceHolder1_pnCambiarPassword">
		                        <h2><?php echo __("Introduce tu nueva contraseña")?></h2>
                                <p><?php echo __("Recuerda que debe tener al menos una mayúscula y un número, si además es algo que puedas recordar facilmente, sería perfecto.")?></p>

                                <div class="separator-top formChangePasswords">
                                    <div class="form-group">
                                        <div class="col-sm-6 col-xs-12 nomargin-left  nopadding-left">
                                            <label><?php echo __("Nueva contraseña")?></label>
                                            <input name="ctl00$ContentPlaceHolder$tbxPassword" type="password" id="ContentPlaceHolder_Password" class="form-control newPassword" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-6 col-xs-12 nomargin-left  nopadding-left">
                                            <label><?php echo __("Repite tu nueva contraseña")?></label>
                                            <input name="ctl00$ContentPlaceHolder$tbxPasswordRepetir" type="password" id="ContentPlaceHolder1_tbxPasswordRepetir" class="form-control repeatNewPassword" />
                                        </div>
                                    </div>
                                    <div class="errorInputMessage ErrorCambiarPassword">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <span id="ContentPlaceHolder1_Label21" class="errorMessage"></span>
									</div>
                                    <div class="form-group">
                                        <div class="col-sm-12 nomargin-left nopadding-left">  
                                            <input type="submit" name="ctl00$ContentPlaceHolder1$btCambiarContrasena" value="Guardar" id="btnChangePassword" class="btn btn-green nomargin-left" />
                                        </div>
                                    </div>
                                </div>
							</div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	
	
	