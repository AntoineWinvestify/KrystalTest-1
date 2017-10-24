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


<?php 
	echo $this->Form->input('', array(	'name'	=> 'username',
										'value'	=> $username,
										'id'	=> 'username',
										'type'	=> 'hidden'
										));
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
		
<?php echo $this->element("progresswizard", array("progressIndicatorStep" => REGISTRATION_PROGRESS_4)); ?>
		
						<div class="tab-content" style="padding-top: 15px;">
							<?php echo $this->Form->create('User', array('url' => "login",)); ?>	
							<form class="form">	
								<div class="row">
									<div class="col-sm-offset-1 col-sm-10">
										<div class="form-group">
											<label><?php echo __("Are you an accredited investor?");?></label><br/>
											<input name="accreditedInvestor" id="ContentPlaceHolder_accreditedInvestor" value ="<?php echo ACCREDITED_INVESTOR ?>" type="radio"> <?php echo __('Yes')?><br/>
											<input name="accreditedInvestor" id="ContentPlaceHolder_accreditedInvestor" value ="<?php echo NOT_ACCREDITED_INVESTOR ?>" type="radio"> <?php echo __('No')?>
			
											<div class="errorInputMessage ErrorInvestor col-xs-offset-1">
											   <i class="fa fa-exclamation-circle"></i>
											   <span id="ContentPlaceHolder_ErrorInvestor" class="errorMessage"><?php echo "Error";?></span>
											</div>							
											
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-10 col-sm-offset-1">
										<?php
											$options = array(0 => "0", 1 => "1", 2 => "2", 3 => "3",  4 => "4", 5 => "5", 6 => "6", 7 => "7", 8 => "8",  9 => "9", 10 => "10", 99 => " > 10");
											
											echo "<label>" . __('In how many crowdlending platforms do you have active investments?') . "</label>";
										?>
										<div class="form-group">
											<?php
												echo $this->Form->input('investor_investmentPlatforms', array(
													'options' => $options,
													'label'	=> false,
													'empty' => __("Choose one"),
													'id'   	=> 'investor_investmentPlatforms',
													'class' => 'form-control blue_noborder'
												));
											?>
										</div>
									</div>
								</div> <!-- /row -->
								<div class="row">
									<div class="col-sm-10 col-sm-offset-1">
										<div class="form-group">
											<div class="row">
												<div class="col-sm-10 col-sm-offset-1 center-block">
													<?php	
														echo "<label>" .__('In which type of crowdlending platform do you invest? <small>[mark all that apply]</small>') . "</label>";
													?>
												</div>
											</div>
											<div class="row">
												<div class="pull-right">
													<div class="col-sm-10">
														<input name="investor_P2PInvestment" id="ContentPlaceHolder_P2PInvestment" value = "<?php echo P2P ?>" type="checkbox"/>
														<label><?php echo __('Peer-to-Peer Lending Consumer') ?></label>
														<input type="hidden" name="investor_P2PInvestment" value="0" />
													</div>
													<div class="col-sm-10">
														<input name="investor_P2BInvestment" id="ContentPlaceHolder_P2BInvestment" value = "<?php echo P2B ?>" type="checkbox"/>
														<label><?php echo __('Peer-to-Peer Lending Business') ?></label>
														<input type="hidden" name="investor_P2BInvestment" value="0" />
													</div>
													<div class="col-sm-10">
														<input name="investor_InvoiceTrading" id="ContentPlaceHolder_InvoiceTrading" value = "<?php echo INVOICE_TRADING ?>" type="checkbox"/>
														<label><?php echo __('Invoice Trading') ?></label>
														<input type="hidden" name="investor_InvoiceTrading" value="0" />
													</div>
													<div class="col-sm-10">
														<input name="investor_CrowdRealEstate" id="ContentPlaceHolder_CrowdRealEstate" value = "<?php echo CROWD_REAL_ESTATE ?>" type="checkbox"/>
														<label><?php echo __('Crowdfunding Real Estate') ?></label>
														<input type="hidden" name="investor_CrowdRealEstate" value="0" />
													</div>
												</div>
											</div>				
											<div class="errorInputMessage ErrorPlatformSelection col-xs-offset-1">
											   <i class="fa fa-exclamation-circle"></i>
											   <span id="ContentPlaceHolder_ErrorPlatformSelection" class="errorMessage"><?php echo "Error";?></span>
											</div>
										</div>
									</div> 
								</div> <!-- /row -->
								<div class="row">
									<div class="col-sm-offset-1 col-sm-10" style="margin-top:100px">
										<div class="form-group">
											<?php
												echo $this->Form->button(__('NEXT'), $options = array('name' 	=> 'btnRegisterUser',
																									   'id' 	=> 'btnSendDataInvestedCompanies',
																									   'href'	=> '/users/registerPanelD',
																									   'class' 	=> 'btn btn-green pull-right btnSendInvestedCompanies')
																					 );
												echo $this->Form->end();
											?>
										</div>
									</div>
								</div> <!-- /row -->
							</form>
						</div> <!-- /tab-content -->
					</div>  <!-- /wizard-card -->
				</div> <!-- /wizard-container -->
			</div> 
		</div>
	</div>
</div><!-- /modal -->