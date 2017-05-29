<?php

/**
 *
 *
 * Show the global panel for handling user data
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-12-27
 * @package



2016-12-27		version 0.1
multi-language support added




Pending:
Javascript,
Error messages and Error classes for each field must be properly named
Add all the funcionality in case the server generates an error while trying to save the data

*/


?>


<script src="/plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="/plugins/datepicker/bootstrap-datepicker.js"></script>


<script type="text/javascript">
$(document).ready(function() {

// Read the data for the first tab (personaldata) when page is loaded	
	var targ = $("#myTabs").find("a:first").attr('data-target');
	var loadurl = $("#myTabs").find("a:first").attr('href');
    $.get(loadurl, function(data) {
        $(targ).html(data);
    });

	
	
$('[data-toggle="tooltip"]').tooltip();



$(function() {

$('[data-toggle="tabajax"]').click(function(e) {
    var $this = $(this),
        loadurl = $this.attr('href');
        targ = $this.attr('data-target');
console.log("data-toggle: loadurl = " + loadurl);
console.log("data-toggle: targ = " + targ);

    $.get(loadurl, function(data) {
        $(targ).html(data);
    });

    $this.tab('show');
    return false;
});






$("#linkedAccountsData").bind("click", function(event) {
console.log("Loading data of all linked accounts");
//	$('.editDatosPersonales').empty();
//	link = $(this).attr( "href" );
//	console.log ("link = " + link); 
//	event.stopPropagation();
//	event.preventDefault();
});	


	
$("#personalData").bind("click", function(event) {
console.log("Loading personal data of User");

//	link = $(this).attr( "href" );
//	console.log ("link = " + link); 
//	event.stopPropagation();
//	event.preventDefault();
});	
	
	
// NOT YET TESTED
$("#socialNetworkUserData").bind("click", function(event) {
console.log("Loading all the profile data of my Social network");

//	var link = $(this).attr( "href" );
	console.log ("link = " + link); 
	event.stopPropagation();
	event.preventDefault();
});	


	





});
});
</script>



	<div class="row">
		<!-- left column -->
		<div class="col-md-12">
			<div class="box box-success">
				<div class="overlay" style="display:none">
					<div class="fa fa-refresh fa-spin">	
					</div>
				</div>
				<div class="box-header with-border">
					<div class="box-title">
						<ul id = "myTabs" class = "nav nav-tabs">
							<li  class = "active">
								<a href = "/investors/editUserProfileData" id="personalData"  rel="tooltip" data-target="#personalDataTab" data-toggle="tabajax"><h4><?php echo __('Personal Data')?></h4></a>
							</li>   
							<li>
								<a href = "/investors/readLinkedAccounts" id="linkedAccountsData"  rel="tooltip" data-target="#linkedAccountsTab" data-toggle="tabajax"><h4><?php echo __('Linked Accounts')?></h4></a>
							</li>
							</li>
                                                        <li>
								<a href = "/investors/ocrInvestorPlatformSelection" id="ocr1"  rel="tooltip" data-target="#OCR1Tab" data-toggle="tabajax"><h4><?php echo __('Investor I')?></h4></a>
							</li>
							<li>
								<a href = "/investors/ocrInvestorDataPanel" id="ocr2"  rel="tooltip" data-target="#OCR2Tab" data-toggle="tabajax"><h4><?php echo __('Investor II')?></h4></a>
							</li>
                                                        <li>
								<a href = "/investors/ocrPfpBillingPanel" id="ocr3"  rel="tooltip" data-target="#OCR3Tab" data-toggle="tabajax"><h4><?php echo __('PFP Admin I')?></h4></a>
							</li>
                                                        <li>
								<a href = "/investors/ocrPfpUsersPanel" id="ocr4"  rel="tooltip" data-target="#OCR4Tab" data-toggle="tabajax"><h4><?php echo __('PFP Admin II')?></h4></a>
							</li>
                                                        <li>
								<a href = "/investors/ocrWinadminInvestorChecking" id="ocr5"  rel="tooltip" data-target="#OCR5Tab" data-toggle="tabajax"><h4><?php echo __('WinAdmin I')?></h4></a>
							</li>
                                                        <li>
								<a href = "/investors/ocrWinadminBillingPanel" id="ocr6"  rel="tooltip" data-target="#OCR6Tab" data-toggle="tabajax"><h4><?php echo __('WinAdmin II')?></h4></a>
							</li>
						</ul>
					</div>
 


					<div id = "myTabContent" class = "tab-content">
						
<!-- ------------------------------------------------------------------------------------------------------------- -->							
						<div class="tab-pane fade in active" id="personalDataTab">		
						<!-- here goes the content of the "personalData" tab   -->	

						</div>
						<!-- .tab-pane  -->	
<!-- ------------------------------------------------------------------------------------------------------------- -->
						<div class = "tab-pane fade" id="linkedAccountsTab">
						<!-- here goes the content of the "linkedAccounts" tab   -->
					
						</div>
						<!-- /.tab-pane -->
<!-- ------------------------------------------------------------------------------------------------------------- -->				
						<div class = "tab-pane fade" id="socialNetworkTab">
						<!-- here goes the content of the "socialNetwork" tab   -->
					
						</div>	
						<!-- /.tab-pane -->
<!-- ------------------------------------------------------------------------------------------------------------- -->
                                                <div class = "tab-pane fade" id="OCR1Tab">
						<!-- here goes the content of the "Investor Panel I" tab   -->
					
						</div>	
						<!-- /.tab-pane -->
<!-- ------------------------------------------------------------------------------------------------------------- -->
                                                <div class = "tab-pane fade" id="OCR2Tab">
						<!-- here goes the content of the "Investor Panel II" tab   -->
					
						</div>	
						<!-- /.tab-pane -->
<!-- ------------------------------------------------------------------------------------------------------------- -->
                                                <div class = "tab-pane fade" id="OCR3Tab">
						<!-- here goes the content of the "PFD Admin I" tab   -->
					
						</div>	
						<!-- /.tab-pane -->
<!-- ------------------------------------------------------------------------------------------------------------- -->
                                                <div class = "tab-pane fade" id="OCR4Tab">
						<!-- here goes the content of the "PFD Admin II" tab   -->
					
						</div>	
						<!-- /.tab-pane -->
<!-- ------------------------------------------------------------------------------------------------------------- -->
                                                <div class = "tab-pane fade" id="OCR5Tab">
						<!-- here goes the content of the "WinAdmin I" tab   -->
					
						</div>	
						<!-- /.tab-pane -->
<!-- ------------------------------------------------------------------------------------------------------------- -->
                                                <div class = "tab-pane fade" id="OCR6Tab">
						<!-- here goes the content of the "WinAdmin II" tab   -->
					
						</div>	
						<!-- /.tab-pane -->
<!-- ------------------------------------------------------------------------------------------------------------- -->

					</div>
				</div>	
			</div>		
		</div>
	</div>
