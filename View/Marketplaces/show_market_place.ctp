<?php

/**
 *
 *
 * Framework for the internal market place
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-12-19
 * @package



2016-12-19		version 0.1
multi-language support added




Pending:
-

*/


?>


<script src="/plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/js/accounting.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">


	
<script>
// Google Analytics functions
	var tableSearchString;
	
function ga_investLinkClicked(loanId, companyId, redirectUrl) {
	console.log("ga 'send' 'event' 'Redirects'  'redirectClick' " + loanId + " " + companyId);
	ga('send', 'event', 'Redirects', 'redirectClick', loanId, companyId);
	location.href = redirectUrl;
}


$(document).ready(function() {
	
var table = $('#investmentMarketplace').DataTable( {
		"responsive": true,
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "/marketplaces/listMarketPlace",
            "type": "POST"
        },
        "columns": [
            { "data": "marketplace_name" },
            { "data": "marketplace_purpose" },
            { "data": "marketplace_interestRate" },
            { "data": "marketplace_duration" },
            { "data": "marketplace_rating" },
            { "data": "marketplace_subscriptionProgress" },
            { "data": "marketplace_amount" },
			{ "data": "marketplace_action" },
        ]
    } );	

	
// save the search string	 
$('#investmentMarketplace').on( 'search.dt', function () {
    tableSearchString = $('.dataTables_filter input').val();
});

// provide the searchstring to google analytics 
$('.dataTables_filter input').on( 'blur', function() {
	ga_tableSearchstring(tableSearchString, 1);
});
 
$('#investmentMarketplace').on( 'order.dt', function () {
    var order = table.order();$(".dataTables_filter input").is(":focus");
	ga_tableSortedColumnClick(order[0][0] + 1, 1);
}); 
  
$('#investmentMarketplace').on( 'length.dt', function ( e, settings, len ) {
	ga_tableChangePageLength(len, 1);
});
 
$('#investmentMarketplace').on( 'page.dt', function () {
    var info = table.page.info();
	ga_tablePageClick(info.page + 1, 1);
});
 
 


	
});
</script>


<?php
$this->assign('title',"MarketPlace"); 
//$this->assign('title',__("MarketPlace")); 
// Required for the counters at the top of the page
	foreach ($globalResults as $result) {
		$global['totalAmount'] = $global['totalAmount'] + $result['TotalInvestmentAmountAvailableInCompany'];
		$global['totalOptions'] = $global['totalOptions'] + $result['TotalInvestmentOptionsAvailableInCompany'];		
		$global['totalPreInvested'] = $global['totalPreInvested'] + $result['TotalAmountPreInvestedInCompany'];				
	}
	
?>


	<div class="row">
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<div class="info-box" data-toggle="tooltip" data-placement="auto" title="<?php echo __('Number of Companies with Open Investments')?>">
				<span class="info-box-icon bg-win1">
					<i class="fa fa-bank"></i>
				</span>
				<div class="info-box-content">
					<span class="info-box-number info-box-text"><?php echo count($globalResults)?></span>
					<span class="info-box-text"><?php echo __('Active Companies')?></span>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<div class="info-box" data-toggle="tooltip" data-placement="auto" title="<?php echo __('Amount already invested in Open Investments')?>">
				<span class="info-box-icon bg-win3">
					<i class="fa fa-line-chart"></i>
				</span>
				<div class="info-box-content">
					<span class="info-box-number info-box-text invested-amount-box"></span>
					<span class="info-box-text"><?php echo __('Total Amount Already Invested')?></span>
				</div>
			</div>
		</div>
	
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<div class="info-box" data-toggle="tooltip" data-placement="auto" title="<?php echo __('Total Amount of all the open investments')?>">
				<span class="info-box-icon bg-win5">
					<i class="fa fa-pie-chart"></i>
				</span>
				<div class="info-box-content">
					<span class="info-box-number info-box-text " data-from="0" data-to="<?php echo (int) ($global['totalAmount'] / 100 );?>"
									data-speed="3000" data-refresh-interval="50">
					</span>
					<span class="info-box-number  info-box-text total-amount-box"></span>	
<?php
	$tempValueTotalAmount= (int) ($global['totalAmount'] / 100);
	$tempValueAlreadyInvestedAmount = (int) ($global['totalPreInvested'] / 100); 
?>
<script type="text/javascript">
	var optionsAccounting = {
		symbol : " &euro;",
		decimal : ",",
		thousand: ".",
		precision : 0,
		format: "%v%s"
		};
		
		temp1 = accounting.formatMoney(<?php echo $tempValueTotalAmount?>, optionsAccounting);
		$(".total-amount-box").append(temp1);

		temp2 = accounting.formatMoney(<?php echo $tempValueAlreadyInvestedAmount?>, optionsAccounting);
		$(".invested-amount-box").append(temp2);
</script>
					<span class="info-box-number  info-box-text total-amount-box"></span>			
					<span class="info-box-text"><?php echo __('Total Investment Amount')?></span>
				</div>
			</div>
		</div>
	
	
	
	
		
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<div class="info-box" data-toggle="tooltip" data-placement="auto" title="<?php echo __('Total Number of Investments in Global Marketplace')?>">
				<span class="info-box-icon bg-win7">
					<i class="fa fa-flag"></i>
				</span>
				<div class="info-box-content">
					<span class="info-box-text info-box-number" data-from="0" data-to="<?php echo $global['totalOptions']?>"
										data-speed="3000" data-refresh-interval="50">
					<span class="monetaryCounter info-box-text  info-box-number"><?php echo $global['totalOptions']?></span>
					</span>
					<span class="info-box-text"><?php echo __('Open Investments')?></span>
				</div>
			</div>
		</div>
	</div>


	<div class="row">
		<div class="col-xs-12">
			<div class="box box-success">
				<div class="box-header with-border">
					<h3 class="box-title"><?php echo __('Global Market Place') ?></h3>
				</div>
			
			    <div class="box-body">
					<div class="overlay" style="display:none;">
						<div class="fa fa-refresh fa-spin">	
						</div>
					</div>
					<div class="col-md-12">
						<div class="table-responsive">  
							<table id="investmentMarketplace" class="investmentDetails display" width="100%" cellspacing="0"
													data-order='[[ 2, "asc" ]]' data-page-length='25'>
								<thead>
									<tr>
										<th><?php echo __('Name')?></th>
										<th><?php echo __('Purpose')?></th>
										<th><?php echo __('Interest Rate')?></th>
										<th><?php echo __('Duration')?></th>
										<th><?php echo __('Rating')?></th>
										<th><?php echo __('Progress')?></th>
										<th><?php echo __('Amount')?></th>
										<th><?php echo __('Action')?></th>
									</tr>
								</thead>
							</table>
						<div class="table-responsive"> 
					</div>
			    </div>
				<!-- /.box-body -->
			</div>
		</div>
	</div>
<?php
/*		
<script type="text/javascript">

//$('.numberCounter').countTo();
 

$('.monetaryCounter').countTo({
    formatter: function (value) {
		
	var options = {
		symbol : " €",
		decimal : ",",
		thousand: ".",
		precision : 0,
		format: "%v%s"
	};
	return accounting.formatMoney(value, options);
    }

});

*/
?>
</script>			
			