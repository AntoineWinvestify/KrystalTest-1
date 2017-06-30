<?php
/**
 *
 *
 *	Form for recovering a password
 *
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-01-16
 * @package



2016-01-16		version 0.1
multi-language support added




Pending:




*/


?>



<script type="text/javascript">

$(".btnReturnToLogin").bind("click", function(event) {
	var link = "loginPanel";
	
	event.stopPropagation();
	event.preventDefault();

	$.ajax({
		type: "POST",
		url: link,
		data: {
			},
		error: function(data){
			$('#modalKO').modal('show');
		},
		success: function(data){
			$('.access').replaceWith(data);
			return true;
		}
	})
});




$("#ContentPlaceHolder_btnRecoverPassword").bind("click", function(event) {
	var email = $(".recoverEmail").val();
	var link = "/users/provideNewPassword";
	
	event.stopPropagation();
	event.preventDefault();

	if (($result = app.visual.checkFormRecoverPassword()) == false) {
		return false;
	}

	$.ajax({
		type: "POST",
		url: link,
		data: {
			username: email
			},
		error: function(data){
//			$('#modalKO').modal('show');
		},
		success: function(data){
			return true;
		}
	})
});


$(document).ajaxComplete(function(event, request, settings) {
	$('#ajax_loader').hide();
});


$(document).ajaxSend(function(event, request, settings) {
	$('#ajax_loader').show();
});


</script>

				<div class="access">
					<div id="ContentPlaceHolder_pnRecoverPassword" class="formRecoverPassword">
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 panel panel-default pull-right panel-accede nomargin" id="formRecoverPassword">
<?php	echo $this->Form->create('User', array('action' => "requestNewPassword",));	 ?>					
                            <p class="accede"><?php echo __('Recuperar contraseña');?> </p>
                            <div class="form-group">
<?php
	echo $this->Form->input('email',array("label"		=> false,
											"type"			=> "text",
											"placeholder"	=> "Email",
											"id"			=> "ContentPlaceHolder_email",
											"class"			=> "center-block recoverEmail"
											));
?>
                                <div class="errorInputMessage ErrorRecoverPassword">
                                    <i class="fa fa-exclamation-circle"></i>
                                    <span id="ContentPlaceHolder1_ErrorOlvidarContrasena" class="errorMessage"><?php echo __('Error');?></span>
                                </div>
                            </div>
							<div id='ajax_loader' style="display: none; margin-left:90px;">
								<?php echo $this->Html->image('gif_carga.gif', array('alt' => 'Wait')); ?>
							</div>

                            <p class="mensajeAvisoErrorRecoverPassword ocultar"></p>
                            <p class="mensajeAvisoExito ocultar"></p>
                            <input type="submit" name="PlaceHolder1$btnRecoverPassword" value="<?php echo __('Recuperar')?>" id="ContentPlaceHolder_btnRecoverPassword" class="btn btn-green center-block" />
                            <p class="center-block">
                                <a id="ContentPlaceHolder_lnkReturnPasswordRecovery" class="btnReturnToLogin"><?php echo __('Volver')?></a>
                            </p>
                        </div>
					</div>
				</div>
