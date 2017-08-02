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


  Shows a modal indicating that this event is going to be charged 
 
  
    
2016-10-25		version 0.1
initial test version
 * 
 * 
 * 

 * 
 * 
 not finished yet
*/
?>
<?php  
echo "0";
	if (!$error) {			// this is the "first" time that this screen is show, will not be send when server has detected an error
// For updating the url in browser if user decides to close the modal

            echo $this->Form->input('', array('name'    => 'ownDomain',
						'value'     => $ownDomain,
						'id'        => 'ownDomain',
						'type'      => 'hidden'
					));
	

$email = $parameters[1];
$phone = $parameters[2];
$userid = $parameters[0];
?>
<link type="text/css" rel="stylesheet" href="/modals/assets/css/paper-bootstrap-wizard.css"/>
<script type="text/javascript">

    $(function () {

        $(document).on('click', '.close', function () {
            console.log("Antoine");
            $('#chargingConfirmationModal').removeClass("show");
        });   

        $(document).on("click", '.closeBtn', function () {
            console.log("Antoin de Poorter");
   //         $("#chargingConfirmationModal").removeClass("show");
            $("#chargingConfirmationModal").remove();
        });

        $(document).on("click", 'ul > li > a', function () {
            return false;
        });

        $(document).on("click", '#tooltip1', function () {
            $('#passwordTooltip').toggle();
        });



        $(document).on("click", "#btnConfirm", function (event) {

            $("#chargingConfirmationModal").removeClass("show");
            var link = $(this).attr("href");
            
            var inputid = "<?php echo $userid ?>";
            var useremail = "<?php echo $email ?>";
            var usertelephone = "<?php echo $phone ?>";
            var chargingconfirmed = 1;

            var params = { inputId: inputid, userEmail:useremail, userTelephone: usertelephone, chargingConfirmed:chargingconfirmed };
            var data = jQuery.param( params );           
            event.stopPropagation();
            event.preventDefault();     

            var data = jQuery.param(params);
            getServerData(link, data, successTallymanData, errorTallymanData);
            return false;
        });
    });
</script>






<style>
    /* Not sure if needed */
    .modal-dialog{
        overflow-y: initial !important
    }

    .modal-body{
        height: 150px;
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
 print_r($parms);       
?>

<div id="chargingConfirmationModal" class="modal show" role="dialog">
    <!--   Big container   -->
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="wizard-container-small">
                    <div class="card wizard-card-small" data-color="green" id="wizardProfile">
                        <div class="wizard-header text-center">
                            <button type="button" class="close closeBtn" data-dismiss="modal" aria-hidden="true" style="margin-right: 15px;">&times;</button>
                            <img src="/img/logo_winvestify/Logo.png" style="max-width:75px;"/>
                            <img src="/img/logo_winvestify/Logo_texto.png" class="center-block" style="max-width:250px;"/>
                        </div>
                        <div class="tab-content">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <p align="justify"><?php echo __('You requested information about an investor. This event is going to be charged to your account') ?></p>
                                </div>
                                <div style="display:none;" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 successMsg">
                                    <div class="feedback errorInputMessage col-xs-offset-1 col-sm-offset-1 col-md-offset-1 col-lg-offset-1 center-block">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <span class="errorMessage" style="font-size:large"><?php echo __('You requested information about an investor. This event is going to be charged to your account.') ?></span>
                                        <button id="btnOk" class="btn btn1CR center-block" type="button"><?php echo __('Confirm') ?></button>
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
                                <input type='button' id="btnConfirm" class='btn btn-default' name='confirm' href="/adminpfp/ocrs/readtallymandata" value='Confirm' />
                            </div>

                            <div class="pull-left">
                                <input type='button' id="btnCancel" class='btn btn-default close' name='cancel' value='Cancel' />
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>  <!-- /wizard-card -->
                </div> <!-- /wizard-container -->
            </div> <!-- /modal -->
        </div>
    </div>
</div>