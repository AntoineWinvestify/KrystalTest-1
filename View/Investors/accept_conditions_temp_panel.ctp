<?php

/**
 *
 *
 * Show the panel when the user enters for the first time in Winvestify.com
 * s/he is offered various options 
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2017-03-05
 * @package



2017-03-05		version 0.1





Pending:

*/


?>




<!--accept conditions modal -->
<div id="acceptConditionsModal" class="modal show" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h1 class="text-center"><?php echo __('Welcome to Winvestify') ?></h1>
			</div>
			<div class="modal-body">
				
				<br/>
				<span><?php echo __('What do you like to do?') ?></span>
				<br/>
				<button type="button" href="/investors/editUserProfileData" class="btn btn-default center-block personaldata" aria-hidden="true">
					<?php echo __('Personalize my Account') ?>
				</button>
				<button type="button" href="KK" class="btn btn-default center-block tour" aria-hidden="true">
					<?php echo __('Take the tour') ?>
				</button>
				<button type="button" href="/companys/showCompanyDataPanel" class="btn btn-default btn-primary center-block socialnetwork" aria-hidden="true">
					<?php echo __('Go to Social Network') ?>
				</button>
				<button type="button" href="/dashboards/getDashboardData" class="btn btn-default btn-primary center-block socialnetwork" aria-hidden="true">
					<?php echo __('Go to my Dashboard') ?>
				</button>	

			</div>
			<div class="modal-footer">
				<div class="col-md-12">
					<button class="btn btn-blue btn-rounded-corner btn-sm" data-dismiss="modal" aria-hidden="true"><?php echo __('Cancel') ?></button>
				</div>	
			</div>
		</div>
	</div>
</div>