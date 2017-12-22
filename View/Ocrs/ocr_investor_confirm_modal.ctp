<?php
/**
 * +--------------------------------------------------------------------------------------------+
 * | Copyright (C) 2016, http://www.winvestify.com                                              |
 * +--------------------------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify                          |
 * | it under the terms of the GNU General Public License as published by                       |
 * | the Free Software Foundation; either version 2 of the License, or                          |
 * | (at your option) any later version.                                                        |
 * | This file is distributed in the hope that it will be useful                                |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of                             |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                               |
 * | GNU General Public License for more details.                                               |
 * +--------------------------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.2
 * @date 2017-06-09
 * @package
 * 
 * 
 * Modal to investor when he/she has to CONFIRM the data giving to Winvestify to register on the 
 * selected platforms shown on list
 * 
 * [2017-06-09] Version 0.1
 * First view.
 * Insert modal
 * Insert style rules.
 * 
 * [2017-06-14] Version 0.2
 * Added buttons to modal
 * Added js & css from paper bootstrap wizard
 * Added js to buttons
 * 
 * [2017-06-16] Version 0.3
 * New feedback and flow
 * 
 * [2017-07-06] version 0.4
 * Final process
 * 
 * [2017/07/11] version 0.5
 * Ajax for delete all investor files
 */
?>
<link type="text/css" rel="stylesheet" href="/modals/assets/css/paper-bootstrap-wizard.css"/>
<script>
    $(function () {
        $(document).on("click", ".closeBtn", function () {
            $("#1CR_investor_3_confirming").removeClass("show");
            $("#1CR_investor_3_confirming").hide();
        });


        $(document).on("click", "#btnConfirm", function () {

            console.log("server validation");
            $(".closeBtn").prop("disabled", true);
            $("#btnCancel").prop("disabled", true);
            $("#btnConfirm").prop("disabled", true);

            var params = {
                investor_name: $("#ContentPlaceHolder_name").val(),
                investor_surname: $("#ContentPlaceHolder_surname").val(),
                investor_DNI: $("#dni").val(),
                investor_dateOfBirth: $("#ContentPlaceHolder_dateOfBirth").val(),
                investor_telephone: $("#ContentPlaceHolder_telephone").intlTelInput("getNumber"),
                investor_address1: $("#ContentPlaceHolder_address1").val(),
                investor_postCode: $("#ContentPlaceHolder_postCode").val(),
                investor_city: $("#ContentPlaceHolder_city").val(),
                investor_country: $("#ContentPlaceHolder_country").val(),
                investor_email: $("#ContentPlaceHolder_email").val()
            };

            if ($("#investmentVehicle").prop("checked")) {
                params.investmentVehicle = 1;
                params.cif = $("#ContentPlaceHolder_cif").val();
                params.businessName = $("#ContentPlaceHolder_businessName").val();
                params.iban = $("#ContentPlaceHolder_iban").val();

            } else {
                params.investmentVehicle = 0;
                params.iban = $("#ContentPlaceHolder_iban").val();
            }

            link = $("#activateOCR").attr('href');
            var data = jQuery.param(params);
            getServerData(link, data, success, error);

        });


        $(document).on("click", "#btnCancel", function () {
            $(".sureMsg").show();
            $(".btnCancel").prop("disabled", true);
        });


<?php //Delete files on cancel   ?>
        $(document).on("click", "#btnSure", function () {
            $("#1CR_investor_3_confirming").removeClass("show");
            $("#1CR_investor_3_confirming").hide();
            link = "/files/deleteAll";
            data = "null";
            getServerData(link, data, successCancel, errorCancel);
        });

    });


    function success(result) {
<?php //Server validation Ok                 ?>
<?php if ($status[0]['Ocr']['ocr_status'] == ERROR) { ?> result = result.slice(0, -1); resultJson = JSON.parse(result + '1]'); <?php } else { ?>
            resultJson = JSON.parse(result);
<?php } ?>

        if (resultJson[0] == 1 && resultJson[2] == 1) {
            email = $('#ContentPlaceHolder_email').val();
            ga_1CRConfirmCompanies(<?php echo $number ?>, email);
            //$(".successMsg").fadeIn();
            $("#1CR_investor_3_confirming").removeClass("show");
            $("#1CR_investor_3_confirming").hide();
            window.location.replace('/ocrs/ocrCompletedProcess');
            //User feedback(Status ocr control?)
        } else {
            console.log("db error");
            //Save error
            $("#1CR_investor_3_confirming").removeClass("show");
            $("#1CR_investor_3_confirming").hide();
            $(".errorMsg").fadeIn();
            //User feedback
        }
    }

<?php //If delete files is ok then ,delete companies_ocr NOT_SENT   ?>
    function successCancel() {
        link = "/ocrs/deleteCompanyOcrAll";
        data = "null";
        getServerData(link, data, successDeleteAll, errorCancel);
    }

<?php //If you delete all files and companies_ocr, cancel is ok   ?>
    function successDeleteAll() {
        window.location.replace('/marketplaces/showMarketPlace');
    }

    function error(result) {
<?php //Server validation Error             ?>
        $(".errorMsg").fadeIn();
    }

    function errorCancel(result) {
        $(".errorMsg").fadeIn();
    }

            
        
    //Google Analytics
    function ga_1CRConfirmCompanies(number,email) {
        if (typeof ga === 'function') { 
            ga('send', 'event', '1ClickRegistration', 'serviceContracted', email ,number);
        }
    }
</script>
<?php if ($status[0]['Ocr']['ocr_status'] == NOT_SENT || $status[0]['Ocr']['ocr_status'] == FINISHED) { ?>
    <div id="1CR_investor_3_confirming" class="modal show" role="dialog">
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
                                        <p align="justify"><?php echo __('To complete the registration process of your selected platforms, we will inform you that '
                                                . 'Winvestify meets your demand by providing to the requested platforms all the information '
                                                . 'and documentation provided by you (name, surname, DNI / NIE, bank account and other '
                                                . 'information that may allow us to identify you as a user). By accepting, '
                                                . 'you confirm that you have read and accept the conditions of this service.') ?></p>
                                        <ul>
                                            <?php foreach ($companies as $company) { ?>
                                                <li><?php echo __($company["name"]) ?></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <div style="display:none;" class="sureMsg col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="feedback col-xs-offset-1 col-sm-offset-1 col-md-offset-1 col-lg-offset-1 center-block">
                                            <p class="errorMessage" style="padding: 20px; margin-top: 10px;"><?php echo __('Do you really want to cancel the process? All your data and documentation will be deleted.') ?></p>
                                            <button id="btnSure" class='btn btn-default center-block' name='sureBtn'><?php echo __('Yes, I want to CANCEL the process') ?></button>
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
<?php } else if ($status[0]['Ocr']['ocr_status'] == ERROR) { //Codigo para corregir los datos?> 
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
                                        <p align="justify"><?php echo __('Thank you for updating the detected incorrect data.') ?></p>
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
    <?php
}
