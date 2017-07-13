<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License



2016-08-25	  version 2016_0.1




Pending:

*/
?>

<?php
echo "1";		// always use branch "success.."
?>


<script type="text/javascript">	

function successErrorReporting(data) {
	$("#feedbackText").text(data);	
}



function errorErrorReporting(data) {
	$("#feedbackText").text(data);	
}



$(document).ready(function() {
	$("#btnSendError").on("click", function(event) {
		var link = $(this).attr( "href" ),
			optionalText = $('#optionalUserProvidedText').val();
			
		console.log("user provided text = " + optionalText);		
		$('#btnSendError').prop('disabled', true);
		
		event.stopPropagation();
		event.preventDefault();

	// get as much information as possible and convert to JSON
		var params = {	
			javascriptData: 0,
			optionalText:optionalText
		};	
		
		var data = jQuery.param( params );
		getServerData(link, data, successErrorReporting, errorErrorReporting);		
	});

	$('.errorBtn').click(function(){
    	$("#errorModal").addClass("show");
  	});	
  	$('.closeBtn').click(function(){
		$("#errorModal").removeClass("show");
	}); 	
	
});
</script>








<!--error reporting modal -->
<div id="errorModal" class="modal show" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" id="login-header-error">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h1 class="text-center"><?php echo __('- W I N V E S T I F Y -') ?></h1>
				<h3 class="text-center" style="margin-top:-10px;"><?php echo __('Error Reporting') ?></h3>
				<div id="feedbackText"></div>
			</div>
			<div class="modal-body" id="login-body-error">
<?php
	echo $this->Form->create('User', array('url' => "erroree2",));	?>
				<form class="form col-md-12 center-block">
					<div class="form-group">
		

<?php 
	echo $this->Form->textarea('userText',array('label'		=> false,
											 'placeholder'	=>  __("Brief Error Description"),
											 'class' 		=> "center-block errorDescription form-control",
											 'type'			=> "text",
											 'name' 		=> "error",
											 'id'			=> "optionalUserProvidedText"
											 ));
?>			
					</div>

					<div class="form-group">					
<?php
echo $this->Form->button(__('Send Report'), $options = array('href' 	=> '/usererrors/createReport',
															   'id' 	=> 'btnSendError',
															   'onClick'=>	'ga_errorReporting();',
															   'class' 	=> 'btn btn-blue btn-primary btn-rounded-corner btn-lg red center-block btnLoginUser '));
echo $this->Form->end();
?>
					</div>
				</form>
			</div>
			<div class="modal-footer" id="login-footer-error">
				<div class="col-md-3"><h3><?php echo __('&#946; Version')?></h3></div>
				
				<div class="col-md-9">
					<button style="margin-top: 10px;" class="btn btn-blue btn-primary btn-rounded-corner btn-sm red closeBtn" data-dismiss="modal" aria-hidden="true"><?php echo __('Cancel') ?></button>
				</div>	
			</div>
		</div>
	</div>
</div>