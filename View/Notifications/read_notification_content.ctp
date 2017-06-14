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
* @date 2017-01-15
* @package
*


Provides the contents of a notification to the browser




2017-01-15		version 0.1
multi-language support added






Pending:


*/


?>
<?php
	if ($error) {		// error occured during processing of request
		echo "0";
	}
	else {
		echo "1";
	}
?>





<script type="text/javascript">	

$(document).on("click", ".closeNotificationBtn", function(event) {
	event.stopPropagation();
	event.preventDefault();
	
	// Close Modal and update the list (read it again using ajax and "close" the pull down list)
	$("#notificationContentModalMain").empty();
	$("#notificationContentModal").removeClass("show");
	//updateNotificationsLocal();
});

function updateNotificationsLocal() {
	var link = "/notifications/getNotificationsList";
	var index = 0;
	var params = { index:index };
	var data = jQuery.param( params );
	getServerData(link, data, successUpdateNotifications, errorUpdateNotifications);
}
function successUpdateNotifications(data){
	console.log("successUpdateNotifications function is called");
	$("#notifications-list").empty();
	$('#notifications-list').html(data);
	$("#notifications-complete-list").hide();
	console.log("Show notifications");	
}

function errorUpdateNotifications(data){
	console.log("errorUpdateNotifications function is called");	
}

$(document).on("click", "#bell", function(){
	$("#notifications-complete-list").toggle();
});


function successReadNotificationContent(data){
	console.log("successReadNotificationContent");
	$("#notificationContentModal").addClass("show");
	$('#notificationContentModalMain').replaceWith(data);
}

function errorReadNotificationContent(data){
	console.log("errorReadNotificationContent");
	$('#notificationContentModalMain').replaceWith(data);
	}
</script>









<?php
if ($error == true) {		// error 
?>
		<!--error modal -->
		<div id="notificationContentModal" class="modal show" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header" id="login-header-error">
						<h3 class="text-center" style="margin-top:-10px;"><?php echo __('Notification') ?></h3>
						<div id="feedbackText"></div>
					</div>
					<div class="modal-body" id="login-body-error">
<?php
						echo $this->Form->create('User', array('url' => "tempurl",));
?>
						<form class="form col-md-12 center-block">
							<div class="form-group">
<?php 
							echo __("A technical error occured while reading the contents of the notification.");
							echo __("Please try again after a few minutes");
?>
							</div>
							<div class="form-group">
<?php
								echo $this->Form->button(__('OK'), $options = array('class' => 'btn btn-blue btn-primary btn-rounded-corner center-block btnLoginUser'));
								echo $this->Form->end();
?>
							</div>
						</form>
					</div>
					<div class="modal-footer" id="login-footer-error">
						<div class="col-md-3">
							<h3 style="float:left"><?php echo __('&#946; Version') ?>
							</h3>
						</div>					
						<div class="col-md-9">
							<button class="btn btn-blue btn-primary btn-rounded-corner btn-sm btn-green .closeNotificationBtn" data-dismiss="modal" aria-hidden="true"><?php echo __('Cancel') ?></button>
						</div>	
					</div>
				</div>
			</div>
		</div>	
<?php
	}
		else {			// show contents
?>	
	<!--notification reporting modal -->
		<div id="notificationContentModal" class="modal show" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header" id="notification-modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title"><?php echo __('Notification')?></h4>
					</div>
					<div class="modal-body" id="notification-modal-content">
						<p><?php echo $notificationResult['Notification']['notification_textLong'];?></p>
					</div>
					<div class="modal-footer" id="notification-modal-footer">
						<button type="button" class="btn pull-right closeNotificationBtn btn-win1" data-dismiss="modal"><?php echo __('OK')?></button>
					</div>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

<?php
	}
?>