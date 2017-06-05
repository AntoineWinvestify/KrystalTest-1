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
	/*if ($error) {		// error occured during processing of request
		echo "0";
	}
	else {
		echo "1";
	}*/
	echo "1";

?>





<script type="text/javascript">
function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}	
$(document).ready(function() {

		$(document).on("click", ".li-notification", function(){
			var id = $(this).attr("id");
			var link = $(this).attr("href");
			var params = { id:id };
			var data = jQuery.param( params );
			getServerData(link, data, successReadNotificationContent, errorReadNotificationContent);
			return false;
		});
		
		$('.closeBtn').click(function(){
			$("#errorModal").removeClass("show");
		});
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
		<div id="notificationModal" class="modal show" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header" id="notification-modal-header">
						<h3 class="text-center" style="margin-top:-10px;"><?php echo __('Notification') ?></h3>
						<div id="feedbackText"></div>
					</div>
					<div class="modal-body" id="notification-modal-body">
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
						<div class="col-md-3"><h3 style="float:left"><?php echo __('&#946; Version') ?></h3></div>
						
						<div class="col-md-9">
							<button class="btn btn-blue btn-primary btn-rounded-corner btn-sm btn-green closeBtn" data-dismiss="modal" aria-hidden="true"><?php echo __('Cancel') ?></button>
						</div>	
					</div>
				</div>
			</div>
		</div>
		
		
<?php
	}
	else {			// show the list contents
?>
				<a id="bell" class="dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bell-o" style="color: black;"></i>
					<span class="label label-warning"><?php echo count($resultNotifications)?></span>
				</a>
				<ul class="dropdown-menu" id="notifications-complete-list">
<?php
					if (count($resultNotifications) == 0) {		// The user has no notifications pending
?>
						<li class="header"><?php echo __('You have no notifications')?></li>
<?php
					}
					else {
?>				
						<li class="header"><?php echo __('You have ')?><?php echo count($resultNotifications)?>
							<?php echo __(' notifications')?>
						</li>
						<li>
						<!-- inner menu: contains the actual data -->
							<ul class="menu notifications-menu">
<?php
							foreach ($resultNotifications as $notification) {
?>	
								<li>
									<a class="li-notification" id="<?php echo $notification['Notification']['id']?>" href="/notifications/readNotificationContent">
										<i class="fa fa-users text-aqua"></i><?php echo $notification['Notification']['notification_textShort']?>
									</a>
								</li>
<?php
							}
?>
							</ul>
						</li>
						<li class="footer"><a href="#"><?php echo __('View all')?></a></li>
<?php						
					}
?>					
				</ul>
				
<?php

	}
?>