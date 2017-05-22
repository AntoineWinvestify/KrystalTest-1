<?php
/**
 *
 *
 * Screen which is part of the registration phase
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-10-25
 * @package

 
 
2016-10-25		version 0.1
multi-language support added
 CHECKOUT WHAT JAVASCRIPT IS NEEDED
 
select people to follow
 

 
ajax: followPeople(array(investorIds)


  or
  new screen: registerPanelD,
 

2017-03-08      version 0.3
modal updated with new css & classes
ajax spinner
 
Pending:
error checking using javascript
move all javascript to register_panel_a.ctp
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



<!--
	var jsonList = {};
	
	jsonList		: JSON.stringify(jsonList),

$("input, select").bind("change", function(event) {
	var id = $(this).attr("id");
	var value = $("#"+id).val();
	var name = $("#"+id).attr("name");
	jsonList[name] = value;
});
								'name' => 'numberOfMonths',
								'value' => $loanRequestDataResult[0]['Loanrequest']['numberOfMonths'],
-->




<div id="registerModal" class="modal show" role="dialog">
<!--   Big container   -->
	<div class="container">
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<div class="wizard-container">
					<div class="card wizard-card" data-color="green" id="wizardProfile">
						<div class="overlay">
							<div class="fa fa-spin fa-refresh">	
							</div>
						</div>
		
<?php echo $this->element("progresswizard", array("progressIndicatorStep" => REGISTRATION_PROGRESS_3)); ?>
		
						<div class="tab-content" style="padding-top: 15px;">
							<?php echo $this->Form->create('User', array('url' => "login",)); ?>
							<form class="form">	
								<div class="row">
									<div class="col-sm-offset-1 col-sm-10">
										<div class="form-group">
											<label><?php echo __('Which people do you like to follow? [Select al least 1]') ?></label>
											<table class="table">
												<thead>
													<tr>
														<th><?php echo __('User') ?></th>
														<th></th>
														<th><?php echo __('Description') ?></th>
														<th><?php echo __('Follow') ?></th>
													</tr>
												</thead>
												<?php 
													foreach ($resultPreferredFollowers as $preferredFollower) {
		
		
														$displayName = ($preferredFollower['investor_useAlias'] ?
																		$preferredFollower['Investor']['investor_alias'] :
																		$preferredFollower['Investor']['investor_name'] . " " . $preferredFollower['Investor']['investor_surname']); 
												?>		
												<tr>
													<td><?php echo $displayName?></td>
													<td><?php
														echo $this->Html->image($preferredFollower['investor_photoChatGUID'], array('alt' => $companyResults[$companyId]['company_name'],
																														'height' => '40px',
																														'width'	 => '90px'
																														));?></td>
													<td><?php echo $preferredFollower['Preferredfollower']['preferredfollower_description']?></td>
													<td><?php echo $this->Form->checkbox('done',
																						 array('value'	=> $preferredFollower['Preferredfollower']['investor_id'],
																							   'id'		=> "follower_" . $preferredFollower['Preferredfollower']['investor_id'],
																							'hiddenField' => false)
																						);
														?>
													</td>
												</tr>			
												<?php		
													}
												?>
											</table>	
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-10 col-sm-offset-1">
										<div class="center-block">
											<?php
												echo $this->Form->button(__('NEXT'), $options = array('name' 	=> 'btnRegisterUser',
																									   'id' 	=> 'btnSendFollowers',
																									   'href'	=> 'users/registerPanelC',
																									   'class' 	=> 'btn btn-green pull-right')
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
</div> <!-- /modal -->