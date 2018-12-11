<?php

/**
 *
 *
 * Screen which is part of the registration phase
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2017-07-07
 * @package

 
 
2016-10-25		version 0.1
initial test version
 * 
 * 
 * 
 * 
 * SHOWS A MODAL WITH A NOTE THAT THIS EVENT WILL BE CHARGED
 * 
 * 
 not finished yet
*/
?>
<?php  
	if (!$error) {			// this is the "first" time that this screen is show, will not be send when server has detected an error
// For updating the url in browser if user decides to close a registration window
		echo $this->Form->input('', ['name'    => 'ownDomain',
						'value'     => $ownDomain,
						'id'        => 'ownDomain',
						'type'      => 'hidden'
					]);
	

?>

<script type="text/javascript">

    $(function () {

        $(document).on('click', '.close', function () {
            $('#registerModal').hide();
            document.location.href = "https://" + $('#ownDomain').val();
        });


        $(document).on("click", '.closeBtn', function () {
            $("#registerModal").removeClass("show");
        });

        $(document).on("click", 'ul > li > a', function () {
            return false;
        });

        $(document).on("click", '#tooltip1', function () {
            $('#passwordTooltip').toggle();
        });




        $(document).on("click", "#btnRequestNewCode", function (event) {
            var link = $(this).attr("href");
            var username = $("#username").val();
            var params = {requestNewCode: true, username: username};
            var data = jQuery.param(params);
            var telephone = $("#telephone").val();

            event.stopPropagation();
            event.preventDefault();

            ga_registrationStep2NewCode(telephone);
            getServerData(link, data, successSendFollowersButton, errorSendFollowersButton);
        });


        $(document).on("click", ".btnSendInvestedCompanies", function (event) {
            var link = $(this).attr("href");
            var username = $("#username").val();
            var investor = $('input[name="accreditedInvestor"]:checked').val();
            var p2p = $('#ContentPlaceHolder_P2PInvestment').is(':checked') ? <?php echo P2P ?> : 0;
            var p2b = $('#ContentPlaceHolder_P2BInvestment').is(':checked') ? <?php echo P2B ?> : 0;
            var invoiceTrading = $('#ContentPlaceHolder_InvoiceTrading').is(':checked') ? <?php echo INVOICE_TRADING ?> : 0;
            var crowdRealEstate = $('#ContentPlaceHolder_CrowdRealEstate').is(':checked') ? <?php echo CROWD_REAL_ESTATE ?> : 0;
            var platformcount = $('#investor_investmentPlatforms option:selected').val();
            var platformtypes = p2b + p2p + invoiceTrading + crowdRealEstate;

            event.stopPropagation();
            event.preventDefault();

            if ((app.visual.checkFormRegistrationD()) === true) {
                ga_registrationStep4(investor, platformtypes);
                var params = {username: username,
                    platformcount: platformcount,
                    platformtypes: platformtypes,
                    accreditedInvestor: investor
                };
                var data = jQuery.param(params);
                getServerData(link, data, successSendRegistrationDButton, errorSendRegistrationDButton);
                return false;
            }
        });



        $(document).on("click", "#btnRegisterGoToAccount", function (event) {
            var link = $(this).attr("href");
            ga_registrationStep5();

            event.stopPropagation();
            event.preventDefault();

            window.location = link;

        });

    });
</script>






<style>
    /* Not sure if needed */
    .modal-dialog{
        overflow-y: initial !important
    }

    .modal-body{
        height: 450px;
        overflow-y: auto;
    }

    ul > li > a {
        cursor:default;
    }

    .modal { overflow-y:scroll; }


</style>


<?php
}
	if ($error) {
		echo "0";
	}
?>

<div id="1CR_investor_3_confirming" class="modal show" role="dialog">
    <!--   Big container   -->
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="wizard-container">
                    <div class="card wizard-card" data-color="green" id="wizardProfile">
                        <div class="wizard-header text-center">
                            <button type="button" class="close closeBtn" data-dismiss="modal" aria-hidden="true" style="margin-right: 15px;">&times;</button>
                            <img src="/img/logo_winvestify/Logo.png" style="max-width:75px;"/>
                            <img src="/img/logo_winvestify/Logo_texto.png" class="center-block" style="max-width:250px;"/>
                        </div>
                        <div class="tab-content">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <p align="justify"><?php echo __('You entered the homepage of winadm') ?></p>
                                    <ul>
                                        <?php foreach ($companies as $companies) { ?>
                                            <li><?php echo __($companies["company_name"]) ?></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <div style="display:none;" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 successMsg">
                                    <div class="feedback errorInputMessage col-xs-offset-1 col-sm-offset-1 col-md-offset-1 col-lg-offset-1 center-block">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <span class="errorMessage" style="font-size:large"><?php echo __('This is an action which will be billed to your account.') ?></span>
                                        <button id="btnOk" class="btn btn1CR center-block" type="button"><?php echo __('Thank you') ?></button>
                                    </div>
                                </div>
                                <div style="display:none;" class="errorMsg col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="feedback errorInputMessage col-xs-offset-1 col-sm-offset-1 col-md-offset-1 col-lg-offset-1 center-block">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <span class="errorMessage" style="font-size:large"><?php echo __('Error.') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- /tab-content -->
                        <div class="wizard-footer">
                            <div class="pull-right">
                                <input type='button' id="btnConfirm" class='btn btn-default' name='confirm' value='Confirm' />
                            </div>

                            <div class="pull-left">
                                <input type='button' id="btnCancel" class='btn btn-default' name='cancel' value='Cancel' />
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>  <!-- /wizard-card -->
                </div> <!-- /wizard-container -->
            </div> <!-- /modal -->
        </div>
    </div>
</div>