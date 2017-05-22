<?php
/**
 *
 *
 * Simple login screen
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-01-30
 * @package

 
 
2016-01-30		version 0.1
multi-language support added
 
 
 
 
 
 
 
*/
	
?>
<script type="text/javascript">

$(document).ready(function() {
var error = "";
	var link = "/users/loginPanel";
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
				app.utils.trace("add the active class");
				error = $("#credentialError").val();
				app.utils.trace("value of credentialError = " + error);
				
				if (error == "Credentialerror") {
					app.utils.sacarMensajeError(true, ".errorCredentials", TEXTOS.T02);
					$('.errorCredentials').addClass('actived4');		
					app.utils.trace("added");
				}
				return true;
			}
		})
});


$(function() {

$(".btnRecoverPassword").bind("click", function(event) {
	event.preventDefault();

	if (($result = app.visual.checkFormRecoverPassword()) == false) {
		return false;
	}
	else {
		var link = "users/requestNewPasswordPanel";
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
	}
});


});
</script>

		   <!--  MAIN CONTENT -->
	<div id="ContentPlaceHolder1_divCabecera" class="row-fluid row-home row-green cabecera">
	    <div class="container">
			<div id="ContentPlaceHolder_upPanel">
<?php
$credentialError = $this->Session->flash('auth');
	if (!empty($credentialError)) {

		echo $this->Form->input('', array(	'name'	=> 'credentialError',
											'value'	=> 'Credentialerror',
											'id'	=> 'credentialError',
											'type'	=> 'hidden'
											));
	}
?>				
				<div class="access">
					<!--  Here the login panel or the passwordrecovery panel is shown -->
				</div>
			</div>
		</div>
	</div>

