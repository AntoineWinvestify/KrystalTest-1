<?php
/**
* 
* Footer for internal portal
*
*
* @author Antoine de Poorter
* @version 0.1
* @date 2017-03-22
* @package
*
*
*		Progress Indicator Wizard
*
*
*	Checks if current stored investment information of the user is recent enough
*	
*	@param 		int		$progressIndicatorStep	(1-5)
* 						
* 	
		
2017-03-22		version 0.1

2017-03-26		version 0.2
Minor bug fixes. 

Pending:
-


*/
?>


<?php
	$progressIndicatorStyle = 'style="background-color: #7AC29A; border: solid 3px #7AC29A"';
	$progressTextStyle = 'style="color: #7AC29A"';
	$progressIconStyle = 'style="color:white"';
?>	
<div class="wizard-header text-center" id="wizardHeader">
				    <button type="button" class="close closeBtn" data-dismiss="modal" aria-hidden="true" style="margin-right: 15px;">&times;</button>
				    <img src="/img/logo_winvestify/Logo.png" style="float:center; max-width:75px;"/>
					<img src="/img/logo_winvestify/Logo_texto.png" style="float:center; max-width:250px;"/>

					<div class="wizard-navigation"> <!-- navigation -->
						<ul class="nav nav-pills">
					        <li style="width:25%">
<?php
							if ($progressIndicatorStep < REGISTRATION_PROGRESS_1) {
								$progressIndicatorStyle = "";
								$progressTextStyle = "";
								$progressIconStyle = "";
							}
?>
								<a style="cursor:default;">
									<div class="icon-circle" <?php echo $progressIndicatorStyle ?>>
										<i class="ti-user" <?php echo $progressIconStyle ?>></i>
									</div>
									<span <?php echo $progressTextStyle ?>> <?php echo __("User Data");?></span>
								</a>
							</li>
							
					        <li style="width:25%">
<?php
							$progressTextIndicatorCss = "";
							$progressIndicatorCss  = "";
							$progressIconCss = "";
							if ($progressIndicatorStep < REGISTRATION_PROGRESS_2) {
								$progressIndicatorStyle = "";
								$progressTextStyle = "";
								$progressIconStyle = "";
							}
?>
								<a style="cursor:default;">
									<div class="icon-circle" <?php echo $progressIndicatorStyle ?>>
										<i class="ti-mobile" <?php echo $progressIconStyle ?>></i></i>
									</div>
									<span <?php echo $progressTextStyle ?>><?php echo __("Confirmation Code");?></span>
								</a>
							</li>
							
					   <?php /* <!--<li style="width:25%">*/?>
<?php
							$progressTextIndicatorCss = "";
							$progressIndicatorCss  = "";
							$progressIconCss = "";
							if ($progressIndicatorStep < REGISTRATION_PROGRESS_3) {
								$progressIndicatorStyle = "";
								$progressTextStyle = "";
								$progressIconStyle = "";
							}
?>								
								<?php /*<a style="cursor:default;">
									<div class="icon-circle" <?php echo $progressIndicatorStyle ?>>
										<i class="ti-map" <?php echo $progressIconStyle?> ></i></i>
									</div>
									<span <?php echo $progressTextStyle ?>><?php echo  __("User Data");?></span>
								</a>
							</li> -->*/?>
						
							<li style="width:25%">
<?php
							$progressTextIndicatorCss = "";
							$progressIndicatorCss  = "";
							$progressIconCss = "";
							if ($progressIndicatorStep < REGISTRATION_PROGRESS_4) {	
								$progressIndicatorStyle = "";
								$progressTextStyle = "";
								$progressIconStyle = "";
							}
?>
								<a style="cursor:default;">
									<div class="icon-circle" <?php echo $progressIndicatorStyle ?>>
										<i class="ti-menu-alt" <?php echo $progressIconStyle ?>></i></i>
									</div>
									<span <?php echo $progressTextStyle ?>><?php echo  __("Investment Information");?></span>
								</a>
							</li>

<?php
							$progressTextIndicatorCss = "";
							$progressIndicatorCss  = "";
							$progressIconCss = "";
							if ($progressIndicatorStep < REGISTRATION_PROGRESS_5) {
								$progressIndicatorStyle = "";
								$progressTextStyle = "";
								$progressIconStyle = "";
							}
?>							
							<li style="width:25%">
								<a style="cursor:default;">
									<div class="icon-circle" <?php echo $progressIndicatorStyle ?>>
										<i class="ti-check" <?php echo $progressIconStyle ?>></i></i>
									</div>
									<span <?php echo $progressTextStyle ?>><?php echo  __("Account Created");?></span>
								</a>
							</li>
						</ul>
					</div> <!-- /wizard-navigation -->
				</div> <!-- /wizard-header -->
