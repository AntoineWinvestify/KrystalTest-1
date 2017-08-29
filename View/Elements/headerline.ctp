<?php
/**
 *
 *
 * shows the topheader line of internal user portal
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date
 * @package

 
 
2016-12-09		version 0.1




*/

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>

	function successNotificationsList(data){
		console.log("successNotificationsList function is called");
		$("#notifications-list").empty();
		$('#notifications-list').html(data).show();
		console.log("Show notifications");	
	}

	function errorNotificationsList(data){
		console.log("errorNotificationsList function is called");	
	}

	function successNotification(data){
		console.log("successNotification function is called");
		console.log("Show notifications");	
	}

	function errorNotification(data){
		console.log("errorNotification function is called");	
	}

	
		
	$(document).ready(function(){
		function updateNotifications() {
			var link = "/notifications/getNotificationsList";
			var index = 0;
			var params = { index:index };
			var data = jQuery.param( params );
			getServerData(link, data, successUpdateNotifications, errorUpdateNotifications);
		}
		//updateNotifications();
		//setTimeout(updateNotifications, 60000);
	});

	function successUpdateNotifications(data){
		console.log("successUpdateNotifications function is called");
		$("#notifications-list").empty();
		$('#notifications-list').html(data);
		console.log("Show notifications");	
	}

	function errorUpdateNotifications(data){
		console.log("errorUpdateNotifications function is called");	
	}
</script>
<style>
    .blackImportant {
       color: black !important;
    }
</style>

      <!-- Navbar Right Menu -->
	<div class="navbar-custom-menu">
		<ul class="nav navbar-nav">
			<!-- Notifications -->
                        <li class="dropdown notifications-menu" id="notifications-list" href="/notifications/getNotifications">
                            <a id="bell" class="dropdown-toggle" data-toggle="dropdown">
								<i class="fa fa-bell-o blackImportant"></i>
                            </a>
                        </li>
			<!-- User Account: style can be found in dropdown.less -->
			<li class="dropdown user user-menu">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					<img src="<?php echo '/' . IMAGES_URL . '/' . $sessionData['Auth']['User']['Investor']['investor_photoChatGUID']?>" class="user-image" alt="Small User Image">
					<span class="hidden-xs blackImportant"><?php echo $sessionData['Auth']['User']['Investor']['investor_name'] . " " .
														$sessionData['Auth']['User']['Investor']['investor_surname'] ?>
					</span>
				</a>
				
			
		        <ul class="dropdown-menu">
		        <!-- User image -->
					<li class="user-header">
					    <img src="<?php echo '../' . IMAGES_URL . $sessionData['Auth']['User']['Investor']['investor_photoChatGUID']?>" class="img-circle" alt="Big User Image">
	
					    <p class="blackImportant"><?php echo $sessionData['Auth']['User']['Investor']['investor_name'] . " " . $sessionData['Auth']['User']['Investor']['investor_surname'] ?> - Inversor
							<small></small>
					    </p>
					</li>
		        <?php /*<!-- Menu Body -->
 <!--
					<li class="user-body">
						<div class="row">
							<div class="col-xs-4 text-center">
								<a href="#">Followers</a>
							</div>
							<div class="col-xs-4 text-center">
								<a href="#">Sales</a>
							</div>
							<div class="col-xs-4 text-center">
								<a href="#">Friends</a>
							</div>
						</div>
-->
		            <!-- /.row -->
<!--
					</li>
-->	*/?>	  
					<!-- Menu Footer-->
					<li class="user-footer">
						<div class="pull-left">
							<a href="/investors/userProfileDataPanel" class="btn btn-default btn-flat btn-win1-inverted"><?php echo __('Profile')?></a>
						</div>
						<div class="pull-right">
							<a href="/users/logout" class="btn btn-default btn-flat btn-win1-inverted"><?php echo __('Sign out')?></a>
					    </div>
				    </li>
			    </ul>
			</li>
                       <?php /* <!-- Language selector -->
                        <li class="dropdown pull-left" style="margin-top:-3px" id="platformLanguage">
                            <?php echo $this->element('languageWidget') ?>
                        </li>*/?>
			<!-- Control Sidebar Toggle Button -->
		    <li>
			<!--            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>  -->
			</li>
		</ul>
	</div>