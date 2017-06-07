<?php
/**
 *
 *
 * Global view of total marketplace
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-08-04
 * @package



2016-08-04		version 0.1
Initial version
show results in a tabular list, with responsive view							[Not OK]
href of button incorrect



Pending:
Sorting of the columns "interest rate", "company", "amount"
-




*/
?>

<script>
$(document).ready(function() {
	console.log("Document ready");	
});
	
	
</script>

	<div id="myModalSubscription" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo __("Subscription") ?></h4>
                </div>
                <div class="modal-body">
                    <p><?php echo __("Thank you very much for your subscription") ?></p>
                    <p class="text-warning"><small><?php echo __("We will send you an email when you can start using our platform.") ?></small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-primary" data-dismiss="modal"><?php echo __("Close") ?></button>
                </div>
            </div>
        </div>
    </div>
	

	<div class="table-responsive">
		<table class="table table-striped">
			<th width="70" style="text-align:left;"><?php echo __("Portal")?></th>
			<th width="200" style="text-align:center;"><?php echo __("Applicant / Purpose<br>of Loan")?></th>
			<th width="40" style="text-align:left;"><?php echo __("Interest<br>Rate")?></th>
			<th width="80" style="text-align:left;"><?php echo __("Duration")?></th>
			<th width="40" style="text-align:left;"><?php echo __("Rating")?></th>
			<th width="45" style="text-align:left;"><?php echo __("Subscribed [%]")?></th>
			<th width="40" style="text-align:left;"><?php echo __("Amount")?></th>		
			<th width="40" style="text-align:left;"><?php echo __("Action")?></th>
<?php
			foreach ($marketPlaceResults as $market) {
				$companyId = $market['Marketplace']['company_id'];	
				echo "<tr>";			
					echo "<td>";
					echo $this->Html->image($companyResults[$companyId]['company_logoGUID'], array('alt' => $companyResults[$companyId]['company_name'],
																								'height' => '40px',
																								'width'	 => '90px'
																								));
					echo "</td>";
					
					echo "<td>";
					echo $market['Marketplace']['marketplace_name'];
					echo $market['Marketplace']['marketplace_purpose'];
					echo "</td>";
					
					echo "<td>";
					echo $market['Marketplace']['marketplace_interestRate']/100 . "% ";
					echo "</td>";
					
					echo "<td>";		
					echo $market['Marketplace']['marketplace_duration'] . " " .
						$durationPublic[$market['Marketplace']['marketplace_durationUnit']];
					echo "</td>";
					
					echo "<td>";
					echo $market['Marketplace']['marketplace_rating'];
					echo "</td>";
								
					echo "<td>";
					echo $market['Marketplace']['marketplace_subscriptionProgress']/100 ."%";
					echo "</td>";
					
					echo "<td>";
					echo $market['Marketplace']['marketplace_amount']/100 ." €";
					echo "</td>";
					
					echo "<td>";
?>
					<p class="btn-light btn btn-default btnRounded" href="<?php echo $companyResults[$companyId]['company_url']?>" target="_blank" id="btnPartners" ><?php echo __("Invest")?></p>
<?php							
					echo "</td>";
				echo "</tr>";			
			}
?>			
		</table>
	</div>
