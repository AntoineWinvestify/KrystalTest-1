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
Javascript was moved to register_panel_a.ctp
modal has been updated


2017-03-08      version 0.3
modal updated with new css & classes
ajax spinner

 
 
*/
	
?>


<?php
	if ($error) {
		echo "0";
	}
	else {
		echo "1";
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
		
<?php echo $this->element("progresswizard", array("progressIndicatorStep" => REGISTRATION_PROGRESS_5)); ?>
		
						<div class="tab-content" style="padding-top: 15px;">	
							<div class="row">
								<div class="col-sm-10 col-sm-offset-1">
									<h2 align="center"><?php echo __('Your account has been successfully created') ?></h2><br/><br/>
									<h3 align="center"><?php echo __('Press the button to enter your account') ?></h3>
								</div> 
							</div> <!-- /row -->
							<div class="row">
								<div class="col-sm-offset-1 col-sm-10" style="margin-top:340px">
									<div class="form-group">
										<?php
											echo $this->Form->button(__('Go to My Account'), $options = array('name' => 'btnRegisterUser',
																				'href'	=> '/users/loginRedirect',
																				'id'	=> 'btnRegisterGoToAccount',
																				'class' => 'btn btn-default pull-right'));
										?>		
									</div>
								</div>
							</div> <!-- /row -->
						</div> <!-- /tab-content -->
					</div>  <!-- /wizard-card -->
				</div> <!-- /wizard-container -->
			</div> 
		</div>
	</div>

</div><!-- /modal -->